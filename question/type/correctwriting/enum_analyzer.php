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
}
