<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');

class qtype_preg_fa_transition_test extends PHPUnit_Framework_TestCase {

    function create_lexer($regex) {
        $options = new qtype_preg_handling_options();
        $options->preserveallnodes = true;
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $lexer->set_options($options);
        return $lexer;
    }

    function leaf_by_regex($regex) {
        $lexer = $this->create_lexer($regex);
        return $lexer->nextToken()->value;
    }

    /**
     * Create transition by regex with merged asserions. All assertions are true/false, except subexpr: that
     * should be 'false' or '+-number' for positive/negative asserting respectively.
     */
    function transition_by_regex($regex, $esca = false, $smallescz = false, $capescz = false, $circumflex = false, $dollar = false, $subexpr = false) {
        $leaf = $this->leaf_by_regex($regex);
        $transition = new qtype_preg_fa_transition(0, $leaf, 0);
        if ($esca) {
            $transition->mergedafter[] = $this->transition_by_regex('\A');
        }
        if ($smallescz) {
            $transition->mergedbefore[] = $this->transition_by_regex('\z');
        }
        if ($capescz) {
            $transition->mergedbefore[] = $this->transition_by_regex('\Z');
        }
        if ($circumflex) {
            $transition->mergedafter[] = $this->transition_by_regex('^');
        }
        if ($dollar) {
            $transition->mergedbefore[] = $this->transition_by_regex('$');
        }
        if ($subexpr !== false) {
            $negative = $subexpr < 0;
            $number = abs($subexpr);
            $assertleaf = new qtype_preg_leaf_assert_subexpr($negative, $number);
            $transition->mergedbefore[] = new qtype_preg_fa_transition(0, $assertleaf, 0);
        }
        return $transition;
    }

    /*function test_match_string_ends() {
        $str = new qtype_poasquestion\string("a\n");
        $length = 0;
        $lexer = $this->create_lexer("[ab\n]");
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsafter[] = $assert;
        $pos = 1;
        $a = $leaf->match($str, $pos, $length);
        $this->assertTrue($a);
        $this->assertEquals($length, 1);
    }

    function test_match_character_with_circumflex() {
        $str = new qtype_poasquestion\string("ab\n");
        $length = 0;
        $lexer = $this->create_lexer("[ab]");
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsafter[] = $assert;
        $pos = 0;
        $this->assertFalse($leaf->match($str, $pos, $length));
        $this->assertEquals($length, 0);
    }

    function test_match_string_ends_dollar_assert() {
        $str = new qtype_poasquestion\string("ab\na\nas");
        $length = 0;
        $lexer = $this->create_lexer("[ab\n]");
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_dollar;
        $leaf->assertionsbefore[] = $assert;
        $pos = 2;
        $this->assertTrue($leaf->match($str, $pos, $length));
        $this->assertEquals($length, 1);
    }

    function test_match_character_with_dollar() {
        $str = new qtype_poasquestion\string("ab\na\nas");
        $length = 0;
        $lexer = $this->create_lexer("[ab]");
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_dollar;
        $leaf->assertionsbefore[] = $assert;
        $pos = 2;
        $this->assertFalse($leaf->match($str, $pos, $length));
        $this->assertEquals($length, 0);
    }

    function test_match_one_string() {
        $str = new qtype_poasquestion\string("ab");
        $length = 0;
        $lexer = $this->create_lexer("[a]");
        $leaf = $lexer->nextToken()->value;
        $pos = 0;
        $this->assertTrue($leaf->match($str, $pos, $length));
        $this->assertEquals($length, 1);
    }

    function test_match_single_assert() {
        $str = new qtype_poasquestion\string("ab\na\nas");
        $length = 0;
        $leaf= new qtype_preg_leaf_assert_circumflex;
        $pos = 0;
        $this->assertTrue($leaf->match($str, $pos, $length));
        $this->assertEquals($length, 0);
    }

    function test_match_before_and_after_asserts_true() {
        $str = new qtype_poasquestion\string("ab\na\nas");
        $length = 0;
        $lexer = $this->create_lexer("[ab\n]");
        $leaf = $lexer->nextToken()->value;
        $assert1 = new qtype_preg_leaf_assert_dollar;
        $assert2 = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsbefore[] = $assert1;
        $leaf->assertionsafter[] = $assert2;
        $pos = 2;
        $this->assertTrue($leaf->match($str, $pos, $length));
        $this->assertEquals($length, 1);
    }

    function test_match_before_and_after_asserts_false() {
        $str = new qtype_poasquestion\string("ab\na\nas");
        $length = 0;
        $lexer = $this->create_lexer("[ab]");
        $leaf = $lexer->nextToken()->value;
        $assert1 = new qtype_preg_leaf_assert_dollar;
        $assert2 = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsbefore[] = $assert1;
        $leaf->assertionsafter[] = $assert2;
        $pos = 2;
        $this->assertFalse($leaf->match($str, $pos, $length));
        $this->assertEquals($length, 0);
    }

    function test_match_empty_string_true() {
        $str = new qtype_poasquestion\string("ab\n\nas");
        $length = 0;
        $lexer = $this->create_lexer("[a-z\n]");
        $leaf = $lexer->nextToken()->value;
        $assert1 = new qtype_preg_leaf_assert_dollar;
        $assert2 = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsbefore[] = $assert1;
        $leaf->assertionsafter[] = $assert2;
        $pos = 3;
        $this->assertTrue($leaf->match($str, $pos, $length));
        $this->assertEquals($length, 1);
    }

    function test_match_empty_string_false() {
        $str = new qtype_poasquestion\string("ab\n\nas");
        $length = 0;
        $lexer = $this->create_lexer("[a-z]");
        $leaf = $lexer->nextToken()->value;
        $assert1 = new qtype_preg_leaf_assert_dollar;
        $assert2 = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsbefore[] = $assert1;
        $leaf->assertionsafter[] = $assert2;
        $pos = 3;
        $this->assertFalse($leaf->match($str, $pos, $length));
        $this->assertEquals($length, 0);
    }

    function test_match_single_dollar_in_the_end() {
        $str = new qtype_poasquestion\string("ab\n\nas");
        $length = 0;
        $leaf = new qtype_preg_leaf_assert_dollar;
        $pos = 6;
        $this->assertTrue($leaf->match($str, $pos, $length));
        $this->assertEquals($length, 0);
    }

    function test_match_middle_of_the_string() {
        $str = new qtype_poasquestion\string("bcd");
        $length = 0;
        $lexer = $this->create_lexer("[a-c\n]");
        $leaf = $lexer->nextToken()->value;
        $assert = new qtype_preg_leaf_assert_circumflex;
        $leaf->assertionsafter[] = $assert;
        $pos = 1;
        $this->assertFalse($leaf->match($str, $pos, $length));
        $this->assertEquals($length, 0);
    }*/

    ////////////////////////////////////////// next_character
    function test_aaa() {
        $str = new qtype_poasquestion\string(" c");
        $pos = 0;
        $length = 0;

        $esca = false;
        $smallescz = false;
        $capescz = false;
        $circumflex = false;
        $dollar = false;
        $subexpr = false;

        $transition1 = $this->transition_by_regex("[\\w\\W]", $esca, $smallescz, $capescz, $circumflex, $dollar, $subexpr);
        $transition2 = $this->transition_by_regex("\\W", $esca, $smallescz, $capescz, $circumflex, $dollar, $subexpr);
        $transition = $transition1->intersect($transition2);
        $result = $transition->pregleaf->match($str, $pos, $length);
        $this->assertTrue($result);
    }

    function test_generation_empty_string() {
        $str = new qtype_poasquestion\string("ax");
        $pos = 1;
        $length = 0;

        $esca = false;
        $smallescz = false;
        $capescz = false;
        $circumflex = true;
        $dollar = false;
        $subexpr = false;

        $transition = $this->transition_by_regex("[ab\n\\x1]", $esca, $smallescz, $capescz, $circumflex, $dollar, $subexpr);
        list($flag, $ch) = $transition->next_character($str, $str, $pos, $length);
        $this->assertEquals($ch, "\n");
    }

    function test_generation_string_ends_false() {
        $str = new qtype_poasquestion\string("b\n");
        $pos = 1;
        $length = 0;

        $esca = false;
        $smallescz = false;
        $capescz = false;
        $circumflex = true;
        $dollar = false;
        $subexpr = false;

        $transition = $this->transition_by_regex("[ab]", $esca, $smallescz, $capescz, $circumflex, $dollar, $subexpr);
        list($flag, $ch) = $transition->next_character($str, $str, $pos, $length);
        $this->assertEquals($flag, qtype_preg_leaf::NEXT_CHAR_CANNOT_GENERATE);
    }

    function test_generation_string_ends_dollar_assert() {
        $str = new qtype_poasquestion\string("bx\na\nas");
        $pos = 2;
        $length = 0;

        $esca = false;
        $smallescz = false;
        $capescz = false;
        $circumflex = false;
        $dollar = true;
        $subexpr = false;

        $transition = $this->transition_by_regex("[ab\n]", $esca, $smallescz, $capescz, $circumflex, $dollar, $subexpr);
        list($flag, $ch) = $transition->next_character($str, $str, $pos, $length);
        $this->assertEquals($ch, "\n");
    }

    function test_generation_character_with_dollar() {
        $str = new qtype_poasquestion\string("b\na\nas");
        $pos = 1;
        $length = 0;

        $esca = false;
        $smallescz = false;
        $capescz = false;
        $circumflex = false;
        $dollar = true;
        $subexpr = false;

        $transition = $this->transition_by_regex("[ab]", $esca, $smallescz, $capescz, $circumflex, $dollar, $subexpr);
        list($flag, $ch) = $transition->next_character($str, $str, $pos, $length);
        $this->assertEquals($flag, qtype_preg_leaf::NEXT_CHAR_CANNOT_GENERATE);
    }

    function test_generation_one_string() {
        $str = new qtype_poasquestion\string("ab");
        $pos = 1;
        $length = 0;

        $esca = false;
        $smallescz = false;
        $capescz = false;
        $circumflex = false;
        $dollar = false;
        $subexpr = false;

        $transition = $this->transition_by_regex("[x-z]", $esca, $smallescz, $capescz, $circumflex, $dollar, $subexpr);
        list($flag, $ch) = $transition->next_character($str, $str, $pos, $length);
        $this->assertEquals($ch, 'x');
    }

    function test_generation_single_assert() {
        $str = new qtype_poasquestion\string("\n\nas");
        $pos = 0;
        $length = 0;

        $esca = false;
        $smallescz = false;
        $capescz = false;
        $circumflex = false;
        $dollar = false;
        $subexpr = false;

        $transition = $this->transition_by_regex("^", $esca, $smallescz, $capescz, $circumflex, $dollar, $subexpr);
        list($flag, $ch) = $transition->next_character($str, $str, $pos, $length);
        $this->assertEquals($ch, '');
    }

    function test_generation_before_and_after_asserts_false() {
        $str = new qtype_poasquestion\string("a\na\nas");
        $pos = 1;
        $length = 0;

        $esca = false;
        $smallescz = false;
        $capescz = false;
        $circumflex = true;
        $dollar = true;
        $subexpr = false;


        $transition = $this->transition_by_regex("[ab]", $esca, $smallescz, $capescz, $circumflex, $dollar, $subexpr);
        list($flag, $ch) = $transition->next_character($str, $str, $pos, $length);
        $this->assertEquals($flag, qtype_preg_leaf::NEXT_CHAR_CANNOT_GENERATE);
    }

    function test_generation_before_and_after_asserts_true() {
        $str = new qtype_poasquestion\string("abcd\nas");
        $pos = 1;
        $length = 0;

        $esca = false;
        $smallescz = false;
        $capescz = false;
        $circumflex = true;
        $dollar = true;
        $subexpr = false;

        $transition = $this->transition_by_regex("[a-z\n]", $esca, $smallescz, $capescz, $circumflex, $dollar, $subexpr);
        list($flag, $ch) = $transition->next_character($str, $str, $pos, $length);
        $this->assertEquals($ch, "\n");
    }

    function test_generation_single_dollar_in_the_end() {
        $str = new qtype_poasquestion\string("as");
        $pos = 2;
        $length = 0;

        $esca = false;
        $smallescz = false;
        $capescz = false;
        $circumflex = false;
        $dollar = false;
        $subexpr = false;

        $transition = $this->transition_by_regex("$", $esca, $smallescz, $capescz, $circumflex, $dollar, $subexpr);
        list($flag, $ch) = $transition->next_character($str, $str, $pos, $length);
        $this->assertEquals($flag, qtype_preg_leaf::NEXT_CHAR_END_HERE);
    }

    function test_generation_middle_of_the_string() {
        $str = new qtype_poasquestion\string("bcd");
        $pos = 1;
        $length = 0;

        $esca = false;
        $smallescz = false;
        $capescz = false;
        $circumflex = true;
        $dollar = false;
        $subexpr = false;

        $transition = $this->transition_by_regex("[c\n]", $esca, $smallescz, $capescz, $circumflex, $dollar, $subexpr);
        list($flag, $ch) = $transition->next_character($str, $str, $pos, $length);
        $this->assertEquals($ch, "\n");
    }

    function test_generation_last_character() {
        $str = new qtype_poasquestion\string("a\n");
        $pos = 1;
        $length = 0;

        $esca = false;
        $smallescz = false;
        $capescz = true;
        $circumflex = false;
        $dollar = false;
        $subexpr = false;

        $transition = $this->transition_by_regex("[\n]", $esca, $smallescz, $capescz, $circumflex, $dollar, $subexpr);
        list($flag, $ch) = $transition->next_character($str, $str, $pos, $length);
        $this->assertEquals($ch, "\n");
        $this->assertEquals($flag, qtype_preg_leaf::NEXT_CHAR_END_HERE, 'Return flag is not equal to expected');
    }
}
