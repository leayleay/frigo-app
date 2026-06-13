-- =======================================================================
--  Base de données « frigo_app »
--  Tables : users • ingredients • recipes
--  MySQL ≥ 5.7 (colonne JSON) / MariaDB ≥ 10.2
-- =======================================================================

USE hemidjle_frigo;
-- ---------------------- TABLE users ------------------------------------
CREATE TABLE users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  email         VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------- TABLE ingredients ------------------------------
CREATE TABLE ingredients (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT NOT NULL,
  name         VARCHAR(100) NOT NULL,
  quantity     INT UNSIGNED NOT NULL,
  expiry_date  DATE NOT NULL,
  category     VARCHAR(50) NOT NULL,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------- TABLE recipes ----------------------------------
-- Les ingrédients d’une recette sont stockés en JSON :
--   [ { "ingredient":"tomate", "quantity":2 }, … ]
CREATE TABLE recipes (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  user_id        INT NOT NULL,
  name           VARCHAR(100) NOT NULL,
  ingredients_js JSON NOT NULL,
  created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------------------
-- Index facultatifs (recherche + perfs)
-- -----------------------------------------------------------------------
ALTER TABLE ingredients ADD INDEX idx_user_name (user_id, name);
ALTER TABLE recipes     ADD INDEX idx_user_name (user_id, name);
