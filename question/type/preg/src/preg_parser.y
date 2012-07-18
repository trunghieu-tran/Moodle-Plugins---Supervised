%name preg_parser_
%include{
    require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
    require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
}
%include_class {
    // Root of the Abstract Syntax Tree (AST).
    private $root;
    // Are there any errors during the parsing.
    private $error;
    // Copies of qtype_preg_node_error for errors during the parsing.
    private $errornodes;
    // Count of reduces made.
    private $reducecount;
    // Open-parenthesis strings.
    private $parens;
    // Quantifier strings.
    private $quants;
    // Node id counter.
    private $idcounter;

    function __construct() {
        $this->error = false;
        $this->errornodes = array();
        $this->reducecount = 0;
        $this->parens = array('grouping' => '(?:',
                              qtype_preg_node_subpatt::SUBTYPE_SUBPATT => '(',
                              qtype_preg_node_subpatt::SUBTYPE_ONCEONLY => '(?>',
                              qtype_preg_node_assert::SUBTYPE_PLA => '(?=',
                              qtype_preg_node_assert::SUBTYPE_PLB => '(?<=',
                              qtype_preg_node_assert::SUBTYPE_NLA => '(?!',
                              qtype_preg_node_assert::SUBTYPE_NLB => '(?<!',
                              qtype_preg_node_cond_subpatt::SUBTYPE_PLA => '(?(?=',
                              qtype_preg_node_cond_subpatt::SUBTYPE_PLB => '(?(?<=',
                              qtype_preg_node_cond_subpatt::SUBTYPE_NLA => '(?(?!',
                              qtype_preg_node_cond_subpatt::SUBTYPE_NLB => '(?(?<!');
        $this->idcounter = 0;
    }

    function get_root() {
        return $this->root;
    }

    function get_error() {
        return $this->error;
    }

    public function get_error_nodes() {
        return $this->errornodes;
    }

    /**
     * Creates and returns an error node, also adds it to the array of parser errors
     * @param subtype type of error
     * @param indfirst the starting index of the highlited area
     * @param indlast the ending index of the highlited area
     * @param addinfo additional info, supplied for this error
     * @return qtype_preg_node_error object
     */
    protected function create_error_node($subtype, $indfirst = -1, $indlast = -1, $addinfo = null) {
        $newnode = new qtype_preg_node_error;
        $newnode->subtype = $subtype;
        $newnode->indfirst = $indfirst;
        $newnode->indlast = $indlast;
        $newnode->addinfo = $addinfo;
        $this->errornodes[] = $newnode;
        $this->error = true;
        return $newnode;
    }
}
%parse_failure {
    if (!$this->error) {
        $this->create_error_node(qtype_preg_node_error::SUBTYPE_UNKNOWN_ERROR);
        $this->error = true;
    }
}
%nonassoc ERROR_PREC_VERY_SHORT.
%nonassoc ERROR_PREC_SHORT.
%nonassoc ERROR_PREC.
%nonassoc CLOSEBRACK.
%left ALT.
%left CONC PARSLEAF.
%nonassoc QUANT.
%nonassoc OPENBRACK CONDSUBPATT.

start ::= lastexpr(B). {
    $this->root = B;
}

expr(A) ::= expr(B) expr(C). [CONC] {
    A = new qtype_preg_node_concat;
    A->operands[0] = B;
    A->operands[1] = C;
    A->userinscription = '';
    A->id = $this->idcounter++;
    $this->reducecount++;
    A->indfirst = B->indfirst;
    A->indlast = C->indlast;
}

expr(A) ::= expr(B) ALT expr(C). {
    //ECHO 'ALT <br/>';
    A = new qtype_preg_node_alt;
    A->operands[0] = B;
    A->operands[1] = C;
    A->userinscription = '|';
    A->id = $this->idcounter++;
    $this->reducecount++;
    A->indfirst = B->indfirst;
    A->indlast = C->indlast;
}

expr(A) ::= expr(B) ALT. {
    A = new qtype_preg_node_alt;
    A->operands[0] = B;
    A->operands[1] = new qtype_preg_leaf_meta;
    A->operands[1]->subtype = qtype_preg_leaf_meta::SUBTYPE_EMPTY;
    A->userinscription = '|';
    A->id = $this->idcounter++;
    $this->reducecount++;
    A->indfirst = B->indfirst;
    A->indlast = B->indlast + 1;
}

expr(A) ::= expr(B) QUANT(C). {
    A = C;
    A->operands[0] = B;
    A->id = $this->idcounter++;
    $this->reducecount++;
    A->indfirst = B->indfirst;
    A->indlast = C->indlast;
}

expr(A) ::= OPENBRACK(B) expr(C) CLOSEBRACK. {
    //ECHO 'SUBPATT '.$this->parens[B].'<br/>';
    if (B->subtype == 'grouping') {
        A = C;
    } else {
        if (B->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT || B->subtype === qtype_preg_node_subpatt::SUBTYPE_ONCEONLY) {
            A = new qtype_preg_node_subpatt;
            A->number = B->number;
        } else {
            A = new qtype_preg_node_assert;
        }
        A->subtype = B->subtype;
        A->operands[0] = C;
        A->id = $this->idcounter++;
        A->userinscription = B->userinscription . ' ... )';
    }
    A->indfirst = B->indfirst;
    A->indlast = C->indlast + 1;
    $this->reducecount++;
}

expr(A) ::= CONDSUBPATT(D) expr(B) CLOSEBRACK expr(C) CLOSEBRACK. {
    A = new qtype_preg_node_cond_subpatt;
    if (C->type != qtype_preg_node::TYPE_NODE_ALT) {
        A->operands[0] = C;
    } else {
        if (C->operands[0]->type == qtype_preg_node::TYPE_NODE_ALT || C->operands[1]->type == qtype_preg_node::TYPE_NODE_ALT) {
            //One or two top-level alternative allowed in conditional subpattern
            A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBPATT_TOO_MUCH_ALTER, D->indfirst, C->indlast + 1);
            $this->reducecount++;
            return;
        } else {
            A->operands[0] = C->operands[0];
            A->operands[1] = C->operands[1];
        }
    }
    A->operands[2] = new qtype_preg_node_assert;
    A->operands[2]->subtype = D->subtype;
    A->operands[2]->operands[0] = B;
    A->operands[2]->userinscription = qtype_poasquestion_string::substr(D->userinscription, 2) . ' ... )';
    A->operands[2]->id = $this->idcounter++;

    A->userinscription = D->userinscription . ' ... ) ... | .... )';
    A->id = $this->idcounter++;
    $this->reducecount++;
    A->indfirst = D->indfirst;
    A->indlast = C->indlast + 1;
}

expr(A) ::= PARSLEAF(B). {
    //ECHO 'LEAF <br/>';
    A = B;
    A->userinscription = B->userinscription;
    A->id = $this->idcounter++;
    $this->reducecount++;
    A->indfirst = B->indfirst;
    A->indlast = B->indlast;
}

lastexpr(A) ::= expr(B). {
    A = B;
    A->userinscription = B->userinscription;
    $this->reducecount++;
}

expr(A) ::= expr(B) CLOSEBRACK. [ERROR_PREC] {
    //ECHO 'UNOPENPARENS <br/>';
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN, B->indlast + 1, B->indlast + 1);
    $this->reducecount++;
}

expr(A) ::= CLOSEBRACK(B). [ERROR_PREC_SHORT] {
    //ECHO 'CLOSEPARENATSTART <br/>';
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN, B->indfirst, B->indlast);
    $this->reducecount++;
}

expr(A) ::= OPENBRACK(B) expr(C). [ERROR_PREC] {
    //ECHO 'UNCLOSEDPARENS <br/>';
    $emptyparens = false;
    foreach($this->errornodes as $key=>$node) {
        if ($node->subtype == qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN && $node->indfirst == B->indlast + 1) {//empty parens, avoiding two error messages
            unset($this->errornodes[$key]);
            A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_EMPTY_PARENS, B->indfirst, B->indlast + 1, $this->parens[B->subtype]);
            $emptyparens = true;
        }
    }
    if (!$emptyparens) {//regular unclosed parens
        A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, B->indfirst, B->indlast, $this->parens[B->subtype]);
    }
    $this->reducecount++;
}

expr(A) ::= OPENBRACK(B). [ERROR_PREC_SHORT] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, B->indfirst,  B->indlast, $this->parens[B->subtype]);
    $this->reducecount++;
}

expr(A) ::= CONDSUBPATT(B) expr CLOSEBRACK(D) expr(C). [ERROR_PREC] {
    //ECHO 'UNCLOSEDPARENS <br/>';
    $emptyparens = false;
    foreach($this->errornodes as $key=>$node) {
        if ($node->subtype == qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN && $node->indfirst == D->indlast + 1) {//empty parens, avoiding two error messages
            unset($this->errornodes[$key]);
            A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_EMPTY_PARENS, B->indfirst, D->indlast + 1, $this->parens[B->subtype]);
            $emptyparens = true;
        }
    }
    if (!$emptyparens) {//regular unclosed parens
        A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, B->indfirst, B->indlast, $this->parens[B->subtype]);
    }
    $this->reducecount++;
}

expr(A) ::= CONDSUBPATT(B) expr(C). [ERROR_PREC_SHORT] {
    //ECHO 'UNCLOSEDPARENS <br/>';
    //Two unclosed parens for conditional subpatterns
    //Create only one error node to avoid confusion when reporting errors to the user
    $emptyparens = false;
    foreach($this->errornodes as $key=>$node) {
        if ($node->subtype == qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN && $node->indfirst == B->indlast + 1) {//unclosed parens + empty parens, avoiding two error messages
            unset($this->errornodes[$key]);
            A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_EMPTY_PARENS, B->indfirst, B->indlast + 1, $this->parens[B->subtype]);
            $emptyparens = true;
        }
    }
    if (!$emptyparens) {//two unclosed parens
        A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, B->indfirst, B->indlast, $this->parens[B->subtype]);
    }
    $this->reducecount++;
}

expr(A) ::= CONDSUBPATT(B). [ERROR_PREC_VERY_SHORT] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, B->indfirst,  B->indlast, $this->parens[B->subtype]);
    $this->reducecount++;
}

expr(A) ::= QUANT(B). [ERROR_PREC] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER, B->indfirst,  B->indlast);
    $this->reducecount++;
}

lastexpr(A) ::= lastexpr(B) LEXERROR(C). {
    A = $this->create_error_node(C->subtype, C->indfirst, C->indlast, C->userinscription);
    $this->reducecount++;
}

lastexpr(A) ::= LEXERROR(C). {
    A = $this->create_error_node(C->subtype, C->indfirst, C->indlast, C->userinscription);
    $this->reducecount++;
}
