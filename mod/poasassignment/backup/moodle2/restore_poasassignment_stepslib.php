<?php

class restore_poasassignment_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('poasassignment', '/activity/poasassignment');
        $paths[] = new restore_path_element('poasassignment_criterion', '/activity/poasassignment/criterions/criterion');
        $paths[] = new restore_path_element('poasassignment_answersetting', '/activity/poasassignment/answersettings/answersetting');
        $paths[] = new restore_path_element('poasassignment_field', '/activity/poasassignment/fields/field');
        $paths[] = new restore_path_element('poasassignment_variant', '/activity/poasassignment/fields/field/variants/variant');
        $paths[] = new restore_path_element('poasassignment_usedgrader', '/activity/poasassignment/usedgraders/usedgrader');
        $paths[] = new restore_path_element('poasassignment_task', '/activity/poasassignment/tasks/task');
        $paths[] = new restore_path_element('poasassignment_nonrandomtaskvalue', 
                '/activity/poasassignment/tasks/task/nonrandomtaskvalues/nonrandomtaskvalue');
        
        // userinfo 
        
        $paths[] = new restore_path_element('poasassignment_assignee', '/activity/poasassignment/assignees/assignee');
        $paths[] = new restore_path_element('poasassignment_randomtaskvalue', 
                '/activity/poasassignment/assignees/assignee/randomtaskvalues/randomtaskvalue');
                
        $paths[] = new restore_path_element('poasassignment_attempt', 
                '/activity/poasassignment/assignees/assignee/attempts/attempt');
        
        // now it's time to come back to assignees and define lastattemptid
        $paths[] = new restore_path_element('poasassignment_assignee_add_lastattemptid', '/activity/poasassignment/extraassignees/extraassignee');
                
        $paths[] = new restore_path_element('poasassignment_submission', 
                '/activity/poasassignment/assignees/assignee/attempts/attempt/submissions/submission');
                
        $paths[] = new restore_path_element('poasassignment_ratings', 
                '/activity/poasassignment/assignees/assignee/attempts/attempt/ratings/rating');
                
        // Apply for 'assignment' subplugins optional paths at assignment level
        $this->add_subplugin_structure('poasassignmenttaskgivers', $poasassignment);

        //if ($userinfo) {
        //    $submission = new restore_path_element('assignment_submission', '/activity/assignment/submissions/submission');
        //    $paths[] = $submission;
            // Apply for 'assignment' subplugins optional stuff at submission level
        //    $this->add_subplugin_structure('assignment', $submission);
        //}

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_poasassignment($data) {
        echo '<br>'.__FUNCTION__;
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        if(isset($data->timedue)) {
            $data->timedue = $this->apply_date_offset($data->timedue);
        }
        if(isset($data->timeavailable)) {
            $data->timeavailable = $this->apply_date_offset($data->timeavailable);
        }
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        //if ($data->grade < 0) { // scale found, get mapping
        //    $data->grade = -($this->get_mappingid('scale', abs($data->grade)));
        //}

        // insert the assignment record
        $newitemid = $DB->insert_record('poasassignment', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }
    protected function process_poasassignment_criterion($data) {
        echo '<br>'.__FUNCTION__;
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->poasassignmentid = $this->get_new_parentid('poasassignment');
        
        $newitemid = $DB->insert_record('poasassignment_criterions', $data);
        $this->set_mapping('poasassignment_criterions', $oldid, $newitemid);
    }
    protected function process_poasassignment_answersetting($data) {
        echo '<br>'.__FUNCTION__;
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->poasassignmentid = $this->get_new_parentid('poasassignment');
        
        $newitemid = $DB->insert_record('poasassignment_ans_stngs', $data);
        $this->set_mapping('poasassignment_ans_stngs', $oldid, $newitemid);
    }
    protected function process_poasassignment_field($data) {
        echo '<br>'.__FUNCTION__;
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->poasassignmentid = $this->get_new_parentid('poasassignment');
        
        $newitemid = $DB->insert_record('poasassignment_fields', $data);
        $this->set_mapping('poasassignment_fields', $oldid, $newitemid);
    }
    protected function process_poasassignment_variant($data) {
        echo '<br>'.__FUNCTION__;
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->fieldid = $this->get_mappingid('poasassignment_fields', $data->fieldid);
        
        $newitemid = $DB->insert_record('poasassignment_variants', $data);
        $this->set_mapping('poasassignment_variants', $oldid, $newitemid);
    }
    protected function process_poasassignment_usedgrader($data) {
        echo '<br>'.__FUNCTION__;
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->poasassignmentid = $this->get_new_parentid('poasassignment');
        
        $newitemid = $DB->insert_record('poasassignment_used_graders', $data);
        $this->set_mapping('poasassignment_used_graders', $oldid, $newitemid);
    }
    
    protected function process_poasassignment_task($data) {
        echo '<br>'.__FUNCTION__;
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->poasassignmentid = $this->get_new_parentid('poasassignment');
        
        $newitemid = $DB->insert_record('poasassignment_tasks', $data);
        $this->set_mapping('poasassignment_tasks', $oldid, $newitemid);
    }
    protected function process_poasassignment_nonrandomtaskvalue($data) {
        echo '<br>'.__FUNCTION__;
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->taskid = $this->get_mappingid('poasassignment_tasks', $data->taskid);
        $data->fieldid = $this->get_mappingid('poasassignment_fields', $data->fieldid);
        // $data->assigneeid here we process nonrandom task values so assigneeid was 0
        
        $newitemid = $DB->insert_record('poasassignment_task_values', $data);
        $this->set_mapping('poasassignment_task_values', $oldid, $newitemid);
    }
    
    protected function process_poasassignment_assignee($data) {
        echo '<br>'.__FUNCTION__;
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->poasassignmentid = $this->get_new_parentid('poasassignment');
        $data->taskid = $this->get_mappingid('poasassignment_tasks', $data->taskid);
        // $data->lastattemptid - we will be back soon to update this value. 
        // At the moment we don't have attempts
        
        $newitemid = $DB->insert_record('poasassignment_assignee', $data);
        $this->set_mapping('poasassignment_assignee', $oldid, $newitemid);
    }
    protected function process_poasassignment_randomtaskvalue($data) {
        echo '<br>'.__FUNCTION__;
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->taskid = $this->get_mappingid('poasassignment_tasks', $data->taskid);
        $data->fieldid = $this->get_mappingid('poasassignment_fields', $data->fieldid);
        $data->assigneeid = $this->get_mappingid('poasassignment_assignee', $data->assigneeid);
        
        $newitemid = $DB->insert_record('poasassignment_task_values', $data);
        $this->set_mapping('poasassignment_task_values', $oldid, $newitemid);
    }
    protected function process_poasassignment_attempt($data) {
        echo '<br>'.__FUNCTION__;
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->assigneeid = $this->get_mappingid('poasassignment_assignee', $data->assigneeid);
        
        $newitemid = $DB->insert_record('poasassignment_attempts', $data);
        $this->set_mapping('poasassignment_attempts', $oldid, $newitemid);
    }
    protected function process_poasassignment_assignee_add_lastattemptid($data) {
        echo '<br>'.__FUNCTION__;
        global $DB;

        $data = (object)$data;
        $newitemid = $this->get_mappingid('poasassignment_assignee', $data->id);
        $assignee = $DB->get_record('poasassignment_assignee', array('id' => $newitemid));
        $assignee->lastattemptid = $this->get_mappingid('poasassignment_attempts', $assignee->lastattemptid);
        
        // $data->lastattemptid - we will be back soon to update this value. 
        // At the moment we don't have attempts
        
        $DB->update_record('poasassignment_assignee', $assignee);
    }
    protected function process_poasassignment_submission($data) {
        echo '<br>'.__FUNCTION__;
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->attemptid = $this->get_mappingid('poasassignment_attempts', $data->attemptid);
        
        $newitemid = $DB->insert_record('poasassignment_submissions', $data);
        $this->set_mapping('poasassignment_submissions', $oldid, $newitemid);
    }
    protected function process_poasassignment_ratings($data) {
        echo '<br>'.__FUNCTION__;
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->attemptid = $this->get_mappingid('poasassignment_attempts', $data->attemptid);
        $data->criterionid = $this->get_mappingid('poasassignment_criterions', $data->criterionid);
        
        $newitemid = $DB->insert_record('poasassignment_rating_values', $data);
        $this->set_mapping('poasassignment_rating_values', $oldid, $newitemid);
    }
    /* protected function process_assignment_submission($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->assignment = $this->get_new_parentid('assignment');
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->timemarked = $this->apply_date_offset($data->timemarked);

        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->teacher = $this->get_mappingid('user', $data->teacher);

        $newitemid = $DB->insert_record('assignment_submissions', $data);
        $this->set_mapping('assignment_submission', $oldid, $newitemid, true); // Going to have files
    } */

    protected function after_execute() {
        //print_r($this);
        // Add assignment related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_poasassignment', 'poasassignmentfiles', null);
        $this->add_related_files('mod_poasassignment', 'poasassignmenttaskfiles', 'poasassignment_task_values');
        $this->add_related_files('mod_poasassignment', 'submissionfiles', 'poasassignment_submission');
        $this->add_related_files('mod_poasassignment', 'commentfiles', null);
        // Add assignment submission files, matching by assignment_submission itemname
        //$this->add_related_files('mod_assignment', 'submission', 'assignment_submission');
        //$this->add_related_files('mod_assignment', 'response', 'assignment_submission');
    }
}
