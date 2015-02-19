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

class block_formal_langs_test_compared_corrected extends UnitTestCase {
    // Test is buggy! Somebody fix it!
    function test_compared_corrected_1() {
        $correctarray = array(array(0,0),array(1,1),array(2,2));
        $string_pair = new block_formal_langs_string_pair();
        $pairs = new block_formal_langs_matches_group();
        $pairs->matchedpairs = array();
        $pair1 = new block_formal_langs_matched_tokens_pair(array(0), array(0), 0);
        $pair2 = new block_formal_langs_matched_tokens_pair(array(1), array(1), 0);
        $pair3 = new block_formal_langs_matched_tokens_pair(array(2), array(2), 0);
        array_push($pairs->matchedpairs, $pair1, $pair2, $pair3, $pair4, $pair5);
        $pairs->comparedcoverage=array(0,1,2);
        $pairs->correctcoverage=array(0,1,2);
        $string_pair->matches = array($pairs);
        $stream = new block_formal_langs_token_stream();
        $lexem1 = new block_formal_langs_token_base(null, 'type', 'you', null, 0);
        $lexem2 = new block_formal_langs_token_base(null, 'type', 'my', null, 1);
        $lexem3 = new block_formal_langs_token_base(null, 'type', 'fire', null, 2);
        $stream->tokens = array ($lexem1, $lexem2, $lexem3);
        $string_pair->comparedstring->stream = $stream;
        $this->assertTrue($correctarray==$string_pair->pairs_between_corrected_compared());
        
    }

    function test_compared_corrected_2() {
    }

    function test_compared_corrected_3() {
    }

    function test_compared_corrected_4() {
    }

    function test_compared_corrected_5() {
    }

    function test_compared_corrected_6() {
    }

}