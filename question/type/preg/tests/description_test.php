<?

/**
 * Unit tests for (some of) question/type/preg/question.php.
 *
 * @copyright &copy; 2012 Pahomov Dmitry
 * @author Pahomov Dmitry
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/author_tool_description/preg_description.php');

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
          array('[^[:^word:]abc\pL]','any symbol except the following: not \w AND [:word:], Letter, <span style="color:red">a</span>, <span style="color:red">b</span>, <span style="color:red">c</span>;'),
          array('a','<span style="color:red">a</span>'),
          array('[^a]','not <span style="color:red">a</span>'),
          array('\w','\w AND [:word:]'),
          array('\W','not \w AND [:word:]'),
          array('[[:word:]\pL[:print:]]','fgh'),
        );
    }
    
    /*public function test_meta()
    {
        $handler = new qtype_preg_author_tool_description('a|b|',null,null);
        $result = $handler->description('%s','%s');
        $expected = 'sdas';
        $this->assertEquals($result, $expected);
    }*/
    
    /**
     * @dataProvider assert_provider
     */
    public function test_assert($regex, $expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
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
    
    public function test_backref()
    {
        $handler = new qtype_preg_author_tool_description('\1',null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $expected = 'back reference to subpattern #1';
        $this->assertEquals($result, $expected);
    }
    
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
          array('ab','<span style="color:red">a</span> then <span style="color:red">b</span>'),
          array('[a|b]c','one of the following characters: <span style="color:red">a</span>, <span style="color:red">|</span>, <span style="color:red">b</span>; then <span style="color:red">c</span>'),
          array('abc','<span style="color:red">a</span> then <span style="color:red">b</span> then <span style="color:red">c</span>'),
        );
    }
    
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
          array('a|b','<span style="color:red">a</span> or <span style="color:red">b</span>'),
          array('a|b|','<span style="color:red">a</span> or <span style="color:red">b</span> or nothing'),
          array('a|b|c','<span style="color:red">a</span> or <span style="color:red">b</span> or <span style="color:red">c</span>'),
        );
    }
}
