%name preg_parser_
%include{
    require_once($CFG->dirroot . '/question/type/preg/node.php');
}
%include_class {
    private $root;
    private $lock;
    private $error;
    function __construct() {
        $this->lock = new stdClass;
        $this->lock->start = false;
        $this->lock->end = false;
        $this->error = false;
    }
    function get_root() {
        return $this->root;
    }
    function get_lock() {
        return $this->lock;
    }
    function get_error() {
        return $this->error;
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
    $this->error = true;
}
%left ALT.
%left CONC.
%nonassoc QUEST PLUS ITER QUANT LAZY_ITER LAZY_QUEST LAZY_PLUS LAZY_QUANT.
%nonassoc CLOSEBRACK.

start ::= lastexpr(B). {
    $this->root = B;
}
expr(A) ::= expr(B) CONC expr(C). {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_CONC;
    A->firop = B;
    A->secop = C;
}
expr(A) ::= expr(B) ALT expr(C). {
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
    A = new node;
    A->type = NODE;
    A->subtype = NODE_SUBPATT;
    A->firop = B;
}
expr(A) ::= GROUPING expr(B) CLOSEBRACK. {
    A = B;
}
expr(A) ::= ASSERT_TF expr(B) CLOSEBRACK. {
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
expr(A) ::= CONDSUBPATT ASSERT_TF expr(B) CLOSEBRACK expr(C) ALT expr(D) CLOSEBRACK. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_CONDSUBPATT;
    A->firop = C;
    A->secop = D;
    A->thirdop->type = NODE;
    A->thirdop->subtype = NODE_ASSERTTF;
    A->thirdop->firop = B;
}
expr(A) ::= CONDSUBPATT ASSERT_TB expr(B) CLOSEBRACK expr(C) ALT expr(D) CLOSEBRACK. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_CONDSUBPATT;
    A->firop = C;
    A->secop = D;
    A->thirdop->type = NODE;
    A->thirdop->subtype = NODE_ASSERTTB;
    A->thirdop->firop = B;
}
expr(A) ::= CONDSUBPATT ASSERT_FF expr(B) CLOSEBRACK expr(C) ALT expr(D) CLOSEBRACK. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_CONDSUBPATT;
    A->firop = C;
    A->secop = D;
    A->thirdop->type = NODE;
    A->thirdop->subtype = NODE_ASSERTFF;
    A->thirdop->firop = B;
}
expr(A) ::= CONDSUBPATT ASSERT_FB expr(B) CLOSEBRACK expr(C) ALT expr(D) CLOSEBRACK. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_CONDSUBPATT;
    A->firop = C;
    A->secop = D;
    A->thirdop->type = NODE;
    A->thirdop->subtype = NODE_ASSERTFB;
    A->thirdop->firop = B;
}
expr(A) ::= PARSLEAF(B). {
    A = new node;
    A = B;
}
expr(A) ::= STARTLOCK(B) expr(C). {
    $this->lock->start = true;
    A = new node;
    A = C;
}
lastexpr(A) ::= lastexpr(B) ENDLOCK(C). {
    $this->lock->end = true;
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