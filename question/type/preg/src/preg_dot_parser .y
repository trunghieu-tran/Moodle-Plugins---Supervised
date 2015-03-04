%name qtype_preg_dot_
%include {
    global $CFG;
}
%declare_class {class qtype_preg_dot_parser}
%include_class {
    //Finite automaton which will be build during parsing.
    private $automaton;

    public function get_automaton() {
        return $this->automaton;
    }
}

start ::= DIGRAPH NAME automaton_body CLOSEBODY.

automaton_body ::= STARTSTATES(A) ENDSTATES(B) transitions_list(C).
transitions_list(A) ::= transition_stmt(B).
transitions_list(A) ::= transition_stmt(B) transitions_list(C).
transition_stmt(A) ::= TRANSITIONSTATES transition_merged_list(B) main_transition(C) transition_merged_list(D).
transition_stmt(A) ::= TRANSITIONSTATES transition_merged_list(B) main_transition(C).
transition_stmt(A) ::= TRANSITIONSTATES main_transition(B) transition_merged_list(C).
transition_stmt(A) ::= TRANSITIONSTATES main_transition(B).
transition_merged_list(A) ::= merged_transition(B).
transition_merged_list(A) ::= merged_transition(B) transition_merged_list(C).
merged_transition(A) ::= transition_desc(B).
main_transition(A) ::= MAINSTART transition_desc(B) MAINEND.
transition_desc(A) ::= OPEN(B) LEAF(C) CLOSE(D).