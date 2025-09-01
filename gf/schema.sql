-- SQL schema for companion app
CREATE TABLE IF NOT EXISTS gf_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id VARCHAR(64) NOT NULL,
  role ENUM('system','user','assistant') NOT NULL,
  content MEDIUMTEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (session_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gf_memories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id VARCHAR(64) NULL,
  type ENUM('preference','fact','goal') NOT NULL,
  value TEXT NOT NULL,
  source_message_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_used TIMESTAMP NULL,
  INDEX (session_id),
  CONSTRAINT fk_mem_source FOREIGN KEY (source_message_id)
    REFERENCES gf_messages(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gf_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  skey VARCHAR(64) UNIQUE NOT NULL,
  svalue TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
