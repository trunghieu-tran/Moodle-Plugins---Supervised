<?php
// This file is part of CorrectWriting question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// CorrectWriting question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// CorrectWriting is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with CorrectWriting.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Defines class of abstract analyzer for correct writing question.
 *
 * Abstract analyzer class defines an interface any analyzer should implement.
 * Analyzers have state, i.e. for each analyzed pair of strings there will be differrent analyzer
 *
 * @copyright &copy; 2013  Oleg Sychev
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/correctwriting/string_pair.php');

abstract class qtype_correctwriting_abstract_analyzer {
    /**
     * A reference to the question object with necessary data (language id, answers, threshold etc).
     * @var qtype_correctwriting_question 
     */
    protected $question;

    /**
     * Language object - contains scaner, parser etc.
     * @var block_formal_langs_abstract_language child classes.
     */
    protected $language;

    /**
     * String pair, passed as input data for the analyzer.
     * @var qtype_correctwriting_string_pair 
     */
    protected $basestringpair;


    /**
     * Best (judging by fitness) string pairs generated as result of analyzer's work.
     *
     * Analyzer should return several string pairs only if they are equivalent from it's point of view.
     * An empty array means error, that don't allow subsequent analyzers to work.
     * @var array of qtype_correctwriting_string_pair 
     */
    protected $resultstringpairs = array();


    /**
     * Returns analyzer internal name, which can be used as an argument to get_string().
     */
    abstract public function name();

    /**
     * Do all processing and fill resultstringpairs and resultmistakes fields.
     *
     * You are normally don't want to overload it. Overload analyze() and bypass() instead.
     * Passed responsestring could be null, than object used just to find errors in the answers, token count etc...
     * When called without params just creates empty object to call analyzer-dependent functions on.
     * @throws moodle_exception if invalid number of string pairs
     * @param qtype_correctwriting_question $question
     * @param qtype_correctwriting_string_pair $basepair a pair, passed as input
     * @param block_formal_langs_abstract_language $language a language
     * @param bool $bypass false if analyzer should work, true if it should just allow subsequent analyzers to work.
     */
    public function __construct($question = null, $basepair = null, $language = null, $bypass = true) {
        if ($question === null) {
            return;
        }
        $this->question = $question;
        $this->language = $language;
        $this->basestringpair = $basepair;
        if ($bypass) {
            $this->bypass();
        } else {
            $this->analyze();
            if (count($this->resultstringpairs) == 0) {
                throw new moodle_exception('There must be at least one output pair in '
                                           . get_string($this->name(), 'qtype_correctwriting')); // TODO - make a language string and normal exception.
            }
        }
    }

    /**
     * Do real analyzing and fill resultstringpairs and resultmistakes fields.
     *
     * Passed responsestring could be null, than object used just to find errors in the answers, token count etc...
     */
    abstract protected function analyze();

    /**
     * Fill resultstringpairs with a string pair, that simulates work of this analyzer allowing subsequent analyzers to work.
     *
     * You are normally would overload this, starting overload with parent function call, then add you work.
     * Don't actually analyze something, no mistakes generated: just fill necessary fields in string pair.
     */
    protected function bypass() {
        $this->resultstringpairs[] = clone $this->basestringpair; //Clone string pair for future use.
    }

    /**
     * Returns resulting string pairs array.
     */
    public function result_pairs() {
        return $this->resultstringpairs;
    }


    /**
     * Returns a mistake type for a error, used by this analyzer
     * @return string
     */
    protected function own_mistake_type() {
        return 'qtype_correctwriting_response_mistake';
    }
    /**
     * Returns fitness as aggregate measure of how students response fits this particular answer - i.e. more fitness = less mistakes.
     * Used to choose best matched answer.
     * Fitness is negative or zero (no errors, full match).
     * Fitness doesn't necessary equivalent to the number of mistakes as each mistake could have different weight.
     * Each analyzer will calculate fitness only for it's own mistakes, ignoring mistakes from other analyzers.
     * Dev. comment: since all mistakes have weight, we can have common algorithm as reduction operation
     * on this mistakes.
     * @param array $mistakes of qtype_correctwriting_response_mistake child classes $mistakes Mistakes to calculate fitness from, can be empty array.
     * @return double
     */
    public function fitness($mistakes) {
        $result = 0;
        $mytype =  $this->own_mistake_type();
        if (count($mistakes)) {
            /** qtype_correctwriting_response_mistake $mistake */
            foreach($mistakes as $mistake) {
                if (is_a($mistake, $mytype)) {
                    $result += $mistake->weight;
                }
            }
        }
        return $result * -1;
    }

    /**
     * Returns an array of hint keys, supported by mistakes from this analyzer.
     */
    abstract public function supported_hints();

    // Question editing form and DB methods starts there.

    /**
     * Returns an array of extra_question_fields used by this analyzer.
     */
     public function extra_question_fields() {
        return array();
     }

    /**
     * Returns array of floating point fields for the form. Subsequent commentaries comments keys:
     * 'name' => field name, there should be label as get_string('name', 'qtype_correctwriting') and help as get_string('name_help', 'qtype_correctwriting')
     * 'default' => default value for the form field
     * 'advanced' => boolean value - whether field is advanced one
     * 'min', 'max' => limits for the field value
     */
    public function float_form_fields() {
        return array();
    }

    /**
     * Called from edit_correctwriting_form::definition_inner() within form section for this analyzer.
     * You will typically call parent, then add other fields.
     * @param MoodleQuickForm $mform
     */
    public function form_section_definition(&$mform) {
        foreach ($this->float_form_fields() as $params) {
            $mform->addElement('text', $params['name'], get_string($params['name'], 'qtype_correctwriting'), array('size' => 6));
            $mform->setType($params['name'], PARAM_FLOAT);
            $mform->setDefault($params['name'], $params['default']);
            if ($params['required']) {
                $mform->addRule($params['name'], null, 'required', null, 'client');
            }
            $mform->addHelpButton($params['name'], $params['name'], 'qtype_correctwriting');
            if ($params['advanced']) {
                $mform->setAdvanced($params['name']);
            }
        }
    }

    /**
     * Called from edit_correctwriting_form::data_preprocessing
     */
    public function form_section_data_preprocessing($question) {
        return $question;
    }

    /**
     * Called from edit_correctwriting_form::validation
     */
    public function form_section_validation ($data, $files) {
        $errors = array();
        return $errors;
    }

    /**
     * If this analyzer requires some other ones to work, not bypass - return an array of such analyzers names.
     */
    public function require_analyzers() {
        return array();
    }

    /**
     * Returns if the language is compatible with this analyzer.
     * I.e. syntax analyzer compatible only with parser containing languages.
     * @param block_formal_langs_abstract_language $lang a language object from block_formal_langs
     * @return boolean
     */
    public function is_lang_compatible($lang) {
        return true; // Accept all by default.
    }

    /**
     * Allows analyzer to replace mistakes from other analyzer.
     * For example syntax_analyzer can replace mistakes from sequence_analyzer.
     *
     * Types of mistakes should be matched against other with replaces_mistake_types.
     * @return array
     */
    protected function replaces_mistake_types() {
        return array();
    }

    /**
     * Whether we should filter mistake from list of mistakes.
     * Called if replaces_mistake_types returns one mistake
     * @param qtype_correctwriting_response_mistake  $mistake
     * @return boolean
     */
    protected function should_mistake_be_removed($mistake) {
        return false;
    }

    /**
     * Analyzer can use this function to filter out all mistakes, thst it does not need
     * @param array $set of qtype_correctwriting_response_mistake a mistake set
     * @return array of qtype_correctwriting_response_mistake a mistake set
     */
    protected function remove_mistake_types_from_mistake_set($set) {
        $result  = array();
        $types = $this->replaces_mistake_types();

        if (count($set)) {
            foreach($set as $mistake) {
                if (count($types) != 0) {

                    $removed = false;
                    foreach($types as $type) {
                        if (is_a($mistake, $type)) {
                            $removed = $removed || $this->should_mistake_be_removed($mistake);
                        }
                    }

                } else {
                    $result[]= clone $mistake;
                }
            }
        }
    }
}
