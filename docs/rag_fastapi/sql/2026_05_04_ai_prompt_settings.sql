-- EDC AI Prompt Settings Schema (MySQL manual migration)
-- Jalankan pada database EDC (contoh: e_edc_development)

START TRANSACTION;

CREATE TABLE IF NOT EXISTS ai_settings (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(120) NOT NULL,
    `value` LONGTEXT NULL,
    `description` VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_ai_settings_key (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default prompt template only when key does not exist
INSERT INTO ai_settings (`key`, `value`, `description`, created_at, updated_at)
SELECT
    'rag_prompt_template',
    'Kamu adalah asisten AI yang membantu menjawab pertanyaan berdasarkan dokumen yang tersedia.\nJawab pertanyaan berikut HANYA berdasarkan konteks yang diberikan.\nJika jawabannya tidak ada dalam konteks, katakan "Maaf, informasi tersebut tidak ditemukan dalam dokumen yang tersedia."\n\nPENTING: Jika kamu menemukan jawabannya dari konteks, kamu WAJIB mengawali jawabanmu dengan menyebutkan identitas dokumen utama yang menjadi acuanmu dengan format persis seperti ini (Gunakan Bold):\n\n**Dokumen**\n**No Dokumen:** [Nomor]\n**Judul Dokumen:** [Judul]\n**Jenis Dokumen:** [Jenis]\n\nSetelah itu, WAJIB tampilkan bagian ringkasan dokumen utama dengan format:\n**Ringkasan Dokumen:**\n- Poin ringkas 1\n- Poin ringkas 2\n- Poin ringkas 3 (opsional)\n\nLalu berikan jawabanmu di bawahnya dengan jelas dan terstruktur.\n\nKONTEKS DOKUMEN:\n{{CONTEXT_BLOCK}}\n\nPERTANYAAN:\n{{QUESTION}}\n\nJAWABAN:',
    'Template prompt utama untuk RAG generation',
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM ai_settings WHERE `key` = 'rag_prompt_template'
);

-- Seed default prompt rules only when key does not exist
INSERT INTO ai_settings (`key`, `value`, `description`, created_at, updated_at)
SELECT
    'rag_prompt_rules',
    '[ATURAN WAJIB SISTEM]\n1) Tampilkan dokumen utama (similarity tertinggi) sebagai acuan utama jawaban.\n2) Jika ada dokumen lain yang tetap relevan kuat terhadap pertanyaan, boleh disebutkan juga.\n3) Setelah blok identitas dokumen, tampilkan "Ringkasan Dokumen" minimal 5 poin ringkas.\n4) Ringkasan harus berisi detail penting dan konkret dari dokumen (misal nomor, tanggal, ketentuan, daftar poin utama).\n5) Jangan singkat berlebihan. Jika konteks cukup, berikan ringkasan yang komprehensif.\n6) Di akhir jawaban, WAJIB tampilkan blok saran pertanyaan lanjutan persis dengan format:\n[FOLLOW_UP_QUESTIONS]\n1. ...\n2. ...\n3. ...\n[/FOLLOW_UP_QUESTIONS]',
    'Aturan sistem dan format output untuk RAG generation',
    NOW(),
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM ai_settings WHERE `key` = 'rag_prompt_rules'
);

COMMIT;
