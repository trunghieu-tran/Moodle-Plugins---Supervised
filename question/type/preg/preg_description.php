<?php
/**
 * Defines handler for generating description of reg exp
 * Also defines specific tree, containing methods for generating descriptions of current node
 *
 * @copyright &copy; 2012 Pahomov Dmitry
 * @author Pahomov Dmitry, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

/**
 * Handler, generating information for regular expression
 */
class qtype_preg_author_tool_description extends qtype_preg_regex_handler {
    
    /*
     * Construct of parent class parses the regex and does all necessary preprocessing.
     *
     * @param string regex - regular expression to handle.
     * @param string modifiers - modifiers of the regular expression.
     * @param object options - options to handle regex, i.e. any necessary additional parameters.
     */
    public function __construct($regex = null, $modifiers = null, $options = null){
        parent::__construct($regex, $modifiers, $options);
    }
    
    /**
     * Genegates description of regexp
     * Example of calling:
     * 
     * description('<span class="description_node_%n%o">%s</span>',' operand','<span class="description">%s</span>');
     * 
     * Operator with id=777 will be plased into: <span class="description_node_777">abc</span>.
     * User defined parts of regex with id=777 will be placed id: <span class="description_node_777  operand">%1 or %2</span>.
     * Whole string will be placed into <span class="description">string</span>
     * 
     * @param string $whole_pattern Pattern for whole decription. Must contain %s - description.
     * @param string $numbering_pattern Pattern to track numbering. 
     * Must contain: %s - description of node;
     * May contain:  %n - id node.
     * @return string description.
     */
    public function description($numbering_pattern,$whole_pattern=null){
        
        $string = $this->dst_root->description($numbering_pattern,null,null);;
        $string = str_replace('%s',$string,$whole_pattern);
        return (string)$string;
    }
    
    /**
     * Calling default description($numbering_pattern,$operand_pattern,$whole_pattern=null with default params
     */
    public function default_description(){
       
        return (string)$this->description('<span class="description_node_%n">%s</span>','<span class="description">%s</span>');
    }
    
    /**
     * Returns the engine-specific node name for the given preg_node name.
     * Overload in case of sophisticated node name schemes.
     */
    protected function get_engine_node_name($pregname) {
        
        return 'qtype_preg_description_'.$pregname;
    }
    
    /**
     * Is a preg_node_... or a preg_leaf_... supported by the engine?
     * Returns true if node is supported or user interface string describing
     *   what properties of node isn't supported.
     */
    protected function is_preg_node_acceptable($pregnode) {
        
        return false;    // Should be overloaded by child classes
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
    
    /**
     * Constructs node.
     * 
     * @param qtype_preg_node $node Reference to automatically generated (by handler) abstract node.                                    
     * @param type $matcher Reference to handler, which generates nodes.
     */
    public function __construct($node, $matcher) {
        
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
     * if $s nor defined in lang file throw exeption
     * 
     * @param string $s same as in get_string
     * @param string $form Required form.
     */
    public static function get_form_string($s,$form=null){
        
        if(isset($form)){
            $s.='_'.$form;
        }
        /* exeption throws automaticly?
        $return = get_string($s);
        if($return == null){
            throw new coding_exception($s.' is missing in current lang file of preg description', 'ask localizator of preg description module');
        }
        return $return;*/
        return get_string($s,'qtype_preg');
    }
    
    /**
     * returns true if engine support the node, rejection string otherwise
     */
    public function accept() {
        return true;
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
        
        return 'seems like this pattern() for this node didnt redefined';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,$node_parent=null,$form=null){
        
        $this->pattern = $this->pattern($node_parent,$form);
        return $this->pattern;
    }
}

/**
 * Represents a character or a charcter set.
 */
class qtype_preg_description_leaf_charset extends qtype_preg_description_leaf{
    
    /**
     * Convertes charset flag to array of descriptions(strings)
     * 
     * @param qtype_preg_charset_flag $flag flag gor description
     * @return string[]|string array of descriptions of current flag
     */
    private static function flag_to_array($flag) {
        
        $characters = array();//array of strings
        $pattern_pseudonym = '';//pseudonym of localized string
        
        if ($flag->type === qtype_preg_charset_flag::FLAG || $flag->type === qtype_preg_charset_flag::UPROP) {
            // flag is something like \w or \pL    
            $pattern_pseudonym = 'description_charflag_'.$flag->data;            
            if($flag->negative == true){
                // using charset pattern 'description_charset_one_neg' because char pattern 'description_char_neg' has a <span> tag, 
                // but dont need to highlight this
                $characters[] = self::get_form_string($pattern_pseudonym);
                $characters[0] = str_replace('%characters',$characters[0],self::get_form_string('description_charset_one_neg') );
            }
            else{
                $characters[] = self::get_form_string($pattern_pseudonym);
            }
            
        } else if ($flag->type === qtype_preg_charset_flag::SET) {
            // flag is simple enumeration of characters
            for ($i=0; $i < $flag->data->length(); $i++) {
                if (ctype_graph ( $flag->data[$i])) {
                    $characters[] = str_replace('%char',$flag->data[$i],self::get_form_string('description_char') );  
                }
                else if ($flag->data[$i]===' ') {
                    $characters[] = self::get_form_string('description_char_space');
                }
                else{
                    $characters[] = str_replace('%code',qtype_poasquestion_string::ord($flag->data[$i]),self::get_form_string('description_char_16value') );  
                }
            }
        }
        return $characters;
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        
        $result_pattern = '';
        $characters = array();
        
        foreach ($this->pregnode->flags as $outer) {

            $characters = array_merge($characters,self::flag_to_array($outer[0]));
        }

        if(count($characters)==1 && $this->pregnode->negative == false){
            // Simulation of: 
            // $string['description_charset_one'] = '%characters'; 
            // w/o calling functions
            $result_pattern = $characters[0];
        }
        else{
            if(count($characters)==1 && $this->pregnode->negative == true){
                $result_pattern = self::get_form_string('description_charset_one_neg');
            }else if($this->pregnode->negative == false){
                $result_pattern = self::get_form_string('description_charset');
            }
            else{
                $result_pattern = self::get_form_string('description_charset_negative');
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
        
        return self::get_form_string('description_empty');
    }
    
    /**
     * returns true if engine support the node, rejection string otherwise
     */
    public function accept() {
        $flag = $this->pregnode->subtype === qtype_preg_leaf_meta::SUBTYPE_EMPTY;
        return ($flag) ? true : false;
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
        $pattern ='';
        switch ($this->pregnode->userinscription) {
            case '^' :
                $pattern = self::get_form_string('description_circumflex');
                break;
            case '$' :
                $pattern = self::get_form_string('description_dollar');
                break;            
            case '\b' :
                $pattern = self::get_form_string('description_wordbreak');
                break;
            case '\B' :
                $pattern = self::get_form_string('description_wordbreak_neg');
                break;
            case '\A' :
                $pattern = self::get_form_string('description_esc_a');
                break;   
            case '\Z' :
                $pattern = self::get_form_string('description_esc_z');
                break;  
            case '\G' :
                $pattern = self::get_form_string('description_esc_g');
                break;
        }
        return $pattern;
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
        
        return '123';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,$node_parent=null,$form=null){
        
        return '123';
    }
}

class qtype_preg_description_leaf_option extends qtype_preg_description_leaf{
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        
        return '123';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,$node_parent=null,$form=null){
        
        return '123';
    }
}

class qtype_preg_description_leaf_recursion extends qtype_preg_description_leaf{
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        
        return '123';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,$node_parent=null,$form=null){
        
        return '123';
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
        
        return '123';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,$node_parent=null,$form=null){
        
        return '123';
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
        
        return '123';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,$node_parent=null,$form=null){
        
        return '123';
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
        
        return '123';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,$node_parent=null,$form=null){
        
        return '123';
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
        
        return '123';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,$node_parent=null,$form=null){
        
        return '123';
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
        
        return '123';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,$node_parent=null,$form=null){
        
        return '123';
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
        
        return '123';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,$node_parent=null,$form=null){
        
        return '123';
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
        
        return '123';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,$node_parent=null,$form=null){
        
        return '123';
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
        
        return '123';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,$node_parent=null,$form=null){
        
        return '123';
    }
}

/**
 * Defines conditional subpatterns, unary, binary or ternary operator.
 * The first operand yes-pattern, second - no-pattern, third - the lookaround assertion (if any).
 * Possible errors: there is no backreference with such number in expression
 */
class qtype_preg_description_node_cond_subpatt extends qtype_preg_description_operator{
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        
        return '123';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,$node_parent=null,$form=null){
        
        return '123';
    }
}
