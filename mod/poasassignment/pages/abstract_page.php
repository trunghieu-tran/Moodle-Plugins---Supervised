<?php

/** 
 *
 *
 */
class abstract_page {
    var $cm;
    var $lasterror;
    function abstract_page($cm) {
        $this->cm=$cm;
    }

    /** Getter of page capability
     * @return capability 
     */
    function get_cap() {
        return 'mod/poasassignment:view';
    }

    /** Checks module settings that prohibit viewing this page, used in has_ability_to_view
     * @return true if neither setting prohibits
     */
    function has_satisfying_parameters() {
        return true;
    }
    
    /** Requires settings and capabilities to view
     */
    function require_ability_to_view() {
        if(!$this->has_satisfying_parameters())
            print_error($this->lasterror,'poasassignment');
        $this->require_cap();
    }

    /** Checks settings and capabilities to view
     * @return true if nothing prohibits
     */
    function has_ability_to_view() {
        if(!$this->has_satisfying_parameters())
            return false;
        return $this->has_cap();
    }
    
    /** Checks capabilities to view, used in has_ability_to_view
     * @return true if has capability to view
     */
    function has_cap() {
        return has_capability($this->get_cap(),get_context_instance(CONTEXT_MODULE,$this->cm->id));
    }

    /** Requires capabilities to view, used in has_ability_to_view
     */
    function require_cap() {
        return require_capability($this->get_cap(),get_context_instance(CONTEXT_MODULE,$this->cm->id));
    }
    function view() {
    }
    
}