/**
 * work module
 * SQL updates
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2022 Gustaf Mossakowski
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

