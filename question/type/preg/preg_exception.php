<?php

/**
 * Defines Preg exception class
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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
 * A special class, throwed by finite automata, should be catched to generate qtype_preg_too_complex_error object by the code building automata.
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
