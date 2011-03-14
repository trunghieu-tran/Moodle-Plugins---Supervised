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
 * This file replaces the legacy STATEMENTS section in db/install.xml,
 * lib.php/modulename_install() post installation hook and partially defaults.php
 *
 * @package   mod_poasassignment
 * @copyright 2010 Your Name <your@email.adress>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Post installation procedure
 */
function xmldb_poasassignment_install() {
    global $DB;

    // Install default common logging actions
    update_log_display_entry('poasassignment', 'add', 'poasassignment', 'name');
    update_log_display_entry('poasassignment', 'update', 'poasassignment', 'name');
    update_log_display_entry('poasassignment', 'view', 'poasassignment', 'name');
    update_log_display_entry('poasassignment', 'view all', 'poasassignment', 'name');
    
    // Add info about default plugins into table
    $record->name='poasassignment_answer_file';
    $record->path='answer/answer_file.php';
    if (!$DB->record_exists('poasassignment_plugins',array('name'=>$record->name,'path'=>$record->path)))
        $DB->insert_record('poasassignment_plugins',$record);
    
        
    $record->name='poasassignment_answer_text';
    $record->path='answer/answer_text.php';
    if (!$DB->record_exists('poasassignment_plugins',array('name'=>$record->name,'path'=>$record->path)))
        $DB->insert_record('poasassignment_plugins',$record);

    // Add taskgivers in table
    $record = new stdClass();

    $record->name = 'randomchoice';
    $record->path = 'taskgivers/randomchoice/randomchoice.php';
    $record->langpath = 'taskgivers/randomchoice/lang';    
    if (!$DB->record_exists('poasassignment_taskgivers',array('path'=>$record->path)))
        $DB->insert_record('poasassignment_taskgivers',$record);

    $record->name = 'parameterchoice';
    $record->path = 'taskgivers/parameterchoice/parameterchoice.php';
    $record->langpath = 'taskgivers/parameterchoice/lang';
    if (!$DB->record_exists('poasassignment_taskgivers',array('path'=>$record->path)))
        $DB->insert_record('poasassignment_taskgivers',$record);

    $record->name = 'studentschoice';
    $record->path = 'taskgivers/studentschoice/studentschoice.php';
    $record->langpath = 'taskgivers/studentschoice/lang';
    if (!$DB->record_exists('poasassignment_taskgivers',array('path'=>$record->path)))
        $DB->insert_record('poasassignment_taskgivers',$record);

    // Add message provider
    $provider = new stdClass();
    $provider->name = 'poasassignment_updates';
    $provider->component='mod_poasassignment';
    $DB->insert_record('message_providers',$provider);
}
