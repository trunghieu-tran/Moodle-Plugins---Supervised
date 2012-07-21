<?

/**
 * Unit tests for (some of) question/type/preg/question.php.
 *
 * @copyright &copy; 2012 Pahomov Dmitry
 * @author Oleg Sychev
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_description.php');

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
          array('[^[^:word:]abc\pL]','any symbol except the following: not \w AND [:word:], Letter, <span style="color:red">a</span>, <span style="color:red">b</span>, <span style="color:red">c</span>;'),
          array('a','<span style="color:red">a</span>'),
          array('[^a]','not <span style="color:red">a</span>'),
          array('\w','\w AND [:word:]'),
          array('\W','not \w AND [:word:]'),
        );
    }
    
    public function test_meta()
    {
        $handler = new qtype_preg_author_tool_description('a|b|',null,null);
        $result = $handler->description('%s','%s');
        $expected = 'sdas';
        $this->assertEquals($result, $expected);
    }
    
    /**
     * @dataProvider assert_provider
     */
    public function test_assert($regex, $expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        var_dump($handler);
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
}
