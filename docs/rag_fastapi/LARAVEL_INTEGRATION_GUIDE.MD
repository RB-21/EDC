# EDC Laravel Integration Guide — AI RAG Service

## 🤖 Prompt untuk AI Assistant

> Gunakan dokumen ini sebagai konteks saat meminta AI assistant mengintegrasikan fitur AI RAG ke aplikasi EDC Laravel.

---

## Context & Overview

Kami memiliki **Python RAG microservice** yang sudah running di `http://localhost:8100`. Service ini:
- Mengindex dokumen PDF dari database `e_edc` ke vector database (Qdrant)
- Melakukan OCR otomatis untuk dokumen scan
- Menjawab pertanyaan berdasarkan konten dokumen yang ter-index
- Mendukung hybrid search (vector + keyword) dan metadata filtering

**Tujuan integrasi ini:** Menambahkan 2 fitur ke aplikasi EDC Laravel:
1. **Tombol "Index AI"** di halaman dokumen — untuk mengirim dokumen ke RAG service
2. **Chat AI** — UI chat untuk tanya-jawab berdasarkan dokumen yang sudah di-index

---

## Teknologi EDC Laravel

- **Framework:** Laravel 9 (PHP 8.0)
- **Frontend:** Blade Templates + jQuery (AJAX) — **BUKAN** Vue/React
- **HTTP Client:** GuzzleHTTP (sudah terinstall)
- **UI Framework:** AdminLTE 3 (Bootstrap 4)
- **Database:** MySQL (`e_edc`)

---

## RAG API Reference

Base URL: `http://localhost:8100` (akan sama server saat deploy)

### 1. Index Dokumen dari Database

```
POST /index/db/{doc_id}
```

**Deskripsi:** Index satu dokumen dari tabel `dokumen` ke vector database. Service akan:
- Query MySQL untuk ambil metadata
- Download PDF dari `edc.ptpn4.com/storage/{filename}`
- Extract teks + OCR (jika scan)
- Embed dan simpan ke Qdrant

**Response:** (langsung, processing di background)
```json
{
    "message": "Dokumen 'Manajemen Log' sedang di-index...",
    "doc_id": 1615,
    "job_key": "doc_1615",
    "nomor": "SOP-DPTI-021",
    "judul": "Manajemen Log"
}
```

### 2. Cek Status Indexing

```
GET /index/status/{job_key}
```

**Response (processing):**
```json
{
    "status": "processing",
    "detail": "Mengekstrak konten (termasuk OCR jika scan)...",
    "total_chunks": 0,
    "updated_at": "2026-04-28 09:50:09"
}
```

**Response (completed):**
```json
{
    "status": "completed",
    "detail": "Berhasil: 11 chunks. Pengendalian dan Pengelolaan...",
    "total_chunks": 11,
    "updated_at": "2026-04-28 09:51:41"
}
```

**Response (error):**
```json
{
    "status": "error",
    "detail": "Error: File not found...",
    "total_chunks": 0,
    "updated_at": "..."
}
```

### 3. Daftar Dokumen yang Sudah Di-Index

```
GET /index/indexed
```

**Response:**
```json
{
    "total": 2,
    "doc_ids": [1615, 1620]
}
```

### 4. Hapus Index Dokumen

```
DELETE /index/doc/{doc_id}
```

**Response:**
```json
{
    "message": "11 chunks dihapus untuk doc_id=1620.",
    "doc_id": 1620,
    "deleted_count": 11
}
```

### 5. Query / Tanya-Jawab RAG

```
POST /query
Content-Type: application/json
```

**Request Body:**
```json
{
    "question": "Apa itu sistem In-Tank?",
    "jenis_file": "se",          // optional: filter by jenis (sop, ik, sk, se, si, ph, sp, ll)
    "bagian": "BOM",             // optional: filter by bagian
    "doc_id": 1620               // optional: filter by specific document
}
```

**Response:**
```json
{
    "question": "Apa itu sistem In-Tank?",
    "answer": "Sistem In-tank adalah alat ukur volume tangki timbun otomatis...",
    "sources": [
        {
            "filename": "Bb5k8T22Xr...pdf",
            "page": 5,
            "type": "text",
            "score": 0.690,
            "doc_id": 1620,
            "nomor": "DRPL/SE/15/III/2026",
            "judul": "Pengendalian dan Pengelolaan Operasional...",
            "jenis_file_kode": "se"
        }
    ]
}
```

### 6. Health Check

```
GET /health
```

**Response:**
```json
{
    "status": "healthy",
    "qdrant_connected": true,
    "message": "All systems operational."
}
```

---

## Database Schema (Tabel Terkait)

### Tabel `dokumen`
```sql
CREATE TABLE `dokumen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jenis_file_kode` varchar(255),       -- FK ke master_jenis_file.kode
  `nomor` varchar(255),                  -- Nomor dokumen (DRPL/SE/15/III/2026)
  `judul` longtext,                      -- Judul dokumen
  `tanggal` date,                        -- Tanggal dokumen
  `bagian` varchar(255),                 -- Bagian/unit (BOM, 06.04, dll)
  `level` varchar(255),                  -- Level akses (1,2,3,4)
  `dokumen` varchar(255),                -- Nama file hash (xxxx.pdf)
  `status_dokumen_id` int(11),           -- 1=Berlaku, 2=Kadaluarsa
  `status_perubahan_id` int(11),
  `dokumen_base_id` int(11),
  `dokumen_prev_id` int(11),
  `dokumen_next_id` int(11),
  `uploaded_by` int(11),
  `created_at` timestamp,
  `updated_at` timestamp,
  PRIMARY KEY (`id`)
);
```

### Tabel `master_jenis_file`
| kode | singkatan | kepanjangan |
|------|-----------|-------------|
| sop | SOP | Standar Operasional Procedure |
| ik | IK | Instruksi Kerja |
| sk | SK | Surat Keputusan |
| se | SE | Surat Edaran |
| si | SI | Surat Instruksi |
| ph | PH | Pedoman Holding |
| sp | SP | Sertifikasi Perusahaan |
| ll | LL | Dokumen Eksternal |

### Tabel `master_status_dokumen`
| id | nama | alias |
|----|------|-------|
| 1 | berlaku | Berlaku |
| 2 | kadaluarsa | Kadaluarsa |

---

## Yang Perlu Diimplementasikan di Laravel

### 1. Service Class: `app/Services/RagService.php`

Service ini sebagai wrapper untuk semua HTTP calls ke Python RAG API.

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RagService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.rag.base_url', 'http://localhost:8100');
    }

    /**
     * Index satu dokumen ke RAG by doc_id.
     */
    public function indexDocument(int $docId): array
    {
        $response = Http::timeout(30)
            ->post("{$this->baseUrl}/index/db/{$docId}");

        return $response->json();
    }

    /**
     * Cek status indexing.
     */
    public function getIndexStatus(string $jobKey): array
    {
        $response = Http::timeout(10)
            ->get("{$this->baseUrl}/index/status/{$jobKey}");

        return $response->json();
    }

    /**
     * Ambil list doc_ids yang sudah ter-index.
     */
    public function getIndexedDocIds(): array
    {
        $response = Http::timeout(10)
            ->get("{$this->baseUrl}/index/indexed");

        $data = $response->json();
        return $data['doc_ids'] ?? [];
    }

    /**
     * Hapus index dokumen.
     */
    public function deleteIndex(int $docId): array
    {
        $response = Http::timeout(10)
            ->delete("{$this->baseUrl}/index/doc/{$docId}");

        return $response->json();
    }

    /**
     * Query RAG - tanya jawab.
     */
    public function query(string $question, ?string $jenisFile = null, ?string $bagian = null, ?int $docId = null): array
    {
        $body = ['question' => $question];

        if ($jenisFile) $body['jenis_file'] = $jenisFile;
        if ($bagian) $body['bagian'] = $bagian;
        if ($docId) $body['doc_id'] = $docId;

        $response = Http::timeout(60)
            ->post("{$this->baseUrl}/query", $body);

        return $response->json();
    }

    /**
     * Health check.
     */
    public function healthCheck(): array
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/health");
            return $response->json();
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => $e->getMessage()];
        }
    }
}
```

### 2. Config: `config/services.php`

Tambahkan di array config:
```php
'rag' => [
    'base_url' => env('RAG_SERVICE_URL', 'http://localhost:8100'),
],
```

### 3. Environment Variable: `.env`

```
RAG_SERVICE_URL=http://localhost:8100
```

### 4. Controller: `app/Http/Controllers/RagController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Services\RagService;
use Illuminate\Http\Request;

class RagController extends Controller
{
    protected RagService $ragService;

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
        return view('rag.chat', compact('health'));
    }

    /**
     * AJAX: Kirim pertanyaan ke RAG.
     */
    public function query(Request $request)
    {
        $request->validate(['question' => 'required|string|min:3']);

        $result = $this->ragService->query(
            $request->question,
            $request->jenis_file,
            $request->bagian,
            $request->doc_id ? (int) $request->doc_id : null,
        );

        return response()->json($result);
    }

    /**
     * AJAX: Index dokumen ke RAG.
     */
    public function indexDocument(int $docId)
    {
        $result = $this->ragService->indexDocument($docId);
        return response()->json($result);
    }

    /**
     * AJAX: Cek status indexing.
     */
    public function indexStatus(string $jobKey)
    {
        $result = $this->ragService->getIndexStatus($jobKey);
        return response()->json($result);
    }

    /**
     * AJAX: Hapus index dokumen.
     */
    public function deleteIndex(int $docId)
    {
        $result = $this->ragService->deleteIndex($docId);
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
```

### 5. Routes: `routes/web.php`

```php
// RAG AI Routes
Route::middleware('auth')->prefix('rag')->group(function () {
    Route::get('/chat', [App\Http\Controllers\RagController::class, 'chatPage'])->name('rag.chat');
    Route::post('/query', [App\Http\Controllers\RagController::class, 'query'])->name('rag.query');
    Route::post('/index/{docId}', [App\Http\Controllers\RagController::class, 'indexDocument'])->name('rag.index');
    Route::get('/index/status/{jobKey}', [App\Http\Controllers\RagController::class, 'indexStatus'])->name('rag.index.status');
    Route::delete('/index/{docId}', [App\Http\Controllers\RagController::class, 'deleteIndex'])->name('rag.delete');
    Route::get('/indexed', [App\Http\Controllers\RagController::class, 'indexedDocuments'])->name('rag.indexed');
});
```

### 6. Tombol "Index AI" di Halaman Dokumen

Tambahkan tombol ini di view daftar dokumen (sebelah tombol Lihat/Download yang sudah ada):

```html
<!-- Tombol Index AI (per dokumen) -->
<button class="btn btn-sm btn-outline-primary btn-index-ai"
        data-doc-id="{{ $dokumen->id }}"
        data-doc-title="{{ Str::limit($dokumen->judul, 50) }}"
        title="Index ke AI RAG">
    <i class="fas fa-robot"></i> Index AI
</button>

<!-- Tombol akan berubah state saat processing -->
<!-- State: idle → processing (spinner) → done (check) / error (x) -->
```

**JavaScript untuk tombol (jQuery):**
```javascript
$(document).ready(function() {
    // Load indexed doc_ids saat halaman dibuka
    let indexedDocIds = [];

    $.get("{{ route('rag.indexed') }}", function(data) {
        indexedDocIds = data.doc_ids || [];
        // Tandai tombol yang sudah di-index
        $('.btn-index-ai').each(function() {
            const docId = $(this).data('doc-id');
            if (indexedDocIds.includes(docId)) {
                $(this).removeClass('btn-outline-primary').addClass('btn-success')
                       .html('<i class="fas fa-check"></i> Indexed');
            }
        });
    });

    // Handler klik tombol Index AI
    $(document).on('click', '.btn-index-ai', function() {
        const btn = $(this);
        const docId = btn.data('doc-id');
        const docTitle = btn.data('doc-title');

        if (btn.hasClass('btn-success')) {
            // Sudah di-index, konfirmasi re-index
            if (!confirm(`Dokumen "${docTitle}" sudah di-index. Re-index ulang?`)) return;
        }

        // Set ke loading state
        btn.prop('disabled', true)
           .removeClass('btn-outline-primary btn-success btn-danger')
           .addClass('btn-warning')
           .html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        // Kirim request index
        $.ajax({
            url: `/rag/index/${docId}`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(data) {
                // Poll status sampai selesai
                pollIndexStatus(data.job_key, btn, docId);
            },
            error: function(xhr) {
                btn.prop('disabled', false)
                   .removeClass('btn-warning').addClass('btn-danger')
                   .html('<i class="fas fa-times"></i> Error');
                alert('Gagal memulai indexing: ' + (xhr.responseJSON?.detail || 'Unknown error'));
            }
        });
    });

    function pollIndexStatus(jobKey, btn, docId) {
        const interval = setInterval(function() {
            $.get(`/rag/index/status/${jobKey}`, function(data) {
                if (data.status === 'completed') {
                    clearInterval(interval);
                    btn.prop('disabled', false)
                       .removeClass('btn-warning').addClass('btn-success')
                       .html(`<i class="fas fa-check"></i> Indexed (${data.total_chunks})`);
                    indexedDocIds.push(docId);
                } else if (data.status === 'error') {
                    clearInterval(interval);
                    btn.prop('disabled', false)
                       .removeClass('btn-warning').addClass('btn-danger')
                       .html('<i class="fas fa-times"></i> Error');
                    alert('Indexing gagal: ' + data.detail);
                }
                // else masih processing, lanjut poll
            });
        }, 3000); // Poll setiap 3 detik
    }
});
```

### 7. Blade View Chat: `resources/views/rag/chat.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Chat AI - EDC Document Assistant')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-robot"></i> EDC AI Document Assistant
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $health['status'] === 'healthy' ? 'success' : 'danger' }}">
                            {{ $health['status'] }}
                        </span>
                    </div>
                </div>

                <div class="card-body" style="height: 500px; overflow-y: auto;" id="chat-container">
                    <!-- Pesan welcome -->
                    <div class="direct-chat-msg">
                        <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-left">AI Assistant</span>
                        </div>
                        <div class="direct-chat-text bg-light">
                            Halo! Saya AI Assistant EDC. Saya bisa menjawab pertanyaan berdasarkan
                            dokumen yang sudah di-index (SOP, SE, SK, dll).
                            <br><br>
                            Silakan ketik pertanyaan Anda di bawah.
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <!-- Filter opsional -->
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <select class="form-control form-control-sm" id="filter-jenis">
                                <option value="">Semua Jenis Dokumen</option>
                                <option value="sop">SOP</option>
                                <option value="ik">Instruksi Kerja</option>
                                <option value="sk">Surat Keputusan</option>
                                <option value="se">Surat Edaran</option>
                                <option value="si">Surat Instruksi</option>
                                <option value="ph">Pedoman Holding</option>
                                <option value="sp">Sertifikasi Perusahaan</option>
                                <option value="ll">Dokumen Eksternal</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm"
                                   id="filter-bagian" placeholder="Filter bagian (opsional)">
                        </div>
                    </div>

                    <!-- Input pertanyaan -->
                    <form id="chat-form">
                        <div class="input-group">
                            <input type="text" class="form-control" id="question-input"
                                   placeholder="Ketik pertanyaan tentang dokumen..."
                                   autocomplete="off">
                            <span class="input-group-append">
                                <button type="submit" class="btn btn-primary" id="send-btn">
                                    <i class="fas fa-paper-plane"></i> Kirim
                                </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const chatContainer = $('#chat-container');
    const questionInput = $('#question-input');

    $('#chat-form').on('submit', function(e) {
        e.preventDefault();

        const question = questionInput.val().trim();
        if (!question) return;

        // Tampilkan pesan user
        appendMessage('user', question);
        questionInput.val('');

        // Tampilkan loading
        const loadingId = 'loading-' + Date.now();
        appendMessage('ai', '<i class="fas fa-spinner fa-spin"></i> Sedang mencari jawaban...', loadingId);

        // Disable input sementara
        $('#send-btn').prop('disabled', true);

        // Kirim ke API
        $.ajax({
            url: "{{ route('rag.query') }}",
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            contentType: 'application/json',
            data: JSON.stringify({
                question: question,
                jenis_file: $('#filter-jenis').val() || null,
                bagian: $('#filter-bagian').val() || null,
            }),
            success: function(data) {
                $(`#${loadingId}`).remove();

                // Format jawaban + sources
                let answer = data.answer || 'Tidak ada jawaban.';
                let sourcesHtml = '';
                if (data.sources && data.sources.length > 0) {
                    sourcesHtml = '<hr><small class="text-muted"><b>Sumber:</b><br>';
                    data.sources.forEach(function(s) {
                        const jenis = (s.jenis_file_kode || '').toUpperCase();
                        sourcesHtml += `📄 [${jenis}] ${s.nomor || ''} - hal.${s.page}<br>`;
                    });
                    sourcesHtml += '</small>';
                }

                appendMessage('ai', answer.replace(/\n/g, '<br>') + sourcesHtml);
            },
            error: function(xhr) {
                $(`#${loadingId}`).remove();
                appendMessage('ai', '❌ Error: ' + (xhr.responseJSON?.detail || 'Gagal mendapatkan jawaban.'));
            },
            complete: function() {
                $('#send-btn').prop('disabled', false);
                questionInput.focus();
            }
        });
    });

    function appendMessage(role, content, id = '') {
        const isAi = role === 'ai';
        const name = isAi ? 'AI Assistant' : 'Anda';
        const bgClass = isAi ? 'bg-light' : 'bg-primary text-white';
        const alignClass = isAi ? '' : 'right';
        const idAttr = id ? `id="${id}"` : '';

        const html = `
            <div class="direct-chat-msg ${alignClass}" ${idAttr}>
                <div class="direct-chat-infos clearfix">
                    <span class="direct-chat-name float-${isAi ? 'left' : 'right'}">${name}</span>
                </div>
                <div class="direct-chat-text ${bgClass}">${content}</div>
            </div>
        `;

        chatContainer.append(html);
        chatContainer.scrollTop(chatContainer[0].scrollHeight);
    }
});
</script>
@endpush
```

### 8. Sidebar Menu

Tambahkan di sidebar layout:
```html
<li class="nav-item">
    <a href="{{ route('rag.chat') }}" class="nav-link {{ request()->routeIs('rag.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-robot"></i>
        <p>AI Assistant</p>
    </a>
</li>
```

---

## Checklist Implementasi

- [ ] Buat `app/Services/RagService.php`
- [ ] Tambah config `services.rag` di `config/services.php`
- [ ] Tambah `RAG_SERVICE_URL` di `.env`
- [ ] Buat `app/Http/Controllers/RagController.php`
- [ ] Tambah routes di `routes/web.php`
- [ ] Buat view `resources/views/rag/chat.blade.php`
- [ ] Tambah tombol "Index AI" di view daftar dokumen
- [ ] Tambah JavaScript polling untuk status indexing
- [ ] Tambah link "AI Assistant" di sidebar
- [ ] Test end-to-end

---

## Catatan Penting

1. **CSRF Token:** Semua AJAX POST/DELETE harus menyertakan `X-CSRF-TOKEN` header
2. **Timeout:** Query RAG bisa memakan waktu ~10-30 detik tergantung OCR, set timeout 60 detik
3. **Rate Limit:** Indexing OCR memakan biaya API (~Rp 32/halaman scan), jangan index semua sekaligus
4. **Status Polling:** Poll status indexing setiap 3 detik, stop setelah `completed` atau `error`
5. **Pastikan** Python RAG service (`http://localhost:8100`) running sebelum akses fitur AI
