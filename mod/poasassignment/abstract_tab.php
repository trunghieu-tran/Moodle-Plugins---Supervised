<?php

class abstract_tab {
    var $cm;
    var $lasterror;
    function abstract_tab($cm) {
        $this->cm=$cm;
    }
    function get_cap() {
        return 'mod/poasassignment:view';
    }
    function has_satisfying_parameters() {
        return true;
    }
    function require_ability_to_view() {
        if(!$this->has_satisfying_parameters())
            print_error($this->lasterror,'poasassignment');
        $this->require_cap();
    }
    function has_ability_to_view() {
        if(!$this->has_satisfying_parameters())
            return false;
        return $this->has_cap();
    }
    function has_cap() {
        return has_capability($this->get_cap(),get_context_instance(CONTEXT_MODULE,$this->cm->id));
    }
    function require_cap() {
        return require_capability($this->get_cap(),get_context_instance(CONTEXT_MODULE,$this->cm->id));
    }
    function view() {
    }
    
}