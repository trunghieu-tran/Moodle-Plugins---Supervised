<?php

/**
 * Unit tests for DFA matcher methods.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Dmitriy Kolesov <xapuyc7@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/dfa_matcher/dfa_matcher.php');

class qtype_preg_dfa_matcher_test extends PHPUnit_Framework_TestCase {
    var $qtype;

    function setUp() {
        $this->qtype = new qtype_preg_dfa_matcher();
    }

    function tearDown() {
        $this->qtype = null;
    }

    function test_name() {
        $this->assertEquals($this->qtype->name(), 'dfa_matcher');
    }
    //Unit test for nullable function
    function test_nullable_leaf() {
        $this->qtype = new qtype_preg_dfa_matcher('a');
        $this->assertFalse($this->qtype->roots[0]->operands[0]->nullable());
    }
    function test_nullable_leaf_iteration_node() {
        $this->qtype = new qtype_preg_dfa_matcher('a*');
        $this->assertTrue($this->qtype->roots[0]->operands[0]->nullable());
    }
    function test_nullable_leaf_concatenation_node() {
        $this->qtype = new qtype_preg_dfa_matcher('ab');
        $this->assertFalse($this->qtype->roots[0]->operands[0]->nullable());
    }
    function test_nullable_leaf_alternation_node() {
        $this->qtype = new qtype_preg_dfa_matcher('a|b');
        $this->assertFalse($this->qtype->roots[0]->operands[0]->nullable());
    }
    function test_nullable_node_concatenation_node() {
        $this->qtype = new qtype_preg_dfa_matcher('a*bc');
        $this->assertFalse($this->qtype->roots[0]->operands[0]->nullable());
    }
    function test_nullable_node_alternation_node() {
        $this->qtype = new qtype_preg_dfa_matcher('a*|bc');
        $this->assertTrue($this->qtype->roots[0]->operands[0]->nullable());
    }
    function test_nullable_third_level_node() {
        $this->qtype = new qtype_preg_dfa_matcher('(?:(?:a|b)|c*)|d*');
        $this->assertTrue($this->qtype->roots[0]->operands[0]->nullable());
    }
    function test_nullable_question_quantificator() {
        $this->qtype = new qtype_preg_dfa_matcher('a?');
        $this->assertTrue($this->qtype->roots[0]->operands[0]->nullable());
    }
    function test_nullable_negative_character_class() {
        $this->qtype = new qtype_preg_dfa_matcher('[^a]');
        $this->assertFalse($this->qtype->roots[0]->operands[0]->nullable());
    }
    /*function test_nullable_assert() {
        $this->qtype = new qtype_preg_dfa_matcher('a(?=.*b)[xcvbnm]*');
        $this->assertFalse($this->qtype->roots[0]->operands[0]->operands[0]->operands[1]->nullable());
    }*/
    //Unit test for firstpos function
    function test_firstpos_leaf() {
        $this->qtype = new qtype_preg_dfa_matcher('a');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->firstpos();
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_firstpos_leaf_concatenation_node() {
        $this->qtype = new qtype_preg_dfa_matcher('ab');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable;
        $result = $this->qtype->roots[0]->operands[0]->firstpos();
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_firstpos_leaf_concatenation_node_with_end() {
        $this->qtype = new qtype_preg_dfa_matcher('ab');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->nullable;
        $result = $this->qtype->roots[0]->firstpos();
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_firstpos_leaf_alternation_node() {
        $this->qtype = new qtype_preg_dfa_matcher('a|b');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->firstpos();
        $this->assertTrue(count($result) == 2 && $result[0] == 1 && $result[1] == 2);
    }
    function test_firstpos_nullable_alternation() {
        $this->qtype = new qtype_preg_dfa_matcher('(?:a|)b');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->firstpos();
        $this->assertTrue(count($result) == 2);
        $this->assertTrue($result[0] == 1);
        $this->assertTrue($result[1] == 2);
    }
    function test_firstpos_three_leaf_alternation() {
        $this->qtype = new qtype_preg_dfa_matcher('a|b|c');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->firstpos();
        $this->assertTrue(count($result) == 3 && $result[0] == 1 && $result[1] == 2 && $result[2] == 3);
    }
    function test_firstpos_leaf_iteration_node() {
        $this->qtype = new qtype_preg_dfa_matcher('a*');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->firstpos();
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_firstpos_node_concatenation_node() {
        $this->qtype = new qtype_preg_dfa_matcher('c*(?:a|b)');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->firstpos();
        $this->assertTrue(count($result) == 3);
        $this->assertTrue($result[0] == 1);
        $this->assertTrue($result[1] == 2);
        $this->assertTrue($result[2] == 3);
    }
    function test_firstpos_node_alternation_node() {
        $this->qtype = new qtype_preg_dfa_matcher('a|b|c*');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->firstpos();
        $this->assertTrue(count($result) == 3 && $result[0] == 1 && $result[1] == 2 && $result[2] == 3);
    }
    function test_firstpos_node_iteration_node() {
        $this->qtype = new qtype_preg_dfa_matcher('(?:a*)*');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->firstpos();
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_firstpos_question_quantificator() {
        $this->qtype = new qtype_preg_dfa_matcher('a?');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->firstpos();
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_firstpos_negative_character_class() {
        $this->qtype = new qtype_preg_dfa_matcher('[^a]b');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $this->qtype->roots[0]->operands[0]->firstpos();
        $this->assertTrue(count($this->qtype->roots[0]->operands[0]->firstpos) == 1 && $this->qtype->roots[0]->operands[0]->firstpos[0] == 1);
        $this->assertTrue(count($this->qtype->roots[0]->operands[0]->operands[0]->firstpos) == 1 && $this->qtype->roots[0]->operands[0]->operands[0]->firstpos[0] == 1);
    }
    /*function test_firstpos_assert() {
        $this->qtype = new qtype_preg_dfa_matcher('a(?=.*b)[xcvbnm]*');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $this->qtype->roots[0]->operands[0]->firstpos();
        $this->assertTrue(count($this->qtype->roots[0]->operands[0]->operands[0]->operands[1]->firstpos) == 1 &&
                            $this->qtype->roots[0]->operands[0]->operands[0]->operands[1]->firstpos[0]>dfa_preg_node_assert::ASSERT_MIN_NUM);
    }*/
    //Unit test for lastpos function
    function test_lastpos_leaf() {
        $this->qtype = new qtype_preg_dfa_matcher('a');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->lastpos();
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_lastpos_leaf_concatenation_node() {
        $this->qtype = new qtype_preg_dfa_matcher('ab');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->lastpos();
        $this->assertTrue(count($result) == 1 && $result[0] == 2);
    }
    function test_lastpos_leaf_concatenation_node_with_end() {
        $this->qtype = new qtype_preg_dfa_matcher('ab');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->nullable();
        $result = $this->qtype->roots[0]->lastpos();
        $this->assertTrue(count($result) == 1 && $result[0] == 186759556);
    }
    function test_lastpos_leaf_alternation_node() {
        $this->qtype = new qtype_preg_dfa_matcher('a|b');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->lastpos();
        $this->assertTrue(count($result) == 2 && $result[0] == 1 && $result[1] == 2);
    }
    function test_lastpos_leaf_iteration_node() {
        $this->qtype = new qtype_preg_dfa_matcher('a*');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->lastpos();
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_lastpos_node_concatenation_node() {
        $this->qtype = new qtype_preg_dfa_matcher('(?:a|b)c*');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->lastpos();
        $this->assertTrue(count($result) == 3);
        $this->assertTrue($result[0] == 3);
        $this->assertTrue($result[1] == 1);
        $this->assertTrue($result[2] == 2);
    }
    function test_lastpos_node_alternation_node() {
        $this->qtype = new qtype_preg_dfa_matcher('a|b|c*');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->lastpos();
        $this->assertTrue(count($result) == 3 && $result[0] == 1 && $result[1] == 2 && $result[2] == 3);
    }
    function test_lastpos_node_iteration_node() {
        $this->qtype = new qtype_preg_dfa_matcher('(?:a*)*');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->lastpos();
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_lastpos_question_quantificator() {
        $this->qtype = new qtype_preg_dfa_matcher('a?');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->lastpos();
        $this->assertTrue(count($result) == 1 && $result[0] == 1);
    }
    function test_lastpos_negative_character_class() {
        $this->qtype = new qtype_preg_dfa_matcher('[^a]|b');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $result = $this->qtype->roots[0]->operands[0]->lastpos();
        $this->assertTrue(count($result) == 2 && $result[0] == 1 && $result[1] == 2);
    }
    /*function test_lastpos_assert() {
        $this->qtype = new qtype_preg_dfa_matcher('a(?=.*b)[xcvbnm]*');
        $connection = array();
        $maxnum = 0;
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $this->qtype->roots[0]->operands[0]->lastpos();
        $this->assertTrue(count($this->qtype->roots[0]->operands[0]->operands[0]->operands[1]->lastpos) &&
                            $this->qtype->roots[0]->operands[0]->operands[0]->operands[1]->lastpos[0]>dfa_preg_node_assert::ASSERT_MIN_NUM);
    }*/
    //Unit tests for followpos function
    function test_followpos_node_concatenation_node() {
        $this->qtype = new qtype_preg_dfa_matcher('(?:a|b)*ab');
        $this->qtype->roots[0]->operands[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->operands[0]->nullable();
        $this->qtype->roots[0]->operands[0]->firstpos();
        $this->qtype->roots[0]->operands[0]->lastpos();
        $result = null;
        $this->qtype->roots[0]->followpos($result);
        $res1 = (count($result[1]) == 3 && $result[1][0] == 1 && $result[1][1] == 2 && $result[1][2] == 3);
        $res2 = (count($result[2]) == 3 && $result[2][0] == 1 && $result[2][1] == 2 && $result[2][2] == 3);
        $res3 = (count($result[3]) == 1 && $result[3][0] == 4);
        $this->assertTrue($res1 && $res2 && $res3);
    }
    function test_followpos_three_node_alternation() {
        $this->qtype = new qtype_preg_dfa_matcher('ab|cd|ef');
        $this->qtype->roots[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->nullable();
        $this->qtype->roots[0]->firstpos();
        $this->qtype->roots[0]->lastpos();
        $result = null;
        $this->qtype->roots[0]->followpos($result);
        $this->assertTrue(count($result[1]) == 1 && $result[1][0] == 2);
        $this->assertTrue(count($result[3]) == 1 && $result[3][0] == 4);
        $this->assertTrue(count($result[5]) == 1 && $result[5][0] == 6);
    }
    function test_followpos_question_quantificator() {
        $this->qtype = new qtype_preg_dfa_matcher('a?b');
        $this->qtype->roots[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->nullable();
        $this->qtype->roots[0]->firstpos();
        $this->qtype->roots[0]->lastpos();
        $result = null;
        $this->qtype->roots[0]->followpos($result);
        $this->assertTrue(count($result[1]) == 1 && $result[1][0] == 2);
    }
    function test_followpos_negative_character_class() {
        $this->qtype = new qtype_preg_dfa_matcher('[^a]b');
        $this->qtype->roots[0]->number($connection, $maxnum);
        $this->qtype->roots[0]->nullable();
        $this->qtype->roots[0]->firstpos();
        $this->qtype->roots[0]->lastpos();
        $result = null;
        $this->qtype->roots[0]->followpos($result);
        $this->assertTrue(count($result[1]) == 1 && $result[1][0] == 2);
    }
    //Unit test for buildfa function
    function test_buildfa_easy() {//ab
        $this->qtype = new qtype_preg_dfa_matcher('ab');
        $this->assertTrue(count($this->qtype->finiteautomates[0][0]->passages) == 1 && $this->qtype->finiteautomates[0][0]->passages[1] == 1);
        $this->assertTrue(count($this->qtype->finiteautomates[0][1]->passages) == 1 && $this->qtype->finiteautomates[0][1]->passages[2] == 2);
        $this->assertTrue(count($this->qtype->finiteautomates[0][2]->passages) == 1 && $this->qtype->finiteautomates[0][2]->passages[qtype_preg_dfa_leaf_meta::ENDREG] == -1);
    }
    function test_buildfa_iteration() {//ab*
        $this->qtype = new qtype_preg_dfa_matcher('ab*');
        $this->assertTrue(count($this->qtype->finiteautomates[0][0]->passages) == 1);
        $n1 = $this->qtype->finiteautomates[0][0]->passages[1];
        $this->assertTrue(count($this->qtype->finiteautomates[0][$n1]->passages) == 2);
        $this->assertTrue($this->qtype->finiteautomates[0][$n1]->passages[qtype_preg_dfa_leaf_meta::ENDREG] == -1 && $this->qtype->finiteautomates[0][$n1]->passages[2] == $n1);
    }
    function test_buildfa_alternation() {//a|b
        $this->qtype = new qtype_preg_dfa_matcher('a|b');
        $this->assertTrue(count($this->qtype->finiteautomates[0][0]->passages) == 2 && $this->qtype->finiteautomates[0][0]->passages[1] == 1 && $this->qtype->finiteautomates[0][0]->passages[2] == 1);
        $this->assertTrue(count($this->qtype->finiteautomates[0][1]->passages) == 1 && $this->qtype->finiteautomates[0][1]->passages[qtype_preg_dfa_leaf_meta::ENDREG] == -1);
    }
    function test_buildfa_alternation_and_iteration() {//(a|b)c*
        $this->qtype = new qtype_preg_dfa_matcher('(?:a|b)c*');
        $this->assertTrue(count($this->qtype->finiteautomates[0][0]->passages) == 2);
        $this->assertTrue(count($this->qtype->finiteautomates[0][1]->passages) == 2 && $this->qtype->finiteautomates[0][1]->passages[3] == 1 &&
                            $this->qtype->finiteautomates[0][1]->passages[qtype_preg_dfa_leaf_meta::ENDREG] == -1);
    }
    function test_buildfa_nesting_alternation_and_iteration() {//(ab|cd)*
        $this->qtype = new qtype_preg_dfa_matcher('(?:ab|cd)*');
        $this->assertTrue(count($this->qtype->finiteautomates[0][0]->passages) == 3 && $this->qtype->finiteautomates[0][0]->passages[qtype_preg_dfa_leaf_meta::ENDREG] == -1);
        $n1 = $this->qtype->finiteautomates[0][0]->passages[1];
        $n2 = $this->qtype->finiteautomates[0][0]->passages[3];
        $this->assertTrue(count($this->qtype->finiteautomates[0][$n1]->passages) == 1 && $this->qtype->finiteautomates[0][$n1]->passages[2] == 0);
        $this->assertTrue(count($this->qtype->finiteautomates[0][$n2]->passages) == 1 && $this->qtype->finiteautomates[0][$n2]->passages[4] == 0);
    }
    function test_buildfa_question_quantificator() {//a?b
        $this->qtype = new qtype_preg_dfa_matcher('a?b');
        $this->assertTrue(count($this->qtype->finiteautomates[0][0]->passages) == 2);
        $n1 = $this->qtype->finiteautomates[0][0]->passages[1];
        $n2 = $this->qtype->finiteautomates[0][0]->passages[2];
        $this->assertTrue(count($this->qtype->finiteautomates[0][$n1]->passages) == 1 && $this->qtype->finiteautomates[0][$n1]->passages[2] == $n2);
        $this->assertTrue(count($this->qtype->finiteautomates[0][$n2]->passages) == 1 && $this->qtype->finiteautomates[0][$n2]->passages[qtype_preg_dfa_leaf_meta::ENDREG] == -1);
    }
    function test_buildfa_negative_character_class() {//(a[^b]|c[^d])*
        $this->qtype = new qtype_preg_dfa_matcher('(?:a[^b]|c[^d])*');
        $this->assertTrue(count($this->qtype->finiteautomates[0][0]->passages) == 3);
        $n1 = $this->qtype->finiteautomates[0][0]->passages[1];
        $n2 = $this->qtype->finiteautomates[0][0]->passages[3];
        $this->assertTrue(count($this->qtype->finiteautomates[0][$n1]->passages) == 1 && $this->qtype->finiteautomates[0][$n1]->passages[2] == 0);
        $this->assertTrue(count($this->qtype->finiteautomates[0][$n2]->passages) == 1 && $this->qtype->finiteautomates[0][$n2]->passages[4] == 0);
    }
    /*function test_buildfa_assert() {//a(?=.*b)[xcvbnm]* test for old style assert matching
        $this->qtype = new qtype_preg_dfa_matcher('a(?=[xcvnm]*b)[xcvbnm]*');
        $this->assertTrue(count($this->qtype->finiteautomates[0][0]->passages)==1 && $this->qtype->finiteautomates[0][0]->passages[3]==1);
        $this->assertTrue(count($this->qtype->finiteautomates[0][1]->passages)==2 && $this->qtype->finiteautomates[0][1]->passages[1]==1 && $this->qtype->finiteautomates[0][1]->passages[2]==2);
        $this->assertTrue(count($this->qtype->finiteautomates[0][2]->passages)==2 && $this->qtype->finiteautomates[0][2]->passages[4]==2 && $this->qtype->finiteautomates[0][2]->passages[186759556]==-1);
        $this->assertTrue(count($this->qtype->finiteautomates[0])==3);
    }*/

    //Unit tests for compare function
    function test_compare_full_incorrect() {//ab
        $this->qtype = new qtype_preg_dfa_matcher('ab');
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[qtype_preg_dfa_leaf_meta::ENDREG] = -1;
        $this->connection[0][1] = $this->qtype->roots[0]->operands[0]->operands[0];
        $this->connection[0][2] = $this->qtype->roots[0]->operands[0]->operands[1];
        $result = $this->qtype->compare(new qtype_poasquestion_string('b'),0);
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == -1 && $result->next == 'a');
    }
    function test_compare_first_character_incorrect() {//ab
        $this->qtype = new qtype_preg_dfa_matcher('ab');
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[qtype_preg_dfa_leaf_meta::ENDREG] = -1;
        $this->qtype->connection[0][1] = $this->qtype->roots[0]->operands[0]->operands[0];
        $this->qtype->connection[0][2] = $this->qtype->roots[0]->operands[0]->operands[1];
        $result = $this->qtype->compare(new qtype_poasquestion_string('cb'),0);
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == -1 && $result->next == 'a');
    }
    function test_compare_particular_correct() {//ab
        $this->qtype = new qtype_preg_dfa_matcher('ab');
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[qtype_preg_dfa_leaf_meta::ENDREG] = -1;
        $this->qtype->connection[0][1] = $this->qtype->roots[0]->operands[0]->operands[0];
        $this->qtype->connection[0][2] = $this->qtype->roots[0]->operands[0]->operands[1];
        $result = $this->qtype->compare(new qtype_poasquestion_string('ac'),0);
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 0 && $result->next == 'b');
    }
    function test_compare_full_correct() {//ab
        $this->qtype = new qtype_preg_dfa_matcher('ab');
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[qtype_preg_dfa_leaf_meta::ENDREG] = -1;
        $this->qtype->connection[0][1] = $this->qtype->roots[0]->operands[0]->operands[0];
        $this->qtype->connection[0][2] = $this->qtype->roots[0]->operands[0]->operands[1];
        $result = $this->qtype->compare(new qtype_poasquestion_string('ab'),0);
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 1 && $result->next == 0);
    }
    function test_compare_question_quantificator() {//a?b
        $this->qtype = new qtype_preg_dfa_matcher('a?b');
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][0]->passages[2] = 2;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[qtype_preg_dfa_leaf_meta::ENDREG] = -1;
        $this->qtype->connection[0][1] = $this->qtype->roots[0]->operands[0]->operands[0]->operands[0];
        $this->qtype->connection[0][2] = $this->qtype->roots[0]->operands[0]->operands[1];
        $result1 = $this->qtype->compare(new qtype_poasquestion_string('ab'), 0);
        $result2 = $this->qtype->compare(new qtype_poasquestion_string('b'), 0);
        $result3 = $this->qtype->compare(new qtype_poasquestion_string('Incorrect string'), 0);
        $this->assertTrue($result1->full);
        $this->assertTrue($result1->index == 1 && $result1->next == 0);
        $this->assertTrue($result2->full);
        $this->assertTrue($result2->index == 0 && $result2->next == 0);
        $this->assertFalse($result3->full);
        $this->assertTrue($result3->index == -1 && $result3->next == 'b' || $result3->next == 'a');
    }
    function test_compare_negative_character_class() {//[^a][b]
        $this->qtype = new qtype_preg_dfa_matcher('[b][b]');
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[qtype_preg_dfa_leaf_meta::ENDREG] = -1;
        $this->qtype->connection[0][1] = $this->qtype->roots[0]->operands[0]->operands[0];
        $this->qtype->connection[0][2] = $this->qtype->roots[0]->operands[0]->operands[1];
        $result1 = $this->qtype->compare(new qtype_poasquestion_string('ab'),0);
        $result2 = $this->qtype->compare(new qtype_poasquestion_string('bb'),0);
        $this->assertFalse($result1->full);
        $this->assertTrue($result1->index == -1 && isset($result1->next) && $result1->next != 'a');
        $this->assertTrue($result2->full);
        $this->assertTrue($result2->index == 1 && $result2->next == 0);
    }
    function test_compare_dot() {//.b
        $this->qtype = new qtype_preg_dfa_matcher('.b');
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[qtype_preg_dfa_leaf_meta::ENDREG] = -1;
        $this->qtype->connection[0][1] = $this->qtype->roots[0]->operands[0]->operands[0];
        $this->qtype->connection[0][2] = $this->qtype->roots[0]->operands[0]->operands[1];
        $result1 = $this->qtype->compare(new qtype_poasquestion_string('ab'), 0);
        $result2 = $this->qtype->compare(new qtype_poasquestion_string('fbf'),0);
        $result3 = $this->qtype->compare(new qtype_poasquestion_string('fff'),0);
        $this->assertTrue($result1->full);
        $this->assertTrue($result1->index == 1 && $result1->next == 0);
        $this->assertFalse($result2->full);
        $this->assertTrue($result2->index == 1 && $result2->next == 0);
        $this->assertFalse($result3->full);
        $this->assertTrue($result3->index == 0 && $result3->next == 'b');
    }
    /*function _test_compare_assert() {//a(?=.*b)[xcvbnm]*
        $this->qtype = new qtype_preg_dfa_matcher('a(?=[xcvnm]*b)[xcvbnm]*');
        $result1 = $this->qtype->compare('an',0);
        $result2 = $this->qtype->compare('annvnvb',0);
        $result3 = $this->qtype->compare('annvnvv',0);
        $result4 = $this->qtype->compare('abnm',0);
        $this->assertFalse($result1->full);
        $this->assertTrue($result1->index == 1 && ($result1->next !== 0));
        $this->assertTrue($result2->full);
        $this->assertTrue($result2->index == 6 && $result2->next === 0);
        $this->assertFalse($result3->full);
        $this->assertTrue($result3->index == 6 && ($result3->next !== 0));
        $this->assertTrue($result4->full);
        $this->assertTrue($result4->index == 3 && $result4->next === 0);
    }*/
    function test_compare_unanchor() {//ab
        $this->qtype = new qtype_preg_dfa_matcher('ab');
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[qtype_preg_dfa_leaf_meta::ENDREG] = -1;
        $this->qtype->connection[0][1] = $this->qtype->roots[0]->operands[0]->operands[0];
        $this->qtype->connection[0][2] = $this->qtype->roots[0]->operands[0]->operands[1];
        $result = $this->qtype->compare(new qtype_poasquestion_string('OabO'), 0, 0, false);
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == -1 && $result->next == 'a' && $result->offset == 0);
        $result = $this->qtype->compare(new qtype_poasquestion_string('OabO'), 0, 1, false);
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 1 && $result->next === 0 && $result->offset == 1);
        $result = $this->qtype->compare(new qtype_poasquestion_string('OabO'), 0, 1, true);
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == 1 && $result->next === 0 && $result->offset == 1);
        $result = $this->qtype->compare(new qtype_poasquestion_string('OabO'), 0, 2, false);
        $this->assertFalse($result->full);
        $this->assertTrue($result->index == -1 && $result->next == 'a' && $result->offset == 2);
    }
    function test_compare_unanchor_iteration() {//(?:abc)*
        $this->qtype = new qtype_preg_dfa_matcher('(?:abc)*');
        $this->qtype->finiteautomates[0][0] = new finite_automate_state;
        $this->qtype->finiteautomates[0][1] = new finite_automate_state;
        $this->qtype->finiteautomates[0][2] = new finite_automate_state;
        $this->qtype->finiteautomates[0][0]->passages[1] = 1;
        $this->qtype->finiteautomates[0][0]->passages[qtype_preg_dfa_leaf_meta::ENDREG] = -1;
        $this->qtype->finiteautomates[0][1]->passages[2] = 2;
        $this->qtype->finiteautomates[0][2]->passages[3] = 0;
        $this->qtype->connection[0][1] = $this->qtype->roots[0]->operands[0]->operands[0]->operands[0];
        $this->qtype->connection[0][2] = $this->qtype->roots[0]->operands[0]->operands[0]->operands[1];
        $this->qtype->connection[0][3] = $this->qtype->roots[0]->operands[0]->operands[0]->operands[2];
        $result = $this->qtype->compare(new qtype_poasquestion_string('abcabcab'), 0, 0, false);
        $this->assertTrue($result->full);
        $this->assertTrue($result->index == 5 && $result->next === 0 && $result->offset == 0);
    }
    //General tests, testing parser + buildfa + compare (also nullable, firstpos, lastpos, followpos and other in buildfa)
    //qtype_preg_dfa_matcher without input and output data.
    function test_general_repeat_characters() {
        $matcher = new qtype_preg_dfa_matcher('^(?:a|b)*abb$');
        $matcher->match('cd');
        $this->assertFalse($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length() == -1 && substr($matcher->get_match_results()->string_extension(), 0, 1) === 'a');
        $matcher->match('ca');
        $this->assertFalse($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length() == -1 && substr($matcher->get_match_results()->string_extension(), 0, 1) === 'a');
        $matcher->match('ac');
        $this->assertFalse($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length() == 1 && (substr($matcher->get_match_results()->string_extension(), 0, 1) === 'b') || substr($matcher->get_match_results()->string_extension(), 0, 1) === 'a');
        $matcher->match('bb');
        $this->assertFalse($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length() == 2 && substr($matcher->get_match_results()->string_extension(), 0, 1) === 'a');
        $matcher->match('abb');
        $this->assertTrue($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length() == 3 && $matcher->get_match_results()->string_extension() === '');
        $matcher->match('ababababababaabbabababababababaabb');//34 characters
        $this->assertTrue($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length() == 34 && $matcher->get_match_results()->string_extension() ==='');
    }
    /*function test_general_assert() {
        $matcher = new qtype_preg_dfa_matcher('a(?=[xcvnm]*b)[xcvbnm]*');
        $result1 = $matcher->match('an');
        $this->assertFalse($matcher->get_match_results()->full);
        $char = substr($matcher->get_match_results()->string_extension(), 0, 1);
        $this->assertTrue($matcher->get_match_results()->length() == 2 && strchr('xcvbnm', $char)!==false);
        $matcher->match('anvnvb');
        $this->assertTrue($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length() == 6 && $matcher->get_match_results()->string_extension() === '');
        $matcher->match('avnvnv');
        $char = substr($matcher->get_match_results()->string_extension(), 0, 1);
        $this->assertFalse($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length() == 6 && strchr('xcvbnm', $char)!==false);
        $matcher->match('abnm');
        $this->assertTrue($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length() == 4 && $matcher->get_match_results()->string_extension() === '');
    }*/
    /*
    *   this is overall test for qtype_preg_dfa_matcher class
    *   you may use it as example of test
    */
    function _test_general_two_asserts() {
        $matcher = new qtype_preg_dfa_matcher('^a(?=b)(?=[xvbnm]*c)[xcvbnm]*$');//put regular expirience in constructor for building dfa.
        /*
        *   call match method for matching string with regex, string is argument, regex was got in constructor,
        *   results of matching get with method
        *   1)index - get_match_results()->length()
        *   2)full  - is_matching_complete()
        *   3)next  - get_match_results()->string_extension()[0]
        */
        $matcher->match('avnm');
        $this->assertFalse($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length() == 0 && substr($matcher->get_match_results()->string_extension(), 0, 1) === 'b');
        $matcher->match('acnm');
        $this->assertFalse($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length() == 0 && substr($matcher->get_match_results()->string_extension(), 0, 1) === 'b');
        $matcher->match('abnm');
        $char = substr($matcher->get_match_results()->string_extension(), 0, 1);
        $this->assertFalse($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length() == 3 && strchr('xcvbnm', $char)!==false);
        $matcher->match('abnc');
        $this->assertTrue($matcher->get_match_results()->full);
        $this->assertTrue($matcher->get_match_results()->length() == 3 && substr($matcher->get_match_results()->string_extension(), 0, 1) === '');
    }
    //Unit tests for convert tree
    function _test_convert_tree_quantificator_l2r4() {
        $this->qtype = new qtype_preg_dfa_matcher('a{2,4}b');
        $result1 = $this->qtype->compare(new qtype_poasquestion_string('ab'), 0);
        $result2 = $this->qtype->compare(new qtype_poasquestion_string('aab'), 0);
        $result3 = $this->qtype->compare(new qtype_poasquestion_string('aaab'), 0);
        $result4 = $this->qtype->compare(new qtype_poasquestion_string('aaaab'), 0);
        $result5 = $this->qtype->compare(new qtype_poasquestion_string('aaaaab'), 0);
        $this->assertFalse($result1->full);
        $this->assertTrue($result2->full);
        $this->assertTrue($result3->full);
        $this->assertTrue($result4->full);
        $this->assertFalse($result5->full);
    }
    function _test_convert_tree_quantificator_l0r4() {
        $this->qtype = new qtype_preg_dfa_matcher('a{,4}b');
        $result0 = $this->qtype->compare(new qtype_poasquestion_string('b'), 0);
        $result1 = $this->qtype->compare(new qtype_poasquestion_string('ab'), 0);
        $result2 = $this->qtype->compare(new qtype_poasquestion_string('aab'), 0);
        $result3 = $this->qtype->compare(new qtype_poasquestion_string('aaab'), 0);
        $result4 = $this->qtype->compare(new qtype_poasquestion_string('aaaab'), 0);
        $result5 = $this->qtype->compare(new qtype_poasquestion_string('aaaaab'), 0);
        $this->assertTrue($result0->full);
        $this->assertTrue($result1->full);
        $this->assertTrue($result2->full);
        $this->assertTrue($result3->full);
        $this->assertTrue($result4->full);
        $this->assertFalse($result5->full);
    }
    function _test_convert_tree_quantificator_l2rinf() {
        $this->qtype = new qtype_preg_dfa_matcher('a{2,}b');
        $result1 = $this->qtype->compare(new qtype_poasquestion_string('ab'), 0);
        $result2 = $this->qtype->compare(new qtype_poasquestion_string('aab'), 0);
        $result3 = $this->qtype->compare(new qtype_poasquestion_string('aaab'), 0);
        $result4 = $this->qtype->compare(new qtype_poasquestion_string('aaaab'), 0);
        $result5 = $this->qtype->compare(new qtype_poasquestion_string('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaab'), 0);
        $this->assertFalse($result1->full);
        $this->assertTrue($result2->full);
        $this->assertTrue($result3->full);
        $this->assertTrue($result4->full);
        $this->assertTrue($result5->full);
    }
    function test_convert_tree_subexpression() {
        $this->qtype = new qtype_preg_dfa_matcher('(a|b)');
        $result1 = $this->qtype->compare(new qtype_poasquestion_string('b'), 0);
        $result2 = $this->qtype->compare(new qtype_poasquestion_string('a'), 0);
        $result3 = $this->qtype->compare(new qtype_poasquestion_string('Incorrect'), 0);
        $this->assertTrue($result1->full);
        $this->assertTrue($result2->full);
        $this->assertFalse($result3->full);
    }
    //Unit test for wave
    function test_wave_easy() {
        $matcher = new qtype_preg_dfa_matcher('abcd');
        $matcher->match('abce');
        $this->assertTrue(substr($matcher->get_match_results()->string_extension(), 0, 1) === 'd');
    }
    function test_wave_iteration() {
        $matcher = new qtype_preg_dfa_matcher('abc*d');
        $matcher->match('abB');
        $this->assertTrue(substr($matcher->get_match_results()->string_extension(), 0, 1) === 'd');
    }
    function test_wave_alternation() {;
        $matcher = new qtype_preg_dfa_matcher('a(?:cdgfhghghgdhgfhdgfydgfdhgfdhgfdhgfhdgfhdgfhdgfydgfy|b)');
        $matcher->match('a_incorrect');
        $this->assertTrue(substr($matcher->get_match_results()->string_extension(), 0, 1) === 'b');
    }
    function test_wave_repeat_chars() {
        $matcher = new qtype_preg_dfa_matcher('^(?:a|b)*abb$');
        $matcher->match('ababababbbbaaaabbbabbbab');
        $this->assertTrue(substr($matcher->get_match_results()->string_extension(), 0, 1) === 'b');
    }
    function test_wave_complex() {
        $matcher = new qtype_preg_dfa_matcher('(?:fgh|ab?c)+');
        $matcher->match('something');
        $this->assertTrue(substr($matcher->get_match_results()->string_extension(), 0, 1) === 'a');
    }
    //Unit tests for left character count determined by wave function
    function test_wave_left_full_true() {
        $matcher = new qtype_preg_dfa_matcher('abcd');
        $matcher->match('abcd');
        $this->assertTrue($matcher->get_match_results()->left == 0);
    }
    function test_wave_left_easy_regex() {
        $matcher = new qtype_preg_dfa_matcher('abcdefghi');
        $matcher->match('abcd');
        $this->assertTrue($matcher->get_match_results()->left == 5);
    }
    function test_wave_left_complex_regex() {
        $matcher = new qtype_preg_dfa_matcher('ab+c{5,9}(?:ab?c|dfg)|averylongword');
        $matcher->match('a');
        $this->assertTrue($matcher->get_match_results()->left == 8);
    }
}
?>
