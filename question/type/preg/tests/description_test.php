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
        $result = (string)($handler->description('<span class="description_node_%n%o">%s</span>',' operand','%s'));
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
}
