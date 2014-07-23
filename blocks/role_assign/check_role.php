<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
require_once('../../config.php');

$courseid = optional_param('courseid', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);
$currole = optional_param('cur_role', 0, PARAM_INT);
// Check current user role.
$testrole = "";
$context = get_context_instance(CONTEXT_COURSE, $courseid);
if ($roles = get_user_roles($context, $userid)) {
    foreach ($roles as $role) {
        if (empty($role->name)) {
            $testrole .= $role->shortname."<br/>";
        } else {
            $testrole .= $role->name."<br/>";
        }
    }
}
/*if (strcmp($testrole, $currole)) {

}*/
echo $testrole;