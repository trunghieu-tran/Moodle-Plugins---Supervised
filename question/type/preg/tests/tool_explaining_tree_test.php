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
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_tree_tool.php');
require_once($CFG->dirroot . '/question/type/preg/preg_dotstyleprovider.php');


class qtype_preg_tool_explaining_tree_test extends PHPUnit_Framework_TestCase {

    function test_dummy() {
       $tree = new qtype_preg_explaining_tree_tool('a');
       var_dump($tree->get_dst_root()->dot_script(new qtype_preg_dot_node_context(true)));
    }

    function test_concat() {
       $tree = new qtype_preg_explaining_tree_tool('ab');
       var_dump($tree->get_dst_root()->dot_script(new qtype_preg_dot_node_context(true)));
    }

    function test_something() {
        $tree = new qtype_preg_explaining_tree_tool('(kind(?:a| of) regex)');
    }
 }

?>
