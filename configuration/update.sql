/**
 * work module
 * SQL updates
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2022, 2025 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */

/* 2022-03-22-1 */	ALTER TABLE `todos` CHANGE `todo_id` `task_id` int unsigned NOT NULL AUTO_INCREMENT FIRST, CHANGE `todo` `task` varchar(63) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `task_id`, CHANGE `todo_description` `task_description` text COLLATE 'utf8mb4_unicode_ci' NULL AFTER `task`, RENAME TO `tasks`;
/* 2022-03-22-2 */	ALTER TABLE `todos_categories` CHANGE `todo_category_id` `task_category_id` int unsigned NOT NULL AUTO_INCREMENT FIRST, CHANGE `todo_id` `task_id` int unsigned NOT NULL AFTER `task_category_id`, RENAME TO `tasks_categories`;
/* 2022-03-22-3 */	ALTER TABLE `tasks_categories` ADD UNIQUE `task_id` (`task_id`, `category_id`), DROP INDEX `todo_id`;
/* 2022-03-22-4 */	ALTER TABLE `todos_contacts` CHANGE `todo_contact_id` `task_contact_id` int unsigned NOT NULL AUTO_INCREMENT FIRST, CHANGE `todo_id` `task_id` int unsigned NOT NULL AFTER `task_contact_id`, RENAME TO `tasks_contacts`;
/* 2022-03-22-5 */	ALTER TABLE `tasks_contacts` ADD UNIQUE `task_id` (`task_id`, `contact_id`), DROP INDEX `todo_id`;
/* 2022-03-22-6 */	UPDATE `_relations` SET master_table = 'tasks', master_field = 'task_id', detail_field = 'task_id' WHERE master_table = 'todos' AND detail_field = 'todo_id';
/* 2022-03-22-7 */	UPDATE `_relations` SET detail_table = 'tasks', detail_id_field = 'task_id' WHERE detail_table = 'todos' AND detail_id_field = 'todo_id';
/* 2022-03-22-8 */	UPDATE `_relations` SET detail_table = 'tasks_categories', detail_id_field = 'task_category_id' WHERE detail_table = 'todos_categories' AND detail_id_field = 'todo_category_id';
/* 2022-03-22-9 */	UPDATE `_relations` SET detail_table = 'tasks_contacts', detail_id_field = 'task_contact_id' WHERE detail_table = 'todos_contacts' AND detail_id_field = 'todo_contact_id';
/* 2025-02-28-1 */	CREATE TABLE `work` (`work_id` int unsigned NOT NULL AUTO_INCREMENT, `work_begin` datetime NOT NULL, `work_end` datetime NOT NULL, `event_id` int unsigned NOT NULL, `work` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `contact_id` int unsigned NOT NULL, PRIMARY KEY (`work_id`), UNIQUE KEY `work_begin` (`work_begin`), KEY `contact_id` (`contact_id`), KEY `work_end` (`work_end`), KEY `event_id` (`event_id`), KEY `event_id_work_begin` (`event_id`,`work_begin`)) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/* 2025-02-28-2 */	INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'events', 'event_id', (SELECT DATABASE()), 'work', 'work_id', 'event_id', 'no-delete');
/* 2025-02-28-3 */	INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'contacts', 'contact_id', (SELECT DATABASE()), 'work', 'work_id', 'contact_id', 'no-delete');
/* 2025-02-28-4 */	CREATE TABLE `work_categories` (`wc_id` int unsigned NOT NULL AUTO_INCREMENT, `work_id` int unsigned NOT NULL, `category_id` int unsigned NOT NULL, `duration_minutes` smallint unsigned DEFAULT NULL, PRIMARY KEY (`wc_id`), UNIQUE KEY `work_id_category_id` (`work_id`,`category_id`), KEY `category_id` (`category_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/* 2025-02-28-5 */	INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'work', 'work_id', (SELECT DATABASE()), 'work_categories', 'wc_id', 'work_id', 'no-delete');
/* 2025-02-28-6 */	INSERT INTO _relations (`master_db`, `master_table`, `master_field`, `detail_db`, `detail_table`, `detail_id_field`, `detail_field`, `delete`) VALUES ((SELECT DATABASE()), 'categories', 'category_id', (SELECT DATABASE()), 'work_categories', 'wc_id', 'category_id', 'no-delete');
/* 2025-02-28-7 */	INSERT INTO categories (`category`, `description`, `main_category_id`, `path`, `parameters`, `sequence`, `last_update`) VALUES ('Tags', NULL, NULL, 'tags', 'alias=tags', NULL, NOW());
/* 2025-02-28-8 */	CREATE OR REPLACE VIEW `work_view` AS SELECT `work`.`work_id` AS `work_id`, `work`.`work_begin` AS `work_begin`, `work`.`work_end` AS `work_end`, `work`.`event_id` AS `event_id`, `work`.`work` AS `work`, `work`.`contact_id` AS `contact_id`, TIMESTAMPDIFF(MINUTE,`work`.`work_begin`,`work`.`work_end`) AS `duration_minutes` FROM `work`;
