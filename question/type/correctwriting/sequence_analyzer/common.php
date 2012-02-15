<?php
/**
 * Defines an implementation for common functions to perform counting in sequences and is same
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
defined('MOODLE_INTERNAL') || die();
 

/**
 * Private implementation of is same function for tokens
 */
function qtype_correctwriting_sequence_analyzer_is_same($c1,$c2) {
    return $c1->is_same($c2);   
}

/**
 *  Returns a length of sequence
 */
function qtype_correctwriting_sequence_analyzer_count($sequence) {
    return count($sequence);
}   

?>