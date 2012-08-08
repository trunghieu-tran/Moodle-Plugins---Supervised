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
          array('[\x00-\xff]','one of the following characters: character with code 0x0, character with code 0x1, character with code 0x2, character with code 0x3, character with code 0x4, character with code 0x5, character with code 0x6, character with code 0x7, character with code 0x8, tabulation, newline(LF), character with code 0xB, character with code 0xC, carriage return character, character with code 0xE, character with code 0xF, character with code 0x10, character with code 0x11, character with code 0x12, character with code 0x13, character with code 0x14, character with code 0x15, character with code 0x16, character with code 0x17, character with code 0x18, character with code 0x19, character with code 0x1A, character with code 0x1B, character with code 0x1C, character with code 0x1D, character with code 0x1E, character with code 0x1F, space, <span style="color:blue">!</span>, <span style="color:blue">"</span>, <span style="color:blue">#</span>, <span style="color:blue">$</span>, <span style="color:blue">%</span>, <span style="color:blue">&</span>, <span style="color:blue">\'</span>, <span style="color:blue">(</span>, <span style="color:blue">)</span>, <span style="color:blue">*</span>, <span style="color:blue">+</span>, <span style="color:blue">,</span>, <span style="color:blue">-</span>, <span style="color:blue">.</span>, <span style="color:blue">/</span>, <span style="color:blue">0</span>, <span style="color:blue">1</span>, <span style="color:blue">2</span>, <span style="color:blue">3</span>, <span style="color:blue">4</span>, <span style="color:blue">5</span>, <span style="color:blue">6</span>, <span style="color:blue">7</span>, <span style="color:blue">8</span>, <span style="color:blue">9</span>, <span style="color:blue">:</span>, <span style="color:blue">;</span>, <span style="color:blue"><</span>, <span style="color:blue">=</span>, <span style="color:blue">></span>, <span style="color:blue">?</span>, <span style="color:blue">@</span>, <span style="color:blue">A</span>, <span style="color:blue">B</span>, <span style="color:blue">C</span>, <span style="color:blue">D</span>, <span style="color:blue">E</span>, <span style="color:blue">F</span>, <span style="color:blue">G</span>, <span style="color:blue">H</span>, <span style="color:blue">I</span>, <span style="color:blue">J</span>, <span style="color:blue">K</span>, <span style="color:blue">L</span>, <span style="color:blue">M</span>, <span style="color:blue">N</span>, <span style="color:blue">O</span>, <span style="color:blue">P</span>, <span style="color:blue">Q</span>, <span style="color:blue">R</span>, <span style="color:blue">S</span>, <span style="color:blue">T</span>, <span style="color:blue">U</span>, <span style="color:blue">V</span>, <span style="color:blue">W</span>, <span style="color:blue">X</span>, <span style="color:blue">Y</span>, <span style="color:blue">Z</span>, <span style="color:blue">[</span>, <span style="color:blue">\</span>, <span style="color:blue">]</span>, <span style="color:blue">^</span>, <span style="color:blue">_</span>, <span style="color:blue">`</span>, <span style="color:blue">a</span>, <span style="color:blue">b</span>, <span style="color:blue">c</span>, <span style="color:blue">d</span>, <span style="color:blue">e</span>, <span style="color:blue">f</span>, <span style="color:blue">g</span>, <span style="color:blue">h</span>, <span style="color:blue">i</span>, <span style="color:blue">j</span>, <span style="color:blue">k</span>, <span style="color:blue">l</span>, <span style="color:blue">m</span>, <span style="color:blue">n</span>, <span style="color:blue">o</span>, <span style="color:blue">p</span>, <span style="color:blue">q</span>, <span style="color:blue">r</span>, <span style="color:blue">s</span>, <span style="color:blue">t</span>, <span style="color:blue">u</span>, <span style="color:blue">v</span>, <span style="color:blue">w</span>, <span style="color:blue">x</span>, <span style="color:blue">y</span>, <span style="color:blue">z</span>, <span style="color:blue">{</span>, <span style="color:blue">|</span>, <span style="color:blue">}</span>, <span style="color:blue">~</span>, character with code 0x7F, character with code 0x80, character with code 0x81, character with code 0x82, character with code 0x83, character with code 0x84, character with code 0x85, character with code 0x86, character with code 0x87, character with code 0x88, character with code 0x89, character with code 0x8A, character with code 0x8B, character with code 0x8C, character with code 0x8D, character with code 0x8E, character with code 0x8F, character with code 0x90, character with code 0x91, character with code 0x92, character with code 0x93, character with code 0x94, character with code 0x95, character with code 0x96, character with code 0x97, character with code 0x98, character with code 0x99, character with code 0x9A, character with code 0x9B, character with code 0x9C, character with code 0x9D, character with code 0x9E, character with code 0x9F, character with code 0xA0, <span style="color:blue">¡</span>, <span style="color:blue">¢</span>, <span style="color:blue">£</span>, <span style="color:blue">¤</span>, <span style="color:blue">¥</span>, <span style="color:blue">¦</span>, <span style="color:blue">§</span>, <span style="color:blue">¨</span>, <span style="color:blue">©</span>, <span style="color:blue">ª</span>, <span style="color:blue">«</span>, <span style="color:blue">¬</span>, character with code 0xAD, <span style="color:blue">®</span>, <span style="color:blue">¯</span>, <span style="color:blue">°</span>, <span style="color:blue">±</span>, <span style="color:blue">²</span>, <span style="color:blue">³</span>, <span style="color:blue">´</span>, <span style="color:blue">µ</span>, <span style="color:blue">¶</span>, <span style="color:blue">·</span>, <span style="color:blue">¸</span>, <span style="color:blue">¹</span>, <span style="color:blue">º</span>, <span style="color:blue">»</span>, <span style="color:blue">¼</span>, <span style="color:blue">½</span>, <span style="color:blue">¾</span>, <span style="color:blue">¿</span>, <span style="color:blue">À</span>, <span style="color:blue">Á</span>, <span style="color:blue">Â</span>, <span style="color:blue">Ã</span>, <span style="color:blue">Ä</span>, <span style="color:blue">Å</span>, <span style="color:blue">Æ</span>, <span style="color:blue">Ç</span>, <span style="color:blue">È</span>, <span style="color:blue">É</span>, <span style="color:blue">Ê</span>, <span style="color:blue">Ë</span>, <span style="color:blue">Ì</span>, <span style="color:blue">Í</span>, <span style="color:blue">Î</span>, <span style="color:blue">Ï</span>, <span style="color:blue">Ð</span>, <span style="color:blue">Ñ</span>, <span style="color:blue">Ò</span>, <span style="color:blue">Ó</span>, <span style="color:blue">Ô</span>, <span style="color:blue">Õ</span>, <span style="color:blue">Ö</span>, <span style="color:blue">×</span>, <span style="color:blue">Ø</span>, <span style="color:blue">Ù</span>, <span style="color:blue">Ú</span>, <span style="color:blue">Û</span>, <span style="color:blue">Ü</span>, <span style="color:blue">Ý</span>, <span style="color:blue">Þ</span>, <span style="color:blue">ß</span>, <span style="color:blue">à</span>, <span style="color:blue">á</span>, <span style="color:blue">â</span>, <span style="color:blue">ã</span>, <span style="color:blue">ä</span>, <span style="color:blue">å</span>, <span style="color:blue">æ</span>, <span style="color:blue">ç</span>, <span style="color:blue">è</span>, <span style="color:blue">é</span>, <span style="color:blue">ê</span>, <span style="color:blue">ë</span>, <span style="color:blue">ì</span>, <span style="color:blue">í</span>, <span style="color:blue">î</span>, <span style="color:blue">ï</span>, <span style="color:blue">ð</span>, <span style="color:blue">ñ</span>, <span style="color:blue">ò</span>, <span style="color:blue">ó</span>, <span style="color:blue">ô</span>, <span style="color:blue">õ</span>, <span style="color:blue">ö</span>, <span style="color:blue">÷</span>, <span style="color:blue">ø</span>, <span style="color:blue">ù</span>, <span style="color:blue">ú</span>, <span style="color:blue">û</span>, <span style="color:blue">ü</span>, <span style="color:blue">ý</span>, <span style="color:blue">þ</span>, <span style="color:blue">ÿ</span>;'),
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
          array('(a','<span style="color:red">Regex syntax error: missing a closing parenthesis \')\' for the opening parenthesis in position 0.</span> Operands: <span style="color:blue">a</span>'),
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


/*class qtype_preg_description_dumping_test extends PHPUnit_Framework_TestCase {
    /**
     * @dataProvider vardump_provider
     */
    /*public function test_vardump($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }
    
    public function vardump_provider()
    {
        return array(
          array(' \t\n\r','')
        );
    }
}*/

