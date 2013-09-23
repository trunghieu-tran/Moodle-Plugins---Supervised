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
 * Defines class of syntax analyzer for correct writing question.
 *
 * Syntax analyzer object is created for each possible LCS of answer and response and
 * is responsible for grouping tokens into abstract syntax tree (AST) and using it to
 * generate more descriptive mistakes, based on tree nodes (which may represent logically
 * grouped sequence of tokens) instead of just single tokens.
 *
 * Syntax analyzers are the last line of analyzers for now.
 * They should throw an exception on creation if given language (or engine)
 * doesn't support syntax analysis.
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitry Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/correctwriting/sequence_analyzer.php');


class qtype_correctwriting_syntax_analyzer extends qtype_correctwriting_abstract_analyzer {

    protected $ast;//abstract syntax tree of answer (with labels)
    protected $subtrees;//array - trees created by parsing parts of response don't covered by LCS
    protected $mistakes;//array of objects - student errors (merged from all stages)

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
        parent::__construct($question, $basepair, $language, $bypass);

    }

    /**
     * Ignore everything
     */
    protected function analyze() {
        //TODO:
        // -1. This list should be edited
        // 0. Throw exception if language doesn't support parsing or syntax analyzer isnt' written yet
        //1. Create Abstract Syntax Tree for answer - Pashaev
        //  - call language object to do it
        //2. Label tree using LCS - Pashaev
        //  - special function
        //3. Parse parts of corrected response don't covered by LCS - Pashaev
        //  - special function, calling language object when necessary
        //4. Find coverage(s) of AST with subtrees from previous stage - Pashaev
        //  - special function
        //5. Set array of mistakes accordingly - Pashaev
        //  - special function
        //NOTE: if response is null just check for errors - Pashaev
        //NOTE: if some stage create errors, stop processing right there
        parent::bypass();
    }


    /**
     * Lexical analyzer does not have any hints, currently
     * @return array
     */
    public function supported_hints() {
        return array();
    }

    public function name() {
        return 'syntax_analyzer';
    }

    // Other necessary access methods
    // Other reimplemented methods

    /**
     * Returns array of objects describing nodes from answer for which teacher-supplied sematic names required
     *
     * Keys are unique id's, that would allow analyzer to identify node, values are substring of answer corresponding to this node
     */
    public function nodes_with_semantic_names() {
    }

    /**
     * Returns a name for a leaf node - token from answer
     */
    public function token_name($index) {
    }

    /**
     * Allows analyzer to replace mistakes from other analyzer.
     * For example syntax_analyzer can replace mistakes from sequence_analyzer.
     *
     * Types of mistakes should be matched against other with replaces_mistake_types.
     * @return array
     */
    public function replaces_mistake_types() {
        return array('sequence_mistake');
    }

}
?>