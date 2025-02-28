/**
 * work module
 * SQL for installation
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2022 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */


-- tasks --
CREATE TABLE `tasks` (
  `task_id` int unsigned NOT NULL AUTO_INCREMENT,
  `task` varchar(63) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `priority` enum('high','medium','low','none') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'none',
  `sequence` decimal(4,2) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `created` date DEFAULT NULL,
  `done` date DEFAULT NULL,
  `author_contact_id` int unsigned NOT NULL,
  PRIMARY KEY (`task_id`),
  KEY `priority` (`priority`),
  KEY `author_contact_id` (`author_contact_id`),
  KEY `deadline` (`deadline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'contacts', 'contact_id', (SELECT DATABASE()), 'tasks', 'task_id', 'author_contact_id', 'delete');


-- tasks_categories --
CREATE TABLE `tasks_categories` (
  `task_category_id` int unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int unsigned NOT NULL,
  `category_id` int unsigned NOT NULL,
  PRIMARY KEY (`task_category_id`),
  UNIQUE KEY `task_id` (`task_id`,`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'tasks', 'task_id', (SELECT DATABASE()), 'tasks_categories', 'task_category_id', 'task_id', 'delete');
INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'categories', 'category_id', (SELECT DATABASE()), 'tasks_categories', 'task_category_id', 'category_id', 'no-delete');


-- tasks_contacts --
CREATE TABLE `tasks_contacts` (
  `task_contact_id` int unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int unsigned NOT NULL,
  `contact_id` int unsigned NOT NULL,
  PRIMARY KEY (`task_contact_id`),
  UNIQUE KEY `task_id` (`task_id`,`contact_id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'tasks', 'task_id', (SELECT DATABASE()), 'tasks_contacts', 'task_contact_id', 'task_id', 'delete');
INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'contacts', 'contact_id', (SELECT DATABASE()), 'tasks_contacts', 'task_contact_id', 'contact_id', 'no-delete');


-- work --
CREATE TABLE `work` (
  `work_id` int unsigned NOT NULL AUTO_INCREMENT,
  `work_begin` datetime NOT NULL,
  `work_end` datetime NOT NULL,
  `event_id` int unsigned NOT NULL,
  `work` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `contact_id` int unsigned NOT NULL,
  PRIMARY KEY (`work_id`),
  UNIQUE KEY `work_begin` (`work_begin`),
  KEY `contact_id` (`contact_id`),
  KEY `work_end` (`work_end`),
  KEY `event_id` (`event_id`),
  KEY `event_id_work_begin` (`event_id`,`work_begin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'events', 'event_id', (SELECT DATABASE()), 'work', 'work_id', 'event_id', 'no-delete');
INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'contacts', 'contact_id', (SELECT DATABASE()), 'work', 'work_id', 'contact_id', 'no-delete');


-- work_categories --
CREATE TABLE `work_categories` (
  `wc_id` int unsigned NOT NULL AUTO_INCREMENT,
  `work_id` int unsigned NOT NULL,
  `category_id` int unsigned NOT NULL,
  `duration_minutes` smallint unsigned DEFAULT NULL,
  PRIMARY KEY (`wc_id`),
  UNIQUE KEY `work_id_category_id` (`work_id`,`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'work', 'work_id', (SELECT DATABASE()), 'work_categories', 'wc_id', 'work_id', 'no-delete');
INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'categories', 'category_id', (SELECT DATABASE()), 'work_categories', 'wc_id', 'category_id', 'no-delete');

INSERT INTO categories (`category`, `description`, `main_category_id`, `path`, `parameters`, `sequence`, `last_update`) VALUES ('Tags', NULL, NULL, 'tags', 'alias=tags', NULL, NOW());


-- work_view ---
CREATE OR REPLACE VIEW `work_view` AS
	SELECT `work`.`work_id` AS `work_id`, `work`.`work_begin` AS `work_begin`,
		`work`.`work_end` AS `work_end`, `work`.`event_id` AS `event_id`,
		`work`.`work` AS `work`, `work`.`contact_id` AS `contact_id`,
		TIMESTAMPDIFF(MINUTE,`work`.`work_begin`,`work`.`work_end`) AS `duration_minutes`
	FROM `work`;
