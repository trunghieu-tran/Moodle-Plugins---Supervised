<?php
/**
 * Defines unit-tests for token_base
 *
 * For a complete info, see qtype_correctwriting_token_base
 *
 * @copyright &copy; 2012  
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package 
 */
global $CFG;
require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_base.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_simple_english.php');

class blocks_formal_langs_token_base_correct_mistakes_test extends UnitTestCase {
    /**
     * Language object
     * @var block_formal_langs_language_simple_english
     */
    protected $language = null;

    public function setUp() {
        $this->language = new block_formal_langs_language_simple_english();
    }

    function test_correct_mistakes_1() {
        $correctstring = $this->language->create_from_string('masha eat cake');
        $comparedstring = $this->language->create_from_string('dasha eat my cake');

        $pair11 = new block_formal_langs_matched_tokens_pair(array(0), array(0), 1);
        $pair12 = new block_formal_langs_matched_tokens_pair(array(1), array(1), 0);
        $pair13 = new block_formal_langs_matched_tokens_pair(array(2), array(3), 0);
        $group = array($pair11, $pair12, $pair13);
        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, $group);
        $res = $stringpair->correct_mistakes();
        $this->assertTrue(count($res->stream->tokens)==4);
    }

    function test_correct_mistakes_2() {
        $correctstring = $this->language->create_from_string('masha eat cake');
        $comparedstring = $this->language->create_from_string('dasha eat');
        $pair11 = new block_formal_langs_matched_tokens_pair(array(0), array(0), 1);
        $pair12 = new block_formal_langs_matched_tokens_pair(array(1), array(1), 0);
        $group = array($pair11, $pair12);
        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, $group);
        $res = $stringpair->correct_mistakes();
        $this->assertTrue(count($res->stream->tokens)==2);
    }


    function test_correct_mistakes_3() {
        $correctstring = $this->language->create_from_string('mashaeat cake');
        $comparedstring = $this->language->create_from_string('masha eat');
        $pair11=new block_formal_langs_matched_tokens_pair(array(0), array(0, 1), 1);
        $group = array($pair11);
        $stringpair = new block_formal_langs_string_pair($correctstring, $comparedstring, $group);
        $res = $stringpair->correct_mistakes();
        $this->assertTrue(count($res->stream->tokens) == 2);
    }

}