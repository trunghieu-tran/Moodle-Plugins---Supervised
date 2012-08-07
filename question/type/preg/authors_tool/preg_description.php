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
     * @param string $wholepattern Pattern for whole decription. Must contain %s - description.
     * @param string $numbering_pattern Pattern to track numbering. 
     * Must contain: %s - description of node;
     * May contain:  %n - id node.
     * @return string description.
     */
    public function description($numbering_pattern,$wholepattern=null){

        $options = array('caseinsensitive' => false);
        if(isset($this->dst_root)){
            $string = $this->dst_root->description($numbering_pattern,$options,null,null);
            $string = self::postprocessing($string);
        }
        else {
           $string = 'tree was not built'; 
        }
        if($wholepattern !== null && $wholepattern !== ''){
            $string = str_replace('%s',$string,$wholepattern);
        }
        return $string;
    }
    
    private static function postprocessing($s){

        $result = preg_replace('%;((?:</span>)?)]%','\1]',$s);
        $result = str_replace('not not ','',$result);
        return $result;
    }
    
    /**
     * Calling default description($numbering_pattern,$operand_pattern,$wholepattern=null with default params
     */
    public function default_description(){
       
        return $this->description('<span class="description_node_%n">%s</span>');
    }
    
    /**
     * for testing
     */
    public function form_description($form){
        $options = array('caseinsensitive' => false);
        $result = $this->dst_root->description('%s',$options,null,$form);
        return $result;      
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
    public $resultpattern;
    
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
        
        if(isset($form) && $form !== ''){
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
        qtype_preg_description_leaf_option::check_options($this,$description,$options,$form);
        return $description;
    }
}

/**
 * Represents a character or a charcter set.
 */
class qtype_preg_description_leaf_charset extends qtype_preg_description_leaf{
    
    public function is_one_char(){
        $flag = $this->pregnode->flags[0][0];
        return count($this->pregnode->flags)===1 && 
            $flag->type===qtype_preg_charset_flag::SET &&
            $flag->data->length()===1 && 
            qtype_preg_unicode::is_in_range($flag->data[0],qtype_preg_unicode::graph_ranges());
    }
    
    
    /**
     * Convertes charset flag to array of descriptions(strings)
     * 
     * @param qtype_preg_charset_flag $flag flag gor description
     * @param string[] $characters enumeration of descriptions in charset
     */
    private static function flag_to_array($flag,&$characters,$form=null) {
        
        $temp_str = '';
        $pseudonym = '';//pseudonym of localized string
        
        if ($flag->type === qtype_preg_charset_flag::FLAG || $flag->type === qtype_preg_charset_flag::UPROP) {
            
            // current flag is something like \w or \pL    
            $pseudonym = 'description_charflag_'.$flag->data;            
            if($flag->negative == true){
                // using charset pattern 'description_charset_one_neg' because char pattern 'description_char_neg' has a <span> tag, 
                // but dont need to highlight this
                $temp_str = self::get_form_string($pseudonym,$form);
                $characters[] = str_replace('%characters',$temp_str,self::get_form_string('description_charset_one_neg') ,$form);
            }
            else{
                $characters[] = self::get_form_string($pseudonym,$form);
            }
            
        } else if ($flag->type === qtype_preg_charset_flag::SET) {
            
            // current flag is simple enumeration of characters
            for ($i=0; $i < $flag->data->length(); $i++) {
                if (qtype_preg_unicode::is_in_range($flag->data[$i],qtype_preg_unicode::graph_ranges())) { //is ctype_graph correct for utf8 string?
                    $characters[] = str_replace('%char',$flag->data[$i],self::get_form_string('description_char' ,$form));  
                }
                else{ 
                    $char_num = qtype_poasquestion_string::ord($flag->data[$i]);
                    if ($flag->data[$i]===' ') {
                        $characters[] = self::get_form_string('description_char_space',$form);
                    }
                    else if ($char_num===8) {
                        $characters[] = self::get_form_string('description_char_b',$form);
                    }
                    else if ($char_num===9) {
                        $characters[] = self::get_form_string('description_char_t',$form);
                    }
                    else if ($char_num===10) {
                        $characters[] = self::get_form_string('description_char_n',$form);
                    }
                    else if ($char_num===13) {
                        $characters[] = self::get_form_string('description_char_r',$form);
                    }
                    else{
                        $characters[] = str_replace('%code',$char_num,self::get_form_string('description_char_16value') ,$form);  
                    }               
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

            self::flag_to_array($outer[0],$characters,$form);
        }

        if(count($characters)==1 && $this->pregnode->negative == false){
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
        switch ($this->pregnode->userinscription) {
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
    public static function check_options($node,&$node_pattern,&$options,$form=null){
        
        if(isset($node->pregnode->caseinsensitive)){

            //$options['caseinsensitive'] = ($node->pregnode->caseinsensitive === true )?true:false;
            if($node->pregnode->caseinsensitive === true && $options['caseinsensitive']===false){
                $options['caseinsensitive'] = true;
                $node_pattern = self::get_form_string('description_option_i',$form).$node_pattern;
            }
            if($node->pregnode->caseinsensitive === false && $options['caseinsensitive']===true){
                $options['caseinsensitive'] = false;
                $node_pattern = self::get_form_string('description_unsetoption_i',$form).$node_pattern;
                //var_dump(1);
                
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
        
        return 'seems like this pattern() for this node didnt redefined';
    }
    
    /**
     * Redifinition of abstruct qtype_preg_description_node::description()
     */
    public function description($numbering_pattern,&$options,$node_parent=null,$form=null){
        
        $description = '';
        $childdescription = '';
        $matches = array();
        
        $this->pattern = $this->pattern($node_parent,$form);
        $description = $this->numbering_pattern($numbering_pattern,$this->pattern);
        
        $i=1;
        $find = '/%(\w+)?'.$i.'/';
        while((count($this->operands) >= $i) && preg_match($find,$description,$matches)){
            $form = (count($matches)>=2) ? $matches[1] : null;
            $childdescription = $this->operands[$i-1]->description($numbering_pattern,$options,$this,$form);
            //var_dump($matches[0]);
            $description = str_replace($matches[0],$childdescription,$description);
            $i++;
            $find = '/%(\w+)?'.$i.'(\w+)?/';
        }
        qtype_preg_description_leaf_option::check_options($this,$description,$options,$form);
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
        $wrongborders =$this->pregnode->leftborder >= $this->pregnode->rightborder;

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
        
        if($wrongborders){
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
        
        if($needshortpattern || $needcontiuneshortpattern) {       
            $resultpattern = self::get_form_string('description_concat_short',$form);
        } else if($firstaheadassert || $secondbehindassert || $aheadassertinprevconcat){
            $resultpattern = self::get_form_string('description_concat_and',$form);
        } else if($type1 === qtype_preg_node::TYPE_NODE_CONCAT){
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

        return self::get_form_string('description_'.$this->pregnode->subtype,$form);
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
        
        if(is_string($this->pregnode->number)){
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
            case qtype_preg_node_cond_subpatt::SUBTYPE_PLA:
                $resultpattern = self::get_form_string('description_pla_node_assert',$form);
                break;

            case qtype_preg_node_cond_subpatt::SUBTYPE_NLA:
                $resultpattern = self::get_form_string('description_nla_node_assert',$form);
                break;
            
            case qtype_preg_node_cond_subpatt::SUBTYPE_PLB:
                $resultpattern = self::get_form_string('description_plb_node_assert',$form);
                break;
            
            case qtype_preg_node_cond_subpatt::SUBTYPE_NLB:
                $resultpattern = self::get_form_string('description_nlb_node_assert',$form);
                break;          
        }
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
            }
            else{
               $resultpattern = self::get_form_string('description_backref_node_cond_subpatt',$form);
               $resultpattern = str_replace('%number', $this->pregnode->number,$resultpattern); 
            }
            
        }
        else if ($this->pregnode->subtype===qtype_preg_node_cond_subpatt::SUBTYPE_RECURSION){
            
            if(is_string($this->pregnode->number)){
                $resultpattern = self::get_form_string('description_recursive_node_cond_subpatt_name',$form);
                $resultpattern = str_replace('%name', $this->pregnode->number,$resultpattern);
            }
            else if($this->pregnode->number===0){
                $resultpattern = self::get_form_string('description_recursive_node_cond_subpatt_all',$form);
            }
            else {
                $resultpattern = self::get_form_string('description_recursive_node_cond_subpatt',$form);
                $resultpattern = str_replace('%number', $this->pregnode->number,$resultpattern);
            }
            
        }
        else if ($this->pregnode->subtype===qtype_preg_node_cond_subpatt::SUBTYPE_DEFINE) {
            
            $resultpattern = self::get_form_string('description_define_node_cond_subpatt',$form);    
            
        }
        else {
            $resultpattern = self::get_form_string('description_node_cond_subpatt',$form);
            $resultpattern = str_replace('%cond', $this->description_of_condition($form),$resultpattern);
        }
        
        $elsereplase = (isset($this->pregnode->operands[1]))?self::get_form_string('description_node_cond_subpatt_else',$form):'';
        $resultpattern = str_replace('%else', $elsereplase,$resultpattern);
            
        return $resultpattern;
    }
    
    
    
}

class qtype_preg_description_node_error extends qtype_preg_description_operator {

    public function pattern($node_parent=null,$form=null){
        
        $resultpattern = self::get_form_string('description_errorbefore',null)
                .$this->pregnode->error_string()
                . self::get_form_string('description_errorafter',null);
        
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
    }
}
