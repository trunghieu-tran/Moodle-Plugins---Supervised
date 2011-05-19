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
        return optional_param('mode', null, PARAM_TEXT);
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
            if($this->get_mode() == 'options') {
                return new options_form(null,
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
        if ($this->get_mode() == 'options') {
            redirect(new moodle_url('view.php', array('id' => $data->id, 'page' => 'taskgiversettings')), null, 0);
        }
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
        $mform->addElement('html', '<br><a href="view.php?id=' .
                                   $instance['id'].'&page=taskgiversettings&mode=id">' . 
                                   get_string('basepoasassignment', 'poasassignmenttaskgivers_categorychoice') .
                                   '</a>');
        if (!$nobase) {
            $mform->addElement('html', '<br><a href="view.php?id='.
                                       $instance['id'].
                                       '&page=taskgiversettings&mode=options">' . 
                                       get_string('options', 'poasassignmenttaskgivers_categorychoice') .
                                       '</a></div>');
        }
        else {
            echo '</a></div>';
        }
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
        
        $mform->addElement('hidden', 'mode', 'id');
        $mform->setType('mode', PARAM_TEXT);
        
        $this->add_action_buttons(false, get_string('savechanges', 'admin'));
    }
}
class options_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        global $DB;
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);
        
        $mform->addElement('hidden', 'page', 'taskgiversettings');
        $mform->setType('page', PARAM_TEXT);
        
        $mform->addElement('hidden', 'mode', 'options');
        $mform->setType('mode', PARAM_TEXT);
        
        $this->add_action_buttons(false, get_string('savechanges', 'admin'));
    }
}
?>
