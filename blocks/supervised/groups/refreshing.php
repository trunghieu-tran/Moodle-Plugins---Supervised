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

$courseid = required_param('courseid', PARAM_INT);
$groupid   = required_param('group', PARAM_INT);
$returnurl = required_param('urlreturn', PARAM_INT);
$sessionid = optional_param('sessionid', -2, PARAM_INT);
$destroy  = optional_param('destroy', false, PARAM_BOOL);

groups_delete_group($groupid);

if ($returnurl == 0) {
    $returnurl = new moodle_url('/course/view.php', array('id' => $courseid));
} else {
    $returnurl = new moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $courseid));
}

redirect($returnurl);