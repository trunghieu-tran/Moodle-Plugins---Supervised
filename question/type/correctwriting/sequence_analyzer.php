<?php
/**
 * Defines class of sequence analyzer for correct writing question.
 *
 * Sequence analyzer object is created for each possible set of lexical mistakes and 
 * is responsible for finding common parts of answer regarding sequence of tokens.
 * Longest common sequence algorithm is used to determine it.
 *
 * Sequence analyzers create and use syntax analyzers to determine structural mistakes using
 * language grammar. When using grammar analyzer is impossible, it determines sequence mistakes 
 * using lcs, i.e. misplaced, extra and missing tokens.
 * There may be more than one syntax analyzer created if there are several LCS'es of 
 * answer and response.
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

//Other necessary requires
require_once($CFG->dirroot.'/question/type/correctwriting/syntax_analyzer.php');
require_once($CFG->dirroot.'/question/type/correctwriting/sequence_analyzer/get_lcs.php');
require_once($CFG->dirroot.'/question/type/correctwriting/sequence_analyzer/lcs_to_mistakes.php');
require_once($CFG->dirroot.'/question/type/correctwriting/sequence_mistakes.php');



class  qtype_correctwriting_sequence_analyzer {

    protected $language;//Language object - contains scaner, parser etc
    protected $errors;//Array of error objects - teacher errors when entering answer

    protected $answer;//Array of answer tokens
    protected $correctedresponse;//Array of response tokens where lexical errors are corrected
    protected $mistakes;//Array of mistake objects - student errors (structural errors)

    private   $fitness;            //Fitness for response
    private   $temporary_fitness;  //A temporary fitness for computation
    
    private   $question; //Used question by analyzer
    
    private   $moved_mistake_weight;   //Moved lexeme error weight
    private   $removed_mistake_weight; //Removed lexeme error weight
    private   $added_mistake_weight;   //Added lexeme error weight
    
    /**
     * Do all processing and fill all member variables
     * Passed response could be null, than object used just to find errors in the answers, token count etc...
     */
    public function __construct($question, $answer, $language, $correctedresponse=null) {
        $this->answer=$answer;
        $this->correctedresponse=$correctedresponse;
        //If question is set null we suppose this is a unit-test mode and don't do stuff
        if ($question!=null) {
            $this->language=$language;
            $this->question=$question;
            if ($corrected_response==null) {
                //Scan errors by syntax_analyzer
                try {
                    $analyzer=new qtype_correctwriting_syntax_analyzer($answer,$language,null,null);
                    $this->errors=$analyzer->errors();
                } catch (Exception $e) {
                    //Currently do nothing. TODO: What to do in that case?
                }
                
            } else {
                //Scan for errors, computing lcs
                $this->scan_response_errors();
            }
        }
        //TODO:
        //1. Compute LCS - Mamontov
        //  - lcs function  (done)
        //2. For each LCS create  qtype_correctwriting_syntax_analyzer object - Mamontov (done)
        //  - if there is exception thrown, skip syntax analysis
        //3. Select best fitted syntax_analyzer using their fitness method - Mamontov
        //4. Set array of mistakes accordingly - Mamontov (done)
        //  - if syntax analyzer is able to return mistakes, use it's mistakes
        //  - otherwise generate own mistakes for individual tokens, using lcs_to_mistakes function
        //NOTE: if response is null just check for errors using syntax analyzer- Mamontov (Done)
        //NOTE: if some stage create errors, stop processing right there (done?)
    }
    /**
     * Scans for an errors in response, computing lcs and 
     * performing syntax analysis
     */
    private function scan_response_errors() {
        //TODO: Extract these from question
        $this->moved_mistake_weight=1;
        $this->removed_mistake_weight=1;
        $this->added_mistake_weight=1;
        
        $lcs=$this->lcs();
        if (count($lcs)==0) {
            //If no LCS found perform only one found in lcs
            $this->mistakes=$this->lcs_to_mistakes(null);
            $this->fitness=$this->temporary_fitness;
        }
        else {
            //Otherwise scan all of lcs
            $max_mistake_array=array();
            $max_fitness=0;
            $is_first=true;
            
            //Find fitting analyzer
            for($i=0;$i<count($lcs);$i++) {
                //Compute fitness and array
                $cur_mistake_array=$this->lcs_to_mistakes($lcs[$i]);
                $cur_fitness=$this->temporary_fitness;
                if ($is_first==true || $cur_fitness>$max_fitness) {
                    //Set according value
                    $is_first=false;
                    $max_mistake_array=$cur_mistake_array;
                    $max_fitness=$cur_fitness;
                }
            }
            
            //Set self-properties to return proper values
            $this->mistakes=$max_mistake_array;
            $this->fitness=$max_fitness;
        }
    }
    /**
     * Compute and return longest common subsequence (tokenwise) of answer and corrected response.
     *
     * Array of individual lcs contains answer indexes as keys and response indexes as values.
     * There may be more than one lcs for a given pair of strings.
     * @return array array of individual lcs arrays
     */
    public function lcs() {
        return qtype_correctwriting_sequence_analyzer_compute_lcs($this->answer,$this->correctedresponse);
    }
    
    private function create_moved_mistake($answerindex,$responseindex) {
        return new qtype_correctwriting_lexeme_moved_mistake($this->language,$this->answer,$answerindex,
                                                             $this->correctedresponse,$responseindex);
    }
    
    private function create_added_mistake($responseindex) {
        return new qtype_correctwriting_lexeme_added_mistake($this->language,$this->answer,
                                                             $this->correctedresponse,$responseindex);
    }
    
    private function create_skipped_mistake($answerindex) {
        return new qtype_correctwriting_lexeme_skipped_mistake($this->language,$this->answer,$answerindex,
                                                               $this->correctedresponse);
    }
    /**
     * Returns an array of mistake objects for given individual lcs array,using syntax_analyzer if needed
     */
    public function lcs_to_mistakes($lcs) {
        //Create an analyzer if can. If can use it's errors, otherwise generate own
        try {
            $analyzer=new qtype_correctwriting_syntax_analyzer($this->answer,$this->language,
                                                               $this->correctedresponse,
                                                               $lcs);
            $temporary_fitness=$analyzer->fitness();
            return $analyzer->mistakes();
        } catch (Exception $e) {
            //If exception is thrown we should create own errors
            $errors=qtype_correctwriting_sequence_analyzer_determine_mistakes($this->answer,
                                                                              $this->response,
                                                                              $lcs);
            //Compute fitness-function
            $temporary_fitness=$this->moved_mistake_weight*count($errors['moved'])
                              +$this->removed_mistake_weight*count($errors['removed'])
                              +$this->added_mistake_weight*count($errors['added']);
            $temporary_fitness=$temporary_fitness*-1;

            //Creates an array of mistake objects
            $result = array();
            

            //Produce errors, when tokens are moved from their places
            foreach($result['moved'] as $answerindex => $responseindex) {
                $result[] = $this->create_moved_mistake($answerindex,$responseindex);
            }
            
            //Produce errors, when tokens are removed from their places
            foreach($result['removed'] as $answerindex) {
                $result[] = $this->create_skipped_mistake($answerindex);
            }
            
            //Produce errors, when an odd tokens are added
            foreach($result['added'] as $responseindex) {
                $result[] = $this->create_added_mistake($responseindex);
            }
            
            return $result;
        }
        
        return null;
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

    public function is_errors() {
        return !empty($this->errors);
    }

    public function errors() {
        return $this->errors;
    }

    //Other necessary access methods
}
?>