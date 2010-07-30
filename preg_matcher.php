<?php
class preg_matcher {
    function name() {
        return 'preg_matcher';
    }
    function preprocess($regex) {
        echo 'Error: preprocess has not been implemented for', $this->name(), 'class';
    }
    function get_result($response) {
        echo 'Error: geting result has not been implemented for', $this->name(), 'class';
    }
    function get_index() {
        echo 'Error: geting index has not been implemented for', $this->name(), 'class';
    }
    function get_full() {
        echo 'Error: getting fullness has not been implemented for', $this->name(), 'class';
    }
    function get_next_char() {
        echo 'Error: getting next character has not been implemented for', $this->name(), 'class';
    }
    function validate() {
        echo 'Error: validation has not been implemented for', $this->name(), 'class';
    }
    static function list_of_supported_operation() {
        echo 'Error: list of supported operation has not been implemented for', $this->name(), 'class';
    }
}
?>