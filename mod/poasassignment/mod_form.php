<?php

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once('model.php');
class mod_poasassignment_mod_form extends moodleform_mod {

    var $plugins=array();
    /** Displays main options of poasassignment
     */
    function definition() {
        global $COURSE, $CFG, $DB;
        $mform =& $this->_form;
        
        // Adding the "general" fieldset
        //----------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('poasassignmentname', 'poasassignment'), array('size'=>'64'));
        $mform->addHelpButton('name', 'instancename', 'poasassignment');
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the standard "intro" and "introformat" fields
        $this->add_intro_editor(true, get_string('poasassignmentintro', 'poasassignment'));

        // Adding filemanager field where teracher can attach file to the assignment
        $mform->addElement('filemanager', 'poasassignmentfiles', get_string('poasassignmentfiles', 'poasassignment'));
        $mform->addHelpButton('poasassignmentfiles', 'poasassignmentfiles', 'poasassignment');

        $mform->addElement('date_time_selector', 'availabledate', get_string('availabledate', 'poasassignment'), array('optional'=>true));
        $mform->addHelpButton('availabledate', 'availabledate', 'poasassignment');
        $mform->setDefault('availabledate', time());

        $mform->addElement('date_time_selector', 'choicedate', get_string('choicedate', 'poasassignment'), array('optional'=>true));
        $mform->addHelpButton('choicedate', 'choicedate', 'poasassignment');
        $mform->setDefault('choicedate', time()+2*24*3600); // By default student have 2 days to choose task
        $mform->disabledIf('choicedate', 'activateindividualtasks');

        
        $mform->addElement('checkbox', 'preventlatechoice', get_string('preventlatechoice', 'poasassignment'));
        $mform->addHelpButton('preventlatechoice', 'preventlatechoice', 'poasassignment');

        $mform->addElement('checkbox', 'randomtasksafterchoicedate', get_string('randomtasksafterchoicedate', 'poasassignment'));
        $mform->addHelpButton('randomtasksafterchoicedate', 'randomtasksafterchoicedate', 'poasassignment');

        $mform->addElement('date_time_selector', 'deadline', get_string('deadline', 'poasassignment'), array('optional'=>true));
        $mform->addHelpButton('deadline', 'deadline', 'poasassignment');
        $mform->setDefault('deadline', time()+7*24*3600); // By default student have 7 days to complete task

        $mform->addElement('checkbox', 'preventlate', get_string('preventlate', 'poasassignment'));
        $mform->addHelpButton('preventlate', 'preventlate', 'poasassignment');
        // Adding answers fieldset
        //----------------------------------------------------------------------
        global $COURSE, $CFG,$DB;
        $mform->addElement('header', 'answers', get_string('answers', 'poasassignment'));
        
        $mform->addElement('checkbox', 'severalattempts', get_string('severalattempts', 'poasassignment'));
        $mform->addHelpButton('severalattempts', 'severalattempts', 'poasassignment');
        
        $mform->addElement('checkbox', 'newattemptbeforegrade', get_string('newattemptbeforegrade', 'poasassignment'));
        $mform->addHelpButton('newattemptbeforegrade', 'newattemptbeforegrade', 'poasassignment');
        $mform->setAdvanced('newattemptbeforegrade');
        
        $mform->addElement('text', 'penalty', get_string('penalty', 'poasassignment'));
        $mform->addHelpButton('penalty', 'penalty', 'poasassignment');
        $mform->setDefault('penalty', 0);
        $mform->disabledIf('penalty', 'severalattempts', 'notchecked');
        
        $mform->addElement('checkbox','finalattempts',get_string('finalattempts','poasassignment'));
        $mform->addHelpButton('finalattempts','finalattempts','poasassignment');
        $mform->setAdvanced('finalattempts');
        $mform->disabledIf('finalattempts', 'severalattempts', 'notchecked');
        
        $mform->addElement('checkbox', 'notifyteachers', get_string('notifyteachers', 'poasassignment'));
        $mform->addHelpButton('notifyteachers', 'notifyteachers', 'poasassignment');
        $mform->setAdvanced('notifyteachers');
        
        $mform->addElement('checkbox', 'notifystudents', get_string('notifystudents', 'poasassignment'));
        $mform->addHelpButton('notifystudents', 'notifystudents', 'poasassignment');
        $mform->setAdvanced('notifystudents');
        
        // Adding answers fieldsets
        //----------------------------------------------------------------------
        $this->plugins = $DB->get_records('poasassignment_answers');
        foreach ($this->plugins as $plugin) { 
            require_once($plugin->path);
            $poasassignmentplugin = new $plugin->name();
            $poasassignmentplugin->show_settings($mform, $this->_instance);
        }

        // Adding individual tasks fieldset
        //----------------------------------------------------------------------
        $mform->addElement('header', 'poasassignmentfieldset', get_string('poasassignmentfieldset', 'poasassignment'));

        $mform->addElement('checkbox', 'activateindividualtasks', get_string('activateindividualtasks', 'poasassignment'));
        $mform->addHelpButton('activateindividualtasks', 'activateindividualtasks', 'poasassignment');

        // Adding taskgivers selectbox
        //----------------------------------------------------------------------
        $taskgivers=$DB->get_records('poasassignment_taskgivers');
        $names = array();
        foreach ($taskgivers as $taskgiver) {
            $names[$taskgiver->id] = get_string('pluginname', 'poasassignmenttaskgivers_' . $taskgiver->name);
            //array_push($names, get_string('pluginname', 
            //                              'poasassignmenttaskgivers_' . $taskgiver->name));
        }
        
        $mform->addElement('select', 
                           'taskgiverid', 
                           get_string('taskgiverid', 'poasassignment'),
                           $names);
                                
        $mform->disabledIf('taskgiverid', 'activateindividualtasks');
        $mform->addHelpButton('taskgiverid', 'taskgiverid', 'poasassignment');

        $mform->addElement('checkbox', 'secondchoice', get_string('secondchoice', 'poasassignment'));
        $mform->disabledIf('secondchoice', 'activateindividualtasks');
        $mform->addHelpButton('secondchoice', 'secondchoice', 'poasassignment');

        $mform->addElement('select', 'uniqueness', get_string('uniqueness', 'poasassignment'),
                            array(
                                get_string('nouniqueness', 'poasassignment'),
                                get_string('uniquewithingroup', 'poasassignment'),
                                get_string('uniquewithingrouping', 'poasassignment'),
                                get_string('uniquewithincourse', 'poasassignment')));
        $mform->disabledIf('uniqueness', 'activateindividualtasks');
        $mform->addHelpButton('uniqueness', 'uniqueness', 'poasassignment');

        $mform->addElement('checkbox', 'cyclicrandom', get_string('cyclicrandom', 'poasassignment'));
        $mform->disabledIf('cyclicrandom', 'activateindividualtasks');
        $mform->disabledIf('cyclicrandom', 'uniqueness', 'eq', 0);
        $mform->addHelpButton('cyclicrandom', 'cyclicrandom', 'poasassignment');
        
        $mform->addElement('checkbox', 'teacherapproval', get_string('teacherapproval', 'poasassignment'));
        $mform->disabledIf('teacherapproval', 'activateindividualtasks');
        $mform->addHelpButton('teacherapproval', 'teacherapproval', 'poasassignment');

        // Adding graders list
        //----------------------------------------------------------------------
        
        $mform->addElement('header', 'poasassignmentgraderslist', get_string('poasassignmentgraderslist', 'poasassignment'));
        
        $this->graders=$DB->get_records('poasassignment_graders');
        foreach ($this->graders as $graderrecord) {
            require_once($graderrecord->path);
            $mform->addElement('checkbox',$graderrecord->name,get_string($graderrecord->name,'poasassignment_'.$graderrecord->name));
            $conditions = array('poasassignmentid' => $this->_instance, 'graderid' => $graderrecord->id);
            if($DB->record_exists('poasassignment_used_graders',$conditions))
                $mform->setDefault($graderrecord->name,'true');
        }

        // add standard elements, common to all modules
        //----------------------------------------------------------------------
        $this->standard_coursemodule_elements();

        // add standard buttons, common to all modules
        //----------------------------------------------------------------------
        $this->add_action_buttons();
    }

    /** Load files and flags from existing module
     */
    function data_preprocessing(&$default_values){
    
        if(!isset($default_values['intro'])) {
            $default_values['introeditor'] = array('text' => '<p>' . get_string('defaultintro', 'poasassignment') . '</p>');
        }
        if (isset($default_values['flags'])) {
            $flags = (int)$default_values['flags'];
            unset($default_values['flags']);
            $default_values['preventlatechoice'] = $flags & PREVENT_LATE_CHOICE;
            $default_values['randomtasksafterchoicedate'] = $flags & RANDOM_TASKS_AFTER_CHOICEDATE;
            $default_values['preventlate'] = $flags & PREVENT_LATE;
            $default_values['severalattempts'] = $flags & SEVERAL_ATTEMPTS;
            $default_values['notifyteachers'] = $flags & NOTIFY_TEACHERS;
            $default_values['notifystudents'] = $flags & NOTIFY_STUDENTS;
            $default_values['activateindividualtasks'] = $flags & ACTIVATE_INDIVIDUAL_TASKS;
            $default_values['secondchoice'] = $flags & SECOND_CHOICE;
            $default_values['teacherapproval'] = $flags & TEACHER_APPROVAL;
            $default_values['newattemptbeforegrade'] = $flags & ALL_ATTEMPTS_AS_ONE;
            $default_values['finalattempts'] = $flags & MATCH_ATTEMPT_AS_FINAL;
            $default_values['cyclicrandom'] = $flags & POASASSIGNMENT_CYCLIC_RANDOM;
        }
        if ($this->current->instance) {
            $draftitemid = file_get_submitted_draft_itemid('poasassignmentfiles');
            file_prepare_draft_area($draftitemid, $this->context->id, 'mod_poasassignment', 'poasassignmentfiles', 0, array('subdirs'=>true));
            $default_values['poasassignmentfiles'] = $draftitemid;
        }
        if(isset($default_values['taskgiverid'])) {
            //echo ' уменьшаем с '.$default_values['taskgiverid'];
            $default_values['taskgiverid'] = $default_values['taskgiverid']/*-1*/;
        }
        
    }

    /** Check dates
     */
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        // Check open and close times are consistent.
        if ($data['availabledate'] != 0 && $data['choicedate'] != 0 && $data['choicedate'] < $data['availabledate']) {
            $errors['choicedate'] = get_string('choicebeforeopen', 'poasassignment');
        }
        if ($data['availabledate'] != 0 && $data['deadline'] != 0 && $data['deadline'] < $data['availabledate']) {
            $errors['deadline'] = get_string('deadlinebeforeopen', 'poasassignment');
        }
        if ($data['choicedate'] != 0 && $data['deadline'] != 0 && $data['deadline'] < $data['choicedate']) {
            $errors['deadline'] = get_string('deadlinebeforechoice', 'poasassignment');
        }
        
        foreach ($this->plugins as $plugin) { 
            $pluginname=$plugin->name;
            $pluginname::validation($data,$errors);
        }
        
        foreach ($this->graders as $grader) { 
            $gradername = $grader->name;
            $gradername::validation($data,$errors);
        }
        
        if (count($errors) == 0) {
            return true;
        } else {
            return $errors;
        }        
        
        // TODO validate graders
    }
}