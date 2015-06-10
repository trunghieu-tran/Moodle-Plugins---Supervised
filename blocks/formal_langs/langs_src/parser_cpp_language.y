%name block_formal_langs_parser_cpp_language
%declare_class {class block_formal_langs_parser_cpp_language}
%include {
require_once($CFG->dirroot.'/blocks/formal_langs/descriptions/descriptionrule.php');
}
%include_class {
    // Root of the Abstract Syntax Tree (AST).
    public $root;
	// Current id for language
	public $currentid;
	// A mapper for parser
	public $mapper;
	// Test, whether parsing error occured
	public $error = false;
    // A current rule for a parser
	public $currentrule = null;
	
	protected function create_node($type, $children) {
		$result = new block_formal_langs_ast_node_base($type, null, $this->currentid, false);
		$this->currentid = $this->currentid + 1;
		$result->set_childs($children);
		$result->rule = $this->currentrule;
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

%left   RIGHTROUNDBRACKET.
%nonassoc THENKWD .
%left    ELSEKWD.
%left    NOTEQUAL EQUAL.
%left    LOGICALAND.
%left    BINARYAND.
%left    BINARYOR.
%left    LOGICALOR.
%left    AMPERSAND.
%left    BINARYXOR.
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

program(R) ::= comment_list(A) stmt_list(B) .  {
	$stack = array( $this->create_node('program', array( A, B ) ) );
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
	$this->currentrule = new block_formal_langs_description_rule("список выражения %l(stmt_or_defined_macro)", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('stmt_list', array(A, B));
}

stmt_list(R) ::= stmt_or_defined_macro(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('stmt_list', array(A));
}

namespace_definition_start(R) ::= namespacekwd(A) . {
	$this->mapper->push_anonymous_type();
	R = $this->create_node('namespace_definition_start', array(A));
}

namespace_definition_start(R) ::=  namespacekwd(A) identifier(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово объявления пространства имен", "%s"));
	$this->mapper->introduce_type(B->value());
	$this->mapper->push_introduced_type(B->value());
	R = $this->create_node('namespace_definition_start', array(A, B));
}

stmt(R) ::= namespace_definition_start(A) namespace_body(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('namespace', array(A, B));
}

start_of_empty_namespace(R) ::= leftfigurebracket(A) . {
	$this->mapper->try_pop_introduced_type();
	R = A;
}

namespace_body(R) ::= start_of_empty_namespace(A) rightfigurebracket(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("левая фигурная скобка", "правая фигурная скобка"));
	R = $this->create_node('namespace_body', array( A, B ));
}

namespace_statements(R) ::= stmt_list(A) . {
	$this->mapper->try_pop_introduced_type();
	R = A;
}

namespace_body(R) ::= leftfigurebracket(A) namespace_statements(B) rightfigurebracket(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("левая фигурная скобка", "%ur(именительный)", "правая фигурная скобка"));
	R = $this->create_node('namespace_body', array( A, B, C ));
}

/* CLASES, UNIONS, STRUCTURES */

stmt(R) ::= class_or_union_or_struct(A) . {
	R = A;
}

identified_type_meta_specifier_with_template_def(R) ::=  type_meta_specifier_with_template_def(A) identifier(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s"));
	$this->mapper->introduce_constructable(B->value());
	$this->mapper->push_introduced_type(B->value(), $this->mapper->extract_template_parameters(A));
	R = $this->create_node('identified_type_meta_specifier_with_template_def', array(A, B));
}

class_or_union_or_struct(R) ::= identified_type_meta_specifier_with_template_def(A) structure_body(B) identifier(C) SEMICOLON(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%s", "точка с запятой"));
	R = $this->create_node('class_or_union_or_struct', array(A, B, C, D));
}

class_or_union_or_struct(R) ::= identified_type_meta_specifier_with_template_def(A) structure_body(B) identifier(C) SEMICOLON(D) comment_list(E). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%s", "точка с запятой"));
	R = $this->create_node('class_or_union_or_struct', array(A, B, C, D, E));
}

class_or_union_or_struct(R) ::= identified_type_meta_specifier_with_template_def(A) structure_body(B) SEMICOLON(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "точка с запятой"));
	R = $this->create_node('class_or_union_or_struct', array(A, B, C));
}

class_or_union_or_struct(R) ::= identified_type_meta_specifier_with_template_def(A) structure_body(B) SEMICOLON(C) comment_list(D). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "точка с запятой"));
	R = $this->create_node('class_or_union_or_struct', array(A, B, C, D));
}


class_or_union_or_struct(R) ::= identified_type_meta_specifier_with_template_def(A) SEMICOLON(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "точка с запятой"));
	R = $this->create_node('class_or_union_or_struct', array(A, B));
}

class_or_union_or_struct(R) ::= identified_type_meta_specifier_with_template_def(A) SEMICOLON(B) comment_list(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "точка с запятой"));
	R = $this->create_node('class_or_union_or_struct', array(A, B, C));
}

nonidentified_type_meta_specifier_with_template_def(R) ::= type_meta_specifier_with_template_def(A) . {
	$this->mapper->push_anonymous_type($this->mapper->extract_template_parameters(A));
	R = A;
}

class_or_union_or_struct(R) ::= nonidentified_type_meta_specifier_with_template_def(A) structure_body(B) identifier(C) SEMICOLON(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%s", "точка с запятой"));
	R = $this->create_node('class_or_union_or_struct', array(A, B, C, D));
}

class_or_union_or_struct(R) ::= nonidentified_type_meta_specifier_with_template_def(A) structure_body(B) identifier(C) SEMICOLON(D) comment_list(E) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%s", "точка с запятой"));
	R = $this->create_node('class_or_union_or_struct', array(A, B, C, D, E));
}


class_or_union_or_struct(R) ::= nonidentified_type_meta_specifier_with_template_def(A) structure_body(B) SEMICOLON(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "точка с запятой"));
	R = $this->create_node('class_or_union_or_struct', array(A, B, C));
}

class_or_union_or_struct(R) ::= nonidentified_type_meta_specifier_with_template_def(A) structure_body(B) SEMICOLON(C) comment_list(D). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "точка с запятой"));
	R = $this->create_node('class_or_union_or_struct', array(A, B, C, D));
}


/* TEMPLATES */


template_spec_list(R) ::= template_spec_list(A) comma(B) template_spec(C) . {
	$this->currentrule = new block_formal_langs_description_rule("список параметров шаблона %l(template_spec)", array("%ur(именительный)", "запятая", "%ur(именительный)"));
	R = $this->create_node('template_spec_list', array(A, B, C));
}

template_spec_list(R) ::= template_spec(A) . {
	R = A;
}

template_spec(R) ::= template_typename(A). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s"));
	R = $this->create_node('template_spec', array(A));
}

template_spec(R) ::= template_typename(A)  identifier(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s"));
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('template_spec', array(A, B));
}

template_spec(R) ::= template_typename(A)  identifier(B) assign(C) type_or_type_ref_or_with_ptr(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%s", "%ur(именительный)"));
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('template_spec', array(A, B, C, D));
}

template_spec(R) ::= template_typename(A)  identifier(B) assign(C) expr_prec_7(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%s", "%ur(именительный)"));
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('template_spec', array(A, B, C, D));
}

template_spec(R) ::= template_def(Z) template_typename(A)  identifier(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%ur(именительный)", "%s"));
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('template_spec', array(Z, A, B));
}

template_spec(R) ::= template_def(Z) template_typename(A)  identifier(B) assign(C) type_or_type_ref_or_with_ptr(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%ur(именительный)", "%s", "%s", "%ur(именительный)"));
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('template_spec', array(Z, A, B, C, D));
}

template_spec(R) ::= template_def(Z) template_typename(A)  identifier(B) assign(C) expr_prec_7(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%ur(именительный)", "%s", "%s", "%ur(именительный)"));
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('template_spec', array(Z, A, B, C, D));
}


template_typename(R) ::= typenamekwd(A) . {
	R = A;
}

template_typename(R) ::= classkwd(A) . {
	R = A;
}

template_typename(R) ::= structkwd(A) . {
	R = A;
}

template_typename(R) ::= enumkwd(A) . {
	R = A;
}

template_typename(R) ::= builtintype(A) . {
	R = A;
}

template_def(R) ::= templatekwd(A) lesser(B) greater(C) . {
	$this->currentrule = new block_formal_langs_description_rule("определение шаблона", array("ключевое слово определения шаблона", "начало аргументов шаблона", "конец аргументов шаблона"));
	R = $this->create_node('template_def', array(A, B, C));
}

template_def(R) ::= templatekwd(A) lesser(B) template_spec_list(C) greater(D) . {
	$this->currentrule = new block_formal_langs_description_rule("определение шаблона", array("ключевое слово определения шаблона", "начало аргументов шаблона", "%ur(именительный)", "конец аргументов шаблона"));
	R = $this->create_node('template_def', array(A, B, C, D));
}


type_meta_specifier_with_template_def(R) ::=  template_def(A) type_meta_specifier(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный) и %2(именительный)", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('type_meta_specifier_with_template_def', array(A, B));
}

type_meta_specifier_with_template_def(R) ::= type_meta_specifier(A) . {
	R = A;
}

type_meta_specifier(R) ::= classkwd(A) . {
	R = A;
}

type_meta_specifier(R) ::= structkwd(A) . {
	R = A;
}

type_meta_specifier(R) ::= unionkwd(A) . {
	R = A;
}

structure_body(R) ::= leftfigurebracket(A) rightfigurebracket(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("левая фигурная скобка", "правая фигурная скобка"));
	$this->mapper->try_pop_introduced_type();
	R = $this->create_node('structure_body', array( A, B ));
}

structure_body(R) ::= leftfigurebracket(A) stmt_or_visibility_spec_list(B) rightfigurebracket(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("левая фигурная скобка", "%ur(именительный)", "правая фигурная скобка"));
	$this->mapper->try_pop_introduced_type();
	R = $this->create_node('structure_body', array( A, B, C ));
}

stmt_or_visibility_spec_list(R) ::= stmt_or_visibility_spec(A) . {
	R = A;
}

stmt_or_visibility_spec_list(R) ::= stmt_or_visibility_spec_list(A) stmt_or_visibility_spec(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%l(stmt_or_visibility_spec)", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('stmt_or_visibility_spec_list', array(A, B));
}

/* VISIBILITY FOR METHODS AND FIELDS OF STRUCTS AND CLASSES*/

stmt_or_visibility_spec(R) ::= visibility_spec_full(A) . {
	R  = A;
}

stmt_or_visibility_spec(R) ::= stmt_or_defined_macro(A) . {
	R = A;
}

visibility_spec_full(R) ::= visibility_spec(A) colon(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "двоеточие"));
	R = $this->create_node('visibility_spec_full', array( A, B ));
}

visibility_spec_full(R) ::= visibility_spec(A) signal_slots(B) colon(C). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "двоеточие"));
	R = $this->create_node('visibility_spec_full', array( A, B, C ));
}

visibility_spec(R) ::= publickwd(A) . {
	R = A;
}

visibility_spec(R) ::= protectedkwd(A) . {
	R = A;
}

visibility_spec(R) ::= privatekwd(A) . {
	R = A;
}

signal_slots(R) ::= signalskwd(A) . {
	R = A;
}

signal_slots(R) ::= slotskwd(A) . {
    R = A;
}

/* ENUM */


enum_body(R) ::= leftfigurebracket(A) enum_value_list(B) rightfigurebracket(C) . {
	$this->currentrule = new block_formal_langs_description_rule("тело перечисления", array("левая фигурная скобка", "%ur(именительный)", "правая фигурная скобка"));
	R = $this->create_node('enum_body', array(A, B, C));
}

enum_body(R) ::= leftfigurebracket(A) rightfigurebracket(B) . {
	$this->currentrule = new block_formal_langs_description_rule("тело перечисления", array("левая фигурная скобка", "правая фигурная скобка"));
	R = $this->create_node('enum_body', array(A, B));
}

enum_value_list(R) ::= enum_value_list(A) comma(B) enum_value(C) . {
	$this->currentrule = new block_formal_langs_description_rule("список значений перечисления %l(enum_value)", array("%ur(именительный)", "запятая", "%ur(именительный)"));
	R = $this->create_node('enum_value_list', array(A, B, C));
}

enum_value_list(R) ::= enum_value(A) . {
	R = A;
}

enum_value(R) ::= identifier(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%ur(именительный)", array("%s"));
	R = $this->create_node('enum_value', array(A));
}

enum_value(R) ::= identifier(A) assign(B) expr_atom(C). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "операция присвоения", "%s"));
	R = $this->create_node('enum_value', array(A, B, C));
}


enum_definition_start(R) ::= enumkwd(A) identifier(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('enum_definition_start', array(A, B));
}

stmt_or_defined_macro(R) ::= enum_definition_start(A) SEMICOLON(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "точка с запятой"));
	R = $this->create_node('enum_definition', array(A, B));
}

stmt_or_defined_macro(R) ::= enum_definition_start(A) SEMICOLON(B) comment_list(C). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "точка с запятой"));
	R = $this->create_node('enum_definition', array(A, B, C));
}

stmt_or_defined_macro(R) ::= enum_definition_start(A)  enum_body(B) SEMICOLON(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "точка с запятой"));
	R = $this->create_node('enum_definition', array(A, B, C));
}

stmt_or_defined_macro(R) ::= enum_definition_start(A)  enum_body(B) SEMICOLON(C) comment_list(D). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "точка с запятой"));
	R = $this->create_node('enum_definition', array(A, B, C, D));
}

stmt_or_defined_macro(R) ::= enumkwd(A)  enum_body(B) SEMICOLON(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово перечисления", "%ur(именительный)", "точка с запятой"));
	R = $this->create_node('enum_definition', array(A, B, C));
}

stmt_or_defined_macro(R) ::= enumkwd(A)  enum_body(B) SEMICOLON(C) comment_list(D). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово перечисления", "%ur(именительный)", "точка с запятой"));
	R = $this->create_node('enum_definition', array(A, B, C, D));
}

stmt_or_defined_macro(R) ::= enum_definition_start(A) enum_body(B) identifier(C) SEMICOLON(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%s", "точка с запятой"));
	R = $this->create_node('enum_definition', array(A, B, C, D));
} 

stmt_or_defined_macro(R) ::= enum_definition_start(A) enum_body(B) identifier(C) SEMICOLON(D) comment_list(E). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%s", "точка с запятой"));
	R = $this->create_node('enum_definition', array(A, B, C, D, E));
} 

stmt_or_defined_macro(R) ::= enumkwd(A)  enum_body(B) identifier(C) SEMICOLON(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово перечисления", "%ur(именительный)", "%s", "точка с запятой"));
	R = $this->create_node('enum_definition', array(A, B, C, D));
}

stmt_or_defined_macro(R) ::= enumkwd(A)  enum_body(B) identifier(C) SEMICOLON(D) comment_list(E). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово перечисления", "%ur(именительный)", "%s", "точка с запятой"));
	R = $this->create_node('enum_definition', array(A, B, C, D, E));
}

/* FUNCTIONS */

stmt_or_defined_macro(R) ::= type_or_type_with_qualifier(A) function_definition_without_type(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('stmt_or_defined_macro', array(A, B));
}

stmt_or_defined_macro(R) ::= template_def(A) type_or_type_with_qualifier(B) function_definition_without_type(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C));
}

function_definition_without_type(R) ::= lvalue(A) formal_args_list_with_or_without_const(B) function_body(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('function_definition_without_type', array(A, B, C));
}

function_definition_without_type(R) ::= operator_overload_declaration_type_modificators(A) . {
	R = A;
}

operator_overload_declaration_type_modificators(R) ::= ampersand(A) operator_overload_declaration_ptr(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%ur(именительный)"));
	R = $this->create_node('operator_overload_declaration', array(A, B));
}

operator_overload_declaration_type_modificators(R) ::= operator_overload_declaration_ptr(A) . {
	R = A;
}

operator_overload_declaration_ptr(R) ::= constkwd(A) multiply(B) operator_overload_declaration_ptr(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('operator_overload_declaration', array(A, B, C));
}

operator_overload_declaration_ptr(R) ::= multiply(A) operator_overload_declaration_ptr(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('operator_overload_declaration', array(A, B));
}

operator_overload_declaration_ptr(R) ::= operator_overload_declaration_without_type(A) . {
	R = A;
}

operator_overload_declaration_without_type(R) ::= operatoroverloaddeclaration(A) formal_args_list_with_or_without_const(B) function_body(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('function_definition_without_type', array(A, B, C));
}

/* CONSTRUCTORS */

/* Template constructor within class */
stmt_or_defined_macro(R) ::= template_def(A) type_or_type_with_qualifier(B) formal_args_list(B) function_body(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('constructor', array(A, B, C));
}

/* Constructor of non-template class within class */
stmt_or_defined_macro(R) ::= type_or_type_with_qualifier(A) formal_args_list(B) function_body(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('constructor', array(A, B, C));
}

/* Destructor, within a class */
stmt_or_defined_macro(R) ::= binarynot(A) typename(B) formal_args_list(C) function_body(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s", "%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('destructor', array(A, B, C, D));
}


/* An outer template constructor for class */

outer_constructor_name(R) ::= namespace_resolve(A) outer_constructor_name_terminal(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s"));
	$this->mapper->clear_lookup_namespace();
	R = $this->create_node('outer_constructor_name', array(A, B));
}

stmt_or_defined_macro(R) ::= template_def(A) outer_constructor_name(B) formal_args_list(C) function_body(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%ur(именительный)", "%ur(именительный)"));
	$this->mapper->clear_lookup_namespace();
	R = $this->create_node('constructor', array(A, B, C, D, E));
}

/* An outer constructor for class */
stmt_or_defined_macro(R) ::=  outer_constructor_name(A)  formal_args_list(B) function_body(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%ur(именительный)"));
	$this->mapper->clear_lookup_namespace();
	R = $this->create_node('constructor', array(A, B, C));
}

outer_destructor_name(R) ::= namespace_resolve(A) binarynot(B) outer_constructor_name_terminal(C) . {
	$this->mapper->clear_lookup_namespace();
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%s"));
	R = $this->create_node('outer_destructor_name', array(A, B, C));
}

/* An outer template destructor for class */
stmt_or_defined_macro(R) ::= template_def(A) outer_destructor_name(B) formal_args_list(C) function_body(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('constructor', array(A, B, C, D));
}

/* An outer destructor for class */
stmt_or_defined_macro(R) ::=  outer_destructor_name(A) formal_args_list(B) function_body(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('constructor', array(A, B, C));
}

function_body(R) ::= leftfigurebracket(A) stmt_list(B) rightfigurebracket(C) . {
	$this->currentrule = new block_formal_langs_description_rule("тело функции", array("левая фигурная скобка", "%ur(именительный)", "правая фигурная скобка"));
	R = $this->create_node('function_body', array(A, B, C));
}

function_body(R) ::= leftfigurebracket(A)  rightfigurebracket(B) . {
	$this->currentrule = new block_formal_langs_description_rule("тело функции", array("левая фигурная скобка", "правая фигурная скобка"));
	R = $this->create_node('function_body', array(A, B));
}

function_body(R) ::= SEMICOLON(A) . {
    $this->currentrule = new block_formal_langs_description_rule("тело функции", array("точка с запятой"));
	R = $this->create_node('function_body', array(A));
}

function_body(R) ::= SEMICOLON(A) comment_list(B). {
    $this->currentrule = new block_formal_langs_description_rule("тело функции", array("точка с запятой"));
	R = $this->create_node('function_body', array(A, B));
}


/* ARGUMENTS */

formal_args_list_with_or_without_const(R) ::= formal_args_list(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('formal_args_list_with_or_without_const', array(A));
}

formal_args_list_with_or_without_const(R) ::= formal_args_list(A) constkwd(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)", "ключевое слово константности"));
	R = $this->create_node('formal_args_list_with_or_without_const', array(A, B));
}

formal_args_list(R) ::= leftroundbracket(A) rightroundbracket(B) . {
	$this->currentrule = new block_formal_langs_description_rule("список формальных аргументов", array("левая круглая скобка", "правая круглая скобка"));
	R = $this->create_node('args_list', array(A, B));
}

formal_args_list(R) ::= leftroundbracket(A) arg_list(B) rightroundbracket(C) . {
	$this->currentrule = new block_formal_langs_description_rule("список формальных аргументов", array("левая круглая скобка", "%ur(именительный)", "правая круглая скобка"));
	R = $this->create_node('formal_args_list', array(A, B, C));
}

arg_list(R) ::= arg(A) . {
	$this->currentrule = new block_formal_langs_description_rule("список аргументов", array("%ur(именительный)"));
	R = $this->create_node('arg_list', array(A));
}

arg_list(R) ::= arg_list(A) comma(B) arg(C) . {
	$this->currentrule = new block_formal_langs_description_rule("список аргументов %l(arg)", array("%ur(именительный)", "запятая", "%ur(именительный)"));
	R = $this->create_node('arg_list', array(A, B, C));
}

arg(R) ::= lvalue(A) . {
	R = A;
}

arg(R) ::= type_or_type_ref_or_with_ptr(A) identifier(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s"));
	R = $this->create_node('arg', array(A, B));
}

arg(R) ::= type_or_type_ref_or_with_ptr(A) . {
	R = A;
}


/* PREPROCESSOR */

stmt_or_defined_macro(R) ::=  preprocessor_cond(A) stmt_list(B) preprocessor_endif(C).  {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "ключевое слово конца условного блока препроцессора"));
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C));
}

stmt_or_defined_macro(R) ::=  preprocessor_cond(A) stmt_list(B) preprocessor_else_clauses(C) preprocessor_endif(D).  {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%ur(именительный)", "ключевое слово конца условного блока препроцессора"));
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D));
}

preprocessor_else_clauses(R) ::= preprocessor_elif_list(A) preprocessor_else(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('preprocessor_else_clauses', array(A, B));
} 

preprocessor_else_clauses(R) ::= preprocessor_elif_list(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('preprocessor_else_clauses', array(A));
}

preprocessor_else_clauses(R) ::= preprocessor_else(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)"));
	R = $this->create_node('preprocessor_else_clauses', array(A));
}

preprocessor_elif_list(R) ::= preprocessor_elif_list(A) preprocessor_elif(B) . {
	$this->currentrule = new block_formal_langs_description_rule("список условий  препроцессора %l(preprocessor_elif)", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('preprocessor_elif_list', array(A, B));
}

preprocessor_elif_list(R) ::= preprocessor_elif(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("список условий препроцессора", array("%1(именительный)"));
	R = $this->create_node('preprocessor_elif_list', array(A));
}
 
preprocessor_elif(R) ::= preprocessor_elif_terminal(A) stmt_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое-слово \"если-то\" препроцессора", "%ur(именительный)"));
	R = $this->create_node('preprocessor_elif', array(A, B));
}

preprocessor_else(R) ::= preprocessor_else_terminal(A) stmt_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое-слово \"если\" препроцессора", "%ur(именительный)"));
	R = $this->create_node('preprocessor_else', array(A, B));
}

preprocessor_cond(R)  ::= preprocessor_ifdef(A) identifier(B)   . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("условная директива препроцессора с условием что макроопределение определено", "%s"));
	R = $this->create_node('preprocessor_cond', array(A, B, C));
}

preprocessor_cond(R)  ::= preprocessor_ifdef(A) typename(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("условная директива препроцессора с условием что макроопределение определено", "%s"));
	R = $this->create_node('preprocessor_cond', array(A, B, C));
}

preprocessor_cond(R) ::= preprocessor_if(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%s", array("условная директива препроцессора вида \"если\""));
	R = $this->create_node('preprocessor_cond', array(A, B));
}

stmt_or_defined_macro(R) ::= preprocessor_define(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R = $this->create_node('stmt_or_defined_macro', array(A, B));
}

stmt_or_defined_macro(R) ::= stmt(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('stmt_or_defined_macro', array(A));
}

stmt(R) ::= preprocessor_include(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R = $this->create_node('stmt', array(A));
}

/* LOOPS */

stmt(R) ::= whilekwd(A)
			leftroundbracket(B)
			expr_prec_10(C)		
			rightroundbracket(D)
			stmt(E) 
			. {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово объявления цикла с предусловием", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка", "%ur(именительный)"));
	R = $this->create_node('while', array(A, B, C, D, E));
}

stmt(R) ::= dokwd(A)
            stmt(B)
			whilekwd(C)
			leftroundbracket(D)
			expr_prec_11(E)		
			rightroundbracket(F)
			semicolon(G)
			. {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово  объявления цикла с постусловием", "%ur(именительный)", "ключевое слово начала условия в цикле с постусловием", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка", "точка с запятой"));
	R = $this->create_node('do_while', array(A, B, C, D, E, F, G));
}
			
			
stmt(R) ::= FORKWD(A) 
			leftroundbracket(B) 
			expr_prec_11(C) semicolon(D)  
			expr_prec_11(E) semicolon(F) 
			expr_prec_11(G)
			rightroundbracket(H)
			stmt(I)
			. {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово объявления цикла со счетчиком", "левая круглая скобка", "%ur(именительный)", "точка с запятой", "%ur(именительный)", "точка с запятой", "%ur(именительный)", "правая круглая скобка", "%ur(именительный)"));
	R = $this->create_node('for', array(A, B, C, D, E, F, G, H, I));
}			


/* RETURN */

stmt(R) ::= returnkwd(A) expr_prec_11(B) SEMICOLON(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово возврата результата", "%ur(именительный)", "точка с запятой"));
	R = $this->create_node('stmt', array(A, B, C));
}

stmt(R) ::= returnkwd(A) expr_prec_11(B) SEMICOLON(C) comment_list(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово возврата результата", "%ur(именительный)", "точка с запятой"));
	R = $this->create_node('stmt', array(A, B, C, D));
}


stmt(R) ::= returnkwd(A) SEMICOLON(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово возврата результата", "точка с запятой"));
	R = $this->create_node('stmt', array(A, B));
}

stmt(R) ::= returnkwd(A) SEMICOLON(B) comment_list(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово возврата результата", "точка с запятой"));
	R = $this->create_node('stmt', array(A, B, C));
}


/* CONTINUE */

stmt(R) ::= continuekwd(A) SEMICOLON(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово пропуска итерации цикла", "точка с запятой"));
	R = $this->create_node('continue', array(A, B));
}

stmt(R) ::= continuekwd(A) SEMICOLON(B) comment_list(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово пропуска итерации цикла", "точка с запятой"));
	R = $this->create_node('continue', array(A, B, C));
}

/* GOTO-STATEMENTS */

stmt(R) ::= gotokwd(A) identifier(B) SEMICOLON(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово безусловного перехода", "имя метки перехода для  операции %ul(именительный)", "точка с запятой"));
	R = $this->create_node('goto', array(A, B, C));
}

stmt(R) ::= gotokwd(A) identifier(B) SEMICOLON(C) comment_list(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово безусловного перехода", "имя метки перехода для  операции %ul(именительный)", "точка с запятой"));
	R = $this->create_node('goto', array(A, B, C, D));
}

stmt(R) ::= gotokwd(A) typename(B) SEMICOLON(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово безусловного перехода", "имя метки перехода для  операции %ul(именительный)", "точка с запятой"));
	R = $this->create_node('goto', array(A, B, C));
}

stmt(R) ::= gotokwd(A) typename(B) SEMICOLON(C) comment_list(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово безусловного перехода", "имя метки перехода для  операции %ul(именительный)", "точка с запятой"));
	R = $this->create_node('goto', array(A, B, C, D));
}

stmt(R) ::= identifier(A) colon(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("имя метки", "двоеточие"));
	R = $this->create_node('goto_label', array(A, B));
}

/* TRY-CATCH-STATEMENTS */

stmt(R) ::= try_catch(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
    R = $this->create_node('stmt', array(A));
}

try_catch(R) ::= try(A) catch_list(B) . {
    $this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('try_catch', array(A, B));
}

try(R) ::= trykwd(A) leftfigurebracket(B) rightfigurebracket(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово начала небезопасного блока", "левая фигурная скобка", "правая фигурная скобка"));
	R = $this->create_node('try', array(A, B, C));
}

try(R) ::= trykwd(A) leftfigurebracket(B) stmt_list(C) rightfigurebracket(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово начала небезопасного блока", "левая фигурная скобка", "%ur(именительный)", "правая фигурная скобка"));
	R = $this->create_node('try', array(A, B, C, D));
}

catch_list(R) ::= catch_list(A) catch(B) . {
	$this->currentrule = new block_formal_langs_description_rule("список веток обработки исключения %l(catch)", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('catch_list', array(A, B));
}

catch_list(R) ::= catch(A) . {
	$this->currentrule = new block_formal_langs_description_rule("список веток обработки исключения", array("%ur(именительный)"));
	R = $this->create_node('catch_list', array(A));
}

catch(R) ::=  catchkwd(A) leftroundbracket(B) expr_prec_11_or_ellipsis(C) rightroundbracket(D) leftfigurebracket(E) rightfigurebracket(F) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово ветки исключения", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка", "левая фигурная скобка", "правая фигурная скобка"));
	R = $this->create_node('catch', array(A, B, C, D, E, F));
}

catch(R) ::=  catchkwd(A) leftroundbracket(B) expr_prec_11_or_ellipsis(C) rightroundbracket(D) leftfigurebracket(E) stmt_list(F) rightfigurebracket(G) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово ветки исключения", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка", "левая фигурная скобка", "%ur(именительный)", "правая фигурная скобка"));
	R = $this->create_node('catch', array(A, B, C, D, E, F, G));
}

expr_prec_11_or_ellipsis(R) ::= expr_prec_11(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('expr_prec_11_or_ellipsis', array( A ));
}

expr_prec_11_or_ellipsis(R) ::= ellipsis(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("эллипсис"));
	R = $this->create_node('expr_prec_11_or_ellipsis', array( A ));
}

/* EMPTY OPERATOR */

stmt(R) ::= SEMICOLON(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("точка с запятой"));
	R = $this->create_node('stmt', array( A ));
}

stmt(R) ::= SEMICOLON(A) comment_list(B) .  {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("точка с запятой"));
	R = $this->create_node('stmt', array( A, B ));
}

/* SWITCH-CASE-STATEMENTS */
 
stmt(R) ::= switch_stmt(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('stmt', array( A ));
}

switch_stmt(R) ::= switchkwd(A) leftroundbracket(B) expr_prec_11(C) rightroundbracket(D) leftfigurebracket(E) rightfigurebracket(F) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово ветвления", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка", "левая фигурная скобка", "правая фигурная скобка"));
	R = $this->create_node('switch_stmt', array(A, B, C, D, E, F));
}

switch_stmt(R) ::= switchkwd(A) leftroundbracket(B) expr_prec_11(C) rightroundbracket(D) leftfigurebracket(E) switch_case_list(F) rightfigurebracket(G) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово ветвления", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка", "левая фигурная скобка", "%ur(именительный)", "правая фигурная скобка"));
	R = $this->create_node('switch_stmt', array(A, B, C, D, E, F, G));
}

switch_case_list(R) ::= case(A) . {
	$this->currentrule = new block_formal_langs_description_rule("список ветвлений", array("%ur(именительный)"));
	R = $this->create_node('switch_case_list', array(A));
}

switch_case_list(R) ::= switch_case_list(A) case(B) . {
	$this->currentrule = new block_formal_langs_description_rule("список ветвлений %l(case)", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('switch_case_list', array(A, B));
}

case(R) ::= casekwd(A) expr_atom(B) colon(C) stmt_list(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово, обозначающее ветку ветвления", "%ur(именительный)", "двоеточие", "%ur(именительный)"));
	R = $this->create_node('case', array(A, B, C, D));
}

case(R) ::= defaultkwd(A) colon(B) stmt_list(C) . {
	$this->currentrule = new block_formal_langs_description_rule("ветка по умолчанию", array("ключевое слово, обозначающее ветку ветвления по умолчанию", "двоеточие", "%ur(именительный)"));
	R = $this->create_node('case', array(A, B, C));
}

/* IF-THEN-ELSE STATEMENT */

stmt(R) ::= if_then_else(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('stmt', array( A ));
}

if_then_else(R) ::=  if_then(A) . [THENKWD] {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('if_then_else', array(A));
}

if_then_else(R) ::=  if_then(A) elsekwd(B) stmt(C).  {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "ключевое слово \"иначе\"", "%ur(именительный)"));
	R = $this->create_node('if_then_else', array(A, B, C));
}

if_then(R) ::= ifkwd(A) leftroundbracket(B) expr_prec_11(C) rightroundbracket(D) stmt(E) .  {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово \"если\"", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка", "%ur(именительный)"));
	R = $this->create_node('if_then', array(A, B, C, D, E));
}

/* STATEMENTS */

stmt(R) ::= leftfigurebracket(A) stmt_list(B) rightfigurebracket(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("левая фигурная скобка", "%s", "правая фигурная скобка"));
	R = $this->create_node('stmt', array( A, B, C ));
}

stmt(R) ::=  typedef(A) type(B) lvalue(C) SEMICOLON(D) . { 
	$this->currentrule = new block_formal_langs_description_rule("объявление синонима типа", array("ключевое слово объявления синонима типа", "%s", "%s", "точка с запятой"));
	R = $this->create_node('typedef_declaration', array(A, B, C, D));
	$this->mapper->perform_typedef_action(C);
}

stmt(R) ::=  typedef(A) type(B) lvalue(C) SEMICOLON(D) comment_list(E) . { 
	$this->currentrule = new block_formal_langs_description_rule("объявление синонима типа", array("ключевое слово объявления синонима типа", "%s", "%s", "точка с запятой"));
	R = $this->create_node('typedef_declaration', array(A, B, C, D, E));
	$this->mapper->perform_typedef_action(C);
}


stmt(R) ::= breakkwd(A) SEMICOLON(B) . {
	$this->currentrule = new block_formal_langs_description_rule("прерывание работы", array("ключевое слово прерывания работы", "точка с запятой"));
	R = $this->create_node('stmt', array(A, B));
}

stmt(R) ::= breakkwd(A) SEMICOLON(B) comment_list(C) . {
	$this->currentrule = new block_formal_langs_description_rule("прерывание работы", array("ключевое слово прерывания работы", "точка с запятой"));
	R = $this->create_node('stmt', array(A, B, C));
}

stmt(R) ::= expr_prec_11(A) SEMICOLON(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)", "точка с запятой"));
	R = $this->create_node('stmt', array(A, B));
}

stmt(R) ::= expr_prec_11(A) SEMICOLON(B) comment_list(C). {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)", "точка с запятой"));
	R = $this->create_node('stmt', array(A, B, C));
}

/* EXPRESSIONS OF ELEVENTH PRECEDENCE */

expr_prec_11(R) ::= newkwd(A) expr_prec_10(B)  . {
	$this->currentrule = new block_formal_langs_description_rule("выделение памяти", array("ключевое слово выделения памяти", "%ur(именительный)"));
	R = $this->create_node('new_kwd', array( A, B ));
} 

expr_prec_11(R) ::= delete(A) leftsquarebracket(B)  rightsquarebracket(C)  expr_prec_10(D) . {
	$this->currentrule = new block_formal_langs_description_rule("освобождение памяти", array("ключевое слово освобождения памяти", "левая квадратная скобка", "правая квадратная скобка", "%ur(именительный)"));
	R = $this->create_node('delete_array', array( A, B, C, D ));
} 

expr_prec_11(R) ::= delete(A) expr_prec_10(B) . {
	$this->currentrule = new block_formal_langs_description_rule("освобождение памяти", array("ключевое слово освобождения памяти", "%ur(именительный)"));
	R = $this->create_node('delete_pointer', array( A, B ));
} 


expr_prec_11(R) ::= type_or_type_with_qualifier(A)  lvalue_or_assignment_list(B)  . {
	$this->currentrule = new block_formal_langs_description_rule("объявление переменных", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('variable_declaration', array( A, B ));
}

expr_prec_11(R) ::= expr_list(A) . {
	R = A;
}

/* LIST OF EXPRESSIONS */

expr_list(R) ::= expr_list(A) comma(B)  expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("список выражений %l(expr_prec_10)", array("%ur(именительный)", "запятая", "%ur(именительный)"));
	R = $this->create_node('expr_list', array( A, B, C ));
}

expr_list(R) ::= expr_prec_10(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* VARIABLE QUALIFIERS */

type_or_type_with_qualifier(R) ::= type_with_qualifier(A) . {
	R = A;
}

type_or_type_with_qualifier(R) ::= type(A) . {
	R = A;
}

type_with_qualifier(R) ::= varqualifier(A) type(B) . {
	$this->currentrule = new block_formal_langs_description_rule("тип с квалифицирующим словом", array("%ur(именительный)", "%ur(именительный)"));
	$result = $this->create_node('type_with_qualifier', array( A, B ));
	R = $result;
}

varqualifier(R) ::= statickwd(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово для статичности значения"));
	R = A;
}

varqualifier(R) ::= externkwd(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово импорта из внешней части"));
	R = A;
}

varqualifier(R) ::= registerkwd(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово, указания, что переменная должна содержаться в регистре процессора"));
	R = A;
}

varqualifier(R) ::= volatilekwd(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово изменяемости"));
	R = A;
}

varqualifier(R) ::= friendkwd(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово дружественности"));
	R = A;
}

/* EXPRESSIONS OF TENTH PRECEDENCE */

expr_prec_10(R) ::= expr_prec_9(A) binaryxor_assign(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание с побитовым исключающим ИЛИ\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция побитового исключающего ИЛИ с присваиванием", "%ur(именительный)"));
	R = $this->create_node('expr_binaryxor_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) binaryor_assign(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание с побитовым ИЛИ\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция побитового ИЛИ  с присваиванием", "%ur(именительный)"));
	R = $this->create_node('expr_binaryor_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) binaryand_assign(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание с побитовым И\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция побитового И  с присваиванием", "%ur(именительный)"));
	R = $this->create_node('expr_binaryand_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) rightshift_assign(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание со сдвигом вправо\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция присваивания со сдвигом вправо", "%ur(именительный)"));
	R = $this->create_node('expr_rightshift_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) leftshift_assign(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание со сдвигом влево\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция присваивания со сдвигом влево", "%ur(именительный)"));
	R = $this->create_node('expr_leftshift_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) modulo_assign(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание с получением остатка от деления\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция присваивания с получением остатка от модуля", "%ur(именительный)"));
	R = $this->create_node('expr_modulo_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) division_assign(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание с делением\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция присваивания с делением", "%ur(именительный)"));
	R = $this->create_node('expr_division_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) multiply_assign(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание с умножением\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция присваивания с умножением", "%ur(именительный)"));
	R = $this->create_node('expr_multiply_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) plus_assign(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание с суммированием\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция присваивания с суммированием", "%ur(именительный)"));
	R = $this->create_node('expr_plus_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) minus_assign(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание с вычитанием\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция присваивания с вычитанием", "%ur(именительный)"));
	R = $this->create_node('expr_minus_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) assign(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция присваивания", "%ur(именительный)"));
	R = $this->create_node('expr_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* EXPRESSIONS OF NINTH PRECEDENCE */

expr_prec_9(R) ::= expr_prec_9(A) logicalor(B) expr_logical_and(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"логического ИЛИ\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция логического ИЛИ", "%ur(именительный)"));
	R = $this->create_node('expr_logical_or', array( A, B, C ));
}

expr_prec_9(R) ::= expr_logical_and(A) . {
	R  = A;
}

expr_logical_and(R) ::= expr_logical_and(A) logicaland(B) expr_binary_ops(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"логического И\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция логического И", "%ur(именительный)"));
	R = $this->create_node('expr_logical_and', array( A, B, C ));
}

expr_logical_and(R) ::= expr_binary_ops(A) . {
	R  = A;
}

expr_binary_ops(R) ::= expr_binary_ops(A) binaryor(B) expr_xor(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"побитового ИЛИ\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция логического ИЛИ", "%ur(именительный)"));
	R = $this->create_node('expr_binary_or', array( A, B, C ));
}

expr_binary_ops(R) ::= expr_xor(A) . {
	R  = A;
}

expr_xor(R) ::= expr_xor(A) binaryxor(B) expr_ampersand(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"исключающего ИЛИ\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция исключающего ИЛИ", "%ur(именительный)"));
	R = $this->create_node('expr_binary_xor', array( A, B, C ));
}

expr_xor(R) ::= expr_ampersand(A) . {
	R  = A;
}

expr_ampersand(R) ::= expr_ampersand(A) ampersand(B) expr_or_equal(C) . {
	// Well, that's what you get when you mix binary and and adress taking
	$this->currentrule = new block_formal_langs_description_rule("операция \"побитового И\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция побитового И", "%ur(именительный)"));
	R = $this->create_node('expr_binary_and', array( A, B, C ));
}

expr_ampersand(R) ::= expr_or_equal(A).  {
	R = A ;
}


expr_or_equal(R) ::= expr_or_equal(A) not_equal(B) expr_prec_8(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"не равно\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция не равно", "%ur(именительный)"));
	R = $this->create_node('expr_notequal', array( A, B, C ));
}

expr_or_equal(R) ::= expr_or_equal(A) equal(B) expr_prec_8(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"равно\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция равно", "%ur(именительный)"));
	R = $this->create_node('expr_equal', array( A, B, C ));
}

expr_or_equal(R) ::= expr_prec_8(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* EXPRESSIONS OF EIGHTH PRECEDENCE */

expr_prec_8(R) ::= expr_prec_8(A) lesser_or_equal(B) expr_prec_7(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"меньше или равно\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция меньше или равно", "%ur(именительный)"));
	R = $this->create_node('expr_lesser_or_equal', array( A, B, C ));
}

expr_prec_8(R) ::= expr_prec_8(A) greater_or_equal(B) expr_prec_7(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"больше или равно\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция больше или равно", "%ur(именительный)"));
	R = $this->create_node('expr_greater_or_equal', array( A, B, C ));
}

expr_prec_8(R) ::= expr_prec_8(A) greater(B) expr_prec_7(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"больше\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция больше", "%ur(именительный)"));
	R = $this->create_node('expr_greater', array( A, B, C ));
}

expr_prec_8(R) ::= expr_prec_8(A) lesser(B) expr_prec_7(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"меньше\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция меньше", "%ur(именительный)"));
	R = $this->create_node('expr_lesser', array( A, B, C ));
}

expr_prec_8(R) ::= expr_prec_7(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* EXPRESSIONS OF SEVENTH PRECEDENCE */

expr_prec_7(R) ::= expr_prec_7(A) leftshift(B) expr_prec_6(C) . {
	$this->currentrule = new block_formal_langs_description_rule("сдвиг влево выражения %1(именительный) на число байт, заданное выражением %3(именительный)", array("%ur(именительный)", "операция сдвига влево", "%ur(именительный)"));
	R = $this->create_node('expr_leftshift', array( A, B, C ));
}

expr_prec_7(R) ::= expr_prec_7(A) rightshift(B) expr_prec_6(C) . {
	$this->currentrule = new block_formal_langs_description_rule("сдвиг вправо выражения %1(именительный) на число байт, заданное выражением %3(именительный)", array("%ur(именительный)", "операция сдвига вправо", "%ur(именительный)"));
	R = $this->create_node('expr_rightshift', array( A, B, C ));
}

expr_prec_7(R) ::= expr_prec_6(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* EXPRESSIONS OF SIXTH PRECEDENCE */

expr_prec_6(R) ::= expr_prec_6(A) minus(B) expr_prec_5(C) . {
	$this->currentrule = new block_formal_langs_description_rule("разность выражений \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция вычитания", "%ur(именительный)"));
	R = $this->create_node('expr_minus', array( A, B, C ));
}

expr_prec_6(R) ::= expr_prec_6(A) plus(B) expr_prec_5(C) . {
	$this->currentrule = new block_formal_langs_description_rule("сумма %1(именительный) и %3(именительный)", array("%ur(именительный)", "операция суммирования", "%ur(именительный)"));
	R = $this->create_node('expr_plus', array( A, B, C ));
}

expr_prec_6(R) ::= expr_prec_5(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* EXPRESSIONS OF FIFTH PRECEDENCE */

expr_prec_5(R) ::= expr_prec_5(A)  modulosign(B) expr_prec_4(C) . {
	$this->currentrule = new block_formal_langs_description_rule("получение остатка от деления выражений %1(именительный) и %3(именительный)", array("%ur(именительный)", "операция получения остатка от деления", "%ur(именительный)"));
	R = $this->create_node('expr_modulosign', array( A, B, C ));
}

expr_prec_5(R) ::= expr_prec_5(A)  division(B) expr_prec_4(C) . {
	$this->currentrule = new block_formal_langs_description_rule("деление %1(именительный) и %3(именительный)", array("%ur(именительный)", "операция деления", "%ur(именительный)"));
	R = $this->create_node('expr_division', array( A, B, C ));
}

expr_prec_5(R) ::= expr_prec_5(A)  multiply(B) expr_prec_4(C) . {
	$this->currentrule = new block_formal_langs_description_rule("умножение %1(именительный) и %3(именительный)", array("%ur(именительный)", "операция умножения", "%ur(именительный)"));
	R = $this->create_node('expr_multiply', array( A, B, C ));
}

expr_prec_5(R) ::= expr_prec_4(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* EXPRESSIONS OF FOURTH PRECEDENCE */

expr_prec_4(R) ::= try_value_access(A) multiply(B) identifier(C) . {
	$this->currentrule = new block_formal_langs_description_rule("взятие поля по указателю", array("%ur(именительный)", "%s", "%s"));
	R = $this->create_node('expr_get_property', array( A, B, C ));
}

expr_prec_4(R) ::= try_pointer_access(A) multiply(B) identifier(C) . {
	$this->currentrule = new block_formal_langs_description_rule("взятие поля по указателю", array("%ur(именительный)", "%s", "%s"));
	R = $this->create_node('expr_get_property', array( A, B, C ));
}

expr_prec_4(R) ::= expr_prec_3(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* EXPRESSIONS OF THIRD PRECEDENCE */

expr_prec_3(R) ::= ampersand(A) expr_prec_3(B) . [UADRESS]  {
	$this->currentrule = new block_formal_langs_description_rule("операция взятия указателя", array("операция взятия указателя", "%ur(именительный)"));
	R = $this->create_node('expr_take_adress', array( A, B ));
}

expr_prec_3(R) ::= multiply(A) expr_prec_3(B) . [UINDIRECTION]  {
	$this->currentrule = new block_formal_langs_description_rule("операция разыменования указателя", array("операция разыменования", "%ur(именительный)"));
	R = $this->create_node('expr_dereference', array( A, B ));
}

expr_prec_3(R) ::= typecast(A) expr_prec_3(B) . {
	$this->currentrule = new block_formal_langs_description_rule("операция приведения к типу", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('expr_typecast', array( A, B));
}

expr_prec_3(R) ::= logicalnot(A) expr_prec_3(B) .  {
	$this->currentrule = new block_formal_langs_description_rule("логическое отрицание на выражении %2(именительный)", array("операция логического отрицания", "%ur(именительный)"));
	R = $this->create_node('expr_logical_not', array( A, B));
}

expr_prec_3(R) ::= binarynot(A) expr_prec_3(B) . {
	$this->currentrule = new block_formal_langs_description_rule("побитовое отрицание на выражении %2(именительный)", array("операция побитового отрицания", "%ur(именительный)"));
	R = $this->create_node('expr_binary_not', array( A, B));
}

expr_prec_3(R) ::= minus(A) expr_prec_2(B)   . [UMINUS] {
	$this->currentrule = new block_formal_langs_description_rule("операция унарного минуса на выражении %2(именительный)", array("операция унарного минуса", "%ur(именительный)"));
	R = $this->create_node('expr_unary_minus', array( A, B));
}

expr_prec_3(R) ::= plus(A) expr_prec_2(B)   . [UPLUS] {
	$this->currentrule = new block_formal_langs_description_rule("операция унарного плюса на выражении %2(именительный)", array("операция унарного плюса", "%ur(именительный)"));
	R = $this->create_node('expr_unary_plus', array( A, B));
}

expr_prec_3(R) ::= decrement(A) expr_prec_3(B)   . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("операция декремента", "%ur(именительный)"));
	R = $this->create_node('expr_prefix_decrement', array( A, B));
}

expr_prec_3(R) ::= increment(A) expr_prec_3(B)   . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("операция инкремента", "%ur(именительный)"));
	R = $this->create_node('expr_prefix_decrement', array( A, B));
}

expr_prec_3(R) ::= expr_prec_2(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* EXPRESSIONS OF SECOND PRECEDENCE */

expr_prec_2(R) ::= try_value_access(A) identifier(B) . {
	$this->currentrule = new block_formal_langs_description_rule("обращение к полю по указателю на метод", array("%ur(именительный)", "имя свойства"));
	R = $this->create_node('expr_property_access', array( A , B) );
}

expr_prec_2(R) ::= try_pointer_access(A) identifier(B) . {
	$this->currentrule = new block_formal_langs_description_rule("обращение к полю по указателю на метод", array("%ur(именительный)", "имя свойства"));
	R = $this->create_node('expr_property_access', array( A , B) );
}

expr_prec_2(R) ::= cpp_style_cast(A)  leftroundbracket(B) expr_prec_11(C)  rightroundbracket(D) . [UBRACKET] {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный) выражения \"%3(именительный)\"", array("%ur(именительный)", "левая круглая скобка", "%ur(именительный)", "правая квадратная скобка"));
	R = $this->create_node('expr_array_access', array( A, B, C, D));
}

expr_prec_2(R) ::= expr_prec_2(A)  leftsquarebracket(B) expr_prec_10(C)  rightsquarebracket(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "левая квадратная скобка", "%ur(именительный)", "правая квадратная скобка"));
	R = $this->create_node('expr_array_access', array( A, B, C, D));
}

expr_prec_2(R) ::= expr_prec_2(A)  leftroundbracket(B) expr_list(C)  rightroundbracket(D) . [UBRACKET] {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка"));
	R = $this->create_node('expr_function_call', array( A, B, C, D));
}

expr_prec_2(R) ::= expr_prec_2(A)  leftroundbracket(B) rightroundbracket(D) . [UBRACKET] {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "левая круглая скобка", "правая круглая скобка"));
	R = $this->create_node('expr_function_call', array( A, B, D));
}

expr_prec_2(R) ::= expr_prec_2(A)  increment(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "операция инкремента"));
	R = $this->create_node('expr_postfix_increment', array( A, B));
}

expr_prec_2(R) ::= expr_prec_2(A)  decrement(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "операция декремента"));
	R = $this->create_node('expr_postfix_decrement', array( A, B));
}

expr_prec_2(R) ::= expr_atom(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R =  A;
}

/* SPECIAL PRODUCTIONS, NEEDED TO SUPPORT ACCESS BY POINTERS TO MEMBERS */

try_value_access(R) ::= expr_prec_2(A) dot(B) . {
	$this->currentrule = new block_formal_langs_description_rule("операция разыменования указателя на метод или переменной", array("%ur (именительный)", "операция взятия указателя на метод или поля переменной"));
	R = $this->create_node('try_value_access', array( A , B) );
}

try_pointer_access(R) ::= expr_prec_2(A) rightarrow(B) . {
	$this->currentrule = new block_formal_langs_description_rule("операция разыменования указателя на метод или переменной", array("%ur (именительный)", "операция взятия указателя на метод или переменной"));
	R = $this->create_node('try_pointer_access', array( A , B) );
}

/* C++ STYLE CASTS */

cpp_style_cast(R) ::= const_cast(A)  lesser(B) type_or_type_ref_or_with_ptr(C) greater(D) . {
	$this->currentrule = new block_formal_langs_description_rule("приведение со снятием константности к %3(родительный) типу ", array("ключевое слово приведения типа", "знак \"меньше\"", "%ur(именительный)", "знак \"больше\""));
	R = $this->create_node('expr_const_cast', array(A, B, C, D));
}

cpp_style_cast(R) ::= static_cast(A)  lesser(B) type_or_type_ref_or_with_ptr(C) greater(D) . {
	$this->currentrule = new block_formal_langs_description_rule("статическое приведение к %3(родительный) типу ", array("ключевое слово приведения типа", "знак \"меньше\"", "%ur(именительный)", "знак \"больше\""));
	R = $this->create_node('expr_static_cast', array(A, B, C, D));
}

cpp_style_cast(R) ::= dynamic_cast(A)  lesser(B) type_or_type_ref_or_with_ptr(C) greater(D) . {
	$this->currentrule = new block_formal_langs_description_rule("динамическое приведение к %3(родительный) типу ", array("ключевое слово приведения типа", "знак \"меньше\"", "%ur(именительный)", "знак \"больше\""));
	R = $this->create_node('expr_dynamic_cast', array(A, B, C, D));
}

cpp_style_cast(R) ::= reinterpret_cast(A)  lesser(B) type_or_type_ref_or_with_ptr(C) greater(D) . {
	$this->currentrule = new block_formal_langs_description_rule("побайтовое приведение к %3(родительный) типу ", array("ключевое слово приведения типа", "знак \"меньше\"", "%ur(именительный)", "знак \"больше\""));
	R = $this->create_node('expr_reinterpret_cast', array(A, B, C, D));
}

/* LVALUE FOR LOCAL DEFINITIONS */

lvalue_or_assignment_list(R) ::= lvalue_or_assignment(A) . {
	R = A;
}

lvalue_or_assignment_list(R) ::= lvalue_or_assignment_list(A) comma(B) lvalue_or_assignment(C). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%ur(именительный)"));
	R =  $this->create_node('lvalue_or_assignment_list', array( A, B, C));
}

lvalue_or_assignment(R) ::= lvalue(A) . {
	R = A;
}

lvalue_or_assignment(R) ::= lvalue(A) assign(B) expr_prec_10(C). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%ur(именительный)"));
	R =  $this->create_node('assign', array( A, B, C));
}

lvalue_or_assignment(R) ::= lvalue(A) assign(B) initialization_list(C). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%ur(именительный)"));
	R =  $this->create_node('assign', array( A, B, C));
}

initialization_list(R) ::= leftfigurebracket(A) rightfigurebracket(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R =  $this->create_node('initialization_list', array( A, B));
}

initialization_list(R) ::= leftfigurebracket(A) initialization_list_argument_list(B) rightfigurebracket(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%ur(именительный)", "%s"));
	R =  $this->create_node('initialization_list', array( A, B, C));
}


initialization_list_argument_list(R) ::= expr_prec_10(A) . {
	R = A;
}

initialization_list_argument_list(R) ::= initialization_list(A) . {
	R = A;
}

initialization_list_argument_list(R) ::= initialization_list_argument_list(A) comma(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%ur(именительный)"));
	R =  $this->create_node('initialization_list_argument_list', array( A, B, C));
}

initialization_list_argument_list(R) ::= initialization_list_argument_list(A) comma(B) initialization_list(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%ur(именительный)"));
	R =  $this->create_node('initialization_list_argument_list', array( A, B, C));
}

lvalue(R) ::= possibly_identifier_preceded_ref(A) . {
	R = A;
}

/* THIS IS ARRAY RULE DON'T REMOVE IT!!!! */
lvalue(R) ::= lvalue(A) leftsquarebracket(B) expr_prec_9(C) rightsquarebracket(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%ur(именительный)", "%s"));
	R =  $this->create_node('lvalue', array( A, B, C, D));
}

possibly_identifier_preceded_ref(R) ::= ampersand(A) possibly_idenitifer_preceded_ptrs(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%ur(именительный)"));
	R =  $this->create_node('lvalue', array( A, B));
}

possibly_identifier_preceded_ref(R) ::= possibly_idenitifer_preceded_ptrs(A) . {
	R = A;
}

possibly_idenitifer_preceded_ptrs(R) ::= identifier(A) . {
	R = A;
}


possibly_idenitifer_preceded_ptrs(R) ::= leftroundbracket(A) arg_list(B) rightroundbracket(C)  formal_args_list_with_or_without_const(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%ur(именительный)", "%ur(именительный)"));
	R =  $this->create_node('lvalue', array( A, B, C, D));
}

possibly_idenitifer_preceded_ptrs(R) ::= leftroundbracket(A) namespace_resolve(B) possibly_identifier_preceded_ref(C) rightroundbracket(D)  formal_args_list_with_or_without_const(E) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%ur(именительный)", "%ur(именительный)"));
	$this->mapper->clear_lookup_namespace();
	R =  $this->create_node('lvalue', array( A, B, C, D, E));
}


possibly_idenitifer_preceded_ptrs(R) ::= multiply(A) possibly_idenitifer_preceded_ptrs(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R =  $this->create_node('lvalue', array( A, B));
}

possibly_idenitifer_preceded_ptrs(R) ::= constkwd(A) multiply(B) possibly_idenitifer_preceded_ptrs(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s", "%s"));
	R =  $this->create_node('lvalue', array( A, B, C));
}

possibly_idenitifer_preceded_ptrs(R) ::= volatilekwd(A) multiply(B) possibly_idenitifer_preceded_ptrs(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s", "%s"));
	R =  $this->create_node('lvalue', array( A, B, C));
}


/* EXPRESSIONS OF FIRST PRECEDENCE */

expr_atom(R) ::= numeric(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R =  A;
}

expr_atom(R) ::= assignable(A) . {
	R =  A;
}

expr_atom(R) ::= character(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R =  A;
}

expr_atom(R) ::= string(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R =  A;
}

assignable(R) ::= identifier(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R =  A;
}

assignable(R) ::= scoped_identifier(A) . {
	R = A;
}

scoped_identifier(R) ::= namespace_resolve(A) identifier(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s"));
	R =  $this->create_node('scoped_identifier', array( A, B));
}

/* TODO: Test this type of expression later */
expr_atom(R) ::= leftroundbracket(A) expr_list(B) rightroundbracket(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("левая круглая скобка", "%s", "провая круглая скобка"));
	R =  $this->create_node('expr_brackets', array( A, B, C));
}

expr_atom(R) ::= preprocessor_stringify(A) identifier(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R =  $this->create_node('preprocessor_stringify', array( A, B));
}

expr_atom(R) ::= preprocessor_stringify(A) typename(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R =  $this->create_node('preprocessor_stringify', array( A, B));
}

expr_atom(R) ::= identifier(A) preprocessor_concat(B) identifier(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%s"));
	R =  $this->create_node('preprocessor_concat', array( A, B, C));
}

expr_atom(R) ::= identifier(A) preprocessor_concat(B) typename(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%s"));
	R =  $this->create_node('preprocessor_concat', array( A, B, C));
}

expr_atom(R) ::= typename(A) preprocessor_concat(B) identifier(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%s"));
	R =  $this->create_node('preprocessor_concat', array( A, B, C));
}

expr_atom(R) ::= typename(A) preprocessor_concat(B) typename(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%s"));
	R =  $this->create_node('preprocessor_concat', array( A, B, C));
}

/* ================================= VALIDATED PART ================================= */

expr_atom(R) ::= sizeof(A) leftroundbracket(B)  type_or_type_ref_or_with_ptr(C) rightroundbracket(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("операция взятия размера структуры", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка"));
	R =  $this->create_node('sizeof', array( A, B, C, D));
}

expr_atom(R) ::= sizeof(A) leftroundbracket(B)  expr_atom(C) rightroundbracket(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("операция взятия размера структуры", "левая круглая скобка", "%s", "правая круглая скобка"));
	R =  $this->create_node('sizeof', array( A, B, C, D));
}

/* TYPECAST */

typecast(R) ::= leftroundbracket(A)  type_or_type_ref_or_with_ptr(B) rightroundbracket(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция приведения к типу %2(именительный) ", array("левая круглая скобка", "%ur(именительный)", "правая круглая скобка"));
	$result = $this->create_node('c_style_typecast_operator', array( A, B, C ));
	R = $result;
}

/* TYPE DEFINITIONS */

type(R) ::= constkwd(A) non_const_type(B) . {
	$this->currentrule = new block_formal_langs_description_rule("константный тип %1(именительный) ", array("признак константности", "%ur(именительный)"));
	R = $this->create_node('type', array( A, B ));
}

type(R) ::= non_const_type(A) . {
    R = A;
}

non_const_type(R) ::= builtintype(A) . {
	R = A;
}

non_const_type(R) ::= scoped_type(A) . {
	R = A;
}

non_const_type(R) ::= typename_or_instantiated_template_type(A) . {
	R = A;
}

scoped_type(R) ::= namespace_resolve(A) typename(B) template_instantiation_arguments(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%s"));
	$this->mapper->clear_lookup_namespace();
	R = $this->create_node('scoped_type', array( A,  B, C));
} 

scoped_type(R) ::= namespace_resolve(A) typename(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%s"));
	$this->mapper->clear_lookup_namespace();
	R = $this->create_node('scoped_type', array( A,  B));
} 

namespace_resolve(R) ::=  namespace_resolve(A) instantiated_template_type_on_in_namespace_resolve(B)  namespace_resolve_terminal(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "операция разрешения видимости"));
	R = $this->create_node('namespace_resolve', array( A, B, C));
}

namespace_resolve(R) ::=  namespace_resolve(A) typename_in_namespace_resolve(B) namespace_resolve_terminal(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "операция разрешения видимости"));
	R = $this->create_node('namespace_resolve', array( A, B, C));
}

typename_in_namespace_resolve(R) ::= typename(A) . {
	$this->mapper->push_lookup_entry((string)(A->value()));
	R = A;
}

instantiated_template_type_on_in_namespace_resolve(R) ::= typename(A) template_instantiation_arguments(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	$this->mapper->push_lookup_entry((string)(A->value()));
	R = $this->create_node('instantiated_template_type', array( A, B));
}

namespace_resolve(R) ::= instantiated_template_type_on_start_of_namespace_resolve(A) namespace_resolve_terminal(B) .  {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "операция разрешения видимости"));
	R = $this->create_node('namespace_resolve', array( A, B));
}


namespace_resolve(R) ::= typename_on_start_of_namespace_resolve(A) namespace_resolve_terminal(B) .  {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "операция разрешения видимости"));
	R = $this->create_node('namespace_resolve', array( A, B));
}

namespace_resolve(R) ::= namespace_resolve_terminal(A) typename_on_start_of_namespace_resolve(B) namespace_resolve_terminal(C) .  {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "операция разрешения видимости"));
	R = $this->create_node('namespace_resolve', array( A, B, C));
}

instantiated_template_type_on_start_of_namespace_resolve(R) ::= typename(A) template_instantiation_arguments(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	$this->mapper->start_new_lookup_namespace();
	$this->mapper->push_lookup_entry((string)(A->value()));
	R = $this->create_node('instantiated_template_type', array( A, B));
}

typename_on_start_of_namespace_resolve(R) ::= typename(A) . {
	$this->mapper->start_new_lookup_namespace();
	$this->mapper->push_lookup_entry((string)(A->value()));
	R = A;
}

/* TYPENAME OR INSTANTIATION */

typename_or_instantiated_template_type(R) ::= typename(A) . {
	R = A;
}

typename_or_instantiated_template_type(R) ::= typename(A) template_instantiation_arguments(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('instantiated_template_type', array( A, B));
}


/* TYPE WITH POINTER OR REFERENCE */

type_ref_one(R) ::= type(A) ampersand(B) . {
	$this->currentrule = new block_formal_langs_description_rule("ссылка на %1(именительный)", array("%ur(именительный)", "признак  ссылки"));
	R = $this->create_node('type_ref', array( A, B));
}

type_ref_two(R) ::= type_ref_one(A) ampersand(B) . {
	A->add_child(B);
	R = A;
}

type_ref(R) ::= type_ref_one(A) . {
	R = A;
}

type_ref(R) ::= type_ref_two(A) . {
	R = A;
}

type_or_type_ref(R) ::= type(A) . {
	R = A;
}

type_or_type_ref(R) ::= type_ref(A) . {
	R = A;
}

type_or_type_ref_or_with_ptr(R) ::= type_or_type_ref(A) . {
	R = A;
}

type_or_type_ref_or_with_ptr(R) ::= type_or_type_ref_or_with_ptr(A) multiply(B) . {
	$this->currentrule = new block_formal_langs_description_rule("ссылка на %1(именительный)", array("%ur(именительный)", "признак указателя"));
	R = $this->create_node('type_with_ptr', array( A, B));
}

type_or_type_ref_or_with_ptr(R) ::= type_or_type_ref_or_with_ptr(A) constkwd(B) multiply(C) . {
	$this->currentrule = new block_formal_langs_description_rule("ссылка на %1(именительный)", array("%ur(именительный)", "признак константности", "признак указателя"));
	R = $this->create_node('type_with_ptr', array( A, B, C));
}

/* ================================= TEMPLATE INSTANTIATION PART  ================================= */

template_instantiation_argument_list(R) ::= type_or_type_ref_or_with_ptr(A) . {
	R = A;
}

template_instantiation_argument_list(R) ::= expr_atom(A) . {
	R = A;
}

template_instantiation_argument_list(R) ::= template_instantiation_argument_list(A) comma(B) type_or_type_ref_or_with_ptr(C)  . {
	$this->currentrule = new block_formal_langs_description_rule("список типов", array("список типов", "запятая", "%ur(именительный)"));
	R = $this->create_node('template_instantiation_argument_list', array( A, B, C));
}

template_instantiation_argument_list(R) ::= template_instantiation_argument_list(A) comma(B) expr_atom(C)  . {
	$this->currentrule = new block_formal_langs_description_rule("список типов", array("список типов", "запятая", "%ur(именительный)"));
	R = $this->create_node('type_with_ptr', array( A, B, C));
}

template_instantiation_arguments_begin(R) ::= lesser(A) . {
	$this->mapper->start_new_lookup_namespace();
	R = A;
}	

template_instantiation_arguments_end(R) ::= greater(B) . {
	$this->mapper->clear_lookup_namespace();
	R = B;
}	

template_instantiation_arguments(R) ::= template_instantiation_arguments_begin(A) template_instantiation_arguments_end(B) . {
	$this->currentrule = new block_formal_langs_description_rule("список аргументов инстанцирования шаблона", array("начало списка аргументов инстанцирования шаблона", "конец списка аргументов инстанцирования шаблона"));
	R = $this->create_node('template_instantiation_arguments', array( A, B));
}

template_instantiation_arguments(R) ::= template_instantiation_arguments_begin(A) template_instantiation_argument_list(B) template_instantiation_arguments_end(C) . {
	$this->currentrule = new block_formal_langs_description_rule("список аргументов инстанцирования шаблона", array("начало списка аргументов инстанцирования шаблона", "%ur(именительный)", "конец списка аргументов инстанцирования шаблона"));
	R = $this->create_node('template_instantiation_arguments', array( A, B, C));
}

/* VOID */

builtintype(R) ::= void(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("имя пустого типа"));
	R = $this->create_node('builtintype', array( A ));
}

/*  FLOATING POINT VARIATIONS */


builtintype(R) ::= float(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("имя типа c плавающей запятой одинарной точности"));
	R = $this->create_node('builtintype', array( A ));
}

builtintype(R) ::= double(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("имя типа c плавающей запятой двойной точности"));
	R = $this->create_node('builtintype', array( A ));
}

builtintype(R) ::= long(A) double(B) . {
    $this->currentrule = new block_formal_langs_description_rule("имя длинного типа c плавающей запятой двойной точности", array("признак длинного числа", "имя типа c плавающей запятой двойной точности"));
	R = $this->create_node('builtintype', array( A, B ));
}


/*  CHAR VARIATIONS */

builtintype(R) ::= char(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("имя символьного типа"));
	R = $this->create_node('builtintype', array( A ));
}

builtintype(R) ::= signed(A) char(B) . {
    $this->currentrule = new block_formal_langs_description_rule("знаковый символьный тип", array("признак знаковости", "%ur(именительный)"));
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= unsigned(A) char(B) . {
    $this->currentrule = new block_formal_langs_description_rule("беззнаковый символьный тип", array("признак беззнаковости", "%ur(именительный)"));
	R = $this->create_node('builtintype', array( A, B ));
}


/* INT VARIATIONS */

builtintype(R) ::= int(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("имя целого типа"));
	R = $this->create_node('builtintype', array( A ));
}

builtintype(R) ::= signed(A) int(B) . {
    $this->currentrule = new block_formal_langs_description_rule("знаковый целый тип", array("признак знаковости", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= unsigned(A) int(B) . {
    $this->currentrule = new block_formal_langs_description_rule("беззнаковый целый тип", array("признак беззнаковости", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= short(A) int(B) . {
    $this->currentrule = new block_formal_langs_description_rule("короткий целый тип", array("признак короткого целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= signed(A) short(B) int(C) . {
    $this->currentrule = new block_formal_langs_description_rule("знаковый короткий целый тип", array("признак знаковости", "признак короткого целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B, C ));
}

builtintype(R) ::= unsigned(A) short(B) int(C) . {
    $this->currentrule = new block_formal_langs_description_rule("беззнаковый короткий целый тип", array("признак беззнаковости", "признак короткого целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B, C ));
}

builtintype(R) ::= long(A) int(B) . {
    $this->currentrule = new block_formal_langs_description_rule("длинный целый тип", array("признак длинного целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= signed(A) long(B) int(C) . {
    $this->currentrule = new block_formal_langs_description_rule("знаковый длинный целый тип", array("признак знаковости", "признак длинного целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B, C ));
}

builtintype(R) ::= unsigned(A) long(B) int(C) . {
    $this->currentrule = new block_formal_langs_description_rule("беззнаковый длинный целый тип", array("признак беззнаковости", "признак длинного целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B, C ));
}

builtintype(R) ::= long(A) long(B) int(C) . {
    $this->currentrule = new block_formal_langs_description_rule("64-битный целый тип", array("признак длинного целого типа", "признак длинного целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B, C ));
}


builtintype(R) ::=  signed(A) long(B) long(C) int(D) . {
    $this->currentrule = new block_formal_langs_description_rule("знаковый 64-битный целый тип", array("признак знаковости", "признак длинного целого типа", "признак длинного целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B, C, D ));
}

builtintype(R) ::=  unsigned(A) long(B) long(C) int(D) . {
    $this->currentrule = new block_formal_langs_description_rule("беззнаковый 64-битный целый тип", array("признак беззнаковости", "признак длинного целого типа", "признак длинного целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B, C, D ));
}


/* SHORT VARIATIONS */

builtintype(R) ::= short(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("короткий целый тип"));
	R = $this->create_node('builtintype', array( A ));
}

builtintype(R) ::= signed(A) short(B) . {
    $this->currentrule = new block_formal_langs_description_rule("знаковый короткий целый тип", array("признак знаковости", "короткий целый тип"));
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= unsigned(A) short(B) . {
    $this->currentrule = new block_formal_langs_description_rule("беззнаковый короткий целый тип", array("признак беззнаковости", "короткий целый тип"));
	R = $this->create_node('builtintype', array( A, B ));
}

/* LONG VARIATIONS */ 

builtintype(R) ::= long(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("длинный целый тип"));
	R = $this->create_node('builtintype', array( A ));
}

builtintype(R) ::= signed(A) long(B) . {
    $this->currentrule = new block_formal_langs_description_rule("знаковый длинный целвый тип", array("признак знаковости", "длинный целый тип"));
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= unsigned(A) long(B) . {
    $this->currentrule = new block_formal_langs_description_rule("беззнаковый длинный целвый тип", array("признак беззнаковости", "длинный целый тип"));
	R = $this->create_node('builtintype', array( A, B ));
}

/* LONG LONG VARIATIONS */

builtintype(R) ::= long(A) long(B) . {
    $this->currentrule = new block_formal_langs_description_rule("64-битный целый тип", array("признак длинного целого", "длинный целый тип"));
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= signed(A) long(B) long(C) . {
    $this->currentrule = new block_formal_langs_description_rule("знаковый 64-битный целый тип", array("признак знаковости", "признак длинного целого", "длинный целый тип"));
	R = $this->create_node('builtintype', array( A, B, C ));
}

builtintype(R) ::= unsigned(A) long(B) long(C) . {
    $this->currentrule = new block_formal_langs_description_rule("беззнаковый 64-битный целый тип", array("признак беззнаковости", "признак длинного целого", "длинный целый тип"));
	R = $this->create_node('builtintype', array( A, B, C ));
}


/* KEYWORDS WITH DATA */



unsigned(R) ::= UNSIGNED(A) . {
	R = A;
}
unsigned(R) ::= UNSIGNED(A) comment_list (B). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('unsigned', array( A, B));
}

signed(R) ::= SIGNED(A) . {
	R = A;
}

signed(R) ::= SIGNED(A) comment_list (B). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('signed', array( A, B));
}

long(R) ::= LONG(A) . {
	R = A;
}

long(R) ::= LONG(A) comment_list (B). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('long', array( A, B));
}

short(R) ::= SHORT(A) . {
	R = A;
}

short(R) ::= SHORT(A) comment_list (B). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('short', array( A, B));
}

int(R) ::= INT(A) . {
	R = A;
}

int(R) ::= INT(A) comment_list (B). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('int', array( A, B));
}

char(R) ::= CHAR(A) . {
	R = A;
}

char(R) ::= CHAR(A) comment_list (B). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('char', array( A, B));
}

double(R) ::= DOUBLE(A) . {
	R = A;
}

double(R) ::= DOUBLE(A) comment_list (B). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('double', array( A, B));
}

float(R) ::= FLOAT(A) . {
	R = A;
}

float(R) ::= FLOAT(A) comment_list (B). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('float', array( A, B));
}

void(R) ::= VOID(A) . {
	R = A;
}

void(R) ::= VOID(A) comment_list (B). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('void', array( A, B));
}

greater(R) ::= GREATER(A) . {
	R = A;
}

greater(R) ::= GREATER(A) comment_list (B). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('greater', array( A, B));
}

comma(R) ::= COMMA(A) . {
	R = A;
}

comma(R) ::= COMMA(A) comment_list (B). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('comma', array( A, B));
}

lesser(R) ::= LESSER(A) . {
	R = A;
}

lesser(R) ::= LESSER(A) comment_list (B). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('lesser', array( A, B));
}

multiply(R) ::= MULTIPLY(A) . {
	R = A;
}

multiply(R) ::= MULTIPLY(A) comment_list (B). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('multiply', array( A, B));
}

ampersand(R) ::= AMPERSAND(A) . {
	R = A;
}

ampersand(R) ::= AMPERSAND(A) comment_list (B). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('ampersand', array( A, B));
}

constkwd(R) ::= CONSTKWD(A) . {
	R = A;
}

constkwd(R) ::= CONSTKWD(A) comment_list (B). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('constkwd', array( A, B));
}

typename(R) ::= TYPENAME(A) . {
	R = A;
}

typename(R) ::= TYPENAME(A) comment_list (B). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('typename', array( A, B));
}

namespace_resolve_terminal(R) ::= NAMESPACE_RESOLVE(A) . {
	R = A;
}

namespace_resolve_terminal(R) ::= NAMESPACE_RESOLVE(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('namespace_resolve_terminal', array( A, B));
}

leftroundbracket(R) ::= LEFTROUNDBRACKET(A) . {
	R = A;
}

leftroundbracket(R) ::= LEFTROUNDBRACKET(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('leftroundbracket', array( A, B));
}

rightroundbracket(R) ::= RIGHTROUNDBRACKET(A) . {
	R = A;
}

rightroundbracket(R) ::= RIGHTROUNDBRACKET(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('rightroundbracket', array( A, B));
}

sizeof(R) ::= SIZEOF(A) . {
	R = A;
}

sizeof(R) ::= SIZEOF(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('sizeof', array( A, B));
}

identifier(R) ::= IDENTIFIER(A) . {
	R = A;
}

identifier(R) ::= IDENTIFIER(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('identifier', array( A, B));
}

preprocessor_concat(R) ::= PREPROCESSOR_CONCAT(A) . {
	R = A;
}

preprocessor_concat(R) ::= PREPROCESSOR_CONCAT(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('preprocessor_concat_terminal', array( A, B));
}

preprocessor_stringify(R) ::= PREPROCESSOR_STRINGIFY(A) . {
	R = A;
}

preprocessor_stringify(R) ::= PREPROCESSOR_STRINGIFY(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('preprocessor_stringify_terminal', array( A, B));
}


string(R) ::= STRING(A) . {
	R = A;
}

string(R) ::= STRING(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('string', array( A, B));
}

string(R) ::= string(A) STRING(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('string', array( A, B));
}

string(R) ::= string(A) STRING(B) comment_list(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s", "%s"));
	R = $this->create_node('string', array( A, B, C));
}

character(R) ::= CHARACTER(A) . {
	R = A;
}

character(R) ::= CHARACTER(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('character', array( A, B));
}

numeric(R) ::= NUMERIC(A) . {
	R = A;
}

numeric(R) ::= NUMERIC(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('numeric', array( A, B));
}

leftsquarebracket(R) ::= LEFTSQUAREBRACKET(A) . {
	R = A;
}

leftsquarebracket(R) ::= LEFTSQUAREBRACKET(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('leftsquarebracket', array( A, B));
}

rightsquarebracket(R) ::= RIGHTSQUAREBRACKET(A) . {
	R = A;
}

rightsquarebracket(R) ::= RIGHTSQUAREBRACKET(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('rightsquarebracket', array( A, B));
}

leftfigurebracket(R) ::= LEFTFIGUREBRACKET(A) . {
	R = A;
}

leftfigurebracket(R) ::= LEFTFIGUREBRACKET(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('leftfigurebracket', array( A, B));
}

rightfigurebracket(R) ::= RIGHTFIGUREBRACKET(A) . {
	R = A;
}

rightfigurebracket(R) ::= RIGHTFIGUREBRACKET(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('rightfigurebracket', array( A, B));
}

assign(R) ::= ASSIGN(A) . {
	R = A;
}

assign(R) ::= ASSIGN(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('assign', array( A, B));
}

reinterpret_cast(R) ::= REINTERPRET_CAST(A) . {
	R = A;
}

reinterpret_cast(R) ::= REINTERPRET_CAST(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('reinterpret_cast', array( A, B));
}

dynamic_cast(R) ::= DYNAMIC_CAST(A) . {
	R = A;
}

dynamic_cast(R) ::= DYNAMIC_CAST(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('dynamic_cast', array( A, B));
}

static_cast(R) ::= STATIC_CAST(A) . {
	R = A;
}

static_cast(R) ::= STATIC_CAST(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('static_cast', array( A, B));
}

const_cast(R) ::= CONST_CAST(A) . {
	R = A;
}

const_cast(R) ::= CONST_CAST(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('const_cast', array( A, B));
}

rightarrow(R) ::= RIGHTARROW(A) . {
	R = A;
}

rightarrow(R) ::= RIGHTARROW(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('rightarrow', array( A, B));
}

dot(R) ::= DOT(A) . {
	R = A;
}

dot(R) ::= DOT(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('dot', array( A, B));
}

decrement(R) ::= DECREMENT(A) . {
	R = A;
}

decrement(R) ::= DECREMENT(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('decrement', array( A, B));
}

increment(R) ::= INCREMENT(A) . {
	R = A;
}

increment(R) ::= INCREMENT(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('decrement', array( A, B));
}

plus(R) ::= PLUS(A) . {
	R = A;
}

plus(R) ::= PLUS(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('plus', array( A, B));
}

minus(R) ::= MINUS(A) . {
	R = A;
}

minus(R) ::= MINUS(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('minus', array( A, B));
}

binarynot(R) ::= BINARYNOT(A) . {
	R = A;
}

binarynot(R) ::= BINARYNOT(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('binarynot', array( A, B));
}

logicalnot(R) ::= LOGICALNOT(A) . {
	R = A;
}

logicalnot(R) ::= LOGICALNOT(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('logicalnot', array( A, B));
}

division(R) ::= DIVISION(A) . {
	R = A;
}

division(R) ::= DIVISION(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('division', array( A, B));
}

modulosign(R) ::= MODULOSIGN(A) . {
	R = A;
}

modulosign(R) ::= MODULOSIGN(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('modulosign', array( A, B));
}

rightshift(R) ::= RIGHTSHIFT(A) . {
	R = A;
}

rightshift(R) ::= RIGHTSHIFT(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('rightshift', array( A, B));
}

leftshift(R) ::= LEFTSHIFT(A) . {
	R = A;
}

leftshift(R) ::= LEFTSHIFT(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('leftshift', array( A, B));
}

greater_or_equal(R) ::= GREATER_OR_EQUAL(A) . {
	R = A;
}

greater_or_equal(R) ::= GREATER_OR_EQUAL(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('greater_or_equal', array( A, B));
}

lesser_or_equal(R) ::= LESSER_OR_EQUAL(A) . {
	R = A;
}

lesser_or_equal(R) ::= LESSER_OR_EQUAL(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('lesser_or_equal', array( A, B));
}

equal(R) ::= EQUAL(A) . {
	R = A;
}

equal(R) ::= EQUAL(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('equal', array( A, B));
}

not_equal(R) ::= NOT_EQUAL(A) . {
	R = A;
}

not_equal(R) ::= NOT_EQUAL(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('not_equal', array( A, B));
}

binaryor(R) ::= BINARYOR(A) . {
	R = A;
}

binaryor(R) ::= BINARYOR(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('binaryor', array( A, B));
}

binaryxor(R) ::= BINARYXOR(A) . {
	R = A;
}

binaryxor(R) ::= BINARYXOR(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('binaryor', array( A, B));
}

logicalor(R) ::= LOGICALOR(A) . {
	R  = A;
}

logicalor(R) ::= LOGICALOR(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('logicalor', array( A, B));
}

logicaland(R) ::= LOGICALAND(A) . {
	R  = A;
}

logicaland(R) ::= LOGICALAND(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('logicaland', array( A, B));
}

minus_assign(R) ::= MINUS_ASSIGN(A) . {
	R  = A;
}

minus_assign(R) ::= MINUS_ASSIGN(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('minus_assign', array( A, B));
}

plus_assign(R) ::= PLUS_ASSIGN(A) . {
	R  = A;
}

plus_assign(R) ::= PLUS_ASSIGN(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('plus_assign', array( A, B));
}

multiply_assign(R) ::= MULTIPLY_ASSIGN(A) . {
	R  = A;
}

multiply_assign(R) ::= MULTIPLY_ASSIGN(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('multiply_assign', array( A, B));
}

division_assign(R) ::= DIVISION_ASSIGN(A) . {
	R  = A;
}

division_assign(R) ::= DIVISION_ASSIGN(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('division_assign', array( A, B));
}

modulo_assign(R) ::= MODULO_ASSIGN(A) . {
	R  = A;
}

modulo_assign(R) ::= MODULO_ASSIGN(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('modulo_assign', array( A, B));
}

leftshift_assign(R) ::= LEFTSHIFT_ASSIGN(A) . {
	R  = A;
}

leftshift_assign(R) ::= LEFTSHIFT_ASSIGN(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('leftshift_assign', array( A, B));
}

rightshift_assign(R) ::= RIGHTSHIFT_ASSIGN(A) . {
	R  = A;
}

rightshift_assign(R) ::= RIGHTSHIFT_ASSIGN(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('rightshift_assign', array( A, B));
}

binaryand_assign(R) ::= BINARYAND_ASSIGN(A) . {
	R  = A;
}

binaryand_assign(R) ::= BINARYAND_ASSIGN(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('binaryand_assign', array( A, B));
}

binaryor_assign(R) ::= BINARYOR_ASSIGN(A) . {
	R  = A;
}

binaryor_assign(R) ::= BINARYOR_ASSIGN(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('binaryor_assign', array( A, B));
}

binaryxor_assign(R) ::= BINARYXOR_ASSIGN(A) . {
	R  = A;
}

binaryxor_assign(R) ::= BINARYXOR_ASSIGN(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('binaryxor_assign', array( A, B));
}

friendkwd(R) ::= FRIENDKWD(A) . {
	R  = A;
}

friendkwd(R) ::= FRIENDKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('friendkwd', array( A, B));
}

volatilekwd(R) ::= VOLATILEKWD(A) . {
	R  = A;
}

volatilekwd(R) ::= VOLATILEKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('volatilekwd', array( A, B));
}

registerkwd(R) ::= REGISTERKWD(A) . {
	R  = A;
}

registerkwd(R) ::= REGISTERKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('volatilekwd', array( A, B));
}

externkwd(R) ::= EXTERNKWD(A) . {
	R  = A;
}

externkwd(R) ::= EXTERNKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('volatilekwd', array( A, B));
}

statickwd(R) ::= STATICKWD(A) . {
	R  = A;
}

statickwd(R) ::= STATICKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('statickwd', array( A, B));
}

delete(R) ::= DELETE(A) . {
	R  = A;
}

delete(R) ::= DELETE(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('delete', array( A, B));
}

newkwd(R) ::= NEWKWD(A) . {
	R  = A;
}

newkwd(R) ::= NEWKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('delete', array( A, B));
}

breakkwd(R) ::= BREAKKWD(A) . {
	R = A;
} 

breakkwd(R) ::= BREAKKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('breakkwd', array( A, B));
}

typedef(R) ::= TYPEDEF(A) . {
	R = A;
} 

typedef(R) ::= TYPEDEF(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('typedef', array( A, B));
}

ifkwd(R) ::= IFKWD(A) . {
	R = A;
} 

ifkwd(R) ::= IFKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('ifkwd', array( A, B));
}

elsekwd(R) ::= ELSEKWD(A) . {
	R = A;
} 

elsekwd(R) ::= ELSEKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('elsekwd', array( A, B));
}

defaultkwd(R) ::= DEFAULTKWD(A) . {
	R = A;
} 

defaultkwd(R) ::= DEFAULTKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('defaultkwd', array( A, B));
}

casekwd(R) ::= CASEKWD(A) . {
	R = A;
} 

casekwd(R) ::= CASEKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('casekwd', array( A, B));
}

colon(R) ::= COLON(A) . {
	R = A;
} 

colon(R) ::= COLON(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('colon', array( A, B));
}

switchkwd(R) ::= SWITCHKWD(A) . {
	R = A;
} 

switchkwd(R) ::= SWITCHKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('switchkwd', array( A, B));
}

ellipsis(R) ::= ELLIPSIS(A) . {
	R = A;
}

ellipsis(R) ::= ELLIPSIS(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('ellipsis', array( A, B));
}

catchkwd(R) ::= CATCHKWD(A) . {
	R = A;
}

catchkwd(R) ::= CATCHKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('catchkwd', array( A, B));
}

trykwd(R) ::= TRYKWD(A) . {
	R = A;
}

trykwd(R) ::= TRYKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('trykwd', array( A, B));
}

gotokwd(R) ::= GOTOKWD(A) . {
	R = A;
}

gotokwd(R) ::= GOTOKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('gotokwd', array( A, B));
}

continuekwd(R) ::= CONTINUEKWD(A) . {
	R = A;
}

continuekwd(R) ::= CONTINUEKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('continuekwd', array( A, B));
}

returnkwd(R) ::= RETURNKWD(A) . {
	R = A;
}

returnkwd(R) ::= RETURNKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('returnkwd', array( A, B));
}

semicolon(R) ::= SEMICOLON(A) . {
	R = A;
}

semicolon(R) ::= SEMICOLON(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('semicolon', array( A, B));
}

dokwd(R) ::= DOKWD(A) . {
	R = A;
}

dokwd(R) ::= DOKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('dokwd', array( A, B));
}

whilekwd(R) ::= WHILEKWD(A) . {
	R = A;
}

whilekwd(R) ::= WHILEKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('whilekwd', array( A, B));
}

preprocessor_include(R) ::= PREPROCESSOR_INCLUDE(A) . {
	R = A;
}

preprocessor_include(R) ::= PREPROCESSOR_INCLUDE(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('preprocessor_include', array( A, B));
}

preprocessor_define(R) ::= PREPROCESSOR_DEFINE(A) . {
	R = A;
}

preprocessor_define(R) ::= PREPROCESSOR_DEFINE(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('preprocessor_define', array( A, B));
}

preprocessor_if(R) ::= PREPROCESSOR_IF(A) . {
	R = A;
}

preprocessor_if(R) ::= PREPROCESSOR_IF(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('preprocessor_if', array( A, B));
}

preprocessor_ifdef(R) ::= PREPROCESSOR_IFDEF(A) . {
	R = A;
}

preprocessor_ifdef(R) ::= PREPROCESSOR_IFDEF(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('preprocessor_ifdef', array( A, B));
}

preprocessor_else_terminal(R) ::= PREPROCESSOR_ELSE(A) . {
	R = A;
}

preprocessor_else_terminal(R) ::= PREPROCESSOR_ELSE(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('preprocessor_else_terminal', array( A, B));
}

preprocessor_elif_terminal(R) ::= PREPROCESSOR_ELIF(A) . {
	R = A;
}

preprocessor_elif_terminal(R) ::= PREPROCESSOR_ELIF(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('preprocessor_elif_terminal', array( A, B));
}

preprocessor_endif(R) ::= PREPROCESSOR_ENDIF(A) . {
	R = A;
}

preprocessor_endif(R) ::= PREPROCESSOR_ENDIF(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('preprocessor_endif', array( A, B));
}

outer_constructor_name_terminal(R) ::= OUTER_CONSTRUCTOR_NAME(A) . {
	R = A;
}

outer_constructor_name_terminal(R) ::= OUTER_CONSTRUCTOR_NAME(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('outer_constructor_name_terminal', array( A, B));
}

operatoroverloaddeclaration(R) ::= OPERATOROVERLOADDECLARATION(A) . {
	R = A;
}

operatoroverloaddeclaration(R) ::= OPERATOROVERLOADDECLARATION(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('operatoroverloaddeclaration', array( A, B));
}

enumkwd(R) ::= ENUMKWD(A) . {
	R = A;
}

enumkwd(R) ::= ENUMKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('enumkwd', array( A, B));
}

slotskwd(R) ::= SLOTSKWD(A) . {
	R = A;
}

slotskwd(R) ::= SLOTSKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('slotskwd', array( A, B));
}

signalskwd(R) ::= SIGNALSKWD(A) . {
	R = A;
}

signalskwd(R) ::= SIGNALSKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('signalskwd', array( A, B));
}

privatekwd(R) ::= PRIVATEKWD(A) . {
	R = A;
}

privatekwd(R) ::= PRIVATEKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('privatekwd', array( A, B));
}

protectedkwd(R) ::= PROTECTEDKWD(A) . {
	R = A;
}

protectedkwd(R) ::= PROTECTEDKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('protectedkwd', array( A, B));
}

publickwd(R) ::= PUBLICKWD(A) . {
	R = A;
}

publickwd(R) ::= PUBLICKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('publickwd', array( A, B));
}

unionkwd(R) ::= UNIONKWD(A) . {
	R = A;
}

unionkwd(R) ::= UNIONKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('unionkwd', array( A, B));
}

structkwd(R) ::= STRUCTKWD(A) . {
	R = A;
}

structkwd(R) ::= STRUCTKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('structkwd', array( A, B));
}

classkwd(R) ::= CLASSKWD(A) . {
	R = A;
}

classkwd(R) ::= CLASSKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('classkwd', array( A, B));
}

templatekwd(R) ::= TEMPLATEKWD(A) . {
	R = A;
}

templatekwd(R) ::= TEMPLATEKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('templatekwd', array( A, B));
}

typenamekwd(R) ::= TYPENAMEKWD(A) . {
	R = A;
}

typenamekwd(R) ::= TYPENAMEKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('typenamekwd', array( A, B));
}

namespacekwd(R) ::= NAMESPACEKWD(A) . {
	R = A;
}

namespacekwd(R) ::= NAMESPACEKWD(A) comment_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R = $this->create_node('namespacekwd', array( A, B));
}

/* COMMENTS */

comment_list(R) ::= comment_list(A) COMMENT(B) . {
	$this->currentrule = new block_formal_langs_description_rule("список комментариев", array("%s", "%s"));
	R = $this->create_node('comment_list', array( A, B));
}


comment_list(R) ::= COMMENT(A) . {
	R = A;
}


