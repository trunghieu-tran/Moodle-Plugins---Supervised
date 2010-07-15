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
				}
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
		$node = $this->form_tree('(la1)');
		$this->assertFalse($this->qtype->nullable($node));
	}
	function nullable_leaf_iteration_node_testing(){
		$node = $this->form_tree('(n* (la1))');
		$this->assertTrue($this->qtype->nullable($node));
	}
	function nullable_leaf_concatenation_node_testing(){
		$node = $this->form_tree('(no (la1)(lb1))');
		$this->assertFalse($this->qtype->nullable($node));
	}
	function nullable_leaf_alternative_node_testing(){
		$node = $this->form_tree('(n| (la1)(lb1))');
		$this->assertFalse($this->qtype->nullable($node));
	}
	function nullable_node_concatenation_node_testing(){
		$node = $this->form_tree('(no (n* (la1))(no (lb1)(lc1)))');
		$this->assertFalse($this->qtype->nullable($node));
	}
	function nullable_node_alternative_node_testing(){
		$node = $this->form_tree('(n| (n* (la1))(no (lb1)(lc1)))');
		$this->assertTrue($this->qtype->nullable($node));
	}
	function nullable_third_level_node_testing(){
		$node = $this->form_tree('(n| (n| (n| (la1)(lb1))(n* (lc1)))(n* (ld1)))');
		$this->assertTrue($this->qtype->nullable($node));
	}
	function nullable_question_quantificator_testing(){
		$node = $this->form_tree('(n? (la1))');
		$this->assertTrue($this->qtype->nullable($node));
	}
	function nullable_negative_character_class_testing(){
		$node = $this->form_tree('(la0)');
		$this->assertFalse($this->qtype->nullable($node));
	}
	function nullable_assert_testing(){
		$node = $this->form_tree('(no (la1)(no (nA (no (n* (d))(lb1)))(n* (lxcvbnm1))))');
		$this->assertTrue($this->qtype->nullable($node->secop->firop));
	}
	//Unit test for firstpos function
	function firstpos_leaf_testing(){
		$node = $this->form_tree('(la1)');
		$result = $this->qtype->firstpos($node);
		$this->assertTrue(count($result)==1&&result[0]==1);
	}
	function firstpos_leaf_concatenation_node_testing(){
		$node = $this->form_tree('(no (la1)(lb1))');
		$result = $this->qtype->firstpos($node);
		$this->assertTrue(count($result)==1&&result[0]==1);
	}
	function firstpos_leaf_alternative_node_testing(){
		$node = $this->form_tree('(n| (la1)(lb1))');
		$result=$this->qtype->fistpos($node);
		$this->assertTrue(count($result)==2&&$result[0]==1&&$result[1]==2);
	}
	function firstpos_three_leaf_alternative_testing(){
		$node = $this->form_tree('(n| (la1)(n| (lb1)(lc1)))');
		$result = $this->qtype->firstpos($node);
		$this->assertTrue(count($result)==3&&$result[0]==1&&$result[1]==2&&$result[2]==3);
	}
	function firstpos_leaf_iteration_node_testing(){
		$node = $this->form_tree('(n* (la1))');
		$result = $this->qtype->firstpos($node);
		$this->assertTrue(count($result)==1&&$result[0]==1);
	}
	function firstpos_node_concatenation_node_testing(){
		$node = $this->form_tree('(no (n* (lc1))(n| (la1)(lb1)))');
		$result = $this->qtype->firstpos($node);
		$this->assertTrue(count($result)==3&&$result[0]==1&&$result[1]==2&&$result[2]==3);
	}
	function firstpos_node_alternative_node_testing(){
		$node = $this->form_tree('(n| (n| (la1)(lb1))(n* (lc1)))');
		$result = $this->qtype->firstpos($node);
		$this->assertTrue(count($result)==3&&$result[0]==1&&$result[1]==2&&$result[2]==3);
	}
	function firstpos_node_iteration_node_testing(){
		$node = $this->form_tree('(n* (n* (la1)))');
		$result = $this->qtype->firstpos($node);
		$this->assertTrue(count($result)==1&&$result[0]==1);
	}
	function firstpos_question_quantificator_testing(){
		$node = $this->form_tree('(n? (la1))');
		$result = firstpos($node);
		$this->assertTrue(count($result)==1&&$result[0]==1);
	}
	function firstpos_negative_character_class_testing(){
		$node = $this->form_tree('(no (la0)(lb1))');
		$this->qtype->firstpos($node);
		$this->assertTrue(count($node->firstpos)==1&&$node->firstpos[0]==-1);
		$this->assertTrue(count($node->firop->firstpos)==1&&$node->firop->firstpos==-1);
	}
	function firstpos_assert_testing(){
		$node = $this->form_tree('(no (la1)(no (nA (no (n* (d))(lb1)))(n* (lxcvbnm1))))');
		$this->qtype->firstpos($node);
		$this->assertTrue(count($node->secop->firop->firstpos)&&$node->secop->firop->firstpos[0]>ASSERT);
	}
	?>