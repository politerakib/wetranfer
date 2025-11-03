CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(64) NOT NULL UNIQUE,
    email VARCHAR(191) NOT NULL,
    name VARCHAR(191) NOT NULL,
    avatar_url VARCHAR(255) DEFAULT NULL,
    last_login_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id CHAR(12) NOT NULL UNIQUE,
    owner_uid VARCHAR(64) NOT NULL,
    type ENUM('p2p', 'team') NOT NULL DEFAULT 'p2p',
    title VARCHAR(191) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX(owner_uid),
    CONSTRAINT fk_rooms_owner FOREIGN KEY (owner_uid) REFERENCES users(uid) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS room_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id CHAR(12) NOT NULL,
    user_uid VARCHAR(64) NOT NULL,
    role ENUM('owner', 'member') NOT NULL DEFAULT 'member',
    joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    left_at DATETIME DEFAULT NULL,
    UNIQUE KEY uq_room_user (room_id, user_uid),
    CONSTRAINT fk_members_room FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE,
    CONSTRAINT fk_members_user FOREIGN KEY (user_uid) REFERENCES users(uid) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS room_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id CHAR(12) NOT NULL,
    user_uid VARCHAR(64) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    mime_type VARCHAR(191) NOT NULL,
    file_size BIGINT NOT NULL,
    storage_path VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (room_id),
    CONSTRAINT fk_files_room FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE,
    CONSTRAINT fk_files_user FOREIGN KEY (user_uid) REFERENCES users(uid) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
