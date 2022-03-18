<?php 

/**
 * work module
 * table definition for tasks/categories
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2011-2012, 2017, 2019-2020, 2022 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */
 

$zz['title'] = 'Task Categories';
$zz['table'] = 'todos_categories';

$zz['fields'][1]['title'] = 'ID';
$zz['fields'][1]['field_name'] = 'todo_category_id';
$zz['fields'][1]['type'] = 'id';

$zz['fields'][2]['title'] = 'Task';
$zz['fields'][2]['field_name'] = 'todo_id';
$zz['fields'][2]['type'] = 'select';
$zz['fields'][2]['sql'] = 'SELECT todo_id, todo
	FROM todos ORDER BY todo';
$zz['fields'][2]['display_field'] = 'todo';

$zz['fields'][3]['field_name'] = 'category_id';
$zz['fields'][3]['type'] = 'select';
$zz['fields'][3]['sql'] = 'SELECT category_id, category, main_category_id
	FROM categories ORDER BY category';
$zz['fields'][3]['show_hierarchy'] = 'main_category_id';
$zz['fields'][3]['display_field'] = 'category';
$zz['fields'][3]['show_hierarchy_subtree'] = wrap_category_id('tasks');
if (wrap_get_setting('default_categories_form_path'))
	$zz['fields'][3]['add_details'] = wrap_get_setting('default_categories_form_path');


$zz['sql'] = 'SELECT todos_categories.*
		, category, todo
	FROM todos_categories
	LEFT JOIN categories USING (category_id)
	LEFT JOIN todos USING (todo_id)
';
$zz['sqlorder'] = ' ORDER BY category';
