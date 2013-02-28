%name qtype_preg_
%include{
    global $CFG;
    require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
    require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
    require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
}
%include_class {
    // Root of the Abstract Syntax Tree (AST).
    private $root;
    // Objects of qtype_preg_node_error for errors found during the parsing.
    private $errornodes;
    // Handling options.
    private $handlingoptions;

    public function __construct($handlingoptions = null) {
        $this->root = null;
        $this->errornodes = array();
        if ($handlingoptions == null) {
            $handlingoptions = new qtype_preg_handling_options();
        }
        $this->handlingoptions = $handlingoptions;
    }

    public function get_root() {
        return $this->root;
    }

    public function get_error() {
        return (count($this->errornodes) > 0);
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
    protected function create_error_node($subtype, $indfirst = -1, $indlast = -1, $addinfo = null, $userinscription = null, $operands = array()) {
        $newnode = new qtype_preg_node_error($subtype, $addinfo);
        $newnode->set_user_info($indfirst, $indlast, $userinscription);
        $newnode->operands = $operands;
        $this->errornodes[] = $newnode;
        return $newnode;
    }

    /**
     * Creates error node(s) if there is an error in the given node.
     * @param node the node to be checked.
     */
    protected function create_error_node_from_lexer($node) {
        if (isset($node->type) && $node->type === qtype_preg_node::TYPE_NODE_ERROR) {
            $this->create_error_node($node->subtype, $node->indfirst, $node->indlast, $node->addinfo, $node->userinscription);
        }
        if (!isset($node->error)) {
            return;
        }
        if (is_array($node->error)) {
            foreach ($node->error as $error) {
                $this->create_error_node($error->subtype, $error->indfirst, $error->indlast, $error->addinfo, $error->userinscription);
            }
        } else if ($node->error !== null) {
            $this->create_error_node($node->error->subtype, $node->error->indfirst, $node->error->indlast, $node->error->addinfo, $node->error->userinscription);
        }
    }

    /**
      * Creates and return correct parenthesis node (subpattern, groping or assertion).
      *
      * Used to avoid code duplication between empty and non-empty parenthesis.
      * @param parens parenthesis token from lexer
      * @param exprnode the node for expression inside parenthesis
      */
    protected function create_parens_node($parens, $exprnode) {
        $result = null;
        if ($parens->subtype === qtype_preg_node_subpatt::SUBTYPE_GROUPING && !$this->handlingoptions->preserveallnodes) {
            $result = $exprnode;
        } else {
            if ($parens->subtype === qtype_preg_node_subpatt::SUBTYPE_GROUPING) {
                $result = new qtype_preg_node_subpatt(-1);
            } else if ($parens->subtype === qtype_preg_node_subpatt::SUBTYPE_SUBPATT || $parens->subtype === qtype_preg_node_subpatt::SUBTYPE_ONCEONLY) {
                $result = new qtype_preg_node_subpatt($parens->number);
            } else {
                $result = new qtype_preg_node_assert();
            }
            $result->subtype = $parens->subtype;
            $result->operands[0] = $exprnode;
            $result->userinscription = new qtype_preg_userinscription($parens->userinscription->data . '...)');
        }
        $result->set_user_info($parens->indfirst, $exprnode->indlast + 1, $result->userinscription);
        return $result;
    }

    protected function create_cond_subpatt_assertion_node($paren, $assertnode, $exprnode) {
        if ($assertnode === null) {
            $assertnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $assertnode->set_user_info($paren->indlast, $paren->indlast, new qtype_preg_userinscription());
        }
        if ($exprnode === null) {
            $exprnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $exprnode->set_user_info($assertnode->indlast + 1, $assertnode->indlast + 1, new qtype_preg_userinscription());
        }
        if ($exprnode->type != qtype_preg_node::TYPE_NODE_ALT) {
            $result = new qtype_preg_node_cond_subpatt($paren->subtype);
            $result->operands[0] = $exprnode;
        } else {
            // Error: only one or two top-level alternative allowed in a conditional subpattern.
            if ($exprnode->operands[0]->type == qtype_preg_node::TYPE_NODE_ALT || $exprnode->operands[1]->type == qtype_preg_node::TYPE_NODE_ALT) {
                $result = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBPATT_TOO_MUCH_ALTER, $paren->indfirst, $exprnode->indlast + 1, null, null, array($exprnode, $assertnode));
                return $result;
            } else {
                $result = new qtype_preg_node_cond_subpatt($paren->subtype);
                $result->operands[0] = $exprnode->operands[0];
                $result->operands[1] = $exprnode->operands[1];
            }
        }
        if ($paren->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLA) {
            $subtype = qtype_preg_node_assert::SUBTYPE_PLA;
        } else if ($paren->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLB) {
            $subtype = qtype_preg_node_assert::SUBTYPE_PLB;
        } else if ($paren->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLA) {
            $subtype = qtype_preg_node_assert::SUBTYPE_NLA;
        } else {
            $subtype = qtype_preg_node_assert::SUBTYPE_NLB;
        }
        $result->operands[2] = new qtype_preg_node_assert($subtype);
        $result->operands[2]->operands[0] = $assertnode;
        $result->operands[2]->userinscription = new qtype_preg_userinscription(qtype_poasquestion_string::substr($paren->userinscription->data, 2) . '...)');
        $result->set_user_info($paren->indfirst, $exprnode->indlast + 1, new qtype_preg_userinscription($paren->userinscription->data . '...)...|...)'));
        return $result;
    }

    protected function create_cond_subpatt_other_node($paren, $exprnode) {
        if ($exprnode === null) {
            $exprnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $exprnode->set_user_info($paren->indlast + 2, $paren->indlast + 2, new qtype_preg_userinscription());
        }
        if ($exprnode->type != qtype_preg_node::TYPE_NODE_ALT) {
            $result = new qtype_preg_node_cond_subpatt($paren->subtype);
            $result->operands[0] = $exprnode;
        } else {
             // Error: only one or two top-level alternative allowed in a conditional subpattern.
            if ($exprnode->operands[0]->type == qtype_preg_node::TYPE_NODE_ALT || $exprnode->operands[1]->type == qtype_preg_node::TYPE_NODE_ALT) {
                $result = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBPATT_TOO_MUCH_ALTER, $paren->indfirst, $exprnode->indlast + 1, null, null, array($exprnode));
                return $result;
            } else {
                $result = new qtype_preg_node_cond_subpatt($paren->subtype);
                $result->operands[0] = $exprnode->operands[0];
                $result->operands[1] = $exprnode->operands[1];
            }
        }
        if ($paren->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT) {
            $result->number = $paren->number;
        }
        $result->set_user_info($paren->indfirst, $exprnode->indlast + 1, new qtype_preg_userinscription($paren->userinscription->data . '...|...)'));
        return $result;
    }

    protected function create_concat_node($left, $right, $indfirst, $indlast, $userinscription) {
        $result = new qtype_preg_node_concat();
        $result->set_user_info($indfirst, $indlast, $userinscription);
        $result->operands[0] = $left;
        $result->operands[1] = $right;
        return $result;
    }

    protected function calculate_nodes_nesting($node) {
        $node->nested = array();
        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $operand) {
                $this->calculate_nodes_nesting($operand);
                $node->nested = array_merge($node->nested, $operand->nested);
                $node->nested[] = $operand;
            }
        }
    }

    protected function remove_subpatterns($node) {
        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $operand) {
                $this->remove_subpatterns($operand);
            }
        }
        if ($node->type == qtype_preg_node::TYPE_NODE_SUBPATT) {
            $node->subtype = qtype_preg_node_subpatt::SUBTYPE_GROUPING;
            $node->number = -1;
        }
    }

    // Returns a node which should replace the given one.
    protected function expand_tree($node) {
        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $key => $operand) {
                $node->operands[$key] = $this->expand_tree($operand);
            }
        }

        // Operators {0} {1} ? * and + can be skipped.
        $isnull = ($node->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT && $node->leftborder == 0 && $node->rightborder == 0);
        $isone = ($node->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT && $node->leftborder == 1 && $node->rightborder == 1);
        $isqu = ($node->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT && $node->leftborder == 0 && $node->rightborder == 1);
        $isaster = ($node->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT && $node->leftborder == 0);
        $isplus = ($node->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT && $node->leftborder == 1);

        if ($isnull || $isone || $isqu || $isaster || $isplus) {
            return $node;
        }

        // Convert {m,n}
        if ($node->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT) {
            // Create clones.
            $clones = array();
            for ($i = 0; $i < $node->rightborder; $i++) {
                $tmp = clone $node->operands[0];
                $res = $tmp;
                if ($i >= $node->leftborder) {
                    $qu = new qtype_preg_node_finite_quant(0, 1);
                    $qu->set_user_info($node->indfirst, $node->indlast, $node->userinscription);
                    $qu->operands[0] = $tmp;
                    $res = $qu;
                }
                // Remove subpatterns from all but last repetition.
                if ($i != $node->rightborder - 1) {
                    $this->remove_subpatterns($res);
                }
                $clones[] = $res;
            }

            // Concatenate created clones.
            $concat = $this->create_concat_node($clones[0], $clones[1], $node->indfirst, $node->indlast, new qtype_preg_userinscription());
            for ($i = 2; $i < count($clones); $i++) {
                $concat = $this->create_concat_node($concat, $clones[$i], $node->indfirst, $node->indlast, new qtype_preg_userinscription());
            }
            return $concat;
        }

        // Convert {m,}
        if ($node->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT) {
            // Create clones.
            $clones = array();
            for ($i = 0; $i < $node->leftborder; $i++) {
                $tmp = clone $node->operands[0];
                $res = $tmp;
                if ($i == $node->leftborder - 1) {
                    $aster = new qtype_preg_node_infinite_quant(0);
                    $aster->set_user_info($node->indfirst, $node->indlast, $node->userinscription);
                    $aster->operands[0] = $tmp;
                    $res = $aster;
                }
                // Remove subpatterns from all but last repetition.
                if ($i != $node->leftborder - 1) {
                    $this->remove_subpatterns($res);
                }
                $clones[] = $res;
            }

            // Concatenate created clones.
            $concat = $this->create_concat_node($clones[0], $clones[1], $node->indfirst, $node->indlast, new qtype_preg_userinscription());
            for ($i = 2; $i < count($clones); $i++) {
                $concat = $this->create_concat_node($concat, $clones[$i], $node->indfirst, $node->indlast, new qtype_preg_userinscription());
            }
            return $concat;
        }

        // Return the subtree unmodified.
        return $node;
    }

    protected function numerate_ast_nodes($node, &$currentnumber) {
        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $operand) {
                $this->numerate_ast_nodes($operand, $currentnumber);
            }
        }
        $node->id = $currentnumber++;
    }
}
%parse_failure {
    if (count($this->errornodes) === 0) {
        $this->create_error_node(qtype_preg_node_error::SUBTYPE_UNKNOWN_ERROR);
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
    // Set the root node.
    $this->root = B;

    // Expand the tree if needed.
    if ($this->handlingoptions->expandtree) {
        $this->root = $this->expand_tree($this->root);
    }

    // Numerate all nodes.
    $idcounter = 1;
    $this->numerate_ast_nodes($this->root, $idcounter);

    // Calculate nodes nesting.
    $this->calculate_nodes_nesting($this->root);
}

expr(A) ::= expr(B) expr(C). [CONC] {
    A = $this->create_concat_node(B, C, B->indfirst, C->indlast, new qtype_preg_userinscription());
}

expr(A) ::= expr(B) ALT expr(C). {
    A = new qtype_preg_node_alt();
    A->set_user_info(B->indfirst, C->indlast, new qtype_preg_userinscription('|'));
    A->operands[0] = B;
    A->operands[1] = C;
}

expr(A) ::= expr(B) ALT. {
    A = new qtype_preg_node_finite_quant(0, 1, false, true, false);
    A->set_user_info(B->indfirst, B->indlast + 1, new qtype_preg_userinscription('|'));
    A->operands[0] = B;
}

expr(A) ::= expr(B) QUANT(C). {
    A = C;
    A->set_user_info(B->indfirst, C->indlast, C->userinscription);
    A->operands[0] = B;
    $this->create_error_node_from_lexer(C);
}

expr(A) ::= OPENBRACK(B) CLOSEBRACK. {
    $emptynode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    $emptynode->set_user_info(B->indlast, B->indlast, new qtype_preg_userinscription());
    A = $this->create_parens_node(B, $emptynode);
    $this->create_error_node_from_lexer(B);
}

expr(A) ::= OPENBRACK(B) expr(C) CLOSEBRACK. {
    A = $this->create_parens_node(B, C);
    $this->create_error_node_from_lexer(B);
}

expr(A) ::= CONDSUBPATT(D) expr(B) CLOSEBRACK expr(C) CLOSEBRACK. {
    if (D->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLA || D->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLA ||
        D->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLB || D->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLB) {
        A = $this->create_cond_subpatt_assertion_node(D, B, C);
    } else {
        A = $this->create_cond_subpatt_other_node(D, C);
    }
}

expr(A) ::= CONDSUBPATT(D) expr(B) CLOSEBRACK CLOSEBRACK. {
    if (D->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLA || D->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLA ||
        D->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLB || D->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLB) {
        A = $this->create_cond_subpatt_assertion_node(D, B, null);
    } else {
        A = $this->create_cond_subpatt_other_node(D, null);
    }
}

expr(A) ::= CONDSUBPATT(D) CLOSEBRACK expr(C) CLOSEBRACK. {
    if (D->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLA || D->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLA ||
        D->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLB || D->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLB) {
        A = $this->create_cond_subpatt_assertion_node(D, null, C);
    } else {
        A = $this->create_cond_subpatt_other_node(D, C);
    }
}

expr(A) ::= CONDSUBPATT(D) CLOSEBRACK CLOSEBRACK. {
    if (D->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLA || D->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLA ||
        D->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLB || D->subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLB) {
        A = $this->create_cond_subpatt_assertion_node(D, null, null);
    } else {
        A = $this->create_cond_subpatt_other_node(D, null);
    }
}

expr(A) ::= PARSLEAF(B). {
    A = B;
    $this->create_error_node_from_lexer(B);
}

lastexpr(A) ::= expr(B). {
    A = B;
}


/**************************************************
 * Below are the rules for errors reporting.     *
 **************************************************/


expr(A) ::= expr(B) CLOSEBRACK. [ERROR_PREC] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN, B->indlast + 1, B->indlast + 1, null, null, array(B));
}

expr(A) ::= CLOSEBRACK(B). [ERROR_PREC_SHORT] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN, B->indfirst, B->indlast, ')', new qtype_preg_userinscription(')'));
}

expr(A) ::= OPENBRACK(B) expr(C). [ERROR_PREC] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, B->indfirst, B->indlast, B->userinscription->data, B->userinscription, array(C));
    $this->create_error_node_from_lexer(B);
}

expr(A) ::= OPENBRACK(B). [ERROR_PREC_SHORT] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, B->indfirst,  B->indlast, B->userinscription->data, B->userinscription);
    $this->create_error_node_from_lexer(B);
}

expr(A) ::= CONDSUBPATT(B) expr(E) CLOSEBRACK(D) expr(C). [ERROR_PREC] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, B->indfirst, B->indlast, B->userinscription->data, B->userinscription, array(C, E));
}

expr(A) ::= CONDSUBPATT(B) expr(C). [ERROR_PREC_SHORT] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, B->indfirst, B->indlast, B->userinscription->data, B->userinscription, array(C));
}

expr(A) ::= CONDSUBPATT(B). [ERROR_PREC_VERY_SHORT] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, B->indfirst,  B->indlast, B->userinscription->data, B->userinscription);
}

expr(A) ::= QUANT(B). [ERROR_PREC] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER, B->indfirst,  B->indlast, B->userinscription->data, B->userinscription);
    $this->create_error_node_from_lexer(B);
}
