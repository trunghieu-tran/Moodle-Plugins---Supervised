<?php
// This file is part of Correct Writing question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Correct Writing question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Correct Writing is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains hint definitions, that is used by Correct Writing question type.
 *
 * @package    qtype_correctwriting
 * @subpackage hints
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/poasquestion/hints.php');
require_once($CFG->dirroot . '/question/type/correctwriting/question.php');
require_once($CFG->dirroot . '/blocks/formal_langs/block_formal_langs.php');


/**
 * "What is" hint shows a token value instead of token description
 *
 * @copyright  2013 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_correctwriting_hintwhatis extends qtype_specific_hint {

    //@var mistake, with which this hint is associated
    protected $mistake;
    //@var token(s) descriptions for the hint
    protected $tokendescr;

    public function hint_type() {
        return qtype_specific_hint::CHOOSEN_MULTIPLE_INSTANCE_HINT;
    }

    /**
     * Constructs hint object, remember question to use.
     */
    public function __construct($question, $hintkey, $mistake) {
        $this->question = $question;
        $this->hintkey = $hintkey;
        $this->mistake = $mistake;
        if ($mistake !== null) {
            $this->tokendescr = $this->mistake->token_descriptions();
        }
    }

    public function hint_description() {
        return get_string('whatis', 'qtype_correctwriting', $this->tokendescr);
    }

    //"What is" hint is obviously response based, since it used to better understand mistake message.
    public function hint_response_based() {
        return true;
    }

    /**
     * The hint is disabled when penalty is set above 1.
     * Mistake === null if attempt to create hint was unsuccessfull
     * Tokendescr === null if there are no token with description on this mistake
     */
    public function hint_available($response = null) {
        return $this->question->whatishintpenalty <= 1.0 && $this->mistake !== null && $this->tokendescr !== null;
    }

    public function penalty_for_specific_hint($response = null) {
        return $this->question->whatishintpenalty;
    }

    //Buttons are rendered by the question to place them in specific feedback near relevant mistake message.
    public function button_rendered_by_question() {
        return true;
    }

    public function render_hint($renderer, question_attempt $qa, question_display_options $options, $response = null) {
        if ($this->mistake !== null) {
            $hinttext = new qtype_poasquestion_string($this->mistake->token_descriptions(true));
            //Capitalize first letter
            $hinttext[0] = textlib::strtoupper($hinttext[0]);
            return $hinttext;
        }
    }
}