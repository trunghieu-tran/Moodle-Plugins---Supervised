%name preg_parser_
%include{
    require_once($CFG->dirroot . '/question/type/preg/node.php');
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
    //Assert characters
    private $assertchars;

    function __construct() {
        $this->anchor = new stdClass;
        $this->anchor->start = false;
        $this->anchor->end = false;
        $this->error = false;
        $this->errormessages = array();
        $this->reducecount = 0;
        $this->assertchars = array(NODE_ASSERTTF => '(?=', NODE_ASSERTTB => '(?<=',NODE_ASSERTFF => '(?!', NODE_ASSERTFB => '(?<!');
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
        $newnode = new node;
        $newnode->type = ERROR;
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
%nonassoc QUEST PLUS ITER QUANT LAZY_ITER LAZY_QUEST LAZY_PLUS LAZY_QUANT.
%nonassoc OPENBRACK GROUPING ASSERT CONDSUBPATT ONETIMESUBPATT.

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
    $this->reducecount++;
}
expr(A) ::= expr(B) expr(C). [CONC] {
    ECHO 'CONC1 <br/>';
    A = new node;
    A->type = NODE;
    A->subtype = NODE_CONC;
    A->firop = B;
    A->secop = C;
    $this->reducecount++;
}
expr(A) ::= expr(B) ALT expr(C). {
    ECHO 'ALT <br/>';
    A = new node;
    A->type = NODE;
    A->subtype = NODE_ALT;
    A->firop = B;
    A->secop = C;
    $this->reducecount++;
}
expr(A) ::= expr(B) ALT. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_ALT;
    A->firop = B;
    A->secop = new node;
    A->secop->type = LEAF;
    A->secop->subtype = LEAF_EMPTY;
    $this->reducecount++;
}
expr(A) ::= expr(B) QUEST. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_QUESTQUANT;
    A->greed = true;
    A->firop = B;
    $this->reducecount++;
}
expr(A) ::= expr(B) ITER. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_ITER;
    A->greed = true;
    A->firop = B;
    $this->reducecount++;
}
expr(A) ::= expr(B) PLUS. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_PLUSQUANT;
    A->greed = true;
    A->firop = B;
    $this->reducecount++;
}
expr(A) ::= expr(B) LAZY_QUEST. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_QUESTQUANT;
    A->greed = false;
    A->firop = B;
    $this->reducecount++;
}
expr(A) ::= expr(B) LAZY_ITER. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_ITER;
    A->greed = false;
    A->firop = B;
    $this->reducecount++;
}
expr(A) ::= expr(B) LAZY_PLUS. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_PLUSQUANT;
    A->greed = false;
    A->firop = B;
    $this->reducecount++;
}
expr(A) ::= expr(B) QUANT(C). {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_QUANT;
    A->greed = true;
    A->leftborder = C->leftborder;
    A->rightborder = C->rightborder;
    A->firop = B;
    $this->reducecount++;
}
expr(A) ::= expr(B) LAZY_QUANT(C). {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_QUANT;
    A->greed = false;
    A->leftborder = C->leftborder;
    A->rightborder = C->rightborder;
    A->firop = B;
    $this->reducecount++;
}
expr(A) ::= OPENBRACK expr(B) CLOSEBRACK. {
    ECHO 'SUBPATT <br/>';
    A = new node;
    A->type = NODE;
    A->subtype = NODE_SUBPATT;
    A->firop = B;
    $this->reducecount++;
}
expr(A) ::= GROUPING expr(B) CLOSEBRACK. {
    A = B;
    $this->reducecount++;
}
expr(A) ::= ASSERT(B) expr(C) CLOSEBRACK. {
    ECHO  'ASSERT TF <br/>';
    A = new node;
    A->type = NODE;
    A->subtype = B;
    A->firop = C;
    $this->reducecount++;
}
expr(A) ::= CONDSUBPATT(D) expr(B) CLOSEBRACK expr(C) CLOSEBRACK. {
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
    A->thirdop->subtype = D;
    A->thirdop->firop = B;
    $this->reducecount++;
}
expr(A) ::= PARSLEAF(B). {
    ECHO 'LEAF <br/>';
    A = new node;
    A = B;
    $this->reducecount++;
}
expr(A) ::= STARTANCHOR(B) expr(C). {
    $this->anchor->start = true;
    A = new node;
    A = C;
    $this->reducecount++;
}
lastexpr(A) ::= lastexpr(B) ENDANCHOR(C). {
    $this->anchor->end = true;
    A = new node;
    A = B;
    $this->reducecount++;
}
lastexpr(A) ::= expr(B). {
    A = new node;
    A = B;
    $this->reducecount++;
}
expr(A) ::= ONETIMESUBPATT expr(B) CLOSEBRACK. {
    A = new node;
    A->type = NODE;
    A->subtype = NODE_ONETIMESUBPATT;
    A->firop = B;
    $this->reducecount++;
}
expr(A) ::= WORDBREAK . {
    A = new node;
    A->type = LEAF;
    A->subtype = LEAF_WORDBREAK;
    $this->reducecount++;
}
expr(A) ::= WORDNOTBREAK . {
    A = new node;
    A->type = LEAF;
    A->subtype = LEAF_WORDNOTBREAK;
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

expr(A) ::= OPENBRACK expr. [ERROR_PREC] {
    ECHO 'UNCLOSEDPARENS <br/>';
    $lasterrormsg = array_pop($this->errormessages);
    if ($lasterrormsg == get_string('closeparenatstart','qtype_preg')) {//empty brackets, avoiding two error messages
        A = $this->create_error_node('emptyparens','(');
    } else {//normal unclosed bracket
        if ($lasterrormsg != null) {
            $this->errormessages[] = $lasterrormsg;
        }
        A = $this->create_error_node('unclosedparen','(');
    }
    $this->reducecount++;
}

expr(A) ::= OPENBRACK. [ERROR_PREC_SHORT] {
    A = $this->create_error_node('openparenatend','(');
    $this->reducecount++;
}

expr(A) ::= GROUPING expr. [ERROR_PREC] {
    //ECHO 'UNCLOSEDPARENS <br/>';
    $lasterrormsg = array_pop($this->errormessages);
    if ($lasterrormsg == get_string('closeparenatstart','qtype_preg')) {//empty brackets, avoiding two error messages
        A = $this->create_error_node('emptyparens','(?:');
    } else {//normal unclosed bracket
        if ($lasterrormsg != null) {
            $this->errormessages[] = $lasterrormsg;
        }
        A = $this->create_error_node('unclosedparen','(?:');
    }
    $this->reducecount++;
}

expr(A) ::= GROUPING. [ERROR_PREC_SHORT] {
    A = $this->create_error_node('openparenatend','(?:');
    $this->reducecount++;
}

expr(A) ::= ASSERT(B) expr. [ERROR_PREC] {
    //ECHO 'UNCLOSEDPARENS <br/>';
    $lasterrormsg = array_pop($this->errormessages);
    if ($lasterrormsg == get_string('closeparenatstart','qtype_preg')) {//empty brackets, avoiding two error messages
        A = $this->create_error_node('emptyparens',$this->assertchars[B]);
    } else {//normal unclosed bracket
        if ($lasterrormsg != null) {
            $this->errormessages[] = $lasterrormsg;
        }
        A = $this->create_error_node('unclosedparen',$this->assertchars[B]);
    }
    $this->reducecount++;
}

expr(A) ::= ASSERT(B). [ERROR_PREC_SHORT] {
    A = $this->create_error_node('openparenatend',$this->assertchars[B]);
    $this->reducecount++; //TODO - get the next rule working and uncomment it. For now we still don't supporting conditional subpatterns anyway
}

expr(A) ::= CONDSUBPATT(B) expr CLOSEBRACK expr. [ERROR_PREC] {
    //ECHO 'UNCLOSEDPARENS <br/>';
    $lasterrormsg = array_pop($this->errormessages);
    if ($lasterrormsg == get_string('closeparenatstart','qtype_preg')) {//empty brackets, avoiding two error messages
        A = $this->create_error_node('emptyparens','(?'.$this->assertchars[B]);
    } else {//normal unclosed bracket
        if ($lasterrormsg != null) {
            $this->errormessages[] = $lasterrormsg;
        }
        A = $this->create_error_node('unclosedparen','(?'.$this->assertchars[B]);
    }
    $this->reducecount++;
}

expr(A) ::= CONDSUBPATT(B) expr. [ERROR_PREC_SHORT] {
    //ECHO 'UNCLOSEDPARENS <br/>';
    $lasterrormsg = array_pop($this->errormessages);
    if ($lasterrormsg == get_string('closeparenatstart','qtype_preg')) {//empty brackets, avoiding two error messages
        A = $this->create_error_node('emptyparens','(?'.$this->assertchars[B]);
    } else {//normal unclosed bracket
        if ($lasterrormsg != null) {
            $this->errormessages[] = $lasterrormsg;
        }
        A = $this->create_error_node('unclosedparen','(?'.$this->assertchars[B]);
    }
    $this->reducecount++;
}

expr(A) ::= CONDSUBPATT(B). [ERROR_PREC_VERY_SHORT] {
    A = $this->create_error_node('openparenatend','(?'.$this->assertchars[B]);
    $this->reducecount++;
}

expr(A) ::= ONETIMESUBPATT expr. [ERROR_PREC] {
    //ECHO 'UNCLOSEDPARENS <br/>';
    $lasterrormsg = array_pop($this->errormessages);
    if ($lasterrormsg == get_string('closeparenatstart','qtype_preg')) {//empty brackets, avoiding two error messages
        A = $this->create_error_node('emptyparens','(?>');
    } else {//normal unclosed bracket
        if ($lasterrormsg != null) {
            $this->errormessages[] = $lasterrormsg;
        }
        A = $this->create_error_node('unclosedparen','(?>');
    }
    $this->reducecount++;
}

expr(A) ::= ONETIMESUBPATT. [ERROR_PREC_SHORT] {
    A = $this->create_error_node('openparenatend','(?>');
    $this->reducecount++;
}

expr(A) ::= QUEST. [ERROR_PREC] {
    A = $this->create_error_node('quantifieratstart','?');
    $this->reducecount++;
}

expr(A) ::= PLUS. [ERROR_PREC] {
    A = $this->create_error_node('quantifieratstart','+');
    $this->reducecount++;
}

expr(A) ::= ITER. [ERROR_PREC] {
    A = $this->create_error_node('quantifieratstart','*');
    $this->reducecount++;
}

expr(A) ::= QUANT. [ERROR_PREC] {
    A = $this->create_error_node('quantifieratstart','{...}');
    $this->reducecount++;
}

expr(A) ::= LAZY_ITER. [ERROR_PREC] {
    A = $this->create_error_node('quantifieratstart','*?');
    $this->reducecount++;
}

expr(A) ::= LAZY_QUEST. [ERROR_PREC] {
    A = $this->create_error_node('quantifieratstart','??');
    $this->reducecount++;
}

expr(A) ::= LAZY_PLUS. [ERROR_PREC] {
    A = $this->create_error_node('quantifieratstart','+?');
    $this->reducecount++;
}

expr(A) ::= LAZY_QUANT. [ERROR_PREC] {
    A = $this->create_error_node('quantifieratstart','{...}?');
    $this->reducecount++;
}

lastexpr(A) ::= lastexpr(B) LEXERROR(C). {
    A = $this->create_error_node(C);
    $this->reducecount++;
}