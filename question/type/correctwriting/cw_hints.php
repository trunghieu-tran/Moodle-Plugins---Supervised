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
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_correctwriting_hintwhatis extends qtype_specific_hint {

    //@var mistake, with which this hint is associated
    protected $mistake;

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
    }

    public function hint_description() {
        $tokendescr = $this->mistake->token_description($this->mistake->answermistaken[0]);//TODO - get better one for mistakes with more than 1 answer token
        //Token description should exists there, otherwise there should be no such hint object at all (cf. question class, available hints method.
        return get_string('whatis', 'qtype_correctwriting', $tokendescr);
    }

    //"What is" hint is obviously response based, since it used to better understand mistake message.
    public function hint_response_based() {
        return true;
    }

    //This hint is always available if hint, otherwise question will return null instead of hint object.
    //The hint is disabled when penalty is set above 1.
    public function hint_available($response = null) {//TODO - maybe create a non-available null instance of the hint object instead?
        return ($this->question->whatishintpenalty <= 1.0);
    }

    public function penalty_for_specific_hint($response = null) {
        return $this->question->whatishintpenalty;
    }

    //Buttons are rendered by the question to place them in specific feedback near relevant mistake message.
    public function button_rendered_by_question() {
        return true;
    }

    public function render_hint($renderer, $response = null) {
        $answerindex = $this->mistake->answermistaken[0];
        $a = new stdClass;
        $a->tokendescr = $this->mistake->token_description($answerindex);//TODO - get better one for mistakes with more than 1 answer token
        $a->tokenvalue = $this->mistake->answer[$answerindex]->value();
        if (!is_string($a->tokenvalue)) {
            $a->tokenvalue = $a->tokenvalue->string();
        }
        return get_string('whatishint', 'qtype_correctwriting', $a);
    }
}