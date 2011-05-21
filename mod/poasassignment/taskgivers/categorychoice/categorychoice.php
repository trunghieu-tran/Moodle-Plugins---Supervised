<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of categorysearch
 *
 * @author Arkanif
 */
global $CFG;
require_once dirname(dirname(__FILE__)).'\taskgiver.php';
require_once($CFG->libdir.'/formslib.php');
class categorychoice extends taskgiver{

    public $showtasks = false;
    public $hassettings = true;
    
    private function get_mode() {
        return optional_param('tgmode', null, PARAM_TEXT);
    }
    public function get_settings_form($id, $poasassignmentid) {
        if(!$this->get_mode()) {
            return new settingmode_form(null, 
                                        array('id' => $id,
                                        'poasassignmentid' => $poasassignmentid)); 
        }
        else {
            if($this->get_mode() == 'id') {
                return new basechoice_form(null, 
                                           array('id' => $id,
                                           'poasassignmentid' => $poasassignmentid)); 
            }
            if($this->get_mode() == 'categories') {
                return new managecategories_form(null,
                                        array('id' => $id,
                                        'poasassignmentid' => $poasassignmentid)); 
            }
            if($this->get_mode() == 'tasks') {
                return new tasks_form(null,
                                        array('id' => $id,
                                        'poasassignmentid' => $poasassignmentid)); 
            }
        }
    }
    public static function get_allowed_instances($mypoasassignmentid) {
        global $DB;
        $poasassignments = $DB->get_records('poasassignment',array());
        $poasassignmentinstances = array();
        foreach ($poasassignments as $poasassignment) {
            if ($poasassignment->id != $mypoasassignmentid &&
                $poasassignment->flags & ACTIVATE_INDIVIDUAL_TASKS) {
                
                $poasassignmentinstances[$poasassignment->id] = $poasassignment->name;
            }
        }
        return $poasassignmentinstances;
    }
    public function save_settings($data){
        global $DB;
        $myinstance = $DB->get_record('poasassignment_tg_cat', array('poasassignmentid' => $data->poasassignmentid));
        if ($this->get_mode() == 'id') {
            $rec = new stdClass();
            $rec->basepoasassignmentid = $data->selectpoasassignment;
            $rec->poasassignmentid = $data->poasassignmentid;
            if (!$DB->record_exists('poasassignment_tg_cat', array('poasassignmentid' => $data->poasassignmentid))) {
                $DB->insert_record('poasassignment_tg_cat', $rec);
            }
            else {
                $newrec = $DB->get_record('poasassignment_tg_cat', array('poasassignmentid' => $data->poasassignmentid));
                $rec->id = $newrec->id;
                $DB->update_record('poasassignment_tg_cat', $rec);
            }
            redirect(new moodle_url('view.php', array('id' => $data->id, 'page' => 'taskgiversettings')), null, 0);
        }
        if ($this->get_mode() == 'categories') {
            $DB->delete_records('poasassignment_tg_cat_ctgrs', array('taskgiver_cat_id' => $myinstance->id));
            
            for($i = 0; $i < $data->option_repeats; $i++) {
                if(empty($data->categoryname[$i])) {
                    continue;
                }
                $rec = new stdClass();
                $rec->fieldid = $data->categoryfield[$i];
                $rec->taskgiver_cat_id = $myinstance->id;
                $rec->name = $data->categoryname[$i];
                $rec->minimum = $data->categorymin[$i];
                $rec->maximum = $data->categorymax[$i];
                
                $DB->insert_record('poasassignment_tg_cat_ctgrs', $rec);
            }
            redirect(new moodle_url('view.php', array('id' => $data->id, 'page' => 'taskgiversettings')), null, 0);
        }
        if ($this->get_mode() == 'tasks') {
            $myinstance = $DB->get_record('poasassignment_tg_cat', array('poasassignmentid' => $data->poasassignmentid));
            $basetasks = $DB->get_records('poasassignment_tasks', array('poasassignmentid' => $myinstance->basepoasassignmentid));
            
            $DB->delete_records('poasassignment_tg_cat_tasks', array('taskgiver_cat_id' => $myinstance->id));
            foreach ($basetasks as $basetask) {
                $fieldname = 'currenttasks' . $basetask->id;
                $value = $data->$fieldname;
                foreach($value as $currenttaskid) {
                    $rec = new stdClass();
                    $rec->basetaskid = $basetask->id;
                    $rec->taskid = $currenttaskid;
                    $rec->taskgiver_cat_id = $myinstance->id;
                    $DB->insert_record('poasassignment_tg_cat_tasks', $rec);
                }
            }
            redirect(new moodle_url('view.php', array('id' => $data->id, 'page' => 'taskgiversettings')), null, 0);
        }
    }
    public function get_settings($poasassignmentid) {
        global $DB;
        if ($this->get_mode() == 'id') {
            $data = new stdClass();
            $rec = $DB->get_record('poasassignment_tg_cat', array('poasassignmentid' => $poasassignmentid));
            $data->selectpoasassignment = $rec->basepoasassignmentid;
            return $data;
        }
        if ($this->get_mode() == 'tasks') {
            $data = new stdClass();
            $myinstance = $DB->get_record('poasassignment_tg_cat', 
                                          array('poasassignmentid' => $poasassignmentid));
            $basetasks = $DB->get_records('poasassignment_tasks', 
                                          array('poasassignmentid' => $myinstance->basepoasassignmentid));
            
            foreach ($basetasks as $basetask) {
                $fieldname = 'currenttasks' . $basetask->id;
                $tgcattasks = $DB->get_records('poasassignment_tg_cat_tasks', 
                                         array('taskgiver_cat_id' => $myinstance->id, 
                                               'basetaskid' => $basetask->id));
                $value = array();
                foreach ($tgcattasks as $tgcattask) {
                    $value[] = $tgcattask->taskid;
                }
                $data->$fieldname = $value;
            }
            return $data;
        }
        if ($this->get_mode() == 'categories') {
            $data = new stdClass();
            $myinstance = $DB->get_record('poasassignment_tg_cat', array('poasassignmentid' => $poasassignmentid));
            $categories = $DB->get_records('poasassignment_tg_cat_ctgrs', array('taskgiver_cat_id' => $myinstance->id));
            
            $i = 0;
            foreach ($categories as $category) {
                $data->categoryname[$i] = $category->name;
                $data->categoryfield[$i] = $category->fieldid;
                $data->categorymin[$i] = $category->minimum;
                $data->categorymax[$i] = $category->maximum;
                $i++;
            }
            return $data;
        }
    }
    public function delete_settings($poasassignmentid) {
        global $DB;
        $tg = $DB->get_record('poasassignment_tg_cat', array('poasassignmentid' => $poasassignmentid));
        
        $DB->delete_records('poasassignment_tg_cat_ctgrs', array('taskgiver_cat_id' => $tg->id));
        $DB->delete_records('poasassignment_tg_cat_tasks', array('taskgiver_cat_id' => $tg->id));
        $DB->delete_record('poasassignment_tg_cat', array('id' => $tg->id));
    }
}
class settingmode_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        $poasmodel = poasassignment_model::get_instance();
        global $DB;
        //$basename = get_string('nobase', 'poasassignmenttaskgivers_categorychoice');
        $nobase = true;
        if ($me = $DB->get_record('poasassignment_tg_cat', array('poasassignmentid' => $instance['poasassignmentid']))) {
            if ($base = $DB->get_record('poasassignment', array('id' => $me->basepoasassignmentid))) {
                $nobase = false;
                $basecourse = $DB->get_record('course', array('id' => $base->course), '*', MUST_EXIST);
                $basecm = get_coursemodule_from_instance('poasassignment', $base->id, $basecourse->id, false, MUST_EXIST);
                $baseurl = '<a href="view.php?id='.$basecm->id.'">'.$base->name.'</a>';
                $mform->addElement('html', '<div align="center">' . 
                                           get_string('currentbase', 'poasassignmenttaskgivers_categorychoice') .
                                           ': ' .
                                           $baseurl);
            }
        }
        if($nobase) {
            $mform->addElement('html', '<div align="center">' . 
                                       get_string('currentbase', 'poasassignmenttaskgivers_categorychoice') .
                                       ': ' .
                                       get_string('nobase', 'poasassignmenttaskgivers_categorychoice'));
        }
        
        // Show link to base poasassignment instance page (show always)
        $mform->addElement('html', '<br><a href="view.php?id=' .
                                   $instance['id'].'&page=taskgiversettings&tgmode=id">' . 
                                   get_string('basepoasassignment', 'poasassignmenttaskgivers_categorychoice') .
                                   '</a>');
        
        // Show link to tasks page (only if taskgiver knows base poasassignment instance)
        if (!$nobase) {
            $mform->addElement('html', '<br><a href="view.php?id='.
                                       $instance['id'].
                                       '&page=taskgiversettings&tgmode=tasks">' . 
                                       get_string('tasks', 'poasassignmenttaskgivers_categorychoice'));
        }
        // Show link to category page (only if taskgiver knows base poasassignment instance)
        if (!$nobase) {
            $mform->addElement('html', '<br><a href="view.php?id='.
                                       $instance['id'].
                                       '&page=taskgiversettings&tgmode=categories">' . 
                                       get_string('managecategories', 'poasassignmenttaskgivers_categorychoice'));
        }
        
        echo '</a></div>';
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);
        
        $mform->addElement('hidden', 'page', 'taskgiversettings');
        $mform->setType('page', PARAM_TEXT);
        //$this->add_action_buttons(false, get_string('savechanges', 'admin'));
    }
}   
class basechoice_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        global $DB;
        $poasassignmentinstances = categorychoice::get_allowed_instances($instance['poasassignmentid']);
        $mform->addElement('header', 'basepoasassignment', get_string('basepoasassignment', 'poasassignmenttaskgivers_categorychoice'));
        $mform->addElement('select', 
                           'selectpoasassignment', 
                           get_string('selectpoasassignment', 'poasassignmenttaskgivers_categorychoice'),
                           $poasassignmentinstances);
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);
        
        $mform->addElement('hidden', 'page', 'taskgiversettings');
        $mform->setType('page', PARAM_TEXT);
        
        $mform->addElement('hidden', 'tgmode', 'id');
        $mform->setType('tgmode', PARAM_TEXT);
        
        $this->add_action_buttons(false, get_string('savechanges', 'admin'));
    }
}
class managecategories_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        global $DB;
        //$mform->addElement('header', 'options', get_string('managecategories', 'poasassignmenttaskgivers_categorychoice'),true);
        
        
        $myinstance = $DB->get_record('poasassignment_tg_cat', array('poasassignmentid' => $instance['poasassignmentid']));
        $myfields = $DB->get_records('poasassignment_fields', array('poasassignmentid' => $myinstance->poasassignmentid));
        $fields = array();
        foreach ($myfields as $myfield) {
            $fields[$myfield->id] = $myfield->name;
        }
        $repeatarray = array();
        $repeatarray[] = &MoodleQuickForm::createElement('header');
        $repeatarray[] = &MoodleQuickForm::createElement('text', 
                                                         'categoryname', 
                                                         get_string('categoryname', 'poasassignmenttaskgivers_categorychoice'));
        $repeatarray[] = &MoodleQuickForm::createElement('select', 
                                                         'categoryfield', 
                                                         get_string('categoryfield', 'poasassignmenttaskgivers_categorychoice'), 
                                                         $fields);
        $repeatarray[] = &MoodleQuickForm::createElement('text', 
                                                         'categorymin', 
                                                         get_string('categorymin', 'poasassignmenttaskgivers_categorychoice'));
        $repeatarray[] = &MoodleQuickForm::createElement('text', 
                                                         'categorymax', 
                                                         get_string('categorymax', 'poasassignmenttaskgivers_categorychoice'));
        
        $repeateoptions = array();

        $repeateoptions['categoryname']['helpbutton'] = array('categoryname', 'poasassignmenttaskgivers_categorychoice');
        $repeateoptions['categoryfield']['helpbutton'] = array('categoryfield', 'poasassignmenttaskgivers_categorychoice');
        $repeateoptions['categorymin']['helpbutton'] = array('categorymin', 'poasassignmenttaskgivers_categorychoice');
        $repeateoptions['categorymax']['helpbutton'] = array('categorymax', 'poasassignmenttaskgivers_categorychoice');
        
        $repeatno = 2;
        if ($instance){
            $repeatno = $DB->count_records('poasassignment_tg_cat_ctgrs', array('taskgiver_cat_id'=>$myinstance->id));
            $repeatno += 1;
        } else {
            $repeatno = 2;
        }
        
        $this->repeat_elements($repeatarray, 
                               $repeatno,
                               $repeateoptions, 
                               'option_repeats', 
                               'option_add_fields', 
                               2);
        
        //$mform->addElement('text', 'categoryname', get_string('categoryname', 'poasassignmenttaskgivers_categorychoice'));
        
        //$mform->addElement('select', 'categoryfield', get_string('categoryfield', 'poasassignmenttaskgivers_categorychoice'), $fields);
        //$mform->addHelpButton('categoryfield', 'categoryfield', 'poasassignmenttaskgivers_categorychoice');
        
        //$mform->addElement('text', 'categorymin', get_string('categorymin', 'poasassignmenttaskgivers_categorychoice'));
        //$mform->addHelpButton('categorymin', 'categorymin', 'poasassignmenttaskgivers_categorychoice');
        
        //$mform->addElement('text', 'categorymax', get_string('categorymax', 'poasassignmenttaskgivers_categorychoice'));
        //$mform->addHelpButton('categorymax', 'categorymax', 'poasassignmenttaskgivers_categorychoice');
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);
        
        $mform->addElement('hidden', 'page', 'taskgiversettings');
        $mform->setType('page', PARAM_TEXT);
        
        $mform->addElement('hidden', 'tgmode', 'categories');
        $mform->setType('tgmode', PARAM_TEXT);
        
        $this->add_action_buttons(false, get_string('savechanges', 'admin'));
    }
}
class tasks_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        global $DB;
        
        // Prepare : load tasks of base instance in $basetasks
        if ($myinstance = $DB->get_record('poasassignment_tg_cat', array('poasassignmentid' => $instance['poasassignmentid']))) {
            $basetasks = $DB->get_records('poasassignment_tasks', array('poasassignmentid' => $myinstance->basepoasassignmentid));
            
            // Prepare : load coursemodule of base instance in $basecm
            $base = $DB->get_record('poasassignment', array('id' => $myinstance->basepoasassignmentid));
            $basecourse = $DB->get_record('course', array('id' => $base->course), '*', MUST_EXIST);
            $basecm = get_coursemodule_from_instance('poasassignment', $base->id, $basecourse->id, false, MUST_EXIST);
            
            // Prepare : load tasks of current instance in $tasks
            $currenttasks = $DB->get_records('poasassignment_tasks', array('poasassignmentid' => $instance['poasassignmentid']));
            
            $tasks = array();
            foreach ($currenttasks as $currenttask) {
                $tasks[$currenttask->id] = $currenttask->name;
            }
            $mform->addElement('header', 'tasks', get_string('tasks', 'poasassignmenttaskgivers_categorychoice'));
            
            // For each task in base instance create form to choose assosiated tasks in current instance
            foreach($basetasks as $basetask) {
                $basetaskurl = '<a href="pages/tasks/taskview.php?id=' .
                               $basecm->id .
                               '&taskid=' .
                               $basetask->id .
                               '">' .
                               $basetask->name .
                               '</a>';
                $mform->addElement('static', 
                                   'basetask' . $basetask->id, 
                                   get_string('basetask', 'poasassignmenttaskgivers_categorychoice'), 
                                   $basetaskurl);
                $select = $mform->addElement('select', 
                                             'currenttasks' . $basetask->id, 
                                             get_string('currenttasks', 'poasassignmenttaskgivers_categorychoice'),
                                             $tasks);
                $select->setMultiple(true);
                $mform->addHelpButton('currenttasks' . $basetask->id, 
                                      'currenttasks', 
                                      'poasassignmenttaskgivers_categorychoice');
            }        
        }        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);
        
        $mform->addElement('hidden', 'page', 'taskgiversettings');
        $mform->setType('page', PARAM_TEXT);
        
        $mform->addElement('hidden', 'tgmode', 'tasks');
        $mform->setType('tgmode', PARAM_TEXT);
        
        $this->add_action_buttons(false, get_string('savechanges', 'admin'));
    }
}
?>
