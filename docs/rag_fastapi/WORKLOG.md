# Worklog

## 2026-05-04 13:00-14:35 (Asia/Jakarta)
Scope:
- Fixed RAG connectivity incidents (`cURL error 7`, `WinError 10061`)
- Added restart automation and troubleshooting docs

Files:
- `scripts/restart-rag-stack.ps1`
- `docs/rag_fastapi/RAG_TROUBLESHOOTING_AND_RECOVERY.md`
- `resources/views/admin/rag/chat.blade.php`
- `app/Services/RagService.php`

Verification:
- Host health check OK
- Container->host RAG endpoint reachable
- Query endpoint reachable

Risks:
- Potential stale process recurrence if manual starts bypass restart script

---

## 2026-05-04 14:00-14:34 (Asia/Jakarta)
Scope:
- Implemented AI chat core features:
  - session history
  - token usage persistence
  - user token balance deduction
  - per-user model whitelist

Files:
- `app/Http/Controllers/Admin/RagController.php`
- `app/Http/Controllers/Admin/UserController.php`
- `app/Models/User.php`
- `app/Models/AiChatSession.php`
- `app/Models/AiChatMessage.php`
- `routes/web.php`
- `config/services.php`
- `resources/views/admin/rag/chat.blade.php`
- `resources/views/admin/user/index.blade.php`
- `database/migrations/2026_05_04_140000_add_ai_settings_to_users_table.php`
- `database/migrations/2026_05_04_140100_create_ai_chat_sessions_table.php`
- `database/migrations/2026_05_04_140200_create_ai_chat_messages_table.php`
- `docs/rag_fastapi/AI_CHAT_FEATURES.md`

Verification:
- PHP syntax checks passed
- Migrations applied (with legacy workaround)
- Route list includes new session endpoints

Risks:
- CLI output noisy due to deprecated warnings in legacy dependency stack

---

## 2026-05-04 14:35-14:50 (Asia/Jakarta)
Scope:
- Added manual MySQL migration SQL
- Simplified chat filters (remove jenis/bagian)
- Full-width/full-height chat layout
- Hardened frontend error handling for non-string answers (prevent showing `true`)
- Added persistent documentation policy

Files:
- `docs/rag_fastapi/sql/2026_05_04_ai_chat_schema.sql`
- `resources/views/admin/rag/chat.blade.php`
- `AGENTS.md`
- `docs/rag_fastapi/HANDOVER_STATUS.md`
- `docs/rag_fastapi/NEXT_STEPS.md`

Verification:
- Blade file updated and references for removed filters cleaned
- SQL script stored in docs

Risks:
- Needs browser refresh and UI smoke test for final visual confirmation

---

## 2026-05-04 14:40 (+07:00)
Scope:
- Menetapkan kebijakan dokumentasi progres permanen lintas sesi + menambah SQL migration docs + final UI simplification/fullscreen.

Files:
- AGENTS.md, docs/rag_fastapi/HANDOVER_STATUS.md, docs/rag_fastapi/WORKLOG.md, docs/rag_fastapi/NEXT_STEPS.md, docs/rag_fastapi/sql/2026_05_04_ai_chat_schema.sql, scripts/append-progress-log.ps1, resources/views/admin/rag/chat.blade.php

Verification:
- Semua file terbentuk dan terupdate; filter jenis/bagian sudah tidak direferensikan; model selector tetap aktif.

Risks:
- Perlu refresh browser untuk melihat layout final.

---

## 2026-05-04 15:25-15:45 (Asia/Jakarta)
Scope:
- Memperjelas label token di UI menjadi `Token Balance`
- Merapatkan spacing teks chat agar tampilan jawaban lebih compact
- Memfilter sumber dokumen dengan similarity rendah agar tidak ikut tampil
  di daftar sumber

Files:
- `resources/views/admin/rag/chat.blade.php`
- `../EDC AI RAG/main.py`
- `../EDC AI RAG/app/config.py`

Verification:
- Python syntax check lulus untuk `main.py` dan `app/config.py`
- Diff UI menunjukkan label token dan typography spacing sudah diperbarui
- Filter sumber aktif di backend (retrieval filtering) dan frontend (guard rendering)

Risks:
- Threshold relevance default (ratio 0.70) mungkin perlu tuning berdasarkan pola query riil pengguna.

---

## 2026-05-04 16:05-16:35 (Asia/Jakarta)
Scope:
- Menambahkan pengaturan instruction RAG langsung dari UI chat EDC
- Memastikan jawaban AI menampilkan ringkasan dokumen utama
- Memperbaiki relevansi source dokumen agar tidak muncul dokumen lain yang tidak terkait

Root cause yang ditemukan:
- Pertanyaan user sebelumnya disisipi katalog semua dokumen ter-index, sehingga retrieval bias
  dan token prompt membengkak.

Perbaikan:
- Hapus injeksi katalog dokumen dari pertanyaan query (`RagController`).
- Tambah field `instruction` dari UI -> Laravel -> FastAPI -> prompt generator.
- Prompt generator mewajibkan blok `Ringkasan Dokumen`.
- Batasi source/context ke dokumen unik utama (`source_max_documents=1`) setelah similarity filter.

Files:
- `app/Http/Controllers/Admin/RagController.php`
- `app/Services/RagService.php`
- `config/services.php`
- `resources/views/admin/rag/chat.blade.php`
- `../EDC AI RAG/app/models.py`
- `../EDC AI RAG/app/embedding.py`
- `../EDC AI RAG/app/config.py`
- `../EDC AI RAG/main.py`

Verification:
- `php -l` lulus untuk controller + service.
- Python syntax check lulus untuk `main.py`, `embedding.py`, `models.py`, `config.py`.

Risks:
- Karena `source_max_documents=1`, jawaban pertanyaan komparatif multi-dokumen bisa jadi terlalu fokus ke satu dokumen.

---

## 2026-05-04 16:40-17:10 (Asia/Jakarta)
Scope:
- Mengubah mekanisme agar template prompt utama RAG (yang sebelumnya hardcoded di `embedding.py`)
  bisa diedit dari pengaturan admin EDC.
- Menghapus input instruction dari halaman chat user agar kontrol prompt terpusat di admin settings.

Perubahan utama:
- Tambah tabel settings AI:
  - migration Laravel: `database/migrations/2026_05_04_170000_create_ai_settings_table.php`
  - SQL manual: `docs/rag_fastapi/sql/2026_05_04_ai_prompt_settings.sql`
- Tambah model `AiSetting` untuk key-value prompt config.
- Tambah halaman admin:
  - `GET /admin/rag/settings`
  - `POST /admin/rag/settings`
  - view: `resources/views/admin/rag/settings.blade.php`
- Query flow EDC sekarang mengirim `prompt_template` ke FastAPI.
- FastAPI `generate_answer` kini menerima `prompt_template` dan merender placeholder:
  - `{{CONTEXT_BLOCK}}`
  - `{{QUESTION}}`
  - optional `{{INSTRUCTION}}`

Files:
- `app/Models/AiSetting.php`
- `database/migrations/2026_05_04_170000_create_ai_settings_table.php`
- `app/Http/Controllers/Admin/RagController.php`
- `app/Services/RagService.php`
- `routes/web.php`
- `resources/views/admin/rag/settings.blade.php`
- `resources/views/admin/rag/chat.blade.php`
- `resources/views/admin/template.blade.php`
- `config/services.php`
- `../EDC AI RAG/app/models.py`
- `../EDC AI RAG/main.py`
- `../EDC AI RAG/app/embedding.py`
- `docs/rag_fastapi/sql/2026_05_04_ai_prompt_settings.sql`

Verification:
- `php -l` lulus untuk controller/service/model baru.
- Python syntax check lulus untuk `embedding.py`, `main.py`, `models.py`.

Risks:
- Template prompt yang diubah admin bisa menurunkan kualitas jawaban jika struktur terlalu jauh dari default.
- Placeholder wajib (`{{CONTEXT_BLOCK}}`, `{{QUESTION}}`) sudah divalidasi saat simpan.

---

## 2026-05-04 17:15-17:30 (Asia/Jakarta)
Scope:
- Menambahkan indikator loading saat user memilih riwayat percakapan.
- Menormalkan tampilan daftar tanggal agar tidak terlihat nomor ganda seperti `1. 1 Januari`.

Perbaikan:
- `sessionSelect` kini menampilkan state "Memuat riwayat..." saat fetch daftar session.
- Saat load isi session, chat menampilkan bubble loading dengan spinner
  "Memuat riwayat percakapan...".
- Input chat dan aksi session dinonaktifkan sementara untuk mencegah interaksi awkward.
- Formatter jawaban menormalisasi pola daftar tanggal Indonesia dari format
  `N. DD <Bulan>` menjadi bullet agar tidak terbaca dobel nomor.

Files:
- `resources/views/admin/rag/chat.blade.php`

Verification:
- Script front-end berhasil diupdate tanpa error syntax pada struktur Blade/JS.

---

## 2026-05-04 17:40-18:00 (Asia/Jakarta)
Scope:
- Memastikan default retrieval hanya memakai satu dokumen utama (top similarity).
- Memastikan sumber dokumen di UI hanya menampilkan dokumen utama.
- Memaksa output ringkasan dokumen tampil lebih lengkap secara default.

Perbaikan:
- Backend RAG:
  - Setelah hybrid search awal, service mengunci dokumen utama berdasarkan score tertinggi.
  - Service melakukan re-fetch konteks tambahan hanya untuk dokumen utama (top-1 doc).
- Frontend chat:
  - Guard filtering sumber kini hanya mengizinkan satu dokumen (dokumen utama) tampil.
- Prompt enforcement:
  - Ditambahkan aturan wajib sistem agar `Ringkasan Dokumen` berisi minimal 5 poin.

Files:
- `../EDC AI RAG/main.py`
- `../EDC AI RAG/app/config.py`
- `../EDC AI RAG/app/vector_store.py`
- `../EDC AI RAG/app/embedding.py`
- `resources/views/admin/rag/chat.blade.php`

Risks:
- Untuk pertanyaan yang memang butuh perbandingan antar dokumen, mode top-1 doc bisa terasa terlalu ketat.

---

## 2026-05-04 18:05-18:35 (Asia/Jakarta)
Scope:
- Mengubah mode strict satu dokumen menjadi mode multi-dokumen berbasis relevansi.
- Menambahkan fitur saran pertanyaan lanjutan pada response dan UI chat.

Perbaikan:
- Backend retrieval:
  - Dokumen utama tetap diprioritaskan.
  - Dokumen kedua/ketiga bisa ikut jika skor dokumen mendekati dokumen utama (`source_related_doc_score_ratio`).
  - Konteks diambil per dokumen terpilih (`source_context_per_document`).
- Backend generation:
  - Prompt kini mewajibkan blok `[FOLLOW_UP_QUESTIONS]`.
  - Parser mengekstrak 3 saran pertanyaan dari output model.
- Frontend:
  - Sumber dokumen ditampilkan berdasarkan relevansi dokumen (bukan paksa satu dokumen).
  - Saran pertanyaan lanjutan ditampilkan sebagai tombol klik otomatis isi input.
- Persistence:
  - `follow_up_questions` disimpan di `meta` assistant message dan tampil saat membuka riwayat.

Files:
- `../EDC AI RAG/main.py`
- `../EDC AI RAG/app/config.py`
- `../EDC AI RAG/app/embedding.py`
- `../EDC AI RAG/app/models.py`
- `resources/views/admin/rag/chat.blade.php`
- `app/Http/Controllers/Admin/RagController.php`

Risks:
- Jika model tidak mengikuti format `[FOLLOW_UP_QUESTIONS]`, fallback saat ini adalah daftar kosong.
