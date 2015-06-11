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
require_once($CFG->dirroot.'/question/type/correctwriting/abstract_analyzer.php');
require_once($CFG->dirroot.'/question/type/correctwriting/sequence_analyzer.php');
require_once($CFG->dirroot.'/question/type/correctwriting/enum_catcher.php');

class  qtype_correctwriting_enum_analyzer extends qtype_correctwriting_abstract_analyzer {

    /**
     * Returns analyzer internal name, which can be used as an argument to get_string().
     */
	public function name() {
		return 'qtype_correctwriting_enum_analyzer';
	}
    /**
     * Function to find order of changing enumeration, and included enumerations to all enumerations
     * @param array $enumdescription enumerations description
     */
    public function get_enum_change_order($enumdescription) {
        $enum1; // Description of enumeration-one.
        $enum2; // Description of enumeration-two.
        $enum1number; // Number of enumeration-one.
        $enum2number; // Number of enumeration-two.
        $changeorderincludedenums = new stdClass();
        // Add fields to stdClass object.
        $changeorderincludedenums->order = array(); // Enumerations change order.
        $changeorderincludedenums->includedenums = array();// Included enumeration numbers to all enumerations.
        // Add empty arrays of included enumerations.
        for ($i = 0; $i < count($enumdescription); $i++) {
            $changeorderincludedenums->includedenums[$i] = array();
        }
        $allincludedenumsinorder = true;// Variable show that all, included in current enumeration, enumerations are fill...
                                            // ...in enumerations change order.
        // Find included enumerations to all enumerations.
        $enum1number = 0;
        $enum2number = 0;
        foreach ($enumdescription as $enum1) {
            $enum2number = 0;
            foreach ($enumdescription as $enum2) {
                // If is not same enumerations.
                if ( $enum1 != $enum2) {
                    // Boolean variables to check including of enumerations.
                    reset($enum1);// Set iterator to first element in first enumeration.
                    reset($enum2);// Set iterator to first element in second enumeration.
                    $compareleftbordersofenums = current($enum2)->begin - current($enum1)->begin;
                    end($enum2);// Set iterator to last element in first enumeration.
                    end($enum1);// Set iterator to last element in second enumeration.
                    $comparerightbordersofenums = current($enum2)->end - current($enum1)->end;
                    // If left borders of j enum rather than i enum and right borders of i enum rather than j enum and...
                    // ...in included enums array for i enum not contains j.
                    if ($compareleftbordersofenums >= 0 && $comparerightbordersofenums <= 0
                        && !in_array($enum2number, $changeorderincludedenums->includedenums[$enum1number])) {
                        // Add j to included enums array for i enum.
                        $changeorderincludedenums->includedenums[$enum1number][] = $enum2number;
                    }
                }
                $enum2number++;
            }
            $enum1number++;
            unset($enum2);
        }
        // Create enumerations change order.
        while ( count($changeorderincludedenums->order) != count($enumdescription)) {
            for ($i = 0; $i < count($changeorderincludedenums->includedenums); $i++) {
                $allincludedenumsinorder = true;
                // Check that all included enumerations are in order.
                for ($j = 0; $j < count($changeorderincludedenums->includedenums[$i]); $j++) {
                    if (!in_array($changeorderincludedenums->includedenums[$i][$j], $changeorderincludedenums->order)) {
                        $allincludedenumsinorder = false;
                    }
                }
                // If all included enumerations are in order and current enumeration aren't in order...
                if ($allincludedenumsinorder && !in_array($i, $changeorderincludedenums->order)) {
                    // ...add current enumeration to order.
                    $changeorderincludedenums->order[] = $i;
                }
            }
        }
        // Ending of included arrays to enumeration which don't contains others enumerations.
        for ($i = 0; $i< count($changeorderincludedenums->includedenums); $i++) {
            // If array of included enumeration is empty...
            if (count($changeorderincludedenums->includedenums[$i]) == 0) {
                // ...add -1 to array.
                $changeorderincludedenums->includedenums[$i][] = -1;
            }
        }
        return $changeorderincludedenums;
    }

    /**
     * Returns an array of hint keys, supported by mistakes from this analyzer.
     */
	public function supported_hints() {
		return null;
	}
    /**
     * Function to find orders specified enumeration in correctstring, based on analyze correctedstring
     * @param array $correctanswer - correct answer
     * @param array $correctedanswer - corrected student answer
     * @param array $enumdescription - enumerations description
     * @param integer $number - number of enumeration to search orders
     * @return array of find orders
     */
    public function find_enum_orders_in_corrected_string($correctanswer, $correctedanswer, $enumdescription, $number) {
        $indexesoftokens = array();// Array with contains indexes of tokens which are members of enumeration...
                                                     // ... in corrected student answer.
        $indexesofelements = array();// Array with indexes of elements of enumeration in corrected answer.
        $elementindexes = array(); // Array with indexes of one element of enumeration in corrected answer.
        $previewelement = null; // Contain preview element value.
        $key = 0; // Current key of some array.
        $ischanged = true; // Is current index changed?
        $isremoved = true; // Is index removed already?
        $currentindex = 0; // Current index value.
        $removeindex = 0; // Remove index value.
        $token = 0; // One token from array.
        $duplicates = array(); // Array for help to remove duplicate orders.
        $elementsincorrectedanswer = array();// Array to keep indexes elements of enumeration in order, which...
                                                // ... it has in corrected student answer, with included missed elements.
        $currentorder = array();// Array to keep current order of enumeration elements.
        $tokennumber = 0;// Number of tokens in enumeration, whose indexes are searched in corrected answer on current iteration.
        $insertplace = 0;// Place to insert in one of arrays.
        $havenextorder = false;// Is has in $elementsincorrectedanswer next order.
        $numberofelementtoskip = 0;// Element number, which will be skip in elementsincorrectedanswer on current iteration.
        $enumorders = array();// Array which contain find orders of enumeration.
        // For all elements of enumeration create array of indexes in corrected answers, which are kept in ascending order.
        for ($i = 0; $i < count($enumdescription[$number]); $i++) {
            // Find tokens which include in current element of enumeration individually.
            $indexesoftokens = array();
            $tokennumber = 0;
            // For all tokens of current element of enumeration find indexes of equal tokens in corrected student answer.
            for ($j = $enumdescription[$number][$i]->begin; $j < $enumdescription[$number][$i]->end +1; $j++) {
                $indexesoftokens[] = array();
                foreach ($correctedanswer as $key => $token) {
                    if ($token->value() == $correctanswer[$j]->value()) {
                        $indexesoftokens[$tokennumber][] = $key;
                    }
                }
                $tokennumber++;
            }
            // Create array of indexes tokens of current element enumeration in corrected student answer.
            $indexesofelements[] = array();
            for ($j = 0; $j < count($indexesoftokens); $j++) {
                // If token number j are find in corrected answer, add it's indexes in $indexesofelementsincorrectedanswer[i].
                if ($indexesoftokens[$j] != null) {
                    // For all indexes find place to insert, because array is kept in  ascending order.
                    for ($k = 0; $k < count($indexesoftokens[$j]); $k++) {
                        // Find place to insert.
                        $insertplace = 0;
                        while ($insertplace < count($indexesofelements[$i]) &&
                                   $indexesofelements[$i][$insertplace] < $indexesoftokens[$j][$k]) {
                            $insertplace++;
                        }
                        // Insert current index in array.
                        array_splice($indexesofelements[$i], $insertplace, 0, $indexesoftokens[$j][$k]);
                    }
                }
            }
        }
        // Remove duplicates in indexes of elements.
        foreach ($indexesofelements as $key => $elementindexes) {
            $indexesofelements[$key] = array_unique($indexesofelements[$key]);
        }
        // Fill array of indexes elements of enumeration in ascending order.
        foreach ($indexesofelements as $elementindexes) {
            // Find place to insert in array for all indexes.
            foreach ($elementindexes as $currentindex) {
                // Find place to insert.
                $insertplace = 0;
                while ($insertplace < count($elementsincorrectedanswer) &&
                           $elementsincorrectedanswer[$insertplace] < $currentindex) {
                    $insertplace++;
                }
                // Insert current index in array.
                array_splice($elementsincorrectedanswer, $insertplace, 0, $currentindex);
            }
        }
        // Change indexes by numbers of elements enumeration, execute repeat contiguous elements.
        $previewelement = null;
        foreach ($elementsincorrectedanswer as $key => $i) {
            // Find element by index.
            $j = 0;
            unset($elementindexes);
            $ischanged = false;
            foreach ($indexesofelements as $k1 => $elementindexes) {
                if ( in_array($i, $elementindexes) && !$ischanged) {
                    // Change index by element number.
                    $ischanged = true;
                    $elementsincorrectedanswer[$key] = $j;
                    // If element are repeated.
                    if ($previewelement != null && $previewelement == $i) {
                        unset($elementsincorrectedanswer[$key]);
                    } else {
                        $previewelement = $i;
                    }
                    // Remove index from array.
                    $isremoved = false;
                    foreach ($indexesofelements[$k1] as $k => $element) {
                        if ($element === $i && !$isremoved) {
                            unset($indexesofelements[$k1][$k]);
                            $isremoved = true;
                        }
                    }
                }
                $j++;
            }
        }
        $elementsincorrectedanswer = array_values($elementsincorrectedanswer);
        // Add to array number of element, which do not contains in corrected student answer.
        for ($i = 0; $i < count($enumdescription[$number]); $i++) {
            // Check that contains current element in order or not.
            if (!in_array($i, $elementsincorrectedanswer)) {
                // If element does not contains in order, add it between all elements pairs and to begin and end of order.
                for ($j = 0; $j < count($elementsincorrectedanswer); $j += 2) {
                    array_splice($elementsincorrectedanswer, $j, 0, $i);
                }
                // Add to end of order.
                if (count($elementsincorrectedanswer)==0 or end($elementsincorrectedanswer) != $i) {
                    $elementsincorrectedanswer[] = $i;
                }
            }
        }
        $elementsincorrectedanswer = array_values($elementsincorrectedanswer);
        // Create orders array based on array of elements numbers which are ordered like in corrected student answer.
        for($i = 0; $i < count($elementsincorrectedanswer) - 1; $i++) {
            if($elementsincorrectedanswer[$i] === $elementsincorrectedanswer[$i+1]) {
                array_splice($elementsincorrectedanswer,$i,1);
                $i--;
            }
        }
        do {
            $numberofelementtoskip = 0;
            do {
                $currentorder = array();// Clear current enumeration order.
                // Fill current order by $elementsincorrectedanswer.
                $i = 0;
                unset($j);
                foreach ($elementsincorrectedanswer as $j) {
                    // If that element number does not contains in current order and...
                    // ...his index does not equal number to skip or number to skip is zero.
                    if (!in_array($j, $currentorder) &&
                            ($i != $numberofelementtoskip || $numberofelementtoskip == 0)) {
                        // Add element to order.
                        $currentorder[] = $j;
                    }
                    $i++;
                }
                $numberofelementtoskip++;// Inc number to skip.
                $enumorders[] =$currentorder;// Add order to array of enum orders.
            } while ($numberofelementtoskip != count($elementsincorrectedanswer));
            // Remove duplicate orders.
            foreach ($enumorders as $currentorder) {
                $duplicates = array_keys($enumorders, $currentorder);
                array_shift($duplicates);
                foreach ($duplicates as $removeindex) {
                    unset($enumorders[$removeindex]);
                }

            }
            // Remove first element from array of elements numbers which are ordered like in corrected student answer.
            array_shift($elementsincorrectedanswer);
            // Check that have next order in array of elements numbers which are ordered like in corrected student answer.
            $havenextorder = true;
            for ($i = 0; $i < count($enumdescription[$number]); $i++) {
                if (!in_array($i, $elementsincorrectedanswer)) {
                    $havenextorder = false;
                }
            }
        } while ($havenextorder);
        // Remove from array of orders not complete orders.
        foreach ($enumorders as $key => $currentorder) {
            if (count($currentorder) != count($enumdescription[$number])) {
                unset($enumorders[$key]);
            }
        }
        return $enumorders;
    }

     
    /**
     * Returns fitness as aggregate measure of how students response fits this particular answer - i.e. more fitness = less mistakes.
     * Used to choose best matched answer.
     * Fitness is negative or zero (no errors, full match).
     * Fitness doesn't necessary equivalent to the number of mistakes as each mistake could have different weight.
     * Each analyzer will calculate fitness only for it's own mistakes, ignoring mistakes from other analyzers.
     * @param array of qtype_correctwriting_response_mistake child classes $mistakes Mistakes to calculate fitness from, can be empty array.
     */
	public function fitness($mistakes) {
			return 0;
	}
    /**
     * Function to find orders of all enumerations in corrected answer.
     * @param array $correctanswer - correct answer
     * @param array $correctedanswer - corrected student answer
     * @param array $enumdescription - enumerations description
     * @return array of find orders
     */
    public function find_all_enum_orders_in_corrected_string($correctanswer, $correctedanswer, $enumdescription) {
        $enumorders = array(); // Array to keep orders of one enumeration.
        $allenumorders = array(); // Array to keep orders of all enumerations.
        $completeenumorders = array(); // Array to keep complete orders of enumerations
        $currentorder = array(); // Array to keep current order of enumeration elements.
        $countofallenumorders = 0; // Count of all enumeration orders.
        $rowsforoneorder = 0; // Count of rows which will be keep same order for all enumerations.
        // Find orders for all enumerations alternatively.
        for ($i = 0; $i < count($enumdescription); $i++) {
            $allenumorders[]=$this->find_enum_orders_in_corrected_string($correctanswer, $correctedanswer, $enumdescription, $i);
        }
        // Find count of complete orders of enumerations.
        $countofallenumorders = 1;
        for ($i = 0; $i < count($enumdescription); $i++) {
            $countofallenumorders *= count($allenumorders[$i]);
        }
        // Paste together all enum orders.
        $rowsforoneorder = $countofallenumorders;
        for ($i = 0; $i < count($enumdescription); $i++) {
            // Add to all complete orders, orders of enumeration alternatively.
            $rowsforoneorder /= count($allenumorders[$i]);
            for ($j = 0; $j < $countofallenumorders; $j) {
                foreach ($allenumorders[$i] as $enumorders) {
                    for ($k = 0; $k < $rowsforoneorder; $k++) {
                        if (!array_key_exists($j, $completeenumorders)) {
                            $completeenumorders[$j] = array();
                        }
                        $completeenumorders[$j] = array_merge($completeenumorders[$j], $enumorders);
                        $j++;
                    }
                }
            }
            // Add space if it needed.
            if ($i!= count($enumdescription)-1) {
                for ($j=0; $j < $countofallenumorders; $j++) {
                    $completeenumorders[$j][] = -1;
                }
            }
        }
        return $completeenumorders;
    }
    /**
     * Function to change enumeration order in correct answer and enumeration description,
     * @param qtype_correctwriting_string_pair object $stringpair - correct and corrected answers
     * @param array $enumchangeorder - enumeration change order
     * @param array $includeenums - array of included enumerations, for all enumerations
     * @param array $newenumorder - new orders for all enumeration
     */
    public function change_enum_order(&$stringpair, $enumchangeorder, $includeenums, $newenumorder) {
        $enumsorders = array(); // Array to keep enums orders separately.
        $currentorder = array(); // Array to keep current order of enum.
        $forchangeenumorder = array(); // Array keep temp information for change enumeration order.
        $elemnumber = 0; // Element number, which contains current included enumeration.
        $enumnumber = 0; // Enumeration number, whose order are changing in current iteration.
        $elemfind = false; // Is searched element find?
        $placefind = false; // Is place to insert element find?
        $insertplace = 0; // Place to insert element in enumeration.
        $elementsdistances = array(); // Array to keep distances between elements of enumerations
        $distances = array(); // Array to keep for all enumerations numbers of elements in other enumeration, which contains...
                               // ...it, and distance from element begin to enumeration begin.
        $leftborderofelem = 0; // Left border of current element.
        $leftborderofelemnew = 0; // New left border of current element.
        $leftborderofenum = 0; // Left border of current enumeration.
        $rightborderofelem = 0; // Right border of current element.
        $rightborderofprevelem = 0; // Right border of previews element.
        $rightborderofelemnew = 0; // New right border of current element.
        $rightborderofenum = 0; // Right border of current enumeration.
        $firstindex = 0; // Array first index, need to arrays, which indexes are difficult to calculate.
        $secondindex = 0; // Array second index, need to arrays, which indexes are difficult to calculate.
        $enumerations = $stringpair->correctstring()->enumerations; // Enumerations descriptions.
        $tempstringbegin = ''; // String to create correct string with correct order, peace before enumeration;
        $tempstringend = ''; // String to create correct string with correct order, peace after enumeration;
        $enumschangecorrectstring = array(); // Indexes of enumerations which take biggest changes in correct answer.
        $includearray = array(); // Array with indexes of include enumeration for one enumeration.
        $isenumincluded = false; // Is current enumeration included in other enumeration.
        $position = 0; // Position to change correct string.
        $indexesintable = array(); // Array of indexes for correct string's tokens.
        $tokens = $stringpair->correctstring()->stream->tokens; // Array of tokens in correctstring.
        // Fill array to keep enums orders separately.
        for ($i = 0; $i < count($newenumorder); $i++) {
            // For all enumerations order end by -1 or end of array.
            if ($newenumorder[$i] == -1) {
                $enumnumber++;
            } else {
                $enumsorders[$enumnumber][] = $newenumorder[$i];
            }
        }
        // Change enumerations orders and enumerations descriptions.
        for ($i = 0; $i < count($enumerations); $i++) {
            $enumnumber = $enumchangeorder[$i];
            // For all included enumerations save important information: number of element, which contain it and ...
            // ...distance from element begin to enumeration begin.
            // If current enumeration have included enumerations.
            if ($includeenums[$enumnumber][0] != -1) {
                // For all included enumerations...
                for ($j = 0; $j < count($includeenums[$enumnumber]); $j++) {
                    $elemfind = false;
                    // ...find element, which contain current included enumeration.
                    for ($elemnumber = 0; $elemnumber < count($enumerations[$enumnumber]) && !$elemfind; $elemnumber++) {
                        $leftborderofelem = $enumerations[$enumnumber][$elemnumber]->begin;
                        $secondindex = reset($enumsorders[$includeenums[$enumnumber][$j]]);
                        $leftborderofenum = $enumerations[$includeenums[$enumnumber][$j]][$secondindex]->begin;
                        $rightborderofelem = $enumerations[$enumnumber][$elemnumber]->end;
                        $secondindex = end($enumsorders[$includeenums[$enumnumber][$j]]);
                        $rightborderofenum = $enumerations[$includeenums[$enumnumber][$j]][$secondindex]->end;
                        // If enumeration borders are between element borders, then element find.
                        if ($leftborderofelem <= $leftborderofenum && $rightborderofelem >= $rightborderofenum) {
                            $elemfind = true;
                        }
                    }
                    if ($elemnumber != 0) {
                        $elemnumber--;
                    }
                    $distances[$j*2] = $elemnumber;
                    // Find distance between element and enumeration which it contain.
                    $distances[$j*2+1] = $leftborderofenum - $leftborderofelem;
                }
            }
            // Find current order of enumeration.
            $currentorder = array();
            $currentorder[] = 0;
            for ($j = 1; $j < count($enumerations[$enumnumber]); $j++) {
                $placefind = false;
                for ($insertplace = 0; $insertplace < count($currentorder) && !$placefind; $insertplace++) {
                    $leftborderofelem = $enumerations[$enumnumber][$currentorder[$insertplace]]->begin;
                    $leftborderofelemnew =$enumerations[$enumnumber][$j]->begin;
                    // If left border of new element are less then left border of element in order, insert place are find.
                    if ($leftborderofelem > $leftborderofelemnew) {
                        $placefind = true;
                    }
                }
                if ($insertplace != 0 && $placefind) {
                    $insertplace--;
                }
                // Add element to order.
                array_splice($currentorder, $insertplace, 0, $j);
            }
            // If current order not equal order which needed now.
            if ($currentorder != $enumsorders[$enumnumber]) {
                // Copy current enumeration to temp array and remove it from correct answer.
                $forchangeenumorder = array();
                $leftborderofenum = $enumerations[$enumnumber][reset($currentorder)]->begin;
                $rightborderofenum = $enumerations[$enumnumber][end($currentorder)]->end;
                $j = 0;
                foreach ($tokens as $key => $token) {
                    if ($j >= $leftborderofenum && $j <= $rightborderofenum) {
                        $forchangeenumorder[] = $token;
                        unset($tokens[$key]);
                    }
                    $j++;
                }
                // Change current order to new in enumeration.
                // Copy elements in new order.
                $leftborderofenum = $enumerations[$enumnumber][reset($currentorder)]->begin;
                for ($j=0; $j < count($enumsorders[$enumnumber]); $j++) {
                    // Copy element, token for token.
                    $leftborderofelem = $enumerations[$enumnumber][$enumsorders[$enumnumber][$j]]->begin;
                    $rightborderofelem = $enumerations[$enumnumber][$enumsorders[$enumnumber][$j]]->end;
                    for ($k = 0; $k < $rightborderofelem-$leftborderofelem+1; $k++) {
                        $forchangeenumorder[] = $forchangeenumorder[$leftborderofelem-$leftborderofenum+$k];
                    }
                    // If we have separates between elements, that copy they to and of temp array.
                    if ( $j != count($enumsorders[$enumnumber])-1) {
                        $leftborderofelemnew =$enumerations[$enumnumber][$currentorder[$j+1]]->begin;
                        $rightborderofelem = $enumerations[$enumnumber][$currentorder[$j]]->end;
                        for ($z = 0; $z < $leftborderofelemnew-$rightborderofelem-1; $z++) {
                            $forchangeenumorder[] = $forchangeenumorder[$z+$rightborderofelem-$leftborderofenum+1];
                        }
                    }
                }
                // Remove old order from temp array.
                $rightborderofenum = $enumerations[$enumnumber][end($currentorder)]->end;
                array_splice($forchangeenumorder, 0, $rightborderofenum - $leftborderofenum +1);
                // Copy enumeration in correct answer, token for token.
                array_splice($tokens, $leftborderofenum, 0, $forchangeenumorder);
                // Change enumeration description.
                // Find distance between elements of enumeration.
                $elementsdistances = array();
                $elementsdistances[] = 0;
                for ($j = 0; $j < count($enumerations[$enumnumber])-1; $j++) {
                    $leftborderofelemnew = $enumerations[$enumnumber][$currentorder[$j+1]]->begin;
                    $rightborderofelem = $enumerations[$enumnumber][$currentorder[$j]]->end;
                    $elementsdistances[] = $leftborderofelemnew-$rightborderofelem;
                }
                // Change description of enumeration, element for element.
                for ($j = 0; $j < count($enumerations[$enumnumber]); $j++) {
                    // Take current element old desription.
                    $leftborderofelem = $enumerations[$enumnumber][$enumsorders[$enumnumber][$j]]->begin;
                    $rightborderofelem = $enumerations[$enumnumber][$enumsorders[$enumnumber][$j]]->end;
                    // If current element not first...
                    if ($j != 0) {
                        // Calculate new description, use previews element description, and current element old description.
                        $rightborderofprevelem = $enumerations[$enumnumber][$enumsorders[$enumnumber][$j-1]]->end;
                        $leftborderofelemnew = $rightborderofprevelem+$elementsdistances[$j];
                        $rightborderofelemnew = $rightborderofelem+ $leftborderofelemnew-$leftborderofelem;
                    } else {
                        // ...else.
                        // Calculate new description, use first element description, and current element old description.
                        $leftborderofelemnew = $enumerations[$enumnumber][$currentorder[0]]->begin;
                        $rightborderofelemnew = $leftborderofelemnew+$rightborderofelem-$leftborderofelem;
                    }
                    // Change description of element.
                    $enumerations[$enumnumber][$enumsorders[$enumnumber][$j]]->begin = $leftborderofelemnew;
                    $enumerations[$enumnumber][$enumsorders[$enumnumber][$j]]->end = $rightborderofelemnew;
                }
                // If current enumeration contains included enumerations...
                if ($includeenums[$enumnumber][0] != -1) {
                    // ...update their descriptions.
                    for ($j = 0; $j < count($includeenums[$enumnumber]); $j++) {
                        // Find distance between elements of enumeration.
                        $elementsdistances = array();
                        $elementsdistances[] = $distances[$j*2+1];
                        for ($k = 0; $k < count($enumerations[$includeenums[$enumnumber][$j]])-1; $k++) {
                            $firstindex = $includeenums[$enumnumber][$j];
                            $secondindex = $enumsorders[$includeenums[$enumnumber][$j]][$k];
                            $rightborderofprevelem = $enumerations[$firstindex][$secondindex]->end;
                            $secondindex = $enumsorders[$includeenums[$enumnumber][$j]][$k+1];
                            $leftborderofelem = $enumerations[$firstindex][$secondindex]->begin;
                            $elementsdistances[] = $leftborderofelem-$rightborderofprevelem;
                        }
                        // Update included enumeration description, element for element.
                        for ($k = 0; $k < count($enumerations[$includeenums[$enumnumber][$j]]); $k++) {
                            // Take current element old desription.
                            $firstindex = $includeenums[$enumnumber][$j];
                            $secondindex = $enumsorders[$includeenums[$enumnumber][$j]][$k];
                            $rightborderofelem = $enumerations[$firstindex][$secondindex]->end;
                            $leftborderofelem = $enumerations[$firstindex][$secondindex]->begin;
                            // If current element not first...
                            if ($k != 0) {
                                // Calculate new description, use previews element description, and current element old description.
                                $secondindex = $enumsorders[$includeenums[$enumnumber][$j]][$k-1];
                                $rightborderofprevelem = $enumerations[$firstindex][$secondindex]->end;
                                $secondindex = $enumsorders[$includeenums[$enumnumber][$j]][$k];
                                $leftborderofelemnew = $rightborderofprevelem+$elementsdistances[$k];
                                $rightborderofelemnew = $leftborderofelemnew+$rightborderofelem-$leftborderofelem;
                            } else {
                                // ...else.
                                // Calculate new description, use description of element, which contains current enumeration,...
                                // ...and current element old description.
                                $rightborderofprevelem = $enumerations[$enumnumber][$distances[$j*2]]->begin;
                                $leftborderofelemnew = $rightborderofprevelem+$elementsdistances[$k];
                                $rightborderofelemnew = $leftborderofelemnew+$rightborderofelem-$leftborderofelem;
                            }
                            // Update description of element.
                            $enumerations[$firstindex][$secondindex]->begin = $leftborderofelemnew;
                            $enumerations[$firstindex][$secondindex]->end = $rightborderofelemnew;
                        }
                    }
                }
            }
        }
        // Find enumerations which enough to change in correctstring to make it like we need.
        for ($i = 0; $i < count($enumerations); $i++) {
            $enumschangecorrectstring[] = $i;
            $isenumincluded = false;
            // Is current enumeration include in some others.
            foreach ($includeenums as $includearray) {
                if (false !== array_search($i, $includearray)) {
                    $isenumincluded = true;
                }
            }
            // If current enumeration is included in other enumeration remove it from array.
            if ($isenumincluded === true) {
                array_pop($enumschangecorrectstring);
            }
        }
        // Change table indexes for tokens in correct answer.       
        $indexesintable = array();
        foreach ($tokens as $token) {       
            $indexesintable[] = $token->token_index();      
        }       
        $stringpair->set_enum_correct_to_correct($indexesintable);
        // Change correctstring.
        $stringpair->correctstring()->stream->tokens = $tokens;
        $enumstring = clone($stringpair->correctstring());
        foreach ($enumschangecorrectstring as $i) {
            $tempstringbegin = '';
            $tempstringend = '';
            $position = reset($enumsorders[$i]);
            $position = $enumerations[$i][$position]->begin;
            if ($position !== 0) {
                $position--;
                $position = $stringpair->enum_correct_string()->stream->tokens[$position]->position()->colend();
                $tempstringbegin = $stringpair->enum_correct_string()->string->substring(0, $position + 1);
                $tempstringbegin = $tempstringbegin.' ';
            } else {
                $tempstringbegin = '';
            }
            $position = end($enumsorders[$i]);
            $position = $enumerations[$i][$position]->end;
            if ($position !== count($tokens) - 1) {
                $position++;
                $position= $stringpair->enum_correct_string()->stream->tokens[$position]->position()->colstart();
                $tempstringend = $stringpair->enum_correct_string()->string->substring($position);
            } else {
                $tempstringend = '';
            }
            $secondindex = end($enumsorders[$i]);
            for ($j = $enumerations[$i][reset($enumsorders[$i])]->begin; $j <= $enumerations[$i][$secondindex]->end; $j++) {
                $tempstringbegin = $tempstringbegin.$tokens[$j]->value();
                $tempstringbegin = $tempstringbegin.' ';
            }
            $tempstringbegin = $tempstringbegin.$tempstringend;


            // Update correct string.
            $enumstring->string = new qtype_poasquestion_string($tempstringbegin);
            // Update enumerations descriptions.
            $stringpair->enum_correct_string()->enumerations = $enumerations;
            // Update token indexes.
            $enumstring->stream = null;
            $enumstring->stream->tokens;
            $stringpair->set_enum_correct_string($enumstring);
            $stringpair->correctstring()->stream = null;
            $stringpair->correctstring()->stream->tokens;
        }
    }

    /**
     * Do all processing and fill all member variables
     *
     * Passed responsestring could be null, than object used just to find errors in the answers, token count etc...
     *
     * @param qtype_correctwriting_string_pair - pair of answers.
     */
    protected function analyze() {
		global $CFG;
        $maxlcslength = 0; // Current maximal LCS length.
        $allfindorders = array(); // All find enumeration orders.
        $enumchangeorder = array(); // Enumeration change order.
        $includedenums = array(); // Included enumerations indexes for all enumerations.
        $forstd = 0; // Variable for function,which return std Class objects.
        $correcttokens = $this->basestringpair->correctstring()->stream->tokens; // Correct answer tokens array;
        $correctedtokens = $this->basestringpair->correctedstring()->stream->tokens; // Corrected student answer tokens array;
        $enumdescription = $this->basestringpair->correctstring()->enumerations; // Correct answer enumerations descriptions.
        $currentorder = array(); // Current order of enumerations elements.
        $currentstringpair = 0; // Current string pair with current order of enumeration.
        $currentcorrectstream = $this->basestringpair->correctstring()->stream; // Stream of correct answer with current...
                                                                           // ...enumerations elements order.
        $lcsarray = array(); // Array of finded LCS for current enuerations elements order.
        $correctedstream =  $this->basestringpair->correctedstring()->stream; // Stream of corrected answer.
        $options = new block_formal_langs_comparing_options(); // Options needed to find lcs.
        $options->usecase = true;
        $count = 0; // Count of LCS tokens for current pair.
        // Get enumerations change order and include enumeration arrays.
        $syntax_tree = $this->basestringpair->correctstring()->syntaxtree;
        $enum_catcher = new qtype_correctwriting_enum_catcher($syntax_tree);
        $enumdescription = $enum_catcher->getEnums();
        for($i = 0; $i < count($enumdescription); $i++) {
            for($j = 0; $j < count($enumdescription[$i]); $j++) {
                $enumdescription[$i][$j] = new enum_element($enumdescription[$i][$j][0],$enumdescription[$i][$j][1]);
            }
        }
        $this->basestringpair->correctstring()->enumerations = $enumdescription;
        $forstd = $this->get_enum_change_order($enumdescription);
        $enumchangeorder = $forstd->order;
        $includedenums = $forstd->includedenums;
        // Find expected orders for all enumeration.
        $allfindorders = $this->find_all_enum_orders_in_corrected_string($correcttokens, $correctedtokens, $enumdescription);
		if (count($allfindorders) > $CFG->qtype_correctwriting_maxorderscount) {
			array_splice($allfindorders,-0,count($allfindorders) - $CFG->qtype_correctwriting_maxorderscount); 
		}
        foreach ($allfindorders as $currentorder) {
        	// Change enumeration elements order.
            $currentstringpair = null;
			$currentstringpair = clone $this->basestringpair;
            $currentstringpair->set_enum_correct_string(clone $currentstringpair->correctstring());
			$this->change_enum_order($currentstringpair, $enumchangeorder, $includedenums, $currentorder);
            // Find LCS of correct and corrected answers.
            $currentcorrectstream = $currentstringpair->enum_correct_string()->stream;
            $lcsarray = qtype_correctwriting_sequence_analyzer::lcs($currentcorrectstream, $correctedstream, $options);
            // If lcs exist keep it's length...
            // Else length is zero.
            if (count($lcsarray) === 0) {
                $count = 0;
            } else {
                $count = count($lcsarray[0]);
            }
            // If length of current lcs are equal length of lcs, which were found early add string pair to array...
            // ...Else if length of current lcs more than length of lcs, which were found early, clear array...
            // ... and add string pair to array.
            if ($maxlcslength === $count) {
                $this->resultstringpairs[] = $currentstringpair;
		   		$this->resultmistakes[] = true; 
            } else if ($maxlcslength < $count) {
                $maxlcslength = $count;
                $this->resultstringpairs = array();
		   		$this->resultmistakes = array(); 
		   		$this->resultmistakes[] = true; 
                $this->resultstringpairs[] = $currentstringpair;
            }
       }  
       // If maximal LCS length is equal zero array of pair must be empty.
       if ($maxlcslength === 0) {
           $this->resultstringpairs = array($this->basestringpair);
		   $this->resultmistakes = array();
       }
    }

		/**
		* If this analyzer requires some other ones to work, not bypass - return an array of such analyzers names.
		*/
		public function require_analyzers() {
				return array("qtype_correctwriting_sequence_analyzer");
		}

		/**
		* Returns if the language is compatible with this analyzer.
		* @param block_formal_langs_abstract_language $lang a language object from block_formal_langs
		* @return boolean
		*/
		public function is_lang_compatible($lang) {
				if($lang->name() == 'cpp_parseable') {
					return true;
				}
				return false;
		}
}

class enum_element {
    public $begin; // Index of first element token.
    public $end; // Index of last element token.

    /**
     * Function create enumeration element, which start on $first token, and ended by $last token.
     * @param integer $first - first element token.
     * @param integer $last - last element token.
     */
    public function __construct($first, $last) {
        $this->begin = $first;
        $this->end = $last;
    }

    /**
    *  Function return number of first element's token  
    * @return integer - number of first element's token
    */
    public function begin() {
        return $this->begin;
    }

    /**
    *  Function return number of last element's token  
    * @return integer - number of last element's token
    */
    public function end() {
        return $this->end;
    }
}
