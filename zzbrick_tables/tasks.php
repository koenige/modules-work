<?php 

/**
 * work module
 * table definition for tasks
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2009-2013, 2017, 2019-2023 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */
 

$zz['title'] = 'Tasks';
$zz['table'] = 'tasks';

$zz['fields'][1]['title'] = 'ID';
$zz['fields'][1]['field_name'] = 'task_id';
$zz['fields'][1]['type'] = 'id';

$zz['fields'][2]['title'] = 'Task';
$zz['fields'][2]['field_name'] = 'task';
$zz['fields'][2]['type'] = 'text';
$zz['fields'][2]['list_append_next'] = true;
$zz['fields'][2]['list_prefix'] = '<strong>';
$zz['fields'][2]['list_suffix'] = '</strong>';

$zz['fields'][3]['title'] = 'Description';
$zz['fields'][3]['field_name'] = 'task_description';
$zz['fields'][3]['type'] = 'memo';
$zz['fields'][3]['format'] = 'markdown';
$zz['fields'][3]['list_format'] = 'markdown';
$zz['fields'][3]['list_prefix'] = '<div class="moretext">';
$zz['fields'][3]['list_suffix'] = '</div>';

$zz['fields'][22] = zzform_include('tasks-categories');
$zz['fields'][22]['title'] = 'Tags';
$zz['fields'][22]['type'] = 'subtable';
$zz['fields'][22]['min_records'] = 1;
$zz['fields'][22]['max_records'] = 10;
$zz['fields'][22]['form_display'] = 'lines';
$zz['fields'][22]['fields'][2]['type'] = 'foreign_key';
$zz['fields'][22]['subselect']['sql'] = sprintf('SELECT task_id
		, CONCAT(IFNULL(main_categories.category, ""), " ", categories.category) AS category
	FROM tasks_categories
	LEFT JOIN categories
		ON tasks_categories.category_id = categories.category_id
	LEFT JOIN categories main_categories
		ON categories.main_category_id = main_categories.category_id
		AND main_categories.category_id != %d
', wrap_category_id('tasks'));
$zz['fields'][22]['subselect']['concat_fields'] = ', ';
$zz['fields'][22]['hide_in_list_if_empty'] = true;

$zz['fields'][23] = zzform_include('tasks-contacts');
$zz['fields'][23]['title'] = 'Task for';
$zz['fields'][23]['type'] = 'subtable';
$zz['fields'][23]['hide_in_list_if_empty'] = true;
$zz['fields'][23]['min_records'] = 0;
$zz['fields'][23]['max_records'] = 10;
$zz['fields'][23]['form_display'] = 'lines';
$zz['fields'][23]['fields'][2]['type'] = 'foreign_key';
$zz['fields'][23]['subselect']['sql'] = 'SELECT task_id, contact
	FROM tasks_contacts
	LEFT JOIN contacts USING (contact_id)
';
$zz['fields'][23]['subselect']['concat_fields'] = ', ';

$zz['fields'][4] = []; // link to projects

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
$zz['fields'][10]['hide_in_list_if_empty'] = true;

$zz['fields'][6]['field_name'] = 'deadline';
$zz['fields'][6]['type'] = 'date';
$zz['fields'][6]['hide_in_list_if_empty'] = true;
if (!empty($_GET['filter']['type']) AND $_GET['filter']['type'] === 'none') {
	$zz['fields'][6]['hide_in_list'] = true;
}
$zz['fields'][6]['group_in_list'] = true;

$zz['fields'][8]['field_name'] = 'time';
$zz['fields'][8]['type'] = 'time';
$zz['fields'][8]['hide_in_list'] = true;

$zz['fields'][12]['field_name'] = 'created';
$zz['fields'][12]['type'] = 'hidden';
$zz['fields'][12]['type_detail'] = 'date';
$zz['fields'][12]['default'] = date('Y-m-d');
$zz['fields'][12]['hide_in_list'] = true;

$zz['fields'][9]['title'] = 'Done on';
$zz['fields'][9]['field_name'] = 'done';
$zz['fields'][9]['type'] = 'date';
$zz['fields'][9]['hide_in_list'] = true;

$zz['fields'][11]['title'] = 'Author';
$zz['fields'][11]['field_name'] = 'author_contact_id';
$zz['fields'][11]['type'] = 'write_once';
$zz['fields'][11]['type_detail'] = 'select';
$zz['fields'][11]['sql'] = sprintf('SELECT contact_id
		, contact, identifier
	FROM contacts
	WHERE contact_category_id = %d
	ORDER BY contact', wrap_category_id('contact/person'));
$zz['fields'][11]['key_field_name'] = 'contact_id';
$zz['fields'][11]['exclude_from_search'] = true;
$zz['fields'][11]['hide_in_list'] = true;
$zz['fields'][11]['default'] = $_SESSION['user_id'];

$zz['fields'][13]['field_name'] = 'reminder';
$zz['fields'][13]['type'] = 'option';
$zz['fields'][13]['type_detail'] = 'select';
$zz['fields'][13]['enum'] = ['yes', 'no'];
$zz['fields'][13]['enum_title'] = [wrap_text('yes'), wrap_text('no')];
$zz['fields'][13]['default'] = 'no';
$zz['fields'][13]['if']['insert']['default'] = 'yes';
$zz['fields'][13]['explanation'] = 'If “yes”, the task is sent as an email to the people involved.';

$zz['fields'][65]['field_name'] = 'sort_order';
$zz['fields'][65]['type'] = 'display';
$zz['fields'][65]['hide_in_form'] = true;
$zz['fields'][65]['hide_in_list'] = true;
$zz['fields'][65]['group_in_list'] = true;
$zz['fields'][65]['exclude_from_search'] = true;

$zz['sql'] = 'SELECT tasks.*
	, IF(deadline, "A with deadline", "B without deadline") AS sort_order
	FROM tasks
';
$zz['sqlorder'] = ' ORDER BY sort_order, deadline ASC, time ASC, ISNULL(sequence), sequence, priority';

$zz['hooks']['after_insert'] =
$zz['hooks']['after_update'] = 'mf_work_task_reminder';

if (empty($_GET['where']['project_id'])) {
	$zz['filter'][1]['title'] = wrap_text('Type');
	$zz['filter'][1]['identifier'] = 'type';
	$zz['filter'][1]['type'] = 'list';
	$zz['filter'][1]['where'] = 'priority';
	$zz['filter'][1]['selection']['none'] = wrap_text('Idea');
	$zz['filter'][1]['selection']['!none'] = wrap_text('Task');
	$zz['filter'][1]['default_selection'] = '!none';

	$zz['filter'][5]['title'] = wrap_text('Status');
	$zz['filter'][5]['identifier'] = 'status';
	$zz['filter'][5]['type'] = 'list';
	$zz['filter'][5]['where'] = 'done';
	$zz['filter'][5]['selection']['NULL'] = wrap_text('pending');
	$zz['filter'][5]['selection']['!NULL'] = wrap_text('done');
	$zz['filter'][5]['default_selection'] = 'NULL';

/*
	$zz['filter'][6]['title'] = 'Persons';
	$zz['filter'][6]['identifier'] = 'person';
	$zz['filter'][6]['type'] = 'list';
	$zz['filter'][6]['where'] = 'contact_id';
	$zz['filter'][6]['selection'][$_SESSION['contact_id']] = 'Own';
*/
}

$zz['filter'][3]['title'] = wrap_text('Tag');
$zz['filter'][3]['sql'] = 'SELECT DISTINCT category_id, SUBSTRING(path, 6)
	FROM tasks_categories
	LEFT JOIN categories USING (category_id)
	LEFT JOIN tasks USING (task_id)
	ORDER BY SUBSTRING(path, 6)
';
$zz['filter'][3]['identifier'] = 'tag';
$zz['filter'][3]['type'] = 'list';
$zz['filter'][3]['sql_join'] = 'LEFT JOIN tasks_categories USING (task_id)';
$zz['filter'][3]['where'] = 'tasks_categories.category_id';

$zz['filter'][4]['title'] = wrap_text('Period');
$zz['filter'][4]['identifier'] = 'period';
$zz['filter'][4]['type'] = 'list';
$zz['filter'][4]['where_if'][0] = '';
$zz['filter'][4]['where_if'][1] = 'YEARWEEK(deadline, 1) < YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 28 DAY), 1) AND ISNULL(done)';
$zz['filter'][4]['where_if'][2] = 'YEARWEEK(deadline, 1) < YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 7 DAY), 1) AND YEARWEEK(deadline, 1) >= YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 28 DAY), 1) AND ISNULL(done)';
$zz['filter'][4]['where_if'][3] = 'YEARWEEK(deadline, 1) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 7 DAY), 1) AND ISNULL(done)';
$zz['filter'][4]['where_if'][4] = 'YEARWEEK(deadline, 1) = YEARWEEK(CURDATE(), 1) AND ISNULL(done)';
$zz['filter'][4]['where_if'][5] = 'YEARWEEK(deadline, 1) = YEARWEEK(DATE_ADD(CURDATE(), INTERVAL 7 DAY), 1) AND ISNULL(done)';
$zz['filter'][4]['where_if'][6] = 'YEARWEEK(deadline, 1) > YEARWEEK(DATE_ADD(CURDATE(), INTERVAL 7 DAY), 1) AND YEARWEEK(deadline, 1) <= YEARWEEK(DATE_ADD(CURDATE(), INTERVAL 28 DAY), 1) AND ISNULL(done)';
$zz['filter'][4]['where_if'][7] = 'YEARWEEK(deadline, 1) > YEARWEEK(DATE_ADD(CURDATE(), INTERVAL 28 DAY), 1) AND ISNULL(done)';
$zz['filter'][4]['where_if'][8] = 'ISNULL(deadline) AND ISNULL(done)';
$zz['filter'][4]['selection'][1] = wrap_text('long ago');
$zz['filter'][4]['selection'][2] = wrap_text('minus four weeks');
$zz['filter'][4]['selection'][3] = wrap_text('last week');
$zz['filter'][4]['selection'][4] = wrap_text('this week');
$zz['filter'][4]['selection'][5] = wrap_text('next week');
$zz['filter'][4]['selection'][6] = wrap_text('plus four weeks');
$zz['filter'][4]['selection'][7] = wrap_text('far away');
$zz['filter'][4]['selection'][8] = wrap_text('no deadline');
$zz['filter'][4]['default_selection'] = 4;

$zz['setting']['zzform_limit'] = 40;
$zz['setting']['zzform_search_form_always'] = true;
