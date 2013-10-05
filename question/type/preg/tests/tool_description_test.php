<?php

/**
 * Tests for /question/type/preg/authoring_tools/preg_description_tool.php.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Pahomov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_description_tool.php');

class qtype_preg_tool_description_test extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider condmask_provider
     */
    public function test_condmask($regex,$expected_en,$expected_ru)
    {
        $handler = new qtype_preg_description_tool($regex);
        //if($regex == '(?(?=a)b)' )var_dump($handler->dst_root);
        $result = $handler->description('%s','%s');
        $this->assertEquals($expected_en, $result);
    }

    public function condmask_provider()
    {
        return array(
          array('(?(?=a)b|c)','if further text matches: [<span style="color:blue">a</span>] then check: [<span style="color:blue">b</span>] else check: [<span style="color:blue">c</span>]','рус - TODO'),
          array('(?(?!a)b|c)','if further text does not match: [<span style="color:blue">a</span>] then check: [<span style="color:blue">b</span>] else check: [<span style="color:blue">c</span>]','рус - TODO'),
          array('(?(?<=a)b|c)','if preceding text matches: [<span style="color:blue">a</span>] then check: [<span style="color:blue">b</span>] else check: [<span style="color:blue">c</span>]','рус - TODO'),
          array('(?(?<!a)b|c)','if preceding text does not match: [<span style="color:blue">a</span>] then check: [<span style="color:blue">b</span>] else check: [<span style="color:blue">c</span>]','рус - TODO'),
          array('(?(?=a)b)','if further text matches: [<span style="color:blue">a</span>] then check: [<span style="color:blue">b</span>]','рус - TODO'),
          array('(?(1)a)','if the subexpression #1 has been successfully matched then check: [<span style="color:blue">a</span>]','рус - TODO'),
          array('(?(name)a)','if the subexpression "name" has been successfully matched then check: [<span style="color:blue">a</span>]','рус - TODO'),
          array('(?(<name>)a)','if the subexpression "name" has been successfully matched then check: [<span style="color:blue">a</span>]','рус - TODO'),
          array('(?(<name>)a|b)','if the subexpression "name" has been successfully matched then check: [<span style="color:blue">a</span>] else check: [<span style="color:blue">b</span>]','рус - TODO'),
          array('(?(DEFINE)(?<name>a))','definition of subexpression #1: [<span style="color:blue">a</span>]','рус - TODO'),
        );
    }
}


class qtype_preg_description_dumping_test extends PHPUnit_Framework_TestCase {
    public function test_vardump()
    {
        $regex = '(?i)[\xff\x00-\x1fA-B\t\n]';
        $expected = '000';
        //var_dump($options);
        $handler = new qtype_preg_description_tool($regex);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($expected_en, $result);
    }
}

/*
[\xff\x00-\x1fA-B\t\n]

  ["userinscription"]=>
  array(3) {
    [0]=>
    string(14) "\xff\x00-\\t\n"
    [1]=>
    string(3) "-"
    [2]=>
    string(3) "A-B"
  }
*/

