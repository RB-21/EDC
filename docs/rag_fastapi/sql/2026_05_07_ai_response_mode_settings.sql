-- EDC AI Response Mode Settings Schema (MySQL manual seed)
-- Jalankan pada database EDC (contoh: e_edc_development)

START TRANSACTION;

INSERT INTO ai_settings (`key`, `value`, `description`, created_at, updated_at)
SELECT
    'rag_response_mode_default',
    'paragraph',
    'Mode format jawaban default untuk RAG generation',
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM ai_settings WHERE `key` = 'rag_response_mode_default'
);

INSERT INTO ai_settings (`key`, `value`, `description`, created_at, updated_at)
SELECT
    'rag_response_mode_table_patterns',
    '/\\b(?:tabel|tabulasi|kolom|matrix|matriks)\\b/u\n/\\b(?:perbandingan|bandingkan|komparasi)\\b/u\n/\\b(?:jadwal|schedule)\\b/u',
    'Pattern pertanyaan yang sebaiknya dijawab dalam format tabel',
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM ai_settings WHERE `key` = 'rag_response_mode_table_patterns'
);

INSERT INTO ai_settings (`key`, `value`, `description`, created_at, updated_at)
SELECT
    'rag_response_mode_numbered_list_patterns',
    '/\\b(?:langkah|tahapan|urutan|prosedur|cara)\\b/u\n/\\b(?:kapan saja|tanggal apa saja|tanggal cuti bersama|hari libur nasional|cuti bersama)\\b/u\n/\\b(?:siapa saja|apa saja)\\b/u\n/\\b(?:sebutkan|daftarkan|rincikan)\\b/u',
    'Pattern pertanyaan yang sebaiknya dijawab dalam numbered list',
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM ai_settings WHERE `key` = 'rag_response_mode_numbered_list_patterns'
);

INSERT INTO ai_settings (`key`, `value`, `description`, created_at, updated_at)
SELECT
    'rag_response_mode_bullet_list_patterns',
    '/\\b(?:ringkas|ringkasan|poin utama|poin-poin utama|highlight)\\b/u\n/\\b(?:key elements|dominant gestures|ketentuan|persyaratan|kriteria|cakupan)\\b/u',
    'Pattern pertanyaan yang sebaiknya dijawab dalam bullet list',
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM ai_settings WHERE `key` = 'rag_response_mode_bullet_list_patterns'
);

COMMIT;
