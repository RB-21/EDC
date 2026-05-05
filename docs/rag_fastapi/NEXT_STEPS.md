# Next Steps

## High Priority
1. Relevance policy validation (multi-document)
- Uji query yang hanya relevan ke satu dokumen: pastikan hanya dokumen utama tampil.
- Uji query komparatif lintas dokumen: pastikan dokumen tambahan muncul hanya jika skor mendekati dokumen utama.
- Verifikasi batas `source_max_documents` dan `source_context_per_document` bekerja konsisten.

2. Follow-up questions UX validation
- Pastikan backend selalu mengembalikan `follow_up_questions` saat format model valid.
- Uji fallback saat blok `[FOLLOW_UP_QUESTIONS]` tidak ada (UI tidak error, tombol tidak muncul).
- Klik tombol saran harus mengisi input pertanyaan dengan benar.

3. UI smoke test on `admin/rag/chat`
- Confirm full-width/full-height layout
- Confirm model selector in toolbar works
- Confirm filter jenis/bagian removed

4. End-to-end chat persistence test
- Send 2-3 queries
- Reload page
- Re-open session from dropdown
- Validate message history reloaded correctly

5. Token accounting validation
- Capture token before query
- Send query
- Validate `ai_token_balance` decrement equals `usage.total_tokens`

## Medium Priority
1. Admin usage report page
- Token usage per user/day
- Top models used
- Remaining balance alerts

2. Stronger quota fallback strategy
- Detect 429 and auto-fallback to allowed backup model
- Return human-friendly response with retry guidance

3. Reduce CLI warning noise
- Optional PHP/runtime tuning for cleaner migration logs in local dev

## Operational Notes
- Use `scripts/restart-rag-stack.ps1` for stable restart flow
- Use SQL migration file if `artisan migrate` is intentionally skipped:
  - `docs/rag_fastapi/sql/2026_05_04_ai_chat_schema.sql`
