<?php
global $CFG;
require_once('abstract_page.php');
require_once('model.php');
class graders_page extends abstract_page {
    var $poasassignment;
    
    function criterions_page($cm,$poasassignment) {
        $this->poasassignment = $poasassignment;
        $this->cm=$cm;
    }
    
    function get_cap() {
        return 'mod/poasassignment:grade';
    }
    
    function view() {
        global $DB,$OUTPUT;
        echo '123';
    }
    
}