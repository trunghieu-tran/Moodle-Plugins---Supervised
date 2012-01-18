<?php
/**
 * Defines abstract class of regular expression matcher, extend it to create a new matching engine.
 *
 * A matcher is a particulary important type of regex handlers, that allows the question to work at all.
 * The file also define a class to store matching results.
 *
 * @copyright &copy; 2010  Oleg Sychev
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');

class qtype_preg_matching_results {

    //No match captured
    const NO_MATCH_FOUND = -1;
    //No next character generated
    const UNKNOWN_NEXT_CHARACTER = '';
    //How many characters left is unknown
    const UNKNOWN_CHARACTERS_LEFT = 999999999;
    //There is no next character: characters should be deleted to form correct match
    const DELETE_TAIL = null;


    /** @var boolean Is match full or partial? */
    public $full;
    /** @var array Indexes of first matched character - array where 0 => full match, 1=> first subpattern etc. */
    public $index_first;
    /** @var array Length of the matches - array where 0 => full match, 1=> first subpattern etc. */
    public $length;
    /** @var character Possible next character.
     *
     * @deprecated since 2.2, use correctending[0] instead
     */
    public $next;
    /** @var integer The number of characters left to complete matching. */
    public $left;
    /** @var integer Start index for the correct ending.
     *
     * May be less than index_first[0]+length[0] if there is no way to complete matching 
     * from current point of fail due to assertions or another reasons.
     */
    public $correctendingstart;
    /** @var string A string (shortest if possible), which, been added after partial match, would give a full match.
     *
     * Should be UNKNOWN_NEXT_CHARACTER if undetermined by engine, DELETE_TAIL if there is nothing to append, but we should delete instead
     */
    public $correctending;
    /** @var boolean Does correct ending, applied from $correctendingstart, produce full match*/
    public $correctendingcomplete;

    public function __construct($full = false, $index_first = array(), $length = array(), $next = qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER,
                                $left = qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT, $correctendingstart =  qtype_preg_matching_results::NO_MATCH_FOUND,
                                $correctending = qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER, $correctendingcomplete = false) {
        $this->full = $full;
        $this->index_first = $index_first;
        $this->length = $length;
        $this->next = $next;
        $this->left = $left;
        $this->correctendingstart = $correctendingstart;
        $this->correctending = $correctending;
        $this->correctendingcomplete = $correctendingcomplete;
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
    * @return whether @this is worse than $other
    */
    public function worse_than($other, $orequal = false, $longestmatch = false) {

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

        //5. More subpatterns captured - TODO - dubious, it may be needed by NFA, but have not much use comparing matches from different positions
        $thissubpatt = $this->captured_subpatterns_count();
        $othersubpatt = $other->captured_subpatterns_count();
        if ($othersubpatt > $thissubpatt) {
            return true;
        } elseif ($thissubpatt > $othersubpatt) {
            return false;
        }

        return $orequal;//results are equal
    }

    /**
    * Invalidates match by setting all data to no match values
    */
    public function invalidate_match($subpattcount = 0) {
        $this->full = false;
        //$this->next = qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER;
        //$this->left = qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT;
        //Produce one-character correct ending from next
        //TODO - remove when next would be deleted
        if ($this->correctending === qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER && $this->next !== qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER) {
            $this->correctendingcomplete = false;
            $this->correctending = $this->next;
        }
        $this->index_first = array();
        $this->length = array();
        //It is correct to have index_first 0 and length 0 (pure-assert expression matches from the beginning of the response)
        //Use negative values for no match at all
        for ($i = 0; $i <= $subpattcount; $i++) {
            $this->index_first[$i] = qtype_preg_matching_results::NO_MATCH_FOUND;
            $this->length[$i] = qtype_preg_matching_results::NO_MATCH_FOUND;
        }
        $this->correctendingstart = qtype_preg_matching_results::NO_MATCH_FOUND;
    }

    /**
    * Returns the count of matched subpatterns
    */
    public function captured_subpatterns_count() {
        $subpattcount = 0;
        foreach ($this->length as $key=>$length) {
            if ($key != 0 && $length != qtype_preg_matching_results::NO_MATCH_FOUND) {
                $subpattcount++;
            }
        }
        return $subpattcount;
    }

    /**
    * Throws exception if match results contain obvious abnormalities
    * Also feel some values with defaults if they are not supplied by matching engine
    */
    public function validate() {
        if ($this->is_match()) {//Match found
            if (!isset($this->index_first[0]) || !isset($this->length[0])) {
                throw new qtype_preg_exception('Error: match was found but no match information returned');
            }
        }

        //Correct ending starts before matching start
        if ($this->correctendingstart !== qtype_preg_matching_results::NO_MATCH_FOUND && $this->correctendingstart < $this->index_first[0]) {
            throw new qtype_preg_exception('Error: correct ending starts at'.$this->correctendingstart.' before matching starts at'.$this->index_first[0]);
        }

        //Correct ending starts after partial match ending
        if ($this->correctendingstart !== qtype_preg_matching_results::NO_MATCH_FOUND && $this->correctendingstart < $this->index_first[0] + $this->length[0]) {
            throw new qtype_preg_exception('Error: correct ending ends at'.$this->correctendingstart.' while matching ends at'.($this->index_first[0] + $this->length[0]));
        }
        

        //The matching engine didn't supply correct ending start, but supplied next character (and match isn't full).
        //We could assume that correctendingstart==index_first[0]+length[0], i.e. right after matching fail position
        if ($this->correctendingstart === qtype_preg_matching_results::NO_MATCH_FOUND && 
            (!$this->full && $this->next !== qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER)) {
            $this->correctendingstart = $this->index_first[0] + $this->length[0];
        }

        //Correct ending supplied, but next character isn't
        //TODO - remove when next would be deleted
        if ($this->correctending !== qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER && $this->next === qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER) {
            $this->next = $this->correctending[0];
        }

        //Produce one-character correct ending from next
        //TODO - remove when next would be deleted
        if ($this->correctending === qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER && $this->next !== qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER) {
            $this->correctendingcomplete = false;
            $this->correctending = $this->next;
        }
    }

    /**
    * Returns true if subpattern is captured
    * @param subpattern subpattern number
    */
    public function is_subpattern_captured($subpattern) {
        if (!isset($this->length[$subpattern])) {
            throw new qtype_preg_exception('Error: Asked for unexisting subpattern '.$subpattern);
        }
        return ($this->length[$subpattern] != qtype_preg_matching_results::NO_MATCH_FOUND);
    }

    /**
    * Calculates and returns last character index from first index and length
    * Use to adopt in case something can't be easily converted to using length
    * @return array of last matched character indexes of matches with (sub)patterns
    * @deprecated since 2.2 - would be removed in the future
    */
    public function from_length_to_last_index() {
        $index_last = array();
        foreach ($this->length as $num => $len) {
            $index_last[$num] = $this->index_first[$num] + $len - 1;
        }
        return $index_last;
    }

}

class qtype_preg_matcher extends qtype_preg_regex_handler {

    //Constants for the capabilities which could (or could not) be supported by matching engine
    //Partial matching (returning the index of last matched character)
    const PARTIAL_MATCHING = 0;
    //Returning next possible character after partial match
    const CORRECT_ENDING = 1;
    //Returning the smallest number of characters that needed to complete partial match
    const CHARACTERS_LEFT = 2;
    //Subpattern capturing during matching
    const SUBPATTERN_CAPTURING = 3;

    /**
    * Returns true for supported capabilities
    * @param capability the capability in question
    * @return bool is capability supported
    */
    public function is_supporting($capability) {
        return false;//abstract class supports nothing
    }

    //String with which match is performed
    protected $str;
    //Matching results as qtype_preg_matching_results object
    protected $matchresults;
    //Cache of the matching results,  string for matching is the key
    protected $resultcache;

    public function name() {
        return 'preg_matcher';
    }

    /**
    * Parse regex and do all necessary preprocessing.
    *
    * @param regex - regular expression for which will be build finite automate
    * @param modifiers - modifiers of regular expression
    */
    public function __construct($regex = null, $modifiers = null) {
        //Set matching data empty
        $this->matchresults = new qtype_preg_matching_results();
        $this->resultcache = array();

        //Do parsing
        parent::__construct($regex, $modifiers);
        if ($regex === null) {
            return;
        }

        //Invalidate match called later to allow parser to count subpatterns
        $this->matchresults->invalidate_match($this->maxsubpatt);
    }

    /**
    * Fill anchor field to show if regex is anchored using ast_root
    *
    * If all top-level alternatives starts from ^ or .* then expression is anchored from start (i.e. if matching from start failed, no other matches possible)
    * If all top-level alternatives ends on $ or .* then expression is anchored from end (i.e. if matching from start failed, no other matches possible)
    */
    /*protected*/ public function look_for_anchors() {
        //TODO(performance) - write real code, for now think no anchoring is in expressions
        $this->anchor = new stdClass;
        $this->anchor->start = false;
        $this->anchor->end = false;
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

        $this->str = $str;
        //Are results cached already?
        if (array_key_exists($str,$this->resultcache)) {
            $this->matchresults = $this->resultcache[$str];
            return $this->matchresults;
        }

        //Reset match data and perform matching.
        $this->matchresults = $this->match_inner($str);

        //Set all string as incorrect if there were no matching
        if (!$this->matchresults->is_match()) {
            $this->matchresults->invalidate_match($this->maxsubpatt);
        } else {//do some sanity checks

            //Check that engine have correct capabilities
            $subpattcnt = $this->matchresults->captured_subpatterns_count();
            if(!$this->is_supporting(qtype_preg_matcher::SUBPATTERN_CAPTURING) && $subpattcnt > 0) {
                throw new qtype_preg_exception('Error: subpatterns returned while engine '.$this->name().' doesn\'t support subpattern matching');
            }
            if(!$this->is_supporting(qtype_preg_matcher::CHARACTERS_LEFT) && $this->matchresults->left != qtype_preg_matching_results::UNKNOWN_CHARACTERS_LEFT) {
                throw new qtype_preg_exception('Error: characters left returned while engine '.$this->name().' doesn\'t support determining of how many characters left');
            }

            $this->matchresults->validate();
        }

        //Save results to the cache
        $this->resultcache[$str] = $this->matchresults;
        return $this->matchresults;
    }

    /**
    * Do real matching
    *
    * This function should be re-implemented in child classes using standard matching functions
    * that already contains starting positions loop inside. Implement match_from_pos otherwise.
    * @param str a string to match
    * @return qtype_preg_matching_results object
    */
    protected function match_inner($str) {

        $result = $this->match_preprocess($str);
        if (is_a($result, 'qtype_preg_matching_results')) {
            return $result;
        }

        $result = new qtype_preg_matching_results();
        $result->invalidate_match($this->maxsubpatt);


        if ($this->anchor->start) {
            //The regex is anchored from start, so we really should check only one offset.
            //Results for other offsets would be same.
            $rightborder = 1;
        } else {
            //Use textlib to be sure under Unicode
            $textlib = textlib_get_instance();
            $len = $textlib->strlen($str);
            // Match from all indexes
            $rightborder = $len;
            //Try matching an empty string at least once
            if ($str === '') {
                $rightborder = 1;
            }
        }

        //Starting positions loop
        for ($j = 0; $j < $rightborder && !$result->best(); $j++) {
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

    /**
    * Is there a matching at all?
    * @deprecated since 2.2, use get_match_results() instead
    */
    public function match_found() {
        return $this->matchresults->is_match();
    }

    /**
    * Returns true if there is a complete match, false otherwise - any matching engine should support at least that
    * @deprecated since 2.2, use get_match_results() instead
    */
    public function is_matching_complete() {
        return $this->matchresults->full;
    }

    /**
     * Returns the number of subpatterns existing in regular expression.
     * It is equal to the last subpattern number
     */
    public function count_subpatterns() {
        return $this->maxsubpatt;
    }

    /**
    * Returns true if subpattern is captured
    * @param subpattern subpattern number
    * @deprecated since 2.2, use get_match_results() instead
    */
    public function is_subpattern_captured($subpattern) {
        if ($subpattern > $this->maxsubpatt) {
            throw new qtype_preg_exception('Error: Asked for subpattern '.$subpattern.' while only '.$this->maxsubpatt.' available');
        }
        return ($this->matchresults->length[$subpattern] != qtype_preg_matching_results::NO_MATCH_FOUND);
    }

    /**
    * Returns first correct character index
    * @param subpattern subpattern number, 0 for the whole match
    * @deprecated since 2.2, use get_match_results() instead
    */
    public function first_correct_character_index($subpattern = 0) {
        if ($subpattern > $this->maxsubpatt) {
            throw new qtype_preg_exception('Error: Asked for subpattern '.$subpattern.' while only '.$this->maxsubpatt.' available');
        }
        return $this->matchresults->index_first[$subpattern];
    }

    /**
    * Returns length of the match
    * @param subpattern subpattern number, 0 for the whole match
    * @deprecated since 2.2, use get_match_results() instead
    */
    public function match_length($subpattern = 0) {
        if ($subpattern > $this->maxsubpatt) {
            throw new qtype_preg_exception('Error: Asked for subpattern '.$subpattern.' while only '.$this->maxsubpatt.' available');
        }
        return $this->matchresults->length[$subpattern];
    }

    /**
    * Returns the index of last correct character if engine supports partial matching
    * @param subpattern subpattern number, 0 for the whole match
    * @return the index of last correct character
    * @deprecated since 2.2, use get_match_results() instead
    */
    public function last_correct_character_index($subpattern = 0) {
        if ($subpattern > $this->maxsubpatt) {
            throw new qtype_preg_exception('Error: Asked for subpattern '.$subpattern.' while only '.$this->maxsubpatt.' available');
        }
        $index_last = $this->matchresults->from_length_to_last_index();
        return $index_last[$subpattern];
    }

    /**
    * Returns (partialy) matched portion of string
    */
    public function matched_part($subpattern = 0) {
        if(array_key_exists($subpattern, $this->matchresults->index_first)) {
            return substr($this->str, $this->matchresults->index_first[$subpattern], $this->matchresults->length[$subpattern]);
        } else if ($subpattern > $this->maxsubpatt) {
            throw new qtype_preg_exception('Error: Asked for subpattern '.$subpattern.' while only '.$this->maxsubpatt.' available');
        } else {
            return '';
        }
    }

    /**
    * Returns next possible character (to hint) or empty string if there is no one possible
    * @deprecated since 2.2 use get_match_results() instead
    */
    public function next_char() {
        if ($this->is_supporting(qtype_preg_matcher::CORRECT_ENDING)) {
            return $this->matchresults->correctending[0];

        }
        throw new qtype_preg_exception('Error:'.$this->name().' class doesn\'t supports hinting');
    }

    /**
    * Returns how many characters left to closest possible match
    * @deprecated since 2.2, use get_match_results() instead
    */
    public function characters_left() {
        if ($this->is_supporting(qtype_preg_matcher::CHARACTERS_LEFT)) {
            return $this->matchresults->left;
        }
        throw new qtype_preg_exception('Error:'.$this->name().' class doesn\'t supports counting of the remaining characters');
    }

}
?>