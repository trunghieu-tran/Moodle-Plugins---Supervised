%name preg_parser_
%include{
    require_once($CFG->dirroot . '/question/type/preg/node.php');
}
%include_class {
    function get_root() {
        return $this->root;
    }
    private $root;
}
%left ALT.
%left CONC.
%nonassoc QUEST PLUS ITER QUANT LAZY_ITER LAZY_QUEST LAZY_PLUS LAZY_QUANT.
%nonassoc CLOSEBRACK.

start ::= expr(B). {
    $this->root = B;
}
expr(A) ::= expr(B) CONC expr(C). {
    A->type = NODE;
    A->subtype = NODE_CONC;
    A->firop = B;
    A->secop = C;
}
expr(A) ::= expr(B) ALT expr(C). {
    A->type = NODE;
    A->subtype = NODE_ALT;
    A->firop = B;
    A->secop = C;
}
expr(A) ::= expr(B) ALT. {
    A->type = NODE;
    A->subtype = NODE_ALT;
    A->firop = B;
    A->secop = new node;
    A->secop->type = LEAF;
    A->secop->subtype = LEAF_EMPTY;
}
expr(A) ::= expr(B) QUEST. {
    A->type = NODE;
    A->subtype = NODE_QUESTQUANT;
    A->greed = true;
    A->firop = B;
}
expr(A) ::= expr(B) ITER. {
    A->type = NODE;
    A->subtype = NODE_ITER;
    A->greed = true;
    A->firop = B;
}
expr(A) ::= expr(B) PLUS. {
    A->type = NODE;
    A->subtype = NODE_PLUSQUANT;
    A->greed = true;
    A->firop = B;
}
expr(A) ::= expr(B) LAZY_QUEST. {
    A->type = NODE;
    A->subtype = NODE_QUESTQUANT;
    A->greed = false;
    A->firop = B;
}
expr(A) ::= expr(B) LAZY_ITER. {
    A->type = NODE;
    A->subtype = NODE_ITER;
    A->greed = false;
    A->firop = B;
}
expr(A) ::= expr(B) LAZY_PLUS. {
    A->type = NODE;
    A->subtype = NODE_PLUSQUANT;
    A->greed = false;
    A->firop = B;
}
expr(A) ::= expr(B) QUANT(C). {
    A->type = NODE;
    A->subtype = NODE_QUANT;
    A->greed = true;
    A->leftborder = C->leftborder;
    A->rightborder = C->rightborder;
    A->firop = B;
}
expr(A) ::= expr(B) LAZY_QUANT(C). {
    A->type = NODE;
    A->subtype = NODE_QUANT;
    A->greed = false;
    A->leftborder = C->leftborder;
    A->rightborder = C->rightborder;
    A->firop = B;
}
expr(A) ::= OPENBRACK expr(B) CLOSEBRACK. {
    A->type = NODE;
    A->subtype = NODE_SUBPATT;
    A->firop = B;
}
expr(A) ::= GROUPING expr(B) CLOSEBRACK. {
    A = B;
}
expr(A) ::= ASSERT_TF expr(B) CLOSEBRACK. {
    A->type = NODE;
    A->subtype = NODE_ASSERTTF;
    A->firop = B;
}
expr(A) ::= ASSERT_TB expr(B) CLOSEBRACK. {
    A->type = NODE;
    A->subtype = NODE_ASSERTTB;
    A->firop = B;
}
expr(A) ::= ASSERT_FF expr(B) CLOSEBRACK. {
    A->type = NODE;
    A->subtype = NODE_ASSERTFF;
    A->firop = B;
}
expr(A) ::= ASSERT_FB expr(B) CLOSEBRACK. {
    A->type = NODE;
    A->subtype = NODE_ASSERTFB;
    A->firop = B;
}
expr(A) ::= CONDSUBPATT ASSERT_TF expr(B) CLOSEBRACK expr(C) ALT expr(D) CLOSEBRACK. {
    A->type = NODE;
    A->subtype = NODE_CONDSUBPATT;
    A->firop = C;
    A->secop = D;
    A->thirdop->type = NODE;
    A->thirdop->subtype = NODE_ASSERTTF;
    A->thirdop->firop = B;
}
expr(A) ::= CONDSUBPATT ASSERT_TB expr(B) CLOSEBRACK expr(C) ALT expr(D) CLOSEBRACK. {
    A->type = NODE;
    A->subtype = NODE_CONDSUBPATT;
    A->firop = C;
    A->secop = D;
    A->thirdop->type = NODE;
    A->thirdop->subtype = NODE_ASSERTTB;
    A->thirdop->firop = B;
}
expr(A) ::= CONDSUBPATT ASSERT_FF expr(B) CLOSEBRACK expr(C) ALT expr(D) CLOSEBRACK. {
    A->type = NODE;
    A->subtype = NODE_CONDSUBPATT;
    A->firop = C;
    A->secop = D;
    A->thirdop->type = NODE;
    A->thirdop->subtype = NODE_ASSERTFF;
    A->thirdop->firop = B;
}
expr(A) ::= CONDSUBPATT ASSERT_FB expr(B) CLOSEBRACK expr(C) ALT expr(D) CLOSEBRACK. {
    A->type = NODE;
    A->subtype = NODE_CONDSUBPATT;
    A->firop = C;
    A->secop = D;
    A->thirdop->type = NODE;
    A->thirdop->subtype = NODE_ASSERTFB;
    A->thirdop->firop = B;
}
expr(A) ::= PARSLEAF(B). {
    A = B;
}