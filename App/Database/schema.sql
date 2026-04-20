CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL AUTO_INCREMENT,
    salutation VARCHAR(20) DEFAULT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS vouchers (
    id INT NOT NULL AUTO_INCREMENT,
    code VARCHAR(50) NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    expiry_date DATE NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_voucher_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO vouchers (code, value, expiry_date)
VALUES
    ('KD10WELCOME', 10.00, '2027-01-31'),
    ('SPRINT1BONUS', 15.00, '2026-12-31'),
    ('USED000', 0.00, '2026-12-31')
ON DUPLICATE KEY UPDATE value = VALUES(value), expiry_date = VALUES(expiry_date);
