<?php

// This file is part of CorrectWriting question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// CorrectWriting question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// CorrectWriting is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with CorrectWriting.  If not, see <http://www.gnu.org/licenses/>.

global $CFG;
require_once($CFG->dirroot.'/question/type/correctwriting/enum_analyzer.php');
require_once($CFG->dirroot.'/question/type/correctwriting/string_pair.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_base.php');
require_once($CFG->dirroot.'/question/type/correctwriting/processed_string.php');


class qtype_correctwriting_enum_analyzer_test extends PHPUnit_Framework_TestCase {
    // Test for get_enum_change_order, without including in enumerations.
    public function testget_enum_change_order_without_including() {
        $enumdescription = array();
        $include_enums = array();
        $enum_change_order = array();
        // Expected result.
        $enum_change_order = array(0, 1);
        $include_enums[] = array(-1);
        $include_enums[] = array(-1);
        // Enumerations description.
        $enumdescription[] = array(new enum_element(8, 9), new enum_element(11, 11), new enum_element(13, 14));
        $enumdescription[] = array(new enum_element(17, 22), new enum_element(24, 29));
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->get_enum_change_order($enumdescription);
        $this->assertTrue( $include_enums == $result->included_enums, "Error in include array found!Without including.");
        $this->assertTrue( $enum_change_order == $result->order, "Error in change order found!Without including.");
    }

    // Test for get_enum_change_order,second enumeration include in first.
    public function testget_enum_change_order_second_include_in_first() {
        $enumdescription = array();
        $include_enums = array();
        $enum_change_order = array();
        // Expected result.
        $enum_change_order = array(1, 0);
        $include_enums[] = array(1);
        $include_enums[] = array(-1);
        // Enumerations description.
        $enumdescription[] = array(new enum_element(3, 4), new enum_element(6, 18));
        $enumdescription[] = array(new enum_element(14, 14), new enum_element(16, 16), new enum_element(18, 18));
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->get_enum_change_order($enumdescription);
        $this->assertTrue( $include_enums == $result->included_enums, "Error in include array found!Second include in first.");
        $this->assertTrue( $enum_change_order == $result->order, "Error in change order found!Second include in first.");
    }

    // Test for get_enum_change_order,two enumerations include in other.
    public function testget_enum_change_order_two_enums_include_in_other() {
        $enumdescription = array();
        $include_enums = array();
        $enum_change_order = array();
        // Expected result.
        $enum_change_order = array(1, 2, 0);
        $include_enums[] = array(1, 2);
        $include_enums[] = array(-1);
        $include_enums[] = array(-1);
        // Enumerations description.
        $enumdescription[] = array(new enum_element(3, 10), new enum_element(13, 25));
        $enumdescription[] = array(new enum_element(6, 6), new enum_element(8, 8), new enum_element(10, 10));
        $enumdescription[] = array(new enum_element(21, 21), new enum_element(23, 23), new enum_element(25, 25));
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->get_enum_change_order($enumdescription);
        $this->assertTrue( $include_enums == $result->included_enums, "Error in include array found!Two enums include in other.");
        $this->assertTrue( $enum_change_order == $result->order, "Error in change order found!Two enums include in other.");
    }

    // Test for get_enum_change_order, matrioshka with three enumerations.
    public function testget_enum_change_order_matrioshka_with_three_enums() {
        $enumdescription = array();
        $include_enums = array();
        $enum_change_order = array();
        // Expected result.
        $enum_change_order = array(0, 1, 2);
        $include_enums[] = array(-1);
        $include_enums[] = array(0);
        $include_enums[] = array(0, 1);
        // Enumerations description.
        $enumdescription[] = array(new enum_element(29, 29), new enum_element(31, 31));
        $enumdescription[] = array(new enum_element(23, 24), new enum_element(26, 34));
        $enumdescription[] = array(new enum_element(13, 13), new enum_element(15, 15), new enum_element(17, 34));
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->get_enum_change_order($enumdescription);
        $this->assertTrue( $include_enums == $result->included_enums, "Error in include array found!Matrioshka with three enums.");
        $this->assertTrue( $enum_change_order == $result->order, "Error in change order found!Matrioshka with three enums.");
    }

    // Test for get_enum_change_order, six enumeration with difficult include map.
    public function testget_enum_change_order_six_enums() {
        $enumdescription = array();
        $include_enums = array();
        $enum_change_order = array();
        // Expected result.
        $enum_change_order = array(3, 4, 5, 1, 2, 0);
        $include_enums[] = array(1, 2, 3, 4, 5);
        $include_enums[] = array(3, 5);
        $include_enums[] = array(4);
        $include_enums[] = array(-1);
        $include_enums[] = array(-1);
        $include_enums[] = array(-1);
        // Enumerations description.
        $enumdescription[] = array(new enum_element(1, 15), new enum_element(17, 27));
        $enumdescription[] = array(new enum_element(3, 3), new enum_element(5, 9), new enum_element(11, 15));
        $enumdescription[] = array(new enum_element(19, 19), new enum_element(21, 21), new enum_element(23, 27));
        $enumdescription[] = array(new enum_element(5, 5), new enum_element(7, 7), new enum_element(9, 9));
        $enumdescription[] = array(new enum_element(23, 23), new enum_element(25, 25), new enum_element(27, 27));
        $enumdescription[] = array(new enum_element(11, 11), new enum_element(13, 13), new enum_element(15, 15));
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->get_enum_change_order($enumdescription);
        $this->assertTrue( $include_enums == $result->included_enums, "Error in include array found!Six enums.");
        $this->assertTrue( $enum_change_order == $result->order, "Error in change order found!Six enums.");
    }

    // Test for find_enum_orders_in_corrected_string, enumeration tokens are missed.
    public function testfind_enum_orders_in_corrected_string_missing_enum_tokens() {
        $correctanswer = array();
        $correctedanswer = array();
        $enumdescription = array();
        $orders = array();
        $lang = new block_formal_langs_language_simple_english;
        $number = 0;
        // Expected result.
        $orders[] = array(2, 1, 0);
        $orders[] = array(2, 0, 1);
        $orders[] = array(1, 2, 0);
        $orders[] = array(1, 0, 2);
        $orders[] = array(0, 2, 1);
        $orders[] = array(0, 1, 2);
        // Input data.
        $number = 1;
        $enumdescription[] = array(new enum_element(3, 4), new enum_element(6, 18));
        $enumdescription[] = array(new enum_element(14, 14), new enum_element(16, 16), new enum_element(18, 18));
        $string = 'Today I meet some friends and my neighbors , with their three children : Victoria , Carry and Tom .';
        $correctanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        $string = 'Today I meet my friends : Sam , Dine and Michel , and my neighbors , with their three children .';
        $correctedanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->find_enum_orders_in_corrected_string($correctanswer->stream->tokens, $correctedanswer->stream->tokens,
                                                              $enumdescription, $number);
        $equal = true;
        foreach ($orders as $current_order) {
            if (false === array_search($current_order, $result)) {
                    $equal = false;
            }
        }
        if (count($orders) != count($result)) {
            $equal = false;
        }
        $this->assertTrue( $equal, "Error in find orders found!Missing tokens.");
    }

    // Test for find_enum_orders_in_corrected_string, several orders are expected.
    public function testfind_enum_orders_in_corrected_string_several_orders() {
        $correctanswer = array();
        $correctedanswer = array();
        $enumdescription = array();
        $orders = array();
        $lang = new block_formal_langs_language_simple_english;
        $number = 0;
        // Expected result.
        $orders[] = array(2, 0, 1);
        $orders[] = array(0, 1, 2);
        // Input data.
        $number = 1;
        $enumdescription[] = array(new enum_element(3, 4), new enum_element(6, 18));
        $enumdescription[] = array(new enum_element(14, 14), new enum_element(16, 16), new enum_element(18, 18));
        $string = 'Today I meet some friends and my neighbors, with their three children: Victoria, Carry and Tom .';
        $correctanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        $string = 'Today I meet my friends: Sam, Dine and Michel, Tom, and my neighbors, with their three children: Victoria, ';
        $string = $string.'Carry and Tom.';
        $correctedanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->find_enum_orders_in_corrected_string($correctanswer->stream->tokens, $correctedanswer->stream->tokens,
                                                              $enumdescription, $number);
        $equal = true;
        foreach ($orders as $current_order) {
            if (false === array_search($current_order, $result)) {
                $equal = false;
            }
        }
        if (count($orders) != count($result)) {
            $equal = false;
        }
        $this->assertTrue( $equal, "Error in find orders found!Several orders.");
    }

    // Test for find_enum_orders_in_corrected_string, several orders are expected.
    public function testfind_enum_orders_in_corrected_string_one_order() {
        $correctanswer = array();
        $correctedanswer = array();
        $enumdescription = array();
        $orders = array();
        $result = array();
        $lang = new block_formal_langs_language_simple_english;
        $number = 0;
        // Expected result.
        $orders[] = array(1, 0);
        // Input data.
        $number = 0;
        $enumdescription[] = array(new enum_element(3, 4), new enum_element(6, 18));
        $enumdescription[] = array(new enum_element(14, 14), new enum_element(16, 16), new enum_element(18, 18));
        $string = 'Today I meet some friends and my neighbors, with their three children: Victoria, Carry and Tom.';
        $correctanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        $string = 'Today I meet my neighbors, with their three children: Victoria, Tom and and Carry some friends.';
        $correctedanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->find_enum_orders_in_corrected_string($correctanswer->stream->tokens, $correctedanswer->stream->tokens,
                                                              $enumdescription, $number);
        $equal = true;
        foreach ($orders as $current_order) {
            if (false === array_search($current_order, $result)) {
                $equal = false;
            }
        }
        if (count($orders) != count($result)) {
            $equal = false;
        }
        $this->assertTrue( $equal, "Error in find orders found!One order.");
    }

    // Test for find_enum_orders_in_corrected_string, all orders are expected.
    public function testfind_enum_orders_in_corrected_string_all_orders() {
        $correctanswer = array();
        $correctedanswer = array();
        $enumdescription = array();
        $orders = array();
        $result = array();
        $lang = new block_formal_langs_language_simple_english;
        $number = 0;
        // Expected result.
        $orders[] = array(2, 1, 0);
        $orders[] = array(2, 0, 1);
        $orders[] = array(1, 2, 0);
        $orders[] = array(1, 0, 2);
        $orders[] = array(0, 2, 1);
        $orders[] = array(0, 1, 2);
        // Input data.
        $number = 0;
        $enumdescription[] = array(new enum_element(8, 9), new enum_element(11, 12), new enum_element(14, 15));
        $string = 'Billy was like the other rich kids had a nurse, fast bicycle and swimming pool, but he never played in the ';
        $string = $string.'street, did not talk to poor people.';
        $correctanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        $string = 'Billy was like the other rich kids had a, bicycle swimming and and nurse, fast, but he never played in a ';
        $string = $string.'street or pool a.';
        $correctedanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->find_enum_orders_in_corrected_string($correctanswer->stream->tokens, $correctedanswer->stream->tokens,
                                                              $enumdescription, $number);
        $equal = true;
        foreach ($orders as $current_order) {
            if (false === array_search($current_order, $result)) {
                $equal = false;
            }
        }
        if (count($orders) != count($result)) {
            $equal = false;
        }
        $this->assertTrue( $equal, "Error in find orders found!All orders.");
    }

    // Test for find_enum_orders_in_corrected_string, two include in other.
    public function testfind_enum_orders_in_corrected_string_two_include_in_other() {
        $correctanswer = array();
        $correctedanswer = array();
        $enumdescription = array();
        $orders = array();
        $result = array();
        $number = 1;
        $lang = new block_formal_langs_language_simple_english;
        // Expected result.
        $orders[] = array(0, 1);
        $orders[] = array(1, 0);
        // Input data.
        $enumdescription[] = array(new enum_element(6, 6), new enum_element(8, 8), new enum_element(10, 10));
        $enumdescription[] = array(new enum_element(3, 10), new enum_element(13, 25));
        $enumdescription[] = array(new enum_element(21, 21), new enum_element(23, 23), new enum_element(25, 25));
        $string = 'Today I meet my friends: Sam, Dine and Michel, and my neighbors, with their three children: Victoria, Carry';
        $string = $string.'and Tom.';
        $correctanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        $string = 'Today I meet my friends: Sam, Dine and Michel, and my neighbors, with their three children: Tom, Carry and ';
        $string = $string.'Victoria.';
        $correctedanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->find_enum_orders_in_corrected_string($correctanswer->stream->tokens, $correctedanswer->stream->tokens,
                                                              $enumdescription, $number);
        $equal = true;
        foreach ($orders as $current_order) {
            if (false === array_search($current_order, $result)) {
                $equal = false;
            }
        }
        if (count($orders) != count($result)) {
            $equal = false;
        }
        $this->assertTrue( $equal, "Error in find orders found!Two include in other.");
    }

    // Test for find_all_enum_orders_in_corrected_string, two enumerations without including.
    public function testfind_all_enum_orders_in_corrected_string_two_without_including() {
        $enumdescription = array();
        $correctanswer = array();
        $correctedanswer = array();
        $expectedorders = array();
        $result = array();
        $lang = new block_formal_langs_language_simple_english;
        $temp = 0;
        // Expected result.
        $expectedorders[] = array(0, 2, 1, -1, 0, 1);
        // Input data.
        $enumdescription[] = array(new enum_element(8, 9), new enum_element(11, 11), new enum_element(13, 14));
        $enumdescription[] = array(new enum_element(17, 22), new enum_element(24, 29));
        $string = 'Billy was like the other rich kids had a nurse, bicycle and swimming pool, but he never played in the street, ';
        $string = $string.'did not talk to poor people.';
        $correctanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        $string = 'Billy was like the other rich kids had a nurse, pool and bicycle, but he never played in street, did not talk ';
        $string = $string.'to poor people.';
        $correctedanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->find_all_enum_orders_in_corrected_string($correctanswer->stream->tokens,
                                                                  $correctedanswer->stream->tokens,
                                                                  $enumdescription);
        $equal = true;
        foreach ($expectedorders as $current_order) {
            if (false === array_search($current_order, $result)) {
                $equal = false;
            }
        }
        if (count($expectedorders) != count($result)) {
            $equal = false;
        }
        $this->assertTrue( $equal, "Error in all find orders found!Two without including.");
    }

    // Test for find_all_enum_orders_in_corrected_string, two enumerations one include in other.
    public function testfind_all_enum_orders_in_corrected_string_one_include_in_other() {
        $enumdescription = array();
        $correctanswer = array();
        $correctedanswer = array();
        $expectedorders = array();
        $result = array();
        $temp = 0;
        $lang = new block_formal_langs_language_simple_english;
        // Expected result.
        $expectedorders[] = array(0, 1, -1, 0, 2, 1);
        $expectedorders[] = array(1, 0, -1, 0, 2, 1);
        // Input data.
        $enumdescription[] = array(new enum_element(3, 4), new enum_element(6, 18));
        $enumdescription[] = array(new enum_element(14, 14), new enum_element(16, 16), new enum_element(18, 18));
        $string = 'Today I meet some friends and my neighbors, with their three children: Victoria, Carry and Tom.';
        $correctanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        $string = 'Today I meet my friends and my neighbors, with their three children: Victoria, Tom and Carry.';
        $correctedanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->find_all_enum_orders_in_corrected_string($correctanswer->stream->tokens, $correctedanswer->stream->tokens,
                                                                  $enumdescription);
        $equal = true;
        foreach ($expectedorders as $current_order) {
            if (false === array_search($current_order, $result)) {
                $equal = false;
            }
        }
        if (count($expectedorders) != count($result)) {
            $equal = false;
        }
        $this->assertTrue( $equal, "Error in all find orders found!One include in other.");
    }

    // Test for find_all_enum_orders_in_corrected_string, two enumerations one include in other.
    public function testfind_all_enum_orders_in_corrected_string_two_include_in_other() {
        $enumdescription = array();
        $correctanswer = array();
        $correctedanswer = array();
        $expectedorders = array();
        $result = array();
        $lang = new block_formal_langs_language_simple_english;
        $temp = 0;
        // Expected result.
        $expectedorders[] = array(0, 1, 2, -1, 0, 1, -1, 2, 1, 0);
        $expectedorders[] = array(0, 1, 2, -1, 1, 0, -1, 2, 1, 0);
        // Input data.
        $enumdescription[] = array(new enum_element(6, 6), new enum_element(8, 8), new enum_element(10, 10));
        $enumdescription[] = array(new enum_element(3, 10), new enum_element(13, 25));
        $enumdescription[] = array(new enum_element(21, 21), new enum_element(23, 23), new enum_element(25, 25));
        $string = 'Today I meet my friends: Sam, Dine and Michel, and my neighbors, with their three children: Victoria, ';
        $string = $string.'Carry and Tom.';
        $correctanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        $string = 'Today I meet my friends: Sam, Dine and Michel, and my neighbors, with their three children: Tom, Carry and ';
        $string = $string.'Victoria.';
        $correctedanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->find_all_enum_orders_in_corrected_string($correctanswer->stream->tokens, $correctedanswer->stream->tokens,
                                                                  $enumdescription);
        $equal = true;
        foreach ($expectedorders as $current_order) {
            if (false === array_search($current_order, $result)) {
                $equal = false;
            }
        }
        if (count($expectedorders) != count($result)) {
            $equal = false;
        }
        $this->assertTrue( $equal, "Error in all find orders found!Two include in other.");
    }

    // Test for find_all_enum_orders_in_corrected_string, matrioska.
    public function testfind_all_enum_orders_in_corrected_string_matrioska_three_enums() {
        $enumdescription = array();
        $correctanswer = array();
        $correctedanswer = array();
        $expectedorders = array();
        $result = array();
        $lang = new block_formal_langs_language_simple_english;
        $temp = 0;
        // Expected result.
        $expectedorders[] = array(1, 0, -1, 0, 1, -1, 2, 1, 0);
        $expectedorders[] = array(1, 0, -1, 1, 0, -1, 2, 1, 0);
        $expectedorders[] = array(1, 0, -1, 0, 1, -1, 1, 0, 2);
        $expectedorders[] = array(1, 0, -1, 1, 0, -1, 1, 0, 2);
        $expectedorders[] = array(1, 0, -1, 0, 1, -1, 1, 2, 0);
        $expectedorders[] = array(1, 0, -1, 1, 0, -1, 1, 2, 0);
        // Input data.
        $enumdescription[] = array(new enum_element(29, 29), new enum_element(31, 31));
        $enumdescription[] = array(new enum_element(23, 24), new enum_element(26, 34));
        $enumdescription[] = array(new enum_element(13, 13), new enum_element(15, 15), new enum_element(17, 35));
        $string = 'I see a big group of people, which contains three parts: children, women and men, who was weared in blue ';
        $string = $string.'overall or strange suits with red and green line in front.';
        $correctanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        $string = 'I see a big group of people, which contains three parts: women, children and men, who was weared in blue ';
        $string = $string.'overall or strange suits with green and red line in front.';
        $correctedanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->find_all_enum_orders_in_corrected_string($correctanswer->stream->tokens, $correctedanswer->stream->tokens,
                                                                  $enumdescription);
        $equal = true;
        foreach ($expectedorders as $current_order) {
            if (false === array_search($current_order, $result)) {
                $equal = false;
            }
        }
        if (count($expectedorders) != count($result)) {
            $equal = false;
        }
        $this->assertTrue( $equal, "Error in all find orders found!Matrioshka.");
    }

    // Test for find_enum_orders_in_corrected_string, two include in other several orders.
    public function testfind_all_enum_orders_in_corrected_string_two_include_in_other_several_orders() {
        $correctanswer = array();
        $correctedanswer = array();
        $enumdescription = array();
        $expectedorders = array();
        $result = array();
        $lang = new block_formal_langs_language_simple_english;
        // Expected result.
        $expectedorders[] = array(0, 1, 2, -1, 0, 1, -1, 2, 1, 0);
        $expectedorders[] = array(0, 1, 2, -1, 0, 1, -1, 0, 2, 1);
        $expectedorders[] = array(0, 1, 2, -1, 1, 0, -1, 0, 2, 1);
        $expectedorders[] = array(0, 1, 2, -1, 1, 0, -1, 2, 1, 0);
        $expectedorders[] = array(1, 2, 0, -1, 0, 1, -1, 2, 1, 0);
        $expectedorders[] = array(1, 2, 0, -1, 0, 1, -1, 0, 2, 1);
        $expectedorders[] = array(1, 2, 0, -1, 1, 0, -1, 0, 2, 1);
        $expectedorders[] = array(1, 2, 0, -1, 1, 0, -1, 2, 1, 0);
        // Input data.
        $enumdescription[] = array(new enum_element(6, 6), new enum_element(8, 8), new enum_element(10, 10));
        $enumdescription[] = array(new enum_element(3, 10), new enum_element(13, 25));
        $enumdescription[] = array(new enum_element(21, 21), new enum_element(23, 23), new enum_element(25, 25));
        $string = 'Today I meet my friends: Sam, Dine and Michel, and my neighbors, with their three children: Victoria, Carry ';
        $string = $string.'and Tom .';
        $correctanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        $string =  'Today I meet my friends: Sam, Dine, Victoria and Michel, and my neighbors, with their three children: Tom, ';
        $string = $string.'Carry, Sam and Victoria.';
        $correctedanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->find_all_enum_orders_in_corrected_string($correctanswer->stream->tokens, $correctedanswer->stream->tokens,
                                                                  $enumdescription);
        $equal = true;
        foreach ($expectedorders as $current_order) {
            if (false === array_search($current_order, $result)) {
                $equal = false;
            }
        }
        if (count($expectedorders) != count($result)) {
            $equal = false;
        }
        $this->assertTrue( $equal, "Error in find orders found!Two include in other.");
    }

    // Test for find_all_enum_orders_in_corrected_string, matrioska.
    public function testfind_all_enum_orders_in_corrected_string_matrioska_three_enums_several_orders() {
        $enumdescription = array();
        $correctanswer = array();
        $correctedanswer = array();
        $expectedorders = array();
        $result = array();
        $lang = new block_formal_langs_language_simple_english;
        $temp = 0;
        // Expected result.
        $expectedorders[] = array(1, 0, -1, 0, 1, -1, 2, 1, 0);
        $expectedorders[] = array(1, 0, -1, 0, 1, -1, 2, 0, 1);
        $expectedorders[] = array(1, 0, -1, 0, 1, -1, 1, 0, 2);
        $expectedorders[] = array(1, 0, -1, 0, 1, -1, 1, 2, 0);
        $expectedorders[] = array(1, 0, -1, 0, 1, -1, 0, 2, 1);
        $expectedorders[] = array(1, 0, -1, 0, 1, -1, 0, 1, 2);

        $expectedorders[] = array(1, 0, -1, 1, 0, -1, 2, 1, 0);
        $expectedorders[] = array(1, 0, -1, 1, 0, -1, 2, 0, 1);
        $expectedorders[] = array(1, 0, -1, 1, 0, -1, 1, 0, 2);
        $expectedorders[] = array(1, 0, -1, 1, 0, -1, 1, 2, 0);
        $expectedorders[] = array(1, 0, -1, 1, 0, -1, 0, 2, 1);
        $expectedorders[] = array(1, 0, -1, 1, 0, -1, 0, 1, 2);

        $expectedorders[] = array(0, 1, -1, 1, 0, -1, 2, 1, 0);
        $expectedorders[] = array(0, 1, -1, 1, 0, -1, 2, 0, 1);
        $expectedorders[] = array(0, 1, -1, 1, 0, -1, 1, 0, 2);
        $expectedorders[] = array(0, 1, -1, 1, 0, -1, 1, 2, 0);
        $expectedorders[] = array(0, 1, -1, 1, 0, -1, 0, 2, 1);
        $expectedorders[] = array(0, 1, -1, 1, 0, -1, 0, 1, 2);

        $expectedorders[] = array(0, 1, -1, 0, 1, -1, 2, 1, 0);
        $expectedorders[] = array(0, 1, -1, 0, 1, -1, 2, 0, 1);
        $expectedorders[] = array(0, 1, -1, 0, 1, -1, 1, 2, 0);
        $expectedorders[] = array(0, 1, -1, 0, 1, -1, 1, 0, 2);
        $expectedorders[] = array(0, 1, -1, 0, 1, -1, 0, 2, 1);
        $expectedorders[] = array(0, 1, -1, 0, 1, -1, 0, 1, 2);
        // Input data.
        $enumdescription[] = array(new enum_element(29, 29), new enum_element(31, 31));
        $enumdescription[] = array(new enum_element(23, 24), new enum_element(26, 34));
        $enumdescription[] = array(new enum_element(13, 13), new enum_element(15, 15), new enum_element(17, 35));
        $string = 'I see a big group of people, which contains three parts: children, women and men, who was weared in blue ';
        $string = $string.'overall or strange suits with red and green line in front.';
        $correctanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        $string =  'I see a big group of people, which contains three parts: women, children and women men, who was weared in ';
        $string = $string.'blue overall or strange suits with green and red line in front or green overall.';
        $correctedanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->find_all_enum_orders_in_corrected_string($correctanswer->stream->tokens, $correctedanswer->stream->tokens,
                                                                  $enumdescription);
        $equal = true;
        foreach ($expectedorders as $current_order) {
            if (false === array_search($current_order, $result)) {
                $equal = false;
            }
        }
        if (count($expectedorders) != count($result)) {
            $equal = false;
        }
        $this->assertTrue( $equal, "Error in all find orders found!Matrioshka with several orders.");
    }

    // Test for find_all_enum_orders_in_corrected_string, two enumerations one include in other, strange elements order.
    public function testfind_all_enum_orders_in_corrected_string_one_include_in_other_strange_orders() {
        $enumdescription = array();
        $correctanswer = array();
        $correctedanswer = array();
        $lang = new block_formal_langs_language_simple_english;
        $expectedorders = array();
        $result = array();
        $temp = 0;
        // Expected result.
        $expectedorders[] = array(0, 1, -1, 0, 2, 1);
        $expectedorders[] = array(1, 0, -1, 0, 1, 2);
        $expectedorders[] = array(0, 1, -1, 0, 1, 2);
        $expectedorders[] = array(1, 0, -1, 0, 2, 1);
        // Input data.
        $enumdescription[] = array(new enum_element(3, 4), new enum_element(6, 18));
        $enumdescription[] = array(new enum_element(14, 14), new enum_element(16, 16), new enum_element(18, 18));
        $string = 'Today I meet some friends and my neighbors, with their three children: Victoria, Carry and Tom.';
        $correctanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        $string =  'Today I meet my neighbors and my friends, with their three children: Victoria, Tom and Carry and some ';
        $string = $string.'children Tom.';
        $correctedanswer = $lang->create_from_string($string, 'qtype_correctwriting_proccesedstring');
        // Test body.
        $temp= new qtype_correctwriting_enum_analyzer();
        $result = $temp->find_all_enum_orders_in_corrected_string($correctanswer->stream->tokens, $correctedanswer->stream->tokens,
                                                                  $enumdescription);
        $equal = true;
        foreach ($expectedorders as $current_order) {
            if (false === array_search($current_order, $result)) {
                $equal = false;
            }
        }
        if (count($expectedorders) != count($result)) {
            $equal = false;
        }
        $this->assertTrue( $equal, "Error in all find orders found!One include in other.");
    }
}

