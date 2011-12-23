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

    /** @var boolean Any match found? 
    *
    *The match considered found if at least one character is matched or there is full match of zero length (regex with just asserts)
    */
    public $is_match;
    /** @var boolean Is match full or partial? */
    public $full;
    /** @var array Indexes of first matched character - array where 0 => full match, 1=> first subpattern etc. */
    public $index_first;
    /** @var array Indexes of last matched character - array where 0 => full match, 1=> first subpattern etc. */
    public $index_last;
    /** @var character Possible next character. 
    * 
    * Should be empty string if there is no possible next character in this location.
    */
    public $next;
    /** @var integer The number of characters left to complete matching. */
    public $left;

    public function __construct($is_match = false, $full = false, $index_first = array(), $index_last = array(), $next = '', $left = -1) {
        $this->is_match = $is_match;
        $this->full = $full;
        $this->index_first = $index_first;
        $this->index_last = $index_last;
        $this->next = $next;
        $this->left = $left;
    }

    /**
    * Invalidates match by setting all data to no match values
    */
    public function invalidate_match($subpattcount = 0) {
        $this->is_match = false;
        $this->full = false;
        $this->next = '';
        $this->left = -1;
        $this->index_last = array();
        $this->index_first = array();
        //Having both indexes as -1 allows to consider all string as the wrong tail
        for ($i = 0; $i <= $subpattcount; $i++) {
            $this->index_first[$i] = -1;
            $this->index_last[$i] = -1;
        }
    }

    /**
    * Returns the count of matched subpatterns
    */
    public function matched_subpatterns_count() {
        $subpattcount = 0;
        foreach ($this->index_first as $key=>$value) {
            if ($key != 0 && $this->matchresults->index_last[$key] >= -1) {//-1 == no match for this subpattern
                $subpattcount++;
            }
        }
        return $subpattcount;
    }

    /**
    * Throws exception if match results contain obvious abnormalities
    */
    public function validate() {
        if ($this->is_match) {//Match found
            if (!isset($this->index_first[0]) || !isset($this->index_last[0])) {
                throw new qtype_preg_exception('Error: match was found but no match information returned');
            }
        }
    }

    /**
    * Calculate last character index from first index (should be in $this->index_first) and length
    * Use to adopt you engine in case it finds length instead of character index
    * Results is set in $this->index_last
    * @param length array of lengths of matches with (sub)patterns
    */
    public function from_length_to_last_index($length) {
        foreach($length as $num=>$len) {
            $this->index_last[$num] = $this->index_first[$num] + $len - 1;
        }
    }
}

class preg_matcher extends preg_regex_handler {

    //Constants for the capabilities which could (or could not) be supported by matching engine
    //Partial matching (returning the index of last matched character)
    const PARTIAL_MATCHING = 0;
    //Returning next possible character after partial match
    const NEXT_CHARACTER = 1;
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
    * @return bool true for complete match, false otherwise
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
            return $this->matchresults->full;
        }

        //Reset match data and perform matching.
        $this->matchresults = new qtype_preg_matching_results();
        $this->matchresults->invalidate_match($this->maxsubpatt);
        $this->match_inner($str);

        //Set all string as incorrect if there were no matching
        if (!$this->matchresults->is_match) {
            $this->matchresults->invalidate_match($this->maxsubpatt);
        } else {//do some sanity checks

            //Check that engine have correct capabilities
            $subpattcnt = $this->matchresults->matched_subpatterns_count();
            if(!$this->is_supporting(preg_matcher::SUBPATTERN_CAPTURING) && $subpattcnt > 0) {
                throw new qtype_preg_exception('Error: subpatterns returned while engine '.$this->name().' doesn\'t support subpattern matching');
            }
            if(!$this->is_supporting(preg_matcher::NEXT_CHARACTER) && $this->matchresults->next !== '') {
                throw new qtype_preg_exception('Error: next character returned while engine '.$this->name().' doesn\'t support next character generation');
            }
            if(!$this->is_supporting(preg_matcher::CHARACTERS_LEFT) && $this->matchresults->left != -1) {
                throw new qtype_preg_exception('Error: characters left returned while engine '.$this->name().' doesn\'t support determining of how many characters left');
            }

            $this->matchresults->validate();
        }

        //Save results to the cache
        $this->resultcache[$str] = $this->matchresults;
        return $this->matchresults->full;
    }

    /**
    * Do real matching, should be implemented in child classes, set matchresults property.
    * @param str a string to match
    */
    protected function match_inner($str) {
        throw new qtype_preg_exception('Error: matching has not been implemented for '.$this->name().' class');
    }

    /** 
    * Returns an associative array of match results, helper method.
    */
    public function get_match_results() {
        return $this->matchresults;
    }

    /**
    * Is there a matching at all?
    */
    public function match_found() {
        return $this->matchresults->is_match;
    }

    /**
    * Returns true if there is a complete match, false otherwise - any matching engine should support at least that
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
    */
    public function is_subpattern_captured($subpattern) {
        if ($subpattern > $this->maxsubpatt) {
            throw new qtype_preg_exception('Error: Asked for subpattern '.$subpattern.' while only '.$this->maxsubpatt.' available');
        }
        return ($this->matchresults->index_first[$subpattern] > -1 || $this->matchresults->index_last[$subpattern] > -1);
    }

    /**
    * Returns first correct character index
    * @param subpattern subpattern number, 0 for the whole match
    */
    public function first_correct_character_index($subpattern = 0) {
        if ($subpattern > $this->maxsubpatt) {
            throw new qtype_preg_exception('Error: Asked for subpattern '.$subpattern.' while only '.$this->maxsubpatt.' available');
        }
        return $this->matchresults->index_first[$subpattern];
    }

    /**
    * Returns the index of last correct character if engine supports partial matching
    * @param subpattern subpattern number, 0 for the whole match
    * @return the index of last correct character
    */
    public function last_correct_character_index($subpattern = 0) {
        if ($subpattern > $this->maxsubpatt) {
            throw new qtype_preg_exception('Error: Asked for subpattern '.$subpattern.' while only '.$this->maxsubpatt.' available');
        }
        return $this->matchresults->index_last[$subpattern];
    }

    /**
    * Returns (partialy) matched portion of string
    */
    public function matched_part($subpattern = 0) {
        if(array_key_exists($subpattern, $this->matchresults->index_first)) {
            return substr($this->str, $this->matchresults->index_first[$subpattern], $this->matchresults->index_last[$subpattern] - $this->matchresults->index_first[$subpattern] + 1);
        } else if ($subpattern > $this->maxsubpatt) {
            throw new qtype_preg_exception('Error: Asked for subpattern '.$subpattern.' while only '.$this->maxsubpatt.' available');
        } else {
            return '';
        }
    }

    /**
    * Returns next possible character (to hint) or empty string if there is no one possible
    */
    public function next_char() {
        if ($this->is_supporting(preg_matcher::NEXT_CHARACTER)) {
            return $this->matchresults->next;

        }
        throw new qtype_preg_exception('Error:'.$this->name().' class doesn\'t supports hinting');
    }

    /**
    * Returns how many characters left to closest possible match
    */
    public function characters_left() {
        if ($this->is_supporting(preg_matcher::CHARACTERS_LEFT)) {
            return $this->matchresults->left;
        }
        throw new qtype_preg_exception('Error:'.$this->name().' class doesn\'t supports counting of the remaining characters');
    }

}
?>