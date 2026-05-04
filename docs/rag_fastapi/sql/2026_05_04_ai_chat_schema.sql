-- EDC AI Chat Schema (MySQL manual migration)
-- Jalankan pada database EDC (contoh: e_edc_development)

START TRANSACTION;

-- =========================================================
-- 1) USERS: saldo token + model whitelist
-- =========================================================

SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'users'
              AND COLUMN_NAME = 'ai_token_balance'
        ),
        'SELECT 1',
        'ALTER TABLE users ADD COLUMN ai_token_balance BIGINT NOT NULL DEFAULT 100000 AFTER password'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'users'
              AND COLUMN_NAME = 'ai_allowed_models'
        ),
        'SELECT 1',
        'ALTER TABLE users ADD COLUMN ai_allowed_models TEXT NULL AFTER ai_token_balance'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =========================================================
-- 2) AI CHAT SESSIONS
-- =========================================================

CREATE TABLE IF NOT EXISTS ai_chat_sessions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NULL,
    model VARCHAR(120) NULL,
    last_message_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    KEY idx_ai_chat_sessions_user_last (user_id, last_message_at),
    KEY idx_ai_chat_sessions_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 3) AI CHAT MESSAGES
-- =========================================================

CREATE TABLE IF NOT EXISTS ai_chat_messages (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    session_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    role VARCHAR(20) NOT NULL,
    message LONGTEXT NULL,
    model VARCHAR(120) NULL,
    prompt_tokens INT NOT NULL DEFAULT 0,
    completion_tokens INT NOT NULL DEFAULT 0,
    total_tokens INT NOT NULL DEFAULT 0,
    meta JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    KEY idx_ai_chat_messages_session_created (session_id, created_at),
    KEY idx_ai_chat_messages_user_created (user_id, created_at),
    KEY idx_ai_chat_messages_session (session_id),
    KEY idx_ai_chat_messages_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
