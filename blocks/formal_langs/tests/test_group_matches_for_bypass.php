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

class blocks_formal_langs_token_base_group_matches_test extends UnitTestCase {

    function test_group_matches_1() {
        $mistakeweight = 1;
        $correctcoverage = array(0, 1);
        $comparedcoverage = array(0, 1);
        // sasha and masha
        // sasha and dasha
        $token_stream = new block_formal_langs_token_stream(array(), array());
        $pair1 = new block_formal_langs_matched_tokens_pair(array(0), array(0), 0);
        $pair3 = new block_formal_langs_matched_tokens_pair(array(1), array(1), 0);
        $matches = array();
        array_push($matches, $pair1,  $pair3);
        $this->assertTrue(count($token_stream->group_matches_for_bypass($matches))==1);
        list($result) = $token_stream->group_matches_for_bypass($matches);
        $this->assertTrue($result->mistakeweight==0);
        $this->assertTrue($result->correctcoverage==$correctcoverage);
        $this->assertTrue($result->comparedcoverage==$comparedcoverage);
    }

    function test_group_matches_2() {
        $mistakeweight = 0;
        $correctcoverage = array();
        $comparedcoverage = array();
        // sasha and masha
        // you name mike
        $token_stream = new block_formal_langs_token_stream(array(), array());
        $matches = array();

        $this->assertTrue(count($token_stream->group_matches_for_bypass($matches)) == 0);
    }

    function test_group_matches_3() {
        // my life
        // my live my
        $pair1 = new block_formal_langs_matched_tokens_pair(array(0), array(0), 0);
        $pair2 = new block_formal_langs_matched_tokens_pair(array(0), array(2), 0);
        
        $matches = array();
        array_push($matches,$pair1, $pair2);
        $token_stream=new block_formal_langs_token_stream(array(), array());
        $sets_of_pairs=array();
        $set_of_pairs1=new block_formal_langs_matches_group();
        $set_of_pairs1->mistakeweight=0;
        $set_of_pairs1->correctcoverage=array(0);
        $set_of_pairs1->comparedcoverage=array(0);
        $set_of_pairs2=new block_formal_langs_matches_group();
        $set_of_pairs2->mistakeweight=0;
        $set_of_pairs2->correctcoverage=array(0);
        $set_of_pairs2->comparedcoverage=array(2);
        array_push($sets_of_pairs, $set_of_pairs1);
        array_push($sets_of_pairs, $set_of_pairs2);

        $groups = $token_stream->group_matches_for_bypass($matches);
        $this->assertTrue(count($groups)==1);
        /** @var block_formal_langs_matches_group $group */
        $group = $groups[0];
        $this->assertTrue(count($group->matchedpairs) == 1);
    }
    
    function test_group_matches_4() {
        // family milk family milk
        // family milk tonus
        $pair1 = new block_formal_langs_matched_tokens_pair(array(0), array(0), 0);
        $pair2 = new block_formal_langs_matched_tokens_pair(array(2), array(0), 0);
        $pair3 = new block_formal_langs_matched_tokens_pair(array(1), array(1), 0);
        $pair4 = new block_formal_langs_matched_tokens_pair(array(3), array(1), 0);
        // 4 groups
        $matches = array();
        array_push($matches,$pair1, $pair2, $pair3, $pair4);
        $token_stream=new block_formal_langs_token_stream(array(), array());
        $sets_of_pairs=array();
        $set_of_pairs1=new block_formal_langs_matches_group();
        $set_of_pairs1->mistakeweight=0;
        $set_of_pairs1->correctcoverage=array(0, 1);
        $set_of_pairs1->comparedcoverage=array(0, 1);
        $set_of_pairs2=new block_formal_langs_matches_group();
        $set_of_pairs2->mistakeweight=0;
        $set_of_pairs2->correctcoverage=array(0, 3);
        $set_of_pairs2->comparedcoverage=array(0, 1);
        $set_of_pairs3=new block_formal_langs_matches_group();
        $set_of_pairs3->mistakeweight=0;
        $set_of_pairs3->correctcoverage=array(1, 2);
        $set_of_pairs3->comparedcoverage=array(0, 1);
        $set_of_pairs4=new block_formal_langs_matches_group();
        $set_of_pairs4->mistakeweight=0;
        $set_of_pairs4->correctcoverage=array(2, 3);
        $set_of_pairs4->comparedcoverage=array(0, 1);
        array_push($sets_of_pairs, $set_of_pairs1);
        array_push($sets_of_pairs, $set_of_pairs2);
        array_push($sets_of_pairs, $set_of_pairs3);
        array_push($sets_of_pairs, $set_of_pairs4);

        $groups = $token_stream->group_matches_for_bypass($matches);
        $this->assertTrue(count($groups)==1);
        /** @var block_formal_langs_matches_group $group */
        $group = $groups[0];
        $this->assertTrue(count($group->matchedpairs) == 2);
    }
}