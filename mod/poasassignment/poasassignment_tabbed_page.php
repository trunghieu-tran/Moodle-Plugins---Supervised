<?php

/** Class of view.php page, displays list of tabs
 *  and content of current tab
 */
class poasassignment_tabbed_page {
    var $cm;
    var $poasassignment;
    var $tabs;              // array of tabs in poasassignment
    var $currenttab;        // name of current tab
    var $context;

    /** Standard constuctor for poasassignment_tabbed_page
     * @param $tabs array of tabs to be displayed
     */
    function poasassignment_tabbed_page($tabs) {
        global $DB,$PAGE;
        $id = optional_param('id', 0, PARAM_INT);           // course_module ID, or
        $p  = optional_param('p', 0, PARAM_INT);            // poasassignment instance ID 
        $tab = optional_param('tab', 'view', PARAM_TEXT);     // set 'view' as default tab

        if ($id) {
            $cm         = get_coursemodule_from_id('poasassignment', $id, 0, false, MUST_EXIST);
            $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
            $poasassignment  = $DB->get_record('poasassignment', array('id' => $cm->instance), '*', MUST_EXIST);
        } elseif ($p) {
            $poasassignment  = $DB->get_record('poasassignment', array('id' => $p), '*', MUST_EXIST);
            $course     = $DB->get_record('course', array('id' => $poasassignment->course), '*', MUST_EXIST);
            $cm         = get_coursemodule_from_instance('poasassignment', $poasassignment->id, $course->id, false, MUST_EXIST);
        } else {
            error(get_string('errornoid','poasassignment'));
        }

        require_login($course, true, $cm);

        // Add record to log
        add_to_log($course->id, 'poasassignment', 'view', "view.php?id=$cm->id&tab=$tab", $poasassignment->name, $cm->id);

        
        //$PAGE->navbar->add(get_string('view','poasassignment'));

        $this->tabs = $tabs;
        $this->currenttab=$tab;
        $this->cm=$cm;
        $this->poasassignment=$poasassignment;
        $this->context=get_context_instance(CONTEXT_MODULE,$this->cm->id);
        
        $PAGE->set_url('/mod/poasassignment/view.php', array('id' => $cm->id,'tab'=>$tab));
        $PAGE->set_title($course->shortname.': '.get_string('modulename','poasassignment').': '.$this->poasassignment->name);
        $PAGE->set_heading($course->fullname);
        $PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'poasassignment')));

    }

    /** Method calls all view-methods in class
     */
    function view() {
        global $PAGE;

        require_capability('mod/poasassignment:view', $this->context);

        $PAGE->navbar->add(get_string($this->currenttab, 'poasassignment'));

        $this->view_header($this->currenttab);

        // Check available date or students
        if (time()<$this->poasassignment->availabledate && !has_capability('mod/poasassignment:managetasks', $this->context)) {
            echo get_string('thismoduleisntopenedyet', 'poasassignment');
        }
        else {
            $this->view_tabs($this->cm);
            $this->view_body();
        }
        $this->view_footer();        
    }

    /** Displays tabs at the top of page
     */
    function view_tabs() {
        global $CFG;
        $cm=$this->cm;
        // for all tabs in $tabs array
        for ($i=0;$i<count($this->tabs);$i++) {
            $tabi = $this->tabs[$i];
            $tabtype = $tabi.'_tab';
            require_once($tabtype.'.php');
            
            // If user has ability to view <tabname>_tab - add tab on panel
            $tabinstance = new $tabtype($cm, $this->poasassignment);
            if ($tabinstance->has_ability_to_view()) {
                $row[]=new tabobject($tabi, "$CFG->wwwroot/mod/poasassignment/view.php?id=$cm->id&tab=".$tabi,get_string($tabi, 'poasassignment'));
            }
        }

        // Show tabs panel
        $tabs[] = $row;
        $inactive[] = $this->currenttab;
        $activated[] = $this->currenttab;
        if (count($row)>1)
            print_tabs($tabs, $this->currenttab, $inactive, $activated);
    }

    /** Displays general content of the page
     */
    function view_body() {       
        $tabtype = $this->currenttab."_tab";
        require_once($tabtype.'.php');
        $poasassignmenttab = new $tabtype($this->cm, $this->poasassignment);
        $poasassignmenttab->require_ability_to_view();
        $poasassignmenttab->view();
    }

    /** Dislpays header
     */
    function view_header() {
        global $OUTPUT;
        echo $OUTPUT->header();
        echo $OUTPUT->heading($this->poasassignment->name.' : '.
                        get_string($this->currenttab, 'poasassignment').
                        $OUTPUT->help_icon($this->currenttab, 'poasassignment'));
    }

    /** Dislpays footer
     */
    function view_footer() {
        global $OUTPUT;
        echo $OUTPUT->footer();
    }
}