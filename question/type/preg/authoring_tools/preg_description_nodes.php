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
 * Defines handler for generating description of reg exp
 * Also defines specific tree, containing methods for generating descriptions of current node
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Pahomov Dmitry
 * @license http:// www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
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
     * @param string $s same as in get_string
     * @param string $form Required form.
     */
    protected static function get_form_string($s, $a, $form = null) {

        if (is_string($a)) {
            $form = $a;
            $a = null;
            $usea = false;
        }
        if (isset($form) && $form !== '') {
            $s .= '_' . $form;
        }
        $str = get_string($s, 'qtype_preg', $a);
        // TODO process $a directly in classes
        $str = qtype_poasquestion_string::replace('{$a->firstoperand}', '%1', $str);
        $str = qtype_poasquestion_string::replace('{$a->secondoperand}', '%2', $str);
        $str = qtype_poasquestion_string::replace('{$a->thirdoperand}', '%3', $str);
        $str = qtype_poasquestion_string::replace('{$a->', '%', $str);
        $str = qtype_poasquestion_string::replace('}', '', $str);
        return $str;
    }

    /**
     * returns true if engine support the node, rejection string otherwise
     */
    public function accept() {
        return true;
    }

    /**
     * Puts $s instead of %s in numbering pattern. Puts node id instead of %n.
     *
     * @param type $s this string will be placed instead of %s
     */
    protected function numbering_pattern($numbering_pattern, $s) { // TODO - rename
        //return qtype_poasquestion_string::replace('%s', $s, qtype_poasquestion_string::replace('%n', $this->pregnode->id, $numbering_pattern));
        $result = $s;
        $classes = array();
        $color = 'white';
        $selected = $this->pregnode->position->indfirst >= $this->handler->get_options()->selection->indfirst &&
                    $this->pregnode->position->indlast <= $this->handler->get_options()->selection->indlast;

        // Highlight generated and selected nodes.
        if ($this->handler->is_node_generated($this->pregnode)) {
            $color = 'lightgrey';
        }
        if ($selected) {
            $color = 'yellow';
        }

        if ($classes !== array() || $color !== '') {
            $classesstr = ' class="' . implode(' ', $classes) . '"';
            $stylestr = ' style="background: ' . $color . '"';
            $result = '<span' . $classesstr . $stylestr . '>' . $result . '</span>';
        }

        return $result;
    }
}

/**
 * Generic leaf class.
 */
abstract class qtype_preg_description_leaf extends qtype_preg_description_node {

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {

        return 'seems like pattern() for ' . get_class($this) . ' node didnt redefined';
    }

    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern, $node_parent = null, $form = null) {
        $description ='';
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
     * Checks if charset contains only one printing character
     */
    public function is_one_char() {
        $flag = $this->pregnode->flags[0][0];
        return count($this->pregnode->flags) === 1
            && $flag->type===qtype_preg_charset_flag::SET
            && $flag->data->length() === 1
            && self::is_chr_printable(textlib::utf8ord($flag->data[0]));
    }

    /**
     * Checks if a character is printable
     *
     * @param $utf8chr character (from qtype_poasquestion_string) for check
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
        $result = '';
        $hexcode = textlib::strtoupper(dechex($code));
        if ($code <= 32 || $code == 127 || $code == 160 || $code == 173 || $code == 8194 ||
            $code == 8195 || $code == 8201 || $code == 8204 || $code == 8205) {
            $result = self::get_form_string('description_char' . $hexcode, $form);
        } else {
            $result = qtype_poasquestion_string::replace('%code', $hexcode, self::get_form_string('description_char_16value', $form));
        }
        return $result;
    }

    /**
     * Describes character with code $code
     *
     * @param int|qtype_poasquestion_string $utf8chr character from qtype_poasquestion_string for describe or decimal code of character
     * @param bool $escapehtml a flag indicating whether to escape html characters (& < > " ')
     * @param string $form required form
     * @return string describes of character
     */
    public static function describe_chr($utf8chr, $escapehtml = true, $form = null) {
        $iscode = is_int($utf8chr);
        $code = $iscode ? $utf8chr : textlib::utf8ord($utf8chr);
        $result = self::describe_nonprinting($code);
        if ($result === null) {
            //   &        >       <       "       '
            // &#38;    &#62;   &#60;   &#34;   &#39;
            if ($escapehtml) {
                $result = qtype_preg_authoring_tool::escape_char_by_code($code, 'html');
            } else {
                $result = $iscode ? textlib::code2utf8($utf8chr) : $utf8chr;
            }
            $result = qtype_poasquestion_string::replace('%char', $result, self::get_form_string('description_char', $form));
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
     * @param $str object of qtype_poasquestion_string.
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
        $curcode = -1;
        for ($i = 0; $i < $length; $i++) {
            $curcode = textlib::utf8ord($str[$i]);
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

        if ($flag->type === qtype_preg_charset_flag::FLAG || $flag->type === qtype_preg_charset_flag::UPROP) {
            // current flag is something like \w or \pL
            if ($flag->negative == true) {
                // using charset pattern 'description_charset_one_neg' because char pattern 'description_char_neg' has a <span> tag,
                // but dont need to highlight this
                $temp_str = self::get_form_string('description_charflag_' . $flag->data, $form);
                $characters[] = qtype_poasquestion_string::replace('%characters', $temp_str, self::get_form_string('description_charset_one_neg', $form));
            } else {
                $characters[] = self::get_form_string('description_charflag_' . $flag->data, $form);
            }
        } else if ($flag->type === qtype_preg_charset_flag::SET) {
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
                            $temp_str = self::get_form_string('description_charset_range', $form);
                            $temp_str = qtype_poasquestion_string::replace('%start', self::describe_chr($range[0], true, $form), $temp_str);
                            $temp_str = qtype_poasquestion_string::replace('%end', self::describe_chr($range[1], true, $form), $temp_str);
                            $characters[] = $temp_str;
                        }
                    }
                }
            }
        }
    }

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        $result_pattern = '';
        $characters = array();
        // check errors
        if (count($this->pregnode->errors) > 0) {
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
            $result_pattern = $characters[0];
        } else {
            if (count($characters) == 1 && $this->pregnode->negative) {
                $result_pattern = self::get_form_string('description_charset_one_neg', $form);
            } else if (!$this->pregnode->negative) {
                $result_pattern = self::get_form_string('description_charset', $form);
            } else {
                $result_pattern = self::get_form_string('description_charset_negative', $form);
            }
            $result_pattern = qtype_poasquestion_string::replace('%characters', implode(", ", $characters), $result_pattern);

        }
        return $result_pattern;
    }
}


/**
 * Defines meta-characters that can't be enumerated.
 */
class qtype_preg_description_leaf_meta extends qtype_preg_description_leaf {

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        return self::get_form_string('description_empty', $form);
    }
}

class qtype_preg_description_leaf_assert extends qtype_preg_description_leaf {
    public function pattern($node_parent = null, $form = null) {
        $key = 'description_' . $this->pregnode->subtype;
        if ($this->pregnode->negative) {
            $key .= '_neg';
        }
        return self::get_form_string($key, $form);
    }
}

/**
 * Defines backreferences.
 */
class qtype_preg_description_leaf_backref extends qtype_preg_description_leaf {

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        $resultpattern = self::get_form_string('description_backref', $form);
        $resultpattern = qtype_poasquestion_string::replace('%number', $this->pregnode->number, $resultpattern);
        return $resultpattern;
    }

}

class qtype_preg_description_leaf_options extends qtype_preg_description_leaf {

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        $resultpattern = '';
        $posopt =& $this->pregnode->posopt;
        $negopt =& $this->pregnode->negopt;
        if ($posopt->length() > 0) {
            $this->handler->state->set_modifier($posopt[0], true);
            $resultpattern = self::get_form_string('description_option_' . $posopt[0], $form);
        } else if ($negopt->length() > 0) {
            $this->handler->state->set_modifier($negopt[0], false);
            $resultpattern = self::get_form_string('description_unsetoption_' . $negopt[0], $form);
        }
        $a = new stdClass();
        $a->option = $resultpattern;
        $resultpattern = self::get_form_string('description_option_wrapper', $a, $form);
        return $resultpattern;
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
        $resultpattern = '';
        $mcaseless =& $node->handler->state->caseless;
        $msingleline =& $node->handler->state->singleline;
        $mmultilineline =& $node->handler->state->multilineline;
        $mextended =& $node->handler->state->extended;
        $mungreedy =& $node->handler->state->ungreedy;
        $mduplicate =& $node->handler->state->duplicate;

        if ($node->pregnode->type === qtype_preg_node::TYPE_NODE_SUBEXPR) {
            $node->handler->state->forceunsetmodifiers = true;
        } else if ($node->handler->state->forceunsetmodifiers === true) {
            // TODO - generate 'caseless, singleline:' instead of 'caseless: singleline:'
            if ($mcaseless === true) {
                $resultpattern .= self::get_form_string('description_unsetoption_i', $form);
                $mcaseless = false;
            }
            if ($msingleline === true) {
                $resultpattern .= self::get_form_string('description_unsetoption_s', $form);
                $msingleline = false;
            }
            if ($mmultilineline === true) {
                $resultpattern .= self::get_form_string('description_unsetoption_m', $form);
                $mmultilineline = false;
            }
            if ($mextended === true) {
                $resultpattern .= self::get_form_string('description_unsetoption_x', $form);
                $mextended = false;
            }
            if ($mungreedy === true) {
                $resultpattern .= self::get_form_string('description_unsetoption_U', $form);
                $mungreedy = false;
            }
            if ($mduplicate === true) {
                $resultpattern .= self::get_form_string('description_unsetoption_J', $form);
                $mduplicate = false;
            }
            if ($resultpattern !== '') {
                $a = new stdClass();
                $a->option = $resultpattern;
                $resultpattern = self::get_form_string('description_option_wrapper', $a, $form) . ' ';
            }
            $node->handler->state->forceunsetmodifiers = false;
            $node_pattern = $resultpattern . $node_pattern;
        }
    }
}

class qtype_preg_description_leaf_recursion extends qtype_preg_description_leaf {

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        $resultpattern = '';
        if ($this->pregnode->number === 0) {
             $resultpattern = self::get_form_string('description_recursion_all', $form);
        } else {
             $resultpattern = self::get_form_string('description_recursion', $form);
             $resultpattern = qtype_poasquestion_string::replace('%number', $this->pregnode->number, $resultpattern);
        }
        return $resultpattern;
    }
}

/**
 * Reperesents backtracking control, newline convention etc sequences like (*...).
 */
class qtype_preg_description_leaf_control extends qtype_preg_description_leaf {

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        $resultpattern = '';

        if ($this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_ACCEPT ||
            $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_FAIL ||
            $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_MARK_NAME ||
            $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_NO_START_OPT ||
            $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_UTF8 ||
            $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_UTF16 ||
            $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_UCP) {

            $resultpattern = self::get_form_string('description_' . $this->pregnode->subtype, $form);

        } else if ($this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_COMMIT ||
                   $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_PRUNE ||
                   $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_SKIP ||
                   $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_THEN ) {

            $resultpattern = self::get_form_string('description_control_backtrack', $form);
            $resultpattern = qtype_poasquestion_string::replace('%what', self::get_form_string('description_' . $this->pregnode->subtype, $form), $resultpattern);

        } else if ($this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_SKIP_NAME) {

            $resultpattern = self::get_form_string('description_control_backtrack', $form);
            $resultpattern = qtype_poasquestion_string::replace('%what', self::get_form_string('description_' . $this->pregnode->subtype, $form), $resultpattern);
            $resultpattern = qtype_poasquestion_string::replace('%name', $this->pregnode->name, $resultpattern);

        } else if ($this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_CR ||
                   $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_LF ||
                   $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_CRLF ||
                   $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_ANYCRLF ||
                   $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_ANY) {

            $resultpattern = self::get_form_string('description_control_newline', $form);
            $resultpattern = qtype_poasquestion_string::replace('%what', self::get_form_string('description_' . $this->pregnode->subtype, $form), $resultpattern);

        } else {
            $resultpattern = self::get_form_string('description_control_r', $form);
            $resultpattern = qtype_poasquestion_string::replace('%what', self::get_form_string('description_' . $this->pregnode->subtype, $form), $resultpattern);
        }
        return $resultpattern;
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
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        return 'seems like pattern() for ' . get_class($this) . ' node is not redefined';
    }

    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern, $node_parent = null, $form = null) {
        $description = '';
        $child_description = '';

        $this->pattern = $this->pattern($node_parent, $form);
        $description = $this->numbering_pattern($numbering_pattern, $this->pattern);

        $replaces = $this->what_to_replace($description);
        foreach ($replaces as $num => $data) {
            $child_description = $this->operands[$num - 1]->description($numbering_pattern, $this, $data['form']);
            $description = qtype_poasquestion_string::replace($data['toreplace'], $child_description, $description);
        }
        qtype_preg_description_leaf_options::check_options($this, $description, $form);
        return $description;
    }

    protected function what_to_replace($str) {
        $str = new qtype_poasquestion_string($str);
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
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        $resultpattern = '';
        $greedypattern = '';
        $wrong_borders = $this->pregnode->leftborder > $this->pregnode->rightborder;

        if ($this->pregnode->leftborder == 0 ) {
            if ($this->pregnode->rightborder == 1) {
                $resultpattern = self::get_form_string('description_finite_quant_01', $form);
                $resultpattern = qtype_poasquestion_string::replace('%rightborder', $this->pregnode->rightborder, $resultpattern);
            } else {
                $resultpattern = self::get_form_string('description_finite_quant_0', $form);
                $resultpattern = qtype_poasquestion_string::replace('%rightborder', $this->pregnode->rightborder, $resultpattern);
            }

        } else if ($this->pregnode->leftborder == 1) {
            $resultpattern = self::get_form_string('description_finite_quant_1', $form);
            $resultpattern = qtype_poasquestion_string::replace('%rightborder', $this->pregnode->rightborder, $resultpattern);
        } else if ($this->pregnode->leftborder == $this->pregnode->rightborder) {
            $resultpattern = self::get_form_string('description_finite_quant_strict', $form);
            $resultpattern = qtype_poasquestion_string::replace('%count', $this->pregnode->rightborder, $resultpattern);
        } else {
            $resultpattern = self::get_form_string('description_finite_quant', $form);
            $resultpattern = qtype_poasquestion_string::replace('%rightborder', $this->pregnode->rightborder, $resultpattern);
            $resultpattern = qtype_poasquestion_string::replace('%leftborder', $this->pregnode->leftborder, $resultpattern);
        }

        if ($this->pregnode->lazy) {
            $greedypattern = self::get_form_string('description_quant_lazy', $form);
        } else if ($this->pregnode->greedy) {
            $greedypattern = self::get_form_string('description_quant_greedy', $form);
        } else if ($this->pregnode->possessive) {
            $greedypattern = self::get_form_string('description_quant_possessive', $form);
        }
        $resultpattern = qtype_poasquestion_string::replace('%greedy', $greedypattern, $resultpattern);

        if ($wrong_borders) {
            $resultpattern = preg_replace('/%(\w+)?1/u', ('%$ {1}1' . self::get_form_string('description_errorbefore', $form)), $resultpattern);
            $resultpattern = $resultpattern
                .self::get_form_string('description_finite_quant_borders_err', $form)
                .self::get_form_string('description_errorafter', $form);
        }
        return $resultpattern;
    }
}

/**
 * Defines infinite quantifiers node with the left border only, unary operator.
 */
class qtype_preg_description_node_infinite_quant extends qtype_preg_description_operator {

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        $resultpattern = '';
        $greedypattern = '';
        if ($this->pregnode->leftborder == 0) {
            $resultpattern = self::get_form_string('description_infinite_quant_0', $form);
        } else if ($this->pregnode->leftborder == 1) {
            $resultpattern = self::get_form_string('description_infinite_quant_1', $form);
        } else {
            $resultpattern = self::get_form_string('description_infinite_quant', $form);
            $resultpattern = qtype_poasquestion_string::replace('%leftborder', $this->pregnode->leftborder, $resultpattern);
        }

        if ($this->pregnode->lazy) {
            $greedypattern = self::get_form_string('description_quant_lazy', $form);
        } else if ($this->pregnode->greedy) {
            $greedypattern = self::get_form_string('description_quant_greedy', $form);
        } else if ($this->pregnode->possessive) {
            $greedypattern = self::get_form_string('description_quant_possessive', $form);
        }

        $resultpattern = qtype_poasquestion_string::replace('%greedy', $greedypattern, $resultpattern);
        return $resultpattern;
    }
}

/**
 * Defines concatenation, binary operator.
 */
class qtype_preg_description_node_concat extends qtype_preg_description_operator {

    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
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

            $needshortpattern = $type1 == qtype_preg_node::TYPE_LEAF_CHARSET && $left->is_one_char() &&
                                $type2 == qtype_preg_node::TYPE_LEAF_CHARSET && $right->is_one_char();

            $needcontiuneshortpattern = $type2 == qtype_preg_node::TYPE_LEAF_CHARSET && $right->is_one_char() &&
                                        $type1 == qtype_preg_node::TYPE_NODE_CONCAT &&
                                        $left->operands[1]->pregnode->type == qtype_preg_node::TYPE_LEAF_CHARSET && $left->operands[1]->is_one_char();

            $firstaheadassert = ($subtype1 == qtype_preg_node_assert::SUBTYPE_PLA || $subtype1 == qtype_preg_node_assert::SUBTYPE_NLA);

            $secondbehindassert = ($subtype2 == qtype_preg_node_assert::SUBTYPE_PLB || $subtype2 == qtype_preg_node_assert::SUBTYPE_NLB);

            $aheadassertinprevconcat = $type1 == qtype_preg_node::TYPE_NODE_CONCAT &&
                                       ($left->operands[1]->pregnode->subtype == qtype_preg_node_assert::SUBTYPE_PLA || $left->operands[1]->pregnode->subtype == qtype_preg_node_assert::SUBTYPE_NLA);

            $neddspacepattern = $type1 == qtype_preg_node::TYPE_LEAF_OPTIONS ||
                                ($type1 == qtype_preg_node::TYPE_NODE_CONCAT && $left->operands[1]->pregnode->type == qtype_preg_node::TYPE_LEAF_OPTIONS);

            if ($neddspacepattern) {
                $description = self::get_form_string('description_concat_space', $form);
            } else if ($needshortpattern || $needcontiuneshortpattern) {
                $description = self::get_form_string('description_concat_short', $form);
            } else if ($firstaheadassert || $secondbehindassert || $aheadassertinprevconcat) {
                $description = self::get_form_string('description_concat_and', $form);
            } else if ($type1 == qtype_preg_node::TYPE_NODE_CONCAT) {
                $description = self::get_form_string('description_concat_wcomma', $form);
            } else {
                $description = self::get_form_string('description_concat', $form);
            }

            // setup the description
            $replace = $this->what_to_replace($description);
            if ($prevdescription === null) {
                $prevdescription = $right->description($numbering_pattern, $this, $replace[2]['form']);
            }
            $description = qtype_poasquestion_string::replace($replace[2]['toreplace'], $prevdescription, $description);
            $child_description = $left->description($numbering_pattern, $this, $replace[1]['form']);
            $description = qtype_poasquestion_string::replace($replace[1]['toreplace'], $child_description, $description);
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

            // getting pattern
            if ($i !== 0) {
                $description = self::get_form_string('description_alt_wcomma', $form);
            } else {
                $description = self::get_form_string('description_alt', $form);
            }

            // setuping description
            $replace = $this->what_to_replace($description);
            if ($prevdescription === null) {
                $prevdescription = $right->description($numbering_pattern, $this, $replace[2]['form']);
            }
            $description = qtype_poasquestion_string::replace($replace[2]['toreplace'], $prevdescription, $description);
            $child_description = $left->description($numbering_pattern, $this, $replace[1]['form']);
            $description = qtype_poasquestion_string::replace($replace[1]['toreplace'], $child_description, $description);
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
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        $suff = ($node_parent !== null && $node_parent->pregnode->type === qtype_preg_node::TYPE_NODE_COND_SUBEXPR) ? '_cond' : '';
        return self::get_form_string('description_' . $this->pregnode->subtype . $suff, $form);
    }
}

/**
 * Defines subexpressions, unary operator.
 */
class qtype_preg_description_node_subexpr extends qtype_preg_description_operator {

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        $resultpattern = '';
        if ($this->pregnode->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING) {
            $resultpattern = self::get_form_string('description_grouping', $form);
        } else if ($this->pregnode->subtype === qtype_preg_node_subexpr::SUBTYPE_DUPLICATE_SUBEXPRESSIONS) {
            $resultpattern = self::get_form_string('description_grouping_duplicate', $form);
        } else if (is_string($this->pregnode->number)) {
            if ($this->pregnode->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR) {
                $resultpattern = self::get_form_string('description_subexpression_name', $form);
            } else {
                $resultpattern = self::get_form_string('description_subexpression_once_name', $form);
            }
            $resultpattern = qtype_poasquestion_string::replace('%name', $this->pregnode->number, $resultpattern);
        } else {
            if ($this->pregnode->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR) {
                $resultpattern = self::get_form_string('description_subexpression', $form);
            } else {
                $resultpattern = self::get_form_string('description_subexpression_once', $form);
            }
            $resultpattern = qtype_poasquestion_string::replace('%number', $this->pregnode->number, $resultpattern);

        }
        return $resultpattern;
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
                $resultpattern = self::get_form_string('description_pla_node_assert', $form);
                break;

            case qtype_preg_node_assert::SUBTYPE_NLA:
                $resultpattern = self::get_form_string('description_nla_node_assert', $form);
                break;

            case qtype_preg_node_assert::SUBTYPE_PLB:
                $resultpattern = self::get_form_string('description_plb_node_assert', $form);
                break;

            case qtype_preg_node_assert::SUBTYPE_NLB:
                $resultpattern = self::get_form_string('description_nlb_node_assert', $form);
                break;
        }
        return $resultpattern;
    }*/

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent = null, $form = null) {
        $resultpattern = '';
        if ($this->pregnode->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR) {
            if (is_string($this->pregnode->number)) {
                $resultpattern = self::get_form_string('description_backref_node_cond_subexpr_name', $form);
                $resultpattern = qtype_poasquestion_string::replace('%name', $this->pregnode->number, $resultpattern);
            } else {
                $resultpattern = self::get_form_string('description_backref_node_cond_subexpr', $form);
                $resultpattern = qtype_poasquestion_string::replace('%number', $this->pregnode->number, $resultpattern);
            }
        } else if ($this->pregnode->subtype===qtype_preg_node_cond_subexpr::SUBTYPE_RECURSION) {
            if (is_string($this->pregnode->number)) {
                $resultpattern = self::get_form_string('description_recursive_node_cond_subexpr_name', $form);
                $resultpattern = qtype_poasquestion_string::replace('%name', $this->pregnode->number, $resultpattern);
            } else if ($this->pregnode->number===0) {
                $resultpattern = self::get_form_string('description_recursive_node_cond_subexpr_all', $form);
            } else {
                $resultpattern = self::get_form_string('description_recursive_node_cond_subexpr', $form);
                $resultpattern = qtype_poasquestion_string::replace('%number', $this->pregnode->number, $resultpattern);
            }
        } else if ($this->pregnode->subtype===qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE) {
            $resultpattern = self::get_form_string('description_define_node_cond_subexpr', $form);
        } else {
            $resultpattern = self::get_form_string('description_node_cond_subexpr', $form);
            // $resultpattern = qtype_poasquestion_string::replace('%cond', '%'.count($this->pregnode->operands), $resultpattern);
        }
        $elsereplase = count($this->pregnode->operands) == 2 + (int)$this->pregnode->is_condition_assertion()
                     ? self::get_form_string('description_node_cond_subexpr_else', $form)
                     : '';
        $resultpattern = qtype_poasquestion_string::replace('%else', $elsereplase, $resultpattern);
        return $resultpattern;
    }

    public function description($numbering_pattern, $node_parent = null, $form = null) {
        $resultpattern = parent::description($numbering_pattern, $this, $form);
        if (textlib::strpos($resultpattern, '%cond') !== false) {
            $conddescription = $this->condbranch->description($numbering_pattern, $this, $form);
            $resultpattern = qtype_poasquestion_string::replace('%cond', $conddescription, $resultpattern);
        }
        return $resultpattern;
    }
}

class qtype_preg_description_node_error extends qtype_preg_description_operator {

    public function pattern($node_parent = null, $form = null) {
        $resultpattern = self::get_form_string('description_errorbefore', null)
            . $this->pregnode->error_string()
            . self::get_form_string('description_errorafter', null);

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
