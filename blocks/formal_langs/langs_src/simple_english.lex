<? 
/**
 * Defines a simple  english language lexer for correctwriting question type.
 *
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Sergey Pashaev Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_base.php');
require_once($CFG->dirroot.'/blocks/formal_langs/jlex.php');

class block_formal_langs_language_simple_english extends block_formal_langs_predefined_language
{
    public function __construct() {
        parent::__construct(null,null);
    }
    
    
    public function name() {
        return 'simple_english';
    }
        
}
// This wrapper is created because there is no way, we can create other lexer without stream
// And current architecture won't allow to do so, because mostly we need string.
class block_formal_langs_predefined_simple_english_lexer {
  
  public function tokenize($processedstring) {
        $lexer = new block_formal_langs_predefined_simple_english_lexer_raw(fopen('data://text/plain;base64,' . base64_encode($processedstring->string), 'r'));
        //Now, we are splitting text into lexemes
        $tokens = array();
        while ($token = $lexer->next_token()) {
            $tokens[] = $token;
        }
        $stream = new block_formal_langs_token_stream();
        $stream->tokens = $tokens;
        $processedstring->stream = $stream;
  }

}

%%

%function next_token
%char
%line
%full
%class block_formal_langs_predefined_simple_english_lexer_raw


%{
  
  // @var int number of  current parsed lexeme.
  private  $counter = 0;
  
  private function create_token($type, $value) {
        // create token object
        $res = new block_formal_langs_token_base(null, $type, $value, $this->return_pos(), $this->counter);
        // increase token count
        $this->counter++;

        return $res;
    }
  
  private function return_pos() {
        $begin_line = $this->yyline;
        $begin_col = $this->yycol;

        if(strpos($this->yytext(), '\n')) {
            $lines = explode("\n", $this->yytext());
            $num_lines = count($lines);
            
            $end_line = $begin_line + $num_lines - 1;
            $end_col = strlen($lines[$num_lines -1]);
        } else {
            $end_line = $begin_line;
            $end_col = $begin_col + strlen($this->yytext());
        }
        
        $res = new block_formal_langs_node_position($begin_line, $end_line, $begin_col, $end_col);
        
        return $res;
    }
%}

%%

[a-zA-Z]+                                               { return $this->create_token("word",$this->yytext()); }
[0-9]+                                                  { return $this->create_token("number",$this->yytext()); } 
[ \t]                                                   {  }
["."","";"":"\!\?]                                      { return $this->create_token("sign",$this->yytext()); } 
[\+-\*/]                                                { return $this->create_token("mathsigns",$this->yytext()); } 
[^a-zA-Z0-9 \t\.,;:\!\?\+-\*/]                          { return $this->create_token("other",$this->yytext());}  
