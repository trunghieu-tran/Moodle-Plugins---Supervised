<?php

/**
 * Unit tests for explain graph tool.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Terechov Grigory <grvlter@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_syntax_tree_tool.php');

class qtype_preg_tool_syntax_tree_test extends PHPUnit_Framework_TestCase {

    function test_something() {
        $tree = new qtype_preg_syntax_tree_tool('(?:(a{6,6})|([^b-f]))(?(2)A)\1+[f\dgjf\w]f');
        var_dump($tree->get_dst_root()->dot_script(new qtype_preg_dot_node_context(true)));
    }
 }

?>
