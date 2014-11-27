<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines an abstract regular expression matcher, extend it to create a new matching engine.
 * A matcher is a particulary important type of regex handlers, that allows the question to work at all.
 * The file also defines a class to store matching results.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

class qtype_preg_matching_results {

    //No match captured
    const NO_MATCH_FOUND = -1;
    //No next character generated
    const UNKNOWN_NEXT_CHARACTER = '';
    //How many characters left is unknown
    const UNKNOWN_CHARACTERS_LEFT = 999999999;

    ////Match data
    /** @var boolean Is match full or partial? */
    public $full;
    /** @var array Indexes of first matched character - array where 0 => full match, 1=> first subpattern etc. */
    public $index_first;
    /** @var array Length of the matches - array where 0 => full match, 1=> first subpattern etc. */
    public $length;
    /** @var integer The number of characters left to complete matching. */
    public $left;
    /** @var object of qtype_preg_matching_results, containing string extended to give more close match than this ($this->extededmatch->left <= $this->left)
     *
     * There are several ways this string could be generated:
     * add characters to the end of matching part (index_first[0]+length[0]);
     * add characters before the end of matching part if it is impossible to complete match from the current point of match fail;
     * just delete unmatched tail if match failed on the $ assertion.
     * Should be null if not generated.
     */
    public $extendedmatch;
    /** @var integer Start index for the added characters in extendedmatch object
     *
     * May be less than index_first[0]+length[0] if there is no way to complete matching
     * from current point of fail due to assertions, backreferences or another reasons.
     * This field is filled by qtype_preg_matching_results::validate() and should not be set by matching engine
     */
    public $extensionstart;

    ////Source data
    /** @var qtype_poasquestion_string A string being matched */
    protected $str;
    /** @var integer Max number of a subpattern available in regular expression */
    protected $maxsubpatt;
    /** @var array A map where keys are subpattern names and values are their numbers */
    protected $subpatternmap;

    public function __construct($full = false, $index_first = array(), $length = array(), $left = qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT,
                                $extendedmatch = null) {
        $this->full = $full;
        $this->index_first = $index_first;
        $this->length = $length;
        $this->left = $left;
        $this->extendedmatch = $extendedmatch;
        $this->extensionstart = qtype_preg_matching_results::NO_MATCH_FOUND;
    }

    /**
     * Sets info about string and regular expression, that is needed for some functions to work
     */
    public function set_source_info($str = null, $maxsubpatt = 0, $subpatternmap = array()) {
        $this->str = clone $str;
        $this->maxsubpatt = $maxsubpatt;
        $this->subpatternmap = $subpatternmap;
    }

    public function str() {
        return $this->str;
    }

    /**
     * Returns keys for all subpatterns in regular expression
     *
     * Use to enumerate subpatterns
     */
    public function all_subpatterns() {
        //Merge all numeric subpattern keys with named subpatterns from $subpatternman
        return array_merge(array_keys($this->index_first), array_keys($this->subpatternmap));
    }

    /**
     * Return subpattern index in the index_first and length arrays
     *
     * If it is subpattern name, use $subpatternmap to find appropriate index,
     * otherwise (numbered subpattern) just return $subpattern.
     */
    public function subpattern_number($subpattern) {
        if (array_key_exists($subpattern, $this->subpatternmap)) {//named subpattern
            return $this->subpatternmap[$subpattern];
        }
        return $subpattern;
    }

    /**
     * Returns true if subpattern is captured
     * @param subpattern subpattern number
     */
    public function is_subpattern_captured($subpattern) {
        $subpattern = $this->subpattern_number($subpattern);
        if (!isset($this->length[$subpattern])) {
            throw new qtype_preg_exception('Error: Asked for unexisting subpattern '.$subpattern);
        }
        return ($this->length[$subpattern] != qtype_preg_matching_results::NO_MATCH_FOUND);
    }

    public function index_first($subpattern = 0) {
        $subpattern = $this->subpattern_number($subpattern);
        return $this->index_first[$subpattern];
    }

    public function length($subpattern = 0) {
        $subpattern = $this->subpattern_number($subpattern);
        return $this->length[$subpattern];
    }

    /**
     * Any match found?
     * The match considered found if at least one character is matched or there is full match of zero length (regex with just asserts)
     */
    public function is_match() {
        if (array_key_exists(0, $this->length)) {
            return $this->full || ($this->length[0] > 0);
        } else {//no matching resutls at all
            return false;
        }
    }

    /**
     * Returns true if there could be no better matching result, so we could stop loop looking for best match
     *
     * For now the first (leftmost) full match is enought
     */
    public function best() {
        return $this->full;
    }

    /**
     * Compares two matching results and returns true if this result is worse than passed by argument
     *
     * @param other object of qtype_preg_matching_results
     * @param orequal make it worse-or-equal function
     * @param longestmatch defines what result is preferable - with more characters matched or with less characters to complete match
     * @param areequal reference to a variable to store boolean value - whether the objects are equal.
     * @return whether @this is worse than $other
     */
    public function worse_than($other, $orequal = false, $longestmatch = false, &$areequal = null) {

        if ($areequal !== null) {
            $areequal = false;
        }

        //1. The match is definitely best (full match)
        if (!$this->best() && $other->best()) {
            return true;
        } elseif ($this->best() && !$other->best()) {
            return false;
        }

        //2. Is match
        if (!$this->is_match() && $other->is_match()) {
            return true;
        } elseif ($this->is_match() && !$other->is_match()) {
            return false;
        }

        if (!$longestmatch) {
            //3. Less characters left
            if ($other->left < $this->left) {
                return true;
            } elseif ($this->left < $other->left) {
                return false;
            }

            //4. Longest match
            if ($other->length[0] > $this->length[0]) {
                return true;
            } elseif ($this->length[0] > $other->length[0]) {
                return false;
            }
        } else {
            //3. Longest match
            if ($other->length[0] > $this->length[0]) {
                return true;
            } elseif ($this->length[0] > $other->length[0]) {
                return false;
            }

            //4. Less characters left
            if ($other->left < $this->left) {
                return true;
            } elseif ($this->left < $other->left) {
                return false;
            }

        }

        if ($areequal !== null) {
            $areequal = true;
        }
        return $orequal;//results are equal
    }

    /**
     * Invalidates match by setting all data to no match values
     */
    public function invalidate_match() {
        $this->full = false;
        //$this->left = qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
        $this->index_first = array();
        $this->length = array();
        for ($i = 0; $i <= $this->maxsubpatt; $i++) {
            $this->index_first[$i] = qtype_preg_matching_results::NO_MATCH_FOUND;
            $this->length[$i] = qtype_preg_matching_results::NO_MATCH_FOUND;
        }
        $this->extensionstart = qtype_preg_matching_results::NO_MATCH_FOUND;
        if ($this->extendedmatch !== null) {
            $this->extendedmatch->extensionstart = 0;//If there is no match, extension should start from the beginning of the string
        }
    }

    /**
     * Throws exception if match results contain obvious abnormalities
     * Also compute extensionstart field
     */
    public function validate() {
        if ($this->is_match()) {//Match found
            if (!isset($this->index_first[0]) || !isset($this->length[0])
                || $this->index_first[0] === qtype_preg_matching_results::NO_MATCH_FOUND || $this->length[0] === qtype_preg_matching_results::NO_MATCH_FOUND) {
                throw new qtype_preg_exception('Error: match was found but no match information returned');
            }

            //Check that each subpattern lies inside overall match
            foreach ($this->index_first as $i => $start) {
                if ($start === qtype_preg_matching_results::NO_MATCH_FOUND) {
                    //No need to check subpattern that wasn't matched
                    break;
                }
                if ($start < $this->index_first[0] || $start > $this->index_first[0] + $this->length[0]) {
                    throw new qtype_preg_exception('Error: '.$i.' subpattern start '.$start.' doesn\'t lie between match start '.$this->index_first[0].' and end '.($this->index_first[0] + $this->length[0]));
                }
                $end = $start + $this->length[$i];
                if ($end < $this->index_first[0] || $end > $this->index_first[0] + $this->length[0]) {
                    throw new qtype_preg_exception('Error: '.$i.' subpattern end '.$end.' doesn\'t lie between match start '.$this->index_first[0].' and end '.($this->index_first[0] + $this->length[0]));
                }
            }
        }
        //Calculate extension start comparing existing and extended strings
        //We could find it looking for the first different character in two strings
        if (!$this->full && is_object($this->extendedmatch)) {
            //Find out extenstion start comparing two strings
            $str1 = $this->str;
            $str2 = $this->extendedmatch->str;
            for ($i = 0; $i <= $this->length[0]; $i++) {
                //One of the string ended or characters are different
                if ($this->extendedmatch->index_first[0] + $i >= qtype_poasquestion_string::strlen($str2) || $this->index_first[0] + $i >= $str1->length() || $str1[$this->index_first[0] + $i] != $str2[$this->extendedmatch->index_first[0] + $i]) {
                    $this->extensionstart = $this->index_first[0] + $i;
                    $this->extendedmatch->extensionstart = $this->extendedmatch->index_first[0] + $i;
                    break;
                }
            }
        } elseif ($this->full && $this->extensionstart === qtype_preg_matching_results::NO_MATCH_FOUND) {
            $this->extensionstart = $this->index_first[0] + $this->length[0];
        }

        if (is_object($this->extendedmatch)) {
            $this->extendedmatch->validate();
        }
    }

    /**
     * Returns non-matched heading before subpattern match
     */
    public function match_heading($subpattern = 0) {
        $subpattern = $this->subpattern_number($subpattern);
        $wronghead = new qtype_poasquestion_string('');
        if ($this->is_match()) {//There is match
            if ($this->index_first[$subpattern] > 0) {//if there is wrong heading
                $wronghead = $this->str->substring(0, $this->index_first[$subpattern]);
            }
        } else {//No match, assuming all string is wrong heading (to display hint after it)
            $wronghead = $this->str;
        }
        return $wronghead->string();
    }

    /**
     * Returns matched part of the string for given subpattern
     */
    public function matched_part($subpattern = 0) {
        $subpattern = $this->subpattern_number($subpattern);
        $correctpart = new qtype_poasquestion_string('');
        if ($this->is_match()) {//There is match
            if (isset($this->index_first[$subpattern]) && $this->index_first[$subpattern] !== qtype_preg_matching_results::NO_MATCH_FOUND) {
                $correctpart = $this->str->substring($this->index_first[$subpattern], $this->length[$subpattern]);
            }
        }
        return $correctpart->string();
    }

    /**
     * Returns non-matched tail after subpattern match
     */
    public function match_tail($subpattern = 0) {
        $subpattern = $this->subpattern_number($subpattern);
        $wrongtail = new qtype_poasquestion_string('');
        if ($this->is_match()) {//There is match
            if ($this->index_first[$subpattern] + $this->length[$subpattern] < qtype_poasquestion_string::strlen($this->str) && $this->length[$subpattern]!== qtype_preg_matching_results::NO_MATCH_FOUND) {//if there is wrong tail
                $wrongtail = $this->str->substring($this->index_first[$subpattern] + $this->length[$subpattern], $this->str->length() - $this->index_first[$subpattern] - $this->length[$subpattern]);
            }
        }
        return $wrongtail->string();
    }

    /**
     * Returns correct part before hint
     */
    public function correct_before_hint() {
        $correctbeforehint = new qtype_poasquestion_string('');
        if ($this->is_match()) {//There is match
            $correctbeforehint = $this->str->substring($this->index_first[0], $this->extensionstart - $this->index_first[0]);
        }
        return $correctbeforehint->string();
    }

    /**
     * Returns tail after point where extension is started
     */
    public function tail_to_delete() {
        $wrongtail = new qtype_poasquestion_string('');
        if ($this->is_match()) {//There is match
            if ($this->extensionstart < $this->str->length() && $this->length[0]!== qtype_preg_matching_results::NO_MATCH_FOUND) {//if there is wrong tail
                $wrongtail = $this->str->substring($this->extensionstart, $this->str->length() - $this->extensionstart);
            }
        }
        return $wrongtail->string();
    }

    /**
     * Returns part of the string, added by matcher
     */
    public function string_extension() {
        $extension = new qtype_poasquestion_string('');
        if ($this->extendedmatch !== null) {
            $extendedstr = $this->extendedmatch->str();
            if ($this->extendedmatch->extensionstart < $extendedstr->length()) {
                $extension = $extendedstr->substring($this->extendedmatch->extensionstart, $extendedstr->length() - $this->extendedmatch->extensionstart);
            } else {
                $extension = new qtype_poasquestion_string('');
            }
        }
        return $extension->string();
    }
}

/**
 * Options, used to specify matching process.
 */
class qtype_preg_matching_options extends qtype_preg_handling_options {

    /** @var boolean Should matcher try to generate extension? */
    public $extensionneeded = true;
    /** @var string Unicode property name for preferred alphabet for \w etc when generating extension.*/
    public $preferredalphabet = null;
    /** @var string Unicode property name for preferred characters for dot meta-character when generating extension.*/
    public $preferfordot = null;

    /** @var boolean Should matcher look for subpattern captures or the whole match only? */
    //TODO - does we need to specify subpatterns we are looking for or there is no sense in it?
    public $capturesubpatterns = true;
}

/**
 * Abstract base class for regular expression matcher
 */
class qtype_preg_matcher extends qtype_preg_regex_handler {

    //Constants for the capabilities which could (or could not) be supported by matching engine
    //Partial matching (returning the index of last matched character)
    const PARTIAL_MATCHING = 0;
    //Returning next possible character(s) after partial match
    const CORRECT_ENDING = 1;
    //Returning the smallest number of characters that needed to complete partial match
    const CHARACTERS_LEFT = 2;
    //Subpattern capturing during matching
    const SUBPATTERN_CAPTURING = 3;
    //Always return full match as the correct ending (if at all possible)
    const CORRECT_ENDING_ALWAYS_FULL = 4;

    /**
    * Returns true for supported capabilities
    * @param capability the capability in question
    * @return bool is capability supported
    */
    public function is_supporting($capability) {
        return false;//abstract class supports nothing
    }

    //Matching results as qtype_preg_matching_results object
    protected $matchresults;
    //Cache of the matching results,  string for matching is the key
    protected $resultcache;

    public function name() {
        return 'preg_matcher';
    }

    /**
    * Parse regex and do all necessary preprocessing
    * @param regex - regular expression to handle
    * @param modifiers - modifiers of regular expression
    * @param options - options to handle regex, object of qtype_preg_matching_options class
    */
    public function __construct($regex = null, $modifiers = null, $options = null) {
        //Set matching data empty
        $this->matchresults = new qtype_preg_matching_results();
        $this->resultcache = array();

        // Options should exist at least as a default object. Be sure to create matching options.
        if ($options === null) {
            $options = new qtype_preg_matching_options();
        }


        //Do parsing
        parent::__construct($regex, $modifiers, $options);
        if ($regex === null) {
            return;
        }

        //If there were backreferences in regex, subpattern capturing should be forced.
        if ($this->lexer !== null && !$this->options->capturesubpatterns) {
            $this->options->capturesubpatterns = (count($this->lexer->get_backrefs()) > 0);
        }


        //Invalidate match called later to allow parser to count subpatterns
        $this->matchresults->set_source_info(new qtype_poasquestion_string(''), $this->get_max_subpattern(), $this->get_subpattern_map());
        $this->matchresults->invalidate_match();
    }

    /**
    * Match regular expression with given string, calls match_inner from a child class to do the real matching
    * @param str a string to match
    * @return object of qtype_preg_matching_results class
    */
    public function match($str) {

        //Are there any errors?
        if (!empty($this->errors)) {
            throw new qtype_preg_exception('Error: trying to do matching on regex with errors!');
        }

        //Are results cached already?
        if (array_key_exists($str, $this->resultcache)) {
            $this->matchresults = $this->resultcache[$str];
        } else {
            $str = new qtype_poasquestion_string($str);
            //Reset match data and perform matching.
            $this->matchresults = $this->match_inner($str);
            //Save source data for the match
            $this->matchresults->set_source_info($str, $this->get_max_subpattern(), $this->get_subpattern_map());

            //Set all string as incorrect if there were no matching
            if (!$this->matchresults->is_match()) {
                $this->matchresults->invalidate_match();
                //Fill extension start as start of the match in extended string if it was generated.
                if (is_object($this->matchresults->extendedmatch)) {
                    $this->matchresults->extendedmatch->extensionstart = $this->matchresults->extendedmatch->index_first[0];
                }
            } else {
                //Do some sanity checks and calculate necessary fields
                $this->matchresults->validate();
            }

            //Save results to the cache
            $this->resultcache[$str->string()] = $this->matchresults;
        }
        return $this->matchresults;
    }

    /**
     * Do real matching.
     *
     * This function should be re-implemented in child classes using standard matching functions
     * that already contains starting positions loop inside. Implement match_from_pos otherwise.
     * @param qtype_poasquestion_string str a string to match.
     * @return qtype_preg_matching_results object.
     */
    protected function match_inner($str) {

        $result = $this->match_preprocess($str);
        if (is_a($result, 'qtype_preg_matching_results')) {
            return $result;
        }

        $result = new qtype_preg_matching_results();
        $result->set_source_info($str, $this->get_max_subpattern(), $this->get_subpattern_map());
        $result->invalidate_match();

        if ($this->anchor->start) {
            //The regex is anchored from start, so we really should check only one offset.
            //Results for other offsets would be same.
            $rightborder = 1;
        } else {
            // Match from all indexes
            $rightborder = $str->length();
            //Try matching an empty string at least once
            if ($str->length() === 0) {
                $rightborder = 1;
            }
        }

        //Starting positions loop
        for ($j = 0; $j <= $rightborder && !$result->best(); $j++) {
            $tmp = $this->match_from_pos($str, $j);
            if ($result->worse_than($tmp)) {
                $result = $tmp;
            }
        }
        return $result;
    }

    /**
    * Do a necessary preprocessing before matching loop.
    *
    * If a @see{qtype_preg_matching_results} object is returned, it is treated as if match was decided during preprocessing
    * and no actual matching needed.
    */
    protected function match_preprocess($str) {
        return false;
    }

    /**
    * Perform a match of string from specified offset
    *
    * Should be implemented by child classes that use custom matching algorithms
    * @param str a string to match
    * @param offset position from where to match
    * @return qtype_preg_matching_results object
    */
    public function match_from_pos($str, $offset) {
        throw new qtype_preg_exception('Error: matching has not been implemented for '.$this->name().' class');
    }

    /**
    * Returns an object of match results, helper method.
    */
    public function get_match_results() {
        return $this->matchresults;
    }

}
