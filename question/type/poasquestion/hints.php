<?php
// This file is part of Poasquestion question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Poasquestion question type is free software: you can redistribute it and/or modify
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
 * This file contains hint definitions, that is used by different poas questions.
 *
 * Note: interfaces and classes there are intentionally left without qtype_poasquestion prefix as
 *  they are intended for more general Moodle use after hinting behaviours would be complete.
 *
 * @package    qtype_poasquestion
 * @subpackage hints
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

/**
 * Question which could return some specific hints and want to use *withhint behaviours should implement this
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface question_with_qtype_specific_hints {

    /**
     * Returns an array of available specific hint types depending on question settings
     *
     * The keys are hint type indentifiers, unique for the qtype.
     * The values are interface strings with the hint description (without "hint" word!)
     * If a question allows for multiple instance choosen hints, it should return a separate key for each instance. That may depend on $response.
     */
    public function available_specific_hints($response = null);

    /**
     * Hint object factory
     *
     * Returns a hint object for given type, for multiple instance choosen hints response may be needed to generate correct object.
     */
    public function hint_object($hintkey, $response = null);
}

/**
 * Base class for question-type specific hints
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_specific_hint {

    /** 
     *  Single instance hint allows exactly one hint for each question state. 
     *  Example is next character or next lexem hint in preg question type.
     */
    const SINGLE_INSTANCE_HINT = 1;
    /** 
     *  Choosen multiple instance hint allows several hint buttons, from which the user (either teacher or student, depending on behaviour) could choose one they want. 
     *  Example is hint, that would show how you should place misplaced lexem in correct writing question type.
     */
    const CHOOSEN_MULTIPLE_INSTANCE_HINT = 2;
    /** 
     *  Sequential multuple instance hint allows several hints, that could be used only in sequence. 
     *  Current moodle text hints are example of this ones since there are no way to allow students to choose between them.
     */
    const SEQENTIAL_MULTIPLE_INSTANCE_HINT = 3;

    /** @var object Question object, created this hint*/
    protected $question;

    /**
     * Returns one of hint type constants (single instance etc).
     */
    abstract public function hint_type();

    /**
     * Constructs hint object, remember question to use.
     */
    public function __construct($question) {
        $this->question = $question;
    }

    /**
     * Is hint based on response or not?
     *
     * @return boolean true if response is used to calculate hint (and, possibly, penalty)
     */
    abstract public function hint_response_based();

    /**
     * Returns whether question and response allows for the hint to be done.
     */
    abstract public function hint_available($response = null);

    /**
     * Returns whether response is used to calculate penalty (cost) for the hint.
     */
    public function penalty_response_based() {
        return false;//Most hint have fixed penalty (cost)
    }

    /**
     * Returns penalty (cost) for using specific hint of given hint type (possibly for given response).
     *
     * Even if response is used to calculate penalty, hint object should still return an approximation
     * to show to the student if $response is null.
     */
    abstract public function penalty_for_specific_hint($response = null);

    /**
     * Question may decide to render buttons for some hints to place them in more appropriate place near a controls or in specific feedback.
     *
     * Questions should render hint buttons when _nonresp_hintbtns and/or _resp_hintbtns behaviour variable is set, depending on whether hint is response based.
     */
    public function button_rendered_by_question() {
        //By default, hint button should be rendered by behaviour.
        return false;
    }

    /**
     * Renders hint information for given response using question renderer.
     *
     * Response may be omitted for non-response based hints.
     */
    abstract public function render_hint($renderer, $response = null);
}
