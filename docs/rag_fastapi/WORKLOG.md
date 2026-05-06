# Worklog

## 2026-05-06 11:05-11:45 (Asia/Jakarta)
Scope:
- Menerapkan arsitektur intent routing yang lebih rapi untuk chat RAG:
  - active document context per session
  - bypass katalog saat user merujuk ke `dokumen ini` / `surat ini`
  - konfigurasi pattern intent routing yang bisa diubah dari dashboard EDC
- Menyiapkan rollout SQL manual saja, tanpa migration Laravel baru.
- Menambahkan renderer markdown table di chat utama dan widget agar output tabel dari model tidak tampil sebagai plain text pipe markdown.

Files:
- `app/Http/Controllers/Admin/RagController.php`
- `app/Models/AiChatSession.php`
- `resources/views/admin/rag/chat.blade.php`
- `resources/views/partials/n4ra-assistance-widget.blade.php`
- `resources/views/admin/rag/settings.blade.php`
- `docs/rag_fastapi/sql/2026_05_04_ai_chat_schema.sql`
- `docs/rag_fastapi/sql/2026_05_06_ai_intent_routing_and_session_meta.sql`
- `docs/rag_fastapi/WORKLOG.md`
- `docs/rag_fastapi/HANDOVER_STATUS.md`
- `docs/rag_fastapi/NEXT_STEPS.md`

Verification:
- `php -l app/Http/Controllers/Admin/RagController.php`
- `php -l app/Models/AiChatSession.php`

Risks:
- Perlu jalankan SQL manual baru agar `ai_chat_sessions.meta` tersedia untuk penyimpanan active document context yang persisten.
- Sebelum SQL dijalankan, sistem masih aman karena ada fallback ke metadata jawaban assistant terakhir, tetapi state session belum sekuat mode full schema.
- Hotfix setelah implementasi: jalur `catalog suggestion` sempat memicu `Undefined variable $activeDocument` di closure transaksi; sudah diperbaiki dengan capture variabel yang benar dan penyelarasan update session meta.

---

## 2026-05-06 10:05-10:20 (Asia/Jakarta)
Scope:
- Memperketat deteksi intent katalog dokumen agar pertanyaan isi dokumen yang memakai frasa umum seperti `apa saja` tidak salah diarahkan ke mode daftar dokumen.
- Kasus yang diperbaiki: follow-up tentang dokumen In-tank seperti `Apa saja produk komoditi yang dimaksud dalam sistem In-tank ini?` kini tetap diproses sebagai pertanyaan konten dokumen.

Files:
- `app/Http/Controllers/Admin/RagController.php`
- `docs/rag_fastapi/WORKLOG.md`
- `docs/rag_fastapi/HANDOVER_STATUS.md`
- `docs/rag_fastapi/NEXT_STEPS.md`

Verification:
- `php -l app/Http/Controllers/Admin/RagController.php`

Risks:
- Heuristik katalog masih berbasis pola teks; query yang sangat pendek dan ambigu tetap perlu divalidasi lagi lewat smoke test browser.

---

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

---

## 2026-05-05 12:20-12:40 (Asia/Jakarta)
Scope:
- Meninjau arsitektur prompt RAG secara end-to-end.
- Mengurangi ketergantungan pada aturan hardcoded di FastAPI.
- Memindahkan kontrol prompt menjadi lebih dinamis melalui menu `AI Prompt Settings` di EDC.

Root cause:
- Sebelumnya admin EDC hanya mengatur `prompt_template`, tetapi FastAPI tetap menambahkan blok aturan sistem hardcoded setelah template.
- Ini membuat perilaku prompt terasa "setengah dinamis": struktur utama bisa berubah dari EDC, tetapi aturan wajib/follow-up tetap dipaksa dari backend Python.

Perbaikan:
- EDC:
  - Menu `AI Prompt Settings` kini memiliki dua field:
    - `Prompt Template`
    - `Prompt Rules / System Rules`
  - `RagController` membaca dua setting tersebut dari DB/config.
  - `RagService` kini mengirim `prompt_rules` ke FastAPI bersama `prompt_template`.
- FastAPI:
  - `QueryRequest` ditambah field `prompt_rules`.
  - `generate_answer()` kini menerima `prompt_rules`.
  - Blok aturan sistem tidak lagi selalu hardcoded append; jika EDC mengirim rules, rules itulah yang dipakai.
  - Hardcoded rules di Python kini hanya berfungsi sebagai fallback safety net.
- SQL docs:
  - Seed default `rag_prompt_rules` ditambahkan ke SQL manual `ai_settings`.

Files:
- `../EDC AI RAG/app/models.py`
- `../EDC AI RAG/app/embedding.py`
- `../EDC AI RAG/main.py`
- `app/Http/Controllers/Admin/RagController.php`
- `app/Services/RagService.php`
- `config/services.php`
- `resources/views/admin/rag/settings.blade.php`
- `docs/rag_fastapi/sql/2026_05_04_ai_prompt_settings.sql`

Verification:
- `python -m py_compile main.py app\\embedding.py app\\models.py` lulus.
- `php -l` lulus untuk:
  - `app/Http/Controllers/Admin/RagController.php`
  - `app/Services/RagService.php`
  - `config/services.php`

Risks:
- FastAPI masih menyimpan default fallback prompt/rules untuk kasus service dipanggil langsung tanpa lewat EDC.

---

## 2026-05-05 14:35-14:45 (Asia/Jakarta)
Scope:
- Rebranding nama AI assistant pada UI admin dari label lama ke `N4R4 AI Assistance`.

Perbaikan:
- Mengganti label menu sidebar admin.
- Mengganti judul halaman chat AI.
- Mengganti heading kartu chat AI.
- Mengganti sapaan default assistant pada state awal dan saat membuat session baru.
- Mengganti teks tombol kembali pada halaman pengaturan prompt.

Files:
- `resources/views/admin/template.blade.php`
- `resources/views/admin/rag/chat.blade.php`
- `resources/views/admin/rag/settings.blade.php`

Verification:
- Pencarian string di `resources/views/admin` sudah tidak menemukan `AI Assistant EDC`, `EDC AI Assistant`, maupun label generik `AI Assistant`.

Risks:
- Branding di dokumen referensi/guide teknis masih dapat menyebut nama lama dan bisa dirapikan terpisah.

---

## 2026-05-05 15:00-15:20 (Asia/Jakarta)
Scope:
- Menambahkan AI Assistance berbentuk widget melayang di seluruh halaman aplikasi (admin/user/operator/tamu).
- Membuka akses endpoint chat AI lintas role agar berfungsi seperti customer service internal tanpa harus masuk halaman chat admin.

Perbaikan:
- Menambah route group auth baru `ai-assistance`:
  - `GET /ai-assistance/sessions`
  - `POST /ai-assistance/sessions`
  - `GET /ai-assistance/sessions/{id}/messages`
  - `DELETE /ai-assistance/sessions/{id}`
  - `POST /ai-assistance/query`
- Endpoint tersebut memanfaatkan logic existing `RagController` sehingga:
  - tetap menghormati token balance user
  - tetap memakai model yang diizinkan per user
  - tetap menyimpan session + history chat
- Membuat partial UI baru `partials/n4ra-assistance-widget.blade.php`:
  - tombol floating di kanan bawah
  - panel chat seperti customer service
  - auto create/load session per user (disimpan di localStorage)
  - kirim query ke backend RAG dan tampilkan jawaban langsung
  - tampilkan saldo token terkini
- Widget di-include ke template:
  - admin (kecuali halaman `admin.rag.chat` agar tidak duplikasi chat)
  - user
  - operator
  - tamu

Files:
- `routes/web.php`
- `resources/views/partials/n4ra-assistance-widget.blade.php`
- `resources/views/admin/template.blade.php`
- `resources/views/user/template.blade.php`
- `resources/views/operator/template.blade.php`
- `resources/views/tamu/template.blade.php`

Verification:
- `php -l routes/web.php` lulus.

Risks:
- Widget baru belum melalui UAT lintas role di browser; perlu smoke test manual untuk alur session/query di tiap role.

---

## 2026-05-05 15:25-15:35 (Asia/Jakarta)
Scope:
- Perbaikan overflow UI pada widget floating `N4R4 AI Assistance`.

Perbaikan:
- Layout panel diubah ke model `flex` vertikal (header/body/footer) agar tinggi body chat adaptif.
- Menghapus kalkulasi tinggi statis body (`calc(100% - 126px)`) yang menyebabkan footer/input terdorong keluar panel.
- Menyetel `textarea` dan tombol kirim agar tidak memaksa lebar/tinggi berlebih.
- Penyesuaian mode mobile supaya panel tetap penuh dalam viewport tanpa overflow bawah.
- Toggle panel dari JS disesuaikan (`display: flex`) agar layout flex aktif saat dibuka.

Files:
- `resources/views/partials/n4ra-assistance-widget.blade.php`

Verification:
- Review struktur CSS/JS memastikan header, message area, dan footer berada dalam satu kontainer fleksibel.

Risks:
- Belum diverifikasi visual di seluruh kombinasi zoom browser; perlu 1 kali smoke test manual.

---

## 2026-05-05 15:40-15:55 (Asia/Jakarta)
Scope:
- Menyamakan formatting widget dengan halaman AI khusus.
- Memperlebar panel widget untuk meningkatkan kenyamanan baca.

Perbaikan:
- Lebar widget dinaikkan dari `360px` ke `480px` (tetap responsif terhadap viewport).
- Rendering jawaban AI widget kini memakai formatter yang setara dengan halaman chat utama:
  - markdown sederhana (`**bold**`, list, heading, inline code)
  - blok `Sumber Dokumen`
  - blok `Saran pertanyaan lanjutan`
  - informasi token usage
- Tombol follow-up question di widget kini bisa langsung mengisi input.
- Tombol `Lihat` pada sumber dokumen ditambahkan di widget, termasuk form post tersembunyi sesuai role aktif (admin/operator/user/tamu).

Files:
- `resources/views/partials/n4ra-assistance-widget.blade.php`

Verification:
- Review struktur JS memastikan payload `sources`, `follow_up_questions`, dan `usage` dari endpoint dipakai untuk render widget.

Risks:
- Perlu smoke test manual klik `Lihat` sumber pada tiap role untuk memastikan route tampil dokumen sesuai hak akses.

---

## 2026-05-05 16:00-16:15 (Asia/Jakarta)
Scope:
- Menambahkan kontrol manajemen session langsung di widget floating (`pilih chat` dan `chat baru`).

Perbaikan:
- Menambahkan toolbar widget berisi:
  - dropdown riwayat chat (`Pilih riwayat chat`)
  - tombol `Chat Baru`
- Menambahkan sinkronisasi session widget ke endpoint:
  - `GET /ai-assistance/sessions`
  - `GET /ai-assistance/sessions/{id}/messages`
  - `POST /ai-assistance/sessions`
- Perilaku baru:
  - User bisa berpindah ke riwayat chat lama dari dropdown.
  - User bisa membuat chat/session baru tanpa keluar halaman.
  - Session aktif disimpan di `localStorage` dan tetap konsisten dengan dropdown.
  - Kontrol toolbar nonaktif saat request berjalan untuk menghindari race condition.

Files:
- `resources/views/partials/n4ra-assistance-widget.blade.php`

Verification:
- Review JS flow memastikan transisi session (select/new/send/open panel) tersambung ke endpoint sessions.

Risks:
- Perlu smoke test manual cepat untuk memastikan urutan session di dropdown sesuai ekspektasi user saat sesi sangat banyak.

---

## 2026-05-05 16:20-16:25 (Asia/Jakarta)
Scope:
- Mengubah default tampilan widget agar selalu memulai dari percakapan baru.

Perbaikan:
- Widget tidak lagi auto-select / auto-load riwayat session lama saat pertama kali dibuka.
- Riwayat chat tetap dimuat ke dropdown, tetapi pilihan aktif default kosong.
- User tetap bisa memilih session lama secara manual dari dropdown jika diperlukan.

Files:
- `resources/views/partials/n4ra-assistance-widget.blade.php`

Verification:
- Review logic `ensureSessionAndHistory()` memastikan state awal `currentSessionId=null` menghasilkan greeting percakapan baru.

Risks:
- Jika user berharap auto-resume session lama setelah refresh, perilaku itu kini dinonaktifkan by default.

---

## 2026-05-06 09:10-09:25 (Asia/Jakarta)
Scope:
- Memperbaiki bug tampilan bubble user yang menampilkan payload konteks internal (`[Konteks percakapan sebelumnya] ...`).

Root cause:
- Frontend mengirim pertanyaan yang sudah diperkaya konteks sebagai field `question`.
- Backend menyimpan field `question` tersebut ke tabel chat messages sebagai pesan user.
- Saat session dibuka ulang, UI merender payload konteks internal seolah-olah itu input asli user.

Perbaikan:
- Backend:
  - Tambah field request opsional `question_context`.
  - `question` tetap dianggap sebagai pertanyaan asli user (untuk penyimpanan session/history).
  - `question_context` dipakai hanya untuk query ke RAG (jika ada).
- Frontend chat admin:
  - Kirim `question` (raw user input) + `question_context` (hasil `buildContextualQuestion`).
  - Tambah normalizer `extractDisplayedUserQuestion()` untuk membersihkan data history lama yang sudah terlanjur tersimpan dalam format konteks.
- Widget:
  - Tambah normalizer yang sama agar riwayat user di widget juga bersih.

Files:
- `app/Http/Controllers/Admin/RagController.php`
- `resources/views/admin/rag/chat.blade.php`
- `resources/views/partials/n4ra-assistance-widget.blade.php`

Verification:
- `php -l app/Http/Controllers/Admin/RagController.php` lulus.

Risks:
- Record lama di DB tetap menyimpan payload lama; perbaikan saat ini membersihkan saat render, bukan migrasi data historis.

---

## 2026-05-05 16:25-16:30 (Asia/Jakarta)
Scope:
- Perbaikan fatal error website "Class 'view' does not exist".

Root cause:
- Direktori `bootstrap/cache` tidak memiliki izin tulis bagi server web.

Perbaikan:
- Mengubah izin direktori `bootstrap/cache` menjadi 777.
- Membersihkan cache aplikasi menggunakan `php artisan optimize:clear`.

Files:
- `bootstrap/cache` (permissions)

Verification:
- `php artisan tinker` mengonfirmasi servis `view` sudah aktif (exists).
- Log Laravel tidak lagi menunjukkan error baru setelah perbaikan.

Risks:
- Pengaturan izin 777 adalah solusi cepat; kedepannya pemilik file sebaiknya diselaraskan dengan user server web.

---

## 2026-05-06 09:40-10:00 (Asia/Jakarta)
Scope:
- Mengubah perilaku chat untuk pertanyaan yang sifatnya umum/katalog dokumen agar tidak langsung merangkum satu dokumen.

Root cause:
- Semua pertanyaan sebelumnya selalu masuk jalur RAG retrieval biasa.
- Untuk pertanyaan seperti "dokumen SOP apa saja", retrieval akan tetap memilih beberapa chunk teratas, lalu model menganggap user sedang meminta jawaban berbasis satu dokumen utama.

Perbaikan:
- Menambahkan deteksi intent katalog di `RagController`.
- Jika pertanyaan terdeteksi sebagai permintaan daftar dokumen umum (contoh: `dokumen SOP apa saja`, `ada dokumen apa saja`, `dokumen SOP`), sistem:
  - tidak memanggil jalur RAG summary biasa
  - mengambil daftar dokumen yang sudah ter-index
  - memfilter berdasarkan jenis dokumen yang diminta jika ada
  - memfilter juga berdasarkan hak akses jenis dokumen user
  - mengembalikan daftar dokumen yang tersedia + follow-up question untuk memilih dokumen yang ingin diringkas
- Jalur ini tidak memotong token user karena tidak memanggil LLM generation.

Files:
- `app/Http/Controllers/Admin/RagController.php`

Verification:
- `php -l app/Http/Controllers/Admin/RagController.php` lulus.

Risks:
- Heuristik intent katalog masih berbasis pattern teks; frasa yang sangat tidak lazim mungkin belum terdeteksi sebagai permintaan daftar dokumen.

---

## 2026-05-05 16:30-16:35 (Asia/Jakarta)
Scope:
- Perbaikan Mixed Content (HTTPS) dan SRI (moment.js) error pada Master Dokumen.

Root cause:
- `APP_URL` di `.env` masih HTTP, sedangkan situs diakses via HTTPS (memicu Mixed Content pada AJAX DataTables).
- Hash SRI pada tag script `moment.js` tidak cocok dengan konten CDN (memicu pemblokiran resource).

Perbaikan:
- Update `.env`: `APP_URL=https://edc.ptpn4.com` dan tambah `FORCE_HTTPS=true`.
- Update `TrustProxies.php`: Set `$proxies = '*'`.
- Update `AppServiceProvider.php`: Tambah logika `URL::forceScheme('https')` jika `FORCE_HTTPS` aktif.
- Views: Menghapus atribut `integrity` dan `crossorigin` pada seluruh pemanggilan `moment.js` di folder `resources/views`.
- Menjalankan `php artisan optimize:clear` untuk menerapkan perubahan config.

Files:
- `.env`
- `app/Http/Middleware/TrustProxies.php`
- `app/Providers/AppServiceProvider.php`
- `resources/views/admin/dokumen/index.blade.php`
- `resources/views/admin/dokumen/index_by_jenis.blade.php`
- `resources/views/operator/dokumen/index.blade.php`
- `resources/views/operator/dokumen/index_by_jenis.blade.php`

Verification:
- Konfigurasi telah diterapkan dan cache dibersihkan.
- Panggilan `route()` sekarang dipaksa menggunakan skema HTTPS.
