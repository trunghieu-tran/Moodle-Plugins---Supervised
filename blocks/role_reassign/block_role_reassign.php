<?php
// get_string
// логи
// B/R
require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
require_once($CFG->libdir.'/eventslib.php');
class block_role_reassign extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_role_reassign');
    }
    function get_content() {
    if ($this->content !== NULL) {
        return $this->content;
    }
    $this->content = new stdClass;
    $this->content->text   = $this->config->text;
    $this->content->footer='';
    return $this->content;
    }
        function instance_allow_config() {
            return false;
    }
    function specialization() {
        global $DB, $CFG;
        if ( has_capability('block/role_reassign:view', $this->context)) {
            if (!empty($this->config->title)) {
            $this->title = $this->config->title;
            } else {
                $this->config->title = get_string('title', 'block_role_reassign');
            }
            if (empty($this->config->text)) {
                $this->config->text='<a href='.$CFG->wwwroot.'/blocks/role_reassign/status.php?courseid='.$this->page->course->id.'>'.get_string('manage_rules', 'block_role_reassign').'</a>';
            }  
        } else {
            $this->config->text = get_string('no_capability', 'block_role_reassign');
        }
    }
}