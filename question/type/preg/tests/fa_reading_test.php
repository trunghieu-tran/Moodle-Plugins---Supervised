<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/fa_matcher/fa_nodes.php');

class qtype_preg_fa_reading_test extends PHPUnit_Framework_TestCase {

    public function test_disclosure_tags() {
        $dotdescription = 'digraph {
        0;
        12;
        0->2[label = <<B>o:3,2,1, a c:3,</B>>];
    0->5[label = <<B>o:3,2,1, a c:3,</B><BR/>o:4, ε c:>,color = red];
    0->6[label = <<B>o:3,2,1, a c:3,</B><BR/>o:4, ε c:>,color = red];
    5->7[label = <o: ε c:4,2,1,<BR/><B>o: \\W c:</B>>, color = red, style = dotted];
    6->7[label = <o: ε c:4,2,1,<BR/><B>o: $ c:</B>>, color = red,style = dotted];
    11->12[label = <<B>o:7, c c:7,</B>>, color = violet];
    14->12[label = <o: ε c:6,<BR/><B>o:7, \\w ∩ c c:7,</B>>, color = red];
}';

        $expectedresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"12"[shape=doublecircle];
    0->6[label = <<B>o:3,2,1, a c:3,</B><BR/>o:4, ε c:(0,6)>, color = red, penwidth = 2];
    0->5[label = <<B>o:3,2,1, a c:3,</B><BR/>o:4, ε c:(0,5)>, color = red, penwidth = 2];
    0->2[label = <<B>o:3,2,1, a c:3,</B>>, color = violet, penwidth = 2];
    14->12[label = <o: ε c:6,(14,12)<BR/><B>o:7, \\\\w c:7,</B>>, color = red, penwidth = 2];
    11->12[label = <<B>o:7, c c:7,</B>>, color = violet, penwidth = 2];
    6->7[label = <o: ε c:4,2,1,(6,7)<BR/><B>o: $ c:</B>>, color = red, penwidth = 2, style = dotted];
    5->7[label = <o: ε c:4,2,1,(5,7)<BR/><B>o: \\\\W c:</B>>, color = red, penwidth = 2, style = dotted];
}';

        $automata = qtype_preg_fa::read_fa($dotdescription);

        $this->assertEquals($automata->fa_to_dot(null, null, true), $expectedresult, 'Result automata is not equal to expected');
    }

    public function test_loop() {
        $dotdescription = 'digraph {
                    0;
                    4;
                    0->1[label=<<B>o: [0-9] c:</B>>];
                    1->2[label=<<B>o: [abc] c:</B>>];
                    1->4[label=<<B>o: [01] c:</B>>];
                    2->2[label=<<B>o: [a-z] c:</B>>];
                    2->3[label=<<B>o: [-?,] c:</B>>];
                    3->4[label=<<B>o: [a] c:</B>>];
                    }';

        $expectedresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"4"[shape=doublecircle];
    0->1[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
    3->4[label = <<B>o: [a] c:</B>>, color = violet, penwidth = 2];
    2->3[label = <<B>o: [-?,] c:</B>>, color = violet, penwidth = 2];
    2->2[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
    1->4[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
    1->2[label = <<B>o: [abc] c:</B>>, color = violet, penwidth = 2];
}';

        $automata = qtype_preg_fa::read_fa($dotdescription);

        $this->assertEquals($automata->fa_to_dot(null, null, true), $expectedresult, 'Result automata is not equal to expected');
    }

    public function test_indirect_loop() {
        $dotdescription = 'digraph {
                    0;
                    4;
                    0->1[label=<<B>o: [a-c] c:</B>>];
                    1->2[label=<<B>o: [0-9] c:</B>>];
                    2->4[label=<<B>o: [a-f] c:</B>>];
                    0->3[label=<<B>o: [01] c:</B>>];
                    3->4[label=<<B>o: [y] c:</B>>];
                    4->0[label=<<B>o: [bc] c:</B>>];
                    }';

        $expectedresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"4"[shape=doublecircle];
    0->3[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
    0->1[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
    4->0[label = <<B>o: [bc] c:</B>>, color = violet, penwidth = 2];
    3->4[label = <<B>o: [y] c:</B>>, color = violet, penwidth = 2];
    2->4[label = <<B>o: [a-f] c:</B>>, color = violet, penwidth = 2];
    1->2[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
}';

        $automata = qtype_preg_fa::read_fa($dotdescription);

        $this->assertEquals($automata->fa_to_dot(null, null, true), $expectedresult, 'Result automata is not equal to expected');
    }

    public function test_hidden_characters() {
        $dotdescription = 'digraph {
                    0;
                    6;
                    0->1[label= <<B>o: [\\\\\\-] c:</B>>];
                    1->2[label= <<B>o: [\\$\\Z] c:</B>>];
                    2->3[label= <<B>o: [\\[\\]] c:</B>>];
                    3->4[label= <<B>o: [\\^\\A] c:</B>>];
                    4->5[label= <<B>o: [\\"\\/\\.] c:</B>>];
                    5->6[label= <<B>o: [\\(\\)] c:</B>>];
                    }';

        $expectedresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"6"[shape=doublecircle];
    0->1[label = <<B>o: [\\\\\\\\\\\\-] c:</B>>, color = violet, penwidth = 2];
    5->6[label = <<B>o: [\\\\(\\\\)] c:</B>>, color = violet, penwidth = 2];
    4->5[label = <<B>o: [\\\\\"\\\\/\\\\.] c:</B>>, color = violet, penwidth = 2];
    3->4[label = <<B>o: [\\\\^\\\\A] c:</B>>, color = violet, penwidth = 2];
    2->3[label = <<B>o: [\\\\[\\\\]] c:</B>>, color = violet, penwidth = 2];
    1->2[label = <<B>o: [\\\\$\\\\Z] c:</B>>, color = violet, penwidth = 2];
}';

        $automata = qtype_preg_fa::read_fa($dotdescription);

        $this->assertEquals($automata->fa_to_dot(null, null, true), $expectedresult, 'Result automata is not equal to expected');
    }

    public function test_several_endstates() {
        $dotdescription = 'digraph {
                    0;
                    1;2;4;
                    0->1[label=<<B>o: [a-c] c:</B>>];
                    1->2[label=<<B>o: [0-9] c:</B>>];
                    2->4[label=<<B>o: [a-f] c:</B>>];
                    0->3[label=<<B>o: [01] c:</B>>];
                    3->4[label=<<B>o: y c:</B>>];
                    4->0[label=<<B>o: [bc] c:</B>>];
                    }';

        $expectedresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"1"[shape=doublecircle];
"2"[shape=doublecircle];
"4"[shape=doublecircle];
    0->3[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
    0->1[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
    1->2[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
    2->4[label = <<B>o: [a-f] c:</B>>, color = violet, penwidth = 2];
    4->0[label = <<B>o: [bc] c:</B>>, color = violet, penwidth = 2];
    3->4[label = <<B>o: y c:</B>>, color = violet, penwidth = 2];
}';

        $automata = qtype_preg_fa::read_fa($dotdescription);

        $this->assertEquals($automata->fa_to_dot(null, null, true), $expectedresult, 'Result automata is not equal to expected');
    }

    public function test_character_ranges() {
        $dotdescription = 'digraph {
                    0;
                    3;
                    0->1[label=<<B>o: [a-kn-z] c:</B>>];
                    1->2[label=<<B>o: [a-jxy] c:</B>>];
                    2->3[label=<<B>o: [abc-hl-x] c:</B>>];
                    }';

       $expectedresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"3"[shape=doublecircle];
    0->1[label = <<B>o: [a-kn-z] c:</B>>, color = violet, penwidth = 2];
    2->3[label = <<B>o: [abc-hl-x] c:</B>>, color = violet, penwidth = 2];
    1->2[label = <<B>o: [a-jxy] c:</B>>, color = violet, penwidth = 2];
}';

        $automata = qtype_preg_fa::read_fa($dotdescription);

        $this->assertEquals($automata->fa_to_dot(null, null, true), $expectedresult, 'Result automata is not equal to expected');
    }

    public function test_asserts() {
        $dotdescription = 'digraph {
                    0;
                    3;
                    0->1[label= <<B>o: [0-9] c:</B>>];
                    1->2[label= <o: $ c:<BR/><B>o: z c:</B>>];
                    2->3[label= <<B>o: [a-z] c:</B><BR/>o: ^ c:>];
                    0->3[label= <o: \z c:<BR/><B>o: [xy] c:</B><BR/>o: \A c:>];
                    1->3[label= <<B>o: [\\\\A] c:</B>>];
                    }';

       $expectedresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"3"[shape=doublecircle];
    0->3[label = <o: \\\\z c:(0,3)<BR/><B>o: [xy] c:</B><BR/>o: \\\\A c:(0,3)>, color = violet, penwidth = 2, style = dotted];
    0->1[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
    1->3[label = <<B>o: [\\\\\\\\A] c:</B>>, color = violet, penwidth = 2];
    1->2[label = <o: $ c:(1,2)<BR/><B>o: z c:</B>>, color = violet, penwidth = 2];
    2->3[label = <<B>o: [a-z] c:</B><BR/>o: ^ c:(2,3)>, color = violet, penwidth = 2];
}';

        $automata = qtype_preg_fa::read_fa($dotdescription);

        $this->assertEquals($automata->fa_to_dot(null, null, true), $expectedresult, 'Result automata is not equal to expected');
    }


    public function test_different_automata(){
        $dotdescription = 'digraph {
                    "0,";
                    ",2";
                    "0,"->"1,0"[label=<<B>o: [a-z] c:</B>>,color=violet];
                    "1,0"->"2,1"[label=<<B>o: [0-9] c:</B>>,color=red];
                    "2,1"->",2"[label=<<B>o: [a-z] c:</B>>,color=blue];
                    }';
        $expectedresult = 'digraph {
    rankdir=LR;
    "0,"[shape=rarrow];
",2"[shape=doublecircle];
    "0,"->"1,0"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
    "2,1"->",2"[label = <<B>o: [a-z] c:</B>>, color = blue, penwidth = 2];
    "1,0"->"2,1"[label = <<B>o: [0-9] c:</B>>, color = red, penwidth = 2];
}';

        $automata = qtype_preg_fa::read_fa($dotdescription);

        $this->assertEquals($automata->fa_to_dot(null, null, true), $expectedresult, 'Result automata is not equal to expected');
    }


    public function test_eps_transition() {
        $dotdescription = 'digraph {
                    0;
                    3;
                    0->2[label=<<B>o: ε c:</B>>];
                    0->1[label=<<B>o: [0-9] c:</B>>];
                    1->3[label=<<B>o: ε c:</B>>];
                    2->3[label=<<B>o: [a-z] c:</B>>];
                    }';
        $expectedresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"3"[shape=doublecircle];
    0->1[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
    0->2[label = <<B>o: ε c:</B>>, color = violet, penwidth = 2];
    2->3[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
    1->3[label = <<B>o: ε c:</B>>, color = violet, penwidth = 2];
}';

        $automata = qtype_preg_fa::read_fa($dotdescription);

        $this->assertEquals($automata->fa_to_dot(null, null, true), $expectedresult, 'Result automata is not equal to expected');
    }

    public function test_imposition_transitions() {
        $dotdescription = 'digraph {
                    0;
                    3;
                    0->2[label=<<B>o: . c:</B>>];
                    0->1[label=<<B>o: [0-9] c:</B>>];
                    1->3[label=<<B>o: [.] c:</B>>];
                    2->3[label=<<B>o: [a-z] c:</B>>];
                    }';
        $expectedresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"3"[shape=doublecircle];
    0->1[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
    0->2[label = <<B>o: . c:</B>>, color = violet, penwidth = 2];
    2->3[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
    1->3[label = <<B>o: [.] c:</B>>, color = violet, penwidth = 2];
}';

        $automata = qtype_preg_fa::read_fa($dotdescription);

        $this->assertEquals($automata->fa_to_dot(null, null, true), $expectedresult, 'Result automata is not equal to expected');
    }
}
