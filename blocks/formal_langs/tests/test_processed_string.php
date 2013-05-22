<?php
/**
 * Defines unit-tests for processed_string class
 *
 * For a complete info, see block_formal_langs_processed_string
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

global $CFG;
require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');

class block_formal_langs_processed_string_tests extends PHPUnit_Framework_TestCase {
    //Tests for processed_string single_line_string function.
    public function test_single_line_string() {
        $string = new block_formal_langs_processed_string(null);
        $string->string = 'abc';
        $this->assertTrue($string->single_line_string());
        $string->string = "ab\ncd";
        $this->assertFalse($string->single_line_string());
        $string->string = "абвг\nде";
        $this->assertFalse($string->single_line_string());
    }
}
 ?>