<?
global $CFG;

define('MOODLE_INTERNAL', 1);

$CFG = new stdClass();
$CFG->dirroot = dirname(dirname(dirname(__FILE__)));
$CFG->libdir = $CFG->dirroot . '/lib';

require_once($CFG->dirroot.'/lib/classes/text.php');
require_once($CFG->dirroot .'/blocks/formal_langs/language_cpp_language.php');
require_once($CFG->dirroot .'/blocks/formal_langs/parser_cpp_language.php');


abstract class block_formal_langs_lexer_parser_mapper {
	/*! A stack as a collection of frames with types and other conflict-solving data
	 */
	protected $stack;
	/*! Pushes a stack frame to a mapper
	 */
	public function push_stack_frame()
	{
		$this->stack[] = array();
	}
	
	public function pop_stack_frame()
	{
		unset($this->stack[count($this->stack) - 1]);
	}
	
	public function __construct()
	{
		$stack = array();
		$this->push_stack_frame();
	}
	/** Returns name of lexer class
		@return name of lexer class
	 */
	public abstract function lexername();
	/** Returns name of parser class
		@return name of parser class
	 */
	public abstract function parsername(); 	
	/*! Returns mappings of lexer tokens to parser tokens
		@param string $any name for any value matching
		@return array mapping
	 */
	public abstract function maptable($any);
	/** Maps token from lexer to parser, returning name of constant to parser
		@param block_formal_langs_token_base  $token a token name
	 */
	public function map($token) {
		$result = 0;
		$any = '===ANY===';
		$table = $this->maptable($any);
		if (array_key_exists($token->type(), $table)) {
			$maps = $table[$token->type()];
			$value = (string)$token->value();
			if (array_key_exists($value, $maps)) {
				$result = $maps[$value];
			} else {
				$result = $maps[$any];
			}
		}
		return $result;
	}
	
	/*! Parses new string for text parser
		@param string $string string parser
	 */
	public function parse($string)  {
		$lexername = $this->lexername();
		$lexer = new $lexername;
		$processedstring = $lexer->create_from_string($string);
		if (count($processedstring->stream->errors) == 0)
		{
			$parsername = $this->parsername();
			$parser = new $parsername();
			$parser->mapper = $this;		
			$parser->repeatlookup = true;
			$processedstring->syntaxtree = null;
			$tokens = $processedstring->stream->tokens;
			if (count($tokens))
			{
				$parser->currentid = count($tokens);
				foreach($tokens as $token) {
					$this->parse_token($token, $parser);
				}
				$parser->doParse(0, null);
				$processedstring->syntaxtree = $parser->root;
			}
		}
		return $processedstring;
	}
	/*! Returns major code for specified token
		@param block_formal_langs_token_base $token a token
		@return code for token
	 */
	public function major_code_for($token) {
		$parsername = $this->parsername();
		$major = $this->map($token);
		$constant = 0;
		if ($major != null) {
			$constant = $parsername . '::' . $major;
			echo $constant . "\r\n";
			$constant = constant($constant);
		}
		return $constant;
	}
	/*! Makes parser parse specific token
		@param block_formal_langs_token_base $token parsed token
		@param mixed $token a token
	 */
	protected function parse_token($token, $parser) {		
		$constant = $this->major_code_for($token);
		$parser->doParse($constant, $token);
	}
	
	
}

class block_formal_langs_lexer_cpp_parser_mapper extends block_formal_langs_lexer_parser_mapper {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/*! Adds new type to a test
		@param[in] $typename a type
	 */
	public function introduce_type($typename)
	{
		$this->stack[count($this->stack) - 1][]= (string)$typename;
	}
	
	
	/*! Returns true, whether mapper is type
		@return type
	 */
	public function is_type($name)
	{
		$result = false;
		$name = (string)$name;
		if (count($this->stack))
		{
			foreach($this->stack as $frame)
			{	
				if (count($frame))
				{
					$result = $result || in_array($name, $frame);
				}
			}
		}
		return $result;
	}
	
	/** Returns name of lexer class
		@return name of lexer class
	 */
	public  function lexername() {
		return 'block_formal_langs_language_cpp_language';
	}
	/** Returns name of parser class
		@return name of parser class
	 */
	public function parsername() {
		return 'block_formal_langs_parser_cpp_language';
	}
	/*! Makes parser parse specific token
		@param block_formal_langs_token_base $token parsed token
		@param mixed $token a token
	 */
	protected function parse_token($token, $parser) {
		if ($token->type() != 'singleline_comment' && $token->type() != 'multiline_comment') {
			parent::parse_token($token, $parser);
		}
	}
	/*! Returns mappings of lexer tokens to parser tokens
		@param string $any name for any value matching
		@return array mapping
	 */
	public function maptable($any) {
		$table = array(
			'identifier' => array( $any => 'IDENTIFIER' ),
			'typename'   => array( $any => 'TYPENAME', 'signed' => 'SIGNED', 'unsigned' => 'UNSIGNED', 'long' => 'LONG'),
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
				'union'            => 'UNIONKWD'
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
							     '#' => 'PREPROCESSOR_CONCAT',
								 '#define'  => 'PREPROCESSOR_DEFINE', 
								 '##' => 'PREPROCESSOR_STRINGIFY',
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
	
	public function is_operator_overload_declaration($token) {
		$ops = array(
			'operator+', 'operator-', 'operator*', 'operator/', 'operator\\', 'operator~=', 'operator&', 'operator|',
			'operator~','operator->','operator+=','operator-=','operator*=','operator/=','operator++','operator--',
			'operator%','operator%=','operator<<=','operator>>=','operator&=','operator|=','operator!=','operator!',
			'operator&&=','operator||=','operator=','operator++','operator--','operator<','operator>','operator<=',
			'operator>=','operator==','operator!=','operator&&','operator||','operator>>','operator<<','operator^','operator^=','operator==',
			'operator.'
		);
		return in_array($token, $ops);
	}
	
	/** Maps token from lexer to parser, returning name of constant to parser
		@param block_formal_langs_token_base  $token a token name
	 */
	public function map($token) {
		if ($token->type() == 'keyword') {
			if ($this->is_operator_overload_declaration($token->value())) {
				return 'OPERATOROVERLOADDECLARATION';
			}
		}
		if ($token->type() == 'identifier') {
			if ($this->is_type($token->value())) {
				return 'CUSTOMTYPENAME';
			}
		}
		return parent::map($token);
	}

}


$mapper = new block_formal_langs_lexer_cpp_parser_mapper();
$result = $mapper->parse('template<typename _A, typename _B> int operator+(int * b, double f) const { k = k + 1; }');

function print_node($node, $paddingcount)
{
	if ($node == null) {
		echo 'No tree!';
		return;
	}
	
	$padding = str_repeat(' ', $paddingcount);
	
	$value = '';
	if (is_a($node, 'block_formal_langs_token_base')) {
		$value = '(' . $node->value() . ')';
	}
	if (!method_exists($node, 'type')) {
		var_dump($node);
	}
	echo $padding . $node->type() . $value . PHP_EOL;
	if (count($node->childs()))  {
		echo $padding . '{' . PHP_EOL;
		foreach($node->childs() as $child) {
			print_node($child, $paddingcount + 1);
		}
		echo $padding . '}' . PHP_EOL;
	}
}
print_node($result->syntaxtree, 0);

