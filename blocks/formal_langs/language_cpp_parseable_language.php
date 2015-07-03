<?php
// This file is part of Formal Languages block - https://code.google.com/p/oasychev-moodle-plugins/
//
// Formal Languages block is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Formal Languages block is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Formal Languages block.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Defines a C++ language with parsing capabilities
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Mamontov Dmitriy, Maria Birukova
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
global $CFG;
require_once($CFG->dirroot .'/blocks/formal_langs/language_cpp_language.php');
require_once($CFG->dirroot .'/blocks/formal_langs/parser_cpp_language.php');
require_once($CFG->dirroot .'/blocks/formal_langs/lexer_to_parser_mapper.php');

/**
 * Class block_formal_langs_lexer_cpp_mapper
 * A mapper for mapping C++ lexer to parser constants
 */
class block_formal_langs_lexer_cpp_mapper extends block_formal_langs_lexer_to_parser_mapper {

    /*! An array, where keys are namespaces and class names and values are nested namespaces.
        If class has no nested namespaces, he should not get it
     */
    protected $namespacetree;
	/*! An array of constructable types
	 */
	protected $constructabletypestree;
    /*! A stack, which should be used for looking up 
     */
    protected $lookupnamespacestack;
    /*! A stack for introducted type namespace
     */
    protected $introducednamespacestack;
	/*! A stack, that determine, whether we should pop introduced namespace stack or not
	 */
	protected $shoulclosenamespacestack;
	/*! Makes parser to not strip comments from data
	 */
	protected $stripcomments;
    /**
     * Construcs mapper
     */
    public function __construct() {
        parent::__construct();
        $this->namespacetree = array();
        $this->lookupnamespacestack = array();
        $this->introducednamespacestack = array();
		$this->constructabletypestree = array();
		$this->shoulclosenamespacestack = array();
		$this->stripcomments = true;
    }
	/** 
	 * Enables or disabled comment stripping
	 * @param $flag a flag value
	 */
	public function set_strip_comments($flag) {
		$this->stripcomments = $flag;
	}
	
	/** Pushed introduced type
	 *  @param[in] string $name a name of type
	 */
	public function push_introduced_type($name, $templateargs = array()) {
		$this->introducednamespacestack[] = (string)$name;
		$this->shoulclosenamespacestack[] = array('anonymous' => false, 'classes' => $templateargs);
	}
	
	/**
	 * Pushes anonynous type on stack
	 */
	public function push_anonymous_type($templateargs = array()) {
		$this->shoulclosenamespacestack[] =  array('anonymous' => true, 'classes' => $templateargs);	
	}
	
	/**
	 * Pops introduced type from stack
	 */
	public function try_pop_introduced_type() {
		if (count($this->shoulclosenamespacestack)) {
			$flag = $this->shoulclosenamespacestack[count($this->shoulclosenamespacestack) - 1]['anonymous'];
			unset($this->shoulclosenamespacestack[count($this->shoulclosenamespacestack) - 1]);
			// Fix atrocious behaviour, when unsetting made indexes be preserved.
			$this->shoulclosenamespacestack = array_values($this->shoulclosenamespacestack);
			
			if ($flag == false) {
				unset($this->introducednamespacestack[count($this->introducednamespacestack) - 1]);
				// Fix atrocious behaviour, when unsetting made indexes be preserved.
				$this->introducednamespacestack = array_values($this->introducednamespacestack);
			}
		}
	}
    
    public function push_lookup_entry($name) {
        if (count($this->lookupnamespacestack) == 0) {
            $this->lookupnamespacestack[] = array();
        }
		//echo "Before push: ";
		//var_dump($this->lookupnamespacestack);
		$index = count($this->lookupnamespacestack) - 1;
		//echo "Mapper::push_lookup_entry(" . $index . "," . $name . ")\n";
        $this->lookupnamespacestack[$index][] = $name;
		//echo "After push: ";
		//var_dump($this->lookupnamespacestack);
    }
    
    public function start_new_lookup_namespace() {
		//echo "Before start: ";
		//var_dump($this->lookupnamespacestack);
		$this->lookupnamespacestack[] = array();
		//echo "After start: ";
		//var_dump($this->lookupnamespacestack);
		//echo "Mapper::start_new_lookup_namespace - count(" . (count($this->lookupnamespacestack)) . ")\n";        
    }
    
    public function clear_lookup_namespace() {
        //echo "Before clean: ";
		//var_dump($this->lookupnamespacestack);
		if (count($this->lookupnamespacestack)) {
            unset($this->lookupnamespacestack[count($this->lookupnamespacestack) - 1]);
			// Fix atrocious behaviour, when unsetting made indexes be preserved.
			$this->lookupnamespacestack = array_values($this->lookupnamespacestack);
        }
		//echo "After clean: ";
		//var_dump($this->lookupnamespacestack);		
		//echo "Mapper::clear_lookup_namespace - count(" . count($this->lookupnamespacestack) . ")\n";
    }
    
    /** Sets namespace tree for a mapper
     *  @param array $tree a tree for namespaces
     */
    public function set_namespace_tree($tree) {
        $this->namespacetree = $tree;
    }
    /**
     * Adds new type to a test
     * @param string $typename a name for type
     */
    public function introduce_type($typename) {
        $root = &$this->namespacetree;
        $exists = true;
		//var_dump($this->introducednamespacestack);
        if (count($this->introducednamespacestack)) {
            $introducednamespacestack = $this->introducednamespacestack[count($this->introducednamespacestack) - 1];
            $tree = $this->namespacetree;
            for($i = 0; $i < count($this->introducednamespacestack) && $exists; $i++) {
                if (array_key_exists($this->introducednamespacestack[$i], $root)) {
                    $root = &$root[$this->introducednamespacestack[$i]];
                } else {
                    $exists = false;
                }
                
            }
        }
        if ($exists) {
            $root[(string)$typename] = array(); 
        }
    }
    /**
     * Introduces an array of types
     * @param array $typenames an array of typenames
     */
    public function introduce_types($typenames) {
        if (count($typenames)) {
            foreach($typenames as $typename) {
                $this->introduce_type($typename);
            }
        }
    }
	/**
	 * Adds new constructable type to a tree
	 * @param string $typename name of type
	 */
	public function introduce_constructable($typename) {
		$this->introduce_type($typename);
		$root = &$this->constructabletypestree;
        $exists = true;
        if (count($this->introducednamespacestack)) {
            $introducednamespacestack = $this->introducednamespacestack[count($this->introducednamespacestack) - 1];
            for($i = 0; $i < count($this->introducednamespacestack) && $exists; $i++) {
                if (array_key_exists($this->introducednamespacestack[$i], $root) == false) {
                    $root[$this->introducednamespacestack[$i]] = array();
                } 
				$root = &$root[$this->introducednamespacestack[$i]];                
            }
        }
		$root[(string)$typename] = array();
	}

    
    public function is_type_in_tree($name, $tree) {
        $currentlookupnamespace = array();
        if (count($this->lookupnamespacestack) != 0) {
            $currentlookupnamespace = $this->lookupnamespacestack[count($this->lookupnamespacestack) - 1];
        }        
		//echo "Looking for a $name in tree, while stack is " . implode($currentlookupnamespace, ",") . "\n";
        $nspace = $tree;
        for($i = 0; $i < count($currentlookupnamespace); $i++) {
            $space = $currentlookupnamespace[$i];
            if (array_key_exists($space, $nspace) == false) {
                return false;
            }            
            $nspace = $nspace[$space];
        }
        return array_key_exists($name, $nspace);
    }
    
    /** Finds template spec for a node
     *  @param block_formal_langs_ast_base $node a node
     *  @return array     
     */
    public function find_template_spec($node) {
        $result = array();
        $type = (string)($node->type());
        if ($type == 'template_spec') {
            $result = array( $node );
        }
        if (count($node->childs())) {
            $children = $node->childs();
            for($i = 0; $i < count($children); $i++) {
                $k = $this->find_template_spec($children[$i]);
                if (count($result)) {
                    if (count($k)) {
                        $result = array_merge($result, $k);
                    }
                } else {
                    $result = $k;
                }
            }
        }
        return $result;
    }
    /** Finds all identifiers except the one in formal args
     *  @param $node node data
     *  @return array
     */
    public function find_identifiers_except_in_formal_args($node) { 
        $result = array();
        $type = (string)($node->type());
        if ($type == 'identifier') {
            $result = array( $node->value() );
        }
        if (count($node->childs())) {
            $children = $node->childs();
            for($i = 0; $i < count($children); $i++) {
                $child = $children[$i];
                $type = (string)($child->type());
                if ($type != 'formal_args_list_with_or_without_const') {
                    $k = $this->find_identifiers_except_in_formal_args($child);
                    if (count($result)) {
                        if (count($k)) {
                            $result = array_merge($result, $k);
                        }
                    } else {
                        $result = $k;
                    }
                }
            }
        }
        return $result;
    }
    /** Performs action for typedef
     *  @param $node node data
     */
    public function perform_typedef_action($node) {
        $this->introduce_types($this->find_identifiers_except_in_formal_args($node));
    }
    /** Extracts template parameters from node as argument 
     *  @param block_formal_langs_ast_base $node a node
     *  @param array
     */
    public function extract_template_parameters($node) {
        $specs = $this->find_template_spec($node);
        $result = array();
        $types = array(
            'struct' => 1,
            'enum' => 1,
            'class' => 1,
            'typename' => 1
        ); 
        for($i = 0; $i < count($specs); $i++) {
            $children = $specs[$i]->childs();
            $istype = false;
            if (method_exists($children[0], 'value')) {
                $type = $children[0]->value();
                $type = (string)$type;
                $istype = array_key_exists($type, $types);
            }
            if ($istype && count($children) > 1)
            {
                $value = $children[1]->value();
                $value = (string)$value;
                $result[] = $value;
            }
        }
        if (count($result)) {
            $result = array_flip($result);
        }
        return $result;
    }

    /**
     * Returns true, whether token value is type
     * @param string $name token value
     * @return boolean whether token value is type
     */
    public function is_type($name) {
        $result = false;
        $name = (string)$name;
        for($i = 0; $i < count($this->shoulclosenamespacestack); $i++) {
            if (array_key_exists($name, $this->shoulclosenamespacestack[$i]['classes'])) {
                return true;
            }
        }
		//var_dump($this->lookupnamespacestack);
        for($i = count($this->introducednamespacestack) - 1; $i > -1; $i--) { 
            $tree = $this->namespacetree;
            $exists = true;
            for($j = 0; $j <= $i && $exists; $j++) {
                if (array_key_exists($this->introducednamespacestack[$j], $tree)) {
                    $tree = $tree[$this->introducednamespacestack[$j]];
                } else {
                    $exists = false;
                }
            }
            $result = $result || $this->is_type_in_tree($name, $tree);
        }
        $result = $result || $this->is_type_in_tree($name, $this->namespacetree);
		//$f = ($result ? " a " : " not ");
		//echo (string) $name . " is "  . $f . "a type\n";
        return $result;
    }
	/**
     * Returns true, whether type is constructable in current tree
     * @param string $name token value
     * @return boolean whether token value is type
     */
	public function is_constructable_type_in_tree($tree) {
        $currentlookupnamespace = array();
        if (count($this->lookupnamespacestack) != 0) {
            $currentlookupnamespace = $this->lookupnamespacestack[count($this->lookupnamespacestack) - 1];
        }
		if (count($currentlookupnamespace) == 0) {
			return false;
		}
		//echo "Looking for a $name in tree, while stack is " . implode($currentlookupnamespace, ",") . "\n";
        $nspace = $tree;
        for($i = 0; $i < count($currentlookupnamespace); $i++) {
            $space = $currentlookupnamespace[$i];
            if (array_key_exists($space, $nspace) == false) {
                return false;
            }            
            $nspace = $nspace[$space];
        }
        return true;
    }
	
	/**
     * Returns true, whether type is constructable
     * @param string $name token value
     * @return boolean whether token value is type
     */
    public function is_constructable_type() {
        $result = false;
		//var_dump($this->lookupnamespacestack);
        for($i = count($this->introducednamespacestack) - 1; $i > -1; $i--) { 
            $tree = $this->constructabletypestree;
            $exists = true;
            for($j = 0; $j <= $i && $exists; $j++) {
                if (array_key_exists($this->introducednamespacestack[$j], $tree)) {
                    $tree = $tree[$this->introducednamespacestack[$j]];
                } else {
                    $exists = false;
                }
            }
            $result = $result || $this->is_constructable_type_in_tree($tree);
        }
        $result = $result || $this->is_constructable_type_in_tree($this->constructabletypestree);
		//$f = ($result ? " a " : " not ");
		//echo (string) $name . " is "  . $f . "a type\n";
        return $result;
    }
	/** Tests, whether this is outer constructor name
	 *  @param string $name name of type
	 *  @return bool
	 */
	public function is_outer_constructor_name($name) {
		$name = (string)$name;
		$result = false;
		$currentlookupnamespace = array();
        if (count($this->lookupnamespacestack) != 0) {
            $currentlookupnamespace = $this->lookupnamespacestack[count($this->lookupnamespacestack) - 1];
        }
		// If lookup namespace is not empty
		if (count($currentlookupnamespace) != 0) {
			// If constructor name is equal to last name of type
			if ($name == $currentlookupnamespace[count($currentlookupnamespace) - 1]) {
				// Check, whether type is constructable
				$result = $this->is_constructable_type();
			}
		}
		return $result;
	}

    /**
     * Returns name of parser class
     * @return name of parser class
     */
    public function parsername() {
        return 'block_formal_langs_parser_cpp_language';
    }
    /**
     * Makes parser parse specific token
     * @param block_formal_langs_token_base $token parsed token
     * @param mixed $parser parser class
     * @param mixed $token a token
     */
    protected function parse_token($token, $parser) {
        if (($token->type() != 'singleline_comment' && $token->type() != 'multiline_comment') || $this->stripcomments == false) {
            parent::parse_token($token, $parser);
        }
    }
    /**
     * Returns mappings of lexer tokens to parser tokens
     * @param string $any name for any value matching
     * @return array mapping
     */
    public function maptable($any) {
        $table = array(
            'singleline_comment' => array( $any => 'COMMENT' ),
            'multiline_comment' => array( $any => 'COMMENT' ),            
            'identifier' => array( $any => 'IDENTIFIER', 'bool' => 'TYPENAME' ),
            'typename'   => array( 
				$any => 'TYPENAME', 
				'signed' => 'SIGNED', 
				'unsigned' => 'UNSIGNED', 
				'long' => 'LONG', 
				'short' => 'SHORT', 
				'char' => 'CHAR', 
				'int' => 'INT',
				'float' => 'FLOAT',
				'double' => 'DOUBLE',                
                'void' => 'VOID',
			),
            'numeric'    => array( $any => 'NUMERIC'),
            'ellipsis'   => array( $any => 'ELLIPSIS'),
            'operators'  => array(
                '-' => 'MINUS',
                '+' => 'PLUS',
                '.' => 'DOT',
                '->' => 'RIGHTARROW',
                '*'  => 'MULTIPLY',
                '&'  => 'AMPERSAND',
                '::' => 'NAMESPACE_RESOLVE',
                '++' => 'INCREMENT',
                '--' => 'DECREMENT',
                '<'  => 'LESSER',
                '>'  => 'GREATER',
                '<=' => 'LESSER_OR_EQUAL',
                '>=' => 'GREATER_OR_EQUAL',
                '!'  => 'LOGICALNOT',
                '~'  => 'BINARYNOT',
                '/'  => 'DIVISION',
                '%'  => 'MODULOSIGN',
                '<<' => 'LEFTSHIFT',
                '>>' => 'RIGHTSHIFT',
                '==' => 'EQUAL',
                '!=' => 'NOT_EQUAL',
                '|'  => 'BINARYOR',
                '^'  => 'BINARYXOR',
                '&&' => 'LOGICALAND',
                '||' => 'LOGICALOR',
                '='  => 'ASSIGN',
                '+=' => 'PLUS_ASSIGN',
                '-=' => 'MINUS_ASSIGN',
                '*=' => 'MULTIPLY_ASSIGN',
                '/=' => 'DIVISION_ASSIGN',
                '%=' => 'MODULO_ASSIGN',
                '<<=' => 'LEFTSHIFT_ASSIGN',
                '>>=' => 'RIGHTSHIFT_ASSIGN',
                '&='  => 'BINARYAND_ASSIGN',
                '|='  => 'BINARYOR_ASSIGN',
                '^='  => 'BINARYXOR_ASSIGN',
                ':'   => 'COLON'
            ),
            'question_mark' => array($any => 'QUESTION'),
            'colon' => array($any => 'COLON'),
            'semicolon' => array($any => 'SEMICOLON'),
            'keyword' => array(
                'sizeof' => 'SIZEOF',
                'new' => 'NEWKWD',
                'delete' => 'DELETE',
                'if' => 'IFKWD',
                'else' => 'ELSEKWD',
                'const_cast'       => 'CONST_CAST',
                'dynamic_cast'     => 'DYNAMIC_CAST',
                'reinterpret_cast' => 'REINTERPRET_CAST',
                'static_cast'      => 'STATIC_CAST',
                'break'            => 'BREAKKWD',
                'typedef'          => 'TYPEDEF',
                'static'           => 'STATICKWD',
                'extern'           => 'EXTERNKWD',
                'register'         => 'REGISTERKWD',
                'switch'           => 'SWITCHKWD',
                'case'             => 'CASEKWD',
                'default'          => 'DEFAULTKWD',
                'try'              => 'TRYKWD',
                'catch'            => 'CATCHKWD',
                'volatile'         => 'VOLATILEKWD',
                'goto'             => 'GOTOKWD',
                'continue'         => 'CONTINUEKWD',
                'const'            => 'CONSTKWD',
                'for'              => 'FORKWD',
                'while'            => 'WHILEKWD',
                'do'               => 'DOKWD',
                'return'           => 'RETURNKWD',
                'friend'           => 'FRIENDKWD',
                'template'         => 'TEMPLATEKWD',
                'typename'         => 'TYPENAMEKWD',
                'class'            => 'CLASSKWD',
                'struct'           => 'STRUCTKWD',
                'enum'             => 'ENUMKWD',
                'union'            => 'UNIONKWD',
                'public'           => 'PUBLICKWD',
                'private'          => 'PRIVATEKWD',
                'protected'        => 'PROTECTEDKWD',
                'signals'          => 'SIGNALSKWD',
                'slots'            => 'SLOTSKWD',
                'namespace'        => 'NAMESPACEKWD'
            ),
            'bracket' =>    array(
                '(' => 'LEFTROUNDBRACKET',
                ')' => 'RIGHTROUNDBRACKET',
                '[' => 'LEFTSQUAREBRACKET',
                ']' => 'RIGHTSQUAREBRACKET',
                '{'   => 'LEFTFIGUREBRACKET',
                '}'   => 'RIGHTFIGUREBRACKET',
            ),
            'character' =>  array( $any => 'CHARACTER'),
            'string'    =>  array( $any => 'STRING'),
            'comma'     =>  array( $any => 'COMMA' ),
            'preprocessor' => array(
                '##' => 'PREPROCESSOR_CONCAT',
                '#define'  => 'PREPROCESSOR_DEFINE',
                '#' => 'PREPROCESSOR_STRINGIFY',
                '#if' => 'PREPROCESSOR_IF',
                '#ifdef' => 'PREPROCESSOR_IFDEF',
                '#elif'  => 'PREPROCESSOR_ELIF',
                '#else'  => 'PREPROCESSOR_ELIF',
                '#endif' => 'PREPROCESSOR_ENDIF',
                $any => 'PREPROCESSOR_INCLUDE'
            ),
        );
        return $table;
    }

    /**
     * Tests, whether token is overloaded operator declaration
     * @param string $token token value
     * @return bool
     */
    public function is_operator_overload_declaration($token) {
        $ops = array(
            'operator+', 'operator-', 'operator*', 'operator/', 'operator\\', 'operator~=', 'operator&', 'operator|',
            'operator~','operator->','operator+=','operator-=','operator*=','operator/=','operator++','operator--',
            'operator%','operator%=','operator<<=','operator>>=','operator&=','operator|=','operator!=','operator!',
            'operator&&=','operator||=','operator=','operator++','operator--','operator<','operator>','operator<=',
            'operator>=','operator==','operator!=','operator&&','operator||','operator>>','operator<<','operator^',
            'operator^=','operator==',
            'operator.'
        );
        return in_array($token, $ops);
    }

    /**
     * Maps token from lexer to parser, returning name of constant to parser
     * @param block_formal_langs_token_base  $token a token name
     * @return string mapped constants name
     */
    public function map($token) {
		if ($token->type() == 'keyword') {
            if ($this->is_operator_overload_declaration($token->value())) {
				return 'OPERATOROVERLOADDECLARATION';
            }
        }
        if ($token->type() == 'identifier') {
			if ($this->is_outer_constructor_name($token->value())) {
				return 'OUTER_CONSTRUCTOR_NAME';
			}
            if ($this->is_type($token->value())) {
				//echo $token->value() . " is type \n";
				return 'TYPENAME';
            }			
        }
		//echo $token->value() . " is not a type \n";
        return parent::map($token);
    }

}


class block_formal_langs_language_cpp_parseable_language extends block_formal_langs_predefined_language
{
    /**
     * Constructs a language
     */
    public function __construct() {
        parent::__construct(null,null);
    }

    /**
     * Returns name for language
     * @return string
     */
    public function name() {
        return 'cpp_parseable';
    }

    /**
     * Returns name for language
     * @return string
     */
    public function lexem_name() {
        return get_string('lexeme', 'block_formal_langs');
    }
    /**
     * Returns name for lexer class
     * @return string
     */
    protected function lexername() {
        return 'block_formal_langs_predefined_cpp_language_lexer_raw';
    }

    /**
     * Returns name for parser class
     * @return string
     */
    protected function parsername() {
        return 'block_formal_langs_lexer_cpp_mapper';
    }

    /**
     * Returns true if this language has parser enabled
     * @return boolean
     */
    public function could_parse() {
        return true;
    }
}
