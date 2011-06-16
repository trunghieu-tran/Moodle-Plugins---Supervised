<?php
require_once('model.php');
/** Class of view.php page, displays list of pages
 *  and content of current page
 */
class poasassignment_tabbed_page {
    private $currentpage;        // name of current page

    /** Standard constuctor for poasassignment_tabbed_page
     * @param $pages array of pages to be displayed
     */
    function poasassignment_tabbed_page() {
        global $DB,$PAGE;
        $id = optional_param('id', 0, PARAM_INT);           // course_module ID, or
        $p  = optional_param('p', 0, PARAM_INT);            // poasassignment instance ID 
        $page = optional_param('page', 'view', PARAM_TEXT);     // set 'view' as default page

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
        
        poasassignment_model::get_instance()->cash_instance($poasassignment->id);

        require_login($course, true, $cm);

        // Add record to log
        add_to_log($course->id, 'poasassignment', 'view', "view.php?id=$cm->id&page=$page", $poasassignment->name, $cm->id);

        $this->currentpage = $page;
        
        $PAGE->set_url('/mod/poasassignment/view.php', array('id' => $cm->id,'page'=>$page));
        $PAGE->set_title($course->shortname.': '.get_string('modulmodulename','poasassignment').': '.$poasassignment->name);
        $PAGE->set_heading($course->fullname);
        $PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'poasassignment')));

    }

    /** Method calls all view-methods in class
     */
    function view() {
        global $PAGE;

        require_capability('mod/poasassignment:view', poasassignment_model::get_instance()->get_context());

        $PAGE->navbar->add(get_string($this->currentpage, 'poasassignment'));

        $this->view_header($this->currentpage);

        // Check available date or students
        if (time() < poasassignment_model::get_instance()->get_poasassignment()->availabledate 
            && !has_capability('mod/poasassignment:managetasks', poasassignment_model::get_instance()->get_context())) {
            
            echo get_string('thismoduleisntopenedyet', 'poasassignment');
        }
        else {
            $this->view_body();
        }
        $this->view_footer();        
    }

    /** Displays general content of the page
     */
    function view_body() {
        $pagetype = $this->currentpage."_page";
        $currentpath = poasassignment_model::$extpages[$this->currentpage];
        require_once($currentpath);
        $poasassignmentpage = new $pagetype(poasassignment_model::get_instance()->get_cm(), 
                                            poasassignment_model::get_instance()->get_poasassignment());
        $poasassignmentpage->require_ability_to_view();
        $poasassignmentpage->view();
    }

    /** Dislpays header
     */
    function view_header() {
        global $OUTPUT;
        echo $OUTPUT->header();
        echo $OUTPUT->heading(poasassignment_model::get_instance()->get_poasassignment()->name.' : '.
                        get_string($this->currentpage, 'poasassignment').
                        $OUTPUT->help_icon($this->currentpage, 'poasassignment'));
    }

    /** Dislpays footer
     */
    function view_footer() {
        global $OUTPUT;
        echo $OUTPUT->footer();
    }

    function view_navigation() {
        
    }
}