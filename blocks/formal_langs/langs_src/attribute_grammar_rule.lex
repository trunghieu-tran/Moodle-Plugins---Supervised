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
 * Defines a language lexer for arrubuted grammar language.
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

    public function lexem_name() {
        return get_string('part', 'block_formal_langs');
    }

}

%%

%function next_token
%char
%line
%unicode
%class block_formal_langs_predefined_attribute_grammar_language_lexer_raw
%state INSIDE_RULE_BEGINNING, INSIDE_RULE,  INSIDE_STRING

%{

    // @var int number of  current parsed lexeme.
    private  $counter = 0;

    private  $buffer;

    public function get_errors() {
        return array();
    }

    private function create_token($type, $value) {
        // get name of object
        $objectname = 'block_formal_langs_token_base';
        // create token object
        $res = new $objectname(null, $type, $value, $this->return_pos(), $this->counter);
        // increase token count
        $this->counter++;

        return $res;
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
            $end_col = strlen($lines[$num_lines - 1]) - 1;
        } else {
            $end_line = $begin_line;
            $end_col = $begin_col + strlen($this->yytext()) - 1;
        }

        $res = new block_formal_langs_node_position($begin_line, $end_line, $begin_col, $end_col, $begin_str, $end_str);

        return $res;
    }

%}



%%

<YYINITIAL> "."                {  return $this->create_token('ending',$this->yytext()); }
<YYINITIAL> [ ]                {   }
<YYINITIAL> "::="              {  return $this->create_token('rule_separator',$this->yytext()); }
<YYINITIAL> [^.:=]+            {  return $this->create_token('symbol_part',$this->yytext()); }
<YYINITIAL> "%"[^(]+           {  $this->yybegin(self::INSIDE_RULE_BEGINNING); return $this->create_token('rule_specifications',$this->yytext()); }
<INSIDE_RULE_BEGINNING>  "("   {  $this->yybegin(self::INSIDE_RULE); return $this->create_token('rule_begin',$this->yytext()); }
<INSIDE_RULE>            ","   {  return $this->create_token('comma',$this->yytext()); }
<INSIDE_RULE>           [^\"]+ {  return $this->create_token('arg',$this->yytext()); }
<INSIDE_RULE>           \"     {  $this->buffer = ""; $this->yybegin(self::INSIDE_STRING); }
<INSIDE_STRING>        [^\"]+  {  $this->buffer .= $this->yytext(); }
<INSIDE_STRING>        \\\\    {  $this->buffer .= "\\"; }
<INSIDE_STRING>        \\\"    {  $this->buffer .= "\""; }
<INSIDE_STRING>         \"     {  $this->yybegin(self::INSIDE_RULE); return $this->create_token('arg',$this->buffer);  }
<INSIDE_RULE>          ")"     { this->yybegin(self::INITIAL); return $this->create_token('rule_end',$this->yytext()); }
