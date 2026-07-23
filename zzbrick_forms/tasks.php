<?php

/**
 * work module
 * form: tasks
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2025-2026 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */


if (!wrap_setting('work_projects')) wrap_quit(404);
if (empty($brick['data']['event_id'])) wrap_quit(404);

$zz = zzform_include('tasks');
$zz['where']['event_id'] = $brick['data']['event_id'];

if (!empty($zz['filter'][2]))
	$zz['filter'][2]['sql'] = wrap_edit_sql($zz['filter'][2]['sql'], 'WHERE',
		sprintf('event_id = %d', $brick['data']['event_id'])
	);
