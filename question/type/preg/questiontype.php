<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
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
 * Defines the Preg question type class.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/shortanswer/questiontype.php');
require_once($CFG->dirroot . '/question/type/preg/question.php');

class qtype_preg extends qtype_shortanswer {

    /**
     * Returns an array of available engines.
     * key = engine indentifier, value = interface string with engine name.
     */
    public function available_engines() {
        return array(   'php_preg_matcher' => get_string('php_preg_matcher', 'qtype_preg'),
                        'fa_matcher' => get_string('fa_matcher', 'qtype_preg')
                    );
    }

    /**
     * Returns an array of supported notations.
     * key = notation indentifier, value = interface string with notation name.
     */
    public function available_notations() {
        return array(   'native' => get_string('notation_native', 'qtype_preg'),
                        'pcreextended' => get_string('notation_pcreextended', 'qtype_preg'),
                        'mdlshortanswer' => get_string('notation_mdlshortanswer', 'qtype_preg')
                    );
    }

    public function name() {
        return 'preg';
    }

    public function extra_question_fields() {
        $extraquestionfields = parent::extra_question_fields();
        array_splice($extraquestionfields, 0, 1, 'qtype_preg_options');
        array_push($extraquestionfields, 'correctanswer', 'exactmatch', 'usecharhint', 'charhintpenalty', 'hintgradeborder',
                    'engine', 'notation', 'uselexemhint', 'lexemhintpenalty', 'langid', 'lexemusername');
        return $extraquestionfields;
    }

    public function extra_answer_fields() {
        return array ('qtype_preg_regex_tests', 'regextests');
    }

    /**
     * Returns true is answer with the $key is empty in the question data and should not be saved in DB.
     *
     * The questions with non-standard uses for question_answers table will want to overload this.
     * @param object $questiondata This holds the information from the question editing form or import.
     * @param int $key A key of the answer in question.
     * @return bool True if answer shouldn't be saved in DB.
     */
    protected function is_answer_empty($questiondata, $key) {
        /*return trim($questiondata->answer[$key]) == '' && $questiondata->fraction[$key] == 0 &&
                    html_is_blank($questiondata->feedback[$key]['text']);*/ // This is shortanswer variant.
        // Empty regex will match with anything and it's easy to save it by mistake.
        // So not saving answers with empty regexes.
        // If the teacher want matching with anything, he could use .* instead.
        return trim($questiondata->answer[$key]) == '';
    }

    /**
     * Returns true if extra answer fields for answer with the $key is empty
     * in the question data and should not be saved in DB.
     *
     * Questions where extra answer fields are optional will want to overload this.
     * @param object $questiondata This holds the information from the question editing form or import.
     * @param int $key A key of the answer in question.
     * @return bool True if extra answer data shouldn't be saved in DB.
     */
    protected function is_extra_answer_fields_empty($questiondata, $key) {
        return !isset($questiondata->regextests) || trim($questiondata->regextests[$key]) == '';
    }

    /** Overload import from Moodle XML format to import hints */
    public function import_from_xml($data, $question, qformat_xml $format, $extra=null) {
        $qo = parent::import_from_xml($data, $question, $format, $extra);
        $format->import_hints($qo, $data, false, true);
        return $qo;
    }

    /*public function export_to_xml($question, qformat_xml $format, $extra=null) {
        $expout = parent::export_to_xml($question, $format, $extra);
        //$expout .= $format->write_hints($question);
        return $expout;
    }*/

    /** Overload hints functions to be able to work with interactivehints*/
    protected function make_hint($hint) {
        return qtype_poasquestion\moodle_hint_adapter::load_from_record($hint);
    }

    protected function save_hint_options($formdata, $number, $withparts) {
        $options = $formdata->interactivehint[$number];
        return $options;
    }

    /**
     * Determine if the hint with specified number is not empty and should be saved.
     * Overloaded because use custom hint controls.
     * @param object $formdata the data from the form.
     * @param int $number number of hint under question.
     * @param bool $withparts whether to take into account clearwrong and shownumcorrect options.
     * @return bool is this particular hint data empty.
     */
    protected function is_hint_empty_in_form_data($formdata, $number, $withparts) {
            return parent::is_hint_empty_in_form_data($formdata, $number, $withparts) && $formdata->interactivehint[$number] == 'hintmatchingpart';
    }

}
