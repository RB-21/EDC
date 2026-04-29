<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumen;
use App\Services\RagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
    public function chatPage()
    {
        $health = $this->ragService->healthCheck();
        $model = config('services.rag.default_model', 'gemini-2.0-flash');
        $availableModels = config('services.rag.available_models', [
            'gemini-2.0-flash',
            'gemini-2.5-flash-preview-04-17',
            'gemini-2.5-pro-preview-03-25',
        ]);

        return view('admin.rag.chat', compact('health', 'model', 'availableModels'));
    }

    /**
     * AJAX: Kirim pertanyaan ke RAG.
     */
    public function query(Request $request)
    {
        $request->validate(['question' => 'required|string|min:3']);

        try {
            // Ambil konteks daftar dokumen ter-index agar AI tidak tampilkan nama file hash
            $docCatalog = $this->getIndexedDocCatalog();
            $question = $request->input('question');

            if (!empty($docCatalog)) {
                $question = $docCatalog . "\n\n" . $question;
            }

            $result = $this->ragService->query(
                $question,
                $request->input('jenis_file'),
                $request->input('bagian'),
                $request->input('doc_id') ? (int) $request->input('doc_id') : null,
                $request->input('model')
            );

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
                $catalog .= "- [{$jenis}] {$doc->nomor} — {$doc->judul}\n";
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
}
