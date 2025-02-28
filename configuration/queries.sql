/**
 * work module
 * SQL queries
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/work
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2025 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */


-- work_logins_count --
/* number of logins available on platform, to simplify interface for single user */
SELECT COUNT(*) FROM /*_PREFIX_*/logins;
