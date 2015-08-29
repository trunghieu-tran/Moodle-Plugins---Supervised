<?php
// This file is part of Moodle - http:// moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
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
// along with Moodle.  If not, see <http:// www.gnu.org/licenses/>.

/**
 * Defines tree nodes specific for generating regex descriptions
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Pahomov Dmitry
 * @license http:// www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');

/**
 * Generic node class.
 */
abstract class qtype_preg_description_node {
    /** @var string pattern for description of current node */
    public $pattern;

    /** @var qtype_preg_node Corresponding AST node */
    public $pregnode;

    /** @var Reference to handler */
    public $handler;

    /**
     * Constructs node.
     *
     * @param qtype_preg_node $node Reference to automatically generated (by handler) abstract node.
     * @param type $matcher Reference to handler, which generates nodes.
     */
    public function __construct($node, $matcher) {
        $this->handler = $matcher;
        $this->pregnode = $node;
    }

    /**
     * Chooses pattern for current node.
     *
     * @param qtype_preg_description_node $node_parent Reference to the parent.
     * @param string $form Required form.
     * @return string Chosen pattern.
     */
    abstract public function pattern($node_parent = null, $form = null);

    /**
     * Constructs {$a} object for get_string
     *
     * @return object object that should be passed to get_string for current node.
     */
    // abstract public function get_a();

    /**
     * Recursively generates description of tree (subtree).
     *
     * @param string $numbering_pattern Pattern to track numbering.
     * Must contain: %s - description of node;
     * May contain:  %n - id node.
     * @param qtype_preg_description_node $node_parent Reference to the parent.
     * @param string $form Required form.
     * @return string
     */
    abstract public function description($numbering_pattern, $node_parent = null, $form = null);

    /**
     * gets localized string, if required a form it gets localized string for required form
     *
     * @param string $key same as in get_string
     * @param string $form Required form.
     */
    protected static function get_form_string($key, $a = null, $form = null) {
        if (!empty($form)) {
            $key .= '_' . $form;
        }
        $str = get_string($key, 'qtype_preg', $a);
        // TODO process $a directly in classes
        $str = qtype_poasquestion\string::replace('{$a}', '%', $str);
        $str = qtype_poasquestion\string::replace('{$a->firstoperand}', '%1', $str);
        $str = qtype_poasquestion\string::replace('{$a->secondoperand}', '%2', $str);
        $str = qtype_poasquestion\string::replace('{$a->thirdoperand}', '%3', $str);
        $str = qtype_poasquestion\string::replace('{$a->', '%', $str);
        $str = qtype_poasquestion\string::replace('}', '', $str);
        return $str;
    }

    /**
     * returns true if engine support the node, rejection string otherwise
     */
    public function accept($options) {
        return true;
    }

    public function is_selected() {
        $selectednode = $this->handler->get_selected_node();
        return ($selectednode !== null &&
                $this->pregnode->position->indfirst >= $selectednode->position->indfirst &&
                $this->pregnode->position->indlast <= $selectednode->position->indlast);
    }

    /**
     * Wraps $s into pattern $numbering_pattern
     * %content will be replaced with generated string
     * %idclass will be replaced with 'description_node_IDENTIFIER'
     * %optionalclasses will be replaced with style classes (with space prefix!)
     * %optionalclassesdevider will be replaced with space if %optionalclasses is no null
     * %style will be replaced with style classes
     *
     * @param type $s this string will be placed instead of %s
     */
    protected function numbering_pattern($numbering_pattern, $s) { // TODO - rename
        $classes = array();
        $bgclor = 'white';

        // Highlight generated and selected nodes.
        if ($this->handler->is_node_generated($this->pregnode)) {
            $bgclor = 'lightgrey';
        }
        if ($this->is_selected()) {
            $bgclor = 'orange';
        }

        $classesstr = count($classes) ? implode(' ', $classes) : '';
        $classesstrdivider = count($classes) ? ' ' : '';
        $stylestr = $bgclor!==null ? 'background: ' . $bgclor : '';

        $result = qtype_poasquestion\string::replace('%content', $s, $numbering_pattern);
        $result = qtype_poasquestion\string::replace('%idclass', 'description_node_'.$this->pregnode->id, $result);
        $result = qtype_poasquestion\string::replace('%optionalclassesdevider', $classesstrdivider, $result);
        $result = qtype_poasquestion\string::replace('%optionalclasses', $classesstr, $result);
        $result = qtype_poasquestion\string::replace('%style', $stylestr, $result);
        return $result;
    }
}

/**
 * Generic leaf class.
 */
abstract class qtype_preg_description_leaf extends qtype_preg_description_node {

    /**
     * Redifinition of abstract qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        return self::get_form_string($this->pregnode->lang_key(true), null, $form);
    }

    /**
     * Redifinition of abstract qtype_preg_description_node::description()
     */
    public function description($numbering_pattern, $node_parent = null, $form = null) {
        $this->pattern = $this->pattern($node_parent, $form);
        // var_dump($this->pattern);
        $description = $this->numbering_pattern($numbering_pattern, $this->pattern);
        qtype_preg_description_leaf_options::check_options($this, $description, $form);
        return $description;
    }
}

/**
 * Represents a character or a charcter set.
 */
class qtype_preg_description_leaf_charset extends qtype_preg_description_leaf {

    const FIRST_CHAR    = 0;
    const INTO_RANGE    = 1;
    const OUT_OF_RANGE  = 2;

    /**
     * Checks if a character is printable
     *
     * @param $utf8chr character (from qtype_poasquestion\string) for check
     */
    public static function is_chr_printable($code) {
        return qtype_preg_unicode::search_number_binary($code, qtype_preg_unicode::C_ranges()) === false &&
               qtype_preg_unicode::search_number_binary($code, qtype_preg_unicode::Z_ranges()) === false;
    }

    /*
     * Returns description of $utf8chr if it is non-printing character, otherwise returns null
     *
     * @param int $code character code
     * @return string|null description of character (if character is non printable) or null.
     */
    public static function describe_nonprinting($code, $form = null) {
        // null returned if description is not needed
        if ($code === null || self::is_chr_printable($code)) {
            return null;
        }
        // ok, character is non-printing, lets find its description in the language file
        $hex = core_text::strtoupper(dechex($code));
        if ($code <= 32 || $code == 127 || $code == 160 || $code == 173 || $code == 8194 ||
            $code == 8195 || $code == 8201 || $code == 8204 || $code == 8205) {
            return self::get_form_string('description_char' . $hex, null, $form);
        } else {
            $a = new stdClass;
            $a->code = $hex;
            $a->char = core_text::code2utf8($code);
            return self::get_form_string('description_char_16value', $a, $form);
        }
    }

    /**
     * Describes character with code $code
     *
     * @param int|qtype_poasquestion\string $char character from qtype_poasquestion\string for describe or decimal code of character
     * @param bool $escapehtml a flag indicating whether to escape html characters (& < > " ')
     * @param string $form required form
     * @return string describes of character
     */
    public static function describe_chr($char, $escapehtml = true, $form = null) {
        if (is_int($char)) {
            $char = core_text::code2utf8($char);
        }
        $code = core_text::utf8ord($char);
        $result = self::describe_nonprinting($code);
        if ($result === null) {
            //   &        >       <       "       '
            // &#38;    &#62;   &#60;   &#34;   &#39;
            if ($escapehtml) {
                $result = qtype_preg_authoring_tool::char_to_html($char);
            }
            $a = new stdClass;
            $a->char = $result;
            $result = self::get_form_string('description_char', $a, $form);
        }
        return $result;
    }

    /**
     * Analyzes the enumeration of characters and finds the range.
     * Input string will transform to:
     * array(
     *     0 => array(10,20),    // range
     *     1 => 30,              // simple char
     *     2 => 40,              // simple char
     *     3 => array(100,200),  // range
     *     ...
     * );
     *
     * @param $str object of qtype_poasquestion\string.
     * @return mixed[] array with ranges and simple characters (see description of the function).
     */
    public function find_ranges($str) {
        $length = $str->length();
        if ($length < 1) {
            return false;
        }
        $result = array();
        $rangestart = 0;
        $prevcode = -1;
        $state = self::FIRST_CHAR;
        for ($i = 0; $i < $length; $i++) {
            $curcode = core_text::utf8ord($str[$i]);
            if ($state == self::FIRST_CHAR) {
                $state = self::OUT_OF_RANGE;
            } else if ($state == self::INTO_RANGE) {
                if ($curcode - 1 != $prevcode) {
                    $state = self::OUT_OF_RANGE;
                    $result[] = array($rangestart, $prevcode);
                }
            } else if ($state == self::OUT_OF_RANGE) {
                if ($curcode - 1 == $prevcode) {
                    $state = self::INTO_RANGE;
                    $rangestart = $prevcode;
                } else {
                    $result[] = $prevcode;
                }
            }
            $prevcode = $curcode;
        }
        if ($state == self::INTO_RANGE) {
            $result[] = array($rangestart, $prevcode);
        } else { // hence $state == OUT_OF_RANGE
            $result[] = $prevcode;
        }
        return $result;
    }

    protected function fix_pattern_name_suffix($suffix) {

    }

    /**
     * Convertes charset flag to array of descriptions(strings)
     *
     * @param qtype_preg_charset_flag $flag flag gor description
     * @param string[] $characters enumeration of descriptions in charset (updated parameter)
     * @param string $form required form
     */
    private function flag_to_array($flag, &$characters, $form = null) {
        $temp_str = '';
        $ranges = null;
        $rangelength = null;
        $rangelengthmax = null;

        if ($flag->type === qtype_preg_charset_flag::TYPE_FLAG) {
            // current flag is something like \w or \pL
            if ($flag->negative == true) {
                // using charset pattern 'description_charset_neg_one' because char pattern 'description_char_neg' has a <span> tag,
                // but dont need to highlight this
                $a = new stdClass;
                $a->characters = self::get_form_string('description_charflag_' . $flag->data, null, $form);
                $characters[] = self::get_form_string('description_charset_neg_one', $a, $form);
            } else {
                $characters[] = self::get_form_string('description_charflag_' . $flag->data, null, $form);
            }
        } else if ($flag->type === qtype_preg_charset_flag::TYPE_SET) {
            // flag is a simple enumeration of characters
            if ($flag->data->length() == 1) {
                $characters[] = self::describe_chr($flag->data[0], true, $form);
            } else {
                $ranges = $this->find_ranges($flag->data);
                // var_dump($ranges);
                $rangelengthmax =& $this->handler->get_options()->rangelengthmax;
                foreach ($ranges as $range) {
                    if (is_int($range)) {
                        // $range is a code of character
                        $characters[] = self::describe_chr($range, true, $form);
                    } else {
                        // $range is a range (from A to Z <=> array(65,90) )
                        $rangelength = $range[1] - $range[0];
                        if ($rangelength < $rangelengthmax) {
                            // Display as enumeration
                            for ($i = $range[0]; $i <= $range[1]; $i++) {
                                $characters[] = self::describe_chr($i, true, $form);
                            }
                        } else { // otherwise it will be displayed
                            $a = new stdClass;
                            $a->start = self::describe_chr($range[0], true, $form);
                            $a->end = self::describe_chr($range[1], true, $form);
                            $range = get_string('description_range', 'qtype_preg', $a);
                            $characters[] = self::get_form_string('description_charset_range', $range, $form);
                        }
                    }
                }
            }
        }
    }

    /**
     * Redifinition of abstract qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        $characters = array();
        // check errors
        if (!empty($this->pregnode->errors)) {
            return $this->pregnode->errors[0]->error_string();
        }

        // 'not not' fix
        if (count($this->pregnode->flags) == 1 && $this->pregnode->negative && $this->pregnode->flags[0][0]->negative) {
            $this->pregnode->negative = false;
            $this->pregnode->flags[0][0]->negative = false;
        }

        // filling $characters[]
        foreach ($this->pregnode->flags as $outer) {
            $this->flag_to_array($outer[0], $characters, $form);
        }

        if (count($characters) == 1 && !$this->pregnode->negative) {
            // adding resulting patterns
            // Simulation of:
            // $string['description_charset_one'] = '%characters';
            // w/o calling functions
            return $characters[0];
        } else {
            $key = 'description_charset';
            if ($this->pregnode->negative) {
                $key .= '_neg';
            }
            if (count($characters) == 1) {
                $key .= '_one';
            }
            $a = new stdClass;
            $a->characters = implode(", ", $characters);
            return self::get_form_string($key, $a, $form);
        }
    }
}


/**
 * Defines meta-characters that can't be enumerated.
 */
class qtype_preg_description_leaf_meta extends qtype_preg_description_leaf {

}

class qtype_preg_description_leaf_assert extends qtype_preg_description_leaf {

}

/**
 * Defines backreferences.
 */
class qtype_preg_description_leaf_backref extends qtype_preg_description_leaf {

    public function pattern($node_parent = null, $form = null) {
        //return parent::pattern($node_parent, $form);
        return self::get_form_string($this->pregnode->lang_key(true), $this->pregnode->number, $form);
    }

}

class qtype_preg_description_leaf_subexpr_call extends qtype_preg_description_leaf {

    public function pattern($node_parent = null, $form = null) {
        //return parent::pattern($node_parent, $form);
        $postfix = $this->pregnode->isrecursive ? '_recursive' : '';
        return self::get_form_string($this->pregnode->lang_key(true).$postfix, $this->pregnode->number, $form);
    }
}

/**
 * Reperesents backtracking control, newline convention etc sequences like (*...).
 */
class qtype_preg_description_leaf_control extends qtype_preg_description_leaf {

    public function pattern($node_parent = null, $form = null) {
        //return parent::pattern($node_parent, $form);
        return self::get_form_string($this->pregnode->lang_key(true), $this->pregnode->name, $form);
    }
}

class qtype_preg_description_leaf_options extends qtype_preg_description_leaf {

    public function pattern($node_parent = null, $form = null) {
        $options = array();
        for ($i = 0; $i < $this->pregnode->posopt->length(); $i++) {
            $this->handler->state->set_modifier($this->pregnode->posopt[$i], true);
            $options[] = self::get_form_string('description_option_' . $this->pregnode->posopt[$i], null, $form);
        }
        for ($i = 0; $i < $this->pregnode->negopt->length(); $i++) {
            $this->handler->state->set_modifier($this->pregnode->negopt[$i], false);
            $options[] = self::get_form_string('description_unsetoption_' . $this->pregnode->negopt[$i], null, $form);
        }
        $options = implode(', ', $options);
        //return parent::pattern($node_parent, $form);
        return self::get_form_string($this->pregnode->lang_key(true), $options, $form);
    }

    /**
     * Using in description functions.
     * Checks the need for the substitution leaf_options patterns
     *
     * @param qtype_preg_description_node $node current node
     * @param string $node_pattern description of current node
     * @param array $options array of options
     */
    public static function check_options($node, &$node_pattern, $form = null) {
        $options = '';
        $mcaseless =& $node->handler->state->caseless;
        $msingleline =& $node->handler->state->singleline;
        $mmultilineline =& $node->handler->state->multilineline;
        $mextended =& $node->handler->state->extended;
        $mungreedy =& $node->handler->state->ungreedy;
        $mduplicate =& $node->handler->state->duplicate;

        if ($node->pregnode->type === qtype_preg_node::TYPE_NODE_SUBEXPR) {
            $node->handler->state->forceunsetmodifiers = true;
        } else if ($node->handler->state->forceunsetmodifiers) {
            // TODO - generate 'caseless, singleline:' instead of 'caseless: singleline:'
            if ($mcaseless === true) {
                $options .= self::get_form_string('description_unsetoption_i', null, $form);
                $mcaseless = false;
            }
            if ($msingleline === true) {
                $options .= self::get_form_string('description_unsetoption_s', null, $form);
                $msingleline = false;
            }
            if ($mmultilineline === true) {
                $options .= self::get_form_string('description_unsetoption_m', null, $form);
                $mmultilineline = false;
            }
            if ($mextended === true) {
                $options .= self::get_form_string('description_unsetoption_x', null, $form);
                $mextended = false;
            }
            if ($mungreedy === true) {
                $options .= self::get_form_string('description_unsetoption_U', null, $form);
                $mungreedy = false;
            }
            if ($mduplicate === true) {
                $options .= self::get_form_string('description_unsetoption_J', null, $form);
                $mduplicate = false;
            }
            $result = '';
            if ($options !== '') {
                $result = self::get_form_string('description_leaf_options', $options, $form) . ' ';
            }
            $node->handler->state->forceunsetmodifiers = false;
            $node_pattern = $result . $node_pattern;
        }
    }
}

/**
 * Defines operator nodes.
 */
abstract class qtype_preg_description_operator extends qtype_preg_description_node {
    /** @var qtype_preg_description_tool[] Array of operands */
    public $operands = array();

    /**
     * Construct array of operands, using method qtype_regex_handler::from_preg_node()
     *
     * @param qtype_preg_node $node Reference to automatically generated (by handler) abstract node.
     * @param type $matcher Reference to handler, which generates nodes.
     */
    public function __construct($node, $matcher) {
        parent::__construct($node, $matcher);
        foreach ($this->pregnode->operands as $operand) {
            array_push($this->operands, $matcher->from_preg_node($operand));
        }
    }

    /**
     * Redifinition of abstract qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        return 'seems like pattern() for ' . get_class($this) . ' node is not redefined';
    }

    /**
     * Redifinition of abstract qtype_preg_description_node::description()
     */
    public function description($numbering_pattern, $node_parent = null, $form = null) {
        $this->pattern = $this->pattern($node_parent, $form);
        $description = $this->numbering_pattern($numbering_pattern, $this->pattern);

        $replaces = $this->what_to_replace($description);
        foreach ($replaces as $num => $data) {
            $child_description = $this->operands[$num - 1]->description($numbering_pattern, $this, $data['form']);
            $description = qtype_poasquestion\string::replace($data['toreplace'], $child_description, $description);
        }
        qtype_preg_description_leaf_options::check_options($this, $description, $form);
        return $description;
    }

    protected function what_to_replace($str) {
        $str = new qtype_poasquestion\string($str);
        $pos = null;
        $form = null;
        $numstr = null;
        $num = null;
        $full = null;
        $result = array();
        $len = $str->length();
        $pos = $str->contains('%');
        $wasnum = false;
        while ($pos !== false) {
            $pos++;
            $form = '';
            while ($len > $pos && ctype_alpha($str[$pos])) {
                $form += $str[$pos];
                $pos++;
            }
            $numstr = '';
            while ($len > $pos && ctype_digit($str[$pos])) {
                $numstr += $str[$pos];
                $pos++;
                $wasnum = true;
            }
            if ($wasnum) {
                $num = (int)$numstr;
                $full = '%' . $form . $numstr;
                $result[$num] = array('toreplace' => $full, 'form' => $form);
            }
            $pos = $str->contains('%', $pos);
        }
        return $result;
    }
}

/**
 * Defines finite quantifiers with left and right borders, unary operator.
 * Possible errors: left border is greater than right one.
 */
class qtype_preg_description_node_finite_quant extends qtype_preg_description_operator {

    /**
     * Redifinition of abstract qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        $a = new stdClass;
        $a->leftborder = $this->pregnode->leftborder;
        $a->rightborder = $this->pregnode->rightborder;
        $a->greedy = get_string($this->pregnode->lang_key_for_greediness(), 'qtype_preg');
        //$a->firstoperand = get_string('description_operand', 'qtype_preg');

        $result = self::get_form_string($this->pregnode->lang_key(true), $a, $form);

        if ($this->pregnode->leftborder > $this->pregnode->rightborder) {
            $result = preg_replace('/%(\w+)?1/u', ('%$ {1}1' . self::get_form_string('description_errorbefore', null, $form)), $result);
            $result .= self::get_form_string('description_node_finite_quant_borders_err', null, $form) .
                       self::get_form_string('description_errorafter', null, $form);
        }

        return $result;
    }
}

/**
 * Defines infinite quantifiers node with the left border only, unary operator.
 */
class qtype_preg_description_node_infinite_quant extends qtype_preg_description_operator {

    /**
     * Redifinition of abstract qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        $a = new stdClass;
        $a->leftborder = $this->pregnode->leftborder;
        $a->greedy = get_string($this->pregnode->lang_key_for_greediness(), 'qtype_preg');
        //$a->firstoperand = get_string('description_operand', 'qtype_preg');
        return self::get_form_string($this->pregnode->lang_key(true), $a, $form);
    }
}

/**
 * Defines concatenation, binary operator.
 */
class qtype_preg_description_node_concat extends qtype_preg_description_operator {

    /**
     * Redifinition of abstract qtype_preg_description_node::description()
     */
    public function description($numbering_pattern, $node_parent = null, $form = null) {
        $description = '';
        $childs_count = count($this->operands);
        $type1 = null;
        $type2 = null;
        $subtype1 = null;
        $subtype2 = null;
        $left = null;
        $right = null;
        $prevdescription = null;
        $resultpattern = '';

        for ($i = $childs_count - 2; $i >= 0; $i--) {
            $left = $this->operands[$i];
            $right = $this->operands[$i + 1];

            // getting pattern
            $type1 = $left->pregnode->type;
            $type2 = $right->pregnode->type;
            $subtype1 = $left->pregnode->subtype;
            $subtype2 = $right->pregnode->subtype;

            $needshortpattern = $type1 == qtype_preg_node::TYPE_LEAF_CHARSET && $left->pregnode->is_single_printable_character() &&
                                $type2 == qtype_preg_node::TYPE_LEAF_CHARSET && $right->pregnode->is_single_printable_character();

            $needcontiuneshortpattern = $type2 == qtype_preg_node::TYPE_LEAF_CHARSET && $right->pregnode->is_single_printable_character() &&
                                        $type1 == qtype_preg_node::TYPE_NODE_CONCAT &&
                                        $left->operands[1]->pregnode->type == qtype_preg_node::TYPE_LEAF_CHARSET && $left->operands[1]->pregnode->is_single_printable_character();

            $firstaheadassert = ($subtype1 == qtype_preg_node_assert::SUBTYPE_PLA || $subtype1 == qtype_preg_node_assert::SUBTYPE_NLA);

            $secondbehindassert = ($subtype2 == qtype_preg_node_assert::SUBTYPE_PLB || $subtype2 == qtype_preg_node_assert::SUBTYPE_NLB);

            $aheadassertinprevconcat = $type1 == qtype_preg_node::TYPE_NODE_CONCAT &&
                                       ($left->operands[1]->pregnode->subtype == qtype_preg_node_assert::SUBTYPE_PLA || $left->operands[1]->pregnode->subtype == qtype_preg_node_assert::SUBTYPE_NLA);

            $neddspacepattern = $type1 == qtype_preg_node::TYPE_LEAF_OPTIONS ||
                                ($type1 == qtype_preg_node::TYPE_NODE_CONCAT && $left->operands[1]->pregnode->type == qtype_preg_node::TYPE_LEAF_OPTIONS);

            $key = 'description_' . $this->pregnode->subtype;
            if ($neddspacepattern) {
                $key .= '_space';
            } else if ($needshortpattern || $needcontiuneshortpattern) {
                $key .= '_short';
            } else if ($firstaheadassert || $secondbehindassert || $aheadassertinprevconcat) {
                $key .= '_and';
            } else if ($type1 == qtype_preg_node::TYPE_NODE_CONCAT) {
                $key .= '_wcomma';
            }
            $description = self::get_form_string($key, null, $form);

            // setup the description
            $replace = $this->what_to_replace($description);
            if ($prevdescription === null) {
                $prevdescription = $right->description($numbering_pattern, $this, $replace[2]['form']);
            }
            $description = qtype_poasquestion\string::replace($replace[2]['toreplace'], $prevdescription, $description);
            $child_description = $left->description($numbering_pattern, $this, $replace[1]['form']);
            $description = qtype_poasquestion\string::replace($replace[1]['toreplace'], $child_description, $description);
            $prevdescription = $description;
        }
        $description = $this->numbering_pattern($numbering_pattern, $description);
        qtype_preg_description_leaf_options::check_options($this, $description, $form);
        return $description;
    }
}

/**
 * Defines alternation, binary operator.
 */
class qtype_preg_description_node_alt extends qtype_preg_description_operator {

    public function description($numbering_pattern, $node_parent = null, $form = null) {
        $description = '';
        $childs_count = count($this->operands);
        $left = null;
        $right = null;
        $prevdescription = null;

        for ($i = $childs_count - 2; $i >= 0; $i--) {
            $left = $this->operands[$i];
            $right = $this->operands[$i + 1];

            $key = 'description_' . $this->pregnode->subtype;
            if ($i !== 0) {
                $key .= '_wcomma';
            }
            $description = self::get_form_string($key, null, $form);

            // setuping description
            $replace = $this->what_to_replace($description);
            if ($prevdescription === null) {
                $prevdescription = $right->description($numbering_pattern, $this, $replace[2]['form']);
            }
            $description = qtype_poasquestion\string::replace($replace[2]['toreplace'], $prevdescription, $description);
            $child_description = $left->description($numbering_pattern, $this, $replace[1]['form']);
            $description = qtype_poasquestion\string::replace($replace[1]['toreplace'], $child_description, $description);
            $prevdescription = $description;
        }
        $description = $this->numbering_pattern($numbering_pattern, $description);
        qtype_preg_description_leaf_options::check_options($this, $description, $form);
        return $description;
    }
}

/**
 * Defines lookaround assertions, unary operator.
 */
class qtype_preg_description_node_assert extends qtype_preg_description_operator {

    /**
     * Redifinition of abstract qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        $suff = ($node_parent !== null && $node_parent->pregnode->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR) ? '_cond' : '';
        return self::get_form_string('description_' . $this->pregnode->subtype . $suff, null, $form);
    }
}

/**
 * Defines subexpressions, unary operator.
 */
class qtype_preg_description_node_subexpr extends qtype_preg_description_operator {

    /**
     * Redifinition of abstract qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        $result = self::get_form_string($this->pregnode->lang_key(true), $this->pregnode, $form);
        return $result;
    }
}

/**
 * Defines conditional subexpressions, unary, binary or ternary operator.
 * The first operand yes-pattern, second - no-pattern, third - the lookaround assertion (if any).
 * Possible errors: there is no backreference with such number in expression
 */
class qtype_preg_description_node_cond_subexpr extends qtype_preg_description_operator {

    protected $condbranch;

    /**
     * Construct array of operands, using method qtype_regex_handler::from_preg_node()
     *
     * @param qtype_preg_node $node Reference to automatically generated (by handler) abstract node.
     * @param type $matcher Reference to handler, which generates nodes.
     */
    public function __construct($node, $matcher) {
        parent::__construct($node, $matcher);
        if ($this->pregnode->is_condition_assertion()) {
            $this->condbranch = array_shift($this->operands);
        }
    }

    /*private function description_of_condition($form) {
        $resultpattern = '';
        switch ($this->pregnode->condbranch->operands[0]->subtype) {
            case qtype_preg_node_assert::SUBTYPE_PLA:
                $resultpattern = self::get_form_string('description_pla_node_assert', null, $form);
                break;

            case qtype_preg_node_assert::SUBTYPE_NLA:
                $resultpattern = self::get_form_string('description_nla_node_assert', null, $form);
                break;

            case qtype_preg_node_assert::SUBTYPE_PLB:
                $resultpattern = self::get_form_string('description_plb_node_assert', null, $form);
                break;

            case qtype_preg_node_assert::SUBTYPE_NLB:
                $resultpattern = self::get_form_string('description_nlb_node_assert', null, $form);
                break;
        }
        return $resultpattern;
    }*/

    /**
     * Redifinition of abstract qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        $a = new stdClass;
        $a->number = $this->pregnode->number;
        $a->name = $a->number;
        $a->else = '';
        if (count($this->pregnode->operands) == 2 + (int)$this->pregnode->is_condition_assertion()) {
            $a->else = self::get_form_string('description_' . $this->pregnode->type . '_else', null, $form);
        }
        $needwrapper = $this->pregnode->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_RECURSION || $this->pregnode->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR;
        if ($needwrapper) {
            $a->cond = self::get_form_string($this->pregnode->lang_key(true), $a, $form);
            return self::get_form_string('description_subexpr_node_cond_subexpr_wrapper', $a, $form);
        }
        return self::get_form_string($this->pregnode->lang_key(true), $a, $form);
    }

    public function description($numbering_pattern, $node_parent = null, $form = null) {
        $resultpattern = parent::description($numbering_pattern, $this, $form);
        if (core_text::strpos($resultpattern, '%cond') !== false) {
            $conddescription = $this->condbranch->description($numbering_pattern, $this, $form);
            $resultpattern = qtype_poasquestion\string::replace('%cond', $conddescription, $resultpattern);
        }
        return $resultpattern;
    }
}

class qtype_preg_description_node_template extends qtype_preg_description_operator
{
    public $regex;
    public $template;

    /**
     * Construct array of operands, using method qtype_regex_handler::from_preg_node()
     *
     * @param qtype_preg_node $node Reference to automatically generated (by handler) abstract node.
     * @param type $matcher Reference to handler, which generates nodes.
     */
    public function __construct($node, $matcher) {
        parent::__construct($node, $matcher);
        if (empty($this->pregnode->errors)) {
            $this->template = qtype_preg\template::available_templates()[$this->pregnode->name];
            $this->regex = $matcher->from_preg_node($this->template->regex); // TODO - options
        }
        /*foreach ($this->pregnode->operands as $operand) {
            array_push($this->operands, $matcher->from_preg_node($operand));
        }*/
    }

    /**
     * Override of abstract qtype_preg_description_operator::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        if (!empty($this->pregnode->errors)) {
            return $this->pregnode->errors[0]->error_string();
        }
        return $this->template->get_description();
    }
}

class qtype_preg_description_leaf_template extends qtype_preg_description_leaf {
    public function __construct($node, $matcher) {
        parent::__construct($node, $matcher);
        if (empty($this->pregnode->errors)) {
            $this->template = qtype_preg\template::available_templates()[$this->pregnode->name];
            $this->regex = $matcher->from_preg_node($this->template->regex); // TODO - options
        }
        /*foreach ($this->pregnode->operands as $operand) {
            array_push($this->operands, $matcher->from_preg_node($operand));
        }*/
    }

    /**
     * Override of abstract qtype_preg_description_operator::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        if (!empty($this->pregnode->errors)) {
            return $this->pregnode->errors[0]->error_string();
        }
        return $this->template->get_description();
    }
}

class qtype_preg_description_node_error extends qtype_preg_description_operator {

    public function pattern($node_parent = null, $form = null) {
        $resultpattern = self::get_form_string('description_errorbefore', null, null)
            . $this->pregnode->error_string()
            . self::get_form_string('description_errorafter', null, null);

        $operandplaces = array();
        foreach ($this->pregnode->operands as $i => $operand) {
            if (isset($operand)) {
                $operandplaces[] = '%' . ($i + 1);
            }
        }
        if (count($operandplaces) != 0) {
            $resultpattern .= ' Operands: ' . implode(', ', $operandplaces);
        }

        return $resultpattern;
    }
}
