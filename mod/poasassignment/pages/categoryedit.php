<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '/model.php');
class categoryedit_page extends abstract_page {
    private $mform;
    function criterions_page() {        
    }
    
    function get_cap() {
        return 'mod/poasassignment:managetasksfields';
    }
    public function pre_view() {        
        $poasmodel = poasassignment_model::get_instance();
        $id = $poasmodel->get_cm()->id;
    }
    function view() {
        global $DB, $OUTPUT;
        $poasmodel = poasassignment_model::get_instance();
    }
    public static function display_in_navbar() {
        return false;
    }
}
class categoryedit_form extends moodleform {
    function definition() {
        global $DB;
        $mform = $this->_form;
        $instance = $this->_customdata;
        
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
        }
        
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
        return true;
    }
}
