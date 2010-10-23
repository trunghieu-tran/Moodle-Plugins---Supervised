%name preg_parser_
%include{
    require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
}
%include_class {
    //Root of the Abstract Syntax Tree (AST)
    private $root;
    //Is a pattern fully anchored?
    private $anchor;
    //Are there any errors during the parsing
    private $error;
    //Error messages for errors during the parsing
    private $errormessages;
    //Count of reduces made
    private $reducecount;
    //Open-parenthesis strings
    private $parens;
    //Quantifier strings
    private $quants;

    function __construct() {
        $this->anchor = new stdClass;
        $this->anchor->start = false;
        $this->anchor->end = false;
        $this->error = false;
        $this->errormessages = array();
        $this->reducecount = 0;
        $this->parens = array(preg_node::TYPE_NODE_SUBPATT => '(', 'grouping' => '(?:', preg_node_subpatt::SUBTYPE_ONCEONLY => '(?>', 
                                preg_node_assert::SUBTYPE_PLA => '(?=', preg_node_assert::SUBTYPE_PLB => '(?<=',preg_node_assert::SUBTYPE_NLA => '(?!',
                                preg_node_assert::SUBTYPE_NLB => '(?<!');
        //$this->quants = array (NODE_QUESTQUANT => '?', NODE_ITER => '*', NODE_PLUSQUANT => '+', NODE_QUANT => '{...}');
    }

    function get_root() {
        return $this->root;
    }

    function get_anchor() {
        return $this->anchor;
    }

    function get_error() {
        return $this->error;
    }

    function get_error_messages() {
        return $this->errormessages;
    }

    /**
    *create and return an error node
    @param errorstr translation string name for the error
    @param a object, string or number to be used in translation string
    @return node
    */
    protected function create_error_node($errorstr, $a = null) {
        $newnode = new preg_node_error;
        $newnode->subtype = $errorstr;
        $this->errormessages[] = get_string($errorstr,'qtype_preg',$a);
        $this->error = true;
    }

    static function is_conc($prevtoken, $currtoken) {
        /*static $condsubpatt = false;
        static $close = 0;
        if ($currtoken == preg_parser_yyParser::CONDSUBPATT) {
            $condsubpatt = true;
            $close = -1;
        }
        if ($condsubpatt && $currtoken == preg_parser_yyParser::CLOSEBRACK) {
            $close++;
        }
        if ($condsubpatt && ($currtoken == preg_parser_yyParser::OPENBRACK || $currtoken == preg_parser_yyParser::ASSERT_TF || $currtoken == preg_parser_yyParser::ASSERT_FF  ||
            $currtoken == preg_parser_yyParser::ASSERT_FB || $currtoken == preg_parser_yyParser::ASSERT_TB || $currtoken == preg_parser_yyParser::GROUPING ||
            $currtoken == preg_parser_yyParser::ONETIMESUBPATT)) {
            $close--;
        }
        if ($close == 0) {
            $condsubpatt = false;
        }
        $flag1 = ($prevtoken == preg_parser_yyParser::PARSLEAF || $prevtoken == preg_parser_yyParser::CLOSEBRACK ||
                  $prevtoken == preg_parser_yyParser::QUEST || $prevtoken == preg_parser_yyParser::LAZY_QUEST ||
                  $prevtoken == preg_parser_yyParser::ITER || $prevtoken == preg_parser_yyParser::LAZY_ITER ||
                  $prevtoken == preg_parser_yyParser::PLUS || $prevtoken == preg_parser_yyParser::LAZY_PLUS ||
                  $prevtoken == preg_parser_yyParser::QUANT || $prevtoken == preg_parser_yyParser::LAZY_QUANT ||
                  $prevtoken == preg_parser_yyParser::WORDBREAK || $prevtoken == preg_parser_yyParser::WORDNOTBREAK);
        $flag2 = ($currtoken == preg_parser_yyParser::PARSLEAF || $currtoken == preg_parser_yyParser::OPENBRACK ||
                  $currtoken == preg_parser_yyParser::GROUPING || $currtoken == preg_parser_yyParser::CONDSUBPATT ||
                  $currtoken == preg_parser_yyParser::ASSERT_TF || $currtoken == preg_parser_yyParser::ASSERT_FF ||
                  $currtoken == preg_parser_yyParser::ASSERT_TF || $currtoken == preg_parser_yyParser::ASSERT_FB||
                  $currtoken == preg_parser_yyParser::WORDBREAK || $currtoken == preg_parser_yyParser::WORDNOTBREAK ||
                  $currtoken == preg_parser_yyParser::ONETIMESUBPATT);
        $flag = ($flag1 && $flag2 && isset($prevtoken) && !$condsubpatt);
        return $flag;*/
        return false;
    }
}
%parse_failure {
    if (!$this->error) {
        $this->errormessages[] = get_string('incorrectregex', 'qtype_preg');
        $this->error = true;
    }
}
%nonassoc ERROR_PREC_VERY_SHORT.
%nonassoc ERROR_PREC_SHORT.
%nonassoc ERROR_PREC.
%nonassoc CLOSEBRACK.
%left ALT.
%left CONC PARSLEAF WORDBREAK WORDNOTBREAK STARTANCHOR.
%nonassoc QUANT.
%nonassoc OPENBRACK CONDSUBPATT.

start ::= lastexpr(B). {
    $this->root = B;
}
expr(A) ::= expr(B) CONC expr(C). {
    //ECHO 'CONC <br/>';
    A = new preg_node_concat;
    A->operands[1] = B;
    A->operands[2] = C;
    $this->reducecount++;
}
expr(A) ::= expr(B) expr(C). [CONC] {
    //ECHO 'CONC1 <br/>';
    A = new preg_node_concat;
    A->operands[1] = B;
    A->operands[2] = C;
    $this->reducecount++;
}
expr(A) ::= expr(B) ALT expr(C). {
    //ECHO 'ALT <br/>';
    A = new preg_node_alt;
    A->operands[1] = B;
    A->operands[2] = C;
    $this->reducecount++;
}
expr(A) ::= expr(B) ALT. {
    A = new preg_node_alt;
    A->operands[1] = B;
    A->operands[2] = new preg_leaf_meta;
    A->operands[2]->subtype = preg_leaf_meta::SUBTYPE_EMPTY;
    $this->reducecount++;
}

expr(A) ::= expr(B) QUANT(C). {
    A = C;
    A->operands[1] = B;
    $this->reducecount++;
}

expr(A) ::= OPENBRACK(B) expr(C) CLOSEBRACK. {
    //ECHO 'SUBPATT '.$this->parens[B].'<br/>';
    if (B !== 'grouping') {
        if (B === preg_node::TYPE_NODE_SUBPATT || B === preg_node_subpatt::SUBTYPE_ONCEONLY) {
            A = new preg_node_subpatt;
        } else {
            A = new preg_node_assert;
        }
        if (B !== preg_node::TYPE_NODE_SUBPATT) {
            A->subtype = B;
        }
        A->operands[1] = C;
    } else {//grouping node
        A = C;
    }
    $this->reducecount++;
}
expr(A) ::= CONDSUBPATT(D) expr(B) CLOSEBRACK expr(C) CLOSEBRACK. {
    //ECHO  'CONDSUB TF <br/>';
    A = new preg_node_cond_subpatt;
    if (C->type != preg_node::TYPE_NODE_ALT) {
        A->operands[1] = C;
    } else {
        if (C->operands[1]->type == preg_node::TYPE_NODE_ALT || C->operands[2]->type == preg_node::TYPE_NODE_ALT) {
            A = $this->create_error_node('threealtincondsubpatt');//One or two top-level alternative in conditional subpattern allowed
            $this->reducecount++;
            return;
        } else {
            A->operands[1] = C->operands[1];
            A->operands[1] = C->operands[1];
        }
    }
    A->operands[3] = new preg_node_assert;
    A->operands[3]->subtype = D;
    A->operands[3]->operands[1] = B;
    $this->reducecount++;
}
expr(A) ::= PARSLEAF(B). {
    //ECHO 'LEAF <br/>';
    A = B;
    $this->reducecount++;
}
expr(A) ::= STARTANCHOR(B). {
    A = B;
    $this->reducecount++;
}
expr(A) ::= ENDANCHOR(B). {
    A = B;
    $this->reducecount++;
}
lastexpr(A) ::= expr(B). {
    A = B;
    $this->reducecount++;
}
expr(A) ::= WORDBREAK(B) . {
    A = B;
    $this->reducecount++;
}
expr(A) ::= WORDNOTBREAK(B) . {
    A = B;
    $this->reducecount++;
}

expr(A) ::= expr CLOSEBRACK. [ERROR_PREC] {
    //ECHO 'UNOPENPARENS <br/>';
    A = $this->create_error_node('unopenedparen');
    $this->reducecount++;
}

expr(A) ::= CLOSEBRACK. [ERROR_PREC_SHORT] {
    //ECHO 'CLOSEPARENATSTART <br/>';
    if($this->reducecount == 0) {//close bracket at the very start of expression
        A = $this->create_error_node('closeparenatverystart');
    } else {
        A = $this->create_error_node('closeparenatstart');
    }
    $this->reducecount++;
}

expr(A) ::= OPENBRACK(B) expr. [ERROR_PREC] {
    //ECHO 'UNCLOSEDPARENS <br/>';
    end($this->errormessages);
    $unopenstr = get_string('unopenedparen','qtype_preg');
    $closeatstartstr = get_string('closeparenatstart','qtype_preg');
    $i = count($this->errormessages) - 1;
    while ($i>=0 && current($this->errormessages) != $unopenstr && current($this->errormessages) != $closeatstartstr) {
        prev($this->errormessages);//Iterate over all previous error messages except unopened brackets (to not catch 'b)c(f' as empty brackets)
        $i--;
    }
    if ($i>=0 && current($this->errormessages) == $closeatstartstr) {
        //empty brackets, avoiding two error messages
        array_splice($this->errormessages, $i, 1);
        A = $this->create_error_node('emptyparens',$this->parens[B]);
    } else {
        A = $this->create_error_node('unclosedparen',$this->parens[B]);
    }
    $this->reducecount++;
}

expr(A) ::= OPENBRACK(B). [ERROR_PREC_SHORT] {
    A = $this->create_error_node('openparenatend',$this->parens[B]);
    $this->reducecount++;
}

expr(A) ::= CONDSUBPATT(B) expr CLOSEBRACK expr. [ERROR_PREC] {
    //ECHO 'UNCLOSEDPARENS <br/>';
    end($this->errormessages);
    $unopenstr = get_string('unopenedparen','qtype_preg');
    $closeatstartstr = get_string('closeparenatstart','qtype_preg');
    $i = count($this->errormessages) - 1;
    while ($i>=0 && current($this->errormessages) != $unopenstr && current($this->errormessages) != $closeatstartstr) {
        prev($this->errormessages);//Iterate over all previous error messages except unopened brackets (to not catch 'b)c(f' as empty brackets)
        $i--;
    }
    if ($i>=0 && current($this->errormessages) == $closeatstartstr) {
        //empty brackets, avoiding two error messages
        array_splice($this->errormessages, $i, 1);
        A = $this->create_error_node('emptyparens','(?'.$this->parens[B]);
    } else {
        A = $this->create_error_node('unclosedparen','(?'.$this->parens[B]);
    }
    $this->reducecount++;
}

expr(A) ::= CONDSUBPATT(B) expr. [ERROR_PREC_SHORT] {
    //ECHO 'UNCLOSEDPARENS <br/>';
    end($this->errormessages);
    $unopenstr = get_string('unopenedparen','qtype_preg');
    $closeatstartstr = get_string('closeparenatstart','qtype_preg');
    $i = count($this->errormessages) - 1;
    while ($i>=0 && current($this->errormessages) != $unopenstr && current($this->errormessages) != $closeatstartstr) {
        prev($this->errormessages);//Iterate over all previous error messages except unopened brackets (to not catch 'b)c(f' as empty brackets)
        $i--;
    }
    if ($i>=0 && current($this->errormessages) == $closeatstartstr) {
        //empty brackets, avoiding two error messages
        array_splice($this->errormessages, $i, 1);
        A = $this->create_error_node('emptyparens','(?'.$this->parens[B]);
        //Two unclosed brackets, firts are empty
        $this->errormessages[] = get_string('unclosedparen', 'qtype_preg', '(?'.$this->parens[B]);
    } else {
        //Two unclosed brackets, so two messages
        A = $this->create_error_node('unclosedparen','(?'.$this->parens[B]);
        $this->errormessages[] = get_string('unclosedparen', 'qtype_preg', '(?'.$this->parens[B]);
    }
    $this->reducecount++;
}

expr(A) ::= CONDSUBPATT(B). [ERROR_PREC_VERY_SHORT] {
    A = $this->create_error_node('openparenatend','(?'.$this->parens[B]);
    $this->reducecount++;
}


expr(A) ::= QUANT(B). [ERROR_PREC] {
    /*Now cannot determine quntifier type.
    $quantstr = $this->quants[B->subtype];
    if (!B->greed) {
        $quantstr .= '?';
    }*/
    A = $this->create_error_node('quantifieratstart');
    $this->reducecount++;
}

lastexpr(A) ::= lastexpr(B) LEXERROR(C). {
    A = $this->create_error_node(C);
    $this->reducecount++;
}