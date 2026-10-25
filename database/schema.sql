-- ICS Market — schéma de la base (MySQL 8 / InnoDB)
-- Marketplace fictive de formation. Données exclusivement synthétiques.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS coupon_uses;
DROP TABLE IF EXISTS coupons;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS carts;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS addresses;
DROP TABLE IF EXISTS otp_challenges;
DROP TABLE IF EXISTS auth_events;
DROP TABLE IF EXISTS security_events;
DROP TABLE IF EXISTS site_settings;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- ───────────── Utilisateurs ─────────────
CREATE TABLE users (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  full_name   VARCHAR(120) NOT NULL,
  email       VARCHAR(160) NOT NULL UNIQUE,
  phone       VARCHAR(20)  NULL,
  pass_hash   VARCHAR(255) NOT NULL,
  role        ENUM('customer','seller','support','admin') NOT NULL DEFAULT 'customer',
  avatar      VARCHAR(120) NULL,
  status      ENUM('active','blocked') NOT NULL DEFAULT 'active',
  is_seeded   TINYINT(1) NOT NULL DEFAULT 0,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ───────────── Catégories ─────────────
CREATE TABLE categories (
  id    INT AUTO_INCREMENT PRIMARY KEY,
  name  VARCHAR(80) NOT NULL,
  slug  VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ───────────── Produits ─────────────
CREATE TABLE products (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  category_id  INT NULL,
  seller_id    INT NULL,
  name         VARCHAR(160) NOT NULL,
  slug         VARCHAR(180) NOT NULL,
  description  TEXT NULL,
  price        BIGINT NOT NULL DEFAULT 0,      -- FCFA entier
  stock        INT NOT NULL DEFAULT 0,
  image        VARCHAR(160) NULL,              -- image principale (dans public/assets/img ou /uploads)
  rating_avg   DECIMAL(2,1) NOT NULL DEFAULT 0,
  status       ENUM('active','hidden') NOT NULL DEFAULT 'active',
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_prod_cat FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  CONSTRAINT fk_prod_seller FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_prod_cat (category_id),
  INDEX idx_prod_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_images (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  filename   VARCHAR(160) NOT NULL,
  CONSTRAINT fk_img_prod FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ───────────── Avis ─────────────
CREATE TABLE reviews (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  product_id  INT NOT NULL,
  user_id     INT NULL,
  author_name VARCHAR(120) NOT NULL,
  rating      TINYINT NOT NULL DEFAULT 5,
  body        TEXT NOT NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_rev_prod FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  INDEX idx_rev_prod (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ───────────── Panier (serveur) ─────────────
CREATE TABLE carts (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cart_items (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  cart_id    INT NOT NULL,
  product_id INT NOT NULL,
  qty        INT NOT NULL DEFAULT 1,
  unit_price BIGINT NOT NULL DEFAULT 0,
  CONSTRAINT fk_ci_cart FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
  CONSTRAINT fk_ci_prod FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ───────────── Commandes ─────────────
CREATE TABLE orders (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  reference   VARCHAR(32) NOT NULL UNIQUE,
  user_id     INT NULL,
  subtotal    BIGINT NOT NULL DEFAULT 0,
  discount    BIGINT NOT NULL DEFAULT 0,
  total       BIGINT NOT NULL DEFAULT 0,
  coupon_code VARCHAR(40) NULL,
  address     VARCHAR(255) NULL,
  status      ENUM('pending','paid','shipped','cancelled') NOT NULL DEFAULT 'paid',
  invoice     VARCHAR(160) NULL,               -- nom de fichier facture (storage/invoices)
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_order_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_order_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  order_id   INT NOT NULL,
  product_id INT NULL,
  name       VARCHAR(160) NOT NULL,
  unit_price BIGINT NOT NULL,
  qty        INT NOT NULL,
  CONSTRAINT fk_oi_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ───────────── Coupons ─────────────
CREATE TABLE coupons (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  code       VARCHAR(40) NOT NULL UNIQUE,
  type       ENUM('percent','amount') NOT NULL DEFAULT 'percent',
  value      INT NOT NULL DEFAULT 0,           -- % ou FCFA
  max_uses   INT NOT NULL DEFAULT 1,
  used_count INT NOT NULL DEFAULT 0,
  active     TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE coupon_uses (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  coupon_id INT NOT NULL,
  user_id   INT NULL,
  order_id  INT NULL,
  used_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_cu_coupon FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ───────────── Adresses ─────────────
CREATE TABLE addresses (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  user_id  INT NOT NULL,
  label    VARCHAR(80) NULL,
  line     VARCHAR(255) NOT NULL,
  city     VARCHAR(80) NULL,
  phone    VARCHAR(20) NULL,
  CONSTRAINT fk_addr_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ───────────── OTP / journaux ─────────────
CREATE TABLE otp_challenges (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NULL,
  phone      VARCHAR(20) NOT NULL,
  code       VARCHAR(8) NOT NULL,
  purpose    ENUM('signup','login','reset') NOT NULL DEFAULT 'signup',
  attempts   INT NOT NULL DEFAULT 0,
  consumed   TINYINT(1) NOT NULL DEFAULT 0,
  expires_at TIMESTAMP NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_otp_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE auth_events (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  login      VARCHAR(160) NULL,
  event      VARCHAR(40) NOT NULL,
  ip         VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_auth_login (login)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE security_events (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  actor       VARCHAR(160) NULL,
  action      VARCHAR(80) NOT NULL,
  target      VARCHAR(160) NULL,
  result      VARCHAR(40) NULL,
  correlation VARCHAR(40) NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ───────────── Réglages du site (bannière, etc.) ─────────────
CREATE TABLE site_settings (
  skey  VARCHAR(60) PRIMARY KEY,
  sval  TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
