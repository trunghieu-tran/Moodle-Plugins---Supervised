<?php

/**
 * tests for /question/type/preg/author_tool_description/preg_description.php'
 *
 * @copyright &copy; 2012 Pahomov Dmitry
 * @author Pahomov Dmitry
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authors_tool/preg_description.php');

class qtype_preg_description_test extends PHPUnit_Framework_TestCase {
    
    /**
     * @dataProvider charset_provider
     */
    public function test_charset($regex, $expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    } 
    public function charset_provider()
    {
        return array(
          array('[^[:^word:]abc\pL]','any symbol except the following: not word character, letter, <span style="color:blue">a</span>, <span style="color:blue">b</span>, <span style="color:blue">c</span>;'),
          array('a','<span style="color:blue">a</span>'),
          array('[^a]','not <span style="color:blue">a</span>'),
          array('\w','word character'),
          array('\W','not word character')
        );
    }
    
    //------------------------------------------------------------------
    
    public function test_meta()
    {
        $handler = new qtype_preg_author_tool_description('a|b|',null,null);
        $result = $handler->description('%s','%s');
        $expected = '<span style="color:blue">a</span> or <span style="color:blue">b</span> or nothing';
        $this->assertEquals($result, $expected);
    }
    
    //------------------------------------------------------------------
 
    /**
     * @dataProvider assert_provider
     */
    public function test_assert($regex, $expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }
    public function assert_provider()
    {
        return array(
          array('^','beginning of the string'),
          array('$','end of the string'),
          array('\b','at a word boundary'),
          array('\B','not at a word boundary'),
          //array('\A','at the start of the subject'),
          array('\Z','at the end of the subject'),
          //array('\G','at the first matching position in the subject')
        );
    }
 
    //------------------------------------------------------------------
    
    public function test_backref()
    {
        $handler = new qtype_preg_author_tool_description('(a)\1',null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $expected = 'subpattern #1: [<span style="color:blue">a</span>] then back reference to subpattern #1';
        $this->assertEquals($result, $expected);
    }
    
    //------------------------------------------------------------------
    
    /**
     * @dataProvider concat_provider
     */
    public function test_concat($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }
    
    public function concat_provider()
    {
        return array(
          array('ab','<span style="color:blue">a</span><span style="color:blue">b</span>'),
          array('[a|b]c','one of the following characters: <span style="color:blue">a</span>, <span style="color:blue">|</span>, <span style="color:blue">b</span>; then <span style="color:blue">c</span>'),
          array('abc','<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>'),
            array(' \t\n\r','space then tabulation then newline(LF) then carriage return character'),
          array('\0113','tabulation then <span style="color:blue">3</span>'),
         );
    }
    
    //------------------------------------------------------------------
    
    /**
     * @dataProvider alt_provider
     */
    public function test_alt($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }
    
    public function alt_provider()
    {
        return array(
          array('a|b','<span style="color:blue">a</span> or <span style="color:blue">b</span>'),
          array('a|b|','<span style="color:blue">a</span> or <span style="color:blue">b</span> or nothing'),
          array('a|b|c','<span style="color:blue">a</span> or <span style="color:blue">b</span> or <span style="color:blue">c</span>'),
        );
    }
    
    //------------------------------------------------------------------
    
    /**
     * @dataProvider nassert_provider
     */
    public function test_nassert($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }
    
    public function nassert_provider()
    {
        return array(
          array('(?=abc)g','further text should match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] and <span style="color:blue">g</span>'),
          array('(?!abc)g','further text should not match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] and <span style="color:blue">g</span>'),
          array('(?<=abc)g','preceding text should match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] then <span style="color:blue">g</span>'),
          array('(?<!abc)g','preceding text should not match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] then <span style="color:blue">g</span>'),
          array('a(?=abc)g','<span style="color:blue">a</span> then further text should match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] and <span style="color:blue">g</span>'),
          array('a(?!abc)g','<span style="color:blue">a</span> then further text should not match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] and <span style="color:blue">g</span>'),
          array('a(?<=abc)g','<span style="color:blue">a</span> and preceding text should match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] then <span style="color:blue">g</span>'),
          array('a(?<!abc)g','<span style="color:blue">a</span> and preceding text should not match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] then <span style="color:blue">g</span>'),
        );
    }
    
    //------------------------------------------------------------------
    
    /**
     * @dataProvider quant_provider
     */
    /*public function test_quant($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }*/
    
    public function quant_provider()
    {
        return array(
          array('g{,1}','jh'),
          array('g+','dg'),
          array('g*','jh'),
          array('g?','dg'),
          array('g{0,1}','jh'),
          array('g{0,}','dg'),
          array('g{1,}','jh'),
          array('g{2,5}','dg'),
        );
    }
    
    //------------------------------------------------------------------    
    
    public function test_option()
    {
        $handler = new qtype_preg_author_tool_description('(a(?i)b)c',null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $expected = 'subpattern #1: [<span style="color:blue">a</span>caseless: <span style="color:blue">b</span>] then case sensitive: <span style="color:blue">c</span>'; 
        $this->assertEquals($result, $expected);
    }
    
    //------------------------------------------------------------------
    
    public function test_numbering()
    {
        $handler = new qtype_preg_author_tool_description('([a|b]|)\W+',null,null);
        //var_dump($handler);
        $result = $handler->default_description();
        $expected = '<span class="description_node_6"><span class="description_node_3">subpattern #1: [<span class="description_node_2"><span class="description_node_0">one of the following characters: <span style="color:blue">a</span>, <span style="color:blue">|</span>, <span style="color:blue">b</span>;</span> or <span class="description_node_1">nothing</span></span>]</span> then <span class="description_node_5"><span class="description_node_4">not word character</span> is repeated at least 1 times</span></span>'; 
        $this->assertEquals($result, $expected);
    }
    
    //------------------------------------------------------------------
    
    /**
     * @dataProvider condmask_provider
     */
    public function test_condmask($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }
    
    public function condmask_provider()
    {
        return array(
          array('(?(?=a)a|b)','if further text should match: [<span style="color:blue">a</span>] then check: [<span style="color:blue">a</span>] else check: [<span style="color:blue">b</span>]'),
          array('(?(?!a)a|b)','if further text should not match: [<span style="color:blue">a</span>] then check: [<span style="color:blue">a</span>] else check: [<span style="color:blue">b</span>]'),
          array('(?(?<=a)a|b)','if preceding text should match: [<span style="color:blue">a</span>] then check: [<span style="color:blue">a</span>] else check: [<span style="color:blue">b</span>]'),
          array('(?(?<!a)a|b)','if preceding text should not match: [<span style="color:blue">a</span>] then check: [<span style="color:blue">a</span>] else check: [<span style="color:blue">b</span>]'),
          array('(?(?=a)a)','if further text should match: [<span style="color:blue">a</span>] then check: [<span style="color:blue">a</span>]'),
          /*array('(a)(?(1)a)','dg'),
          //array('(?(name)a)','jh'),
          array('(?(<name>)a)','jh'),
          array('(?(<name>)a|b)','jh'),
          array('(?(*DEFINE)(?<name>a))','definition of subpattern #1: [<span style="color:blue">a</span>]'),*/
        );
    }
}
