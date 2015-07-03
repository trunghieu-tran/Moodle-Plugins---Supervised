<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');
require_once($CFG->dirroot . '/question/type/preg/fa_matcher/fa_nodes.php');
require_once($CFG->dirroot . '/question/type/poasquestion/stringstream/stringstream.php');
require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/fa_matcher/fa_matcher.php');


/*class qtype_preg_fa_intersection_test extends PHPUnit_Framework_TestCase {
    // --------------------- Merge wordbreaks tests ------------------------

    public function test_word_starts() {
        $regex = '\t\bc';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "5"[shape=doublecircle];
                            0->7[label = <<B>o:1,0, \\\\t ∩ \\\\W c:1,</B><BR/>o:2, ε c:(0,7)>, color = red, penwidth = 2];
                            7->5[label = <o: ε c:2,(7,5)<BR/><B>o:3, \\\\w ∩ c c:3,0,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_word_ends() {
        $regex = 'c\b\t';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "5"[shape=doublecircle];
                            0->7[label = <<B>o:1,0, c ∩ \\\\w c:1,</B><BR/>o:2, ε c:(0,7)>, color = red, penwidth = 2];
                            7->5[label = <o: ε c:2,(7,5)<BR/><B>o:3, \\\\W ∩ \\\\t c:3,0,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_wordbreak_into_word() {
        $regex = 'a\bc';
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        } else {
            $this->assertTrue(true, "fa merging wordbreaks failed\n");
        }
    }

    public function test_word_starts_string() {
        $regex = '\bcat';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "8"[shape=rarrow];
                        "7"[shape=doublecircle];
                            4->6[label = <<B>o:3, a c:3,</B>>, color = violet, penwidth = 2];
                            6->7[label = <<B>o:4, t c:4,0,</B>>, color = violet, penwidth = 2];
                            8->9[label = <<B>o: \\\\W c:</B><BR/>o:1,0, ε c:(8,9)>, color = red, penwidth = 2, style = dotted];
                            8->10[label = <<B>o: ^ c:</B><BR/>o:1,0, ε c:(8,10)>, color = red, penwidth = 2, style = dotted];
                            9->4[label = <o: ε c:1,(9,4)<BR/><B>o:2, \\\\w ∩ c c:2,</B>>, color = red, penwidth = 2];
                            10->4[label = <o: ε c:1,(10,4)<BR/><B>o:2, \\\\w ∩ c c:2,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_word_ends_string() {
        $regex = 'cat\b';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "8"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, c c:1,</B>>, color = violet, penwidth = 2];
                            2->4[label = <<B>o:2, a c:2,</B>>, color = violet, penwidth = 2];
                            4->9[label = <<B>o:3, t ∩ \\\\w c:3,</B><BR/>o:4, ε c:(4,9)>, color = red, penwidth = 2];
                            4->10[label = <<B>o:3, t ∩ \\\\w c:3,</B><BR/>o:4, ε c:(4,10)>, color = red, penwidth = 2];
                            9->8[label = <o: ε c:4,0,(9,8)<BR/><B>o: \\\\W c:</B>>, color = red, penwidth = 2, style = dotted];
                            10->8[label = <o: ε c:4,0,(10,8)<BR/><B>o: $ c:</B>>, color = red, penwidth = 2, style = dotted];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_wordbreak_not_in_wordboundary_start() {
        $regex = '\b\tcat';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "10"[shape=rarrow];
                        "9"[shape=doublecircle];
                            4->6[label = <<B>o:3, c c:3,</B>>, color = violet, penwidth = 2];
                            6->8[label = <<B>o:4, a c:4,</B>>, color = violet, penwidth = 2];
                            8->9[label = <<B>o:5, t c:5,0,</B>>, color = violet, penwidth = 2];
                            10->11[label = <<B>o: \\\\w c:</B><BR/>o:1,0, ε c:(10,11)>, color = red, penwidth = 2, style = dotted];
                            11->4[label = <o: ε c:1,(11,4)<BR/><B>o:2, \\\\W ∩ \\\\t c:2,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_wordbreak_not_in_wordboundary_end() {
        $regex = 'cat\t\b';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "10"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, c c:1,</B>>, color = violet, penwidth = 2];
                            2->4[label = <<B>o:2, a c:2,</B>>, color = violet, penwidth = 2];
                            4->6[label = <<B>o:3, t c:3,</B>>, color = violet, penwidth = 2];
                            6->11[label = <<B>o:4, \\\\t ∩ \\\\W c:4,</B><BR/>o:5, ε c:(6,11)>, color = red, penwidth = 2];
                            11->10[label = <o: ε c:5,0,(11,10)<BR/><B>o: \\\\w c:</B>>, color = red, penwidth = 2, style = dotted];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_word_no_start() {
        $regex = '\t\Bc';

        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        } else {
            $this->assertTrue(true, "fa merging wordbreaks failed\n");
        }
    }

    public function test_word_no_end() {
        $regex = 'c\B\t';

        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        } else {
            $this->assertTrue(true, "fa merging wordbreaks failed\n");
        }
    }

    public function test_no_wordbreak_into_word() {
        $regex = 'a\Bc';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "5"[shape=doublecircle];
                            0->7[label = <<B>o:1,0, a ∩ \\\\w c:1,</B><BR/>o:2, ε c:(0,7)>, color = red, penwidth = 2];
                            7->5[label = <o: ε c:2,(7,5)<BR/><B>o:3, \\\\w ∩ c c:3,0,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_word_no_start_string() {
        $regex = '\Bcat';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "8"[shape=rarrow];
                        "7"[shape=doublecircle];
                            4->6[label = <<B>o:3, a c:3,</B>>, color = violet, penwidth = 2];
                            6->7[label = <<B>o:4, t c:4,0,</B>>, color = violet, penwidth = 2];
                            8->9[label = <<B>o: \\\\w c:</B><BR/>o:1,0, ε c:(8,9)>, color = red, penwidth = 2, style = dotted];
                            9->4[label = <o: ε c:1,(9,4)<BR/><B>o:2, \\\\w ∩ c c:2,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_word_no_end_string() {
        $regex = 'cat\B';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "8"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, c c:1,</B>>, color = violet, penwidth = 2];
                            2->4[label = <<B>o:2, a c:2,</B>>, color = violet, penwidth = 2];
                            4->9[label = <<B>o:3, t ∩ \\\\w c:3,</B><BR/>o:4, ε c:(4,9)>, color = red, penwidth = 2];
                            9->8[label = <o: ε c:4,0,(9,8)<BR/><B>o: \\\\w c:</B>>, color = red, penwidth = 2, style = dotted];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_no_wordbreak_not_in_wordboundary_start() {
        $regex = '\B\tcat';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "10"[shape=rarrow];
                        "9"[shape=doublecircle];
                            4->6[label = <<B>o:3, c c:3,</B>>, color = violet, penwidth = 2];
                            6->8[label = <<B>o:4, a c:4,</B>>, color = violet, penwidth = 2];
                            8->9[label = <<B>o:5, t c:5,0,</B>>, color = violet, penwidth = 2];
                            10->11[label = <<B>o: \\\\W c:</B><BR/>o:1,0, ε c:(10,11)>, color = red, penwidth = 2, style = dotted];
                            10->12[label = <<B>o: ^ c:</B><BR/>o:1,0, ε c:(10,12)>, color = red, penwidth = 2, style = dotted];
                            11->4[label = <o: ε c:1,(11,4)<BR/><B>o:2, \\\\W ∩ \\\\t c:2,</B>>, color = red, penwidth = 2];
                            12->4[label = <o: ε c:1,(12,4)<BR/><B>o:2, \\\\W ∩ \\\\t c:2,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_no_wordbreak_not_in_wordboundary_end() {
        $regex = 'cat\t\B';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "10"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, c c:1,</B>>, color = violet, penwidth = 2];
                            2->4[label = <<B>o:2, a c:2,</B>>, color = violet, penwidth = 2];
                            4->6[label = <<B>o:3, t c:3,</B>>, color = violet, penwidth = 2];
                            6->11[label = <<B>o:4, \\\\t ∩ \\\\W c:4,</B><BR/>o:5, ε c:(6,11)>, color = red, penwidth = 2];
                            6->12[label = <<B>o:4, \\\\t ∩ \\\\W c:4,</B><BR/>o:5, ε c:(6,12)>, color = red, penwidth = 2];
                            11->10[label = <o: ε c:5,0,(11,10)<BR/><B>o: \\\\W c:</B>>, color = red, penwidth = 2, style = dotted];
                            12->10[label = <o: ε c:5,0,(12,10)<BR/><B>o: $ c:</B>>, color = red, penwidth = 2, style = dotted];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_several_wordbreaks() {
        $regex = '^\bcat\b$';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "13"[shape=doublecircle];
                            0->18[label = <<B>o:1,0, \\\\A c:1,</B><BR/>o:2, ε c:(0,18)>, color = red, penwidth = 2];
                            6->8[label = <<B>o:4, a c:4,</B>>, color = violet, penwidth = 2];
                            8->15[label = <<B>o:5, t ∩ \\\\w c:5,</B><BR/>o:6, ε c:(8,15)>, color = red, penwidth = 2];
                            15->13[label = <o: ε c:6,(15,13)<BR/><B>o:7, $ c:7,0,</B>>, color = red, penwidth = 2];
                            18->6[label = <o: ε c:2,(18,6)<BR/><B>o:3, \\\\w ∩ c c:3,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_several_not_wordbreaks() {
        $regex = '^c\Ba\Bt$';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "13"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, \\\\A c:1,</B>>, color = violet, penwidth = 2];
                            2->17[label = <<B>o:2, c ∩ \\\\w c:2,</B><BR/>o:3, ε c:(2,17)>, color = red, penwidth = 2];
                            12->13[label = <<B>o:7, \\\\Z c:7,0,</B>>, color = violet, penwidth = 2];
                            15->12[label = <o: ε c:5,(15,12)<BR/><B>o:6, \\\\w ∩ t c:6,</B>>, color = red, penwidth = 2];
                            17->15[label = <o: ε c:3,(17,15)<BR/><B>o:4, a ∩ \\\\w c:4,</B><BR/>o:5, ε c:(17,15)>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_wordbreak_and_not_wordbreak() {
        $regex = '^\bc\Ba\Bt\b$';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "17"[shape=doublecircle];
                            0->26[label = <<B>o:1,0, \\\\A c:1,</B><BR/>o:2, ε c:(0,26)>, color = red, penwidth = 2];
                            19->17[label = <o: ε c:8,(19,17)<BR/><B>o:9, $ c:9,0,</B>>, color = red, penwidth = 2];
                            21->19[label = <o: ε c:6,(21,19)<BR/><B>o:7, t ∩ \\\\w c:7,</B><BR/>o:8, ε c:(21,19)>, color = red, penwidth = 2];
                            23->21[label = <o: ε c:4,(23,21)<BR/><B>o:5, a ∩ \\\\w c:5,</B><BR/>o:6, ε c:(23,21)>, color = red, penwidth = 2];
                            26->23[label = <o: ε c:2,(26,23)<BR/><B>o:3, c ∩ \\\\w c:3,</B><BR/>o:4, ε c:(26,23)>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_wordbreak_and_not_wordbreak_no_success() {
        $regex = '^\Bc\Ba\Bt\b$';
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        } else {
            $this->assertTrue(true, "fa merging wordbreaks failed\n");
        }
    }

    public function test_two_lines() {
        $regex = '[a!?]\b[c+]';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "5"[shape=doublecircle];
                            0->7[label = <<B>o:1,0, [a!?] ∩ \\\\w c:1,</B><BR/>o:2, ε c:(0,7)>, color = red, penwidth = 2];
                            0->8[label = <<B>o:1,0, [a!?] ∩ \\\\W c:1,</B><BR/>o:2, ε c:(0,8)>, color = red, penwidth = 2];
                            7->5[label = <o: ε c:2,(7,5)<BR/><B>o:3, \\\\W ∩ [c+] c:3,0,</B>>, color = red, penwidth = 2];
                            8->5[label = <o: ε c:2,(8,5)<BR/><B>o:3, \\\\w ∩ [c+] c:3,0,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_two_lines_2() {
        $regex = '([a!?]|)\b([c+]|)';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "7"[shape=doublecircle];
                            0->11[label = <<B>o: \\\\w c:</B><BR/>o:3,1, ε c:3,1,(0,11)<BR/>o:4,0, ε c:(0,11)>, color = red, penwidth = 2, style = dotted];
                            0->11[label = <<B>o:2,1,0, [a!?] ∩ \\\\w c:2,1,</B><BR/>o:4, ε c:(0,11)>, color = red, penwidth = 2];
                            0->12[label = <<B>o: \\\\w c:</B><BR/>o:3,1, ε c:3,1,(0,12)<BR/>o:4,0, ε c:(0,12)>, color = red, penwidth = 2, style = dotted];
                            0->12[label = <<B>o:2,1,0, [a!?] ∩ \\\\w c:2,1,</B><BR/>o:4, ε c:(0,12)>, color = red, penwidth = 2];
                            0->13[label = <<B>o: \\\\W c:</B><BR/>o:3,1, ε c:3,1,(0,13)<BR/>o:4,0, ε c:(0,13)>, color = red, penwidth = 2, style = dotted];
                            0->13[label = <<B>o:2,1,0, [a!?] ∩ \\\\W c:2,1,</B><BR/>o:4, ε c:(0,13)>, color = red, penwidth = 2];
                            0->14[label = <<B>o: \\\\W c:</B><BR/>o:3,1, ε c:3,1,(0,14)<BR/>o:4,0, ε c:(0,14)>, color = red, penwidth = 2, style = dotted];
                            0->14[label = <<B>o:2,1,0, [a!?] ∩ \\\\W c:2,1,</B><BR/>o:4, ε c:(0,14)>, color = red, penwidth = 2];
                            0->15[label = <<B>o: ^ c:</B><BR/>o:3,1, ε c:3,1,(0,15)<BR/>o:4,0, ε c:(0,15)>, color = red, penwidth = 2, style = dotted];
                            0->16[label = <<B>o: ^ c:</B><BR/>o:3,1, ε c:3,1,(0,16)<BR/>o:4,0, ε c:(0,16)>, color = red, penwidth = 2, style = dotted];
                            0->17[label = <<B>o: \\\\w c:</B><BR/>o:3,1, ε c:3,1,(0,17)<BR/>o:4,0, ε c:(0,17)>, color = red, penwidth = 2, style = dotted];
                            0->17[label = <<B>o:2,1,0, [a!?] ∩ \\\\w c:2,1,</B><BR/>o:4, ε c:(0,17)>, color = red, penwidth = 2];
                            11->7[label = <o: ε c:4,(11,7)<BR/><B>o:6,5, \\\\W ∩ [c+] c:6,5,0,</B>>, color = red, penwidth = 2];
                            12->7[label = <o: ε c:4,(12,7)<BR/>o:7,5, ε c:7,5,0,(12,7)<BR/><B>o: \\\\W c:</B>>, color = red, penwidth = 2, style = dotted];
                            13->7[label = <o: ε c:4,(13,7)<BR/><B>o:6,5, \\\\w ∩ [c+] c:6,5,0,</B>>, color = red, penwidth = 2];
                            14->7[label = <o: ε c:4,(14,7)<BR/>o:7,5, ε c:7,5,0,(14,7)<BR/><B>o: \\\\w c:</B>>, color = red, penwidth = 2, style = dotted];
                            15->7[label = <o: ε c:4,(15,7)<BR/><B>o:6,5, \\\\w ∩ [c+] c:6,5,0,</B>>, color = red, penwidth = 2];
                            16->7[label = <o: ε c:4,(16,7)<BR/>o:7,5, ε c:7,5,0,(16,7)<BR/><B>o: \\\\w c:</B>>, color = red, penwidth = 2, style = dotted];
                            17->7[label = <o: ε c:4,(17,7)<BR/>o:7,5, ε c:7,5,0,(17,7)<BR/><B>o: $ c:</B>>, color = red, penwidth = 2, style = dotted];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_two_lines_3() {
        $regex = '[a!&]\b[b?+]\b[c*//*]\b(d|&)';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "13"[shape=doublecircle];
                            0->25[label = <<B>o:1,0, [a!&] ∩ \\\\w c:1,</B><BR/>o:2, ε c:(0,25)>, color = red, penwidth = 2];
                            0->26[label = <<B>o:1,0, [a!&] ∩ \\\\W c:1,</B><BR/>o:2, ε c:(0,26)>, color = red, penwidth = 2];
                            17->13[label = <o: ε c:6,(17,13)<BR/><B>o:9,7, \\\\W ∩ & c:9,7,0,</B>>, color = red, penwidth = 2];
                            18->13[label = <o: ε c:6,(18,13)<BR/><B>o:8,7, \\\\w ∩ d c:8,7,0,</B>>, color = red, penwidth = 2];
                            21->18[label = <o: ε c:4,(21,18)<BR/><B>o:5, [c*//*] ∩ \\\\W c:5,</B><BR/>o:6, ε c:(21,18)>, color = red, penwidth = 2];
                            22->17[label = <o: ε c:4,(22,17)<BR/><B>o:5, [c*//*] ∩ \\\\w c:5,</B><BR/>o:6, ε c:(22,17)>, color = red, penwidth = 2];
                            25->22[label = <o: ε c:2,(25,22)<BR/><B>o:3, [b?+] ∩ \\\\W c:3,</B><BR/>o:4, ε c:(25,22)>, color = red, penwidth = 2];
                            26->21[label = <o: ε c:2,(26,21)<BR/><B>o:3, [b?+] ∩ \\\\w c:3,</B><BR/>o:4, ε c:(26,21)>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_two_lines_4() {
        $regex = '[a!&]\b[b?+]\b[c*//*]\bd';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "13"[shape=doublecircle];
                            0->20[label = <<B>o:1,0, [a!&] ∩ \\\\W c:1,</B><BR/>o:2, ε c:(0,20)>, color = red, penwidth = 2];
                            15->13[label = <o: ε c:6,(15,13)<BR/><B>o:7, \\\\w ∩ d c:7,0,</B>>, color = red, penwidth = 2];
                            18->15[label = <o: ε c:4,(18,15)<BR/><B>o:5, [c*//*] ∩ \\\\W c:5,</B><BR/>o:6, ε c:(18,15)>, color = red, penwidth = 2];
                            20->18[label = <o: ε c:2,(20,18)<BR/><B>o:3, [b?+] ∩ \\\\w c:3,</B><BR/>o:4, ε c:(20,18)>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_two_lines_5() {
        $regex = '[a!&]\b[b?+]\b[c*//*]\b&';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "13"[shape=doublecircle];
                            0->20[label = <<B>o:1,0, [a!&] ∩ \\\\w c:1,</B><BR/>o:2, ε c:(0,20)>, color = red, penwidth = 2];
                            15->13[label = <o: ε c:6,(15,13)<BR/><B>o:7, \\\\W ∩ & c:7,0,</B>>, color = red, penwidth = 2];
                            17->15[label = <o: ε c:4,(17,15)<BR/><B>o:5, [c*//*] ∩ \\\\w c:5,</B><BR/>o:6, ε c:(17,15)>, color = red, penwidth = 2];
                            20->17[label = <o: ε c:2,(20,17)<BR/><B>o:3, [b?+] ∩ \\\\W c:3,</B><BR/>o:4, ε c:(20,17)>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_two_lines_start_b() {
        $regex = '\b[a!&]\b[b?+]\b[c*//*]\b(d|&)';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "30"[shape=rarrow];
                        "15"[shape=doublecircle];
                            19->15[label = <o: ε c:7,(19,15)<BR/><B>o:10,8, \\\\W ∩ & c:10,8,0,</B>>, color = red, penwidth = 2];
                            20->15[label = <o: ε c:7,(20,15)<BR/><B>o:9,8, \\\\w ∩ d c:9,8,0,</B>>, color = red, penwidth = 2];
                            23->20[label = <o: ε c:5,(23,20)<BR/><B>o:6, [c*//*] ∩ \\\\W c:6,</B><BR/>o:7, ε c:(23,20)>, color = red, penwidth = 2];
                            24->19[label = <o: ε c:5,(24,19)<BR/><B>o:6, [c*//*] ∩ \\\\w c:6,</B><BR/>o:7, ε c:(24,19)>, color = red, penwidth = 2];
                            27->24[label = <o: ε c:3,(27,24)<BR/><B>o:4, [b?+] ∩ \\\\W c:4,</B><BR/>o:5, ε c:(27,24)>, color = red, penwidth = 2];
                            28->23[label = <o: ε c:3,(28,23)<BR/><B>o:4, [b?+] ∩ \\\\w c:4,</B><BR/>o:5, ε c:(28,23)>, color = red, penwidth = 2];
                            30->31[label = <<B>o: \\\\w c:</B><BR/>o:1,0, ε c:(30,31)>, color = red, penwidth = 2, style = dotted];
                            30->32[label = <<B>o: \\\\W c:</B><BR/>o:1,0, ε c:(30,32)>, color = red, penwidth = 2, style = dotted];
                            30->33[label = <<B>o: ^ c:</B><BR/>o:1,0, ε c:(30,33)>, color = red, penwidth = 2, style = dotted];
                            31->28[label = <o: ε c:1,(31,28)<BR/><B>o:2, [a!&] ∩ \\\\W c:2,</B><BR/>o:3, ε c:(31,28)>, color = red, penwidth = 2];
                            32->27[label = <o: ε c:1,(32,27)<BR/><B>o:2, [a!&] ∩ \\\\w c:2,</B><BR/>o:3, ε c:(32,27)>, color = red, penwidth = 2];
                            33->27[label = <o: ε c:1,(33,27)<BR/><B>o:2, [a!&] ∩ \\\\w c:2,</B><BR/>o:3, ε c:(33,27)>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_divarication_character_classes() {
        $regex = '[c!](at|\b[a\t])$';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "15"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, [c!] c:1,</B>>, color = violet, penwidth = 2];
                            0->11[label = <<B>o:1,0, [c!] ∩ \\\\w c:1,</B><BR/>o:5,2, ε c:(0,11)>, color = red, penwidth = 2];
                            0->12[label = <<B>o:1,0, [c!] ∩ \\\\W c:1,</B><BR/>o:5,2, ε c:(0,12)>, color = red, penwidth = 2];
                            2->4[label = <<B>o:3,2, a c:3,</B>>, color = violet, penwidth = 2];
                            4->14[label = <<B>o:4, t c:4,2,</B>>, color = violet, penwidth = 2];
                            11->14[label = <o: ε c:5,(11,14)<BR/><B>o:6, \\\\W ∩ [a\\\\t] c:6,2,</B>>, color = red, penwidth = 2];
                            12->14[label = <o: ε c:5,(12,14)<BR/><B>o:6, \\\\w ∩ [a\\\\t] c:6,2,</B>>, color = red, penwidth = 2];
                            14->15[label = <<B>o:7, \\\\Z c:7,0,</B>>, color = violet, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_divarication() {
        $regex = 'c(at|\b\t)$';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "13"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, c c:1,</B>>, color = violet, penwidth = 2];
                            0->11[label = <<B>o:1,0, c ∩ \\\\w c:1,</B><BR/>o:5,2, ε c:(0,11)>, color = red, penwidth = 2];
                            2->4[label = <<B>o:3,2, a c:3,</B>>, color = violet, penwidth = 2];
                            4->12[label = <<B>o:4, t c:4,2,</B>>, color = violet, penwidth = 2];
                            11->12[label = <o: ε c:5,(11,12)<BR/><B>o:6, \\\\W ∩ \\\\t c:6,2,</B>>, color = red, penwidth = 2];
                            12->13[label = <<B>o:7, \\\\Z c:7,0,</B>>, color = violet, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_two_branches_into_one() {
        $regex = 'c(a\t|\B)t$';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "11"[shape=doublecircle];
                            0->13[label = <<B>o:1,0, c ∩ \\\\w c:1,</B><BR/>o:5,2, ε c:(0,13)>, color = red, penwidth = 2];
                            10->11[label = <<B>o:7, \\\\Z c:7,0,</B>>, color = violet, penwidth = 2];
                            13->10[label = <o: ε c:5,2,(13,10)<BR/><B>o:6, \\\\w ∩ t c:6,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_before_divarication() {
        $regex = '\t\b(cat|dog)$';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "17"[shape=doublecircle];
                            0->19[label = <<B>o:1,0, \\\\t ∩ \\\\W c:1,</B><BR/>o:2, ε c:(0,19)>, color = red, penwidth = 2];
                            0->20[label = <<B>o:1,0, \\\\t ∩ \\\\W c:1,</B><BR/>o:2, ε c:(0,20)>, color = red, penwidth = 2];
                            6->8[label = <<B>o:5, a c:5,</B>>, color = violet, penwidth = 2];
                            8->16[label = <<B>o:6, t c:6,3,</B>>, color = violet, penwidth = 2];
                            12->14[label = <<B>o:8, o c:8,</B>>, color = violet, penwidth = 2];
                            14->16[label = <<B>o:9, g c:9,3,</B>>, color = violet, penwidth = 2];
                            16->17[label = <<B>o:10, \\\\Z c:10,0,</B>>, color = violet, penwidth = 2];
                            19->6[label = <o: ε c:2,(19,6)<BR/><B>o:4,3, \\\\w ∩ c c:4,</B>>, color = red, penwidth = 2];
                            20->12[label = <o: ε c:2,(20,12)<BR/><B>o:7,3, \\\\w ∩ d c:7,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_before_divarication_classes() {
        $regex = '[a\t]\b([c!]at|[d?]og)$';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "17"[shape=doublecircle];
                            0->19[label = <<B>o:1,0, [a\\\\t] ∩ \\\\w c:1,</B><BR/>o:2, ε c:(0,19)>, color = red, penwidth = 2];
                            0->20[label = <<B>o:1,0, [a\\\\t] ∩ \\\\w c:1,</B><BR/>o:2, ε c:(0,20)>, color = red, penwidth = 2];
                            0->21[label = <<B>o:1,0, [a\\\\t] ∩ \\\\W c:1,</B><BR/>o:2, ε c:(0,21)>, color = red, penwidth = 2];
                            0->22[label = <<B>o:1,0, [a\\\\t] ∩ \\\\W c:1,</B><BR/>o:2, ε c:(0,22)>, color = red, penwidth = 2];
                            6->8[label = <<B>o:5, a c:5,</B>>, color = violet, penwidth = 2];
                            8->16[label = <<B>o:6, t c:6,3,</B>>, color = violet, penwidth = 2];
                            12->14[label = <<B>o:8, o c:8,</B>>, color = violet, penwidth = 2];
                            14->16[label = <<B>o:9, g c:9,3,</B>>, color = violet, penwidth = 2];
                            16->17[label = <<B>o:10, \\\\Z c:10,0,</B>>, color = violet, penwidth = 2];
                            19->6[label = <o: ε c:2,(19,6)<BR/><B>o:4,3, \\\\W ∩ [c!] c:4,</B>>, color = red, penwidth = 2];
                            20->12[label = <o: ε c:2,(20,12)<BR/><B>o:7,3, \\\\W ∩ [d?] c:7,</B>>, color = red, penwidth = 2];
                            21->6[label = <o: ε c:2,(21,6)<BR/><B>o:4,3, \\\\w ∩ [c!] c:4,</B>>, color = red, penwidth = 2];
                            22->12[label = <o: ε c:2,(22,12)<BR/><B>o:7,3, \\\\w ∩ [d?] c:7,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_before_divarication_to_one() {
        $regex = '\t\b(cat|\tog)$';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "17"[shape=doublecircle];
                            0->20[label = <<B>o:1,0, \\\\t ∩ \\\\W c:1,</B><BR/>o:2, ε c:(0,20)>, color = red, penwidth = 2];
                            6->8[label = <<B>o:5, a c:5,</B>>, color = violet, penwidth = 2];
                            8->16[label = <<B>o:6, t c:6,3,</B>>, color = violet, penwidth = 2];
                            16->17[label = <<B>o:10, \\\\Z c:10,0,</B>>, color = violet, penwidth = 2];
                            20->6[label = <o: ε c:2,(20,6)<BR/><B>o:4,3, \\\\w ∩ c c:4,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_end_of_divarication() {
        $regex = 'c(at\b|ow)\t';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "16"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, c c:1,</B>>, color = violet, penwidth = 2];
                            2->4[label = <<B>o:3,2, a c:3,</B>>, color = violet, penwidth = 2];
                            2->13[label = <<B>o:6,2, o c:6,</B>>, color = violet, penwidth = 2];
                            4->9[label = <<B>o:4, t ∩ \\\\w c:4,</B><BR/>o:5, ε c:(4,9)>, color = red, penwidth = 2];
                            9->16[label = <o: ε c:5,2,(9,16)<BR/><B>o:8, \\\\W ∩ \\\\t c:8,0,</B>>, color = red, penwidth = 2];
                            13->15[label = <<B>o:7, w c:7,2,</B>>, color = violet, penwidth = 2];
                            15->16[label = <<B>o:8, \\\\t c:8,0,</B>>, color = violet, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_end_of_divarication_classes() {
        $regex = 'c(a[!t]\b|ow)[a\t]';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "17"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, c c:1,</B>>, color = violet, penwidth = 2];
                            2->4[label = <<B>o:3,2, a c:3,</B>>, color = violet, penwidth = 2];
                            2->14[label = <<B>o:6,2, o c:6,</B>>, color = violet, penwidth = 2];
                            4->9[label = <<B>o:4, [!t] ∩ \\\\w c:4,</B><BR/>o:5, ε c:(4,9)>, color = red, penwidth = 2];
                            4->10[label = <<B>o:4, [!t] ∩ \\\\W c:4,</B><BR/>o:5, ε c:(4,10)>, color = red, penwidth = 2];
                            9->17[label = <o: ε c:5,2,(9,17)<BR/><B>o:8, \\\\W ∩ [a\\\\t] c:8,0,</B>>, color = red, penwidth = 2];
                            10->17[label = <o: ε c:5,2,(10,17)<BR/><B>o:8, \\\\w ∩ [a\\\\t] c:8,0,</B>>, color = red, penwidth = 2];
                            14->16[label = <<B>o:7, w c:7,2,</B>>, color = violet, penwidth = 2];
                            16->17[label = <<B>o:8, [a\\\\t] c:8,0,</B>>, color = violet, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_end_of_divarication_classes_end() {
        $regex = 'c(a[!t]\b|ow)';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "8"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, c c:1,</B>>, color = violet, penwidth = 2];
                            2->4[label = <<B>o:3,2, a c:3,</B>>, color = violet, penwidth = 2];
                            2->14[label = <<B>o:6,2, o c:6,</B>>, color = violet, penwidth = 2];
                            4->9[label = <<B>o:4, [!t] ∩ \\\\w c:4,</B><BR/>o:5, ε c:(4,9)>, color = red, penwidth = 2];
                            4->10[label = <<B>o:4, [!t] ∩ \\\\W c:4,</B><BR/>o:5, ε c:(4,10)>, color = red, penwidth = 2];
                            4->11[label = <<B>o:4, [!t] ∩ \\\\w c:4,</B><BR/>o:5, ε c:(4,11)>, color = red, penwidth = 2];
                            9->8[label = <o: ε c:5,2,0,(9,8)<BR/><B>o: \\\\W c:</B>>, color = red, penwidth = 2, style = dotted];
                            10->8[label = <o: ε c:5,2,0,(10,8)<BR/><B>o: \\\\w c:</B>>, color = red, penwidth = 2, style = dotted];
                            11->8[label = <o: ε c:5,2,0,(11,8)<BR/><B>o: $ c:</B>>, color = red, penwidth = 2, style = dotted];
                            14->8[label = <<B>o:7, w c:7,2,0,</B>>, color = violet, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_after_divarication() {
        $regex = 'c(at|ow)\b\t';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "13"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, c c:1,</B>>, color = violet, penwidth = 2];
                            2->4[label = <<B>o:3,2, a c:3,</B>>, color = violet, penwidth = 2];
                            2->8[label = <<B>o:5,2, o c:5,</B>>, color = violet, penwidth = 2];
                            4->15[label = <<B>o:4, t ∩ \\\\w c:4,2,</B><BR/>o:7, ε c:(8,15)>, color = red, penwidth = 2];
                            8->15[label = <<B>o:6, w ∩ \\\\w c:6,2,</B><BR/>o:7, ε c:(8,15)>, color = red, penwidth = 2];
                            15->13[label = <o: ε c:7,(15,13)<BR/><B>o:8, \\\\W ∩ \\\\t c:8,0,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_after_divarication_classes() {
        $regex = 'c(a[t!]|ow)\b[a\t]';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "13"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, c c:1,</B>>, color = violet, penwidth = 2];
                            2->4[label = <<B>o:3,2, a c:3,</B>>, color = violet, penwidth = 2];
                            2->8[label = <<B>o:5,2, o c:5,</B>>, color = violet, penwidth = 2];
                            4->15[label = <<B>o:4, [t!] ∩ \\\\w c:4,2,</B><BR/>o:7, ε c:(8,15)>, color = red, penwidth = 2];
                            4->16[label = <<B>o:4, [t!] ∩ \\\\W c:4,2,</B><BR/>o:7, ε c:(4,16)>, color = red, penwidth = 2];
                            8->15[label = <<B>o:6, w ∩ \\\\w c:6,2,</B><BR/>o:7, ε c:(8,15)>, color = red, penwidth = 2];
                            15->13[label = <o: ε c:7,(15,13)<BR/><B>o:8, \\\\W ∩ [a\\\\t] c:8,0,</B>>, color = red, penwidth = 2];
                            16->13[label = <o: ε c:7,(16,13)<BR/><B>o:8, \\\\w ∩ [a\\\\t] c:8,0,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_divarication_with_eps() {
        $regex = '\b(a|)b';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "8"[shape=rarrow];
                        "7"[shape=doublecircle];
                            6->7[label = <<B>o:5, b c:5,0,</B>>, color = violet, penwidth = 2];
                            8->9[label = <<B>o: \\\\W c:</B><BR/>o:1,0, ε c:(8,9)>, color = red, penwidth = 2, style = dotted];
                            8->10[label = <<B>o: \\\\W c:</B><BR/>o:1,0, ε c:(8,10)>, color = red, penwidth = 2, style = dotted];
                            8->11[label = <<B>o: ^ c:</B><BR/>o:1,0, ε c:(8,11)>, color = red, penwidth = 2, style = dotted];
                            8->12[label = <<B>o: ^ c:</B><BR/>o:1,0, ε c:(8,12)>, color = red, penwidth = 2, style = dotted];
                            9->6[label = <o: ε c:1,(9,6)<BR/><B>o:3,2, \\\\w ∩ a c:3,2,</B>>, color = red, penwidth = 2];
                            10->7[label = <o: ε c:1,(10,7)<BR/>o:4,2, ε c:4,2,(10,7)<BR/><B>o:5, \\\\w ∩ b c:5,0,</B>>, color = red, penwidth = 2];
                            11->6[label = <o: ε c:1,(11,6)<BR/><B>o:3,2, \\\\w ∩ a c:3,2,</B>>, color = red, penwidth = 2];
                            12->7[label = <o: ε c:1,(12,7)<BR/>o:4,2, ε c:4,2,(12,7)<BR/><B>o:5, \\\\w ∩ b c:5,0,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_cycle() {
        $regex = 'd(\tcat\b)+';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "12"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, d c:1,</B>>, color = violet, penwidth = 2];
                            2->4[label = <<B>o:4,3,2, \\\\t c:4,</B>>, color = violet, penwidth = 2];
                            4->6[label = <<B>o:5, c c:5,</B>>, color = violet, penwidth = 2];
                            6->8[label = <<B>o:6, a c:6,</B>>, color = violet, penwidth = 2];
                            8->13[label = <<B>o:7, t ∩ \\\\w c:7,</B><BR/>o:8, ε c:(8,13)>, color = red, penwidth = 2];
                            8->14[label = <<B>o:7, t ∩ \\\\w c:7,</B><BR/>o:8, ε c:(8,14)>, color = red, penwidth = 2];
                            13->12[label = <o: ε c:8,3,2,0,(13,12)<BR/><B>o: \\\\W c:</B>>, color = red, penwidth = 2, style = dotted];
                            13->4[label = <o: ε c:8,3,(13,4)<BR/><B>o:4,3, \\\\W ∩ \\\\t c:4,</B>>, color = red, penwidth = 2];
                            14->12[label = <o: ε c:8,3,2,0,(14,12)<BR/><B>o: $ c:</B>>, color = red, penwidth = 2, style = dotted];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_cycle_2() {
        $regex = 'd([b\t]ca[t!]\b)+';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "12"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, d c:1,</B>>, color = violet, penwidth = 2];
                            2->4[label = <<B>o:4,3,2, [b\\\\t] c:4,</B>>, color = violet, penwidth = 2];
                            4->6[label = <<B>o:5, c c:5,</B>>, color = violet, penwidth = 2];
                            6->8[label = <<B>o:6, a c:6,</B>>, color = violet, penwidth = 2];
                            8->13[label = <<B>o:7, [t!] ∩ \\\\w c:7,</B><BR/>o:8, ε c:(8,13)>, color = red, penwidth = 2];
                            8->14[label = <<B>o:7, [t!] ∩ \\\\W c:7,</B><BR/>o:8, ε c:(8,14)>, color = red, penwidth = 2];
                            8->15[label = <<B>o:7, [t!] ∩ \\\\w c:7,</B><BR/>o:8, ε c:(8,15)>, color = red, penwidth = 2];
                            13->12[label = <o: ε c:8,3,2,0,(13,12)<BR/><B>o: \\\\W c:</B>>, color = red, penwidth = 2, style = dotted];
                            13->4[label = <o: ε c:8,3,(13,4)<BR/><B>o:4,3, \\\\W ∩ [b\\\\t] c:4,</B>>, color = red, penwidth = 2];
                            14->12[label = <o: ε c:8,3,2,0,(14,12)<BR/><B>o: \\\\w c:</B>>, color = red, penwidth = 2, style = dotted];
                            14->4[label = <o: ε c:8,3,(14,4)<BR/><B>o:4,3, \\\\w ∩ [b\\\\t] c:4,</B>>, color = red, penwidth = 2];
                            15->12[label = <o: ε c:8,3,2,0,(15,12)<BR/><B>o: $ c:</B>>, color = red, penwidth = 2, style = dotted];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_to_start() {
        $regex = '(\tac\b)+';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "8"[shape=doublecircle];
                            0->2[label = <<B>o:2,1,0, \\\\t c:2,</B>>, color = violet, penwidth = 2];
                            2->4[label = <<B>o:3, a c:3,</B>>, color = violet, penwidth = 2];
                            4->9[label = <<B>o:4, c ∩ \\\\w c:4,</B><BR/>o:5, ε c:(4,9)>, color = red, penwidth = 2];
                            4->10[label = <<B>o:4, c ∩ \\\\w c:4,</B><BR/>o:5, ε c:(4,10)>, color = red, penwidth = 2];
                            9->8[label = <o: ε c:5,1,0,(9,8)<BR/><B>o: \\\\W c:</B>>, color = red, penwidth = 2, style = dotted];
                            9->2[label = <o: ε c:5,1,(9,2)<BR/><B>o:2,1, \\\\W ∩ \\\\t c:2,</B>>, color = red, penwidth = 2];
                            10->8[label = <o: ε c:5,1,0,(10,8)<BR/><B>o: $ c:</B>>, color = red, penwidth = 2, style = dotted];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_to_start_2() {
        $regex = '([b\t]ac\b)+';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "8"[shape=doublecircle];
                            0->2[label = <<B>o:2,1,0, [b\\\\t] c:2,</B>>, color = violet, penwidth = 2];
                            2->4[label = <<B>o:3, a c:3,</B>>, color = violet, penwidth = 2];
                            4->9[label = <<B>o:4, c ∩ \\\\w c:4,</B><BR/>o:5, ε c:(4,9)>, color = red, penwidth = 2];
                            4->10[label = <<B>o:4, c ∩ \\\\w c:4,</B><BR/>o:5, ε c:(4,10)>, color = red, penwidth = 2];
                            9->8[label = <o: ε c:5,1,0,(9,8)<BR/><B>o: \\\\W c:</B>>, color = red, penwidth = 2, style = dotted];
                            9->2[label = <o: ε c:5,1,(9,2)<BR/><B>o:2,1, \\\\W ∩ [b\\\\t] c:2,</B>>, color = red, penwidth = 2];
                            10->8[label = <o: ε c:5,1,0,(10,8)<BR/><B>o: $ c:</B>>, color = red, penwidth = 2, style = dotted];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_to_start_from_end() {
        $regex = '(\ta\b)+';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "6"[shape=doublecircle];
                            0->2[label = <<B>o:2,1,0, \\\\t c:2,</B>>, color = violet, penwidth = 2];
                            2->7[label = <<B>o:3, a ∩ \\\\w c:3,</B><BR/>o:4, ε c:(2,7)>, color = red, penwidth = 2];
                            2->8[label = <<B>o:3, a ∩ \\\\w c:3,</B><BR/>o:4, ε c:(2,8)>, color = red, penwidth = 2];
                            7->6[label = <o: ε c:4,1,0,(7,6)<BR/><B>o: \\\\W c:</B>>, color = red, penwidth = 2, style = dotted];
                            7->2[label = <o: ε c:4,1,(7,2)<BR/><B>o:2,1, \\\\W ∩ \\\\t c:2,</B>>, color = red, penwidth = 2];
                            8->6[label = <o: ε c:4,1,0,(8,6)<BR/><B>o: $ c:</B>>, color = red, penwidth = 2, style = dotted];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_to_start_from_end_2() {
        $regex = '([a!]\b)+';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "4"[shape=doublecircle];
                            0->5[label = <<B>o:2,1,0, [a!] ∩ \\\\w c:2,</B><BR/>o:3, ε c:(0,5)>, color = red, penwidth = 2];
                            0->6[label = <<B>o:2,1,0, [a!] ∩ \\\\W c:2,</B><BR/>o:3, ε c:(0,6)>, color = red, penwidth = 2];
                            0->7[label = <<B>o:2,1,0, [a!] ∩ \\\\w c:2,</B><BR/>o:3, ε c:(0,7)>, color = red, penwidth = 2];
                            5->4[label = <o: ε c:3,1,0,(5,4)<BR/><B>o: \\\\W c:</B>>, color = red, penwidth = 2, style = dotted];
                            5->6[label = <o: ε c:3,1,(5,6)<BR/><B>o:2,1, [a!] ∩ \\\\W c:2,</B><BR/>o:3, ε c:(5,6)>, color = red, penwidth = 2];
                            6->4[label = <o: ε c:3,1,0,(6,4)<BR/><B>o: \\\\w c:</B>>, color = red, penwidth = 2, style = dotted];
                            6->5[label = <o: ε c:3,1,(6,7)<BR/><B>o:2,1, [a!] ∩ \\\\w c:2,</B><BR/>o:3, ε c:(6,5)>, color = red, penwidth = 2];
                            6->7[label = <o: ε c:3,1,(6,7)<BR/><B>o:2,1, [a!] ∩ \\\\w c:2,</B><BR/>o:3, ε c:(6,7)>, color = red, penwidth = 2];
                            7->4[label = <o: ε c:3,1,0,(7,4)<BR/><B>o: $ c:</B>>, color = red, penwidth = 2, style = dotted];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_result_state_cycle() {
        $regex = '(a\B)+';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "4"[shape=doublecircle];
                            0->5[label = <<B>o:2,1,0, a ∩ \\\\w c:2,</B><BR/>o:3, ε c:(0,5)>, color = red, penwidth = 2];
                            5->4[label = <o: ε c:3,1,0,(5,4)<BR/><B>o: \\\\w c:</B>>, color = red, penwidth = 2, style = dotted];
                            5->5[label = <o: ε c:3,1,(5,5)<BR/><B>o:2,1, a ∩ \\\\w c:2,</B><BR/>o:3, ε c:(5,5)>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_cycle_no_success() {
        $regex = 'd(\tcat\B)+';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "12"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, d c:1,</B>>, color = violet, penwidth = 2];
                            2->4[label = <<B>o:4,3,2, \\\\t c:4,</B>>, color = violet, penwidth = 2];
                            4->6[label = <<B>o:5, c c:5,</B>>, color = violet, penwidth = 2];
                            6->8[label = <<B>o:6, a c:6,</B>>, color = violet, penwidth = 2];
                            8->13[label = <<B>o:7, t ∩ \\\\w c:7,</B><BR/>o:8, ε c:(8,13)>, color = red, penwidth = 2];
                            13->12[label = <o: ε c:8,3,2,0,(13,12)<BR/><B>o: \\\\w c:</B>>, color = red, penwidth = 2, style = dotted];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_cycle_divarication() {
        $regex = 'd((\b\t|bde)g)+f';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "17"[shape=doublecircle];
                            0->6[label = <<B>o:1,0, d c:1,</B>>, color = violet, penwidth = 2];
                            0->7[label = <<B>o:1,0, d ∩ \\\\w c:1,</B><BR/>o:5,4,3,2, ε c:(0,7)>, color = red, penwidth = 2];
                            6->10[label = <<B>o:7,4,3,2, b c:7,</B>>, color = violet, penwidth = 2];
                            7->14[label = <o: ε c:5,(7,14)<BR/><B>o:6, \\\\W ∩ \\\\t c:6,4,</B>>, color = red, penwidth = 2];
                            10->12[label = <<B>o:8, d c:8,</B>>, color = violet, penwidth = 2];
                            12->14[label = <<B>o:9, e c:9,4,</B>>, color = violet, penwidth = 2];
                            14->7[label = <<B>o:10, g ∩ \\\\w c:10,3,</B><BR/>o:5,4,3, ε c:(14,7)>, color = red, penwidth = 2];
                            14->7[label = <<B>o:10, g ∩ \\\\w c:10,3,2,</B><BR/>o:5,4,3, ε c:(14,7)>, color = red, penwidth = 2];
                            14->16[label = <<B>o:10, g c:10,3,2,</B>>, color = violet, penwidth = 2];
                            16->17[label = <<B>o:11, f c:11,0,</B>>, color = violet, penwidth = 2];
                            16->10[label = <<B>o:7,4,3, b c:7,</B>>, color = violet, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_cycle_state() {
        $regex = '\ba*t';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "7"[shape=rarrow];
                        "6"[shape=doublecircle];
                            3->3[label = <<B>o:3, a c:3,</B>>, color = violet, penwidth = 2];
                            3->6[label = <o: ε c:2,(3,6)<BR/><B>o:4, t c:4,0,</B>>, color = violet, penwidth = 2];
                            7->8[label = <<B>o: \\\\W c:</B><BR/>o:1,0, ε c:(7,8)>, color = red, penwidth = 2, style = dotted];
                            7->9[label = <<B>o: \\\\W c:</B><BR/>o:1,0, ε c:(7,9)>, color = red, penwidth = 2, style = dotted];
                            7->10[label = <<B>o: ^ c:</B><BR/>o:1,0, ε c:(7,10)>, color = red, penwidth = 2, style = dotted];
                            7->11[label = <<B>o: ^ c:</B><BR/>o:1,0, ε c:(7,11)>, color = red, penwidth = 2, style = dotted];
                            8->3[label = <o: ε c:1,(8,3)<BR/><B>o:3,2, \\\\w ∩ a c:3,</B>>, color = red, penwidth = 2];
                            9->6[label = <o: ε c:1,(9,6)<BR/>o:2, ε c:2,(9,6)<BR/><B>o:4, \\\\w ∩ t c:4,0,</B>>, color = red, penwidth = 2];
                            10->3[label = <o: ε c:1,(10,3)<BR/><B>o:3,2, \\\\w ∩ a c:3,</B>>, color = red, penwidth = 2];
                            11->6[label = <o: ε c:1,(11,6)<BR/>o:2, ε c:2,(11,6)<BR/><B>o:4, \\\\w ∩ t c:4,0,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_cycle_state_start() {
        $regex = 'a*\Bt';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "6"[shape=doublecircle];
                            0->1[label = <<B>o:2,1,0, a c:2,</B>>, color = violet, penwidth = 2];
                            0->8[label = <<B>o: \\\\w c:</B><BR/>o:1,0, ε c:1,(0,8)<BR/>o:3, ε c:(0,8)>, color = red, penwidth = 2, style = dotted];
                            0->8[label = <<B>o:2,1,0, a ∩ \\\\w c:2,</B><BR/>o: ε c:1,(0,8)<BR/>o:3, ε c:(1,8)>, color = red, penwidth = 2];
                            1->1[label = <<B>o:2, a c:2,</B>>, color = violet, penwidth = 2];
                            1->8[label = <<B>o: \\\\w c:</B><BR/>o: ε c:1,(1,8)<BR/>o:3, ε c:(1,8)>, color = red, penwidth = 2, style = dotted];
                            1->8[label = <<B>o:2, a ∩ \\\\w c:2,</B><BR/>o: ε c:1,(1,8)<BR/>o:3, ε c:(1,8)>, color = red, penwidth = 2];
                            8->6[label = <o: ε c:3,(8,6)<BR/><B>o:4, \\\\w ∩ t c:4,0,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_cycle_b() {
        $regex = 'a\B*t';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "6"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, a c:1,</B>>, color = violet, penwidth = 2];
                            0->7[label = <<B>o:1,0, a ∩ \\\\w c:1,</B><BR/>o:3,2, ε c:(0,7)>, color = red, penwidth = 2];
                            2->6[label = <o:2, ε c:2,(2,6)<BR/><B>o:4, t c:4,0,</B>>, color = violet, penwidth = 2];
                            7->6[label = <o: ε c:3,(7,6)<BR/>o: ε c:2,(7,6)<BR/><B>o:4, \\\\w ∩ t c:4,0,</B>>, color = red, penwidth = 2];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_cycle_b_2() {
        $regex = 'c(a[t!]eo)+\b[d?]';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0"[shape=rarrow];
                    "13"[shape=doublecircle];
                        0->2[label = <<B>o:1,0, c c:1,</B>>, color = violet, penwidth = 2];
                        2->4[label = <<B>o:4,3,2, a c:4,</B>>, color = violet, penwidth = 2];
                        4->6[label = <<B>o:5, [t!] c:5,</B>>, color = violet, penwidth = 2];
                        6->8[label = <<B>o:6, e c:6,</B>>, color = violet, penwidth = 2];
                        8->14[label = <<B>o:7, o c:7,3,2,</B>>, color = violet, penwidth = 2];
                        8->15[label = <<B>o:7, o ∩ \\\\w c:7,3,2,</B><BR/>o:8, ε c:(8,15)>, color = red, penwidth = 2];
                        14->4[label = <<B>o:4,3, a c:4,</B>>, color = violet, penwidth = 2];
                        15->13[label = <o: ε c:8,(15,13)<BR/><B>o:9, \\\\W ∩ [d?] c:9,0,</B>>, color = red, penwidth = 2];
                    }';
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    public function test_cycle_b_3() {
        $regex = 'c(a[t!]eo)+\b';
        $dotresult = 'digraph {
                            rankdir=LR;
                            "0"[shape=rarrow];
                        "12"[shape=doublecircle];
                            0->2[label = <<B>o:1,0, c c:1,</B>>, color = violet, penwidth = 2];
                            2->4[label = <<B>o:4,3,2, a c:4,</B>>, color = violet, penwidth = 2];
                            4->6[label = <<B>o:5, [t!] c:5,</B>>, color = violet, penwidth = 2];
                            6->8[label = <<B>o:6, e c:6,</B>>, color = violet, penwidth = 2];
                            8->10[label = <<B>o:7, o c:7,3,2,</B>>, color = violet, penwidth = 2];
                            8->13[label = <<B>o:7, o ∩ \\\\w c:7,3,2,</B><BR/>o:8, ε c:(8,13)>, color = red, penwidth = 2];
                            8->14[label = <<B>o:7, o ∩ \\\\w c:7,3,2,</B><BR/>o:8, ε c:(8,14)>, color = red, penwidth = 2];
                            10->4[label = <<B>o:4,3, a c:4,</B>>, color = violet, penwidth = 2];
                            13->12[label = <o: ε c:8,0,(13,12)<BR/><B>o: \\\\W c:</B>>, color = red, penwidth = 2, style = dotted];
                            14->12[label = <o: ε c:8,0,(14,12)<BR/><B>o: $ c:</B>>, color = red, penwidth = 2, style = dotted];
                        }';
        $search = '
                        ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $options = new qtype_preg_matching_options();
        $options->mergeassertions = true;
        $matcher = new qtype_preg_fa_matcher($regex, $options);
        if (!$matcher->errors_exist()) {
            $result = $matcher->automaton->fa_to_dot();
            $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
        } else {
            $this->assertTrue(false, "fa merging wordbreaks failed\n");
        }
    }

    // --------------------- Copy branches tests ------------------------


    public function test_copy_whole_branch() {
        $sourcedescription = 'digraph {
                                0;
                                4;
                                0->1[label=<<B>o: [df] c:</B>>];
                                0->2[label=<<B>o: [0-9] c:</B>>];
                                1->3[label=<<B>o: [abc] c:</B>>];
                                2->3[label=<<B>o: [01] c:</B>>];
                                3->4[label=<<B>o: a c:</B>>];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "1,"[shape=doublecircle];
                    "4,"[shape=doublecircle];
                        "0,"->"2,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,"[label = <<B>o: [df] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"3,"[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"4,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                    }';

        $source = qtype_preg_fa::read_fa($sourcedescription);
        $direct = new qtype_preg_fa();
        $numbers = $source->get_state_numbers();
        $stopcoping = array(array_search('1', $numbers));
        $oldfront = array(array_search('0', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot(null, null, true);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_copy_impliciment_cycle() {
        $sourcedescription = 'digraph {
                                0;
                                3;
                                0->1[label=<<B>o: [ab] c:</B>>];
                                0->2[label=<<B>o: [0-9] c:</B>>];
                                1->3[label=<<B>o: . c:</B>>];
                                2->3[label=<<B>o: [01] c:</B>>];
                                3->0[label=<<B>o: a c:</B>>];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "1,"[shape=doublecircle];
                    "3,"[shape=doublecircle];
                        "0,"->"2,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"3,"[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"0,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                    }';

        $source = qtype_preg_fa::read_fa($sourcedescription);
        $direct = new qtype_preg_fa();
        $numbers = $source->get_state_numbers();
        $stopcoping = array(array_search('1', $numbers));
        $oldfront = array(array_search('0', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot(null, null, true);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_copy_cycle_end() {
        $sourcedescription = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>];
                                1->1[label=<<B>o: [0-9] c:</B>>];
                                1->2[label=<<B>o: a c:</B>>];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "1,"[shape=doublecircle];
                        "0,"->"1,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                    }';

        $source = qtype_preg_fa::read_fa($sourcedescription);
        $direct = new qtype_preg_fa();
        $numbers = $source->get_state_numbers();
        $stopcoping = array(array_search('1', $numbers));
        $oldfront = array(array_search('0', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot(null, null, true);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_copy_not_empty_direct() {
        $sourcedescription = 'digraph {
                                0;
                                4;
                                0->1[label=<<B>o: [df] c:</B>>];
                                0->2[label=<<B>o: [0-9] c:</B>>];
                                1->3[label=<<B>o: [abc] c:</B>>];
                                2->3[label=<<B>o: [01] c:</B>>];
                                3->4[label=<<B>o: a c:</B>>];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "4,"[shape=doublecircle];
                        "0,"->"1,"[label = <<B>o: [df] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"2,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"3,"[label = <<B>o: [abc] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"3,"[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"4,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                    }';
        $directdescription = 'digraph {
                                "0,";
                                "1,";
                                "0,"->"1,"[label=<<B>o: [df] c:</B>>];
                            }';


        $source = qtype_preg_fa::read_fa($sourcedescription);
        $direct = qtype_preg_fa::read_fa($directdescription);
        $numbers = $source->get_state_numbers();
        $stopcoping = array(array_search('4', $numbers));
        $oldfront = array(array_search('2', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot(null, null, true);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

   public function test_direct_has_states_for_coping() {
        $sourcedescription = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>];
                                1->2[label=<<B>o: [ab] c:</B>>];
                                2->0[label=<<B>o: [ab] c:</B>>];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "2,"[shape=doublecircle];
                        "0,"->"1,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"0,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                    }';
        $directdescription = 'digraph {
                                "0,";
                                "1,";
                                "0,"->"1,"[label=<<B>o: [ab] c:</B>>];
                            }';

        $source = qtype_preg_fa::read_fa($sourcedescription);
        $direct = qtype_preg_fa::read_fa($directdescription);
        $numbers = $source->get_state_numbers();
        $stopcoping = array(array_search('1', $numbers));
        $oldfront = array(array_search('2', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot(null, null, true);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_coping_not_nessesary() {
        $sourcedescription = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>];
                                1->2[label=<<B>o: [ab] c:</B>>];
                                2->0[label=<<B>o: [ab] c:</B>>];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "2,"[shape=doublecircle];
                        "0,"->"1,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"0,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                    }';
        $directdescription = 'digraph {
                                "0,";
                                "2,";
                                "0,"->"1,"[label=<<B>o: [ab] c:</B>>];
                                "1,"->"2,"[label=<<B>o: [ab] c:</B>>];
                                "2,"->"0,"[label=<<B>o: [ab] c:</B>>];
                            }';

        $source = qtype_preg_fa::read_fa($sourcedescription);
        $direct = qtype_preg_fa::read_fa($directdescription);
        $numbers = $source->get_state_numbers();
        $stopcoping = array(array_search('0', $numbers));
        $oldfront = array(array_search('2', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot(null, null, true);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_coping_back() {
        $sourcedescription = 'digraph {
                                0;
                                4;
                                0->1[label=<<B>o: [df] c:</B>>];
                                0->2[label=<<B>o: [0-9] c:</B>>];
                                1->3[label=<<B>o: [abc] c:</B>>];
                                2->3[label=<<B>o: [01] c:</B>>];
                                3->4[label=<<B>o: [a] c:</B>>];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "4,"[shape=doublecircle];
                        "3,"->"4,"[label = <<B>o: [a] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"3,"[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"3,"[label = <<B>o: [abc] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"2,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,"[label = <<B>o: [df] c:</B>>, color = violet, penwidth = 2];
                    }';

        $source = qtype_preg_fa::read_fa($sourcedescription);
        $direct = new qtype_preg_fa();
        $numbers = $source->get_state_numbers();
        $stopcoping = array(array_search('0', $numbers));
        $oldfront = array(array_search('4', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 1);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot(null, null, true);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_copy_second_automata() {
        $sourcedescription = 'digraph {
                                0;
                                4;
                                0->1[label=<<B>o: [df] c:</B>>, color = blue];
                                0->2[label=<<B>o: [0-9] c:</B>>, color = blue];
                                1->3[label=<<B>o: [abc] c:</B>>, color = blue];
                                2->3[label=<<B>o: [01] c:</B>>, color = blue];
                                3->4[label=<<B>o: [a] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        ",0"[shape=rarrow];
                    ",1"[shape=doublecircle];
                    ",4"[shape=doublecircle];
                        ",0"->",2"[label = <<B>o: [0-9] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->",1"[label = <<B>o: [df] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->",3"[label = <<B>o: [01] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",3"->",4"[label = <<B>o: [a] c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $source = qtype_preg_fa::read_fa($sourcedescription);
        $direct = $direct = new qtype_preg_fa();;
        $numbers = $source->get_state_numbers();
        $stopcoping = array(array_search('1', $numbers));
        $oldfront = array(array_search('0', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot(null, null, true);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_copy_second_cycle() {
        $sourcedescription = 'digraph {
                                0;
                                3;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                0->2[label=<<B>o: [0-9] c:</B>>, color = blue];
                                1->3[label=<<B>o: [.] c:</B>>, color = blue];
                                2->3[label=<<B>o: [01] c:</B>>, color = blue];
                                3->0[label=<<B>o: [a] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        ",0"[shape=rarrow];
                    ",1"[shape=doublecircle];
                    ",3"[shape=doublecircle];
                        ",0"->",2"[label = <<B>o: [0-9] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->",1"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->",3"[label = <<B>o: [01] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",3"->",0"[label = <<B>o: [a] c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $source = qtype_preg_fa::read_fa($sourcedescription);
        $direct = $direct = new qtype_preg_fa();
        $numbers = $source->get_state_numbers();
        $stopcoping = array(array_search('1', $numbers));
        $oldfront = array(array_search('0', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot(null, null, true);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_copy_not_empty_second() {
        $sourcedescription = 'digraph {
                                0;
                                4;
                                0->1[label=<<B>o: [df] c:</B>>, color = blue];
                                0->2[label=<<B>o: [0-9] c:</B>>, color = blue];
                                1->3[label=<<B>o: [abc] c:</B>>, color = blue];
                                2->3[label=<<B>o: [01] c:</B>>, color = blue];
                                3->4[label=<<B>o: [a] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        ",0"[shape=rarrow];
                    ",4"[shape=doublecircle];
                        ",0"->",1"[label = <<B>o: [df] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->",2"[label = <<B>o: [0-9] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->",3"[label = <<B>o: [abc] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->",3"[label = <<B>o: [01] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",3"->",4"[label = <<B>o: [a] c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';
        $directdescription = 'digraph {
                                ",0";
                                ",1";
                                ",0"->",1"[label=<<B>o: [df] c:</B>>, color = blue, style = dotted];
                            }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $source = qtype_preg_fa::read_fa($sourcedescription);
        $direct = qtype_preg_fa::read_fa($directdescription);
        $numbers = $source->get_state_numbers();
        $stopcoping = array(array_search('4', $numbers));
        $oldfront = array(array_search('2', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot(null, null, true);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_coping_not_nessesary_second() {
        $sourcedescription = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [ab] c:</B>>, color = blue];
                                2->0[label=<<B>o: [ab] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        ",0"[shape=rarrow];
                    ",2"[shape=doublecircle];
                        ",0"->",1"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->",0"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->",2"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';
        $directdescription = 'digraph {
                                ",0";
                                ",2";
                                ",0"->",1"[label=<<B>o: [ab] c:</B>>, color = blue, style = dotted];
                                ",1"->",2"[label=<<B>o: [ab] c:</B>>, color = blue, style = dotted];
                                ",2"->",0"[label=<<B>o: [ab] c:</B>>, color = blue, style = dotted];
                            }';

        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;
        $source = qtype_preg_fa::read_fa($sourcedescription);
        $direct = qtype_preg_fa::read_fa($directdescription);
        $numbers = $source->get_state_numbers();
        $stopcoping = array(array_search('0', $numbers));
        $oldfront = array(array_search('2', $numbers));
        $direct->copy_modify_branches($source, $oldfront, $stopcoping, 0);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $result = $direct->fa_to_dot(null, null, true);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }


    // --------------------- Remove unreachable tests ------------------------


    public function test_without_blind_states() {
        $dotdescription = 'digraph {
                            0;
                            3;
                            0->1[label=<<B>o: [01] c:</B>>];
                            1->2[label=<<B>o: [abc] c:</B>>];
                            2->3[label=<<B>o: [01] c:</B>>];
                        }';
        $dotresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"3"[shape=doublecircle];
    0->1[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
    2->3[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
    1->2[label = <<B>o: [abc] c:</B>>, color = violet, penwidth = 2];
}';
        $input = qtype_preg_fa::read_fa($dotdescription);
        $input->remove_unreachable_states();
        $inputdesc = $input->fa_to_dot(null, null, true);
        $this->assertEquals($inputdesc, $dotresult, 'Result automata is not equal to expected');
    }

    public function test_one_blind_state() {
        $dotdescription = 'digraph {
                            0;
                            3;
                            0->1[label=<<B>o: [01] c:</B>>];
                            1->2[label=<<B>o: [abc] c:</B>>];
                            1->3[label=<<B>o: [01] c:</B>>];
                        }';
        $dotresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"3"[shape=doublecircle];
    0->1[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
    1->3[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
}';
        $input = qtype_preg_fa::read_fa($dotdescription);
        $input->remove_unreachable_states();
        $inputdesc = $input->fa_to_dot(null, null, true);
        $this->assertEquals($inputdesc, $dotresult, 'Result automata is not equal to expected');
    }

    public function test_several_linked_blind_states() {
        $dotdescription = 'digraph {
                            0;
                            2;
                            0->1[label=<<B>o: [01] c:</B>>];
                            1->2[label=<<B>o: a c:</B>>];
                            1->3[label=<<B>o: [01] c:</B>>];
                            3->4[label=<<B>o: b c:</B>>];
                        }';
        $dotresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"2"[shape=doublecircle];
    0->1[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
    1->2[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
}';
        $input = qtype_preg_fa::read_fa($dotdescription);
        $input->remove_unreachable_states();
        $inputdesc = $input->fa_to_dot(null, null, true);
        $this->assertEquals($inputdesc, $dotresult, 'Result automata is not equal to expected');
    }

    public function test_blind_cycle() {
        $dotdescription = 'digraph {
                            0;
                            2;
                            0->1[label=<<B>o: [01] c:</B>>];
                            1->2[label=<<B>o: a c:</B>>];
                            1->3[label=<<B>o: [ab] c:</B>>];
                            3->4[label=<<B>o: [cd] c:</B>>];
                            4->3[label=<<B>o: [xy] c:</B>>];
                        }';
        $dotresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"2"[shape=doublecircle];
    0->1[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
    1->2[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
}';
        $input = qtype_preg_fa::read_fa($dotdescription);
        $input->remove_unreachable_states();
        $inputdesc = $input->fa_to_dot(null, null, true);
        $this->assertEquals($inputdesc, $dotresult, 'Result automata is not equal to expected');
    }

    public function test_cycle_remove() {
        $dotdescription = 'digraph {
                            0;
                            2;
                            0->1[label=<<B>o: [01] c:</B>>];
                            1->2[label=<<B>o: a c:</B>>];
                            1->3[label=<<B>o: [ab] c:</B>>];
                            3->4[label=<<B>o: [cd] c:</B>>];
                            4->1[label=<<B>o: [xy] c:</B>>];
                        }';
        $dotresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"2"[shape=doublecircle];
    0->1[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
    4->1[label = <<B>o: [xy] c:</B>>, color = violet, penwidth = 2];
    1->3[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
    1->2[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
    3->4[label = <<B>o: [cd] c:</B>>, color = violet, penwidth = 2];
}';
        $input = qtype_preg_fa::read_fa($dotdescription);
        $input->remove_unreachable_states();
        $inputdesc = $input->fa_to_dot(null, null, true);
        $this->assertEquals($inputdesc, $dotresult, 'Result automata is not equal to expected');
    }

    public function test_del_merged_blind_states() {
        $dotdescription = 'digraph {
                            0;
                            5;
                            0->1[label=<<B>o: [01] c:</B>>];
                            1->4[label=<<B>o: [01] c:</B>>];
                            0->4[label=<<B>o: [a-v] c:</B>>];
                            4->5[label=<<B>o: [kmn] c:</B>>];
                            1->6[label=<<B>o: a c:</B>>];
                            1->7[label=<<B>o: a c:</B>>];
                        }';
        $dotresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"5"[shape=doublecircle];
    0->4[label = <<B>o: [a-v] c:</B>>, color = violet, penwidth = 2];
    0->1[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
    1->4[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
    4->5[label = <<B>o: [kmn] c:</B>>, color = violet, penwidth = 2];
}';
        $input = qtype_preg_fa::read_fa($dotdescription);
        $input->remove_unreachable_states();
        $inputdesc = $input->fa_to_dot(null, null, true);
        $this->assertEquals($inputdesc, $dotresult, 'Result automata is not equal to expected');
    }

    public function test_del_states_with_intersection() {
        $dotdescription = 'digraph {
                            "0,";
                            ",5";
                            "0,"->"1,2"[label=<<B>o: [01] c:</B>>];
                            "1,2"->",3"[label=<<B>o: [01] c:</B>>];
                            "0,"->",3"[label=<<B>o: [a-v] c:</B>>];
                            ",3"->",5"[label=<<B>o: [kmn] c:</B>>];
                            "1,2"->"6,"[label=<<B>o: a c:</B>>];
                            "1,2"->"8,7"[label=<<B>o: a c:</B>>];
                        }';
        $dotresult = 'digraph {
    rankdir=LR;
    "0,"[shape=rarrow];
",5"[shape=doublecircle];
    "0,"->",3"[label = <<B>o: [a-v] c:</B>>, color = violet, penwidth = 2];
    "0,"->"1,2"[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
    "1,2"->",3"[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
    ",3"->",5"[label = <<B>o: [kmn] c:</B>>, color = violet, penwidth = 2];
}';
        $input = qtype_preg_fa::read_fa($dotdescription);
        $input->remove_unreachable_states();
        $inputdesc = $input->fa_to_dot(null, null, true);
        $this->assertEquals($inputdesc, $dotresult, 'Result automata is not equal to expected');
    }

    public function test_blind_states_from_start() {
        $dotdescription = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [01] c:</B>>];
                                1->2[label=<<B>o: a c:</B>>];
                                3->1[label=<<B>o: [ab] c:</B>>];
                                4->3[label=<<B>o: [cd] c:</B>>];
                                5->4[label=<<B>o: [xy] c:</B>>];
                                6->5[label=<<B>o: [cd] c:</B>>];
                            }';
        $dotresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"2"[shape=doublecircle];
    0->1[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
    1->2[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
}';
        $input = qtype_preg_fa::read_fa($dotdescription);
        $input->remove_unreachable_states();
        $inputdesc = $input->fa_to_dot(null, null, true);
        $this->assertEquals($inputdesc, $dotresult, 'Result automata is not equal to expected');
    }

    public function test_blind_cycle_from_start() {
        $dotdescription = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [01] c:</B>>];
                                1->2[label=<<B>o: a c:</B>>];
                                3->1[label=<<B>o: [ab] c:</B>>];
                                4->3[label=<<B>o: [cd] c:</B>>];
                                5->4[label=<<B>o: [xy] c:</B>>];
                                6->5[label=<<B>o: [cd] c:</B>>];
                                4->6[label=<<B>o: [cd] c:</B>>];
                            }';
        $dotresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"2"[shape=doublecircle];
    0->1[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
    1->2[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
}';
        $input = qtype_preg_fa::read_fa($dotdescription);
        $input->remove_unreachable_states();
        $inputdesc = $input->fa_to_dot(null, null, true);
        $this->assertEquals($inputdesc, $dotresult, 'Result automata is not equal to expected');
    }

    public function test_cycle_from_start() {
        $dotdescription = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [01] c:</B>>];
                                1->2[label=<<B>o: a c:</B>>];
                                3->1[label=<<B>o: [ab] c:</B>>];
                                4->3[label=<<B>o: [cd] c:</B>>];
                                5->4[label=<<B>o: [xy] c:</B>>];
                                6->5[label=<<B>o: [cd] c:</B>>];
                                4->6[label=<<B>o: [cd] c:</B>>];
                                1->6[label=<<B>o: [cd] c:</B>>];
                            }';
        $dotresult = 'digraph {
    rankdir=LR;
    "0"[shape=rarrow];
"2"[shape=doublecircle];
    0->1[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
    1->6[label = <<B>o: [cd] c:</B>>, color = violet, penwidth = 2];
    1->2[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
    6->5[label = <<B>o: [cd] c:</B>>, color = violet, penwidth = 2];
    4->6[label = <<B>o: [cd] c:</B>>, color = violet, penwidth = 2];
    4->3[label = <<B>o: [cd] c:</B>>, color = violet, penwidth = 2];
    5->4[label = <<B>o: [xy] c:</B>>, color = violet, penwidth = 2];
    3->1[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
}';

        $input = qtype_preg_fa::read_fa($dotdescription);
        $input->remove_unreachable_states();
        $inputdesc = $input->fa_to_dot(null, null, true);
        $this->assertEquals($inputdesc, $dotresult, 'Result automata is not equal to expected');
    }


    // --------------------- Intersect transitions tests ------------------------
    function create_lexer($regex, $options = null) {
        if ($options === null) {
            $options = new qtype_preg_handling_options();
            $options->preserveallnodes = true;
        }
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $lexer->set_options($options);
        return $lexer;
    }

    public function test_characters_diapason_and_single() {
        $lexer = $this->create_lexer('[a-z][cd]');
        $leaf1 = $lexer->nextToken()->value;
        $leaf2 = $lexer->nextToken()->value;
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);  //0->1[label="[a-z]"];
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);  //0->1[label="[cd]"];
        $rescharset = $leaf1->intersect_leafs($leaf2, false, false);
        $restran = new qtype_preg_fa_transition(0, $rescharset, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);
        $resulttran = $transition1->intersect($transition2);

        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_eps() {
        $leaf1 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $leaf2 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);  //0->1[label="[]"];
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);  //0->1[label="[]"];
        $resulttran = $transition1->intersect($transition2);
        $restran = new qtype_preg_fa_transition(0, $leaf1, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_merged_asserts() {
        $lexer = $this->create_lexer('[a][a-c]');
        $leaf1 = $lexer->nextToken()->value;
        $assert1 = new qtype_preg_leaf_assert_circumflex;
        $leaf2 = $lexer->nextToken()->value;
        $assert2 = new qtype_preg_leaf_assert_esc_a;
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);  //0->1[label="[^a]"];
        $transition1->mergedafter[] = new qtype_preg_fa_transition(0, $assert1, 1);
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);  //0->1[label="[\\Aa-c]"];
        $transition1->mergedafter[] = new qtype_preg_fa_transition(0, $assert2, 1);
        $rescharset = $leaf1->intersect_leafs($leaf2, false, false);
        $restran = new qtype_preg_fa_transition(0, $rescharset, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER); //0->1[label="[\\Aa]"];
        $restran->mergedafter[] = new qtype_preg_fa_transition(0, $assert2, 1);
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_ununited_asserts() {
        $lexer = $this->create_lexer('[a]');
        $leaf1 = $lexer->nextToken()->value;
        $leaf2 = $leaf1;
        $assert1 = new qtype_preg_leaf_assert_circumflex;
        $assert2 = new qtype_preg_leaf_assert_dollar;
        $rescharset = $leaf1->intersect_leafs($leaf2, false, false);
        $restran = new qtype_preg_fa_transition(0, $rescharset, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);  //0->1[label="[^$a]"];
        $restran->mergedafter[] = new qtype_preg_fa_transition(0, $assert1, 1);
        $restran->mergedbefore[] = new qtype_preg_fa_transition(0, $assert2, 1);
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);   //0->1[label="[^a]"];
        $transition1->mergedafter[] = new qtype_preg_fa_transition(0, $assert1, 1);
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);   //0->1[label="[$a]"];
        $transition2->mergedbefore[] = new qtype_preg_fa_transition(0, $assert2, 1);
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_assert_and_character() {
        $lexer = $this->create_lexer('[a]');
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_circumflex;
        $transition1 = new qtype_preg_fa_transition(0, $leaf, 1);   //0->1[label="[^a]"];
        $transition1->mergedafter[] = new qtype_preg_fa_transition(0, $assert, 1);
        $transition2 = new qtype_preg_fa_transition(0, $leaf, 1);   //0->1[label="[a]"];
        $rescharset = $leaf->intersect_leafs($leaf, false, false);

        $restran = new qtype_preg_fa_transition(0, $rescharset, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);
        $restran->mergedafter[] = new qtype_preg_fa_transition(0, $assert, 1);
        $resulttran = $transition1->intersect($transition2);        //0->1[label="[^a]"];
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_unmerged_asserts() {
        $leaf1 = new qtype_preg_leaf_assert_circumflex;
        $leaf2 = new qtype_preg_leaf_assert_esc_a;
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);  //0->1[label="[^]"];
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);  //0->1[label="[\\A]"];
        $restran = new qtype_preg_fa_transition(0, $leaf1, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_intersecion_eps_and_assert() {
        $leaf1 = new qtype_preg_leaf_assert_circumflex;
        $leaf2 = $assert = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1);  //0->1[label="[^]"];
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1);  //0->1[label="[]"];
        $restran = new qtype_preg_fa_transition(0, $leaf1, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_INTER);
        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_no_intersecion_tags() {
        $lexer = $this->create_lexer('[a-c][g-k]');
        $leaf1 = $lexer->nextToken()->value;
        $leaf2 = $lexer->nextToken()->value;
        $subpatt1 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $subpatt2 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1); //0->1[label="[(a-c)]"];
        $transition1->subpatt_start[] = $subpatt1;
        $transition1->subpatt_end[] = $subpatt2;
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1); //0->1[label="[(g-k)]"];
        $transition2->subpatt_start[] = $subpatt1;
        $transition2->subpatt_end[] = $subpatt2;
        $restran = null;

        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_no_intersecion_merged() {
        $lexer = $this->create_lexer('[a][01]');
        $leaf1 = $lexer->nextToken()->value;
        $leaf2 = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_circumflex;
        $leaf1->mergedassertions[] = $assert;
        $leaf2->mergedassertions[] = $assert;
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1); //0->1[label="[^a]"];
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1); //0->1[label="[^01]"];
        $restran = null;

        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    public function test_no_intersecion_assert_and_character() {
        $lexer = $this->create_lexer('[a][01]');
        $leaf1 = $lexer->nextToken()->value;
        $leaf2 = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_circumflex;
        $leaf1->mergedassertions[] = $assert;
        $transition1 = new qtype_preg_fa_transition(0, $leaf1, 1); //0->1[label="[^a]"];
        $transition2 = new qtype_preg_fa_transition(0, $leaf2, 1); //0->1[label="[01]"];
        $restran = null;

        $resulttran = $transition1->intersect($transition2);
        $this->assertEquals($restran, $resulttran, 'Result transition is not equal to expected');
    }

    // --------------------- Intersect asserts tests ------------------------

    public function test_with_and_without_assert() {
        $assert1 = new qtype_preg_leaf_assert_circumflex;
        $assert2 = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition1 = new qtype_preg_fa_transition(0, $assert1, 1);
        $transition2 = new qtype_preg_fa_transition(0, $assert2, 1);
        $result = $transition1->intersect_asserts($transition2);
        $resassert = new qtype_preg_leaf_assert_circumflex;
        $resulttran = new qtype_preg_fa_transition(0, $resassert, 1);
        $this->assertEquals($resulttran, $result, 'Result assert is not equal to expected');
    }

    public function test_esc_a_and_circumflex() {
        $assert1 = new qtype_preg_leaf_assert_circumflex;
        $mergedassert1 = new qtype_preg_leaf_assert_circumflex;
        //$assert1->mergedassertions = array($mergedassert1);

        $assert2 = new qtype_preg_leaf_assert_esc_a;
        $mergedassert2 = new qtype_preg_leaf_assert_esc_a;
        //$assert2->mergedassertions = array($assert2);
        $transition1 = new qtype_preg_fa_transition(0, $assert1, 1);
        $transition2 = new qtype_preg_fa_transition(0, $assert2, 1);
        $mergedtransition1 = new qtype_preg_fa_transition(0, $mergedassert1, 1);
        $mergedtransition2 = new qtype_preg_fa_transition(0, $mergedassert2, 1);
        $transition1->mergedafter[] = $mergedtransition1;
        $transition2->mergedafter[] = $mergedtransition2;
        $result = $transition1->intersect_asserts($transition2);
        $this->assertEquals($transition2, $result, 'Result assert is not equal to expected');
    }

    public function test_esc_z_and_dollar() {
        $assert1 = new qtype_preg_leaf_assert_dollar;
        $mergedassert1 = new qtype_preg_leaf_assert_dollar;
        //$assert1->mergedassertions = array($mergedassert1);

        $assert2 = new qtype_preg_leaf_assert_small_esc_z;
        $mergedassert2 = new qtype_preg_leaf_assert_small_esc_z;
        $transition1 = new qtype_preg_fa_transition(0, $assert1, 1);
        $transition2 = new qtype_preg_fa_transition(0, $assert2, 1);
        $mergedtransition1 = new qtype_preg_fa_transition(0, $mergedassert1, 1);
        $mergedtransition2 = new qtype_preg_fa_transition(0, $mergedassert2, 1);
        $transition1->mergedbefore[] = $mergedtransition1;
        $transition2->mergedbefore[] = $mergedtransition2;
        $result = $transition1->intersect_asserts($transition2);
        $this->assertEquals($transition2, $result, 'Result assert is not equal to expected');

        //$this->assertEquals($assert2->mergedassertions, $result->mergedassertions, 'Result array of asserts is not equal to expected');
    }

    public function test_circumflex_and_dollar() {
        $assert1 = new qtype_preg_leaf_assert_circumflex;
        $mergedassert1 = new qtype_preg_leaf_assert_circumflex;
        //$assert1->mergedassertions = array($mergedassert1);

        $assert2 = new qtype_preg_leaf_assert_dollar;
        $mergedassert2 = new qtype_preg_leaf_assert_dollar;
        //$assert1->mergedassertions = array($mergedassert2);

        $transition1 = new qtype_preg_fa_transition(0, $assert1, 1);
        $transition2 = new qtype_preg_fa_transition(0, $assert2, 1);
        $mergedtransition1 = new qtype_preg_fa_transition(0, $mergedassert1, 1);
        $mergedtransition2 = new qtype_preg_fa_transition(0, $mergedassert2, 1);
        $transition1->mergedafter[] = $mergedtransition1;
        $transition2->mergedbefore[] = $mergedtransition2;
        $result = $transition1->intersect_asserts($transition2);

        $assertresult = new qtype_preg_leaf_assert_dollar;
        $resulttran = new qtype_preg_fa_transition(0, $assertresult, 1);
        $resmergedtransition = new qtype_preg_fa_transition(0, $assert1, 1);
        $resulttran->mergedafter[] = $transition1;
        $resulttran->mergedafter[] = $mergedtransition1;
        $resulttran->mergedbefore[] = $mergedtransition2;
        $result = $transition1->intersect_asserts($transition2);
        $this->assertEquals($resulttran, $result, 'Result assert is not equal to expected');
        //$this->assertEquals($assertresult->mergedassertions, $result->mergedassertions, 'Result array of asserts is not equal to expected');
    }

    public function test_esc_b_and_esc_a() {
        $assert1 = new qtype_preg_leaf_assert_esc_b;
        $assert2 = new qtype_preg_leaf_assert_esc_a;
        $transition1 = new qtype_preg_fa_transition(0, $assert1, 1);
        $transition2 = new qtype_preg_fa_transition(0, $assert2, 1);
        $assertresult = new qtype_preg_leaf_assert_esc_a;
        $resulttran = new qtype_preg_fa_transition(0, $assertresult, 1);


        $result = $transition1->intersect_asserts($transition2);
        $this->assertEquals($resulttran, $result, 'Result assert is not equal to expected');
    }

    public function test_assert_with_backref() {
        $assert1 = new qtype_preg_leaf_assert_esc_a;
        $backref = new qtype_preg_leaf_backref();
        $expected = new qtype_preg_fa_transition(0, $backref, 1);
        $backreftran = new qtype_preg_fa_transition(0, clone $backref, 1);
        $transition1 = new qtype_preg_fa_transition(0, $assert1, 1);
        $expected->mergedafter[] = $transition1;

        $result = $transition1->intersect_asserts($backreftran);
        $this->assertEquals($expected, $result, 'Result assert is not equal to expected');

        $result = $backreftran->intersect_asserts($transition1);
        $this->assertEquals($expected, $result, 'Result assert is not equal to expected');
    }

    // --------------------- Intersection part tests ------------------------


    public function test_no_intersection() {
        $dotdescription1 = 'digraph {
                                0;
                                2;4;
                                0->1[label=<<B>o: [a-z] c:</B>>, color = violet];
                                1->2[label=<<B>o: [0-9] c:</B>>, color = violet];
                                1->3[label=<<B>o: [abc] c:</B>>, color = violet];
                                3->4[label=<<B>o: [\\/] c:</B>>, color = violet];
                                4->4[label=<<B>o: [\\[\\]\\(\\)] c:</B>>, color = violet];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                3;4;
                                0->1[label=<<B>o: [01] c:</B>>, color = blue];
                                1->2[label=<<B>o: 4 c:</B>>, color = blue];
                                1->0[label=<<B>o: [,\\.!] c:</B>>, color = blue];
                                2->4[label=<<B>o: [*+] c:</B>>, color = blue];
                                2->3[label=<<B>o: [xy] c:</B>>, color = blue];
                                0->2[label=<<B>o: [-?] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        "0,0";
                        "0,0";
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $direct = qtype_preg_fa::read_fa($dotresult);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $direct->get_state_numbers();
        $startstate = array_search('0,0', $realnumbers);
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 0, false);
        $result = qtype_preg_fa::read_fa($dotresult);
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_end() {
        $dotdescription1 = 'digraph {
                                0;
                                4;
                                0->1[label=<<B>o: [0-9] c:</B>>, color = violet];
                                1->2[label=<<B>o: [abc] c:</B>>, color = violet];
                                1->4[label=<<B>o: [01] c:</B>>, color = violet];
                                2->3[label=<<B>o: [\\-&,] c:</B>>, color = violet];
                                2->2[label=<<B>o: [a-z] c:</B>>, color = violet];
                                3->4[label=<<B>o: a c:</B>>, color = violet];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                3;
                                0->1[label=<<B>o: [01] c:</B>>, color = blue];
                                1->2[label=<<B>o: ? c:</B>>, color = blue];
                                1->3[label=<<B>o: a c:</B>>, color = blue];
                                2->3[label=<<B>o: [01] c:</B>>, color = blue];
                            }';
        $dotdirect = 'digraph {
                            "0,";
                            "2,3";
                            "0,"->"1,"[label=<<B>o: [0-9] c:</B>>];
                            "1,"->"4,"[label=<<B>o: [01] c:</B>>];
                            "3,"->"4,"[label=<<B>o: a] c:</B>>];
                            "2,3"->"3,"[label=<<B>o: [\\-?,] c:</B>>];
                        }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "0,0"[shape=rarrow];
                    "2,3"[shape=doublecircle];
                        "0,"->"1,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "2,3"->"3,"[label = <<B>o: [\\\\-?,] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"4,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "1,"->"4,"[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
                        "2,1"->"2,3"[label = <<B>o: [a-z] ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,3"[label = <<B>o: [abc] ∩ a c:</B>>, color = red, penwidth = 2];
                        "0,0"->"1,1"[label = <<B>o: [0-9] ∩ [01] c:</B>>, color = red, penwidth = 2];
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $direct = qtype_preg_fa::read_fa($dotdirect);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $direct->get_state_numbers();
        $startstate = array(array_search('2,3', $realnumbers));
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 1, false);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_start() {
        $dotdescription1 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [01] c:</B>>, color = violet];
                                1->2[label=<<B>o: [a-z] c:</B>>, color = violet];
                                2->2[label=<<B>o: [a-c] c:</B>>, color = violet];
                                1->1[label=<<B>o: [0-9] c:</B>>, color = violet];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [a-z] c:</B>>, color = blue];
                                1->2[label=<<B>o: [a-c] c:</B>>, color = blue];
                                0->2[label=<<B>o: [0-9] c:</B>>, color = blue];
                                1->1[label=<<B>o: [\\.,] c:</B>>, color = blue];
                                2->2[label=<<B>o: [ab] c:</B>>, color = blue];
                            }';
        $dotdirect = 'digraph {
                            "0,";
                            "1,0";
                            "0,"->"1,0"[label=<<B>o: [01] c:</B>>];
                        }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "2,2"[shape=doublecircle];
                        "0,"->"1,0"[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
                        "1,0"->"1,2"[label = <<B>o: [0-9] ∩ [0-9] c:</B>>, color = red, penwidth = 2];
                        "1,0"->"2,1"[label = <<B>o: [a-z] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "1,2"->"2,2"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,1"->"2,2"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "2,2"->"2,2"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $direct = qtype_preg_fa::read_fa($dotdirect);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $direct->get_state_numbers();
        $startstate = array(array_search('1,0', $realnumbers));
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 0, false);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_cycles() {
        $dotdescription1 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = violet];
                                1->2[label=<<B>o: [ab] c:</B>>, color = violet];
                                2->0[label=<<B>o: [ab] c:</B>>, color = violet];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                0->2[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [ab] c:</B>>, color = blue];
                                2->1[label=<<B>o: [ab] c:</B>>, color = blue];
                            }';
        $dotdirect = 'digraph {
                            "0,";
                            "1,0";
                            "0,"->"1,0"[label=<<B>o: [ab] c:</B>>, color = violet];
                        }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "1,2"[shape=doublecircle];
                    "1,1"[shape=doublecircle];
                        "0,"->"1,0"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,0"->"2,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,0"->"2,1"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,2"->"0,1"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,1"->"0,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "0,1"->"1,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "0,2"->"1,1"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,2"->"2,1"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $direct = qtype_preg_fa::read_fa($dotdirect);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $direct->get_state_numbers();
        $startstate = array(array_search('1,0', $realnumbers));
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 0, false);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_unmerged_eps() {
        $dotdescription1 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: a c:</B>>];
                                1->2[label=<<B>o:0, ε c:0,</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: a c:</B>>, color = blue];
                                1->2[label=<<B>o:1, ε c:1,</B>>, color = blue];
                            }';
        $dotdirect = 'digraph {
                            "2,2";
                            "2,2";
                        }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,0"[shape=rarrow];
                    "2,2"[shape=doublecircle];
                        "1,1"->"2,2"[label = <<B>o:0,1, ε c:0,1,</B>>, color = red, penwidth = 2];
                        "0,0"->"1,1"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $direct = qtype_preg_fa::read_fa($dotdirect);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $direct->get_state_numbers();
        $startstate = array(array_search('2,2', $realnumbers));
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 1, false);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_merged_asserts() {
        $dotdescription1 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: a c:</B><BR/>o: ^ c:>];
                                1->2[label=<o: $ c:<BR/><B>o: b c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: a c:</B><BR/>o: ^ c:>, color = blue];
                                1->2[label=<o: $ c:<BR/><B>o: b c:</B>>, color = blue];
                            }';
        $dotdirect = 'digraph {
                            "2,2";
                            "2,2";
                        }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,0"[shape=rarrow];
                    "2,2"[shape=doublecircle];
                        "1,1"->"2,2"[label = <o: $ c:(1,2)<BR/><B>o: b ∩ b c:</B>>, color = red, penwidth = 2];
                        "0,0"->"1,1"[label = <<B>o: a ∩ a c:</B><BR/>o: ^ c:(0,1)>, color = red, penwidth = 2];
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $direct = qtype_preg_fa::read_fa($dotdirect);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $direct->get_state_numbers();
        $startstate = array(array_search('2,2', $realnumbers));
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 1, false);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    // ???
    public function test_intersection_with_unmerged_asserts() {
        $dotdescription1 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: ^ c:</B>>];
                                1->2[label=<<B>o:0, $ c:0,</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: a c:</B><BR/>o: ^ c:>, color = blue];
                                1->2[label=<<B>o: b c:</B>>, color = blue];
                            }';
        $dotdirect = 'digraph {
                            "0,2";
                            "0,2";
                        }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,2"[shape=rarrow];
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $direct = qtype_preg_fa::read_fa($dotdirect);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $direct->get_state_numbers();
        $startstate = array(array_search('0,2', $realnumbers));
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 1, false);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_cycle() {
        $dotdescription1 = 'digraph {
                                0;
                                5;
                                0->1[label=<<B>o: [01] c:</B>>];
                                1->2[label=<<B>o: [a-k] c:</B>>];
                                1->3[label=<<B>o: [0-9] c:</B>>];
                                2->4[label=<<B>o: [a-c] c:</B>>];
                                3->4[label=<<B>o: [ab] c:</B>>];
                                4->5[label=<<B>o: [xy] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [c-n] c:</B>>, color = blue];
                                1->1[label=<<B>o: [a-z] c:</B>>, color = blue];
                                1->2[label=<<B>o: [a-z] c:</B>>, color = blue];
                            }';
        $dotdirect = 'digraph {
                            "0,";
                            "1,0";
                            "0,"->"1,0"[label=<<B>o: [01] c:</B>>];
                        }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "5,2"[shape=doublecircle];
                    "5,1"[shape=doublecircle];
                        "0,"->"1,0"[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
                        "1,0"->"2,1"[label = <<B>o: [a-k] ∩ [c-n] c:</B>>, color = red, penwidth = 2];
                        "2,1"->"4,2"[label = <<B>o: [a-c] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "2,1"->"4,1"[label = <<B>o: [a-c] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "4,1"->"5,2"[label = <<B>o: [xy] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "4,1"->"5,1"[label = <<B>o: [xy] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $direct = qtype_preg_fa::read_fa($dotdirect);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $direct->get_state_numbers();
        $startstate = array(array_search('1,0', $realnumbers));
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 0, false);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_implicit_cycle() {
        $dotdescription1 = 'digraph {
                                0;
                                4;
                                0->1[label=<<B>o: [01] c:</B>>];
                                1->2[label=<<B>o: [c-k] c:</B>>];
                                2->3[label=<<B>o: [a-c] c:</B>>];
                                3->4[label=<<B>o: [ab] c:</B>>];
                                3->2[label=<<B>o: [xy] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                3;
                                0->1[label=<<B>o: [cd] c:</B>>, color = blue];
                                1->2[label=<<B>o: a c:</B>>, color = blue];
                                2->3[label=<<B>o: [ab] c:</B>>, color = blue];
                                2->1[label=<<B>o: [xy] c:</B>>, color = blue];
                            }';
        $dotdirect = 'digraph {
                            "0,";
                            "1,0";
                            "0,"->"1,0"[label=<<B>o: [01] c:</B>>];
                        }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "4,3"[shape=doublecircle];
                        "0,"->"1,0"[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
                        "1,0"->"2,1"[label = <<B>o: [c-k] ∩ [cd] c:</B>>, color = red, penwidth = 2];
                        "2,1"->"3,2"[label = <<B>o: [a-c] ∩ a c:</B>>, color = red, penwidth = 2];
                        "3,2"->"2,1"[label = <<B>o: [xy] ∩ [xy] c:</B>>, color = red, penwidth = 2];
                        "3,2"->"4,3"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $direct = qtype_preg_fa::read_fa($dotdirect);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $direct->get_state_numbers();
        $startstate = array(array_search('1,0', $realnumbers));
        $resultautomata = $firstautomata->get_intersection_part($secondautomata, $direct, $startstate, 0, false);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }


    // --------------------- Intersection automata tests ------------------------

    public function test_inter_no_intersection() {
        $dotdescription1 = 'digraph {
                                0;
                                2;4;
                                0->1[label=<<B>o: [a-z] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                1->3[label=<<B>o: [abc] c:</B>>];
                                3->4[label=<<B>o: [\\/] c:</B>>];
                                4->4[label=<<B>o: [\\[\\]\\(\\)] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                3;4;
                                0->1[label=<<B>o: [01] c:</B>>, color = blue];
                                1->2[label=<<B>o: 4 c:</B>>, color = blue];
                                1->0[label=<<B>o: [,\\.!] c:</B>>, color = blue];
                                2->4[label=<<B>o: [*+] c:</B>>, color = blue];
                                2->3[label=<<B>o: [xy] c:</B>>, color = blue];
                                0->2[label=<<B>o: [-?] c:</B>>, color = blue];
                            }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('0', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, array($stateforinter), 0);
        $result = new qtype_preg_fa();
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_intersection_with_end() {
        $dotdescription1 = 'digraph {
                                0;
                                4;
                                0->1[label=<<B>o: [0-9] c:</B>>];
                                1->2[label=<<B>o: [abc] c:</B>>];
                                1->4[label=<<B>o: [01] c:</B>>];
                                2->3[label=<<B>o: [\\-\\&,] c:</B>>];
                                2->2[label=<<B>o: [a-z] c:</B>>];
                                3->4[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                3;
                                0->1[label=<<B>o: [01] c:</B>>, color = blue];
                                1->2[label=<<B>o: ? c:</B>>, color = blue];
                                1->3[label=<<B>o: [abc] c:</B>>, color = blue];
                                2->3[label=<<B>o: [01] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "0,0"[shape=rarrow];
                    "4,"[shape=doublecircle];
                        "3,"->"4,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "1,"->"4,"[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
                        "2,3"->"3,"[label = <<B>o: [\\\\-\\\\&,] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "2,1"->"2,3"[label = <<B>o: [a-z] ∩ [abc] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,3"[label = <<B>o: [abc] ∩ [abc] c:</B>>, color = red, penwidth = 2];
                        "0,0"->"1,1"[label = <<B>o: [0-9] ∩ [01] c:</B>>, color = red, penwidth = 2];
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('2', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, array($stateforinter), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_intersection_with_cycles() {
        $dotdescription1 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [01] c:</B>>];
                                1->2[label=<<B>o: [a-z] c:</B>>];
                                1->1[label=<<B>o: [0-9] c:</B>>];
                                2->2[label=<<B>o: [a-c] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [a-z] c:</B>>, color = blue];
                                1->2[label=<<B>o: [a-c] c:</B>>, color = blue];
                                0->2[label=<<B>o: [0-9] c:</B>>, color = blue];
                                1->1[label=<<B>o: [\\.,] c:</B>>, color = blue];
                                2->2[label=<<B>o: [ab] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "2,2"[shape=doublecircle];
                        "0,"->"1,0"[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
                        "1,0"->"1,2"[label = <<B>o: [0-9] ∩ [0-9] c:</B>>, color = red, penwidth = 2];
                        "1,0"->"2,1"[label = <<B>o: [a-z] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "1,2"->"2,2"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,1"->"2,2"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "2,2"->"2,2"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('1', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, array($stateforinter), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_implicent_cycles() {
        $dotdescription1 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>];
                                1->2[label=<<B>o: [ab] c:</B>>];
                                2->0[label=<<B>o: [ab] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                0->2[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [ab] c:</B>>, color = blue];
                                2->1[label=<<B>o: [ab] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "2,2"[shape=doublecircle];
                        "0,"->"1,0"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,0"->"2,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,0"->"2,1"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,2"->"0,1"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,1"->"0,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "0,1"->"1,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "0,2"->"1,1"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,2"->"2,1"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('1', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, array($stateforinter), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_no_way_to_end_state() {
        $dotdescription1 = 'digraph {
                                0;
                                4;
                                0->1[label=<<B>o: [xy] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                2->3[label=<<B>o: [a-z] c:</B>>];
                                3->4[label=<<B>o: [xy] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                3;
                                0->1[label=<<B>o: [01] c:</B>>, color = blue];
                                1->2[label=<<B>o: [a-z] c:</B>>, color = blue];
                                2->3[label=<<B>o: [\\.,-] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph res {
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('1', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, array($stateforinter), 0);
        $result = new qtype_preg_fa();
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_no_way_to_start_state() {
        $dotdescription1 = 'digraph {
                                0;
                                4;
                                0->1[label=<<B>o: [xy] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                2->3[label=<<B>o: [a-z] c:</B>>];
                                3->4[label=<<B>o: [xy] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                3;
                                0->1[label=<<B>o: [01] c:</B>>, color = blue];
                                1->2[label=<<B>o: [0-9] c:</B>>, color = blue];
                                2->3[label=<<B>o: [a-z] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph res {
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('3', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, array($stateforinter), 1);
        $result = new qtype_preg_fa();
        $this->assertEquals($resultautomata, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_with_branches_from_first_and_second() {
        $dotdescription1 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [a-z] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [0-9] c:</B>>, color = blue];
                                1->2[label=<<B>o: [a-z] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    ",2"[shape=doublecircle];
                        "0,"->"1,0"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "1,0"->"2,1"[label = <<B>o: [0-9] ∩ [0-9] c:</B>>, color = red, penwidth = 2];
                        "2,1"->",2"[label = <<B>o: [a-z] c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('1', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, array($stateforinter), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_intersection_with_cycle() {
        $dotdescription1 = 'digraph {
                                0;
                                4;
                                0->1[label=<<B>o: [0-9] c:</B>>];
                                1->2[label=<<B>o: [abc0-9] c:</B>>];
                                1->4[label=<<B>o: [01] c:</B>>];
                                2->3[label=<<B>o: [\\-?,] c:</B>>];
                                2->2[label=<<B>o: [a-z] c:</B>>];
                                3->4[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                3;
                                0->1[label=<<B>o: [01] c:</B>>, color = blue];
                                1->2[label=<<B>o: ? c:</B>>, color = blue];
                                1->3[label=<<B>o: [ab] c:</B>>, color = blue];
                                2->3[label=<<B>o: [<>] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "0,0"[shape=rarrow];
                    "4,"[shape=doublecircle];
                        "3,"->"4,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "1,"->"4,"[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
                        "2,3"->"3,"[label = <<B>o: [\\\\-?,] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,0"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "2,1"->"2,3"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,3"[label = <<B>o: [abc0-9] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,0"->"2,1"[label = <<B>o: [abc0-9] ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "0,0"->"1,1"[label = <<B>o: [0-9] ∩ [01] c:</B>>, color = red, penwidth = 2];
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('2', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, array($stateforinter), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_divarication_into_cycle() {
        $dotdescription1 = 'digraph {
                                0;
                                4;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                0->3[label=<<B>o: [01] c:</B>>];
                                3->4[label=<<B>o: y c:</B>>];
                                2->4[label=<<B>o: [a-f] c:</B>>];
                                4->0[label=<<B>o: [bc] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                4;
                                0->1[label=<<B>o: [c-z] c:</B>>, color = blue];
                                1->2[label=<<B>o: [ab] c:</B>>, color = blue];
                                2->3[label=<<B>o: [0-9] c:</B>>, color = blue];
                                3->4[label=<<B>o: [x-z] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "4,"[shape=doublecircle];
                    "4,4   1"[shape=doublecircle];
                        "0,"->"3,"[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"4,"[label = <<B>o: y c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,0"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "4,"->"0,"[label = <<B>o: [bc] c:</B>>, color = violet, penwidth = 2];
                        "2,0"->"4,1"[label = <<B>o: [a-f] ∩ [c-z] c:</B>>, color = red, penwidth = 2];
                        "4,1"->"0,2"[label = <<B>o: [bc] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "0,2"->"3,3"[label = <<B>o: [01] ∩ [0-9] c:</B>>, color = red, penwidth = 2];
                        "3,3"->"4,4   1"[label = <<B>o: y ∩ [x-z] c:</B>>, color = red, penwidth = 2];
                        "4,4   1"->"0,2"[label = <<B>o: [bc] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('2', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, array($stateforinter), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_intersection_big_cycle() {
        $dotdescription1 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>];
                                1->2[label=<<B>o: [ab] c:</B>>];
                                2->0[label=<<B>o: [ab] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                0->2[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [ab] c:</B>>, color = blue];
                                2->1[label=<<B>o: [ab] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,0"[shape=rarrow];
                    "0,"[shape=rarrow];
                    "2,"[shape=doublecircle];
                    "2,2"[shape=doublecircle];
                        "2,"->"0,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,2"->"2,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,2"->"2,1"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "0,0"->"1,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "0,0"->"1,1"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "0,1"->"1,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,0"->"0,1"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,0"->"0,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,2"->"0,1"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,0"->"2,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,0"->"2,1"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "0,2"->"1,1"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,1"->"0,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,0"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,0"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                    }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();
        $realnumbers = $firstautomata->get_state_numbers();
        $stateforinter = array_search('1', $realnumbers);
        $resultautomata = $firstautomata->intersect_fa($secondautomata, array($stateforinter), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $search = '
                    ';
        $replace = "\n";
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }


    // --------------------- Intersect fa tests ------------------------

    public function test_nessesary_merging() {
        $dotdescription1 = 'digraph {
                                0;
                                4;5;
                                0->2[label=<<B>o: [0-9] c:</B>>];
                                0->3[label=<o: ε c:<BR/><B>o: [a-c] c:</B>>];
                                2->4[label=<<B>o: . c:</B>>];
                                3->4[label=<<B>o: . c:</B>>];
                                2->5[label=<<B>o: [01] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                3;
                                0->1[label=<<B>o: [01] c:</B>>, color = blue];
                                0->3[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->1[label=<<B>o: [<>] c:</B>>, color = blue];
                                1->3[label=<<B>o: [xy] c:</B>>, color = blue];
                                2->3[label=<o: ε c:<BR/><B>o: [cd] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "4,"[shape=doublecircle];
                        "0,"->"3,"[label = <o: ε c:(0,1)<BR/><B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"2,0"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"4,"[label = <<B>o: . c:</B>>, color = violet, penwidth = 2];
                        "2,0"->"5,1"[label = <<B>o: [01] ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "2,0"->"4,3"[label = <<B>o: . ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,0"->"4,1"[label = <<B>o: . ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "5,1"->",3"[label = <<B>o: [xy] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        "5,1"->",1"[label = <<B>o: [<>] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        "4,3"->"4,"[label = <<B>o: ε c:</B>>, color = red, penwidth = 2];
                        "4,1"->",3"[label = <<B>o: [xy] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        "4,1"->",1"[label = <<B>o: [<>] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",3"->"4,"[label = <<B>o: ε c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->",3"[label = <<B>o: [xy] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->",1"[label = <<B>o: [<>] c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('2'), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_with_blind() {
        $dotdescription1 = 'digraph {
                                0;
                                4;
                                0->1[label=<<B>o: [0-9] c:</B>>];
                                1->2[label=<<B>o: [a-c] c:</B>>];
                                1->4[label=<<B>o: [01] c:</B>>];
                                2->3[label=<<B>o: [\\-\\\\&,] c:</B>>];
                                2->2[label=<<B>o: [a-z] c:</B>>];
                                3->4[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                3;
                                0->1[label=<<B>o: [01] c:</B>>, color = blue];
                                1->2[label=<<B>o: ? c:</B>>, color = blue];
                                1->3[label=<<B>o: [ab] c:</B>>, color = blue];
                                2->3[label=<<B>o: [01] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "0,0"[shape=rarrow];
                    "4,"[shape=doublecircle];
                        "3,"->"4,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "1,"->"4,"[label = <<B>o: [01] c:</B>>, color = violet, penwidth = 2];
                        "2,3"->"3,"[label = <<B>o: [\\\\-\\\\\\\\&,] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "1,1"->"2,3"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "0,0"->"1,1"[label = <<B>o: [0-9] ∩ [01] c:</B>>, color = red, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('2'), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_back_with_changing_state_for_inter() {
        $dotdescription1 = 'digraph {
                                0;
                                5;6;
                                0->1[label=<<B>o: a c:</B>>];
                                1->2[label=<<B>o: a c:</B>>];
                                0->3[label=<<B>o: a c:</B><BR/>o: ^ c:>];
                                2->1[label=<<B>o: a c:</B>>];
                                2->4[label=<<B>o: a c:</B>>];
                                3->4[label=<<B>o: a c:</B>>];
                                4->5[label=<<B>o: a c:</B>>];
                                4->6[label=<<B>o: a c:</B>>];
                                5->5[label=<<B>o: a c:</B>>];
                                5->6[label=<<B>o: a c:</B>>];
                                6->6[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                5;
                                0->2[label=<<B>o: a c:</B>>, color = blue];
                                0->3[label=<<B>o: a c:</B>>, color = blue];
                                2->4[label=<<B>o: a c:</B>>, color = blue];
                                2->3[label=<<B>o: a c:</B>>, color = blue];
                                3->5[label=<<B>o: a c:</B>>, color = blue];
                                4->3[label=<<B>o: a c:</B>>, color = blue];
                                4->5[label=<<B>o: a c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "6,"[shape=doublecircle];
                        "0,"->"3,0"[label = <<B>o: a c:</B><BR/>o: ^ c:(0,1)>, color = violet, penwidth = 2];
                        "0,"->"1,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "3,0"->"4,3"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "3,0"->"4,2"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "2,"->"4,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "2,"->"1,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "4,"->"6,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "4,"->"5,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "6,"->"6,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "5,"->"6,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "5,"->"6,"[label = <<B>o: ε c:</B>>, color = violet, penwidth = 2];
                        "5,"->"5,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "4,3"->"6,5"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,3"->"5,5"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,2"->"6,3"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,2"->"6,4"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,2"->"5,3"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,2"->"5,4"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "6,5"->"6,"[label = <<B>o: ε c:</B>>, color = red, penwidth = 2];
                        "5,5"->"6,"[label = <<B>o: ε c:</B>>, color = red, penwidth = 2];
                        "6,3"->"6,5"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "6,4"->"6,5"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "6,4"->"6,3"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "5,3"->"6,5"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "5,3"->"5,5"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "5,4"->"6,5"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "5,4"->"6,3"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "5,4"->"5,5"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "5,4"->"5,3"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('3'), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_simple() {
        $dotdescription1 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: b c:</B>>];
                                1->2[label=<<B>o: a c:</B>>];
                                1->1[label=<<B>o: ε c:</B>>];
                                0->2[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [ab] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "2,"[shape=doublecircle];
                        "0,"->"2,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,0"[label = <<B>o: b c:</B>>, color = violet, penwidth = 2];
                        "1,0"->"2,1"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,1"->",2"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->"2,"[label = <<B>o: ε c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('1'), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_branches() {
        $dotdescription1 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: b c:</B>>];
                                1->2[label=<<B>o: a c:</B>>];
                                0->2[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [ab] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,0"[shape=rarrow];
                    "2,2"[shape=doublecircle];
                        "0,0"->"2,1"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "0,0"->"1,1"[label = <<B>o: b ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,1"->",2"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        "1,1"->"2,2"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        ",2"->"2,2"[label = <<B>o: ε c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('0'), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_eps_cycle() {
        $dotdescription1 = 'digraph {
                                0;
                                3;
                                0->1[label=<<B>o: b c:</B>>];
                                1->1[label=<<B>o: ε c:</B>>];
                                1->3[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [ab] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,0"[shape=rarrow];
                    "3,2"[shape=doublecircle];
                        "0,0"->"1,1"[label = <<B>o: b ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"3,2"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('0'), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_assert_cycle() {
        $dotdescription1 = 'digraph {
                                0;
                                3;
                                0->1[label=<<B>o: b c:</B><BR/>o: ^ c:>];
                                1->1[label=<<B>o: ^ c:</B>>];
                                1->3[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [ab] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,0"[shape=rarrow];
                    "3,2"[shape=doublecircle];
                        "0,0"->"1,1"[label = <<B>o: b ∩ [ab] c:</B><BR/>o: ^ c:(0,1)>, color = red, penwidth = 2];
                        "1,1"->"3,2"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('0'), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_start_implicent_cycle() {
        $dotdescription1 = 'digraph {
                                0;
                                5;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                2->3[label=<<B>o: a c:</B>>];
                                3->4[label=<<B>o: [a-z] c:</B>>];
                                4->5[label=<<B>o: [a-z] c:</B>>];
                                4->1[label=<<B>o: m c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [01] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,0"[shape=rarrow];
                    "5,"[shape=doublecircle];
                        "0,0"->"1,1"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,2"[label = <<B>o: [0-9] ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "2,2"->"3,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "3,"->"4,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "4,"->"1,"[label = <<B>o: m c:</B>>, color = violet, penwidth = 2];
                        "4,"->"5,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"3,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('0'), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_three_time_in_cycle() {
        $dotdescription1 = 'digraph {
                                0;
                                5;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: a c:</B>>];
                                2->3[label=<<B>o: a c:</B>>];
                                3->4[label=<<B>o: [a-z] c:</B>>];
                                4->5[label=<<B>o: [a-z] c:</B>>];
                                4->1[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                9;
                                0->1[label=<<B>o: a c:</B>>, color = blue];
                                1->2[label=<<B>o: a c:</B>>, color = blue];
                                2->3[label=<<B>o: a c:</B>>, color = blue];
                                3->4[label=<<B>o: a c:</B>>, color = blue];
                                4->5[label=<<B>o: [ab] c:</B>>, color = blue];
                                5->6[label=<<B>o: a c:</B>>, color = blue];
                                6->7[label=<<B>o: a c:</B>>, color = blue];
                                7->8[label=<<B>o: a c:</B>>, color = blue];
                                8->9[label=<<B>o: a c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        ",0"[shape=rarrow];
                    "5,"[shape=doublecircle];
                        "4,9"->"5,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "3,8"->"4,9"[label = <<B>o: [a-z] ∩ a c:</B>>, color = red, penwidth = 2];
                        "2,7"->"3,8"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,6"->"2,7"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "0,5"->"1,6"[label = <<B>o: [a-c] ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,5   9"->"1,6"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "3,4   8"->"4,5   9"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,3   7"->"3,4   8"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,2   6"->"2,3   7"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,2   6"->"(2,3   7)"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "0,1"->"1,2   6"[label = <<B>o: [a-c] ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,1   5   9"->"1,2   6"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "3,0   4   8"->"4,1   5   9"[label = <<B>o: [a-z] ∩ a c:</B>>, color = red, penwidth = 2];
                        "(2,3   7)"->"3,0   4   8"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        ",4"->"0,5"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",3"->",4"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->",3"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->",2"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->",1"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->"0,1"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('4'), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_two_time_in_cycle() {
        $dotdescription1 = 'digraph {
                                0;
                                5;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                2->3[label=<<B>o: a c:</B>>];
                                3->4[label=<<B>o: [a-z] c:</B>>];
                                4->5[label=<<B>o: [a-z] c:</B>>];
                                4->1[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                8;
                                0->1[label=<<B>o: . c:</B>>, color = blue];
                                1->2[label=<<B>o: . c:</B>>, color = blue];
                                2->3[label=<<B>o: . c:</B>>, color = blue];
                                3->4[label=<<B>o: . c:</B>>, color = blue];
                                4->5[label=<<B>o: . c:</B>>, color = blue];
                                5->6[label=<<B>o: . c:</B>>, color = blue];
                                6->7[label=<<B>o: . c:</B>>, color = blue];
                                7->8[label=<<B>o: . c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        ",0"[shape=rarrow];
                    "5,8"[shape=doublecircle];
                        "4,7"->"5,8"[label = <<B>o: [a-z] ∩ . c:</B>>, color = red, penwidth = 2];
                        "3,6"->"4,7"[label = <<B>o: [a-z] ∩ . c:</B>>, color = red, penwidth = 2];
                        "2,5"->"3,6"[label = <<B>o: a ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,4"->"2,5"[label = <<B>o: [0-9] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,4"->"1,0   4"[label = <<B>o: ε c:</B>>, color = red, penwidth = 2];
                        "0,3"->"1,4"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,3   7"->"1,4"[label = <<B>o: a ∩ . c:</B>>, color = red, penwidth = 2];
                        "3,2   6"->"4,3   7"[label = <<B>o: [a-z] ∩ . c:</B>>, color = red, penwidth = 2];
                        "2,1   5"->"3,2   6"[label = <<B>o: a ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,0   4"->"2,1   5"[label = <<B>o: [0-9] ∩ . c:</B>>, color = red, penwidth = 2];
                        ",2"->"0,3"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->",2"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->",1"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('5'), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_res_no_intersection() {
        $dotdescription1 = 'digraph {
                                0;
                                5;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                2->3[label=<<B>o: a c:</B>>];
                                3->4[label=<<B>o: [a-z] c:</B>>];
                                4->5[label=<<B>o: [a-z] c:</B>>];
                                4->1[label=<<B>o: m c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [01] c:</B>>, color = blue];
                            }';

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('2'), 0);
        $result = new qtype_preg_fa();
        $this->assertEquals($result, $resultautomata, 'Result automata is not equal to expected');
    }

    public function test_implicent_cycle_not_start() {
        $dotdescription1 = 'digraph {
                                0;
                                5;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                2->3[label=<<B>o: a c:</B>>];
                                3->4[label=<<B>o: [a-z] c:</B>>];
                                4->5[label=<<B>o: [a-z] c:</B>>];
                                4->1[label=<<B>o: m c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [a-k] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "5,"[shape=doublecircle];
                        "0,"->"1,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,0"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "2,0"->"3,1"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "3,1"->"4,2"[label = <<B>o: [a-z] ∩ [a-k] c:</B>>, color = red, penwidth = 2];
                        "4,2"->"1,"[label = <<B>o: m c:</B>>, color = violet, penwidth = 2];
                        "4,2"->"5,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"3,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "3,"->"4,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "4,"->"1,"[label = <<B>o: m c:</B>>, color = violet, penwidth = 2];
                        "4,"->"5,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('2'), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_implicent_cycle_in_branch() {
        $dotdescription1 = 'digraph {
                                0;
                                7;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                2->4[label=<<B>o: a c:</B>>];
                                1->3[label=<<B>o: [a-z] c:</B>>];
                                3->5[label=<<B>o: [012] c:</B>>];
                                4->6[label=<<B>o: [a-z] c:</B>>];
                                4->1[label=<<B>o: m c:</B>>];
                                5->6[label=<<B>o: [+=] c:</B>>];
                                6->1[label=<<B>o: [a-s] c:</B>>];
                                6->7[label=<<B>o: [a-c] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [01] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,0"[shape=rarrow];
                    "7,"[shape=doublecircle];
                        "0,0"->"1,1"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,2"[label = <<B>o: [0-9] ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "2,2"->"4,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "4,"->"1,"[label = <<B>o: m c:</B>>, color = violet, penwidth = 2];
                        "4,"->"6,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"3,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "6,"->"7,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "6,"->"1,"[label = <<B>o: [a-s] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"5,"[label = <<B>o: [012] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"4,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "5,"->"6,"[label = <<B>o: [+=] c:</B>>, color = violet, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('0'), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_cycle_three_times_back() {
        $dotdescription1 = 'digraph {
                                0;
                                5;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: a c:</B>>];
                                2->3[label=<<B>o: a c:</B>>];
                                3->4[label=<<B>o: [a-z] c:</B>>];
                                4->5[label=<<B>o: [a-z] c:</B>>];
                                4->1[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                5;
                                0->1[label=<<B>o: a c:</B>>, color = blue];
                                1->2[label=<<B>o: a c:</B>>, color = blue];
                                2->3[label=<<B>o: a c:</B>>, color = blue];
                                3->4[label=<<B>o: a c:</B>>, color = blue];
                                4->5[label=<<B>o: [ab] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        ",0"[shape=rarrow];
                    "5,"[shape=doublecircle];
                        "4,5"->"5,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "3,4"->"4,5"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,3"->"3,4"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,2"->"2,3"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,2"->"(2,3)"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "0,1"->"1,2"[label = <<B>o: [a-c] ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,1   5"->"1,2"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "3,0   4"->"4,1   5"[label = <<B>o: [a-z] ∩ a c:</B>>, color = red, penwidth = 2];
                        "(2,3)"->"3,0   4"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        ",0"->"0,1"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('4'), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_cycle_in_branch() {
        $dotdescription1 = 'digraph {
                                0;
                                7;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                2->4[label=<<B>o: a c:</B>>];
                                1->3[label=<<B>o: [a-z] c:</B>>];
                                3->5[label=<<B>o: [012] c:</B>>];
                                4->6[label=<<B>o: 0 c:</B>>];
                                4->1[label=<<B>o: m c:</B>>];
                                5->6[label=<<B>o: [+=] c:</B>>];
                                6->1[label=<<B>o: [a-s] c:</B>>];
                                6->7[label=<<B>o: [a-c] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [01] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "7,"[shape=doublecircle];
                        "0,"->"1,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"3,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,0"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"5,"[label = <<B>o: [012] c:</B>>, color = violet, penwidth = 2];
                        "2,0"->"4,1"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "5,"->"6,"[label = <<B>o: [+=] c:</B>>, color = violet, penwidth = 2];
                        "6,"->"7,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "6,"->"1,"[label = <<B>o: [a-s] c:</B>>, color = violet, penwidth = 2];
                        "4,1"->"6,2"[label = <<B>o: 0 ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "6,2"->"7,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "6,2"->"1,"[label = <<B>o: [a-s] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"4,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "4,"->"6,"[label = <<B>o: 0 c:</B>>, color = violet, penwidth = 2];
                        "4,"->"1,"[label = <<B>o: m c:</B>>, color = violet, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('2'), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_two_branches_back() {
        $dotdescription1 = 'digraph {
                                0;
                                7;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                2->4[label=<<B>o: a c:</B>>];
                                1->3[label=<<B>o: [a-z] c:</B>>];
                                3->5[label=<<B>o: [a-z] c:</B>>];
                                4->6[label=<<B>o: 0 c:</B>>];
                                4->1[label=<<B>o: m c:</B>>];
                                5->6[label=<<B>o: [0-9] c:</B>>];
                                6->1[label=<<B>o: [a-s] c:</B>>];
                                6->7[label=<<B>o: [a-c] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [01] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "7,"[shape=doublecircle];
                        "6,2"->"7,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "5,1"->"6,2"[label = <<B>o: [0-9] ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "4,1"->"6,2"[label = <<B>o: 0 ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "3,0"->"5,1"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,0"->"4,1"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"3,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"3,0"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,0"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "6,"->"7,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "6,"->"1,"[label = <<B>o: [a-s] c:</B>>, color = violet, penwidth = 2];
                        "4,"->"1,"[label = <<B>o: m c:</B>>, color = violet, penwidth = 2];
                        "4,"->"6,"[label = <<B>o: 0 c:</B>>, color = violet, penwidth = 2];
                        "5,"->"6,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"4,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "3,"->"5,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('6'), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_two_branches_asserts_back() {
        $dotdescription1 = 'digraph {
                                0;
                                7;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                2->4[label=<<B>o: a c:</B>>];
                                1->3[label=<<B>o: [a-z] c:</B>>];
                                3->5[label=<<B>o: [a-z] c:</B>>];
                                4->6[label=<<B>o: 0 c:</B>>];
                                4->1[label=<<B>o: m c:</B>>];
                                5->6[label=<<B>o: [0-9] c:</B>>];
                                5->1[label=<<B>o: [0-9] c:</B><BR/>o: ^ c:>];
                                6->7[label=<<B>o: [a-c] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [01] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "7,"[shape=doublecircle];
                        "6,2"->"7,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "5,1"->"6,2"[label = <<B>o: [0-9] ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "4,1"->"6,2"[label = <<B>o: 0 ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "3,0"->"5,1"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,0"->"4,1"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,"->"3,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"3,0"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,0"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "5,"->"1,"[label = <<B>o: [0-9] c:</B><BR/>o: ^ c:(8,6)>, color = violet, penwidth = 2];
                        "4,"->"1,"[label = <<B>o: m c:</B>>, color = violet, penwidth = 2];
                        "3,"->"5,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"4,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('6'), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_two_cycles() {
        $dotdescription1 = 'digraph {
                                0;
                                7;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                2->4[label=<<B>o: a c:</B>>];
                                1->3[label=<<B>o: [012] c:</B>>];
                                3->5[label=<<B>o: [+=] c:</B>>];
                                4->6[label=<<B>o: 0 c:</B>>];
                                4->1[label=<<B>o: m c:</B>>];
                                5->6[label=<<B>o: [0-9] c:</B>>];
                                6->1[label=<<B>o: [a-s] c:</B>>];
                                6->7[label=<<B>o: [a-c] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                3;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [01] c:</B>>, color = blue];
                                2->3[label=<<B>o: [a-z] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,0"[shape=rarrow];
                    "7,"[shape=doublecircle];
                        "0,0"->"1,1"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,2"[label = <<B>o: [0-9] ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "2,2"->"4,3"[label = <<B>o: a ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "4,3"->"1,"[label = <<B>o: m c:</B>>, color = violet, penwidth = 2];
                        "4,3"->"6,"[label = <<B>o: 0 c:</B>>, color = violet, penwidth = 2];
                        "1,"->"3,"[label = <<B>o: [012] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "6,"->"7,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "6,"->"1,"[label = <<B>o: [a-s] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"5,"[label = <<B>o: [+=] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"4,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "5,"->"6,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "4,"->"6,"[label = <<B>o: 0 c:</B>>, color = violet, penwidth = 2];
                        "4,"->"1,"[label = <<B>o: m c:</B>>, color = violet, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('0'), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_two_start_states() {
        $dotdescription1 = 'digraph {
                                0;
                                7;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                2->4[label=<<B>o: a c:</B>>];
                                1->3[label=<<B>o: [012] c:</B>>];
                                3->5[label=<<B>o: [+=] c:</B>>];
                                4->6[label=<<B>o: 0 c:</B>>];
                                4->1[label=<<B>o: m c:</B>>];
                                5->7[label=<<B>o: [0-9] c:</B>>];
                                7->1[label=<<B>o: [a-s] c:</B>>];
                                6->7[label=<<B>o: [a-c] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                3;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [01] c:</B>>, color = blue];
                                2->3[label=<<B>o: [a-z] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "0,0"[shape=rarrow];
                    "7,"[shape=doublecircle];
                        "7,"->"1,"[label = <<B>o: [a-s] c:</B>>, color = violet, penwidth = 2];
                        "6,"->"7,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "6,"->"7,0"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "5,"->"7,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "5,"->"7,0"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "4,3"->"6,"[label = <<B>o: 0 c:</B>>, color = violet, penwidth = 2];
                        "4,3"->"1,"[label = <<B>o: m c:</B>>, color = violet, penwidth = 2];
                        "3,"->"5,"[label = <<B>o: [+=] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"3,"[label = <<B>o: [012] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "2,2"->"4,3"[label = <<B>o: a ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,2"[label = <<B>o: [0-9] ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "0,0"->"1,1"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "7,0"->"1,1"[label = <<B>o: [a-s] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "4,"->"1,"[label = <<B>o: m c:</B>>, color = violet, penwidth = 2];
                        "4,"->"6,"[label = <<B>o: 0 c:</B>>, color = violet, penwidth = 2];
                        "2,"->"4,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array(4), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_full_inter_in_branch() {
        $dotdescription1 = 'digraph {
                                0;
                                6;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [a-c] c:</B>>];
                                2->3[label=<<B>o: [0-5as] c:</B>>];
                                3->4[label=<<B>o: [+=] c:</B>>];
                                4->5[label=<<B>o: 0 c:</B>>];
                                4->2[label=<<B>o: m c:</B>>];
                                5->6[label=<<B>o: [0-9] c:</B>>];
                                5->1[label=<<B>o: [a-s] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [01] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "6,"[shape=doublecircle];
                        "0,"->"1,0"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "1,0"->"2,1"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,1"->"3,2"[label = <<B>o: [0-5as] ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "3,2"->"4,"[label = <<B>o: [+=] c:</B>>, color = violet, penwidth = 2];
                        "4,"->"2,"[label = <<B>o: m c:</B>>, color = violet, penwidth = 2];
                        "4,"->"5,"[label = <<B>o: 0 c:</B>>, color = violet, penwidth = 2];
                        "2,"->"3,"[label = <<B>o: [0-5as] c:</B>>, color = violet, penwidth = 2];
                        "5,"->"1,"[label = <<B>o: [a-s] c:</B>>, color = violet, penwidth = 2];
                        "5,"->"6,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"4,"[label = <<B>o: [+=] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array(1), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_full_inter_in_branch_back() {
        $dotdescription1 = 'digraph {
                                0;
                                6;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [a-c] c:</B>>];
                                2->3[label=<<B>o: [0-5as] c:</B>>];
                                3->4[label=<<B>o: [ab] c:</B>>];
                                4->5[label=<<B>o: 0 c:</B>>];
                                4->2[label=<<B>o: m c:</B>>];
                                5->6[label=<<B>o: [0-9] c:</B>>];
                                5->1[label=<<B>o: [a-s] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [01] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "6,"[shape=doublecircle];
                        "5,2"->"6,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "4,1"->"5,2"[label = <<B>o: 0 ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "3,0"->"4,1"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,"->"3,"[label = <<B>o: [0-5as] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"3,0"[label = <<B>o: [0-5as] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "4,"->"2,"[label = <<B>o: m c:</B>>, color = violet, penwidth = 2];
                        "4,"->"5,"[label = <<B>o: 0 c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "5,"->"6,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "5,"->"1,"[label = <<B>o: [a-s] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"4,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array(5), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_with_meta_dots() {
        $dotdescription1 = 'digraph {
                                0;
                                7;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                2->4[label=<<B>o: a c:</B>>];
                                1->3[label=<<B>o: [a-z] c:</B>>];
                                3->5[label=<<B>o: [012] c:</B>>];
                                4->6[label=<<B>o: 0 c:</B>>];
                                4->1[label=<<B>o: m c:</B>>];
                                5->6[label=<<B>o: [+=] c:</B>>];
                                6->1[label=<<B>o: [a-s] c:</B>>];
                                6->7[label=<<B>o: [a-c] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: . c:</B>>, color = blue];
                                1->2[label=<<B>o: . c:</B>>, color = blue];
                                2->0[label=<<B>o: . c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    ",0"[shape=rarrow];
                    "7,"[shape=doublecircle];
                        "0,"->"1,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"3,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,0"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"5,"[label = <<B>o: [012] c:</B>>, color = violet, penwidth = 2];
                        "2,0"->"4,1"[label = <<B>o: a ∩ . c:</B>>, color = red, penwidth = 2];
                        "5,"->"6,"[label = <<B>o: [+=] c:</B>>, color = violet, penwidth = 2];
                        "6,"->"7,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "6,"->"1,"[label = <<B>o: [a-s] c:</B>>, color = violet, penwidth = 2];
                        "4,1"->"1,2"[label = <<B>o: m ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,1"->"6,2"[label = <<B>o: 0 ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,2"->"3,0"[label = <<B>o: [a-z] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,2"->"2,0"[label = <<B>o: [0-9] ∩ . c:</B>>, color = red, penwidth = 2];
                        "6,2"->"7,0"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "6,2"->"1,0"[label = <<B>o: [a-s] ∩ . c:</B>>, color = red, penwidth = 2];
                        "3,0"->"5,1"[label = <<B>o: [012] ∩ . c:</B>>, color = red, penwidth = 2];
                        "7,0"->",1"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        "1,0"->"3,1"[label = <<B>o: [a-z] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,0"->"2,1"[label = <<B>o: [0-9] ∩ . c:</B>>, color = red, penwidth = 2];
                        "5,1"->"6,2"[label = <<B>o: [+=] ∩ . c:</B>>, color = red, penwidth = 2];
                        "3,1"->"5,2"[label = <<B>o: [012] ∩ . c:</B>>, color = red, penwidth = 2];
                        "2,1"->"4,2"[label = <<B>o: a ∩ . c:</B>>, color = red, penwidth = 2];
                        "5,2"->"6,0"[label = <<B>o: [+=] ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,2"->"1,0"[label = <<B>o: m ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,2"->"6,0"[label = <<B>o: 0 ∩ . c:</B>>, color = red, penwidth = 2];
                        "6,0"->"7,1"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "6,0"->"1,1"[label = <<B>o: [a-s] ∩ . c:</B>>, color = red, penwidth = 2];
                        "7,1"->",2"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        "1,1"->"3,2"[label = <<B>o: [a-z] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,2"[label = <<B>o: [0-9] ∩ . c:</B>>, color = red, penwidth = 2];
                        "3,2"->"5,0"[label = <<B>o: [012] ∩ . c:</B>>, color = red, penwidth = 2];
                        "2,2"->"4,0"[label = <<B>o: a ∩ . c:</B>>, color = red, penwidth = 2];
                        "5,0"->"6,1"[label = <<B>o: [+=] ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,0"->"1,1"[label = <<B>o: m ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,0"->"6,1"[label = <<B>o: 0 ∩ . c:</B>>, color = red, penwidth = 2];
                        "6,1"->"7,2"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "6,1"->"1,2"[label = <<B>o: [a-s] ∩ . c:</B>>, color = red, penwidth = 2];
                        "7,2"->"7,"[label = <<B>o: ε c:</B>>, color = red, penwidth = 2];
                        ",1"->",2"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->",0"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->"7,"[label = <<B>o: ε c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->",1"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array(2), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_full_intersection() {
        $dotdescription1 = 'digraph {
                                0;
                                7;
                                0->1[label=<<B>o: [01] c:</B>>];
                                1->2[label=<<B>o: [ab] c:</B>>];
                                2->4[label=<<B>o: a c:</B>>];
                                1->3[label=<<B>o: [a-z] c:</B>>];
                                3->5[label=<<B>o: [012] c:</B>>];
                                4->6[label=<<B>o: 0 c:</B>>];
                                4->1[label=<<B>o: m c:</B>>];
                                5->6[label=<<B>o: [+=] c:</B>>];
                                6->1[label=<<B>o: [a-s] c:</B>>];
                                6->7[label=<<B>o: [a-c] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [01] c:</B>>, color = blue];
                                1->2[label=<<B>o: [ab] c:</B>>, color = blue];
                                2->0[label=<<B>o: . c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,0"[shape=rarrow];
                    "7,2"[shape=doublecircle];
                        "6,1"->"7,2"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "4,0"->"6,1"[label = <<B>o: 0 ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "2,2"->"4,0"[label = <<B>o: a ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "0,0"->"1,1"[label = <<B>o: [01] ∩ [01] c:</B>>, color = red, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array(7), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_branches_in_intersection() {
        $dotdescription1 = 'digraph {
                                0;
                                7;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                2->4[label=<<B>o: 0 c:</B>>];
                                1->3[label=<<B>o: [a-z] c:</B>>];
                                3->5[label=<<B>o: [012] c:</B>>];
                                4->6[label=<<B>o: [ab] c:</B>>];
                                4->1[label=<<B>o: m c:</B>>];
                                5->6[label=<o: $ c:<BR/><B>o: a c:</B>>];
                                6->1[label=<<B>o: [a-s] c:</B>>];
                                6->7[label=<<B>o: [a-c] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: [01] c:</B>>, color = blue];
                                1->2[label=<<B>o: [ab] c:</B>>, color = blue];
                                2->0[label=<<B>o: . c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        ",0"[shape=rarrow];
                    "7,"[shape=doublecircle];
                        "6,2"->"7,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "5,1"->"6,2"[label = <o: $ c:(5,6)<BR/><B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "4,1"->"6,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "3,0"->"5,1"[label = <<B>o: [012] ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "2,0"->"4,1"[label = <<B>o: 0 ∩ [01] c:</B>>, color = red, penwidth = 2];
                        "1,2"->"3,0"[label = <<B>o: [a-z] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,2"->"2,0"[label = <<B>o: [0-9] ∩ . c:</B>>, color = red, penwidth = 2];
                        "0,1"->"1,2"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        ",0"->",1"[label = <<B>o: [01] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->"0,1"[label = <<B>o: [01] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->",0"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->"7,"[label = <<B>o: ε c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->",2"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array(6), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_with_meta_dots_back() {
        $dotdescription1 = 'digraph {
                                0;
                                7;
                                0->1[label=<<B>o: [0-9] c:</B>>];
                                1->2[label=<<B>o: [a-n] c:</B>>];
                                2->4[label=<<B>o: a c:</B>>];
                                1->3[label=<<B>o: [a-z] c:</B>>];
                                3->5[label=<<B>o: [012] c:</B>>];
                                4->6[label=<<B>o: 0 c:</B>>];
                                4->1[label=<<B>o: m c:</B>>];
                                5->6[label=<<B>o: [+=] c:</B>>];
                                6->1[label=<<B>o: [a-s] c:</B>>];
                                6->7[label=<<B>o: [a-c] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                2;
                                0->1[label=<<B>o: . c:</B>>, color = blue];
                                1->2[label=<<B>o: . c:</B>>, color = blue];
                                2->0[label=<<B>o: . c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,0"[shape=rarrow];
                    ",0"[shape=rarrow];
                    "7,2"[shape=doublecircle];
                        "6,1"->"7,2"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "6,1"->"1,2"[label = <<B>o: [a-s] ∩ . c:</B>>, color = red, penwidth = 2];
                        "5,0"->"6,1"[label = <<B>o: [+=] ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,0"->"6,1"[label = <<B>o: 0 ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,0"->"1,1"[label = <<B>o: m ∩ . c:</B>>, color = red, penwidth = 2];
                        "3,2"->"5,0"[label = <<B>o: [012] ∩ . c:</B>>, color = red, penwidth = 2];
                        "2,2"->"4,0"[label = <<B>o: a ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,1"->"3,2"[label = <<B>o: [a-z] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,2"[label = <<B>o: [a-n] ∩ . c:</B>>, color = red, penwidth = 2];
                        "0,0"->"1,1"[label = <<B>o: [0-9] ∩ . c:</B>>, color = red, penwidth = 2];
                        "6,0"->"1,1"[label = <<B>o: [a-s] ∩ . c:</B>>, color = red, penwidth = 2];
                        "5,2"->"6,0"[label = <<B>o: [+=] ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,2"->"6,0"[label = <<B>o: 0 ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,2"->"1,0"[label = <<B>o: m ∩ . c:</B>>, color = red, penwidth = 2];
                        "3,1"->"5,2"[label = <<B>o: [012] ∩ . c:</B>>, color = red, penwidth = 2];
                        "2,1"->"4,2"[label = <<B>o: a ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,0"->"3,1"[label = <<B>o: [a-z] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,0"->"2,1"[label = <<B>o: [a-n] ∩ . c:</B>>, color = red, penwidth = 2];
                        "0,2"->"1,0"[label = <<B>o: [0-9] ∩ . c:</B>>, color = red, penwidth = 2];
                        "6,2"->"1,0"[label = <<B>o: [a-s] ∩ . c:</B>>, color = red, penwidth = 2];
                        "5,1"->"6,2"[label = <<B>o: [+=] ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,1"->"6,2"[label = <<B>o: 0 ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,1"->"1,2"[label = <<B>o: m ∩ . c:</B>>, color = red, penwidth = 2];
                        "3,0"->"5,1"[label = <<B>o: [012] ∩ . c:</B>>, color = red, penwidth = 2];
                        "2,0"->"4,1"[label = <<B>o: a ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,2"->"3,0"[label = <<B>o: [a-z] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,2"->"2,0"[label = <<B>o: [a-n] ∩ . c:</B>>, color = red, penwidth = 2];
                        "0,1"->"1,2"[label = <<B>o: [0-9] ∩ . c:</B>>, color = red, penwidth = 2];
                        ",1"->",2"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->"0,2"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->",1"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->"0,1"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->",0"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->"7,2"[label = <<B>o: ε c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array(7), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_full_coping_with_intersection() {
        $dotdescription1 = 'digraph {
                                0;
                                9;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [0-9] c:</B>>];
                                1->3[label=<<B>o: [ab] c:</B>>];
                                2->8[label=<<B>o: [as] c:</B>>];
                                1->5[label=<<B>o: [a-z] c:</B>>];
                                3->4[label=<<B>o: [a-c] c:</B>>];
                                4->8[label=<<B>o: [ab] c:</B>>];
                                5->6[label=<<B>o: [0-9] c:</B>>];
                                7->8[label=<<B>o: [0-9] c:</B>>];
                                6->7[label=<<B>o: [0-9] c:</B>>];
                                8->9[label=<<B>o: [0-9] c:</B>>];
                                8->1[label=<<B>o: [0-9] c:</B>>];
                                9->2[label=<<B>o: [0-9] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                9;
                                0->1[label=<<B>o: [a-z] c:</B>>, color = blue];
                                1->2[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->3[label=<<B>o: [as] c:</B>>, color = blue];
                                2->6[label=<<B>o: [a-c] c:</B>>, color = blue];
                                3->4[label=<<B>o: . c:</B>>, color = blue];
                                4->5[label=<<B>o: [a-d] c:</B>>, color = blue];
                                5->6[label=<<B>o: [as01] c:</B>>, color = blue];
                                6->7[label=<<B>o: [axy] c:</B>>, color = blue];
                                6->8[label=<<B>o: . c:</B>>, color = blue];
                                7->9[label=<<B>o: a c:</B>>, color = blue];
                                8->9[label=<<B>o: [a-c] c:</B>>, color = blue];
                                7->2[label=<<B>o: [a-z] c:</B>>, color = blue];
                                8->1[label=<<B>o: [a-f] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "0,0"[shape=rarrow];
                    ",0"[shape=rarrow];
                    "9,"[shape=doublecircle];
                        "9,"->"2,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "8,"->"9,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "8,"->"1,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "8,"->"1,0"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"8,"[label = <<B>o: [as] c:</B>>, color = violet, penwidth = 2];
                        "7,"->"8,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "4,"->"8,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"3,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"5,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "6,"->"7,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"4,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,0"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "5,9"->"6,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                        "1,8"->"5,9"[label = <<B>o: [a-z] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "1,8"->"3,1"[label = <<B>o: [ab] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "1,7"->"5,9"[label = <<B>o: [a-z] ∩ a c:</B>>, color = red, penwidth = 2];
                        "0,6"->"1,8"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "0,6"->"1,7"[label = <<B>o: [a-c] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "8,6"->"1,8"[label = <<B>o: [0-9] ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,2"->"8,6"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "4,5"->"8,6"[label = <<B>o: [ab] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "3,1"->"4,2"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "3,7"->"4,2"[label = <<B>o: [a-c] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "3,4"->"4,5"[label = <<B>o: [a-c] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "1,0"->"3,1"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "1,6"->"3,7"[label = <<B>o: [ab] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "1,3"->"3,4"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,3"->"2,4"[label = <<B>o: [0-9] ∩ . c:</B>>, color = red, penwidth = 2];
                        "0,2"->"1,6"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "0,5"->"1,6"[label = <<B>o: [a-c] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "8,5"->"1,6"[label = <<B>o: [0-9] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "0,1"->"1,3"[label = <<B>o: [a-c] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "2,4"->"8,5"[label = <<B>o: [as] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "4,4"->"8,5"[label = <<B>o: [ab] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "3,3"->"4,4"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,1"->"3,3"[label = <<B>o: [ab] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "0,0"->"1,1"[label = <<B>o: [a-c] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "0,8"->"1,1"[label = <<B>o: [a-c] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        ",2"->",6"[label = <<B>o: [a-c] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->"0,6"[label = <<B>o: [a-c] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",5"->",6"[label = <<B>o: [as01] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",5"->"0,6"[label = <<B>o: [as01] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->",2"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->",3"[label = <<B>o: [as] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->"0,2"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",7"->",2"[label = <<B>o: [a-z] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",7"->"0,2"[label = <<B>o: [a-z] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",4"->",5"[label = <<B>o: [a-d] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",4"->"0,5"[label = <<B>o: [a-d] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->",1"[label = <<B>o: [a-z] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->"0,1"[label = <<B>o: [a-z] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",8"->",1"[label = <<B>o: [a-f] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",8"->"0,1"[label = <<B>o: [a-f] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",6"->",7"[label = <<B>o: [axy] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",6"->",8"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",6"->"0,8"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",3"->",4"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        "5,"->"6,"[label = <<B>o: [0-9] c:</B>>, color = violet, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array(5), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_two_times_in_cycle_back() {
        $dotdescription1 = 'digraph {
                                0;
                                4;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [a-z] c:</B>>];
                                2->3[label=<<B>o: a c:</B>>];
                                3->4[label=<<B>o: [a-z] c:</B>>];
                                3->1[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                3;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [a-z] c:</B>>, color = blue];
                                2->3[label=<<B>o: a c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    ",3"[shape=doublecircle];
                        "0,"->"1,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,0"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "2,0"->"3,1"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "3,1"->"1,2"[label = <<B>o: a ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "3,1"->"4,2"[label = <<B>o: [a-z] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "1,2"->"2,3   0"[label = <<B>o: [a-z] ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,2"->",3"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        "2,3   0"->"3,1"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('2'), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_two_times_in_cycle_merged() {
        $dotdescription1 = 'digraph {
                                0;
                                5;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [a-z] c:</B>>];
                                1->3[label=<<B>o: [a-z] c:</B>>];
                                2->4[label=<<B>o: a c:</B>>];
                                3->4[label=<<B>o: [a-z] c:</B>>];
                                4->1[label=<<B>o: a c:</B>>];
                                4->5[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                9;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [a-z] c:</B>>, color = blue];
                                2->3[label=<<B>o: a c:</B>>, color = blue];
                                3->4[label=<<B>o: [ab] c:</B>>, color = blue];
                                4->5[label=<<B>o: [a-z] c:</B>>, color = blue];
                                5->6[label=<<B>o: a c:</B>>, color = blue];
                                6->7[label=<<B>o: [ab] c:</B>>, color = blue];
                                7->8[label=<<B>o: [a-z] c:</B>>, color = blue];
                                8->9[label=<<B>o: a c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "5,9"[shape=doublecircle];
                        "0,"->"1,0"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "1,0"->"3,1"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,0"->"2,1"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "3,1"->"4,2"[label = <<B>o: [a-z] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "2,1"->"4,2"[label = <<B>o: a ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "4,2"->"5,3"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,2"->"1,3   0"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "5,3"->",4"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        "1,3   0"->"3,4   1"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,3   0"->"2,4   1"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "3,4   1"->"4,5   2"[label = <<B>o: [a-z] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "2,4   1"->"4,5   2"[label = <<B>o: a ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "4,5   2"->"5,6"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,5   2"->"1,6   3   0"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "5,6"->",7"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        "1,6   3   0"->"3,7   4   1"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,6   3   0"->"2,7   4   1"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "3,7   4   1"->"4,8   5   2"[label = <<B>o: [a-z] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "2,7   4   1"->"4,8   5   2"[label = <<B>o: a ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "4,8   5   2"->"5,9"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,8   5   2"->"1,9   6   3   0"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,9   6   3   0"->"3,7   4   1"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,9   6   3   0"->"2,7   4   1"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        ",4"->",5"[label = <<B>o: [a-z] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",5"->",6"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",6"->",7"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",7"->",8"[label = <<B>o: [a-z] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",8"->",9"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",9"->"5,9"[label = <<B>o: ε c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $origin = qtype_preg_fa_transition::ORIGIN_TRANSITION_SECOND;

        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array(1), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merged_first_and_cycle() {
        $dotdescription1 = 'digraph {
                                0;
                                4;
                                0->2[label=<o: ε c:<BR/><B>o: [a-z] c:</B>>];
                                2->3[label=<<B>o: [a-z] c:</B>>];
                                3->4[label=<<B>o: a c:</B>>];
                                3->1[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                6;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [a-z] c:</B>>, color = blue];
                                2->3[label=<<B>o: a c:</B>>, color = blue];
                                3->4[label=<<B>o: [ab] c:</B>>, color = blue];
                                4->5[label=<<B>o: [a-z] c:</B>>, color = blue];
                                5->6[label=<<B>o: a c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    ",6"[shape=doublecircle];
                        "0,"->"2,"[label = <o: ε c:(0,1)<BR/><B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"3,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"4,0"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "4,0"->",1"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->",2"[label = <<B>o: [a-z] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->",3"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",3"->",4"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",4"->",5"[label = <<B>o: [a-z] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",5"->",6"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array(1), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_merged_first_in_both() {
        $dotdescription1 = 'digraph {
                                0;
                                4;
                                0->2[label=<o: ε c:<BR/><B>o: [a-z] c:</B>>];
                                1->2[label=<<B>o: [a-z] c:</B>>];
                                2->3[label=<<B>o: [a-z] c:</B>>];
                                3->4[label=<<B>o: a c:</B>>];
                                3->1[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                6;
                                0->2[label=<o: ε c:<BR/><B>o: [a-z] c:</B>>, color = blue];
                                1->2[label=<<B>o: [a-z] c:</B>>, color = blue];
                                2->3[label=<<B>o: a c:</B>>, color = blue];
                                3->4[label=<<B>o: [ab] c:</B>>, color = blue];
                                4->5[label=<<B>o: [a-z] c:</B>>, color = blue];
                                5->6[label=<<B>o: a c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "4,6"[shape=doublecircle];
                        "0,"->"2,0"[label = <o: ε c:(0,1)<BR/><B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"2,"[label = <o: ε c:(0,13)<BR/><B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "2,0"->"3,2"[label = <o: ε c:(0,2)<BR/><B>o: [a-z] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "3,2"->"1,3"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "3,2"->"4,3"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,3"->"2,4"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "4,3"->",4"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        "2,4"->"3,5"[label = <<B>o: [a-z] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "3,5"->"1,6"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "3,5"->"4,6"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,6"->"2,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        ",4"->",5"[label = <<B>o: [a-z] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",5"->",6"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",6"->"4,6"[label = <<B>o: ε c:</B>>, color = blue, penwidth = 2, style = dotted];
                        "1,"->"2,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"3,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"1,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "3,"->"4,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "4,"->"4,6"[label = <<B>o: ε c:</B>>, color = violet, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array(2), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_branches_same_length_cycle() {
        $dotdescription1 = 'digraph {
                                0;
                                5;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: a c:</B>>];
                                2->3[label=<<B>o: a c:</B>>];
                                3->4[label=<<B>o: [a-z] c:</B>>];
                                4->5[label=<<B>o: [a-z] c:</B>>];
                                4->1[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                8;
                                0->1[label=<<B>o: a c:</B>>, color = blue];
                                1->2[label=<<B>o: a c:</B>>, color = blue];
                                2->3[label=<<B>o: a c:</B>>, color = blue];
                                3->4[label=<<B>o: a c:</B>>, color = blue];
                                4->5[label=<<B>o: [ab] c:</B>>, color = blue];
                                5->6[label=<<B>o: [ab] c:</B>>, color = blue];
                                6->7[label=<<B>o: [ab] c:</B>>, color = blue];
                                7->8[label=<<B>o: [ab] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,0"[shape=rarrow];
                    ",0"[shape=rarrow];
                    "5,"[shape=doublecircle];
                        "4,8"->"5,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "3,7"->"4,8"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,6"->"3,7"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,5"->"2,6"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "0,4"->"1,5"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "4,4   8"->"1,5"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "3,3   7"->"4,4   8"[label = <<B>o: [a-z] ∩ a c:</B>>, color = red, penwidth = 2];
                        "2,2   6"->"3,3   7"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,1   5"->"2,2   6"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,1   5"->"(2,2   6)"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "0,0"->"1,1   5"[label = <<B>o: [a-c] ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,0   4   8"->"1,1   5"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "(3,3   7)"->"4,0   4   8"[label = <<B>o: [a-z] ∩ a c:</B>>, color = red, penwidth = 2];
                        "(2,2   6)"->"(3,3   7)"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        ",3"->"0,4"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->",3"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->",2"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->",1"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array(4), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_with_cycles_full_first() {
        $dotdescription1 = 'digraph {
                                0;
                                8;
                                0->1[label=<<B>o: [ab] c:</B>>];
                                1->2[label=<<B>o: [a-z] c:</B>>];
                                2->3[label=<<B>o: [a-z] c:</B>>];
                                1->4[label=<<B>o: [ab] c:</B>>];
                                4->5[label=<<B>o: [ab] c:</B>>];
                                5->6[label=<<B>o: [ab] c:</B>>];
                                6->7[label=<<B>o: [ab] c:</B>>];
                                3->7[label=<<B>o: a c:</B>>];
                                3->1[label=<<B>o: a c:</B>>];
                                7->1[label=<<B>o: [ab] c:</B>>];
                                7->8[label=<<B>o: [ab] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                6;
                                0->1[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->2[label=<<B>o: [a-z] c:</B>>, color = blue];
                                2->3[label=<<B>o: a c:</B>>, color = blue];
                                3->4[label=<<B>o: [ab] c:</B>>, color = blue];
                                4->5[label=<<B>o: [a-z] c:</B>>, color = blue];
                                5->6[label=<<B>o: a c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "8,"[shape=doublecircle];
                        "0,"->"1,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"4,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,0"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "4,"->"5,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "2,0"->"3,1"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "5,"->"6,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "6,"->"7,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "7,"->"8,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "7,"->"1,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "3,1"->"1,2"[label = <<B>o: a ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "3,1"->"7,2"[label = <<B>o: a ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "1,2"->"4,3"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,2"->"2,3   0"[label = <<B>o: [a-z] ∩ a c:</B>>, color = red, penwidth = 2];
                        "7,2"->"8,3"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "7,2"->"1,3"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,3"->"5,4"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,3   0"->"3,4   1"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "8,3"->",4"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        "1,3"->"4,4"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,3"->"2,4   0"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "5,4"->"6,5"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "3,4   1"->"1,5   2"[label = <<B>o: a ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "3,4   1"->"7,5"[label = <<B>o: a ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "4,4"->"5,5"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "2,4   0"->"3,5   1"[label = <<B>o: [a-z] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "6,5"->"7,6"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,5   2"->"4,6"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,5   2"->"2,6   3   0"[label = <<B>o: [a-z] ∩ a c:</B>>, color = red, penwidth = 2];
                        "7,5"->"8,6"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "7,5"->"1,6   2"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "5,5"->"6,6"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "3,5   1"->"1,6   3"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "3,5   1"->"7,6   2"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "7,6"->"8,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "7,6"->"1,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "4,6"->"5,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "2,6   3   0"->"3,4   1"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "8,6"->"8,"[label = <<B>o: ε c:</B>>, color = red, penwidth = 2];
                        "1,6   2"->"4,3"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,6   2"->"2,3   0"[label = <<B>o: [a-z] ∩ a c:</B>>, color = red, penwidth = 2];
                        "6,6"->"7,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,6   3"->"4,4"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,6   3"->"2,4   0"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "7,6   2"->"8,3"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "7,6   2"->"1,3"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        ",4"->",5"[label = <<B>o: [a-z] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",5"->",6"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",6"->"8,"[label = <<B>o: ε c:</B>>, color = blue, penwidth = 2, style = dotted];
                        "2,"->"3,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"1,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                        "3,"->"7,"[label = <<B>o: a c:</B>>, color = violet, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array(2), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_with_cycle_back() {
        $dotdescription1 = 'digraph {
                                0;
                                5;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: a c:</B>>];
                                2->3[label=<<B>o: a c:</B>>];
                                3->4[label=<<B>o: [a-z] c:</B>>];
                                4->5[label=<<B>o: [a-z] c:</B>>];
                                4->1[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                7;
                                0->1[label=<<B>o: a c:</B>>, color = blue];
                                1->2[label=<<B>o: a c:</B>>, color = blue];
                                2->3[label=<<B>o: a c:</B>>, color = blue];
                                3->4[label=<<B>o: a c:</B>>, color = blue];
                                4->5[label=<<B>o: [ab] c:</B>>, color = blue];
                                5->6[label=<<B>o: [ab] c:</B>>, color = blue];
                                6->7[label=<<B>o: [ab] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        ",0"[shape=rarrow];
                    "5,"[shape=doublecircle];
                        "4,7"->"5,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "3,6"->"4,7"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,5"->"3,6"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,4"->"2,5"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,4"->"1,0   4"[label = <<B>o: ε c:</B>>, color = red, penwidth = 2];
                        "0,3"->"1,4"[label = <<B>o: [a-c] ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,3   7"->"1,4"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "3,2   6"->"4,3   7"[label = <<B>o: [a-z] ∩ a c:</B>>, color = red, penwidth = 2];
                        "2,1   5"->"3,2   6"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,0   4"->"2,1   5"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        ",2"->"0,3"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->",2"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->",1"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array(4), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_inter_with_three_times_in_cycle_back() {
        $dotdescription1 = 'digraph {
                                0;
                                5;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: a c:</B>>];
                                2->3[label=<<B>o: a c:</B>>];
                                3->4[label=<<B>o: [a-z] c:</B>>];
                                4->5[label=<<B>o: [a-z] c:</B>>];
                                4->1[label=<<B>o: a c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                8;
                                0->1[label=<<B>o: a c:</B>>, color = blue];
                                1->2[label=<<B>o: a c:</B>>, color = blue];
                                2->3[label=<<B>o: a c:</B>>, color = blue];
                                3->4[label=<<B>o: a c:</B>>, color = blue];
                                4->5[label=<<B>o: [ab] c:</B>>, color = blue];
                                5->6[label=<<B>o: [ab] c:</B>>, color = blue];
                                6->7[label=<<B>o: [ab] c:</B>>, color = blue];
                                7->8[label=<<B>o: [ab] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,0"[shape=rarrow];
                    ",0"[shape=rarrow];
                    "5,"[shape=doublecircle];
                        "4,8"->"5,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "3,7"->"4,8"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,6"->"3,7"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,5"->"2,6"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "0,4"->"1,5"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "4,4   8"->"1,5"[label = <<B>o: a ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "3,3   7"->"4,4   8"[label = <<B>o: [a-z] ∩ a c:</B>>, color = red, penwidth = 2];
                        "2,2   6"->"3,3   7"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,1   5"->"2,2   6"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,1   5"->"(2,2   6)"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "0,0"->"1,1   5"[label = <<B>o: [a-c] ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,0   4   8"->"1,1   5"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        "(3,3   7)"->"4,0   4   8"[label = <<B>o: [a-z] ∩ a c:</B>>, color = red, penwidth = 2];
                        "(2,2   6)"->"(3,3   7)"[label = <<B>o: a ∩ a c:</B>>, color = red, penwidth = 2];
                        ",3"->"0,4"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->",3"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->",2"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->",1"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array(4), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_big_forward() {
        $dotdescription1 = 'digraph {
                                0;
                                9;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [ab] c:</B>>];
                                1->3[label=<<B>o: [ab] c:</B>>];
                                2->8[label=<<B>o: [as] c:</B>>];
                                1->5[label=<<B>o: [a-z] c:</B>>];
                                3->4[label=<<B>o: [a-c] c:</B>>];
                                4->8[label=<<B>o: [ab] c:</B>>];
                                5->6[label=<<B>o: a c:</B><BR/>o: ^ c:>];
                                7->8[label=<<B>o: [a-c] c:</B>>];
                                6->7[label=<<B>o: [a-c] c:</B>>];
                                8->9[label=<<B>o: [ab] c:</B>>];
                                8->1[label=<<B>o: [ab] c:</B>>];
                                9->2[label=<<B>o: [ab] c:</B>>];
                            }';
        $dotdescription2 = 'digraph {
                                0;
                                9;
                                0->1[label=<<B>o: [a-z] c:</B>>, color = blue];
                                1->2[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->3[label=<<B>o: [as] c:</B>>, color = blue];
                                2->6[label=<<B>o: [a-c] c:</B>>, color = blue];
                                3->4[label=<<B>o: . c:</B>>, color = blue];
                                4->5[label=<<B>o: [a-d] c:</B>>, color = blue];
                                5->6[label=<<B>o: [as01] c:</B>>, color = blue];
                                6->7[label=<<B>o: [axy] c:</B>>, color = blue];
                                6->8[label=<<B>o: . c:</B>>, color = blue];
                                7->9[label=<<B>o: a c:</B>>, color = blue];
                                8->9[label=<<B>o: [a-c] c:</B>>, color = blue];
                                7->2[label=<<B>o: [a-z] c:</B>>, color = blue];
                                8->1[label=<<B>o: [a-f] c:</B>>, color = blue];
                                9->9[label=<<B>o: [b-n] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "9,"[shape=doublecircle];
                        "0,"->"1,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"5,0"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"3,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"5,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "5,0"->"6,1"[label = <<B>o: a ∩ [a-z] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "3,"->"4,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"8,"[label = <<B>o: [as] c:</B>>, color = violet, penwidth = 2];
                        "4,"->"8,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "8,"->"1,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "8,"->"9,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "9,"->"2,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "6,1"->"7,3"[label = <<B>o: [a-c] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "6,1"->"7,2"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "7,3"->"8,4"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "7,2"->"8,6"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "8,4"->"1,5"[label = <<B>o: [ab] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "8,4"->"9,5"[label = <<B>o: [ab] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "8,6"->"1,8"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "8,6"->"1,7"[label = <<B>o: [ab] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "8,6"->"9,8"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "8,6"->"9,7"[label = <<B>o: [ab] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "1,5"->"5,6"[label = <<B>o: [a-z] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "1,5"->"3,6"[label = <<B>o: [ab] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "1,5"->"2,6"[label = <<B>o: [ab] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "9,5"->"2,6"[label = <<B>o: [ab] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "1,8"->"5,1"[label = <<B>o: [a-z] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "1,8"->"5,9"[label = <<B>o: [a-z] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "1,8"->"3,1"[label = <<B>o: [ab] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "1,8"->"3,9"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "1,8"->"2,1"[label = <<B>o: [ab] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "1,8"->"2,9"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "1,7"->"5,2"[label = <<B>o: [a-z] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "1,7"->"5,9"[label = <<B>o: [a-z] ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,7"->"3,2"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "1,7"->"3,9"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,7"->"2,2"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "1,7"->"2,9"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "9,8"->"2,1"[label = <<B>o: [ab] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "9,8"->"2,9"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "9,7"->"2,2"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "9,7"->"2,9"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "5,6"->"6,8"[label = <<B>o: a ∩ . c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "5,6"->"6,7"[label = <<B>o: a ∩ [axy] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "3,6"->"4,8"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "3,6"->"4,7"[label = <<B>o: [a-c] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "2,6"->"8,8"[label = <<B>o: [as] ∩ . c:</B>>, color = red, penwidth = 2];
                        "2,6"->"8,7"[label = <<B>o: [as] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "5,1"->"6,3"[label = <<B>o: a ∩ [as] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "5,1"->"6,2"[label = <<B>o: a ∩ [ab] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "5,9"->"6,"[label = <<B>o: a c:</B><BR/>o: ^ c:(23,89)>, color = violet, penwidth = 2];
                        "3,1"->"4,3"[label = <<B>o: [a-c] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "3,1"->"4,2"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "3,9"->"4,9"[label = <<B>o: [a-c] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "2,1"->"8,3"[label = <<B>o: [as] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "2,1"->"8,2"[label = <<B>o: [as] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,9"->"8,"[label = <<B>o: [as] c:</B>>, color = violet, penwidth = 2];
                        "5,2"->"6,6"[label = <<B>o: a ∩ [a-c] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "3,2"->"4,6"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "2,2"->"8,6"[label = <<B>o: [as] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "6,8"->"7,1"[label = <<B>o: [a-c] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "6,8"->"7,9"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "6,7"->"7,2"[label = <<B>o: [a-c] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "6,7"->"7,9"[label = <<B>o: [a-c] ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,8"->"8,1"[label = <<B>o: [ab] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "4,8"->"8,9"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "4,7"->"8,2"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "4,7"->"8,9"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "8,8"->"1,1"[label = <<B>o: [ab] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "8,8"->"1,9"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "8,8"->"9,1"[label = <<B>o: [ab] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "8,8"->"9,9"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "8,7"->"1,2"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "8,7"->"1,9"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "8,7"->"9,2"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "8,7"->"9,9"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "6,3"->"7,4"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "6,2"->"7,6"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "4,3"->"8,4"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,2"->"8,6"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "4,9"->"8,9"[label = <<B>o: [ab] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "8,3"->"1,4"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "8,3"->"9,4"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "8,2"->"1,6"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "8,2"->"9,6"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "6,6"->"7,8"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "6,6"->"7,7"[label = <<B>o: [a-c] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "4,6"->"8,8"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,6"->"8,7"[label = <<B>o: [ab] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "7,1"->"8,3"[label = <<B>o: [a-c] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "7,1"->"8,2"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "7,9"->"8,9"[label = <<B>o: [a-c] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "8,1"->"1,3"[label = <<B>o: [ab] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "8,1"->"1,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "8,1"->"9,3"[label = <<B>o: [ab] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "8,1"->"9,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "8,9"->"1,9"[label = <<B>o: [ab] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "8,9"->"9,9"[label = <<B>o: [ab] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"5,3"[label = <<B>o: [a-z] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"5,2"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"3,3"[label = <<B>o: [ab] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"3,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,3"[label = <<B>o: [ab] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,9"->"5,9"[label = <<B>o: [a-z] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "1,9"->"3,9"[label = <<B>o: [ab] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "1,9"->"2,9"[label = <<B>o: [ab] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "9,1"->"2,3"[label = <<B>o: [ab] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "9,1"->"2,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "9,9"->"2,9"[label = <<B>o: [ab] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "9,9"->"9,"[label = <<B>o: ε c:</B>>, color = red, penwidth = 2];
                        "1,2"->"5,6"[label = <<B>o: [a-z] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "1,2"->"3,6"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "1,2"->"2,6"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "9,2"->"2,6"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "7,4"->"8,5"[label = <<B>o: [a-c] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "7,6"->"8,8"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "7,6"->"8,7"[label = <<B>o: [a-c] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "1,4"->"5,5"[label = <<B>o: [a-z] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "1,4"->"3,5"[label = <<B>o: [ab] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "1,4"->"2,5"[label = <<B>o: [ab] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "9,4"->"2,5"[label = <<B>o: [ab] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "1,6"->"5,8"[label = <<B>o: [a-z] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,6"->"5,7"[label = <<B>o: [a-z] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "1,6"->"3,8"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,6"->"3,7"[label = <<B>o: [ab] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "1,6"->"2,8"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,6"->"2,7"[label = <<B>o: [ab] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "9,6"->"2,8"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "9,6"->"2,7"[label = <<B>o: [ab] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "7,8"->"8,1"[label = <<B>o: [a-c] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "7,8"->"8,9"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "7,7"->"8,2"[label = <<B>o: [a-c] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "7,7"->"8,9"[label = <<B>o: [a-c] ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,3"->"5,4"[label = <<B>o: [a-z] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,3"->"3,4"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,3"->"2,4"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "9,3"->"2,4"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "5,3"->"6,4"[label = <<B>o: a ∩ . c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "3,3"->"4,4"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "2,3"->"8,4"[label = <<B>o: [as] ∩ . c:</B>>, color = red, penwidth = 2];
                        "8,5"->"1,6"[label = <<B>o: [ab] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "8,5"->"9,6"[label = <<B>o: [ab] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "5,5"->"6,6"[label = <<B>o: a ∩ [as01] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "3,5"->"4,6"[label = <<B>o: [a-c] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "2,5"->"8,6"[label = <<B>o: [as] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "5,8"->"6,1"[label = <<B>o: a ∩ [a-f] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "5,8"->"6,9"[label = <<B>o: a ∩ [a-c] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "5,7"->"6,2"[label = <<B>o: a ∩ [a-z] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "5,7"->"6,9"[label = <<B>o: a ∩ a c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "3,8"->"4,1"[label = <<B>o: [a-c] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "3,8"->"4,9"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "3,7"->"4,2"[label = <<B>o: [a-c] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "3,7"->"4,9"[label = <<B>o: [a-c] ∩ a c:</B>>, color = red, penwidth = 2];
                        "2,8"->"8,1"[label = <<B>o: [as] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "2,8"->"8,9"[label = <<B>o: [as] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "2,7"->"8,2"[label = <<B>o: [as] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "2,7"->"8,9"[label = <<B>o: [as] ∩ a c:</B>>, color = red, penwidth = 2];
                        "5,4"->"6,5"[label = <<B>o: a ∩ [a-d] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "3,4"->"4,5"[label = <<B>o: [a-c] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "2,4"->"8,5"[label = <<B>o: [as] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "6,4"->"7,5"[label = <<B>o: [a-c] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "4,4"->"8,5"[label = <<B>o: [ab] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "6,9"->"7,9"[label = <<B>o: [a-c] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "4,1"->"8,3"[label = <<B>o: [ab] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "4,1"->"8,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "6,5"->"7,6"[label = <<B>o: [a-c] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "4,5"->"8,6"[label = <<B>o: [ab] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "7,5"->"8,6"[label = <<B>o: [a-c] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "6,"->"7,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "7,"->"8,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "5,"->"6,"[label = <<B>o: a c:</B><BR/>o: ^ c:(91,89)>, color = violet, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('5'), 0);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }

    public function test_big_back() {
        $dotdescription1 = 'digraph {
                                0;
                                9;
                                0->1[label=<<B>o: [a-c] c:</B>>];
                                1->2[label=<<B>o: [ab] c:</B>>];
                                1->3[label=<<B>o: [ab] c:</B>>];
                                2->8[label=<<B>o: [as] c:</B>>];
                                1->5[label=<<B>o: [a-z] c:</B>>];
                                3->4[label=<<B>o: [a-c] c:</B>>];
                                4->8[label=<<B>o: [ab] c:</B>>];
                                5->6[label=<<B>o: a c:</B><BR/>o: ^ c:>];
                                7->8[label=<<B>o: [a-c] c:</B>>];
                                6->7[label=<<B>o: [a-c] c:</B>>];
                                8->9[label=<<B>o: [ab] c:</B>>];
                                8->1[label=<<B>o: [ab] c:</B>>];
                                9->2[label=<<B>o: [ab] c:</B>>];
                            }';

        $dotdescription2 = 'digraph {
                                0;
                                9;
                                0->1[label=<<B>o: [a-z] c:</B>>, color = blue];
                                1->2[label=<<B>o: [ab] c:</B>>, color = blue];
                                1->3[label=<<B>o: [as] c:</B>>, color = blue];
                                2->6[label=<<B>o: [a-c] c:</B>>, color = blue];
                                3->4[label=<<B>o: . c:</B>>, color = blue];
                                4->5[label=<<B>o: [a-d] c:</B>>, color = blue];
                                5->6[label=<<B>o: [as01] c:</B>>, color = blue];
                                6->7[label=<<B>o: [axy] c:</B>>, color = blue];
                                6->8[label=<<B>o: . c:</B>>, color = blue];
                                7->9[label=<<B>o: a c:</B>>, color = blue];
                                8->9[label=<<B>o: [a-c] c:</B>>, color = blue];
                                7->2[label=<<B>o: [a-z] c:</B>>, color = blue];
                                8->1[label=<<B>o: [a-f] c:</B>>, color = blue];
                                9->9[label=<<B>o: [b-n] c:</B>>, color = blue];
                            }';
        $dotresult = 'digraph {
                        rankdir=LR;
                        "0,"[shape=rarrow];
                    "0,0"[shape=rarrow];
                    ",0"[shape=rarrow];
                    "9,"[shape=doublecircle];
                        "9,"->"2,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "9,"->"2,0"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "8,"->"9,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "8,"->"1,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "8,"->"1,0"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "8,"->"9,0"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"8,"[label = <<B>o: [as] c:</B>>, color = violet, penwidth = 2];
                        "2,"->"8,0"[label = <<B>o: [as] c:</B>>, color = violet, penwidth = 2];
                        "7,"->"8,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "7,"->"8,0"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "4,"->"8,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "4,"->"8,0"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"3,"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"5,"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"5,0"[label = <<B>o: [a-z] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"2,0"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "1,"->"3,0"[label = <<B>o: [ab] c:</B>>, color = violet, penwidth = 2];
                        "6,"->"7,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "6,"->"7,0"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"4,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "3,"->"4,0"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "0,"->"1,0"[label = <<B>o: [a-c] c:</B>>, color = violet, penwidth = 2];
                        "5,9"->"6,"[label = <<B>o: a c:</B><BR/>o: ^ c:(9,6)>, color = violet, penwidth = 2];
                        "1,9"->"5,9"[label = <<B>o: [a-z] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "1,9"->"3,9"[label = <<B>o: [ab] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "1,8"->"5,9"[label = <<B>o: [a-z] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "1,8"->"3,9"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "1,8"->"3,1"[label = <<B>o: [ab] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "1,8"->"5,1"[label = <<B>o: [a-z] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "1,8"->"2,1"[label = <<B>o: [ab] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "1,7"->"5,9"[label = <<B>o: [a-z] ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,7"->"2,2"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "1,7"->"3,9"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "1,7"->"3,2"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "1,7"->"5,2"[label = <<B>o: [a-z] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "0,9"->"1,9"[label = <<B>o: [a-c] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "0,8"->"1,9"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "0,8"->"1,1"[label = <<B>o: [a-c] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "0,7"->"1,9"[label = <<B>o: [a-c] ∩ a c:</B>>, color = red, penwidth = 2];
                        "0,7"->"1,2"[label = <<B>o: [a-c] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "8,9"->"1,9"[label = <<B>o: [ab] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "8,8"->"1,9"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "8,8"->"9,1"[label = <<B>o: [ab] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "8,8"->"1,1"[label = <<B>o: [ab] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "8,7"->"1,9"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "8,7"->"9,2"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "8,7"->"1,2"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "0,6"->"1,8"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "0,6"->"1,7"[label = <<B>o: [a-c] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "8,6"->"1,8"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "8,6"->"1,7"[label = <<B>o: [ab] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "8,6"->"9,7"[label = <<B>o: [ab] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "8,6"->"9,8"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "2,8"->"8,9"[label = <<B>o: [as] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "2,8"->"8,1"[label = <<B>o: [as] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "2,7"->"8,9"[label = <<B>o: [as] ∩ a c:</B>>, color = red, penwidth = 2];
                        "2,7"->"8,2"[label = <<B>o: [as] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "7,9"->"8,9"[label = <<B>o: [a-c] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "7,8"->"8,9"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "7,8"->"8,1"[label = <<B>o: [a-c] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "7,7"->"8,9"[label = <<B>o: [a-c] ∩ a c:</B>>, color = red, penwidth = 2];
                        "7,7"->"8,2"[label = <<B>o: [a-c] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "4,9"->"8,9"[label = <<B>o: [ab] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "4,8"->"8,9"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "4,8"->"8,1"[label = <<B>o: [ab] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "4,7"->"8,9"[label = <<B>o: [ab] ∩ a c:</B>>, color = red, penwidth = 2];
                        "4,7"->"8,2"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "2,6"->"8,8"[label = <<B>o: [as] ∩ . c:</B>>, color = red, penwidth = 2];
                        "2,6"->"8,7"[label = <<B>o: [as] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "7,6"->"8,8"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "7,6"->"8,7"[label = <<B>o: [a-c] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "4,6"->"8,8"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,6"->"8,7"[label = <<B>o: [ab] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "2,2"->"8,6"[label = <<B>o: [as] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "2,5"->"8,6"[label = <<B>o: [as] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "7,2"->"8,6"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "7,5"->"8,6"[label = <<B>o: [a-c] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "4,2"->"8,6"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "4,5"->"8,6"[label = <<B>o: [ab] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "9,6"->"2,8"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "9,6"->"2,7"[label = <<B>o: [ab] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "1,6"->"2,8"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,6"->"2,7"[label = <<B>o: [ab] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "1,6"->"3,8"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,6"->"3,7"[label = <<B>o: [ab] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "1,6"->"5,8"[label = <<B>o: [a-z] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,6"->"5,7"[label = <<B>o: [a-z] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "6,9"->"7,9"[label = <<B>o: [a-c] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "6,8"->"7,9"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "6,8"->"7,1"[label = <<B>o: [a-c] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "6,7"->"7,9"[label = <<B>o: [a-c] ∩ a c:</B>>, color = red, penwidth = 2];
                        "6,7"->"7,2"[label = <<B>o: [a-c] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "6,6"->"7,8"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "6,6"->"7,7"[label = <<B>o: [a-c] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "3,9"->"4,9"[label = <<B>o: [a-c] ∩ [b-n] c:</B>>, color = red, penwidth = 2];
                        "3,8"->"4,9"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "3,8"->"4,1"[label = <<B>o: [a-c] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "3,7"->"4,9"[label = <<B>o: [a-c] ∩ a c:</B>>, color = red, penwidth = 2];
                        "3,7"->"4,2"[label = <<B>o: [a-c] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "3,6"->"4,8"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "3,6"->"4,7"[label = <<B>o: [a-c] ∩ [axy] c:</B>>, color = red, penwidth = 2];
                        "9,2"->"2,6"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "9,5"->"2,6"[label = <<B>o: [ab] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "1,2"->"2,6"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "1,2"->"3,6"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "1,2"->"5,6"[label = <<B>o: [a-z] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "1,5"->"2,6"[label = <<B>o: [ab] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "1,5"->"3,6"[label = <<B>o: [ab] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "1,5"->"5,6"[label = <<B>o: [a-z] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "6,2"->"7,6"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "6,5"->"7,6"[label = <<B>o: [a-c] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "3,2"->"4,6"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "3,5"->"4,6"[label = <<B>o: [a-c] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "9,1"->"2,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "9,1"->"2,3"[label = <<B>o: [ab] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "9,7"->"2,2"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"3,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"5,2"[label = <<B>o: [a-z] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"5,3"[label = <<B>o: [a-z] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"2,3"[label = <<B>o: [ab] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "1,1"->"3,3"[label = <<B>o: [ab] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "9,4"->"2,5"[label = <<B>o: [ab] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "1,4"->"2,5"[label = <<B>o: [ab] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "1,4"->"3,5"[label = <<B>o: [ab] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "1,4"->"5,5"[label = <<B>o: [a-z] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "6,1"->"7,2"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "6,1"->"7,3"[label = <<B>o: [a-c] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "6,4"->"7,5"[label = <<B>o: [a-c] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "3,1"->"4,2"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "3,1"->"4,3"[label = <<B>o: [a-c] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "3,4"->"4,5"[label = <<B>o: [a-c] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "8,2"->"9,6"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "8,2"->"1,6"[label = <<B>o: [ab] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "8,5"->"9,6"[label = <<B>o: [ab] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "8,5"->"1,6"[label = <<B>o: [ab] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "0,2"->"1,6"[label = <<B>o: [a-c] ∩ [a-c] c:</B>>, color = red, penwidth = 2];
                        "0,5"->"1,6"[label = <<B>o: [a-c] ∩ [as01] c:</B>>, color = red, penwidth = 2];
                        "5,8"->"6,9"[label = <<B>o: a ∩ [a-c] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "5,8"->"6,1"[label = <<B>o: a ∩ [a-f] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "5,7"->"6,9"[label = <<B>o: a ∩ a c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "5,7"->"6,2"[label = <<B>o: a ∩ [a-z] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "5,6"->"6,8"[label = <<B>o: a ∩ . c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "5,6"->"6,7"[label = <<B>o: a ∩ [axy] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "5,2"->"6,6"[label = <<B>o: a ∩ [a-c] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "5,5"->"6,6"[label = <<B>o: a ∩ [as01] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "8,1"->"9,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "8,1"->"1,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "8,1"->"1,3"[label = <<B>o: [ab] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "8,1"->"9,3"[label = <<B>o: [ab] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "8,4"->"9,5"[label = <<B>o: [ab] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "8,4"->"1,5"[label = <<B>o: [ab] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "0,1"->"1,2"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "0,1"->"1,3"[label = <<B>o: [a-c] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "0,4"->"1,5"[label = <<B>o: [a-c] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "5,1"->"6,2"[label = <<B>o: a ∩ [ab] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "5,1"->"6,3"[label = <<B>o: a ∩ [as] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "5,4"->"6,5"[label = <<B>o: a ∩ [a-d] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "8,0"->"9,1"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "8,0"->"1,1"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "0,0"->"1,1"[label = <<B>o: [a-c] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "8,3"->"9,4"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "8,3"->"1,4"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "0,3"->"1,4"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "5,0"->"6,1"[label = <<B>o: a ∩ [a-z] c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "5,3"->"6,4"[label = <<B>o: a ∩ . c:</B><BR/>o: ^ c:(5,6)>, color = red, penwidth = 2];
                        "1,0"->"3,1"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "1,0"->"5,1"[label = <<B>o: [a-z] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "1,0"->"2,1"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "1,3"->"3,4"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,3"->"5,4"[label = <<B>o: [a-z] ∩ . c:</B>>, color = red, penwidth = 2];
                        "1,3"->"2,4"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "2,1"->"8,2"[label = <<B>o: [as] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "2,1"->"8,3"[label = <<B>o: [as] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "7,1"->"8,2"[label = <<B>o: [a-c] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "7,1"->"8,3"[label = <<B>o: [a-c] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "4,1"->"8,2"[label = <<B>o: [ab] ∩ [ab] c:</B>>, color = red, penwidth = 2];
                        "4,1"->"8,3"[label = <<B>o: [ab] ∩ [as] c:</B>>, color = red, penwidth = 2];
                        "2,4"->"8,5"[label = <<B>o: [as] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "7,4"->"8,5"[label = <<B>o: [a-c] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "4,4"->"8,5"[label = <<B>o: [ab] ∩ [a-d] c:</B>>, color = red, penwidth = 2];
                        "2,0"->"8,1"[label = <<B>o: [as] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "7,0"->"8,1"[label = <<B>o: [a-c] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "4,0"->"8,1"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "2,3"->"8,4"[label = <<B>o: [as] ∩ . c:</B>>, color = red, penwidth = 2];
                        "7,3"->"8,4"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "4,3"->"8,4"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "9,0"->"2,1"[label = <<B>o: [ab] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "9,8"->"2,1"[label = <<B>o: [ab] ∩ [a-f] c:</B>>, color = red, penwidth = 2];
                        "6,0"->"7,1"[label = <<B>o: [a-c] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "3,0"->"4,1"[label = <<B>o: [a-c] ∩ [a-z] c:</B>>, color = red, penwidth = 2];
                        "9,3"->"2,4"[label = <<B>o: [ab] ∩ . c:</B>>, color = red, penwidth = 2];
                        "6,3"->"7,4"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        "3,3"->"4,4"[label = <<B>o: [a-c] ∩ . c:</B>>, color = red, penwidth = 2];
                        ",9"->",9"[label = <<B>o: [b-n] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",9"->"0,9"[label = <<B>o: [b-n] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",9"->"9,"[label = <<B>o: ε c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",8"->",9"[label = <<B>o: [a-c] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",8"->",1"[label = <<B>o: [a-f] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",8"->"0,9"[label = <<B>o: [a-c] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",8"->"0,1"[label = <<B>o: [a-f] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",7"->",9"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",7"->",2"[label = <<B>o: [a-z] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",7"->"0,9"[label = <<B>o: a c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",7"->"0,2"[label = <<B>o: [a-z] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",6"->",8"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",6"->",7"[label = <<B>o: [axy] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",6"->"0,8"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",6"->"0,7"[label = <<B>o: [axy] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->",6"[label = <<B>o: [a-c] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",2"->"0,6"[label = <<B>o: [a-c] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",5"->",6"[label = <<B>o: [as01] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",5"->"0,6"[label = <<B>o: [as01] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->",2"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->",3"[label = <<B>o: [as] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->"0,2"[label = <<B>o: [ab] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",1"->"0,3"[label = <<B>o: [as] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",4"->",5"[label = <<B>o: [a-d] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",4"->"0,5"[label = <<B>o: [a-d] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->",1"[label = <<B>o: [a-z] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",0"->"0,1"[label = <<B>o: [a-z] c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",3"->",4"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        ",3"->"0,4"[label = <<B>o: . c:</B>>, color = blue, penwidth = 2, style = dotted];
                        "5,"->"6,"[label = <<B>o: a c:</B><BR/>o: ^ c:(117,6)>, color = violet, penwidth = 2];
                        "5,"->"6,0"[label = <<B>o: a c:</B><BR/>o: ^ c:(117,102)>, color = violet, penwidth = 2];
                    }';

        $search = '
                    ';
        $replace = "\n";
        $firstautomata = qtype_preg_fa::read_fa($dotdescription1);
        $secondautomata = qtype_preg_fa::read_fa($dotdescription2);
        $resultautomata = new qtype_preg_fa();

        $resultautomata = $firstautomata->intersect($secondautomata, array('5'), 1);
        $result = $resultautomata->fa_to_dot(null, null, true);
        $dotresult = str_replace($search, $replace, $dotresult);
        $this->assertEquals($dotresult, $result, 'Result automata is not equal to expected');
    }
}*/