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

        $options = array('caseinsensitive' => false);
        $string = $this->dst_root->description($numbering_pattern,$options,null,null);
        if($whole_pattern !== null){
            $string = str_replace('%s',$string,$whole_pattern);
        }
        return $string;
    }
    
    /**
     * Calling default description($numbering_pattern,$operand_pattern,$whole_pattern=null with default params
     */
    public function default_description(){
       
        return $this->description('<span class="description_node_%n">%s</span>');
    }
    
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
    public $pattern_t;
    
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
    abstract public function description($numbering_pattern,&$options,$node_parent=null,$form=null);
    
    /**
     * gets localized string, if required a form it gets localized string for required form
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
    
    /**
     * Puts $s instead of %s in numbering pattern. Puts node id instead of %n.
     * 
     * @param type $s this string will be placed instead of %s
     */
    protected function numbering_pattern($numbering_pattern,$s){       
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
        
        return 'seems like this pattern() for this node didnt redefined';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,&$options,$node_parent=null,$form=null){
        
        $description ='';
        $this->pattern = $this->pattern($node_parent,$form);
        $description = $this->numbering_pattern($numbering_pattern,$this->pattern);
        qtype_preg_description_leaf_option::check_options($this,$description,$options);
        return $description;
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
     * @param string[] $characters enumeration of descriptions in charset
     */
    private static function flag_to_array($flag,&$characters) {
        
        $temp_str = '';
        $pseudonym = '';//pseudonym of localized string
        
        if ($flag->type === qtype_preg_charset_flag::FLAG || $flag->type === qtype_preg_charset_flag::UPROP) {
            
            // current flag is something like \w or \pL    
            $pseudonym = 'description_charflag_'.$flag->data;            
            if($flag->negative == true){
                // using charset pattern 'description_charset_one_neg' because char pattern 'description_char_neg' has a <span> tag, 
                // but dont need to highlight this
                $temp_str = self::get_form_string($pseudonym);
                $characters[] = str_replace('%characters',$temp_str,self::get_form_string('description_charset_one_neg') );
            }
            else{
                $characters[] = self::get_form_string($pseudonym);
            }
            
        } else if ($flag->type === qtype_preg_charset_flag::SET) {
            
            // current flag is simple enumeration of characters
            for ($i=0; $i < $flag->data->length(); $i++) {
                if (qtype_preg_unicode::is_in_range($flag->data[$i],qtype_preg_unicode::graph_ranges())) { //is ctype_graph correct for utf8 string?
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
        //return $characters;
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        
        $result_pattern = '';
        $characters = array();
        
        foreach ($this->pregnode->flags as $outer) {

            self::flag_to_array($outer[0],$characters);
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
       
}

/**
 * Defines simple assertions.
 */
class qtype_preg_description_leaf_assert extends qtype_preg_description_leaf{
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        $pattern_t ='';
        switch ($this->pregnode->userinscription) {
            case '^' :
                $pattern_t = self::get_form_string('description_circumflex');
                break;
            case '$' :
                $pattern_t = self::get_form_string('description_dollar');
                break;            
            case '\b' :
                $pattern_t = self::get_form_string('description_wordbreak');
                break;
            case '\B' :
                $pattern_t = self::get_form_string('description_wordbreak_neg');
                break;
            case '\A' :
                $pattern_t = self::get_form_string('description_esc_a');
                break;   
            case '\Z' :
                $pattern_t = self::get_form_string('description_esc_z');
                break;  
            case '\G' :
                $pattern_t = self::get_form_string('description_esc_g');
                break;
        }
        return $pattern_t;
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
        $pattern_t = self::get_form_string('description_backref');
        $pattern_t = str_replace('%number', $this->pregnode->number,$pattern_t);
        return $pattern_t;
    }
    
}

class qtype_preg_description_leaf_option extends qtype_preg_description_leaf{
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        
        // TODO - pattern
        return('nodes of options dont presents in trees now...');
    }
    
    /**
     * Using in description functions. 
     * Checks the need for the substitution leaf_options patterns
     * 
     * @param qtype_preg_description_node $node current node
     * @param string $node_pattern description of current node
     * @param array $options array of options
     */
    public static function check_options($node,&$node_pattern,&$options){
        
        if(isset($node->pregnode->caseinsensitive)){

            //$options['caseinsensitive'] = ($node->pregnode->caseinsensitive === true )?true:false;
            if($node->pregnode->caseinsensitive === true && $options['caseinsensitive']===false){
                $options['caseinsensitive'] = true;
                $node_pattern = self::get_form_string('description_option_i').$node_pattern;
            }
            if($node->pregnode->caseinsensitive === false && $options['caseinsensitive']===true){
                $options['caseinsensitive'] = false;
                $node_pattern = self::get_form_string('description_unsetoption_i').$node_pattern;
                var_dump(1);
                
            }
        }
        /*else{
            $need_pattern = $node->pregnode->type===qtype_preg_node::TYPE_NODE_SUBPATT || 
                    $node->pregnode->type===qtype_preg_node::TYPE_NODE_COND_SUBPATT ||
                    (count($node->pregnode->operands)>=2 && isset($node->operand[1]->pregnode->caseinsensitive) && 
                     $node->operand[1]->pregnode->caseinsensitive===false);
            if($need_pattern){    
                $node_pattern = self::caseinsensitive($node_pattern);
                var_dump($node_pattern);
            }
        }*/
    }
   
}

class qtype_preg_description_leaf_recursion extends qtype_preg_description_leaf{
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::pattern()
     */
    public function pattern($node_parent=null,$form=null){
        
        $pattern_t = '';
        if($this->pregnode->number === 0){
             $pattern_t = self::get_form_string('description_recursion_all');
        }
        else{
             $pattern_t = self::get_form_string('description_recursion');
             $pattern_t = str_replace('%number', $this->pregnode->number,$pattern_t);
        }
        return $pattern_t;
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
        $pattern_t = '';

        if($this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_ACCEPT ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_FAIL ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_MARK_NAME ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_NO_START_OPT ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_UTF8 ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_UTF1 ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_UCP) {
            
            $pattern_t = self::get_form_string('description_'.$this->pregnode->subtype);
            
        } else if ($this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_COMMIT ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_PRUNE ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_SKIP ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_THEN ) {
            
            $pattern_t = self::get_form_string('description_control_backtrack');
            $pattern_t = str_replace('%what', self::get_form_string('description_'.$this->pregnode->subtype),$pattern_t);       
            
        } else if ($this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_SKIP_NAME){
            
            $pattern_t = self::get_form_string('description_control_backtrack');
            $pattern_t = str_replace('%what', self::get_form_string('description_'.$this->pregnode->subtype),$pattern_t);
            $pattern_t = str_replace('%name', $this->pregnode->name,$pattern_t);
        
            
        } else if ($this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_CR ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_LF ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_CRLF ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_ANYCRLF ||
                $this->pregnode->subtype === qtype_preg_leaf_control::SUBTYPE_ANY) {
            
            $pattern_t = self::get_form_string('description_control_newline');
            $pattern_t = str_replace('%what', self::get_form_string('description_'.$this->pregnode->subtype),$pattern_t);

        } else {
            $pattern_t = self::get_form_string('description_control_r');
            $pattern_t = str_replace('%what', self::get_form_string('description_'.$this->pregnode->subtype),$pattern_t);
        }
        return $pattern_t;
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
        
        return 'seems like this pattern() for this node didnt redefined';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,&$options,$node_parent=null,$form=null){
        
        $description = '';
        $child_description = '';
        $form = '';
        $matches = array();
        
        $this->pattern = $this->pattern($node_parent,$form);
        $description = $this->numbering_pattern($numbering_pattern,$this->pattern);
        
        $i=1;
        $find = '/%(\w+)?'.$i.'/';
        while((count($this->operands) >= $i) && preg_match($find,$description,$matches)){
            $form = (count($matches)>=2) ? $matches[1] : null;
            $child_description = $this->operands[$i-1]->description($numbering_pattern,$options,$this,$form);
            //var_dump($matches[0]);
            $description = str_replace($matches[0],$child_description,$description);
            $i++;
            $find = '/%(\w+)?'.$i.'(\w+)?/';
        }
        qtype_preg_description_leaf_option::check_options($this,$description,$options);
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
        
        $pattern_t ='';
        $greed_pattern='';
        if($this->pregnode->leftborder===0 ){
            if($this->pregnode->rightborder ===1){
                $pattern_t = self::get_form_string('description_finite_quant_01');
                $pattern_t = str_replace('%rightborder',$this->pregnode->rightborder,$pattern_t);
            }
            else {
                $pattern_t = self::get_form_string('description_finite_quant_0');
                $pattern_t = str_replace('%rightborder',$this->pregnode->rightborder,$pattern_t);
            }
            
        }
        else if ($this->pregnode->leftborder===1) {
            $pattern_t = self::get_form_string('description_finite_quant_1');
            $pattern_t = str_replace('%rightborderr',$this->pregnode->rightborder,$pattern_t);
        }
        else {
            $pattern_t = self::get_form_string('description_finite_quant');
            $pattern_t = str_replace('%rightborder',$this->pregnode->rightborder,$pattern_t);
            $pattern_t = str_replace('%leftborder',$this->pregnode->leftborder,$pattern_t);
        }
        
        if($this->pregnode->lazy==true){
            $greed_pattern = self::get_form_string('description_quant_lazy');
        }
        else if ($this->pregnode->greed==true) {
            $greed_pattern = self::get_form_string('description_quant_greed');
        }
        else if ($this->pregnode->possessive==true) {
            $greed_pattern = self::get_form_string('description_quant_possessive');
        }
        $pattern_t = str_replace('%greed',$greed_pattern,$pattern_t);
        return $pattern_t;
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
        
        $pattern_t ='';
        $greed_pattern='';
        if($this->pregnode->leftborder===0){
            $pattern_t = self::get_form_string('description_infinite_quant_0');
        }
        else if ($this->pregnode->leftborder===0) {
            $pattern_t = self::get_form_string('description_infinite_quant_1');
        }
        else {
            $pattern_t = self::get_form_string('description_infinite_quant');
            $pattern_t = str_replace('%leftborder',$this->pregnode->leftborder,$pattern_t);
        }
        
        if($this->pregnode->lazy==true){
            $greed_pattern = self::get_form_string('description_quant_lazy');
        }
        else if ($this->pregnode->greed==true) {
            $greed_pattern = self::get_form_string('description_quant_greed');
        }
        else if ($this->pregnode->possessive==true) {
            $greed_pattern = self::get_form_string('description_quant_possessive');
        }
        $pattern_t = str_replace('%greed',$greed_pattern,$pattern_t);
        return $pattern_t;
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
        $pattern_t = '';
        $type1 = $this->operands[0]->pregnode->type;
        $type2 = $this->operands[1]->pregnode->type;
        /*if($type1===qtype_preg_node::TYPE_LEAF_CHARSET &&
                count($this->operands[0]->pregnode->flags)===1 &&
                $this->operands[0]->pregnode->flags[0][0]->type===qtype_preg_charset_flag::SET &&
                $this->operands[0]->pregnode->flags[0][0]->data->length()===1 &&
                $type2===qtype_preg_node::TYPE_LEAF_CHARSET &&
                count($this->operands[1]->pregnode->flags)===1 &&
                $this->operands[1]->pregnode->flags[0][0]->type===qtype_preg_charset_flag::SET &&
                $this->operands[1]->pregnode->flags[0][0]->data->length()===1 ) {
            
            $pattern_t = self::get_form_string('description_concat_short');*/
            
        if($type1===qtype_preg_node::TYPE_NODE_ASSERT || $type2===qtype_preg_node::TYPE_NODE_ASSERT){
            $pattern_t = self::get_form_string('description_concat_wo_union');
        } else if($type1 === qtype_preg_node::TYPE_NODE_CONCAT){
            $pattern_t = self::get_form_string('description_concat_wcomma');
        } else {
            $pattern_t = self::get_form_string('description_concat');
        }
        return $pattern_t;
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
        
        $pattern_t = '';
        $type1 = $this->operands[0]->pregnode->type;
        $type2 = $this->operands[1]->pregnode->type;
            
        if($type1 === qtype_preg_node::TYPE_NODE_ALT){
            $pattern_t = self::get_form_string('description_alt_wcomma');
        } else {
            $pattern_t = self::get_form_string('description_alt');
        }
        return $pattern_t;
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
        
        return self::get_form_string('description_'.$this->pregnode->subtype);
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
        
        $pattern_t = '';
        if($this->pregnode->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT){
            $pattern_t = self::get_form_string('description_subpattern');
        } else {
            $pattern_t = self::get_form_string('description_subpattern_once');
        }
        $pattern_t = str_replace('%number', $this->pregnode->number,$pattern_t);
        return $pattern_t;
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
        
        $pattern_t = '';
        if($this->pregnode->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_BACKREF){
            $pattern_t = self::get_form_string('description_backref_node_cond_subpatt');
            $pattern_t = str_replace('%number', $this->pregnode->number,$pattern_t);
        }
        else if ($this->pregnode->subtype===qtype_preg_node_cond_subpatt::SUBTYPE_RECURSIVE){
            if($this->pregnode->number===0){
                $pattern_t = self::get_form_string('description_recursive_node_cond_subpatt_all');
            }
            else {
                $pattern_t = self::get_form_string('description_recursive_node_cond_subpatt');
                $pattern_t = str_replace('%number', $this->pregnode->number,$pattern_t);
            }
        }
        else {
            $pattern_t = self::get_form_string('description_'.$this->pregnode->subtype);
        }
    }
    
}
