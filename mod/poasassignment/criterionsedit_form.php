<?php

require_once($CFG->libdir.'/formslib.php');


class criterionsedit_form extends moodleform {
    function definition(){
        global $DB;
        $mform = $this->_form;
       // echo '1';
        $instance = $this->_customdata;
         
        $repeatarray=array();
        $repeatarray[] = &MoodleQuickForm::createElement('header', 'criterionheader');
        $repeatarray[] = &MoodleQuickForm::createElement('text', 'name', get_string('criterionname','poasassignment'),array('size'=>45));
        $repeatarray[] = $mform->createElement('htmleditor', 'description', get_string('criteriondescription','poasassignment'));
        $repeatarray[] = &MoodleQuickForm::createElement('text', 'weight', get_string('criterionweight','poasassignment'));
        
        $sources[]='manually';
        $sources[]='module';
        $repeatarray[] = &MoodleQuickForm::createElement('select', 'source', get_string('criterionsource','poasassignment'),$sources);
        
        if ($instance){
            $repeatno = $DB->count_records('poasassignment_criterions', array('poasassignmentid'=>$instance['poasassignmentid']));
            $repeatno += 1;
        } else {
            $repeatno = 2;
        }
        
        $repeateloptions = array();
        $mform->setType('weight', PARAM_NUMBER);

        //$repeateloptions['option']['helpbutton'] = array('choiceoptions', get_string('modulenameplural', 'choice'), 'choice');
        //$mform->setType('option', PARAM_CLEAN);

        //$mform->setType('optionid', PARAM_INT);

        $this->repeat_elements($repeatarray, $repeatno,
                    $repeateloptions, 'option_repeats', 'option_add_fields', 2);
                
        
       /*  $mform->addElement('text','name',get_string('criterionname','poasassignment'),array('size'=>45));
        $mform->addRule('name', null, 'required', null, 'client');
        
        $mform->addElement('htmleditor','description',get_string('criteriondescription','poasassignment'));
        
        $mform->addElement('text','weight',get_string('criterionweight','poasassignment'));
        $mform->setDefault('weight','1.00');
        
        $sources[]='manually';
        //$sources[]=$DB->get_records('poasassignment_rating_modules_list',null,'name','name');
        $mform->addElement('select','source',get_string('criterionsource','poasassignment'),$sources); */
        
        // hidden params
        //$mform->addElement('hidden', 'criterionid', $instance['criterionid']);
        //$mform->setType('criterionid', PARAM_INT);
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'tab', 'criterions');
        $mform->setType('tab', PARAM_TEXT);
        
        //$mform->addElement('hidden', 'mode', $instance['mode']);
        //$mform->setType('mode', PARAM_INT);
        
        $this->add_action_buttons(false, get_string('savechanges', 'admin'));
    }
    
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $i = 0;
        while (!empty($data['name'][$i] )) {
            if(!isset($data['name'][$i])) {
                $errors['name'][$i] = get_string('errornoname', 'poasassignment');
                return $errors;
            }
            $i++;
        }
        return true;
    }
}
