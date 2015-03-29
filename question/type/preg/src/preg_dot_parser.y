%name qtype_preg_dot_
%include {
    global $CFG;
	require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
}
%declare_class {class qtype_preg_dot_parser}
%include_class {
    //Finite automaton which will be build during parsing.
    private $automaton;

	public function __construct() {
		$this->automaton = new qtype_preg_fa();
	}
    public function get_automaton() {
        return $this->automaton;
    }

    protected function get_transition($from, $main, $to, $type, $consumes, $mergedbefore = array(), $mergedafter = array()) {
        $main->origin = $type;
        $main->consumeschars = $consumes;
        $main->from = $from;
        $main->to = $to;
        $main->mergedbefore = $mergedbefore;
        $main->mergedafter = $mergedafter;
        $main->redirect_merged_transitions();
        return $main;
    }
}

start ::= DIGRAPH automaton_body CLOSEBODY.

automaton_body ::= start_end_states_description.
automaton_body ::= start_end_states_description transitions_list.


start_end_states_description ::= START(A) END(B). {
    foreach (A as $start) {
        $this->automaton->add_start_state($this->automaton->add_state($start));
    }
    foreach (B as $end) {
        $this->automaton->add_end_state($this->automaton->add_state($end));
    }
}

transitions_list ::= transition_stmt(A).{
	$fromid = $this->automaton->add_state(A->from);
	$toid = $this->automaton->add_state(A->to);
	A->from = $fromid;
	A->to = $toid;
    	$this->automaton->add_transition(A);
}

transitions_list ::= transition_stmt(A) transitions_list.{
	$fromid = $this->automaton->add_state(A->from);
	$toid = $this->automaton->add_state(A->to);
	A->from = $fromid;
	A->to = $toid;
    $this->automaton->add_transition(A);

}
transition_stmt(A) ::= TRANSITIONSTATES(B) transition_merged_list(C) main_transition(D) transition_merged_list(E). {
    A = $this->get_transition(B[0], D, B[1], qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST, false, C, E);
}
transition_stmt(A) ::= TRANSITIONSTATES(B) transition_merged_list(C) main_transition(D).{
    A = $this->get_transition(B[0], D, B[1], qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST, true, C);
}
transition_stmt(A) ::= TRANSITIONSTATES(B) main_transition(C) transition_merged_list(D). {
    A = $this->get_transition(B[0], C, B[1], qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST, true, array(), D);
}
transition_stmt(A) ::= TRANSITIONSTATES(B) main_transition(C). {
    A = $this->get_transition(B[0], C, B[1], qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST, true);
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
    A = array();
    A[] = B;
}
transition_merged_list(A) ::= merged_transition(B) transition_merged_list(C). {
    C[] = B;
    A = C;
}
transition_params(A) ::= transition_params_type(B). {
    A =  array();
    A['type'] = B;
    A['consumes'] = true;
}
transition_params(A) ::= transition_params_type(B) COMMA STYLE EQUALS DOTTED. {
    A =  array();
    A['type'] = B;
    A['consumes'] = false;
}
transition_params_type(A) ::= COLOR EQUALS VIOLET. {
    A = qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST;
}
transition_params_type(A) ::= COLOR EQUALS BLUE. {
    A = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
}
transition_params_type(A) ::= COLOR EQUALS RED.{
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
    A = new qtype_preg_fa_transition(0, $pregleaf, 1);
    foreach (B as $tag) {
        $open = new qtype_preg_leaf_meta();
        $open->subpattern = $tag;
        A->opentags[] = $open;
    }
    foreach (D as $tag) {
        $close = new qtype_preg_leaf_meta();
        $close->subpattern = $tag;
        A->closetags[] = $close;
    }
}
