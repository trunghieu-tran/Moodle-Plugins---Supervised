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
require_once($CFG->dirroot.'/blocks/formal_langs/language_simple_english.php');


class qtype_correctwriting_enum_analyzer_test extends PHPUnit_Framework_TestCase {
    /**
     *  Test for get_enum_change_order function.
     *  Two enumerations without include one to another.
     */
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
        $this->assertTrue( $include_enums == $result->includedenums, 'Error in include array found!Without including.');
        $this->assertTrue( $enum_change_order == $result->order, 'Error in change order found!Without including.');
    }

    /** 
     * Test for get_enum_change_order function.
     * First enumeration include second.
     */
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
        $this->assertTrue( $include_enums == $result->includedenums, 'Error in include array found!Second include in first.');
        $this->assertTrue( $enum_change_order == $result->order, 'Error in change order found!Second include in first.');
    }

    /**
     *  Test for get_enum_change_order function.
     *  Three enumerations. First include to others.
     */
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
        $this->assertTrue( $include_enums == $result->includedenums, 'Error in include array found!Two enums include in other.');
        $this->assertTrue( $enum_change_order == $result->order, 'Error in change order found!Two enums include in other.');
    }

    /** 
     * Test for get_enum_change_order function.
     * Three enumerations. Second enumeration contain first. Third  enumeration contain second.
     */
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
        $this->assertTrue( $include_enums == $result->includedenums, 'Error in include array found!Matrioshka with three enums.');
        $this->assertTrue( $enum_change_order == $result->order, 'Error in change order found!Matrioshka with three enums.');
    }

    /**
     *  Test for get_enum_change_order, six enumeration with difficult include map.
     *  Six enumerations. Second enumeration contain fourth and sixth. Fifth is include in third. 
     *  First contain second and third enumeratins.  
     */
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
        $this->assertTrue( $include_enums == $result->includedenums, 'Error in include array found!Six enums.');
        $this->assertTrue( $enum_change_order == $result->order, 'Error in change order found!Six enums.');
    }

    /**
     * Test for find_enum_orders_in_corrected_string function.
     * Tokens which contains in enumeration are missed in corrected student answer.
     * Tokens: Victoria, Carry, Tom do not contains in corrected student answer.
     */
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
        $this->assertTrue( $equal, 'Error in find orders found!Missing tokens.');
    }

    /**
     * Test for find_enum_orders_in_corrected_string function.
     * Several orders of enumeration in corrected student answer.
     * In corrected student answer we have enumeration tokens in order: Tom, Victoria, Carry, Tom.
     * Base on that order we can get two orders:
     *
     *  Tom, Victoria, Carry 
     *  AND
     *  Victoria, Carry, Tom
     */
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
        $this->assertTrue( $equal, 'Error in find orders found!Several orders.');
    }

    /**
     *  Test for find_enum_orders_in_corrected_string function.
     *  One order of enumeration tokens in corrected student answer.
     */
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
        $this->assertTrue( $equal, 'Error in find orders found!One order.');
    }

    /**
     * Test for find_enum_orders_in_corrected_string function.
     * All orders of enumeration tokens contain in corrected student answer.
     * Order in corrected answer:
     * a nurse, fast bicycle, swimming pool, a nurse, fast bicycle, a nurse, swimming pool, a nurse.
     * We get orders: 
     * a nurse, fast bicycle, swimming pool
     * a nurse, swimming pool, fast bicycle
     * fast bicycle, swimming pool, a nurse
     * fast bicycle, a nurse, swimming pool
     * swimming pool, a nurse, fast bicycle
     * swimmnig pool, fast bicycle, a nurse
     */
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
        $this->assertTrue( $equal, 'Error in find orders found!All orders.');
    }

    /**
     * Test for find_enum_orders_in_corrected_string function
     * One enumeration.
     * Enumeration order in corrected student answer:
     * friend... , neighbors... , friends... , neighbors...
     * Because token "my" is include in both of enumeration.
     */
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
        $this->assertTrue( $equal, 'Error in find orders found!Two include in other.');
    }

    /**
     * Test for find_all_enum_orders_in_corrected_string, two enumerations without including.
     * Enumeration in correct order. Just one order.
     */
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
        $this->assertTrue( $equal, 'Error in all find orders found!Two without including.');
    }

    /**
     * Test for find_all_enum_orders_in_corrected_string.
     * Two enumerations one include in other.
     * Enumeration order in corrected student answer:
     * friend... , neighbors... , friends... , neighbors...
     * Because token "my" is include in both of first enumeration.
     */
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
        $this->assertTrue( $equal, 'Error in all find orders found!One include in other.');
    }

    /**
     * Test for find_all_enum_orders_in_corrected_string.
     * Three enumerations. First and third include in second.
     * Enumeration order in corrected student answer:
     * friend... , neighbors... , friends... , neighbors...
     * Because token "my" is include in both of second enumeration.
     */
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
        $this->assertTrue( $equal, 'Error in all find orders found!Two include in other.');
    }

    /**
     * Test for find_all_enum_orders_in_corrected_string.
     * Three enumerations. First include in second and second include in third.
     * A lot of orders because token 'and' contains in enumerations elements, and include twice in answer.
     */
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
        $this->assertTrue( $equal, 'Error in all find orders found!Matrioshka.');
    }

    /**
     * Test for find_all_enum_orders_in_corrected_string.
     * Three enumerations. First include in second and second include in third.
     * We have several orders: in first enumeration because tokens "my" and ":" is include in both of enumeration,
     * in second because token "Sam" contains twice in corrected answer, and in third because token "Victoria" contains
     * twice in corrected answer.
     */
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
        $this->assertTrue( $equal, 'Error in find orders found!Two include in other.');
    }

    /**
     * Test for find_all_enum_orders_in_corrected_string.
     * Three enumerations. First and third include in second.
     * We have several orders: in first enumeration because token "green" contains twice in corrected answer,
     * in second because tokens "green" and "overall" contains twice in corrected answer, and in third because
     * token "women" contains twice in corrected answer.
     */
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
        $this->assertTrue( $equal, 'Error in all find orders found!Matrioshka with several orders.');
    }

    /**
     * Test for find_all_enum_orders_in_corrected_string.
     * Two enumerations. Second include in first. Strange elements order.
     * We have several orders: in first enumeration because element's tokens are mixed,
     * in second because token "Tom" contains twice in corrected answer.
     */
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
        $this->assertTrue( $equal, 'Error in all find orders found!One include in other.');
    }

    // Test for change_enum_order, two enumerations without including.
    public function testchange_enum_order_without_including() {
        $lang = new block_formal_langs_language_simple_english;
        // Input data.
        $string = 'int k = j / h + t + o - r ; bool g = kill = live ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $order = array(1, 0, 2, -1, 1, 0);
        $enumorder = array(0, 1);
        $enumdescription = array();
        $include = array();
        $include[] = array(-1);
        $include[] = array(-1);
        $enumdescription[] = array(new enum_element(3, 5), new enum_element(7, 7), new enum_element(9, 11));
        $enumdescription[] = array(new enum_element(16, 16), new enum_element(18, 18));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $correct, null);
        // Expected result.
        $string = 'int k = t + j / h + o - r ; bool g = live = kill ;';
        $newcorrect = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(5, 7), new enum_element(3, 3), new enum_element(9, 11));
        $enumdescription[] = array(new enum_element(18, 18), new enum_element(16, 16));
        $newcorrect->enumerations = $enumdescription;
        $newpair = new qtype_correctwriting_string_pair($newcorrect, $correct, null);
        $indexesintable = array(0, 1, 2, 7, 6, 3, 4, 5, 8, 9, 10, 11, 12, 13, 14, 15, 18, 17, 16, 19);
        $newpair->set_indexes_in_table($indexesintable);
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer();
        $temp->change_enum_order($pair, $enumorder, $include, $order);
        $this->assertEquals($newpair, $pair, 'Error change order found!Without including');
    }

    // Test for change_enum_order, two enumeration, one include in other.
    public function testchange_enum_order_one_include_in_other() {
        $lang = new block_formal_langs_language_simple_english;
        // Input data.
        $string = 'Today I meet some friends and my neighbors , with their three children : Victoria , Carry and Tom .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $order = array(1, 0, -1, 2, 0, 1);
        $enumorder = array(1, 0);
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(3, 4), new enum_element(6, 18));
        $enumdescription[] = array(new enum_element(14, 14), new enum_element(16, 16), new enum_element(18, 18));
        $include = array();
        $include[] = array(1);
        $include[] = array(-1);
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $correct, null);
        // Expected result.
        $string = 'Today I meet my neighbors , with their three children : Tom , Victoria and Carry and some friends .';
        $newcorrect = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(17, 18), new enum_element(3, 15));
        $enumdescription[] = array(new enum_element(13, 13), new enum_element(15, 15), new enum_element(11, 11));
        $newcorrect->enumerations = $enumdescription;
        $newpair = new qtype_correctwriting_string_pair($newcorrect, $correct, null);
        $indexesintable = array(0, 1, 2, 6, 7, 8, 9, 10, 11, 12, 13, 18, 15, 14, 17, 16, 5, 3, 4, 19);
        $newpair->set_indexes_in_table($indexesintable);
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer();
        $temp->change_enum_order($pair, $enumorder, $include, $order);
        $this->assertEquals($newpair, $pair, 'Error change order found!One include in other');
    }

    // Test for change_enum_order, three enumeration, two include in other.
    public function testchange_enum_order_two_include_in_other() {
        $lang = new block_formal_langs_language_simple_english;
        // Input data.
        $string = 'Today I meet my friends : Sam , Dine and Michel , and my neighbors , with their three children : Victoria ,';
        $string = $string.' Carry and Tom .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $order = array(1, 0, -1, 0, 2, 1, -1, 2, 1, 0);
        $enumorder = array(1, 2, 0);
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(3, 10), new enum_element(13, 25));
        $enumdescription[] = array(new enum_element(6, 6), new enum_element(8, 8), new enum_element(10, 10));
        $enumdescription[] = array(new enum_element(21, 21), new enum_element(23, 23), new enum_element(25, 25));
        $include = array();
        $include[] = array(1, 2);
        $include[] = array(-1);
        $include[] = array(-1);
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $correct, null);
        // Expected result.
        $string = 'Today I meet my neighbors , with their three children : Tom , Carry and Victoria , and my friends : Sam , ';
        $string = $string.'Michel and Dine .';
        $newcorrect = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(18, 25), new enum_element(3, 15));
        $enumdescription[] = array(new enum_element(21, 21), new enum_element(25, 25), new enum_element(23, 23));
        $enumdescription[] = array(new enum_element(15, 15), new enum_element(13, 13), new enum_element(11, 11));
        $newcorrect->enumerations = $enumdescription;
        $newpair = new qtype_correctwriting_string_pair($newcorrect, $correct, null);
        $indexesintable = array(0, 1, 2, 13, 14, 15, 16, 17, 18, 19 , 20, 25, 22, 23, 24, 21, 11, 12, 3, 4, 5, 6, 7, 10, 9, 8, 26);
        $newpair->set_indexes_in_table($indexesintable);
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer();
        $temp->change_enum_order($pair, $enumorder, $include, $order);
        $this->assertEquals($newpair, $pair, 'Error change order found!Two include in other');
    }

    // Test for change_enum_order, three enumeration, matrioshka.
    public function testchange_enum_order_three_enums_matrioshka() {
        $lang = new block_formal_langs_language_simple_english;
        // Input data.
        $string = 'I see a big group of people , which contains three parts : children , women and men , who was weared in blue ';
        $string = $string.'overall or strange suits with red and green line in front.';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $order = array(0, 1, -1, 1, 0, -1, 1, 2, 0);
        $enumorder = array(0, 1, 2);
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(29, 29), new enum_element(31, 31));
        $enumdescription[] = array(new enum_element(23, 24), new enum_element(26, 34));
        $enumdescription[] = array(new enum_element(13, 13), new enum_element(15, 15), new enum_element(17, 34));
        $include = array();
        $include[] = array(-1);
        $include[] = array(0);
        $include[] = array(0, 1);
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $correct, null);
        // Expected result.
        $string = 'I see a big group of people , which contains three parts : women , men , who was weared in strange suits with';
        $string = $string.' red and green line in front or blue overall and children .';
        $newcorrect = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(24, 24), new enum_element(26, 26));
        $enumdescription[] = array(new enum_element(31, 32), new enum_element(21, 29));
        $enumdescription[] = array(new enum_element(34, 34), new enum_element(13, 13), new enum_element(15, 32));
        $newcorrect->enumerations = $enumdescription;
        $newpair = new qtype_correctwriting_string_pair($newcorrect, $correct, null);
        $indexesintable = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 15, 14, 17, 18, 19, 20, 21, 22, 26, 27, 28, 29, 30, 31,
                                32, 33, 34, 25, 23, 24, 16, 13, 35);
        $newpair->set_indexes_in_table($indexesintable);
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer();
        $temp->change_enum_order($pair, $enumorder, $include, $order);
        $this->assertEquals($newpair, $pair, 'Error change order found!Matrioska');
    }

    // Test for change_enum_order, six enumerations.
    public function testchange_enum_order_six_enums() {
        $lang = new block_formal_langs_language_simple_english;
        // Input data.
        $string = 'int a = b + c * d * f + k * z * e , p = j + h + t * r * w ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $order = array(1, 0, -1, 1, 0, 2, -1, 2, 0, 1, -1, 0, 2, 1, -1, 0, 2, 1, -1, 2, 1, 0);
        $enumorder = array(3, 4, 5, 1, 2, 0);
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(1, 15), new enum_element(17, 27));
        $enumdescription[] = array(new enum_element(3, 3), new enum_element(5, 9), new enum_element(11, 15));
        $enumdescription[] = array(new enum_element(19, 19), new enum_element(21, 21), new enum_element(23, 27));
        $enumdescription[] = array(new enum_element(5, 5), new enum_element(7, 7), new enum_element(9, 9));
        $enumdescription[] = array(new enum_element(23, 23), new enum_element(25, 25), new enum_element(27, 27));
        $enumdescription[] = array(new enum_element(11, 11), new enum_element(13, 13), new enum_element(15, 15));
        $include = array();
        $include[] = array(1, 2, 3, 4, 5);
        $include[] = array(3, 5);
        $include[] = array(4);
        $include[] = array(-1);
        $include[] = array(-1);
        $include[] = array(-1);
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $correct, null);
        // Expected result.
        $string = 'int p = t * w * r + j + h , a = c * f * d + b + e * z * k ;';
        $newcorrect = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(13, 27), new enum_element(1, 11));
        $enumdescription[] = array(new enum_element(21, 21), new enum_element(15, 19), new enum_element(23, 27));
        $enumdescription[] = array(new enum_element(9, 9), new enum_element(11, 11), new enum_element(3, 7));
        $enumdescription[] = array(new enum_element(15, 15), new enum_element(19, 19), new enum_element(17, 17));
        $enumdescription[] = array(new enum_element(3, 3), new enum_element(7, 7), new enum_element(5, 5));
        $enumdescription[] = array(new enum_element(27, 27), new enum_element(25, 25), new enum_element(23, 23));
        $newcorrect->enumerations = $enumdescription;
        $newpair = new qtype_correctwriting_string_pair($newcorrect, $correct, null);
        $indexesintable = array(0, 17, 18, 23, 24, 27, 26, 25, 20, 19, 22, 21, 16, 1, 2, 5, 6, 9, 8, 7, 4, 3, 10, 15, 12, 13, 14,
                                11, 28);
        $newpair->set_indexes_in_table($indexesintable);
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer();
        $temp->change_enum_order($pair, $enumorder, $include, $order);
        $this->assertEquals($newpair, $pair, 'Error change order found!Six enumerations');
    }

    // Test for construct, one lcs.
    public function test__construct_one_lcs() {
        // Input data.
        $lang = new block_formal_langs_language_simple_english;
        $string = 'Billy was like the other rich kids had a nurse , swimming pool and bicycle , but he never played in the street';
        $string = $string.' , did not talk to poor people .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $string = 'Billy was like the other rich kids had a nurse , pool and bicycle , but he never played in street , did not ';
        $string = $string.'talk to poor people .';
        $corrected = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(8, 9), new enum_element(11, 12), new enum_element(14, 14));
        $enumdescription[] = array(new enum_element(17, 22), new enum_element(24, 29));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        // Expected result.
        $pairs = array();
        $pairs[] = $pair;
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer('q',$pair,null,false);
        $this->assertEquals($pairs, $temp->result_pairs(), 'Error in work found!One LCS');
    }

    // Test for construct, several lcs.
    public function test__construct_several_lcs() {
        $lang = new block_formal_langs_language_simple_english;
        $string = 'Billy was like the other rich kids had nurse a , bicycle and or swimming , but he never played in the street ,';
        $string = $string.' did to poor people not talk .';
        $corrected = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        // Expected result.
        $string = 'Billy was like the other rich kids had a nurse , bicycle and swimming pool , but he never played in the street';
        $string = $string.' , did not talk to poor people .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(8, 9), new enum_element(13, 14), new enum_element(11, 11));
        $enumdescription[] = array(new enum_element(17, 22), new enum_element(24, 29));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        $indexesintable = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 14, 13, 11, 12, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26,
                                27, 28, 29, 30);
        $pair->set_indexes_in_table($indexesintable);
        $pairs = array();
        $pairs[] = $pair;
        // Input data.
        $string = 'Billy was like the other rich kids had a nurse , swimming pool and bicycle , but he never played in the street';
        $string = $string.' , did not talk to poor people .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(8, 9), new enum_element(11, 12), new enum_element(14, 14));
        $enumdescription[] = array(new enum_element(17, 22), new enum_element(24, 29));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer('q',$pair,null,false);
        $this->assertEquals($pairs, $temp->result_pairs(), 'Error in work found!Several LCS');
    }

    // Test for construct, same tokens in diffrent enumerations.
    public function test__construct_same_tokens() {
        $lang = new block_formal_langs_language_simple_english;
        $string = 'Today I meet my friends : Sam , Dine and Sam , and my Sam , with their three friends : Sam , Carry and  Sam .';
        $corrected = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        // Expected result.
        $string = 'Today I meet my friends : Michel , Dine and Sam , and my neighbors , with their three children : Victoria ,';
        $string = $string.' Carry and Sam .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(10, 10), new enum_element(8, 8), new enum_element(6, 6));
        $enumdescription[] = array(new enum_element(3, 10), new enum_element(13, 25));
        $enumdescription[] = array(new enum_element(21, 21), new enum_element(23, 23), new enum_element(25, 25));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        $indexesintable = array(0, 1, 2, 3, 4, 5, 10, 7, 8, 9, 6, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26);
        $pair->set_indexes_in_table($indexesintable);
        $pairs = array();
        $pairs[] = $pair;

        $string = 'Today I meet my friends : Michel , Dine and Sam , and my neighbors , with their three children : Sam ,';
        $string = $string.' Carry and Victoria .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(10, 10), new enum_element(8, 8), new enum_element(6, 6));
        $enumdescription[] = array(new enum_element(3, 10), new enum_element(13, 25));
        $enumdescription[] = array(new enum_element(25, 25), new enum_element(23, 23), new enum_element(21, 21));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        $indexesintable = array(0, 1, 2, 3, 4, 5, 10, 7, 8, 9, 6, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 25, 22, 23, 24, 21, 26);
        $pair->set_indexes_in_table($indexesintable);
        $pairs[] = $pair;

        $string = 'Today I meet my friends : Sam , Dine and Michel , and my neighbors , with their three children : Victoria ,';
        $string = $string.' Carry and Sam .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(6, 6), new enum_element(8, 8), new enum_element(10, 10));
        $enumdescription[] = array(new enum_element(3, 10), new enum_element(13, 25));
        $enumdescription[] = array(new enum_element(21, 21), new enum_element(23, 23), new enum_element(25, 25));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        $indexesintable = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26);
        $pair->set_indexes_in_table($indexesintable);
        $pairs[] = $pair;

        $string = 'Today I meet my friends : Sam , Dine and Michel , and my neighbors , with their three children : Sam ,';
        $string = $string.' Carry and Victoria .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(6, 6), new enum_element(8, 8), new enum_element(10, 10));
        $enumdescription[] = array(new enum_element(3, 10), new enum_element(13, 25));
        $enumdescription[] = array(new enum_element(25, 25), new enum_element(23, 23), new enum_element(21, 21));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        $indexesintable = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 25, 22, 23, 24, 21, 26);
        $pair->set_indexes_in_table($indexesintable);
        $pairs[] = $pair;
        // Input data.
        $string = 'Today I meet my friends : Sam , Dine and Michel , and my neighbors , with their three children : Victoria ,';
        $string = $string.' Carry and Sam .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(6, 6), new enum_element(8, 8), new enum_element(10, 10));
        $enumdescription[] = array(new enum_element(3, 10), new enum_element(13, 25));
        $enumdescription[] = array(new enum_element(21, 21), new enum_element(23, 23), new enum_element(25, 25));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer('q',$pair,null,false);
        $this->assertEquals($pairs, $temp->result_pairs(), 'Error in work found!Same tokens');
    }

    // Test for construct, gap of elements.
    public function test__construct_elements_gaps() {
        $lang = new block_formal_langs_language_simple_english;
        $string = 'Billy was like the other rich kids had nurse a , bicycle and or swimming , but he never played in the street ,';
        $string = $string.' did to poor people not talk .';
        $corrected = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        // Expected result.
        $string = 'Billy was like the other rich kids had a nurse , fast bicycle and swimming pool , but he never played in the';
        $string = $string.' street , did not talk to poor people .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(8, 9), new enum_element(14, 15), new enum_element(11, 12));
        $enumdescription[] = array(new enum_element(18, 23), new enum_element(25, 30));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        $indexesintable = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 14, 15, 13, 11, 12, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26,
                                27, 28, 29, 30, 31);
        $pair->set_indexes_in_table($indexesintable);
        $pairs = array();
        $pairs[] = $pair;
        // Input data.
        $string = 'Billy was like the other rich kids had a nurse , swimming pool and fast bicycle , but he never played in the';
        $string = $string.' street , did not talk to poor people .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(8, 9), new enum_element(11, 12), new enum_element(14, 15));
        $enumdescription[] = array(new enum_element(18, 23), new enum_element(25, 30));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer('q',$pair,null,false);
        $this->assertEquals($pairs, $temp->result_pairs(), 'Error in work found!Gaps of elements');
    }

    // Test for construct, no lCS.
    public function test__construct_without_lcs() {
        $lang = new block_formal_langs_language_simple_english;
        $string = 'Cat mouse walk in garden';
        $corrected = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        // Expected result.
        $pairs = array();
        // Input data.
        $string = 'Billy was like the other rich kids had a nurse , swimming pool and bicycle .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(8, 9), new enum_element(11, 12), new enum_element(14, 14));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer('q',$pair,null,false);
        $this->assertEquals($pairs, $temp->result_pairs(), 'Error in work found!No LCS');
    }

    // Test for construct, some include enumeration tokens in wrong order, that take must to keep elements completely.
    public function test__construct_enum_include_some_tokens_wrong_order() {
        $lang = new block_formal_langs_language_simple_english;
        $string = '( b - t ) * ( с / e ) + a . q';
        $corrected = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        // Expected result.
        $string = '( c - t ) * ( b / e ) + a . q ';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(12, 14), new enum_element(0, 10));
        $enumdescription[] = array(new enum_element(6, 10), new enum_element(0, 4));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        $indexesintable = array(10, 11, 12, 13, 14, 9, 4, 5, 6, 7, 8, 3, 0, 1, 2);
        $pair->set_indexes_in_table($indexesintable);
        $pairs = array();
        $pairs[] = $pair;
        // Input data.
        $string = 'a . q + ( b / e ) * ( c - t )';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(0, 2), new enum_element(4, 14));
        $enumdescription[] = array(new enum_element(4, 8), new enum_element(10, 14));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer('q',$pair,null,false);
        $this->assertEquals($pairs, $temp->result_pairs(), 'Error in work found!Include enumeration tokens wrong order');
    }

    // Test for construct, some include enumeration tokens are missed, that take must to keep elements completely.
    public function test__construct_enums_include_some_tokens_missed() {
        $lang = new block_formal_langs_language_simple_english;
        $string = 't - ) * ( / e ) + a .';
        $corrected = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        // Expected result.
        $string = '( c - t ) * ( b / e ) + a . q ';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(12, 14), new enum_element(0, 10));
        $enumdescription[] = array(new enum_element(6, 10), new enum_element(0, 4));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        $indexesintable = array(10, 11, 12, 13, 14, 9, 4, 5, 6, 7, 8, 3, 0, 1, 2);
        $pair->set_indexes_in_table($indexesintable);
        $pairs = array();
        $pairs[] = $pair;
        // Input data.
        $string = 'a . q + ( b / e ) * ( c - t )';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(0, 2), new enum_element(4, 14));
        $enumdescription[] = array(new enum_element(4, 8), new enum_element(10, 14));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer('q',$pair,null,false);
        $this->assertEquals($pairs, $temp->result_pairs(), 'Error in work found!Include enumeration some tokens are missed');
    }

    // Test for construct, include enumeration tokens in wrong order, that take must to keep elements completely.
    public function test__construct_enum_include_tokens_wrong_order() {
        $lang = new block_formal_langs_language_simple_english;
        $string = '( / e b ) * c t - ) + a . q';
        $corrected = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        // Expected result.
        $string = '( b / e ) * ( c - t ) + a . q ';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(12, 14), new enum_element(0, 10));
        $enumdescription[] = array(new enum_element(0, 4), new enum_element(6, 10));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        $indexesintable = array(4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 3, 0, 1, 2);
        $pair->set_indexes_in_table($indexesintable);
        $pairs = array();
        $pairs[] = $pair;
        // Input data.
        $string = 'a . q + ( b / e ) * ( c - t )';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(0, 2), new enum_element(4, 14));
        $enumdescription[] = array(new enum_element(4, 8), new enum_element(10, 14));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer('q',$pair,null,false);
        $this->assertEquals($pairs, $temp->result_pairs(), 'Error in work found!Include enumeration some tokens are missed');
    }

    // Test for construct, enumeration tokens in wrong order, that take must to keep elements completely.One LCS.
    public function test__construct_enum_tokens_wrong_order_one_lcs() {
        $lang = new block_formal_langs_language_simple_english;
        $string = '( a / e ( b t c - ) + . q';
        $corrected = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        // Expected result.
        $string = '( b / e ) % ( c - t ) + a . q ';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(12, 14), new enum_element(0, 10));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        $indexesintable = array(4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 3, 0, 1, 2);
        $pair->set_indexes_in_table($indexesintable);
        $pairs = array();
        $pairs[] = $pair;
        // Input data.
        $string = 'a . q + ( b / e ) % ( c - t )';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(0, 2), new enum_element(4, 14));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer('q',$pair,null,false);
        $this->assertEquals($pairs, $temp->result_pairs(), 'Error in work found!Enumeration tokens in wrong order. One LCS');
    }

    // Test for construct, enumeration tokens in wrong order, that take must to keep elements completely.Some LCS.
    public function test__construct_enum_tokens_wrong_order_some_lcs() {
        $lang = new block_formal_langs_language_simple_english;
        $string = 'e * q b / . ) + ( t ) a -c (';
        $corrected = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        // Expected result.
        $string = '( b / e ) % ( c - t ) + a . q ';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(12, 14), new enum_element(0, 10));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        $indexesintable = array(4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 3, 0, 1, 2);
        $pair->set_indexes_in_table($indexesintable);
        $pairs[] = $pair;
        // Input data.
        $string = 'a . q + ( b / e ) % ( c - t ) ';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(0, 2), new enum_element(4, 14));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        $pairs[] = $pair;
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer('q',$pair,null,false);
        $this->assertEquals($pairs, $temp->result_pairs(), 'Error in work found!Enumeration tokens in wrong order. Some LCS');
    }

    // Test for construct, six enumerations.
    public function test__construct_six_enums() {
        $lang = new block_formal_langs_language_simple_english;
        $string = 'int q = z * w * r + j + h , p = * f * d + b + e * r * z ;';
        $corrected = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        // Expected result.
        $string = 'int p = t * w * r + j + h , a = c * f * d + b + e * k * z ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(13, 27), new enum_element(1, 11));
        $enumdescription[] = array(new enum_element(21, 21), new enum_element(15, 19), new enum_element(23, 27));
        $enumdescription[] = array(new enum_element(9, 9), new enum_element(11, 11), new enum_element(3, 7));
        $enumdescription[] = array(new enum_element(15, 15), new enum_element(19, 19), new enum_element(17, 17));
        $enumdescription[] = array(new enum_element(3, 3), new enum_element(7, 7), new enum_element(5, 5));
        $enumdescription[] = array(new enum_element(25, 25), new enum_element(27, 27), new enum_element(23, 23));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        $indexesintable = array(0, 17, 18, 23, 24, 27, 26, 25, 20, 19, 22, 21, 16, 1, 2, 5, 6, 9, 8, 7, 4, 3, 10, 15, 12, 11, 14,
                                13, 28);
        $pair->set_indexes_in_table($indexesintable);
        $pairs = array();
        $pairs[] = $pair;
        // Input data.
        $string = 'int a = b + c * d * f + k * z * e , p = j + h + t * r * w ;';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(1, 15), new enum_element(17, 27));
        $enumdescription[] = array(new enum_element(3, 3), new enum_element(5, 9), new enum_element(11, 15));
        $enumdescription[] = array(new enum_element(19, 19), new enum_element(21, 21), new enum_element(23, 27));
        $enumdescription[] = array(new enum_element(5, 5), new enum_element(7, 7), new enum_element(9, 9));
        $enumdescription[] = array(new enum_element(23, 23), new enum_element(25, 25), new enum_element(27, 27));
        $enumdescription[] = array(new enum_element(11, 11), new enum_element(13, 13), new enum_element(15, 15));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer('q',$pair,null,false);
        $this->assertEquals($pairs, $temp->result_pairs(), 'Error in work found!Six enumerations');
    }

    // Test for construct, enumeration elements are missed.
    public function test__construct_enum_elems_are_missed() {
        $lang = new block_formal_langs_language_simple_english;
        $string = 'Billy was like the other rich kids had bicycle , nurse and .';
        $corrected = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        // Expected result.
        $string = 'Billy was like the other rich kids had bicycle , a nurse and swimming pool .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(10, 11), new enum_element(13, 14), new enum_element(8, 8));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        $indexesintable = array(0, 1, 2, 3, 4, 5, 6, 7, 14, 10, 8, 9, 13, 11, 12, 15);
        $pair->set_indexes_in_table($indexesintable);
        $pairs = array();
        $pairs[] = $pair;
        // Input data.
        $string = 'Billy was like the other rich kids had a nurse , swimming pool and bicycle .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(8, 9), new enum_element(11, 12), new enum_element(14, 14));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer('q',$pair,null,false);
        $this->assertEquals($pairs, $temp->result_pairs(), 'Error in work found!Enumeration elements are missed');
    }

	/**
	 *  Test for all fuctions. 
	 *  Max count of analyze orders 0.
	 */
    public function test_analyze_zero_orders_count() {
		global $CFG;
        $lang = new block_formal_langs_language_simple_english;
        $string = 'Billy was like the other rich kids had bicycle , nurse and .';
        $corrected = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        // Expected result.
        $string = 'Billy was like the other rich kids had bicycle , a nurse and swimming pool .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(10, 11), new enum_element(13, 14), new enum_element(8, 8));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
        $indexesintable = array(0, 1, 2, 3, 4, 5, 6, 7, 14, 10, 8, 9, 13, 11, 12, 15);
        $pair->set_indexes_in_table($indexesintable);
        $pairs = array();
        // Input data.
        $string = 'Billy was like the other rich kids had a nurse , swimming pool and bicycle .';
        $correct = $lang->create_from_string(new qtype_poasquestion_string($string), 'qtype_correctwriting_proccesedstring');
        $enumdescription = array();
        $enumdescription[] = array(new enum_element(8, 9), new enum_element(11, 12), new enum_element(14, 14));
        $correct->enumerations = $enumdescription;
        $pair = new qtype_correctwriting_string_pair($correct, $corrected, null);
		$CFG->qtype_correctwriting_maxorderscount = 0;
        // Test body.
        $temp = new qtype_correctwriting_enum_analyzer('q',$pair,null,false);
        $this->assertEquals($pairs, $temp->result_pairs(), 'Error in work found!Enumeration elements are missed');
    }
}

