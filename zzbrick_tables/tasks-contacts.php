<?php 

/**
 * work module
 * table definition for tasks/contacts
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2012, 2014, 2017, 2019-2022 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */


$zz['title'] = 'Tasks/Contacts';
$zz['table'] = 'todos_contacts';

$zz['fields'][1]['title'] = 'ID';
$zz['fields'][1]['field_name'] = 'todo_contact_id';
$zz['fields'][1]['type'] = 'id';

$zz['fields'][2]['title'] = 'Task';
$zz['fields'][2]['field_name'] = 'todo_id';
$zz['fields'][2]['type'] = 'select';
$zz['fields'][2]['sql'] = 'SELECT todo_id, todo
	FROM todos ORDER BY todo';
$zz['fields'][2]['display_field'] = 'todo';

$zz['fields'][3]['field_name'] = 'contact_id';
$zz['fields'][3]['type'] = 'select';
$zz['fields'][3]['sql'] = sprintf('SELECT contact_id
		, contact
	FROM contacts
	WHERE contact_category_id = %d
	ORDER BY contact', wrap_category_id('contact/person'));
$zz['fields'][3]['display_field'] = 'contact';
$zz['fields'][3]['if']['where']['class'] = 'hidden';


$zz['sql'] = 'SELECT todos_contacts.*
		, contact
		, todo
	FROM todos_contacts
	LEFT JOIN contacts USING (contact_id)
	LEFT JOIN todos USING (todo_id)
';
$zz['sqlorder'] = ' ORDER BY contact';
