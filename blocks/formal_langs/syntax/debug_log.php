<?php
// This file is part of Formal Languages block - https://code.google.com/p/oasychev-moodle-plugins/
//
// Formal Languages block is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Formal Languages block is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Formal Languages block.  If not, see <http://www.gnu.org/licenses/>.
/**
 * A simple debug log, which will be used to tackle some incorrect runs
 * and output debug stuff
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Mamontov Dmitriy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once($CFG->dirroot.'/lib/outputcomponents.php');

class block_formal_langs_debug_log {
    /**
     * A log messages, which are defined as lines
     * @var  array of string
     */
    public static $strings = array();
    /**
     * Whether we should output some log, when called dump_log()
     * Override it when we need some debug data
     * @var bool
     */
    public static $debug = false;

    /**
     * Returns a placeholder data
     * @param array $args of mixed
     * @return array of string - placeholders
     */
    private function get_names($args) {
        $names = array();
        for($i = 0; $i < count($args); $i++) {
            $names[] = '%' . $i;
        }
        return $names;
    }
    /**
     * Interpolates a message. Partial copy of ::log, because moving stuff from other function
     * @param $message
     * @return string interpolated message
     */
    public function get_log_message($message) {
        $args = func_get_args();
        array_shift( $args );
        $m = str_replace($this->get_names($args), $args, $message);
        return $m;
    }
    /**
     * Adds into a string new string. You can support an additional arguments in
     * interpolation, thus replacing %1-%{number} references in arguments
     * @param string $message a message string.
     */
    public function log($message) {
        $args = func_get_args();
        array_shift( $args );
        $names = array();
        for($i = 0; $i < count($args); $i++) {
            $names[] = '%' . $i;
        }
        $m = str_replace($this->get_names($args), $args, $message);
        block_formal_langs_debug_log::$strings[] = $m;
    }

    /**
     * Dumps whole log string, using echo
     */
    public function dump_log() {
        if (block_formal_langs_debug_log::$debug) {
            $br = html_writer::empty_tag('br') . PHP_EOL;
            $a = implode($br, block_formal_langs_debug_log::$strings);
            $a .= $br;
            echo $a;
        }
    }
}