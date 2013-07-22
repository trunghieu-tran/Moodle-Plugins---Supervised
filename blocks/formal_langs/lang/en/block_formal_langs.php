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
 * A language strings file of block
 *
 * @package    formal_langs
 * @copyright  2012 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
$string['clanguagemulticharliteral'] = 'There are several characters in character literal at {$a->line}:{$a->col}';
$string['clanguageunknownsymbol'] = 'There is unknown character "{$a->symbol}" at {$a->line}:{$a->position}';
$string['clanguageunmatchedquote'] = 'There is unmatched quote at {$a->line}:{$a->col}';
$string['clanguageunmatchedsquote'] = 'There is unmatched single quote at {$a->line}:{$a->col}';
$string['defaultlangdescription'] = 'Language selected by default when creating a new {$a}';
$string['defaultlanglabel'] = 'Default language';
$string['extraseparatormsg'] = 'there may be an extra separator inside {$a->correct[0]}';
$string['lang_c_language'] = 'C programming language';
$string['lang_c_language_help'] = 'C programming language. Scanning only.';
$string['lang_cpp_language'] = 'C++ programming language';
$string['lang_cpp_language_help'] = 'C++ programming language. Scanning only.';
$string['lang_printf_language'] = 'Language for formatting string (C language, like in printf).';
$string['lang_printf_language_help'] = 'Language for formatting string (C language, like in printf). Scanning only.';
$string['lang_simple_english'] = 'English';
$string['lang_simple_english_help'] = 'English Language with support for words, numbers and punctuation marks. Scanning only.';
$string['lexical_error_message'] = 'Cannot match input {$a->symbol} near {$a->line}:{$a->position} ';
$string['lexeme'] = 'lexeme';
$string['missingseparatormsg'] = 'there is no separator between {$a->correct[0]} and {$a->correct[1]}';
$string['part'] = 'part';
$string['pluginname'] = 'Formal languages block';
$string['quote'] = '"{$a}"';
$string['quoteat'] = '"{$a->value}" starting from position {$a->line}:{$a->column}';
$string['quoteatsingleline'] = '"{$a->value}" starting from character {$a->column}';
$string['typomsg'] = 'there may be a typo in {$a->correct[0]}';
$string['visiblelangsdescription'] = 'Users on you site could only use these languages, other languages will be hidden from them';
$string['visiblelangslabel'] =  'Visible languages';
$string['word'] = 'word';