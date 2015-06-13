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
 * Defines an abstract regular expression matcher, extend it to create a new matching engine.
 * A matcher is a particulary important type of regex handlers, that allows the question to work at all.
 * The file also defines a class to store matching results.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

class qtype_preg_matching_results {

    // No match captured.
    const NO_MATCH_FOUND = -1;
    // No next character generated.
    const UNKNOWN_NEXT_CHARACTER = -1;
    // How many characters left is unknown.
    const UNKNOWN_CHARACTERS_LEFT = 999999999;

    //      Match data.
    /** @var boolean Is match full or partial? */
    public $full;
    /** @var array Indexes of first matched character - array where 0 => full match, 1=> first subexpression etc. */
    public $indexfirst;
    /** @var array Length of the matches - array where 0 => full match, 1=> first subexpression etc. */
    public $length;
    /** @var integer The number of characters left to complete matching. */
    public $left;
    /** @var object of qtype_preg_matching_results, containing string extended to give more close match than this ($this->extededmatch->left <= $this->left).
     *
     * There are several ways this string could be generated:
     * add characters to the end of matching part (indexfirst[0]+length[0]);
     * add characters before the end of matching part if it is impossible to complete match from the current point of match fail;
     * just delete unmatched tail if match failed on the $ assertion.
     * Should be null if not generated.
     */
    public $extendedmatch;
    /** @var integer Start index for the added characters in extendedmatch object.
     *
     * May be less than indexfirst[0]+length[0] if there is no way to complete matching
     * from current point of fail due to assertions, backreferences or another reasons.
     * This field is filled by qtype_preg_matching_results::validate() and should not be set by matching engine.
     */
    public $extensionstart;

    //      Source data.
    /** @var qtype_poasquestion\string A string being matched. */
    protected $str;
    /** @var integer Max number of a subexpression available in regular expression. */
    protected $maxsubexpr;
    /** @var array A map where keys are subexpression names and values are their numbers. */
    protected $subexprmap;

    public function __construct($full = false, $indexfirst = array(), $length = array(), $left = self::UNKNOWN_CHARACTERS_LEFT, $extendedmatch = null) {
        $this->full = $full;
        $this->indexfirst = $indexfirst;
        $this->length = $length;
        $this->left = $left;
        $this->extendedmatch = $extendedmatch;
        $this->extensionstart = self::NO_MATCH_FOUND;
    }

    /**
     * Sets info about string and regular expression, that is needed for some functions to work.
     */
    public function set_source_info($str = null, $maxsubexpr = 0, $subexprmap = array()) {
        $this->str = clone $str;
        $this->maxsubexpr = $maxsubexpr;
        $this->subexprmap = $subexprmap;
    }

    public function str() {
        return $this->str;
    }

    /**
     * Returns keys for all subexpressions in regular expression.
     *
     * Use to enumerate subexpressions.
     */
    public function all_subexpressions() {
        // Merge all numeric subexpression keys with named subexpressions from $subexprmap.
        return array_merge(array_keys($this->indexfirst), array_keys($this->subexprmap));
    }

    /**
     * Return subexpression index in the indexfirst and length arrays.
     *
     * If it is subexpression name, use $subexprmap to find appropriate index,
     * otherwise (numbered subexpression) just return $subexpression.
     */
    public function subexpression_number($subexpression) {
        if (isset($this->subexprmap[$subexpression])) {// Named subexpression.
            return $this->subexprmap[$subexpression];
        }
        return $subexpression;
    }

    public function index_first($subexpression = 0) {
        $subexpression = $this->subexpression_number($subexpression);
        return $this->indexfirst[$subexpression];
    }

    public function length($subexpression = 0) {
        $subexpression = $this->subexpression_number($subexpression);
        return $this->length[$subexpression];
    }

    public function is_subexpr_captured($subexpression) {
        return $this->length($subexpression) !== self::NO_MATCH_FOUND;
    }

    /**
     * Any match found?
     * The match considered found if at least one character is matched or there is full match of zero length (regex with just asserts).
     */
    public function is_match() {
        if (isset($this->length[0])) {
            return $this->full || ($this->length[0] > 0);
        } else {// No matching resutls at all.
            return false;
        }
    }

    /**
     * Returns true if there could be no better matching result, so we could stop loop looking for best match.
     *
     * For now the first (leftmost) full match is enought.
     */
    public function best() {
        return $this->full;
    }

    /**
     * Compares two matching results and returns true if this result is worse than passed by argument.
     *
     * @param other object of qtype_preg_matching_results.
     * @param orequal make it worse-or-equal function.
     * @param longestmatch defines what result is preferable - with more characters matched or with less characters to complete match.
     * @param areequal reference to a variable to store boolean value - whether the objects are equal.
     * @return whether @this is worse than $other.
     */
    public function worse_than($other, $orequal = false, $longestmatch = false, &$areequal = null) {

        if ($areequal !== null) {
            $areequal = false;
        }

        // 1. The match is definitely best (full match).
        if (!$this->best() && $other->best()) {
            return true;
        } else if ($this->best() && !$other->best()) {
            return false;
        }

        // 2. Is match.
        if (!$this->is_match() && $other->is_match()) {
            return true;
        } else if ($this->is_match() && !$other->is_match()) {
            return false;
        }

        if (!$longestmatch) {
            // 3. Less characters left.
            if ($other->left < $this->left) {
                return true;
            } else if ($this->left < $other->left) {
                return false;
            }

            // 4. Longest match.
            if ($other->length[0] > $this->length[0]) {
                return true;
            } else if ($this->length[0] > $other->length[0]) {
                return false;
            }
        } else {
            // 3. Longest match.
            if ($other->length[0] > $this->length[0]) {
                return true;
            } else if ($this->length[0] > $other->length[0]) {
                return false;
            }

            // 4. Less characters left.
            if ($other->left < $this->left) {
                return true;
            } else if ($this->left < $other->left) {
                return false;
            }

        }

        if ($areequal !== null) {
            $areequal = true;
        }
        return $orequal;// Results are equal.
    }

    /**
     * Invalidates match by setting all data to no match values.
     */
    public function invalidate_match() {
        $this->full = false;
        // $this->left = self::UNKNOWN_CHARACTERS_LEFT;
        $this->indexfirst = array();
        $this->length = array();
        for ($i = 0; $i <= $this->maxsubexpr; $i++) {
            $this->indexfirst[$i] = self::NO_MATCH_FOUND;
            $this->length[$i] = self::NO_MATCH_FOUND;
        }
        $this->extensionstart = self::NO_MATCH_FOUND;
        if ($this->extendedmatch !== null) {
            $this->extendedmatch->extensionstart = 0;// If there is no match, extension should start from the beginning of the string.
        }
    }

    /**
     * Throws exception if match results contain obvious abnormalities.
     * Also compute extensionstart field.
     */
    public function validate() {
        if ($this->is_match()) {// Match found.
            if (!isset($this->indexfirst[0]) || !isset($this->length[0])
                || $this->indexfirst[0] === self::NO_MATCH_FOUND || $this->length[0] === self::NO_MATCH_FOUND) {
                throw new qtype_preg_exception('Error: match was found but no match information returned');
            }

            // Check that each subexpression lies inside overall match.
            // TODO - decide, what to do with this check.
            // The problem is subexpression inside assertion - it can have match outside of main match.
            /*foreach ($this->indexfirst as $i => $start) {
                if ($start === self::NO_MATCH_FOUND) {
                    // No need to check subexpression that wasn't matched.
                    break;
                }
                if ($start < $this->indexfirst[0] || $start > $this->indexfirst[0] + $this->length[0]) {
                    throw new qtype_preg_exception('Error: '.$i.' subexpression start '.$start.' doesn\'t lie between match start '.
                        $this->indexfirst[0].' and end '.($this->indexfirst[0] + $this->length[0]));
                }
                $end = $start + $this->length[$i];
                if ($end < $this->indexfirst[0] || $end > $this->indexfirst[0] + $this->length[0]) {
                    throw new qtype_preg_exception('Error: '.$i.' subexpression end '.$end.' doesn\'t lie between match start '.
                        $this->indexfirst[0].' and end '.($this->indexfirst[0] + $this->length[0]));
                }
            }*/
        }
        // Calculate extension start comparing existing and extended strings.
        // We could find it looking for the first different character in two strings.
        if (!$this->full && is_object($this->extendedmatch)) {
            // Find out extenstion start comparing two strings.
            $str1 = $this->str;
            $str2 = $this->extendedmatch->str;

            for ($i = 0; $i <= $str1->length(); $i++) {
                // One of the string ended or characters are different.
                if ($i >= core_text::strlen($str2) || $i >= $str1->length() || $str1[$i] != $str2[$i]) {
                    $this->extensionstart = $i;
                    $this->extendedmatch->extensionstart = $i;
                    break;
                }
            }
        } else if ($this->full && $this->extensionstart === self::NO_MATCH_FOUND) {
            $this->extensionstart = $this->indexfirst[0] + $this->length[0];
        }

        if (is_object($this->extendedmatch)) {
            $this->extendedmatch->validate();
        }
    }

    /**
     * Returns non-matched heading before subexpression match.
     */
    public function match_heading($subexpression = 0) {
        $subexpression = $this->subexpression_number($subexpression);
        $wronghead = new qtype_poasquestion\string('');
        if ($this->is_match()) {// There is match.
            if ($this->indexfirst[$subexpression] > 0) {// If there is wrong heading.
                $wronghead = $this->str->substring(0, $this->indexfirst[$subexpression]);
            }
        } else {// No match, assuming all string is wrong heading (to display hint after it).
            $wronghead = $this->str;
        }
        return $wronghead->string();
    }

    /**
     * Returns matched part of the string for given subexpression.
     */
    public function matched_part($subexpression = 0) {
        $subexpression = $this->subexpression_number($subexpression);
        $correctpart = new qtype_poasquestion\string('');
        if ($this->is_match()) {// There is match.
            if (isset($this->indexfirst[$subexpression]) && $this->indexfirst[$subexpression] !== self::NO_MATCH_FOUND) {
                $correctpart = $this->str->substring($this->indexfirst[$subexpression], $this->length[$subexpression]);
            }
        }
        return $correctpart->string();
    }

    /**
     * Returns non-matched tail after subexpression match.
     */
    public function match_tail($subexpression = 0) {
        $subexpression = $this->subexpression_number($subexpression);
        $wrongtail = new qtype_poasquestion\string('');
        if ($this->is_match()) {// There is match.
            if ($this->indexfirst[$subexpression] + $this->length[$subexpression] < core_text::strlen($this->str) &&
                $this->length[$subexpression]!== self::NO_MATCH_FOUND) {// If there is wrong tail.
                $wrongtail = $this->str->substring($this->indexfirst[$subexpression] + $this->length[$subexpression], $this->str->length() -
                        $this->indexfirst[$subexpression] - $this->length[$subexpression]);
            }
        }
        return $wrongtail->string();
    }

    /**
     * Returns correct part before hint.
     */
    public function correct_before_hint() {
        $correctbeforehint = new qtype_poasquestion\string('');
        if ($this->is_match()) {// There is match.
            $correctbeforehint = $this->str->substring($this->indexfirst[0], $this->extensionstart - $this->indexfirst[0]);
        }
        return $correctbeforehint->string();
    }

    /**
     * Returns tail after point where extension is started.
     */
    public function tail_to_delete() {
        $wrongtail = new qtype_poasquestion\string('');
        if ($this->is_match()) {// There is match.
            if ($this->extensionstart < $this->str->length() && $this->length[0]!== self::NO_MATCH_FOUND) {// If there is wrong tail.
                $wrongtail = $this->str->substring($this->extensionstart, $this->str->length() - $this->extensionstart);
            }
        }
        return $wrongtail->string();
    }

    /**
     * Returns part of the string, added by matcher.
     */
    public function string_extension() {
        $extension = new qtype_poasquestion\string('');
        if ($this->extendedmatch !== null) {
            $extendedstr = $this->extendedmatch->str();
            if ($this->extendedmatch->extensionstart < $extendedstr->length()) {
                $extension = $extendedstr->substring($this->extendedmatch->extensionstart, $extendedstr->length() - $this->extendedmatch->extensionstart);
            } else {
                $extension = new qtype_poasquestion\string('');
            }
        }
        return $extension->string();
    }
}

/**
 * Options, used to specify matching process.
 */
class qtype_preg_matching_options extends qtype_preg_handling_options {

    /** @var boolean Should matcher merge assertions? */
    public $mergeassertions = false;
    /** @var boolean Should matcher try to generate extension? */
    public $extensionneeded = true;
    /** @var string Unicode property name for preferred alphabet for \w etc when generating extension.*/
    public $preferredalphabet = null;
    /** @var string Unicode property name for preferred characters for dot meta-character when generating extension.*/
    public $preferfordot = null;

    /** @var boolean Should matcher look for subexpression captures or the whole match only? */
    // TODO - does we need to specify subexpressions we are looking for or there is no sense in it?
    public $capturesubexpressions = true;
}

/**
 * Class with information about regular expression anchoring.
 */
class qtype_preg_regex_anchoring {

    // TODO - comment accurately before every field which asserts under which modifiers will lead to it!
    /** @var boolean Regex anchored from start. */
    public $start = false;
    /** @var boolean Regex anchored from start and after each line break.*/
    public $startlinebreak = false;
    /** @var boolean Regex anchored from end.*/
    public $end = false;
    /** @var boolean Regex anchored from end and before each line break.*/
    public $endlinebreak = false;
}

/**
 * Abstract base class for regular expression matcher.
 */
class qtype_preg_matcher extends qtype_preg_regex_handler {

    // Constants for the capabilities which could (or could not) be supported by matching engine.
    // Partial matching (returning the index of last matched character).
    const PARTIAL_MATCHING = 0;
    // Returning next possible character(s) after partial match.
    const CORRECT_ENDING = 1;
    // Returning the smallest number of characters that needed to complete partial match.
    const CHARACTERS_LEFT = 2;
    // Subexpression capturing during matching.
    const SUBEXPRESSION_CAPTURING = 3;
    // Always return full match as the correct ending (if at all possible).
    const CORRECT_ENDING_ALWAYS_FULL = 4;

    /**
     * Returns true for supported capabilities.
     * @param capability the capability in question.
     * @return bool is capability supported.
     */
    public function is_supporting($capability) {
        return false;// Abstract class supports nothing.
    }

    // Matching results as qtype_preg_matching_results object.
    protected $matchresults;
    // Cache of the matching results,  string for matching is the key.
    protected $resultcache;
    // Anchoring - object, with 'start' and 'end' logical fields, which are true if all regex is anchored.
    protected $anchor;

    public function name() {
        return 'preg_matcher';
    }

    /**
     * Parse regex and do all necessary preprocessing.
     * @param regex - regular expression to handle.
     * @param options - options to handle regex, object of qtype_preg_matching_options class.
     */
    public function __construct($regex = null, $options = null) {
        // Set matching data empty.
        $this->matchresults = new qtype_preg_matching_results();
        $this->resultcache = array();

        // Options should exist at least as a default object. Be sure to create matching options.
        if ($options === null) {
            $options = new qtype_preg_matching_options();
        }

        // Do parsing.
        parent::__construct($regex, $options);
        if ($regex === null) {
            return;
        }

        if ($this->astroot !== null && !$this->errors_exist()) {
            $this->look_for_anchors();
        }

        // If there were backreferences in regex, subexpression capturing should be forced.
        if ($this->lexer !== null && !$this->options->capturesubexpressions) {
            $this->options->capturesubexpressions = (count($this->lexer->get_nodes_with_subexpr_refs()) > 0);
        }

        // Invalidate match called later to allow parser to count subexpression.
        $this->matchresults->set_source_info(new qtype_poasquestion\string(''), $this->get_max_subexpr(), $this->get_subexpr_name_to_number_map());
        $this->matchresults->invalidate_match();
    }

    /**
     * Overloaded from qtype_preg_regex_handler.
     */
    protected function add_selection_nodes($oldroot) {
        $newroot = parent::add_selection_nodes($oldroot);
        if ($this->selectednode === null) {
            return $newroot;
        }

        $parent = $this->find_parent_node($newroot, $this->selectednode);
        $subexpression = new qtype_preg_node_subexpr(qtype_preg_node_subexpr::SUBTYPE_SUBEXPR, -2);
        $subexpression->subpattern = -2;

        if ($parent === null) {
            // Replace the AST root.
            $subexpression->operands[] = $newroot;
            return $subexpression;
        }

        // Just insert a subexpression.
        $subexpression->operands[] = $this->selectednode;
        foreach ($parent->operands as $key => $operand) {
            if ($operand === $this->selectednode) {
                $parent->operands[$key] = $subexpression;
                break;
            }
        }
        return $newroot;
    }

    /**
     * Returns an object of match results, helper method.
     */
    public function get_match_results() {
        return $this->matchresults;
    }

    /**
     * Match regular expression with given string, calls match_inner from a child class to do the real matching.
     * @param str a string to match.
     * @return object of qtype_preg_matching_results class.
     */
    public function match($str) {

        // Are there any errors?
        if ($this->errors_exist()) {
            throw new qtype_preg_exception('Error: trying to do matching on regex with errors!');
        }

        // Are results cached already?
        if (isset($this->resultcache[$str])) {
            $this->matchresults = $this->resultcache[$str];
        } else {
            $str = new qtype_poasquestion\string($str);
            // Reset match data and perform matching.
            $this->matchresults = $this->match_inner($str);
            // Save source data for the match.
            $this->matchresults->set_source_info($str, $this->get_max_subexpr(), $this->get_subexpr_name_to_number_map());

            // Set all string as incorrect if there were no matching.
            if (!$this->matchresults->is_match()) {
                $this->matchresults->invalidate_match();
            }
            // Do some sanity checks and calculate necessary fields.
            $this->matchresults->validate();

            // Save results to the cache.
            $this->resultcache[$str->string()] = $this->matchresults;
        }
        return $this->matchresults;
    }

    /**
     * Perform a match of string from specified offset.
     *
     * Should be implemented by child classes that use custom matching algorithms.
     * @param str a string to match.
     * @param offset position from where to match.
     * @return qtype_preg_matching_results object.
     */
    public function match_from_pos($str, $offset) {
        throw new qtype_preg_exception('Error: matching has not been implemented for '.$this->name().' class');
    }

    public function is_regex_anchored($start = true, $linebreak = true) {
        if ($start) {
            if ($linebreak) {
                return $this->anchor->start && $this->anchor->startlinebreak;
            } else {
                return $this->anchor->start;
            }
        } else {
            if ($linebreak) {
                return $this->anchor->end && $this->anchor->endlinebreak;
            } else {
                return $this->anchor->end;
            }
        }
    }

    /**
     * Do real matching.
     *
     * This function should be re-implemented in child classes using standard matching functions.
     * that already contains starting positions loop inside. Implement match_from_pos otherwise.
     * @param qtype_poasquestion\string str a string to match.
     * @return qtype_preg_matching_results object.
     */
    protected function match_inner($str) {

        $result = $this->match_preprocess($str);
        if (is_a($result, 'qtype_preg_matching_results')) {
            return $result;
        }

        $result = new qtype_preg_matching_results();
        $result->set_source_info($str, $this->get_max_subexpr(), $this->get_subexpr_name_to_number_map());
        $result->invalidate_match();

        if ($this->anchor->start) {
            // The regex is anchored from start, so we really should check only start of the string and every line break if necessary.
            // Results for other offsets would be same.
            $rightborders = array(0);
            if ($this->anchor->startlinebreak) {// Looking for line breaks.
                $offset = 0;
                $pos = core_text::strpos($str, "\n", $offset);
                while ($pos !== false) {
                    $rightborders[] = $pos + 1;// Starting matching after line break.
                    $offset = $pos + 1;
                    $pos = core_text::strpos($str, "\n", $offset);
                }
            }
            // Starting positions loop.
            foreach ($rightborders as $i) {
                $tmp = $this->match_from_pos($str, $i);
                if ($result->worse_than($tmp)) {
                    $result = $tmp;
                }
                // the actual match starting position is $result->indexfirst[0].
                // $result->indexfirst[0] can differ from $i if assertions merging is turned on.
                // assertions merging can lead to non-consuming transitions in the beginning or in the end of the finite automaton,
                // those transitions shift the actual matching position.
                // to ensure the leftmost-longest match, we should stop matching when $result->indexfirst[0] equals
                // current position of the loop and the match is 'best'.
                if ($result->indexfirst[0] <= $i && $result->best()) {
                    break;
                }
            }
        } else {// Match from all indexes.
            $rightborder = $str->length();
            // Starting positions loop.
            for ($i = 0; $i <= $rightborder; $i++) {
                $tmp = $this->match_from_pos($str, $i);
                if ($result->worse_than($tmp)) {
                    $result = $tmp;
                }
                if ($result->indexfirst[0] <= $i && $result->best()) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Do a necessary preprocessing before matching loop.
     *
     * If a @see{qtype_preg_matching_results} object is returned, it is treated as if match was decided during preprocessing.
     * and no actual matching needed.
     */
    protected function match_preprocess($str) {
        return false;
    }

    /**
     * Fill anchor field to show if regex is anchored using astroot.
     * If all top-level alternations starts from ^ or .* then expression is anchored from start (i.e. if matching from start failed, no other matches possible).
     * If all top-level alternations ends on $ or .* then expression is anchored from end (i.e. if matching from start failed, no other matches possible).
     */
    protected function look_for_anchors() {
        $this->anchor = new qtype_preg_regex_anchoring;
        $this->anchor->start = $this->look_for_circumflex($this->astroot);// TODO - make $this->look_for_circumflex change $this->anchor instead of returning result.
        $this->anchor->startlinebreak = $this->anchor->start;// TODO - temporary for compatibility reasons, remove when change in the string above will be made.
    }

    protected function look_for_circumflex($node, $wasconcat = false) {
        if (is_a($node, 'qtype_preg_leaf')) {
            // Expression starts from ^.
            return $node->type == qtype_preg_node::TYPE_LEAF_ASSERT && $node->is_start_anchor();
        }

        /*if ($node->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT && $node->leftborder == 0) {
            // Expression starts from .*
            $operand = $node->operands[0];
            return ($node->leftborder === 0 && $operand->type == qtype_preg_node::TYPE_LEAF_CHARSET &&
                    count($operand->flags) > 0 && $operand->flags[0][0]->data === qtype_preg_charset_flag::META_DOT);
        }*/

        if ($node->type == qtype_preg_node::TYPE_NODE_CONCAT || $node->type == qtype_preg_node::TYPE_NODE_SUBEXPR) {
            // Check the first operand for concatenation and subexpressions.
            return $this->look_for_circumflex($node->operands[0], $wasconcat || $node->type == qtype_preg_node::TYPE_NODE_CONCAT);
        }

        if ($node->type == qtype_preg_node::TYPE_NODE_ALT) {
            // Every branch of alternation is anchored.
            $cf = true;
            $empty = false;
            foreach ($node->operands as $operand) {
                $empty = $empty || $operand->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY;
                $cf = $cf && $this->look_for_circumflex($operand, $wasconcat);
            }
            $empty = $empty && !$wasconcat;
            return $cf || $empty;
        }
        return false;
    }
}
