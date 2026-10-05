<?php 

/**
 * work module
 * Work log iCal subscription links (overview per login)
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2009-2012, 2017, 2026 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */


/**
 * List logins with signed URLs for each person’s work log ICS feed.
 *
 * @return array
 */
function mod_work_worklog_calendars() {
	$sql = 'SELECT contact_id, contact, username
		FROM logins
		LEFT JOIN contacts USING (contact_id)
		ORDER BY contact';
	$worklog_calendars = wrap_db_fetch($sql, 'contact_id');

	foreach ($worklog_calendars as $contact_id => $contact) {
		$worklog_calendars[$contact_id]['hash'] = wrap_set_hash(
			$contact['contact_id'].'/'.$contact['username'],
			'work_worklog_ics_secret_key'
		);
	}

	$page['text'] = wrap_template('worklog-calendars', $worklog_calendars);
	return $page;
}
