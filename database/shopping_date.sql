-- ══════════════════════════════════════════════════════
-- SHOPPING DATE — Base de données v4
-- Hébergement : ezyro.byetcluster.com
-- DB : ezyro_41332997_shopping
-- ══════════════════════════════════════════════════════

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- Participants
CREATE TABLE IF NOT EXISTS participants (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom              VARCHAR(100)  NOT NULL,
  prenom           VARCHAR(100)  NOT NULL,
  email            VARCHAR(150)  NOT NULL UNIQUE,
  telephone        VARCHAR(25)   NOT NULL,
  sexe             ENUM('homme','femme') NOT NULL,
  ville            VARCHAR(100)  NOT NULL,
  age              TINYINT UNSIGNED NOT NULL,
  profession       VARCHAR(150)  NOT NULL,
  partner_criteria TEXT          DEFAULT NULL,
  red_flags        TEXT          DEFAULT NULL,
  green_flags      TEXT          DEFAULT NULL,
  ideal_date       TEXT          DEFAULT NULL,
  description      TEXT          NOT NULL,
  photo            VARCHAR(255)  DEFAULT NULL,
  carte_identite   VARCHAR(255)  DEFAULT NULL,
  statut           ENUM('en_attente','selectionne','rejete') DEFAULT 'en_attente',
  date_inscription DATETIME      DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_statut (statut),
  INDEX idx_date   (date_inscription)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Administrateurs
CREATE TABLE IF NOT EXISTS admins (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom           VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password      VARCHAR(255) NOT NULL,
  role          ENUM('admin','superadmin') DEFAULT 'admin',
  date_creation DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sélections
CREATE TABLE IF NOT EXISTS selections (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  participant_id    INT UNSIGNED NOT NULL,
  admin_id          INT UNSIGNED NOT NULL,
  date_selection    DATETIME     DEFAULT CURRENT_TIMESTAMP,
  notification_sent TINYINT(1)  DEFAULT 0,
  date_notification DATETIME    DEFAULT NULL,
  FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
  FOREIGN KEY (admin_id)       REFERENCES admins(id)       ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE participants ADD COLUMN partner_criteria TEXT DEFAULT NULL AFTER profession;
ALTER TABLE participants ADD COLUMN red_flags TEXT DEFAULT NULL AFTER partner_criteria;
ALTER TABLE participants ADD COLUMN green_flags TEXT DEFAULT NULL AFTER red_flags;
ALTER TABLE participants ADD COLUMN ideal_date TEXT DEFAULT NULL AFTER green_flags;
ALTER TABLE participants ADD COLUMN carte_identite VARCHAR(255) DEFAULT NULL AFTER photo;
