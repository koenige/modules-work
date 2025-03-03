<?php

/**
 * work module
 * form: work
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2025 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */


if (empty($brick['data']['event_id'])) wrap_quit(404);

$zz = zzform_include('work');
$zz['where']['event_id'] = $brick['data']['event_id'];

$zz['filter'][1]['sql'] = wrap_edit_sql($zz['filter'][1]['sql'], 'WHERE',
	sprintf('event_id = %d', $brick['data']['event_id'])
);

// @todo $zz['filter'][3], more complicated

if (!empty($zz['filter'][2])) {
	$zz['filter'][2]['sql'] = wrap_edit_sql($zz['filter'][2]['sql'], 'WHERE',
		sprintf('event_id = %d', $brick['data']['event_id'])
	);
}
