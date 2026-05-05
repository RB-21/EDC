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

---

## 2026-05-05 09:10-09:35 (Asia/Jakarta)
Scope:
- Mengalihkan runtime AI service menjadi Vertex AI only.
- Menghapus jalur auth AI Studio/API key dari backend Python.
- Menyelaraskan test suite, env template, dan docs agar tidak ambigu soal backend aktif.

Perbaikan:
- Backend AI:
  - `get_client()` kini selalu membuat client `google-genai` dengan `vertexai=True`.
  - Validasi `GOOGLE_CLOUD_PROJECT` dan `GOOGLE_CLOUD_LOCATION` ditambahkan agar error auth/config lebih jelas.
  - Root endpoint kini mengembalikan `ai_backend=vertex_ai`.
- Operasional:
  - `.env.example` diubah menjadi Vertex-only.
  - README dan AI context diperbarui agar tidak lagi mengarahkan tim ke AI Studio.
- Testability:
  - `test_layers.py` diperbarui untuk label Vertex AI.
  - Perbaikan bug lama pada test suite: output `generate_answer()` sekarang dibaca dari `answer["text"]`.

Files:
- `../EDC AI RAG/app/config.py`
- `../EDC AI RAG/app/embedding.py`
- `../EDC AI RAG/main.py`
- `../EDC AI RAG/test_layers.py`
- `../EDC AI RAG/.env.example`
- `../EDC AI RAG/README.md`
- `../EDC AI RAG/AI_CONTEXT.md`

Verification:
- `python -m py_compile main.py app\\config.py app\\embedding.py app\\ocr.py test_layers.py` lulus.

Risks:
- Collection aktif saat ini masih memakai `gemini-embedding-2` (`edc_documents_v2`); perlu smoke test runtime untuk memastikan model yang sama berjalan baik via Vertex pada environment target.

---

## 2026-05-05 10:00-10:20 (Asia/Jakarta)
Scope:
- Investigasi `restart-rag-stack.ps1` yang timeout dengan pesan RAG tidak siap dalam 90 detik.
- Memperbaiki kompatibilitas startup setelah refactor Vertex-only.
- Menambahkan diagnostik log pada script restart agar kegagalan startup lebih terlihat.

Root cause:
- Service Python crash saat startup karena `.env` lama masih berisi `GEMINI_API_KEY`, sementara field tersebut sempat dihapus dari `Settings`.
- Setelah startup kompatibel lagi, health check menunjukkan `gcp_connected=false`.
- Verifikasi manual menemukan model `gemini-embedding-2` tidak tersedia / tidak diizinkan pada Vertex project `palm-reg4-dev` (`404 NOT_FOUND`).

Perbaikan:
- `app/config.py`:
  - `gemini_api_key` dikembalikan sebagai field deprecated agar `.env` lama tidak mematahkan startup.
- `scripts/restart-rag-stack.ps1`:
  - Tambah redirect stdout/stderr ke log file.
  - Jika proses RAG exit cepat atau timeout, script kini menampilkan tail log untuk diagnosis cepat.

Files:
- `../EDC AI RAG/app/config.py`
- `scripts/restart-rag-stack.ps1`

Verification:
- `uvicorn main:app` kini bisa start kembali.
- `restart-rag-stack.ps1` tidak lagi gagal karena crash startup tersembunyi.
- `/health` sekarang merespons, tetapi status masih `degraded` karena Vertex embedding model aktif belum accessible.

Risks:
- Query/indexing tetap belum usable penuh sampai model embedding Vertex diganti ke model yang tersedia atau akses model saat ini dibuka.

---

## 2026-05-05 10:25-10:40 (Asia/Jakarta)
Scope:
- Mengganti region Vertex AI aktif dari `us-central1` ke Singapore (`asia-southeast1`).
- Verifikasi ulang apakah `gemini-embedding-2` menjadi accessible setelah pindah region.

Perbaikan:
- Region runtime aktif diubah ke `asia-southeast1` pada config dan `.env`.
- Template env dan dokumentasi internal ikut diselaraskan ke Singapore region.

Files:
- `../EDC AI RAG/.env`
- `../EDC AI RAG/app/config.py`
- `../EDC AI RAG/.env.example`
- `../EDC AI RAG/README.md`
- `../EDC AI RAG/AI_CONTEXT.md`

Verification:
- Root endpoint tetap sehat dan melaporkan service running.
- `/health` masih `degraded` karena `gcp_connected=false`.
- Verifikasi manual `embed_text('test')` masih gagal `404 NOT_FOUND` untuk:
  - `projects/palm-reg4-dev/locations/asia-southeast1/publishers/google/models/gemini-embedding-2`

Risks:
- Pindah region saja tidak menyelesaikan masalah; indikasi kuat bahwa project/account belum punya akses efektif ke `gemini-embedding-2` di Vertex, walau region sudah diganti.

---

## 2026-05-05 10:45-10:55 (Asia/Jakarta)
Scope:
- Memperbaiki error script restart: `Cannot overwrite variable PID because it is read-only or constant`.
- Menambahkan auto-elevation agar script bisa relaunch sebagai Administrator saat dibutuhkan.

Root cause:
- Variabel loop memakai nama `$pid` yang bentrok dengan variabel bawaan PowerShell `$PID` (read-only).

Perbaikan:
- Ganti nama variabel loop dari `$pid` menjadi `$owningPid`.
- Tambah parameter `-NoElevate` dan logika auto-relauch `RunAs` jika belum admin.

Files:
- `scripts/restart-rag-stack.ps1`

Verification:
- `restart-rag-stack.ps1 -NoElevate` berjalan tanpa error `VariableNotWritable`.
- `/health` endpoint tetap merespons setelah restart.

Risks:
- Status health masih `degraded` karena isu akses model embedding Vertex (`gcp_connected=false`), terpisah dari isu script restart.

---

## 2026-05-05 10:55-11:10 (Asia/Jakarta)
Scope:
- Investigasi error query `400 FAILED_PRECONDITION`.
- Verifikasi jalur Vertex aktif untuk generation dan embedding secara terpisah.

Root cause:
- `.env` runtime ternyata sempat mengarah ke `GOOGLE_CLOUD_LOCATION=asia-southeast2` (Jakarta), bukan `asia-southeast1` (Singapore).
- Dengan region `asia-southeast2`, panggilan Vertex untuk `gemini-2.5-flash` dan embedding sama-sama gagal `400 FAILED_PRECONDITION`.
- Verifikasi one-off ke `asia-southeast1` menunjukkan:
  - `gemini-2.5-flash` berhasil dipanggil.
  - `text-embedding-005` berhasil dipanggil.
  - `gemini-embedding-001` berhasil dipanggil.

Perbaikan:
- Region runtime aktif dikembalikan ke `asia-southeast1` pada `.env`.
- RAG service direstart ulang dengan script restart.

Files:
- `../EDC AI RAG/.env`

Verification:
- Startup log kini menunjukkan `Location: asia-southeast1`.
- Error `FAILED_PRECONDITION` teridentifikasi sebagai akibat region `asia-southeast2`.
- Isu yang tersisa sekarang adalah kompatibilitas / akses model embedding aktif `gemini-embedding-2`, bukan lagi precondition region.

Risks:
- Query tetap belum sehat penuh karena collection aktif masih memakai `gemini-embedding-2`, sedangkan model Vertex yang terverifikasi berhasil saat ini adalah `gemini-embedding-001` atau `text-embedding-005`.

---

## 2026-05-05 11:10-11:25 (Asia/Jakarta)
Scope:
- Mengganti embedding runtime ke model Vertex yang benar-benar tersedia.
- Memisahkan collection baru agar vector lama tidak tercampur dengan embedding space baru.

Perbaikan:
- Runtime embedding diubah menjadi `gemini-embedding-001` (3072 dim).
- Collection aktif diubah menjadi `edc_documents_vertex_v1`.
- Config default, `.env`, `.env.example`, dan README diselaraskan dengan model/collection baru.

Files:
- `../EDC AI RAG/app/config.py`
- `../EDC AI RAG/.env`
- `../EDC AI RAG/.env.example`
- `../EDC AI RAG/README.md`

Verification:
- Restart service dijalankan ulang setelah perubahan config.
- Health check diverifikasi ulang setelah switch model.

Risks:
- Collection baru masih kosong; perlu reindex dokumen agar query kembali relevan.
- Collection lama `edc_documents_v2` tidak kompatibel untuk query baru karena embedding space berbeda.

---

## 2026-05-05 11:25-11:40 (Asia/Jakarta)
Scope:
- Memastikan pipeline tetap cocok untuk PDF yang berisi gambar, tabel, dan scan setelah pindah ke model text embedding Vertex.

Perbaikan:
- `embed_chunks()` untuk `image` chunk kini:
  - mencoba embed gambar langsung terlebih dahulu
  - jika model aktif tidak mendukung image-bytes embedding, fallback ke OCR
  - hasil OCR disimpan ke `chunk.content` lalu di-embed sebagai teks
- Payload Qdrant untuk `image` chunk kini menyimpan hasil OCR jika tersedia, sehingga tetap searchable oleh hybrid search.

Files:
- `../EDC AI RAG/app/embedding.py`
- `../EDC AI RAG/app/vector_store.py`
- `../EDC AI RAG/README.md`

Verification:
- `py_compile` lulus untuk file yang diubah.
- Uji sintetis gambar berisi teks:
  - direct image embedding gagal pada model text embedding
  - OCR fallback berhasil
  - chunk image tetap menghasilkan vector 3072 dim dan teks OCR tersimpan di `chunk.content`

Risks:
- Ini bukan image-bytes embedding murni; kualitas retrieval gambar bergantung pada kualitas OCR.

---

## 2026-05-05 11:45-11:50 (Asia/Jakarta)
Scope:
- Menukar urutan tampilan UI antara `saran pertanyaan lanjutan` dan `sumber dokumen`.

Perbaikan:
- Urutan render message AI di chat diubah menjadi:
  - Jawaban
  - Usage token
  - Saran pertanyaan lanjutan
  - Sumber dokumen
- Perubahan diterapkan untuk:
  - pesan baru dari request query
  - pesan riwayat saat load session

Files:
- `resources/views/admin/rag/chat.blade.php`

Verification:
- Struktur render JS diperbarui konsisten pada dua jalur append message AI.

Risks:
- Tidak ada risiko backend; perubahan murni urutan presentasi di frontend.

---

## 2026-05-05 11:55-12:05 (Asia/Jakarta)
Scope:
- Menempatkan informasi token usage di bagian paling bawah bubble jawaban AI.
- Mengecilkan badge jenis sumber dokumen pada kartu sumber.

Perbaikan:
- Urutan render message AI diubah menjadi:
  - Jawaban
  - Saran pertanyaan lanjutan
  - Sumber dokumen
  - Token usage (paling bawah)
- Selector CSS badge sumber diperbaiki ke lokasi render yang benar (`.source-title .badge`) dan ukuran font/padding diperkecil.

Files:
- `resources/views/admin/rag/chat.blade.php`

Verification:
- Urutan append message AI diperbarui konsisten pada jalur load riwayat dan jawaban baru.
- Badge jenis sumber kini memakai style small yang benar-benar ter-apply.

Risks:
- Tidak ada dampak backend; perubahan hanya presentasi frontend.

---

## 2026-05-05 12:05-12:15 (Asia/Jakarta)
Scope:
- Menyembunyikan blok sumber dokumen ketika jawaban AI menyatakan informasi tidak ditemukan.

Perbaikan:
- Ditambahkan guard frontend `shouldHideSources(answerText)` pada rendering bubble AI.
- Jika jawaban mengandung pola "informasi tidak ditemukan" / "tidak ada dokumen relevan", `formatSources()` tidak ditampilkan.
- Diterapkan pada:
  - render jawaban baru
  - render riwayat session

Files:
- `resources/views/admin/rag/chat.blade.php`

Verification:
- Jalur append message AI kini kondisional terhadap isi jawaban.

Risks:
- Rule berbasis frasa jawaban; jika wording model berubah total, perlu update pattern.
