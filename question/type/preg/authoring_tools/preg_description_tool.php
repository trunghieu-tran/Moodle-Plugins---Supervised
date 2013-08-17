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
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_authoring_tool.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');

/**
 * State of description generating
 */
class qtype_preg_description_state {

    /** @var bool is (?i) set */
    public $caseless = false;

    /** @var bool is (?s) set */
    public $singleline = false;

    /** @var bool is (?m) set */
    public $multilineline = false;

    /** @var bool is (?x) set */
    public $extended = false;

    /** @var bool is (?U) set */
    public $ungreedy = false;

    /** @var bool is (?J) set */
    public $duplicate = false;

    public $forceunsetmodifiers = false;

    /**
     * set default values to all state variables
     */
    public function reset() {
        $this->caseless        = false;
        $this->singleline      = false;
        $this->multilineline   = false;
        $this->extended        = false;
        $this->ungreedy        = false;
        $this->duplicate       = false;
        $this->forceunsetmodifiers = false;
    }

    /**
     * Set or unsets the flag meaning that $modifier is (un)set
     *
     * @param string $modifier modifier to (un)set
     */
    public function set_modifier($modifier, $value) {
        switch ($modifier) {
            case 'i':
                $this->caseless = $value;
                break;
            case 's':
                $this->singleline = $value;
                break;
            case 'm':
                $this->multilineline = $value;
                break;
            case 'x':
                $this->extended = $value;
                break;
            case 'U':
                $this->ungreedy = $value;
                break;
            case 'J':
                $this->duplicate = $value;
                break;
        }
    }
}

/**
 * Options, for generating description - affects scanning, parsing, description genetating.
 */
class qtype_preg_description_options extends qtype_preg_handling_options {

    /** @var bool use userinscription for charset description instead of flags */
    public $charsetuserinscription = false;

    /** @var int limit for charset in which it is displayed as a enum of characters */
    public $rangelengthmax = 5;

    public function __construct() {
        $this->preserveallnodes = true;
    }
}

/**
 * Handler, generating information for regular expression
 */
class qtype_preg_description_tool extends qtype_preg_authoring_tool {

    /** @var qtype_preg_description_state state of description generating */
    public $state;

    /*
     * Construct of parent class parses the regex and does all necessary preprocessing.
     *
     * @param string $regex - regular expression to handle.
     * @param object $options - options to handle regex, i.e. any necessary additional parameters.
     */
    public function __construct($regex = null, $options = null, $engine = null, $notation = null) {
        parent::__construct($regex, $options, $engine, $notation);
        $this->state = new qtype_preg_description_state();
    }

    /**
     * Overloaded from qtype_preg_regex_handler.
     */
    public function name() {
        return 'description_tool';
    }

    /**
     * Overloaded from qtype_preg_regex_handler.
     */
    protected function node_infix() {
        return 'description';
    }

    /**
     * Overloaded from qtype_preg_regex_handler.
     */
    protected function is_preg_node_acceptable($pregnode) {
        return true;
    }

    /**
     * Overloaded from qtype_preg_authoring_tool.
     */
    protected function json_key() {
        return 'description';
    }

    /**
     * Generate description
     *
     * @param array $json contains text of description
     */
    protected function generate_json_for_accepted_regex(&$json, $id = -1) {
        $json[$this->json_key()] = $this->default_description();
    }

    public function options() {
        return $this->options;
    }

    /**
     * Genegates description of regexp
     * Example of calling:
     *
     * description('<span class="description_node_%n">%s</span>', '<span class="description">%s</span>');
     *
     * Operator with id=777 will be plased into: <span class="description_node_777">abc</span>.
     * User defined parts of regex with id=777 will be placed id: <span class="description_node_777">%1 or %2</span>.
     * Whole string will be placed into <span class="description">string</span>
     *
     * @param string $wholepattern Pattern for whole decription. Must contain %s - description.
     * @param string $numbering_pattern Pattern to track numbering.
     * Must contain: %s - description of node;
     * May contain:  %n - node id.
     * @param bool $charsetuserinscr use userinscription for charset description instead of flags
     * @param int $rangelengthmax limit for charset ranges in which it is displayed as a enum of characters
     * @return string description.
     */
    public function description($numbering_pattern, $wholepattern=null, $charsetuserinscr=false, $rangelengthmax=5) {

        // set up options
        $this->state->reset();// restore default state
        $backupoptions = $this->options;// save original options
        $this->options->charsetuserinscription  = (bool)$charsetuserinscr;
        $this->options->rangelengthmax          = (int)$rangelengthmax;
        // make description
        if (isset($this->dst_root)) {
            // var_dump(123);
            $string = $this->dst_root->description($numbering_pattern, null, null);
            $string = $this->postprocessing($string);
        } else {
            $string = 'tree was not built';
        }
        // put string into $wholepattern
        if ($wholepattern !== null && $wholepattern !== '') {
            $string = str_replace('%s', $string, $wholepattern);
        }
        $this->options = $backupoptions; // restore original options
        return $string;
    }

    private function postprocessing($s) {

        $result = preg_replace('%;((?:</span>)?)]%', '\1]', $s);
        return $result;
    }

    /**
     * Calling default description() with default params
     */
    public function default_description() {

        return $this->description('<span class="description_node_%n">%s</span>');
    }

    /**
     * for testing
     */
    public function form_description($form) {
        $result = $this->dst_root->description('%s', null, $form);
        return $result;
    }
}


/**
 * Generic node class.
 */
abstract class qtype_preg_description_node {
    /** @var string pattern for description of current node */
    public $pattern;

    /** @var qtype_preg_node Aggregates a pointer to the automatically generated abstract node */
    public $pregnode;

    /** @var Reference to handler (for reading global option) */
    public $handler;

    /**
     * Constructs node.
     *
     * @param qtype_preg_node $node Reference to automatically generated (by handler) abstract node.
     * @param type $matcher Reference to handler, which generates nodes.
     */
    public function __construct($node, &$matcher) {
        $this->handler =& $matcher;
        $this->pregnode = $node;
    }

    /**
     * Chooses pattern for current node.
     *
     * @param qtype_preg_description_node $node_parent Reference to the parent.
     * @param string $form Required form.
     * @return string Chosen pattern.
     */
    abstract public function pattern($node_parent=null, $form=null);

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
    abstract public function description($numbering_pattern, $node_parent=null, $form=null);

    /**
     * gets localized string, if required a form it gets localized string for required form
     *
     * @param string $s same as in get_string
     * @param string $form Required form.
     */
    protected static function get_form_string($s, $a, $form=null) {

        if (!is_object($a)) {
            $form = $a;
            $a = null;
        }
        if (isset($form) && $form !== '') {
            $s.='_'.$form;
        }
        $str = get_string($s, 'qtype_preg', $a);
        // TODO process $a directly in classes
        $str = str_replace('{$a->firstoperand}', '%1', $str);
        $str = str_replace('{$a->secondoperand}', '%2', $str);
        $str = str_replace('{$a->thirdoperand}', '%3', $str);
        $str = str_replace('{$a->', '%', $str);
        $str = str_replace('}', '', $str);
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
    protected function numbering_pattern($numbering_pattern, $s) {
        return str_replace('%s', $s, str_replace('%n', $this->pregnode->id, $numbering_pattern));
    }
}

/**
 * Generic leaf class.
 */
abstract class qtype_preg_description_leaf extends qtype_preg_description_node {

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null, $form=null) {

        return 'seems like pattern() for '.get_class($this).' node didnt redefined';
    }

    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern, $node_parent=null, $form=null) {

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
        return count($this->pregnode->flags)===1
            && $flag->type===qtype_preg_charset_flag::SET
            && $flag->data->length()===1
            && self::is_chr_printable(textlib::utf8ord($flag->data[0]));
    }

    /**
     * Checks if a character is printable
     *
     * @param $utf8chr character (from qtype_poasquestion_string) for check
     */
    public static function is_chr_printable($code) {
        // var_dump($code);
        return qtype_preg_unicode::search_number_binary($code, qtype_preg_unicode::C_ranges())===false &&
               qtype_preg_unicode::search_number_binary($code, qtype_preg_unicode::Z_ranges())===false;
    }

    /*
     * Returns description of $utf8chr if it is non-printing character, otherwise returns null
     *
     * @param int $code character code
     * @return string|null description of character (if character is non printable) or null.
     */
    public static function describe_nonprinting($code, $form=null) {
        // null returns if description is not needed
        if ($code === null || self::is_chr_printable($code)) {
            return null;
        }
        // ok, character is non-printing, lets find its description in the language file
        $result = '';
        $hexcode = strtoupper(dechex($code));
        if ($code<=32||$code==127||$code==160||$code==173
            ||$code==8194||$code==8195||$code==8201||$code==8204||$code==8205) {
            $result = self::get_form_string('description_char'.$hexcode, $form);
        } else {
            $result = str_replace('%code', $hexcode,
                                  self::get_form_string('description_char_16value' , $form));
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
    public static function describe_chr($utf8chr, $escapehtml=true, $form=null) {
        $iscode = is_int($utf8chr);
        $code = $iscode ? $utf8chr : textlib::utf8ord($utf8chr);
        $result = self::describe_nonprinting($code);
        if ($result===null) {
            //   &        >       <       "       '
            // &#38;    &#62;   &#60;   &#34;   &#39;
            if ($escapehtml) {
                $result = qtype_preg_authoring_tool::escape_char_by_code($code, 'html');
            } else {
                $result = $iscode ? textlib::code2utf8($utf8chr) : $utf8chr;
            }
            $result = str_replace('%char', $result, self::get_form_string('description_char' , $form));
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
        $lenth = $str->length();
        $badparams = !($str instanceof qtype_poasquestion_string) && $lenth < 1;
        if ($badparams)
            return false;
        $result = array();
        $rangestart = 0;
        $prevcode = -1;
        $state = self::FIRST_CHAR;
        $curcode = -1;
        for ($i=0; $i<$lenth; $i++) {
            // if-else magic 8-)
            $curcode = textlib::utf8ord($str[$i]);
            if ($state==self::FIRST_CHAR) {
                $state = self::OUT_OF_RANGE;
            } else if ($state == self::INTO_RANGE) {
                if ($curcode-1 != $prevcode) {
                    $state = self::OUT_OF_RANGE;
                    $result[] = array($rangestart, $prevcode);
                }
            } else if ($state == self::OUT_OF_RANGE) {
                if ($curcode-1 == $prevcode) {
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
    private function flag_to_array($flag, &$characters, $form=null) {

        $temp_str = '';
        $ranges = null;
        $rangelength = null;
        $rangelengthmax = null;

        if ($flag->type === qtype_preg_charset_flag::FLAG || $flag->type === qtype_preg_charset_flag::UPROP) {
            // current flag is something like \w or \pL
            if ($flag->negative == true) {
                // using charset pattern 'description_charset_one_neg' because char pattern 'description_char_neg' has a <span> tag,
                // but dont need to highlight this
                $temp_str = self::get_form_string('description_charflag_'.$flag->data, $form);
                $characters[] = str_replace('%characters', $temp_str, self::get_form_string('description_charset_one_neg', $form));
            } else {
                $characters[] = self::get_form_string('description_charflag_'.$flag->data, $form);
            }

        } else if ($flag->type === qtype_preg_charset_flag::SET) {
            // flag is a simple enumeration of characters
            if ($flag->data->length()==1) {
                $characters[] = self::describe_chr($flag->data[0], true, $form);
            } else {
                $ranges = $this->find_ranges($flag->data);
                // var_dump($ranges);
                $rangelengthmax =& $this->handler->options()->rangelengthmax;
                foreach ($ranges as $range) {
                    if (is_int($range)) { // $range is a code of character
                        $characters[] = self::describe_chr($range, true, $form);
                    } else { // $range is a range (from A to Z <=> array(65,90) )
                        $rangelength = $range[1]-$range[0];
                        if ($rangelength<$rangelengthmax) { // if length of range less than $rangelengthmax it will be displayed as enumeration
                            for ($i=$range[0]; $i<=$range[1]; $i++) {
                                $characters[] = self::describe_chr($i, true, $form);
                            }
                        } else { // otherwise it will be displayed
                            $temp_str = self::get_form_string('description_charset_range' , $form);
                            $temp_str = str_replace('%start', self::describe_chr($range[0], true, $form), $temp_str);
                            $temp_str = str_replace('%end', self::describe_chr($range[1], true, $form), $temp_str);
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
        if ( count($this->pregnode->flags)==1
            && $this->pregnode->negative == true
            && $this->pregnode->flags[0][0]->negative === true) {

            $this->pregnode->negative = false;
            $this->pregnode->flags[0][0]->negative = false;
        }

        // filling $characters[]
        foreach ($this->pregnode->flags as $outer) {
            $this->flag_to_array($outer[0], $characters, $form);
        }

        if ( count($characters)==1
            && $this->pregnode->negative == false) {
            // adding resulting patterns
            // Simulation of:
            // $string['description_charset_one'] = '%characters';
            // w/o calling functions
            $result_pattern = $characters[0];
        } else {
            if (count($characters)==1 && $this->pregnode->negative == true) {
                $result_pattern = self::get_form_string('description_charset_one_neg', $form);
            } else if ($this->pregnode->negative == false) {
                $result_pattern = self::get_form_string('description_charset', $form);
            } else {
                $result_pattern = self::get_form_string('description_charset_negative', $form);
            }
            $result_pattern = str_replace('%characters', implode(", ", $characters), $result_pattern);

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
    public function pattern($node_parent=null, $form=null) {

        return self::get_form_string('description_empty', $form);
    }
}

/**
 * Defines simple assertions.
 */
class qtype_preg_description_leaf_assert extends qtype_preg_description_leaf {

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null, $form=null) {
        $resultpattern ='';
        switch ($this->pregnode->userinscription->data) {
            case '^' :
                $resultpattern = self::get_form_string('description_circumflex', $form);
                break;
            case '$' :
                $resultpattern = self::get_form_string('description_dollar', $form);
                break;
            case '\b' :
                $resultpattern = self::get_form_string('description_wordbreak', $form);
                break;
            case '\B' :
                $resultpattern = self::get_form_string('description_wordbreak_neg', $form);
                break;
            case '\A' :
                $resultpattern = self::get_form_string('description_esc_a', $form);
                break;
            case '\Z' :
                $resultpattern = self::get_form_string('description_esc_z', $form);
                break;
        }
        return $resultpattern;
    }
}

/**
 * Defines backreferences.
 */
class qtype_preg_description_leaf_backref extends qtype_preg_description_leaf {

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null, $form=null) {
        $resultpattern = self::get_form_string('description_backref', $form);
        $resultpattern = str_replace('%number', $this->pregnode->number, $resultpattern);
        return $resultpattern;
    }

}

class qtype_preg_description_leaf_options extends qtype_preg_description_leaf {

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null, $form=null) {
        $resultpattern = '';
        $posopt =& $this->pregnode->posopt;
        $negopt =& $this->pregnode->negopt;
        if ($posopt->length() > 0) {
            $this->handler->state->set_modifier($posopt[0], true);
            $resultpattern = self::get_form_string('description_option_'.$posopt[0], $form);
        } else if ($negopt->length() > 0) {
            $this->handler->state->set_modifier($negopt[0], false);
            $resultpattern = self::get_form_string('description_unsetoption_'.$negopt[0], $form);
        }
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
    public static function check_options($node, &$node_pattern, $form=null) {

        $resultpattern = '';
        $mcaseless =& $node->handler->state->caseless;
        $msingleline =& $node->handler->state->singleline;
        $mmultilineline =& $node->handler->state->multilineline;
        $mextended =& $node->handler->state->extended;
        $mungreedy =& $node->handler->state->ungreedy;
        $mduplicate =& $node->handler->state->duplicate;

        if ($node->pregnode->type === qtype_preg_node::TYPE_NODE_SUBEXPR) {

            $node->handler->state->forceunsetmodifiers = true;

        } else if ($node->handler->state->forceunsetmodifiers === true) { // any other leaf

            // TODO - generate 'caseless, singleline:' instead of 'caseless: singleline:'
            if ($mcaseless === true) {
                $resultpattern .= self::get_form_string('description_unsetoption_i', $form) . ' ';
                $mcaseless = false;
            }
            if ($msingleline === true) {
                $resultpattern .= self::get_form_string('description_unsetoption_s', $form) . ' ';
                $msingleline = false;
            }
            if ($mmultilineline === true) {
                $resultpattern .= self::get_form_string('description_unsetoption_m', $form) . ' ';
                $mmultilineline = false;
            }
            if ($mextended === true) {
                $resultpattern .= self::get_form_string('description_unsetoption_x', $form) . ' ';
                $mextended = false;
            }
            if ($mungreedy === true) {
                $resultpattern .= self::get_form_string('description_unsetoption_U', $form) . ' ';
                $mungreedy = false;
            }
            if ($mduplicate === true) {
                $resultpattern .= self::get_form_string('description_unsetoption_J', $form) . ' ';
                $mduplicate = false;
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
    public function pattern($node_parent=null, $form=null) {

        $resultpattern = '';
        if ($this->pregnode->number === 0) {
             $resultpattern = self::get_form_string('description_recursion_all', $form);
        } else {
             $resultpattern = self::get_form_string('description_recursion', $form);
             $resultpattern = str_replace('%number', $this->pregnode->number, $resultpattern);
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
    public function pattern($node_parent=null, $form=null) {
        $resultpattern = '';

        if ($this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_ACCEPT ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_FAIL ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_MARK_NAME ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_NO_START_OPT ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_UTF8 ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_UTF1 ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_UCP) {

            $resultpattern = self::get_form_string('description_'.$this->pregnode->subtype, $form);

        } else if ($this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_COMMIT ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_PRUNE ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_SKIP ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_THEN ) {

            $resultpattern = self::get_form_string('description_control_backtrack', $form);
            $resultpattern = str_replace('%what', self::get_form_string('description_'.$this->pregnode->subtype), $resultpattern, $form);

        } else if ($this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_SKIP_NAME) {

            $resultpattern = self::get_form_string('description_control_backtrack', $form);
            $resultpattern = str_replace('%what', self::get_form_string('description_'.$this->pregnode->subtype), $resultpattern, $form);
            $resultpattern = str_replace('%name', $this->pregnode->name, $resultpattern);

        } else if ($this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_CR ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_LF ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_CRLF ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_ANYCRLF ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_ANY) {

            $resultpattern = self::get_form_string('description_control_newline', $form);
            $resultpattern = str_replace('%what', self::get_form_string('description_'.$this->pregnode->subtype), $resultpattern, $form);

        } else {
            $resultpattern = self::get_form_string('description_control_r', $form);
            $resultpattern = str_replace('%what', self::get_form_string('description_'.$this->pregnode->subtype), $resultpattern, $form);
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
    public function __construct(&$node, &$matcher) {
        parent::__construct($node, $matcher);
        foreach ($this->pregnode->operands as $operand) {
            array_push($this->operands, $matcher->from_preg_node($operand));
        }
    }

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null, $form=null) {

        return 'seems like pattern() for '.get_class($this).' node didnt redefined';
    }

    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern, $node_parent=null, $form=null) {
        // var_dump(123);
        $description = '';
        $child_description = '';
        // $matches = array();
        // $i = 0;

        $this->pattern = $this->pattern($node_parent, $form);
        $description = $this->numbering_pattern($numbering_pattern, $this->pattern);

        /*$find = '/%(\w+)?(\d)/';
        while(preg_match($find, $description, $matches) !== 0) {
            $form = $matches[1];
            $i = (int)$matches[2];
            $child_description = $this->operands[$i-1]->description($numbering_pattern, $this, $form);
            $description = str_replace($matches[0], $child_description, $description);
        }*/
        $replaces = $this->what_to_replace($description);
        foreach ($replaces as $num => $data) {
            // var_dump($num);
            $child_description = $this->operands[$num-1]->description($numbering_pattern, $this, $data['form']);
            $description = str_replace($data['toreplace'], $child_description, $description);
        }
        qtype_preg_description_leaf_options::check_options($this, $description, $form);
        return $description;
    }

    protected function what_to_replace($str) {
        $pos = null;
        $form = null;
        $numstr = null;
        $num = null;
        $full = null;
        $result = array();
        $pos = strpos($str, '%');
        $len = strlen($str);
        $wasnum = false;
        while ($pos!==false) {
            // echo($pos);
            $pos++;
            $form = '';
            while ($len>$pos && ctype_alpha($str[$pos])) {
                $form += $str[$pos];
                $pos++;
            }
            $numstr = '';
            while ($len>$pos && ctype_digit($str[$pos])) {
                $numstr += $str[$pos];
                $pos++;
                $wasnum = true;
            }
            if ($wasnum) {
                $num = (int)$numstr;
                $full = '%'.$form.$numstr;
                $result[$num] = array('toreplace'=>$full, 'form'=>$form);
            }
            $pos = strpos($str, '%', $pos);
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
    public function pattern($node_parent=null, $form=null) {

        $resultpattern ='';
        $greedypattern='';
        $wrong_borders =$this->pregnode->leftborder > $this->pregnode->rightborder;

        if ($this->pregnode->leftborder===0 ) {
            if ($this->pregnode->rightborder ===1) {
                $resultpattern = self::get_form_string('description_finite_quant_01', $form);
                $resultpattern = str_replace('%rightborder', $this->pregnode->rightborder, $resultpattern);
            } else {
                $resultpattern = self::get_form_string('description_finite_quant_0', $form);
                $resultpattern = str_replace('%rightborder', $this->pregnode->rightborder, $resultpattern);
            }

        } else if ($this->pregnode->leftborder===1) {
            $resultpattern = self::get_form_string('description_finite_quant_1', $form);
            $resultpattern = str_replace('%rightborder', $this->pregnode->rightborder, $resultpattern);
        } else if ($this->pregnode->leftborder==$this->pregnode->rightborder) {
            $resultpattern = self::get_form_string('description_finite_quant_strict', $form);
            $resultpattern = str_replace('%count', $this->pregnode->rightborder, $resultpattern);
        } else {
            $resultpattern = self::get_form_string('description_finite_quant', $form);
            $resultpattern = str_replace('%rightborder', $this->pregnode->rightborder, $resultpattern);
            $resultpattern = str_replace('%leftborder', $this->pregnode->leftborder, $resultpattern);
        }

        if ($this->pregnode->lazy==true) {
            $greedypattern = self::get_form_string('description_quant_lazy', $form);
        } else if ($this->pregnode->greedy==true) {
            $greedypattern = self::get_form_string('description_quant_greedy', $form);
        } else if ($this->pregnode->possessive==true) {
            $greedypattern = self::get_form_string('description_quant_possessive', $form);
        }
        $resultpattern = str_replace('%greedy', $greedypattern, $resultpattern);

        if ($wrong_borders) {
            $resultpattern = preg_replace('/%(\w+)?1/', ('%$ {1}1'.self::get_form_string('description_errorbefore', $form)), $resultpattern);
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
    public function pattern($node_parent=null, $form=null) {

        $resultpattern ='';
        $greedypattern='';
        if ($this->pregnode->leftborder===0) {
            $resultpattern = self::get_form_string('description_infinite_quant_0', $form);
        } else if ($this->pregnode->leftborder===1) {
            $resultpattern = self::get_form_string('description_infinite_quant_1', $form);
        } else {
            $resultpattern = self::get_form_string('description_infinite_quant', $form);
            $resultpattern = str_replace('%leftborder', $this->pregnode->leftborder, $resultpattern);
        }

        if ($this->pregnode->lazy==true) {
            $greedypattern = self::get_form_string('description_quant_lazy', $form);
        } else if ($this->pregnode->greedy==true) {
            $greedypattern = self::get_form_string('description_quant_greedy', $form);
        } else if ($this->pregnode->possessive==true) {
            $greedypattern = self::get_form_string('description_quant_possessive', $form);
        }

        $resultpattern = str_replace('%greedy', $greedypattern, $resultpattern);
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
    public function description($numbering_pattern, $node_parent=null, $form=null) {

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

        for ($i=$childs_count-2; $i>=0; $i--) {
            $left = $this->operands[$i];
            $right = $this->operands[$i+1];

            // getting pattern
            $type1 = $left->pregnode->type;
            $type2 = $right->pregnode->type;
            $subtype1 = $left->pregnode->subtype;
            $subtype2 = $right->pregnode->subtype;

            $needshortpattern = $type1===qtype_preg_node::TYPE_LEAF_CHARSET &&
                    $left->is_one_char() &&
                    $type2===qtype_preg_node::TYPE_LEAF_CHARSET &&
                    $right->is_one_char();
            $needcontiuneshortpattern = $type2===qtype_preg_node::TYPE_LEAF_CHARSET &&
                    $right->is_one_char() &&
                    $type1===qtype_preg_node::TYPE_NODE_CONCAT &&
                    $left->operands[1]->pregnode->type===qtype_preg_node::TYPE_LEAF_CHARSET &&
                    $left->operands[1]->is_one_char();
            $firstaheadassert = $subtype1===qtype_preg_node_assert::SUBTYPE_PLA || $subtype1===qtype_preg_node_assert::SUBTYPE_NLA;
            $secondbehindassert = $subtype2===qtype_preg_node_assert::SUBTYPE_PLB || $subtype2===qtype_preg_node_assert::SUBTYPE_NLB;
            $aheadassertinprevconcat = $type1===qtype_preg_node::TYPE_NODE_CONCAT &&
                    ($left->operands[1]->pregnode->subtype===qtype_preg_node_assert::SUBTYPE_PLA ||
                    $left->operands[1]->pregnode->subtype===qtype_preg_node_assert::SUBTYPE_NLA);
            $neddspacepattern = $type1===qtype_preg_node::TYPE_LEAF_OPTIONS ||
                    ($type1===qtype_preg_node::TYPE_NODE_CONCAT &&
                    $left->operands[1]->pregnode->type===qtype_preg_node::TYPE_LEAF_OPTIONS);
            if ($neddspacepattern) {
                $description = self::get_form_string('description_concat_space', $form);
            } else if ($needshortpattern || $needcontiuneshortpattern) {
                $description = self::get_form_string('description_concat_short', $form);
            } else if ($firstaheadassert || $secondbehindassert || $aheadassertinprevconcat) {
                $description = self::get_form_string('description_concat_and', $form);
            } else if ($type1 === qtype_preg_node::TYPE_NODE_CONCAT) {
                $description = self::get_form_string('description_concat_wcomma', $form);
            } else {
                $description = self::get_form_string('description_concat', $form);
            }

            // setuping description
            $replace = $this->what_to_replace($description);
            if ($prevdescription===null) {
                $prevdescription = $right->description($numbering_pattern, $this, $replace[2]['form']);
            }
            $description = str_replace($replace[2]['toreplace'], $prevdescription, $description);
            $child_description = $left->description($numbering_pattern, $this, $replace[1]['form']);
            $description = str_replace($replace[1]['toreplace'], $child_description, $description);
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

    public function description($numbering_pattern, $node_parent=null, $form=null) {

        $description = '';
        $childs_count = count($this->operands);
        $left = null;
        $right = null;
        $prevdescription = null;

        for ($i=$childs_count-2; $i>=0; $i--) {
            $left = $this->operands[$i];
            $right = $this->operands[$i+1];

            // getting pattern
            if ($i!==0) {
                $description = self::get_form_string('description_alt_wcomma', $form);
            } else {
                $description = self::get_form_string('description_alt', $form);
            }

            // setuping description
            $replace = $this->what_to_replace($description);
            if ($prevdescription===null) {
                $prevdescription = $right->description($numbering_pattern, $this, $replace[2]['form']);
            }
            $description = str_replace($replace[2]['toreplace'], $prevdescription, $description);
            $child_description = $left->description($numbering_pattern, $this, $replace[1]['form']);
            $description = str_replace($replace[1]['toreplace'], $child_description, $description);
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
    public function pattern($node_parent=null, $form=null) {

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
    public function pattern($node_parent=null, $form=null) {

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
            $resultpattern = str_replace('%name', $this->pregnode->number, $resultpattern);

        } else {

            $resultpattern = '';
            if ($this->pregnode->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR) {
                $resultpattern = self::get_form_string('description_subexpression', $form);
            } else {
                $resultpattern = self::get_form_string('description_subexpression_once', $form);
            }
            $resultpattern = str_replace('%number', $this->pregnode->number, $resultpattern);

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
    public function __construct(&$node, &$matcher) {
        parent::__construct($node, $matcher);
        $this->condbranch = $matcher->from_preg_node($node->condbranch);
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
    public function pattern($node_parent=null, $form=null) {

        $resultpattern = '';
        if ($this->pregnode->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR) {

            if (is_string($this->pregnode->number)) {
                $resultpattern = self::get_form_string('description_backref_node_cond_subexpr_name', $form);
                $resultpattern = str_replace('%name', $this->pregnode->number, $resultpattern);
            } else {
                $resultpattern = self::get_form_string('description_backref_node_cond_subexpr', $form);
                $resultpattern = str_replace('%number', $this->pregnode->number, $resultpattern);
            }

        } else if ($this->pregnode->subtype===qtype_preg_node_cond_subexpr::SUBTYPE_RECURSION) {

            if (is_string($this->pregnode->number)) {
                $resultpattern = self::get_form_string('description_recursive_node_cond_subexpr_name', $form);
                $resultpattern = str_replace('%name', $this->pregnode->number, $resultpattern);
            } else if ($this->pregnode->number===0) {
                $resultpattern = self::get_form_string('description_recursive_node_cond_subexpr_all', $form);
            } else {
                $resultpattern = self::get_form_string('description_recursive_node_cond_subexpr', $form);
                $resultpattern = str_replace('%number', $this->pregnode->number, $resultpattern);
            }

        } else if ($this->pregnode->subtype===qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE) {

            $resultpattern = self::get_form_string('description_define_node_cond_subexpr', $form);

        } else {
            $resultpattern = self::get_form_string('description_node_cond_subexpr', $form);
            // $resultpattern = str_replace('%cond', '%'.count($this->pregnode->operands), $resultpattern);
        }

        $elsereplase = isset($this->pregnode->operands[1])?self::get_form_string('description_node_cond_subexpr_else', $form):'';
        $resultpattern = str_replace('%else', $elsereplase, $resultpattern);
        return $resultpattern;
    }

    public function description($numbering_pattern, $node_parent=null, $form=null) {
        $resultpattern = parent::description($numbering_pattern, $this, $form);
        if (strpos($resultpattern, '%cond')!==false) {
            $conddescription = $this->condbranch->description($numbering_pattern, $this, $form);
            $resultpattern = str_replace('%cond', $conddescription , $resultpattern);
        }
        return $resultpattern;
    }
}

class qtype_preg_description_node_error extends qtype_preg_description_operator {

    public function pattern($node_parent=null, $form=null) {

        $resultpattern = self::get_form_string('description_errorbefore', null)
            .$this->pregnode->error_string()
            .self::get_form_string('description_errorafter', null);

        $operandplaces = array();
        foreach ($this->pregnode->operands as $i => $operand) {
            if (isset($operand)) {
                $operandplaces[] = '%'.($i+1);
            }
        }
        if (count($operandplaces)!=0) {
            $resultpattern .= ' Operands: '.implode(', ', $operandplaces);
        }

        return $resultpattern;
    }
}
