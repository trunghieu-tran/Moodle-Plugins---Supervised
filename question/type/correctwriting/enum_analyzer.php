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

defined('MOODLE_INTERNAL') || die();

//Other necessary requires
require_once($CFG->dirroot.'/question/type/correctwriting/sequence_analyzer.php');

class  qtype_correctwriting_enum_analyzer {

    /**
     * Array which contains orders of all enumerations, and give maximum length of LCS of correctstring and correctedstring
     * @var array
     */
    private $orders;

    private $fitness;               //Fitness for response

    /**
     * Function to find order of changing enumeration, and included enumerations to all enumerations
     * @param array $enumdescription enumerations description
     */
    private function get_enum_change_order($enumdescription){
        $change_order_and_included_enums = new stdClass();
        //add fields to stdClass object
        $change_order_and_included_enums->order = array(); //enumerations change order
        $change_order_and_included_enums->included_enums = array();//included enumeration numbers to all enumerations
        //add empty arrays of included enumerations
        for($i = 0; $i < count($enumdescription); $i++){
            $change_order_and_included_enums->included_enums[$i] = array();
        }
        $all_included_enums_in_order = true;//variable show that all, included in current enumeration, enumerations are fill
                                            //in enumerations change order
        //find included enumerations to all enumerations
        for($i = 0; $i < count($enumdescription); $i++){
            for($j = 0; $j < count($enumdescription); $j++){
                //if is not same enumerations
                if( $i != $j){
                    //boolean variables to check including of enumerations
                    $compare_left_borders_of_enums=$enumdescription[$i][0]->$begin >= $enumdescription[$j][0]->$begin;
                    $compare_right_borders_of_enums=$enumdescription[$i][0]->$end >= $enumdescription[$j][0]->$end;
                    //if left borders of i enum rather than j enum and right borders of j enum rather than i enum and
                    //in included enums array for j enum not contains i
                    if( $compare_left_borders_of_enums && ! $compare_right_borders_of_enums
                                && !in_array($i,$change_order_and_included_enums->incuded_enums[$j])){
                        //add i to included enums array for j enum
                        $change_order_and_included_enums->included_enums[$j][] = $i;
                    }
                    //else if left borders of j enum rather than i enum and right borders of i enum rather than j enum and
                    //in included enums array for i enum not contains j
                    else if(! $compare_left_borders_of_enums && $compare_right_borders_of_enums
                                && !in_array($j,$change_order_and_included_enums->incuded_enums[$i])){
                        //add j to included enums array for i enum
                        $change_order_and_included_enums->included_enums[$i][] = $j;
                    }
                }
            }
        }
        //create enumerations change order
        while( count($change_order_and_included_enums->order) != count($enumdescription)){
            for($i = 0; $i < count($change_order_and_included_enums->included_enums); $i++){
                $all_included_enums_in_order = true;
                //check that all included enumerations are in order
                for($j = 0; $j < count($change_order_and_included_enums->included_enums[$i]); $j++)
			    	if(!in_array($change_order_and_included_enums->included_enums[$i], $change_order_and_included_enums->order))
                        $all_included_enums_in_order = false;
			    //if all included enumerations are in order and current enumeration aren't in order
                if($all_included_enums_in_order && in_array($i, $change_order_and_included_enums->order))
                //add current enumeration to order
                    $change_order_and_included_enums->order[] = $i;
            }
        }
        //ending of included arrays to enumeration which don't contains others enumerations
        for($i = 0; $i< count($change_order_and_included_enums->included_enums); $i++){
            //array of included enumeration is empty
            if(count($change_order_and_included_enums->included_enums[$i]) == 0)
                //add -1 to array
                $change_order_and_included_enums->included_enums[$i][] = -1;
	    }
        return $change_order_and_included_enums;
    }

    /**
     * Function to find orders specified enumeration in correctstring, based on analyze correctedstring
     * @param array $correctanswer - correct answer
     * @param array $correctedanswer - corrected student answer
     * @param array $enumdescription - enumerations description
     * @param integer $number - number of enumeration to search orders
     * @return array of find orders
     */
    private function find_enum_orders_in_corrected_string($correctanswer, $correctedanswer, $enumdescription, $number) {
        $indexes_of_tokens = array();// Array with contains indexes of tokens which are members of enumeration...
                                                     // ... in corrected student answer.
        $indexes_of_elements = array();// Array with indexes of elements of enumeration in corrected answer.
        $elements_in_corrected_answer = array();// Array to keep indexes elements of enumeration in order, which...
                                                // ... it has in corrected student answer, with included missed elements.
        $current_order = array();// Array to keep current order of enumeration elements.
        $token_number = 0;// Number of tokens in enumeration, whose indexes are searched in corrected answer on current iteration.
        $insert_place = 0;// Place to insert in one of arrays.
        $have_next_order = false;// Is has in $elements_in_corrected_answer next order.
        $number_of_element_to_skip = 0;// Element number, which will be skip in elements_in_corrected_answer on current iteration.
        $enum_orders=array();// Array which contain find orders of enumeration.
        // For all elements of enumeration create array of indexes in corrected answers, which are kept in ascending order.
        for ($i = 0; $i < count($enumdescription); $i++) {
            // Find tokens which include in current element of enumeration individually.
            $indexes_of_tokens = array();
            $token_number = 0;
            // For all tokens of current element of enumeration find indexes of equal tokens in corrected student answer.
            for ($j = $enumdescription[$number][$i]->begin; $j < $enumdescription[$number][$i]->end +1; $j++) {
                $indexes_of_tokens[$token_number] = array_keys($correctedanswer, $correctanswer[$j]);
                $token_number++;
            }
            // Create array of indexes tokens of current element enumeration in corrected student answer.
            for ($j = 0; $j < count($indexes_of_tokens); $j++) {
                // If token number j are find in corrected answer, add it's indexes in $indexes_of_elements_in_corrected_answer[i].
                if ($indexes_of_tokens[$j][0]!=-1) {
                    // For all indexes find place to insert, because array is kept in  ascending order.
                    for ($k = 0; $k < count($indexes_of_tokens[$j]); $k++) {
                        // Find place to insert.
                        $insert_place = 0;
                        while ($insert_place < count($indexes_of_elements[$i]) &&
                                   $indexes_of_elements[$i][$insert_place] < $indexes_of_tokens[$j][$k]) {
                            $insert_place++;
                        }
                        // Insert current index in array.
                        array_splice($indexes_of_elements[$i], $insert_place, 0, $indexes_of_tokens[$j][$k]);
                    }
                }
            }
        }
        // Fill array of indexes elements of enumeration in ascending order.
        for ($i = 0; $i < count($enumdescription); $i++) {
            // Find place to insert in array for all indexes.
            for ($j = 0; $j < count($indexes_of_elements[$i]); $j++) {
                // Find place to insert.
                $insert_place = 0;
                while ($insert_place < count($elements_in_corrected_answer) &&
                           $elements_in_corrected_answer[$insert_place] < $indexes_of_elements[$i][$j]) {
                    $insert_place++;
                }
                // Insert current index in array.
                array_splice($elements_in_corrected_answer, $insert_place, 0, $indexes_of_elements[$i][$j]);
            }
        }
        // Change indexes by numbers of elements enumeration, execute repeat contiguous elements.
        for ($i = 0; $i < count($elements_in_corrected_answer); $i++) {
            // Find element by index.
            for ($j = 0; $j < count($indexes_of_elements); $j++) {
                if ( in_array($elements_in_corrected_answer[$i], $indexes_of_elements[$j])) {
                    // Change index by element number.
                    $elements_in_corrected_answer[$i] = $j;
                    // If element are repeated.
                    if ($i != 0 && $elements_in_corrected_answer[$i - 1] == $elements_in_corrected_answer[$i]) {
                        unset($elements_in_corrected_answer[$i]);
                        $i--;
                    }
                }
            }
        }
        // Add to array number of element, which do not contains in corrected student answer.
        for ($i = 0; $i < count($enumdescription[$number]); $i++) {
            // Check that contains current element in order or not.
            if (!in_array($i, $elements_in_corrected_answer)) {
                // If element does not contains in order, add it between all elements pairs and to begin and end of order.
                for ($j = 0; $j < count($elements_in_corrected_answer); $j += 2) {
                    array_splice($elements_in_corrected_answer, $j, 0, $i);
                }
                // Add to end of order.
                if (count($elements_in_corrected_answer) or array_pop($elements_in_corrected_answer) != $i+1) {
                     $elements_in_corrected_answer[] = $i+1;
                }
            }
        }
        // Create orders array based on array of elements numbers which are ordered like in corrected student answer.
        do {
            $number_of_element_to_skip = 0;
            do {
                $current_order = array();// Clear current enumeration order.
                // Fill current order by $elements_in_corrected_answer.
                for ($i = 0; $i < count($elements_in_corrected_answer); $i++) {
                    // If that element number does not contains in current order and...
                    // ...his index does not equal number to skip or number to skip is zero.
                    if (!in_array($elements_in_corrected_answer[$i], $current_order) &&
                            ($i != $number_of_element_to_skip || $number_of_element_to_skip == 0)) {
                        // Add element to order.
                        $current_order[] = $elements_in_corrected_answer[$i];
                    }
                }
                $number_of_element_to_skip++;// Inc number to skip.
                $enum_orders[] =$current_order;// Add order to array of enum orders.
            } while ($number_of_element_to_skip != count($elements_in_corrected_answer));
            // Remove duplicate orders.
            array_unique($enum_orders);
            // Remove first element from array of elements numbers which are ordered like in corrected student answer.
            unset($elements_in_corrected_answer[0]);
            // Check that have next order in array of elements numbers which are ordered like in corrected student answer.
            $have_next_order= true;
            for ($i = 0; $i < count($enumdescription[$number]; $i++) {
                if (!in_array($i, $elements_in_corrected_answer)) {
                    $have_next_order = false;
                }
            }
        } while ($have_next_order);
        // Remove from array of orders not complete orders.
        for ($i = 0; $i < count($enum_orders); $i++) {
            if(count($enum_orders[$i]) != count($enumdescription[$number]){
                unset($enum_orders[$i]);
                $i--;
            }
        }
        return $enum_orders;
    }
}
