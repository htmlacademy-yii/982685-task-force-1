DROP DATABASE IF EXISTS taskforce_982685;

CREATE DATABASE taskforce_982685
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE taskforce_982685;

-- города (справочник)
CREATE TABLE cities (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY COMMENT 'ID города',
  `city`              VARCHAR(64) NOT NULL          COMMENT 'название города',
  `lat`               FLOAT(10,7) NOT NULL          COMMENT 'широта города',
  `long`              FLOAT(10,7) NOT NULL          COMMENT 'долгота города'
)
COMMENT = 'Города'
ENGINE = InnoDB;

-- локации заданий
CREATE TABLE locations (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY COMMENT 'ID локации',
  `city_id`           BIGINT UNSIGNED NOT NULL      COMMENT 'ID города',
  `lat`               FLOAT(10,7) NOT NULL          COMMENT 'широта локации, введенной пользователем',
  `long`              FLOAT(10,7) NOT NULL          COMMENT 'долгота локации, введенной пользователем',
  FOREIGN KEY (`city_id`) REFERENCES cities(`id`) ON DELETE CASCADE
)
COMMENT = 'Локации заданий'
ENGINE = InnoDB;

-- файлы на диске (вложения, аватары пользователей, фотографии работ и т.д.)
CREATE TABLE files (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY COMMENT 'ID файла',
  `path`              VARCHAR(255) NOT NULL UNIQUE  COMMENT 'путь к файлам на диске относительно корня приложения'
)
COMMENT = 'Файлы на диске (вложения, аватары пользователей, фотографии работ и т.д.)'
ENGINE = InnoDB;

-- пользователи (заказчики и исполнители)
CREATE TABLE users (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY COMMENT 'ID пользователя',
  `role`              ENUM('customer', 'executor') NOT NULL DEFAULT 'customer' COMMENT 'роль (заказчик или исполнитель)',
  -- регистрация:
  `dt_add`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'дата/время регистрации пользователя',
  `name`              VARCHAR(64) NOT NULL              COMMENT 'ФИО пользователя',
  `email`             VARCHAR(64) NOT NULL UNIQUE       COMMENT 'e-mail пользователя (login)',
  `password`          VARCHAR(64) NOT NULL              COMMENT 'пароль',
  `city_id`           BIGINT UNSIGNED NOT NULL          COMMENT 'местонахождение пользователя (ID города)',
  -- профиль пользователя:
  `dt_last_action`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'зафиксированное время последнего действия на сайте',
  `birthday`          DATE DEFAULT NULL                 COMMENT 'дата рождения',
  `about`             VARCHAR(512) DEFAULT NULL         COMMENT 'информация о пользователе',
  `phone`             VARCHAR(32) DEFAULT NULL          COMMENT 'телефон (мобильный)',
  `skype`             VARCHAR(64) DEFAULT NULL          COMMENT 'skype',
  `telegram`          VARCHAR(64) DEFAULT NULL          COMMENT 'telegram',
  `other_messenger`   VARCHAR(64) DEFAULT NULL          COMMENT 'другой мессенжер',
  `cnt_done_tasks`    INT UNSIGNED NOT NULL DEFAULT 0   COMMENT 'количество выполненных работ (завершенных заданий)',
  `cnt_failed_tasks`  INT UNSIGNED NOT NULL DEFAULT 0   COMMENT 'количество проваленных работ (отказов от заданий)',
  `rating`            FLOAT(4, 2) NOT NULL DEFAULT 0    COMMENT 'рейтинг пользователя (от 0.00 до 5.00)',
  `notice_message`    TINYINT NOT NULL DEFAULT 1        COMMENT 'уведомлять о новом сообщении',
  `notice_actions`    TINYINT NOT NULL DEFAULT 1        COMMENT 'уведомлять о действиях по заданию',
  `notice_review`     TINYINT NOT NULL DEFAULT 1        COMMENT 'уведомлять о новом отзыве',
  `hide_profile`      TINYINT NOT NULL DEFAULT 1        COMMENT 'не показывать профиль',
  `customer_only`     TINYINT NOT NULL DEFAULT 0        COMMENT 'показывать контакты только заказчику',
  `avatar_id`         BIGINT UNSIGNED NOT NULL          COMMENT 'ID фотографии пользователя (по умолчанию - аватар-заглушка)',
  FOREIGN KEY (`city_id`) REFERENCES cities(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`avatar_id`) REFERENCES files (`id`) ON DELETE CASCADE
)
COMMENT = 'Пользователи (заказчики и исполнители)'
ENGINE = InnoDB;

-- фотографии работ исполнителя
CREATE TABLE photos_works (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY COMMENT 'ID',
  `photo_id`          BIGINT UNSIGNED NOT NULL          COMMENT 'ID фотографии работы',
  `executor_id`       BIGINT UNSIGNED NOT NULL          COMMENT 'ID исполнителя',
  FOREIGN KEY (`photo_id`) REFERENCES files (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`executor_id`) REFERENCES users (`id`) ON DELETE CASCADE
)
COMMENT = 'Фотографии работ исполнителя'
ENGINE = InnoDB;

-- категории заданий
CREATE TABLE categories (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY COMMENT 'ID категории',
  `category_name`     VARCHAR(128) NOT NULL UNIQUE      COMMENT 'название категории',
  `icon_id`           BIGINT UNSIGNED NOT NULL          COMMENT 'ID файла иконки категории',
  FOREIGN KEY (`icon_id`) REFERENCES files (`id`) ON DELETE CASCADE
)
COMMENT = 'Категории заданий'
ENGINE = InnoDB;

-- специализации исполнителя
CREATE TABLE executor_specialties (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY COMMENT 'ID',
  `executor_id`       BIGINT UNSIGNED NOT NULL          COMMENT 'ID исполнителя',
  `category_id`       BIGINT UNSIGNED NOT NULL          COMMENT 'ID категории',
  FOREIGN KEY (`executor_id`) REFERENCES users (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES categories(`id`) ON DELETE CASCADE
)
COMMENT = 'Специализации исполнителя'
ENGINE = InnoDB;

-- задания (задачи)
CREATE TABLE tasks (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY COMMENT 'ID задания',
  `dt_add`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'дата/время добавления задания',
  `status`            ENUM('new', 'cancelled', 'progress', 'completed', 'failed') NOT NULL DEFAULT 'new' COMMENT 'статус задания',
  `job_essence`       VARCHAR(255) NOT NULL             COMMENT 'краткое описание задания',
  `job_details`       VARCHAR(1024) NOT NULL            COMMENT 'подробности задания',
  `expire`            DATETIME DEFAULT NULL             COMMENT 'срок исполнения',
  `budget`            INT UNSIGNED NOT NULL DEFAULT 0   COMMENT 'бюджет (цена)',
  `location_id`       BIGINT UNSIGNED DEFAULT NULL      COMMENT 'ID локации (адрес исполнения, если задание требует присутствия)',
  `category_id`       BIGINT UNSIGNED NOT NULL          COMMENT 'ID категории',
  `customer_id`       BIGINT UNSIGNED NOT NULL          COMMENT 'ID заказчика',
  `executor_id`       BIGINT UNSIGNED DEFAULT NULL      COMMENT 'ID исполнителя',
  FOREIGN KEY (`location_id`) REFERENCES locations (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`category_id`) REFERENCES categories (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`customer_id`) REFERENCES users (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`executor_id`) REFERENCES users (`id`) ON DELETE SET NULL
)
COMMENT = 'Задания (задачи)'
ENGINE = InnoDB;

-- дополнительные файлы к заданиям (вложения)
CREATE TABLE tasks_attachments (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY COMMENT 'ID',
  `task_id`           BIGINT UNSIGNED NOT NULL          COMMENT 'ID задания',
  `attachment_id`     BIGINT UNSIGNED NOT NULL          COMMENT 'ID файла вложения',
  FOREIGN KEY (`task_id`) REFERENCES tasks (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`attachment_id`) REFERENCES files (`id`) ON DELETE CASCADE
)
COMMENT = 'Дополнительные файлы к заданиям (вложения)'
ENGINE = InnoDB;

-- отзывы об исполнителях
CREATE TABLE reviews (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY COMMENT 'ID отзыва',
  `dt_add`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'дата/время добавления отзыва',
  `eval`              TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'оценка (от 1 до 5)',
  `review_text`       VARCHAR(1024) NOT NULL              COMMENT 'текст отзыва',
  `task_id`           BIGINT UNSIGNED NOT NULL            COMMENT 'ID задания, о котором пишется отзыв',
  `executor_id`       BIGINT UNSIGNED NOT NULL            COMMENT 'ID исполнителя',
  FOREIGN KEY (`task_id`) REFERENCES tasks (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`executor_id`) REFERENCES users (`id`) ON DELETE CASCADE
)
COMMENT = 'Отзывы об исполнителях'
ENGINE = InnoDB;

-- отклики исполнителей на предложенные задания
CREATE TABLE responses (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY COMMENT 'ID отклика',
  `dt_add`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'дата/время добавления отклика',
  `budget`            INT UNSIGNED NOT NULL DEFAULT 0     COMMENT 'предложенная цена',
  `response_text`     VARCHAR(1024) NOT NULL              COMMENT 'текст отклика',
  `task_id`           BIGINT UNSIGNED NOT NULL            COMMENT 'ID задания',
  `executor_id`       BIGINT UNSIGNED NOT NULL            COMMENT 'ID исполнителя',
  FOREIGN KEY (`task_id`) REFERENCES tasks (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`executor_id`) REFERENCES users (`id`) ON DELETE CASCADE
)
COMMENT = 'Отклики исполнителей на предложенные задания'
ENGINE = InnoDB;

-- переписка между заказчиками и исполнителями по поставленным заданиям
CREATE TABLE messages (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY COMMENT 'ID сообщения',
  `dt_add`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'дата/время отправки сообщения',
  `message_text`      VARCHAR(1024) NOT NULL              COMMENT 'текст сообщения',
  `message_read`      TINYINT NOT NULL DEFAULT 0          COMMENT 'признак, было ли прочитано сообщение (по умолчанию - нет)',
  `task_id`           BIGINT UNSIGNED NOT NULL            COMMENT 'ID задания',
  `sender_id`         BIGINT UNSIGNED NOT NULL            COMMENT 'ID отправителя сообщения',
  `recipient_id`      BIGINT UNSIGNED NOT NULL            COMMENT 'ID получателя сообщения',
  FOREIGN KEY (`task_id`) REFERENCES tasks (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`sender_id`) REFERENCES users (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`recipient_id`) REFERENCES users (`id`) ON DELETE CASCADE
)
COMMENT = 'Переписка между заказчиками и исполнителями'
ENGINE = InnoDB;
