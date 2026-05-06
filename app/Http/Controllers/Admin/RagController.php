<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiChatMessage;
use App\Models\AiChatSession;
use App\Models\AiSetting;
use App\Models\Dokumen;
use App\Models\MasterJenisFile;
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
        $promptRules = $this->getRagPromptRules();
        $intentRoutingSettings = $this->getIntentRoutingSettings();

        return view('admin.rag.settings', [
            'promptTemplate' => $promptTemplate,
            'promptRules' => $promptRules,
            'intentActiveDocumentContextEnabled' => $intentRoutingSettings['enable_active_document_context'],
            'intentCatalogPatterns' => $intentRoutingSettings['catalog_patterns'],
            'intentActiveDocumentReferencePatterns' => $intentRoutingSettings['active_document_reference_patterns'],
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
            'prompt_rules' => 'required|string|min:20|max:30000',
            'intent_catalog_patterns' => 'nullable|string|max:20000',
            'intent_active_document_reference_patterns' => 'nullable|string|max:12000',
            'intent_enable_active_document_context' => 'nullable|boolean',
        ]);

        $template = (string) $request->input('prompt_template');
        $rules = trim((string) $request->input('prompt_rules'));
        $intentCatalogPatterns = trim((string) $request->input('intent_catalog_patterns', ''));
        $intentActiveDocumentReferencePatterns = trim((string) $request->input('intent_active_document_reference_patterns', ''));
        $intentEnableActiveDocumentContext = $request->has('intent_enable_active_document_context')
            ? $request->boolean('intent_enable_active_document_context')
            : false;

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

        AiSetting::updateOrCreate(
            ['key' => 'rag_prompt_rules'],
            [
                'value' => $rules,
                'description' => 'Aturan sistem dan format output untuk RAG generation',
            ]
        );

        AiSetting::updateOrCreate(
            ['key' => 'rag_intent_enable_active_document_context'],
            [
                'value' => $intentEnableActiveDocumentContext ? '1' : '0',
                'description' => 'Aktifkan konteks dokumen aktif untuk follow-up question',
            ]
        );

        AiSetting::updateOrCreate(
            ['key' => 'rag_intent_catalog_patterns'],
            [
                'value' => $intentCatalogPatterns,
                'description' => 'Pattern intent katalog dokumen (regex/frase per baris)',
            ]
        );

        AiSetting::updateOrCreate(
            ['key' => 'rag_intent_active_document_reference_patterns'],
            [
                'value' => $intentActiveDocumentReferencePatterns,
                'description' => 'Pattern referensi ke dokumen aktif (regex/frase per baris)',
            ]
        );

        return back()->with([
            'success' => true,
            'message' => 'Pengaturan prompt RAG berhasil disimpan.',
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
                'active_document' => $this->getSessionActiveDocument($session),
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
                'active_document' => null,
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
            'question_context' => 'nullable|string|max:60000',
            'model' => 'nullable|string|max:120',
            'session_id' => 'nullable|integer',
        ]);

        $user = $request->user();
        $question = trim((string) $request->input('question'));
        $availableModels = $this->getAllowedModelsForUser($user);
        $selectedModel = $request->input('model') ?: (config('services.rag.default_model', 'gemini-2.0-flash'));
        $requestedDocId = $request->input('doc_id') ? (int) $request->input('doc_id') : null;

        if (!in_array($selectedModel, $availableModels, true)) {
            return response()->json([
                'error' => true,
                'answer' => 'Model yang dipilih tidak diizinkan untuk akun Anda.',
                'sources' => [],
            ], 422);
        }

        $intentRoutingSettings = $this->getIntentRoutingSettings();
        $existingSession = $this->findExistingSession($user->id, $request->input('session_id'));
        $routingContext = $this->buildIntentRoutingContext(
            $question,
            $requestedDocId,
            $existingSession,
            $intentRoutingSettings
        );

        $catalogIntent = $this->detectCatalogIntent(
            $question,
            $request->input('jenis_file'),
            $intentRoutingSettings,
            $routingContext
        );
        if ($catalogIntent !== null) {
            return $this->respondWithCatalogSuggestion(
                $user,
                $request->input('session_id'),
                $selectedModel,
                $question,
                $catalogIntent,
                $routingContext
            );
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
            $questionContext = trim((string) $request->input('question_context', ''));
            $questionForRag = $questionContext !== '' ? $questionContext : $question;

            $session = $existingSession ?: $this->resolveSession($user->id, $request->input('session_id'), $question, $selectedModel);
            $promptTemplate = $this->getRagPromptTemplate();
            $promptRules = $this->getRagPromptRules();
            $effectiveDocId = $routingContext['resolved_doc_id'] ?? $requestedDocId;

            $result = $this->ragService->query(
                $questionForRag,
                $request->input('jenis_file'),
                $request->input('bagian'),
                $effectiveDocId,
                $selectedModel,
                null,
                $promptTemplate,
                $promptRules
            );

            if (($result['error'] ?? false) === true) {
                return response()->json($result, 500);
            }

            $answer = (string) ($result['answer'] ?? '');
            $sources = $result['sources'] ?? [];
            $followUpQuestions = array_values(array_filter((array) ($result['follow_up_questions'] ?? [])));
            $usage = $result['usage'] ?? [];
            $activeDocument = $this->extractActiveDocumentFromSources($sources, $effectiveDocId);
            if ($activeDocument === null && $effectiveDocId) {
                $activeDocument = $this->resolveDocumentById($effectiveDocId);
            }

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
                $newBalance,
                $activeDocument,
                $routingContext
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
                        'active_document' => $activeDocument,
                        'routing' => [
                            'mode' => 'rag',
                            'effective_doc_id' => $activeDocument['doc_id'] ?? null,
                            'used_active_document_context' => (bool) ($routingContext['used_active_document_context'] ?? false),
                            'matched_active_document_reference' => (bool) ($routingContext['matched_active_document_reference'] ?? false),
                        ],
                    ],
                ]);

                $sessionAttributes = [
                    'model' => $selectedModel,
                    'last_message_at' => now(),
                    'title' => $session->title ?: Str::limit($question, 80),
                ];

                if ($this->supportsSessionMeta()) {
                    $sessionAttributes['meta'] = $this->mergeSessionMeta($session, [
                        'active_document' => $activeDocument,
                    ]);
                }

                $session->update($sessionAttributes);

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

    private function detectCatalogIntent(
        string $question,
        ?string $requestedJenisFile = null,
        array $intentRoutingSettings = [],
        array $routingContext = []
    ): ?array
    {
        $normalized = Str::lower(trim($question));
        $normalized = preg_replace('/\s+/', ' ', $normalized ?? '') ?? '';

        if (($routingContext['matched_active_document_reference'] ?? false) === true) {
            return null;
        }

        if (($routingContext['has_explicit_doc_id'] ?? false) === true) {
            return null;
        }

        $jenisInfo = $this->inferJenisFileFromQuestion($normalized, $requestedJenisFile);
        $jenisPatterns = [];
        if ($jenisInfo !== null) {
            foreach (array_filter([
                Str::lower((string) ($jenisInfo['kode'] ?? '')),
                Str::lower((string) ($jenisInfo['display'] ?? '')),
            ]) as $term) {
                $jenisPatterns[] = preg_quote($term, '/');
            }
        }

        $documentTerms = [
            'dokumen',
            'surat',
            'file',
            'arsip',
            'berkas',
        ];

        if (!empty($jenisPatterns)) {
            $documentTerms = array_merge($documentTerms, $jenisPatterns);
        }

        $documentPattern = '/\b(?:' . implode('|', array_unique($documentTerms)) . ')\b/u';
        $listPattern = '/\b(?:apa saja|daftar|list|yang tersedia|yang ada|tersedia|ada)\b/u';

        $isGeneralCatalogQuery = false;

        if ($this->matchesConfiguredPatterns($normalized, (string) ($intentRoutingSettings['catalog_patterns'] ?? ''))) {
            $isGeneralCatalogQuery = true;
        }

        if (!$isGeneralCatalogQuery && $jenisInfo !== null) {
            $compact = trim($normalized);
            $jenisAlternatives = implode('|', array_unique($jenisPatterns));
            if (
                $jenisAlternatives !== '' &&
                preg_match('/^(?:(?:ada|daftar|list)\s+)?(?:dokumen\s+)?(?:' . $jenisAlternatives . ')(?:\s+(?:apa saja|yang tersedia|yang ada|tersedia))?$/u', $compact)
            ) {
                $isGeneralCatalogQuery = true;
            }
        }

        if (
            !$isGeneralCatalogQuery &&
            $jenisInfo !== null &&
            preg_match($documentPattern, $normalized) &&
            preg_match($listPattern, $normalized)
        ) {
            $isGeneralCatalogQuery = true;
        }

        if (!$isGeneralCatalogQuery) {
            return null;
        }

        return [
            'jenis_file' => $jenisInfo['kode'] ?? $requestedJenisFile,
            'jenis_label' => $jenisInfo['display'] ?? null,
        ];
    }

    private function inferJenisFileFromQuestion(string $question, ?string $requestedJenisFile = null): ?array
    {
        if ($requestedJenisFile) {
            $jenis = MasterJenisFile::where('kode', $requestedJenisFile)->first();
            if ($jenis) {
                return [
                    'kode' => $jenis->kode,
                    'display' => $jenis->singkatan ?: $jenis->kepanjangan ?: strtoupper($jenis->kode),
                ];
            }

            return [
                'kode' => $requestedJenisFile,
                'display' => strtoupper($requestedJenisFile),
            ];
        }

        $allJenis = MasterJenisFile::all(['kode', 'singkatan', 'kepanjangan']);
        foreach ($allJenis as $jenis) {
            $terms = array_filter([
                Str::lower((string) $jenis->kode),
                Str::lower((string) $jenis->singkatan),
                Str::lower((string) $jenis->kepanjangan),
            ]);

            foreach ($terms as $term) {
                if ($term !== '' && preg_match('/\b' . preg_quote($term, '/') . '\b/', $question)) {
                    return [
                        'kode' => $jenis->kode,
                        'display' => $jenis->singkatan ?: $jenis->kepanjangan ?: strtoupper($jenis->kode),
                    ];
                }
            }
        }

        return null;
    }

    private function respondWithCatalogSuggestion(
        $user,
        $sessionId,
        string $selectedModel,
        string $question,
        array $catalogIntent,
        array $routingContext = []
    )
    {
        $session = $this->resolveSession($user->id, $sessionId, $question, $selectedModel);
        $docs = $this->getIndexedDocsForUser($user, $catalogIntent['jenis_file'] ?? null);
        $jenisLabel = $catalogIntent['jenis_label'] ?? null;
        $activeDocument = $this->getSessionActiveDocument($session);

        if ($docs->isEmpty()) {
            $answer = $jenisLabel
                ? "Saat ini saya belum menemukan dokumen {$jenisLabel} yang sudah tersedia dan ter-index di sistem."
                : 'Saat ini saya belum menemukan dokumen yang sudah tersedia dan ter-index di sistem.';

            $followUpQuestions = [
                'Dokumen apa saja yang sudah ter-index saat ini?',
                'Bisakah tampilkan jenis dokumen lain yang tersedia?',
                'Dokumen mana yang perlu saya pilih untuk diringkas?',
            ];
        } else {
            $label = $jenisLabel ? "dokumen {$jenisLabel}" : 'dokumen';
            $answerLines = [
                "Saat ini {$label} yang tersedia di sistem adalah:",
                '',
            ];

            foreach ($docs->take(12) as $index => $doc) {
                $jenisText = strtoupper((string) ($doc->jenis_file_kode ?? ''));
                $title = trim((string) ($doc->judul ?? 'Tanpa judul'));
                $number = trim((string) ($doc->nomor ?? '-'));
                $answerLines[] = ($index + 1) . '. [' . $jenisText . '] ' . $number . ' - ' . $title;
            }

            if ($docs->count() > 12) {
                $remaining = $docs->count() - 12;
                $answerLines[] = '';
                $answerLines[] = "Masih ada {$remaining} dokumen lain yang relevan.";
            }

            $answerLines[] = '';
            $answerLines[] = 'Jika Anda mau, sebutkan nomor atau judul dokumen yang ingin saya ringkas atau jelaskan lebih lanjut.';
            $answer = implode("\n", $answerLines);

            $firstDoc = $docs->first();
            $followUpQuestions = array_values(array_filter([
                $firstDoc ? 'Ringkas dokumen ' . trim((string) $firstDoc->nomor) : null,
                $jenisLabel ? "Apa isi utama dokumen {$jenisLabel} yang paling terbaru?" : 'Dokumen mana yang paling terbaru?',
                'Tampilkan dokumen lain yang serupa atau terkait.',
            ]));
        }

        DB::transaction(function () use (
            $session,
            $user,
            $question,
            $answer,
            $selectedModel,
            $followUpQuestions,
            $activeDocument,
            $routingContext
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
                'prompt_tokens' => 0,
                'completion_tokens' => 0,
                'total_tokens' => 0,
                'meta' => [
                    'sources' => [],
                    'follow_up_questions' => $followUpQuestions,
                    'active_document' => $activeDocument,
                    'routing' => [
                        'mode' => 'catalog',
                        'effective_doc_id' => $activeDocument['doc_id'] ?? null,
                        'used_active_document_context' => (bool) ($routingContext['used_active_document_context'] ?? false),
                        'matched_active_document_reference' => (bool) ($routingContext['matched_active_document_reference'] ?? false),
                    ],
                ],
            ]);

            $sessionAttributes = [
                'model' => $selectedModel,
                'last_message_at' => now(),
                'title' => $session->title ?: Str::limit($question, 80),
            ];

            if ($this->supportsSessionMeta()) {
                $sessionAttributes['meta'] = $this->mergeSessionMeta($session, [
                    'active_document' => $activeDocument,
                ]);
            }

            $session->update($sessionAttributes);
        });

        return response()->json([
            'error' => false,
            'answer' => $answer,
            'sources' => [],
            'usage' => [
                'prompt_tokens' => 0,
                'completion_tokens' => 0,
                'total_tokens' => 0,
            ],
            'session_id' => $session->id,
            'token_balance' => (int) ($user->ai_token_balance ?? 0),
            'model' => $selectedModel,
            'follow_up_questions' => $followUpQuestions,
        ]);
    }

    private function getIndexedDocsForUser($user, ?string $jenisFile = null)
    {
        $docIds = $this->ragService->getIndexedDocIds();
        if (empty($docIds)) {
            return collect();
        }

        $query = Dokumen::with('dJenisFile:id,kode,singkatan,kepanjangan')
            ->whereIn('id', $docIds)
            ->select('id', 'nomor', 'judul', 'jenis_file_kode', 'created_at');

        $allowedJenis = array_values(array_filter(array_map('trim', explode(',', (string) ($user->jenis_file ?? '')))));
        if (!empty($allowedJenis)) {
            $query->whereIn('jenis_file_kode', $allowedJenis);
        }

        if ($jenisFile) {
            $query->where('jenis_file_kode', $jenisFile);
        }

        return $query
            ->orderBy('jenis_file_kode')
            ->orderByDesc('created_at')
            ->orderBy('nomor')
            ->get();
    }

    private function findExistingSession(int $userId, $sessionId): ?AiChatSession
    {
        if (!$sessionId) {
            return null;
        }

        return AiChatSession::where('id', (int) $sessionId)
            ->where('user_id', $userId)
            ->first();
    }

    private function resolveSession(int $userId, $sessionId, string $question, string $model): AiChatSession
    {
        $existingSession = $this->findExistingSession($userId, $sessionId);
        if ($existingSession) {
            return $existingSession;
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

    private function getRagPromptRules(): string
    {
        $default = (string) config('services.rag.default_prompt_rules', '');
        $fromDb = trim((string) AiSetting::getValue('rag_prompt_rules', $default));

        return $fromDb !== '' ? $fromDb : $default;
    }

    private function getIntentRoutingSettings(): array
    {
        return [
            'enable_active_document_context' => $this->getBooleanAiSetting(
                'rag_intent_enable_active_document_context',
                true
            ),
            'catalog_patterns' => $this->getTextAiSetting(
                'rag_intent_catalog_patterns',
                $this->getDefaultCatalogPatterns()
            ),
            'active_document_reference_patterns' => $this->getTextAiSetting(
                'rag_intent_active_document_reference_patterns',
                $this->getDefaultActiveDocumentReferencePatterns()
            ),
        ];
    }

    private function getDefaultCatalogPatterns(): string
    {
        return implode("\n", [
            '/\bdaftar\s+(?:dokumen|arsip|berkas|file)\b/u',
            '/\b(?:dokumen|arsip|berkas|file)\b.*\b(?:apa saja|yang tersedia|yang ada|tersedia)\b/u',
            '/\b(?:apa saja|yang tersedia|yang ada|tersedia)\b.*\b(?:dokumen|arsip|berkas|file)\b/u',
            '/\bada\s+(?:dokumen|arsip|berkas|file)\b/u',
            '/^(?:dokumen|daftar dokumen)$/u',
        ]);
    }

    private function getDefaultActiveDocumentReferencePatterns(): string
    {
        return implode("\n", [
            'dokumen ini',
            'dokumen tersebut',
            'surat ini',
            'surat tersebut',
            'berdasarkan dokumen ini',
            'berdasarkan dokumen tersebut',
            'berdasarkan surat ini',
            'dalam dokumen ini',
            'di dokumen ini',
            'pada dokumen ini',
        ]);
    }

    private function getTextAiSetting(string $key, string $default = ''): string
    {
        $value = trim((string) AiSetting::getValue($key, $default));
        return $value !== '' ? $value : $default;
    }

    private function getBooleanAiSetting(string $key, bool $default = false): bool
    {
        $value = AiSetting::getValue($key, $default ? '1' : '0');
        if ($value === null || $value === '') {
            return $default;
        }

        return in_array(Str::lower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }

    private function buildIntentRoutingContext(
        string $question,
        ?int $requestedDocId,
        ?AiChatSession $session,
        array $intentRoutingSettings
    ): array {
        $normalized = Str::lower(trim($question));
        $normalized = preg_replace('/\s+/', ' ', $normalized ?? '') ?? '';
        $activeDocument = null;

        if (($intentRoutingSettings['enable_active_document_context'] ?? true) && $session) {
            $activeDocument = $this->getSessionActiveDocument($session);
        }

        $matchedActiveDocumentReference = $activeDocument !== null
            && $this->matchesConfiguredPatterns(
                $normalized,
                (string) ($intentRoutingSettings['active_document_reference_patterns'] ?? '')
            );

        $resolvedDocId = $requestedDocId;
        $usedActiveDocumentContext = false;

        if (!$resolvedDocId && $matchedActiveDocumentReference && !empty($activeDocument['doc_id'])) {
            $resolvedDocId = (int) $activeDocument['doc_id'];
            $usedActiveDocumentContext = true;
        }

        return [
            'has_explicit_doc_id' => $requestedDocId !== null,
            'requested_doc_id' => $requestedDocId,
            'resolved_doc_id' => $resolvedDocId,
            'active_document' => $activeDocument,
            'matched_active_document_reference' => $matchedActiveDocumentReference,
            'used_active_document_context' => $usedActiveDocumentContext,
        ];
    }

    private function getSessionActiveDocument(?AiChatSession $session): ?array
    {
        if (!$session) {
            return null;
        }

        if ($this->supportsSessionMeta()) {
            $meta = is_array($session->meta ?? null) ? $session->meta : [];
            $fromSessionMeta = $this->normalizeActiveDocument($meta['active_document'] ?? null);
            if ($fromSessionMeta !== null) {
                return $fromSessionMeta;
            }
        }

        $lastAssistantMessage = AiChatMessage::where('session_id', $session->id)
            ->where('role', 'assistant')
            ->orderByDesc('id')
            ->first();

        if (!$lastAssistantMessage) {
            return null;
        }

        $meta = is_array($lastAssistantMessage->meta ?? null) ? $lastAssistantMessage->meta : [];
        $fromMessageMeta = $this->normalizeActiveDocument($meta['active_document'] ?? null);
        if ($fromMessageMeta !== null) {
            return $fromMessageMeta;
        }

        return $this->extractActiveDocumentFromSources((array) ($meta['sources'] ?? []));
    }

    private function normalizeActiveDocument($document): ?array
    {
        if (!is_array($document)) {
            return null;
        }

        $docId = isset($document['doc_id']) && $document['doc_id'] !== ''
            ? (int) $document['doc_id']
            : null;
        $nomor = trim((string) ($document['nomor'] ?? ''));
        $judul = trim((string) ($document['judul'] ?? ''));
        $jenisFileKode = trim((string) ($document['jenis_file_kode'] ?? ''));

        if ($docId === null && $nomor === '' && $judul === '') {
            return null;
        }

        return [
            'doc_id' => $docId,
            'nomor' => $nomor !== '' ? $nomor : null,
            'judul' => $judul !== '' ? $judul : null,
            'jenis_file_kode' => $jenisFileKode !== '' ? $jenisFileKode : null,
        ];
    }

    private function extractActiveDocumentFromSources(array $sources, ?int $preferredDocId = null): ?array
    {
        if (empty($sources)) {
            return null;
        }

        $selectedSource = null;
        if ($preferredDocId !== null) {
            foreach ($sources as $source) {
                if ((int) ($source['doc_id'] ?? 0) === $preferredDocId) {
                    $selectedSource = $source;
                    break;
                }
            }
        }

        if ($selectedSource === null) {
            foreach ($sources as $source) {
                if (!empty($source['doc_id']) || !empty($source['nomor']) || !empty($source['judul'])) {
                    $selectedSource = $source;
                    break;
                }
            }
        }

        return $this->normalizeActiveDocument($selectedSource);
    }

    private function resolveDocumentById(int $docId): ?array
    {
        $document = Dokumen::query()
            ->select('id', 'nomor', 'judul', 'jenis_file_kode')
            ->find($docId);

        if (!$document) {
            return null;
        }

        return $this->normalizeActiveDocument([
            'doc_id' => $document->id,
            'nomor' => $document->nomor,
            'judul' => $document->judul,
            'jenis_file_kode' => $document->jenis_file_kode,
        ]);
    }

    private function matchesConfiguredPatterns(string $subject, string $patternsText): bool
    {
        $lines = preg_split('/\r\n|\r|\n/', $patternsText) ?: [];
        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '' || Str::startsWith($line, '#')) {
                continue;
            }

            if ($this->lineMatchesSubject($subject, $line)) {
                return true;
            }
        }

        return false;
    }

    private function lineMatchesSubject(string $subject, string $line): bool
    {
        $pattern = $line;
        if (Str::startsWith($pattern, 'regex:')) {
            $pattern = trim((string) Str::after($pattern, 'regex:'));
        }

        if (Str::startsWith($pattern, '/')) {
            $matchResult = @preg_match($pattern, $subject);
            return $matchResult === 1;
        }

        return Str::contains($subject, Str::lower($pattern));
    }

    private function supportsSessionMeta(): bool
    {
        static $supportsSessionMeta = null;

        if ($supportsSessionMeta === null) {
            $supportsSessionMeta = Schema::hasColumn('ai_chat_sessions', 'meta');
        }

        return $supportsSessionMeta;
    }

    private function mergeSessionMeta(AiChatSession $session, array $overrides = []): array
    {
        $existing = is_array($session->meta ?? null) ? $session->meta : [];

        foreach ($overrides as $key => $value) {
            if ($value === null) {
                unset($existing[$key]);
                continue;
            }

            $existing[$key] = $value;
        }

        return $existing;
    }
}
