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
    // Counter of nodes id. After parsing, equals the number of nodes in the tree.
    private $id_counter;
    // Counter of subpatterns.
    private $subpatt_counter;
    // Followpos map.
    private $followpos;
    // Max difference (right - left).
    private $max_finite_quant_borders_difference;   // В принципе это костыль; когда ДКА будет получаться из НКА - удалить это.

    public function __construct($handlingoptions = null) {
        $this->root = null;
        $this->errornodes = array();
        if ($handlingoptions == null) {
            $handlingoptions = new qtype_preg_handling_options();
        }
        $this->handlingoptions = $handlingoptions;
        $this->id_counter = 0;
        $this->subpatt_counter = 0;
        $this->followpos = array();
        $this->max_finite_quant_borders_difference = 0;
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

    public function get_max_subpatt() {
        return $this->subpatt_counter;
    }

    public function get_followpos() {
        return $this->followpos;
    }

    public function get_max_finite_quant_borders_difference() {
        return $this->max_finite_quant_borders_difference;
    }

    /**
     * Creates and returns an error node, also adds it to the array of parser errors
     * @param subtype type of error
     * @param indfirst the starting index of the highlited area
     * @param indlast the ending index of the highlited area
     * @param addinfo additional info, supplied for this error
     * @return qtype_preg_node_error object
     */
    protected function create_error_node($subtype, $addinfo, $indfirst, $indlast, $userinscription, $operands = array()) {
        $newnode = new qtype_preg_node_error($subtype, $addinfo);
        $newnode->set_user_info($indfirst, $indlast, $userinscription);
        $newnode->operands = $operands;
        $this->errornodes[] = $newnode;
        return $newnode;
    }

    /**
      * Creates and return correct parenthesis node (subexpression, groping or assertion).
      *
      * Used to avoid code duplication between empty and non-empty parenthesis.
      * @param parens parenthesis token from lexer
      * @param exprnode the node for expression inside parenthesis
      */
    protected function create_parens_node($parens, $exprnode) {
        $result = null;
        if ($parens->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING && !$this->handlingoptions->preserveallnodes) {
            $result = $exprnode;
        } else {
            if ($parens->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING) {
                $result = new qtype_preg_node_subexpr(-1);
            } else if ($parens->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR || $parens->subtype === qtype_preg_node_subexpr::SUBTYPE_ONCEONLY) {
                $result = new qtype_preg_node_subexpr($parens->number);
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

    protected function create_cond_subexpr_assertion_node($paren, $assertnode, $exprnode) {
        if ($assertnode === null) {
            $assertnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $assertnode->set_user_info($paren->indlast, $paren->indlast, new qtype_preg_userinscription());
        }
        if ($exprnode === null) {
            $exprnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $exprnode->set_user_info($assertnode->indlast + 1, $assertnode->indlast + 1, new qtype_preg_userinscription());
        }
        if ($exprnode->type != qtype_preg_node::TYPE_NODE_ALT) {
            $result = new qtype_preg_node_cond_subexpr($paren->subtype);
            $result->operands[0] = $exprnode;
        } else {
            // Error: only one or two top-level alternations allowed in a conditional subexpression.
            if ($exprnode->type == qtype_preg_node::TYPE_NODE_ALT && count($exprnode->operands) > 2) {
                $result = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_TOO_MUCH_ALTER, null, $paren->indfirst, $exprnode->indlast + 1, new qtype_preg_userinscription(), array($exprnode, $assertnode));
                return $result;
            } else {
                $result = new qtype_preg_node_cond_subexpr($paren->subtype);
                $result->operands = $exprnode->operands;
            }
        }
        if ($paren->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA) {
            $subtype = qtype_preg_node_assert::SUBTYPE_PLA;
        } else if ($paren->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB) {
            $subtype = qtype_preg_node_assert::SUBTYPE_PLB;
        } else if ($paren->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA) {
            $subtype = qtype_preg_node_assert::SUBTYPE_NLA;
        } else {
            $subtype = qtype_preg_node_assert::SUBTYPE_NLB;
        }
        $result->condbranch = new qtype_preg_node_assert($subtype);
        $result->condbranch->operands[0] = $assertnode;
        $result->condbranch->userinscription = new qtype_preg_userinscription(qtype_poasquestion_string::substr($paren->userinscription->data, 2) . '...)');
        $result->set_user_info($paren->indfirst, $exprnode->indlast + 1, new qtype_preg_userinscription($paren->userinscription->data . '...)...|...)'));
        return $result;
    }

    protected function create_cond_subexpr_other_node($paren, $exprnode) {
        if ($exprnode === null) {
            $exprnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $exprnode->set_user_info($paren->indlast + 2, $paren->indlast + 2, new qtype_preg_userinscription());
        }
        if ($exprnode->type != qtype_preg_node::TYPE_NODE_ALT) {
            $result = new qtype_preg_node_cond_subexpr($paren->subtype);
            $result->operands[0] = $exprnode;
        } else {
             // Error: only one or two top-level alternations allowed in a conditional subexpression.
            if ($exprnode->type == qtype_preg_node::TYPE_NODE_ALT && count($exprnode->operands) > 2) {
                $result = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_TOO_MUCH_ALTER, null, $paren->indfirst, $exprnode->indlast + 1, new qtype_preg_userinscription(), array($exprnode));
                return $result;
            } else {
                $result = new qtype_preg_node_cond_subexpr($paren->subtype);
                $result->operands[0] = $exprnode->operands[0];
                $result->operands[1] = $exprnode->operands[1];
            }
        }
        if ($paren->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR) {
            $result->number = $paren->number;
        }
        $result->set_user_info($paren->indfirst, $exprnode->indlast + 1, new qtype_preg_userinscription($paren->userinscription->data . '...|...)'));
        return $result;
    }

    protected function assign_subpatts($node) {
        if ($node->is_subpattern() || $node === $this->root) {
            $node->subpattern = $this->subpatt_counter++;
        }
        if (is_a($node, 'qtype_preg_operator')) {
            if ($node->type == qtype_preg_node::TYPE_NODE_COND_SUBEXPR && $node->condbranch !== null) {
                $this->assign_subpatts($node->condbranch);
            }
            foreach ($node->operands as $operand) {
                $this->assign_subpatts($operand);
            }
        }
    }

    protected function assign_ids($node) {
        $node->id = ++$this->id_counter;
        if (is_a($node, 'qtype_preg_operator')) {
            if ($node->type == qtype_preg_node::TYPE_NODE_COND_SUBEXPR && $node->condbranch !== null) {
                $this->assign_ids($node->condbranch);
            }
            foreach ($node->operands as $operand) {
                $this->assign_ids($operand);
            }
        }
    }

    protected function expand_quantifiers($node) {
        if (is_a($node, 'qtype_preg_operator')) {
            if ($node->type == qtype_preg_node::TYPE_NODE_COND_SUBEXPR && $node->condbranch !== null) {
                $node->condbranch = $this->expand_quantifiers($node->condbranch);
            }
            foreach ($node->operands as $key => $operand) {
                $node->operands[$key] = $this->expand_quantifiers($operand);
            }
        }
        if ($node->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT) {
            $this->max_finite_quant_borders_difference = max($this->max_finite_quant_borders_difference, $node->rightborder - $node->leftborder);
            if ($node->leftborder == 0 && $node->rightborder == 0) {
                // Convert x{0} to emptiness.
                $node = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            } else if ($node->rightborder > 1) {
                // Expand finite quantifier to a sequence like xxxxx?x?x?x?
                $concat = new qtype_preg_node_concat();
                for ($i = 0; $i < $node->rightborder; $i++) {
                    $operand = clone $node->operands[0];
                    if ($i >= $node->leftborder) {
                        $qu = new qtype_preg_node_finite_quant(0, 1);
                        $qu->operands[] = $operand;
                        $operand = $qu;
                    }
                    $concat->operands[] = $operand;
                }
                $node = $concat;
            }
        }
        if ($node->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT && $node->leftborder > 1) {
            // Expand finite quantifier to a sequence like xxxx+
            $concat = new qtype_preg_node_concat();
            for ($i = 0; $i < $node->leftborder; $i++) {
                $operand = clone $node->operands[0];
                if ($i == $node->leftborder - 1) {
                    $plus = new qtype_preg_node_infinite_quant(1);
                    $plus->operands[] = $operand;
                    $operand = $plus;
                }
                $concat->operands[] = $operand;
            }
            $node = $concat;
        }
        return $node;
    }

    protected static function is_alt_nullable($altnode) {
        foreach ($altnode->operands as $operand) {
            if ($operand->type == qtype_preg_node::TYPE_LEAF_META && $operand->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                return true;
            }
        }
        return false;
    }
}
%parse_failure {
    if (count($this->errornodes) === 0) {
        $this->create_error_node(qtype_preg_node_error::SUBTYPE_UNKNOWN_ERROR, null, -1, -1, new qtype_preg_userinscription());
    }
}
%nonassoc ERROR_PREC_SHORTEST.
%nonassoc ERROR_PREC_SHORT.
%nonassoc ERROR_PREC.
%nonassoc CLOSEBRACK.
%left ALT_SHORTEST.
%left ALT_SHORT.
%left ALT.
%left CONC PARSLEAF.
%nonassoc QUANT.
%nonassoc OPENBRACK CONDSUBEXPR.

start ::= expr(B). {
    // Set the root node.
    $this->root = B;

    // Assign subpattern numbers.
    $this->assign_subpatts($this->root);

    // Expand quantifiers if needed.
    if ($this->handlingoptions->expandquantifiers) {
        $this->root = $this->expand_quantifiers($this->root);
    }

    // Assign identifiers.
    $this->assign_ids($this->root);

    // Calculate nullable, firstpos, lastpos and followpos for all nodes.
    $this->root->calculate_nflf($this->followpos);
}

expr(A) ::= PARSLEAF(B). {
    A = B;
}

expr(A) ::= expr(B) expr(C). [CONC] {
    if (B->type == qtype_preg_node::TYPE_NODE_CONCAT && C->type == qtype_preg_node::TYPE_NODE_CONCAT) {
        B->operands = array_merge(B->operands, C->operands);
        A = B;
    } else if (B->type == qtype_preg_node::TYPE_NODE_CONCAT && C->type != qtype_preg_node::TYPE_NODE_CONCAT) {
        B->operands[] = C;
        A = B;
    } else if (B->type != qtype_preg_node::TYPE_NODE_CONCAT && C->type == qtype_preg_node::TYPE_NODE_CONCAT) {
        C->operands = array_merge(array(B), C->operands);
        A = C;
    } else {
        A = new qtype_preg_node_concat();
        A->operands[] = B;
        A->operands[] = C;
    }
    A->set_user_info(B->indfirst, C->indlast, new qtype_preg_userinscription());
}

expr(A) ::= expr(B) ALT expr(C). {
    if (B->type == qtype_preg_node::TYPE_NODE_ALT && C->type == qtype_preg_node::TYPE_NODE_ALT) {
        B->operands = array_merge(B->operands, C->operands);
        A = B;
    } else if (B->type == qtype_preg_node::TYPE_NODE_ALT && C->type != qtype_preg_node::TYPE_NODE_ALT) {
        B->operands[] = C;
        A = B;
    } else if (B->type != qtype_preg_node::TYPE_NODE_ALT && C->type == qtype_preg_node::TYPE_NODE_ALT) {
        C->operands = array_merge(array(B), C->operands);
        A = C;
    } else {
        A = new qtype_preg_node_alt();
        A->operands[] = B;
        A->operands[] = C;
    }
    A->set_user_info(B->indfirst, C->indlast, new qtype_preg_userinscription('|'));
}

expr(A) ::= expr(B) ALT. [ALT_SHORT] {
    if (B->type == qtype_preg_node::TYPE_LEAF_META && B->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
        A = B;
    } else if (B->type == qtype_preg_node::TYPE_NODE_ALT) {
        if (!self::is_alt_nullable(B)) {
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $epsleaf->set_user_info(B->indlast + 1, B->indlast + 1, new qtype_preg_userinscription('|'));
            B->operands[] = $epsleaf;
        }
        A = B;
    } else {
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $epsleaf->set_user_info(B->indlast + 1, B->indlast + 1, new qtype_preg_userinscription('|'));
        A = new qtype_preg_node_alt();
        A->operands[] = B;
        A->operands[] = $epsleaf;
    }
    A->set_user_info(B->indfirst, B->indlast + 1, new qtype_preg_userinscription('|'));
}

expr(A) ::= ALT expr(B). [ALT_SHORT] {
    if (B->type == qtype_preg_node::TYPE_LEAF_META && B->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
        A = B;
    } else if (B->type == qtype_preg_node::TYPE_NODE_ALT) {
        if (!self::is_alt_nullable(B)) {
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $epsleaf->set_user_info(B->indfirst - 1, B->indfirst - 1, new qtype_preg_userinscription('|'));
            B->operands = array_merge(array($epsleaf), B->operands);
        }
        A = B;
    } else {
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $epsleaf->set_user_info(B->indfirst - 1, B->indfirst - 1, new qtype_preg_userinscription('|'));
        A = new qtype_preg_node_alt();
        A->operands[] = $epsleaf;
        A->operands[] = B;
    }
    A->set_user_info(B->indfirst - 1, B->indlast, new qtype_preg_userinscription('|'));
}

expr(A) ::= ALT(B). [ALT_SHORTEST] {
    A = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    A->set_user_info(B->indfirst, B->indlast, new qtype_preg_userinscription('|'));
}

expr(A) ::= expr(B) QUANT(C). {
    A = C;
    A->set_user_info(B->indfirst, C->indlast, C->userinscription);
    A->operands[0] = B;
}

expr(A) ::= OPENBRACK(B) CLOSEBRACK. {
    $emptynode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    $emptynode->set_user_info(B->indfirst, B->indlast, new qtype_preg_userinscription(B->userinscription->data . ')'));
    A = $this->create_parens_node(B, $emptynode);
}

expr(A) ::= OPENBRACK(B) expr(C) CLOSEBRACK. {
    A = $this->create_parens_node(B, C);
}

expr(A) ::= CONDSUBEXPR(D) expr(B) CLOSEBRACK expr(C) CLOSEBRACK. {
    if (D->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA || D->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA ||
        D->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB || D->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB) {
        A = $this->create_cond_subexpr_assertion_node(D, B, C);
    } else {
        A = $this->create_cond_subexpr_other_node(D, C);
    }
}

expr(A) ::= CONDSUBEXPR(D) expr(B) CLOSEBRACK CLOSEBRACK. {
    if (D->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA || D->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA ||
        D->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB || D->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB) {
        A = $this->create_cond_subexpr_assertion_node(D, B, null);
    } else {
        A = $this->create_cond_subexpr_other_node(D, null);
    }
}

expr(A) ::= CONDSUBEXPR(D) CLOSEBRACK expr(C) CLOSEBRACK. {
    if (D->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA || D->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA ||
        D->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB || D->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB) {
        A = $this->create_cond_subexpr_assertion_node(D, null, C);
    } else {
        A = $this->create_cond_subexpr_other_node(D, C);
    }
}

expr(A) ::= CONDSUBEXPR(D) CLOSEBRACK CLOSEBRACK. {
    if (D->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA || D->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA ||
        D->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB || D->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB) {
        A = $this->create_cond_subexpr_assertion_node(D, null, null);
    } else {
        A = $this->create_cond_subexpr_other_node(D, null);
    }
}

/**************************************************
 * Below are the rules for errors reporting.     *
 **************************************************/


expr(A) ::= expr(B) CLOSEBRACK(C). [ERROR_PREC] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN, C->userinscription->data, B->indlast + 1, B->indlast + 1, C->userinscription, array(B));
}

expr(A) ::= CLOSEBRACK(B). [ERROR_PREC_SHORT] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN, B->userinscription->data, B->indfirst, B->indlast, B->userinscription);
}

expr(A) ::= OPENBRACK(B) expr(C). [ERROR_PREC] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, B->userinscription->data, B->indfirst, B->indlast, B->userinscription, array(C));
}

expr(A) ::= OPENBRACK(B). [ERROR_PREC_SHORT] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, B->userinscription->data, B->indfirst,  B->indlast, B->userinscription);
}

expr(A) ::= CONDSUBEXPR(B) expr(E) CLOSEBRACK(D) expr(C). [ERROR_PREC] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, B->userinscription->data, B->indfirst, B->indlast, B->userinscription, array(C, E));
}

expr(A) ::= CONDSUBEXPR(B) expr(C). [ERROR_PREC_SHORT] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, B->userinscription->data, B->indfirst, B->indlast, B->userinscription, array(C));
}

expr(A) ::= CONDSUBEXPR(B). [ERROR_PREC_SHORTEST] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, B->userinscription->data, B->indfirst, B->indlast, B->userinscription);
}

expr(A) ::= QUANT(B). [ERROR_PREC] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER, B->userinscription->data, B->indfirst,  B->indlast, B->userinscription);
}
