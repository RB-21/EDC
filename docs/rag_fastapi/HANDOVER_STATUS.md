# Handover Status

Last updated: 2026-05-05

## Current Objective
Stabilize and evolve EDC AI Assistant into production-like chat:
- persistent session history
- token usage tracking
- per-user token balance
- per-user model access control
- reliable restart + troubleshooting flow

## Completed
1. RAG service recovery automation
- Script: `scripts/restart-rag-stack.ps1`
- Host/container connectivity checks included.

2. Chat backend features (Laravel)
- Session + message persistence via:
  - `ai_chat_sessions`
  - `ai_chat_messages`
- Token balance enforcement on `users.ai_token_balance`
- Allowed model whitelist via `users.ai_allowed_models`
- Session APIs added under `/admin/rag/sessions...`

3. RAG response enrichment (Python service)
- `/query` now returns:
  - `usage.prompt_tokens`
  - `usage.completion_tokens`
  - `usage.total_tokens`
  - `model`

4. UI improvements (chat page)
- Session selector, new chat, refresh, delete session
- Token balance badge
- Usage token display per assistant answer
- Degraded status badge support
- Full-width chat layout and filter simplification

5. Migration alternatives
- Laravel migrations exist
- Manual SQL migration exists:
  - `docs/rag_fastapi/sql/2026_05_04_ai_chat_schema.sql`

6. Relevance and UX tightening
- Token badge clarified as `Token Balance`
- Chat answer typography made denser/compact
- Low-similarity source filtering added:
  - backend gate in `EDC AI RAG/main.py`
  - frontend guard in `resources/views/admin/rag/chat.blade.php`

7. Instruction control + summary enforcement
- Jawaban dipaksa memuat blok `Ringkasan Dokumen`.
- Injeksi katalog dokumen ke pertanyaan dihapus (mengurangi noise retrieval dan token prompt).
- Source/context dibatasi ke dokumen utama (`source_max_documents=1`) untuk menekan dokumen tidak relevan.

8. Admin-managed prompt template (new baseline)
- Prompt template utama RAG kini bisa diedit di admin:
  - `AI Prompt Settings` (`/admin/rag/settings`)
- Template disimpan di DB key-value (`ai_settings.key = rag_prompt_template`).
- Chat user tidak lagi mengirim instruction manual; prompt terpusat dari admin settings.
- FastAPI sekarang mendukung `prompt_template` dari EDC pada endpoint `/query`.
- Placeholder runtime yang didukung:
  - `{{CONTEXT_BLOCK}}`
  - `{{QUESTION}}`
  - optional `{{INSTRUCTION}}`

9. Chat UX loading indicators
- Riwayat percakapan kini menampilkan indikator loading visual saat:
  - memuat daftar session
  - memuat isi session terpilih
- Tujuan: menghilangkan jeda tanpa feedback yang terasa awkward bagi user.

10. Date-list rendering normalization
- Frontend formatter menormalkan pola daftar tanggal Indonesia (`N. DD Bulan ...`)
  menjadi bullet list agar tidak muncul tampilan nomor ganda seperti `1. 1 Januari`.

11. Relevance-based multi-document policy
- Dokumen utama (similarity tertinggi) tetap wajib tampil.
- Dokumen tambahan ditampilkan jika skor dokumennya cukup dekat/relevan terhadap dokumen utama.
- Frontend menampilkan sumber berdasarkan relevansi per-dokumen (bukan strict satu dokumen).
- Prompt enforcement menuntut `Ringkasan Dokumen` minimal 5 poin secara default.

12. Follow-up suggestions
- Backend mengekstrak blok `[FOLLOW_UP_QUESTIONS]` dari output model.
- Response API kini membawa `follow_up_questions`.
- UI chat menampilkan saran pertanyaan lanjutan sebagai tombol yang bisa langsung diklik.

13. Vertex-only AI backend
- FastAPI tidak lagi memilih backend berdasarkan `GEMINI_API_KEY`.
- Seluruh jalur embedding, generation, dan OCR sekarang memakai client `google-genai` dengan backend Vertex AI.
- Root endpoint sekarang mengekspos `ai_backend=vertex_ai` untuk verifikasi cepat.
- Env template, README, dan AI context sudah diselaraskan ke mode Vertex-only.

14. Restart diagnostics hardening
- `scripts/restart-rag-stack.ps1` kini menyimpan stdout/stderr RAG ke file log.
- Saat startup gagal atau timeout, script menampilkan tail log agar root cause langsung terlihat.
- `GEMINI_API_KEY` tetap diterima sebagai env legacy field agar `.env` lama tidak mematahkan startup setelah refactor Vertex-only.

15. Singapore region switch
- Region Vertex AI aktif dipindahkan dari `us-central1` ke `asia-southeast1` (Singapore).
- Root/service startup berjalan normal setelah pindah region.
- Model runtime yang tervalidasi di region ini:
  - generation: `gemini-2.5-flash`
  - embedding: `gemini-embedding-001`

16. Restart script admin-safe fix
- Bug PowerShell `VariableNotWritable` pada loop PID sudah diperbaiki (nama variabel tidak lagi bentrok dengan `$PID` bawaan).
- Script kini mendukung auto-elevation (`Run as Administrator`) dan opsi `-NoElevate`.

17. FAILED_PRECONDITION diagnosis
- Error query `400 FAILED_PRECONDITION` terbukti muncul saat runtime mengarah ke `asia-southeast2`.
- Setelah runtime dikembalikan ke `asia-southeast1`, precondition region tidak lagi menjadi blocker utama.
- Verifikasi one-off menunjukkan `gemini-2.5-flash`, `text-embedding-005`, dan `gemini-embedding-001` berhasil di `asia-southeast1`.

18. Vertex embedding model switch
- Runtime embedding diganti dari `gemini-embedding-2` ke `gemini-embedding-001`.
- Collection aktif dipindahkan ke `edc_documents_vertex_v1` agar embedding space baru tidak tercampur dengan vector lama.
- Konsekuensi: dokumen perlu diindex ulang ke collection baru sebelum query RAG mengembalikan hasil relevan.

19. PDF image OCR fallback for embeddings
- Runtime saat ini memakai model text embedding Vertex (`gemini-embedding-001`), bukan image-bytes embedding murni.
- Untuk `image` chunk dari PDF, backend otomatis fallback ke OCR lalu meng-embed hasil teksnya.
- Ini menjaga dokumen PDF bergambar / tabel / scan tetap searchable tanpa bergantung pada model `gemini-embedding-2`.

20. Chat UI ordering update
- Posisi komponen jawaban AI di chat diperbarui agar `saran pertanyaan lanjutan` tampil sebelum `sumber dokumen`.
- Urutan baru berlaku untuk message baru dan saat membuka riwayat session.

21. Token and source badge UI refinement
- Informasi token usage dipindahkan ke bagian paling bawah bubble jawaban AI.
- Badge jenis dokumen pada sumber diperkecil (small) dengan selector CSS yang sesuai lokasi render.

22. Conditional source visibility
- Blok `Sumber Dokumen` kini disembunyikan saat jawaban menyatakan informasi tidak ditemukan.
- Aturan diterapkan untuk jawaban baru maupun riwayat chat.

23. Dynamic prompt layering via EDC
- Pengaturan prompt kini dipisah menjadi dua lapisan:
  - `Prompt Template`: susunan prompt utama + placeholder konteks/pertanyaan
  - `Prompt Rules`: aturan sistem, format output, ringkasan, dan follow-up questions
- EDC mengirim kedua lapisan ini ke FastAPI pada setiap query.
- Aturan hardcoded di FastAPI sudah diturunkan menjadi fallback saja, bukan sumber utama perilaku prompt.

24. Assistant UI rebranding
- Nama AI assistant pada UI admin diganti menjadi `N4R4 AI Assistance`.
- Berlaku di sidebar admin, judul halaman chat, header chat card, sapaan default assistant, dan tombol kembali pada halaman setting prompt.

25. Floating AI customer-service widget (all roles)
- Ditambahkan widget melayang `N4R4 AI Assistance` pada template admin/user/operator/tamu.
- Widget dapat membuka percakapan, memuat riwayat session, dan mengirim query RAG langsung dari halaman mana pun.
- Endpoint chat AI dibuat lintas role di group auth (`/ai-assistance/*`) dengan backend logic existing `RagController`.
- Token balance, model allowlist user, dan persistence session tetap mengikuti mekanisme yang sama seperti chat admin.

26. Floating widget overflow fix
- Layout internal panel widget diubah ke flex-based vertical layout agar footer/input selalu berada di dalam panel.
- Kasus overflow bawah pada viewport tertentu sudah ditangani dengan tinggi panel adaptif.

27. Widget formatting parity with main AI page
- Widget melayang kini menampilkan output dengan format yang setara halaman `admin/rag/chat`:
  - answer formatter (bold/list/heading/code)
  - saran pertanyaan lanjutan
  - sumber dokumen + tombol lihat
  - token usage
- Lebar panel widget diperbesar agar konten jawaban dan sumber lebih mudah dibaca.

28. Widget session controls
- Widget kini memiliki dropdown riwayat chat untuk memilih session yang sudah ada.
- Widget memiliki tombol `Chat Baru` untuk membuat session baru langsung dari panel.
- Session aktif tersinkron dengan localStorage, dropdown, dan endpoint sessions lintas role.

29. Default widget state = percakapan baru
- Saat widget dibuka, tampilan awal kini selalu percakapan baru (greeting), bukan auto-load session lama.
- Riwayat chat tetap tersedia di dropdown dan bisa dipilih manual kapan saja.

30. Web Error Fix: "Class 'view' does not exist"
- Root cause: `bootstrap/cache` not writable.
- Fix: Set permissions to 777 and cleared optimization caches.
- Verification: `view()->exists()` confirmed working via Tinker.

31. Mixed Content and SRI Error Fixes
- Issue: DataTables blocked due to HTTP AJAX on HTTPS site; `moment.js` blocked by SRI.
- Fix: Updated `APP_URL` to HTTPS, enabled `TrustProxies`, forced HTTPS scheme in `AppServiceProvider`, and removed `integrity` from `moment.js`.
- Files: `.env`, `TrustProxies.php`, `AppServiceProvider.php`, multiple Blade views.

32. User input payload normalization fix
- Pemisahan field request:
  - `question`: input asli user (disimpan ke history).
  - `question_context`: prompt teraugmentasi konteks untuk query RAG.
- UI chat admin + widget kini menormalkan record lama agar bagian konteks internal tidak tampil sebagai bubble user.

33. Catalog-intent response for general document questions
- Pertanyaan yang sifatnya umum/katalog dokumen (mis. `dokumen SOP apa saja`, `ada dokumen apa saja`, `dokumen SOP`) kini tidak langsung masuk jalur ringkasan satu dokumen.
- Backend akan menjawab dengan daftar dokumen tersedia yang sudah ter-index, disaring berdasarkan jenis dokumen yang diminta dan hak akses user.
- User kemudian diarahkan untuk memilih nomor/judul dokumen yang ingin diringkas lebih lanjut.

## Known Constraints / Risks
1. Legacy PHP dependency deprecation warnings still noisy on CLI.
2. Kuota/model availability tetap bergantung pada project Vertex AI dan auth ADC/service account yang aktif.
3. Existing legacy data (`users`) required SQL-mode workaround during schema change.
4. Collection baru `edc_documents_vertex_v1` masih kosong sampai proses reindex dijalankan.
5. Collection lama `edc_documents_v2` memakai embedding space `gemini-embedding-2` dan tidak boleh dicampur dengan runtime `gemini-embedding-001`.
6. Retrieval untuk konten gambar sekarang bergantung pada kualitas OCR fallback, bukan native multimodal image embedding.
7. Jika FastAPI dipanggil langsung tanpa lewat EDC, service masih memakai default fallback prompt/rules internal.

## Active Defaults
- `AI_BACKEND=vertex_ai`
- `RAG_DEFAULT_MODEL=gemini-2.5-flash`
- `RAG_AVAILABLE_MODELS=gemini-2.5-flash`
- `COLLECTION_NAME=edc_documents_vertex_v1`
- `EMBEDDING_MODEL=gemini-embedding-001`
- `GOOGLE_CLOUD_LOCATION=asia-southeast1`

## Verification Snapshot
- RAG `/health`: degraded (`gcp_connected=false`)
- Container app -> `host.docker.internal:8100/health`: reachable
- RAG startup via `restart-rag-stack.ps1`: process start OK, logs captured
- Python syntax check after Vertex-only refactor: pass
