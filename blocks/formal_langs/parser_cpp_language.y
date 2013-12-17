%name lock_formal_langs_parser_cpp_language
%declare_class {class block_formal_langs_parser_cpp_language}
%include_class {
    // Root of the Abstract Syntax Tree (AST).
    public $root;
	// Current id for language
	public $currentid;
	// A mapper for parser
	public $mapper;
	
	protected function create_node($type, $children) {
		$result = new block_formal_langs_ast_node_base($type, null, $this->currentid, false);
		$this->currentid = $this->currentid + 1;
		$result->set_childs($children);
		return $result;
	}
	
	public function perform_repeat_lookup($oldmajor, $token) {
		if (is_object($token) == false)
		{
			return $oldmajor;
		}
		if ($token->type() == 'identifier')
		{
			return $this->mapper->major_code_for($token);
		}
		return $oldmajor;
	}
	
}

%syntax_error {
    echo "Syntax Error on line " . $this->lex->line . ": token '" . 
        $this->lex->value . "' while parsing rule:\n";
    echo "Stack: ";
	foreach ($this->yystack as $entry) {
        echo self::$yyTokenName[$entry->major] . "\n";
    }
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = self::$yyTokenName[$token];
    }
	throw new Exception(implode(',', $expect));
    //throw new Exception('Unexpected ' . $this->tokenName($yymajor) . '(' . $TOKEN
    //    . '), expected one of: ' . implode(',', $expect)) . "\n";
}

%nonassoc THENKWD .
%left    ELSEKWD.
%left    LOGICALOR.
%left    LOGICALAND.
%left    BINARYXOR.
%left    BINARYAND.
%left    NOTEQUAL EQUAL.
%right   UINDIRECTION UADRESS.
%left    NAMESPACE_RESOLVE.
%nonassoc UMINUS UPLUS UBRACKET.
%left    TYPEUNARY.
%nonassoc MACROPARAMETERPRIORITY.

program(R) ::= stmt_list(A) .  {
	$result = $this->create_node('program', array( A ));
	$this->root = $result;
	R = $result;
}


stmt_list(R) ::= stmt_list(A) stmt_or_defined_macro(B) . {
	A->add_child(B);
	R = A;
}

stmt_list(R) ::= stmt_or_defined_macro(A) . {
	$result = $this->create_node('stmt_list', array(A));
	R = $result;
}

stmt_or_defined_macro(R) ::= type(A) possible_function_name(B) formal_args_list_with_or_without_const(C) function_body(D) . {
	$result = $this->create_node('function', array(A, B, C, D));
	R = $result;	
}

stmt_or_defined_macro(R) ::= type_with_qualifier(A) possible_function_name(C) formal_args_list_with_or_without_const(D) function_body(E) . {
	$result = $this->create_node('function', array(A, C, D, E));
	R = $result;	
}

stmt_or_defined_macro(R) ::= template_def(A) type_with_qualifier(B) possible_function_name(C) formal_args_list_with_or_without_const(D) function_body(E) . {
	$result = $this->create_node('function', array(A, B, C, D, E));
	R = $result;	
}

stmt_or_defined_macro(R) ::= template_def(A) type(B) possible_function_name(C) formal_args_list_with_or_without_const(D) function_body(E) . {
	$result = $this->create_node('function', array(A, B, C, D, E));
	R = $result;	
}

template_def(R) ::= TEMPLATEKWD(A) LESSER(B) GREATER(C) . {
	$result = $this->create_node('template_def', array(A, B, C));
	R = $result;	
}

template_def(R) ::= TEMPLATEKWD(A) LESSER(B) template_spec_list(C) GREATER(D) . {
	$result = $this->create_node('template_def', array(A, B, C, D));
	R = $result;	
}

template_spec_list(R) ::= template_spec_list(A) COMMA(B) template_spec(C) . {
	A->add_child(B);
	A->add_child(C);
	R = A;
}

template_spec_list(R) ::= template_spec(A) . {
	$result = $this->create_node('template_spec', array(A));
	R = $result;	
}

template_spec(R) ::= template_typename(A)  IDENTIFIER(B) . {
	$this->mapper->introduce_type(B->value());
	$result = $this->create_node('template_spec', array(A, B));
	R = $result;	
}


template_typename(R) ::= TYPENAMEKWD(A) . {
	R = A;
}

template_typename(R) ::= CLASSKWD(A) . {
	R = A;
}

template_typename(R) ::= STRUCTKWD(A) . {
	R = A;
}

template_typename(R) ::= ENUMKWD(A) . {
	R = A;
}


function_body(R) ::= LEFTFIGUREBRACKET(A) stmt_list(B) RIGHTFIGUREBRACKET(C) . {
	$result = $this->create_node('function_body', array(A, B, C));
	R = $result;	
}

function_body(R) ::= LEFTFIGUREBRACKET(A)  RIGHTFIGUREBRACKET(C) . {
	$result = $this->create_node('function_body', array(A, B));
	R = $result;	
}

function_body(R) ::= SEMICOLON(A) . {
	R = A;
}


possible_function_name(R) ::= primitive_or_complex_type(A) . {
	R = A ;
}

possible_function_name(R) ::= IDENTIFIER(A) . {
	R = A ;
}

possible_function_name(R) ::= OPERATOROVERLOADDECLARATION(A) . {
	R = A;
}

formal_args_list_with_or_without_const(R) ::= formal_args_list(A) . {
	R = A;
}
formal_args_list_with_or_without_const(R) ::= formal_args_list(A) CONSTKWD(B) . {
	$result = $this->create_node('formal_args_with_const', array(A, B));
	R = $result;	
}


formal_args_list(R) ::= LEFTROUNDBRACKET(A) RIGHTROUNDBRACKET(B) . {
	$result = $this->create_node('args_list', array(A, B));
	R = $result;	
}

formal_args_list(R) ::= LEFTROUNDBRACKET(A) arg_list(B) RIGHTROUNDBRACKET(C) . {
	$result = $this->create_node('args_list', array(A, B, C));
	R = $result;	
}

arg_list(R) ::= arg(A) . {
	$result = $this->create_node('arg_list', array(A));
	R = $result;	
}

arg_list(R) ::= arg_list(A) COMMA(B) arg(C) . {
	A->add_child(B);
	A->add_child(C);	
	R = A;
}

arg(R) ::= type(A) IDENTIFIER(B) . {
	$result = $this->create_node('arg', array(A, B));
	R = $result;	
}

stmt_or_defined_macro(R) ::=  preprocessor_cond(A) stmt_list(B) PREPROCESSOR_ENDIF(C).  {
	$result = $this->create_node('preprocessor_ifdef', array(A, B, C));
	R = $result;
}

stmt_or_defined_macro(R) ::=  preprocessor_cond(A) stmt_list(B) preprocessor_else_clauses(C) PREPROCESSOR_ENDIF(D).  {
	$result = $this->create_node('preprocessor_ifdef', array(A, B, C, D));
	R = $result;
}

preprocessor_else_clauses(R) ::= preprocessor_elif_list(A) preprocessor_else(B) . {
	$result = $this->create_node('preprocessor_else_clauses', array(A, B));
	R = $result;
} 

preprocessor_else_clauses(R) ::= preprocessor_elif_list(A) . {
	R = A;
} 

preprocessor_else_clauses(R) ::= preprocessor_else(A) . {
	R = A;
} 

preprocessor_elif_list(R) ::= preprocessor_elif_list(A) preprocessor_elif(B) . {
	A->add_child(B);
	R = A;
}

preprocessor_elif_list(R) ::= preprocessor_elif(A) .  {
	$result = $this->create_node('preprocessor_elif_list', array(A));
	R = $result;
}
 
preprocessor_elif(R) ::= PREPROCESSOR_ELIF(A) stmt_list(B) . {
	$result = $this->create_node('preprocessor_elif', array(A, B));
	R = $result;
}

preprocessor_else(R) ::= PREPROCESSOR_ELSE(A) stmt_list(B) . {
	$result = $this->create_node('preprocessor_else', array(A, B));
	R = $result;
}

preprocessor_cond(R)  ::= PREPROCESSOR_IFDEF(A) IDENTIFIER(B) . {
	$result = $this->create_node('preprocessor_cond', array(A, B));
	R = $result;
}

preprocessor_cond(R)  ::= PREPROCESSOR_IFDEF(A) CUSTOMTYPENAME(B) . {
	$result = $this->create_node('preprocessor_cond', array(A, B));
	R = $result;
}

preprocessor_cond(R) ::= PREPROCESSOR_IF(A) . {
	$result = $this->create_node('preprocessor_cond', array(A));
	R = $result;
}


stmt_or_defined_macro(R) ::= PREPROCESSOR_DEFINE(A) . {
	$result = $this->create_node('define', array(A, B));
	R = $result;	
}


stmt_or_defined_macro(R) ::= stmt(A) . {
	R = A;	
}

stmt(R) ::= PREPROCESSOR_INCLUDE(A) . {
	R = A;
}

stmt(R) ::= WHILEKWD(A)
			LEFTROUNDBRACKET(B)
			expr_prec_17(C)		
			RIGHTROUNDBRACKET(D)
			stmt(E) 
			. {
	$result = $this->create_node('while', array(A, B, C, D, E));
	R = $result;			
}

stmt(R) ::= DOKWD(A)
            stmt(B)
			WHILEKWD(C)
			LEFTROUNDBRACKET(D)
			expr_prec_17(E)		
			RIGHTROUNDBRACKET(F)
			SEMICOLON(G)
			. {
	$result = $this->create_node('do_while', array(A, B, C, D, E, F, G));
	R = $result;			
}
			
			
stmt(R) ::= FORKWD(A) 
			LEFTROUNDBRACKET(B) 
			expr_prec_17(C) SEMICOLON(D)  
			expr_prec_17(E) SEMICOLON(F) 
			expr_prec_17(G)
			RIGHTROUNDBRACKET(H)
			stmt(I)
			. {
	$result = $this->create_node('for', array(A, B, C, D, E, F, G, H, I));
	R = $result;
}			
			

stmt(R) ::= RETURNKWD(A) expr_prec_17(B) SEMICOLON(C) . {
	$result = $this->create_node('return', array(A, B, C));
	R = $result;
}

stmt(R) ::= CONTINUEKWD(A) SEMICOLON(B) . {
	$result = $this->create_node('continue', array(A, B));
	R = $result;
}

stmt(R) ::= GOTOKWD(A) IDENTIFIER(B) COLON(C) . {
	$result = $this->create_node('goto', array(A, B, C));
	R = $result;
}

stmt(R) ::= GOTOKWD(A) CUSTOMTYPENAME(B) COLON(C) . {
	$result = $this->create_node('goto', array(A, B, C));
	R = $result;
}

stmt(R) ::= IDENTIFIER(A) COLON(B) . {
	$result = $this->create_node('goto_label', array(A, B));
	R = $result;
}

stmt(R) ::= try_catch(A) . {
	R = A;
}

try_catch(R) ::= try(A) catch_list(B) . {
	$result = $this->create_node('try_catch', array(A, B));
	R = $result;
}

try(R) ::= TRYKWD(A) LEFTFIGUREBRACKET(B) RIGHTFIGUREBRACKET(C) . {
	$result = $this->create_node('try', array(A, B, C));
	R = $result;
}

try(R) ::= TRYKWD(A) LEFTFIGUREBRACKET(B) stmt_list(C) RIGHTFIGUREBRACKET(D) . {
	$result = $this->create_node('try', array(A, B, C, D));
	R = $result;
}

catch_list(R) ::= catch(A) . {
	R = $this->create_node('catch', array(A));
}

catch_list(R) ::= catch_list(A) catch(B) . {
	A->add_child(B);
	R = A;
}

catch(R) ::=  CATCHKWD(A) LEFTROUNDBRACKET(B) expr_prec_17_or_ellipsis(C) RIGHTROUNDBRACKET(D) LEFTFIGUREBRACKET(E) RIGHTFIGUREBRACKET(F) . {
	$result = $this->create_node('catch', array(A, B, C, D, E, F));
	R = $result;
}

catch(R) ::=  CATCHKWD(A) LEFTROUNDBRACKET(B) expr_prec_17_or_ellipsis(C) RIGHTROUNDBRACKET(D) LEFTFIGUREBRACKET(E) stmt_list(F) RIGHTFIGUREBRACKET(G) . {
	$result = $this->create_node('catch', array(A, B, C, D, E, F, G));
	R = $result;
}

expr_prec_17_or_ellipsis(R) ::= expr_prec_17(A) . {
	R = A;
}

expr_prec_17_or_ellipsis(R) ::= ELLIPSIS(A) . {
	R = A;
}

 
stmt(R) ::= switch_stmt(A) .  {
	R = A;
}

switch_stmt(R) ::= SWITCHKWD(A) LEFTROUNDBRACKET(B) expr_prec_17(C) RIGHTROUNDBRACKET(D) LEFTFIGUREBRACKET(E) RIGHTFIGUREBRACKET(F) . {
	$result = $this->create_node('stmt_list', array(A, B, C, D, E, F));
	R = $result;
}

switch_stmt(R) ::= SWITCHKWD(A) LEFTROUNDBRACKET(B) expr_prec_17(C) RIGHTROUNDBRACKET(D) LEFTFIGUREBRACKET(E) switch_case_list(F) RIGHTFIGUREBRACKET(G) . {
	$result = $this->create_node('stmt_list', array(A, B, C, D, E, F, G));
	R = $result;
}

switch_case_list(R) ::= case(A) . {
	R = $this->create_node('switch_case_list', array(A));
}

switch_case_list(R) ::= switch_case_list(A) case(B) . {
	A->add_child(B);
	R = A;
}

case(R) ::= CASEKWD(A) expr_atom(B) COLON(C) stmt_list(D) . {
	$result = $this->create_node('case', array(A, B, C, D));
	R = $result;
}

case(R) ::= DEFAULTKWD(A) COLON(B) stmt_list(C) . {
	$result = $this->create_node('case', array(A, B, C));
	R = $result;
}


stmt(R) ::= if_then_else(A) .  {
	R = A;
}

if_then_else(R) ::=  if_then(A) . [THENKWD] {
	R = A;
}

if_then_else(R) ::=  if_then(A) ELSEKWD(B) stmt(C).  {
	A->add_child(B);
	A->add_child(C);
	R = A;
}

if_then(R) ::= IFKWD(A) LEFTROUNDBRACKET(B) expr_prec_17(C) RIGHTROUNDBRACKET(D) stmt(E) .  {
	$result = $this->create_node('if_then', array(A, B, C, D, E));
	R = $result;
}

stmt(R) ::= LEFTFIGUREBRACKET(A) stmt_list(B) RIGHTFIGUREBRACKET(C) . {
	$result = $this->create_node('stmt', array(A, B, C));
	R = $result;
}

stmt(R) ::=  TYPEDEF(A) type(B) IDENTIFIER(C) SEMICOLON(D) . { 
	$result = $this->create_node('stmt', array(A, B, C, D));
	$this->mapper->introduce_type(C->value());
	R = $result;
}


stmt(R) ::= BREAKKWD(A) SEMICOLON(B) . {
	$result = $this->create_node('stmt', array(A, B));
	R = $result;
}

stmt(R) ::= expr_prec_17(A) SEMICOLON(B) . {
	$result = $this->create_node('stmt', array(A, B));
	R = $result;
}

expr_prec_17(R) ::= NEWKWD(A) expr_prec_16(B)  . {
	$result = $this->create_node('expr_prec_17', array( A, B ));
	R = $result;
} 

expr_prec_17(R) ::= DELETE(A) LEFTSQUAREBRACKET(B)  RIGHTSQUAREBRACKET(C)  expr_prec_16(D) . {
	$result = $this->create_node('expr_prec_17', array( A, B, C, D ));
	R = $result;
} 

expr_prec_17(R) ::= DELETE(A) expr_prec_16(B) . {
	$result = $this->create_node('expr_prec_17', array( A, B ));
	R = $result;
} 

expr_prec_17(R) ::= expr_prec_16(A) . {
	R = A;
}

expr_prec_17(R) ::= type(A) expr_atom(B) ASSIGN(C) expr_prec_9(D) . {
	$result = $this->create_node('expr_prec_17', array( A, B, C, D ));
	R = $result;
}

expr_prec_17(R) ::= type(A) primitive_or_complex_type(B) ASSIGN(C) expr_prec_9(D) . {
	$result = $this->create_node('expr_prec_17', array( A, B, C, D ));
	R = $result;
}

expr_prec_17(R) ::= type(A) expr_atom(B) . {
	$result = $this->create_node('expr_prec_17', array( A, B ));
	R = $result;
}

expr_prec_17(R) ::= type(A) primitive_or_complex_type(B)  . {
	$result = $this->create_node('expr_prec_17', array( A, B ));
	R = $result;
}

varqualifier(R) ::= STATICKWD(A) .  {
	R = A;
}

varqualifier(R) ::= EXTERNKWD(A) .  {
	R = A;
}

varqualifier(R) ::= REGISTERKWD(A) .  {
	R = A;
}

varqualifier(R) ::= VOLATILEKWD(A) .  {
	R = A;
}

varqualifier(R) ::= FRIENDKWD(A) .  {
	R = A;
}




expr_prec_17(R) ::= type_with_qualifier(A) expr_atom(C) ASSIGN(D) expr_prec_9(E) . {
	$result = $this->create_node('expr_prec_17', array( A,  C, D, E ));
	R = $result;
}

expr_prec_17(R) ::= type_with_qualifier(A)  primitive_or_complex_type(C) ASSIGN(D) expr_prec_9(E) . {
	$result = $this->create_node('expr_prec_17', array( A, C, D, E ));
	R = $result;
}

expr_prec_17(R) ::= type_with_qualifier(A)  expr_atom(C)  . {
	$result = $this->create_node('expr_prec_17', array( A, C ));
	R = $result;
}

expr_prec_17(R) ::= type_with_qualifier(A) primitive_or_complex_type(C) . {
	$result = $this->create_node('expr_prec_17', array( A, C ));
	R = $result;
}

type_with_qualifier(R) ::= varqualifier(A) type(B) . {
	$result = $this->create_node('type_with_qualifier', array( A, B ));
	R = $result;
}

expr_prec_17(R) ::= expr_prec_17(A) COMMA(B)  expr_prec_16(C) . {
	A->add_child(B);
	A->add_child(C);
	R = A;
}


expr_prec_16(R) ::= expr_prec_10(A) . {
	R = A;
}

expr_prec_10(R) ::= expr_prec_9(A) BINARYXOR_ASSIGN(B) expr_prec_10(C) . {
	$result = $this->create_node('expr_prec_10', array( A, B, C ));
	R = $result;
}

expr_prec_10(R) ::= expr_prec_9(A) BINARYOR_ASSIGN(B) expr_prec_10(C) . {
	$result = $this->create_node('expr_prec_10', array( A, B, C ));
	R = $result;
}

expr_prec_10(R) ::= expr_prec_9(A) BINARYAND_ASSIGN(B) expr_prec_10(C) . {
	$result = $this->create_node('expr_prec_10', array( A, B, C ));
	R = $result;
}

expr_prec_10(R) ::= expr_prec_9(A) RIGHTSHIFT_ASSIGN(B) expr_prec_10(C) . {
	$result = $this->create_node('expr_prec_10', array( A, B, C ));
	R = $result;
}

expr_prec_10(R) ::= expr_prec_9(A) LEFTSHIFT_ASSIGN(B) expr_prec_10(C) . {
	$result = $this->create_node('expr_prec_10', array( A, B, C ));
	R = $result;
}

expr_prec_10(R) ::= expr_prec_9(A) MODULO_ASSIGN(B) expr_prec_10(C) . {
	$result = $this->create_node('expr_prec_10', array( A, B, C ));
	R = $result;
}

expr_prec_10(R) ::= expr_prec_9(A) DIVISION_ASSIGN(B) expr_prec_10(C) . {
	$result = $this->create_node('expr_prec_10', array( A, B, C ));
	R = $result;
}

expr_prec_10(R) ::= expr_prec_9(A) MULTIPLY_ASSIGN(B) expr_prec_10(C) . {
	$result = $this->create_node('expr_prec_10', array( A, B, C ));
	R = $result;
}

expr_prec_10(R) ::= expr_prec_9(A) PLUS_ASSIGN(B) expr_prec_10(C) . {
	$result = $this->create_node('expr_prec_10', array( A, B, C ));
	R = $result;
}

expr_prec_10(R) ::= expr_prec_9(A) MINUS_ASSIGN(B) expr_prec_10(C) . {
	$result = $this->create_node('expr_prec_10', array( A, B, C ));
	R = $result;
}

expr_prec_10(R) ::= expr_prec_9(A) ASSIGN(B) expr_prec_10(C) . {
	$result = $this->create_node('expr_prec_10', array( A, B, C ));
	R = $result;
}


expr_prec_10(R) ::= expr_prec_9(A) . {
	R = A;
}

expr_prec_9(R) ::= expr_prec_9(A) LOGICALOR(B) expr_prec_8(C) . {
	$result = $this->create_node('expr_prec_9', array( A, B, C ));
	R = $result;
}

expr_prec_9(R) ::= expr_prec_9(A) LOGICALAND(B) expr_prec_8(C) . {
	$result = $this->create_node('expr_prec_9', array( A, B, C ));
	R = $result;
}

expr_prec_9(R) ::= expr_prec_9(A) BINARYXOR(B) expr_prec_8(C) . {
	$result = $this->create_node('expr_prec_9', array( A, B, C ));
	R = $result;
}

expr_prec_9(R) ::= expr_prec_9(A) BINARYAND(B) expr_prec_8(C) . {
	$result = $this->create_node('expr_prec_9', array( A, B, C ));
	R = $result;
}

expr_prec_9(R) ::= expr_prec_9(A) NOT_EQUAL(B) expr_prec_8(C) . {
	$result = $this->create_node('expr_prec_9', array( A, B, C ));
	R = $result;
}

expr_prec_9(R) ::= expr_prec_9(A) EQUAL(B) expr_prec_8(C) . {
	$result = $this->create_node('expr_prec_9', array( A, B, C ));
	R = $result;
}

expr_prec_9(R) ::= expr_prec_8(A) . {
	R = A;
}

expr_prec_8(R) ::= expr_prec_8(A) LESSER_OR_EQUAL(B) expr_prec_7(C) . {
	$result = $this->create_node('expr_prec_8', array( A, B, C ));
	R = $result;
}

expr_prec_8(R) ::= expr_prec_8(A) GREATER_OR_EQUAL(B) expr_prec_7(C) . {
	$result = $this->create_node('expr_prec_8', array( A, B, C ));
	R = $result;
}

expr_prec_8(R) ::= expr_prec_8(A) GREATER(B) expr_prec_7(C) . {
	$result = $this->create_node('expr_prec_8', array( A, B, C ));
	R = $result;
}

expr_prec_8(R) ::= expr_prec_8(A) LESSER(B) expr_prec_7(C) . {
	$result = $this->create_node('expr_prec_8', array( A, B, C ));
	R = $result;
}

expr_prec_8(R) ::= expr_prec_7(A) . {
	R = A;
}

expr_prec_7(R) ::= expr_prec_7(A) LEFTSHIFT(B) expr_prec_6(C) . {
	$result = $this->create_node('expr_prec_7', array( A, B, C ));
	R = $result;
}

expr_prec_7(R) ::= expr_prec_7(A) RIGHTSHIFT(B) expr_prec_6(C) . {
	$result = $this->create_node('expr_prec_7', array( A, B, C ));
	R = $result;
}

expr_prec_7(R) ::= expr_prec_6(A) . {
	R = A;
}

expr_prec_6(R) ::= expr_prec_6(A) MINUS(B) expr_prec_5(C) . {
	$result = $this->create_node('expr_prec_6', array( A, B, C ));
	R = $result;
}

expr_prec_6(R) ::= expr_prec_6(A) PLUS(B) expr_prec_5(C) . {
	$result = $this->create_node('expr_prec_6', array( A, B, C ));
	R = $result;
}

expr_prec_6(R) ::= expr_prec_5(A) . {
	R = A;
}

expr_prec_5(R) ::= expr_prec_5(A)  MODULOSIGN(B) expr_prec_4(C) . {
	$result = $this->create_node('expr_prec_5', array( A, B, C ));
	R = $result;
}

expr_prec_5(R) ::= expr_prec_5(A)  DIVISION(B) expr_prec_4(C) . {
	$result = $this->create_node('expr_prec_5', array( A, B, C ));
	R = $result;
}

expr_prec_5(R) ::= expr_prec_5(A)  MULTIPLY(B) expr_prec_4(C) . {
	$result = $this->create_node('expr_prec_5', array( A, B, C ));
	R = $result;
}


expr_prec_5(R) ::= expr_prec_4(A) . {
	R = A;
}

expr_prec_4(R) ::= try_value_access(A) MULTIPLY(B) IDENTIFIER(C) . {
	$result = $this->create_node('expr_prec_4', A->childs());
	$result->add_child(B);
	$result->add_child(C);
	R = $result;
}

expr_prec_4(R) ::= try_pointer_access(A) MULTIPLY(B) IDENTIFIER(C) . {
	$result = $this->create_node('expr_prec_4', A->childs());
	$result->add_child(B);
	$result->add_child(C);
	R = $result;
}


expr_prec_4(R) ::= expr_prec_3(A) . {
	R = A;
}

expr_prec_3(R) ::= AMPERSAND(A) expr_prec_3(B) . [UADRESS]  {
	$result = $this->create_node('expr_prec_3', array( A, B ));
	R = $result;
}

expr_prec_3(R) ::= MULTIPLY(A) expr_prec_3(B) . [UINDIRECTION]  {
	$result = $this->create_node('expr_prec_3', array( A, B ));
	R = $result;
}

expr_prec_3(R) ::= typecast(A) expr_prec_3(B) . {
	$result = $this->create_node('expr_prec_3', array( A, B));
	R = $result;
}

expr_prec_3(R) ::= LOGICALNOT(A) expr_prec_3(B) .  {
	$result = $this->create_node('expr_prec_3', array( A, B));
	R = $result;
}

expr_prec_3(R) ::= BINARYNOT(A) expr_prec_3(B) . {
	$result = $this->create_node('expr_prec_3', array( A, B));
	R = $result;
}

expr_prec_3(R) ::= MINUS(A) expr_prec_2(B)   . [UMINUS] {
	$result = $this->create_node('expr_prec_3', array( A, B));
	R = $result;
}

expr_prec_3(R) ::= PLUS(A) expr_prec_2(B)   . [UPLUS] {
	$result = $this->create_node('expr_prec_3', array( A, B));
	R = $result;
}

expr_prec_3(R) ::= DECREMENT(A) expr_prec_3(B)   . {
	$result = $this->create_node('expr_prec_3', array( A, B));
	R = $result;
}

expr_prec_3(R) ::= INCREMENT(A) expr_prec_3(B)   . {
	$result = $this->create_node('expr_prec_3', array( A, B));
	R = $result;
}

expr_prec_3(R) ::= expr_prec_2(A) . {
	R = A;
}

expr_prec_2(R) ::= try_value_access(A) IDENTIFIER(B) . {
	$result = $this->create_node('expr_prec_2', array( A , B) );
	R = $result;
}

expr_prec_2(R) ::= try_pointer_access(A) IDENTIFIER(B) . {
	$result = $this->create_node('expr_prec_2', array( A , B) );
	R = $result;
}

try_value_access(R) ::= expr_prec_2(A) DOT(B) . {
	$result = $this->create_node('try_value_access', array( A , B) );
	R = $result;
}

try_pointer_access(R) ::= expr_prec_2(A) RIGHTARROW(B) . {
	$result = $this->create_node('try_pointer_access', array( A , B) );
	R = $result;
}

cpp_style_cast(R) ::= CONST_CAST(A)  LESSER(B) type(C) GREATER(D) . {
	$result = $this->create_node('cpp_style_cast', array(A, B, C, D));
	R = $result;
}

cpp_style_cast(R) ::= STATIC_CAST(A)  LESSER(B) type(C) GREATER(D) . {
	$result = $this->create_node('cpp_style_cast', array(A, B, C, D));
	R = $result;
}

cpp_style_cast(R) ::= DYNAMIC_CAST(A)  LESSER(B) type(C) GREATER(D) . {
	$result = $this->create_node('cpp_style_cast', array(A, B, C, D));
	R = $result;
}

cpp_style_cast(R) ::= REINTERPRET_CAST(A)  LESSER(B) type(C) GREATER(D) . {
	$result = $this->create_node('cpp_style_cast', array(A, B, C, D));
	R = $result;
}


expr_prec_2(R) ::= cpp_style_cast(A)  LEFTROUNDBRACKET(B) expr_prec_17(C)  RIGHTROUNDBRACKET(D) . [UBRACKET] {
	$result = $this->create_node('expr_prec_2', array( A, B, C, D));
	R = $result;
}

expr_prec_2(R) ::= expr_prec_2(A)  LEFTSQUAREBRACKET(B) expr_prec_16(C)  RIGHTSQUAREBRACKET(D) . {
	$result = $this->create_node('expr_prec_2', array( A, B, C, D));
	R = $result;
}

expr_prec_2(R) ::= expr_prec_2(A)  LEFTROUNDBRACKET(B) expr_prec_17(C)  RIGHTROUNDBRACKET(D) . [UBRACKET] {
	$result = $this->create_node('expr_prec_2', array( A, B, C, D));
	R = $result;
}

expr_prec_2(R) ::= expr_prec_2(A)  INCREMENT(B) . {
	$result = $this->create_node('expr_prec_2', array( A, B));
	R = $result;
}

expr_prec_2(R) ::= expr_prec_2(A)  DECREMENT(B) . {
	$result = $this->create_node('expr_prec_2', array( A, B));
	R = $result;
}

expr_prec_2(R) ::= expr_atom(A) . {
	R = A;
}

expr_atom(R) ::= NUMERIC(A) . {
	R = A;
}

expr_atom(R) ::= IDENTIFIER(A) . {
	R = A;
}

expr_atom(R) ::= CHARACTER(A) . {
	R = A;
}

expr_atom(R) ::= STRING(A) . {
	R = A;
}

expr_atom(R) ::= LEFTROUNDBRACKET(A) expr_prec_17(B) RIGHTROUNDBRACKET(C) . {
	R = A;
}


expr_atom(R) ::= PREPROCESSOR_STRINGIFY(A) IDENTIFIER(B) . {
	R =  $this->create_node('stringify', array( A, B));
}


expr_atom(R) ::= expr_atom(A) PREPROCESSOR_CONCAT(B) IDENTIFIER(C) . {
	R =  $this->create_node('concat', array( A, B, C));
}

typecast(R) ::= LEFTROUNDBRACKET(A)  type(B) RIGHTROUNDBRACKET(C) . {
	$result = $this->create_node('typecast', array( A, B, C ));
	R = $result;
}


/* TYPE DEFINITIONS */

type_list(R) ::= type(A) . 
{
	R = A;
}

type_list(R) ::= type_list(A) COMMA(B) type(C) . 
{
	R = $this->create_node('type_list', array( A, B, C ) );
}

type(R) ::= CONSTKWD(A) non_const_type(B) . {
	$result = $this->create_node('type', array( A, B ));
	R = $result;	
}

type(R) ::= non_const_type(A) . {
	R = A;
}

non_const_type(R) ::= non_const_type(A) MULTIPLY(B) . [TYPEUNARY] {
	$result = $this->create_node('type', array( A, B ));
	R = $result;	
}

non_const_type(R) ::= non_const_type(A) CONSTKWD(B) MULTIPLY(C) . [TYPEUNARY] {
	$result = $this->create_node('type', array( A, B, C ));
	R = $result;	
}

non_const_type(R) ::= non_const_type(A) AMPERSAND(B) . [TYPEUNARY] {
	$result = $this->create_node('type', array( A, B ));
	R = $result;	
}

non_const_type(R) ::= builtintype(A) . {
	R = A;
}

non_const_type(R) ::= primitive_or_complex_type(A) . {
	R = A;
}

primitive_or_complex_type(R) ::= CUSTOMTYPENAME(A) . {
	R = A;
}

primitive_or_complex_type(R) ::= CUSTOMTYPENAME(A) LESSER(B) GREATER(C) .  {
	$result = $this->create_node('primitive_or_complex_type', array( A, B, C ));
	R = $result;
}

primitive_or_complex_type(R) ::= CUSTOMTYPENAME(A) LESSER(B) type_list(C) GREATER(D) .  {
	$result = $this->create_node('primitive_or_complex_type', array( A, B, C, D ));
	R = $result;
}


primitive_or_complex_type(R) ::= primitive_or_complex_type(A)  NAMESPACE_RESOLVE(B) IDENTIFIER(C)  . {
	A->add_child(B);
	A->add_child(C);
	R = A;
}
primitive_or_complex_type(R) ::= primitive_or_complex_type(A)  NAMESPACE_RESOLVE(B) CUSTOMTYPENAME(C)  . {
	A->add_child(B);
	A->add_child(C);
	R = A;
}

primitive_or_complex_type(R) ::= primitive_or_complex_type(A)  NAMESPACE_RESOLVE(B) CUSTOMTYPENAME(C) LESSER(D) GREATER(E) . {
	A->add_child(B);
	A->add_child(C);
	A->add_child(D);
	A->add_child(E);
	R = A;
}

primitive_or_complex_type(R) ::= primitive_or_complex_type(A)  NAMESPACE_RESOLVE(B) CUSTOMTYPENAME(C) LESSER(D) type_list(E) GREATER(F) . {
	A->add_child(B);
	A->add_child(C);
	A->add_child(D);
	A->add_child(E);
	A->add_child(F);
	
	R = A;
}


builtintype(R) ::= SIGNED(A) TYPENAME(B) . {
	$result = $this->create_node('builtintype', array( A, B ));
	R = $result;
}

builtintype(R) ::= UNSIGNED(A) TYPENAME(B) . {
	$result = $this->create_node('builtintype', array( A, B ));
	R = $result;
}

builtintype(R) ::= LONG(A) TYPENAME(B) . {
	$result = $this->create_node('builtintype', array( A, B ));
	R = $result;
}

builtintype(R) ::= TYPENAME(A) . {
	R = A;
}