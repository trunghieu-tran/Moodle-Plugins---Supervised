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

require_once($CFG->dirroot . '/question/type/preg/preg_matcher.php');

class preg_php_matcher extends preg_matcher {

    public function is_supporting($capability) {
        switch ($capability) {
        case preg_matcher::SUBPATTERN_CAPTURING :
            return true;
            break;
        }
        return false; //Native matching doesn't support any partial matching capabilities
    }

    public function name() {
        return 'preg_php_matcher';
    }

    /**
    * returns string of regular expression modifiers supported by this engine
    */
    public function get_supported_modifiers() {
        return 'imsxeADSUX';
    }

    /**
    * is this engine need a parsing of regular expression?
    @return bool if parsing needed
    */
    protected function is_parsing_needed() {
        //no parsing needed
        return false;
    }

    /**
    *check regular expression for errors
    @return bool is tree accepted
    */
    protected function accept_regex() {
        $for_regexp = $this->regex;
        if (strpos($for_regexp,'/') !== false) {//escape any slashes
            $for_regexp = implode('\/',explode('/',$for_regexp));
        }
        $for_regexp = '/'.$for_regexp.'/u';

        if (preg_match($for_regexp,'test') === false) {
            $this->errors[] = new preg_error(get_string('PCREincorrectregex','qtype_preg'));
            return false;
        }

        return true;
    }

    /**
    *do real matching
    @param str a string to match
    */
    protected function match_inner($str) {
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
            $this->is_match = true;
            $this->full = true;
            foreach ($matches as $i => $match) {
                $this->index_first[$i] = $match[1];
                $this->index_last[$i] = $this->index_first[$i] + strlen($match[0]) - 1;
            }
        }

    }



}
?>