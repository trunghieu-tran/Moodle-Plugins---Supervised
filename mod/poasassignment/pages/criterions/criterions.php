<?php
global $CFG;
require_once(dirname(dirname(__FILE__)) . '\abstract_page.php');
require_once($CFG->libdir . '\tablelib.php');
require_once('criterionsedit_form.php');
require_once(dirname(dirname(dirname(__FILE__))) . '\model.php');
class criterions_page extends abstract_page {
    var $poasassignment;
    
    function criterions_page($cm, $poasassignment) {
        $this->poasassignment = $poasassignment;
        $this->cm = $cm;
    }
    
    function get_cap() {
        return 'mod/poasassignment:managecriterions';
    }
    
    function view() {
        global $DB, $OUTPUT;
        $id = $this->cm->id; 
        $poasmodel = poasassignment_model::get_instance($this->poasassignment);
        $mform = new criterionsedit_form(null, array('id' => $id, 'poasassignmentid' => $this->poasassignment->id));
        if($mform->get_data()) {
                $data = $mform->get_data();
                $poasmodel->save_criterion($data);
                redirect(new moodle_url('view.php', array('id' => $id, 'page' => 'criterions')), null, 0);
        }
        $mform->set_data($poasmodel->get_criterions_data());
        $mform->set_data(array('id' => $id));
        $mform->display();
    }
}