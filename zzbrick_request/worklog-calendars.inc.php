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
 * List logins with signed URLs for each person’s work log ICS feed,
 * subscription (current year) plus one static file per year and `all`.
 *
 * @return array
 */
function mod_work_worklog_calendars() {
	$sql = 'SELECT contact_id, contact, username
		FROM logins
		LEFT JOIN contacts USING (contact_id)
		ORDER BY contact';
	$worklog_calendars = wrap_db_fetch($sql, 'contact_id');

	$sql = 'SELECT DISTINCT contact_id, YEAR(work_end) AS year
		FROM worklogs
		WHERE work_begin <> work_end
		ORDER BY year DESC';
	$years = wrap_db_fetch($sql, ['contact_id', 'year'], 'key/values');

	foreach ($worklog_calendars as $contact_id => $contact) {
		$hash = wrap_set_hash(
			$contact['contact_id'].'/'.$contact['username'],
			'work_worklog_ics_secret_key'
		);
		$worklog_calendars[$contact_id]['hash'] = $hash;
		foreach ($years[$contact_id] ?? [] as $year) {
			$worklog_calendars[$contact_id]['years'][] = [
				'year' => $year,
				'username' => $contact['username'],
				'hash' => $hash
			];
		}
	}

	$page['text'] = wrap_template('worklog-calendars', $worklog_calendars);
	return $page;
}
