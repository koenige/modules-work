<?php 

/**
 * work module
 * table definition for work
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2009-2013, 2015-2017, 2019-2020, 2022-2023, 2025 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */


$zz['table'] = 'work';
$zz['title'] = 'Working Hours';

$zz['fields'][1]['title'] = 'ID';
$zz['fields'][1]['field_name'] = 'work_id';
$zz['fields'][1]['type'] = 'id';

$zz['fields'][2]['title'] = 'Start';
$zz['fields'][2]['field_name'] = 'work_begin';
$zz['fields'][2]['type'] = 'datetime';
$zz['fields'][2]['default'] = 'current_date';
$zz['fields'][2]['round_date'] = true;
$zz['fields'][2]['buttons'][] = 'round_date';

$zz['fields'][3]['title'] = 'End';
$zz['fields'][3]['field_name'] = 'work_end';
$zz['fields'][3]['type'] = 'datetime';
$zz['fields'][3]['default'] = 'current_date';
$zz['fields'][3]['round_date'] = true;
$zz['fields'][3]['buttons'][] = 'round_date';
$zz['fields'][3]['validate']['>='] = ['work_begin'];
$zz['fields'][3]['validate_msg']['>='] = 'The end must be after the beginning.';

$zz['fields'][4]['title'] = 'Project';
$zz['fields'][4]['field_name'] = 'event_id';
$zz['fields'][4]['type'] = 'select';
$zz['fields'][4]['sql'] = 'SELECT event_id, event
	FROM events
	ORDER BY event';
$zz['fields'][4]['display_field'] = 'event';
$zz['fields'][4]['link'] = [
	'area' => 'events_project',
	'fields' => ['identifier']
];
$zz['fields'][4]['link_title'] = [
	'string1' => wrap_text('Website for project '),
	'field1' => 'event'
];
$zz['fields'][4]['add_details'] = wrap_path('events_projects_internal');
$zz['fields'][4]['add_details_target'] = '_blank';
$zz['fields'][4]['if']['where']['class'] = 'hidden';
$zz['fields'][4]['if']['where']['hide_in_list'] = true;
$zz['fields'][4]['sql_translate'] = ['event_id' => 'events'];

$zz['fields'][5]['title'] = 'Description';
$zz['fields'][5]['field_name'] = 'work';
$zz['fields'][5]['type'] = 'memo';
$zz['fields'][5]['format'] = 'markdown';
$zz['fields'][5]['list_append_next'] = true;

$zz['fields'][12] = zzform_include('work-categories');
$zz['fields'][12]['title'] = 'Tags';
$zz['fields'][12]['type'] = 'subtable';
$zz['fields'][12]['fields'][2]['type'] = 'foreign_key';
$zz['fields'][12]['min_records'] = 1;
$zz['fields'][12]['form_display'] = 'lines';

$zz['fields'][11]['title'] = 'Who?';
$zz['fields'][11]['field_name'] = 'contact_id';
$zz['fields'][11]['type'] = 'select';
$zz['fields'][11]['sql'] = 'SELECT contact_id, contact
	FROM contacts
	ORDER BY contact';
$zz['fields'][11]['display_field'] = 'contact';
$zz['fields'][11]['if']['where']['class'] = 'hidden';
$zz['fields'][11]['if']['where']['hide_in_list'] = true;
$zz['fields'][11]['add_details'] = wrap_path('contacts_persons');
$zz['fields'][11]['list_format'] = 'nl2br';
$zz['fields'][11]['default'] = $_SESSION['user_id'];
if (wrap_db_data('work_logins_count') == 1) {
	$zz['fields'][11]['hide_in_form'] = true;
	$zz['fields'][11]['hide_in_list'] = true;
}

if (wrap_package('finance')) {
	$zz['fields'][13] = zzform_include('positions-work');
	$zz['fields'][13]['title'] = 'Position';
	$zz['fields'][13]['title_tab'] = 'Pos.';
	$zz['fields'][13]['type'] = 'subtable';
	$zz['fields'][13]['form_display'] = 'lines';
	$zz['fields'][13]['min_records'] = 1;
	$zz['fields'][13]['max_records'] = 1;
	$zz['fields'][13]['fields'][3]['type'] = 'foreign_key';
	$zz['fields'][13]['fields'][4]['hide_in_form'] = true;
	$zz['fields'][13]['subselect']['sql'] = 'SELECT work_id, document_no, position_no
	    FROM positions_work
	    LEFT JOIN positions USING (position_id)
	    LEFT JOIN documents USING (document_id)';
	$zz['fields'][13]['subselect']['concat_fields'] = '/';
	$zz['fields'][13]['hide_in_list_if_empty'] = true;
}

$zz['fields'][6]['title'] = 'Δ Time';
$zz['fields'][6]['field_name'] = 'duration_minutes';
$zz['fields'][6]['type'] = 'display';
$zz['fields'][6]['type_detail'] = 'number';
$zz['fields'][6]['sum'] = true;
$zz['fields'][6]['format'] = 'wrap_duration';
$zz['fields'][6]['hide_format_in_title_desc'] = true;
$zz['fields'][6]['list_format'] = 'wrap_duration';
$zz['fields'][6]['exclude_from_search'] = true;
$zz['fields'][6]['if']['add']['hide_in_form'] = true;
wrap_setting('duration_format', 'H:i');

$zz['fields'][7]['field_name'] = 'week';
$zz['fields'][7]['type'] = 'display';
$zz['fields'][7]['hide_in_list'] = true;
$zz['fields'][7]['hide_in_form'] = true;
$zz['fields'][7]['exclude_from_search'] = true;

$zz['fields'][8]['field_name'] = 'month';
$zz['fields'][8]['type'] = 'display';
$zz['fields'][8]['hide_in_list'] = true;
$zz['fields'][8]['hide_in_form'] = true;
$zz['fields'][8]['exclude_from_search'] = true;


$zz['sql'] = 'SELECT work.* 
		, event
		, events.identifier
		, CONCAT(YEAR(work_begin), "/", WEEK(work_begin, 1)) AS week
		, CONCAT(YEAR(work_begin), "/", MONTH(work_begin)) AS month
		, contacts.contact
		, (SELECT duration_minutes * 60 FROM work_view WHERE work_view.work_id = work.work_id) AS duration_minutes
	FROM work
	LEFT JOIN contacts USING (contact_id)
	LEFT JOIN events USING (event_id)
';
$zz['sqlorder'] = ' ORDER BY work_begin DESC';
$zz['sql_translate'] = ['event_id' => 'events'];

$zz['list']['tfoot'] = true;
$zz['subtitle']['event_id']['sql'] = $zz['fields'][4]['sql'];
$zz['subtitle']['event_id']['var'] = ['event'];

$zz['export'][] = 'CSV';
$zz['record']['copy'] = true;

$where_condition = [];
if (!empty($_GET['where']['event_id']))
	$where_condition[] = sprintf('event_id = %d', $_GET['where']['event_id']);
$zz['filter'][1]['sql'] = 'SELECT DISTINCT YEAR(work_begin) AS year_idf
		, YEAR(work_begin) AS year
	FROM work
	'.($where_condition ? 'WHERE '.implode(' AND ', $where_condition) : '').'
	ORDER BY YEAR(work_begin) DESC';
$zz['filter'][1]['title'] = wrap_text('Year');
$zz['filter'][1]['identifier'] = 'year';
$zz['filter'][1]['type'] = 'list';
$zz['filter'][1]['where'] = 'YEAR(work_begin)';

if (!empty($_GET['filter']['year'])) {
	$where_condition[] = sprintf(' YEAR(work_begin) = %d', $_GET['filter']['year']);
	$zz['filter'][2]['sql'] = 'SELECT DISTINCT CONCAT(YEAR(work_begin), "/", MONTH(work_begin)) AS month
			, CONCAT(YEAR(work_begin), "/", MONTH(work_begin)) AS month
			, work_begin
		FROM work
		'.($where_condition ? 'WHERE '.implode(' AND ', $where_condition) : '').'
		ORDER BY work_begin DESC';
	$zz['filter'][2]['title'] = wrap_text('Month');
	$zz['filter'][2]['identifier'] = 'month';
	$zz['filter'][2]['type'] = 'list';
	$zz['filter'][2]['depends_on'] = 1;
	$zz['filter'][2]['where'] = 'CONCAT(YEAR(work_begin), "/", MONTH(work_begin))';
}

if (!empty($_GET['filter']['month']))
	$where_condition[] = sprintf(' MONTH(work_begin) = %d', substr(urldecode($_GET['filter']['month']), strrpos(urldecode($_GET['filter']['month']), '/') + 1));
$zz['filter'][3]['title'] = wrap_text('Tag');
$zz['filter'][3]['sql'] = 'SELECT category_id,
		CONCAT(SUBSTRING(path, 6), " ("
			, (SELECT SUM(IFNULL(work_categories.duration_minutes, work_view.duration_minutes))
			FROM work_view LEFT JOIN work_categories USING (work_id)
			WHERE work_categories.category_id = categories.category_id
			'.($where_condition ? 'AND '.implode(' AND ', $where_condition) : '').'), ")"
		) AS category_time, path
	FROM categories
	HAVING NOT ISNULL(category_time)
	UNION SELECT NULL AS category_id, NULL AS category_time, NULL AS path
	ORDER BY SUBSTRING(path, 6)
';

$zz['filter'][3]['identifier'] = 'tag';
$zz['filter'][3]['type'] = 'list';
$zz['filter'][3]['sql_join'] = 'LEFT JOIN work_categories USING (work_id)';
$zz['filter'][3]['where'] = 'work_categories.category_id';

if (wrap_package('finance')) {
	$zz['filter'][4]['title'] = wrap_text('Positions');
	$zz['filter'][4]['identifier'] = 'positions';
	$zz['filter'][4]['type'] = 'list';
	$zz['filter'][4]['sql_join'] = 'LEFT JOIN positions_work USING (work_id)';
	$zz['filter'][4]['where_if'][1] = 'ISNULL(position_work_id)';
	$zz['filter'][4]['where_if'][2] = 'NOT ISNULL(position_work_id)';
	$zz['filter'][4]['selection'][1] = wrap_text('without Positions');
	$zz['filter'][4]['selection'][2] = wrap_text('with Positions');
}
