<?php 
// This file is part of Formal Languages block - https://code.google.com/p/oasychev-moodle-plugins/
//
// Formal Languages block is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Formal Languages block is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Formal Languages block.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Defines a simple  english language lexer for correctwriting question type.
 *
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_base.php');
require_once($CFG->dirroot.'/question/type/poasquestion/jlex.php');
require_once($CFG->dirroot.'/blocks/formal_langs/c_language_tokens.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_utils.php');
require_once($CFG->dirroot.'/lib/textlib.class.php');

class block_formal_langs_language_c_language extends block_formal_langs_predefined_language
{
    public function __construct() {
        parent::__construct(null,null);
    }
    
    
    public function name() {
        return 'c_language';
    }
    }

    public function lexem_name() {
        return get_string('lexeme', 'block_formal_langs');
    }
}


%%

%unicode
%function next_token
%char
%line
%class block_formal_langs_predefined_c_language_lexer_raw
%state CHARACTER
%state STRING
%state MULTILINE_COMMENT
%state SINGLELINE_COMMENT


%{
  
    // @var int number of  current parsed lexeme.
    private  $counter = 0;
    private  $errors  = array();
    // @var qtype_poasquestion\string  temporary string for buffer
    protected $statestring = null;
    // @var int line yyline for token
    protected $stateyyline = 0;
    // @var int column yycol for token
    protected $stateyycol = 0;
    // @var int column yychar for token
    protected $stateyychar = 0;
	
    // @var int end yyline
    protected $endyyline = 0;
    // @var int end yycolumn
    protected $endyycol  = 0;
    // @var int end yycolumn
    protected $endyychar  = 0;

    // @var bool state - is a state for returning error
    protected $endstate = false;
    // @var mixed token
    protected $endtoken;


    private function startbuffer() {
        $this->stateyyline = $this->yyline;
        $this->stateyycol = $this->yycol;
		$this->stateyychar = $this->yychar;
        $this->statestring = new qtype_poasquestion\string();
    }
    // Appends a symbol string to a buffer
    private function append($sym) {
        $this->statestring->concatenate($sym);
    }
    // Returns buffer
    private function  buffer() {
        $result = $this->statestring;
        $this->statestring = null;
        return $result;
    }

    private function create_error($symbol) {
        $res = new block_formal_langs_scanning_error();
        $res->tokenindex = $this->counter;
        $a = new stdClass();
        $a->line = $this->yyline;
        $a->position = $this->yycol;
		$a->str = $this->yychar;

        $a->symbol = $symbol;
        if (is_object($symbol)) {
            $a->symbol = $symbol->string();
        }


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

    private function create_buffer_error($symbol, $token_backward_offset = 0) {
        $res = new block_formal_langs_lexical_error();
        $res->tokenindex = $this->counter - $token_backward_offset;
        $a = new stdClass();
        $a->line = $this->stateyyline;
        $a->position = $this->stateyycol;
        $a->col = $this->stateyycol;
		$a->str = $this->stateyychar;
        $a->symbol = $symbol->string();
        $errormessage = 'lexical_error_message';
        if ($a->symbol[0] == '\'') {
            $errormessage = 'clanguageunmatchedsquote';
        }
        if ($a->symbol[0] == '"') {
            $errormessage = 'clanguageunmatchedquote';
        }
        $res->errormessage = get_string($errormessage,'block_formal_langs',$a);
        $this->errors[] = $res;
    }

    public function get_errors() {
        return $this->errors;
    }

    private function create_token_with_position($class, $value, $position) {
        // create token object
        $classname = 'block_formal_langs_c_token_' . $class;
        if (is_object($value) == false) {
            $value = new qtype_poasquestion\string($value);
        }
        $res = new $classname(null, $class, $value, $position, $this->counter);
        // increase token count
        $this->counter++;

        return $res;
    }

    private function create_token_from_pos($class, $value, $poscb) {
        return $this->create_token_with_position($class, $value, $this->$poscb());
    }

    private function create_token($class,$value) {
        return $this->create_token_from_pos($class, $value, 'return_pos');
    }

    private function create_buffered_token($class,$value) {
        return $this->create_token_from_pos($class, $value, 'return_buffered_pos');
    }

    private function create_buffered_error_token($class, $value) {
        return $this->create_token_from_pos($class, $value, 'return_error_token_pos');
    }
    private function is_white_space($string) {
        // Here we need to escape symbols, so double quotes are inavoidable
        $whitespace = array(' ', "\t", "\n", "\r", "f", "\v");
        $unboxedstring = $string;
        if (is_object($string)) {
            $unboxedstring = $string->string();
        }
        return in_array($unboxedstring[0], $whitespace);
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
        if ( core_text::strlen($value) > $maxcharacterlength) {
            $res = new block_formal_langs_lexical_error();
            $res->tokenindex = $this->counter - 1;
            $a = new stdClass();
            $a->line = $result->position()->linestart();
            $a->col = $result->position()->colstart();
			$a->str = $result->position()->stringstart();
            $a->symbol = $value;
            $res->errorkind = 'clanguagemulticharliteral';
            $res->errormessage = get_string('clanguagemulticharliteral','block_formal_langs',$a);
            $this->errors[] = $res;
        }
        return $result;
    }
  
    private function return_pos() {
        $begin_line = $this->yyline;
        $begin_col = $this->yycol;
		$begin_str  = $this->yychar;
		$end_str = $begin_str + strlen($this->yytext()) - 1;
		
        if(strpos($this->yytext(), '\n')) {
            $lines = explode("\n", $this->yytext());
            $num_lines = count($lines);
            
            $end_line = $begin_line + $num_lines - 1;
            $end_col = core_text::strlen($lines[$num_lines -1]) - 1;
        } else {
            $end_line = $begin_line;
            $end_col = $begin_col + core_text::strlen($this->yytext()) - 1;
        }
        
        $res = new block_formal_langs_node_position($begin_line, $end_line, $begin_col, $end_col, $begin_str, $end_str);
        
        return $res;
    }
    private function return_pos_by_field($blfield, $bcfield, $yycbeg,  $elfield, $ecfield, $yycend)  {
        $begin_line = $this->$blfield;
        $begin_col = $this->$bcfield;
        $end_line =  $this->$elfield;
        $end_col =  $this->$ecfield;

        $res = new block_formal_langs_node_position($begin_line, $end_line, $begin_col, $end_col, $this->$yycbeg, $this->$yycend);

        return $res;
    }

    private function return_buffered_pos() {
        $this->endyyline = $this->yyline;
        $this->endyycol = $this->yycol + core_text::strlen($this->yytext()) - 1;
		$this->endyychar = $this->yychar + core_text::strlen($this->yytext()) - 1;
        return $this->return_pos_by_field('stateyyline', 'stateyycol', 'stateyychar', 'endyyline', 'endyycol', 'endyychar');
    }

    private function return_error_token_pos() {
        return $this->return_pos_by_field('stateyyline', 'stateyycol', 'stateyychar', 'yyline', 'yycol', 'yychar');
    }



    private function hande_buffered_token_error($errorstring, $tokenstring, $splitoffset) {
        $pos = $this->return_error_token_pos();
        $pos1 = new block_formal_langs_node_position($pos->linestart(), $pos->linestart(), $pos->colstart(), $pos->colstart() + $splitoffset - 1);
        $pos2 = new block_formal_langs_node_position($pos->linestart(), $pos->lineend(), $pos->colstart() + $splitoffset, $pos->colend() - 1);
        $this->endstate = true;

        $realstring = $tokenstring;
        if (is_object($realstring)) {
            $realstring = $realstring->string();
        }
    }



    private function hande_buffered_token_error($errorstring, $tokenstring, $splitoffset) {
        $pos = $this->return_error_token_pos();
        $pos1 = new block_formal_langs_node_position($pos->linestart(), $pos->linestart(), $pos->colstart(), $pos->colstart() + $splitoffset - 1);
        $pos2 = new block_formal_langs_node_position($pos->linestart(), $pos->lineend(), $pos->colstart() + $splitoffset, $pos->colend() - 1);
        $this->endstate = true;

        $realstring = $tokenstring;
        if (is_object($realstring)) {
            $realstring = $realstring->string();
        }
        $token1string = core_text::substr($realstring,0, $splitoffset);
        $token2string = core_text::substr($realstring, $splitoffset, null);
        $token1string = new qtype_poasquestion\string($token1string);
        $token2string = new qtype_poasquestion\string($token2string);

        $token1 =  $this->create_token_with_position('unknown', $token1string, $pos1);
        $token2 =  $this->create_token_with_position('unknown', $token2string, $pos2);

        $this->create_buffer_error($errorstring, 2);
        $this->endstate = true;
        $this->endtoken = $token2;
        $this->yybegin(self::YYINITIAL);
        return $token1;
    }
%}

%eofval{
    if ($this->yy_lexical_state == self::SINGLELINE_COMMENT) {
        $this->yybegin(self::YYINITIAL);
        $this->yycol--;
        $pos = $this->return_pos_by_field('stateyyline', 'stateyycol', 'stateyychar', 'yyline', 'yycol', 'yychar');
        $this->yycol++;
        $t = $this->create_token_with_position('singleline_comment', $this->statestring, $pos);
        return $t;
    } else if ($this->yy_lexical_state == self::MULTILINE_COMMENT)  {
        return $this->hande_buffered_token_error($this->statestring, $this->buffer(), 2);
        return $this->hande_buffered_token_error($this->statestring, $this->buffer(), 2);
    } else if ($this->yy_lexical_state == self::STRING)  {
        return $this->hande_buffered_token_error($this->statestring, $this->statestring, 1);
        return $this->hande_buffered_token_error($this->statestring, $this->statestring, 1);
    } else if ($this->yy_lexical_state == self::CHARACTER)  {
        return $this->hande_buffered_token_error($this->statestring, $this->statestring, 1);
    } else {
        if ($this->endstate) {
            $this->endstate = false;
            return $this->endtoken;
        } else {
            return null;
        }
        }
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
<YYINITIAL> [\n\r]                       { }
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
