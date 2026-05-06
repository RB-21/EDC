# Next Steps

## High Priority
1. Vertex runtime smoke test
- Jalankan `/health` dan 1 query nyata untuk memastikan auth Vertex AI berhasil.
- Verifikasi `ai_backend=vertex_ai` muncul di root endpoint.
- Pastikan `GOOGLE_APPLICATION_CREDENTIALS` atau ADC host benar-benar aktif di environment target.
- Jalankan restart script sekali dari non-admin shell untuk validasi auto-elevation path.
- Pastikan runtime tetap di `asia-southeast1`; jangan kembali ke `asia-southeast2` karena terbukti memicu `FAILED_PRECONDITION`.

2. Prompt settings smoke test from EDC
- Buka menu `AI Prompt Settings`.
- Ubah `Prompt Template` dan `Prompt Rules`, simpan, lalu kirim 1 query.
- Verifikasi perubahan perilaku prompt benar-benar mengikuti setting dari EDC.

3. Reindex ke collection Vertex baru
- Collection aktif sekarang `edc_documents_vertex_v1`.
- Jalankan indexing ulang dokumen yang dibutuhkan ke collection baru.
- Verifikasi query hanya memakai vector yang berasal dari `gemini-embedding-001`.
- Verifikasi beberapa PDF yang berisi gambar/tabel/scan untuk memastikan OCR fallback menghasilkan teks yang cukup baik.

4. Cleanup model lama
- Tandai `edc_documents_v2` sebagai collection legacy berbasis `gemini-embedding-2`.
- Putuskan apakah collection lama akan dipertahankan sementara atau dihapus setelah reindex selesai.

5. Relevance policy validation (multi-document)
- Uji query yang hanya relevan ke satu dokumen: pastikan hanya dokumen utama tampil.
- Uji query komparatif lintas dokumen: pastikan dokumen tambahan muncul hanya jika skor mendekati dokumen utama.
- Verifikasi batas `source_max_documents` dan `source_context_per_document` bekerja konsisten.

6. Follow-up questions UX validation
- Pastikan backend selalu mengembalikan `follow_up_questions` saat format model valid.
- Uji fallback saat blok `[FOLLOW_UP_QUESTIONS]` tidak ada (UI tidak error, tombol tidak muncul).
- Klik tombol saran harus mengisi input pertanyaan dengan benar.

7. UI smoke test on `admin/rag/chat`
- Confirm full-width/full-height layout
- Confirm model selector in toolbar works
- Confirm filter jenis/bagian removed
- Confirm urutan AI bubble: jawaban -> usage -> saran pertanyaan -> sumber dokumen
- Confirm urutan final AI bubble: jawaban -> saran pertanyaan -> sumber dokumen -> usage
- Confirm badge jenis dokumen pada sumber tampil small/compact
- Confirm blok `Sumber Dokumen` tidak tampil saat jawaban menyatakan informasi tidak ditemukan
- Confirm seluruh label assistant tampil sebagai `N4R4 AI Assistance`
- Confirm bubble user tidak menampilkan payload internal `[Konteks percakapan sebelumnya]`.
- Confirm pertanyaan katalog seperti `dokumen SOP apa saja` menampilkan daftar dokumen, bukan ringkasan satu dokumen.
- Confirm follow-up konten seperti `Apa saja produk komoditi...` setelah membahas satu dokumen tidak salah masuk ke mode katalog.
- Confirm follow-up seperti `Apa saja tanggal cuti bersama di tahun 2026 berdasarkan dokumen ini?` memakai dokumen aktif session, bukan daftar dokumen tersedia.
- Confirm output markdown table dari model dirender sebagai tabel HTML, bukan plain text `| ... |`.
- Confirm format jawaban tetap konsisten antar-turn untuk pertanyaan berulang pada topik yang sama, terutama daftar tanggal/libur yang diharapkan berbentuk tabel.

8. Jalankan SQL manual untuk intent routing
- Jalankan:
  - `docs/rag_fastapi/sql/2026_05_06_ai_intent_routing_and_session_meta.sql`
- Verifikasi kolom `ai_chat_sessions.meta` sudah ada.
- Verifikasi key `rag_intent_enable_active_document_context`, `rag_intent_catalog_patterns`, dan `rag_intent_active_document_reference_patterns` sudah ter-seed di `ai_settings`.

9. Floating widget smoke test (all roles)
- Buka halaman dashboard/dokumen pada role admin, user, operator, dan tamu.
- Pastikan tombol floating `N4R4 AI Assistance` selalu tampil.
- Kirim minimal 1 pertanyaan per role dan pastikan ada jawaban.
- Reload halaman dan pastikan history session widget tetap terbaca.
- Verifikasi saldo token di widget ikut berubah setelah query sukses.
- Verifikasi panel widget tidak overflow ke bawah pada desktop/mobile dan zoom 90%-125%.
- Verifikasi formatting widget setara halaman AI utama (sources, follow-up questions, token usage, dan tombol lihat dokumen).
- Verifikasi fungsi dropdown riwayat chat + tombol `Chat Baru` di widget berjalan stabil.
- Verifikasi default state widget selalu `percakapan baru` setelah refresh halaman.
- Verifikasi riwayat user di widget tidak menampilkan payload internal `[Konteks percakapan sebelumnya]`.
- Verifikasi pertanyaan katalog umum di widget juga mengikuti mode daftar dokumen.

10. End-to-end chat persistence test
- Send 2-3 queries
- Reload page
- Re-open session from dropdown
- Validate message history reloaded correctly

11. Token accounting validation
- Capture token before query
- Send query
- Validate `ai_token_balance` decrement equals `usage.total_tokens`

## Medium Priority
1. Admin usage report page
- Token usage per user/day
- Top models used
- Remaining balance alerts

2. Vertex auth hardening
- Tambahkan pemeriksaan health/auth yang lebih spesifik untuk ADC vs service account file.
- Bersihkan secret/config lama yang tidak lagi dipakai (`GEMINI_API_KEY`) setelah verifikasi selesai.

3. Reduce CLI warning noise
- Optional PHP/runtime tuning for cleaner migration logs in local dev

4. Branding copy cleanup (docs)
- Rapikan penyebutan nama lama `AI Assistant EDC` / `EDC AI Assistant` di dokumen panduan teknis agar konsisten.

## Operational Notes
- Use `scripts/restart-rag-stack.ps1` for stable restart flow
- Use SQL migration file if `artisan migrate` is intentionally skipped:
  - `docs/rag_fastapi/sql/2026_05_04_ai_chat_schema.sql`
