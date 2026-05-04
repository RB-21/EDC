# AI Chat Features (History, Token, Saldo, Model per User)

Fitur baru yang sudah ditambahkan:

1. History percakapan per user
- Session tersimpan di tabel `ai_chat_sessions`.
- Pesan tersimpan di tabel `ai_chat_messages`.
- Endpoint:
  - `GET /admin/rag/sessions`
  - `GET /admin/rag/sessions/{id}/messages`
  - `DELETE /admin/rag/sessions/{id}`

2. Tracking token usage
- RAG service (`EDC AI RAG`) sekarang mengembalikan:
  - `usage.prompt_tokens`
  - `usage.completion_tokens`
  - `usage.total_tokens`
  - `model`
- Nilai usage ditampilkan di bubble jawaban AI.

3. Saldo token per user
- Kolom baru di tabel `users`:
  - `ai_token_balance` (default `100000`)
- Setiap query sukses akan mengurangi saldo sesuai `total_tokens`.
- Jika saldo habis, query ditolak.

4. Model AI per user
- Kolom baru di tabel `users`:
  - `ai_allowed_models` (CSV, nullable)
- Jika kosong, user boleh semua model dari `config/services.php`.
- Jika terisi, user hanya boleh model pada whitelist tersebut.
- Pengaturan tersedia di UI `admin/user` saat tambah/edit user:
  - Saldo Token AI
  - Model AI Diizinkan

## File migrasi SQL manual
- Jika tidak ingin memakai `artisan migrate`, gunakan file SQL ini:
  - `docs/rag_fastapi/sql/2026_05_04_ai_chat_schema.sql`

## Persistent progress / handover policy
Dokumentasi wajib diupdate pada setiap progres penting:
- `docs/rag_fastapi/WORKLOG.md`
- `docs/rag_fastapi/HANDOVER_STATUS.md`
- `docs/rag_fastapi/NEXT_STEPS.md`

Rule ini juga dituangkan di file:
- `AGENTS.md` (root project)

Helper script untuk append log cepat:
- `scripts/append-progress-log.ps1`

## Konfigurasi model global
Set di `.env` aplikasi EDC:

```env
RAG_DEFAULT_MODEL=gemini-2.5-flash
RAG_AVAILABLE_MODELS=gemini-2.5-flash
```

Jika ingin multi model:

```env
RAG_AVAILABLE_MODELS=gemini-2.5-flash,gemini-2.5-pro
```

Lalu jalankan:

```bash
php artisan config:clear
```
