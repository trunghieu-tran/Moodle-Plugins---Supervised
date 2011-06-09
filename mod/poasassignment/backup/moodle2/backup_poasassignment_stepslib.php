<?php

class backup_poasassignment_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $poasassignment = new backup_nested_element('poasassignment', array('id'), array(
                'name', 'intro', 'introformat', 'timemodified', 
                'availabledate', 'choicedate', 'deadline', 'flags',
                'taskgiverid', 'uniqueness', 'penalty'));
                
        $criterions = new backup_nested_element('criterions');
        
        $criterion = new backup_nested_element('criterion', array('id'), array(
                'name', 'weight', 'description', 'graderid'));
                
        $answersettings = new backup_nested_element('answersettings');
        
        $answersetting = new backup_nested_element('answersetting', array('id'), array(
                'name', 'value', 'answerid'));
        
//        $assignment = new backup_nested_element('assignment', array('id'), array(
//            'name', 'intro', 'introformat', 'assignmenttype',
//            'resubmit', 'preventlate', 'emailteachers', 'var1',
//            'var2', 'var3', 'var4', 'var5',
//            'maxbytes', 'timedue', 'timeavailable', 'grade',
//            'timemodified'));

//        $submissions = new backup_nested_element('submissions');

//        $submission = new backup_nested_element('submission', array('id'), array(
//            'userid', 'timecreated', 'timemodified', 'numfiles',
//            'data1', 'data2', 'grade', 'submissioncomment',
//            'format', 'teacher', 'timemarked', 'mailed'));

        // Build the tree

        // Apply for 'assignment' subplugins optional stuff at assignment level (not multiple)
        // Remember that order is important, try moving this line to the end and compare XML
//        $this->add_subplugin_structure('assignment', $assignment, false);

        $poasassignment->add_child($criterions);
        $criterions->add_child($criterion);
        
        $poasassignment->add_child($answersettings);
        $answersettings->add_child($answersetting);
//        $assignment->add_child($submissions);
//        $submissions->add_child($submission);

        // Apply for 'assignment' subplugins optional stuff at submission level (not multiple)
//        $this->add_subplugin_structure('assignment', $submission, false);

        // Define sources
        $poasassignment->set_source_table('poasassignment', array('id' => backup::VAR_ACTIVITYID));
        $criterion->set_source_table('poasassignment_criterions', array('poasassignmentid' => backup::VAR_ACTIVITYID));
        $answersetting->set_source_table('poasassignment_ans_stngs', array('poasassignmentid' => backup::VAR_ACTIVITYID));

        // All the rest of elements only happen if we are including user info
        //if ($userinfo) {
        //    $submission->set_source_table('assignment_submissions', array('assignment' => backup::VAR_PARENTID));
        //}

        // Define id annotations
        //$poasassignment->annotate_ids('scale', 'grade');
        //$submission->annotate_ids('user', 'userid');
        //$submission->annotate_ids('user', 'teacher');

        // Define file annotations
        $poasassignment->annotate_files('mod_poasassignment', 'poasassignmentfiles', null); // This file area hasn't itemid
        //$submission->annotate_files('mod_assignment', 'submission', 'id');
        //$submission->annotate_files('mod_assignment', 'response', 'id');

        // Return the root element (assignment), wrapped into standard activity structure
        return $this->prepare_activity_structure($poasassignment);
    }
}
