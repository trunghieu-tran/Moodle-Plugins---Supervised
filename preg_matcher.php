<?php
/**
 * Defines abstract class of matcher, extend it for get matcher
 *
 * @copyright &copy; 2010  Kolesov Dmitriy 
 * @author Kolesov Dmitriy, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
class preg_matcher {
    function name() {
        return 'preg_matcher';
    }
    function preprocess($regex) {
        echo 'Error: preprocess has not been implemented for', $this->name(), 'class';
    }
    function match($response) {
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
    static function validate($regex) {
        echo 'Error: validation has not been implemented for', $this->name(), 'class';
    }
    static function list_of_supported_operations_and_operands() {
        echo 'Error: list of supported operation has not been implemented for', $this->name(), 'class';
    }
}
?>