<?php

class poasassignment_tabbed_page {
    var $cm;
    var $poasassignment;
    var $tabs;
    var $currenttab;
    var $context;
    function poasassignment_tabbed_page($tabs) {
        global $DB,$PAGE;
        $id = optional_param('id', 0, PARAM_INT);           // course_module ID, or
        $p  = optional_param('p', 0, PARAM_INT);            // poasassignment instance ID 
        $tab = optional_param('tab','view',PARAM_TEXT);

        if ($id) {
            $cm         = get_coursemodule_from_id('poasassignment', $id, 0, false, MUST_EXIST);
            $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
            $poasassignment  = $DB->get_record('poasassignment', array('id' => $cm->instance), '*', MUST_EXIST);
        } elseif ($p) {
            $poasassignment  = $DB->get_record('poasassignment', array('id' => $p), '*', MUST_EXIST);
            $course     = $DB->get_record('course', array('id' => $poasassignment->course), '*', MUST_EXIST);
            $cm         = get_coursemodule_from_instance('poasassignment', $poasassignment->id, $course->id, false, MUST_EXIST);
        } else {
            error('You must specify a course_module ID or an instance ID');
        }

        require_login($course, true, $cm);

        add_to_log($course->id, 'poasassignment', 'view', "view.php?id=$cm->id&tab=$tab", $poasassignment->name, $cm->id);

        /// Print the page header

        $PAGE->set_url('/mod/poasassignment/view.php', array('id' => $cm->id,'tab'=>$tab));
        $PAGE->set_title(get_string('modulename','poasassignment').':'.$poasassignment->name);
        $PAGE->set_heading($course->fullname);
        $PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'poasassignment')));
      
        $this->tabs = $tabs;
        $this->currenttab=$tab;
        $this->cm=$cm;
        $this->poasassignment=$poasassignment;
        $this->context=get_context_instance(CONTEXT_MODULE,$this->cm->id);
    }
    
    function view() {
        require_capability('mod/poasassignment:view',$this->context);
        
        $this->view_header($this->currenttab);
        if(time()<$this->poasassignment->availabledate && !has_capability('mod/poasassignment:managetasks',$this->context)) {
            echo get_string('thismoduleisntopenedyet','poasassignment');
        }
        else {
            $this->view_tabs($this->cm);
            $this->view_body();
        }
        $this->view_footer();
        
    }
    
    function view_tabs() {
        global $CFG;
        $cm=$this->cm;
        for ($i=0;$i<count($this->tabs);$i++) {
            $tabi = $this->tabs[$i];
            $tabtype = $tabi.'_tab';
            require_once($tabtype.'.php');
            $tabinstance = new $tabtype($cm,$this->poasassignment);
            if($tabinstance->has_ability_to_view()) {
                $row[]=new tabobject($tabi,"$CFG->wwwroot/mod/poasassignment/view.php?id=$cm->id&tab=".$tabi,get_string($tabi,'poasassignment'));
            }
        }
        
        $tabs[] = $row;
        $inactive[] = $this->currenttab;
        $activated[] = $this->currenttab;
        if(count($row)>1)
            print_tabs($tabs, $this->currenttab, $inactive, $activated);
    }
    
    function view_body() {       
        $tabtype = $this->currenttab."_tab";
        require_once($tabtype.'.php');
        $poasassignmenttab = new $tabtype($this->cm,$this->poasassignment);
        $poasassignmenttab->require_ability_to_view();
        $poasassignmenttab->view();
    }
    
    function view_header() {
        global $OUTPUT;
        echo $OUTPUT->header();
        echo $OUTPUT->heading($this->poasassignment->name.' : '.
                        get_string($this->currenttab,'poasassignment').
                        $OUTPUT->help_icon($this->currenttab,'poasassignment'));
    }
    
    function view_footer() {
        global $OUTPUT;
        echo $OUTPUT->footer();
    }
}