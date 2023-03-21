<?php 

/**
 * work module
 * table definition for tasks/categories
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2011-2012, 2014, 2017, 2019-2020, 2022-2023 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */
 

$zz['title'] = 'Task Categories';
$zz['table'] = 'tasks_categories';

$zz['fields'][1]['title'] = 'ID';
$zz['fields'][1]['field_name'] = 'task_category_id';
$zz['fields'][1]['type'] = 'id';

$zz['fields'][2]['title'] = 'Task';
$zz['fields'][2]['field_name'] = 'task_id';
$zz['fields'][2]['type'] = 'select';
$zz['fields'][2]['sql'] = 'SELECT task_id, task
	FROM tasks ORDER BY task';
$zz['fields'][2]['display_field'] = 'task';

$zz['fields'][3]['field_name'] = 'category_id';
$zz['fields'][3]['type'] = 'select';
$zz['fields'][3]['sql'] = 'SELECT category_id, category, main_category_id
	FROM categories ORDER BY category';
$zz['fields'][3]['show_hierarchy'] = 'main_category_id';
$zz['fields'][3]['display_field'] = 'category';
$zz['fields'][3]['show_hierarchy_subtree'] = wrap_category_id('tasks');
if (wrap_setting('default_categories_form_path'))
	$zz['fields'][3]['add_details'] = sprintf('%s?filter[maincategory]=%d'
	, wrap_setting('default_categories_form_path'), wrap_category_id('tasks'));


$zz['sql'] = 'SELECT tasks_categories.*
		, category, task
	FROM tasks_categories
	LEFT JOIN categories USING (category_id)
	LEFT JOIN tasks USING (task_id)
';
$zz['sqlorder'] = ' ORDER BY category';
