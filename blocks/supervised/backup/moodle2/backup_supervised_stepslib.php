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
 * @copyright 2010 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that wll be used by the backup_supervised_block_task
 */

/**
 * Define the complete forum structure for backup, with file and id annotations
 */
class backup_supervised_block_structure_step extends backup_block_structure_step {

    protected function define_structure() {
        global $DB;
        // Get the block
        $block = $DB->get_record('block_instances', array('id' => $this->task->get_blockid()));
        $supervised = new backup_nested_element('supervised', array('id'), null);

        $classperiods = new backup_nested_element('classperiods');
        $classperiod = new backup_nested_element('classperiod', array('id'), array(
            'logid',        'classroomid',      'courseid', 
            'groupid',      'starttimework',    'timework', 
            'lecturerid',   'typeaction',       'twinkeyid'));

        $classrooms = new backup_nested_element('classrooms');
        $classroom = new backup_nested_element('classroom', array('id'), array(
            'id',   'number',   'initialvalueip', 'finishvalueip'));

        $groups = new backup_nested_element('groups');
        $group = new backup_nested_element('group', array('id'), array(
            'id',                   'courseid',      'name', 'description', 
            'descriptionformat',    'enrolmentkey',  'picture', 'hidepicture',
            'timecreated',          'timemodified'));

        

        $supervised->add_child($classperiods);
        $classperiods->add_child($classperiod);

        $supervised->add_child($classrooms);
        $classrooms->add_child($classroom);

        //$supervised->add_child($groups);
        //$groups->add_child($group);

        $supervised->set_source_array(array((object)array('id' => $this->task->get_blockid())));
        $classperiod->set_source_sql('SELECT * FROM {block_supervised}', array());

        $classroom->set_source_sql('SELECT * FROM {block_supervised_classroom}', array());

        /*
        $allgroups = $DB->get_records_sql('SELECT DISTINCT `groupid` FROM {block_supervised}');
        list($in_sql, $in_params) = $DB->get_in_or_equal($allgroups);
        // Define all the in_params as sqlparams
        foreach ($in_params as $key => $value) {
            $in_params[$key] = backup_helper::is_sqlparam($value);
        }
        $group->set_source_sql("SELECT * FROM {groups} WHERE id $in_sql", $in_params);
        */
        
        $userinfo = $this->get_setting_value('users');
        if ($userinfo) {
            //  if choose "Include enrolled users"
            //  save info about lecturer

            $lecturers = new backup_nested_element('lecturers');
            $lecturer = new backup_nested_element('lecturer', array('id'), array(
                'id',               'auth',         'confirmed',            'policyagreed', 
                'deleted',          'suspended',    'mnethostid',           'username', 
                'password',         'idnumber',     'firstname',            'lastname', 
                'email',            'emailstop',    'icq',                  'skype', 
                'yahoo',            'aim',          'msn',                  'phone1', 
                'phone2',           'institution',  'department',           'address', 
                'city',             'country',      'lang',                 'theme', 
                'timezone',         'firstaccess',  'lastaccess',           'lastlogin', 
                'currentlogin',     'lastip',       'secret',               'picture', 
                'url',              'description',  'descriptionformat',    'mailformat', 
                'maildigest',       'maildisplay',  'htmleditor',           'ajax', 
                'autosubscribe',    'trackforums',  'timecreated',          'timemodified', 
                'trustbitmask',     'imagealt',     'screenreader'));
            $supervised->add_child($lecturers);
            $lecturers->add_child($lecturer);

            $alllecturer = $DB->get_records_sql('SELECT DISTINCT `lecturerid` FROM {block_supervised}');
            list($in_sql, $in_params) = $DB->get_in_or_equal($alllecturer, SQL_PARAMS_NAMED);
            $params = array();
            foreach ($in_params as $param=>$key) {
                $params[$param] = $key->lecturerid;
            }

            $lecturerarray = array();
            $lecturervalue = $DB->get_records_select('user', "id $in_sql", $params);
            foreach ($lecturervalue as $item) {
                $lecturerarray[] = $item;
            }
            $lecturer->set_source_array($lecturerarray);
        }


        if ($this->get_setting_value('logs')) {
            // if coose "Include course logs"
            $logs = new backup_nested_element('logs');
            $log = $group = new backup_nested_element('log', array('id'), array(
            'id',       'time',     'userid',   'ip', 
            'course',   'module',   'cmid',     'action',
            'url',      'info'));
            $supervised->add_child($logs);
            $logs->add_child($log);
            // write all logs to 
            $logsarray = array();
            $idstartlessons = $DB->get_records_sql('SELECT id FROM {block_supervised} WHERE typeaction="startsession"');
            foreach ($idstartlessons as $idstart) {
            $idend = $DB->get_field_sql('SELECT twinkeyid FROM {block_supervised} WHERE id=?', array($idstart->id));
            $logidstart = $DB->get_field_sql('SELECT logid FROM {block_supervised} WHERE id=?', array($idstart->id));
            $logidend = $DB->get_field_sql('SELECT logid FROM {block_supervised} WHERE id=?', array($idend));
            $logsindeval = $DB->get_records_select('log', 'id >= :start and id <= :end', array('start'=>$logidstart, 'end'=>$logidend));
                foreach ($logsindeval as $logval) {
                    $logsarray[] = $logval;
                }
            }
            $log->set_source_array($logsarray);
        }

        $classperiod->annotate_ids('log',   'logid');
        $classperiod->annotate_ids('supervised_classroom', 'classroomid');
        $classperiod->annotate_ids('course', 'courseid');
        $classperiod->annotate_ids('group', 'groupid');
        $classperiod->annotate_ids('user', 'lecturerid');
        $classperiod->annotate_ids('supervised', 'twinkeyid');
        return $this->prepare_block_structure($supervised);
    }
}
