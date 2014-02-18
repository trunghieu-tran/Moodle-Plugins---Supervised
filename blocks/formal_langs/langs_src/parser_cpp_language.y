%name lock_formal_langs_parser_cpp_language
%declare_class {class block_formal_langs_parser_cpp_language}
%include_class {
    // Root of the Abstract Syntax Tree (AST).
    public $root;
	// Current id for language
	public $currentid;
	// A mapper for parser
	public $mapper;
	// Test, whether parsing error occured
	public $error = false;
	
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
    $this->error = true;
    $stack = array();
    foreach($this->yystack as $entry) {
        if ($entry->minor != null) {
            $stack[] = $entry->minor;
        }
    }
     // var_dump(array_map(function($a) { return $a->type() . ' ';  }, $stack));
    if (is_array($this->root)) {
        if (count($this->root)) {
            $this->root = array_merge($this->root, $stack);
        }
        else {
            $this->root  = $stack;
        }
    } else {
        $this->root = $stack;
    }
    /*
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
	*/
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
	$stack = array( $this->create_node('program', array( A ) ) );
	if (is_array($this->root)) {
            if (count($this->root)) {
                $this->root = array_merge($this->root, $stack);
            }
            else {
                $this->root  = $stack;
            }
    } else {
            $this->root = $stack;
    }
	R = $stack;
}


stmt_list(R) ::= stmt_list(A) stmt_or_defined_macro(B) . {
	R = $this->create_node('stmt_list', array(A, B));
}

stmt_list(R) ::= stmt_or_defined_macro(A) . {
	R = $this->create_node('stmt_list', array(A));
}

stmt(R) ::= NAMESPACEKWD(A) IDENTIFIER(B) namespace_body(C) . {
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('stmt', array(A, B, C));
}

namespace_body(R) ::= LEFTFIGUREBRACKET(A) RIGHTFIGUREBRACKET(B) . {
	R = $this->create_node('namespace_body', array( A, B ));
}

namespace_body(R) ::= LEFTFIGUREBRACKET(A) stmt_list(B) RIGHTFIGUREBRACKET(C) . {
	R = $this->create_node('namespace_body', array( A, B, C ));
}

/* CLASES, UNIONS, STRUCTURES */

stmt(R) ::= class_or_union_or_struct(A) . {
	R = $this->create_node('class_or_union_or_struct', array(A));
}

class_or_union_or_struct(R) ::= type_meta_specifier_with_template_def(A) IDENTIFIER(B) structure_body(C) IDENTIFIER(D) SEMICOLON(E) . {
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('class_or_union_or_struct', array(A, B, C, D, E));
}

class_or_union_or_struct(R) ::= type_meta_specifier_with_template_def(A) IDENTIFIER(B) structure_body(C) SEMICOLON(D) . {
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('class_or_union_or_struct', array(A, B, C, D));
}

class_or_union_or_struct(R) ::= type_meta_specifier_with_template_def(A) IDENTIFIER(B) SEMICOLON(C) . {
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('class_or_union_or_struct', array(A, B, C));
}

class_or_union_or_struct(R) ::= type_meta_specifier_with_template_def(A) structure_body(B) IDENTIFIER(C) SEMICOLON(D) . {
	R = $this->create_node('class_or_union_or_struct', array(A, B, C, D));
}

class_or_union_or_struct(R) ::= type_meta_specifier_with_template_def(A) structure_body(B) SEMICOLON(C) . {
	R = $this->create_node('class_or_union_or_struct', array(A, B, C));
}

type_meta_specifier_with_template_def(R) ::=  template_def(a) type_meta_specifier(B) . {
	R = $this->create_node('type_meta_specifier_with_template_def', array(A, B));
}

type_meta_specifier_with_template_def(R) ::= type_meta_specifier(A) . {
	R = $this->create_node('type_meta_specifier_with_template_def', array(A));
}

type_meta_specifier(R) ::= CLASSKWD(A) . {
	R = $this->create_node('type_meta_specifier', array(A));
}

type_meta_specifier(R) ::= STRUCTKWD(A) . {
	R = $this->create_node('type_meta_specifier', array(A));
}

type_meta_specifier(R) ::= UNIONKWD(A) . {
	R = $this->create_node('type_meta_specifier', array(A));
}

structure_body(R) ::= LEFTFIGUREBRACKET(A) RIGHTFIGUREBRACKET(B) . {
	R = $this->create_node('structure_body', array( A, B ));
}

structure_body(R) ::= LEFTFIGUREBRACKET(A) stmt_or_visibility_spec_list(B) RIGHTFIGUREBRACKET(C) . {
	R = $this->create_node('structure_body', array( A, B, C ));
}

stmt_or_visibility_spec_list(R) ::= stmt_or_visibility_spec(A) . {
	R = $this->create_node('stmt_or_visibility_spec_list', array(A));
}

stmt_or_visibility_spec_list(R) ::= stmt_or_visibility_spec_list(A) stmt_or_visibility_spec(B) . {
	R = $this->create_node('stmt_or_visibility_spec_list', array(A, B));
}

/* VISIBILITY FOR METHODS AND FIELDS OF STRUCTS AND CLASSES*/

stmt_or_visibility_spec(R) ::= visibility_spec_full(A) . {
	R = $this->create_node('stmt_or_visibility_spec', array(A));
	R  = A;
}

stmt_or_visibility_spec(R) ::= stmt_or_defined_macro(A) . {
	R = $this->create_node('stmt_or_visibility_spec', array(A));
}

visibility_spec_full(R) ::= visibility_spec(A) COLON(B) . {
	R = $this->create_node('visibility_spec_full', array( A, B ));
}

visibility_spec_full(R) ::= visibility_spec(A) signal_slots(B) COLON(C). {
	R = $this->create_node('visibility_spec_full', array( A, B, C ));
}

visibility_spec(R) ::= PUBLICKWD(A) . {
	R = $this->create_node('visibility_spec', array(A));
}

visibility_spec(R) ::= PROTECTEDKWD(A) . {
	R = $this->create_node('visibility_spec', array(A));
}

visibility_spec(R) ::= PRIVATEKWD(A) . {
	R = $this->create_node('visibility_spec', array(A));
}

signal_slots(R) ::= SIGNALSKWD(A) . {
	R = $this->create_node('signal_slots', array(A));
}

signal_slots(R) ::= SLOTSKWD(A) . {
	R = $this->create_node('signal_slots', array(A));
}

/* ENUM */

stmt_or_defined_macro(R) ::= ENUMKWD(A) IDENTIFIER(B) SEMICOLON(C) . {
	$this->mapper->introduce_type(B->value());	
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C));
}

stmt_or_defined_macro(R) ::= ENUMKWD(A) IDENTIFIER(B)  enum_body(C) SEMICOLON(D) . {
	$this->mapper->introduce_type(B->value());	
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D));
}

stmt_or_defined_macro(R) ::= ENUMKWD(A)  enum_body(B) SEMICOLON(C) . {
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C));
}

stmt_or_defined_macro(R) ::= ENUMKWD(A) IDENTIFIER(B) enum_body(C) IDENTIFIER(D) SEMICOLON(E) . {
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D, E));
}

stmt_or_defined_macro(R) ::= ENUMKWD(A)  enum_body(B) IDENTIFIER(C) SEMICOLON(D) . {
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D));
}

enum_body(R) ::= LEFTFIGUREBRACKET(A) enum_value_list(B) RIGHTFIGUREBRACKET(C) . {
	R = $this->create_node('enum_body', array(A, B, C));
}

enum_body(R) ::= LEFTFIGUREBRACKET(A) RIGHTFIGUREBRACKET(B) . {
	R = $this->create_node('enum_body', array(A, B));
}

enum_value_list(R) ::= enum_value_list(A) COMMA(B) enum_value(C) . {
	R = $this->create_node('enum_value_list', array(A, B, C));
}

enum_value_list(R) ::= enum_value(A) . {
	R = $this->create_node('enum_value_list', array(A));
}

enum_value(R) ::= IDENTIFIER(A) . {
	R = $this->create_node('enum_value', array(A));
}

enum_value(R) ::= IDENTIFIER(A) ASSIGN(B) expr_atom(C). {
	R = $this->create_node('enum_value', array(A, B, C));
}

/* FUNCTIONS */

stmt_or_defined_macro(R) ::= type(A) possible_function_name(B) formal_args_list_with_or_without_const(C) function_body(D) . {
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D));
}

stmt_or_defined_macro(R) ::= type_with_qualifier(A) possible_function_name(C) formal_args_list_with_or_without_const(D) function_body(E) . {
	R = $this->create_node('stmt_or_defined_macro', array(A, C, D, E));
}

stmt_or_defined_macro(R) ::= template_def(A) type_with_qualifier(B) possible_function_name(C) formal_args_list_with_or_without_const(D) function_body(E) . {
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D, E));
}

stmt_or_defined_macro(R) ::= template_def(A) type(B) possible_function_name(C) formal_args_list_with_or_without_const(D) function_body(E) . {
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D, E));
}

/* CONSTRUCTORS */
stmt_or_defined_macro(R) ::= template_def(A) non_const_type(B) LEFTROUNDBRACKET(C) RIGHTROUNDBRACKET(D) function_body(E) . {
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D));
}

/* Somehow putting type here allows more than we need, but also solves conflicts */
stmt_or_defined_macro(R) ::= type(A) LEFTROUNDBRACKET(B) RIGHTROUNDBRACKET(C) function_body(D) . {
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D));
}

stmt_or_defined_macro(R) ::= template_def(A) BINARYNOT(B) CUSTOMTYPENAME(C) LEFTROUNDBRACKET(D) RIGHTROUNDBRACKET(E) function_body(F) . {
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D, E, F));
}

stmt_or_defined_macro(R) ::= template_def(A) primitive_or_complex_type(B) NAMESPACE_RESOLVE(C) BINARYNOT(D) CUSTOMTYPENAME(E) LEFTROUNDBRACKET(F) RIGHTROUNDBRACKET(G) function_body(H) . {
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D, E, F));
}

stmt_or_defined_macro(R) ::= BINARYNOT(B) CUSTOMTYPENAME(C) LEFTROUNDBRACKET(D) RIGHTROUNDBRACKET(E) function_body(F) . {
	R = $this->create_node('stmt_or_defined_macro', array(B, C, D, E, F));
}

stmt_or_defined_macro(R) ::= primitive_or_complex_type(B) NAMESPACE_RESOLVE(C) BINARYNOT(D) CUSTOMTYPENAME(E) LEFTROUNDBRACKET(F) RIGHTROUNDBRACKET(G) function_body(H) . {
	R = $this->create_node('stmt_or_defined_macro', array(B, C, D, E, F));
}

/* TEMPLATES */

/* Due to imperfect resolution of names, we allow such constructions to make constructors and destructors compilable. 
   That is weird but it doesn't give us any kind of errors 
 */

template_def(R) ::= TEMPLATEKWD(A) LESSER(B) GREATER(C) . {
	R = $this->create_node('template_def', array(A, B, C));
}

template_def(R) ::= TEMPLATEKWD(A) LESSER(B) template_spec_list(C) GREATER(D) . {
	R = $this->create_node('template_def', array(A, B, C, D));
}

template_spec_list(R) ::= template_spec_list(A) COMMA(B) template_spec(C) . {
	R = $this->create_node('template_spec_list', array(A, B, C));
}

template_spec_list(R) ::= template_spec(A) . {
	R = $this->create_node('template_spec_list', array(A));
}

template_spec(R) ::= template_typename(A)  IDENTIFIER(B) . {
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('template_spec', array(A, B));
}


template_typename(R) ::= TYPENAMEKWD(A) . {
	R = $this->create_node('template_typename', array(A));
}

template_typename(R) ::= CLASSKWD(A) . {
	R = $this->create_node('template_typename', array(A));

}

template_typename(R) ::= STRUCTKWD(A) . {
	R = $this->create_node('template_typename', array(A));
}

template_typename(R) ::= ENUMKWD(A) . {
	R = $this->create_node('template_typename', array(A));
}


function_body(R) ::= LEFTFIGUREBRACKET(A) stmt_list(B) RIGHTFIGUREBRACKET(C) . {
	R = $this->create_node('function_body', array(A, B, C));
}

function_body(R) ::= LEFTFIGUREBRACKET(A)  RIGHTFIGUREBRACKET(B) . {
	R = $this->create_node('function_body', array(A, B));
}

function_body(R) ::= SEMICOLON(A) . {
	R = $this->create_node('function_body', array(A));
}


possible_function_name(R) ::= primitive_or_complex_type(A) . {
	R = $this->create_node('possible_function_name', array(A));
}

possible_function_name(R) ::= IDENTIFIER(A) . {
	R = $this->create_node('possible_function_name', array(A));
}

possible_function_name(R) ::= OPERATOROVERLOADDECLARATION(A) . {
	R = $this->create_node('possible_function_name', array(A));
}


/* ARGUMENTS */

formal_args_list_with_or_without_const(R) ::= formal_args_list(A) . {
	R = $this->create_node('formal_args_list_with_or_without_const', array(A));
}

formal_args_list_with_or_without_const(R) ::= formal_args_list(A) CONSTKWD(B) . {
	R = $this->create_node('formal_args_list_with_or_without_const', array(A, B));
}

formal_args_list(R) ::= LEFTROUNDBRACKET(A) RIGHTROUNDBRACKET(B) . {
	R = $this->create_node('args_list', array(A, B));
}

formal_args_list(R) ::= LEFTROUNDBRACKET(A) arg_list(B) RIGHTROUNDBRACKET(C) . {
	R = $this->create_node('formal_args_list', array(A, B, C));
}

arg_list(R) ::= arg(A) . {
	R = $this->create_node('arg_list', array(A));
}

arg_list(R) ::= arg_list(A) COMMA(B) arg(C) . {
	R = $this->create_node('arg_list', array(A, B, C));
}

arg(R) ::= type(A) IDENTIFIER(B) . {
	R = $this->create_node('arg', array(A, B));
}

/* PREPROCESSOR */

stmt_or_defined_macro(R) ::=  preprocessor_cond(A) stmt_list(B) PREPROCESSOR_ENDIF(C).  {
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C));
}

stmt_or_defined_macro(R) ::=  preprocessor_cond(A) stmt_list(B) preprocessor_else_clauses(C) PREPROCESSOR_ENDIF(D).  {
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D));
}

preprocessor_else_clauses(R) ::= preprocessor_elif_list(A) preprocessor_else(B) . {
	R = $this->create_node('preprocessor_else_clauses', array(A, B));
} 

preprocessor_else_clauses(R) ::= preprocessor_elif_list(A) . {
	R = $this->create_node('preprocessor_else_clauses', array(A));
}

preprocessor_else_clauses(R) ::= preprocessor_else(A) . {
	R = $this->create_node('preprocessor_else_clauses', array(A));
}

preprocessor_elif_list(R) ::= preprocessor_elif_list(A) preprocessor_elif(B) . {
	R = $this->create_node('preprocessor_elif_list', array(A, B));
}

preprocessor_elif_list(R) ::= preprocessor_elif(A) .  {
	R = $this->create_node('preprocessor_elif_list', array(A));
}
 
preprocessor_elif(R) ::= PREPROCESSOR_ELIF(A) stmt_list(B) . {
	R = $this->create_node('preprocessor_elif', array(A, B));
}

preprocessor_else(R) ::= PREPROCESSOR_ELSE(A) stmt_list(B) . {
	R = $this->create_node('preprocessor_else', array(A, B));
}

preprocessor_cond(R)  ::= PREPROCESSOR_IFDEF(A) IDENTIFIER(B)   . {
	R = $this->create_node('preprocessor_cond', array(A, B, C));
}

preprocessor_cond(R)  ::= PREPROCESSOR_IFDEF(A) CUSTOMTYPENAME(B) . {
	R = $this->create_node('preprocessor_cond', array(A, B, C));
}

preprocessor_cond(R) ::= PREPROCESSOR_IF(A) . {
	R = $this->create_node('preprocessor_cond', array(A, B));
}

stmt_or_defined_macro(R) ::= PREPROCESSOR_DEFINE(A) . {
	R = $this->create_node('stmt_or_defined_macro', array(A, B));
}

stmt_or_defined_macro(R) ::= stmt(A) . {
	R = $this->create_node('stmt_or_defined_macro', array(A));
}

stmt(R) ::= PREPROCESSOR_INCLUDE(A) . {
	R = $this->create_node('stmt', array(A));
}

/* LOOPS */

stmt(R) ::= WHILEKWD(A)
			LEFTROUNDBRACKET(B)
			expr_prec_11(C)		
			RIGHTROUNDBRACKET(D)
			stmt(E) 
			. {
	R = $this->create_node('while', array(A, B, C, D, E));
}

stmt(R) ::= DOKWD(A)
            stmt(B)
			WHILEKWD(C)
			LEFTROUNDBRACKET(D)
			expr_prec_11(E)		
			RIGHTROUNDBRACKET(F)
			SEMICOLON(G)
			. {
	R = $this->create_node('do_while', array(A, B, C, D, E, F, G));
}
			
			
stmt(R) ::= FORKWD(A) 
			LEFTROUNDBRACKET(B) 
			expr_prec_11(C) SEMICOLON(D)  
			expr_prec_11(E) SEMICOLON(F) 
			expr_prec_11(G)
			RIGHTROUNDBRACKET(H)
			stmt(I)
			. {
	R = $this->create_node('for', array(A, B, C, D, E, F, G, H, I));
}			


/* RETURN */

stmt(R) ::= RETURNKWD(A) expr_prec_11(B) SEMICOLON(C) . {
	R = $this->create_node('stmt', array(A, B, C));
}

stmt(R) ::= RETURNKWD(A) SEMICOLON(B) . {
	R = $this->create_node('stmt', array(A, B));
}


/* CONTINUE */

stmt(R) ::= CONTINUEKWD(A) SEMICOLON(B) . {
	R = $this->create_node('continue', array(A, B));
}

/* GOTO-STATEMENTS */

stmt(R) ::= GOTOKWD(A) IDENTIFIER(B) SEMICOLON(C) . {
	R = $this->create_node('goto', array(A, B, C));
}

stmt(R) ::= GOTOKWD(A) CUSTOMTYPENAME(B) SEMICOLON(C) . {
	R = $this->create_node('goto', array(A, B, C));
}

stmt(R) ::= IDENTIFIER(A) COLON(B) . {
	R = $this->create_node('goto_label', array(A, B));
}

/* TRY-CATCH-STATEMENTS */

stmt(R) ::= try_catch(A) . {
    R = $this->create_node('stmt', array(A));
}

try_catch(R) ::= try(A) catch_list(B) . {
	R = $this->create_node('try_catch', array(A, B));
}

try(R) ::= TRYKWD(A) LEFTFIGUREBRACKET(B) RIGHTFIGUREBRACKET(C) . {
	R = $this->create_node('try', array(A, B, C));
}

try(R) ::= TRYKWD(A) LEFTFIGUREBRACKET(B) stmt_list(C) RIGHTFIGUREBRACKET(D) . {
	R = $this->create_node('try', array(A, B, C, D));
}

catch_list(R) ::= catch_list(A) catch(B) . {
	R = $this->create_node('catch_list', array(A, B));
}

catch_list(R) ::= catch(A) . {
	R = $this->create_node('catch_list', array(A));
}

catch(R) ::=  CATCHKWD(A) LEFTROUNDBRACKET(B) expr_prec_11_or_ellipsis(C) RIGHTROUNDBRACKET(D) LEFTFIGUREBRACKET(E) RIGHTFIGUREBRACKET(F) . {
	R = $this->create_node('catch', array(A, B, C, D, E, F));
}

catch(R) ::=  CATCHKWD(A) LEFTROUNDBRACKET(B) expr_prec_11_or_ellipsis(C) RIGHTROUNDBRACKET(D) LEFTFIGUREBRACKET(E) stmt_list(F) RIGHTFIGUREBRACKET(G) . {
	R = $this->create_node('catch', array(A, B, C, D, E, F, G));
}

expr_prec_11_or_ellipsis(R) ::= expr_prec_11(A) . {
	R = $this->create_node('expr_prec_11_or_ellipsis', array( A ));
}

expr_prec_11_or_ellipsis(R) ::= ELLIPSIS(A) . {
	R = $this->create_node('expr_prec_11_or_ellipsis', array( A ));
}

/* EMPTY OPERATOR */

stmt(R) ::= SEMICOLON(A) .  {
	R = $this->create_node('stmt', array( A ));
}

/* SWITCH-CASE-STATEMENTS */
 
stmt(R) ::= switch_stmt(A) .  {
	R = $this->create_node('stmt', array( A ));
}

switch_stmt(R) ::= SWITCHKWD(A) LEFTROUNDBRACKET(B) expr_prec_11(C) RIGHTROUNDBRACKET(D) LEFTFIGUREBRACKET(E) RIGHTFIGUREBRACKET(F) . {
	R = $this->create_node('switch_stmt', array(A, B, C, D, E, F));
}

switch_stmt(R) ::= SWITCHKWD(A) LEFTROUNDBRACKET(B) expr_prec_11(C) RIGHTROUNDBRACKET(D) LEFTFIGUREBRACKET(E) switch_case_list(F) RIGHTFIGUREBRACKET(G) . {
	R = $this->create_node('switch_stmt', array(A, B, C, D, E, F, G));
}

switch_case_list(R) ::= case(A) . {
	R = $this->create_node('switch_case_list', array(A));
}

switch_case_list(R) ::= switch_case_list(A) case(B) . {
	R = $this->create_node('switch_case_list', array(A, B));
}

case(R) ::= CASEKWD(A) expr_atom(B) COLON(C) stmt_list(D) . {
	R = $this->create_node('case', array(A, B, C, D));
}

case(R) ::= DEFAULTKWD(A) COLON(B) stmt_list(C) . {
	R = $this->create_node('case', array(A, B, C));
}

/* IF-THEN-ELSE STATEMENT */

stmt(R) ::= if_then_else(A) .  {
	R = $this->create_node('stmt', array( A ));
}

if_then_else(R) ::=  if_then(A) . [THENKWD] {
	R = $this->create_node('if_then_else', array(A));
}

if_then_else(R) ::=  if_then(A) ELSEKWD(B) stmt(C).  {
	R = $this->create_node('if_then_else', array(A, B, C));
}

if_then(R) ::= IFKWD(A) LEFTROUNDBRACKET(B) expr_prec_11(C) RIGHTROUNDBRACKET(D) stmt(E) .  {
	R = $this->create_node('if_then', array(A, B, C, D, E));
}

/* STATEMENTS */

stmt(R) ::= LEFTFIGUREBRACKET(A) stmt_list(B) RIGHTFIGUREBRACKET(C) . {
	R = $this->create_node('stmt', array( A, B, C ));
}

stmt(R) ::=  TYPEDEF(A) type(B) IDENTIFIER(C) SEMICOLON(D) . { 
	R = $this->create_node('stmt', array(A, B, C, D));
	$this->mapper->introduce_type(C->value());
}


stmt(R) ::= BREAKKWD(A) SEMICOLON(B) . {
	R = $this->create_node('stmt', array(A, B));
}

stmt(R) ::= expr_prec_11(A) SEMICOLON(B) . {
	R = $this->create_node('stmt', array(A, B));
}

/* EXPRESSIONS OF ELEVENTH PRECEDENCE */

expr_prec_11(R) ::= NEWKWD(A) expr_prec_10(B)  . {
	R = $this->create_node('expr_prec_11', array( A, B ));
} 

expr_prec_11(R) ::= DELETE(A) LEFTSQUAREBRACKET(B)  RIGHTSQUAREBRACKET(C)  expr_prec_10(D) . {
	R = $this->create_node('expr_prec_11', array( A, B, C, D ));
} 

expr_prec_11(R) ::= DELETE(A) expr_prec_10(B) . {
	R = $this->create_node('expr_prec_11', array( A, B ));
} 

expr_prec_11(R) ::= type(A) expr_atom(B) ASSIGN(C) expr_prec_9(D) . {
	R = $this->create_node('expr_prec_11', array( A, B, C, D ));
}

expr_prec_11(R) ::= type(A) primitive_or_complex_type(B) ASSIGN(C) expr_prec_9(D) . {
	R = $this->create_node('expr_prec_11', array( A, B, C, D ));
}

expr_prec_11(R) ::= type(A) IDENTIFIER(B) . {
	R = $this->create_node('expr_prec_11', array( A, B ));
}

expr_prec_11(R) ::= type(A) primitive_or_complex_type(B)  . {
	R = $this->create_node('expr_prec_11', array( A, B ));
}

expr_prec_11(R) ::= type_with_qualifier(A) IDENTIFIER(C) ASSIGN(D) expr_prec_9(E) . {
	R = $this->create_node('expr_prec_11', array( A,  C, D, E ));
}

expr_prec_11(R) ::= type_with_qualifier(A)  primitive_or_complex_type(C) ASSIGN(D) expr_prec_9(E) . {
	R = $this->create_node('expr_prec_11', array( A, C, D, E ));
}

expr_prec_11(R) ::= type_with_qualifier(A)  IDENTIFIER(C)  . {
	R = $this->create_node('expr_prec_11', array( A, C ));
}

expr_prec_11(R) ::= type_with_qualifier(A) primitive_or_complex_type(C) . {
	R = $this->create_node('expr_prec_11', array( A, C ));
}

expr_prec_11(R) ::= expr_prec_11(A) COMMA(B)  expr_prec_10(C) . {
	R = $this->create_node('expr_prec_11', array( A, B, C ));
}

expr_prec_11(R) ::= expr_prec_10(A) . {
	R = $this->create_node('expr_prec_11', array( A ));
}

/* VARIABLE QUALIFIERS */

type_with_qualifier(R) ::= varqualifier(A) type(B) . {
	$result = $this->create_node('type_with_qualifier', array( A, B ));
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

/* EXPRESSIONS OF TENTH PRECEDENCE */

expr_prec_10(R) ::= expr_prec_9(A) BINARYXOR_ASSIGN(B) expr_prec_10(C) . {
	R = $this->create_node('expr_prec_10', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) BINARYOR_ASSIGN(B) expr_prec_10(C) . {
	R = $this->create_node('expr_prec_10', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) BINARYAND_ASSIGN(B) expr_prec_10(C) . {
	R = $this->create_node('expr_prec_10', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) RIGHTSHIFT_ASSIGN(B) expr_prec_10(C) . {
	R = $this->create_node('expr_prec_10', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) LEFTSHIFT_ASSIGN(B) expr_prec_10(C) . {
	R = $this->create_node('expr_prec_10', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) MODULO_ASSIGN(B) expr_prec_10(C) . {
	R = $this->create_node('expr_prec_10', array( A, B, C ));
 }

expr_prec_10(R) ::= expr_prec_9(A) DIVISION_ASSIGN(B) expr_prec_10(C) . {
	R = $this->create_node('expr_prec_10', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) MULTIPLY_ASSIGN(B) expr_prec_10(C) . {
	R = $this->create_node('expr_prec_10', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) PLUS_ASSIGN(B) expr_prec_10(C) . {
	R = $this->create_node('expr_prec_10', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) MINUS_ASSIGN(B) expr_prec_10(C) . {
	R = $this->create_node('expr_prec_10', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) ASSIGN(B) expr_prec_10(C) . {
	R = $this->create_node('expr_prec_10', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) . {
	R = $this->create_node('expr_prec_10', array( A ) );
}

/* EXPRESSIONS OF NINTH PRECEDENCE */

expr_prec_9(R) ::= expr_prec_9(A) LOGICALOR(B) expr_prec_8(C) . {
	R = $this->create_node('expr_prec_9', array( A, B, C ));
}

expr_prec_9(R) ::= expr_prec_9(A) LOGICALAND(B) expr_prec_8(C) . {
	R = $this->create_node('expr_prec_9', array( A, B, C ));
}

expr_prec_9(R) ::= expr_prec_9(A) BINARYXOR(B) expr_prec_8(C) . {
	R = $this->create_node('expr_prec_9', array( A, B, C ));
}

expr_prec_9(R) ::= expr_prec_9(A) BINARYAND(B) expr_prec_8(C) . {
	R = $this->create_node('expr_prec_9', array( A, B, C ));
}

expr_prec_9(R) ::= expr_prec_9(A) NOT_EQUAL(B) expr_prec_8(C) . {
	R = $this->create_node('expr_prec_9', array( A, B, C ));
}

expr_prec_9(R) ::= expr_prec_9(A) EQUAL(B) expr_prec_8(C) . {
	R = $this->create_node('expr_prec_9', array( A, B, C ));
}

expr_prec_9(R) ::= expr_prec_8(A) . {
	R = $this->create_node('expr_prec_9', array( A ) );
}

/* EXPRESSIONS OF EIGHTH PRECEDENCE */

expr_prec_8(R) ::= expr_prec_8(A) LESSER_OR_EQUAL(B) expr_prec_7(C) . {
	R = $this->create_node('expr_prec_8', array( A, B, C ));
}

expr_prec_8(R) ::= expr_prec_8(A) GREATER_OR_EQUAL(B) expr_prec_7(C) . {
	R = $this->create_node('expr_prec_8', array( A, B, C ));
}

expr_prec_8(R) ::= expr_prec_8(A) GREATER(B) expr_prec_7(C) . {
	R = $this->create_node('expr_prec_8', array( A, B, C ));
}

expr_prec_8(R) ::= expr_prec_8(A) LESSER(B) expr_prec_7(C) . {
	R = $this->create_node('expr_prec_8', array( A, B, C ));
}

expr_prec_8(R) ::= expr_prec_7(A) . {
	R = $this->create_node('expr_prec_8', array( A ) );
}

/* EXPRESSIONS OF SEVENTH PRECEDENCE */

expr_prec_7(R) ::= expr_prec_7(A) LEFTSHIFT(B) expr_prec_6(C) . {
	R = $this->create_node('expr_prec_7', array( A, B, C ));
}

expr_prec_7(R) ::= expr_prec_7(A) RIGHTSHIFT(B) expr_prec_6(C) . {
	R = $this->create_node('expr_prec_7', array( A, B, C ));
}

expr_prec_7(R) ::= expr_prec_6(A) . {
	R = $this->create_node('expr_prec_7', array( A ) );
}

/* EXPRESSIONS OF SIXTH PRECEDENCE */

expr_prec_6(R) ::= expr_prec_6(A) MINUS(B) expr_prec_5(C) . {
	R = $this->create_node('expr_prec_6', array( A, B, C ));
}

expr_prec_6(R) ::= expr_prec_6(A) PLUS(B) expr_prec_5(C) . {
	R = $this->create_node('expr_prec_6', array( A, B, C ));
}

expr_prec_6(R) ::= expr_prec_5(A) . {
	R = $this->create_node('expr_prec_6', array( A ) );
}

/* EXPRESSIONS OF FIFTH PRECEDENCE */

expr_prec_5(R) ::= expr_prec_5(A)  MODULOSIGN(B) expr_prec_4(C) . {
	R = $this->create_node('expr_prec_5', array( A, B, C ));
}

expr_prec_5(R) ::= expr_prec_5(A)  DIVISION(B) expr_prec_4(C) . {
	R = $this->create_node('expr_prec_5', array( A, B, C ));
}

expr_prec_5(R) ::= expr_prec_5(A)  MULTIPLY(B) expr_prec_4(C) . {
	R = $this->create_node('expr_prec_5', array( A, B, C ));
}

expr_prec_5(R) ::= expr_prec_4(A) . {
	R = $this->create_node('expr_prec_5', array( A ) );
}

/* EXPRESSIONS OF FOURTH PRECEDENCE */

expr_prec_4(R) ::= try_value_access(A) MULTIPLY(B) IDENTIFIER(C) . {
	R = $this->create_node('expr_prec_4', array( A, B, C ));
}

expr_prec_4(R) ::= try_pointer_access(A) MULTIPLY(B) IDENTIFIER(C) . {
	R = $this->create_node('expr_prec_4', array( A, B, C ));
}

expr_prec_4(R) ::= expr_prec_3(A) . {
	R = $this->create_node('expr_prec_4', array( A ));
}

/* EXPRESSIONS OF THIRD PRECEDENCE */

expr_prec_3(R) ::= AMPERSAND(A) expr_prec_3(B) . [UADRESS]  {
	R = $this->create_node('expr_prec_3', array( A, B ));
}

expr_prec_3(R) ::= MULTIPLY(A) expr_prec_3(B) . [UINDIRECTION]  {
	R = $this->create_node('expr_prec_3', array( A, B ));
}

expr_prec_3(R) ::= typecast(A) expr_prec_3(B) . {
	R = $this->create_node('expr_prec_3', array( A, B));
}

expr_prec_3(R) ::= LOGICALNOT(A) expr_prec_3(B) .  {
	R = $this->create_node('expr_prec_3', array( A, B));
}

expr_prec_3(R) ::= BINARYNOT(A) expr_prec_3(B) . {
	R = $this->create_node('expr_prec_3', array( A, B));
}

expr_prec_3(R) ::= MINUS(A) expr_prec_2(B)   . [UMINUS] {
	R = $this->create_node('expr_prec_3', array( A, B));
}

expr_prec_3(R) ::= PLUS(A) expr_prec_2(B)   . [UPLUS] {
	R = $this->create_node('expr_prec_3', array( A, B));
}

expr_prec_3(R) ::= DECREMENT(A) expr_prec_3(B)   . {
	R = $this->create_node('expr_prec_3', array( A, B));
}

expr_prec_3(R) ::= INCREMENT(A) expr_prec_3(B)   . {
	R = $this->create_node('expr_prec_3', array( A, B));
}

expr_prec_3(R) ::= expr_prec_2(A) . {
	R = $this->create_node('expr_prec_3', array( A ) );
}

/* EXPRESSIONS OF SECOND PRECEDENCE */

expr_prec_2(R) ::= try_value_access(A) IDENTIFIER(B) . {
	R = $this->create_node('expr_prec_2', array( A , B) );
}

expr_prec_2(R) ::= try_pointer_access(A) IDENTIFIER(B) . {
	R = $this->create_node('expr_prec_2', array( A , B) );
}

expr_prec_2(R) ::= cpp_style_cast(A)  LEFTROUNDBRACKET(B) expr_prec_11(C)  RIGHTROUNDBRACKET(D) . [UBRACKET] {
	R = $this->create_node('expr_prec_2', array( A, B, C, D));
}

expr_prec_2(R) ::= expr_prec_2(A)  LEFTSQUAREBRACKET(B) expr_prec_10(C)  RIGHTSQUAREBRACKET(D) . {
	R = $this->create_node('expr_prec_2', array( A, B, C, D));
}

expr_prec_2(R) ::= expr_prec_2(A)  LEFTROUNDBRACKET(B) expr_prec_11(C)  RIGHTROUNDBRACKET(D) . [UBRACKET] {
	R = $this->create_node('expr_prec_2', array( A, B, C, D));
}

expr_prec_2(R) ::= expr_prec_2(A)  LEFTROUNDBRACKET(B) RIGHTROUNDBRACKET(D) . [UBRACKET] {
	R = $this->create_node('expr_prec_2', array( A, B, D));
}

expr_prec_2(R) ::= expr_prec_2(A)  INCREMENT(B) . {
	R = $this->create_node('expr_prec_2', array( A, B));
}

expr_prec_2(R) ::= expr_prec_2(A)  DECREMENT(B) . {
	R = $this->create_node('expr_prec_2', array( A, B));
}

expr_prec_2(R) ::= expr_atom(A) . {
	R =  $this->create_node('expr_prec_2', array( A ));
}

/* SPECIAL PRODUCTIONS, NEEDED TO SUPPORT ACCESS BY POINTERS TO MEMBERS */

try_value_access(R) ::= expr_prec_2(A) DOT(B) . {
	R = $this->create_node('try_value_access', array( A , B) );
}

try_pointer_access(R) ::= expr_prec_2(A) RIGHTARROW(B) . {
	R = $this->create_node('try_pointer_access', array( A , B) );
}

/* C++ STYLE CASTS */

cpp_style_cast(R) ::= CONST_CAST(A)  LESSER(B) type(C) GREATER(D) . {
	R = $this->create_node('cpp_style_cast', array(A, B, C, D));
}

cpp_style_cast(R) ::= STATIC_CAST(A)  LESSER(B) type(C) GREATER(D) . {
	R = $this->create_node('cpp_style_cast', array(A, B, C, D));
}

cpp_style_cast(R) ::= DYNAMIC_CAST(A)  LESSER(B) type(C) GREATER(D) . {
	R = $this->create_node('cpp_style_cast', array(A, B, C, D));
}

cpp_style_cast(R) ::= REINTERPRET_CAST(A)  LESSER(B) type(C) GREATER(D) . {
	R = $this->create_node('cpp_style_cast', array(A, B, C, D));
}

/* EXPRESSIONS OF FIRST PRECEDENCE */

expr_atom(R) ::= NUMERIC(A) . {
	R =  $this->create_node('expr_atom', array( A ));
}

expr_atom(R) ::= IDENTIFIER(A) . {
	R =  $this->create_node('expr_atom', array( A ));
}

expr_atom(R) ::= CHARACTER(A) . {
	R =  $this->create_node('expr_atom', array( A ));
}

expr_atom(R) ::= STRING(A) . {
	R =  $this->create_node('expr_atom', array( A ));
}

expr_atom(R) ::= LEFTROUNDBRACKET(A) expr_prec_11(B) RIGHTROUNDBRACKET(C) . {
	R =  $this->create_node('expr_atom', array( A, B, C));
}

expr_atom(R) ::= PREPROCESSOR_STRINGIFY(A) IDENTIFIER(B) . {
	R =  $this->create_node('expr_atom', array( A, B));
}

expr_atom(R) ::= expr_atom(A) PREPROCESSOR_CONCAT(B) IDENTIFIER(C) . {
	R =  $this->create_node('expr_atom', array( A, B, C));
}

/* TYPECAST */

typecast(R) ::= LEFTROUNDBRACKET(A)  type(B) RIGHTROUNDBRACKET(C) . {
	$result = $this->create_node('typecast', array( A, B, C ));
	R = $result;
}

/* LIST OF TYPES */

type_list(R) ::= type(A) .  {
	R = $this->create_node('type_list', array( A ) );
}

type_list(R) ::= type_list(A) COMMA(B) type(C) . {
	R = $this->create_node('type_list', array( A, B, C ) );
}

/* TYPE DEFINITIONS */

type(R) ::= CONSTKWD(A) non_const_type(B) . {
	R = $this->create_node('type', array( A, B ));
}

type(R) ::= non_const_type(A) . {
    R = $this->create_node('type', array( A ));
}

non_const_type(R) ::= non_const_type(A) MULTIPLY(B) . [TYPEUNARY] {
	R = $this->create_node('type', array( A, B ));
}

non_const_type(R) ::= non_const_type(A) CONSTKWD(B) MULTIPLY(C) . [TYPEUNARY] {
	R = $this->create_node('type', array( A, B, C ));
}

non_const_type(R) ::= non_const_type(A) AMPERSAND(B) . [TYPEUNARY] {
	R = $this->create_node('type', array( A, B ));
}

non_const_type(R) ::= builtintype(A) . {
	R = $this->create_node('non_const_type', array( A ));
}

non_const_type(R) ::= primitive_or_complex_type(A) . {
	R = $this->create_node('non_const_type', array( A ));
}

primitive_or_complex_type(R) ::= CUSTOMTYPENAME(A) . {
	R = $this->create_node('primitive_or_complex_type', array( A ));
}

primitive_or_complex_type(R) ::= CUSTOMTYPENAME(A) LESSER(B) GREATER(C) .  {
	R = $this->create_node('primitive_or_complex_type', array( A, B, C ));
}

primitive_or_complex_type(R) ::= CUSTOMTYPENAME(A) LESSER(B) type_list(C) GREATER(D) .  {
	R = $this->create_node('primitive_or_complex_type', array( A, B, C, D ));
}

primitive_or_complex_type(R) ::= primitive_or_complex_type(A)  NAMESPACE_RESOLVE(B) IDENTIFIER(C)  . {
	R = $this->create_node('primitive_or_complex_type', array( A, B, C));
}
primitive_or_complex_type(R) ::= primitive_or_complex_type(A)  NAMESPACE_RESOLVE(B) CUSTOMTYPENAME(C)  . {
	R = $this->create_node('primitive_or_complex_type', array( A, B, C));
}

primitive_or_complex_type(R) ::= primitive_or_complex_type(A)  NAMESPACE_RESOLVE(B) CUSTOMTYPENAME(C) LESSER(D) GREATER(E) . {
	R = $this->create_node('primitive_or_complex_type', array( A, B, C, D, E));
}

primitive_or_complex_type(R) ::= primitive_or_complex_type(A)  NAMESPACE_RESOLVE(B) CUSTOMTYPENAME(C) LESSER(D) type_list(E) GREATER(F) . {
	R = $this->create_node('primitive_or_complex_type', array( A, B, C, D, E, F));
}

builtintype(R) ::= SIGNED(A) TYPENAME(B) . {
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= UNSIGNED(A) TYPENAME(B) . {
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= LONG(A) TYPENAME(B) . {
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= LONG(A) LONG(B) TYPENAME(C) . {
	R = $this->create_node('builtintype', array( A, B, C ));
}

builtintype(R) ::= UNSIGNED(A) LONG(B) LONG(C) TYPENAME(D) . {
	R = $this->create_node('builtintype', array( A, B, C, D ));
}

builtintype(R) ::= TYPENAME(A) . {
	R = $this->create_node('builtintype', array( A ));
}