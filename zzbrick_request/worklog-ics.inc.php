<?php 

/**
 * work module
 * Work log iCal export (ICS format)
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2009-2012, 2017, 2019-2023, 2025-2026 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */


use Kigkonsult\Icalcreator\Vcalendar;

/**
 * Subscription feed of one login’s work logs (username in URL, hash in query string).
 *
 * @param array $params one segment: login username
 * @return array|false
 */
function mod_work_worklog_ics($params) {
	if (count($params) !== 1) return false;


	$sql = 'SELECT contact_id, contact, username
		FROM logins
		LEFT JOIN contacts USING (contact_id)
		WHERE username = "%s"';
	$sql = sprintf($sql, wrap_db_escape($params[0]));
	$contact = wrap_db_fetch($sql);
	if (!$contact) return false;

	if (empty($_GET['hash'])) wrap_quit(403);

	wrap_check_hash(
		$contact['contact_id'].'/'.$contact['username'],
		$_GET['hash'],
		wrap_text('Your subscription link is not valid.'),
		'work_worklog_ics_secret_key'
	);

	wrap_db_charset('utf8');		// ICS in utf8

	$sql = 'SELECT worklog_id
			, DATE_FORMAT(IFNULL(work_begin, work_end), "%%Y%%m%%d") AS date_begin
			, DATE_FORMAT(work_begin, "T%%H%%i%%s") AS time_begin
			, DATE_FORMAT(IFNULL(work_end, work_begin), "%%Y%%m%%d") AS date_end
			, DATE_FORMAT(work_end, "T%%H%%i%%s") AS time_end
			, DATE_FORMAT(IFNULL(DATE_ADD(work_end, INTERVAL 1 DAY), work_begin), "%%Y%%m%%d") AS dt_date_end
			, event AS summary
			, work AS description
		FROM worklogs
		LEFT JOIN events USING (event_id)
		WHERE worklogs.contact_id = %s
		AND worklogs.work_begin <> worklogs.work_end
		ORDER BY IFNULL(work_begin, work_end) DESC';
	$sql = sprintf($sql, $contact['contact_id']);
	$events = wrap_db_fetch($sql, 'worklog_id');
	if (!$events) return false;

	wrap_lib('icalcreator');

	$tz = wrap_setting('timezone');
	$cal_title = wrap_text('Work logs').' '.$contact['contact'];

	$v = Vcalendar::factory([Vcalendar::UNIQUE_ID => wrap_setting('hostname')]);
	$v->setMethod(Vcalendar::PUBLISH);
	$v->setXprop(Vcalendar::X_WR_CALNAME, $cal_title);
	$v->setXprop(Vcalendar::X_WR_CALDESC, $cal_title);
	$v->setXprop(Vcalendar::X_WR_TIMEZONE, $tz);
	$v->setConfig(Vcalendar::LANGUAGE, wrap_setting('lang'));

	$default_duration = sprintf('PT%dM', wrap_setting('work_worklog_ics_default_duration_minutes'));

	foreach ($events as $event) {
		$e = $v->newVevent();
		$e->setSummary($event['summary']);
		if ($event['time_begin']) {
			$e->setDtstart(new DateTime($event['date_begin'].$event['time_begin'], new DateTimezone($tz)));
			if ($event['time_end']) {
				$e->setDtend(new DateTime($event['date_end'].$event['time_end'], new DateTimezone($tz)));
			} else {
				// No end time: use default duration for this calendar entry.
				$e->setDuration($default_duration);
			}
		} else {
			$e->setDtstart(new DateTime($event['date_begin']), ['VALUE' => 'DATE']);
			$e->setDtend(new DateTime($event['dt_date_end']), ['VALUE' => 'DATE']);
		}
		$e->setDescription($event['description']);

		$e->setUid('work-'.$event['worklog_id'].'@'.wrap_setting('site'));
	}

	$v->vtimezonePopulate();

	$page['text'] = $v->createCalendar();
	$page['content_type'] = 'ics';
	$page['headers']['filename'] = sprintf('%s %s.ics'
		, wrap_text('Work logs')
		, html_entity_decode($contact['contact'], ENT_QUOTES, 'utf-8')
	);
	$page['query_strings'] = ['hash'];
	return $page;
}
