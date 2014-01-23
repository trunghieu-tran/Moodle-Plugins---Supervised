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

/**
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that wll be used by the restore_supervised_block_task
 */

/**
 * Define the complete supervised  structure for restore
 */
class restore_supervised_block_structure_step extends restore_structure_step {

    protected function define_structure() {

        $paths = array();

        $paths[] = new restore_path_element('block', '/block', true);
        $paths[] = new restore_path_element('root', '/block/root');
        $paths[] = new restore_path_element('classroom', '/block/root/classrooms/classroom');
        $paths[] = new restore_path_element('lessontype', '/block/root/lessontypes/lessontype');
        $paths[] = new restore_path_element('session', '/block/root/sessions/session');

        return $paths;
    }

    public function process_block($data) {
        global $DB;

        $data = (object)$data;
        $root = (object)($data->root);

        // For any reason (non multiple, dupe detected...) block not restored, return.
        if (!$this->task->get_blockid()) {
            return;
        }

        // Restore classrooms.
        $DB->delete_records('block_supervised_classroom');
        $classrooms = (object)$root->classrooms;
        $classrooms = $classrooms->classroom;
        if ($classrooms) {
            foreach ($classrooms as $classroom) {
                $classroom = (object)$classroom;
                $olditemid = $classroom->id;
                $newitemid = $DB->insert_record('block_supervised_classroom', $classroom);
                $this->set_mapping('classroom', $olditemid, $newitemid);
            }
        }

        // Restore lessontypes.
        $DB->delete_records('block_supervised_lessontype', array('courseid' => $this->get_courseid()));
        $lessontypes = (object)$root->lessontypes;
        $lessontypes = $lessontypes->lessontype;
        if ($lessontypes) {
            foreach ($lessontypes as $lessontype) {
                $lessontype = (object)$lessontype;
                $olditemid = $lessontype->id;
                $lessontype->courseid = $this->get_courseid();
                $newitemid = $DB->insert_record('block_supervised_lessontype', $lessontype);
                $this->set_mapping('lessontype', $olditemid, $newitemid);
            }
        }

        // Restore sessions.
        $DB->delete_records('block_supervised_session', array('courseid' => $this->get_courseid()));
        $sessions = (object)$root->sessions;
        $sessions = $sessions->session;
        if ($sessions) {
            foreach ($sessions as $session) {
                $session = (object)$session;
                $session->courseid = $this->get_courseid();
                $session->classroomid = $this->get_mappingid('classroom', $session->classroomid);
                $session->teacherid = $this->get_mappingid('user', $session->teacherid, $session->teacherid);
                $session->lessontypeid = $this->get_mappingid('lessontype', $session->lessontypeid);
                $DB->insert_record('block_supervised_session', $session);
            }
        }
    }
}
