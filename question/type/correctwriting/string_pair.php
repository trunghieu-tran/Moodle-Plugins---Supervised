<?
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
 * Correct writing question definition class.
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/formal_langs/tokens_base.php');

// A string pair with lcs, used in sequence analyzer
class qtype_correctwriting_string_pair extends block_formal_langs_string_pair {
    /**
     * LCS for sequence analyzer
     * @var array
     */
    protected $lcs = array();
    /**
     * Set to true, when two answers are equal
     *
     * If two answers are equal, we should not care about results - because a student response is
     * correct
     *
     * @var bool
     */
    protected $aretokensequencesequal = false;

    /**
     * A mistake set for arrays
     *
     * @var array
     */
    protected $mistakes = array();


    public function __clone() {
        parent::__clone();
        $oldmistakes = $this->mistakes;
        if (is_array($oldmistakes)) {
            foreach($oldmistakes as $type => $mistakes) {
                $this->mistakes[$type] = array();
                if (count($mistakes)) {
                    foreach($mistakes as $mistake) {
                        $this->mistakes[$type][] = $mistake;
                    }
                }
            }
        }
    }

    public function are_strings_equal() {
        return $this->aretokensequencesequal;
    }

    public function assert_that_strings_are_equal() {
        $this->aretokensequencesequal = true;
    }

    public function mistakes() {
        $result = array();
        foreach($this->mistakes as $type => $mistakes) {
            if (count($result) == 0) {
                $result = $mistakes;
            } else {
                $result = array_merge($result, $mistakes);
            }
        }
        return $result;
    }

    public function set_mistakes($mistakes) {
        $this->mistakes = array();
        $this->append_mistakes($mistakes);
    }


    public function append_mistake($mistake) {
        $type = get_class($mistake);
        if (array_key_exists($type, $this->mistakes) == false) {
            $this->mistakes[$type] = array();
        }
        $this->mistakes[$type][] = $mistake;
    }

    public function append_mistakes($mistakes) {
        if (count($mistakes) != 0) {
            foreach($mistakes as $mistake) {
                $this->append_mistake($mistake);
            }
        }
    }

    public function mistakes_by_type($type) {
        $totaltype = 'qtype_correctwriting_' . $type;
        $result = array();
        if (array_key_exists($totaltype, $this->mistakes)) {
            $result = $this->mistakes[$totaltype];
        }
        return $result;
    }

    public function set_mistakes_by_type($type, $set) {
        $totaltype = 'qtype_correctwriting_' . $type;
        $this->mistakes[$totaltype] = $set;
    }

    /**
     * Array of real indexes for correct answer in table.
     * @var array
     */
    protected $indexesintable;

    /**
     * Creates a new string as a copy of this with a lcs
     * @param array $lcs LCS
     * @return qtype_correctwriting_string_pair
     */
    public function copy_with_lcs($lcs) {
        $pair = clone $this;
        $pair->lcs = $lcs;
        return $pair;
    }

    /**
     * Returns an LCS for tokens
     * @return array
     */
    public function lcs() {
        return $this->lcs;
    }    

    /**
     * Return object of class
     */
   public function __construct($correct, $compared, $matches) {
        block_formal_langs_string_pair::__construct($correct, $compared, $matches);
        $this->indexesintable = array();
        foreach($this->correctstring()->stream->tokens as $token) {
            $this->indexesintable[$token->token_index()] = $token->token_index();
        }
    }

    /**
    * Set indexes in table  array for correctstring
    * @param array - array of indexes
    */
    public function set_indexes_in_table($newindexes) {
        $this->indexesintable = $newindexes;
    }

    /**
    * Create complete copy of current pair without common references
    * @return object of qtype_correctwriting_string_pair $pair copy of current pair 
    */
    public function __clone() {
        // Clone answers.
        $this->correctstring = clone($this->correctstring());
        $this->correctedstring = clone($this->correctedstring());
        foreach($this->correctstring()->stream->tokens as $token) {
            $this->indexesintable[$token->token_index()] = $token->token_index();
        }
    }
}

