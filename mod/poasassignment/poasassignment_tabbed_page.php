<?php

/** Class of view.php page, displays list of tabs
 *  and content of current tab
 */
class poasassignment_tabbed_page {
    private $cm;
    private $poasassignment;
    private $pages;              // array of pages in poasassignment
    private $currentpage;        // name of current page
    private $context;

    /** Standard constuctor for poasassignment_tabbed_page
     * @param $pages array of pages to be displayed
     */
    function poasassignment_tabbed_page($pages) {
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

        require_login($course, true, $cm);

        // Add record to log
        add_to_log($course->id, 'poasassignment', 'view', "view.php?id=$cm->id&page=$page", $poasassignment->name, $cm->id);

        
        //$PAGE->navbar->add(get_string('view','poasassignment'));

        $this->pages = $pages;
        $this->currentpage=$page;
        $this->cm=$cm;
        $this->poasassignment=$poasassignment;
        $this->context=get_context_instance(CONTEXT_MODULE,$this->cm->id);
        
        $PAGE->set_url('/mod/poasassignment/view.php', array('id' => $cm->id,'page'=>$page));
        $PAGE->set_title($course->shortname.': '.get_string('modulename','poasassignment').': '.$this->poasassignment->name);
        $PAGE->set_heading($course->fullname);
        $PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'poasassignment')));

    }

    /** Method calls all view-methods in class
     */
    function view() {
        global $PAGE;

        require_capability('mod/poasassignment:view', $this->context);

        $PAGE->navbar->add(get_string($this->currentpage, 'poasassignment'));

        $this->view_header($this->currentpage);

        // Check available date or students
        if (time()<$this->poasassignment->availabledate && !has_capability('mod/poasassignment:managetasks', $this->context)) {
            echo get_string('thismoduleisntopenedyet', 'poasassignment');
        }
        else {
            // TODO: убрать метод view_pages
            //$this->view_pages($this->cm);
            $this->view_body();
        }
        $this->view_footer();        
    }

    /** Displays pages at the top of page
     */
    function view_pages() {
        global $CFG;
        $cm=$this->cm;
        // for all pages in $pages array
        for ($i = 0; $i < count($this->pages); $i++) {
            $pagei = $this->pages[$i];
            $pagetype = $pagei.'_page';
            require_once($pagetype.'.php');
            
            // If user has ability to view <pagename>_page - add page on panel
            $pageinstance = new $pagetype($cm, $this->poasassignment);
            if ($pageinstance->has_ability_to_view()) {
                $row[] = new pageobject($pagei, "$CFG->wwwroot/mod/poasassignment/view.php?id=$cm->id&page=".$pagei,get_string($pagei, 'poasassignment'));
            }
        }

        // Show pages panel
        $pages[] = $row;
        $inactive[] = $this->currentpage;
        $activated[] = $this->currentpage;
        if (count($row)>1)
            print_pages($pages, $this->currentpage, $inactive, $activated);
    }

    /** Displays general content of the page
     */
    function view_body() {
        $pagetype = $this->currentpage."_page";
        if(file_exists($pagetype.'.php')) {
            require_once($pagetype.'.php');
            $poasassignmentpage = new $pagetype($this->cm, $this->poasassignment);
            $poasassignmentpage->require_ability_to_view();
            $poasassignmentpage->view();
        }
        else {
            $currentpath = poasassignment_model::$extpages[$this->currentpage];
            require_once($currentpath);
            $poasassignmentpage = new $pagetype($this->cm, $this->poasassignment);
            $poasassignmentpage->require_ability_to_view();
            $poasassignmentpage->view();
        }
    }

    /** Dislpays header
     */
    function view_header() {
        global $OUTPUT;
        echo $OUTPUT->header();
        echo $OUTPUT->heading($this->poasassignment->name.' : '.
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