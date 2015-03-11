%name qtype_preg_
%include {
    global $CFG;
    require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
    require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
}
%declare_class {class qtype_preg_parser}
%include_class {
    // Root of the Abstract Syntax Tree (AST).
    private $root;

    // Objects of qtype_preg_node_error for errors found during parsing.
    private $errornodes;

    // Handling options.
    private $options;

    // Counter of nodes id. After parsing, equals the number of nodes in the tree.
    private $id_counter;

    // Counter of subpatterns.
    private $subpatt_counter;

    // Map subpattern number => subpattern node.
    private $subpatt_number_to_node_map;

    // Map subexpression number => nodes (possibly duplicates).
    private $subexpr_number_to_nodes_map;

    public function __construct($options = null) {
        $this->root = null;
        $this->errornodes = array();
        if ($options == null) {
            $options = new qtype_preg_handling_options();
        }
        $this->options = $options;
        $this->id_counter = 0;
        $this->subpatt_counter = 0;
        $this->subpatt_number_to_node_map = array();
        $this->subexpr_number_to_nodes_map = array();
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

    public function set_max_id($value) {
        $this->id_counter = $value;
    }

    public function get_max_subpatt() {
        return $this->subpatt_counter - 1;
    }

    public function get_subpatt_number_to_node_map() {
        return $this->subpatt_number_to_node_map;
    }

    public function get_subexpr_number_to_nodes_map() {
        return $this->subexpr_number_to_nodes_map;
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

    protected function add_cond_subexpr_negative_branch($node) {
        $shift = (int)$node->is_condition_assertion();

        // Add an eps leaf if there's only positive branch.
        if (count($node->operands) - $shift == 1) {
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $epsleaf->set_user_info($node->position->right()->add_chars_right(-1));
            $node->operands[] = $epsleaf;
        }
    }

    protected function create_cond_subexpr_assertion_node($node, $assertbody, $exprnode, $closeparen) {
        if ($node->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA) {
            $subtype = qtype_preg_node_assert::SUBTYPE_PLA;
        } else if ($node->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB) {
            $subtype = qtype_preg_node_assert::SUBTYPE_PLB;
        } else if ($node->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA) {
            $subtype = qtype_preg_node_assert::SUBTYPE_NLA;
        } else {
            $subtype = qtype_preg_node_assert::SUBTYPE_NLB;
        }

        if ($assertbody === null) {
            $assertbody = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $startpos = $node->position->indlast;
            $assertbody->set_user_info(new qtype_preg_position($startpos + 1, $startpos));
        }
        $condbranch = new qtype_preg_node_assert($subtype);
        $condbranch->operands = array($assertbody);
        $condbranch->set_user_info($node->position->compose($assertbody->position)->add_chars_left(2)->add_chars_right(1),
                                   array(new qtype_preg_userinscription(core_text::substr($node->userinscription[0], 2) . '...)')));

        $node->operands = array($condbranch);

        $position = $node->position->compose($closeparen->position);

        if ($exprnode === null) {
            $exprnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $exprnode->set_user_info($node->position->add_chars_left(1));
        }
        if ($exprnode->type == qtype_preg_node::TYPE_NODE_ALT) {
            $node->operands = array_merge($node->operands, $exprnode->operands);
            if (count($exprnode->operands) > 2) {
                // Error: only one or two top-level alternations allowed in a conditional subexpression.
                $node->errors[] = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_TOO_MUCH_ALTER, null, $position, array(), array($exprnode));
            }
        } else {
            $node->operands[] = $exprnode;
        }

        $this->add_cond_subexpr_negative_branch($node);

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
            if ($node->subtype == qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE || count($exprnode->operands) > 2) {
                // Error: only one or two top-level alternations allowed in a conditional subexpression.
                $node->errors[] = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_TOO_MUCH_ALTER, null, $position, array(), array($exprnode));
            }
        } else {
            $node->operands = array($exprnode);
        }

        $ui = implode('|', array_fill(0, count($node->operands), '...')) . ')';

        $this->add_cond_subexpr_negative_branch($node);

        $node->set_user_info($position, array(new qtype_preg_userinscription($node->userinscription[0] . $ui)));
        return $node;
    }

    protected function build_subexpr_number_to_nodes_map($node) {
        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $operand) {
                $this->build_subexpr_number_to_nodes_map($operand);
            }
        }
        if ($node->subtype == qtype_preg_node_subexpr::SUBTYPE_SUBEXPR) {
            if (!array_key_exists($node->number, $this->subexpr_number_to_nodes_map)) {
                $this->subexpr_number_to_nodes_map[$node->number] = array();
            }
            $this->subexpr_number_to_nodes_map[$node->number][] = $node;
        }
    }

    protected function detect_recursive_subexpr_calls($node, $currentsubexprs = array(0)) {
        if ($node->type == qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL) {
            $node->isrecursive = in_array($node->number, $currentsubexprs);
        }

        $newsubexprs = $currentsubexprs;
        if ($node->type == qtype_preg_node::TYPE_NODE_SUBEXPR) {
            if ($node->number != -1 && !in_array($node->number, $newsubexprs)) {
                $newsubexprs[] = $node->number;
            }
        }

        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $operand) {
                $this->detect_recursive_subexpr_calls($operand, $newsubexprs);
            }
        }
    }

    protected function replace_non_recursive_subexpr_calls($node) {
        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $key => $operand) {
                $node->operands[$key] = $this->replace_non_recursive_subexpr_calls($operand);
            }
        }

        if ($node->type == qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL && !$node->isrecursive && array_key_exists($node->number, $this->subexpr_number_to_nodes_map)) {

            // According to pcre.txt, we are able to replace the node.
            // Options affected the (?n) leaf do not affect the original node.
            // But we shoud treat the replacement node as an atomic group.
            $node = clone $this->subexpr_number_to_nodes_map[$node->number][0];
            $node->subtype = qtype_preg_node_subexpr::SUBTYPE_GROUPING;   // TODO: onceonly.
        }

        return $node;
    }

    protected function expand_quantifiers($node) {
        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $key => $operand) {
                $node->operands[$key] = $this->expand_quantifiers($operand);
            }
        }
        if ($node->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT) {
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

    protected function assign_subpatts($node) {
        if ($node->is_subpattern() || $node === $this->root) {
            $node->subpattern = $this->subpatt_counter++;
            $this->subpatt_number_to_node_map[$node->subpattern] = $node;
        }
        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $operand) {
                $this->assign_subpatts($operand);
            }
        }
    }

    protected function assign_ids($node) {
        $node->id = ++$this->id_counter;
        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $operand) {
                $this->assign_ids($operand);
            }
        }
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
        $this->create_error_node(qtype_preg_node_error::SUBTYPE_UNKNOWN_ERROR, null, new qtype_preg_position(), array());
    }
}
%nonassoc ERROR_PREC_SHORTEST.
%nonassoc ERROR_PREC_SHORT.
%nonassoc ERROR_PREC.
%nonassoc CLOSEBRACK TEMPLATECLOSEBRACK.
%left TEMPLATESEP_SHORTEST.
%left TEMPLATESEP_SHORT.
%left TEMPLATESEP.
%left ALT_SHORTEST.
%left ALT_SHORT.
%left ALT.
%left CONC PARSELEAF TEMPLATEPARSELEAF.
%nonassoc QUANT.
%nonassoc OPENBRACK TEMPLATEOPENBRACK CONDSUBEXPR.

start ::= expr(B). {
    // Set the root node.
    $this->root = B;

    // Build subexpr map.
    $this->build_subexpr_number_to_nodes_map($this->root);

    // Calculate recursive subexpression calls.
    $this->detect_recursive_subexpr_calls($this->root);

    // Replace non-recursive subexpression calls if needed.
    if ($this->options->replacesubexprcalls) {
        $this->root = $this->replace_non_recursive_subexpr_calls($this->root);
    }

    // Expand quantifiers if needed.
    if ($this->options->expandquantifiers) {
        $this->root = $this->expand_quantifiers($this->root);
    }

    // Assign subpattern numbers.
    $this->assign_subpatts($this->root);

     // Assign identifiers.
    $this->assign_ids($this->root);

    // Calculate nullable, firstpos, lastpos and followpos for all nodes.
    $followpos = array();   // TODO: make a field + getter if this is needed
    $this->root->calculate_nflf($followpos);
}

expr(A) ::= PARSELEAF(B). {
    A = B;
}

expr(A) ::= TEMPLATEPARSELEAF(B). {
    A = B;
    $available = qtype_preg\template::available_templates();
    if (array_key_exists(A->name, $available)) {
        $template = $available[A->name];
        if ($template->placeholderscount > 0) {
            // Template node called as a leaf
            $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_TEMPLATE_PARAMS_COUNT, A->name, A->position, A->userinscription, array());
        }
    }
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
        if ($this->options->preserveallnodes || !self::is_alt_nullable(B)) {
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
        if ($this->options->preserveallnodes || !self::is_alt_nullable(C)) {
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
    A->set_user_info(B->position->compose(C->position), B->userinscription);
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
    if (B->is_condition_assertion()) {
        A = $this->create_cond_subexpr_assertion_node(B, C, E, F);
    } else {
        A = $this->create_cond_subexpr_other_node(B, E, F);
    }
}

expr(A) ::= CONDSUBEXPR(B) expr(C) CLOSEBRACK(D) CLOSEBRACK(E). {
    if (B->is_condition_assertion()) {
        A = $this->create_cond_subexpr_assertion_node(B, C, null, E);
    } else {
        A = $this->create_cond_subexpr_other_node(B, null, E);
    }
}

expr(A) ::= CONDSUBEXPR(B) CLOSEBRACK(C) expr(D) CLOSEBRACK(E). {
    if (B->is_condition_assertion()) {
        A = $this->create_cond_subexpr_assertion_node(B, null, D, E);
    } else {
        A = $this->create_cond_subexpr_other_node(B, D, E);
    }
}

expr(A) ::= CONDSUBEXPR(B) CLOSEBRACK(C) CLOSEBRACK(D). {
    if (B->is_condition_assertion()) {
        A = $this->create_cond_subexpr_assertion_node(B, null, null, D);
    } else {
        A = $this->create_cond_subexpr_other_node(B, null, D);
    }
}

/**************************************************
 *    Below are the rules for templates parsing.  *
 **************************************************/

expr(A) ::= expr(B) TEMPLATESEP(C) expr(D). {
    $b = is_array(B) ? B : array(B);
    $d = is_array(D) ? D : array(D);
    A = array_merge($b, $d);
}

expr(A) ::= expr(B) TEMPLATESEP(C). [TEMPLATESEP_SHORT] {
    A = B;
}

expr(A) ::= TEMPLATESEP(B) expr(C). [TEMPLATESEP_SHORT] {
    A = C;
}

expr(A) ::= TEMPLATESEP(B). [TEMPLATESEP_SHORTEST] {
    A = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    A->set_user_info(B->position, B->userinscription);
}

expr(A) ::= TEMPLATEOPENBRACK(B) TEMPLATECLOSEBRACK(C). {
    $emptynode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    $emptynode->set_user_info(C->position->add_chars_right(-1), array_merge(B->userinscription, C->userinscription));
    $position = B->position->compose(C->position);
    A = B;
    A->operands = array($emptynode);
    A->position = $position;
    $available = qtype_preg\template::available_templates();
    if (array_key_exists(A->name, $available)) {
        $template = $available[A->name];
        if ($template->placeholderscount === 0) {
            // Template leaf called as a node
            $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_TEMPLATE_PARAMS_COUNT, A->name, $position, A->userinscription, array());
        }
    }
}

expr(A) ::= TEMPLATEOPENBRACK(B) expr(C) TEMPLATECLOSEBRACK(D). {
    $position = B->position->compose(D->position);
    A = B;
    A->operands = is_array(C) ? C : array(C);
    A->position = $position;
    $available = qtype_preg\template::available_templates();
    if (array_key_exists(A->name, $available)) {
        $template = $available[A->name];
        if ($template->placeholderscount !== count(A->operands)) {
            // Wrong number of parameters
            $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_TEMPLATE_PARAMS_COUNT, A->name, $position, A->userinscription, array());
        }
    }
}

/**************************************************
 *    Below are the rules for error reporting.    *
 **************************************************/


expr(A) ::= expr(B) CLOSEBRACK(C). [ERROR_PREC] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_OPEN_PAREN, C->userinscription[0]->data, C->position, C->userinscription, array(B));
}

expr(A) ::= expr(B) TEMPLATECLOSEBRACK(C). [ERROR_PREC] {
    $b = is_array(B) ? B : array(B);
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_OPEN_PAREN, C->userinscription[0]->data, C->position, C->userinscription, $b);
}

expr(A) ::= CLOSEBRACK(B). [ERROR_PREC_SHORT] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_OPEN_PAREN, B->userinscription[0]->data, B->position, B->userinscription);
}

expr(A) ::= TEMPLATECLOSEBRACK(B). [ERROR_PREC_SHORT] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_OPEN_PAREN, B->userinscription[0]->data, B->position, B->userinscription);
}

expr(A) ::= OPENBRACK(B) expr(C). [ERROR_PREC] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN, B->userinscription[0]->data, B->position, B->userinscription, array(C));
}

expr(A) ::= TEMPLATEOPENBRACK(B) expr(C). [ERROR_PREC] {
    $c = is_array(C) ? C : array(C);
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_CLOSE_PAREN, B->userinscription[0]->data, B->position, B->userinscription, $c);
}

expr(A) ::= OPENBRACK(B). [ERROR_PREC_SHORT] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN, B->userinscription[0]->data, B->position, B->userinscription);
}

expr(A) ::= TEMPLATEOPENBRACK(B). [ERROR_PREC_SHORT] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_CLOSE_PAREN, B->userinscription[0]->data, B->position, B->userinscription);
}

expr(A) ::= CONDSUBEXPR(B) expr(C) CLOSEBRACK(D) expr(E). [ERROR_PREC] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN, B->userinscription[0]->data, B->position, B->userinscription, array(E, C));
}

expr(A) ::= CONDSUBEXPR(B) expr(C). [ERROR_PREC_SHORT] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN, B->userinscription[0]->data, B->position, B->userinscription, array(C));
}

expr(A) ::= CONDSUBEXPR(B). [ERROR_PREC_SHORTEST] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN, B->userinscription[0]->data, B->position, B->userinscription);
}

expr(A) ::= QUANT(B). [ERROR_PREC] {
    A = $this->create_error_node(qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER, B->userinscription[0]->data, B->position, B->userinscription);
}
