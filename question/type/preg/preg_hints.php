<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
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
 * Perl-compatible regular expression question hints classes.
 *
 * @package    qtype
 * @subpackage preg
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

/**
 * Hint class for next character hint
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_preg_hintnextchar extends qtype_specific_hint {

    /** @var object Matching results to use*/
    public $matchresults = null;

    /**
     * Is hint based on response or not?
     *
     * @return boolean true if response is used to calculate hint (and, possibly, penalty)
     */
    public function hint_response_based() {
        return true;//Next character hint based on response
    }

    /** 
     * Returns whether response allows for the hint to be done
     */
    public function hint_available($response = null) {
        return true;// next character hint available anywhere - TODO check where answer is correct or no next character could be generated
    }

    /** 
     * Returns specific hint value of given hint type for given response
     */
    public function specific_hint($response = null) {
            if( $this->matchresults === null) {
                $bestfit = $this->question->get_best_fit_answer($response);
                $this->matchresults = $bestfit['match'];
            }
            if ($this->matchresults->correctending === qtype_preg_matching_results::UNKNOWN_NEXT_CHARACTER || $this->matchresults->correctending === qtype_preg_matching_results::DELETE_TAIL) {
                $hint = null;
            } else {
                $hint = new stdClass();
                $hint->str = $this->matchresults->correctending[0];
                $hint->tobecontinued = false;
                if (strlen($this->matchresults->correctending) > 1 || $this->matchresults->correctendingcomplete === false) {
                    $hint->tobecontinued = true;
                }
            }
            return $hint;
    }

    /** 
     * Returns penalty for using specific hint of given hint type (possibly for given response)
     */
    public function penalty_for_specific_hint($response = null) {
            return $this->question->hintpenalty;
    }
}