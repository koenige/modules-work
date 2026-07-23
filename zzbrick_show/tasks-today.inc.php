<?php 

/**
 * work module
 * show tasks from today
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2017, 2020, 2022-2023, 2025-2026 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */


function mod_work_show_tasks_today() {
	$areas = ['today' => wrap_text('Today'), 'important' => wrap_text('Important')];
	$sql['today'] = 'SELECT task_id, task, task_description, event, event_id, identifier
		FROM tasks
		LEFT JOIN events USING (event_id)
		WHERE deadline = CURDATE()
		AND ISNULL(done)
		ORDER BY time, tasks.sequence, priority';
	$sql['important'] = 'SELECT task_id, task, task_description, event, event_id, identifier
		FROM tasks
		LEFT JOIN events USING (event_id)
		LEFT JOIN tasks_categories USING (task_id)
		LEFT JOIN categories
			ON tasks_categories.category_id = categories.category_id
		WHERE categories.parameters LIKE "%&work_tasks_important=1%"
		AND (ISNULL(deadline) OR deadline != CURDATE())
		AND ISNULL(done)
		ORDER BY time, tasks.sequence, priority';

	$data = [];
	foreach ($areas as $index => $area) {
		$data[$index]['area'] = $area;
		$data[$index]['tasks'] = wrap_db_fetch($sql[$index], 'task_id');
		$data[$index]['sum_tasks'] = count($data[$index]['tasks']);
		$data[$index]['sum_projects'] = [];
		foreach ($data[$index]['tasks'] as $id => $line) {
			if (!is_numeric($id)) continue;
			$data[$index]['sum_projects'][$line['event_id']] = $line['event_id'];
		}
		$data[$index]['sum_projects'] = count($data[$index]['sum_projects']);
	}
	$data = array_values($data);

	$sql = 'SELECT COUNT(*) FROM tasks
		WHERE YEARWEEK(deadline, 1) = YEARWEEK(CURDATE(), 1)
		AND ISNULL(done)';
	$data['this_week'] = wrap_db_fetch($sql, '', 'single value');

	$sql = 'SELECT COUNT(*) FROM tasks
		WHERE YEARWEEK(deadline, 1) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 7 DAY), 1)
		AND ISNULL(done)';
	$data['last_week'] = wrap_db_fetch($sql, '', 'single value');

	$sql = 'SELECT COUNT(*) FROM tasks
		WHERE YEARWEEK(deadline, 1) = YEARWEEK(DATE_ADD(CURDATE(), INTERVAL 7 DAY), 1)
		AND ISNULL(done)';
	$data['next_week'] = wrap_db_fetch($sql, '', 'single value');

	$sql = 'SELECT COUNT(*) FROM tasks
		WHERE deadline < CURDATE()
		AND ISNULL(done)';
	$data['past'] = wrap_db_fetch($sql, '', 'single value');

	$sql = 'SELECT COUNT(*) FROM tasks
		WHERE deadline > CURDATE()
		AND ISNULL(done)';
	$data['future'] = wrap_db_fetch($sql, '', 'single value');

	$page['text'] = wrap_template('tasks-today', $data);
	return $page;
}
