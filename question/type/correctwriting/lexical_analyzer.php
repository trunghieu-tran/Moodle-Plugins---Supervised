<?php
/**
 * Defines class of lexical analyzer for correct writing question.
 *
 * Lexical analyzer object is created for each correct answer and 
 * is responsible for tokenizing, looking for lexical mistakes (typos,
 * missing and extra separators etc) and other mistakes involving individual tokens,
 * merging resulting array of mistakes from all analyzers and determine 
 * answer fitness for the response.
 *
 * Lexical analyzers create and use sequence analyzers to determine structural mistakes.
 * There may be more than one sequence analyzer created if there are several equal groups of 
 * lexical mistakes possible.
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Sergey Pashaev, Birukova Maria, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/correctwriting/question.php');
require_once($CFG->dirroot.'/blocks/formal_langs/base_token.php');
//Other necessary requires

class qtype_correctwriting_lexical_analyzer {
    protected $answerobj;//Correct answer as an object (answer, fraction, feedback, names for tokens or sematic nodes)
    protected $answerstr;//Correct answer as a string
    protected $responsestr;//Student's response as a string
    protected $question;//A reference to the question object with necessary data (language id, answers, threshold etc)
    protected $language;//Language object - contains scaner, parser etc
    protected $errors;//Array of error objects - teacher errors when entering answer

    protected $answer;//Array of answer tokens
    protected $response;//Array of response tokens
    protected $mistakes;//Array of mistake objects - student errors (merged from all stages)

    /**
     * Do all processing and fill all member variables
     *
     * Passed responsestring could be null, than object used just to find errors in the answers, token count etc...
     *
     * @param object $question question object
     * @param object $answer answer object for which lcs is created
     * @param string $responsestr student response as a string
     */
    public function __construct($question, $answer, $responsestr=null) {

        $this->answerobj = $answer;
        $this->answerstr = $answer->answer;
        $this->responsestr = $responsestr;
        $this->question = $question;
        
        //TODO:
        //0. Create language object
        //1. Scan answer and response - Pashaev
        //  - call language object to do it
        //2. Check for full match - stop processing if answer and response arrays are equal - Pashaev
        //3. Find matched pairs (typos, typical errors etc) - Birukova
        //  - look_for_matches function
        //4. Find best groups of pairs - Birukova
        //  - group_matches function, with criteria defined by compare_matches_groups function
        //5. Create qtype_correctwriting_sequence_analyzer for each group of pairs, passing corrected array of tokens - Birukova or Pashaev
        //6. Select best fitted sequence analyzer using their fitness method - Birukova or Pashaev
        //7. Set array of mistakes accordingly - Birukova
        //  - matches_to_mistakes function  + merging mistakes from sequence analyzer
        //NOTE: if responsestr is null just check for errors - Pashaev
        //NOTE: if some stage create errors, stop processing right there
        //NOTE: throw exception (c.f. moodle_exception and preg_exception) if there are errors when responsestr!==null - e.g. during real analysis
    }

    /**
     * Creates an array of all possible matched pairs.
     *
     * Uses token's look_for_matches function and fill necessary fields in matched_tokens_pair objects.
     *
     * @param double $threshold threshold as a fraction of token length for creating pairs
     * @return array array of matched_tokens_pair objects representing all possible pairs within threshold
     */
    public function look_for_matches($threshold) {
    }

    /**
     * Generates array of best groups of matches representing possible set of student's mistakes.
     *
     * Use recursive backtracking.
     * No token from answer or response could appear twice in any group, otherwise groups are
     * compared using compare_matches_groups function
     *
     * @param array $matches array of matched_tokens_pair objects representing all possible pairs within threshold
     * @return array array of matches_group objects
     */
    public function group_matches($matches) {
    }

    /**
     * Compares two matches groups.
     *
     * Basic strategy is to have as much tokens in both answer and response covered,
     * if the number of tokens covered are equal, than choose group with less summ of mistake weights.
     *
     * @return number <0 if $group1 worse than $group2; 0 if $group1==$group2; >0 if $group1 better than $group2
     */
    public function compare_matches_groups($group1, $group2) {
    }

    /**
     * Returns an array of mistakes objects for given matches_group object
     */
    public function matches_to_mistakes($group) {
    }

    /**
    * Returns fitness as aggregate measure of how students response fits this particular answer - i.e. more fitness = less mistakes
    * Used to choose best matched answer
    * Fitness is negative or zero (no errors, full match)
    * Fitness doesn't necessary equivalent to the number of mistakes as each mistake could have different weight
    */
    public function fitness() {
    }

    public function mistakes() {
        return $this->mistakes;
    }

    public function is_errors() {
        return !empty($this->errors);
    }

    public function errors() {
        return $this->errors;
    }

    //Other necessary access methods
}

/**
 * Represents possible set of student's lexical mistakes
 */
class qtype_correctwriting_matches_group {
    /**
     * Array of matched pairs
     */
    public $matchedpairs;

    //Sum of mistake weight
    public $mistakeweight;

    //Sorted array of all answer token indexes for tokens, covered by pairs from this group
    public $answercoverage;

    //Sorted array of all response token indexes for tokens, covered by pairs from this group
    public $responsecoverage;
}

?>