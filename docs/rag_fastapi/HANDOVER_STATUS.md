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
