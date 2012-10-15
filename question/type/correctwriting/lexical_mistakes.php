<?php
/**
 * Defines an implementation of mistakes, that are determined by lexical analyzer
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author  Oleg Sychev, Dmitriy Mamontov,Birukova Maria Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
defined('MOODLE_INTERNAL') || die();
 
require_once($CFG->dirroot.'/question/type/correctwriting/response_mistakes.php');

// A marker class to indicate errors from lexical analyzer. We need them to indicate
// what lexemes was corrected by analyzer.
class qtype_correctwriting_lexical_mistake extends qtype_correctwriting_response_mistake {
    // An array of corrected response lexemes
    public $correctedresponse;
    // An array of fixed lexemes indexes
    public $correctedresponseindex;
}


?>