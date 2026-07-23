<?php 

/**
 * work module
 * weekly work overview
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2006-2012, 2017, 2020, 2025-2026 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */
 

/**
 * show a weekly overview of projects
 *
 * @return array
 */
function mod_work_work_overview() {
	$overview = [];

	$page['query_strings'] = ['week', 'year'];
	if (!empty($_GET['week'])) {
		$week = explode('/', $_GET['week']);
		$week['year'] = intval($week[0]);
		$week['week'] = intval($week[1]);
	} else {
		$week['year'] = date('o');
		$week['week'] = date('W');
	}

	$monday = (new DateTimeImmutable())->setISODate($week['year'], $week['week'], 1);
	$week['year'] = (int) $monday->format('o');
	$week['week'] = (int) $monday->format('W');
	$weekdays = [
		'begin' => $monday->format('Y-m-d'),
		'end' => $monday->modify('+6 days')->format('Y-m-d'),
	];

	$overview['last_week'] = $monday->modify('-7 days')->format('o/W');
	$overview['next_week'] = $monday->modify('+7 days')->format('o/W');

	$duration = wrap_date($weekdays['begin'].'/'.$weekdays['end'], 'dates-'.wrap_setting('lang').'-weekday');
	$page['title'] = wrap_text('Work during week').' '.$week['year'].'/'.$week['week']
		.' <br>('.$duration.')';

	$yearweek = (int) sprintf('%04d%02d', $week['year'], $week['week']);
	$sql = 'SELECT event_id, event, identifier
			, (SELECT SUM(TIMESTAMPDIFF(MINUTE, work_begin, work_end)) FROM worklogs
				WHERE events.event_id = worklogs.event_id
				AND YEARWEEK(work_begin, 3) = %d) AS duration
		FROM events
		HAVING duration > 0
		ORDER BY event
	';
	$sql = sprintf($sql, $yearweek);
	$overview['work'] = wrap_db_fetch($sql, 'event_id');
	if (!$overview['work']) {
		$page['text'] = wrap_template('work-overview', $overview);
		return $page;
	}

	$overview['total'] = 0;
	foreach ($overview['work'] as $index => $time) {
		$overview['work'][$index]['css_width'] = floor($time['duration'] / 60 * 10);
		$overview['total'] += $time['duration'];
	}
	$page['text'] = wrap_template('work-overview', $overview);
	return $page;
}
