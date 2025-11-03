CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id VARCHAR(64) NOT NULL UNIQUE,
    room_type ENUM('direct', 'team') DEFAULT 'direct',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS file_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id VARCHAR(64) NOT NULL,
    user_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(120) DEFAULT 'application/octet-stream',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_room_id (room_id),
    INDEX idx_user_id (user_id),
    CONSTRAINT fk_file_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
