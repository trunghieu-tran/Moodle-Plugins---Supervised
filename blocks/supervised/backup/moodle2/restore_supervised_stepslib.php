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
 * @package     moodlecore
 * @subpackage  backup-moodle2
 * @author      Andrey Ushakov <andrey200964@yandex.ru>
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
        $paths[] = new restore_path_element('user', '/block/root/users/user');

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
        $classrooms = (object)$root->classrooms;
        $classrooms = $classrooms->classroom;
        if ($classrooms) {
            foreach ($classrooms as $classroom) {
                $classroom = (object)$classroom;
                $olditemid = $classroom->id;
                // We don't restore classrooms that we already have in DB.
                $select = "name='" . $classroom->name . "' AND ". $DB->sql_compare_text('iplist') . "='". $classroom->iplist ."'";
                if ($DB->record_exists_select('block_supervised_classroom', $select)) {
                    $newitemid = $olditemid;
                } else {
                    $newitemid = $DB->insert_record('block_supervised_classroom', $classroom);
                }
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
                $olditemid = $session->id;
                $session->courseid = $this->get_courseid();
                $session->classroomid = $this->get_mappingid('classroom', $session->classroomid);
                $session->teacherid = $this->get_mappingid('user', $session->teacherid, $session->teacherid);
                $session->lessontypeid = $this->get_mappingid('lessontype', $session->lessontypeid);
                $newitemid = $DB->insert_record('block_supervised_session', $session);
                $this->set_mapping('session', $olditemid, $newitemid);
            }
        }

        // Restore users.
        $sessionids = $DB->get_records('block_supervised_session', array('courseid' => $this->get_courseid()));
        $DB->delete_records_list('block_supervised_user', 'sessionid', array_keys($sessionids));
        $users = (object)$root->users;
        $users = $users->user;
        if ($users) {
            foreach ($users as $user) {
                $user = (object)$user;
                $user->sessionid = $this->get_mappingid('session', $user->sessionid);
                $olditemid = $user->id;
                $newitemid = $DB->insert_record('block_supervised_user', $user);
                $this->set_mapping('user', $olditemid, $newitemid);
            }
        }
    }
}
