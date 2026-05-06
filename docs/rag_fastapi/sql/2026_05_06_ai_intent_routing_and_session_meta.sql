-- EDC AI Intent Routing + Session Meta Schema (MySQL manual migration)
-- Jalankan pada database EDC (contoh: e_edc_development)

START TRANSACTION;

-- =========================================================
-- 1) AI CHAT SESSIONS: tambah meta JSON untuk menyimpan
--    active document context lintas follow-up question.
-- =========================================================

SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'ai_chat_sessions'
              AND COLUMN_NAME = 'meta'
        ),
        'SELECT 1',
        'ALTER TABLE ai_chat_sessions ADD COLUMN meta JSON NULL AFTER model'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =========================================================
-- 2) AI SETTINGS: seed default intent routing settings.
-- =========================================================

INSERT INTO ai_settings (`key`, `value`, `description`, created_at, updated_at)
SELECT
    'rag_intent_enable_active_document_context',
    '1',
    'Aktifkan konteks dokumen aktif untuk follow-up question',
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM ai_settings WHERE `key` = 'rag_intent_enable_active_document_context'
);

INSERT INTO ai_settings (`key`, `value`, `description`, created_at, updated_at)
SELECT
    'rag_intent_catalog_patterns',
    '/\\bdaftar\\s+(?:dokumen|arsip|berkas|file)\\b/u\n/\\b(?:dokumen|arsip|berkas|file)\\b.*\\b(?:apa saja|yang tersedia|yang ada|tersedia)\\b/u\n/\\b(?:apa saja|yang tersedia|yang ada|tersedia)\\b.*\\b(?:dokumen|arsip|berkas|file)\\b/u\n/\\bada\\s+(?:dokumen|arsip|berkas|file)\\b/u\n/^(?:dokumen|daftar dokumen)$/u',
    'Pattern intent katalog dokumen (regex/frase per baris)',
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM ai_settings WHERE `key` = 'rag_intent_catalog_patterns'
);

INSERT INTO ai_settings (`key`, `value`, `description`, created_at, updated_at)
SELECT
    'rag_intent_active_document_reference_patterns',
    'dokumen ini\ndokumen tersebut\nsurat ini\nsurat tersebut\nberdasarkan dokumen ini\nberdasarkan dokumen tersebut\nberdasarkan surat ini\ndalam dokumen ini\ndi dokumen ini\npada dokumen ini',
    'Pattern referensi ke dokumen aktif (regex/frase per baris)',
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM ai_settings WHERE `key` = 'rag_intent_active_document_reference_patterns'
);

COMMIT;
