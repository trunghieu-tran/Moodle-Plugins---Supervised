<?php
// This file is part of Student Access Control Kit - https://code.google.com/p/oasychev-moodle-plugins/
//
// Student Access Control Kit is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Student Access Control Kit is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     block
 * @subpackage  supervised
 * @author      Hieu Tran <trantrunghieu7492@gmail.com>
 * @copyright   2016 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once('../../../group/lib.php');

$sessionid   = required_param('sessionid', PARAM_INT);
$courseid    = required_param('courseid', PARAM_INT);
$urlreturn   = required_param('urlreturn', PARAM_INT);
$editmode    = required_param('editmode', PARAM_BOOL);

$data->name = get_string('internship', 'block_supervised');
$data->courseid = $courseid;
$groupid = groups_create_group($data);

$params['group'] = $groupid;
$params['sessionid'] = $sessionid;
$params['urlreturn'] = $urlreturn;
$params['editmode']  = $editmode;
$params['reload']    = false;

$url = new moodle_url('/blocks/supervised/groups/members.php', $params);
redirect($url);