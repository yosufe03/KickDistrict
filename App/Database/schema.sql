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

-- Seed logins:
-- admin / Admin123
-- testuser / User1234
INSERT INTO users (salutation, first_name, last_name, email, username, password, role, active)
VALUES
    ('Herr', 'Admin', 'KickDistrict', 'admin@kickdistrict.local', 'admin', '$2y$12$5h9RDCNuDzoWyoRuHwuxCeF780Pq8JgfpVAstRpOcAjEy.vsTDQ8G', 'admin', 1),
    ('Frau', 'Test', 'User', 'test@kickdistrict.local', 'testuser', '$2y$12$UcQe5IzVPtDef7k.gArZUOd98K6hGu13Cdr0KaC0XL2eDzlNSIB3e', 'user', 1)
ON DUPLICATE KEY UPDATE role = VALUES(role), active = VALUES(active);

CREATE TABLE IF NOT EXISTS vouchers (
    id INT NOT NULL AUTO_INCREMENT,
    code VARCHAR(50) NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    expiry_date DATE NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_voucher_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS voucher_usages (
    id INT NOT NULL AUTO_INCREMENT,
    voucher_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id INT DEFAULT NULL,
    used_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_voucher_user (voucher_id, user_id),
    KEY idx_voucher_usages_user (user_id),
    KEY idx_voucher_usages_order (order_id),
    CONSTRAINT fk_voucher_usages_voucher
        FOREIGN KEY (voucher_id) REFERENCES vouchers (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_voucher_usages_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO vouchers (code, value, expiry_date)
VALUES
    ('KD10WELCOME', 10.00, '2027-01-31'),
    ('SPRINT1BONUS', 15.00, '2026-12-31'),
    ('USED000', 0.00, '2026-12-31')
ON DUPLICATE KEY UPDATE value = VALUES(value), expiry_date = VALUES(expiry_date);

CREATE TABLE IF NOT EXISTS categories (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS products (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL,
    category_id INT DEFAULT NULL,
    description TEXT DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_products_category (category_id),
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories (id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS cartitems (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT DEFAULT NULL,
    session_id VARCHAR(128) DEFAULT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_cartitems_user (user_id),
    KEY idx_cartitems_session (session_id),
    KEY idx_cartitems_product (product_id),
    CONSTRAINT fk_cartitems_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_cartitems_product
        FOREIGN KEY (product_id) REFERENCES products (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS orders (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    voucher_code VARCHAR(50) DEFAULT NULL,
    status ENUM('pending', 'confirmed', 'paid', 'shipped', 'cancelled') NOT NULL DEFAULT 'confirmed',
    PRIMARY KEY (id),
    KEY idx_orders_user (user_id),
    CONSTRAINT fk_orders_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS order_items (
    id INT NOT NULL AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT DEFAULT NULL,
    product_name VARCHAR(200) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id),
    KEY idx_order_items_order (order_id),
    KEY idx_order_items_product (product_id),
    CONSTRAINT fk_order_items_order
        FOREIGN KEY (order_id) REFERENCES orders (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_order_items_product
        FOREIGN KEY (product_id) REFERENCES products (id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO categories (id, name)
VALUES
    (1, 'Trikots'),
    (2, 'Schuhe'),
    (3, 'Zubehör')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO products (id, name, image, price, category_id, description)
VALUES
    (1, 'KickDistrict Home Jersey', '../res/img/KickDistrict Home Jersey.jpeg', 79.90, 1, 'Klassisches Heimtrikot mit atmungsaktivem Stoff.'),
    (2, 'KickDistrict Away Jersey', '../res/img/KickDistrict Away Jersey.jpeg', 84.90, 1, 'Leichtes Auswärtstrikot für Training und Spieltag.'),
    (3, 'Sprint Speed Cleats', '../res/img/Sprint Speed Cleats.jpeg', 119.00, 2, 'Stabile Fußballschuhe mit gutem Grip.'),
    (4, 'Control Pro Cleats', '../res/img/Control Pro Cleats.jpeg', 139.00, 2, 'Fokus auf Ballgefühl und Präzision.'),
    (5, 'Match Ball', '../res/img/Match Ball.jpeg', 29.90, 3, 'Strapazierfähiger Ball für Training.'),
    (6, 'Goalkeeper Gloves', '../res/img/Goalkeeper Gloves.jpeg', 39.90, 3, 'Handschuhe mit sicherem Halt bei jedem Wetter.')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    image = VALUES(image),
    price = VALUES(price),
    category_id = VALUES(category_id),
    description = VALUES(description);
