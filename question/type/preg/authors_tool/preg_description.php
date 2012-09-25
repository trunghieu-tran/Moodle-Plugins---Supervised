<?php
/**
 * Defines handler for generating description of reg exp
 * Also defines specific tree, containing methods for generating descriptions of current node
 *
 * @copyright &copy; 2012 Pahomov Dmitry
 * @author Pahomov Dmitry
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
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
     * set flag that means modifier $modifier is set
     * 
     * @param string $modifier modifier to set
     */
    public function set_modifier($modifier) {
        switch ($modifier){
            case 'i':
                $this->caseless = true;
                break;
            case 's':
                $this->singleline = true;
                break;
            case 'm':
                $this->multilineline = true;
                break;
            case 'x':
                $this->extended = true;
                break;
            case 'U':
                $this->ungreedy = true;
                break;
            case 'J':
                $this->duplicate = true;
                break;
        }
    }
    
    /**
     * set flag that means modifier $modifier is unset
     * 
     * @param string $modifier modifier to unset
     */
    public function unset_modifier($modifier) {
        switch ($modifier){
            case 'i':
                $this->caseless = false;
                break;
            case 's':
                $this->singleline = false;
                break;
            case 'm':
                $this->multilineline = false;
                break;
            case 'x':
                $this->extended = false;
                break;
            case 'U':
                $this->ungreedy = false;
                break;
            case 'J':
                $this->duplicate = false;
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
class qtype_preg_author_tool_description extends qtype_preg_regex_handler {
    
    /** @var qtype_preg_description_options options for description and state of description */
    public $options;
    
    /** @var qtype_preg_description_state state of description generating */
    public $state;

    /*
     * Construct of parent class parses the regex and does all necessary preprocessing.
     *
     * @param string $regex - regular expression to handle.
     * @param string $modifiers - modifiers of the regular expression.
     * @param object $options - options to handle regex, i.e. any necessary additional parameters.
     */
    public function __construct($regex = null, $modifiers = null, $options = null){
        if($options === null) {
            $options = new qtype_preg_description_options();
        }
        parent::__construct($regex, $modifiers, $options);
        $this->options = $options;
        $this->state = new qtype_preg_description_state();
    }

    /**
     * Genegates description of regexp
     * Example of calling:
     * 
     * description('<span class="description_node_%n">%s</span>','<span class="description">%s</span>');
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
    public function description($numbering_pattern,$wholepattern=null,$charsetuserinscr=false,$rangelengthmax=5){
        
        // set up options
        $this->state->reset();// restore default state
        $backupoptions = $this->options;// save original options
        $this->options->charsetuserinscription  = (bool)$charsetuserinscr;
        $this->options->rangelengthmax          = (int)$rangelengthmax;
        // make description
        if(isset($this->dst_root)){
            $string = $this->dst_root->description($numbering_pattern,null,null);
            $string = self::postprocessing($string);
        }
        else {
           $string = 'tree was not built'; 
        }
        // put string into $wholepattern
        if($wholepattern !== null && $wholepattern !== ''){
            $string = str_replace('%s',$string,$wholepattern);
        }
        $this->options = $backupoptions; // restore original options
        return $string;
    }
    
    private static function postprocessing($s){

        $result = preg_replace('%;((?:</span>)?)]%','\1]',$s);
        return $result;
    }
    
    /**
     * Calling default description() with default params
     */
    public function default_description(){
       
        return $this->description('<span class="description_node_%n">%s</span>');
    }
    
    /**
     * for testing
     */
    public function form_description($form){
        $result = $this->dst_root->description('%s',null,$form);
        return $result;      
    }
    
    /*public function &get_description_options(){
        return $this->descriptionoptions;
    }*/
    
    /**
     * Returns the engine-specific node name for the given preg_node name.
     * Overload in case of sophisticated node name schemes.
     */
    protected function node_infix() {
        return 'description';
    }
    
    /**
     * Is a preg_node_... or a preg_leaf_... supported by the engine?
     * Returns true if node is supported or user interface string describing
     *   what properties of node isn't supported.
     */
    protected function is_preg_node_acceptable($pregnode) {
        return true;
    }
    
}


/**
 * Generic node class.
 */
abstract class qtype_preg_description_node{
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
    abstract public function pattern($node_parent=null,$form=null);
    
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
    abstract public function description($numbering_pattern,$node_parent=null,$form=null);
    
    /**
     * gets localized string, if required a form it gets localized string for required form
     * 
     * @param string $s same as in get_string
     * @param string $form Required form.
     */
    public static function get_form_string($s,$form=null){
        
        if(isset($form) && $form !== ''){
            $s.='_'.$form;
        }
        return get_string($s,'qtype_preg');
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
    protected function numbering_pattern($numbering_pattern,$s) {       
        return str_replace('%s',$s,str_replace('%n',$this->pregnode->id,$numbering_pattern));
    }
}

/**
 * Generic leaf class.
 */
abstract class qtype_preg_description_leaf extends qtype_preg_description_node{
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        
        return 'seems like pattern() for '.get_class($this).' node didnt redefined';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,$node_parent=null,$form=null){
        
        $description ='';
        $this->pattern = $this->pattern($node_parent,$form);
        //var_dump($this->pattern);
        $description = $this->numbering_pattern($numbering_pattern,$this->pattern);
        qtype_preg_description_leaf_options::check_options($this,$description,$form);
        return $description;
    }
}

/**
 * Represents a character or a charcter set.
 */
class qtype_preg_description_leaf_charset extends qtype_preg_description_leaf{

    const FIRST_CHAR    = 0;
    const INTO_RANGE    = 1;
    const OUT_OF_RANGE  = 2;

    /**
     * Checks if charset contains only one printing character
     */
    public function is_one_char(){
        $flag = $this->pregnode->flags[0][0];
        return count($this->pregnode->flags)===1 
            && $flag->type===qtype_preg_charset_flag::SET
            && $flag->data->length()===1 
            && self::is_chr_printable(qtype_poasquestion_string::ord($flag->data[0]));
    }

    /**
     * Gets unicode char from code $code
     * 
     * @param int $code decimal code of character
     * @return string utf8 character;
     */
    public static function uchr($code) { 
        if ($code < 128) { 
            $utf = chr($code); 
        } else if ($code < 2048) { 
            $utf = chr(192 + (($code - ($code % 64)) / 64)); 
            $utf .= chr(128 + ($code % 64)); 
        } else { 
            $utf = chr(224 + (($code - ($code % 4096)) / 4096)); 
            $utf .= chr(128 + ((($code % 4096) - ($code % 64)) / 64)); 
            $utf .= chr(128 + ($code % 64)); 
        } 
        return $utf;
    }

    /**
     * Checks if a character is printable
     * 
     * @param $utf8chr character (from qtype_poasquestion_string) for check
     */
    public static function is_chr_printable($code){
        //var_dump($code);
        return qtype_preg_unicode::search_number_binary($code,qtype_preg_unicode::C_ranges())===false &&
               qtype_preg_unicode::search_number_binary($code,qtype_preg_unicode::Z_ranges())===false;
    }

    /*
     * Returns description of $utf8chr if it is non-printing character, otherwise returns null
     * 
     * @param int $code character code
     * @return string|null description of character (if character is non printable) or null.
     */
    public static function describe_nonprinting($code,$form=null){
        // null returns if description is not needed
        if ($code === null || self::is_chr_printable($code)) {
            return null;
        }
        // ok, character is non-printing, lets find its description in the language file
        $result = '';
        $hexcode = strtoupper(dechex($code));
        if($code<=32||$code==127||$code==160||$code==173
            ||$code==8194||$code==8195||$code==8201||$code==8204||$code==8205){
            $result = self::get_form_string('description_char'.$hexcode,$form);
        } else {
            $result = str_replace('%code',$hexcode,
                                  self::get_form_string('description_char_16value' ,$form)); 
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
    public static function describe_chr($utf8chr,$escapehtml=true,$form=null){
        $iscode = is_int($utf8chr);
        $code = $iscode ? $utf8chr : qtype_poasquestion_string::ord($utf8chr);
        $result = self::describe_nonprinting($code);
        if($result===null){
            //   &        >       <       "       '
            // &#38;    &#62;   &#60;   &#34;   &#39;
            if($escapehtml && ($code==34||$code==38||$code==39||$code==60||$code==62)){
                $result = '&#'.$code.';';
            } else {
                $result = $iscode ? self::uchr($utf8chr) : $utf8chr;
            }
            $result = str_replace('%char',$result,self::get_form_string('description_char' ,$form));
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
    public static function find_ranges($str){
        $lenth = $str->length();
        if(!($str instanceof qtype_poasquestion_string) && $lenth < 1)
            return false;
        $result = array();
        $rangestart = 0;
        $prevcode = -1;
        $state = self::FIRST_CHAR;
        $curcode = -1;
        for($i=0;$i<$lenth;$i++){
            // if-else magic 8-)
            $curcode = qtype_poasquestion_string::ord($str[$i]);
            if ($state==self::FIRST_CHAR) {
                $state = self::OUT_OF_RANGE;
            } else if ($state == self::INTO_RANGE) {
                if($curcode-1 != $prevcode){
                    $state = self::OUT_OF_RANGE;
                    $result[] = array($rangestart,$prevcode);
                } 
            } else if ($state == self::OUT_OF_RANGE) {
                if($curcode-1 == $prevcode){
                    $state = self::INTO_RANGE;
                    $rangestart = $prevcode;
                } else {
                    $result[] = $prevcode;
                }
            }
            $prevcode = $curcode;
        }
        if($state == self::INTO_RANGE){
            $result[] = array($rangestart,$prevcode);
        } else { // hence $state == OUT_OF_RANGE
            $result[] = $prevcode;
        }
        return $result;
    }

    /**
     * Convertes charset flag to array of descriptions(strings)
     * 
     * @param qtype_preg_charset_flag $flag flag gor description
     * @param string[] $characters enumeration of descriptions in charset (updated parameter)
     * @param string $form required form
     */
    private function flag_to_array($flag,&$characters,$form=null) {

        $temp_str = '';
        $ranges = NULL;
        $rangelength = NULL;
        $rangelengthmax = NULL;

        if ($flag->type === qtype_preg_charset_flag::FLAG || $flag->type === qtype_preg_charset_flag::UPROP) {
            // current flag is something like \w or \pL    
            if($flag->negative == true){
                // using charset pattern 'description_charset_one_neg' because char pattern 'description_char_neg' has a <span> tag, 
                // but dont need to highlight this
                $temp_str = self::get_form_string('description_charflag_'.$flag->data,$form);
                $characters[] = str_replace('%characters',$temp_str,self::get_form_string('description_charset_one_neg' ,$form));
            }
            else{
                $characters[] = self::get_form_string('description_charflag_'.$flag->data,$form);
            }

        } else if ($flag->type === qtype_preg_charset_flag::SET) {
            // flag is a simple enumeration of characters
            if($flag->data->length()==1){
                $characters[] = self::describe_chr($flag->data[0],true,$form);
            } else {
                $ranges = self::find_ranges($flag->data);
                //var_dump($ranges);
                $rangelengthmax =& $this->handler->options->rangelengthmax;
                foreach($ranges as $range){
                    if(is_int($range)){ // $range is a code of character
                        $characters[] = self::describe_chr($range,true,$form);
                    } else { // $range is a range (from A to Z <=> array(65,90) )
                        $rangelength = $range[1]-$range[0];
                        if ($rangelength<$rangelengthmax) { // if length of range less than $rangelengthmax it will be displayed as enumeration
                            for($i=$range[0];$i<=$range[1];$i++){
                                $characters[] = self::describe_chr($i,true,$form);
                            }
                        } else { // otherwise it will be displayed
                            $temp_str = self::get_form_string('description_charset_range' ,$form);
                            $temp_str = str_replace('%start',self::describe_chr($range[0],true,$form),$temp_str);
                            $temp_str = str_replace('%end',self::describe_chr($range[1],true,$form),$temp_str);
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
    public function pattern($node_parent=null,$form=null){

        $result_pattern = '';
        $characters = array();
        // check errors
        if($this->pregnode->error !== NULL){
            return $this->pregnode->error[0]->error_string();
        }
        
        // 'not not' fix
        if( count($this->pregnode->flags)==1 
            && $this->pregnode->negative == true
            && $this->pregnode->flags[0][0]->negative === true) {

            $this->pregnode->negative = false;
            $this->pregnode->flags[0][0]->negative = false;
        }

        // filling $characters[]
        foreach ($this->pregnode->flags as $outer) {
            $this->flag_to_array($outer[0],$characters,$form);
        }

        if( count($characters)==1 
            && $this->pregnode->negative == false) {
            // adding resulting patterns
            // Simulation of: 
            // $string['description_charset_one'] = '%characters'; 
            // w/o calling functions
            $result_pattern = $characters[0];
        }
        else{
            if(count($characters)==1 && $this->pregnode->negative == true){
                $result_pattern = self::get_form_string('description_charset_one_neg',$form);
            }else if($this->pregnode->negative == false){
                $result_pattern = self::get_form_string('description_charset',$form);
            }
            else{
                $result_pattern = self::get_form_string('description_charset_negative',$form);
            }
            $result_pattern = str_replace('%characters', implode(", ", $characters),$result_pattern);

        }
        return $result_pattern;
    }

}


/**
 * Defines meta-characters that can't be enumerated.
 */
class qtype_preg_description_leaf_meta extends qtype_preg_description_leaf{

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){

        return self::get_form_string('description_empty',$form);
    }

}

/**
 * Defines simple assertions.
 */
class qtype_preg_description_leaf_assert extends qtype_preg_description_leaf{

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        $resultpattern ='';
        switch ($this->pregnode->userinscription->data) {
            case '^' :
                $resultpattern = self::get_form_string('description_circumflex',$form);
                break;
            case '$' :
                $resultpattern = self::get_form_string('description_dollar',$form);
                break;            
            case '\b' :
                $resultpattern = self::get_form_string('description_wordbreak',$form);
                break;
            case '\B' :
                $resultpattern = self::get_form_string('description_wordbreak_neg',$form);
                break;
            case '\A' :
                $resultpattern = self::get_form_string('description_esc_a',$form);
                break;   
            case '\Z' :
                $resultpattern = self::get_form_string('description_esc_z',$form);
                break;  
            case '\G' :
                $resultpattern = self::get_form_string('description_esc_g',$form);
                break;
        }
        return $resultpattern;
    }

}

/**
 * Defines backreferences.
 */
class qtype_preg_description_leaf_backref extends qtype_preg_description_leaf{
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        $resultpattern = self::get_form_string('description_backref',$form);
        $resultpattern = str_replace('%number', $this->pregnode->number,$resultpattern);
        return $resultpattern;
    }

}

class qtype_preg_description_leaf_options extends qtype_preg_description_leaf{
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        $resultpattern = '';
        $posopt =& $this->pregnode->posopt;
        $negopt =& $this->pregnode->negopt;
        if($posopt->length() > 0) {
            $this->handler->state->set_modifier($posopt[0]);
            $resultpattern = self::get_form_string('description_option_'.$posopt[0],$form);
        } else if($negopt->length() > 0) { 
            $this->handler->state->unset_modifier($negopt[0]);
            $resultpattern = self::get_form_string('description_unsetoption_'.$negopt[0],$form);
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
    public static function check_options($node,&$node_pattern,$form=null){
        
        $resultpattern = '';
        $mcaseless =& $node->handler->state->caseless;
        $msingleline =& $node->handler->state->singleline;
        $mmultilineline =& $node->handler->state->multilineline;
        $mextended =& $node->handler->state->extended;
        $mungreedy =& $node->handler->state->ungreedy;
        $mduplicate =& $node->handler->state->duplicate;
        
        if($node->pregnode->type === qtype_preg_node::TYPE_NODE_SUBPATT) {
			
			$node->handler->state->forceunsetmodifiers = true;	
					
		} else if($node->handler->state->forceunsetmodifiers === true) { // any other leaf

			// TODO - generate 'caseless, singleline:' instead of 'caseless: singleline:'
			if($mcaseless === true) {			
				$resultpattern .= self::get_form_string('description_unsetoption_i',$form) . ' ';
				$mcaseless = false;
			}
			if($msingleline === true) {				
				$resultpattern .= self::get_form_string('description_unsetoption_s',$form) . ' ';
				$msingleline = false;
			} 
			if($mmultilineline === true) {
				$resultpattern .= self::get_form_string('description_unsetoption_m',$form) . ' ';
				$mmultilineline = false;
			}
			if($mextended === true) {
				$resultpattern .= self::get_form_string('description_unsetoption_x',$form) . ' ';
				$mextended = false;
			}
			if($mungreedy === true) {
				$resultpattern .= self::get_form_string('description_unsetoption_U',$form) . ' ';
				$mungreedy = false;
			}
			if($mduplicate === true) {
				$resultpattern .= self::get_form_string('description_unsetoption_J',$form) . ' ';
				$mduplicate = false;
			}
			$node->handler->state->forceunsetmodifiers = false;
			$node_pattern = $resultpattern . $node_pattern;
		}
    }
   
}

class qtype_preg_description_leaf_recursion extends qtype_preg_description_leaf{
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        
        $resultpattern = '';
        if($this->pregnode->number === 0){
             $resultpattern = self::get_form_string('description_recursion_all',$form);
        }
        else{
             $resultpattern = self::get_form_string('description_recursion',$form);
             $resultpattern = str_replace('%number', $this->pregnode->number,$resultpattern);
        }
        return $resultpattern;
    }
    
}

/**
 * Reperesents backtracking control, newline convention etc sequences like (*...).
 */
class qtype_preg_description_leaf_control extends qtype_preg_description_leaf{
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        $resultpattern = '';

        if($this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_ACCEPT ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_FAIL ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_MARK_NAME ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_NO_START_OPT ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_UTF8 ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_UTF1 ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_UCP) {
            
            $resultpattern = self::get_form_string('description_'.$this->pregnode->subtype,$form);
            
        } else if ($this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_COMMIT ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_PRUNE ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_SKIP ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_THEN ) {
            
            $resultpattern = self::get_form_string('description_control_backtrack',$form);
            $resultpattern = str_replace('%what', self::get_form_string('description_'.$this->pregnode->subtype),$resultpattern,$form);       
            
        } else if ($this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_SKIP_NAME){
            
            $resultpattern = self::get_form_string('description_control_backtrack',$form);
            $resultpattern = str_replace('%what', self::get_form_string('description_'.$this->pregnode->subtype),$resultpattern,$form);
            $resultpattern = str_replace('%name', $this->pregnode->name,$resultpattern);
        
            
        } else if ($this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_CR ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_LF ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_CRLF ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_ANYCRLF ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_ANY) {
            
            $resultpattern = self::get_form_string('description_control_newline',$form);
            $resultpattern = str_replace('%what', self::get_form_string('description_'.$this->pregnode->subtype),$resultpattern,$form);

        } else {
            $resultpattern = self::get_form_string('description_control_r',$form);
            $resultpattern = str_replace('%what', self::get_form_string('description_'.$this->pregnode->subtype),$resultpattern,$form);
        }
        return $resultpattern;
    }
    
}

/**
 * Defines operator nodes.
 */
abstract class qtype_preg_description_operator extends qtype_preg_description_node{
    /** @var qtype_preg_author_tool_description[] Array of operands */
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
    public function pattern($node_parent=null,$form=null){
        
        return 'seems like pattern() for '.get_class($this).' node didnt redefined';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,$node_parent=null,$form=null){
        
        $description = '';
        $child_description = '';
        $matches = array();
        $i = 0;
        
        $this->pattern = $this->pattern($node_parent,$form);
        $description = $this->numbering_pattern($numbering_pattern,$this->pattern);
        
        $find = '/%(\w+)?(\d)/';
        while(preg_match($find,$description,$matches) !== 0){
            $form = $matches[1];
            $i = (int)$matches[2];
            $child_description = $this->operands[$i-1]->description($numbering_pattern,$this,$form);
            $description = str_replace($matches[0],$child_description,$description);
        }
        qtype_preg_description_leaf_options::check_options($this,$resultpattern,$form);
        return $description;
    }
}

/**
 * Defines finite quantifiers with left and right borders, unary operator.
 * Possible errors: left border is greater than right one.
 */
class qtype_preg_description_node_finite_quant extends qtype_preg_description_operator{

    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){

        $resultpattern ='';
        $greedpattern='';
        $wrong_borders =$this->pregnode->leftborder >= $this->pregnode->rightborder;

        if($this->pregnode->leftborder===0 ){
            if($this->pregnode->rightborder ===1){
                $resultpattern = self::get_form_string('description_finite_quant_01',$form);
                $resultpattern = str_replace('%rightborder',$this->pregnode->rightborder,$resultpattern);
            }
            else {
                $resultpattern = self::get_form_string('description_finite_quant_0',$form);
                $resultpattern = str_replace('%rightborder',$this->pregnode->rightborder,$resultpattern);
            }
            
        }
        else if ($this->pregnode->leftborder===1) {
            $resultpattern = self::get_form_string('description_finite_quant_1',$form);
            $resultpattern = str_replace('%rightborder',$this->pregnode->rightborder,$resultpattern);
        }
        else {
            $resultpattern = self::get_form_string('description_finite_quant',$form);
            $resultpattern = str_replace('%rightborder',$this->pregnode->rightborder,$resultpattern);
            $resultpattern = str_replace('%leftborder',$this->pregnode->leftborder,$resultpattern);
        }
        
        if($this->pregnode->lazy==true){
            $greedpattern = self::get_form_string('description_quant_lazy',$form);
        }
        else if ($this->pregnode->greed==true) {
            $greedpattern = self::get_form_string('description_quant_greed',$form);
        }
        else if ($this->pregnode->possessive==true) {
            $greedpattern = self::get_form_string('description_quant_possessive',$form);
        }
        $resultpattern = str_replace('%greed',$greedpattern,$resultpattern);
        
        if($wrong_borders){
            $resultpattern = preg_replace('/%(\w+)?1/',('%${1}1'.self::get_form_string('description_errorbefore',$form)),$resultpattern);
            $resultpattern = $resultpattern
                .self::get_form_string('description_finite_quant_borders_err',$form)
                .self::get_form_string('description_errorafter',$form);
        }
        return $resultpattern;
    }
    
}

/**
 * Defines infinite quantifiers node with the left border only, unary operator.
 */
class qtype_preg_description_node_infinite_quant extends qtype_preg_description_operator{
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        
        $resultpattern ='';
        $greedpattern='';
        if($this->pregnode->leftborder===0){
            $resultpattern = self::get_form_string('description_infinite_quant_0',$form);
        }
        else if ($this->pregnode->leftborder===1) {
            $resultpattern = self::get_form_string('description_infinite_quant_1',$form);
        }
        else {
            $resultpattern = self::get_form_string('description_infinite_quant',$form);
            $resultpattern = str_replace('%leftborder',$this->pregnode->leftborder,$resultpattern);
        }
        
        if($this->pregnode->lazy==true){
            $greedpattern = self::get_form_string('description_quant_lazy',$form);
        }
        else if ($this->pregnode->greed==true) {
            $greedpattern = self::get_form_string('description_quant_greed',$form);
        }
        else if ($this->pregnode->possessive==true) {
            $greedpattern = self::get_form_string('description_quant_possessive',$form);
        }

        $resultpattern = str_replace('%greed',$greedpattern,$resultpattern);
        return $resultpattern;
    }

}

/**
 * Defines concatenation, binary operator.
 */
class qtype_preg_description_node_concat extends qtype_preg_description_operator{
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        $resultpattern = '';
        $type1 = $this->operands[0]->pregnode->type;
        $type2 = $this->operands[1]->pregnode->type;
        $subtype1 = $this->operands[0]->pregnode->subtype;
        $subtype2 = $this->operands[1]->pregnode->subtype;
        
        // TODO - calculate flags inside if-esle block for optimization
        $needshortpattern = $type1===qtype_preg_node::TYPE_LEAF_CHARSET &&
                $this->operands[0]->is_one_char() &&
                $type2===qtype_preg_node::TYPE_LEAF_CHARSET &&
                $this->operands[1]->is_one_char();
        $needcontiuneshortpattern = $type2===qtype_preg_node::TYPE_LEAF_CHARSET &&
                $this->operands[1]->is_one_char() &&
                $type1===qtype_preg_node::TYPE_NODE_CONCAT && 
                $this->operands[0]->operands[1]->pregnode->type===qtype_preg_node::TYPE_LEAF_CHARSET &&
                $this->operands[0]->operands[1]->is_one_char();
        $firstaheadassert = $subtype1===qtype_preg_node_assert::SUBTYPE_PLA || $subtype1===qtype_preg_node_assert::SUBTYPE_NLA;
        $secondbehindassert = $subtype2===qtype_preg_node_assert::SUBTYPE_PLB || $subtype2===qtype_preg_node_assert::SUBTYPE_NLB;
        $aheadassertinprevconcat = $type1===qtype_preg_node::TYPE_NODE_CONCAT && 
                ($this->operands[0]->operands[1]->pregnode->subtype===qtype_preg_node_assert::SUBTYPE_PLA ||
                $this->operands[0]->operands[1]->pregnode->subtype===qtype_preg_node_assert::SUBTYPE_NLA);
        $neddspacepattern = $type1===qtype_preg_node::TYPE_LEAF_OPTIONS ||
                ($type1===qtype_preg_node::TYPE_NODE_CONCAT && 
                $this->operands[0]->operands[1]->pregnode->type===qtype_preg_node::TYPE_LEAF_OPTIONS);
        if($neddspacepattern) {
            $resultpattern = self::get_form_string('description_concat_space',$form);
        } else if($needshortpattern || $needcontiuneshortpattern) {       
            $resultpattern = self::get_form_string('description_concat_short',$form);
        } else if($firstaheadassert || $secondbehindassert || $aheadassertinprevconcat){
            $resultpattern = self::get_form_string('description_concat_and',$form);
        } else if($type1 === qtype_preg_node::TYPE_NODE_CONCAT) {
            $resultpattern = self::get_form_string('description_concat_wcomma',$form);
        } else {
            $resultpattern = self::get_form_string('description_concat',$form);
        }
        return $resultpattern;
    }
    
}

/**
 * Defines alternative, binary operator.
 */
class qtype_preg_description_node_alt extends qtype_preg_description_operator{
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        
        $resultpattern = '';
        $type1 = $this->operands[0]->pregnode->type;
        $type2 = $this->operands[1]->pregnode->type;
            
        if($type1 === qtype_preg_node::TYPE_NODE_ALT){
            $resultpattern = self::get_form_string('description_alt_wcomma',$form);
        } else {
            $resultpattern = self::get_form_string('description_alt',$form);
        }
        return $resultpattern;
    }
    
}

/**
 * Defines lookaround assertions, unary operator.
 */
class qtype_preg_description_node_assert extends qtype_preg_description_operator{
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){

        $suff = ($node_parent !== null && $node_parent->pregnode->type === qtype_preg_node::TYPE_NODE_COND_SUBPATT) ? '_cond' : '';
        return self::get_form_string('description_' . $this->pregnode->subtype . $suff,$form);
    }
    
}

/**
 * Defines subpatterns, unary operator.
 */
class qtype_preg_description_node_subpatt extends qtype_preg_description_operator{
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        
        $resultpattern = '';
        if($this->pregnode->subtype === qtype_preg_node_subpatt::SUBTYPE_GROUPING) {
            
            $resultpattern = self::get_form_string('description_grouping',$form);
            
        } else if($this->pregnode->subtype === qtype_preg_node_subpatt::SUBTYPE_DUPLICATE_SUBPATTERNS) {
            
            $resultpattern = self::get_form_string('description_grouping_duplicate',$form);
            
        } else if(is_string($this->pregnode->number)) {
            
            if($this->pregnode->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT){
                $resultpattern = self::get_form_string('description_subpattern_name',$form);
            } else {
                $resultpattern = self::get_form_string('description_subpattern_once_name',$form);
            }
            $resultpattern = str_replace('%name', $this->pregnode->number,$resultpattern);
            
        } else {
            
            $resultpattern = '';
            if($this->pregnode->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT){
                $resultpattern = self::get_form_string('description_subpattern',$form);
            } else {
                $resultpattern = self::get_form_string('description_subpattern_once',$form);
            }
            $resultpattern = str_replace('%number', $this->pregnode->number,$resultpattern);
            
        }
        return $resultpattern;
    }
    
}

/**
 * Defines conditional subpatterns, unary, binary or ternary operator.
 * The first operand yes-pattern, second - no-pattern, third - the lookaround assertion (if any).
 * Possible errors: there is no backreference with such number in expression
 */
class qtype_preg_description_node_cond_subpatt extends qtype_preg_description_operator{
    
    private function description_of_condition($form){
        $resultpattern = '';
        switch ($this->pregnode->operands[2]->subtype) {
            case qtype_preg_node_assert::SUBTYPE_PLA:
                $resultpattern = self::get_form_string('description_pla_node_assert',$form);
                break;

            case qtype_preg_node_assert::SUBTYPE_NLA:
                $resultpattern = self::get_form_string('description_nla_node_assert',$form);
                break;
            
            case qtype_preg_node_assert::SUBTYPE_PLB:
                $resultpattern = self::get_form_string('description_plb_node_assert',$form);
                break;
            
            case qtype_preg_node_assert::SUBTYPE_NLB:
                $resultpattern = self::get_form_string('description_nlb_node_assert',$form);
                break;          
        }
        //var_dump($resultpattern);
        return $resultpattern;
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        
        $resultpattern = '';
        if($this->pregnode->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT){
            
            if(is_string($this->pregnode->number)){
                $resultpattern = self::get_form_string('description_backref_node_cond_subpatt_name',$form);
                $resultpattern = str_replace('%name', $this->pregnode->number,$resultpattern);
            } else{
               $resultpattern = self::get_form_string('description_backref_node_cond_subpatt',$form);
               $resultpattern = str_replace('%number', $this->pregnode->number,$resultpattern); 
            }
            
        } else if ($this->pregnode->subtype===qtype_preg_node_cond_subpatt::SUBTYPE_RECURSION){
            
            if(is_string($this->pregnode->number)){
                $resultpattern = self::get_form_string('description_recursive_node_cond_subpatt_name',$form);
                $resultpattern = str_replace('%name', $this->pregnode->number,$resultpattern);
            } else if($this->pregnode->number===0){
                $resultpattern = self::get_form_string('description_recursive_node_cond_subpatt_all',$form);
            } else {
                $resultpattern = self::get_form_string('description_recursive_node_cond_subpatt',$form);
                $resultpattern = str_replace('%number', $this->pregnode->number,$resultpattern);
            }
            
        } else if ($this->pregnode->subtype===qtype_preg_node_cond_subpatt::SUBTYPE_DEFINE) {
            
            $resultpattern = self::get_form_string('description_define_node_cond_subpatt',$form);    
            
        } else {
            $resultpattern = self::get_form_string('description_node_cond_subpatt',$form);
            // replacing %cond with %2 or %3 whichever how many alternatives has this node
            $resultpattern = str_replace('%cond', '%'.count($this->pregnode->operands),$resultpattern);
        }
        
        $elsereplase = isset($this->pregnode->operands[1])?self::get_form_string('description_node_cond_subpatt_else',$form):'';
        $resultpattern = str_replace('%else', $elsereplase,$resultpattern);
        //var_dump($resultpattern);
        return $resultpattern;
    }
    
    
    
}

class qtype_preg_description_node_error extends qtype_preg_description_operator {

    public function pattern($node_parent=null,$form=null){
        
        $resultpattern = self::get_form_string('description_errorbefore',null)
                .$this->pregnode->error_string()
                .self::get_form_string('description_errorafter',null);
        
                $operandplaces = array();
		foreach($this->pregnode->operands as $i => $operand){
			if(isset($operand)){
                $operandplaces[] = '%'.($i+1);
			}
		}
        if(count($operandplaces)!=0){
            $resultpattern .= ' Operands: '.implode(', ',$operandplaces);
		}

        return $resultpattern;
		/*$pseudonym='';
		switch($this->pregnode->subtype){
			case qtype_preg_node_error::SUBTYPE_UNKNOWN_ERROR :
			break;
			case qtype_preg_node_error::SUBTYPE_CONDSUBPATT_TOO_MUCH_ALTER :
			break;
			case qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN :
			break;
			case qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN :
			break;
			case qtype_preg_node_error::SUBTYPE_EMPTY_PARENS :
			break;
			case qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER :
			break;
			case qtype_preg_node_error::SUBTYPE_UNCLOSED_CHARSET :
			break;
			case qtype_preg_node_error::SUBTYPE_SET_UNSET_MODIFIER :
			break;
			case qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY :
			break;
			case qtype_preg_node_error::SUBTYPE_UNKNOWN_POSIX_CLASS :
			break;
			case qtype_preg_node_error::SUBTYPE_UNKNOWN_CONTROL_SEQUENCE :
			break;
			case qtype_preg_node_error::SUBTYPE_INCORRECT_CHARSET_RANGE :
			break;
			case qtype_preg_node_error::SUBTYPE_INCORRECT_QUANT_RANGE :
			break;
			case qtype_preg_node_error::SUBTYPE_SLASH_AT_END_OF_PATTERN :
			break;
			case qtype_preg_node_error::SUBTYPE_C_AT_END_OF_PATTERN :
			break;
			case qtype_preg_node_error::SUBTYPE_INVALID_ESCAPE_SEQUENCE :
			break;
			case qtype_preg_node_error::SUBTYPE_POSIX_CLASS_OUTSIDE_CHARSET :
			break;
			case qtype_preg_node_error::SUBTYPE_UNEXISTING_SUBPATT :
			break;
			case qtype_preg_node_error::SUBTYPE_UNKNOWN_MODIFIER :
			break;
			case qtype_preg_node_error::SUBTYPE_MISSING_COMMENT_ENDING :
			break;
			case qtype_preg_node_error::SUBTYPE_MISSING_CONDSUBPATT_ENDING :
			break;
			case qtype_preg_node_error::SUBTYPE_MISSING_CALLOUT_ENDING :
			break;
			case qtype_preg_node_error::SUBTYPE_MISSING_SUBPATT_ENDING :
			break;
			case qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_ENDING :
			break;
			case qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_BEGINNING :
			break;
			case qtype_preg_node_error::SUBTYPE_WRONG_CONDSUBPATT_NUMBER :
			break;
			case qtype_preg_node_error::SUBTYPE_CONDSUBPATT_ASSERT_EXPECTED :
			break;
			case qtype_preg_node_error::SUBTYPE_CHAR_CODE_TOO_BIG :
			break;
			case qtype_preg_node_error::SUBTYPE_CONSUBPATT_ZERO_CONDITION :
			break;
			case qtype_preg_node_error::SUBTYPE_CALLOUT_BIG_NUMBER :
			break;
			case qtype_preg_node_error::SUBTYPE_DUPLICATE_SUBPATT_NAMES :
			break;
			case qtype_preg_node_error::SUBTYPE_BACKREF_TO_ZERO :
			break;
			case qtype_preg_node_error::SUBTYPE_DIFFERENT_SUBPATT_NAMES :
			break;
			case qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED :
			break;
			case qtype_preg_node_error::SUBTYPE_CX_SHOULD_BE_ASCII :
			break;
			case qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED :
			break;
		}*/
    }
}
