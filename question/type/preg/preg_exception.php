<?php
/**
 * Defines preg exception class
 *
 * @copyright &copy; 2010  Oleg Sychev
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */


/**
 * Preg questiontype exception class.
 */
class qtype_preg_exception extends moodle_exception {
    /**
     * @param string $errorcode
     * @param object $a
     * @param string $debuginfo
     */
    function __construct($errorcode, $a = NULL, $debuginfo = null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}

/**
 * A special class, throwed by finite automaton, Should be catched to generate qtype_preg_too_complex_error object by the code, building automaton
 * No actual info needed since it would be filled by catching code.
 */
class qtype_preg_toolargefa_exception extends qtype_preg_exception {
    /**
     * @param string $errorcode
     * @param object $a
     * @param string $debuginfo
     */
    function __construct($errorcode, $a = NULL, $debuginfo = null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
