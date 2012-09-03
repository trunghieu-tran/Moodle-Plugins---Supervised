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
require_once($CFG->dirroot.'/question/type/correctwriting/sequence_analyzer.php');
require_once($CFG->dirroot.'/question/type/correctwriting/lexical_mistakes.php');
require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');
require_once($CFG->dirroot.'/blocks/formal_langs/block_formal_langs.php');
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

    protected $fitness;//Fitness, used to choose appropriate analyzer
    protected $correctedresponse; // Generated response stream (because we need to build image)
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
        
        $language = $question->get_used_language();
        $responsestring = $language->create_from_string($responsestr); 
        $answerstring = $language->create_from_db('question_answers', $answer->id, $answer->answer);
        // We assume that, lexical analyzer must fix all the lexical mistakes and find them at all
        // Because, why not? He must fix a lexical mistakes, but if can't fix stuff, he can put some mistakes.
        // If question starts working with lexical errors, what should it do? Which mistakes should it take - 
        // lexical_analyzer's or own? How he manages to split lexical_analyzer's mistakes from sequence_analyzer's
        // Do we need another loop with is_a?
        $mistakes = array();
        if (count($responsestring->stream->tokens) != 0) {
            foreach($responsestring->stream->tokens as $index => $token) {
                if (is_a($token, "block_formal_langs_c_token_character")) {
                    $value = $token->value();
                    $len = strlen($token->value());
                    if ($value[0] == 'L') {
                        $len = $len - 1;
                    }
                    if ($len > 3) {
                       $mistakes[] = new qtype_correctwriting_c_language_multicharacter_literal($question, $responsestring, $token);
                    }
                }
                if (is_a($token,"block_formal_langs_c_token_unknown")) {
                    $value = $token->value();
                    if ($value == '"') {
                        $mistakes[] = new qtype_correctwriting_c_language_unmatched_quote_mistake($question, $responsestring, $token);
                    } elseif ($value == "\'") {
                        $mistakes[] = new qtype_correctwriting_c_language_unmatched_single_quote_mistake($question, $responsestring, $token);
                    } else {
                        $mistakes[] = new qtype_correctwriting_c_language_unknown_symbol_mistake($question, $responsestring, $token);
                    }
                }
            }
        }

        
        $analyzer = new qtype_correctwriting_sequence_analyzer($question, $answerstring, $language, $responsestring);
        $this->correctedresponse= $responsestring->stream->tokens;
        $this->mistakes = array_merge($mistakes, $analyzer->mistakes());
        $this->fitness = $analyzer->fitness();
        //TODO:
        //0. Create language object
        //1. Scan answer and response - Pashaev
        //  - call language object to do it
        //2. Check for full match - stop processing if answer and response arrays are equal - Pashaev
        //3. Look for matched pairs group using block_formal_langs_token_stream::look_for_token_pairs - Birukova
        //4. Create corrected response using block_formal_langs_token_stream::correct_mistakes - Birukova
        //5. Create qtype_correctwriting_sequence_analyzer for each group of pairs, passing corrected array of tokens - Birukova or Pashaev
        //6. Select best fitted sequence analyzer using their fitness method - Birukova or Pashaev
        //7. Set array of mistakes accordingly - Birukova
        //  - matches_to_mistakes function  + merging mistakes from sequence analyzer
        //NOTE: if responsestr is null just check for errors - Pashaev
        //NOTE: if some stage create errors, stop processing right there
        //NOTE: throw exception (c.f. moodle_exception and preg_exception) if there are errors when responsestr!==null - e.g. during real analysis
    }

    public function get_corrected_response() {
        return $this->correctedresponse;
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
        return $this->fitness;
    }

    public function mistakes() {
        return $this->mistakes;
    }

    public function has_errors() {
        return !empty($this->errors);
    }

    public function errors() {
        return $this->errors;
    }

    //Other necessary access methods
}

?>