<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '\model.php');

class taskfieldedit_page extends abstract_page {
    private $fieldid;
    private $field;
    private $mform;
    function __construct() {
        $this->fieldid = optional_param('fieldid', 0, PARAM_INT);
    }
    function get_cap() {
        return 'mod/poasassignment:managetasksfields';
    }
    function has_satisfying_parameters() {
        // page is available if individual tasks mode is avtive
        $flag = poasassignment_model::get_instance()->has_flag(ACTIVATE_INDIVIDUAL_TASKS);
        if (!$flag) {
            $this->lasterror = 'errorindtaskmodeisdisabled';
            return false;
        }
        // field is available for edidting if exists
        global $DB;
        $poasassignmentid = poasassignment_model::get_instance()->get_poasassignment()->id;
        $options = array('id' => $this->fieldid, 'poasassignmentid' => $poasassignmentid);
        $fieldexistsininstance = $this->field = $DB->get_record('poasassignment_fields', $options);
        if($this->fieldid != 0 && !$fieldexistsininstance ) {
            $this->lasterror = 'errornonexistentfield';
            return false;
        }
        return true;
    }
    public function pre_view() {
        $model = poasassignment_model::get_instance();
        $poasassignmentid = $model->get_poasassignment()->id;
        $this->mform = new taskfieldedit_form(null, array('id' => $model->get_cm()->id,
                                                      'fieldid' => $this->fieldid,
                                                      'poasassignmentid' => $poasassignmentid));
        if ($this->mform->is_cancelled()) {
            // return to taskfields page
            redirect(new moodle_url('view.php',
                                    array('id' => $model->get_cm()->id,
                                          'page' => 'tasksfields')), 
                     null, 
                     0);
        }
        else {
            if ($this->mform->get_data()) {
                $data = $this->mform->get_data();    
                if ($this->fieldid > 0) {
                    $model->update_task_field($this->fieldid, $data);
                }
                else {
                    $model->add_task_field($data);
                }
                redirect(new moodle_url('view.php',array('id' => $model->get_cm()->id,'page' => 'tasksfields')), null, 0);
            }
        }
    }
    function view() {
        global $DB, $OUTPUT, $USER;
        $model = poasassignment_model::get_instance();
        $poasassignmentid = $model->get_poasassignment()->id;
        if ($this->fieldid > 0) {
            $this->mform->set_data($DB->get_record('poasassignment_fields', array('id' => $this->fieldid)));
            $data = new stdClass();
            $data->variants = $model->get_field_variants($this->fieldid, 0);
            $data->id = $model->get_cm()->id;
            $this->mform->set_data($data);
        }
        $this->mform->display();
    }
    public static function display_in_navbar() {
        return false;
    }
}
class taskfieldedit_form extends moodleform {
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
                        //get_string('category', 'poasassignment'));
        $mform->addElement('select','ftype',get_string('ftype','poasassignment'),$ftypes);
        $mform->addElement('checkbox','showintable',get_string('showintable','poasassignment'));       
        
        $mform->addElement('checkbox','secretfield',get_string('secretfield','poasassignment'));
        
        $mform->addElement('checkbox','random',get_string('random','poasassignment'));
        $types = array(STR, TEXT, DATE, FILE, MULTILIST, CATEGORY);
        foreach ($types as $type) {
            $mform->disabledIf('random', 'ftype', 'eq', $type);
        }
        //$mform->disabledIf('random','valuemin','eq','valuemax');
       
        
        $mform->addElement('text','valuemin',get_string('valuemin','poasassignment'),10);
        $mform->setDefault('valuemin', 0);
        
        $mform->addElement('text','valuemax',get_string('valuemax','poasassignment'),10);
        $mform->setDefault('valuemax', 100);
        
        $types = array(STR, TEXT, DATE, FILE, LISTOFELEMENTS, MULTILIST);
        foreach ($types as $type) {
            $mform->disabledIf('valuemin', 'ftype', 'eq', $type);
            $mform->disabledIf('valuemax', 'ftype', 'eq', $type);
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
        $mform->addElement('hidden', 'page', 'taskfieldedit');
        $mform->setType('id', PARAM_TEXT);
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
        if (isset($data['valuemax']) && isset($data['valuemin'])) {
            if ($data['valuemax'] < $data['valuemin']) {
                $errors['valuemax'] = get_string('errormaxislessthenmin', 'poasassignment');
                return $errors;
            }
        }
        return true;
    }
}
