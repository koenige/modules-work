<?php

/**
 * work module
 * zzform hooks
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2012-2013, 2018-2019, 2021-2022 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */


/**
 * send task reminder per mail for new tasks, done tasks or if explicitly said so
 *
 * @param array $ops
 * @return void
 */
function mf_work_task_reminder($ops) {
	global $zz_setting;

	$send_mail = false;
	$text = $ops['record_new'][0];
	if ($ops['record_diff'][0]['done'] === 'diff') {
		// @todo categories, persons (?)
		$text['action_done'] = true;
		$send_mail = true;
	} elseif ($ops['record_diff'][0]['todo_id'] === 'insert') {
		$text['action_new'] = true;
		$send_mail = true;
	} elseif ($ops['record_new'][0]['reminder'] === 'yes') {
		$text['action_update'] = true;
		$send_mail = true;
	}
	if (!$send_mail) return;

	// get data for mail
	$task_for = [];
	$category_ids = [];
	$contact_ids = [];
	$contact_ids[] = $text['author_contact_id'];
	if (!empty($_SESSION['contact_id']))
		$contact_ids[] = $_SESSION['contact_id'];
	foreach ($ops['return'] as $index => $detailrecord) {
		if ($detailrecord['table'] === 'todos_contacts') {
			$contact_ids[] = $ops['record_new'][$index]['contact_id'];
			$task_for[] = $ops['record_new'][$index]['contact_id'];
		} elseif ($detailrecord['table'] === 'todos_categories') {
			$category_ids[] = $ops['record_new'][$index]['category_id'];
		}
	}
	array_unique($contact_ids);
	$sql = 'SELECT contact_id, identifier, contact
			, (SELECT identification FROM contactdetails
				WHERE contactdetails.contact_id = contacts.contact_id
				AND provider_category_id = %d
				LIMIT 1
			) AS e_mail
			, IF(sex = "female", 1, NULL) AS female
			, IF(sex = "male", 1, NULL) AS male
			, IF(sex = "diverse", 1, NULL) AS diverse
		FROM /*_PREFIX_*/persons
		LEFT JOIN contacts USING (contact_id)
		WHERE contact_id IN (%s)';
	$sql = sprintf($sql
		, wrap_category_id('provider/e-mail')
		, implode(',', $contact_ids)
	);
	$recipients = wrap_db_fetch($sql, 'contact_id');
	$task_for = array_intersect(array_keys($recipients), $task_for);
	foreach ($task_for as $id) {
		$text['task_for'][$id] = $recipients[$id];
	}
	$text['author_contact'] = $recipients[$text['author_contact_id']]['contact'];

	if ($category_ids) {
		$sql = 'SELECT categories.category_id
				, categories.category, main_categories.category AS main_category
			FROM categories
			LEFT JOIN categories main_categories
				ON categories.main_category_id = main_categories.category_id
			WHERE categories.category_id IN (%s)';
		$sql = sprintf($sql, implode(',', $category_ids));
		$text['categories'] = wrap_db_fetch($sql, 'category_id');
	}
	
	$text['task_path'] = wrap_path('work_tasks_table');
	if (!$text['task_path'])
		$text['task_path'] = wrap_path('default_tables', 'tasks');

	// set sender data
	if (!empty($_SESSION['contact_id'])) {
		$text['sender'] = $recipients[$_SESSION['contact_id']]['contact'];
		$text['sender_link'] = $recipients[$_SESSION['contact_id']]['identifier'];
	} else {
		$text['sender'] = $zz_setting['own_name'];
	}

	// send mails
	foreach ($recipients as $recipient) {
		$recipient = array_merge($text, $recipient);
		$mail['to']['name'] = $recipient['contact'];
		$mail['to']['e_mail'] = $recipient['e_mail'];
		$mail['parameters'] = '-f '.$zz_setting['own_e_mail'];
		$mail['message'] = wrap_template('task-reminder-mail', $recipient);
		wrap_mail($mail);
	}
}
