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
require_once($CFG->dirroot.'/question/type/poasquestion/jlex.php');
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
    } else {
        $string = mb_convert_encoding('&#' . intval($code) . ';', 'UTF-8', 'HTML-ENTITIES');
    }
    return $string;
}

%%

%unicode
%function next_token
%char
%line
%full
%class block_formal_langs_predefined_c_language_lexer_raw
%state CHARACTER
%state STRING
%state MULTILINE_COMMENT
%state SINGLELINE_COMMENT

%{
  
    // @var int number of  current parsed lexeme.
    private  $counter = 0;
    private  $errors  = array();
    // @var qtype_poasquestion_string  temporary string for buffer
    protected $statestring = null;
    // @var int line yyline for token
    protected $stateyyline = 0;
    // @var int column yycol for token
    protected $stateyycol = 0;
    // @var int end yyline
    protected $endyyline = 0;
    // @var int end yycolumn
    protected $endyycol  = 0;

    private function startbuffer() {
        $this->stateyyline = $this->yyline;
        $this->stateyycol = $this->yycol;
        $this->statestring = new qtype_poasquestion_string();
    }
    // Appends a symbol string to a buffer
    private function append($sym) {
        $this->advance_buffered_pos();
        $this->statestring->concatenate($sym);
    }
    // Returns buffer
    private function  buffer() {
        $result = $this->statestring;
        $this->statestring = null;
        return $result;
    }

    private function create_error($symbol) {
        $res = new block_formal_langs_lexical_error();
        $res->tokenindex = $this->counter;
        $a = new stdClass();
        $a->line = $this->yyline;
        $a->position = $this->yycol;


        $a->symbol = $symbol->string();
        $errormessage = 'clanguageunknownsymbol';
        if (mb_strlen($symbol) == 1) {
            if ($symbol[0] == '\'') {
                $errormessage = 'clanguageunmatchedsquote';
            }
            if ($symbol[0] == '"') {
                $errormessage = 'clanguageunmatchedquote';
            }
        }
        $res->errormessage = get_string($errormessage,'block_formal_langs',$a);
        $this->errors[] = $res;
    }

    private function create_buffer_error($symbol) {
        $res = new block_formal_langs_lexical_error();
        $res->tokenindex = $this->counter;
        $a = new stdClass();
        $a->line = $this->stateyyline;
        $a->position = $this->stateyycol;
        $a->symbol = $symbol->string();

        $res->errormessage = get_string('lexical_error_message','block_formal_langs',$a);
        $this->errors[] = $res;
    }

    public function get_errors() {
        return $this->errors;
    }
  
    private function create_token($class,$value) {
        // create token object
        $classname = 'block_formal_langs_c_token_' . $class;
        $res = new $classname(null, $class, $value, $this->return_pos(), $this->counter);
        // increase token count
        $this->counter++;

        return $res;
    }

    private function create_buffered_token($class,$value) {
            // create token object
            $classname = 'block_formal_langs_c_token_' . $class;
            $res = new $classname(null, $class, $value, $this->return_buffered_pos(), $this->counter);
            // increase token count
            $this->counter++;

            return $res;
    }

    private function is_white_space($string) {
        // Here we need to escape symbols, so double quotes are inavoidable
        $whitespace = array(' ', "\t", "\n", "\r", "f", "\v");
        return in_array($string[0], $whitespace);
    }
    // Enters state with buffered output
    private function enterbufferedstate($state) {
        $this->startbuffer();
        $this->append($this->yytext());
        $this->yybegin($state);
    }
    // Leaves state with buffered output
    private function leavebufferedstate($tokentype) {
        $this->append($this->yytext());
        $this->yybegin(self::YYINITIAL);
        return $this->create_buffered_token($tokentype,$this->buffer());
    }

    protected function check_and_create_character()
    {
        $result = $this->leavebufferedstate('character');
        $maxcharacterlength = 3;
        $value = $result->value();
        if ($value[0] == 'L')
            $maxcharacterlength = $maxcharacterlength + 1;
        if ( mb_strlen($value) > $maxcharacterlength) {
            $res = new block_formal_langs_lexical_error();
            $res->tokenindex = $this->counter - 1;
            $a = new stdClass();
            $a->line = $result->position()->linestart();
            $a->col = $result->position()->colstart();
            $a->symbol = $value;
            $res->errormessage = get_string('clanguagemulticharliteral','block_formal_langs',$a);
            $this->errors[] = $res;
        }
        return $result;
    }
  
    private function return_pos() {
        $begin_line = $this->yyline;
        $begin_col = $this->yycol;

        if(strpos($this->yytext(), '\n')) {
            $lines = explode("\n", $this->yytext());
            $num_lines = count($lines);
            
            $end_line = $begin_line + $num_lines - 1;
            $end_col = strlen($lines[$num_lines -1]) - 1;
        } else {
            $end_line = $begin_line;
            $end_col = $begin_col + strlen($this->yytext()) - 1;
        }
        
        $res = new block_formal_langs_node_position($begin_line, $end_line, $begin_col, $end_col);
        
        return $res;
    }

    private function return_buffered_pos() {
        $begin_line = $this->stateyyline;
        $begin_col = $this->stateyycol;
        $end_line =  $this->endyyline;
        $end_col =  $this->endyycol;

        $res = new block_formal_langs_node_position($begin_line, $end_line, $begin_col, $end_col);

        return $res;
    }

    private function advance_buffered_pos() {
        if(strpos($this->yytext(), '\n')) {
            $lines = explode("\n", $this->yytext());
            $num_lines = count($lines);

            $this->endyyline = $this->endyyline + $num_lines - 1;
            $this->endyycol = strlen($lines[$num_lines -1]) - 1;
        } else {
            $this->endyycol = $this->endyycol + strlen($this->yytext()) - 1;
        }
    }

%}

%eofval{
    if ($this->yy_lexical_state == self::SINGLELINE_COMMENT) {
        $this->yybegin(self::YYINITIAL);
        return $this->create_token('singleline_comment', $this->buffer());
    } else if ($this->yy_lexical_state == self::MULTILINE_COMMENT)  {
        $this->yybegin(self::YYINITIAL);
        $this->create_buffer_error($this->statestring);
        return $this->create_token('unknown', $this->buffer());
    } else if ($this->yy_lexical_state == self::STRING)  {
        $this->yybegin(self::YYINITIAL);
        $this->create_buffer_error($this->statestring);
        return $this->create_token('unknown', $this->statestring);
    } else if ($this->yy_lexical_state == self::CHARACTER)  {
        $this->yybegin(self::YYINITIAL);
        $this->create_buffer_error($this->statestring);
        return $this->create_token('unknown', $this->buffer());
    } else {
        return null;
    }
%eofval}

D = [0-9]
L = [a-zA-Z_]
H = [a-fA-F0-9]
E = [Ee][+-]?[0-9]+
FS = (f|F|l|L)
IS = (u|U|l|L)
INC = "#include"
%%

<YYINITIAL> //                           { $this->enterbufferedstate(self::SINGLELINE_COMMENT); break; }
<YYINITIAL> (auto|break|const|continue)  { return $this->create_token('keyword',$this->yytext()); }
<YYINITIAL> (case|default|do|else|enum)  { return $this->create_token('keyword',$this->yytext()); }
<YYINITIAL> (extern|for|goto|if)         { return $this->create_token('keyword',$this->yytext()); }
<YYINITIAL> (return|sizeof|static)       { return $this->create_token('keyword',$this->yytext()); }
<YYINITIAL> (struct|register)            { return $this->create_token('keyword',$this->yytext()); }
<YYINITIAL> (switch|typedef)             { return $this->create_token('keyword',$this->yytext()); }
<YYINITIAL> (union|volatile|while)       { return $this->create_token('keyword',$this->yytext()); }
<YYINITIAL> (char|double|float|int)      { return $this->create_token('typename',$this->yytext()); }
<YYINITIAL> (long|signed|unsigned|void)  { return $this->create_token('typename',$this->yytext()); }
<YYINITIAL> {L}({L}|{D})*                { return $this->create_token('identifier',$this->yytext()); }
<YYINITIAL> 0[xX]{H}+{IS}?               { return $this->create_token('numeric',$this->yytext()); }
<YYINITIAL> 0{D}+{IS}?                   { return $this->create_token('numeric',$this->yytext()); }
<YYINITIAL> {D}+{IS}?                    { return $this->create_token('numeric',$this->yytext()); }
<YYINITIAL> {INC}[" "]*"<"[^">"]+">"     { return $this->create_token('preprocessor',$this->yytext()); }                              
<YYINITIAL> {INC}[" "]*\"[^">"]+\"       { return $this->create_token('preprocessor',$this->yytext()); }
<YYINITIAL> "#"                          { return $this->create_token('preprocessor',$this->yytext()); }
<YYINITIAL> "##"                         { return $this->create_token('preprocessor',$this->yytext()); }
<YYINITIAL> "#define"                    { return $this->create_token('preprocessor',$this->yytext()); }
<YYINITIAL> "#if"                        { return $this->create_token('preprocessor',$this->yytext()); }
<YYINITIAL> "#ifdef"                     { return $this->create_token('preprocessor',$this->yytext()); }
<YYINITIAL> "#elif"                      { return $this->create_token('preprocessor',$this->yytext()); }
<YYINITIAL> "#else"                      { return $this->create_token('preprocessor',$this->yytext()); }
<YYINITIAL> "#endif"                     { return $this->create_token('preprocessor',$this->yytext()); }                              
<YYINITIAL> {D}+{E}({FS}|{IS})?          { return $this->create_token('numeric',$this->yytext()); }
<YYINITIAL> {D}*"."{D}+({E})?{FS}?       { return $this->create_token('numeric',$this->yytext()); }
<YYINITIAL> {D}+"."{D}*({E})?{FS}?       { return $this->create_token('numeric',$this->yytext()); }
<YYINITIAL> "..."                        { return $this->create_token('ellipsis',$this->yytext()); }
<YYINITIAL> ">>="                        { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "<<="                        { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "="                          { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "+="                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "-="                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "*="                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "/="                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "%="                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "&="                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "^="                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "|="                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> ">>"                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "<<"                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "++"                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "--"                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "->"                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "&&"                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "||"                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "<="                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> ">="                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "=="                         { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> ("!="|"~=")                  { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> ";"                          { return $this->create_token('semicolon',$this->yytext()); }
<YYINITIAL> ("{"|"<%")                   { return $this->create_token('bracket','{'); }
<YYINITIAL> ("}"|"%>")                   { return $this->create_token('bracket','}'); }
<YYINITIAL> ","                          { return $this->create_token('comma',$this->yytext()); }
<YYINITIAL> ":"                          { return $this->create_token('colon',$this->yytext()); }
<YYINITIAL> "("                          { return $this->create_token('bracket',$this->yytext()); }
<YYINITIAL> ")"                          { return $this->create_token('bracket',$this->yytext()); }
<YYINITIAL> ("["|"<:")                   { return $this->create_token('bracket','['); }
<YYINITIAL> ("]"|":>")                   { return $this->create_token('bracket',']'); }
<YYINITIAL> "."                          { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "&"                          { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "|"                          { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "^"                          { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "!"                          { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "~"                          { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "-"|"+"|"*"|"/"|"%"|">"|"<"  { return $this->create_token('operators',$this->yytext()); }
<YYINITIAL> "?"                          { return $this->create_token('question_mark',$this->yytext()); }
<YYINITIAL> /\*                          { $this->enterbufferedstate(self::MULTILINE_COMMENT); break; }
<YYINITIAL> L'                           { $this->enterbufferedstate(self::CHARACTER); break; }
<YYINITIAL> '                            { $this->enterbufferedstate(self::CHARACTER); break; }
<YYINITIAL> L\"                          { $this->enterbufferedstate(self::STRING); break; }
<YYINITIAL> \"                           { $this->enterbufferedstate(self::STRING); break; }
<YYINITIAL> .                            { if (!$this->is_white_space($this->yytext())) { $this->create_error($this->yytext()); return $this->create_token('unknown',$this->yytext()); } break; }
<MULTILINE_COMMENT>   \*/                { return $this->leavebufferedstate('multiline_comment');  }
<MULTILINE_COMMENT>   .                  { $this->append($this->yytext()); break;  }
<CHARACTER> \\'                          { $this->append('\''); break;}
<CHARACTER> \\a                          { $this->append("\a"); break;}
<CHARACTER> \\b                          { $this->append("\b"); break;}
<CHARACTER> \\f                          { $this->append("\f"); break;}
<CHARACTER> \\n                          { $this->append("\n"); break;}
<CHARACTER> \\r                          { $this->append("\r"); break;}
<CHARACTER> \\t                          { $this->append("\t"); break;}
<CHARACTER> \\v                          { $this->append("\v"); break; }
<CHARACTER> \\\"                         { $this->append('"'); break;}
<CHARACTER> \\\\                         { $this->append('\\'); break;}
<CHARACTER> \\\?                         { $this->append('?'); break;}
<CHARACTER> \\[0-7]+                     { $this->append(block_formal_langs_octal_to_decimal_char(substr($this->yytext(),1))); break;}
<CHARACTER> \\[xX][0-9a-fA-F]+           { $this->append(block_formal_langs_hex_to_decimal_char(substr($this->yytext(),2))); break;}
<CHARACTER> '                            { return $this->check_and_create_character();  }
<CHARACTER> .                            { $this->append($this->yytext()); break;}
<STRING> \\'                             { $this->append('\''); break;}
<STRING> \\a                             { $this->append("\a"); break;}
<STRING> \\b                             { $this->append("\b"); break;}
<STRING> \\f                             { $this->append("\f"); break;}
<STRING> \\n                             { $this->append("\n"); break;}
<STRING> \\r                             { $this->append("\r"); break;}
<STRING> \\t                             { $this->append("\t"); break;}
<STRING> \\v                             { $this->append("\v"); break; }
<STRING> \\\"                            { $this->append('"'); break;}
<STRING> \\\\                            { $this->append('\\'); break;}
<STRING> \\\?                            { $this->append('?'); break;}
<STRING> \\[0-7]+                        { $this->append(block_formal_langs_octal_to_decimal_char(substr($this->yytext(),1))); break;}
<STRING> \\[xX][0-9a-fA-F]+              { $this->append(block_formal_langs_hex_to_decimal_char(substr($this->yytext(),2))); break;}
<STRING> \"                              { return $this->leavebufferedstate('string');  }
<STRING> .                               { $this->append($this->yytext()); break;}
<SINGLELINE_COMMENT> \n\r                { return $this->leavebufferedstate('singleline_comment'); break; }
<SINGLELINE_COMMENT> \n                  { return $this->leavebufferedstate('singleline_comment'); break; }
<SINGLELINE_COMMENT> \r                  { return $this->leavebufferedstate('singleline_comment'); break; }
<SINGLELINE_COMMENT> .                   { $this->append($this->yytext()); break;}
