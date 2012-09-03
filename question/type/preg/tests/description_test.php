<?php

/**
 * Tests for /question/type/preg/author_tool_description/preg_description.php.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Pahomov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
          array('\A','at the start of the subject'),
          array('\Z','at the end of the subject'),
          array('\G','at the first matching position in the subject')
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
    public function test_quant($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }

    public function quant_provider()
    {
        return array(
          array('g{,1}','<span style="color:blue">g</span> may be missing'),
          array('g+','<span style="color:blue">g</span> is repeated any number of times'),
          array('g*','<span style="color:blue">g</span> is repeated any number of times or missing'),
          array('g?','<span style="color:blue">g</span> may be missing'),
          array('g{0,1}','<span style="color:blue">g</span> may be missing'),
          array('g{0,}','<span style="color:blue">g</span> is repeated any number of times or missing'),
          array('g{1,}','<span style="color:blue">g</span> is repeated any number of times'),
          array('g{2,5}','<span style="color:blue">g</span> is repeated from 2 to 5 times'),
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
        $expected = '<span class="description_node_6"><span class="description_node_3">subpattern #1: [<span class="description_node_2"><span class="description_node_0">one of the following characters: <span style="color:blue">a</span>, <span style="color:blue">|</span>, <span style="color:blue">b</span>;</span> or <span class="description_node_1">nothing</span></span>]</span> then <span class="description_node_5"><span class="description_node_4">not word character</span> is repeated any number of times</span></span>';
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
          array('(?(1)a)','if the subpattern #1 has been successfully matched then check: [<span style="color:blue">a</span>]'),
          array('(?(name)a)','if the subpattern "name" has been successfully matched then check: [<span style="color:blue">a</span>]'),
          array('(?(<name>)a)','if the subpattern "name" has been successfully matched then check: [<span style="color:blue">a</span>]'),
          array('(?(<name>)a|b)','if the subpattern "name" has been successfully matched then check: [<span style="color:blue">a</span>] else check: [<span style="color:blue">b</span>]'),
          array('(?(DEFINE)(?<name>a))','definition of subpattern #1: [<span style="color:blue">a</span>]'),
        );
    }

    //------------------------------------------------------------------

    /**
     * @dataProvider postprocessing_provider
     */
    public function test_postprocessing($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }

    public function postprocessing_provider()
    {
        return array(
          array('([abc])','subpattern #1: [one of the following characters: <span style="color:blue">a</span>, <span style="color:blue">b</span>, <span style="color:blue">c</span>]'),
          array('[^\S]','white space'),
        );
    }

    //------------------------------------------------------------------

    /**
     * @dataProvider err_provider
     */
    public function test_err($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }

    public function err_provider()
    {
        return array(
          array('a{9,0}','<span style="color:blue">a</span><span style="color:red"> is repeated from 9 to 0 times (incorrect quantifier borders)</span>'),
          array('(a','<span style="color:red">Regex syntax error: missing a closing parenthesis ')' for the opening parenthesis in position 0.</span> Operands: <span style="color:blue">a</span>'),
        );
    }

}

class qtype_preg_description_form_test extends PHPUnit_Framework_TestCase {
    /**
     * @dataProvider form_provider
     */
    public function test_form($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        $result = $handler->form_description('g');
        $this->assertEquals($result, $expected);
    }

    public function form_provider()
    {
        return array(
          array('a','<span style="color:blue">a</span>(form g)'),
          array('\w','word character(form g)'),
          array('$','end of the string(form g)'),
          array('a|bc|','<span style="color:blue">a</span>(form g) or <span style="color:blue">b</span>(form g)<span style="color:blue">c</span>(form g) or nothing(form g)'),
        );
    }
}

