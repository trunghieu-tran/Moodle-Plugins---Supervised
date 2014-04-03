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
 * Defines NFA matcher class.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_nodes.php');

/**
 * Represents an execution state of an nfa.
 */
class qtype_preg_nfa_exec_state implements qtype_preg_matcher_state {

    // Indicates that this state is a full match state.
    const FLAG_FULL = 0x01;
    // Indicates that this state had \A or ^ transition.
    const FLAG_VISITED_START_ANCHOR = 0x02;
    // Indicates that this state had \Z \z or $ transition.
    const FLAG_VISITED_END_ANCHOR   = 0x04;

    // The nfa being executed.
    public $matcher;

    // Level of recursion
    public $recursionlevel;

    // The corresponding nfa state.
    public $state;

    // 2-dimensional array of matches; 1st is subpattern number; 2nd is repetitions of the subpattern.
    // Each subpattern is initialized with (-1,-1) at start.
    public $matches;

    // Starting position of the match.
    public $startpos;

    // Length of the match.
    public $length;

    // Array used mostly for disambiguation when there are duplicate subpexpressions numbers.
    public $subexpr_to_subpatt;

    // Bitwise union of the above constants.
    public $flags;

    // How many characters left for full match?
    public $left;

    // Match extension in case of partial match. An object of this same class.
    public $extendedmatch;

    // String being captured and/or generated.
    public $str;

    // The last transition matched.
    public $last_transition;

    // Length of the last match.
    public $last_match_len;

    // States to backtrack to when generating extensions of partial matches.
    public $backtrack_states;

    public function __clone() {
        $this->str = clone $this->str;  // Needs to be cloned for correct string generation.
    }

    public static function empty_subpatt_match() {
        return array(qtype_preg_matching_results::NO_MATCH_FOUND, qtype_preg_matching_results::NO_MATCH_FOUND);
    }

    public static function is_being_captured($index, $length) {
        return ($index != qtype_preg_matching_results::NO_MATCH_FOUND && $length == qtype_preg_matching_results::NO_MATCH_FOUND);
    }

    public function set_flag($flag) {
        $this->flags = ($this->flags | $flag);
    }

    public function unset_flag($flag) {
        $flag = ~$flag;
        $this->flags = ($this->flags & $flag);
    }

    public function is_flag_set($flag) {
        return ($this->flags & $flag) != 0;
    }

    public function set_full($value) {
        if ($value) {
            $this->set_flag(self::FLAG_FULL);
        } else {
            $this->unset_flag(self::FLAG_FULL);
        }
    }

    public function is_full() {
        return $this->is_flag_set(self::FLAG_FULL);
    }

    public function root_subpatt_number() {
        return $this->matcher->get_ast_root()->subpattern;
    }

    // Returns the current match for the given subpattern number. If there was no attemt to match, returns null.
    public function current_match($subpatt) {
        if (!isset($this->matches[$subpatt])) {
            return null;
        }
        return end($this->matches[$subpatt]);
    }

    // Sets the current match for the given subpattern number.
    public function set_current_match($subpatt, $index, $length) {
        if (!array_key_exists($subpatt, $this->matches)) {
            // Can get here when matching recursive patterns
            return;
        }
        $count = count($this->matches[$subpatt]);
        $this->matches[$subpatt][$count - 1] = array($index, $length);
    }

    // Returns the last match for the given subpattern number. This function has different behaviour in PCRE and POSIX mode.
    // If there was no attemt to match, returns null.
    public function last_match($subpatt) {
        if ($this->matcher->get_options()->mode == qtype_preg_handling_options::MODE_POSIX) {
            return $this->current_match($subpatt);
        }

        if (!isset($this->matches[$subpatt])) {
            return null;
        }

        $matches = $this->matches[$subpatt];
        $count = count($matches);

        // It's a tricky part. PCRE uses last successful match for situations like "(a|b\1)*" and string "ababbabbba".
        // Hence we need to iterate from the last to the first repetitions until a match found.
        for ($i = $count - 1; $i >= 0; $i--) {
            $cur = $matches[$i];
            if ($cur[0] != qtype_preg_matching_results::NO_MATCH_FOUND && $cur[1] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                return $cur;
            }
        }
        return self::empty_subpatt_match();
    }

    // Checks if this state equals another
    /*public function equals($to) {
        if ($this->state !== $to->state) {
            return false;
        }
        if (!$this->matcher->get_options()->capturesubexpressions) {
            return $this->length == $to->length;
        }
        foreach ($this->matches as $key => $repetitions) {
            if ($this->current_match($key) !== $to->current_match($key)) {
                return false;
            }
        }
        return true;
    }*/

    public function find_dup_subexpr_match($subexpr) {
        if (!isset($this->subexpr_to_subpatt[$subexpr])) {
            // Can get here when {0} occurs in the regex.
            return self::empty_subpatt_match();
        }
        $subpatt = $this->subexpr_to_subpatt[$subexpr];
        $last = $this->last_match($subpatt);
        if (!self::is_being_captured($last[0], $last[1])) {
            return $last;
        }
        return self::empty_subpatt_match();
    }

    public function index_first($subexpr = 0) {
        $last = $this->find_dup_subexpr_match($subexpr);
        return $last[0];
    }

    public function length($subexpr = 0) {
        $last = $this->find_dup_subexpr_match($subexpr);
        return $last[1];
    }

    public function is_subexpr_captured($subexpr) {
        $last = $this->find_dup_subexpr_match($subexpr);
        return $last[1] != qtype_preg_matching_results::NO_MATCH_FOUND;
    }

    public function match_from_pos_internal($str, $startpos, $subexpr = 0, $recursionlevel = 0) {
        return $this->matcher->match_from_pos_internal($str, $startpos, $subexpr, $recursionlevel);
    }

    public function start_pos() {
        return $this->startpos;
    }

    public function recursion_level() {
        return $this->recursionlevel;
    }

    public function to_matching_results() {
        $index = array();
        $length = array();
        $subexprs = array(-2);
        for ($subexpr = 0; $subexpr <= $this->matcher->get_max_subexpr(); $subexpr++) {
            $subexprs[] = $subexpr;
        }
        foreach ($subexprs as $subexpr) {
            if (!isset($this->subexpr_to_subpatt[$subexpr])) {
                // Can get here when {0} occurs in the regex.
                $index[$subexpr] = qtype_preg_matching_results::NO_MATCH_FOUND;
                $length[$subexpr] = qtype_preg_matching_results::NO_MATCH_FOUND;
            } else {
                $subpatt = $this->subexpr_to_subpatt[$subexpr];
                $match = $this->last_match($subpatt);
                if ($match[1] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                    $index[$subexpr] = $match[0];
                    $length[$subexpr] = $match[1];
                } else {
                    $index[$subexpr] = qtype_preg_matching_results::NO_MATCH_FOUND;
                    $length[$subexpr] = qtype_preg_matching_results::NO_MATCH_FOUND;
                }
            }
        }
        if ($length[-2] == qtype_preg_matching_results::NO_MATCH_FOUND) {
            $cur = $this->current_match(-2);
            if ($cur !== null && $cur[0] != qtype_preg_matching_results::NO_MATCH_FOUND) {
                $index[-2] = $cur[0];
                $length[-2] = $this->length - $cur[0];
            }
        }
        $index[0] = $this->startpos;
        $length[0] = $this->length;
        $result = new qtype_preg_matching_results($this->is_full(), $index, $length, $this->left, $this->extendedmatch);
        $result->set_source_info($this->str, $this->matcher->get_max_subexpr(), $this->matcher->get_subexpr_map());
        return $result;
    }

    /**
     * Resets the given subpattern to no match.
     */
    public function begin_subpatt_iteration($node) {
        if (!$this->matcher->get_options()->capturesubexpressions && $node->subpattern != $this->root_subpatt_number()) {
            return;
        }

        $nodes = array($node);
        if ($this->matcher->get_options()->capturesubexpressions) {
            $nodes = array_merge($nodes, $this->matcher->get_nested_nodes($node->subpattern));
        }

        foreach ($nodes as $node) {
            if ($node->subpattern == -1) {
                continue;
            }

            $cur = $this->current_match($node->subpattern);

            if ($cur === null) {
                $this->matches[$node->subpattern] = array(); // Very first iteration.
            }

            if ($cur[0] == qtype_preg_matching_results::NO_MATCH_FOUND && $cur[1] == qtype_preg_matching_results::NO_MATCH_FOUND) {
                continue;   // The new iteration is already started.
            }

            $this->matches[$node->subpattern][] = self::empty_subpatt_match();
        }
    }

    /**
     * Checks if this state contains null iterations, for example \b*. Such states should be skipped during matching.
     */
    public function has_null_iterations() {
        foreach ($this->matches as $subpatt => $repetitions) {
            $count = count($repetitions);
            if ($count < 2) {
                continue;
            }
            for ($i = $count - 1; $i > 0; $i--) {
                $penult = $repetitions[$i - 1];
                $last = $repetitions[$i];
                if ($last[1] != qtype_preg_matching_results::NO_MATCH_FOUND && $penult == $last) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns true if this beats other, false if other beats this; for equal states returns false.
     */
    public function leftmost_longest($other, $matchinginprogress = true) {
        // Check for full match.
        if ($this->is_full() && !$other->is_full()) {
            return true;
        } else if (!$this->is_full() && $other->is_full()) {
            return false;
        }

        // Choose the longest match
        if ($this->length > $other->length) {
            return true;
        } else if ($other->length > $this->length) {
            return false;
        }

        // If both states have partial match, choose one with minimal left
        if (!$matchinginprogress && !$this->is_full() && !$other->is_full()) {
            if ($this->left < $other->left) {
                return true;
            } else if ($other->left < $this->left) {
                return false;
            }
        }

        // PCRE/POSIX selection goes on below. Iterate over all subpatterns skipping the first which is the whole expression.
        $modepcre = $this->matcher->get_options()->mode == qtype_preg_handling_options::MODE_PCRE;
        for ($i = $this->root_subpatt_number() + 1; $i <= $this->matcher->get_max_subpatt(); $i++) {
            $this_match = isset($this->matches[$i]) ? $this->matches[$i] : array(self::empty_subpatt_match());
            $other_match = isset($other->matches[$i]) ? $other->matches[$i] : array(self::empty_subpatt_match());

            $this_repetitions_count = count($this_match);
            $other_repetitions_count = count($other_match);

            $repetitions_count_difference = $this_repetitions_count - $other_repetitions_count;
            if ($modepcre && abs($repetitions_count_difference) == 1) {
                // PCRE mode selection: if states have N and N + 1 subpattern repetitions, respectively,
                // and the (N + 1)th repetition is empty, then select the second state. And vice versa.
                $this_last = $this->last_match($i);
                $other_last = $other->last_match($i);
                if ($repetitions_count_difference == 1 && $this_last[1] == 0 && $this_last[0] > $other_last[0]) {
                    return true;
                } else if ($repetitions_count_difference == -1 && $other_last[1] == 0 && $other_last[0] > $this_last[0]) {
                    return false;
                }
            }

            // Iterate over all repetitions.
            for ($j = 0; $j < min($this_repetitions_count, $other_repetitions_count); $j++) {
                $this_index = $this_match[$j][0];
                $this_length = $this_match[$j][1];
                $other_index = $other_match[$j][0];
                $other_length = $other_match[$j][1];
                $this_being_captured = self::is_being_captured($this_index, $this_length);
                $other_being_captured = self::is_being_captured($other_index, $other_length);

                if ($matchinginprogress && $this_being_captured) {
                    $this_length = $this->startpos + $this->length - $this_index;
                }
                if ($matchinginprogress && $other_being_captured) {
                    $other_length = $this->startpos + $other->length - $other_index;
                }

                // Continue if both iterations have no match.
                if ($this_index == qtype_preg_matching_results::NO_MATCH_FOUND && $other_index == qtype_preg_matching_results::NO_MATCH_FOUND) {
                    continue;
                }

                // Match existance.
                if ($other_index == qtype_preg_matching_results::NO_MATCH_FOUND) {
                    return true;
                } else if ($this_index == qtype_preg_matching_results::NO_MATCH_FOUND) {
                    return false;
                }

                // Longest of all possible matches.
                if ($this_length > $other_length) {
                    return true;
                } else if ($other_length > $this_length) {
                    return false;
                }
            }
        }

        // Iterate over all subpatterns for the 2nd time to compare numbers of repetitions
        for ($i = $this->root_subpatt_number() + 1; $i <= $this->matcher->get_max_subpatt(); $i++) {
            $this_match = isset($this->matches[$i]) ? $this->matches[$i] : array(self::empty_subpatt_match());
            $other_match = isset($other->matches[$i]) ? $other->matches[$i] : array(self::empty_subpatt_match());

            $this_repetitions_count = count($this_match);
            $other_repetitions_count = count($other_match);

            if ($this_repetitions_count < $other_repetitions_count) {
                return true;
            } else if ($other_repetitions_count < $this_repetitions_count) {
                return false;
            }
        }

        return false;
    }

    /**
     * Returns true if this beats other, false if other beats this; for equal states returns false.
     */
    public function leftmost_shortest($other) {
        // Check for full match.
        if ($this->is_full() && !$other->is_full()) {
            return true;
        } else if (!$this->is_full() && $other->is_full()) {
            return false;
        }

        if ($this->length < $other->length) {
            return true;
        } else if ($other->length < $this->length) {
            return false;
        }

        return false;
    }

    /**
     * Writes subpatterns start\end information to this state.
     */
    public function write_subpatt_info($transition, $pos, $matchlen) {
        $tagsets = array_reverse($transition->tagsets);
        foreach ($tagsets as $tagset) {
            $this->write_subpatt_info_inner($tagset, $pos, $matchlen);
        }
    }

    public function write_subpatt_info_inner($tagset, $pos, $matchlen) {
        // Begin a new iteration of a subpattern. In fact, we can call the method for
        // the subpattern with minimal number; all "bigger" subpatterns will be reset recursively.
        $min = $tagset->min_open_tag();
        if ($min !== null) {
            $this->begin_subpatt_iteration($min->pregnode);
        }

        $options = $this->matcher->get_options();

        // Set matches to (pos, -1) for the new iteration.
        foreach ($tagset->tags as $tag) {
            if ($tag->type != qtype_preg_fa_tag::TYPE_OPEN) {
                continue;
            }
            if (!$options->capturesubexpressions && $tag->pregnode->subpattern != $this->root_subpatt_number()) {
                continue;
            }
            // Starting indexes are always the same, equal $pos
            $index = $pos;
            if ($tag->pos == qtype_preg_fa_tag::POS_AFTER_TRANSITION) {
                $index += $matchlen;
            }
            //echo "opening {$tag->pregnode->subpattern} at pos {$tag->pos}\n";
            $this->set_current_match($tag->pregnode->subpattern, $index, qtype_preg_matching_results::NO_MATCH_FOUND);
        }

        // Set matches to (pos, length) for the ending iterations.
        foreach ($tagset->tags as $tag) {
            if ($tag->type != qtype_preg_fa_tag::TYPE_CLOSE) {
                continue;
            }
            if (!$options->capturesubexpressions && $tag->pregnode->subpattern != $this->root_subpatt_number()) {
                continue;
            }
            $current_match = $this->current_match($tag->pregnode->subpattern);
            $index = $current_match[0];
            $length = $pos - $index;
            if ($tag->pos != qtype_preg_fa_tag::POS_BEFORE_TRANSITION) {
                $length += $matchlen;
            }
            //echo "closing {$tag->pregnode->subpattern} at pos {$tag->pos}\n";
            if ($index != qtype_preg_matching_results::NO_MATCH_FOUND) {
                $this->set_current_match($tag->pregnode->subpattern, $index, $length);
            }
        }

        // Some stuff for subexpressions.
        foreach ($tagset->tags as $tag) {
            if ($tag->type != qtype_preg_fa_tag::TYPE_OPEN || $tag->pregnode->subtype != qtype_preg_node_subexpr::SUBTYPE_SUBEXPR) {
                continue;
            }
            if (!$options->capturesubexpressions && $tag->pregnode->subpattern != $this->root_subpatt_number()) {
                continue;
            }
            $this->subexpr_to_subpatt[$tag->pregnode->number] = $tag->pregnode->subpattern;
        }
    }

    public function subpatts_to_string() {
        $res = '';
        foreach ($this->matches as $subpatt => $repetitions) {
            $res .= $subpatt . ': ';
            foreach ($repetitions as $repetition) {
                $ind = $repetition[0];
                $len = $repetition[1];
                $res .= "($ind, $len) ";
            }
            $res .= "\n";
        }
        return $res;
    }

    public function subexprs_to_string() {
        $res = '';
        foreach ($this->subexpr_to_subpatt as $subexpr => $subpatt) {
            $lastmatch = $this->last_match($subpatt);
            $ind = $lastmatch[0];
            $len = $lastmatch[1];
            $res .= $subexpr . ": ($ind, $len) ";
        }
        $res .= "\n";
        return $res;
    }
}
