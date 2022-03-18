<?php 

/**
 * work module
 * table definition for tasks
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2021-2022 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */
 

$zz['title'] = 'Tasks';
$zz['table'] = 'todos';

$zz['fields'][1]['title'] = 'ID';
$zz['fields'][1]['field_name'] = 'todo_id';
$zz['fields'][1]['type'] = 'id';

$zz['fields'][2]['title'] = 'Task';
$zz['fields'][2]['field_name'] = 'todo';
$zz['fields'][2]['type'] = 'text';
$zz['fields'][2]['list_append_next'] = true;
$zz['fields'][2]['list_prefix'] = '<strong>';
$zz['fields'][2]['list_suffix'] = '</strong>';

$zz['fields'][3]['title'] = 'Description';
$zz['fields'][3]['field_name'] = 'todo_description';
$zz['fields'][3]['type'] = 'memo';
$zz['fields'][3]['format'] = 'markdown';
$zz['fields'][3]['list_format'] = 'markdown';
$zz['fields'][3]['list_prefix'] = '<div class="moretext">';
$zz['fields'][3]['list_suffix'] = '</div>';

$zz['fields'][22] = zzform_include_table('tasks-categories');
$zz['fields'][22]['title'] = 'Tags';
$zz['fields'][22]['type'] = 'subtable';
$zz['fields'][22]['min_records'] = 0;
$zz['fields'][22]['max_records'] = 10;
$zz['fields'][22]['form_display'] = 'lines';
$zz['fields'][22]['fields'][2]['type'] = 'foreign_key';
$zz['fields'][22]['subselect']['sql'] = sprintf('SELECT todo_id
		, CONCAT(IFNULL(main_categories.category, ""), " ", categories.category) AS category
	FROM todos_categories
	LEFT JOIN categories
		ON todos_categories.category_id = categories.category_id
	LEFT JOIN categories main_categories
		ON categories.main_category_id = main_categories.category_id
		AND main_categories.category_id != %d
', wrap_category_id('todos'));
$zz['fields'][22]['subselect']['concat_fields'] = ', ';

$zz['fields'][23] = zzform_include_table('tasks-contacts');
$zz['fields'][23]['title'] = 'Task for';
$zz['fields'][23]['type'] = 'subtable';
$zz['fields'][23]['min_records'] = 0;
$zz['fields'][23]['max_records'] = 10;
$zz['fields'][23]['form_display'] = 'lines';
$zz['fields'][23]['fields'][2]['type'] = 'foreign_key';
$zz['fields'][23]['subselect']['sql'] = 'SELECT todo_id, contact
	FROM todos_contacts
	LEFT JOIN contacts USING (contact_id)
';
$zz['fields'][23]['subselect']['concat_fields'] = ', ';

$zz['fields'][5]['title_tab'] = '<abbr title="Priority">!</abbr>';
$zz['fields'][5]['field_name'] = 'priority';
$zz['fields'][5]['type'] = 'select';
$zz['fields'][5]['enum'] = ['high', 'medium', 'low', 'none'];
$zz['fields'][5]['enum_title'] = [
	wrap_text('high'), wrap_text('medium'), wrap_text('low'),
	wrap_text('none (= idea)')
];
$zz['fields'][5]['default'] = 'low';
$zz['fields'][5]['show_values_as_list'] = true;
if (!empty($_GET['filter']['type']) AND $_GET['filter']['type'] === 'none') {
	$zz['fields'][5]['hide_in_list'] = true;
}

$zz['fields'][10]['title_tab'] = 'Seq.';
$zz['fields'][10]['field_name'] = 'sequence';
$zz['fields'][10]['type'] = 'number';

$zz['fields'][6]['field_name'] = 'deadline';
$zz['fields'][6]['type'] = 'date';
$zz['fields'][6]['default'] = date('d.m.Y');
if (!empty($_GET['filter']['type']) AND $_GET['filter']['type'] === 'none') {
	$zz['fields'][6]['hide_in_list'] = true;
}
$zz['fields'][6]['group_in_list'] = true;

$zz['fields'][65]['field_name'] = 'sort_order';
$zz['fields'][65]['type'] = 'display';
$zz['fields'][65]['hide_in_form'] = true;
$zz['fields'][65]['hide_in_list'] = true;
$zz['fields'][65]['group_in_list'] = true;
$zz['fields'][65]['exclude_from_search'] = true;

$zz['fields'][8]['field_name'] = 'time';
$zz['fields'][8]['type'] = 'time';
$zz['fields'][8]['hide_in_list'] = true;

$zz['fields'][9]['title'] = 'Done on';
$zz['fields'][9]['field_name'] = 'done';
$zz['fields'][9]['type'] = 'date';
$zz['fields'][9]['hide_in_list'] = true;

$zz['fields'][11]['title'] = 'Author';
$zz['fields'][11]['field_name'] = 'author_contact_id';
$zz['fields'][11]['type'] = 'write_once';
$zz['fields'][11]['type_detail'] = 'select';
$zz['fields'][11]['sql'] = 'SELECT contact_id
		, contact, identifier
	FROM contacts
	ORDER BY contact';
$zz['fields'][11]['key_field_name'] = 'contact_id';
$zz['fields'][11]['exclude_from_search'] = true;
$zz['fields'][11]['hide_in_list'] = true;
$zz['fields'][11]['default'] = $_SESSION['contact_id'];

/*
$zz['fields'][12]['field_name'] = 'reminder';
$zz['fields'][12]['type'] = 'option';
$zz['fields'][12]['type_detail'] = 'select';
$zz['fields'][12]['enum'] = ['yes', 'no'];
$zz['fields'][12]['enum_title'] = [wrap_text('yes'), wrap_text('no')];
$zz['fields'][12]['default'] = 'nein';
$zz['fields'][12]['if']['add']['default'] = 'yes';
$zz['fields'][12]['explanation'] = 'If “yes”, the task is sent as an email to the people involved.';
*/

$zz['sql'] = 'SELECT todos.*
	, IF(deadline, "A with deadline", "B without deadline") AS sort_order
	FROM todos
';
$zz['sqlorder'] = ' ORDER BY sort_order, deadline ASC, time ASC, ISNULL(sequence), sequence, priority';

/*
$zz['hooks']['after_insert'] =
$zz['hooks']['after_update'] = 'task_reminder';
*/

if (empty($_GET['where']['project_id'])) {
	$zz['filter'][1]['title'] = wrap_text('Type');
	$zz['filter'][1]['identifier'] = 'type';
	$zz['filter'][1]['type'] = 'list';
	$zz['filter'][1]['where'] = 'priority';
	$zz['filter'][1]['selection']['none'] = wrap_text('Idea');
	$zz['filter'][1]['selection']['!none'] = wrap_text('Task');
	$zz['filter'][1]['default_selection'] = '!none';

	$zz['filter'][2]['title'] = wrap_text('Status');
	$zz['filter'][2]['identifier'] = 'status';
	$zz['filter'][2]['type'] = 'list';
	$zz['filter'][2]['where'] = 'done';
	$zz['filter'][2]['selection']['NULL'] = wrap_text('pending');
	$zz['filter'][2]['selection']['!NULL'] = wrap_text('done');
	$zz['filter'][2]['default_selection'] = 'NULL';

/*
	$zz['filter'][3]['title'] = 'Persons';
	$zz['filter'][3]['identifier'] = 'person';
	$zz['filter'][3]['type'] = 'list';
	$zz['filter'][3]['where'] = 'contact_id';
	$zz['filter'][3]['selection'][$_SESSION['contact_id']] = 'Own';
*/
}
