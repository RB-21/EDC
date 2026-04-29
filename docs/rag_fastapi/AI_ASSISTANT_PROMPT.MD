# Prompt untuk AI Assistant — Integrasi RAG ke EDC Laravel

Salin prompt di bawah ini dan kirim ke AI assistant yang akan mengerjakan integrasi Laravel.
Sertakan juga file `LARAVEL_INTEGRATION_GUIDE.md` sebagai konteks.

---

## PROMPT:

```
Saya ingin mengintegrasikan fitur AI RAG (Retrieval-Augmented Generation) ke aplikasi EDC Laravel 9.

## Konteks
- Saya sudah memiliki Python RAG microservice yang running di http://localhost:8100
- Service ini bisa mengindex dokumen PDF dari database MySQL, melakukan OCR untuk dokumen scan, dan menjawab pertanyaan berdasarkan konten dokumen
- Aplikasi EDC menggunakan Laravel 9, PHP 8, Blade + jQuery (BUKAN Vue/React), dan AdminLTE 3

## Yang Perlu Dibuat

### 1. Service Class
Buat `app/Services/RagService.php` sebagai wrapper HTTP client untuk RAG API.
Endpoint yang perlu di-wrapper:
- POST /index/db/{doc_id} — index dokumen
- GET /index/status/{job_key} — cek status indexing
- GET /index/indexed — list doc_ids yang sudah di-index
- DELETE /index/doc/{doc_id} — hapus index
- POST /query — tanya jawab RAG (dengan optional filter: jenis_file, bagian, doc_id)
- GET /health — health check

### 2. Controller + Routes
Buat `RagController.php` dengan routes:
- GET /rag/chat — halaman chat AI
- POST /rag/query — AJAX query RAG
- POST /rag/index/{docId} — AJAX index dokumen
- GET /rag/index/status/{jobKey} — AJAX cek status
- DELETE /rag/index/{docId} — AJAX hapus index
- GET /rag/indexed — AJAX list indexed docs

### 3. Tombol "Index AI" di Halaman Dokumen
Tambahkan tombol di view daftar dokumen dengan:
- Load list indexed doc_ids saat halaman dibuka
- Tombol berubah state: idle → processing (spinner) → done (check) / error (x)
- Polling status setiap 3 detik sampai selesai
- Pakai jQuery AJAX, jangan forget CSRF token

### 4. Chat UI
Buat `resources/views/rag/chat.blade.php` dengan:
- Chat interface (AdminLTE direct-chat style)
- Input pertanyaan + tombol kirim
- Filter opsional: dropdown jenis dokumen, input bagian
- Response menampilkan jawaban + sumber dokumen
- Loading spinner saat menunggu jawaban

### 5. Config & Environment
- Tambah `services.rag.base_url` di config/services.php
- Tambah `RAG_SERVICE_URL=http://localhost:8100` di .env

## File Referensi
Lihat file LARAVEL_INTEGRATION_GUIDE.md untuk detail lengkap termasuk contoh kode, API reference, dan database schema.

## Catatan
- Semua frontend pakai Blade + jQuery, BUKAN Vue/React
- UI framework: AdminLTE 3 (Bootstrap 4)
- CSRF token wajib di semua AJAX POST/DELETE
- Query RAG bisa 10-30 detik, set timeout 60 detik
- Indexing berjalan di background, perlu polling status
```
