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
 * Defines finite automata states and transitions classes for regular expression matching.
 * The class is used by FA-based matching engines, provides standartisation to them and enchances testability.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>, Valeriy Streltsov, Elena Lepilkina
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/poasquestion/stringstream/stringstream.php');
require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_dot_lexer.lex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_dot_parser.php');

/**
 * Represents a finite automaton transition.
 */
class qtype_preg_fa_transition {

    //const GREED_ZERO = 1;
    const GREED_LAZY = 2;
    const GREED_GREEDY = 4;
    const GREED_POSSESSIVE = 8;

    /** Empty transition. */
    const TYPE_TRANSITION_EPS = 'eps_transition';
    /** Transition with unmerged simple assert. */
    const TYPE_TRANSITION_ASSERT = 'assert';
    /** Empty transition or transition with unmerged simple assert. */
    const TYPE_TRANSITION_BOTH = 'both';
    /** Capturing transition. */
    const TYPE_TRANSITION_CAPTURE = 'capturing';

    /** Transition from first automata. */
    const ORIGIN_TRANSITION_FIRST = 0x01;
    /** Transition from second automata. */
    const ORIGIN_TRANSITION_SECOND = 0x02;
    /** Transition from intersection part. */
    const ORIGIN_TRANSITION_INTER = 0x04;

    /** @var int - state which transition starts from. */
    public $from;
    /** @var object of qtype_preg_leaf class - condition for this transition. */
    public $pregleaf;
    /** @var int - state which transition leads to. */
    public $to;
    /** @var greediness of this transition. */
    public $greediness;
    /** @var array of qtype_preg_node objects - subpatterns opened by this transition */
    public $opentags;
    /** @var array of qtype_preg_node objects - subpatterns closed by this transition */
    public $closetags;
    public $minopentag;
    /** @var type of the transition - should be equal to a constant defined in this class. */
    public $type;
    /** @var origin of the transition - should be equal to a constant defined in this class. */
    public $origin;
    /** @var bool - TODO. */
    public $consumeschars;
    /** @var bool - does this transition start a backreferenced subexpression(s)? */
    public $startsbackrefedsubexprs;
    /** @var bool - does this transition start a quantifier? */
    public $startsquantifier;
    /** @var bool - does this transition end a quantifier? */
    public $endsquantifier;
    /** @var bool - does this transition make a infinite quantifier loop? */
    public $loopsback;

    /** Array of qtype_preg_fa_transition objects merged to this transition and matched before it. Note that:
      a) Merged transitions are expected to be zero-length (simple assertions, epsilons)
      b) Max 'nestedness' level is 2, i.e. you are not expected to merge transitions into merged transitions
      c) You should guarantee that merged transitins are placed in the same order as they occurred originally */
    public $mergedbefore;

    /** Array of qtype_preg_fa_transition objects merged to this transition and matched after it. */
    public $mergedafter;


    public function __toString() {
        return $this->from . ' -> ' . $this->pregleaf->leaf_tohr() . ' -> ' . $this->to;
    }

    public function __construct($from, $pregleaf, $to, $origin = self::ORIGIN_TRANSITION_FIRST, $consumeschars = true) {
        $this->from = $from;
        $this->pregleaf = clone $pregleaf;
        $this->to = $to;
        $this->greediness = self::GREED_GREEDY;
        $this->opentags = array();
        $this->closetags = array();
        $this->minopentag = null;
        $this->type = null; // TODO
        $this->origin = $origin;
        $this->consumeschars = $consumeschars;
        $this->startsbackrefedsubexprs = false;
        $this->startsquantifier = false;
        $this->endsquantifier = false;
        $this->loopsback = false;
        $this->mergedbefore = array();
        $this->mergedafter = array();
    }

    public function __clone() {
        $this->pregleaf = clone $this->pregleaf;
        if ($this->minopentag !== null) {
            $this->minopentag = clone $this->minopentag;
        }
        foreach ($this->mergedbefore as $key => $merged) {
            $this->mergedbefore[$key]->mergedafter = array();
            $this->mergedbefore[$key] = clone $merged;
        }

        foreach ($this->mergedafter as $key => $merged) {
            $this->mergedafter[$key]->mergedafter = array();
            $this->mergedafter[$key] = clone $merged;
        }
    }

    public function equal($other) {
        return $this->from == $other->from && $this->to == $other->to && $this->pregleaf == $other->pregleaf && count($this->mergedbefore) == count($other->mergedbefore) && count($this->mergedafter) == count($other->mergedafter);
    }

    /**
     * Generates a character considering merged transitions that affect the resulting char (^ \A $ \Z \z)
     */
    public function next_character($originalstr, $newstr, $pos, $length = 0, $matcherstateobj = null) {

        if ($this->pregleaf->type != qtype_preg_node::TYPE_LEAF_CHARSET) {
            return $this->pregleaf->next_character($originalstr, $newstr, $pos, $length, $matcherstateobj);
        }

        // Get ranges from charset
        $ranges = $this->pregleaf->ranges();

        if (empty($ranges)) {
            return array(qtype_preg_leaf::NEXT_CHAR_CANNOT_GENERATE, null);
        }

        // Determine which assertions we have

        $circumflex = array('before' => false, 'after' => false);
        $dollar = array('before' => false, 'after' => false);
        $capz = array('before' => false, 'after' => false);

        $key = 'before';
        foreach (array($this->mergedbefore, $this->mergedafter) as $assertions) {
            foreach ($assertions as $assertion) {
                switch ($assertion->pregleaf->subtype) {
                case qtype_preg_leaf_assert::SUBTYPE_SMALL_ESC_Z:
                    return array(qtype_preg_leaf::NEXT_CHAR_CANNOT_GENERATE, null);
                case qtype_preg_leaf_assert::SUBTYPE_ESC_A:
                    return array(qtype_preg_leaf::NEXT_CHAR_CANNOT_GENERATE, null);
                case qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX:
                    $circumflex[$key] = true;
                    break;
                case qtype_preg_leaf_assert::SUBTYPE_DOLLAR:
                    $dollar[$key] = true;
                    break;
                case qtype_preg_leaf_assert::SUBTYPE_CAPITAL_ESC_Z:
                    $capz[$key] = true;
                    break;
                default:
                    break;
                }
            }
            $key = 'after';
        }

        // If there are assertions we can only return \n
        if ($dollar['before'] || $capz['before']) {
            // There are end string assertions.
            if (qtype_preg_unicode::is_in_range("\n", $ranges)) {
                return $capz['before']
                    ? array(qtype_preg_leaf::NEXT_CHAR_END_HERE, new qtype_poasquestion\string("\n"))
                    : array(qtype_preg_leaf::NEXT_CHAR_OK, new qtype_poasquestion\string("\n"));
            } else {
                return array(qtype_preg_leaf::NEXT_CHAR_CANNOT_GENERATE, null);
            }
        } else if ($circumflex['after']) {
            // There are start string assertions.
            if (qtype_preg_unicode::is_in_range("\n", $ranges)) {
                return array(qtype_preg_leaf::NEXT_CHAR_OK, new qtype_poasquestion\string("\n"));
            } else {
                return array(qtype_preg_leaf::NEXT_CHAR_CANNOT_GENERATE, null);
            }
        }


        // Now we don't have assertions affecting characters. Form the resulting ranges. trying desired ranges first

        $originalchar = $originalstr[$pos];
        $originalcode = core_text::utf8ord($originalchar);

        $desired_ranges = array();
        if ($pos < $originalstr->length()) {
            $desired_ranges[] = array(array($originalcode, $originalcode)); // original character - highest priority
        }
        $desired_ranges[] = array(array(0x21, 0x7F));   // regular ASCII characters - middle priority
        $desired_ranges[] = array(array(0x20, 0x20));   // space for \s - lowest priority

        $result_ranges = $ranges;   // By default original leaf's ranges.
        foreach ($desired_ranges as $desired) {
            $tmp = qtype_preg_unicode::intersect_ranges($ranges, $desired);
            //$tmp = qtype_preg_unicode::kinda_operator($ranges, $desired, true, false, false, false);
            if (!empty($tmp)) {
                $result_ranges = $tmp;
                break;
            }
        }

        return array(qtype_preg_leaf::NEXT_CHAR_OK, new qtype_poasquestion\string(core_text::code2utf8($result_ranges[0][0])));
    }

    public function is_start_anchor() {
        return ($this->pregleaf->type == qtype_preg_node::TYPE_LEAF_ASSERT && $this->pregleaf->is_start_anchor()) /*&& empty($this->mergedbefore))*/;
    }

    public function is_end_anchor() {
        return ($this->pregleaf->type == qtype_preg_node::TYPE_LEAF_ASSERT && $this->pregleaf->is_end_anchor()) /*&& empty($this->mergedafter))*/;
    }

    public function is_artificial_assert() {
        return ($this->pregleaf->type == qtype_preg_node::TYPE_LEAF_ASSERT &&  ($this->pregleaf->is_artificial_assert() && (!empty($this->mergedafter) || !empty($this->mergedbefore))));
    }

    /**
     * Find intersection of asserts.
     *
     * @param other - the second assert for intersection.
     * @return assert, which is intersection of ginen.
     */
    public function intersect_asserts($other) {

        // Adding assert to array.
        if ($this->is_start_anchor()) {
            array_unshift($this->mergedafter, clone $this);
        } else if ($this->is_end_anchor()) {
            $this->mergedbefore[] = clone $this;    // TODO: maybe prepend?
        }

        if ($other->is_start_anchor()) {
            array_unshift($other->mergedafter,clone $other);
        } else if ($other->is_end_anchor()){
            $other->mergedbefore[] = clone $other;  // TODO: same
        }

        $resultbefore = array_merge($this->mergedbefore, $other->mergedbefore);
        $resultafter = array_merge($this->mergedafter, $other->mergedafter);
        // Removing same asserts.
        for ($i = 0; $i < count($resultbefore); $i++) {
            for ($j = ($i+1); $j < count($resultbefore); $j++) {
                if ($resultbefore[$i] == $resultbefore[$j]) {
                    unset($resultbefore[$j]);
                    $resultbefore = array_values($resultbefore);
                    $j--;
                }
            }
        }

        for ($i = 0; $i < count($resultafter); $i++) {
            for ($j = ($i+1); $j < count($resultafter); $j++) {
                if ($resultafter[$i] == $resultafter[$j]) {
                    unset($resultafter[$j]);
                    $resultafter = array_values($resultafter);
                    $j--;
                }
            }
        }

        $resultbefore = array_values($resultbefore);
        $resultafter = array_values($resultafter);
        foreach ($resultbefore as $tran) {
            $before[] = $tran->pregleaf;
        }
        foreach ($resultafter as $tran) {
            $after[] = $tran->pregleaf;
        }
        foreach ($resultafter as $assert) {
            $key = array_search($assert, $resultafter);
            if ($assert->pregleaf->subtype == qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX) {
                // Searching compatible asserts.
                if (qtype_preg_leaf::contains_node_of_subtype(qtype_preg_leaf_assert::SUBTYPE_ESC_A, $after)) {
                    unset($resultafter[$key]);
                    $resultafter = array_values($resultafter);
                }
            }
        }

        foreach ($resultbefore as $assert) {
            $key = array_search($assert, $resultbefore);
            if ($assert->pregleaf->subtype == qtype_preg_leaf_assert::SUBTYPE_DOLLAR) {
                // Searching compatible asserts.
                if (qtype_preg_leaf::contains_node_of_subtype(qtype_preg_leaf_assert::SUBTYPE_SMALL_ESC_Z, $before) || qtype_preg_leaf::contains_node_of_subtype(qtype_preg_leaf_assert::SUBTYPE_CAPITAL_ESC_Z, $before)) {
                    unset($resultbefore[$key]);
                    $resultbefore = array_values($resultbefore);
                }

            }
            if ($assert->pregleaf->subtype == qtype_preg_leaf_assert::SUBTYPE_CAPITAL_ESC_Z) {
                // Searching compatible asserts.
                if (qtype_preg_leaf::contains_node_of_subtype(qtype_preg_leaf_assert::SUBTYPE_SMALL_ESC_Z, $before)) {
                    unset($resultbefore[$key]);
                    $resultbefore = array_values($resultbefore);
                }

            }
        }

        // Getting result leaf.
        if ($this->pregleaf->type == qtype_preg_node::TYPE_LEAF_CHARSET || $this->pregleaf->type == qtype_preg_node::TYPE_LEAF_BACKREF) {
            $assert = clone $this;
        } else if ($other->pregleaf->type == qtype_preg_node::TYPE_LEAF_CHARSET || $other->pregleaf->type == qtype_preg_node::TYPE_LEAF_BACKREF) {
            $assert = clone $other;
        } else {
            if (!empty($resultbefore)) {
                $assert = clone $resultbefore[count($resultbefore) - 1];
                //unset($resultbefore[count($resultbefore) - 1]);
            } else if (!empty($resultafter)) {
                $assert = $resultafter[0];
                //unset($resultafter[0]);
            } else {
                $pregleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
                $assert = new qtype_preg_fa_transition(0, $pregleaf, 1);
            }
        }
        $assert->mergedbefore = $resultbefore;
        $assert->mergedafter = $resultafter;
        if ($this->pregleaf->type == qtype_preg_node::TYPE_LEAF_ASSERT) {
            if ($this->is_start_anchor()) {
                unset($this->mergedafter[0]);
            } else {
                unset($this->mergedbefore[count($this->mergedbefore) - 1]);
            }
        }
        if ($other->pregleaf->type == qtype_preg_node::TYPE_LEAF_ASSERT) {
            if ($other->is_start_anchor()) {
                unset($other->mergedafter[0]);
            } else {
                unset($other->mergedbefore[count($other->mergedbefore) - 1]);
            }
        }
        return $assert;
    }

    /**
     * Return the laziest greedines of two
     */
    public static function min_greediness($g1, $g2) {
        return min($g1, $g2);   // This actually works
    }

    public function all_open_tags() {
        $allopentags = array();
        foreach ($this->mergedbefore as $merged) {
            foreach ($merged->opentags as $tag) {
                $allopentags[] = $tag;
            }
        }
        foreach ($this->opentags as $tag) {
            $allopentags[] = $tag;
        }
        foreach ($this->mergedafter as $merged) {
            foreach ($merged->opentags as $tag) {
                $allopentags[] = $tag;
            }
        }
        return $allopentags;
    }

    public function all_close_tags() {
        $allclosetags = array();
        foreach ($this->mergedbefore as $merged) {
            foreach ($merged->closetags as $tag) {
                $allclosetags[] = $tag;
            }
        }
        foreach ($this->closetags as $tag) {
            $allclosetags[] = $tag;
        }
        foreach ($this->mergedafter as $merged) {
            foreach ($merged->closetags as $tag) {
                $allclosetags[] = $tag;
            }
        }
        return $allclosetags;
    }

    public function get_label_for_dot($index1, $index2) {
        $addedcharacters = '/(), ';
        if (strpbrk($index1, $addedcharacters) !== false) {
            $index1 = '"' . $index1 . '"';
        }
        if (strpbrk($index2, $addedcharacters) !== false) {
            $index2 = '"' . $index2 . '"';
        }
        if ($this->origin == self::ORIGIN_TRANSITION_FIRST) {
            $color = 'violet';
        } else if ($this->origin == self::ORIGIN_TRANSITION_SECOND) {
            $color = 'blue';
        } else if ($this->origin == self::ORIGIN_TRANSITION_INTER) {
            $color = 'red';
        }
        $lab = '';
        foreach ($this->mergedbefore as $before) {
            $open = $before->tags_before_transition();
            $close = $before->tags_after_transition();
            $label = $before->pregleaf->leaf_tohr();
            $lab .= $open . ' ' . $label . ' ' . $close;
            $lab .= '(' . $before->from . ',' . $before->to . ')';
            $lab .= '<BR/>';
        }
        $open = $this->tags_before_transition();
        $close = $this->tags_after_transition();
        $label = $this->pregleaf->leaf_tohr();
        $lab .= '<B>' . $open . ' ' . $label . ' ' . $close . '</B>';

        foreach ($this->mergedafter as $after) {
            $lab .= '<BR/>';
            $open = $after->tags_before_transition();
            $close = $after->tags_after_transition();
            $label = $after->pregleaf->leaf_tohr();
            $lab .= $open . ' ' . $label . ' ' . $close;
            $lab .= '(' . $after->from . ',' . $after->to . ')';
        }

        $lab = str_replace('\\', '\\\\', $lab);
        $lab = str_replace('"', '\"', $lab);
        $lab = '<' . $lab . '>';

        $thickness = 2;
        if ($this->greediness == self::GREED_LAZY) {
            $thickness = 1;
        } else if ($this->greediness == self::GREED_POSSESSIVE) {
            $thickness = 3;
        }

        // Dummy transitions are displayed dotted.
        if ($this->consumeschars) {
            return "$index1->$index2" . "[label = $lab, color = $color, penwidth = $thickness];";
        } else {
            return "$index1->$index2" . "[label = $lab, color = $color, penwidth = $thickness, style = dotted];";
        }
    }

    protected static function compare_tags($node1, $node2) {
        $result = $node1->type == $node2->type &&
                  $node1->pos == $node2->pos &&
                  $node1->pregnode->subpattern == $node2->pregnode->subpattern;
      return $result ? 0 : 1;
    }

    /**
     * Copies tags from other transition in this transition.
     */
    public function unite_tags($other, $result) {
        $result->opentags = array_merge($this->opentags, $other->opentags);
        $result->closetags = array_merge($this->closetags, $other->closetags);
        foreach ($result->opentags as $key => $tag) {
            $result->opentags[$key] = clone $tag;
        }
        foreach ($result->closetags as $key => $tag) {
            $result->closetags[$key] = clone $tag;
        }
    }

    /**
     * Returns intersection of transitions.
     *
     * @param other another transition for intersection.
     */
    public function intersect($other) {

        $thishastags = $this->has_tags();
        $otherhastags = $other->has_tags();
        $resulttran = null;
        $flag = new qtype_preg_charset_flag();
        $flag->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string("\n"));
        $charset = new qtype_preg_leaf_charset();
        $charset->flags = array(array($flag));
        $charset->userinscription = array(new qtype_preg_userinscription("\n"));
        $righttran = new qtype_preg_fa_transition(0, $charset, 1);
        // Consider that eps and transition which doesn't consume characters always intersect
        if ($this->is_eps() && $other->consumeschars == false) {
            $resulttran = new qtype_preg_fa_transition(0, $other->pregleaf, 1, self::ORIGIN_TRANSITION_INTER, $other->consumeschars);
            $assert = $this->intersect_asserts($other);
            $resulttran->mergedbefore = $assert->mergedbefore;
            if ($thishastags) {
                $resulttran->mergedafter = array_merge($assert->mergedafter, array(clone $this));
            } else {
                $resulttran->mergedafter = $assert->mergedafter;
            }
            $resulttran->loopsback = $this->loopsback || $other->loopsback;
            //$this->unite_tags($other, $resulttran);
            return $resulttran;
        }
        if ($other->is_eps() && $this->consumeschars == false) {
            $resulttran = new qtype_preg_fa_transition(0, $this->pregleaf, 1, self::ORIGIN_TRANSITION_INTER, $this->consumeschars);

            $assert = $this->intersect_asserts($other);

            $resulttran->mergedafter = $assert->mergedafter;

            if ($otherhastags) {
                $resulttran->mergedbefore = array_merge( $assert->mergedbefore, array(clone $other));

            } else {
                $resulttran->mergedbefore = $assert->mergedbefore;
            }
            $resulttran->loopsback = $this->loopsback || $other->loopsback;
            $resulttran->count_min_open_tag();
            return $resulttran;
        }
        if ($this->is_unmerged_assert()  && (!$other->is_eps() && !$other->is_unmerged_assert())
            || $other->is_unmerged_assert() && (!$this->is_eps() && !$this->is_unmerged_assert())) {
            // We can intersect asserts only with \n.
            $intersection = null;

            if ($this->is_unmerged_assert()) {
                $intersection = $other->intersect($righttran);
                $resulttran = clone $other;
            } else if ($other->is_unmerged_assert()) {
                $intersection = $this->intersect($righttran);
                $resulttran = clone $this;
            }
            if ($intersection != null) {
                $resulttran->pregleaf = $intersection->pregleaf;
                $resulttran->count_min_open_tag();
                $assert = $this->intersect_asserts($other);
                $resulttran->mergedbefore = $assert->mergedbefore;
                $resulttran->mergedafter = $assert->mergedafter;
                if ($this->is_unmerged_assert()) {
                    $resulttran->consumeschars = false;
                }
                $resulttran->loopsback = $this->loopsback || $other->loopsback;
                return $resulttran;
            }
            return null;
        }
        $resultleaf = $this->pregleaf->intersect_leafs($other->pregleaf, $thishastags, $otherhastags);
        if ($resultleaf != null) {
            if (($this->is_eps() || $this->is_unmerged_assert()) && (!$other->is_eps() && !$other->is_unmerged_assert())) {
                $resulttran = null;
            } else if (($other->is_eps() || $other->is_unmerged_assert()) && (!$this->is_eps() && !$this->is_unmerged_assert())) {
                $resulttran = null;
            } else {
                $resulttran = new qtype_preg_fa_transition(0, $resultleaf, 1, self::ORIGIN_TRANSITION_INTER);
            }
        }
        if ($resulttran !== null ) {
            $assert = $this->intersect_asserts($other);
            $resulttran->mergedbefore = $assert->mergedbefore;
            $resulttran->mergedafter = $assert->mergedafter;
            if (!$this->is_eps()) {
                $this->unite_tags($other, $resulttran);
            }
            $resulttran->loopsback = $this->loopsback || $other->loopsback;

            $resulttran->count_min_open_tag();
        }
        return $resulttran;
    }


    private function count_min_open_tag() {
        $minopentag;
        if (!empty($this->opentags)) {
            $minopentag = $this->opentags[0];
            foreach ($this->opentags as $tag) {
                if ($tag->subpattern < $minopentag->subpattern) {
                    $minopentag = $tag;
                }
            }
            $this->minopentag = $minopentag;
        }
    }

    /**
     * Returns true if transition has any tag.
     */
    public function has_tags() {
        foreach (array_merge($this->mergedbefore, array($this), $this->mergedafter) as $transition) {
            if (!empty($transition->opentags) || !empty($transition->closetags)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true if transition is eps.
     */
    public function is_eps() {
        return $this->pregleaf->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY;
    }

    /**
     * Returns true if transition is with unmerged assert.
     */
    public function is_unmerged_assert() {
        return ($this->pregleaf->type == qtype_preg_node::TYPE_LEAF_ASSERT && $this->pregleaf->subtype != qtype_preg_leaf_assert::SUBTYPE_ESC_B  && $this->pregleaf->subtype != qtype_preg_leaf_assert::SUBTYPE_ESC_G);
    }

    public function is_wordbreak() {
        return $this->pregleaf->type == qtype_preg_node::TYPE_LEAF_ASSERT && $this->pregleaf->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_B;
    }

    /**
     * Set this transition right type.
     */
    public function set_transition_type() {
        if ($this->is_eps()) {
            $this->type = self::TYPE_TRANSITION_EPS;
        } else if ($this->is_unmerged_assert()) {
            $this->type = self::TYPE_TRANSITION_ASSERT;
        } else {
            $this->type = self::TYPE_TRANSITION_CAPTURE;
        }
    }

    public function redirect_merged_transitions() {
        foreach ($this->mergedbefore as &$merged) {
            $merged->from = $this->from;
            $merged->to = $this->to;
        }
        unset($merged);
        foreach ($this->mergedafter as &$merged) {
            $merged->from = $this->from;
            $merged->to = $this->to;
        }
        unset($merged);
    }

    private function this_tags_tohr($open, $close) {
        //return '';  // uncomment when needed

        $result = '';
        if ($open) {
            $result .= 'o:';
            foreach ($this->opentags as $tag) {
                $result .= $tag->subpattern . ',';
            }
        }
        if ($close) {
            $result .= 'c:';
            foreach ($this->closetags as $tag) {
                $result .= $tag->subpattern . ',';
            }
        }
        return $result;
    }

    public function tags_before_transition() {
        return $this->this_tags_tohr(true, false);
    }

    public function tags_after_transition() {
        return $this->this_tags_tohr(false, true);
    }
}

/**
 * Represents a finite automaton. Inherit to define qtype_preg_deterministic_fa and qtype_preg_nondeterministic_fa.
 */
class qtype_preg_fa {

    /** @var two-dimensional array of qtype_preg_fa_transition objects: first index is "from", second index is "to"*/
    public $adjacencymatrix = array();
    /** @var array with strings with numbers of states, indexed by their ids from adjacencymatrix. */
    public $statenumbers = array();
    /** @var array of int ids of states - start states. */
    public $startstates = array();
    /** @var array of of int ids of states - end states. */
    public $endstates = array();

    public $fastartstates = array();
    public $faendstates = array();
    // Regex handler
    protected $handler;

    // Subexpr references (numbers) existing in the regex.
    protected $subexpr_ref_numbers;

    public $subexpr_recursive_ref_numbers = array();

    /** @var boolean is automaton really deterministic - it can be even if it shoudn't.
     * May be used for optimisation when an FA object actually stores a DFA.
     */
    protected $deterministic = true;

    /** @var boolean whether automaton has epsilon-transtions. */
    protected $haseps = false;
    /** @var boolean whether automaton has simple assertion transtions. */
    protected $hasassertiontransitions = false;

    protected $statecount = 0;
    protected $transitioncount = 0;
    protected $idcounter = 0;

    protected $statelimit;
    protected $transitionlimit;

    public $innerautomata;

    public function __construct($handler = null, $subexprrefs = array()) {
        $this->handler = $handler;
        $this->subexpr_ref_numbers = array();
        foreach ($subexprrefs as $ref) {
            $this->subexpr_ref_numbers[] = $ref->number;
            if ($ref->type === qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL && $ref->isrecursive) {
                $this->subexpr_recursive_ref_numbers[] = $ref->number;
            }
        }
        $this->set_limits();
        $this->innerautomata = array();
    }

    public function handler() {
        return $this->handler;
    }

    public function on_subexpr_added($pregnode, $body) {
        // Copy the node to the starting transitions.
        $start = $body['start'];
        $outgoing = $this->get_adjacent_transitions($start, true);
        foreach ($outgoing as $transition) {
            if (in_array($pregnode->number, $this->subexpr_ref_numbers)) {
                $transition->startsbackrefedsubexprs = true;
            }
        }
    }

    /**
     * The function should set $this->statelimit and $this->transitionlimit properties using $CFG.
     */
    protected function set_limits() {
        global $CFG;
        $this->statelimit = 250;
        $this->transitionlimit = 250;
        if (isset($CFG->qtype_preg_fa_state_limit)) {
            $this->statelimit = $CFG->qtype_preg_fa_state_limit;
        }
        if (isset($CFG->qtype_preg_fa_transition_limit)) {
            $this->transitionlimit = $CFG->qtype_preg_fa_transition_limit;
        }
    }

    public function transitions_tohr() {
        $result = '';
        foreach ($this->adjacencymatrix as $from => $row) {
            foreach ($row as $to => $transitions) {
                foreach ($transitions as $transition) {
                    $result .= $from . ' -> ' . $transition->pregleaf->leaf_tohr() . ' -> ' . $to . "\n";
                }
            }
        }
        return $result;
    }

    /**
     * Returns whether automaton is really deterministic.
     */
    public function is_deterministic() {
        return $this->deterministic;
    }

    /**
     * Used from qype_preg_fa_state class to signal that automaton become non-deterministic.
     *
     * Note that only methods of the automaton can make it deterministic and set this property to true.
     */
    public function make_nondeterministic() {
        $this->deterministic = false;
    }

    /**
     * Returns whether this implementation support DFA or NFA.
     */
    public function should_be_deterministic() {
        return false;
    }

    /**
     * Returns the start states for automaton.
     */
    public function get_start_states($subpattern = 0) {
        if (!isset($this->fastartstates[$subpattern])) {
            return isset($this->startstates[$subpattern]) ? $this->startstates[$subpattern] : array();
        }
        return $this->fastartstates[$subpattern];
    }

    /**
     * Return the end states of the automaton.
     */
    public function get_end_states($subpattern = 0) {
        if (!isset($this->faendstates[$subpattern])) {
            return isset($this->endstates[$subpattern]) ? $this->endstates[$subpattern] : array();
        }
        return $this->faendstates[$subpattern];
    }

    public function is_empty() {
        return empty($this->adjacencymatrix);
    }

    /**
     * Return array of all state ids of automata.
     */
    public function get_states() {
        return array_keys($this->adjacencymatrix);
    }

    /**
     * Calculates where subexpressions start and end.
     */
    public function calculate_subexpr_start_and_end_states() {
        $result = $this->calculate_start_and_end_states_inner(true);
        $this->startstates = $result[0];
        $this->endstates = $result[1];
    }

    /**
     * Calculates states that cause backtrack when generating strings
     */
    public function calculate_backtrack_states() {
        $subpatterns = $this->calculate_start_and_end_states_inner(false);
        $startstates = $subpatterns[0];
        $endstates = $subpatterns[1];
        $states = $this->get_states();
        $result = array();
        // First kind of backtrack states: backreferenced subexpressions
        foreach ($states as $state) {
            $transitions = $this->get_adjacent_transitions($state, true);
            foreach ($transitions as $transition) {
                // Check if the transition starts a backref'd subexpression
                if ($transition->startsbackrefedsubexprs) {
                    $result[$transition->from] = true;
                }
                // Check if the transition starts a recursive subexpression call
                if ($transition->pregleaf->type == qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL && $transition->pregleaf->isrecursive) {
                    $result[$transition->from] = true;
                }
            }
        }

        // Second kind of backtrack states: quantifiers have non-empty intersection with next transitions
        $subpattmap = $this->handler->get_subpatt_number_to_node_map();
        foreach ($endstates as $subpatt => $states) {
            // Check if current subpattern is a quantifier
            $node = $subpattmap[$subpatt];
            if ($node->type != qtype_preg_node::TYPE_NODE_FINITE_QUANT && $node->type != qtype_preg_node::TYPE_NODE_INFINITE_QUANT) {   // TODO: nullable alternation?
                continue;
            }
            // Get quantifier's end state's inner epsilon closure
            $innerclosure = array();
            foreach ($states as $state) {
                $innerclosure = array_merge($innerclosure, $this->get_epsilon_closure($state, true));
            }
            $innertransitions = array();
            foreach ($innerclosure as $state) {
                $innertransitions = array_merge($innertransitions, $this->get_adjacent_transitions($state, false));

                // Crutch: logically, we do not need outer transitions of the start state, but when FA transformation is on,
                // this leads to lack of backtrack states because of merged transitions
                $innertransitions = array_merge($innertransitions, $this->get_adjacent_transitions($state, true));
            }
            // Get quantifier's end state's outer epsilon closure
            $outerclosure = array();
            foreach ($states as $state) {
                $outerclosure = array_merge($outerclosure, $this->get_epsilon_closure($state, false));
            }
            $outertransitions = array();
            foreach ($outerclosure as $state) {
                $outertransitions = array_merge($outertransitions, $this->get_adjacent_transitions($state, true));

                // Crutch: logically, we do not need inner transitions of the end state, but when FA transformation is on,
                // this leads to lack of backtrack states because of merged transitions
                $outertransitions = array_merge($outertransitions, $this->get_adjacent_transitions($state, false));
            }
            // Check for intersections.
            $add = false;
            // First fast check: backreferences
            foreach ($innertransitions as $transition) {
                if ($add || $transition->pregleaf->type == qtype_preg_node::TYPE_LEAF_BACKREF) {
                    $add = true;
                    break;
                }
            }
            foreach ($outertransitions as $transition) {
                if ($transition->loopsback) {
                    continue;
                }
                if ($add || $transition->pregleaf->type == qtype_preg_node::TYPE_LEAF_BACKREF) {
                    $add = true;
                    break;
                }
            }
            // Now check for charset intersections.
            foreach ($innertransitions as $inner) {
                if ($inner->pregleaf->type != qtype_preg_node::TYPE_LEAF_CHARSET) {
                    continue;
                }
                if ($add) {
                    break;
                }
                //echo "inner: {$inner->from} -> {$inner->pregleaf->leaf_tohr()} -> {$inner->to}\n";
                $innerranges = $inner->pregleaf->ranges();
                foreach ($outertransitions as $outer) {
                    if ($outer->pregleaf->type != qtype_preg_node::TYPE_LEAF_CHARSET || $outer->loopsback) {
                        continue;
                    }
                    //echo "outer: {$outer->from} -> {$outer->pregleaf->leaf_tohr()} -> {$outer->to}\n";
                    // Finally check for an intersection
                    $outerranges = $outer->pregleaf->ranges();
                    if (qtype_preg_unicode::intersects($innerranges, $outerranges)) {
                        $add = true;
                        break;
                    }
                }
            }
            if ($add && array_key_exists($subpatt, $startstates)) {
                foreach ($startstates[$subpatt] as $state) {
                    $result[$state] = true;
                }
            }
        }
        return array_keys($result);
    }

    private function states_numbers_to_ids() {
        foreach ($this->statenumbers as $id => &$number) {
            $number = $id;
        }
    }

    /**
     * Calculates start and end states for subpatterns.
     */
    private function calculate_start_and_end_states_inner($subexpronly = false) {
        $startstates = array();
        $endstates = array();
        $states = $this->get_states();
        foreach ($states as $state) {
            $outgoing = $this->get_adjacent_transitions($state, true);
            foreach ($outgoing as $transition) {
                $opentags = $transition->all_open_tags();
                $closetags = $transition->all_close_tags();
                $alltags = array_merge($opentags, $closetags);
                foreach ($alltags as $tag) {
                    // Skip all non-subpatterns
                    if ($tag->subpattern == -1) {
                        continue;
                    }
                    if ($subexpronly && $tag->type != qtype_preg_node::TYPE_NODE_SUBEXPR && $tag->subpattern != $this->handler->get_ast_root()->subpattern) {
                        continue;
                    }
                    // Do not count duplicate subexpressions
                    if ($subexpronly && $tag->type == qtype_preg_node::TYPE_NODE_SUBEXPR && $tag->isduplicate) {
                        continue;
                    }
                    $keys = array();

                    if ($subexpronly) {
                        // Add subexpression number as a key
                        if ($tag->type == qtype_preg_node::TYPE_NODE_SUBEXPR) {
                            $keys[] = $tag->number;
                        }
                        if ($tag->subpattern == $this->handler->get_ast_root()->subpattern) {
                            $keys[] = 0;
                        }
                    } else {
                        // Add subpattern number as a key
                        $keys[] = $tag->subpattern;
                    }

                    $keys = array_values($keys);
                    foreach ($keys as $key) {
                        if (!array_key_exists($key, $startstates)) {
                            $startstates[$key] = array();
                        }
                        if (!array_key_exists($key, $endstates)) {
                            $endstates[$key] = array();
                        }
                        if (in_array($tag, $opentags) && !in_array($transition->from, $startstates[$key])) {
                            $startstates[$key][] = $transition->from;
                        }
                        if (in_array($tag, $closetags) && !in_array($transition->to, $endstates[$key])) {
                            $endstates[$key][] = $transition->to;
                        }
                    }
                }
            }
        }
        return array($startstates, $endstates);
    }

    public function get_epsilon_closure($state, $backwards = false) {
        $result = array($state);
        $current = array($state);
        while (!empty($current)) {
            $cur = array_pop($current);
            $transitions = $this->get_adjacent_transitions($cur, !$backwards);
            foreach ($transitions as $transition) {
                if ($transition->pregleaf->subtype != qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                    continue;
                }
                $interesting = $backwards
                             ? $transition->from
                             : $transition->to;
                if (in_array($interesting, $result)) {
                    continue;
                }
                $result[] = $interesting;
                $current[] = $interesting;
            }
        }
        return $result;
    }

    public function has_transition($from, $to) {
        return array_key_exists($from, $this->adjacencymatrix) && array_key_exists($to, $this->adjacencymatrix[$from]) && !empty($this->adjacencymatrix[$from][$to]);
    }

    /**
     * Return outtransitions of state with id $state.
     *
     * @param state - id of state which outtransitions are intresting.
     * @param outgoing - boolean flag which type of transitions to get (true - outtransitions, false - intotransitions).
     */
    public function get_adjacent_transitions($stateid, $outgoing = true) {
        $result = array();
        if ($outgoing && array_key_exists($stateid, $this->adjacencymatrix)) {
            foreach ($this->adjacencymatrix[$stateid] as $transitions) {
                $result = array_merge($result, $transitions);
            }
        }
        if (!$outgoing) {
            foreach ($this->adjacencymatrix as $row) {
                if (array_key_exists($stateid, $row)) {
                    $result = array_merge($result, $row[$stateid]);
                }
            }
        }
        return $result;
    }

    /**
     * Get array with reak numbers of states of this automata.
     */
    public function get_state_numbers() {
        return $this->statenumbers;
    }

    public function state_exists($state) {
        foreach ($this->states as $curstate) {
            if ($curstate === $state) {
                return true;
            }
        }
        return false;
    }

    /**
     * Passing automata in given direction.
     * @return array with ids of passed states.
     */
    public function reachable_states($backwards = false) {
        // Initialization wavefront.
        $front = $backwards
               ? array_values($this->get_end_states())
               : array_values($this->get_start_states());

        $reached = array();

        while (!empty($front)) {
            $curstate = array_pop($front);
            if (in_array($curstate, $reached)) {
                continue;
            }
            $reached[] = $curstate;
            $transitions = $this->get_adjacent_transitions($curstate, !$backwards);
            foreach ($transitions as $transition) {
                $front[] = $backwards
                         ? $transition->from
                         : $transition->to;
            }
        }
        return $reached;
    }

    /**
     * Delete all blind states in automata.
     */
    public function remove_unreachable_states() {
        // Pass automata forward.
        $aregoneforward = $this->reachable_states(false);
        // Pass automata backward.
        $aregoneback = $this->reachable_states(true);
        // Check for each state of atomata was it gone or not.
        $states = $this->get_states();
        foreach ($states as $curstate) {
            // Current state wasn't passed.
            if (!in_array($curstate, $aregoneforward) || !in_array($curstate, $aregoneback)) {
                $this->remove_state($curstate);
                if (array_key_exists($curstate, $this->innerautomata)) {
                    unset($this->innerautomata[$curstate]);
                }
            }
        }
    }

    /**
     * Write automata as a dot-style string.
     * @param type type of the resulting image, should be 'svg', png' or something.
     * @param filename the absolute path to the resulting image file.
     * @return dot_style string with the description of automata.
     */
    public function fa_to_dot($type = null, $filename = null, $usestateids = false) {
        $start = '';
        $end = '';
        $transitions = '';
        if ($this->statecount != 0) {
            // Add start states.
            foreach ($this->get_states() as $id) {
                $realnumber = $usestateids
                            ? $this->statenumbers[$id]
                            : $id;
                $tmp = '"' . $realnumber . '"';
                if (in_array($id, $this->get_start_states())) {
                    $start .= "{$tmp}[shape=rarrow];\n";
                } else if (in_array($id, $this->get_end_states())) {
                    $end .= "{$tmp}[shape=doublecircle];\n";
                }

                $outgoing = $this->get_adjacent_transitions($id, true);
                foreach ($outgoing as $transition) {
                    $from = $transition->from;
                    $to = $transition->to;
                    if ($usestateids) {
                        $from = $this->statenumbers[$from];
                        $to = $this->statenumbers[$to];
                    }
                    $transitions .= '    ' . $transition->get_label_for_dot($from, $to) . "\n";
                }
            }
        }
        $result = "digraph {\n    rankdir=LR;\n    " . $start . $end . $transitions . "}";
        if ($type != null) {
            $result = qtype_preg_regex_handler::execute_dot($result, $type, $filename);
        }
        return $result;
    }

    /**
     * Add the start state of the automaton to given state.
     */
    public function add_start_state($state, $subpattern = 0) {
        if (!array_key_exists($state, $this->adjacencymatrix)) {
            throw new qtype_preg_exception('set_start_state error: No state ' . $state . ' in automaton');
        }
        if (!in_array($state, $this->get_start_states($subpattern))) {
            if (isset($this->fastartstates[$subpattern])) {
                $this->fastartstates[$subpattern][] = $state;
            } else {
                $this->fastartstates[$subpattern] = array($state);
            }

        }
    }

    /**
     * Add the end state of the automaton to given state.
     */
    public function add_end_state($state, $subpattern = 0) {
        if (!array_key_exists($state, $this->adjacencymatrix)) {
            throw new qtype_preg_exception('set_end_state error: No state ' . $state . ' in automaton');
        }
        if (!in_array($state, $this->get_end_states($subpattern))) {
            if (isset($this->faendstates[$subpattern])) {
                $this->faendstates[$subpattern][] = $state;
            } else {
                $this->faendstates[$subpattern] = array($state);
            }

        }
    }

    /**
     * Remove the end state of the automaton.
     */
    public function remove_end_state($state) {
        unset($this->faendstates[0][array_search($state, $this->faendstates[0])]);
        $this->faendstates[0] = array_values($this->faendstates[0]);
    }

    /**
     * Remove the start state of the automaton.
     */
    public function remove_start_state($state) {
        unset($this->fastartstates[0][array_search($state, $this->fastartstates[0])]);
        $this->fastartstates[0] = array_values($this->fastartstates[0]);
    }

    /**
     * Remove all end states of the automaton.
     */
    public function remove_all_end_states() {
        $this->faendstates[0] = array();
    }

    /**
     * Remove all start states of the automaton.
     */
    public function remove_all_start_states() {
        $this->fastartstates[0] = array();
    }

    /**
     * Set state as copied.
     *
     * @param state - state to be copied.
     */
    public function set_copied_state($state) {
        $number = $this->statenumbers[$state];
        $number = '(' . $number;
        $number .= ')';
        $this->statenumbers[$state] = $number;
    }

    /**
     * Change real number of state.
     *
     * @param state - state to change.
     * @param realnumber - new real number.
     */
    public function change_real_number($state, $realnumber) {
        $this->statenumbers[$state] = $realnumber;
    }

    /**
     * Adds a state to the automaton.
     *
     * @param real number of state.
     * @return state id of added state.
     */
    public function add_state($statenumber = null) {
        if ($statenumber === null) {
            $statenumber = $this->idcounter;
        }
        if (!in_array($statenumber, $this->statenumbers)) {
            $this->adjacencymatrix[] = array();
            $this->statenumbers[] = $statenumber;
            $this->statecount++;
            $this->idcounter++;
            if ($this->statecount > $this->statelimit) {
                throw new qtype_preg_toolargefa_exception('');
            }
        }
        return array_search($statenumber, $this->statenumbers);
    }

    /**
     * Removes a state from the automaton.
     */
    public function remove_state($stateid) {
        // Remove outgoing transitions.
        unset($this->adjacencymatrix[$stateid]);

        // Remove incoming transitions.
        foreach ($this->adjacencymatrix as &$column) {
            if (array_key_exists($stateid, $column)) {
                unset($column[$stateid]);
            }
        }

        // Remove real numbers.
        unset($this->statenumbers[$stateid]);
        $this->statecount--;

        // Remove from start and end states.
        foreach ($this->fastartstates as $subpatt => $states) {
            $key = array_search($stateid, $states);
            if ($key !== false) {
                unset($this->fastartstates[$subpatt][$key]);
            }
        }
        foreach ($this->faendstates as $subpatt => $states) {
            $key = array_search($stateid, $states);
            if ($key !== false) {
                unset($this->faendstates[$subpatt][$key]);
            }
        }

    }

    public function append_inner_automaton($state, $automaton, $direction) {
        if (array_key_exists($state, $this->innerautomata)) {
            $this->innerautomata[$state][] = array($automaton, $direction);
        } else {
            $this->innerautomata[$state] = array(array($automaton, $direction));
        }
    }

    public function change_state_for_intersection($oldkey, $newkeys) {
        if (array_key_exists($oldkey, $this->innerautomata)) {
            foreach ($newkeys as $newkey) {
                foreach ($this->innerautomata[$oldkey] as $innerautomaton) {
                    $this->append_inner_automaton($newkey, $innerautomaton[0], $innerautomaton[1]);
                }
            }

            //unset($this->innerautomata[$oldkey]);
        }
    }

    public function change_recursive_start_states($oldkey, $newkeys) {
        foreach ($this->fastartstates as &$subpattern) {
            if (array_search($oldkey, $subpattern) !== false) {
                $subpattern = array_merge($subpattern, $newkeys);
                //unset($subpattern[array_search($oldkey, $subpattern)]);
            }
        }

    }

    public function change_recursive_end_states($oldkey, $newkeys) {
        foreach ($this->faendstates as &$subpattern) {
            if (array_search($oldkey, $subpattern) !== false) {
                $subpattern = array_merge($subpattern, $newkeys);
               // unset($subpattern[array_search($oldkey, $subpattern)]);
            }
        }
    }

    /**
     * Changes states which transitions come to/from.
     */
    public function redirect_transitions($oldstateid, $newstateid) {
        if ($oldstateid == $newstateid) {
            return;
        }

        // Get all transitions.
        $outgoing = $this->get_adjacent_transitions($oldstateid, true);
        $incoming = $this->get_adjacent_transitions($oldstateid, false);
        $transitions = array_merge($outgoing, $incoming);

        // Remember transitions to be added and remove them.
        $toadd = array();
        foreach ($transitions as $transition) {
            $this->remove_transition($transition);
            // Change "from" and "to" and add the transitions again.
            if ($transition->from == $oldstateid) {
                $transition->from = $newstateid;
            }
            if ($transition->to == $oldstateid) {
                $transition->to = $newstateid;
            }
            // Redirect merged transitions too.
            $transition->redirect_merged_transitions();
            $this->add_transition($transition);
        }

        // Delete the old state.
        $this->remove_state($oldstateid);
    }


    /**
     * Adds a transition.
     */
    public function add_transition($transition) {
        if (!array_key_exists($transition->to, $this->adjacencymatrix[$transition->from])) {
            // No transitions from->to yet.
            $this->adjacencymatrix[$transition->from][$transition->to] = array();
        }
        $this->adjacencymatrix[$transition->from][$transition->to][] = $transition;
        $this->transitioncount++;
        if ($this->transitioncount > $this->transitionlimit) {
            throw new qtype_preg_toolargefa_exception('');
        }
    }

    /**
     * Removes a transition.
     */
    public function remove_transition($transition) {
        $key = array_search($transition, $this->adjacencymatrix[$transition->from][$transition->to]);
        unset($this->adjacencymatrix[$transition->from][$transition->to][$key]);
        $this->transitioncount--;
    }

    /**
     * Check if this state is from intersection part of autmata.
     */
    public function is_intersectionstate($state) {
        return strpos($this->statenumbers[$state], ',') !== false;
    }

    /**
     * Check if this state was copied.
     */
    public function is_copied_state($state) {
        return (strpos($this->statenumbers[$state], ')'));
    }

    /**
     * Check if this state is full intersect state, it means it has two numbers from both automata.
     */
    public function is_full_intersect_state($state) {
        $numbers = $this->statenumbers[$state];
        $number = explode(',', $numbers, 2);
        if (count($number) == 2 && $number[0] != '' && $number[1] != '') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if such state is in array of start states.
     */
    public function has_startstate($state) {
        return in_array($state, $this->get_start_states());
    }

    /**
     * Check if such state is in array of end states.
     */
    public function has_endstate($state) {
        return in_array($state, $this->get_end_states());
    }

    /**
     * Read and create a FA from dot-like language. Mainly used for unit-testing.   TODO: replace subpatt_start with tags
     */
    public static function read_fa($dotstring) {
        StringStreamController::createRef('dot', $dotstring);
        $pseudofile = fopen('string://dot', 'r');
        $lexer = new qtype_preg_dot_lexer($pseudofile);
        $parser = new qtype_preg_dot_parser();
        while (($token = $lexer->nextToken()) !== null) {
            $parser->doParse($token->type, $token->value);
        }
        return $parser->get_automaton();
    }

    /**
     * Compares to FA and returns whether they are equal. Mainly used for unit-testing.
     *
     * @param another qtype_preg_fa object - FA to compare.
     * @return boolean true if this FA equal to $another.
     */
    public function compare_fa($another, &$differences) {
        // TODO
        return false;
    }

    /**
     * Decide if the intersection was successful or not.
     *
     * @param fa qtype_preg_fa object - first automata taking part in intersection.
     * @param anotherfa qtype_preg_fa object - second automata taking part in intersection.
     * @return boolean true if intersection was successful.
     */
    public function has_successful_intersection($fa, $anotherfa, $direction) {
        $issuccessful = false;
        // Analysis of result intersection.
        if ($direction == 0) {
            // Analysis if the end state of intersection includes one of end states of given automata.
            $fastates = $fa->get_end_states();
            $anotherfastates = $anotherfa->get_end_states();
            $states = $this->get_end_states();
        } else {
            // Analysis if the start state of intersection includes one of start states of given automata.
            $fastates = $fa->get_start_states();
            $anotherfastates = $anotherfa->get_start_states();
            $states = $this->get_start_states();
        }
        // Get real numbers.
        $numbers = $fa->get_state_numbers();
        $realfanumbers = array();
        $realanotherfanumbers = array();
        foreach ($fastates as $state) {
            $realfanumbers[] = $numbers[$state];
        }
        $numbers = $anotherfa->get_state_numbers();
        foreach ($anotherfastates as $state) {
            $realanotherfanumbers[] = $numbers[$state];
        }
        $result = array();
        foreach ($states as $state) {
            $result[] = $this->statenumbers[$state];
        }
        // Compare real numbers
        foreach ($realfanumbers as $num1) {
            $num1 = rtrim($num1, ",");
            foreach ($result as $num2) {
                $resnumbers = explode(',', $num2, 2);
                if ($num1 == $resnumbers[0]) {
                    $issuccessful = true;
                }
            }
        }

        foreach ($realanotherfanumbers as $num1) {
            $num1 = ltrim($num1, ",");
            foreach ($result as $num2) {
                $resnumbers = explode(',', $num2, 2);
                if (strpos($resnumbers[1], $num1) === 0 || $resnumbers[1] == $num1) {
                    $issuccessful = true;
                }
            }
        }
        return $issuccessful;
    }


    /**
     * Get connected with given states in given direction.
     *
     * @param state - state for searching connexted.
     * @param direction - direction of searching.
     */
    public function get_connected_states($state, $direction) {
        $result = array();
        $transitions = $this->get_adjacent_transitions($state, !$direction);
        foreach ($transitions as $tran) {
            if ($direction == 0) {
                $result[] = $tran->to;
            } else {
                $result[] = $tran->from;
            }
        }
        return $result;
    }

    /**
     * Modify state for adding to automata which is intersection of two others.
     *
     * @param changedstate - state for modifying.
     * @param origin - origin of automata with this state.
     */
    public function modify_state($changedstate, $origin) {
        $resultstate = $changedstate;
        if ($origin == qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST) {
            $last = substr($changedstate, -1);
            if ($last != ',') {
                $resultstate = $changedstate . ',';
            }
        } else {
            $first = substr($changedstate, 1);
            if ($first != ',') {
                $resultstate = ',' . $changedstate;
            }
        }
        return $resultstate;
    }

    private function has_same_transition($transition) {
        if ($this->has_transition($transition->from, $transition->to)) {

            $innertransitions = $this->adjacencymatrix[$transition->from][$transition->to];
            foreach ($innertransitions as $inner) {
                if ($transition->equal($inner)) {
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * Copy transitions to workstate from automata source in given direction.
     *
     * @param stateswere - states which were in automata.
     * @param statefromsource - state from source automata which transitions are coped.
     * @param memoryfront - states added to automata in last state.
     * @param source - automata-source.
     * @param direction - direction of coping (0 - forward; 1 - back).
     */
    public function copy_transitions($stateswere, $statefromsource, $workstate, $memoryfront, $source, $direction) {
        // Get origin of source automata.
        $states = $source->get_states();
        if (!empty($states)) {
            $keys = array_keys($states);
            $transitions = $source->get_adjacent_transitions($states[$keys[0]], true);
            $keys = array_keys($transitions);
            if (!empty($keys)) {
                $origin = $transitions[$keys[0]]->origin;
            } else {
                $keys = array_keys($states);
                $transitions = $source->get_adjacent_transitions($states[$keys[0]], false);
                $keys = array_keys($transitions);
                if (empty($keys)) {
                    $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
                } else {
                    $origin = $transitions[$keys[0]]->origin;
                }
            }
        }
        // Get transition for analysis.
        if ($direction == 0) {
            $transitions = $source->get_adjacent_transitions($statefromsource, false);
        } else {
            $transitions = $source->get_adjacent_transitions($statefromsource, true);
        }
        $numbers = $source->get_state_numbers();
        // Search transition among states were.
        foreach ($stateswere as $state) {
            // Get real number of source state.
            if ($origin == qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST) {
                $number = rtrim($state, ',');
            } else {
                $number = ltrim($state, ',');
            }
            if (in_array($number, $numbers) && $source->is_copied_state($numbers[array_search($number, $numbers)])) {
                foreach ($transitions as $tran) {
                    if ($direction == 0) {
                        $sourcenum = trim($numbers[$tran->from], '()');
                    } else {
                        $sourcenum = trim($numbers[$tran->to], '()');
                    }
                    if ($sourcenum == $number) {
                        // Add transition.
                        $memstate = array_search($state, $this->statenumbers);
                        $transition = clone $tran;
                        if ($direction == 0) {
                            //$transition = new qtype_preg_fa_transition($memstate, $tran->pregleaf, $workstate, $tran->origin, $tran->consumeschars);
                            $transition->from = $memstate;
                            $transition->to = $workstate;
                        } else {
                            //$transition = new qtype_preg_fa_transition($workstate, $tran->pregleaf, $memstate, $tran->origin, $tran->consumeschars);
                            $transition->to = $memstate;
                            $transition->from = $workstate;
                        }
                        if ($tran->origin == qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND && !($tran->is_eps())) {
                            $transition->consumeschars = false;
                        }
                        $transition->redirect_merged_transitions();
                        $transition->set_transition_type();
                        $hassametransition = $this->has_same_transition($transition);

                        if (!$hassametransition) {
                            $this->add_transition($transition);
                        }
                    }
                }
            }
        }

        // Search transition among states added on last step.
        foreach ($memoryfront as $state) {
            $number = $this->statenumbers[$state];
            $number = trim($number, ',');
            foreach ($transitions as $tran) {
                if ($direction == 0) {
                    $sourcenum = trim($numbers[$tran->from], '()');
                } else {
                    $sourcenum = trim($numbers[$tran->to], '()');
                }
                if ($sourcenum == $number) {
                    $transition = clone $tran;
                    // Add transition.
                    if ($direction == 0) {
                        //$transition = new qtype_preg_fa_transition($state, $tran->pregleaf, $workstate, $tran->origin, $tran->consumeschars);
                        $transition->from = $state;
                        $transition->to = $workstate;
                    } else {
                        //$transition = new qtype_preg_fa_transition($workstate, $tran->pregleaf, $state, $tran->origin, $tran->consumeschars);
                        $transition->to = $state;
                        $transition->from = $workstate;
                    }
                    if ($tran->origin == qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND && !($tran->is_eps())) {
                        $transition->consumeschars = false;
                    }
                    $transition->redirect_merged_transitions();
                    $transition->set_transition_type();
                    $hassametransition = $this->has_same_transition($transition);
                    if (!$hassametransition) {
                        $this->add_transition($transition);
                    }
                }
            }
        }
    }

    /**
     * Copy and modify automata to stopcoping state or to the end of automata, if stopcoping == NULL.
     *
     * @param source - automata-source for coping.
     * @param oldfront - states from which coping starts.
     * @param stopcoping - array of states to which automata will be copied.
     * @param direction - direction of coping (0 - forward; 1 - back).
     * @return automata after coping.
     */
    public function copy_modify_branches($source, $oldfront, $stopcoping, $direction, $origin = null) {
        $resultstop = array();
        $memoryfront = array();
        $newfront = array();
        $newmemoryfront = array();
        // Getting origin of automata.
        $states = $source->get_states();
        if ($origin === null && !empty($states)) {
            $keys = array_keys($states);
            $transitions = $source->get_adjacent_transitions($states[$keys[0]], true);
            $keys = array_keys($transitions);
            if (!empty($keys)) {
                $origin = $transitions[$keys[0]]->origin;
            } else {
                $keys = array_keys($states);
                $transitions = $source->get_adjacent_transitions($states[$keys[0]], false);
                $keys = array_keys($transitions);
                if (empty($keys)) {
                    $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
                } else {
                    $origin = $transitions[$keys[0]]->origin;
                }
            }
        }
        // Getting all states which are in automata for coping.
        $stateswere = $this->get_state_numbers();
        // Cleaning end states.
        $this->remove_all_end_states();
        // Coping.
        while (!empty($oldfront)) {
            foreach ($oldfront as $curstate) {
                if (!$source->is_copied_state($curstate)) {

                    // Modify states.
                    $changedstate = $source->statenumbers[$curstate];
                    $changedstate = $this->modify_state($changedstate, $origin);
                    // Mark state as copied state.
                    $source->set_copied_state($curstate);
                    $isfind = false;
                    // Search among states which were in automata.
                    if (in_array($changedstate, $stateswere)) {
                        $isfind = true;
                        $workstate = array_search($changedstate, $stateswere);
                    }

                    // Hasn't such state.
                    if (!$isfind) {
                        $this->add_state($changedstate);
                        $workstate = array_search($changedstate, $this->statenumbers);
                        $this->copy_transitions($stateswere, $curstate, $workstate, $memoryfront, $source, $direction);
                        // Check end of coping.
                        if ($stopcoping !== null && array_search($curstate, $stopcoping) !== false) {
                            if ($direction == 0) {
                                $this->add_end_state($workstate);
                            }
                            if (!in_array($workstate, $resultstop)) {
                                $resultstop[] = $workstate;
                            }
                            $connectedstates = $source->get_connected_states($curstate, $direction);
                            $connectedstopstates = array();
                            foreach ($connectedstates as $connected) {
                                if (in_array($connected, $stopcoping) && !in_array($connected, $connectedstopstates)) {
                                    $connectedstopstates[] = $connected;
                                }
                            }
                            $newmemoryfront[] = $workstate;
                            if (count($connectedstopstates) + 1 == count($stopcoping)) {
                                $connectedstates = $connectedstopstates;

                                // Adding connected states.

                                $newfront = array_merge($newfront, $connectedstates);
                            }
                        } else {
                            $newmemoryfront[] = $workstate;
                            // Adding connected states.

                            $connectedstates = $source->get_connected_states($curstate, $direction);
                            $newfront = array_merge($newfront, $connectedstates);
                        }

                    } else {
                        $this->copy_transitions($stateswere, $curstate, $workstate, $memoryfront, $source, $direction);
                        $newmemoryfront[] = $workstate;
                        // Adding connected states.
                        $connectedstates = $source->get_connected_states($curstate, $direction);
                        $newfront = array_merge($newfront, $connectedstates);
                    }
                } else {
                    $changedstate = $source->statenumbers[$curstate];
                    $changedstate = trim($changedstate, '()');
                    $changedstate = $this->modify_state($changedstate, $origin);
                    $workstate = array_search($changedstate, $this->statenumbers);
                    if ($stopcoping === null || !in_array($workstate, $stopcoping) || (in_array($workstate, $stopcoping) && in_array($workstate, $memoryfront))) {
                        $this->copy_transitions($stateswere, $curstate, $workstate, $memoryfront, $source, $direction);
                    }
                }
            }
            $oldfront = $newfront;
            $memoryfront = $newmemoryfront;
            $newfront = array();
            $newmemoryfront = array();
        }
        $sourcenumbers = $source->get_state_numbers();
        // Add start states if fa has no one.
        if (count($this->get_start_states()) == 0) {
            $sourcestart = $source->get_start_states();
            foreach ($sourcestart as $start) {
                $realnumber = $sourcenumbers[$start];
                $realnumber = trim($realnumber, '()');
                $newstart = array_search($this->modify_state($realnumber, $origin), $this->statenumbers);
                if ($newstart !== false) {
                    $this->add_start_state($newstart);
                }
            }
        }

        $sourceend = $source->get_end_states();
        foreach ($sourceend as $end) {
            $realnumber = $sourcenumbers[$end];
            $realnumber = trim($realnumber, '()');
            $newend = array_search($this->modify_state($realnumber, $origin), $this->statenumbers);
            if ($newend !== false) {
                // Get last copied state.
                if (empty($resultstop)) {
                    $resultstop[] = $newend;
                }
                $this->add_end_state($newend);
            }
        }
        // Remove flag of coping from states of source automata.
        $source->remove_flags_of_coping();
        return $resultstop;
    }

    public function merge_end_transitions() {
        foreach ($this->get_end_states() as $end) {
            $endtransitions = $this->get_adjacent_transitions($end, false);
            foreach ($endtransitions as $endtran) {
                $beforeeps = true;
                foreach ($endtran->mergedbefore as $before) {
                    $beforeeps = $beforeeps && !$before->is_end_anchor();
                }
                if ($endtran->is_eps() && $endtran->from != $endtran->to && $beforeeps) {
                    $wasadded = false;
                    $canmerge = true;
                    $transitions = $this->get_adjacent_transitions($endtran->from, false);
                    foreach ($transitions as $tran) {
                        if ($tran->from === $tran->to) {
                            $canmerge = false;
                        }
                    }
                    if ($canmerge) {
                        foreach ($transitions as $tran) {
                            if ($tran->from !== $tran->to) {
                                $clonetran = clone $tran;
                                $delclone = clone $endtran;
                                $delclone->mergedafter = array();
                                $delclone->mergedbefore = array();
                                $delclonemerged = clone $endtran;
                                $clonetran->loopsback = $tran->loopsback || $endtran->loopsback;
                                $clonetran->greediness = qtype_preg_fa_transition::min_greediness($tran->greediness, $delclone->greediness);
                                $merged = array_merge($delclonemerged->mergedbefore, array($delclone), $delclonemerged->mergedafter);
                                // Work with tags.
                                $merged = array_merge($merged, $clonetran->mergedafter);
                                $clonetran->mergedafter = $merged;
                                $clonetran->to = $endtran->to;
                                $clonetran->redirect_merged_transitions();
                                $this->add_transition($clonetran);
                                $wasadded = true;
                            }
                        }
                        if ($wasadded) {
                            $this->change_state_for_intersection($endtran->from, array($endtran->to));
                            $this->remove_transition($endtran);

                        }
                    }
                }

            }
        }
    }

    /**
     * Check if there is such state in intersection part and add modified version of it.
     *
     * @param anotherfa - second automata, which toke part in intersection.
     * @param transition - transition for checking.
     * @param laststate - last added state.
     * @param realnumber - real number of serching state.
     * @param direction - direction of checking (0 - forward; 1 - back).
     * @return flag if it was possible to add another version of state.
     */
    public function has_same_state($anotherfa, $transition, $laststate, &$clones, &$realnumber, $direction) {
        $oldfront = array();
        $isfind = false;
        $hasintersection = false;
        $aregone = array();
        $newfront = array();
        // Get right clones in case of divarication.
        $clones = array();
        $clones[] = $transition;
        $numbers = explode(',', $realnumber, 2);
        $numbertofind = $numbers[0];
        $addnum = $numbers[1];
        $oldfront[] = $laststate;
        $secnumbers = $anotherfa->get_state_numbers();

        // While there are states for analysis.
        while (count($oldfront) != 0 && !$isfind) {
            foreach ($oldfront as $state) {
                $aregone[] = $state;
                $numbers = explode(',', $this->statenumbers[$state], 2);
                // State with same number is found.
                if ($numbers[0] == $numbertofind && $numbers[1] !== '') {
                    // State with same number was found and there is one more.
                    if ($isfind) {
                        $clones[] = $clones[count($clones) - 1];
                        // Get added numbers
                        $tran = $clones[count($clones) - 2];
                    } else {
                        // State wasn't found earlier but this state is a searched state.
                        $isfind = true;
                        $tran = $transition;
                    }
                    if ($direction == 0) {
                        $clone = $tran->to;    // TODO:
                    } else {
                        $clone = $tran->from;  // unused
                    }
                    $addnumber = $numbertofind . ',' . $addnum . '   ' . $numbers[1];
                    foreach ($secnumbers as $num) {
                        if (strpos($numbers[1], $num) === 0 || $numbers[1] == $num) {
                            $statefromsecond = array_search($num, $secnumbers);
                        }
                    }

                    $transitions = $anotherfa->get_adjacent_transitions($statefromsecond, $direction);
                    $transitions = array_values($transitions);

                    // There are transitions for analysis.
                    if (count($transitions) != 0) {
                        $intertran = $tran->intersect($transitions[0]);
                        if ($intertran !== null) {
                            $hasintersection = true;
                            // Form new transition.
                            $addstate = $this->add_state($addnumber);
                            $realnumber = $addnumber;
                            if ($direction == 0) {
                                $tran->to = $addstate;
                            } else {
                                $tran->from = $addstate;
                            }
                        }
                    } else {
                        // Form new transition.
                        $hasintersection = true;
                        $addstate = $this->add_state($addnumber);
                        $realnumber = $addnumber;
                        if ($direction == 0) {
                            $tran->to = $addstate;
                        } else {
                            $tran->from = $addstate;
                        }
                    }
                } else {
                    // Add connected states to new wave front.
                    if ($direction == 0) {
                        $conectstates = $this->get_connected_states($state, 1);
                    } else {
                        $conectstates = $this->get_connected_states($state, 0);
                    }
                    foreach ($conectstates as $conectstate) {
                        if (!in_array($conectstate, $newfront) && !in_array($conectstate, $aregone)) {
                            $newfront[] = $conectstate;
                        }
                    }
                }
            }
            $oldfront = $newfront;
            $newfront = array();
        }
        if (!$isfind) {
            $hasintersection = true;
        }
        return $hasintersection;
    }

    /**
     * Get transitions from automata for intersection.
     *
     * @param workstate state for getting transitions.
     * @param direction direction of intersection.
     * @return array of transitions for intersection.
     */
    public function get_transitions_for_intersection($workstate, $direction) {
        $transitions = $this->get_adjacent_transitions($workstate, !$direction);
        return $transitions;
    }


    /**
     * Generate real number of state from intersection part.
     *
     * @param firststate real number of state from first automata.
     * @param secondstate real number of state from second automata.
     * @return real number of state from intersection part.
     */
    public function get_inter_state($firststate, $secondstate) {
        $first = trim($firststate, '(,)');
        $second = trim($secondstate, '()');
        $state = $first . ',' . $second;
        return $state;
    }

    /**
     * Find state which should be added in way of passing cycle.
     *
     * @param anotherfa object automaton to find.
     * @param resulttransitions array of intersected transitions.
     * @param curstate last added state.
     * @param clones transitions appeared in case of several ways.
     * @param realnumber real number of $curstate.
     * @param index index of transition in $resulttransitions for analysis.
     * @return boolean flag if automata has state which should be added in way of passing cycle.
     */
    public function have_add_state_in_cycle($anotherfa, &$resulttransitions, $curstate, &$clones, &$realnumber, $index, $direction) {
        $resnumbers = $this->get_state_numbers();
        $hasalready = false;
        $wasdel = false;
        // No transitions from last state.
        if (count($clones) <= 1) {
            $ispossible = $this->has_same_state($anotherfa, $resulttransitions[$index], $curstate, $clones, $realnumber, $direction);
            // It's possible to add state in case of having state.
            if ($ispossible) {
                // Search same state in result automata.
                $searchnumbers = explode(',', $realnumber, 2);
                $searchnumber = $searchnumbers[0];
                foreach ($resnumbers as $resnum) {
                    $pos = strpos($resnum, $searchnumber);
                    if ($pos !== false && $pos < strpos($resnum, ',') && $searchnumbers[1] == '') {
                        $hasalready = true;
                    }
                }
            } else {
                // It's impossible to add state.
                unset($resulttransitions[$index]);
                $wasdel = true;
            }
        } else {
            // Has transitions from previous states.
            if (in_array($realnumber, $resnumbers)) {
                $hasalready = true;
            }
            unset($clones[count($clones) - 2]);
        }
        if ($hasalready || $wasdel) {
            return true;
        } else {
            // Coping transition copies.
            if (count($clones) > 1) {
                for ($i = count($clones) - 2; $i >= 0; $i--) {
                    // TODO - add after index in array.
                    $resulttransitions[] = $clones[$i];
                }
            }
            return false;
        }
    }

    /**
     * Find cycle in the automata.
     *
     * @return flag if automata has cycle or not.
     */
    public function has_cycle() {// TODO: запоминать есть ли циклы при построении
        $newfront = array();
        $aregone = array();
        $hascycle = false;
        $states = $this->get_state_numbers();
        // Add start states to wave front.
        $oldfront = $this->get_start_states();

        // Analysis sattes from wave front.
        while (count($oldfront) != 0) {
            foreach ($oldfront as $curstate) {
                // State hasn't been  already gone.
                if (!in_array($curstate, $aregone)) {
                    // Mark as gone.
                    $aregone[] = $curstate;
                    // Get connected states if they are.
                    $connectedstates = $this->get_connected_states($curstate, 0);
                    $newfront = array_merge($newfront, $connectedstates);
                } else {
                    // Analysis intotransitions.
                    $transitions = $this->get_adjacent_transitions($curstate, false);
                    foreach ($transitions as $tran) {
                        // Transition has come from state which is far in automata.
                        if ($states[$tran->from] > $states[$curstate]) {
                            $hascycle = true;
                        }
                    }
                }
            }
            $oldfront = $newfront;
            $newfront = array();
        }
        return $hascycle;
    }

    /**
     * Set right start and end states after before completing branches.
     *
     * @param fa object automaton taken part in intersection.
     * @param anotherfa object automaton second automaton taken part in intersection.
     */
    public function set_start_end_states_before_coping($fa, $anotherfa) {
        // Get nessesary data.
        $faends = $fa->get_end_states();
        $anotherfaends = $anotherfa->get_end_states();
        $fastarts = $fa->get_start_states();
        $anotherfastarts = $anotherfa->get_start_states();
        $fastates = $fa->get_state_numbers();
        $anotherfastates = $anotherfa->get_state_numbers();
        $states = $this->get_state_numbers();
        // Set right start and end states.
        foreach ($states as $statenum) {
            // Get states from first and second automata.
            $numbers = explode(',', $statenum, 2);
            if ($numbers[0] !== '') {
                $workstate1 = array_search($numbers[0], $fastates);
            }
            if ($numbers[1] != '') {
                foreach ($anotherfastates as $num) {
                    if (strpos($numbers[1], $num) === 0 || $numbers[1] == $num) {
                        $workstate2 = array_search($num, $anotherfastates);
                    }
                }
            }
            $state = array_search($statenum, $this->statenumbers);
            // Set start states.
            $isfirststart = $numbers[0] !== '' && in_array($workstate1, $fastarts);
            $issecstart = $numbers[1] !== '' && in_array($workstate2, $anotherfastarts);
            if (($isfirststart || $issecstart) && count($this->get_adjacent_transitions($state, false)) == 0) {
                $this->add_start_state(array_search($statenum, $this->statenumbers));
            }
            // Set end states.
            $isfirstend = $numbers[0] !== '' && in_array($workstate1, $faends);
            $issecend = $numbers[1] !== '' && in_array($workstate2, $anotherfaends);
            if (($isfirstend || $issecend) /*&& count($this->get_adjacent_transitions($state, true)) == 0*/) {
                $this->add_end_state(array_search($statenum, $this->statenumbers));
            }
        }
    }

    /**
     * Set right start and end states after inetrsection two automata.
     *
     * @param fa object automaton taken part in intersection.
     * @param anotherfa object automaton second automaton taken part in intersection.
     */
    public function set_start_end_states_after_intersect($fa, $anotherfa) {
        // Get nessesary data.
        $faends = $fa->get_end_states();
        $anotherfaends = $anotherfa->get_end_states();
        $fastarts = $fa->get_start_states();
        $anotherfastarts = $anotherfa->get_start_states();
        $fastates = $fa->get_state_numbers();
        $anotherfastates = $anotherfa->get_state_numbers();
        $states = $this->get_state_numbers();
        // Set right start and end states.
        foreach ($states as $statenum) {
            // Get states from first and second automata.
            $numbers = explode(',', $statenum, 2);
            if ($numbers[0] != '') {
                $workstate1 = array_search($numbers[0], $fastates);
            }

            if ($numbers[1] != '') {
                foreach ($anotherfastates as $num) {
                    if (strpos($numbers[1], $num) === 0 || $numbers[1] == $num) {
                        $workstate2 = array_search($num, $anotherfastates);
                    }
                }
            }
            // Set start states.
            $isfirststart = ($numbers[0] !== '' && in_array($workstate1, $fastarts)) || $numbers[0] == '';
            $issecstart = ($numbers[1] !== '' && in_array($workstate2, $anotherfastarts)) || $numbers[1] == '';
            if ($isfirststart && $issecstart) {
                $this->add_start_state(array_search($statenum, $this->statenumbers));
            }
            // Set end states.
            $isfirstend = ($numbers[0] !== '' && in_array($workstate1, $faends)) || $numbers[0] == '';
            $issecend = ($numbers[1] !== '' && in_array($workstate2, $anotherfaends)) || $numbers[1] == '';
            if ($isfirstend && $issecend) {
                $this->add_end_state(array_search($statenum, $this->statenumbers));
            }
        }
    }

    /**
     * Return count of states from second automata which includes state from intersection.
     *
     * @param anotherfa object automaton second automaton taken part in intersection.
     * @param state id of state from intersection for counting.
     */
    public function get_second_numbers_count($anotherfa, $state) {
        $count = 0;
        $numbers = $this->get_state_numbers();
        $anotherfanumbers = $anotherfa->get_state_numbers();
        $realnum = $numbers[$state];
        $realsecond = explode(',', $realnum, 2);
        $realsecond = $realsecond[1];
        foreach ($anotherfanumbers as $curnum) {
            if (strpos($realsecond, $curnum) !== false || $realsecond == $curnum) {
                $count++;
            }
        }
        return $count;
    }

    private function find_state($state) {
        foreach ($this->statenumbers as $key => $curstate) {
            $statenumber = rtrim($curstate, ',');
            $statenumber = ltrim($statenumber, ',');
            if ($statenumber == $state) {
                return $key;
            }
        }
        return false;
    }

    /**
     * Find intersection part of automaton in case of intersection it with another one.
     *
     * @param anotherfa object automaton to intersect.
     * @param result object automaton to write intersection part.
     * @param start array of states of $this automaton with which to start intersection.
     * @param direction boolean intersect by superpose start or end state of anotherfa with stateindex state.
     * @param withcycle boolean intersect in case of forming right cycle.
     * @return result automata.
     */
    public function get_intersection_part($anotherfa, &$result, $start, $direction, $withcycle) {
        $oldfront = array();
        $newfront = array();
        $clones = array();
        $possibleend = array();

        $oldfront = $start;
        // Work with each state.
        while (!empty($oldfront)) {
            foreach ($oldfront as $curstate) {
                // Get states from first and second automata.
                $secondnumbers = $anotherfa->get_state_numbers();
                $resnumbers = $result->get_state_numbers();
                $resultnumber = $resnumbers[$curstate];

                $numbers = explode(',', $resultnumber, 2);
                $workstate1 = $this->find_state($numbers[0]);
                foreach ($secondnumbers as $num) {
                    if (strpos($numbers[1], $num) === 0 || $numbers[1] == $num) {
                        $workstate2 = array_search($num, $secondnumbers);

                    }
                }
                // Get transitions for ntersection.
                $intertransitions1 = $this->get_transitions_for_intersection($workstate1, $direction);
                foreach ($intertransitions1 as &$tran) {
                    if ($tran->is_eps() && $tran->origin === qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND) {
                        unset($tran);
                    }
                }
                $intertransitions2 = $anotherfa->get_transitions_for_intersection($workstate2, $direction);
                // Intersect all possible transitions.
                $resulttransitions = array();
                $resultnumbers = array();
                foreach ($intertransitions1 as $intertran1) {
                    foreach ($intertransitions2 as $intertran2) {
                        $resulttran = $intertran1->intersect($intertran2);
                        if ($resulttran !== null) {
                            $resulttransitions[] = $resulttran;
                            if ($direction == 0) {
                                $resultnumbers[] = $result->get_inter_state($this->statenumbers[$intertran1->to], $secondnumbers[$intertran2->to]);
                            } else {
                                $resultnumbers[] = $result->get_inter_state($this->statenumbers[$intertran1->from], $secondnumbers[$intertran2->from]);
                            }
                        }
                    }
                }
                // Analysis result transitions.
                for ($i = 0; $i < count($resulttransitions); $i++) {
                    // Search state with the same number in result automata.
                    if ($withcycle) {
                        $searchstate = $result->have_add_state_in_cycle($anotherfa, $resulttransitions, $curstate, $clones, $resultnumbers[$i], $i, $direction);
                    } else {
                        $searchstate = array_search($resultnumbers[$i], $resnumbers);
                    }
                    // State was found.
                    if ($searchstate !== false) {
                        $resnumbers = $result->get_state_numbers();
                        $newstate = array_search($resultnumbers[$i], $resnumbers);
                    } else {
                        // State wasn't found.
                        $newstate = $result->add_state($resultnumbers[$i]);
                        $newfront[] = $newstate;
                    }
                    $resnumbers = $result->get_state_numbers();
                    // Change transitions.
                    if ($direction == 0) {
                        $resulttransitions[$i]->from = $curstate;
                        $resulttransitions[$i]->to = $newstate;
                    } else {
                        $resulttransitions[$i]->from = $newstate;
                        $resulttransitions[$i]->to = $curstate;
                    }
                    $resulttransitions[$i]->redirect_merged_transitions();
                    if (!$result->has_same_transition($resulttransitions[$i]))
                    {
                        $result->add_transition($resulttransitions[$i]);
                    }
                }
                // Removing arrays.
                $intertransitions1 = array();
                $intertransitions2 = array();
                $resulttransitions = array();
                $resultnumbers = array();
            }
            $possibleend = $oldfront;
            $oldfront = $newfront;
            $newfront = array();
        }

        // Set right start and end states.
        if ($direction == 0) {
            // Cleaning end states.
            $result->remove_all_end_states();
            foreach ($possibleend as $end) {
                $result->add_end_state($end);
            }
        } else {
            // Cleaning start states.
            $startstates = $result->get_start_states();
            foreach ($startstates as $startstate) {
                if ($result->is_full_intersect_state($startstate)) {
                    $result->remove_start_state($startstate);
                }
            }
            // Add new start states.
            $state = $result->get_inter_state(0, 0);
            $resnumbers = $result->get_state_numbers();
            $state = array_search($state, $resnumbers);
            if ($state) {
                $result->add_start_state($state);
            } else {
                foreach ($possibleend as $start) {
                    $result->add_start_state($start);
                }
            }
        }

        // Get cycle if it's nessessary.
        $newfront = array();
        $resultnumbers = $result->get_state_numbers();
        if ($withcycle) {
            foreach ($possibleend as $state) {
                $aregone = array();
                $isfind = false;
                $divfind = false;
                $searchnumbers = explode(',', $resultnumbers[$state], 2);
                $numbertofind = $searchnumbers[0];
                $oldfront = $result->get_connected_states($state, !$direction);
                $secondnumberscount = $result->get_second_numbers_count($anotherfa, $state);
                // Analysis states of automata serching interecsting state.
                while (!empty($oldfront) && !$isfind) {
                    foreach ($oldfront as $curstate) {
                        $aregone[] = $curstate;
                        $curnumberscount = $result->get_second_numbers_count($anotherfa, $curstate);
                        if (!$divfind && $secondnumberscount != $curnumberscount) {
                            $divfind = true;
                            //$divstate = $curstate;
                        }
                        $divstate = $curstate;
                        $numbers = explode(',', $resultnumbers[$curstate], 2);

                        // State with same number is found.
                        if ($numbers[0] == $numbertofind && $numbers[1] !== '' && strpos($searchnumbers[1], $numbers[1]) !== false) {
                            if ($direction == 0) {
                                $transitions = $result->get_adjacent_transitions($curstate, true);
                                foreach ($transitions as $tran) {
                                    $clonetran = clone($tran);
                                    $clonetran->from = $state;
                                    $clonetran->redirect_merged_transitions();
                                    if (!$result->has_same_transition($clonetran))
                                    {
                                        $result->add_transition($clonetran);
                                    }
                                }
                            } else {
                                $realdiv = explode(',', $resultnumbers[$divstate], 2);
                                if ($realdiv[0] == $numbertofind) {
                                    $newpregleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
                                    $addtran = new qtype_preg_fa_transition ($divstate, $newpregleaf, $state, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);
                                    $result->add_transition($addtran);
                                } else {
                                    $lastcopied = false;
                                    $frontstate = $curstate;
                                    $clonestate = null;
                                    // Coping states to the state which is last in cycle.
                                    while (!$lastcopied) {
                                        $transitions = $result->get_adjacent_transitions($frontstate, false);
                                        // Analasis transitions.
                                        foreach ($transitions as $tran) {
                                            // Check should we copy this state or not.
                                            if ($tran->from == $divstate) {
                                                // No nessesary of coping.
                                                $fromtran = clone($tran);
                                                $fromtran->to = $clonestate;
                                                $fromtran->redirect_merged_transitions();
                                                $result->add_transition($fromtran);
                                                $lastcopied = true;
                                            } else {
                                                // We should copy.
                                                $newnumber = $resultnumbers[$tran->from];
                                                $newnumber = '(' . $newnumber . ')';
                                                $fromtran = clone($tran);
                                                if ($clonestate === null) {
                                                    $fromtran->to = $state;
                                                } else {
                                                    $fromtran->to = $clonestate;
                                                }
                                                $clonestate = $result->add_state($newnumber);
                                                $fromtran->from = $clonestate;
                                                $fromtran->redirect_merged_transitions();
                                                $result->add_transition($fromtran);
                                                $frontstate = $tran->from;
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            // Add connected states to new wave front.
                            if ($direction == 0) {
                                $conectstates = $result->get_connected_states($curstate, 1);
                            } else {
                                $conectstates = $result->get_connected_states($curstate, 0);
                            }
                            foreach ($conectstates as $conectstate) {
                                if (!in_array($conectstate, $newfront) && !in_array($conectstate, $aregone)) {
                                    $newfront[] = $conectstate;
                                }
                            }
                        }
                    }
                    $oldfront = $newfront;
                    $newfront = array();
                }
            }
        }
        return $result;
    }

    /**
     * Lead all end states to one with epsilon-transitions.
     */
    public function lead_to_one_end() {
        $newleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $i = count($this->get_end_states()) - 1;
        if ($i > 0) {
            $to = $this->faendstates[0][0];
        }
        // Connect end states with first while automata has only one end state.
        while ($i > 0) {
            $exendstate = $this->faendstates[0][$i];
            $epstran = new qtype_preg_fa_transition ($exendstate, $newleaf, $to);
            $this->add_transition($epstran);
            $i--;
            $this->remove_end_state($exendstate);
        }
        /*$i = count($this->get_start_states()) - 1;
        if ($i > 0) {
            $from = $this->fastartstates[0][0];
        }
        // Connect end states with first while automata has only one end state.
        while ($i > 0) {
            $exendstate = $this->fastartstates[0][$i];
            $epstran = new qtype_preg_fa_transition ($from, $newleaf, $exendstate);
            $this->add_transition($epstran);
            $i--;
            $this->remove_start_state($exendstate);
        }*/
    }

    /**
     * Intersect automaton with another one.
     *
     * @param anotherfa object automaton to intersect.
     * @param stateindex array of string with real number of state of $this automaton with which to start intersection.
     * @param isstart boolean intersect by superpose start or end state of anotherfa with stateindex state.
     * @return result automata.
     */
    public function intersect($anotherfa, $stateindex, $isstart) {
        // Check right direction.
        if ($isstart != 0 && $isstart !=1) {
            throw new qtype_preg_exception('intersect error: Wrong direction');
        }
        // Prepare automata for intersection.
        $this->remove_unreachable_states();
        $anotherfa->remove_unreachable_states();
        $numbers = array();
        foreach ($stateindex as $index) {
            $number = array_search($index, $this->statenumbers);
            if ($number !== false && !in_array($number, $numbers)) {
                $numbers[] = $number;
            }
            //$numbers[] = $number;
        }
        //var_dump($numbers);
        $this->to_origin(qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST);
        $anotherfa->to_origin(qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND);
        $result = $this->intersect_fa($anotherfa, $numbers, $isstart);

        $result->remove_unreachable_states();
        $result->remove_wrong_end_states();
        $result->lead_to_one_end();
        $result->merge_after_intersection();
        //$result->merge_end_transitions();
        $result->handler = $this->handler;
        $result->update_intersection_states($this);
        $result->states_numbers_to_ids();
        return $result;
    }

    private function to_origin($origin) {
        foreach ($this->adjacencymatrix as $from) {
            foreach ($from as $to) {
                foreach ($to as $transition) {
                    $transition->origin = $origin;
                }
            }
        }
    }

    private function merge_after_intersection() {
        $startstates = $this->get_start_states();
        $front = $startstates;
        $stackitem = array();
        $success = false;
        $newfront = array();
        $states = array();
        while (!empty($front)) {
            foreach ($front as $state) {
                if (!in_array($state, $states)) {
                    $transitions = $this->get_adjacent_transitions($state, true);
                    foreach ($transitions as $transition) {
                        if (($transition->is_start_anchor() || $transition->is_end_anchor() || $transition->is_eps())) {
                            $stackitem['end'] = $this->get_end_states();
                            $success = $this->merge_transitions($transition, $stackitem) || $success;
                        }
                        $newfront[] = $transition->to;
                    }
                    $states[] = $state;
                }
            }

            $front = $newfront;
            $newfront = array();
        }
        if ($success) {
            $this->merge_after_intersection();
        }
    }

    /**
     * Merging transitions without merging states.
     *
     * @param del - uncapturing transition for deleting.
     */
    public function merge_transitions($del, &$stackitem, $back = null) {
        $clonetransitions = array();
        $tagsets = array();
        $fromstates = array();
        $tostates = array();
        $oppositetransitions = array();
        $intersection = null;
        $transitionadded = false;
        $flag = new qtype_preg_charset_flag();
        $flag->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string("\n"));
        $charset = new qtype_preg_leaf_charset();
        $charset->flags = array(array($flag));
        $charset->userinscription = array(new qtype_preg_userinscription("\n"));
        $righttran = new qtype_preg_fa_transition(0, $charset, 1);
        $outtransitions = $this->get_adjacent_transitions($del->to, true);
        if (!is_array($stackitem['end'])) {
            $endstates = array($stackitem['end']);
        } else {
            $endstates = $stackitem['end'];
        }

        // Cycled last states.
        if ((in_array($del->to, $endstates) && $del->is_eps()) || !$del->consumeschars) {
            return false;
        }

        if ($back === null) {

            // Get transitions for merging back.
            if (($del->is_unmerged_assert() && $del->is_start_anchor()) || ($del->is_eps() && in_array($del->to, $endstates))) {
                $transitions = $this->get_adjacent_transitions($del->from, false);
                $tostates[] = $del->to;
                $back = true;
            } else {
                // Get transitions for merging forward.
                $transitions = $this->get_adjacent_transitions($del->to, true);
                $fromstates[] = $del->from;
                $back = false;
            }
        } else {

            if ($back) {
                $transitions = $this->get_adjacent_transitions($del->from, false);
            } else {
                $transitions = $this->get_adjacent_transitions($del->to, true);
            }
        }

        // Changing leafs in case of merging.
        foreach ($transitions as $transition) {
            if (!($transition->from === $transition->to && ($transition->is_unmerged_assert() || $transition->is_eps()))) {
                $tran = clone $transition;
                $delclone = clone $del;
                $delclone->mergedafter = array();
                $delclone->mergedbefore = array();
                $delclonemerged = clone $del;
                $tran->loopsback = $transition->loopsback || $del->loopsback;
                $tran->greediness = qtype_preg_fa_transition::min_greediness($tran->greediness, $del->greediness);
                $merged = array_merge($delclonemerged->mergedbefore, array($delclone), $delclonemerged->mergedafter);
                // Work with tags.
                if (!$tran->consumeschars && $del->is_eps() && $del->from !== $del->to && $tran->origin !== qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND) {
                    if ($back) {
                        $tran->mergedbefore = array_merge($tran->mergedbefore, $merged);
                    } else {
                        $tran->mergedafter = array_merge($merged, $tran->mergedafter);
                    }
                } else if ($back) {
                    $tran->mergedafter = array_merge($tran->mergedafter, $merged);
                } else {
                    $tran->mergedbefore = array_merge($merged, $tran->mergedbefore);
                }

                $clonetransitions[] = $tran;
            }

        }
        // Has deleting or changing transitions.
        if (empty($transitions)) {
            return false;
        }

        $breakpos = null;
        $newkeys = array();
        if (!$back) {
            foreach ($clonetransitions as &$tran) {
                $tostates[] = $tran->to;
                if ($del->is_end_anchor() && !$tran->is_unmerged_assert() && !$tran->is_eps()) {
                    $righttran->pregleaf->position = $tran->pregleaf->position;
                    $intersection = $tran->intersect($righttran);
                    if ($intersection !== null) {
                        $tran->pregleaf = $intersection->pregleaf;
                    }
                }

                if (($del->pregleaf->subtype !== qtype_preg_leaf_assert::SUBTYPE_SMALL_ESC_Z && $intersection !== null) ||
                    !$del->is_end_anchor() || $tran->is_unmerged_assert() || $tran->is_eps()) {
                    $tran->from = $del->from;
                    $tran->redirect_merged_transitions();
                    $this->add_transition($tran);
                    $newkeys[] = $tran->to;
                    $transitionadded = true;
                } else if ($breakpos === null) {
                    $breakpos = $del->pregleaf->position->compose($tran->pregleaf->position);
                }
            }
            unset($tran);
            $this->change_state_for_intersection($del->to, array($del->from));
            $this->change_recursive_start_states($del->to, array($del->from));
            $this->change_recursive_end_states($del->to, $newkeys);
        } else {
            foreach ($clonetransitions as &$tran) {
                $fromstates[] = $tran->from;
                if ($del->is_start_anchor() && !$tran->is_unmerged_assert() && !$tran->is_eps()) {
                    $righttran->pregleaf->position = $tran->pregleaf->position;
                    $intersection = $tran->intersect($righttran);
                    if ($intersection !== null) {
                        $tran->pregleaf = $intersection->pregleaf;
                    }
                }
                if (($del->pregleaf->subtype !== qtype_preg_leaf_assert::SUBTYPE_ESC_A && $intersection !== null) ||
                    !$del->is_start_anchor() || $tran->is_unmerged_assert() || $tran->is_eps()) {
                    $tran->to = $del->to;
                    $tran->redirect_merged_transitions();
                    $newkeys[] = $tran->from;
                    $this->add_transition($tran);
                    $transitionadded = true;
                } else if ($breakpos === null) {
                    $breakpos = $tran->pregleaf->position->compose($del->pregleaf->position);
                }
            }
            unset($tran);
            $this->change_state_for_intersection($del->from, array($del->to));
            $this->change_recursive_end_states($del->from, array($del->to));
            $this->change_recursive_start_states($del->from, $newkeys);
        }

        if (!($del->is_end_anchor() && in_array($del->to, $endstates)) && !($transition->from === $transition->to && ($transition->is_unmerged_assert() || $transition->is_eps()))) {
            $this->remove_transition($del);
        }

        $hastransitions = $this->check_connection($fromstates, $tostates);
        $stackitem['breakpos'] = ($transitionadded || $hastransitions) ? null : $breakpos;

        return true;
    }

    public function check_connection($fromstates, $tostates) {
        foreach ($fromstates as $from) {
            foreach ($tostates as $to) {
                if ($this->has_transition($from, $to)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function remove_wrong_end_states() {
        $endstates = array_values($this->get_end_states());

        foreach ($endstates as $end) {
            $reached = array();
            $iswrong = true;
            $front = array($end);
            while (!empty($front)) {
                $curstate = array_pop($front);
                if (in_array($curstate, $reached)) {
                    continue;
                }
                $reached[] = $curstate;
                $transitions = $this->get_adjacent_transitions($curstate, false);
                foreach ($transitions as $transition) {
                    $front[] = $transition->from;
                    if ($transition->origin !== qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND) {
                        $iswrong = false;
                    }
                }

            }
            if ($iswrong) {
                $this->remove_end_state($end);
            }
        }
    }

    private function update_intersection_states($sourcefa) {
        $newstates = $this->get_state_numbers();
        foreach ($sourcefa->innerautomata as $state => $inner) {
            foreach ($newstates as $newstate) {
                $numbers = explode(',', $newstate, 2);
                if ($numbers[0] !== '' && $numbers[0] == $state) {
                    $this->innerautomata[$this->get_id_by_state_number($newstate)] = $inner;
                }
            }
        }
    }

    private function get_id_by_state_number($number) {
        return array_search($number, $this->statenumbers);
    }

    /**
     * Complete branches ends with state, one number of which isn't start or end state depending on direction.
     *
     * @param fa object automaton to check start/end states.
     * @param anotherfa object automaton check start/end states.
     * @param durection direction of coping.
     */
    public function complete_non_intersection_branches($fa, $anotherfa, $direction) {

        $front = array();
        $secondnumbers = $anotherfa->get_state_numbers();
        $firstnumbers = $fa->get_state_numbers();
        // Find uncompleted branches.
        if ($direction == 0) {
            $states = $this->get_end_states();
            foreach ($states as $state) {
                if ($this->is_full_intersect_state($state)) {
                    $front[] = $state;
                }
            }
            foreach ($front as $state) {
                // Get states from first and second automata.
                $numbers = explode(',', $this->statenumbers[$state], 2);
                $workstate1 = $fa->find_state($numbers[0]);
                if ($numbers[1] != '') {
                    foreach ($secondnumbers as $num) {
                        if (strpos($numbers[1], $num) === 0 || $numbers[1] == $num) {
                            $workstate2 = array_search($num, $secondnumbers);
                        }
                    }
                }
                $oldfront = array();
                if (!$fa->has_endstate($workstate1) && $anotherfa->has_endstate($workstate2)) {
                    $transitions = $fa->get_adjacent_transitions($workstate1, true);
                    foreach ($transitions as $tran) {
                        $oldfront[] = $tran->to;
                    }
                    $this->copy_modify_branches($fa, $oldfront, null, $direction);
                    // Connect last state of intersection and copied branch.
                    foreach ($transitions as $tran) {
                        // Get number of copied state.
                        $number = $firstnumbers[$tran->to];
                        $number = trim($number, '()');
                        $number = $number . ',';
                        $copiedstate = array_search($number, $this->statenumbers);
                        // Add transition.
                        //$addtran = new qtype_preg_fa_transition($state, $tran->pregleaf, $copiedstate, $tran->origin, $tran->consumeschars);
                        $addtran = clone $tran;
                        $addtran->from = $state;
                        $addtran->to = $copiedstate;
                        $addtran->redirect_merged_transitions();
                        if ($copiedstate !== false) {
                            $this->add_transition($addtran);
                        }
                    }
                }
                $oldfront = array();
                if (!$anotherfa->has_endstate($workstate2) && $fa->has_endstate($workstate1)) {
                    $transitions = $anotherfa->get_adjacent_transitions($workstate2, true);
                    foreach ($transitions as $tran) {
                        $oldfront[] = $tran->to;
                    }
                    $this->copy_modify_branches($anotherfa, $oldfront, null, $direction, qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND);

                    // Connect last state of intersection and copied branch.
                    foreach ($transitions as $tran) {
                        // Get number of copied state.
                        $number = $secondnumbers[$tran->to];
                        $number = trim($number, '()');
                        $number = ',' . $number;
                        $copiedstate = array_search($number, $this->statenumbers);
                        // Add transition.
                        //$addtran = new qtype_preg_fa_transition($state, $tran->pregleaf, $copiedstate, $tran->origin, $tran->consumeschars);
                        $addtran = clone $tran;
                        $addtran->from = $state;
                        $addtran->to = $copiedstate;
                        $addtran->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
                        $addtran->redirect_merged_transitions();
                        $addtran->consumeschars = false;
                        if ($copiedstate !== false) {
                            $this->add_transition($addtran);
                        }
                    }
                }
            }
        } else {
            $states = $this->get_start_states();
            foreach ($states as $state) {
                if ($this->is_full_intersect_state($state)) {
                    $front[] = $state;
                }
            }
            foreach ($front as $state) {
                // Get states from first and second automata.
                $numbers = explode(',', $this->statenumbers[$state], 2);
                $workstate1 = $fa->find_state($numbers[0]);
                if ($numbers[1] != '') {
                    foreach ($secondnumbers as $num) {
                        if (strpos($numbers[1], $num) === 0 || $num == $numbers[1]) {
                            $workstate2 = array_search($num, $secondnumbers);
                        }
                    }
                }
                $oldfront = array();
                if (!$fa->has_startstate($workstate1) && $anotherfa->has_startstate($workstate2)) {
                    $transitions = $fa->get_adjacent_transitions($workstate1, false);
                    foreach ($transitions as $tran) {
                        $oldfront[] = $tran->from;
                    }
                    $this->copy_modify_branches($fa, $oldfront, null, $direction);
                    // Connect last state of intersection and copied branch.
                    foreach ($transitions as $tran) {
                        // Get number of copied state.
                        $number = $firstnumbers[$tran->from];
                        $number = trim($number, '()');
                        $last = substr($number, -1);
                        if ($last != ',') {
                            $number = $number . ',';
                        }
                        $copiedstate = array_search($number, $this->statenumbers);
                        // Add transition.
                        //$addtran = new qtype_preg_fa_transition($copiedstate, $tran->pregleaf, $state);
                        $addtran = clone $tran;
                        $addtran->to = $state;
                        $addtran->from = $copiedstate;
                        $addtran->redirect_merged_transitions();
                        $this->add_transition($addtran);
                    }
                }
                $oldfront = array();
                if (!$anotherfa->has_startstate($workstate2) && $fa->has_startstate($workstate1)) {
                    $transitions = $anotherfa->get_adjacent_transitions($workstate2, false);
                    $oldfront = array();
                    foreach ($transitions as $tran) {
                        $oldfront[] = $tran->from;
                    }
                    $this->copy_modify_branches($anotherfa, $oldfront, null, $direction, qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND);
                    // Connect last state of intersection and copied branch.
                    foreach ($transitions as $tran) {
                        // Get number of copied state.
                        $number = $secondnumbers[$tran->from];
                        $number = trim($number, '()');
                        $number = ',' . $number;
                        $copiedstate = array_search($number, $this->statenumbers);
                        // Add transition.
                        //$addtran = new qtype_preg_fa_transition($copiedstate, $tran->pregleaf, $state, $tran->origin, $tran->consumeschars);
                        $addtran = clone $tran;
                        $addtran->to = $state;
                        $addtran->from = $copiedstate;
                        $addtran->redirect_merged_transitions();
                        if ($tran->origin == qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND) {
                            $addtran->consumeschars = false;
                        }
                        $this->add_transition($addtran);
                    }
                }
            }
        }
    }

    /**
     * Remove flags that state was copied from all states of the automaton.
     */
    public function remove_flags_of_coping() {
        // Remove flag of coping from states of automata.
        $states = $this->get_states();
        $numbers = $this->get_state_numbers();
        foreach ($states as $statenum) {
            $backnumber = trim($numbers[$statenum], '()');
            $this->change_real_number($statenum, $backnumber);
        }
    }

    private function intersect_uncapturing_in_another_direction($anotherfa, $isstart, $result, $stop) {
        $uncapturingclone = new qtype_preg_fa();
        $hasuncapturing = false;
        $copedstates = array();
        $newfront = array();
        $newstop = array();
        if ($isstart == 0) {
            $states = $anotherfa->get_start_states();
        } else {
            $states = $anotherfa->get_end_states();
        }

        $oldfront = $states;
        $assotiatedstates = array();

        // Get automaton only with uncapturing transitions.
        while (!empty($oldfront)) {
            foreach ($oldfront as $state) {
                if (!in_array($state, $copedstates)) {
                    if (!array_key_exists($state, $assotiatedstates)) {
                        if ($isstart == 0) {
                            $from = $uncapturingclone->add_state($state);
                            $assotiatedstates[$state] = $from;
                        } else {
                            $to = $uncapturingclone->add_state($state);
                            $assotiatedstates[$state] = $to;
                        }
                    } else {
                        if ($isstart == 0) {
                            $from = $assotiatedstates[$state];
                        } else {
                            $to = $assotiatedstates[$state];
                        }

                    }

                    if (in_array($state, $states) && $isstart == 0) {
                        $uncapturingclone->add_start_state($from);
                    } else if (in_array($state, $states)) {
                        $uncapturingclone->add_end_state($to);
                    }

                    $transitions = $anotherfa->get_adjacent_transitions($state, !$isstart);
                    foreach ($transitions as $transition) {
                        if (!$transition->consumeschars) {
                            $hasuncapturing = true;
                            if ($isstart == 0) {
                                if (!array_key_exists($transition->to, $assotiatedstates)) {
                                    $to = $uncapturingclone->add_state($transition->to);
                                    $assotiatedstates[$state] = $to;
                                } else {
                                    $to = $assotiatedstates[$transition->to];
                                }
                            } else {
                                if (!array_key_exists($transition->from, $assotiatedstates)) {
                                    $from = $uncapturingclone->add_state($transition->from);
                                    $assotiatedstates[$state] = $from;
                                } else {
                                    $from = $assotiatedstates[$transition->from];
                                }
                            }

                            $clonetran = clone $transition;
                            $clonetran->from = $from;
                            $clonetran->to = $to;
                            $clonetran->redirect_merged_transitions();
                            $clonetran->consumeschars = true;
                            $uncapturingclone->add_transition($clonetran);
                            $anotherfa->remove_transition($transition);
                            $copedstates[] = $state;
                            if ($isstart == 0) {
                                $newfront[] = $transition->to;
                            } else {
                                $newfront[] = $transition->from;
                            }
                        }
                    }
                }
            }
            $oldfront = $newfront;
            $newfront = array();
        }

        if ($hasuncapturing) {
            // Set its start/end states.
            $states = $uncapturingclone->get_states();
            foreach ($states as $state) {
                $transitions = $uncapturingclone->get_adjacent_transitions($state, !$isstart);

                // Check if there are only loopsback transitions.
                $loopsback = true;
                foreach ($transitions as $transition) {
                    if (!$transition->loopsback) {
                        $loopsback = false;
                    }
                }
                if ($loopsback) {
                    if ($isstart == 0) {
                        $uncapturingclone->add_end_state($state);
                    } else {
                        $uncapturingclone->add_start_state($state);
                    }
                }
            }
            // Set start states for coped automaton.
            $states = $result->get_states();
            foreach ($states as $state) {
                $transitionsinto = $result->get_adjacent_transitions($state, false);
                $transitionsout = $result->get_adjacent_transitions($state, true);
                // Check if there are only loopsback transitions.
                $loopsbackinto = true;
                foreach ($transitionsinto as $transition) {
                    if (!$transition->loopsback) {
                        $loopsbackinto = false;
                    }
                }
                $loopsbackout = true;
                foreach ($transitionsout as $transition) {
                    if (!$transition->loopsback) {
                        $loopsbackout = false;
                    }
                }

                if ($loopsbackinto) {
                    $result->add_start_state($state);
                }
                if ($loopsbackout) {
                    $result->add_end_state($state);
                }
            }

            // Intersect it in another direction.
            $resultpart = $result->intersect_fa($uncapturingclone, $stop, !$isstart);

            // Add this automaton as branch of result automaton.
            $resultpart->remove_all_end_states();
            $resultpart->remove_all_start_states();
            $states = $resultpart->get_states();
            foreach ($states as $state) {
                $transitionsout = $resultpart->get_adjacent_transitions($state, true);
                $transitionsinto = $resultpart->get_adjacent_transitions($state, false);
                // Check if there are only loopsback transitions.
                $loopsbackout = true;
                $loopsbackinto = true;
                foreach ($transitionsout as $transition) {
                    if (!$transition->loopsback) {
                        $loopsbackout = false;
                    }
                }
                foreach ($transitionsinto as $transition) {
                    if (!$transition->loopsback) {
                        $loopsbackinto = false;
                    }
                }
                if ($loopsbackout) {
                    $resultpart->add_end_state($state);
                }
                if ($loopsbackinto) {
                        $resultpart->add_start_state($state);
                }
            }

            $resultpart->remove_unreachable_states();
            if ($isstart == 0) {
                $oldfront = $resultpart->get_start_states();
            } else {
                $oldfront = $resultpart->get_end_states();
            }
            $copedstates = array();
            $newfront = array();
            $assotiatedstates = array();
            while (!empty($oldfront)) {
                foreach ($oldfront as $state) {
                    if (!in_array($state, $copedstates)) {
                        if (!array_key_exists($state, $assotiatedstates)) {
                            if ($isstart == 0) {
                                $from = $result->add_state($resultpart->get_state_numbers()[$state]);
                                $assotiatedstates[$state] = $from;
                            } else {
                                $to = $result->add_state($resultpart->get_state_numbers()[$state]);
                                $assotiatedstates[$state] = $to;
                            }
                        } else {
                            if ($isstart == 0) {
                                $from = $assotiatedstates[$state];
                            } else {
                                $to = $assotiatedstates[$state];
                            }
                        }

                        $copedstates[] = $state;
                        $transitions = $resultpart->get_adjacent_transitions($state, !$isstart);
                        foreach ($transitions as $transition) {
                            if ($isstart == 0) {
                                if (!array_key_exists($transition->to, $assotiatedstates)) {
                                    $to = $result->add_state($resultpart->get_state_numbers()[$transition->to]);
                                    $assotiatedstates[$transition->to] = $to;
                                } else {
                                    $to = $assotiatedstates[$transition->to];
                                }
                            } else {
                                if (!array_key_exists($transition->from, $assotiatedstates)) {
                                    $from = $result->add_state($resultpart->get_state_numbers()[$transition->from]);
                                    $assotiatedstates[$transition->from] = $from;
                                } else {
                                    $from = $assotiatedstates[$transition->from];
                                }
                            }
                            $clonetran = clone $transition;
                            $clonetran->from = $from;
                            $clonetran->to = $to;
                            $clonetran->redirect_merged_transitions();
                            $result->add_transition($clonetran);
                            if ($isstart == 0) {
                                $newfront[] = $transition->to;
                            } else {
                                $newfront[] = $transition->from;
                            }
                        }
                    }
                }
                $oldfront = $newfront;
                $newfront = array();
            }
                // Redirect start states of getting part to real start state.
               /* $resultstart = $result->get_start_states()[0];
                $startstates = $resultpart->get_start_states();
                foreach ($startstates as $start) {
                    if (!in_array($assotiatedstates[$start], $result->get_start_states())) {
                        $result->redirect_transitions($assotiatedstates[$start], $resultstart);
                    }
                }*/
                // Form new stop states.
                // Set its end states.
            if ($isstart == 0) {
                $endstates = $resultpart->get_end_states();
                foreach ($endstates as $end) {
                    $newstop[] = $assotiatedstates[$end];
                }
            } else {
                $startstates = $resultpart->get_start_states();
                foreach ($startstates as $start) {
                    $newstop[] = $assotiatedstates[$start];
                }
            }
        }
        return $newstop;
    }

    private function skip_uncapturing_transitions($anotherfa, $stateindex, $isstart, $result, $stop) {
        $startstates = $this->get_start_states();
        $endstates = $this->get_end_states();
        // Change state first from intersection.
        $secondnumbers = $anotherfa->get_state_numbers();
        $firstnumbers = $this->get_state_numbers();
        $resnumbers = $result->get_state_numbers();
        $newstop = array();
        // Skip transitions from first automaton.
        foreach ($stateindex as $number) {
            // If eps - copy it to result automaton.
            if ($isstart == 0 && in_array($number, $startstates)) {
                $transitions = $this->get_adjacent_transitions($number, true);
                foreach ($transitions as $transition) {
                    if ($transition->is_start_anchor() || $transition->is_eps())  {
                        foreach ($stop as $stopindex) {
                            $tran = clone $transition;
                            $tran->from = $stopindex;
                            $addednumber = $result->get_inter_state($firstnumbers[$transition->to], $resnumbers[$anotherfa->get_start_states()[0]]);
                            $addednumber = trim($addednumber, ",");
                            $addedstate = $result->add_state($addednumber);
                            $tran->to = $addedstate;
                            $result->add_transition($tran);
                            $newstop[] = $addedstate;
                        }
                    }
                }
            } else if ($isstart == 1 && in_array($number, $startstates)) {
                $transitions = $this->get_adjacent_transitions($number, false);
                foreach ($transitions as $transition) {
                    if ($transition->is_end_anchor() || $transition->is_eps()) {
                        foreach ($stop as $stopindex) {
                            $tran = clone $transition;
                            $tran->to = $stopindex;
                            $addednumber = $result->get_inter_state($firstnumbers[$transition->from],$resnumbers[$anotherfa->get_end_states()[0]]);
                            $addednumber = trim($addednumber, ",");
                            $addedstate = $result->add_state($addednumber);
                            $tran->from = $addedstate;
                            $result->add_transition($tran);
                            $newstop[] = $addedstate;
                        }
                    }
                }
            }
        }
        $intersectedstop = $this->intersect_uncapturing_in_another_direction($anotherfa, $isstart, $result, $stop);
        $newstop = array_merge($newstop, $intersectedstop);
        // Skip transitions from second automaton.
        if ($isstart == 0) {
            $states = $anotherfa->get_start_states();
            // If eps - copy it to result automaton.
            foreach ($states as $state) {
                $transitions = $anotherfa->get_adjacent_transitions($state, true);
                foreach ($transitions as $transition) {
                    if ($transition->is_start_anchor() || $transition->is_eps()) {
                        foreach ($stop as $stopindex) {
                            $tran = clone $transition;
                            $tran->from = $stopindex;
                            $addednumber = $result->get_inter_state($resnumbers[$stopindex], $secondnumbers[$transition->to]);
                            $addedstate = $result->add_state($addednumber);
                            $tran->to = $addedstate;
                            $result->add_transition($tran);
                            $tran->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
                            $newstop[] = $addedstate;
                        }
                        //$anotherfa->remove_transition($transition);
                    }
                }
            }

        } else {
            $states = $anotherfa->get_end_states();
            foreach ($states as $state) {
                $transitions = $anotherfa->get_adjacent_transitions($state, false);
                foreach ($transitions as $transition) {
                    if ($transition->is_end_anchor() || $transition->is_eps()) {
                        foreach ($stop as $stopindex) {
                            $tran = clone $transition;
                            $tran->to = $stopindex;
                            $addednumber = $result->get_inter_state($resnumbers[$stopindex], $secondnumbers[$transition->from]);
                            $addedstate = $result->add_state($addednumber);
                            $tran->from = $addedstate;
                            $tran->origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
                            $result->add_transition($tran);
                            $newstop[] = $addedstate;
                        }
                        //$anotherfa->remove_transition($transition);
                    }
                }
            }
        }
        return $newstop;
    }

    /**
     * Intersect automaton with another one.
     *
     * @param anotherfa object automaton to intersect.
     * @param stateindex array of integer indexes of states of $this automaton with which to start intersection.
     * @param isstart boolean intersect by superpose start or end state of anotherfa with stateindex state.
     * @return result automata without blind states with one end state and with merged asserts.
     */
    public function intersect_fa($anotherfa, $stateindex, $isstart) {
        $result = new qtype_preg_fa();
        $stopcoping = $stateindex;
        // Get states for starting coping.
        if ($isstart == 0) {
            $oldfront = $this->get_start_states();
        } else {
            $oldfront = $this->get_end_states();
        }
        // Copy branches.
        $stop = $result->copy_modify_branches($this, $oldfront, $stopcoping, $isstart);
        $transitions = array();
        $startstates = $this->get_start_states();
        $endstates = $this->get_end_states();
        // Change state first from intersection.
        $secondnumbers = $anotherfa->get_state_numbers();
        $firstnumbers = $this->get_state_numbers();
        $resnumbers = $result->get_state_numbers();
        $newstop = array();
        // Skip uncapturing transitions.
        if ($isstart == 0) {
            $states = $anotherfa->get_start_states();
        } else {
            $states = $anotherfa->get_end_states();
        }
        $newstop = $this->skip_uncapturing_transitions($anotherfa, $stateindex, $isstart, $result, $stop);

        $secforinter = $secondnumbers[$states[0]];
        $addedstop = array();
        foreach ($stop as $stopnumber) {
            $state = $result->get_inter_state($resnumbers[$stopnumber], $secforinter);
            $result->change_real_number($stopnumber, $state);
            $i = count($states) - 1;
            while ($i > 0) {
                $state = $result->get_inter_state($resnumbers[$stopnumber], $secondnumbers[$states[$i]]);
                $added = $result->add_state($state);
                $addedstop[] = $added;
                $transitions = $result->get_adjacent_transitions($stopnumber, true);
                foreach ($transitions as $transition) {
                    $clone = clone $transition;
                    $clone->from = $added;
                    $clone->redirect_merged_transitions();
                    $result->add_transition($clone);
                }
                $transitions = $result->get_adjacent_transitions($stopnumber, false);
                foreach ($transitions as $transition) {
                    $clone = clone $transition;
                    $clone->to = $added;
                    $clone->redirect_merged_transitions();
                    $result->add_transition($clone);
                }
                $i--;
            }

        }
        $stop = array_merge($stop, $addedstop, $newstop);
        // Find intersection part.
        if (!$anotherfa->has_cycle() && $this->has_cycle()) {
            $this->get_intersection_part($anotherfa, $result, $stop, $isstart, true);
        } else {
            $this->get_intersection_part($anotherfa, $result, $stop, $isstart, false);
        }
        // Set right start and end states for completing branches.
        $result->set_start_end_states_before_coping($this, $anotherfa);

        if ($result->has_successful_intersection($this, $anotherfa, $isstart)) {
            // Cleaning end states.
            $result->remove_all_end_states();
            // Cleaning start states.
            $result->remove_all_start_states();
            // Set right start and end states for completing branches.
            $result->set_start_end_states_before_coping($this, $anotherfa);
            $result->complete_non_intersection_branches($this, $anotherfa, $isstart);
            // Cleaning end states.
            $result->remove_all_end_states();
            // Cleaning start states.
            $result->remove_all_start_states();
            $result->set_start_end_states_after_intersect($this, $anotherfa);
        } else {
            $result = new qtype_preg_fa();
        }
        return $result;
    }

    /**
     * Return set substraction: $this - $anotherfa. Used to get negation.
     */
    public function substract_fa($anotherfa) {
        // TODO
    }

    /**
     * Return inversion of fa.
     */
    public function invert_fa() {
        // TODO
    }

    public function __clone() {
        // TODO - clone automaton.
    }
}
