# Handover Status

Last updated: 2026-05-04

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

## Known Constraints / Risks
1. Legacy PHP dependency deprecation warnings still noisy on CLI.
2. Gemini model quota may fail for deprecated/limited models.
3. Existing legacy data (`users`) required SQL-mode workaround during schema change.

## Active Defaults
- `RAG_DEFAULT_MODEL=gemini-2.5-flash`
- `RAG_AVAILABLE_MODELS=gemini-2.5-flash`

## Verification Snapshot
- RAG `/health`: healthy
- Container app -> `host.docker.internal:8100/health`: reachable
- RAG `/query` with `gemini-2.5-flash`: success
