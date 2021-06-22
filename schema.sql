DROP DATABASE IF EXISTS taskforce_982685;

CREATE DATABASE taskforce_982685
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE taskforce_982685;

-- города (справочник)
CREATE TABLE cities (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `city`              VARCHAR(64) NOT NULL,         -- название города
  `lat`               FLOAT(10,7) NOT NULL,         -- широта города
  `long`              FLOAT(10,7) NOT NULL          -- долгота города
);

-- локации заданий
CREATE TABLE locations (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `city_id`           BIGINT UNSIGNED NOT NULL,     -- ID города (из справочника городов)
  `lat`               FLOAT(10,7) NOT NULL,         -- широта локации, введенной пользователем
  `long`              FLOAT(10,7) NOT NULL,         -- долгота локации, введенной пользователем
  FOREIGN KEY (`city_id`) REFERENCES cities(`id`) ON DELETE CASCADE
);

-- файлы на диске (вложения, аватары пользователей, фотографии работ и т.д.)
CREATE TABLE files (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `path`              VARCHAR(255) NOT NULL UNIQUE  -- путь к файлам на диске относительно корня приложения
);

-- пользователи (заказчики и исполнители)
CREATE TABLE users (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `role`              ENUM('customer', 'executor') NOT NULL DEFAULT 'customer', -- роль: заказчик или исполнитель
  -- регистрация:
  `dt_add`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- дата/время регистрации пользователя
  `name`              VARCHAR(64) NOT NULL,             -- ФИО пользователя
  `email`             VARCHAR(64) NOT NULL UNIQUE,      -- e-mail пользователя (login)
  `password`          VARCHAR(64) NOT NULL,             -- пароль
  `city_id`           BIGINT UNSIGNED NOT NULL,         -- местонахождение пользователя (ID города из справочника городов)
  -- профиль пользователя:
  `dt_last_action`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- зафиксированное время последнего действия на сайте
  `birthday`          DATE DEFAULT NULL,                -- дата рождения
  `about`             VARCHAR(512) DEFAULT NULL,        -- информация о пользователе
  `phone`             VARCHAR(32) DEFAULT NULL,         -- телефон (мобильный)
  `skype`             VARCHAR(64) DEFAULT NULL,         -- skype
  `telegram`          VARCHAR(64) DEFAULT NULL,         -- telegram
  `other_messenger`   VARCHAR(64) DEFAULT NULL,         -- другой мессенжер
  `cnt_done_tasks`    INT UNSIGNED NOT NULL DEFAULT 0,  -- количество выполненных работ (завершенных заданий)
  `cnt_failed_tasks`  INT UNSIGNED NOT NULL DEFAULT 0,  -- количество проваленных работ (отказов от заданий)
  `rating`            FLOAT(4, 2) NOT NULL DEFAULT 0,   -- рейтинг пользователя (от 0.00 до 5.00)
  `notice_message`    TINYINT NOT NULL DEFAULT 1,       -- уведомлять о новом сообщении
  `notice_actions`    TINYINT NOT NULL DEFAULT 1,       -- уведомлять о действиях по заданию
  `notice_review`     TINYINT NOT NULL DEFAULT 1,       -- уведомлять о новом отзыве
  `hide_profile`      TINYINT NOT NULL DEFAULT 1,       -- не показывать профиль
  `customer_only`     TINYINT NOT NULL DEFAULT 0,       -- показывать контакты только заказчику
  `avatar_id`         BIGINT UNSIGNED NOT NULL,         -- ID фотографии пользователя (по умолчанию - аватар-заглушка)
  FOREIGN KEY (`city_id`) REFERENCES cities(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`avatar_id`) REFERENCES files (`id`) ON DELETE CASCADE
);

-- фотографии работ исполнителя
CREATE TABLE photos_works (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `photo_id`          BIGINT UNSIGNED NOT NULL,     -- ID фотографии работы
  `executor_id`       BIGINT UNSIGNED NOT NULL,     -- ID исполнителя
  FOREIGN KEY (`photo_id`) REFERENCES files (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`executor_id`) REFERENCES users (`id`) ON DELETE CASCADE
);

-- категории заданий
CREATE TABLE categories (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `category_name`     VARCHAR(128) NOT NULL UNIQUE, -- название категории
  `icon_id`           BIGINT UNSIGNED NOT NULL,     -- ID файла иконки категории
  FOREIGN KEY (`icon_id`) REFERENCES files (`id`) ON DELETE CASCADE
);

-- специализации исполнителя
CREATE TABLE executor_specialties (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `executor_id`       BIGINT UNSIGNED NOT NULL,     -- ID исполнителя
  `category_id`       BIGINT UNSIGNED NOT NULL,     -- ID категории
  FOREIGN KEY (`executor_id`) REFERENCES users (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES categories(`id`) ON DELETE CASCADE
);

-- задания (задачи)
CREATE TABLE tasks (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `dt_add`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- дата/время добавления задания
  `status`            ENUM('new', 'cancelled', 'progress', 'completed', 'failed') NOT NULL DEFAULT 'new',  -- статус задания
  `job_essence`       VARCHAR(255) NOT NULL,            -- краткое описание сути задания
  `job_details`       VARCHAR(1024) NOT NULL,           -- подробности задания
  `expire`            DATETIME DEFAULT NULL,            -- срок исполнения
  `budget`            INT UNSIGNED NOT NULL DEFAULT 0,  -- бюджет (цена)
  `location_id`       BIGINT UNSIGNED DEFAULT NULL,     -- ID локации (адрес исполнения, если задание требует присутствия)
  `category_id`       BIGINT UNSIGNED NOT NULL,         -- ID категории
  `customer_id`       BIGINT UNSIGNED NOT NULL,         -- ID заказчика
  `executor_id`       BIGINT UNSIGNED DEFAULT NULL,     -- ID исполнителя
  FOREIGN KEY (`location_id`) REFERENCES locations (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`category_id`) REFERENCES categories (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`customer_id`) REFERENCES users (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`executor_id`) REFERENCES users (`id`) ON DELETE SET NULL
);

-- дополнительные файлы к заданию (вложения)
CREATE TABLE tasks_attachments (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `task_id`           BIGINT UNSIGNED NOT NULL,     -- ID задания
  `attachment_id`     BIGINT UNSIGNED NOT NULL,     -- ID файла вложения
  FOREIGN KEY (`task_id`) REFERENCES tasks (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`attachment_id`) REFERENCES files (`id`) ON DELETE CASCADE
);

-- отзывы об исполнителе
CREATE TABLE reviews (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `dt_add`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- дата/время добавления отзыва
  `eval`              TINYINT UNSIGNED NOT NULL DEFAULT 0,         -- оценка (от 1 до 5)
  `review_text`       VARCHAR(1024) NOT NULL,             -- текст отзыва
  `task_id`           BIGINT UNSIGNED NOT NULL,           -- ID задания, о котором пишется отзыв
  `executor_id`       BIGINT UNSIGNED NOT NULL,           -- ID исполнителя
  FOREIGN KEY (`task_id`) REFERENCES tasks (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`executor_id`) REFERENCES users (`id`) ON DELETE CASCADE
);

-- отклики исполнителей на предложенное задание
CREATE TABLE responses (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `dt_add`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- дата/время добавления отклика
  `budget`            INT UNSIGNED NOT NULL DEFAULT 0,    -- предложенная цена
  `response_text`     VARCHAR(1024) NOT NULL,             -- текст отклика
  `task_id`           BIGINT UNSIGNED NOT NULL,           -- ID задания
  `executor_id`       BIGINT UNSIGNED NOT NULL,           -- ID исполнителя
  FOREIGN KEY (`task_id`) REFERENCES tasks (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`executor_id`) REFERENCES users (`id`) ON DELETE CASCADE
);

-- переписка между заказчиком и исполнителем по поставленному заданию 
CREATE TABLE messages (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
  `dt_add`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- дата/время отправки сообщения
  `message_text`      VARCHAR(1024) NOT NULL,             -- текст сообщения
  `message_read`      TINYINT NOT NULL DEFAULT 0,         -- признак, было ли прочитано сообщение (по умолчанию - нет)
  `task_id`           BIGINT UNSIGNED NOT NULL,           -- ID задания
  `sender_id`         BIGINT UNSIGNED NOT NULL,           -- ID отправителя сообщения
  `recipient_id`      BIGINT UNSIGNED NOT NULL,           -- ID получателя сообщения
  FOREIGN KEY (`task_id`) REFERENCES tasks (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`sender_id`) REFERENCES users (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recipient_id`) REFERENCES users (`id`) ON DELETE CASCADE
);
