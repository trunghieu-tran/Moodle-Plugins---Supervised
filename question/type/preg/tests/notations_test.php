<?php

/**
 * Unit tests for question/type/preg/preg_notations.php.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/question.php');
require_once($CFG->dirroot . '/question/type/preg/preg_notations.php');


class qtype_preg_notations_test extends PHPUnit_Framework_TestCase {

    //Test conversion from native to PCRE strict notation.
    /*public function test_PCRE_from_native() {
        $notationobj = new qtype_preg_notation_native('()(?:)(?|)(?=)(?<=)(?!)(?<!)');
        $this->assertTrue($notationobj->convert_regex('pcrestrict') == '\(\)\(\?\:\)\(\?\|\)\(\?\=\)\(\?\<\=\)\(\?\!\)\(\?\<\!\)');
        $notationobj = new qtype_preg_notation_native('\(\)\(?:\)\(?|\)\(?=\)\(?<=\)\(?!\)\(?<!\)');
        $this->assertTrue($notationobj->convert_regex('pcrestrict') == '\(\)\(?:\)\(?|\)\(?=\)\(?<=\)\(?!\)\(?<!\)');
    }*/
}
