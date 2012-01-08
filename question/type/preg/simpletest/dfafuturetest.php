<?php  // $Id: testquestiontype.php,v 0.1 beta 2010/08/10 21:40:20 dvkolesov Exp $
/**
 * Unit tests for (some of) question/type/preg/dfa_preg_matcher.php.
 *
 * @copyright &copy; 2010 Dmitriy Kolesov
 * @author Dmitriy Kolesov
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/dfa_preg_matcher/dfa_preg_matcher.php');
//see carefully commented example of test on lines 617-644
class qtype_preg_dfa_future_test extends UnitTestCase {
    var $qtype;
    
    function setUp() {
    }
    
    function tearDown() {
    }

    function test_assert_nesting() {
        $matcher = new qtype_dfa_preg_matcher('g(?=[bcd]*(?=[cd]*b)a)[abcd]*');
        $matcher->match('gccbadcdcd');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index()==10);
        $this->assertTrue($matcher->next_char()=='');
        $matcher->match('gccabdcdcd');
        $this->assertTrue($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index()==10);
        $this->assertTrue($matcher->next_char()=='');
        $matcher->match('gccaddcdcd');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index()==10);
        $this->assertTrue($matcher->next_char()=='b');
    }
    function test_next_character() {
        $matcher = new qtype_dfa_preg_matcher('a(?=[%asd])\W');
        $matcher->match('aa');
        $this->assertFalse($matcher->is_matching_complete());
        $this->assertTrue($matcher->last_correct_character_index()==0);
        $this->assertTrue($matcher->next_char()=='%');
    }
}
?>