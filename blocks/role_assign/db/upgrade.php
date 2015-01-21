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
function xmldb_block_role_assign_upgrade($oldversion = 0) {
    global $CFG, $DB;
    if ($oldversion < 2013122401) {
        $record = new stdClass();
        $record->name = 'test';
        $DB->insert_record('block_role_assign_tasks', $record, false);
        $record = new stdClass();
        $record->name = 'poasas';
        $DB->insert_record('block_role_assign_tasks', $record, false);
        $record = new stdClass();
        $record->name = 'lesson';
        $DB->insert_record('block_role_assign_tasks', $record, false);
    }
    return true;
}