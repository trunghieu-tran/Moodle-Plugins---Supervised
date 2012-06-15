<?php 
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

class block_formal_langs_language_c_language extends block_formal_langs_predefined_language
{
    public function __construct() {
        parent::__construct(null,null);
    }
    
    
    public function name() {
        return 'c_language';
    }
        
}
// This wrapper is created because there is no way, we can create other lexer without stream
// And current architecture won't allow to do so, because mostly we need string.
class block_formal_langs_predefined_c_language_lexer {
  
  public function tokenize($processedstring) {
        $lexer = new block_formal_langs_predefined_c_language_lexer_raw(fopen('data://text/plain;base64,' . base64_encode($processedstring->string), 'r'));
        //Now, we are splitting text into lexemes
        $tokens = array();
        while ($token = $lexer->next_token()) {
            $tokens[] = $token;
        }
        $stream = new block_formal_langs_token_stream();
        $stream->tokens = $tokens;
        $stream->errors = $lexer->get_errors();
        $processedstring->stream = $stream;
  }

}

%%

%function next_token
%char
%line
%full
%class block_formal_langs_predefined_c_language_lexer_raw


%{
  
  // @var int number of  current parsed lexeme.
  private  $counter = 0;
  private  $errors  = array();
  
  private function create_error($symbol) {
     $res = new block_formal_langs_lexical_error();
     $res->token_index = $this->counter;
     $a = new stdClass();
     $a->line = $this->yyline;
     $a->position = $this->yycol;
     $a->symbol = $symbol;
     $res->errormessage = get_string('lexical_error_message','block_formal_langs',$a);
     $this->counter++;
     $this->errors[] = $res;
  }
  
  public function get_errors() {
     return $this->errors;
  }
  
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

D = [0-9]
L = [a-zA-Z_]
H = [a-fA-F0-9]
E = [Ee][+-]?[0-9]+
FS = (f|F|l|L)
IS = (u|U|l|L)

%%

/\*[^\*/]+\*/                                               { return $this->create_token("comment",$this->yytext()); }
//[^\n\r]\n\r                                               { return $this->create_token("comment",$this->yytext()); }
(auto|break|case|const|continue|default|do|else|enum)       { return $this->create_token("keyword",$this->yytext()); }
(extern|for|goto|if|register|return|sizeof|static|struct)   { return $this->create_token("keyword",$this->yytext()); }
(switch|typedef|union|volatile|while)                       { return $this->create_token("keyword",$this->yytext()); }
(char|double|float|int|long|signed|unsigned|void)           { return $this->create_token("typename",$this->yytext()); }
{L}({L}|{D})*                                               { return $this->create_token("identifier",$this->yytext()); }
0[xX]{H}+{IS}?                                              { return $this->create_token("constant",$this->yytext()); }
0{D}+{IS}?                                                  { return $this->create_token("constant",$this->yytext()); }
{D}+{IS}?                                                   { return $this->create_token("constant",$this->yytext()); }
L?\'(\\\'|[^\'])\'                                          { return $this->create_token("character",$this->yytext()); }
L?\"(\\\"|[^\"])+\"                                         { return $this->create_token("string",$this->yytext()); }
{D}+{E}{FS}?                                                { return $this->create_token("constant",$this->yytext()); }
{D}*"."{D}+({E})?{FS}?                                      { return $this->create_token("constant",$this->yytext()); }
{D}+"."{D}*({E})?{FS}?                                      { return $this->create_token("constant",$this->yytext()); }
"..."                                                       { return $this->create_token("ellipsis",$this->yytext()); }
">>="                                                       { return $this->create_token("right_assign",$this->yytext()); }
"<<="                                                       { return $this->create_token("left_assign",$this->yytext()); }
"="                                                        { return $this->create_token("assign",$this->yytext()); }
"+="                                                       { return $this->create_token("add_assign",$this->yytext()); }
"-="                                                       { return $this->create_token("sub_assign",$this->yytext()); }
"*="                                                       { return $this->create_token("mul_assign",$this->yytext()); }
"/="                                                       { return $this->create_token("div_assign",$this->yytext()); }
"%="                                                       { return $this->create_token("mod_assign",$this->yytext()); }
"&="                                                       { return $this->create_token("and_assign",$this->yytext()); }
"^="                                                       { return $this->create_token("xor_assign",$this->yytext()); }
"|="                                                       { return $this->create_token("or_assign",$this->yytext()); }
">>"                                                       { return $this->create_token("right_shift",$this->yytext()); }
"<<"                                                       { return $this->create_token("left_shift",$this->yytext()); }
"++"                                                       { return $this->create_token("increment",$this->yytext()); }
"--"                                                       { return $this->create_token("decrement",$this->yytext()); }
"->"                                                       { return $this->create_token("ptr_field_access",$this->yytext()); }
"&&"                                                       { return $this->create_token("and",$this->yytext()); }
"||"                                                       { return $this->create_token("or",$this->yytext()); }
"<="                                                       { return $this->create_token("le",$this->yytext()); }
">="                                                       { return $this->create_token("ge",$this->yytext()); }
"=="                                                       { return $this->create_token("eq",$this->yytext()); }
"!="                                                       { return $this->create_token("neq",$this->yytext()); }
";"                                                        { return $this->create_token("semicolon",$this->yytext()); }
("{"|"<%")                                                 { return $this->create_token("lfbrace","{"); }
("}"|"%>")                                                 { return $this->create_token("rfbrace","}"); }
","                                                        { return $this->create_token("comma",$this->yytext()); }
":"                                                        { return $this->create_token("colon",$this->yytext()); }
"("                                                        { return $this->create_token("lbrace",$this->yytext()); }
")"                                                        { return $this->create_token("rbrace",$this->yytext()); }
("["|"<:")                                                 { return $this->create_token("lsbrace","["); }
("]"|":>")                                                 { return $this->create_token("rsbrace","]"); }
"."                                                        { return $this->create_token("dot",$this->yytext()); }
"&"                                                        { return $this->create_token("binand",$this->yytext()); }
"|"                                                        { return $this->create_token("binor",$this->yytext()); }
"^"                                                        { return $this->create_token("binxor",$this->yytext()); }
"!"                                                        { return $this->create_token("not",$this->yytext()); }
"~"                                                        { return $this->create_token("binnot",$this->yytext()); }
"-"|"+"|"*"|"/"|"%"                                        { return $this->create_token("mathops",$this->yytext()); }
"?"                                                        { return $this->create_token("question",$this->yytext()); }
[ \t\v\n\f]                                                {  } 
.                                                          { $this->create_error($this->yytext()); return $this->create_token("unknown",$this->yytext());}