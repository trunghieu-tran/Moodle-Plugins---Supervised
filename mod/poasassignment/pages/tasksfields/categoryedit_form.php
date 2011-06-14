<?php

require_once($CFG->libdir . '/formslib.php');
require_once(dirname(dirname(dirname(__FILE__))) . '\lib.php');

class categoryedit_form extends moodleform {
    function definition() {
        global $DB;
        $mform = $this->_form;
        $instance = $this->_customdata;
        // if ($instance['fieldid'] > 0)
            // $mform->addElement('header','taskfieldeditheader',get_string('taskfieldeditheader','poasassignment'));
        // else
            // $mform->addElement('header','taskfieldaddheader',get_string('taskfieldaddheader','poasassignment'));
        
        $mform->addElement('text', 'name', get_string('categoryname', 'poasassignment'));
        $fields = $DB->get_records('poasassignment_fields', array('poasassignmentid' => $instance['poasassignmentid']));
        $basefields = array();
        foreach($fields as $field) {
            if(($field->ftype == FLOATING 
                || $field->ftype == NUMBER 
                || $field->ftype == LISTOFELEMENTS 
                || $field->ftype == MULTILIST)
                && !$field->random) {
                
                $basefields[$field->id] = $field->name;                
            }
        }
        $mform->addElement('select', 'basefield', get_string('basefield', 'poasassignment'), $basefields);
        if($instance['fieldid'] && $instance['fieldid'] > 0) {
            $mform->setDefault('basefield', $instance['fieldid']);
        }
        $mform->addElement('submit', 'apply', get_string('apply','poasassignment'));
        
        
        $field = $DB->get_record('poasassignment_fields', array('id' => $instance['fieldid']));
        if($field) {
            $repeatarray = array();
            $repeatarray[] = &MoodleQuickForm::createElement('header');
            $repeatarray[] = &MoodleQuickForm::createElement('text', 
                                                         'groupname', 
                                                         get_string('groupname', 'poasassignment'));
            
            if ($field->ftype == NUMBER || $field->ftype == FLOATING) {
                $repeatarray[] = &MoodleQuickForm::createElement('text', 
                                                             'valuemin', 
                                                             get_string('valuemin', 'poasassignment'));
                $repeatarray[] = &MoodleQuickForm::createElement('text', 
                                                             'valuemax', 
                                                             get_string('valuemax', 'poasassignment'));
            }
            if($field->ftype == LISTOFELEMENTS || $field->ftype == MULTILIST) {
                $variantsrecs = $DB->get_records('poasassignment_variants', array('fieldid' => $instance['fieldid']));
                $variants = array();
                foreach($variantsrecs as $variantrec) {
                    $variants[$variantrec->id] = $variantrec->value;
                }
                $select = &MoodleQuickForm::createElement('select', 
                                                             'variants', 
                                                             get_string('variants', 'poasassignment'),
                                                             $variants);
                $select->setMultiple(true);
                $repeatarray[] = $select;
            }
            $repeateoptions = array();

            $repeateoptions['groupname']['helpbutton'] = array('groupname', 'poasassignment');
            //$repeateoptions['valuemin']['helpbutton'] = array('valuemin', 'poasassignment');
            //$repeateoptions['valuemax']['helpbutton'] = array('valuemax', 'poasassignment');
            
            if($instance['categoryid'] && $instance['categoryid'] > 0) {
                $repeatnumber = 3;
            }
            else {
                $repeatnumber = 2;
            }
            $this->repeat_elements($repeatarray, 
                               $repeatnumber,
                               $repeateoptions, 
                               'option_repeats', 
                               'option_add_fields', 
                               2);
            
            //$mform->addElement('text', 'valuemin', get_string('valuemin', 'poasassignment'));
            //$mform->addElement('text', 'valuemax', get_string('valuemax', 'poasassignment'));
            //$mform->addElement('text', 'groupname', get_string('groupname', 'poasassignment'));
        }
        //$mform->addElement('select', 'variants', get_string('groupname', 'poasassignment'));
        
        //$mform->addElement('text','name',get_string('taskfieldname','poasassignment'),array('size'=>45));
        // $mform->addElement('textarea','description',get_string('taskfielddescription','poasassignment'),'rows="5" cols="50"');
        // $mform->addRule('name', null, 'required', null, 'client');        
        // $ftypes = array(get_string('char','poasassignment'),
                        // get_string('text','poasassignment'),
                        // get_string('float','poasassignment'),
                        // get_string('int','poasassignment'),
                        // get_string('date','poasassignment'),
                        // get_string('file','poasassignment'),
                        // get_string('list','poasassignment'),
                        // get_string('multilist','poasassignment'));
                        //get_string('category', 'poasassignment'));
        // $mform->addElement('select','ftype',get_string('ftype','poasassignment'),$ftypes);
        // $mform->addElement('checkbox','showintable',get_string('showintable','poasassignment'));       
        
        // $mform->addElement('checkbox','secretfield',get_string('secretfield','poasassignment'));
        
        // $mform->addElement('checkbox','random',get_string('random','poasassignment'));
        // $types = array(STR, TEXT, DATE, FILE, MULTILIST, CATEGORY);
        // foreach ($types as $type) {
            // $mform->disabledIf('random', 'ftype', 'eq', $type);
        // }
        //$mform->disabledIf('random','valuemin','eq','valuemax');
       
        
        // $mform->addElement('text','valuemin',get_string('valuemin','poasassignment'),10);
        // $mform->setDefault('valuemin', 0);
        
        // $mform->addElement('text','valuemax',get_string('valuemax','poasassignment'),10);
        // $mform->setDefault('valuemax', 100);
        
        // $types = array(STR, TEXT, DATE, FILE, LISTOFELEMENTS, MULTILIST);
        // foreach ($types as $type) {
            // $mform->disabledIf('valuemin', 'ftype', 'eq', $type);
            // $mform->disabledIf('valuemax', 'ftype', 'eq', $type);
        // }
        
        // $mform->addElement('textarea','variants',get_string('variants','poasassignment'),'rows="10" cols="50"');
        // $mform->addHelpButton('variants', 'variants', 'poasassignment');
        // $types = array(STR, TEXT, FLOATING, NUMBER, DATE, FILE);
        // foreach ($types as $type) {
                // $mform->disabledIf('variants', 'ftype', 'eq', $type);
        // }
        
        // hidden params
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);
        $mform->addElement('hidden', 'fieldid', $instance['fieldid']);
        $mform->setType('fieldid', PARAM_INT);
        
        $this->add_action_buttons(true, get_string('savechanges', 'admin'));
    }
    
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        // if ($data['ftype'] == LISTOFELEMENTS || $data['ftype'] == MULTILIST) {
            // $tok = strtok($data['variants'], "\n");
            // $count = 0;
            // while ($tok) {
                // $count++;
                // $tok = strtok("\n");
            // }
            // if ($count < 2) {
                // $errors['variants'] = get_string('errorvariants', 'poasassignment');
                // return $errors;
            // }
        // }
        // if (isset($data['valuemax']) && isset($data['valuemin'])) {
            // if ($data['valuemax'] < $data['valuemin']) {
                // $errors['valuemax'] = get_string('errormaxislessthenmin', 'poasassignment');
                // return $errors;
            // }
        // }
        return true;
    }
}
