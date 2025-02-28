<?php 

/**
 * work module
 * table definition for work/categories
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2019-2020, 2023, 2025 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */


$zz['title'] = 'Categories for Working Hours';
$zz['table'] = 'work_categories';

$zz['fields'][1]['title'] = 'ID';
$zz['fields'][1]['field_name'] = 'wc_id';
$zz['fields'][1]['type'] = 'id';

$zz['fields'][2]['title'] = 'Work';
$zz['fields'][2]['field_name'] = 'work_id';
$zz['fields'][2]['type'] = 'select';
$zz['fields'][2]['sql'] = 'SELECT work_id, work_begin, contact
	FROM work
	LEFT JOIN contacts USING (contact_id)
	ORDER BY work_begin, contact';
$zz['fields'][2]['display_field'] = 'work';

$zz['fields'][3]['field_name'] = 'category_id';
$zz['fields'][3]['type'] = 'select';
$zz['fields'][3]['sql'] = 'SELECT category_id, category, main_category_id
	FROM categories ORDER BY category';
$zz['fields'][3]['show_hierarchy'] = 'main_category_id';
$zz['fields'][3]['display_field'] = 'category';
$zz['fields'][3]['add_details'] = wrap_path('default_tables', 'categories')
	.sprintf('?filter[maincategory]=%d', wrap_category_id('tags'));
$zz['fields'][3]['show_hierarchy_subtree'] = wrap_category_id('tags');

$zz['fields'][4]['title'] = 'Duration';
$zz['fields'][4]['field_name'] = 'duration_minutes';
$zz['fields'][4]['type'] = 'number';
$zz['fields'][4]['unit'] = 'min';


$zz['sql'] = 'SELECT work_categories.*
		, category, CONCAT(work_begin, " ", contact) AS work
	FROM work_categories
	LEFT JOIN categories USING (category_id)
	LEFT JOIN work USING (work_id)
	LEFT JOIN contacts USING (contact_id)
';
$zz['sqlorder'] = ' ORDER BY category';

$zz['subselect']['sql'] = 'SELECT work_id, category, duration_minutes
	FROM work_categories
	LEFT JOIN categories USING (category_id)
';
$zz['subselect']['prefix'] = '<p><em>';
$zz['subselect']['suffix'] = '</em></p>';
$zz['subselect']['concat_rows'] = ', ';
