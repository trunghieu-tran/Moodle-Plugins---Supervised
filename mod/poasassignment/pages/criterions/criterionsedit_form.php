<?php

require_once($CFG->libdir.'/formslib.php');


class criterionsedit_form extends moodleform {
    function definition(){
        global $DB;
        $mform = $this->_form;
        $instance = $this->_customdata;
        
        $repeatarray=array();
        $repeatarray[] = &MoodleQuickForm::createElement('header', 'criterionheader');
        $repeatarray[] = &MoodleQuickForm::createElement('text', 'name', get_string('criterionname','poasassignment'),array('size'=>45));
        $repeatarray[] = $mform->createElement('htmleditor', 'description', get_string('criteriondescription','poasassignment'));
        $repeatarray[] = &MoodleQuickForm::createElement('text', 'weight', get_string('criterionweight','poasassignment'));
        $sources[] = 'manually';
        $usedgraders = $DB->get_records('poasassignment_used_graders',array('poasassignmentid' => $instance['poasassignmentid']));
        foreach($usedgraders as $usedgraderrecord) {
            $grader = $DB->get_record('poasassignment_graders',array('id' => $usedgraderrecord->graderid));
            $gradername = $grader->name;
            require_once($grader->path);
            $sources[] = $gradername::name();
            
            // adding graders identificators - hidden elements to form
            
            $mform->addElement('hidden', 'grader' . (count($sources) - 1), $usedgraderrecord->graderid);
            $mform->setType('grader' . (count($sources) - 1), PARAM_INT);
        }
        //$sources[]='module';
        $repeatarray[] = &MoodleQuickForm::createElement('select', 'source', get_string('criterionsource','poasassignment'),$sources);
        
        if ($instance){
            $repeatno = $DB->count_records('poasassignment_criterions', array('poasassignmentid'=>$instance['poasassignmentid']));
            $repeatno += 1;
        } else {
            $repeatno = 2;
        }
        
        $repeateloptions = array();
        //$mform->setType('weight', PARAM_NUMBER);

        $repeateloptions['name']['helpbutton'] = array('criterionname', 'poasassignment');
        $repeateloptions['description']['helpbutton'] = array('criteriondescription', 'poasassignment');
        $repeateloptions['weight']['helpbutton'] = array('criterionweight', 'poasassignment');
        $repeateloptions['source']['helpbutton'] = array('criterionsource', 'poasassignment');
        //$repeateloptions['weight']['type'] = PARAM_NUMBER;
        
        
        $this->repeat_elements($repeatarray, $repeatno,
                    $repeateloptions, 'option_repeats', 'option_add_fields', 2);
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'page', 'criterions');
        $mform->setType('page', PARAM_TEXT);
        
        $this->add_action_buttons(false, get_string('savechanges', 'admin'));
    }
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $i = 0;
        while (!empty($data['name'][$i] )) {
            if(!isset($data['name'][$i])) {
                $errors["name[$i]"] = get_string('errornoname', 'poasassignment');
            }
            if(!isset($data['weight'][$i])) {
                $errors["weight[$i]"] = get_string('errornoweight', 'poasassignment');
            }
            if($data['weight'][$i] <= 0) {
                $errors["weight[$i]"] = get_string('errornotpositiveweight', 'poasassignment');
            }
            $i++;
        }
        if(count($errors) > 0) {
            return $errors;
        }
        else {
            return true;
        }
    }
}
