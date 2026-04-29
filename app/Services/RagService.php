<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RagService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.rag.base_url', 'http://localhost:8100');
    }

    /**
     * Index satu dokumen ke RAG by doc_id.
     */
    public function indexDocument($docId)
    {
        try {
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/index/db/{$docId}");

            return $response->json() ?? ['error' => true, 'message' => 'Empty response from RAG service'];
        } catch (\Exception $e) {
            Log::error('RAG indexDocument error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * Cek status indexing.
     */
    public function getIndexStatus($jobKey)
    {
        try {
            $response = Http::timeout(10)
                ->get("{$this->baseUrl}/index/status/{$jobKey}");

            return $response->json() ?? ['status' => 'error', 'detail' => 'Empty response'];
        } catch (\Exception $e) {
            Log::error('RAG getIndexStatus error: ' . $e->getMessage());
            return ['status' => 'error', 'detail' => $e->getMessage()];
        }
    }

    /**
     * Ambil list doc_ids yang sudah ter-index.
     */
    public function getIndexedDocIds()
    {
        try {
            $response = Http::timeout(10)
                ->get("{$this->baseUrl}/index/indexed");

            $data = $response->json();
            return $data['doc_ids'] ?? [];
        } catch (\Exception $e) {
            Log::error('RAG getIndexedDocIds error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Hapus index dokumen.
     */
    public function deleteIndex($docId)
    {
        try {
            $response = Http::timeout(10)
                ->delete("{$this->baseUrl}/index/doc/{$docId}");

            return $response->json() ?? ['error' => true, 'message' => 'Empty response'];
        } catch (\Exception $e) {
            Log::error('RAG deleteIndex error: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * Query RAG - tanya jawab.
     *
     * @param string $question
     * @param string|null $jenisFile
     * @param string|null $bagian
     * @param int|null $docId
     * @param string|null $model AI model to use (e.g. gemini-2.0-flash)
     * @return array
     */
    public function query($question, $jenisFile = null, $bagian = null, $docId = null, $model = null)
    {
        try {
            $body = ['question' => $question];

            if ($jenisFile) $body['jenis_file'] = $jenisFile;
            if ($bagian) $body['bagian'] = $bagian;
            if ($docId) $body['doc_id'] = $docId;
            if ($model) $body['model'] = $model;

            Log::info('RAG query request', ['body' => $body]);

            $response = Http::timeout(120)
                ->post("{$this->baseUrl}/query", $body);

            Log::info('RAG query response status: ' . $response->status());

            if (!$response->successful()) {
                $errorBody = $response->json() ?? [];
                Log::error('RAG query failed', ['status' => $response->status(), 'body' => $errorBody]);
                return [
                    'error' => true,
                    'answer' => $errorBody['detail'] ?? 'RAG service returned error (HTTP ' . $response->status() . ')',
                    'sources' => []
                ];
            }

            $result = $response->json();

            if (is_null($result)) {
                return [
                    'error' => true,
                    'answer' => 'RAG service returned empty response',
                    'sources' => []
                ];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('RAG query error: ' . $e->getMessage());
            return [
                'error' => true,
                'answer' => 'Gagal menghubungi RAG service: ' . $e->getMessage(),
                'sources' => []
            ];
        }
    }

    /**
     * Health check.
     */
    public function healthCheck()
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/health");

            return $response->json() ?? ['status' => 'offline', 'message' => 'Empty response'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => $e->getMessage()];
        }
    }
}
