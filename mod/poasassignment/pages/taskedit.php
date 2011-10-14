<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '\model.php');

class taskedit_page extends abstract_page {
    private $taskid;
    
    function __construct() {
        global $DB;
        $this->taskid = optional_param('taskid', 0, PARAM_INT);
        $this->mode   = optional_param('mode', null, PARAM_INT);
    }
    function get_cap() {
        return 'mod/poasassignment:managetasks';
    }
    
    function has_satisfying_parameters() {
        global $DB;
        if($this->taskid != 0 && !$this->task = $DB->get_record('poasassignment_tasks', array('id' => $this->taskid))) {        
            $this->lasterror = 'errornonexistenttask';
            return false;
        }
        return true;
    }
    function pre_view() {
        global $DB, $PAGE;
		$id = poasassignment_model::get_instance()->get_cm()->id;
		// add navigation nodes
		$tasks = new moodle_url('view.php', array('id' => $id,
														'page' => 'tasks'));
		$PAGE->navbar->add(get_string('tasks','poasassignment'), $tasks);
		
		$taskedit = new moodle_url('view.php', array('id' => $id,
														  'page' => 'taskedit',
														  'taskid' => $this->taskid));
		$PAGE->navbar->add(get_string('taskedit','poasassignment'), $taskedit);
		
        $model = poasassignment_model::get_instance();
        if ($this->mode == SHOW_MODE || $this->mode == HIDE_MODE) {
            if (isset($this->taskid) && $this->taskid > 0) {
                $this->task = $DB->get_record('poasassignment_tasks', array('id'=>$this->taskid));
                if ($this->mode == SHOW_MODE) {
                    $this->task->hidden = 0;
                }
                else {
                    $this->task->hidden = 1;
                }
                $DB->update_record('poasassignment_tasks', $this->task);
                redirect(new moodle_url('view.php',array('id'=>$model->get_cm()->id, 'page'=>'tasks')), null, 0);
            }
            else
                print_error('invalidtaskid','poasassignment');
        }
        if ($this->mode == DELETE_MODE) {
            if ($this->taskid > 0) {
                //TODO delete task and task values & references from student's pagele
                $model->delete_task($this->taskid);
                redirect(new moodle_url('view.php',array('id'=>$model->get_cm()->id, 'page'=>'tasks')), null, 0);
            } 
            else
                print_error('invalidtaskid','poasassignment');
        }
        $poasassignmentid = $model->get_poasassignment()->id;
        $this->mform = new taskedit_form(null, array('id' => $model->get_cm()->id, 
                                       'taskid' => $this->taskid,
                                       'poasassignmentid' => $poasassignmentid));
        if ($this->mform->is_cancelled()) {
            redirect(new moodle_url('view.php', array('id' => $model->get_cm()->id, 
                                                      'page' => 'tasks')), 
                                                      null, 
                                                      0);
        }
        else {
            if ($this->mform->get_data()) {
                $data = $this->mform->get_data();
                if ($this->taskid > 0) {
                    $model->update_task($this->taskid,$data);            
                }
                else {
                    $model->add_task($data);
                }
                redirect(new moodle_url('view.php', array('id' => $model->get_cm()->id, 'page' => 'tasks')), null, 0);
            }
            
        }
        if ($this->taskid > 0) {
            $data = $model->get_task_values($this->taskid);
            $data->id = $model->get_cm()->id;
            $this->mform->set_data($data);
        }
    }
    function view() {
        $this->mform->display();
    }
    public static function display_in_navbar() {
        return false;
    }
}
class taskedit_form extends moodleform {

    function definition(){
        global $DB;
        $mform = $this->_form;
        $instance = $this->_customdata;
        if($instance['taskid']>0)
            $mform->addElement('header','taskeditheader',get_string('taskeditheader','poasassignment'));
        else
            $mform->addElement('header','taskaddheader',get_string('taskaddheader','poasassignment'));
        
        $mform->addElement('text','name',get_string('taskname','poasassignment'),array('size'=>45));
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addElement('htmleditor','description',get_string('taskintro', 'poasassignment'));
        $mform->addElement('checkbox','hidden',get_string('taskhidden', 'poasassignment'));
        $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$instance['poasassignmentid']));
        $poasmodel= poasassignment_model::get_instance();
        foreach($fields as $field) {
            $name = $field->name.' '.$poasmodel->help_icon($field->description);
            if($field->ftype==STR)
                $mform->addElement('text','field'.$field->id,$name,array('size'=>45));
                
            if($field->ftype==TEXT)
                $mform->addElement('htmleditor','field'.$field->id,$name);
                
            if( ($field->ftype==FLOATING || $field->ftype==NUMBER) && $field->random) {
                $mform->addElement('static','field'.$field->id,$name,'random field');
            }
            
            if( ($field->ftype==FLOATING || $field->ftype==NUMBER) && !$field->random) {
                $mform->addElement('text','field'.$field->id,$name,array('size'=>10));
            }

            if($field->ftype==DATE) {
                $mform->addElement('date_selector','field'.$field->id,$name);
            }
            
            if($field->ftype==FILE) {
                $mform->addElement('filemanager','field'.$field->id,$name);
            }
            if($field->ftype==LISTOFELEMENTS || $field->ftype==MULTILIST) {
                if($field->random==0) {
                    /* $tok = strtok($field->variants,"\n");
                    while($tok) {
                        $opt[]=$tok;
                        $tok=strtok("\n");
                    } */
                    $opt=$poasmodel->get_field_variants($field->id);
                    $select=&$mform->addElement('select','field'.$field->id,$name,$opt);
                    if($field->ftype==MULTILIST)
                        $select->setMultiple(true);
                }
                else
                    $mform->addElement('static','field'.$field->id,$name,'random field');
            }
        }
        
        // hidden params
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);
        
        $mform->addElement('hidden', 'taskid', $instance['taskid']);
        $mform->setType('taskid', PARAM_INT);
        $mform->addElement('hidden', 'page', 'taskedit');
        $mform->setType('taskid', PARAM_TEXT);
        
        $this->add_action_buttons(true, get_string('savechanges', 'admin'));
    }
    
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        global $DB;
        $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$data['poasassignmentid']));
        foreach($fields as $field) {
            if(!$field->random &&($field->ftype==FLOATING || $field->ftype==NUMBER)) {
                if(!($field->valuemin==0 && $field->valuemax==0 )) {
                    if($data['field'.$field->id]>$field->valuemax || $data['field'.$field->id]<$field->valuemin) {
                    $errors['field'.$field->id]=get_string('valuemustbe','poasassignment').' '.
                                                get_string('morethen','poasassignment').' '.
                                                $field->valuemin.' '.
                                                get_string('and','poasassignment').' '.
                                                get_string('lessthen','poasassignment').' '.
                                                $field->valuemax;
                    return $errors;
                    }
                }
            }
            if($field->ftype==MULTILIST && !isset($data['field'.$field->id])) {
                $errors['field'.$field->id]=get_string('errornovariants','poasassignment');
                return $errors;
            }
            
        }
       
        return true;
    }
}
