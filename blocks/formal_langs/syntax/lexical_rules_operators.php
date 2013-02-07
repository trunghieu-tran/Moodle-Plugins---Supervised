<?
/**
 * Describes an rules and operators for building lexical patterns
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Mamontov Dmitriy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
global $CFG;
require_once($CFG->dirroot.'/lib/textlib.class.php');
/**
 *  Determines a type enumeration for matching rules of lexer
 */
class  block_formal_langs_lexical_matching_rule_type {
    /**
     * Determines a special zero-width epsilon rule
     * @var int
     */
    public static $EPSILON = 0;
    /**
     * A special kind of trule, which matches all symbols
     * @var int
     */
    public static $ALLMATCH = 1;
    /**
     * A type of rule, which match any symbol from set
     * @var int
     */
    public static $MATCHANYONEFROMSET = 2;
    /**
     * A type of rule, which match any symbol not from set
     * @var int
     */
    public static $MATCHANYONENOTFROMSET = 3;
    /**
     * A set, which matches any symbol
     * @var int
     */
    public static $EOF_SYMBOL = 4;
};

/**
 *  A common lexical matching rule, which handles most of character style types
 */
class block_formal_langs_lexical_matching_rule  {
    /**
     *  Determines a type of matching rule
     *  @var int as value from block_formal_langs_lexical_matching_rule_type
     */
    protected $type;
    /**
     *  Determines a character set for matching
     *  @var array as set of characters
     */
    protected $characterset;
    /**
     *  Constructs new rule. 
     *  @param int $type type of rule 
     *  @param array $set set of characters to be matched with
     */
    public function __construct($type, $set = array()) {
        $this->type = $type;
        $this->characterset = $set;
    }
    /**
     *  Creates new epsilon matching rule
     *  @return block_formal_langs_lexical_matching_rule
     */
    public static function epsilon_rule() {
        return new block_formal_langs_lexical_matching_rule(
                   block_formal_langs_lexical_matching_rule_type::$EPSILON
               );
    }
    /**
     *  Creates new rule, which matches all of symbols
     *  @return block_formal_langs_lexical_matching_rule
     */
    public static function all_matching_rule() {
        return new block_formal_langs_lexical_matching_rule(
                   block_formal_langs_lexical_matching_rule_type::$ALLMATCH
               );    
    }
    /**
     *  Creates new rule, which matches only one symbol
     *  @param string $character matching chatacter
     *  @return block_formal_langs_lexical_matching_rule
     */
    public static function simple_rule($character) {
        return new block_formal_langs_lexical_matching_rule(
                   block_formal_langs_lexical_matching_rule_type::$MATCHANYONEFROMSET,
                   array( $character )
               );      
    }

    /**
     * A rule, which only eof matches to
     * @return block_formal_langs_lexical_matching_rule
     */
    public static function eof_rule() {
        return self::simple_rule(block_formal_langs_lexical_matching_rule_type::$EOF_SYMBOL);
    }
    /**
     *  Creates new character class
     *  @param array $set matching character set
     *  @return block_formal_langs_lexical_matching_rule
     */
    public static function charclass_rule($set) {
        return new block_formal_langs_lexical_matching_rule(
                   block_formal_langs_lexical_matching_rule_type::$MATCHANYONEFROMSET,
                   array( $set )
               );      
    }
    
    /**
     *  Creates new negative character class
     *  @param array $set matching character set
     *  @return block_formal_langs_lexical_matching_rule
     */
    public static function neg_charclass_rule($set) {
        return new block_formal_langs_lexical_matching_rule(
                   block_formal_langs_lexical_matching_rule_type::$MATCHANYONENOTFROMSET,
                   array( $set )
               );      
    }
    /**
     * Returns a label for matching rule
     * @return string label for drawing a rule
     */
    public function label() {
        $result = '.';
        $denormalizedcset = '[]';
        if (count($this->characterset)) {
            $denormalizedcset =  '[' . implode('', $this->characterset) .']';  
        }
        if ($this->type == block_formal_langs_lexical_matching_rule_type::$EPSILON) {
            $result = 'eps';
        }
        if ($this->type == block_formal_langs_lexical_matching_rule_type::$MATCHANYONEFROMSET) {
            $result = $denormalizedcset;
        }
        if ($this->type == block_formal_langs_lexical_matching_rule_type::$MATCHANYONENOTFROMSET) {
            /** @noinspection PhpUndefinedClassInspection */
            $result = '[^' . textlib::substr($denormalizedcset, 1);
        }        
        return $result;
    }
    /**
     *  Determines, whether rule doesn't consume character
     *  @return boolean
     */
    public function is_zero_width() {
        return $this->type == block_formal_langs_lexical_matching_rule_type::$EPSILON;
    }
    /**
     * Determines, whether character matches rule
     * @param string $character character, that is tested against rule
     * @return boolean whether it matched rule
     */
    public function match($character) {
        $result = true;
        if ($this->type == block_formal_langs_lexical_matching_rule_type::$MATCHANYONEFROMSET) {
            $result = in_array($character, $this->characterset);
        }
        if ($this->type == block_formal_langs_lexical_matching_rule_type::$MATCHANYONENOTFROMSET) {
            $result = !in_array($character, $this->characterset);
        }
        return $result;
    }
    /**
     * Call methods if one of rules is of specified type
     * @param block_formal_langs_lexical_matching_rule $other other node
     * @param int $type type of rule
     * @param string $method method, used in speified type
     * @param mixed $result result of executed method
     * @param boolean $handled was it handled before or after
     */     
    protected function call_method_if_one_of_spec_type($other, $type, $method, &$result, &$handled) {
        // If method was not handled before
        if ($handled == false) {
            if ($this->type == $type) {
                $handled = true;
                $result = $this->$method($other, array( 0 => 0, 1 => 1));
            } else {
                if ($other->type == $type) {
                    $handled = true;
                    $result = $other->$method($this, array( 0 => 1, 1 => 0));
                }
            }
        }
    }

    /**
     * Intersects epsilon type rule with other rule
     * @param block_formal_langs_lexical_matching_rule $other node, which is tested
     * @param array $mapping mapping of source states to relative states
     * @return array   <rule, array( index of states, can be [0,1], 0, 1)>
     */
    protected function intersect_epsilon($other, $mapping) {
       return array( array($this, array( $mapping[0] )),
                     array($other, array( $mapping[1] ))
                   );
    }
    /**
     * Intersects epsilon type rule with other rule
     * @param block_formal_langs_lexical_matching_rule $other node, which is tested
     * @param array $mapping mapping of source states to relative states
     * @return array   <rule, array( index of states, can be [0,1], 0, 1)>
     */
    protected function intersect_allmatch($other, $mapping) {
        $result = array();
        if ($other->type == block_formal_langs_lexical_matching_rule_type::$MATCHANYONEFROMSET) {
            $result = array( array( self::charclass_rule($other->characterset), array($mapping[0], $mapping[1]) ),
                             array( self::neg_charclass_rule($other->characterset), array($mapping[0]))
                           );
        }
        if ($other->type == block_formal_langs_lexical_matching_rule_type::$MATCHANYONENOTFROMSET) {
            $result = array( array( self::charclass_rule($other->characterset), array($mapping[0]) ),
                             array( self::neg_charclass_rule($other->characterset), array($mapping[0], $mapping[1]))
                      );

        }
        return $result;
    }

    /**
     * Performs safe array diff for set stuff
     * @param array $a1
     * @param array $a2
     * @return array
     */
    protected static function diff($a1 , $a2) {
        if (count($a1) == 0) {
            return $a2;
        }
        if (count($a2) == 0) {
            return $a1;
        }
        return array_diff($a1, $a2);
    }

    /**
     * Performs safe array intersect for set stuff
     * @param array $a1
     * @param array $a2
     * @return array
     */
    protected static function aintersect($a1, $a2) {
        if (count($a1) == 0 || count($a2) == 0) {
            return array();
        }
        return array_intersect($a1, $a2);
    }

    /**
     * Performs safe array union for set stuff
     * @param array $a1
     * @param array $a2
     * @return array
     */
    protected static function union($a1, $a2) {
        if (count($a1) == 0) {
            return $a2;
        }
        if (count($a2) == 0) {
            return $a1;
        }
        return array_unique(array_merge($a1, $a2));
    }

    /**
     * Inserts new created rule, if set is not empty
     * @param array $result result, where set will be pushed
     * @param string $method ruletype
     * @param array $set set which will be checked for emptiness
     * @param array $states
     */
    protected static function insert_rule(&$result, $method, $set, $states) {
        if (count($set) != 0) {
            $methodname = $method . '_rule';
            $result[] = array( self::$methodname($set) , $states );
        }
    }
    /**
     * Intersects a character class type rule with other rule
     * @param block_formal_langs_lexical_matching_rule $other node, which is tested
     * @param array $mapping mapping of source states to relative states
     * @return array   <rule, array( index of states, can be [0,1], 0, 1)>
     */
    protected function intersect_charclass($other, $mapping) {
        $result = array();
        $froute = array($mapping[0]);
        $broute = array($mapping[0], $mapping[1]);
        $sroute = array($mapping[1]);
        if ($other->type == block_formal_langs_lexical_matching_rule_type::$MATCHANYONEFROMSET) {
            $common = self::aintersect($this->characterset, $other->characterset);
            $fst = self::diff($this->characterset, $common);
            $snd = self::diff($other->characterset, $common);

            self::insert_rule($result, 'charclass', $fst, $froute);
            self::insert_rule($result, 'charclass', $common, $broute);
            self::insert_rule($result, 'charclass', $snd, $sroute);

        }
        if ($other->type == block_formal_langs_lexical_matching_rule_type::$MATCHANYONENOTFROMSET) {
            $fst = self::aintersect($this->characterset, $other->characterset);
            $both = self::diff($this->characterset, $fst);
            $snd = self::union($this->characterset, $other->characterset);

            self::insert_rule($result, 'charclass', $fst, $froute);
            self::insert_rule($result, 'charclass', $both, $broute);
            self::insert_rule($result, 'neg_charclass', $snd, $sroute);
        }
        return $result;
    }
    /**
     * Intersects a negative character class type rule with other rule, which
     * is negative character class too, because all other cases are handled already
     * @param block_formal_langs_lexical_matching_rule $other node, which is tested
     * @param array $mapping mapping of source states to relative states
     * @return array   <rule, array( index of states, can be [0,1], 0, 1)>
     */
    protected function intersect_neg_charclass($other, $mapping) {
        $froute = array($mapping[0]);
        $broute = array($mapping[0], $mapping[1]);
        $sroute = array($mapping[1]);
        $result = array();
        $both = self::union($this->characterset, $other->characterset);
        $intersect = self::aintersect($this->characterset, $other->characterset);
        $fst = self::diff($other->characterset, $intersect);
        $snd = self::diff($this->characterset, $intersect);
        self::insert_rule($result, 'charclass', $fst, $froute);
        self::insert_rule($result, 'neg_charclass', $both, $broute);
        self::insert_rule($result, 'charclass', $snd, $sroute);
    }
    /**
     * And now function, which is reason, why this is one class, that uses a type 
     * and character set to split or union 
     * two edges which can intersect by character set to make one edge for a symbol
     * @param block_formal_langs_lexical_matching_rule $other node, which is tested
     * @return array   <rule, array( index of states, can be [0,1], 0, 1)>
     * @throws Exception if intersection where not properlt handled
     */
     public function intersect($other) {
        $result = array(); 
        if ($this->type == $other->type
            && ($this->type == block_formal_langs_lexical_matching_rule_type::$EPSILON
            ||  $this->type == block_formal_langs_lexical_matching_rule_type::$ALLMATCH)) {
            $result = array( array( clone $other, array(0, 1) ) );
        } else {
            $handled = false;
            $methods = array(
                'EPSILON' => 'intersect_epsilon',
                'ALLMATCH' => 'intersect_allmatch',
                'MATCHANYONEFROMSET' => 'intersect_charclass',
                'MATCHANYONENOTFROMSET' => 'intersect_neg_charclass'
            );
            foreach($methods as $typestring => $method) {
                $type = block_formal_langs_lexical_matching_rule_type::$$typestring;
                $this->call_method_if_one_of_spec_type($other, $type, $method, $result, $handled);
            }
        }
        if (count($result) == 0) {
            throw new Exception('Rule type was not handled!');
        }
        return $result;
     }
};

