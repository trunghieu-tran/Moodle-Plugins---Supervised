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
require_once($CFG->dirroot.'/blocks/formal_langs/c_language_tokens.php');

class block_formal_langs_language_c_language extends block_formal_langs_predefined_language
{
    public function __construct() {
        parent::__construct(null,null);
    }
    
    
    public function name() {
        return 'c_language';
    }
        
}

function block_formal_langs_octal_to_decimal_char($matches) {
  $code = $matches[0];
  $code = octdec($code);
  return chr(intval($code));
}

function block_formal_langs_hex_to_decimal_char($matches) {
   $code = $matches[0];
   $code = hexdec($code);
   $string = '';
   if (strlen($matches[0]) == 2) {
       $string = chr(intval($code));
   }
   else {
       $string = mb_convert_encoding('&#' . intval($code) . ';', 'UTF-8', 'HTML-ENTITIES');
   }
   return $string;
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
     //$this->counter++;
     $this->errors[] = $res;
  }
  
  public function get_errors() {
     return $this->errors;
  }
  
  private function create_token($class,$value) {
        // create token object
        $classname = "block_formal_langs_c_language_" . $class;
        $res = new $classname(null, $class, $value, $this->return_pos(), $this->counter);
        // increase token count
        $this->counter++;

        return $res;
    }
  
  private function create_character($string) {
    $preprocessedstring = $this->unescapestring($string);
    return $this->create_token("character", $preprocessedstring);
  }
  
  private function create_string($string) {
    $preprocessedstring = $this->unescapestring($string);
    return $this->create_token("string", $preprocessedstring);
  }
  
  private function unescapestring($value) {
    $sourcearray = array("\\a", "\\b", "\\f", "\\n", "\\r", "\\t", "\\v","\\'","\\\"","\\\\","\\?");
    $resultarray = array("\a",  "\b",  "\f",  "\n",  "\r",  "\t",  "\v", "\'", "\"",  "\\",  "?"  );
    $preprocessedstring = str_replace($sourcearray, $resultarray, $value);
    $preprocessedstring = preg_replace_callback("/\\\\([0-7]+)/i",
                                                'block_formal_langs_octal_to_decimal_char',
                                                $preprocessedstring);
    $preprocessedstring = preg_replace_callback("/\\\\x([0-7a-fA-F]+)/i",
                                                'block_formal_langs_hex_to_decimal_char',
                                                $preprocessedstring);
    return $preprocessedstring; 
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

/\*[^\*/]+\*/                                               { return $this->create_token("multiline_comment",$this->yytext()); }
//[^\n\r]\n\r                                               { return $this->create_token("singleline_comment",$this->yytext()); }
(auto|break|case|const|continue|default|do|else|enum)       { return $this->create_token("keyword",$this->yytext()); }
(extern|for|goto|if|register|return|sizeof|static|struct)   { return $this->create_token("keyword",$this->yytext()); }
(switch|typedef|union|volatile|while)                       { return $this->create_token("keyword",$this->yytext()); }
(char|double|float|int|long|signed|unsigned|void)           { return $this->create_token("typename",$this->yytext()); }
{L}({L}|{D})*                                               { return $this->create_token("identifier",$this->yytext()); }
0[xX]{H}+{IS}?                                              { return $this->create_token("numeric",$this->yytext()); }
0{D}+{IS}?                                                  { return $this->create_token("numeric",$this->yytext()); }
{D}+{IS}?                                                   { return $this->create_token("numeric",$this->yytext()); }
"#include"[" "]*"<"[^">"]+">"                               { return $this->create_token("preprocessor",$this->yytext()); }                              
"#include"[" "]*\"[^">"]+\"                                 { return $this->create_token("preprocessor",$this->yytext()); }
"#define"                                                   { return $this->create_token("preprocessor",$this->yytext()); }
"#if"                                                       { return $this->create_token("preprocessor",$this->yytext()); }
"#ifdef"                                                    { return $this->create_token("preprocessor",$this->yytext()); }
"#elif"                                                     { return $this->create_token("preprocessor",$this->yytext()); }
"#else"                                                     { return $this->create_token("preprocessor",$this->yytext()); }
"#endif"                                                    { return $this->create_token("preprocessor",$this->yytext()); }                              
L?\'(\\\'|[^\'])\'                                          { return $this->create_character($this->yytext()); }
L?\"(\\\"|[^\"])+\"                                         { return $this->create_string($this->yytext()); }
{D}+{E}{FS}?                                                { return $this->create_token("numeric",$this->yytext()); }
{D}*"."{D}+({E})?{FS}?                                      { return $this->create_token("numeric",$this->yytext()); }
{D}+"."{D}*({E})?{FS}?                                      { return $this->create_token("numeric",$this->yytext()); }
"..."                                                       { return $this->create_token("ellipsis",$this->yytext()); }
">>="                                                       { return $this->create_token("operators",$this->yytext()); }
"<<="                                                       { return $this->create_token("operators",$this->yytext()); }
"="                                                        { return $this->create_token("operators",$this->yytext()); }
"+="                                                       { return $this->create_token("operators",$this->yytext()); }
"-="                                                       { return $this->create_token("operators",$this->yytext()); }
"*="                                                       { return $this->create_token("operators",$this->yytext()); }
"/="                                                       { return $this->create_token("operators",$this->yytext()); }
"%="                                                       { return $this->create_token("operators",$this->yytext()); }
"&="                                                       { return $this->create_token("operators",$this->yytext()); }
"^="                                                       { return $this->create_token("operators",$this->yytext()); }
"|="                                                       { return $this->create_token("operators",$this->yytext()); }
">>"                                                       { return $this->create_token("operators",$this->yytext()); }
"<<"                                                       { return $this->create_token("operators",$this->yytext()); }
"++"                                                       { return $this->create_token("operators",$this->yytext()); }
"--"                                                       { return $this->create_token("operators",$this->yytext()); }
"->"                                                       { return $this->create_token("operators",$this->yytext()); }
"&&"                                                       { return $this->create_token("operators",$this->yytext()); }
"||"                                                       { return $this->create_token("operators",$this->yytext()); }
"<="                                                       { return $this->create_token("operators",$this->yytext()); }
">="                                                       { return $this->create_token("operators",$this->yytext()); }
"=="                                                       { return $this->create_token("operators",$this->yytext()); }
"!="                                                       { return $this->create_token("operators",$this->yytext()); }
";"                                                        { return $this->create_token("semicolon",$this->yytext()); }
("{"|"<%")                                                 { return $this->create_token("brackets","{"); }
("}"|"%>")                                                 { return $this->create_token("brackets","}"); }
","                                                        { return $this->create_token("comma",$this->yytext()); }
":"                                                        { return $this->create_token("colon",$this->yytext()); }
"("                                                        { return $this->create_token("brackets",$this->yytext()); }
")"                                                        { return $this->create_token("brackets",$this->yytext()); }
("["|"<:")                                                 { return $this->create_token("brackets","["); }
("]"|":>")                                                 { return $this->create_token("brackets","]"); }
"."                                                        { return $this->create_token("operators",$this->yytext()); }
"&"                                                        { return $this->create_token("operators",$this->yytext()); }
"|"                                                        { return $this->create_token("operators",$this->yytext()); }
"^"                                                        { return $this->create_token("operators",$this->yytext()); }
"!"                                                        { return $this->create_token("operators",$this->yytext()); }
"~"                                                        { return $this->create_token("operators",$this->yytext()); }
"-"|"+"|"*"|"/"|"%"|">"|"<"                                { return $this->create_token("operators",$this->yytext()); }
"?"                                                        { return $this->create_token("question_mark",$this->yytext()); }
[ \t\v\n\f]                                                {  } 
.                                                          { $this->create_error($this->yytext()); return $this->create_token("unknown",$this->yytext());}