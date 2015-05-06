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
 
$string['lexical_error_message'] = 'Не могу разобрать {$a->symbol} на позиции {$a->line}:{$a->position} ';
$string['clanguageunmatchedquote'] = 'Не закрыта кавычка на позиции {$a->line}:{$a->col}';
$string['clanguageunmatchedsquote'] = 'Не закрыта одинарная кавычка на позиции {$a->line}:{$a->col}';
$string['clanguageunknownsymbol'] = 'Неизвестный символ "{$a->symbol}" на позиции {$a->line}:{$a->position}';
$string['clanguagemulticharliteral'] = 'Символьный литерал из нескольких символов на позиции {$a->line}:{$a->col}';
$string['defaultlangdescription'] = 'Язык, используемый по умолчанию при создании нового {$a}';
$string['defaultlanglabel'] = 'Язык, используемый по умолчанию';
$string['extraseparatormsg'] = '{$a->correct[0]} возможно содержит лишний разделитель';
$string['lang_c_language'] = 'Язык программирования C';
$string['lang_c_language_help'] = 'Язык программирования C (только лексический разбор).';
$string['lang_cpp_language'] = 'Язык программирования C++';
$string['lang_cpp_language_help'] = 'Язык программирования C++ (только лексический разбор).';
$string['lang_printf_language'] = 'Язык форматированной строки (язык C, используется в printf).';
$string['lang_printf_language_help'] = 'Язык форматированной строки (язык C, используется в printf, только лекс. разборк).';
$string['lang_simple_english'] = 'Английский язык';
$string['lang_simple_english_help'] = 'Простая реализация английского языка (только лексический разбор).';
$string['lexeme'] = 'лексема';
$string['missingseparatormsg'] = '{$a->correct[0]} и {$a->correct[1]} возможно записаны без разделителя';
$string['part'] = 'часть';
$string['pluginname'] = 'Блок формальных языков';
$string['quoteat'] = '"{$a->value}" начинающееся с позиции {$a->line}:{$a->column}';
$string['quoteatsingleline'] = '"{$a->value}" начинающееся с символа {$a->column}';
$string['showedlangsdescription'] = 'Языки, которые будут показаны в форме выбора языка';
$string['showedlangslabel'] =  'Показываемые языки';
$string['typomsg'] = '{$a->correct[0]} возможно содержит опечатку';
$string['visiblelangsdescription'] = 'Пользователи вашего сайта смогут использовать только эти языки, остальные будут скрыты и неактивны';
$string['visiblelangslabel'] =  'Доступные языки';
$string['word'] = 'слово';