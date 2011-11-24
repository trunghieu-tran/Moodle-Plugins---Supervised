<?php  // $Id: testquestiontype.php,v 0.1 beta 2010/08/08 21:01:01 dvkolesov Exp $

/**
 * Unit tests for (some of) question/type/preg/preg_nodes.php.
 *
 * @copyright &copy; 2011 Oleg Sychev
 * @author Oleg Sychev
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

 
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

class regex_handler_test extends UnitTestCase {

    function test_clone_preg_operator() {
        //Try copying tree for a|b*
        $anode = new preg_leaf_charset;
        $anode->charset = 'a';
        $bnode = new preg_leaf_charset;
        $bnode->charset = 'b';
        $astnode = new preg_node_infinite_quant;
        $astnode->leftborder = 0;
        $astnode->operands[] = $bnode;
        $altnode = new preg_node_alt;
        $altnode->operands[] = $anode;
        $altnode->operands[] = $astnode;

        $copyroot = clone $altnode;

        $this->assertTrue($copyroot == $altnode, 'Root node contents copied wrong');
        $this->assertTrue($copyroot !== $altnode, 'Root node wasn\'t copyied');
        $this->assertTrue($copyroot->operands[0] == $altnode->operands[0], 'A character node contents copied wrong');
        $this->assertTrue($copyroot->operands[0] !== $altnode->operands[0], 'A character node wasn\'t copyied');
        $this->assertTrue($copyroot->operands[1] == $altnode->operands[1], 'Asterisk node contents copied wrong');
        $this->assertTrue($copyroot->operands[1] !== $altnode->operands[1], 'Asterisk node wasn\'t copyied');
        $this->assertTrue($copyroot->operands[1]->operands[0] == $altnode->operands[1]->operands[0], 'B character node contents copied wrong');
        $this->assertTrue($copyroot->operands[1]->operands[0] !== $altnode->operands[1]->operands[0], 'B character node wasn\'t copyied');
    }

}
?>