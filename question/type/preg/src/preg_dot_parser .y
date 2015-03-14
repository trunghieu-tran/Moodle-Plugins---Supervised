%name qtype_preg_dot_
%include {
    global $CFG;
}
%declare_class {class qtype_preg_dot_parser}
%include_class {
    //Finite automaton which will be build during parsing.
    private $automaton = new qtype_preg_fa();;

    public function get_automaton() {
        return $this->automaton;
    }

    protected function get_transition($from, $main, $to, $type, $consumes, $mergedbefore = array(), $mergedafter = array()) {
        $main->type = $type;
        $main->consumeschars = $consumes;
        $main->from = $from;
        $main->to = $to;
        $main->mergedbefore = $mergedbefore;
        $main->mergedafter = $mergedafter;
        $main->redirect_merged_transitions();
        return $main;
    }
}

start ::= DIGRAPH NAME automaton_body CLOSEBODY.

automaton_body ::= STARTSTATES(A) ENDSTATES(B) transitions_list. {
    foreach (A as $start) {
        $this->automaton->add_start_state($start);
    }
    foreach (B as $end) {
        $this->automaton->add_end_state($end);
    }
}
transitions_list ::= transition_stmt(A).{
    $this->automaton->add_transition(A);
}
transitions_list ::= transition_stmt(A) transitions_list.{
    $this->automaton->add_transition(A);
}
transition_stmt(A) ::= TRANSITIONSTATES(B) transition_merged_list(C) main_transition(D) transition_merged_list(E) transition_params(F). {
    A = $this->get_transition(B[0], D, B[1], F['type'], F['consumes'], C, E);
}
transition_stmt(A) ::= TRANSITIONSTATES(B) transition_merged_list(C) main_transition(D) transition_params(E).{
    A = $this->get_transition(B[0], D, B[1], E['type'], E['consumes'], C);
}
transition_stmt(A) ::= TRANSITIONSTATES(B) main_transition(C) transition_merged_list(D) transition_params(E). {
    A = $this->get_transition(B[0], C, B[1], E['type'], E['consumes'], array(), D);
}
transition_stmt(A) ::= TRANSITIONSTATES(B) main_transition(C) transition_params(D). {
    A = $this->get_transition(B[0], C, B[1], D['type'], D['consumes']);
}
transition_merged_list(A) ::= merged_transition(B). {
    A = new array();
    A[] = B;
}
transition_merged_list(A) ::= merged_transition(B) transition_merged_list(C). {
    C[] = B;
    A = C;
}
transition_params(A) ::= transition_params_type(B). {
    A =  new array();
    A['type'] = B;
    A['consumes'] = false;
}
transition_params(A) ::= transition_params_type(B) COMMA STYLE EQUALS DOTTED. {
    A =  new array();
    A['type'] = B;
    A['consumes'] = true;
}
transition_params_type(A) ::= COMMA COLOR EQUALS VIOLET. {
    A = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
}
transition_params_type(A) ::= COMMA COLOR EQUALS BLUE. {
    A = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
}
transition_params_type(A) ::= COMMA COLOR EQUALS RED.{
    A = qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER;
}
merged_transition(A) ::= transition_desc(B). {
    A = B;
}
main_transition(A) ::= MAINSTART transition_desc(B) MAINEND. {
    A = B;
}
transition_desc(A) ::= OPEN(B) LEAF(C) CLOSE(D). {
    $pregleaf = C;
    A = new qtype_preg_fa_transition(0, $pregleaf, $to);
    foreach (B as $tag) {
        $open = new qtype_preg_fa_leaf_meta();
        $open->subpattern = $tag;
        A->opentags[] = $open;
    }
    foreach (D as $tag) {
        $close = new qtype_preg_fa_leaf_meta();
        $close->subpattern = $tag;
        A->closetags[] = $close;
    }
}