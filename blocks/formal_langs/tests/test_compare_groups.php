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

class block_formal_langs_token_stream_test extends UnitTestCase {
    function test_compare_matches_groups_1() {
        $arr = new block_formal_langs_token_stream(array(), array());
        $group11 = new block_formal_langs_matches_group();
        $group12 = new block_formal_langs_matches_group();
        $pair11 = new block_formal_langs_matched_tokens_pair(array(0), array(0), 0);
        $pair12 = new block_formal_langs_matched_tokens_pair(array(1), array(1), 1);
        $pair13 = new block_formal_langs_matched_tokens_pair(array(0), array(1), 1);
        $pair14 = new block_formal_langs_matched_tokens_pair(array(1), array(0), 0);
        $group11->matchedpairs=array($pair11, $pair12);
        $group12->matchedpairs=array($pair13, $pair14);
        $group11->mistakeweight=1;
        $group12->mistakeweight=1;
        $group11->correctcoverage=array(0, 1);
        $group11->comparedcoverage=array(0, 1);
        $group12->correctcoverage=array(0, 1);
        $group12->comparedcoverage=array(0, 1);
        $this->assertTrue($arr->compare_matches_groups($group11, $group12)==0, '$group1==$group2');
    }
    function test_compare_matches_groups_2() {
        $arr = new block_formal_langs_token_stream(array(), array());
        $group21=new block_formal_langs_matches_group();
        $group22=new block_formal_langs_matches_group();
        $pair21=new block_formal_langs_matched_tokens_pair(array(0), array(0), 2);
        $pair22=new block_formal_langs_matched_tokens_pair(array(1), array(1), 2);
        $pair23=new block_formal_langs_matched_tokens_pair(array(0), array(1), 1);
        $pair24=new block_formal_langs_matched_tokens_pair(array(1), array(0), 1);
        $group21->matchedpairs=array($pair21, $pair22);
        $group22->matchedpairs=array($pair23, $pair24);
        $group21->mistakeweight=4;
        $group22->mistakeweight=2;
        $group21->correctcoverage=array(0, 1);
        $group21->comparedcoverage=array(0, 1);
        $group22->correctcoverage=array(0, 1);
        $group22->comparedcoverage=array(0, 1);
        $this->assertTrue($arr->compare_matches_groups($group21, $group22)<0, '$group1 worse than $group2');
    }
    function test_compare_matches_groups_3() {
        $arr = new block_formal_langs_token_stream(array(), array());
        $group31=new block_formal_langs_matches_group();
        $group32=new block_formal_langs_matches_group();
        $pair31=new block_formal_langs_matched_tokens_pair(array(0), array(0), 0);
        $pair32=new block_formal_langs_matched_tokens_pair(array(1), array(1), 0);
        $pair33=new block_formal_langs_matched_tokens_pair(array(0), array(1), 2);
        $pair34=new block_formal_langs_matched_tokens_pair(array(1), array(0), 1);
        $group31->matchedpairs=array($pair31, $pair32);
        $group32->matchedpairs=array($pair33, $pair34);
        $group31->mistakeweight=0;
        $group32->mistakeweight=3;
        $group31->correctcoverage=array(0, 1);
        $group31->comparedcoverage=array(0, 1);
        $group32->correctcoverage=array(0, 1);
        $group32->comparedcoverage=array(0, 1);
        $this->assertTrue($arr->compare_matches_groups($group31, $group32)>0, '$group1 better than $group2');
    }
    function test_compare_matches_groups_4() {
        $arr = new block_formal_langs_token_stream(array(), array());
        $group41=new block_formal_langs_matches_group();
        $group42=new block_formal_langs_matches_group();
        $pair41=new block_formal_langs_matched_tokens_pair(array(0), array(1), 0);
        $pair42=new block_formal_langs_matched_tokens_pair(array(1), array(2), 0);
        $pair43=new block_formal_langs_matched_tokens_pair(array(0), array(1), 0);
        $pair44=new block_formal_langs_matched_tokens_pair(array(1, 2), array(0), 1);
        $group41->matchedpairs=array($pair41, $pair42);
        $group42->matchedpairs=array($pair43, $pair44);
        $group41->mistakeweight=0;
        $group42->mistakeweight=1;
        $group41->correctcoverage=array(0, 1);
        $group41->comparedcoverage=array(1, 2);
        $group42->correctcoverage=array(0, 1, 2);
        $group42->comparedcoverage=array(0, 1);
        $this->assertTrue($arr->compare_matches_groups($group41, $group42)<0, '$group1 worse than $group2');
    }
    function test_compare_matches_groups_5() {
        $arr = new block_formal_langs_token_stream(array(), array());
        $group51=new block_formal_langs_matches_group();
        $group52=new block_formal_langs_matches_group();
        $pair51=new block_formal_langs_matched_tokens_pair(array(0), array(1), 0);
        $pair52=new block_formal_langs_matched_tokens_pair(array(1), array(2), 0);
        $pair53=new block_formal_langs_matched_tokens_pair(array(0), array(0), 0);
        $pair54=new block_formal_langs_matched_tokens_pair(array(1), array(1, 2), 1);
        $group51->matchedpairs=array($pair51, $pair52);
        $group52->matchedpairs=array($pair53, $pair54);
        $group51->mistakeweight=0;
        $group52->mistakeweight=1;
        $group51->correctcoverage=array(0, 1);
        $group51->comparedcoverage=array(1, 2);
        $group52->correctcoverage=array(0, 1);
        $group52->comparedcoverage=array(0, 1, 2);
        $this->assertTrue($arr->compare_matches_groups($group51, $group52)<0, '$group1 worse than $group2');
    }
}


?>