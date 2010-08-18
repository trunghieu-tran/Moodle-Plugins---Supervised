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
        return false; //Native matching doesn't support any special capabilities
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
    @param node root of the tree
    @return bool is tree accepted
    */
    protected function accept_regex($node) {
        $for_regexp = $this->regex;
        if (strpos($for_regexp,'/') !== false) {//escape any slashes
            $for_regexp = implode('\/',explode('/',$for_regexp));
        }
        $for_regexp = '/'.$for_regexp.'/u';

        if (preg_match($for_regexp,'test') === false) {

            $this->errors[] = get_string('incorrectregex','qtype_preg');
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
        $this->full = preg_match($for_regexp, $str, $matches, PREG_OFFSET_CAPTURE);
        if ($this->full) {
            $this->index_first = $matches[0][1];//$matches[0] - match with the whole regexp, array(0=> match, 1 => offset of this match)
            $this->index_last = $this->index_first + strlen($matches[0][0]) - 1;
        }
    }



}
?>