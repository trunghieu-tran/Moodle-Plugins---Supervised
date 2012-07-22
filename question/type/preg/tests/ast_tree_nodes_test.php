<?php
/**
 * Unit tests for (some of) question/type/preg/ast_tree_nodes_test.php.
 *
 * @copyright &copy; 2012  Terechov Grigory
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/ast_tree_nodes.php');

class qtype_preg_authors_tool_node_leaf_test extends PHPUnit_Framework_TestCase {
    
    function test_node_info_tree() {
        return true;
    }
}

class qtype_preg_authors_tool_node_operator_test extends PHPUnit_Framework_TestCase {
    
    function test_node_info_tree() {
        return true;
    }
}

class qtype_preg_authors_tool_explain_tree_test extends PHPUnit_Framework_TestCase {
    
    function test_create_dot() {
        
        echo "-------------------Start testing------------------\n";        
        
        $tree1 = new qtype_preg_author_tool_explain_tree('(a|)');
        $etalon_dot_instructions1 = 'digraph{\nrankdir = LR\n"node3"[label="SUBPATTERN", shape="square"]\n"node0"[label="a"]\n"node2"->"node0"\n"node1"[label="EMPTY", shape="square"]\n"node2"->"node1"\n"node2"[label="ALTERNATIVE", shape="square"]\n"node3"->"node2"\n}';

        $tree2 = new qtype_preg_author_tool_explain_tree('^\\\\a\\b\\A\\Z\\G$');        
        $etalon_dot_instructions2 = 'digraph{\nrankdir = LR\n"node14"[label="СONCAT", shape="square"]\n"node0"[label="^"]\n"node2"->"node0"\n"node1"[label="\\"]\n"node2"->"node1"\n"node2"[label="СONCAT", shape="square"]\n"node4"->"node2"\n"node3"[label="a"]\n"node4"->"node3"\n"node4"[label="СONCAT", shape="square"]\n"node6"->"node4"\n"node5"[label="\b"]\n"node6"->"node5"\n"node6"[label="СONCAT", shape="square"]\n"node8"->"node6"\n"node7"[label="^"]\n"node8"->"node7"\n"node8"[label="СONCAT", shape="square"]\n"node10"->"node8"\n"node9"[label="$"]\n"node10"->"node9"\n"node10"[label="СONCAT", shape="square"]\n"node12"->"node10"\n"node11"[label="^"]\n"node12"->"node11"\n"node12"[label="СONCAT", shape="square"]\n"node14"->"node12"\n"node13"[label="$"]\n"node14"->"node13"\n}';
        
        $tree3 = new qtype_preg_author_tool_explain_tree('abc[\\w\\W\\s\\S\\d\\D][:word:]');        
        $etalon_dot_instructions3 = 'digraph{\nrankdir = LR\n"node8"[label="СONCAT", shape="square"]\n"node0"[label="a"]\n"node2"->"node0"\n"node1"[label="b"]\n"node2"->"node1"\n"node2"[label="СONCAT", shape="square"]\n"node4"->"node2"\n"node3"[label="c"]\n"node4"->"node3"\n"node4"[label="СONCAT", shape="square"]\n"node6"->"node4"\n"node5"[label="[\w\W\s\S\d\D]"]\n"node6"->"node5"\n"node6"[label="СONCAT", shape="square"]\n"node8"->"node6"\n"node7"[label=":word:"]\n"node8"->"node7"\n}';
        
        $tree4 = new qtype_preg_author_tool_explain_tree('[a-z2-5]33*');        
        $etalon_dot_instructions4 = 'digraph{\nrankdir = LR\n"node5"[label="СONCAT", shape="square"]\n"node0"[label="[a-z2-5]"]\n"node2"->"node0"\n"node1"[label="3"]\n"node2"->"node1"\n"node2"[label="СONCAT", shape="square"]\n"node5"->"node2"\n"node3"[label="3"]\n"node4"->"node3"\n"node4"[label="Infinite quantificator:\nleft border is 0", shape="square"]\n"node5"->"node4"\n}';

        $tree5 = new qtype_preg_author_tool_explain_tree('[B-D!.].{1,5}23?');        
        $etalon_dot_instructions5 = 'digraph{\nrankdir = LR\n"node8"[label="СONCAT", shape="square"]\n"node0"[label="[!.B-D]"]\n"node3"->"node0"\n"node1"[label="."]\n"node2"->"node1"\n"node2"[label="Finite quantificator:\nleft border is 1;\nright border is 5", shape="square"]\n"node3"->"node2"\n"node3"[label="СONCAT", shape="square"]\n"node5"->"node3"\n"node4"[label="2"]\n"node5"->"node4"\n"node5"[label="СONCAT", shape="square"]\n"node8"->"node5"\n"node6"[label="3"]\n"node7"->"node6"\n"node7"[label="Finite quantificator:\nleft border is 0;\nright border is 1", shape="square"]\n"node8"->"node7"\n}';

        $tree6 = new qtype_preg_author_tool_explain_tree('\\A[^c-z;-](ef)+');        
        $etalon_dot_instructions6 = 'digraph{\nrankdir = LR\n"node8"[label="СONCAT", shape="square"]\n"node0"[label="^"]\n"node8"->"node0"\n"node1"[label="[^;-c-z]"]\n"node7"->"node1"\n"node2"[label="e"]\n"node4"->"node2"\n"node3"[label="f"]\n"node4"->"node3"\n"node4"[label="СONCAT", shape="square"]\n"node5"->"node4"\n"node5"[label="SUBPATTERN", shape="square"]\n"node6"->"node5"\n"node6"[label="Infinite quantificator:\nleft border is 1", shape="square"]\n"node7"->"node6"\n"node7"[label="СONCAT", shape="square"]\n"node8"->"node7"\n}';
        
        //qtype_preg_regex_handler::execute_dot($tree6->create_dot(),"/var/www/test.svg");
        //var_dump($tree6->create_dot());
        
        //$tree = new qtype_preg_author_tool_explain_tree('[a-z2-5]33*');            
        //qtype_preg_regex_handler::execute_dot($tree->create_dot(),'/var/www/test.svg');
        
        $this->assertTrue($etalon_dot_instructions1 !== $tree1->create_dot(),'Test with regex (a|) failed!');
        $this->assertTrue($etalon_dot_instructions2 !== $tree2->create_dot(),'Test with regex ^a\\b\\A\\Z\\G$ failed!');
        $this->assertTrue($etalon_dot_instructions3 !== $tree3->create_dot(),'Test with regex abc[\\w\\W\\s\\S\\d\\D][:word] failed!');
        $this->assertTrue($etalon_dot_instructions4 !== $tree4->create_dot(),'Test with regex [a-z2-5]33* failed!');
        $this->assertTrue($etalon_dot_instructions5 !== $tree5->create_dot(),'Test with regex [B-D!.].{1,5}23? failed!');
        $this->assertTrue($etalon_dot_instructions6 !== $tree6->create_dot(),'Test with regex \\A[^c-z;-](ef)+ failed!');
        
        echo "-------------------End testing------------------\n";
    }
}
?>
