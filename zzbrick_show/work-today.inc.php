<?php 

/**
 * work module
 * show work from today
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2006-2012, 2017, 2020, 2025-2026 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */
 

function mod_work_show_work_today() {
	$sql = 'SELECT worklog_id, work
			, DATE_FORMAT(work_begin, "%%H:%%i") AS begin
			, DATE_FORMAT(work_end, "%%H:%%i") AS end
			, TIMESTAMPDIFF(MINUTE, work_begin, work_end) AS duration
			, CONCAT(TIMESTAMPDIFF(HOUR, work_begin, work_end), ":", LPAD(MOD(TIMESTAMPDIFF(MINUTE, work_begin, work_end), 60), 2, 0)) AS duration_time
		FROM worklogs
		WHERE work_begin BETWEEN CONCAT(CURDATE(), " 00:00:00") AND CONCAT(CURDATE(), " 23:59:59")
		OR work_end BETWEEN CONCAT(CURDATE(), " 00:00:00") AND CONCAT(CURDATE(), " 23:59:59")
		AND contact_id = %d
		ORDER BY work_begin
	';
	if (wrap_setting('work_projects')) {
		$sql = wrap_edit_sql($sql, 'SELECT', 'event_id, event, identifier,');
		$sql = wrap_edit_sql($sql, 'JOIN', 'LEFT JOIN events USING (event_id)');
	}
	$sql = sprintf($sql, $_SESSION['user_id']);

	$work = wrap_db_fetch($sql, 'worklog_id');
	if (empty($work)) $work['no_work'] = true;
	$work['sum_duration'] = 0;
	$work['sum_projects'] = [];
	foreach ($work as $id => $line) {
		if (!is_numeric($id)) continue;
		$work['sum_duration'] += $line['duration'];
		if (wrap_setting('work_projects') AND !empty($line['event_id']))
			$work['sum_projects'][$line['event_id']] = $line['event_id'];
	}
	$work['sum_duration'] = sprintf('%d:%02d', floor($work['sum_duration'] / 60), $work['sum_duration'] % 60);
	if (wrap_setting('work_projects'))
		$work['sum_projects'] = count($work['sum_projects']);
	$page['text'] = wrap_template('work-today', $work);
	$page['text'] = str_replace('%%% ', '%%% explain ', $page['text']);
	return $page;
}
