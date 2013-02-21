<?php
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '/model.php');
class taskgiversettings_page extends abstract_page {
    //var $poasassignment;
    
    function __construct($cm, $poasassignment) {
        //$this->poasassignment = $poasassignment;
        //$this->cm = $cm;
    }
    
    function get_cap() {
        return 'mod/poasassignment:grade';
    }
     function has_satisfying_parameters() {
        if(!poasassignment_model::get_instance()->has_flag(ACTIVATE_INDIVIDUAL_TASKS)) {
            $this->lasterror = 'errorindtaskmodeisdisabled';
            return false;
        }
        global $DB;
        $tgid = poasassignment_model::get_instance()->get_poasassignment()->taskgiverid;
        if(!$DB->record_exists('poasassignment_taskgivers', array('id' => $tgid))) {
            $this->lasterror = 'errorindtaskmodeisdisabled';
            return false;
        }
        else {
            $tg = $DB->get_record('poasassignment_taskgivers', array('id' => $tgid));
            $tgname = $tg->name;
            require_once(dirname(dirname(__FILE__)) . '/' . $tg->path);
            if (!$tgname::has_settings()) {
                $this->lasterror = 'errorthistghasntsettings';
                return false;
            }
        }
        return true;
    }
    
    function view() {
        global $DB;
        $model = poasassignment_model::get_instance();
        $id = $model->get_cm()->id;
        $poasassignmentid = $model->get_poasassignment()->id;
        $taskgiverrec = $DB->get_record('poasassignment_taskgivers', array('id' => $model->get_poasassignment()->taskgiverid));
        require_once($taskgiverrec->path);
        $taskgivername = $taskgiverrec->name;
        $taskgiver = new $taskgivername();
        echo '<div align="center"><b><big>' .
             get_string('currenttaskgiver', 'poasassignment') .
             ' : ' .
             get_string('pluginname', "poasassignmenttaskgivers_$taskgivername") .
             '</big></b></div><br>';
        $mform = $taskgiver->get_settings_form($id, $poasassignmentid);
        $data = $taskgiver->get_settings($poasassignmentid);
        $mform->set_data($data);
        if($mform->get_data()) {
            $taskgiver->save_settings($mform->get_data());
        }
        $mform->display(); 
    }
    
}