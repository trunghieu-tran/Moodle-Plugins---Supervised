<?php

require_once($CFG->libdir . '/formslib.php');
require_once(dirname(dirname(dirname(__FILE__))) . '\lib.php');

class tasksfieldsedit_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        if ($instance['fieldid'] > 0)
            $mform->addElement('header','taskfieldeditheader',get_string('taskfieldeditheader','poasassignment'));
        else
            $mform->addElement('header','taskfieldaddheader',get_string('taskfieldaddheader','poasassignment'));
        
        $mform->addElement('text','name',get_string('taskfieldname','poasassignment'),array('size'=>45));
        $mform->addElement('textarea','description',get_string('taskfielddescription','poasassignment'),'rows="5" cols="50"');
        $mform->addRule('name', null, 'required', null, 'client');        
        $ftypes = array(get_string('char','poasassignment'),
                        get_string('text','poasassignment'),
                        get_string('float','poasassignment'),
                        get_string('int','poasassignment'),
                        get_string('date','poasassignment'),
                        get_string('file','poasassignment'),
                        get_string('list','poasassignment'),
                        get_string('multilist','poasassignment'));
        $mform->addElement('select','ftype',get_string('ftype','poasassignment'),$ftypes);
        $mform->addElement('checkbox','showintable',get_string('showintable','poasassignment'));       
        
        $mform->addElement('checkbox','secretfield',get_string('secretfield','poasassignment'));
        
        $mform->addElement('checkbox','random',get_string('random','poasassignment'));
        $types = array(STR, TEXT, DATE, FILE, MULTILIST);
        foreach ($types as $type) {
            $mform->disabledIf('random', 'ftype', 'eq', $type);
        }
        //$mform->disabledIf('random','minvalue','eq','maxvalue');
       
        
        $mform->addElement('text','minvalue',get_string('minvalue','poasassignment'),10);
        $mform->setDefault('minvalue', 0);
        
        $mform->addElement('text','maxvalue',get_string('maxvalue','poasassignment'),10);
        $mform->setDefault('maxvalue', 100);
        
        $types = array(STR, TEXT, DATE, FILE, LISTOFELEMENTS, MULTILIST);
        foreach ($types as $type) {
            $mform->disabledIf('minvalue', 'ftype', 'eq', $type);
            $mform->disabledIf('maxvalue', 'ftype', 'eq', $type);
        }
        
        $mform->addElement('textarea','variants',get_string('variants','poasassignment'),'rows="10" cols="50"');
        $mform->addHelpButton('variants', 'variants', 'poasassignment');
        $types = array(STR, TEXT, FLOATING, NUMBER, DATE, FILE);
        foreach ($types as $type) {
                $mform->disabledIf('variants', 'ftype', 'eq', $type);
        }
        
        // hidden params
        $mform->addElement('hidden', 'fieldid', $instance['fieldid']);
        $mform->setType('fieldid', PARAM_INT);
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);
        
        $this->add_action_buttons(true, get_string('savechanges', 'admin'));
    }
    
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if ($data['ftype'] == LISTOFELEMENTS || $data['ftype'] == MULTILIST) {
            $tok = strtok($data['variants'], "\n");
            $count = 0;
            while ($tok) {
                $count++;
                $tok = strtok("\n");
            }
            if ($count < 2) {
                $errors['variants'] = get_string('errorvariants', 'poasassignment');
                return $errors;
            }
        }
        if (isset($data['maxvalue']) && isset($data['minvalue'])) {
            if ($data['maxvalue'] < $data['minvalue']) {
                $errors['maxvalue'] = get_string('errormaxislessthenmin', 'poasassignment');
                return $errors;
            }
        }
        return true;
    }
}
