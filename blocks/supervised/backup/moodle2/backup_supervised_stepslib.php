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
 * Define all the backup steps that wll be used by the backup_supervised_block_task
 */

/**
 * Define the complete structure for backup
 */
class backup_supervised_block_structure_step extends backup_block_structure_step {

    protected function define_structure() {
        global $DB;
        // Define each element separated.
        $root = new backup_nested_element('root');

        $classrooms = new backup_nested_element('classrooms');
        $classroom = new backup_nested_element('classroom', array('id'), array(
            'name', 'iplist', 'active'));

        $lessontypes = new backup_nested_element('lessontypes');
        $lessontype = new backup_nested_element('lessontype', array('id'), array(
            'name', 'courseid'));

        $sessions = new backup_nested_element('sessions');
        $session = new backup_nested_element('session', array('id'), array(
            'courseid', 'classroomid', 'groupid', 'teacherid', 'lessontypeid',
            'timestart', 'duration', 'timeend', 'state', 'iplist', 'sendemail', 'sessioncomment'));

        $users = new backup_nested_element('users');
        $user = new backup_nested_element('user', array('id'), array('userid', 'sessionid'));

        // Build the tree.
        $root->add_child($classrooms);
        $classrooms->add_child($classroom);
        $root->add_child($lessontypes);
        $lessontypes->add_child($lessontype);
        $root->add_child($sessions);
        $sessions->add_child($session);
        $root->add_child($users);
        $users->add_child($user);

        // Define sources.
        $classroom->set_source_table('block_supervised_classroom', array());
        $lessontype->set_source_table('block_supervised_lessontype', array('courseid' => backup::VAR_COURSEID));
        $session->set_source_table('block_supervised_session', array('courseid' => backup::VAR_COURSEID));
        $select = "SELECT
            {block_supervised_user}.id,
            {block_supervised_user}.userid,
            {block_supervised_user}.sessionid

            FROM {block_supervised_user}
              JOIN {block_supervised_session}
                ON {block_supervised_user}.sessionid = {block_supervised_session}.id

            WHERE {block_supervised_session}.courseid = :courseid";
        $user->set_source_sql($select, array('courseid' => backup::VAR_COURSEID));

        // Annotations (none).

        // Return the root element, wrapped into standard block structure.
        return $this->prepare_block_structure($root);
    }
}