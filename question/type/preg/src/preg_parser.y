%name preg_parser_
%include{
    require_once($CFG->dirroot . '/question/type/preg/node.php');
}
%include_class {
    private $root;
    private $anchor;
    private $error;
    private $errormessages;

    function __construct() {
        $this->anchor = new stdClass;
        $this->anchor->start = false;
        $this->anchor->end = false;
        $this->error = false;
        $this->errormessages = array();
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

    static function is_conc($prevtoken, $currtoken) {
        static $condsubpatt = false;
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
        return $flag;
    }
}
%parse_failure {
    if (!$this->error) {
        $this->errormessages[] = get_string('incorrectregex', 'qtype_preg');
        $this->error = true;
    }
}
%nonassoc ERROR_PREC_SHORT.
%nonassoc ERROR_PREC.
%nonassoc CLOSEBRACK.
%left ALT.
%left CONC PARSLEAF WORDBREAK WORDNOTBREAK STARTANCHOR.
%nonassoc QUEST PLUS ITER QUANT LAZY_ITER LAZY_QUEST LAZY_PLUS LAZY_QUANT.
%nonassoc OPENBRACK GROUPING ASSERT_TF ASSERT_TB ASSERT_FF ASSERT_FB CONDSUBPATT ONETIMESUBPATT.

start ::= lastexpr(B). {
    $this->root = B;
}
expr(A) ::= expr(B) CONC expr(C). {
    ECHO 'CONC <br/>';
    A = new node;
    A->type = NODE;
    A->subtype = NODE_CONC;
    A->firop = B;
    A->secop = C;
}
expr(A) ::= expr(B) expr(C). [CONC] {
    ECHO 'CONC1 <br/>';
    A = new node;
    A->type = NODE;
    A->subtype = NODE_CONC;
    A->firop = B;
    A->secop = C;
}
expr(A) ::= expr(B) ALT expr(C). {
    ECHO 'ALT <br/>';
    A = new node;
    A->type = NODE;
    A->subtype = NODE_ALT;
    A->firop = B;
    A->secop = C;
}
expr(A) ::= expr(B) ALT. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_ALT;
    A->firop = B;
    A->secop = new node;
    A->secop->type = LEAF;
    A->secop->subtype = LEAF_EMPTY;
}
expr(A) ::= expr(B) QUEST. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_QUESTQUANT;
    A->greed = true;
    A->firop = B;
}
expr(A) ::= expr(B) ITER. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_ITER;
    A->greed = true;
    A->firop = B;
}
expr(A) ::= expr(B) PLUS. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_PLUSQUANT;
    A->greed = true;
    A->firop = B;
}
expr(A) ::= expr(B) LAZY_QUEST. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_QUESTQUANT;
    A->greed = false;
    A->firop = B;
}
expr(A) ::= expr(B) LAZY_ITER. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_ITER;
    A->greed = false;
    A->firop = B;
}
expr(A) ::= expr(B) LAZY_PLUS. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_PLUSQUANT;
    A->greed = false;
    A->firop = B;
}
expr(A) ::= expr(B) QUANT(C). {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_QUANT;
    A->greed = true;
    A->leftborder = C->leftborder;
    A->rightborder = C->rightborder;
    A->firop = B;
}
expr(A) ::= expr(B) LAZY_QUANT(C). {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_QUANT;
    A->greed = false;
    A->leftborder = C->leftborder;
    A->rightborder = C->rightborder;
    A->firop = B;
}
expr(A) ::= OPENBRACK expr(B) CLOSEBRACK. {
    ECHO 'SUBPATT <br/>';
    A = new node;
    A->type = NODE;
    A->subtype = NODE_SUBPATT;
    A->firop = B;
}
expr(A) ::= GROUPING expr(B) CLOSEBRACK. {
    A = B;
}
expr(A) ::= ASSERT_TF expr(B) CLOSEBRACK. {
    ECHO  'ASSERT TF <br/>';
    A = new node;
    A->type = NODE;
    A->subtype = NODE_ASSERTTF;
    A->firop = B;
}
expr(A) ::= ASSERT_TB expr(B) CLOSEBRACK. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_ASSERTTB;
    A->firop = B;
}
expr(A) ::= ASSERT_FF expr(B) CLOSEBRACK. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_ASSERTFF;
    A->firop = B;
}
expr(A) ::= ASSERT_FB expr(B) CLOSEBRACK. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_ASSERTFB;
    A->firop = B;
}
expr(A) ::= CONDSUBPATT ASSERT_TF expr(B) CLOSEBRACK expr(C) CLOSEBRACK. {
    ECHO  'CONDSUB TF <br/>';
    A = new node;
    A->type = NODE;
    A->subtype = NODE_CONDSUBPATT;
    /*TODO - add check that there is no ALT operators in C (or C->firop/C->secop)*/
    if (C->subtype != NODE_ALT) {
        A->firop = C;
    } else {
        A->firop = C->firop;
        A->secop = C->secop;
    }
    A->thirdop->type = NODE;
    A->thirdop->subtype = NODE_ASSERTTF;
    A->thirdop->firop = B;
}
expr(A) ::= CONDSUBPATT ASSERT_TB expr(B) CLOSEBRACK expr(C) ALT expr(D) CLOSEBRACK. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_CONDSUBPATT;
    /*TODO - add check that there is no ALT operators in C (or C->firop/C->secop)*/
    if (C->subtype != NODE_ALT) {
        A->firop = C;
    } else {
        A->firop = C->firop;
        A->secop = C->secop;
    }
    A->thirdop->type = NODE;
    A->thirdop->subtype = NODE_ASSERTTB;
    A->thirdop->firop = B;
}
expr(A) ::= CONDSUBPATT ASSERT_FF expr(B) CLOSEBRACK expr(C) ALT expr(D) CLOSEBRACK. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_CONDSUBPATT;
    /*TODO - add check that there is no ALT operators in C (or C->firop/C->secop)*/
    if (C->subtype != NODE_ALT) {
        A->firop = C;
    } else {
        A->firop = C->firop;
        A->secop = C->secop;
    }
    A->thirdop->type = NODE;
    A->thirdop->subtype = NODE_ASSERTFF;
    A->thirdop->firop = B;
}
expr(A) ::= CONDSUBPATT ASSERT_FB expr(B) CLOSEBRACK expr(C) ALT expr(D) CLOSEBRACK. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_CONDSUBPATT;
    /*TODO - add check that there is no ALT operators in C (or C->firop/C->secop)*/
    if (C->subtype != NODE_ALT) {
        A->firop = C;
    } else {
        A->firop = C->firop;
        A->secop = C->secop;
    }
    A->thirdop->type = NODE;
    A->thirdop->subtype = NODE_ASSERTFB;
    A->thirdop->firop = B;
}
expr(A) ::= PARSLEAF(B). {
    ECHO 'LEAF <br/>';
    A = new node;
    A = B;
}
expr(A) ::= STARTANCHOR(B) expr(C). {
    $this->anchor->start = true;
    A = new node;
    A = C;
}
lastexpr(A) ::= lastexpr(B) ENDANCHOR(C). {
    $this->anchor->end = true;
    A = new node;
    A = B;
}
lastexpr(A) ::= expr(B). {
    A = new node;
    A = B;
}
expr(A) ::= ONETIMESUBPATT expr(B) CLOSEBRACK. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_ONETIMESUBPATT;
    A->firop = B;
}
expr(A) ::= WORDBREAK . {
    A = new node;
    A->type = LEAF;
    A->subtype = LEAF_WORDBREAK;
}
expr(A) ::= WORDNOTBREAK . {
    A = new node;
    A->type = LEAF;
    A->subtype = LEAF_WORDNOTBREAK;
}

expr(A) ::= expr CLOSEBRACK. [ERROR_PREC] {
    ECHO 'UNOPENPARENS <br/>';
    A = new node;
    A->type = ERROR;
    $this->errormessages[] = get_string('unopenedparen','qtype_preg');
    $this->error = true;
}

expr(A) ::= CLOSEBRACK. [ERROR_PREC_SHORT] {
    A = new node;
    A->type = ERROR;
    $this->errormessages[] = get_string('closeparenatstart','qtype_preg');
    $this->error = true;
}

expr(A) ::= OPENBRACK expr. [ERROR_PREC] {
    ECHO 'UNCLOSEDPARENS <br/>';
    A = new node;
    A->type = ERROR;
    $this->errormessages[] = get_string('unclosedparen','qtype_preg');
    $this->error = true; 
}

expr(A) ::= OPENBRACK. [ERROR_PREC_SHORT] {
    A = new node;
    A->type = ERROR;
    $this->errormessages[] = get_string('openparenatend','qtype_preg');
    $this->error = true; 
}

expr(A) ::= QUEST. [ERROR_PREC] {
    A = new node;
    A->type = ERROR;
    $this->errormessages[] = get_string('quantifieratstart','qtype_preg','?');
    $this->error = true; 
}

expr(A) ::= PLUS. [ERROR_PREC] {
    A = new node;
    A->type = ERROR;
    $this->errormessages[] = get_string('quantifieratstart','qtype_preg','+');
    $this->error = true; 
}

expr(A) ::= ITER. [ERROR_PREC] {
    A = new node;
    A->type = ERROR;
    $this->errormessages[] = get_string('quantifieratstart','qtype_preg','*');
    $this->error = true; 
}

expr(A) ::= QUANT. [ERROR_PREC] {
    A = new node;
    A->type = ERROR;
    $this->errormessages[] = get_string('quantifieratstart','qtype_preg','{..}');
    $this->error = true; 
}

expr(A) ::= LAZY_ITER. [ERROR_PREC] {
    A = new node;
    A->type = ERROR;
    $this->errormessages[] = get_string('quantifieratstart','qtype_preg','*?');
    $this->error = true; 
}

expr(A) ::= LAZY_QUEST. [ERROR_PREC] {
    A = new node;
    A->type = ERROR;
    $this->errormessages[] = get_string('quantifieratstart','qtype_preg','??');
    $this->error = true; 
}

expr(A) ::= LAZY_PLUS. [ERROR_PREC] {
    A = new node;
    A->type = ERROR;
    $this->errormessages[] = get_string('quantifieratstart','qtype_preg','+?');
    $this->error = true; 
}

expr(A) ::= LAZY_QUANT. [ERROR_PREC] {
    A = new node;
    A->type = ERROR;
    $this->errormessages[] = get_string('quantifieratstart','qtype_preg','{..}?');
    $this->error = true; 
}