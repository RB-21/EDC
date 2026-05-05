<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiChatMessage;
use App\Models\AiChatSession;
use App\Models\AiSetting;
use App\Models\Dokumen;
use App\Services\RagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RagController extends Controller
{
    protected $ragService;

    public function __construct(RagService $ragService)
    {
        $this->ragService = $ragService;
    }

    /**
     * Halaman chat AI.
     */
    public function chatPage(Request $request)
    {
        $user = $request->user();
        $health = $this->ragService->healthCheck();
        $availableModels = $this->getAllowedModelsForUser($user);

        $defaultModel = config('services.rag.default_model', 'gemini-2.0-flash');
        $model = in_array($defaultModel, $availableModels, true)
            ? $defaultModel
            : ($availableModels[0] ?? $defaultModel);

        $tokenBalance = (int) ($user->ai_token_balance ?? 0);

        return view('admin.rag.chat', compact('health', 'model', 'availableModels', 'tokenBalance'));
    }

    /**
     * Halaman pengaturan prompt RAG (admin).
     */
    public function promptSettingsPage()
    {
        $promptTemplate = $this->getRagPromptTemplate();

        return view('admin.rag.settings', [
            'promptTemplate' => $promptTemplate,
        ]);
    }

    /**
     * Simpan pengaturan prompt RAG.
     */
    public function updatePromptSettings(Request $request)
    {
        if (!Schema::hasTable('ai_settings')) {
            return back()->with([
                'success' => false,
                'message' => 'Tabel ai_settings belum tersedia. Jalankan migration atau SQL docs terlebih dahulu.',
            ]);
        }

        $request->validate([
            'prompt_template' => 'required|string|min:50|max:60000',
        ]);

        $template = (string) $request->input('prompt_template');

        // Wajib ada placeholder agar template valid untuk runtime.
        foreach (['{{CONTEXT_BLOCK}}', '{{QUESTION}}'] as $requiredPlaceholder) {
            if (mb_strpos($template, $requiredPlaceholder) === false) {
                return back()->with([
                    'success' => false,
                    'message' => "Template wajib memuat placeholder {$requiredPlaceholder}.",
                ])->withInput();
            }
        }

        AiSetting::updateOrCreate(
            ['key' => 'rag_prompt_template'],
            [
                'value' => $template,
                'description' => 'Template prompt utama untuk RAG generation',
            ]
        );

        return back()->with([
            'success' => true,
            'message' => 'Template prompt RAG berhasil disimpan.',
        ]);
    }

    /**
     * AJAX: daftar session chat user.
     */
    public function sessions(Request $request)
    {
        $user = $request->user();

        $sessions = AiChatSession::with('latestMessage')
            ->where('user_id', $user->id)
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->limit(50)
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'title' => $session->title ?: 'Percakapan Baru',
                    'model' => $session->model,
                    'last_message_at' => optional($session->last_message_at)->toDateTimeString(),
                    'preview' => Str::limit((string) optional($session->latestMessage)->message, 90),
                ];
            });

        return response()->json([
            'sessions' => $sessions,
            'token_balance' => (int) ($user->ai_token_balance ?? 0),
        ]);
    }

    /**
     * AJAX: detail pesan dalam satu session.
     */
    public function sessionMessages(Request $request, $sessionId)
    {
        $session = AiChatSession::where('id', $sessionId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $messages = AiChatMessage::where('session_id', $session->id)
            ->orderBy('id')
            ->get()
            ->map(function ($message) {
                $meta = $message->meta ?? [];
                return [
                    'id' => $message->id,
                    'role' => $message->role === 'assistant' ? 'ai' : 'user',
                    'content' => (string) $message->message,
                    'sources' => $meta['sources'] ?? [],
                    'follow_up_questions' => $meta['follow_up_questions'] ?? [],
                    'usage' => [
                        'prompt_tokens' => (int) ($message->prompt_tokens ?? 0),
                        'completion_tokens' => (int) ($message->completion_tokens ?? 0),
                        'total_tokens' => (int) ($message->total_tokens ?? 0),
                    ],
                    'model' => $message->model,
                    'created_at' => optional($message->created_at)->toDateTimeString(),
                ];
            });

        return response()->json([
            'session' => [
                'id' => $session->id,
                'title' => $session->title,
                'model' => $session->model,
            ],
            'messages' => $messages,
            'token_balance' => (int) ($request->user()->ai_token_balance ?? 0),
        ]);
    }

    /**
     * AJAX: buat session baru (opsional dipakai frontend).
     */
    public function createSession(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:120',
        ]);

        $user = $request->user();
        $allowedModels = $this->getAllowedModelsForUser($user);
        $selectedModel = $request->input('model');
        if ($selectedModel && !in_array($selectedModel, $allowedModels, true)) {
            return response()->json([
                'error' => true,
                'message' => 'Model tidak diizinkan untuk user ini.',
            ], 422);
        }

        $session = AiChatSession::create([
            'user_id' => $user->id,
            'title' => $request->input('title', 'Percakapan Baru'),
            'model' => $selectedModel ?: (config('services.rag.default_model')),
            'last_message_at' => now(),
        ]);

        return response()->json([
            'error' => false,
            'session' => [
                'id' => $session->id,
                'title' => $session->title,
                'model' => $session->model,
            ],
        ]);
    }

    /**
     * AJAX: hapus session user.
     */
    public function deleteSession(Request $request, $sessionId)
    {
        $session = AiChatSession::where('id', $sessionId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $session->delete();

        return response()->json([
            'error' => false,
            'message' => 'Session berhasil dihapus.',
        ]);
    }

    /**
     * AJAX: Kirim pertanyaan ke RAG.
     */
    public function query(Request $request)
    {
        $request->validate([
            'question' => 'required|string|min:3',
            'model' => 'nullable|string|max:120',
            'session_id' => 'nullable|integer',
        ]);

        $user = $request->user();
        $availableModels = $this->getAllowedModelsForUser($user);
        $selectedModel = $request->input('model') ?: (config('services.rag.default_model', 'gemini-2.0-flash'));

        if (!in_array($selectedModel, $availableModels, true)) {
            return response()->json([
                'error' => true,
                'answer' => 'Model yang dipilih tidak diizinkan untuk akun Anda.',
                'sources' => [],
            ], 422);
        }

        $currentBalance = (int) ($user->ai_token_balance ?? 0);
        if ($currentBalance <= 0) {
            return response()->json([
                'error' => true,
                'answer' => 'Saldo token Anda habis. Hubungi admin untuk topup.',
                'sources' => [],
            ], 402);
        }

        try {
            $question = $request->input('question');
            $session = $this->resolveSession($user->id, $request->input('session_id'), $question, $selectedModel);
            $promptTemplate = $this->getRagPromptTemplate();

            $result = $this->ragService->query(
                $question,
                $request->input('jenis_file'),
                $request->input('bagian'),
                $request->input('doc_id') ? (int) $request->input('doc_id') : null,
                $selectedModel,
                null,
                $promptTemplate
            );

            if (($result['error'] ?? false) === true) {
                return response()->json($result, 500);
            }

            $answer = (string) ($result['answer'] ?? '');
            $sources = $result['sources'] ?? [];
            $followUpQuestions = array_values(array_filter((array) ($result['follow_up_questions'] ?? [])));
            $usage = $result['usage'] ?? [];

            $promptTokens = (int) ($usage['prompt_tokens'] ?? 0);
            $completionTokens = (int) ($usage['completion_tokens'] ?? 0);
            $totalTokens = (int) ($usage['total_tokens'] ?? 0);
            if ($totalTokens <= 0) {
                $totalTokens = $this->estimateTotalTokens($question, $answer);
            }
            if ($promptTokens <= 0) {
                $promptTokens = (int) ceil($totalTokens * 0.4);
            }
            if ($completionTokens <= 0) {
                $completionTokens = max(0, $totalTokens - $promptTokens);
            }

            $newBalance = max(0, $currentBalance - $totalTokens);

            DB::transaction(function () use (
                $session,
                $user,
                $question,
                $answer,
                $sources,
                $followUpQuestions,
                $selectedModel,
                $promptTokens,
                $completionTokens,
                $totalTokens,
                $newBalance
            ) {
                AiChatMessage::create([
                    'session_id' => $session->id,
                    'user_id' => $user->id,
                    'role' => 'user',
                    'message' => $question,
                    'model' => $selectedModel,
                    'prompt_tokens' => 0,
                    'completion_tokens' => 0,
                    'total_tokens' => 0,
                ]);

                AiChatMessage::create([
                    'session_id' => $session->id,
                    'user_id' => $user->id,
                    'role' => 'assistant',
                    'message' => $answer,
                    'model' => $selectedModel,
                    'prompt_tokens' => $promptTokens,
                    'completion_tokens' => $completionTokens,
                    'total_tokens' => $totalTokens,
                    'meta' => [
                        'sources' => $sources,
                        'follow_up_questions' => $followUpQuestions,
                    ],
                ]);

                $session->update([
                    'model' => $selectedModel,
                    'last_message_at' => now(),
                    'title' => $session->title ?: Str::limit($question, 80),
                ]);

                $user->update([
                    'ai_token_balance' => $newBalance,
                ]);
            });

            $result['usage'] = [
                'prompt_tokens' => $promptTokens,
                'completion_tokens' => $completionTokens,
                'total_tokens' => $totalTokens,
            ];
            $result['session_id'] = $session->id;
            $result['token_balance'] = $newBalance;
            $result['model'] = $selectedModel;
            $result['follow_up_questions'] = $followUpQuestions;

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'answer' => 'Gagal menghubungi RAG service: ' . $e->getMessage(),
                'sources' => []
            ], 500);
        }
    }

    /**
     * Ambil katalog dokumen ter-index dari database.
     * Di-cache selama 5 menit agar tidak query DB setiap kali.
     */
    private function getIndexedDocCatalog()
    {
        return Cache::remember('rag_doc_catalog', 300, function () {
            $docIds = $this->ragService->getIndexedDocIds();

            if (empty($docIds)) {
                return '';
            }

            $docs = Dokumen::whereIn('id', $docIds)
                ->select('id', 'nomor', 'judul', 'jenis_file_kode', 'bagian')
                ->get();

            if ($docs->isEmpty()) {
                return '';
            }

            $catalog = "[Daftar Dokumen yang Tersedia di Sistem]\n";
            $catalog .= "Gunakan nomor dan judul berikut (JANGAN gunakan nama file PDF) saat menyebut dokumen:\n";
            foreach ($docs as $doc) {
                $jenis = strtoupper($doc->jenis_file_kode ?? '');
                $catalog .= "- [{$jenis}] {$doc->nomor} - {$doc->judul}\n";
            }

            return $catalog;
        });
    }

    /**
     * AJAX: Index dokumen ke RAG.
     */
    public function indexDocument($docId)
    {
        $result = $this->ragService->indexDocument($docId);

        // Hapus cache katalog agar list terbaru
        Cache::forget('rag_doc_catalog');

        return response()->json($result);
    }

    /**
     * AJAX: Cek status indexing.
     */
    public function indexStatus($jobKey)
    {
        $result = $this->ragService->getIndexStatus($jobKey);
        return response()->json($result);
    }

    /**
     * AJAX: Hapus index dokumen.
     */
    public function deleteIndex($docId)
    {
        $result = $this->ragService->deleteIndex($docId);

        // Hapus cache katalog agar list terbaru
        Cache::forget('rag_doc_catalog');

        return response()->json($result);
    }

    /**
     * AJAX: Ambil list indexed doc_ids.
     */
    public function indexedDocuments()
    {
        $docIds = $this->ragService->getIndexedDocIds();
        return response()->json(['doc_ids' => $docIds]);
    }

    private function getAllowedModelsForUser($user): array
    {
        $defaultModels = config('services.rag.available_models', [
            'gemini-2.0-flash',
            'gemini-2.5-flash-preview-04-17',
            'gemini-2.5-pro-preview-03-25',
        ]);

        if (!$user) {
            return $defaultModels;
        }

        return $user->getAllowedAiModels($defaultModels);
    }

    private function estimateTotalTokens(string $question, string $answer): int
    {
        $raw = max(1, (int) ceil((mb_strlen($question) + mb_strlen($answer)) / 4));
        return max(1, $raw);
    }

    private function resolveSession(int $userId, $sessionId, string $question, string $model): AiChatSession
    {
        if ($sessionId) {
            $session = AiChatSession::where('id', (int) $sessionId)
                ->where('user_id', $userId)
                ->first();
            if ($session) {
                return $session;
            }
        }

        return AiChatSession::create([
            'user_id' => $userId,
            'title' => Str::limit($question, 80),
            'model' => $model,
            'last_message_at' => now(),
        ]);
    }

    private function getRagPromptTemplate(): string
    {
        $default = (string) config('services.rag.default_prompt_template', '');
        $fromDb = (string) AiSetting::getValue('rag_prompt_template', $default);

        if (trim($fromDb) === '') {
            return $default;
        }

        if (mb_strpos($fromDb, '{{CONTEXT_BLOCK}}') === false || mb_strpos($fromDb, '{{QUESTION}}') === false) {
            return $default;
        }

        return $fromDb;
    }
}
