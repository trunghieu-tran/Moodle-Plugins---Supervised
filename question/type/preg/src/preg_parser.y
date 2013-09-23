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

    public function errors_exist() {
        return (count($this->errornodes) > 0);
    }

    public function get_error_nodes() {
        return $this->errornodes;
    }

    public function get_max_id() {
        return $this->id_counter;
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
     */
    protected function create_error_node($subtype, $addinfo, $position, $userinscription, $operands = array()) {
        $newnode = new qtype_preg_node_error($subtype, $addinfo);
        $newnode->set_user_info($position, $userinscription);
        $newnode->operands = $operands;
        $this->errornodes[] = $newnode;
        return $newnode;
    }

    /**
      * Creates and return correct parenthesis node (subexpression, groping or assertion).
      *
      * Used to avoid code duplication between empty and non-empty parenthesis.
      * @param operator parenthesis token from lexer
      * @param operand the node for expression inside parenthesis
      */
    protected function create_parens_node($operator, $operand, $closeparen) {
        $position = $operator->position->compose($closeparen->position);
        $result = $operator;
        $result->operands[0] = $operand;
        $result->set_user_info($position, array(new qtype_preg_userinscription($operator->userinscription[0] . '...)')));
        return $result;
    }

    protected function create_cond_subexpr_assertion_node($node, $assertnode, $exprnode, $closeparen) {
        if ($assertnode === null) {
            $assertnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $assertnode->set_user_info($node->position->add_chars_right(-1));
        }
        if ($exprnode === null) {
            $exprnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $exprnode->set_user_info($node->position->add_chars_left(1));
        }

        $position = $node->position->compose($closeparen->position);

        if ($exprnode->type == qtype_preg_node::TYPE_NODE_ALT) {
            $node->operands = $exprnode->operands;
            if (count($exprnode->operands) > 2) {
                // Error: only one or two top-level alternations allowed in a conditional subexpression.
                $node->errors[] = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_TOO_MUCH_ALTER, null, $position, null, array($exprnode));
            }
        } else {
            $node->operands[0] = $exprnode;
        }

        if ($node->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA) {
            $subtype = qtype_preg_node_assert::SUBTYPE_PLA;
        } else if ($node->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB) {
            $subtype = qtype_preg_node_assert::SUBTYPE_PLB;
        } else if ($node->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA) {
            $subtype = qtype_preg_node_assert::SUBTYPE_NLA;
        } else {
            $subtype = qtype_preg_node_assert::SUBTYPE_NLB;
        }
        $node->condbranch = new qtype_preg_node_assert($subtype);
        $node->condbranch->operands[0] = $assertnode;
        $node->condbranch->userinscription = array(new qtype_preg_userinscription(textlib::substr($node->userinscription[0], 2) . '...)'));
        $node->set_user_info($position, array(new qtype_preg_userinscription($node->userinscription[0] . '...)...|...)')));
        return $node;
    }

    protected function create_cond_subexpr_other_node($node, $exprnode, $closeparen) {
        if ($exprnode === null) {
            $exprnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $exprnode->set_user_info($node->position->add_chars_left(1));
        }

        $position = $node->position->compose($closeparen->position);

        if ($exprnode->type == qtype_preg_node::TYPE_NODE_ALT) {
            $node->operands = $exprnode->operands;
            if (count($exprnode->operands) > 2) {
                // Error: only one or two top-level alternations allowed in a conditional subexpression.
                $node->errors[] = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_TOO_MUCH_ALTER, null, $position, null, array($exprnode));
            }
        } else {
            $node->operands[0] = $exprnode;
        }

        $node->set_user_info($position, array(new qtype_preg_userinscription($node->userinscription[0] . '...|...)')));
        return $node;
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
        $this->create_error_node(qtype_preg_node_error::SUBTYPE_UNKNOWN_ERROR, null, new qtype_preg_position(), null);
    }
}
%nonassoc ERROR_PREC_SHORTEST.
%nonassoc ERROR_PREC_SHORT.
%nonassoc ERROR_PREC.
%nonassoc CLOSEBRACK.
%left ALT_SHORTEST.
%left ALT_SHORT.
%left ALT.
%left CONC PARSELEAF.
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

expr(A) ::= PARSELEAF(B). {
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
    A->set_user_info(B->position->compose(C->position));
}

expr(A) ::= expr(B) ALT(C) expr(D). {
    if (B->type == qtype_preg_node::TYPE_NODE_ALT && D->type == qtype_preg_node::TYPE_NODE_ALT) {
        B->operands = array_merge(B->operands, D->operands);
        A = B;
    } else if (B->type == qtype_preg_node::TYPE_NODE_ALT && D->type != qtype_preg_node::TYPE_NODE_ALT) {
        B->operands[] = D;
        A = B;
    } else if (B->type != qtype_preg_node::TYPE_NODE_ALT && D->type == qtype_preg_node::TYPE_NODE_ALT) {
        D->operands = array_merge(array(B), D->operands);
        A = D;
    } else {
        A = new qtype_preg_node_alt();
        A->operands[] = B;
        A->operands[] = D;
    }
    A->set_user_info(B->position->compose(D->position), C->userinscription);
}

expr(A) ::= expr(B) ALT(C). [ALT_SHORT] {
    if (B->type == qtype_preg_node::TYPE_LEAF_META && B->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
        A = B;
    } else if (B->type == qtype_preg_node::TYPE_NODE_ALT) {
        if (!self::is_alt_nullable(B)) {
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $epsleaf->set_user_info(C->position->add_chars_left(1));
            B->operands[] = $epsleaf;
        }
        A = B;
    } else {
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $epsleaf->set_user_info(C->position->add_chars_left(1));
        A = new qtype_preg_node_alt();
        A->operands[] = B;
        A->operands[] = $epsleaf;
    }
    A->set_user_info(B->position->compose(C->position), C->userinscription);
}

expr(A) ::= ALT(B) expr(C). [ALT_SHORT] {
    if (C->type == qtype_preg_node::TYPE_LEAF_META && C->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
        A = C;
    } else if (C->type == qtype_preg_node::TYPE_NODE_ALT) {
        if (!self::is_alt_nullable(C)) {
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $epsleaf->set_user_info(B->position->add_chars_right(-1));
            C->operands = array_merge(array($epsleaf), C->operands);
        }
        A = C;
    } else {
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $epsleaf->set_user_info(B->position->add_chars_right(-1));
        A = new qtype_preg_node_alt();
        A->operands[] = $epsleaf;
        A->operands[] = C;
    }
    A->set_user_info(B->position->compose(C->position), C->userinscription);
}

expr(A) ::= ALT(B). [ALT_SHORTEST] {
    A = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    A->set_user_info(B->position, B->userinscription);
}

expr(A) ::= expr(B) QUANT(C). {
    A = C;
    A->set_user_info(B->position->compose(C->position), C->userinscription);
    A->operands[0] = B;
}

expr(A) ::= OPENBRACK(B) CLOSEBRACK(C). {
    $emptynode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    $emptynode->set_user_info(C->position->add_chars_right(-1), array_merge(B->userinscription, C->userinscription));
    A = $this->create_parens_node(B, $emptynode, C);
}

expr(A) ::= OPENBRACK(B) expr(C) CLOSEBRACK(D). {
    A = $this->create_parens_node(B, C, D);
}

expr(A) ::= CONDSUBEXPR(B) expr(C) CLOSEBRACK(D) expr(E) CLOSEBRACK(F). {
    if (B->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA || B->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA ||
        B->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB || B->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB) {
        A = $this->create_cond_subexpr_assertion_node(B, C, E, F);
    } else {
        A = $this->create_cond_subexpr_other_node(B, E, F);
    }
}

expr(A) ::= CONDSUBEXPR(B) expr(C) CLOSEBRACK(D) CLOSEBRACK(E). {
    if (B->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA || B->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA ||
        B->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB || B->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB) {
        A = $this->create_cond_subexpr_assertion_node(B, C, null, E);
    } else {
        A = $this->create_cond_subexpr_other_node(B, null, E);
    }
}

expr(A) ::= CONDSUBEXPR(B) CLOSEBRACK(C) expr(D) CLOSEBRACK(E). {
    if (B->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA || B->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA ||
        B->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB || B->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB) {
        A = $this->create_cond_subexpr_assertion_node(B, null, D, E);
    } else {
        A = $this->create_cond_subexpr_other_node(B, D, E);
    }
}

expr(A) ::= CONDSUBEXPR(B) CLOSEBRACK(C) CLOSEBRACK(D). {
    if (B->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA || B->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA ||
        B->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB || B->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB) {
        A = $this->create_cond_subexpr_assertion_node(B, null, null, D);
    } else {
        A = $this->create_cond_subexpr_other_node(B, null, D);
    }
}

/**************************************************
 *    Below are the rules for error reporting.    *
 **************************************************/


expr(A) ::= expr(B) CLOSEBRACK(C). [ERROR_PREC] {
    $position = new qtype_preg_position(C->position->indfirst, C->position->indlast,
                                        C->position->linefirst, C->position->linelast,
                                        C->position->colfirst, C->position->collast);
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_OPEN_PAREN, C->userinscription[0]->data, $position, C->userinscription[0], array(B));
}

expr(A) ::= CLOSEBRACK(B). [ERROR_PREC_SHORT] {
    $position = new qtype_preg_position(B->position->indfirst, B->position->indlast,
                                        B->position->linefirst, B->position->linelast,
                                        B->position->colfirst, B->position->collast);
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_OPEN_PAREN, B->userinscription[0]->data, $position, B->userinscription[0]);
}

expr(A) ::= OPENBRACK(B) expr(C). [ERROR_PREC] {
    $position = new qtype_preg_position(B->position->indfirst, B->position->indlast,
                                        B->position->linefirst, B->position->linelast,
                                        B->position->colfirst, B->position->collast);
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN, B->userinscription[0]->data, $position, B->userinscription[0], array(C));
}

expr(A) ::= OPENBRACK(B). [ERROR_PREC_SHORT] {
    $position = new qtype_preg_position(B->position->indfirst, B->position->indlast,
                                        B->position->linefirst, B->position->linelast,
                                        B->position->colfirst, B->position->collast);
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN, B->userinscription[0]->data, $position, B->userinscription[0]);
}

expr(A) ::= CONDSUBEXPR(B) expr(C) CLOSEBRACK(D) expr(E). [ERROR_PREC] {
    $position = new qtype_preg_position(B->position->indfirst, B->position->indlast,
                                        B->position->linefirst, B->position->linelast,
                                        B->position->colfirst, B->position->collast);
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN, B->userinscription[0]->data, $position, B->userinscription[0], array(E, C));
}

expr(A) ::= CONDSUBEXPR(B) expr(C). [ERROR_PREC_SHORT] {
    $position = new qtype_preg_position(B->position->indfirst, B->position->indlast,
                                        B->position->linefirst, B->position->linelast,
                                        B->position->colfirst, B->position->collast);
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN, B->userinscription[0]->data, $position, B->userinscription[0], array(C));
}

expr(A) ::= CONDSUBEXPR(B). [ERROR_PREC_SHORTEST] {
    $position = new qtype_preg_position(B->position->indfirst, B->position->indlast,
                                        B->position->linefirst, B->position->linelast,
                                        B->position->colfirst, B->position->collast);
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN, B->userinscription[0]->data, $position, B->userinscription[0]);
}

expr(A) ::= QUANT(B). [ERROR_PREC] {
    $position = new qtype_preg_position(B->position->indfirst, B->position->indlast,
                                        B->position->linefirst, B->position->linelast,
                                        B->position->colfirst, B->position->collast);
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER, B->userinscription[0]->data, $position, B->userinscription[0]);
}
