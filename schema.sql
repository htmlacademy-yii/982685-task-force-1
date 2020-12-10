CREATE DATABASE taskforce_982685
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;
USE taskforce_982685;

-- города
CREATE TABLE cities (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `city`              VARCHAR(64) NOT NULL,
  `lat`               FLOAT(10, 7) NOT NULL,
  `long`              FLOAT(10, 7) NOT NULL
);

-- профили пользователей
CREATE TABLE profiles (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `address`           VARCHAR(1024) NOT NULL,
  `birthday`          DATE NOT NULL,
  `about`             VARCHAR(512) NOT NULL,
  `phone`             VARCHAR(32) NOT NULL UNIQUE,
  `skype`             VARCHAR(64) NOT NULL UNIQUE,
  `telegram`          VARCHAR(64) NOT NULL UNIQUE,
  `role`              ENUM('customer', 'executor'),  -- заказчик или исполнитель
  `rank`              FLOAT(4, 2) DEFAULT 0,
  `avatar`            VARCHAR(255) DEFAULT NULL,
  `dt_logon`          DATETIME DEFAULT NULL
);

-- пользователи
CREATE TABLE users (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `email`             VARCHAR(64) NOT NULL,
  `name`              VARCHAR(64) NOT NULL,
  `password`          VARCHAR(64) NOT NULL,
  `dt_add`            DATETIME DEFAULT CURRENT_TIMESTAMP,
  `profile_id`        BIGINT UNSIGNED NOT NULL,
  `city_id`           BIGINT UNSIGNED NOT NULL,
  FOREIGN KEY (`profile_id`) REFERENCES profiles (`id`),
  FOREIGN KEY (`city_id`) REFERENCES cities (`id`)
);

-- фотографии работ исполнителя
CREATE TABLE photos_works (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `image`             VARCHAR(255) NOT NULL,
  `executor_id`       BIGINT UNSIGNED NOT NULL,
  FOREIGN KEY (`executor_id`) REFERENCES users (`id`) ON DELETE CASCADE
);

-- категории задач
CREATE TABLE categories (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `name`              VARCHAR(128) NOT NULL UNIQUE,
  `icon`              VARCHAR(255) NOT NULL UNIQUE
);

-- задачи
CREATE TABLE tasks (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `dt_add`            DATETIME DEFAULT CURRENT_TIMESTAMP,
  `detail_work`       VARCHAR(1024) NOT NULL,
  `expire`            DATETIME NOT NULL,
  `essence_work`      VARCHAR(255) NOT NULL,
  `address`           VARCHAR(255) NOT NULL,
  `budget`            INT UNSIGNED DEFAULT 0,
  `lat`               FLOAT(10, 7) NOT NULL,
  `long`              FLOAT(10, 7) NOT NULL,
  `status`            ENUM('new', 'cancelled', 'progress', 'completed', 'failed'),
  `category_id`       BIGINT UNSIGNED NOT NULL,
  `customer_id`       BIGINT UNSIGNED NOT NULL,
  `executor_id`       BIGINT UNSIGNED DEFAULT NULL,
  FOREIGN KEY (`category_id`) REFERENCES categories (`id`),
  FOREIGN KEY (`customer_id`) REFERENCES users (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`executor_id`) REFERENCES users (`id`) ON DELETE SET NULL
);

-- долнительные файлы вложения к задаче
CREATE TABLE tasks_attachments (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `file`              VARCHAR(255) NOT NULL,
  `task_id`           BIGINT UNSIGNED NOT NULL,
  FOREIGN KEY (`task_id`) REFERENCES tasks (`id`) ON DELETE CASCADE
);

-- отзывы об исполнителе
CREATE TABLE replies (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `dt_add`            DATETIME DEFAULT CURRENT_TIMESTAMP,
  `rate`              TINYINT UNSIGNED DEFAULT 0,
  `description`       VARCHAR(1024) NOT NULL,
  `task_id`           BIGINT UNSIGNED NOT NULL,
  `executor_id`       BIGINT UNSIGNED NOT NULL,
  FOREIGN KEY (`task_id`) REFERENCES tasks (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`executor_id`) REFERENCES users (`id`) ON DELETE CASCADE
);

-- отклики исполнителей на предложенную задачу
CREATE TABLE opinions (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `dt_add`            DATETIME DEFAULT CURRENT_TIMESTAMP,
  `rate`              TINYINT UNSIGNED DEFAULT 0,
  `description`       VARCHAR(1024) NOT NULL,
  `task_id`           BIGINT UNSIGNED DEFAULT NULL,
  `executor_id`       BIGINT UNSIGNED DEFAULT NULL,
  FOREIGN KEY (`task_id`) REFERENCES tasks (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`executor_id`) REFERENCES users (`id`) ON DELETE SET NULL
);
