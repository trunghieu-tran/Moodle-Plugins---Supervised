<?php
/**
 * Defines preg exception class
 *
 * @copyright &copy; 2010  Oleg Sychev & Kolesov Dmitriy 
 * @author Oleg Sychev & Kolesov Dmitriy, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */


/**
 * Preg questiontype exception class
 */
class qtype_preg_exception extends moodle_exception {
    /**
     * @param string $errorcode
     * @param string $a
     * @param string $debuginfo
     */
    function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}

?>