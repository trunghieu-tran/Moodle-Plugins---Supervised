<?php 
/**
 * Defines a printf language lexer for correctwriting question type.
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
require_once($CFG->dirroot.'/blocks/formal_langs/printf_language_tokens.php');

class block_formal_langs_language_printf_language extends block_formal_langs_predefined_language
{
    public function __construct() {
        parent::__construct(null,null);
    }
    
    /** Preprocesses a string before scanning. This can be used for simplifying analyze
        and some other purposes, like merging some different variations of  same character
        into one
        @param string $string input string for scanning
        @return string
     */
    protected function preprocess_for_scan($string) {
        return $string;
    }
    
    public function name() {
        return 'printf_language';
    }
        
}

%%

%function next_token
%char
%line
%unicode
%class block_formal_langs_predefined_printf_language_lexer_raw
%state STRING

%{
  
    // @var int number of  current parsed lexeme.
    private  $counter = 0;
  
    public function get_errors() {
        return array();
    }
  
    private function create_token($name, $value) {
        // get name of object
        $objectname = 'block_formal_langs_token_printf_' . $name;
        // create token object
        $res = new $objectname(null, strtoupper($name), $value, $this->return_pos(), $this->counter);
        // increase token count
        $this->counter++;

        return $res;
    }

    function octal_to_decimal_char($matches) {
        $code = $matches[0];
        $code = octdec($code);
        return chr(intval($code));
    }

    function hex_to_decimal_char($matches) {
        $code = $matches[0];
        $code = hexdec($code);
        $string = '';
        if (strlen($matches[0]) == 2) {
            $string = chr(intval($code));
        } else {
            //  mb_convert_encoding left intentionally, because
            // textlib uses iconv to convert, and iconv fails
            // to conver from entities
            $string = mb_convert_encoding('&#' . intval($code) . ';', 'UTF-8', 'HTML-ENTITIES');
        }
        return $string;
    }

    function to_text($text) {
        $state = 0;
        $length = textlib::strlen($text);
        $result = "";
        $statetext = '';
        $esc = array('\'' => '\'', '"' => '"' , 'a' => "\a", 'b' => "\b", 'f' => "\f",
                     'n'  => "\n", 'r' => "\r", 't' => "\t", 'v' => "\v", '\\' => '\\',
                     '?'  => '?');
        $numbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        for($i = 0; $i < $length; $i++) {
            $c = $text[$i];
            $handled = false;
            if ($state == 0 && !$handled) {
                if ($c == '\\') {
                    $state = 1;
                } else {
                    $result .= $c;
                }
                $handled = true;
            }
            if ($state == 1 && !$handled) {
                $handled = true;
                if (array_key_exists($c, $esc)) {
                    $result .= $esc[$c];
                }  else {
                    if ($c == '0') {
                        $state = 2;  $
                        $statetext = '';
                    }  else {
                        if ($c == 'x' || $c == 'X') {
                            $state = 3;
                            $statetext = '';
                        } else {
                            $result .= '\\' . $c;
                        }
                    }
                }
            }
            if ($state == 2 && !$handled) {
                 $handled = true;
                 $ia = array($c, $numbers, $result, $state, $statetext, 'octal_to_decimal_char', '\\0', $i);
                 $a =  $this->handle_cstate_transition($ia);
                 list($state, $statetext, $result, $i) = $a;
            }
            if ($state == 3 && !$handled) {
                 $handled = true;
                 $ia = array($c, $numbers, $result, $state, $statetext, 'hex_to_decimal_char', '\\x', $i);
                 $a =  $this->handle_cstate_transition($ia);
                 list($state, $statetext, $result, $i) = $a;
            }
        }
        $handled = false;
        if ($state == 2 && !$handled) {
            $handled = true;
            $result .= $this->octal_to_decimal_char(array($statetext));
        }
        if ($state == 3 && !$handled) {
            $handled = true;
            $result .= $this->hex_to_decimal_char(array($statetext));
        }
        return $result;
    }

    private function handle_cstate_transition($input_array)
    {
         list($c, $numbers, $result, $state, $statetext, $fun, $d, $i) = $input_array;
         if (in_array($c, $numbers)) {
            $statetext .= $c;
         } else {
            if (textlib::strlen($statetext) != 0) {
                $result .= $this->$fun(array($statetext));
            } else {
                $result .= $d;
            }
            $state = 0;
            --$i;
         }
         return array($state, $statetext, $result, $i);
    }

    private function return_pos() {
        $begin_line = $this->yyline;
        $begin_col = $this->yycol;

        if(strpos($this->yytext(), '\n')) {
            $lines = explode("\n", $this->yytext());
            $num_lines = count($lines);
            
            $end_line = $begin_line + $num_lines - 1;
            $end_col = strlen($lines[$num_lines - 1]) - 1;
        } else {
            $end_line = $begin_line;
            $end_col = $begin_col + strlen($this->yytext()) - 1;
        }
        
        $res = new block_formal_langs_node_position($begin_line, $end_line, $begin_col, $end_col);
        
        return $res;
    }
    
%}



%%

<YYINITIAL> "\""           {  $this->yybegin(self::STRING); return $this->create_token('quote',$this->yytext()); }
<YYINITIAL> [^"\""]+       {  return $this->create_token('text',$this->yytext()); }
<STRING> "\""       {  $this->yybegin(self::YYINITIAL); return $this->create_token('quote',$this->yytext()); }
<STRING> "%%"     { return $this->create_token('text',$this->yytext()); }
<STRING> "%"("-"|"+"|#|0)?([0-9]+|"*")?("."([0-9]+|"*"))?(hh|h|l|ll|j|z|t|l|L)?[diuoxXfFeEgGaAcspn]    { return $this->create_token('specifier',$this->yytext()); }
<STRING> ([^"\"""%"\\]|\\.)+     { return $this->create_token('text',$this->to_text($this->yytext())); }
<STRING> .               { return $this->create_token('text',$this->yytext()); }