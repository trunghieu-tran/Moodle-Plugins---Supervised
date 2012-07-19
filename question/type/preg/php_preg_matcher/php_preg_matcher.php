<?php //$Id: dfa_preg_matcher.php, v 0.1 beta 2010/08/08 23:47:35 dvkolesov Exp $

/**
 * Defines class preg_php_matcher, matching engine based on php preg extension
 * It support the more complicated regular expression possible with great speed, but doesn't allow partial matching and hinting
 *
 * @copyright &copy; 2010  Oleg Sychev
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

class qtype_preg_php_preg_matcher extends qtype_preg_matcher {

    public function is_supporting($capability) {
        switch ($capability) {
        case qtype_preg_matcher::SUBPATTERN_CAPTURING :
            return true;
            break;
        }
        return false; //Native matching doesn't support any partial matching capabilities
    }

    public function name() {
        return 'php_preg_matcher';
    }

    /**
    * Returns string of regular expression modifiers supported by this engine
    */
    public function get_supported_modifiers() {
        return new qtype_poasquestion_string('imsxeADSUX');
    }

    /**
    * Does this engine need a parsing of regular expression?
    * @return bool if parsing needed
    */
    protected function is_parsing_needed() {
        //no parsing needed
        return false;
    }

    /**
    * Check regular expression for errors
    * @return bool is tree accepted
    */
    protected function accept_regex() {
        $for_regexp = $this->regex;
        if (strpos($for_regexp,'/') !== false) {//escape any slashes
            $for_regexp = implode('\/',explode('/',$for_regexp));
        }
        $for_regexp = '/'.$for_regexp.'/u';

        if (preg_match($for_regexp,'test') === false) {//preg_match returns false when regular expression contains error
            $this->errors[] = new qtype_preg_error(get_string('PCREincorrectregex','qtype_preg'));
            return false;
        }

        return true;
    }

    /**
    * Do real matching
    * @param str a string to match
    */
    protected function match_inner($str) {
        //Prepare results
        $matchresults = new qtype_preg_matching_results();
        $matchresults->invalidate_match($this->get_max_subpattern());

        //Preparing regexp
        $for_regexp = $this->regex;
        if (strpos($for_regexp,'/') !== false) {//escape any slashes
            $for_regexp = implode('\/',explode('/',$for_regexp));
        }
        $for_regexp = '/'.$for_regexp.'/u';
        $for_regexp .= $this->modifiers;

        //Do matching
        $matches = array();
        //No need to find all matches since preg_match don't return partial matches, any full match is sufficient
        $full = preg_match($for_regexp, $str, $matches, PREG_OFFSET_CAPTURE);
        //$matches[0] - match with the whole regexp, $matches[1] - first subpattern etc
        //$matches[$i] format is array(0=> match, 1 => offset of this match)
        if ($full) {
            $matchresults->full = true;//No partial matching from preg_match
            foreach ($matches as $i => $match) {
                $matchresults->index_first[$i] = $match[1];
                $matchresults->length[$i] = strlen($match[0]);
            }
        }

        return $matchresults;
    }



}
?>
