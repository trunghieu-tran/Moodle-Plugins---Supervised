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

%nonassoc THENKWD .
%left    ELSEKWD.
%left    LOGICALAND.
%left    BINARYAND.
%left    BINARYOR.
%left    LOGICALOR.
%left    AMPERSAND.
%left    BINARYXOR.
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
	$this->currentrule = new block_formal_langs_description_rule("список выражения %l(stmt_or_defined_macro)", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('stmt_list', array(A, B));
}

stmt_list(R) ::= stmt_or_defined_macro(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('stmt_list', array(A));
}

stmt(R) ::= NAMESPACEKWD(A) IDENTIFIER(B) namespace_body(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово объявления пространства имен", "идентификатор", "%ur(именительный)"));
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('stmt', array(A, B, C));
}

namespace_body(R) ::= LEFTFIGUREBRACKET(A) RIGHTFIGUREBRACKET(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("левая фигурная скобка", "правая фигурная скобка"));
	R = $this->create_node('namespace_body', array( A, B ));
}

namespace_body(R) ::= LEFTFIGUREBRACKET(A) stmt_list(B) RIGHTFIGUREBRACKET(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("левая фигурная скобка", "%ur(именительный)", "правая фигурная скобка"));
	R = $this->create_node('namespace_body', array( A, B, C ));
}

/* CLASES, UNIONS, STRUCTURES */

stmt(R) ::= class_or_union_or_struct(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('class_or_union_or_struct', array(A));
}

class_or_union_or_struct(R) ::= type_meta_specifier_with_template_def(A) IDENTIFIER(B) structure_body(C) IDENTIFIER(D) SEMICOLON(E) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%ur(именительный)", "%s", "точка с запятой"));
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('class_or_union_or_struct', array(A, B, C, D, E));
}

class_or_union_or_struct(R) ::= type_meta_specifier_with_template_def(A) IDENTIFIER(B) structure_body(C) SEMICOLON(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%ur(именительный)", "точка с запятой"));
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('class_or_union_or_struct', array(A, B, C, D));
}

class_or_union_or_struct(R) ::= type_meta_specifier_with_template_def(A) IDENTIFIER(B) SEMICOLON(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "точка с запятой"));
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('class_or_union_or_struct', array(A, B, C));
}

class_or_union_or_struct(R) ::= type_meta_specifier_with_template_def(A) structure_body(B) IDENTIFIER(C) SEMICOLON(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%s", "точка с запятой"));
	R = $this->create_node('class_or_union_or_struct', array(A, B, C, D));
}

class_or_union_or_struct(R) ::= type_meta_specifier_with_template_def(A) structure_body(B) SEMICOLON(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "точка с запятой"));
	R = $this->create_node('class_or_union_or_struct', array(A, B, C));
}

type_meta_specifier_with_template_def(R) ::=  template_def(a) type_meta_specifier(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный) и %2(именительный)", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('type_meta_specifier_with_template_def', array(A, B));
}

type_meta_specifier_with_template_def(R) ::= type_meta_specifier(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('type_meta_specifier_with_template_def', array(A));
}

type_meta_specifier(R) ::= CLASSKWD(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово объявления класса"));
	R = $this->create_node('type_meta_specifier', array(A));
}

type_meta_specifier(R) ::= STRUCTKWD(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово объявления структуры"));
	R = $this->create_node('type_meta_specifier', array(A));
}

type_meta_specifier(R) ::= UNIONKWD(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово объявления объединения"));
	R = $this->create_node('type_meta_specifier', array(A));
}

structure_body(R) ::= LEFTFIGUREBRACKET(A) RIGHTFIGUREBRACKET(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("левая фигурная скобка", "правая фигурная скобка"));
	R = $this->create_node('structure_body', array( A, B ));
}

structure_body(R) ::= LEFTFIGUREBRACKET(A) stmt_or_visibility_spec_list(B) RIGHTFIGUREBRACKET(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("левая фигурная скобка", "%ur(именительный)", "правая фигурная скобка"));
	R = $this->create_node('structure_body', array( A, B, C ));
}

stmt_or_visibility_spec_list(R) ::= stmt_or_visibility_spec(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('stmt_or_visibility_spec_list', array(A));
}

stmt_or_visibility_spec_list(R) ::= stmt_or_visibility_spec_list(A) stmt_or_visibility_spec(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%l(stmt_or_visibility_spec)", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('stmt_or_visibility_spec_list', array(A, B));
}

/* VISIBILITY FOR METHODS AND FIELDS OF STRUCTS AND CLASSES*/

stmt_or_visibility_spec(R) ::= visibility_spec_full(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('stmt_or_visibility_spec', array(A));
	R  = A;
}

stmt_or_visibility_spec(R) ::= stmt_or_defined_macro(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('stmt_or_visibility_spec', array(A));
}

visibility_spec_full(R) ::= visibility_spec(A) COLON(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "двоеточие"));
	R = $this->create_node('visibility_spec_full', array( A, B ));
}

visibility_spec_full(R) ::= visibility_spec(A) signal_slots(B) COLON(C). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "двоеточие"));
	R = $this->create_node('visibility_spec_full', array( A, B, C ));
}

visibility_spec(R) ::= PUBLICKWD(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово открытой видимости"));
	R = $this->create_node('visibility_spec', array(A));
}

visibility_spec(R) ::= PROTECTEDKWD(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово защищенной видимости"));
	R = $this->create_node('visibility_spec', array(A));
}

visibility_spec(R) ::= PRIVATEKWD(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово скрытой видимости"));
	R = $this->create_node('visibility_spec', array(A));
}

signal_slots(R) ::= SIGNALSKWD(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово объявления сигнала"));
	R = $this->create_node('signal_slots', array(A));
}

signal_slots(R) ::= SLOTSKWD(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово объявления слота"));
	R = $this->create_node('signal_slots', array(A));
}

/* ENUM */

stmt_or_defined_macro(R) ::= ENUMKWD(A) IDENTIFIER(B) SEMICOLON(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово перечисления", "%s", "точка с запятой"));
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C));
}

stmt_or_defined_macro(R) ::= ENUMKWD(A) IDENTIFIER(B)  enum_body(C) SEMICOLON(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово перечисления", "%s", "%ur(именительный)", "точка с запятой"));
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D));
}

stmt_or_defined_macro(R) ::= ENUMKWD(A)  enum_body(B) SEMICOLON(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово перечисления", "%ur(именительный)", "точка с запятой"));
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C));
}

stmt_or_defined_macro(R) ::= ENUMKWD(A) IDENTIFIER(B) enum_body(C) IDENTIFIER(D) SEMICOLON(E) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово перечисления", "%s", "%ur(именительный)", "%s", "точка с запятой"));
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D, E));
}

stmt_or_defined_macro(R) ::= ENUMKWD(A)  enum_body(B) IDENTIFIER(C) SEMICOLON(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово перечисления", "%ur(именительный)", "%s", "точка с запятой"));
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D));
}

enum_body(R) ::= LEFTFIGUREBRACKET(A) enum_value_list(B) RIGHTFIGUREBRACKET(C) . {
	$this->currentrule = new block_formal_langs_description_rule("тело перечисления", array("левая фигурная скобка", "%ur(именительный)", "правая фигурная скобка"));
	R = $this->create_node('enum_body', array(A, B, C));
}

enum_body(R) ::= LEFTFIGUREBRACKET(A) RIGHTFIGUREBRACKET(B) . {
	$this->currentrule = new block_formal_langs_description_rule("тело перечисления", array("левая фигурная скобка", "правая фигурная скобка"));
	R = $this->create_node('enum_body', array(A, B));
}

enum_value_list(R) ::= enum_value_list(A) COMMA(B) enum_value(C) . {
	$this->currentrule = new block_formal_langs_description_rule("список значений перечисления %l(enum_value)", array("%ur(именительный)", "запятая", "%ur(именительный)"));
	R = $this->create_node('enum_value_list', array(A, B, C));
}

enum_value_list(R) ::= enum_value(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('enum_value_list', array(A));
}

enum_value(R) ::= IDENTIFIER(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%ur(именительный)", array("%s"));
	R = $this->create_node('enum_value', array(A));
}

enum_value(R) ::= IDENTIFIER(A) ASSIGN(B) expr_atom(C). {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "операция присвоения", "%s"));
	R = $this->create_node('enum_value', array(A, B, C));
}

/* FUNCTIONS */

stmt_or_defined_macro(R) ::= type(A) possible_function_name(B) formal_args_list_with_or_without_const(C) function_body(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D));
}

stmt_or_defined_macro(R) ::= type_with_qualifier(A) possible_function_name(C) formal_args_list_with_or_without_const(D) function_body(E) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('stmt_or_defined_macro', array(A, C, D, E));
}

stmt_or_defined_macro(R) ::= template_def(A) type_with_qualifier(B) possible_function_name(C) formal_args_list_with_or_without_const(D) function_body(E) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%ur(именительный)", "%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D, E));
}

stmt_or_defined_macro(R) ::= template_def(A) type(B) possible_function_name(C) formal_args_list_with_or_without_const(D) function_body(E) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "%ur(именительный)", "%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D, E));
}

/* CONSTRUCTORS */
stmt_or_defined_macro(R) ::= template_def(A) non_const_type(B) LEFTROUNDBRACKET(C) RIGHTROUNDBRACKET(D) function_body(E) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "левая круглая скобка", "правая круглая скобка", "%ur(именительный)"));
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D));
}

/* Somehow putting type here allows more than we need, but also solves conflicts */
stmt_or_defined_macro(R) ::= type(A) LEFTROUNDBRACKET(B) RIGHTROUNDBRACKET(C) function_body(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("тип", "левая круглая скобка", "правая круглая скобка", "%ur(именительный)"));
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D));
}

stmt_or_defined_macro(R) ::= template_def(A) BINARYNOT(B) CUSTOMTYPENAME(C) LEFTROUNDBRACKET(D) RIGHTROUNDBRACKET(E) function_body(F) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "тильда", "%s", "левая круглая скобка", "правая круглая скобка", "%ur(именительный)"));
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D, E, F));
}

/*
stmt_or_defined_macro(R) ::= template_def(A) primitive_or_complex_type(B) NAMESPACE_RESOLVE(C) BINARYNOT(D) CUSTOMTYPENAME(E) LEFTROUNDBRACKET(F) RIGHTROUNDBRACKET(G) function_body(H) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "оператор разрешения пространства имен", "тильда", "%s", "левая круглая скобка", "правая круглая скобка", "%ur(именительный)"));
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C, D, E, F));
}
*/

stmt_or_defined_macro(R) ::= BINARYNOT(B) CUSTOMTYPENAME(C) LEFTROUNDBRACKET(D) RIGHTROUNDBRACKET(E) function_body(F) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("тильда", "%s", "левая круглая скобка", "правая круглая скобка", "%ur(именительный)"));
	R = $this->create_node('stmt_or_defined_macro', array(B, C, D, E, F));
}

/*
stmt_or_defined_macro(R) ::= primitive_or_complex_type(B) NAMESPACE_RESOLVE(C) BINARYNOT(D) CUSTOMTYPENAME(E) LEFTROUNDBRACKET(F) RIGHTROUNDBRACKET(G) function_body(H) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "оператор разрешения пространства имен", "тильда", "%s", "левая круглая скобка", "правая круглая скобка", "%ur(именительный)"));
	R = $this->create_node('stmt_or_defined_macro', array(B, C, D, E, F));
}
*/

/* TEMPLATES */

/* Due to imperfect resolution of names, we allow such constructions to make constructors and destructors compilable. 
   That is weird but it doesn't give us any kind of errors 
 */

template_def(R) ::= TEMPLATEKWD(A) LESSER(B) GREATER(C) . {
	$this->currentrule = new block_formal_langs_description_rule("определение шаблона", array("ключевое слово определения шаблона", "начало аргументов шаблона", "конец аргументов шаблона"));
	R = $this->create_node('template_def', array(A, B, C));
}

template_def(R) ::= TEMPLATEKWD(A) LESSER(B) template_spec_list(C) GREATER(D) . {
	$this->currentrule = new block_formal_langs_description_rule("определение шаблона", array("ключевое слово определения шаблона", "начало аргументов шаблона", "%ur(именительный)", "конец аргументов шаблона"));
	R = $this->create_node('template_def', array(A, B, C, D));
}

template_spec_list(R) ::= template_spec_list(A) COMMA(B) template_spec(C) . {
	$this->currentrule = new block_formal_langs_description_rule("список параметров шаблона %l(template_spec)", array("%ur(именительный)", "запятая", "%ur(именительный)"));
	R = $this->create_node('template_spec_list', array(A, B, C));
}

template_spec_list(R) ::= template_spec(A) . {
	$this->currentrule = new block_formal_langs_description_rule("список параметров шаблона", array("%ur(именительный)"));
	R = $this->create_node('template_spec_list', array(A));
}

template_spec(R) ::= template_typename(A)  IDENTIFIER(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s"));
	$this->mapper->introduce_type(B->value());
	R = $this->create_node('template_spec', array(A, B));
}


template_typename(R) ::= TYPENAMEKWD(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово имени типа"));
	R = $this->create_node('template_typename', array(A));
}

template_typename(R) ::= CLASSKWD(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово объявления класса"));
	R = $this->create_node('template_typename', array(A));
}

template_typename(R) ::= STRUCTKWD(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово объявления структуры"));
	R = $this->create_node('template_typename', array(A));
}

template_typename(R) ::= ENUMKWD(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово перечисления"));
	R = $this->create_node('template_typename', array(A));
}


function_body(R) ::= LEFTFIGUREBRACKET(A) stmt_list(B) RIGHTFIGUREBRACKET(C) . {
	$this->currentrule = new block_formal_langs_description_rule("тело функции", array("левая фигурная скобка", "%ur(именительный)", "правая фигурная скобка"));
	R = $this->create_node('function_body', array(A, B, C));
}

function_body(R) ::= LEFTFIGUREBRACKET(A)  RIGHTFIGUREBRACKET(B) . {
	$this->currentrule = new block_formal_langs_description_rule("тело функции", array("левая фигурная скобка", "правая фигурная скобка"));
	R = $this->create_node('function_body', array(A, B));
}

function_body(R) ::= SEMICOLON(A) . {
    $this->currentrule = new block_formal_langs_description_rule("тело функции", array("точка с запятой"));
	R = $this->create_node('function_body', array(A));
}


possible_function_name(R) ::= primitive_or_complex_type(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R = $this->create_node('possible_function_name', array(A));
}

possible_function_name(R) ::= IDENTIFIER(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R = $this->create_node('possible_function_name', array(A));
}

possible_function_name(R) ::= OPERATOROVERLOADDECLARATION(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R = $this->create_node('possible_function_name', array(A));
}


/* ARGUMENTS */

formal_args_list_with_or_without_const(R) ::= formal_args_list(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('formal_args_list_with_or_without_const', array(A));
}

formal_args_list_with_or_without_const(R) ::= formal_args_list(A) CONSTKWD(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)", "ключевое слово константности"));
	R = $this->create_node('formal_args_list_with_or_without_const', array(A, B));
}

formal_args_list(R) ::= LEFTROUNDBRACKET(A) RIGHTROUNDBRACKET(B) . {
	$this->currentrule = new block_formal_langs_description_rule("список формальных аргументов", array("левая круглая скобка", "правая круглая скобка"));
	R = $this->create_node('args_list', array(A, B));
}

formal_args_list(R) ::= LEFTROUNDBRACKET(A) arg_list(B) RIGHTROUNDBRACKET(C) . {
	$this->currentrule = new block_formal_langs_description_rule("список формальных аргументов", array("левая круглая скобка", "%ur(именительный)", "правая круглая скобка"));
	R = $this->create_node('formal_args_list', array(A, B, C));
}

arg_list(R) ::= arg(A) . {
	$this->currentrule = new block_formal_langs_description_rule("список аргументов", array("%ur(именительный)"));
	R = $this->create_node('arg_list', array(A));
}

arg_list(R) ::= arg_list(A) COMMA(B) arg(C) . {
	$this->currentrule = new block_formal_langs_description_rule("список аргументов %l(arg)", array("%ur(именительный)", "запятая", "%ur(именительный)"));
	R = $this->create_node('arg_list', array(A, B, C));
}

arg(R) ::= type(A) IDENTIFIER(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s"));
	R = $this->create_node('arg', array(A, B));
}

/* PREPROCESSOR */

stmt_or_defined_macro(R) ::=  preprocessor_cond(A) stmt_list(B) PREPROCESSOR_ENDIF(C).  {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%ur(именительный)", "ключевое слово конца условного блока препроцессора"));
	R = $this->create_node('stmt_or_defined_macro', array(A, B, C));
}

stmt_or_defined_macro(R) ::=  preprocessor_cond(A) stmt_list(B) preprocessor_else_clauses(C) PREPROCESSOR_ENDIF(D).  {
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
 
preprocessor_elif(R) ::= PREPROCESSOR_ELIF(A) stmt_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое-слово \"если-то\" препроцессора", "%ur(именительный)"));
	R = $this->create_node('preprocessor_elif', array(A, B));
}

preprocessor_else(R) ::= PREPROCESSOR_ELSE(A) stmt_list(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое-слово \"если\" препроцессора", "%ur(именительный)"));
	R = $this->create_node('preprocessor_else', array(A, B));
}

preprocessor_cond(R)  ::= PREPROCESSOR_IFDEF(A) IDENTIFIER(B)   . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("условная директива препроцессора с условием что макроопределение определено", "%s"));
	R = $this->create_node('preprocessor_cond', array(A, B, C));
}

preprocessor_cond(R)  ::= PREPROCESSOR_IFDEF(A) CUSTOMTYPENAME(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("условная директива препроцессора с условием что макроопределение определено", "%s"));
	R = $this->create_node('preprocessor_cond', array(A, B, C));
}

preprocessor_cond(R) ::= PREPROCESSOR_IF(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%s", array("условная директива препроцессора вида \"если\""));
	R = $this->create_node('preprocessor_cond', array(A, B));
}

stmt_or_defined_macro(R) ::= PREPROCESSOR_DEFINE(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R = $this->create_node('stmt_or_defined_macro', array(A, B));
}

stmt_or_defined_macro(R) ::= stmt(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('stmt_or_defined_macro', array(A));
}

stmt(R) ::= PREPROCESSOR_INCLUDE(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R = $this->create_node('stmt', array(A));
}

/* LOOPS */

stmt(R) ::= WHILEKWD(A)
			LEFTROUNDBRACKET(B)
			expr_prec_11(C)		
			RIGHTROUNDBRACKET(D)
			stmt(E) 
			. {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово объявления цикла с предусловием", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка", "%ur(именительный)"));
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
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово  объявления цикла с постусловием", "%ur(именительный)", "ключевое слово начала условия в цикле с постусловием", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка", "точка с запятой"));
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
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово объявления цикла со счетчиком", "левая круглая скобка", "%ur(именительный)", "точка с запятой", "%ur(именительный)", "точка с запятой", "%ur(именительный)", "правая круглая скобка", "%ur(именительный)"));
	R = $this->create_node('for', array(A, B, C, D, E, F, G, H, I));
}			


/* RETURN */

stmt(R) ::= RETURNKWD(A) expr_prec_11(B) SEMICOLON(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово возврата результата", "%ur(именительный)", "точка с запятой"));
	R = $this->create_node('stmt', array(A, B, C));
}

stmt(R) ::= RETURNKWD(A) SEMICOLON(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово возврата результата", "точка с запятой"));
	R = $this->create_node('stmt', array(A, B));
}


/* CONTINUE */

stmt(R) ::= CONTINUEKWD(A) SEMICOLON(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово пропуска итерации цикла", "точка с запятой"));
	R = $this->create_node('continue', array(A, B));
}

/* GOTO-STATEMENTS */

stmt(R) ::= GOTOKWD(A) IDENTIFIER(B) SEMICOLON(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово безусловного перехода", "имя метки перехода для  операции %ul(именительный)", "точка с запятой"));
	R = $this->create_node('goto', array(A, B, C));
}

stmt(R) ::= GOTOKWD(A) CUSTOMTYPENAME(B) SEMICOLON(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово безусловного перехода", "имя метки перехода для  операции %ul(именительный)", "точка с запятой"));
	R = $this->create_node('goto', array(A, B, C));
}

stmt(R) ::= IDENTIFIER(A) COLON(B) . {
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

try(R) ::= TRYKWD(A) LEFTFIGUREBRACKET(B) RIGHTFIGUREBRACKET(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово начала небезопасного блока", "левая фигурная скобка", "правая фигурная скобка"));
	R = $this->create_node('try', array(A, B, C));
}

try(R) ::= TRYKWD(A) LEFTFIGUREBRACKET(B) stmt_list(C) RIGHTFIGUREBRACKET(D) . {
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

catch(R) ::=  CATCHKWD(A) LEFTROUNDBRACKET(B) expr_prec_11_or_ellipsis(C) RIGHTROUNDBRACKET(D) LEFTFIGUREBRACKET(E) RIGHTFIGUREBRACKET(F) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово ветки исключения", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка", "левая фигурная скобка", "правая фигурная скобка"));
	R = $this->create_node('catch', array(A, B, C, D, E, F));
}

catch(R) ::=  CATCHKWD(A) LEFTROUNDBRACKET(B) expr_prec_11_or_ellipsis(C) RIGHTROUNDBRACKET(D) LEFTFIGUREBRACKET(E) stmt_list(F) RIGHTFIGUREBRACKET(G) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово ветки исключения", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка", "левая фигурная скобка", "%ur(именительный)", "правая фигурная скобка"));
	R = $this->create_node('catch', array(A, B, C, D, E, F, G));
}

expr_prec_11_or_ellipsis(R) ::= expr_prec_11(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('expr_prec_11_or_ellipsis', array( A ));
}

expr_prec_11_or_ellipsis(R) ::= ELLIPSIS(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("эллипсис"));
	R = $this->create_node('expr_prec_11_or_ellipsis', array( A ));
}

/* EMPTY OPERATOR */

stmt(R) ::= SEMICOLON(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("точка с запятой"));
	R = $this->create_node('stmt', array( A ));
}

/* SWITCH-CASE-STATEMENTS */
 
stmt(R) ::= switch_stmt(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('stmt', array( A ));
}

switch_stmt(R) ::= SWITCHKWD(A) LEFTROUNDBRACKET(B) expr_prec_11(C) RIGHTROUNDBRACKET(D) LEFTFIGUREBRACKET(E) RIGHTFIGUREBRACKET(F) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово ветвления", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка", "левая фигурная скобка", "правая фигурная скобка"));
	R = $this->create_node('switch_stmt', array(A, B, C, D, E, F));
}

switch_stmt(R) ::= SWITCHKWD(A) LEFTROUNDBRACKET(B) expr_prec_11(C) RIGHTROUNDBRACKET(D) LEFTFIGUREBRACKET(E) switch_case_list(F) RIGHTFIGUREBRACKET(G) . {
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

case(R) ::= CASEKWD(A) expr_atom(B) COLON(C) stmt_list(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово, обозначающее ветку ветвления", "%ur(именительный)", "двоеточие", "%ur(именительный)"));
	R = $this->create_node('case', array(A, B, C, D));
}

case(R) ::= DEFAULTKWD(A) COLON(B) stmt_list(C) . {
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

if_then_else(R) ::=  if_then(A) ELSEKWD(B) stmt(C).  {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "ключевое слово \"иначе\"", "%ur(именительный)"));
	R = $this->create_node('if_then_else', array(A, B, C));
}

if_then(R) ::= IFKWD(A) LEFTROUNDBRACKET(B) expr_prec_11(C) RIGHTROUNDBRACKET(D) stmt(E) .  {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("ключевое слово \"если\"", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка", "%ur(именительный)"));
	R = $this->create_node('if_then', array(A, B, C, D, E));
}

/* STATEMENTS */

stmt(R) ::= LEFTFIGUREBRACKET(A) stmt_list(B) RIGHTFIGUREBRACKET(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("левая фигурная скобка", "%s", "правая фигурная скобка"));
	R = $this->create_node('stmt', array( A, B, C ));
}

stmt(R) ::=  TYPEDEF(A) type(B) IDENTIFIER(C) SEMICOLON(D) . { 
	$this->currentrule = new block_formal_langs_description_rule("объявление синонима типа", array("ключевое слово объявления синонима типа", "%s", "%s", "точка с запятой"));
	R = $this->create_node('stmt', array(A, B, C, D));
	$this->mapper->introduce_type(C->value());
}


stmt(R) ::= BREAKKWD(A) SEMICOLON(B) . {
	$this->currentrule = new block_formal_langs_description_rule("прерывание работы", array("ключевое слово прерывания работы", "точка с запятой"));
	R = $this->create_node('stmt', array(A, B));
}

stmt(R) ::= expr_prec_11(A) SEMICOLON(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)", "точка с запятой"));
	R = $this->create_node('stmt', array(A, B));
}

/* EXPRESSIONS OF ELEVENTH PRECEDENCE */

expr_prec_11(R) ::= NEWKWD(A) expr_prec_10(B)  . {
	$this->currentrule = new block_formal_langs_description_rule("выделение памяти", array("ключевое слово выделения памяти", "%ur(именительный)"));
	R = $this->create_node('new_kwd', array( A, B ));
} 

expr_prec_11(R) ::= DELETE(A) LEFTSQUAREBRACKET(B)  RIGHTSQUAREBRACKET(C)  expr_prec_10(D) . {
	$this->currentrule = new block_formal_langs_description_rule("освобождение памяти", array("ключевое слово освобождения памяти", "левая квадратная скобка", "правая квадратная скобка", "%ur(именительный)"));
	R = $this->create_node('delete_array', array( A, B, C, D ));
} 

expr_prec_11(R) ::= DELETE(A) expr_prec_10(B) . {
	$this->currentrule = new block_formal_langs_description_rule("освобождение памяти", array("ключевое слово освобождения памяти", "%ur(именительный)"));
	R = $this->create_node('delete_pointer', array( A, B ));
} 

expr_prec_11(R) ::= type(A) expr_atom(B) ASSIGN(C) expr_prec_9(D) . {
	$this->currentrule = new block_formal_langs_description_rule("объявление переменной %2(имя переменной)", array("%ur(именительный)", "%s", "оператор присваивания", "%ur(именительный)"));
	R = $this->create_node('variable_declaration_with_assignment', array( A, B, C, D ));
}

expr_prec_11(R) ::= type(A) primitive_or_complex_type(B) ASSIGN(C) expr_prec_9(D) . {
	$this->currentrule = new block_formal_langs_description_rule("объявление переменной %2(имя переменной)", array("%ur(именительный)", "%s", "оператор присваивания", "%ur(именительный)"));
	R = $this->create_node('variable_declaration_with_assignment', array( A, B, C, D ));
}

expr_prec_11(R) ::= type(A) IDENTIFIER(B) . {
	$this->currentrule = new block_formal_langs_description_rule("объявление переменной %2(имя переменной)", array("%ur(именительный)", "%s"));
	R = $this->create_node('variable_declaration', array( A, B ));
}

expr_prec_11(R) ::= type(A) primitive_or_complex_type(B)  . {
    $this->currentrule = new block_formal_langs_description_rule("объявление переменной %2(имя переменной)", array("%ur(именительный)", "%s"));
	R = $this->create_node('variable_declaration', array( A, B ));
}

expr_prec_11(R) ::= type_with_qualifier(A) IDENTIFIER(C) ASSIGN(D) expr_prec_9(E) . {
	$this->currentrule = new block_formal_langs_description_rule("объявление переменной %2(имя переменной)", array("%ur(именительный)", "%s", "оператор присваивания", "%ur(именительный)"));
	R = $this->create_node('variable_declaration_with_assignment', array( A,  C, D, E ));
}

expr_prec_11(R) ::= type_with_qualifier(A)  primitive_or_complex_type(C) ASSIGN(D) expr_prec_9(E) . {
	$this->currentrule = new block_formal_langs_description_rule("объявление переменной %2(имя переменной)", array("%ur(именительный)", "%s", "оператор присваивания", "%ur(именительный)"));
	R = $this->create_node('variable_declaration_with_assignment', array( A, C, D, E ));
}

expr_prec_11(R) ::= type_with_qualifier(A)  IDENTIFIER(C)  . {
	$this->currentrule = new block_formal_langs_description_rule("объявление переменной %2(имя переменной)", array("%ur(именительный)", "%s"));
	R = $this->create_node('variable_declaration', array( A, C ));
}

expr_prec_11(R) ::= type_with_qualifier(A) primitive_or_complex_type(C) . {
	$this->currentrule = new block_formal_langs_description_rule("объявление переменной %2(имя переменной)", array("%ur(именительный)", "%s"));
	R = $this->create_node('variable_declaration', array( A, C ));
}

expr_prec_11(R) ::= expr_prec_11(A) COMMA(B)  expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("список выражений %l(expr_prec_10)", array("%ur(именительный)", "запятая", "%ur(именительный)"));
	R = $this->create_node('expr_comma', array( A, B, C ));
}

expr_prec_11(R) ::= expr_prec_10(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* VARIABLE QUALIFIERS */

type_with_qualifier(R) ::= varqualifier(A) type(B) . {
	$this->currentrule = new block_formal_langs_description_rule("тип с квалифицирующим словом", array("%ur(именительный)", "%ur(именительный)"));
	$result = $this->create_node('type_with_qualifier', array( A, B ));
	R = $result;
}

varqualifier(R) ::= STATICKWD(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово для статичности значения"));
	R = A;
}

varqualifier(R) ::= EXTERNKWD(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово импорта из внешней части"));
	R = A;
}

varqualifier(R) ::= REGISTERKWD(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово, указания, что переменная должна содержаться в регистре процессора"));
	R = A;
}

varqualifier(R) ::= VOLATILEKWD(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово изменяемости"));
	R = A;
}

varqualifier(R) ::= FRIENDKWD(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("ключевое слово дружественности"));
	R = A;
}

/* EXPRESSIONS OF TENTH PRECEDENCE */

expr_prec_10(R) ::= expr_prec_9(A) BINARYXOR_ASSIGN(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание с побитовым исключающим ИЛИ\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция побитового исключающего ИЛИ с присваиванием", "%ur(именительный)"));
	R = $this->create_node('expr_binaryxor_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) BINARYOR_ASSIGN(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание с побитовым ИЛИ\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция побитового ИЛИ  с присваиванием", "%ur(именительный)"));
	R = $this->create_node('expr_binaryor_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) BINARYAND_ASSIGN(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание с побитовым И\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция побитового И  с присваиванием", "%ur(именительный)"));
	R = $this->create_node('expr_binaryand_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) RIGHTSHIFT_ASSIGN(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание со сдвигом вправо\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция присваивания со сдвигом вправо", "%ur(именительный)"));
	R = $this->create_node('expr_rightshift_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) LEFTSHIFT_ASSIGN(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание со сдвигом влево\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция присваивания со сдвигом влево", "%ur(именительный)"));
	R = $this->create_node('expr_leftshift_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) MODULO_ASSIGN(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание с получением остатка от деления\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция присваивания с получением остатка от модуля", "%ur(именительный)"));
	R = $this->create_node('expr_modulo_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) DIVISION_ASSIGN(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание с делением\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция присваивания с делением", "%ur(именительный)"));
	R = $this->create_node('expr_division_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) MULTIPLY_ASSIGN(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание с умножением\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция присваивания с умножением", "%ur(именительный)"));
	R = $this->create_node('expr_multiply_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) PLUS_ASSIGN(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание с суммированием\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция присваивания с суммированием", "%ur(именительный)"));
	R = $this->create_node('expr_plus_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) MINUS_ASSIGN(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание с вычитанием\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция присваивания с вычитанием", "%ur(именительный)"));
	R = $this->create_node('expr_minus_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) ASSIGN(B) expr_prec_10(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"присваивание\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция присваивания", "%ur(именительный)"));
	R = $this->create_node('expr_assign', array( A, B, C ));
}

expr_prec_10(R) ::= expr_prec_9(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* EXPRESSIONS OF NINTH PRECEDENCE */

expr_prec_9(R) ::= expr_prec_9(A) BINARYOR(B) expr_prec_8(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"побитового ИЛИ\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция логического ИЛИ", "%ur(именительный)"));
	R = $this->create_node('expr_binary_or', array( A, B, C ));
}

expr_prec_9(R) ::= expr_prec_9(A) LOGICALAND(B) expr_prec_8(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"логического И\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция логического И", "%ur(именительный)"));
	R = $this->create_node('expr_logical_and', array( A, B, C ));
}

expr_prec_9(R) ::= expr_prec_9(A) LOGICALOR(B) expr_prec_8(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"логического ИЛИ\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция логического ИЛИ", "%ur(именительный)"));
	R = $this->create_node('expr_logical_or', array( A, B, C ));
}

expr_prec_9(R) ::= expr_prec_9(A) BINARYXOR(B) expr_prec_8(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"исключающего ИЛИ\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция исключающего ИЛИ", "%ur(именительный)"));
	R = $this->create_node('expr_binary_xor', array( A, B, C ));
}

expr_prec_9(R) ::= expr_prec_9(A) AMPERSAND(B) expr_prec_8(C) . {
	// Well, that's what you get when you mix binary and and adress taking
	$this->currentrule = new block_formal_langs_description_rule("операция \"побитового И\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция побитового И", "%ur(именительный)"));
	R = $this->create_node('expr_binary_and', array( A, B, C ));
}

expr_prec_9(R) ::= expr_prec_9(A) NOT_EQUAL(B) expr_prec_8(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"не равно\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция не равно", "%ur(именительный)"));
	R = $this->create_node('expr_notequal', array( A, B, C ));
}

expr_prec_9(R) ::= expr_prec_9(A) EQUAL(B) expr_prec_8(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"равно\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция равно", "%ur(именительный)"));
	R = $this->create_node('expr_equal', array( A, B, C ));
}

expr_prec_9(R) ::= expr_prec_8(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* EXPRESSIONS OF EIGHTH PRECEDENCE */

expr_prec_8(R) ::= expr_prec_8(A) LESSER_OR_EQUAL(B) expr_prec_7(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"меньше или равно\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция меньше или равно", "%ur(именительный)"));
	R = $this->create_node('expr_lesser_or_equal', array( A, B, C ));
}

expr_prec_8(R) ::= expr_prec_8(A) GREATER_OR_EQUAL(B) expr_prec_7(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"больше или равно\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция больше или равно", "%ur(именительный)"));
	R = $this->create_node('expr_greater_or_equal', array( A, B, C ));
}

expr_prec_8(R) ::= expr_prec_8(A) GREATER(B) expr_prec_7(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"больше\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция больше", "%ur(именительный)"));
	R = $this->create_node('expr_greater', array( A, B, C ));
}

expr_prec_8(R) ::= expr_prec_8(A) LESSER(B) expr_prec_7(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция \"меньше\"  на выражениях \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция меньше", "%ur(именительный)"));
	R = $this->create_node('expr_lesser', array( A, B, C ));
}

expr_prec_8(R) ::= expr_prec_7(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* EXPRESSIONS OF SEVENTH PRECEDENCE */

expr_prec_7(R) ::= expr_prec_7(A) LEFTSHIFT(B) expr_prec_6(C) . {
	$this->currentrule = new block_formal_langs_description_rule("сдвиг влево выражения %1(именительный) на число байт, заданное выражением %3(именительный)", array("%ur(именительный)", "операция сдвига влево", "%ur(именительный)"));
	R = $this->create_node('expr_leftshift', array( A, B, C ));
}

expr_prec_7(R) ::= expr_prec_7(A) RIGHTSHIFT(B) expr_prec_6(C) . {
	$this->currentrule = new block_formal_langs_description_rule("сдвиг вправо выражения %1(именительный) на число байт, заданное выражением %3(именительный)", array("%ur(именительный)", "операция сдвига вправо", "%ur(именительный)"));
	R = $this->create_node('expr_rightshift', array( A, B, C ));
}

expr_prec_7(R) ::= expr_prec_6(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* EXPRESSIONS OF SIXTH PRECEDENCE */

expr_prec_6(R) ::= expr_prec_6(A) MINUS(B) expr_prec_5(C) . {
	$this->currentrule = new block_formal_langs_description_rule("разность выражений \"%1(именительный)\" и \"%3(именительный)\"", array("%ur(именительный)", "операция вычитания", "%ur(именительный)"));
	R = $this->create_node('expr_minus', array( A, B, C ));
}

expr_prec_6(R) ::= expr_prec_6(A) PLUS(B) expr_prec_5(C) . {
	$this->currentrule = new block_formal_langs_description_rule("сумма %1(именительный) и %3(именительный)", array("%ur(именительный)", "операция суммирования", "%ur(именительный)"));
	R = $this->create_node('expr_plus', array( A, B, C ));
}

expr_prec_6(R) ::= expr_prec_5(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* EXPRESSIONS OF FIFTH PRECEDENCE */

expr_prec_5(R) ::= expr_prec_5(A)  MODULOSIGN(B) expr_prec_4(C) . {
	$this->currentrule = new block_formal_langs_description_rule("получение остатка от деления выражений %1(именительный) и %3(именительный)", array("%ur(именительный)", "операция получения остатка от деления", "%ur(именительный)"));
	R = $this->create_node('expr_modulosign', array( A, B, C ));
}

expr_prec_5(R) ::= expr_prec_5(A)  DIVISION(B) expr_prec_4(C) . {
	$this->currentrule = new block_formal_langs_description_rule("деление %1(именительный) и %3(именительный)", array("%ur(именительный)", "операция деления", "%ur(именительный)"));
	R = $this->create_node('expr_division', array( A, B, C ));
}

expr_prec_5(R) ::= expr_prec_5(A)  MULTIPLY(B) expr_prec_4(C) . {
	$this->currentrule = new block_formal_langs_description_rule("умножение %1(именительный) и %3(именительный)", array("%ur(именительный)", "операция умножения", "%ur(именительный)"));
	R = $this->create_node('expr_multiply', array( A, B, C ));
}

expr_prec_5(R) ::= expr_prec_4(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* EXPRESSIONS OF FOURTH PRECEDENCE */

expr_prec_4(R) ::= try_value_access(A) MULTIPLY(B) IDENTIFIER(C) . {
	$this->currentrule = new block_formal_langs_description_rule("взятие поля по указателю", array("%ur(именительный)", "%s", "%s"));
	R = $this->create_node('expr_get_property', array( A, B, C ));
}

expr_prec_4(R) ::= try_pointer_access(A) MULTIPLY(B) IDENTIFIER(C) . {
	$this->currentrule = new block_formal_langs_description_rule("взятие поля по указателю", array("%ur(именительный)", "%s", "%s"));
	R = $this->create_node('expr_get_property', array( A, B, C ));
}

expr_prec_4(R) ::= expr_prec_3(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* EXPRESSIONS OF THIRD PRECEDENCE */

expr_prec_3(R) ::= AMPERSAND(A) expr_prec_3(B) . [UADRESS]  {
	$this->currentrule = new block_formal_langs_description_rule("операция взятия указателя", array("операция взятия указателя", "%ur(именительный)"));
	R = $this->create_node('expr_take_adress', array( A, B ));
}

expr_prec_3(R) ::= MULTIPLY(A) expr_prec_3(B) . [UINDIRECTION]  {
	$this->currentrule = new block_formal_langs_description_rule("операция разыменования указателя", array("операция разыменования", "%ur(именительный)"));
	R = $this->create_node('expr_dereference', array( A, B ));
}

expr_prec_3(R) ::= typecast(A) expr_prec_3(B) . {
	$this->currentrule = new block_formal_langs_description_rule("операция приведения к типу", array("%ur(именительный)", "%ur(именительный)"));
	R = $this->create_node('expr_typecast', array( A, B));
}

expr_prec_3(R) ::= LOGICALNOT(A) expr_prec_3(B) .  {
	$this->currentrule = new block_formal_langs_description_rule("логическое отрицание на выражении %2(именительный)", array("операция логического отрицания", "%ur(именительный)"));
	R = $this->create_node('expr_logical_not', array( A, B));
}

expr_prec_3(R) ::= BINARYNOT(A) expr_prec_3(B) . {
	$this->currentrule = new block_formal_langs_description_rule("побитовое отрицание на выражении %2(именительный)", array("операция побитового отрицания", "%ur(именительный)"));
	R = $this->create_node('expr_binary_not', array( A, B));
}

expr_prec_3(R) ::= MINUS(A) expr_prec_2(B)   . [UMINUS] {
	$this->currentrule = new block_formal_langs_description_rule("операция унарного минуса на выражении %2(именительный)", array("операция унарного минуса", "%ur(именительный)"));
	R = $this->create_node('expr_unary_minus', array( A, B));
}

expr_prec_3(R) ::= PLUS(A) expr_prec_2(B)   . [UPLUS] {
	$this->currentrule = new block_formal_langs_description_rule("операция унарного плюса на выражении %2(именительный)", array("операция унарного плюса", "%ur(именительный)"));
	R = $this->create_node('expr_unary_plus', array( A, B));
}

expr_prec_3(R) ::= DECREMENT(A) expr_prec_3(B)   . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("операция декремента", "%ur(именительный)"));
	R = $this->create_node('expr_prefix_decrement', array( A, B));
}

expr_prec_3(R) ::= INCREMENT(A) expr_prec_3(B)   . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("операция инкремента", "%ur(именительный)"));
	R = $this->create_node('expr_prefix_decrement', array( A, B));
}

expr_prec_3(R) ::= expr_prec_2(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = A;
}

/* EXPRESSIONS OF SECOND PRECEDENCE */

expr_prec_2(R) ::= try_value_access(A) IDENTIFIER(B) . {
	$this->currentrule = new block_formal_langs_description_rule("обращение к полю по указателю на метод", array("%ur(именительный)", "имя свойства"));
	R = $this->create_node('expr_property_access', array( A , B) );
}

expr_prec_2(R) ::= try_pointer_access(A) IDENTIFIER(B) . {
	$this->currentrule = new block_formal_langs_description_rule("обращение к полю по указателю на метод", array("%ur(именительный)", "имя свойства"));
	R = $this->create_node('expr_property_access', array( A , B) );
}

expr_prec_2(R) ::= cpp_style_cast(A)  LEFTROUNDBRACKET(B) expr_prec_11(C)  RIGHTROUNDBRACKET(D) . [UBRACKET] {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный) выражения \"%3(именительный)\"", array("%ur(именительный)", "левая круглая скобка", "%ur(именительный)", "правая квадратная скобка"));
	R = $this->create_node('expr_array_access', array( A, B, C, D));
}

expr_prec_2(R) ::= expr_prec_2(A)  LEFTSQUAREBRACKET(B) expr_prec_10(C)  RIGHTSQUAREBRACKET(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "левая квадратная скобка", "%ur(именительный)", "правая квадратная скобка"));
	R = $this->create_node('expr_array_access', array( A, B, C, D));
}

expr_prec_2(R) ::= expr_prec_2(A)  LEFTROUNDBRACKET(B) expr_prec_11(C)  RIGHTROUNDBRACKET(D) . [UBRACKET] {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка"));
	R = $this->create_node('expr_function_call', array( A, B, C, D));
}

expr_prec_2(R) ::= expr_prec_2(A)  LEFTROUNDBRACKET(B) RIGHTROUNDBRACKET(D) . [UBRACKET] {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "левая круглая скобка", "правая круглая скобка"));
	R = $this->create_node('expr_function_call', array( A, B, D));
}

expr_prec_2(R) ::= expr_prec_2(A)  INCREMENT(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "операция инкремента"));
	R = $this->create_node('expr_postfix_increment', array( A, B));
}

expr_prec_2(R) ::= expr_prec_2(A)  DECREMENT(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "операция декремента"));
	R = $this->create_node('expr_postfix_decrement', array( A, B));
}

expr_prec_2(R) ::= expr_atom(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R =  A;
}

/* SPECIAL PRODUCTIONS, NEEDED TO SUPPORT ACCESS BY POINTERS TO MEMBERS */

try_value_access(R) ::= expr_prec_2(A) DOT(B) . {
	$this->currentrule = new block_formal_langs_description_rule("операция разыменования указателя на метод или переменной", array("%ur (именительный)", "операция взятия указателя на метод или поля переменной"));
	R = $this->create_node('try_value_access', array( A , B) );
}

try_pointer_access(R) ::= expr_prec_2(A) RIGHTARROW(B) . {
	$this->currentrule = new block_formal_langs_description_rule("операция разыменования указателя на метод или переменной", array("%ur (именительный)", "операция взятия указателя на метод или переменной"));
	R = $this->create_node('try_pointer_access', array( A , B) );
}

/* C++ STYLE CASTS */

cpp_style_cast(R) ::= CONST_CAST(A)  LESSER(B) type(C) GREATER(D) . {
	$this->currentrule = new block_formal_langs_description_rule("приведение со снятием константности к %3(родительный) типу ", array("ключевое слово приведения типа", "знак \"меньше\"", "%ur(именительный)", "знак \"больше\""));
	R = $this->create_node('expr_const_cast', array(A, B, C, D));
}

cpp_style_cast(R) ::= STATIC_CAST(A)  LESSER(B) type(C) GREATER(D) . {
	$this->currentrule = new block_formal_langs_description_rule("статическое приведение к %3(родительный) типу ", array("ключевое слово приведения типа", "знак \"меньше\"", "%ur(именительный)", "знак \"больше\""));
	R = $this->create_node('expr_static_cast', array(A, B, C, D));
}

cpp_style_cast(R) ::= DYNAMIC_CAST(A)  LESSER(B) type(C) GREATER(D) . {
	$this->currentrule = new block_formal_langs_description_rule("динамическое приведение к %3(родительный) типу ", array("ключевое слово приведения типа", "знак \"меньше\"", "%ur(именительный)", "знак \"больше\""));
	R = $this->create_node('expr_dynamic_cast', array(A, B, C, D));
}

cpp_style_cast(R) ::= REINTERPRET_CAST(A)  LESSER(B) type(C) GREATER(D) . {
	$this->currentrule = new block_formal_langs_description_rule("побайтовое приведение к %3(родительный) типу ", array("ключевое слово приведения типа", "знак \"меньше\"", "%ur(именительный)", "знак \"больше\""));
	R = $this->create_node('expr_reinterpret_cast', array(A, B, C, D));
}

/* EXPRESSIONS OF FIRST PRECEDENCE */

expr_atom(R) ::= NUMERIC(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R =  A;
}

expr_atom(R) ::= IDENTIFIER(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R =  A;
}

expr_atom(R) ::= CHARACTER(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R =  A;
}

expr_atom(R) ::= STRING(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R =  A;
}

expr_atom(R) ::= LEFTROUNDBRACKET(A) expr_prec_11(B) RIGHTROUNDBRACKET(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("левая круглая скобка", "%s", "провая круглая скобка"));
	R =  $this->create_node('expr_brackets', array( A, B, C));
}

expr_atom(R) ::= PREPROCESSOR_STRINGIFY(A) IDENTIFIER(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s"));
	R =  $this->create_node('expr_preprocessor_stringify', array( A, B));
}

expr_atom(R) ::= expr_atom(A) PREPROCESSOR_CONCAT(B) IDENTIFIER(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%s"));
	R =  $this->create_node('expr_preprocessor_concat', array( A, B, C));
}

expr_atom(R) ::= SIZEOF(A) LEFTROUNDBRACKET(B)  type(C) RIGHTROUNDBRACKET(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("операция взятия размера структуры", "левая круглая скобка", "%ur(именительный)", "правая круглая скобка"));
	R =  $this->create_node('expr_sizeof', array( A, B, C, D));
}

expr_atom(R) ::= SIZEOF(A) LEFTROUNDBRACKET(B)  IDENTIFIER(C) RIGHTROUNDBRACKET(D) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("операция взятия размера структуры", "левая круглая скобка", "%s", "правая круглая скобка"));
	R =  $this->create_node('expr_sizeof', array( A, B, C, D));
}

/* TYPECAST */

typecast(R) ::= LEFTROUNDBRACKET(A)  type(B) RIGHTROUNDBRACKET(C) . {
	$this->currentrule = new block_formal_langs_description_rule("операция приведения к типу %2(именительный) ", array("левая круглая скобка", "%ur(именительный)", "правая круглая скобка"));
	$result = $this->create_node('typecast', array( A, B, C ));
	R = $result;
}

/* LIST OF TYPES */
/*
type_list(R) ::= type(A) .  {
	$this->currentrule = new block_formal_langs_description_rule("список типов", array("%ur(именительный)"));
	R = $this->create_node('type_list', array( A ) );
}

type_list(R) ::= type_list(A) COMMA(B) type(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%l(type)", array("список типов", "запятая", "%n-ый тип"));
	R = $this->create_node('type_list', array( A, B, C ) );
}
*/

/* TYPE DEFINITIONS */

type(R) ::= CONSTKWD(A) non_const_type(B) . {
	$this->currentrule = new block_formal_langs_description_rule("константный тип %1(именительный) ", array("признак константности", "%ur(именительный)"));
	R = $this->create_node('type', array( A, B ));
}

type(R) ::= non_const_type(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
    R = $this->create_node('type', array( A ));
}

non_const_type(R) ::= non_const_type(A) MULTIPLY(B) . [TYPEUNARY] {
	$this->currentrule = new block_formal_langs_description_rule("указатель на переменную %1(родительный) типа", array("%ur(именительный)", "признак указателя"));
	R = $this->create_node('type', array( A, B ));
}

non_const_type(R) ::= non_const_type(A) CONSTKWD(B) MULTIPLY(C) . [TYPEUNARY] {
	$this->currentrule = new block_formal_langs_description_rule("указатель на константную переменную %1(родительный) типа", array("%ur(именительный)", "ключевое слово константности", "признак указателя"));
	R = $this->create_node('type', array( A, B, C ));
}

non_const_type(R) ::= non_const_type(A) AMPERSAND(B) . [TYPEUNARY] {
	$this->currentrule = new block_formal_langs_description_rule("ссылка на  переменную %1(родительный) типа", array("%ur(именительный)", "амперсанд"));
	R = $this->create_node('type', array( A, B ));
}

non_const_type(R) ::= builtintype(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('non_const_type', array( A ));
}

non_const_type(R) ::= primitive_or_complex_type(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%ur(именительный)"));
	R = $this->create_node('non_const_type', array( A ));
}

/*
primitive_or_complex_type(R) ::= CUSTOMTYPENAME(A) . {
	$this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R = $this->create_node('primitive_or_complex_type', array( A ));
}

primitive_or_complex_type(R) ::= CUSTOMTYPENAME(A) LESSER(B) GREATER(C) .  {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s", "%s"));
	R = $this->create_node('primitive_or_complex_type', array( A, B, C ));
}

primitive_or_complex_type(R) ::= CUSTOMTYPENAME(A) LESSER(B) type_list(C) GREATER(D) .  {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%s", "%s", "%ur(именительный)", "%s"));
	R = $this->create_node('primitive_or_complex_type', array( A, B, C, D ));
}

primitive_or_complex_type(R) ::= primitive_or_complex_type(A)  NAMESPACE_RESOLVE(B) IDENTIFIER(C)  . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%s"));
	R = $this->create_node('primitive_or_complex_type', array( A, B, C));
}
primitive_or_complex_type(R) ::= primitive_or_complex_type(A)  NAMESPACE_RESOLVE(B) CUSTOMTYPENAME(C)  . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%s"));
	R = $this->create_node('primitive_or_complex_type', array( A, B, C));
}

primitive_or_complex_type(R) ::= primitive_or_complex_type(A)  NAMESPACE_RESOLVE(B) CUSTOMTYPENAME(C) LESSER(D) GREATER(E) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%s", "%s", "%s"));
	R = $this->create_node('primitive_or_complex_type', array( A, B, C, D, E));
}

primitive_or_complex_type(R) ::= primitive_or_complex_type(A)  NAMESPACE_RESOLVE(B) CUSTOMTYPENAME(C) LESSER(D) type_list(E) GREATER(F) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%s", "%s", "%ur", "%s"));
	R = $this->create_node('primitive_or_complex_type', array( A, B, C, D, E, F));
}
*/

non_const_type(R) ::= user_defined_type(A) . {
	R = A;
}

primitive_or_complex_type(R) ::= namespace_resolve(A) TYPENAME(B) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "%s"));
	$this->mapper->clear_lookup_namespace();
	R = $this->create_node('primitive_or_complex_type', array( A,  B));
} 

namespace_resolve(R) ::=  namespace_resolve(A) TYPENAME(B) NAMESPACE_RESOLVE(C) . {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "%s", "операция разрешения видимости"));
	$this->mapper->push_lookup_entry((string)(B->value()));
	R = $this->create_node('namespace_resolve', array( A, B, C));
}

namespace_resolve(R) ::= TYPENAME(A) NAMESPACE_RESOLVE(B) .  {
	$this->currentrule = new block_formal_langs_description_rule("%s", array("%ur(именительный)", "операция разрешения видимости"));
	$this->mapper->push_lookup_entry((string)(A->value()));
	R = $this->create_node('namespace_resolve', array( A, B));
}

/* ================================= VALIDATED PART ================================= */

user_defined_type(R) ::= TYPENAME(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("%s"));
	R = $this->create_node('user_defined_type', array( A ));
}

/* VOID */

builtintype(R) ::= VOID(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("имя пустого типа"));
	R = $this->create_node('builtintype', array( A ));
}

/*  FLOATING POINT VARIATIONS */


builtintype(R) ::= FLOAT(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("имя типа c плавающей запятой одинарной точности"));
	R = $this->create_node('builtintype', array( A ));
}

builtintype(R) ::= DOUBLE(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("имя типа c плавающей запятой двойной точности"));
	R = $this->create_node('builtintype', array( A ));
}

builtintype(R) ::= LONG(A) DOUBLE(B) . {
    $this->currentrule = new block_formal_langs_description_rule("имя длинного типа c плавающей запятой двойной точности", array("признак длинного числа", "имя типа c плавающей запятой двойной точности"));
	R = $this->create_node('builtintype', array( A, B ));
}


/*  CHAR VARIATIONS */

builtintype(R) ::= CHAR(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("имя символьного типа"));
	R = $this->create_node('builtintype', array( A ));
}

builtintype(R) ::= SIGNED(A) CHAR(B) . {
    $this->currentrule = new block_formal_langs_description_rule("знаковый символьный тип", array("признак знаковости", "%ur(именительный)"));
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= UNSIGNED(A) CHAR(B) . {
    $this->currentrule = new block_formal_langs_description_rule("беззнаковый символьный тип", array("признак беззнаковости", "%ur(именительный)"));
	R = $this->create_node('builtintype', array( A, B ));
}


/* INT VARIATIONS */

builtintype(R) ::= INT(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("имя целого типа"));
	R = $this->create_node('builtintype', array( A ));
}

builtintype(R) ::= SIGNED(A) INT(B) . {
    $this->currentrule = new block_formal_langs_description_rule("знаковый целый тип", array("признак знаковости", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= UNSIGNED(A) INT(B) . {
    $this->currentrule = new block_formal_langs_description_rule("беззнаковый целый тип", array("признак беззнаковости", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= SHORT(A) INT(B) . {
    $this->currentrule = new block_formal_langs_description_rule("короткий целый тип", array("признак короткого целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= SIGNED(A) SHORT(B) INT(C) . {
    $this->currentrule = new block_formal_langs_description_rule("знаковый короткий целый тип", array("признак знаковости", "признак короткого целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B, C ));
}

builtintype(R) ::= UNSIGNED(A) SHORT(B) INT(C) . {
    $this->currentrule = new block_formal_langs_description_rule("беззнаковый короткий целый тип", array("признак беззнаковости", "признак короткого целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B, C ));
}

builtintype(R) ::= LONG(A) INT(B) . {
    $this->currentrule = new block_formal_langs_description_rule("длинный целый тип", array("признак длинного целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= SIGNED(A) LONG(B) INT(C) . {
    $this->currentrule = new block_formal_langs_description_rule("знаковый длинный целый тип", array("признак знаковости", "признак длинного целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B, C ));
}

builtintype(R) ::= UNSIGNED(A) LONG(B) INT(C) . {
    $this->currentrule = new block_formal_langs_description_rule("беззнаковый длинный целый тип", array("признак беззнаковости", "признак длинного целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B, C ));
}

builtintype(R) ::= LONG(A) LONG(B) INT(C) . {
    $this->currentrule = new block_formal_langs_description_rule("64-битный целый тип", array("признак длинного целого типа", "признак длинного целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B, C ));
}


builtintype(R) ::=  SIGNED(A) LONG(B) LONG(C) INT(D) . {
    $this->currentrule = new block_formal_langs_description_rule("знаковый 64-битный целый тип", array("признак знаковости", "признак длинного целого типа", "признак длинного целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B, C, D ));
}

builtintype(R) ::=  UNSIGNED(A) LONG(B) LONG(C) INT(D) . {
    $this->currentrule = new block_formal_langs_description_rule("беззнаковый 64-битный целый тип", array("признак беззнаковости", "признак длинного целого типа", "признак длинного целого типа", "имя целого типа"));
	R = $this->create_node('builtintype', array( A, B, C, D ));
}


/* SHORT VARIATIONS */

builtintype(R) ::= SHORT(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("короткий целый тип"));
	R = $this->create_node('builtintype', array( A ));
}

builtintype(R) ::= SIGNED(A) SHORT(B) . {
    $this->currentrule = new block_formal_langs_description_rule("знаковый короткий целый тип", array("признак знаковости", "короткий целый тип"));
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= UNSIGNED(A) SHORT(B) . {
    $this->currentrule = new block_formal_langs_description_rule("беззнаковый короткий целый тип", array("признак беззнаковости", "короткий целый тип"));
	R = $this->create_node('builtintype', array( A, B ));
}

/* LONG VARIATIONS */ 

builtintype(R) ::= LONG(A) . {
    $this->currentrule = new block_formal_langs_description_rule("%1(именительный)", array("длинный целый тип"));
	R = $this->create_node('builtintype', array( A ));
}

builtintype(R) ::= SIGNED(A) LONG(B) . {
    $this->currentrule = new block_formal_langs_description_rule("знаковый длинный целвый тип", array("признак знаковости", "длинный целый тип"));
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= UNSIGNED(A) LONG(B) . {
    $this->currentrule = new block_formal_langs_description_rule("беззнаковый длинный целвый тип", array("признак беззнаковости", "длинный целый тип"));
	R = $this->create_node('builtintype', array( A, B ));
}

/* LONG LONG VARIATIONS */

builtintype(R) ::= LONG(A) LONG(B) . {
    $this->currentrule = new block_formal_langs_description_rule("64-битный целый тип", array("признак длинного целого", "длинный целый тип"));
	R = $this->create_node('builtintype', array( A, B ));
}

builtintype(R) ::= SIGNED(A) LONG(B) LONG(C) . {
    $this->currentrule = new block_formal_langs_description_rule("знаковый 64-битный целый тип", array("признак знаковости", "признак длинного целого", "длинный целый тип"));
	R = $this->create_node('builtintype', array( A, B, C ));
}

builtintype(R) ::= UNSIGNED(A) LONG(B) LONG(C) . {
    $this->currentrule = new block_formal_langs_description_rule("беззнаковый 64-битный целый тип", array("признак беззнаковости", "признак длинного целого", "длинный целый тип"));
	R = $this->create_node('builtintype', array( A, B, C ));
}
