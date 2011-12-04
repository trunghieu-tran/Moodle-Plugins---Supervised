<?php
/**
 * Defines abstract class of regular expression matcher, extend it to create a new matching engine
 *
 * @copyright &copy; 2010  Oleg Sychev
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');

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
    * returns true for supported capabilities
    * @param capability the capability in question
    * @return bool is capability supported
    */
    public function is_supporting($capability) {
        return false;//abstract class supports nothing
    }

    //Matching results
    //String with which match is performed
    protected $str;
    //Is any match found?
    protected $is_match;
    //Is match full or partial?
    protected $full;
    //Indexes of first matched character - array where 0 => full match, 1=> first subpattern etc
    protected $index_first;
    //Indexes of the last matched character - array where 0 => full match, 1=> first subpattern etc
    protected $index_last;
    //Possible next character
    protected $next;
    //The number of characters left for matching
    protected $left;
    //Cache of the matching results,  string for matching is the key
    protected $result_cache;

    public function name() {
        return 'preg_matcher';
    }

    /**
    *parse regex and do all necessary preprocessing
    @param regex - regular expression for which will be build finite automate
    @param modifiers - modifiers of regular expression
    */
    public function __construct($regex = null, $modifiers = null) {
        $this->is_match = false;
        $this->full = false;
        $this->next = '';
        $this->left = -1;
        $this->result_cache = array();


        parent::__construct($regex, $modifiers);
        if ($regex === null) {
            return;
        }

        $this->reset_subpattern_indexes();
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
    match regular expression with given string, calls match_inner from a child class to do the real matching
    @param str a string to match
    @return bool true for complete match, false otherwise
    */
    public function match($str) {

        //Are there any errors?
        if (!empty($this->errors)) {
            throw new qtype_preg_exception('Error: trying to do matching on regex with errors!');
        }

        $this->str = $str;
        //Are results cached already?
        if (array_key_exists($str,$this->result_cache)) {
            $result = $this->result_cache[$str];
            $this->full = $result['full'];
            $this->index_last = $result['index_last'];
            $this->index_first = $result['index_first'];
            $this->next = $result['next'];
            $this->left = $result['left'];
            $this->is_match = $result['is_match'];
            return $this->full;
        }

        //Reset match data and perform matching.
        $this->is_match = false;
        $this->full = false;
        $this->next = '';
        $this->left = -1;
        $this->match_inner($str);

        //Set all string as incorrect if there were no matching
        if (!$this->is_match) {
            $this->index_first[0] = strlen($str);//first correct character is outside the string, so all string is the wrong heading
            $this->index_last[0] = $this->index_first[0] - 1 ;//there are no correct characters
            $this->full = false;
        } else {//do some sanity checks
            $subpattcnt = 0;
            foreach ($this->index_first as $key=>$value) {
                if ($key != 0 && $this->index_last[$key] >= $value) {
                    $subpattcnt++;
                }
            }
            if(!$this->is_supporting(preg_matcher::SUBPATTERN_CAPTURING) && $subpattcnt > 0) {
                throw new qtype_preg_exception('Error: subpatterns returned while engine '.$this->name().' doesn\'t support subpattern matching');
            }
            if(!isset($this->index_first[0]) || !isset($this->index_last[0])) {
                throw new qtype_preg_exception('Error: match was found but no match information returned');
            }
        }

        //Save results to the cache
        $this->result_cache[$str] = array('full' => $this->full, 'index_last' => $this->index_last, 'index_first' => $this->index_first, 'next' => $this->next, 'left' => $this->left, 'is_match' => $this->is_match);
        return $this->full;
    }

    /**
    *Do real matching, should be implemented in child classes, set properties full, index, next and left.
    @param str a string to match
    */
    protected function match_inner($str) {
        throw new qtype_preg_exception('Error: matching has not been implemented for '.$this->name().' class');
    }

    /** 
    * Returns an associative array of match results, helper method.
    */
    public function get_match_results() {
        $res = array('is_match' => $this->is_match);
        if ($this->is_match) {
            $res['full'] = $this->full;
            $res['index_first'] = $this->index_first;
            $res['index_last'] = $this->index_last;
            if ($this->is_supporting(preg_matcher::NEXT_CHARACTER)) {
                $res['next'] = $this->next;
            }
            if ($this->is_supporting(preg_matcher::CHARACTERS_LEFT)) {
                $res['left'] = $this->left;
            }
        } else {
            $res['full'] = false;
            //We could still hint first possible character if there was no match at all.
            if ($this->is_supporting(preg_matcher::NEXT_CHARACTER)) {
                $res['next'] = $this->next;
            }
            //If there is no match at all, we have all length of regex to fulfill - but it do exists.
            if ($this->is_supporting(preg_matcher::CHARACTERS_LEFT)) {
                $res['left'] = $this->left;
            }

        }
        return $res;
    }

    /**
    * is there a matching at all?
    */
    public function match_found() {
        return $this->is_match;
    }

    /**
    *returns true if there is a complete match, false otherwise - any matching engine should support at least that
    */
    public function is_matching_complete() {
        return $this->full;
    }


    /**
    * Calculate last character index from first index (should be in $this->index_first) and length
    * Use to adopt you engine in case it finds length instead of character index
    * Results is set in $this->index_last
    @param length array of lengths of matches with (sub)patterns
    */
    protected function from_length_to_last_index($length) {
        foreach($length as $num=>$len) {
            $this->index_last[$num] = $this->index_first[$num] + $len - 1;
        }
    }

    /**
     * Returns the number of subpatterns existing in regular expression.
     * It is equal to the last subpattern number
     */
    public function count_subpatterns() {
        return $this->maxsubpatt;
    }

    /**
     * Returns the number of captured subpatterns (except full match) in the match
     */
    public function count_matched_subpatterns() {
        return count($this->index_first) - 1;//-1 to not include full match
    }

    /**
    @param subpattern subpattern number
    *returns true if subpattern is captured
    */
    public function is_subpattern_captured($subpattern) {
        if ($subpattern > $this->maxsubpatt) {
            throw new qtype_preg_exception('Error: Asked for subpattern '.$subpattern.' while only '.$this->maxsubpatt.' available');
        }
        return ($this->index_last[$subpattern] >= -1);
    }

    /**
    * Resets first and last indexes to -1 and -2 for all subpatterns
    */
    protected function reset_subpattern_indexes() {
        $this->index_last = array();
        $this->index_first = array();
        for ($i = 0; $i <= $this->maxsubpatt; $i++) {
            $this->index_first[$i] = -1;
            $this->index_last[$i] = -2;
        }
    }

    /**
    @param subpattern subpattern number, 0 for the whole match
    *returns first correct character index
    */
    public function first_correct_character_index($subpattern = 0) {
        if ($subpattern > $this->maxsubpatt) {
            throw new qtype_preg_exception('Error: Asked for subpattern '.$subpattern.' while only '.$this->maxsubpatt.' available');
        }
        return $this->index_first[$subpattern];
    }

    /**
    *returns the index of last correct character if engine supports partial matching
    @param subpattern subpattern number, 0 for the whole match
    @return the index of last correct character
    */
    public function last_correct_character_index($subpattern = 0) {
        if ($subpattern > $this->maxsubpatt) {
            throw new qtype_preg_exception('Error: Asked for subpattern '.$subpattern.' while only '.$this->maxsubpatt.' available');
        }
        return $this->index_last[$subpattern];
    }

    /**
    * returns (partialy) matched portion of string
    */
    public function matched_part($subpattern = 0) {
        if(array_key_exists($subpattern, $this->index_first)) {
            return substr($this->str, $this->index_first[$subpattern], $this->index_last[$subpattern] - $this->index_first[$subpattern] + 1);
        } else if ($subpattern > $this->maxsubpatt) {
            throw new qtype_preg_exception('Error: Asked for subpattern '.$subpattern.' while only '.$this->maxsubpatt.' available');
        } else {
            return '';
        }
    }

    /**
    *returns next possible character (to hint)
    */
    public function next_char() {
        if ($this->is_supporting(preg_matcher::NEXT_CHARACTER)) {
            return $this->next;

        }
        throw new qtype_preg_exception('Error:'.$this->name().' class doesn\'t supports hinting');
    }

    /**
    *returns how many characters left to closest possible match
    */
    public function characters_left() {
        if ($this->is_supporting(preg_matcher::CHARACTERS_LEFT)) {
            return $this->left;
        }
        throw new qtype_preg_exception('Error:'.$this->name().' class doesn\'t supports counting of the remaining characters');
    }

}
?>