<?php

/**
 * Unit tests for (some of) question/type/preg/preg_notations.php.
 *
 * @copyright &copy; 2012 Oleg Sychev
 * @author Oleg Sychev
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/question.php');
require_once($CFG->dirroot . '/question/type/preg/preg_notations.php');


class qtype_preg_notations_test extends PHPUnit_Framework_TestCase {

    //Test conversion from native to PCRE strict notation.
    public function test_PCRE_from_native() {
        $notationobj = new qtype_preg_notation_native('()(?:)(?|)(?=)(?<=)(?!)(?<!)');
        $this->assertTrue($notationobj->convert_regex('pcrestrict') == '\(\)\(\?\:\)\(\?\|\)\(\?\=\)\(\?\<\=\)\(\?\!\)\(\?\<\!\)');
        $notationobj = new qtype_preg_notation_native('\(\)\(?:\)\(?|\)\(?=\)\(?<=\)\(?!\)\(?<!\)');
        $this->assertTrue($notationobj->convert_regex('pcrestrict') == '\(\)\(?:\)\(?|\)\(?=\)\(?<=\)\(?!\)\(?<!\)');
    }
}