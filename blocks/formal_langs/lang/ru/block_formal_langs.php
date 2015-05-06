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

$string['addnewlanguage'] = 'Добавить новый язык';
$string['affectedcourses'] = 'Затронутые курсы: ';
$string['changevisibility'] = 'Изменить видимость в данном контексте';
$string['lexical_error_message'] = 'Не могу разобрать {$a->symbol} на позиции {$a->line}:{$a->position} ';
$string['clanguageunmatchedquote'] = 'Не закрыта кавычка на позиции {$a->line}:{$a->col}';
$string['clanguageunmatchedsquote'] = 'Не закрыта одинарная кавычка на позиции {$a->line}:{$a->col}';
$string['clanguageunknownsymbol'] = 'Неизвестный символ "{$a->symbol}" на позиции {$a->line}:{$a->position}';
$string['clanguagemulticharliteral'] = 'Символьный литерал из нескольких символов на позиции {$a->line}:{$a->col}';
$string['defaultlangdescription'] = 'Язык, используемый по умолчанию при создании нового {$a}';
$string['defaultlanglabel'] = 'Язык, используемый по умолчанию';
$string['deletelanguage'] = 'Удалить язык "{$a}"';
$string['editlanguage'] = 'Редактировать язык "{$a}"';
$string['editinglanguage'] = 'Редактирование языка "{$a}"';
$string['editingnewlanguage'] = 'Добавление нового языка';
$string['editpermissionslink'] = 'Редактировать права в текущем контексте';
$string['editpermissionspagename'] = 'Редактировать права';
$string['extraseparatormsg'] = '{$a->correct0} возможно содержит лишний разделитель';
$string['formallangsglobalsettings'] = 'Глобальные настройки модуля формальных языков';
$string['formallangsvisibilitysettings'] = 'Глобальные настройки видимости формальных языков';
$string['formal_langs:addinstance'] = 'Может добавить новый блок формальных языков в текущий модуль';
$string['formal_langs:addlanguage'] = 'Может добавлять новые формальные языки';
$string['formal_langs:changelanguagevisibility'] = 'Может менять видимость языков в рамках курса';
$string['formal_langs:editalllanguages'] = 'Может редактировать все формальные языки (кроме системных)';
$string['formal_langs:editownlanguages'] = 'Может редактировать только те, формальные языки, что создал сам';
$string['formal_langs:viewlanguagelist']     = 'Может просматривать список формальных языков';
$string['inherited'] = '(Унаследовано)';
$string['inherited_course'] = 'Курс';
$string['inherited_site'] = 'Сайт';
$string['lang_c_language'] = 'Язык программирования C';
$string['lang_c_language_help'] = 'Язык программирования C (только лексический разбор).';
$string['lang_cpp_parseable_language'] = 'Язык программирования C++';
$string['lang_cpp_parseable_language_help'] = 'Язык программирования C++ с базовой поддержкой препроцессора';
$string['lang_cpp_language'] = 'Язык программирования C++';
$string['lang_cpp_language_help'] = 'Язык программирования C++ (только лексический разбор).';
$string['lang_not_found'] = 'Указанный язык не найден';
$string['lang_printf_language'] = 'Язык форматированной строки (язык C, используется в printf).';
$string['lang_printf_language_help'] = 'Язык форматированной строки (язык C, используется в printf, только лекс. разборк).';
$string['lang_simple_english'] = 'Английский язык';
$string['lang_simple_english_help'] = 'Простая реализация английского языка (только лексический разбор).';
$string['language_editing_field_description'] = 'Описание языка';
$string['language_editing_field_name'] = 'Имя языка';
$string['language_editing_field_lexemname'] = 'Название лексемы в языке';
$string['language_editing_field_uiname'] = 'Отображаемое имя в формах';
$string['language_editing_field_scanrules'] = 'Правила лексического разбора';
$string['language_editing_field_parserules'] = 'Правила грамматического разбора';
$string['language_editing_field_version'] = 'Версия';
$string['language_editing_field_visible'] = 'Видим ли в глобальном контексте';
$string['language_editing_submit_add'] = 'Добавить язык и продолжить работу';
$string['language_editing_submit_edit'] = 'Изменить язык и продолжить работу';
$string['language_editing_submit_save_as_new'] = 'Сохранить язык как новый';
$string['lexeme'] = 'лексема';
$string['maximumlexicalbacktrackingexecutuiontimesettingname'] = 'Максимальное время исполнения рекурсивного перебора при анализе опечаток (в секундах)';
$string['maximumlexicalbacktrackingexecutuiontimesettingdescription'] = 'Максимальное время исполнения рекурсивного перебора при анализе опечаток при определении покрытия';
$string['maximumvariationsoftypocorrectionsettingname'] = 'Максимальное число вариантов исправления опечаток';
$string['maximumvariationsoftypocorrectionsettingdescription'] = 'Иногда, когда студент делает большое число опечаток, модуль может сгенерировать большое число вариантов их исправления, отнимая большое количество времени на их анализ. В таком случае - уменьшите его. Однако, если оно окажется слишком малым, то модуль может не найти все опечатки. В таком случае его необходимо увеличить.';
$string['missingseparatormsg'] = '{$a->correct0} и {$a->correct1} возможно записаны без разделителя';
$string['missingseparatornodescriptionmsg'] = 'Возможно пропущен разделитель в {$a->compared}';
$string['part'] = 'часть';
$string['pluginname'] = 'Блок формальных языков';
$string['quoteat'] = '"{$a->value}" начинающееся с позиции {$a->line}:{$a->column}';
$string['quoteatsingleline'] = '"{$a->value}" начинающееся с символа {$a->column}';
$string['showedlangsdescription'] = 'Языки, которые будут показаны в форме выбора языка';
$string['showedlangslabel'] =  'Показываемые языки';
$string['typomsg'] = '{$a->correct0} возможно содержит опечатку';
$string['visiblelangsdescription'] = 'Пользователи вашего сайта смогут использовать только эти языки, остальные будут скрыты и неактивны';
$string['visiblelangslabel'] =  'Доступные языки';
$string['word'] = 'слово';