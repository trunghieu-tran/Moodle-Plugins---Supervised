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
 
require_once($CFG->dirroot . '/blocks/role_reassign/backup/moodle2/backup_role_reassign_stepslib.php'); // Because it exists (must)
 
/**
 * role_reassign backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */
 echo 'class ';
class backup_role_reassign_block_task extends backup_block_task {
 
    protected function define_my_settings() {
    }
 
    protected function define_my_steps() {
        echo 'definingmy ';
        $this->add_step(new backup_role_reassign_block_structure_step('role_reassign_structure', 'role_reassign.xml'));
    }
 
    public function get_fileareas() {
        return array();
    }
    
    public function get_configdata_encoded_attributes() {
        return array();
    }
    
    static public function encode_content_links($content) {
        return $content;
    }
}