<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines preg_php_matcher class, the matching engine based on php preg extension.
 * It supports more complicated regular expressions possible with great speed, but doesn't allow partial matching and hintings
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

class qtype_preg_php_preg_matcher extends qtype_preg_matcher {

    public function __construct($regex = null, $options = null) {
        // This matcher sometimes use common lexer/parser to count subexpressions, since preg_match don't always return them all.
        // We need to be sure it uses PCRE strict parsing mode and don't generate any additional error messages.
        if (is_object($options)) {
            $options->pcrestrict = true;
        }
        parent::__construct($regex, $options);
    }

    public function is_supporting($capability) {
        switch ($capability) {
        case qtype_preg_matcher::SUBEXPRESSION_CAPTURING :
            return true;
            break;
        }
        return false; // Native matching doesn't support any partial matching capabilities.
    }

    public function name() {
        return 'php_preg_matcher';
    }

    /**
     * Returns supported modifiers as bitwise union of constants MODIFIER_XXX.
     */
    public function get_supported_modifiers() {
        return parent::get_supported_modifiers(); // TODO: imsxeADSUX.
    }

    /**
     * Does this engine need a parsing of regular expression?
     * @return bool if parsing needed
     */
    protected function is_parsing_needed() {
        // We need parsing if option is set for capture subexpressions.
        return $this->options->capturesubexpressions || $this->options->parsetemplates;
    }

    protected function is_preg_node_acceptable($pregnode) {
        return true;    // We actually need tree only for subexpressions, so accept anything.
    }

    /**
     * Check regular expression for errors.
     * @return bool is tree accepted.
     */
    protected function accept_regex() {

        // Clear away errors from parser - we don't really need them...
        // TODO improve this ugly hack to save modifier errors or create conversion from native to PCRE Strict.
        // $this->errors = array();

        $regex = $this->regex;
        if (strpos($regex, '/') !== false) {// Escape any slashes.
            $regex = implode('\/', explode('/', $regex));
        }
        if (!$this->options->is_modifier_set(qtype_preg_handling_options::MODIFIER_EXTENDED)) { // Avoid newlines in non-extended mode.
            $regex = qtype_poasquestion\string::replace("\n", '', $regex);
        }
        $regex = '/' . $regex . '/u';

        if (preg_match($regex, 'test') === false) {// Function preg_match returns false when regular expression contains error.
            $this->errors[] = new qtype_preg_error(get_string('error_PCREincorrectregex', 'qtype_preg'), '',
                                                   new qtype_preg_position(-2, -2, -2, -2, -2, -2), true);  // Preserve error message to show the link.
            return false;
        }

        return true;
    }

    /**
     * Do real matching.
     * @param str a string to match.
     */
    protected function match_inner($str) {
        // Prepare results.
        $matchresults = new qtype_preg_matching_results();
        $matchresults->set_source_info($str, $this->get_max_subexpr(), $this->get_subexpr_name_to_number_map());// Needed to invalidate match correctly.
        $matchresults->invalidate_match();

        // Preparing regexp.
        $regex = $this->regex;
        // Enclose
        if (strpos($regex, '/') !== false) {// Escape any slashes.
            $regex = implode('\/', explode('/', $regex));
        }
        if ($this->options->exactmatch) {
        // Add characters to regex in exact match mode, since adding nodes to the tree won't affects preg_match.
        // Using grouping to not interfere with user's subexpressions numbering, but in case regex contains top-level alternatives.
        // Adding line break in the end, since in extended notation regex may end on the comment; in other notations it would be deleted anyway.
            $regex = '^(?:' . $regex . "\n)$";
        }
        if (!$this->options->is_modifier_set(qtype_preg_handling_options::MODIFIER_EXTENDED)) { // Avoid newlines in non-extended mode.
            $regex = qtype_poasquestion\string::replace("\n", '', $regex);
        }
        $regex = '/' . $regex . '/u';
        $regex .= $this->options->modifiers_to_string();

        // Do matching.
        $matches = array();
        // No need to find all matches since preg_match don't return partial matches, any full match is sufficient.
        $full = preg_match($regex, $str, $matches, PREG_OFFSET_CAPTURE);
        // $matches[0] - match with the whole regexp, $matches[1] - first subexpression etc.
        // $matches[$i] format is array(0=> match, 1 => offset of this match).
        if ($full) {
            $matchresults->full = true;// No partial matching from preg_match.
            foreach ($matches as $i => $match) {
                $matchresults->indexfirst[$i] = $match[1];
                if ($match[1] !== -1) {
                    $matchresults->length[$i] = qtype_preg_unicode::strlen($match[0]);
                } else {
                    $matchresults->length[$i] = qtype_preg_matching_results::NO_MATCH_FOUND;
                }
            }
            // Show selection if it is equivalent to some capturing subexpression.
            // TODO: find a way to show selection when it's not equivalent to capturing subexpression, without interfering with subpattern numbering.
            if ($this->selectednode !== null && is_a($this->selectednode, 'qtype_preg_node_subexpr') && $this->selectednode->number > -1) {
                // There is selection and it is capturing subexpression.
                $matchresults->indexfirst[-2] = $matchresults->indexfirst[$this->selectednode->number];
                $matchresults->length[-2] = $matchresults->length[$this->selectednode->number];
            }
        }

        return $matchresults;
    }
}
