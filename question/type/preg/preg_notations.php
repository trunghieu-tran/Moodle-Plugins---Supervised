<?php
/**
 * Defines classes of notations, used to write regexes
 *
 * @copyright &copy; 2010  Oleg Sychev
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/preg/preg_exception.php');

/**
 * Abstract notation class
 */
abstract class qtype_preg_notation {

    //Regular expression in this notation
    public $regex;
    //Regular expression modifiers in this notation
    public $modifiers;

    /**
    * Constructs notation object, should suit most notations
    */
    public function __construct($regex, $modifiers = '') {
        $this->regex = $regex;
        $this->modifiers = $modifiers;
    }

    /**
    * Return notation name
    */
    abstract public function name();

    /**
    * Returns regular expression in desired notation, should be overloaded
    */
    public function convert_regex($targetnotation) {
        throw new qtype_preg_exception('Sorry, no conversion from '.$this->name().' to '.$targetnotation.' implemented yet.');
    }
    /**
    * Returns regular expression modifiers in desired notation, should suit most notation
    * When overloading this, you probably would want to add some modifers based on regular expression
    */
    public function convert_modifiers($targetnotation) {
        return $this->modifiers;
    }
}

/**
 * Native notation, supported by internal regular expression parser and used by any regular expression handlers that using this parser.
 * You would usually convert other regexes to it with notable exception of preg_php_extension engine, that wants PCRE strict notation.
 */
class qtype_preg_notation_native extends qtype_preg_notation {

    public function name() {
        return 'native';
    }

    //TODO - implement converting from native to PCRE strict notation
}

/**
 * Moodle shortanswer notation is basically a string to match with '*' wildcard for any number of any characters
 * Easily converts to both native and PCRE strict notations
 */
class qtype_preg_notation_mdlshortanswer extends qtype_preg_notation {

    public function name() {
        return 'mdlshortanswer';
    }

    public function convert_regex($targetnotation) {

        if ($targetnotation == 'native' || $targetnotation == 'PCRE') {
            //Code from qtype_shortanswer_question::compare_string_with_wildcard with proper respect for Tim Hunt

            // Break the string on non-escaped asterisks.
            $bits = preg_split('/(?<!\\\\)\*/', $this->regex);
            // Escape regexp special characters in the bits.
            $excapedbits = array();
            foreach ($bits as $bit) {
                $excapedbits[] = preg_quote(str_replace('\*', '*', $bit));
            }
            // Put it back together to make the regexp.
            return implode('.*', $excapedbits);
        }
        parent::convert_regex($targetnotation);
    }
}
 ?>