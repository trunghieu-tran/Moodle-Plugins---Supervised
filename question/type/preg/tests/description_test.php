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
require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');

class qtype_preg_description_test extends PHPUnit_Framework_TestCase {
    function test_first() {
        $rh = new qtype_preg_regex_handler('(?=.*\d)(?=.*[a-z])(?=.*[A-Z@#$%])(?=.*[@#$%]).{6,20}');
        echo var_dump($rh);
    }
}
