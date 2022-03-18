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


CREATE TABLE `todos` (
  `todo_id` int unsigned NOT NULL AUTO_INCREMENT,
  `todo` varchar(63) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `todo_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `priority` enum('high','medium','low','none') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'none',
  `sequence` decimal(4,2) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `created` date DEFAULT NULL,
  `done` date DEFAULT NULL,
  `author_contact_id` int unsigned NOT NULL,
  PRIMARY KEY (`todo_id`),
  KEY `priority` (`priority`),
  KEY `author_contact_id` (`author_contact_id`),
  KEY `deadline` (`deadline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'contacts', 'contact_id', (SELECT DATABASE()), 'todos', 'todo_id', 'author_contact_id', 'delete');


CREATE TABLE `todos_categories` (
  `todo_category_id` int unsigned NOT NULL AUTO_INCREMENT,
  `todo_id` int unsigned NOT NULL,
  `category_id` int unsigned NOT NULL,
  PRIMARY KEY (`todo_category_id`),
  UNIQUE KEY `todo_id` (`todo_id`,`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'todos', 'todo_id', (SELECT DATABASE()), 'todos_categories', 'todo_category_id', 'todo_id', 'delete');
INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'categories', 'category_id', (SELECT DATABASE()), 'todos_categories', 'todo_category_id', 'category_id', 'no-delete');


CREATE TABLE `todos_contacts` (
  `todo_contact_id` int unsigned NOT NULL AUTO_INCREMENT,
  `todo_id` int unsigned NOT NULL,
  `contact_id` int unsigned NOT NULL,
  PRIMARY KEY (`todo_contact_id`),
  UNIQUE KEY `todo_id` (`todo_id`,`contact_id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'todos', 'todo_id', (SELECT DATABASE()), 'todos_contacts', 'todo_contact_id', 'todo_id', 'delete');
INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'contacts', 'contact_id', (SELECT DATABASE()), 'todos_contacts', 'todo_contact_id', 'contact_id', 'no-delete');
