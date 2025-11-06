CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id VARCHAR(64) NOT NULL UNIQUE,
    room_type ENUM('direct', 'team') DEFAULT 'direct',
    transfer_mode ENUM('webrtc', 'store') DEFAULT 'webrtc',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS room_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id VARCHAR(64) NOT NULL,
    user_id INT NOT NULL,
    message_type ENUM('text', 'file') NOT NULL,
    message_text TEXT,
    file_name VARCHAR(255),
    file_path VARCHAR(255),
    file_type VARCHAR(120),
    transfer_mode ENUM('webrtc', 'store') DEFAULT 'webrtc',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_room_id (room_id),
    INDEX idx_user_id (user_id),
    CONSTRAINT fk_room_messages_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
