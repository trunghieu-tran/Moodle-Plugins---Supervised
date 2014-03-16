<?php
/**
 * Enables students to select tasks using search form.
 */
global $CFG;
require_once dirname(dirname(__FILE__)).'/taskgiver.php';
require_once($CFG->libdir.'/formslib.php');
class parameterchoice extends taskgiver{

    private $mform = NULL;
    private $message = "";

    public static function has_settings() {
        return true;
    }
    public static function show_tasks() {
        return true;
    }

    function parameter_search($cmid, $poasassignment) {
        $poasmodel = poasassignment_model::get_instance();
        if ($poasmodel->check_dates())
            return null;
        global $DB,$USER;

        $poasmodel->cash_instance($poasassignment->id);
        $poasmodel->cash_assignee_by_user_id($USER->id);
        $mform = new parametersearch_form(null, array('poasassignmentid' => $poasassignment->id, 'id' => $cmid));
        if($data = $mform->get_data()) {
            $tasks = $poasmodel->get_available_tasks($USER->id);
            if(count($tasks) == 0) {
                print_string('notasks', 'poasassignmenttaskgivers_parameterchoice');
                return $mform;
            }
            $fields = parameterchoice::get_parameters_fields($poasassignment->id);
            if($fields) {
                $satisfyingtasks = array();
                // Tasks that match not all search fields
                $taskswithmismatch = array();
                foreach($fields as $field) {
                    $fieldelementname = 'field' . $field->id;
                    $fieldvalues = $DB->get_records('poasassignment_task_values', array('fieldid' => $field->id));
                    if($fieldvalues) {
                        if($field->ftype == LISTOFELEMENTS || $field->ftype == MULTILIST || $field->ftype == STR || $field->ftype == TEXT) {
                            foreach($fieldvalues as $fieldvalue) {
                                if(!isset($fieldvalue->value) || !isset($tasks[$fieldvalue->taskid])) {
                                    continue;
                                }
                                if($tasks[$fieldvalue->taskid]->hidden == 0) {
                                    $contains = strpos($fieldvalue->value, $data->$fieldelementname);
                                    if($contains !== false) {
                                        for($i = 0; $i < 5; $i++) {
                                            $satisfyingtasks[] = $fieldvalue->taskid;
                                        }
                                    }
                                    else {
                                        $taskswithmismatch[] = $fieldvalue->taskid;
                                    }
                                }
                            }
                        }
                        if ($field->ftype == NUMBER || $field->ftype == FLOATING || $field->ftype == DATE) {
                            foreach ($fieldvalues as $fieldvalue) {
                                if(empty($fieldvalue->value) || !isset($tasks[$fieldvalue->taskid])) {
                                    continue;
                                }
                                if ($tasks[$fieldvalue->taskid]->hidden == 0) {
                                    if ($data->$fieldelementname == $fieldvalue->value) {
                                        for($i = 0; $i < 5; $i++) {
                                            $satisfyingtasks[] = $fieldvalue->taskid;
                                        }
                                    }
                                    else {
                                        if ($data->$fieldelementname == 0) {
                                            continue;
                                        }
                                        else {
                                            for($dif = 1; $dif < 5; $dif++) {
                                                if(abs($data->$fieldelementname - $fieldvalue->value) / $data->$fieldelementname < (0.1 * $dif)) {
                                                    for($i = 0; $i < 5 - $dif; $i++)
                                                        $satisfyingtasks[] = $fieldvalue->taskid;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $taskswithmismatch = array_unique($taskswithmismatch);
                if (count ($fields) > 1 && count($taskswithmismatch) > 0) {
                    // If there are tasks that matched not all search fields, don't use them
                    $satisfyingtasks = array_diff($satisfyingtasks, $taskswithmismatch);
                }
                if (count($satisfyingtasks) > 0) {
                    shuffle($satisfyingtasks);
                    $taskid = $satisfyingtasks[rand(0, count($satisfyingtasks) - 1)];
                    $poasmodel->bind_task_to_assignee($USER->id, $taskid);
                    redirect(new moodle_url('/mod/poasassignment/view.php',array('id'=>$cmid,'page'=>'view')),null,0);
                }
                else { 
                    $this->message = get_string('nosatisfyingtasks','poasassignmenttaskgivers_parameterchoice');
                }
            }
        }
        return $mform;
    }

    public function process_before_output($cmid, $poasassignment) {
        $model = poasassignment_model::get_instance();
        $hascaptohavetask = has_capability('mod/poasassignment:havetask', $model->get_context());
        if ($hascaptohavetask && !$model->check_dates()) {
            $this->mform = $this->parameter_search($cmid, $poasassignment);
        }
    }
    
    function process_before_tasks($cmid, $poasassignment) {
        if (isset($this->mform)) {
            if ($this->message) {
                echo $this->message;
            }
            $this->mform->display();
        }
    }
    //put your code here
    public function get_settings_form($id, $poasassignmentid) {
        return new taskgiver_form(null, 
                                  array('id' => $id,
                                        'poasassignmentid' => $poasassignmentid));  
    }
    public function get_settings($poasassignmentid) {
        global $DB;
        $data = new stdClass();
        $fields = $DB->get_records('poasassignment_fields', array('poasassignmentid' => $poasassignmentid));
        foreach ($fields as $field) {
            $fieldname = 'field' . $field->id;
            if ($DB->record_exists('poasassignment_paramch', array('fieldid' => $field->id))) {
                $data->$fieldname = true;
            }
        }
        return $data;
    }
    public function save_settings($data){
        global $DB;
        $fields = $DB->get_records('poasassignment_fields', array('poasassignmentid' => $data->poasassignmentid));
        foreach ($fields as $field) {
            $fieldname = 'field' . $field->id;
            if(isset($data->$fieldname)) {
                if(!$DB->record_exists('poasassignment_paramch', array('fieldid' => $field->id))) {
                    $DB->insert_record('poasassignment_paramch', array('fieldid' => $field->id));
                }
            }
            else {
                $DB->delete_records('poasassignment_paramch', array('fieldid' => $field->id));
            }
        }
    }
    public static function get_parameters_fields($poasassignmentid) {
        global $DB;
        // get all fields for current poasassignment
        $allfields = $DB->get_records('poasassignment_fields', array('poasassignmentid' => $poasassignmentid));
        $fields = array();
        foreach ($allfields as $field) {
            // add fields that are parameters in $fields
            if ($DB->record_exists('poasassignment_paramch', array('fieldid' => $field->id))) {
                array_push($fields, $field);
            }
        }
        return $fields;
    }
    public function delete_settings($poasassignmentid) {
        global $DB;
        $fields = $DB->get_records('poasassignment_fields', array('poasassignmentid' => $poasassignmentid));
        foreach($fields as $field) {
            $DB->delete_records('poasassignment_paramch', array('fieldid' => $field->id));
        }
    }
}
class taskgiver_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        $poasmodel= poasassignment_model::get_instance();
        global $DB;
        $mform->addElement('header', 'header', get_string('makefieldparameters','poasassignmenttaskgivers_parameterchoice'));
        
        $fields = $DB->get_records('poasassignment_fields', array('poasassignmentid' => $instance['poasassignmentid']));
        
        foreach ($fields as $field) {
            if($field->random == 1) {
                $mform->addElement('static', 'field' . $field->id, $field->name, get_string('fieldisrandom', 'poasassignment'));
            }
            else {
                if($field->ftype == FILE) {
                    $mform->addElement('static', 'field' . $field->id, $field->name, get_string('fieldisfile', 'poasassignment'));
                }
                else {
                    $mform->addElement('checkbox', 'field' . $field->id, $field->name);
                }
            }
        }
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);
        
        $mform->addElement('hidden', 'page', 'taskgiversettings');
        $mform->setType('page', PARAM_TEXT);

        $this->add_action_buttons(false, get_string('savechanges', 'admin'));
    }
}
class parametersearch_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        $poasmodel= poasassignment_model::get_instance();
        global $DB;
        
        $fields = parameterchoice::get_parameters_fields($instance['poasassignmentid']);
        if($fields) {
            $mform->addElement('header','header',get_string('inputparameters','poasassignmenttaskgivers_parameterchoice'));
            $poasmodel= poasassignment_model::get_instance();
            foreach($fields as $field) {
                if($field->ftype!=MULTILIST && $field->ftype!=LISTOFELEMENTS)
                    if($field->ftype==DATE)
                        $mform->addElement('date_selector','field'.$field->id,$field->name);
                    else {
                        if(has_capability('mod/poasassignment:seefielddescription', context_module::instance($instance['id'])))
                            $mform->addElement('text','field'.$field->id,$field->name.'('.$poasmodel->ftypes[$field->ftype].')'.$poasmodel->help_icon($field->description));
                        else
                            $mform->addElement('text','field'.$field->id,$field->name.'('.$poasmodel->ftypes[$field->ftype].')');
                    }
                else {
                    $opt=$poasmodel->get_field_variants($field->id);
                    $mform->addElement('select','field'.$field->id,$field->name,$opt);
                }
            }

            $mform->addElement('hidden', 'id', $instance['id']);
            $mform->setType('id', PARAM_INT);

            $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
            $mform->setType('poasassignmentid', PARAM_INT);

            $mform->addElement('hidden', 'page', 'tasks');
            $mform->setType('page', PARAM_TEXT);

            $this->add_action_buttons(false, get_string('getrandomtask', 'poasassignment'));
        }
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        global $DB;
        $fields = parameterchoice::get_parameters_fields($data['poasassignmentid']);
        foreach($fields as $field) {
            if(($field->ftype==FLOATING || $field->ftype==NUMBER ) && !is_numeric($data['field'.$field->id])) {
                if(strlen($data['field'.$field->id])>0) {
                    $errors['field'.$field->id]=get_string('errormustbefloat','poasassignment');
                    return $errors;
                }
            }
        }
        return true;
    }
}
?>
