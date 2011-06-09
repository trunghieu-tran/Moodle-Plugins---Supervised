<?php

class restore_poasassignment_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('poasassignment', '/activity/poasassignment');
        $paths[] = new restore_path_element('poasassignment_criterion', '/activity/poasassignment/criterions/criterion');
        $paths[] = new restore_path_element('poasassignment_answersetting', '/activity/poasassignment/answersettings/answersetting');
        $paths[] = new restore_path_element('poasassignment_field', '/activity/poasassignment/fields/field');

        // Apply for 'assignment' subplugins optional paths at assignment level
        //$this->add_subplugin_structure('poasassignment', $poasassignment);

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
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->poasassignmentid = $this->get_new_parentid('poasassignment');
        
        $newitemid = $DB->insert_record('poasassignment_criterions', $data);
        $this->set_mapping('poasassignment_criterions', $oldid, $newitemid);
    }
    protected function process_poasassignment_answersetting($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->poasassignmentid = $this->get_new_parentid('poasassignment');
        
        $newitemid = $DB->insert_record('poasassignment_ans_stngs', $data);
        $this->set_mapping('poasassignment_ans_stngs', $oldid, $newitemid);
    }
    protected function process_poasassignment_field($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->poasassignmentid = $this->get_new_parentid('poasassignment');
        
        $newitemid = $DB->insert_record('poasassignment_fields', $data);
        $this->set_mapping('poasassignment_fields', $oldid, $newitemid);
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
        // Add assignment related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_poasassignment', 'poasassignmentfiles', null);
        // Add assignment submission files, matching by assignment_submission itemname
        //$this->add_related_files('mod_assignment', 'submission', 'assignment_submission');
        //$this->add_related_files('mod_assignment', 'response', 'assignment_submission');
    }
}
