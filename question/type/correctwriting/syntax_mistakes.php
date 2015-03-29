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
 * Defines an implementation of mistakes, that are determined by marking of tree
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/correctwriting/response_mistakes.php');


// A marker class to indicate errors from syntax_analyzer.
abstract class qtype_correctwriting_syntax_mistake extends qtype_correctwriting_response_mistake {
    /**
     * A error node for mistake
     * @var block_formal_langs_ast_node_base
     */
    protected $node;

    /**
     * Converts tokens to indexes, sorting them descending
     * @param array $tokens
     * @return indexes
     */
    protected  function tokens_to_indexes($tokens) {
        $result = array();
        for($i = 0; $i < count($tokens); $i++) {
            /** @var block_formal_langs_token_base $token */
            $token = $tokens[$i];
            $result[] = $token->number();
        }
        if (count($result)) {
            sort($result);
        }
        return $result;
    }
}


// A mistake, that consists from moving one lexeme to different position, than original.
class qtype_correctwriting_node_moved_mistake extends qtype_correctwriting_syntax_mistake {

    /**
     * Constructs a new error, filling it with constant message.
     * @param object $language      a language object
     * @param qtype_correctwriting_string_pair  $stringpair  a string pair with information about strings
     * @param block_formal_langs_ast_node_base   $node   node to be used
     */
    public function __construct($language, $stringpair, $node) {
        $this->languagename = $language->name();

        $this->node = $node;
        $this->stringpair = $stringpair;
        $this->position = $node->position();
        $this->mistakemsg = null;
        $lexemes = array_flip($stringpair->movedlexemesindexes);

        // Fill answer data.
        $this->answermistaken = $this->tokens_to_indexes($node->tokens_list());
        // Fill response data.
        $this->responsemistaken = array();
        foreach($this->answermistaken as $index) {
            $this->responsemistaken[] = $lexemes[$index];
        }
        sort($this->responsemistaken);
    }

    /**
     *Performs a mistake message creation if needed
     */
    public function get_mistake_message() {
        if ($this->mistakemsg === null) {
            // Create a mistake message.
            $a = $this->token_description($this->node->number(), true, true);
            $this->mistakemsg = get_string('movedmistakemessage', 'qtype_correctwriting', $a);
        }
        return parent::get_mistake_message();
    }

    /**
     * Returns a key, uniquely identifying mistake.
     */
    public function mistake_key() {
        return 'moved_node_'.implode(' ', $this->answermistaken);// 'movedtoken_' is better, but too long for question_attempt_step_data name column (32).
    }

}


// A mistake, that consists from adding a lexeme to response, that is not in answer.
class qtype_correctwriting_node_added_mistake extends qtype_correctwriting_syntax_mistake {
    /**
     * Constructs a new error, filling it with constant message.
     * @param object $language      a language object
     * @param block_formal_langs_string_pair  $stringpair  a string pair with information about strings
     * @param block_formal_langs_ast_node_base $node
     */
    public function __construct($language, $stringpair, $node) {
        $this->languagename = $language->name();
        $this->stringpair = $stringpair;
        $this->node = $node;
        $this->position = $node->position();
        // Fill answer data.
        $this->answermistaken = array();
        // Fill response data.
        $this->responsemistaken = $this->tokens_to_indexes($node->tokens_list());

        // Create a mistake message.
        $data = $node->value();
        if (!is_string($data)) {
            $data = $data->string();
        }

        $this->mistakemsg = get_string('addedmistakemessage_notexist', 'qtype_correctwriting', $data);
    }

    public function mistake_key() {
        return 'added_node_'.implode(' ', $this->responsemistaken[0]);// 'addedtoken_' is better, but too long for question_attempt_step_data name column (32).
    }
}


// A mistake, that consists of  skipping a lexeme from answer.
class qtype_correctwriting_node_absent_mistake extends qtype_correctwriting_syntax_mistake {

    /**
     * Constructs a new error, filling it with constant message.
     * @param object $language      a language object
     * @param block_formal_langs_string_pair  $stringpair  a string pair with information about strings
     * @param block_formal_langs_ast_node_base $node node data
     */
    public function __construct($language, $stringpair, $node) {
        $this->node = $node;
        $this->languagename = $language->name();

        $this->stringpair = $stringpair;

        $this->position = $node->position();
        // Fill answer data.
        $this->answermistaken = $this->tokens_to_indexes($node->tokens_list());
        // Fill response data.
        $this->responsemistaken = array();

        $this->mistakemsg = null;
    }

    /**
     * Performs a mistake message creation if needed.
     */
    public function get_mistake_message() {
        if ($this->mistakemsg == null) {
            // Create a mistake message.
            $a = $this->token_description($this->node->number());
            $this->mistakemsg = get_string('absentmistakemessage', 'qtype_correctwriting', $a);
        }
        return parent::get_mistake_message();
    }

    public function mistake_key() {
        return 'absent_node_'.implode(' ', $this->answermistaken[0]);// 'absenttoken_' is better, but too long for question_attempt_step_data name column (32).
    }

}
