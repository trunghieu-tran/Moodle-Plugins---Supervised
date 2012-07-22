<?php
/**
 * Unit tests for explain graph tool.
 *
 * @copyright &copy; 2012 Vladimir Ivanov
 * @author Vladimir Ivanov
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/explain_graph/explain_graph_tool.php');

class qtype_preg_explain_graph_test extends PHPUnit_Framework_TestCase
{
    function test_create_graph_subpattern()
    {
        $tree = new qtype_preg_author_tool_explain_graph('(b)');
        
        $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('submask #1', 'solid');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('b', 'ellipse', 'black', $etalon->subgraphs[0]);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('begin', 'box, style=filled', 'purple', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('end', 'box, style=filled', 'purple', $etalon);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

        $result = $tree->create_graph();

        //qtype_preg_author_tool_explain_graph::execute_dot($result->create_dot(), 'C:\Users\User\Desktop\ololo1.png');

        $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with subpattern!');
        
        //-----------------------------------------------------------------------------
        
        $tree = new qtype_preg_author_tool_explain_graph('(?:\d)');
 
        $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('Any digit', 'ellipse', 'green', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('begin', 'box, style=filled', 'purple', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('end', 'box, style=filled', 'purple', $etalon);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[1], $etalon->nodes[0]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->nodes[2]);
        
        $result = $tree->create_graph();

        //qtype_preg_author_tool_explain_graph::execute_dot($result->create_dot(), 'C:\Users\User\Desktop\ololo2.png');
 
        $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with grouping!');
    }
    
    function test_create_graph_alter()
    {
        $tree = new qtype_preg_author_tool_explain_graph('\.|\D');
 
        $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('Any character', 'ellipse', 'green', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('Any character except of digit', 'ellipse', 'green', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('begin', 'box, style=filled', 'purple', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('end', 'box, style=filled', 'purple', $etalon);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[2], $etalon->nodes[1]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[2], $etalon->nodes[0]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[1], $etalon->nodes[3]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->nodes[3]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[4], $etalon->nodes[2]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[3], $etalon->nodes[5]);

        $result = $tree->create_graph();

        //qtype_preg_author_tool_explain_graph::execute_dot($result->create_dot(), 'C:\Users\User\Desktop\ololo3.png');
        
        $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with alternative!');
    }
    
    function test_create_graph_charclass()
    {
        $tree = new qtype_preg_author_tool_explain_graph('[ab0-9?]');

        $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('ab?0-9', 'record', 'black', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('begin', 'box, style=filled', 'purple', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('end', 'box, style=filled', 'purple', $etalon);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[1], $etalon->nodes[0]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->nodes[2]);

        $result = $tree->create_graph();

        //qtype_preg_author_tool_explain_graph::execute_dot($result->create_dot(), 'C:\Users\User\Desktop\ololo4.png');
        
        $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with charclass!');
    }
    
    function test_create_graph_alone_meta()
    {
        $tree = new qtype_preg_author_tool_explain_graph('\W');

        $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('Not a word character', 'ellipse', 'green', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('begin', 'box, style=filled', 'purple', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('end', 'box, style=filled', 'purple', $etalon);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[1], $etalon->nodes[0]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->nodes[2]);

        $result = $tree->create_graph();

        //qtype_preg_author_tool_explain_graph::execute_dot($result->create_dot(), 'C:\Users\User\Desktop\ololo5.png');
        
        $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with alone meta!');
    }
    
    function test_create_graph_alone_simple()
    {
        $tree = new qtype_preg_author_tool_explain_graph('test');

        $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('begin', 'box, style=filled', 'purple', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('end', 'box, style=filled', 'purple', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('test', 'ellipse', 'black', $etalon);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->nodes[2]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[2], $etalon->nodes[1]);

        $result = $tree->create_graph();

        //qtype_preg_author_tool_explain_graph::execute_dot($result->create_dot(), 'C:\Users\User\Desktop\ololo6.png');
        
        $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with alone simple!');
    }
    
    function test_create_graph_asserts()
    {
        $tree = new qtype_preg_author_tool_explain_graph('^$');

        $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('begin', 'box, style=filled', 'purple', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('end', 'box, style=filled', 'purple', $etalon);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('Begining of line\nEnd of line', $etalon->nodes[0], $etalon->nodes[1]);

        $result = $tree->create_graph();

        //qtype_preg_author_tool_explain_graph::execute_dot($result->create_dot(), 'C:\Users\User\Desktop\ololo7.png');
        
        $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with asserts!');
    }
    
    function test_create_graph_quantifiers()
    {
        $tree = new qtype_preg_author_tool_explain_graph('x+');

        $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('from 1 to infinity times', 'dotted', 'black', $etalon);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('x', 'ellipse', 'black', $etalon->subgraphs[0]);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('begin', 'box, style=filled', 'purple', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('end', 'box, style=filled', 'purple', $etalon);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);
        
        $result = $tree->create_graph();

        //qtype_preg_author_tool_explain_graph::execute_dot($result->create_dot(), 'C:\Users\User\Desktop\ololo8.png');
        
        $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with quantifier +!');
        
        //-----------------------------------------------------------------------------
        
        $tree = new qtype_preg_author_tool_explain_graph('x*');

        $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('from 0 to infinity times', 'dotted', 'black', $etalon);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('x', 'ellipse', 'black', $etalon->subgraphs[0]);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('begin', 'box, style=filled', 'purple', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('end', 'box, style=filled', 'purple', $etalon);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);
        
        $result = $tree->create_graph();

        //qtype_preg_author_tool_explain_graph::execute_dot($result->create_dot(), 'C:\Users\User\Desktop\ololo9.png');
        
        $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with quantifier *!');
        
        //-----------------------------------------------------------------------------
        
        $tree = new qtype_preg_author_tool_explain_graph('x?');

        $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('from 0 to 1 time', 'dotted', 'black', $etalon);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('x', 'ellipse', 'black', $etalon->subgraphs[0]);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('begin', 'box, style=filled', 'purple', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('end', 'box, style=filled', 'purple', $etalon);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);
        
        $result = $tree->create_graph();

        //qtype_preg_author_tool_explain_graph::execute_dot($result->create_dot(), 'C:\Users\User\Desktop\ololo10.png');
        
        $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with quantifier ?!');
        
        //-----------------------------------------------------------------------------
        
        $tree = new qtype_preg_author_tool_explain_graph('x{3,7}');

        $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('from 3 to 7 times', 'dotted', 'black', $etalon);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('x', 'ellipse', 'black', $etalon->subgraphs[0]);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('begin', 'box, style=filled', 'purple', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('end', 'box, style=filled', 'purple', $etalon);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);
        
        $result = $tree->create_graph();

        //qtype_preg_author_tool_explain_graph::execute_dot($result->create_dot(), 'C:\Users\User\Desktop\ololo11.png');
        
        $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with quantifier {}!');
    }
    
    function test_create_graph_assert_and_subgraph()
    {
        $tree = new qtype_preg_author_tool_explain_graph('^(a)');

        $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('submask #1', 'solid');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('a', 'ellipse', 'black', $etalon->subgraphs[0]);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $etalon->subgraphs[0]);
        $etalon->subgraphs[0]->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[1], $etalon->subgraphs[0]->nodes[0]);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('begin', 'box, style=filled', 'purple', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('end', 'box, style=filled', 'purple', $etalon);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('Begining of line', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[1]);

        $result = $tree->create_graph();

        //qtype_preg_author_tool_explain_graph::execute_dot($result->create_dot(), 'C:\Users\User\Desktop\ololo12.png');
        
        $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with assert and subgraph ^(a)!');

        //---------------------------------------------------------------------------

        $tree = new qtype_preg_author_tool_explain_graph('a(\b)');

        $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('submask #1', 'solid');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $etalon->subgraphs[0]);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $etalon->subgraphs[0]);
        $etalon->subgraphs[0]->links[] = new qtype_preg_author_tool_explain_graph_link('A word border', $etalon->subgraphs[0]->nodes[1], $etalon->subgraphs[0]->nodes[0]);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('a', 'ellipse', 'black', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('begin', 'box, style=filled', 'purple', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('end', 'box, style=filled', 'purple', $etalon);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[1], $etalon->nodes[0]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[1], $etalon->nodes[2]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);

        $result = $tree->create_graph();

        //qtype_preg_author_tool_explain_graph::execute_dot($result->create_dot(), 'C:\Users\User\Desktop\ololo13.png');
        
        $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with assert and subgraph a(\b)!');

         //---------------------------------------------------------------------------

        $tree = new qtype_preg_author_tool_explain_graph('^(a)$');

        $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('submask #1', 'solid');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('a', 'ellipse', 'black', $etalon->subgraphs[0]);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $etalon->subgraphs[0]);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $etalon->subgraphs[0]);
        $etalon->subgraphs[0]->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[1], $etalon->subgraphs[0]->nodes[0]);
        $etalon->subgraphs[0]->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[0], $etalon->subgraphs[0]->nodes[2]);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('begin', 'box, style=filled', 'purple', $etalon);
        $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('end', 'box, style=filled', 'purple', $etalon);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('Begining of line', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[1]);
        $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('End of line', $etalon->subgraphs[0]->nodes[2], $etalon->nodes[1]);

        $result = $tree->create_graph();

        //qtype_preg_author_tool_explain_graph::execute_dot($result->create_dot(), 'C:\Users\User\Desktop\ololo14.png');
        
        $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with assert and subgraph ^(a)$!');
    }
}

?>
