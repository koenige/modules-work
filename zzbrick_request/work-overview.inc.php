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
	// get week of year
	if (!empty($_GET['week'])) {
		$week = explode('/', $_GET['week']);
		$week['year'] = intval($week[0]);
		$week['week'] = intval($week[1]);
		// last week
		if ($week['week'] == 1) {
			$overview['last_week'] = ($week['year'] - 1).'/53';
		} else {
			$overview['last_week'] = $week['year'].'/'.($week['week']-1);
		}
		// next week
		if ($week['week'] == 53) {
			$overview['next_week'] = ($week['year'] + 1).'/1';
		} else {
			$overview['next_week'] = $week['year'].'/'.($week['week']+1);
		}
	} else {
		$week['year'] = date('Y');
		$week['week'] = date('W');
		$overview['last_week'] = date('Y/W', time() - 60 * 60 * 24 * 7);
		$overview['next_week'] = date('Y/W', time() + 60 * 60 * 24 * 7);
	}
	
	$sql = 'SELECT ADDDATE("%d-01-01", INTERVAL 7*%d DAY)';
	$sql = sprintf($sql, $week['year'], $week['week']);
	$someday_in_week = wrap_db_fetch($sql, '', 'single value');

	$sql = 'SELECT SUBDATE("%s", INTERVAL weekday("%s") DAY) AS begin, 
		ADDDATE("%s", INTERVAL 6-weekday("%s") DAY) AS end';
	$sql = sprintf($sql, $someday_in_week, $someday_in_week, $someday_in_week, $someday_in_week);
	$weekdays = wrap_db_fetch($sql);

	$duration = wrap_date($weekdays['begin'].'/'.$weekdays['end'], 'dates-'.wrap_setting('lang').'-weekday');
	$page['title'] = wrap_text('Work during week').' '.$week['year'].'/'.$week['week']
		.' <br>('.$duration.')';

	$sql = 'SELECT event_id, event, identifier
			, (SELECT SUM(TIMESTAMPDIFF(MINUTE, work_begin, work_end)) FROM worklogs
				WHERE events.event_id = worklogs.event_id
				AND YEAR(work_begin) = %d
				AND WEEK(work_begin, 3) = %d) AS duration
		FROM events
		HAVING duration > 0
		ORDER BY event
	';
	$sql = sprintf($sql, $week['year'], $week['week']);
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
