<?php 

/**
 * work module
 * table definition for tasks/contacts
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2012, 2014, 2017, 2019-2022, 2024 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */


$zz['title'] = 'Tasks/Contacts';
$zz['table'] = 'tasks_contacts';

$zz['fields'][1]['title'] = 'ID';
$zz['fields'][1]['field_name'] = 'task_contact_id';
$zz['fields'][1]['type'] = 'id';

$zz['fields'][2]['title'] = 'Task';
$zz['fields'][2]['field_name'] = 'task_id';
$zz['fields'][2]['type'] = 'select';
$zz['fields'][2]['sql'] = 'SELECT task_id, task
	FROM tasks ORDER BY task';
$zz['fields'][2]['display_field'] = 'task';

$zz['fields'][3]['field_name'] = 'contact_id';
$zz['fields'][3]['type'] = 'select';
$zz['fields'][3]['sql'] = 'SELECT contact_id
		, contact
	FROM contacts
	WHERE contact_category_id = /*_ID categories contact/person _*/
	ORDER BY contact';
$zz['fields'][3]['display_field'] = 'contact';
$zz['fields'][3]['if']['where']['class'] = 'hidden';


$zz['sql'] = 'SELECT tasks_contacts.*
		, contact
		, task
	FROM tasks_contacts
	LEFT JOIN contacts USING (contact_id)
	LEFT JOIN tasks USING (task_id)
';
$zz['sqlorder'] = ' ORDER BY contact';
