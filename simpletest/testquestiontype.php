<?php  // $Id: testquestiontype.php,put version put time dvkolesov Exp $
/**
 * Unit tests for (some of) question/type/preg/reasc.php.
 *
 * @copyright &copy; 2010 Dmitriy Kolesov
 * @author Dmitriy Kolesov
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/question/type/preg/reasc.php');

class reasc_test extends UnitTestCase {
    var $qtype;
    
    function setUp() {
        $this->qtype = new reasc();
    }
    
    function tearDown() {
        $this->qtype = null;   
    }

    function test_name() {
        $this->assertEqual($this->qtype->name(), 'reasc');
    }
	//service function for testing
	/**
	*form the tree of the regexp from prefix form, for unit tests only!
	*this function is unsafe, input data must be correct!
	*
	*@param prefixform string with regexp in prefix form
	*@return root of formed tree
	*/
	function form_tree($prefixform){
		$result = & new node;
		//forming the node or leaf
		switch(prefixform[1]){ //analyze first character, type of node/leaf
			case 'l': //simple leaf with char class
				$result->type = LEAF;
				$result->subtype = LEAF_CHARCLASS;
				$result->chars = null;
				for($i=2; $prefixform[$i+1]!=')'; $i++){
					$result->chars += $prefixform[i];
				if($prefixform[$i]=='0'){
					$result->direction=false;
				} else {
					$result->direction=true;
				}
				break;
			case 'e': //empty leaf
				$result->type = LEAF;
				$result->subtype = LEAF_EMPTY;
				break;
			case 'd': //metasymbol dot
				$result->type = LEAF;
				$result->subtype = LEAF_METASYMBOLDOT;
				break;
			case 'n':
				$result->type = NODE;
				switch(prefixform[2]){
					case 'o': //concatenation node
						$result->subtype = NODE_CONC;
						break;
					case '|': //alternative node
						$result->subtype = NODE_ALT;
						break;
					case '*': //iteration node
						$result->subtype = NODE_ITER;
						break;
					case '?': //quantificator ? node
						$result->subtype = NODE_QUESTQUANT;
						break;
					case 'A': //true forward assert node
						$result->subtype = NODE_ASSERTTF;
						break;
				}
				//forming operand
				$brackets=0;
				$tmp=null;
				for($i=4; $brackets!=0||$==4; $i++){
					$tmp.$prefixform[$i];
					if($prefixform[$i]=='('){
						$brackets++;
					}
					if($prefixform[$i]==')'){
						$brackets--;
					}
				}
				//forming second operand
				$result->firop = form_tree($tmp);
				if($result->subtype==NODE_CONC||$result->subtype==NODE_ALT){
					$tmp = null;
					do{
						$tmp.prefixform[$i];
						if($prefixform[$i]=='('){
							$brackets++;
						}
						if($prefixform[$i]==')'){
							$brackets--;
						}
						$i++;
					}while($brackets!=0);
					$result->secop = form_tree($tmp);
				}
			}
			break;
		}
		return $result;
	}
	//Unit test for nullable function
	function nullable_leaf_testing(){
		$node = form_tree('(la1)');
		$this->assertFalse(nullable($node));
	}
	function nullable_leaf_iteration_node_testing(){
		$node=form_tree('(k* (la1))');
		$this->assertTrue(nullable($node));
	}
	function nullable_leaf_concatenation_node_testing(){
		$node = form_tree('(ko (la1)(lb1))');
		$this->assertFalse(nullable($node));
	}
	function nullable_leaf_alternative_node_testing(){
		$node->form_tree('(k| (la1)(lb1))');
		$this->assertFalse(nullable($node));
	}
	function nullable_node_concatenation_node_testing(){
		$node = form_tree('(ko (k* (la1))(ko (lb1)(lc1)))');
		$this->assertFalse(nullable($node));
	}
	function nullable_node_alternative_node_testing(){
		$node = form_tree('(k| (k* (la1))(ko (lb1)(lc1)))');
		$this->assertTrue(nullable($node));
	}
	function nullable_third_level_node_testing(){
		$node = form_tree('(k| (k| (k| (la1)(lb1))(k* (lc1)))(k* (ld1)))');
		$this->assertTrue(nullable($node));
	}
	function nullable_question_quantificator_testing(){
		$node = form_tree('(k? (la1))');
		$this->assertTrue(nullable($node));
	}
	function nullable_negative_character_class_testing(){
		$node = form_tree('(la0)');
		$this->assertFalse(nullable($node));
	}
	function nullable_assert_testing(){
		$node = form_tree('(ko (la1)(ko (kA (ko (k* (l\11))(lb1)))(k* (lxcvbnm1))))');
		$this->assertTrue(nullable($node->secop->firop));
	}
	?>