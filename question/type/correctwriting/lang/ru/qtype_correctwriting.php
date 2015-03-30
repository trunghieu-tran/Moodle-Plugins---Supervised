<?php
// This file is part of CorrectWriting question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// CorrectWriting question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// CorrectWriting is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with CorrectWriting.  If not, see <http://www.gnu.org/licenses/>.

//Lang string file for the correct writing question type

$string['absenthintpenaltyfactor'] = 'Множитель штрафов за подсказки при отсутствии лексемы';
$string['absenthintpenaltyfactor_help'] = 'Если подсказка раскрывает содержимое лексемы, то ошибка отсутствия лексемы представляет собой особый случай: если в других ошибках студент написал где-то в ответе что-то близкое к данной лексеме, то при ошибке отсутствия лексемы ничего похожего на нее в ответе нет. Так что для этой ошибки подсказка раскрывает больше информации. Этот множительно позволит вам увеличить штрафы за подобные подсказки. Если результирующий штраф превысит 1, подсказка будет отключена.';
$string['absentmistakemessage'] = '{$a} отсутствует в ответе';
$string['absentmistakeweight'] = 'Штраф за отсутствие лексемы';
$string['absentmistakeweight_help'] = 'Штраф за каждую отсутствующую лексему в ответе студента, если количество ошибок не превосходит максимально дозволенного.';
$string['addedmistakemessage'] = '"{$a}" лишнее в ответе';
$string['addedmistakemessage_notexist'] = '"{$a}" не должно быть в ответе';
$string['addedmistakeweight'] = 'Штраф за лишнюю лексему';
$string['addedmistakeweight_help'] = 'Штраф за каждую лишнюю лексему в ответе студента, если количество ошибок не превосходит максимально дозволенного.';
$string['and'] = ' и ';
$string['answersinstruct'] = 'Введите один или несколько корректных ответов. Когда вы попытаетесь сохранить вопрос, ответы будут разбиты на лексемы в соответствии с выбранным вами языком и вам будет дана возможность ввести графические описания для каждой лексемы. Описания используются вместо текста лексемы в сообщениях об ошибках и подсказках, чтобы не раскрывать студенту значения лексемы. Если строка описания оставлена пустой, вместо него будет использован текст данной лексемы. Однако вы должны ввести необходимое количество пустых строк если не хотите использовать описания, чтобы быть уверенным что вы не просто забыли их ввести создавая новый вопрос.';
$string['caseno'] = 'Нет, регистр не важен';
$string['casesensitive'] = 'Важен ли регистр?';
$string['caseyes'] = 'Да, регистр важен';
$string['clanguagemulticharliteral'] = 'Символьный литерал из нескольких символов на позиции {$a->linestart}:{$a->colstart}';
$string['correctwriting'] = 'Пишем правильно';
$string['enterlexemedescriptions']  = 'Пожалуйста, введите описания лексем';
$string['hintgradeborder'] = 'Минимальная оценка ответа для анализа на ошибки';
$string['hintgradeborder_help'] = 'Анализ ошибок будет производится только для ответов, чья оценка не менее данной. Ответы с меньшими оценками будут учитываться только при полном совпадении с ответом студента. Вы можете использовать ответы с меньшими ошибками если хотите показать особый комментарий для каких-либо ошибок.';
$string['imageanswer']  = 'Правильный ответ: ';
$string['imageresponse']  = 'Ваш ответ: ';
$string['langid'] = 'Язык ответов';
$string['langid_help'] = 'Правила этого языка будут использоваться для разбиения ответов на лексемы.';
$string['foundlexicalerrors']  = 'В вашем ответе содержатся лексические ошибки. Пожалуйста, исправьте следующие ошибки:';
$string['foundmistake'] = 'В вашем ответе содержится ошибка:';
$string['foundmistakes'] = 'В вашем ответе содержатся ошибки:';
$string['lexemedescriptions'] = 'Описания лексем';
$string['lexicalerrorthreshold'] = 'Порог ошибки в символах (как отношение к длине лексемы)';
$string['lexicalerrorthreshold_help'] = 'Максимальное <a href = "http://habrahabr.ru/post/114997/">расстояние Дамерау-Левенштейна</a> между корректной и некорректной лексемой, чтобы некорректная лексема считалась опечаткой. Расстояние вводится как доля длины корректного слова.';
$string['lexicalerrorweight'] = 'Штраф за лексическую ошибку';
$string['lexicalerrorweight_help'] = 'Штраф за каждую лексическую ошибку в ответе студента: опечатку, лишний или отсутствующий разделитель и т.д.';
$string['maxmistakepercentage'] = 'Максимальное отношение количества ошибок к количеству лексем в ответе для оценки';
$string['maxmistakepercentage_help'] = 'Если количество ошибок в ответе студента превосходит указанную долю количества лексем правильного ответа, будет считаться что ответ вообще не совпадал.';
$string['mistakentokens'] = 'ошибочные лексемы';
$string['movedmistakemessage'] = '{$a} находится не на месте';
$string['movedmistakeweight'] = 'Штраф за перемещение лексемы';
$string['movedmistakeweight_help'] = 'Штраф за каждую перемещенную лексему в ответе студента, если количество ошибок не превосходит максимально дозволенного.';
$string['objectname'] = 'вопроса';
$string['pleaseenterananswer'] = 'Пожалуйста, введите ответ.';
$string['pluginname'] = 'Пишем правильно';
$string['pluginname_help'] = 'Введите вопрос и (возможно несколько) вариантов ответа. При попытке сохранить вопрос в соответствии с выбранным вами языком, варианты ответа будут разбиты на элементарные смысловые единицы - <b>лексемы</b>. Вам необходимо описать грамматическую роль каждой лексемы, которая будет показываться в сообщении об ошибке. Если вы оставите строку описания лексемы пустой, в сообщении будет показано содержимое лексемы.';
$string['pluginname_link'] = 'question/type/correctwriting';
$string['pluginnameadding'] = 'Добавление вопроса типа "Пишем правильно"';
$string['pluginnameediting'] = 'Редактирование вопроса типа "Пишем правильно"';
$string['pluginnamesummary'] = 'Тип вопроса, который умеет находить ошибки в ответе студента и оценивать его соответственно. В настоящее время он может определять отсутствующие, перемещенные и лишние лексемы.';
$string['tokens'] = 'Лексемы';
$string['toobigfloatvalue'] = 'Значение поля не должно превышать {$a}';
$string['toosmallfloatvalue'] = 'Значение поля не должно быть менее {$a}';
$string['whatis'] = 'что такое {$a}';
$string['whatishint'] = '{$a->tokendescr} в данном случае "{$a->tokenvalue}"';
$string['whatishintpenalty'] = 'Штраф за подсказу "что такое"';
$string['whatishintpenalty_help'] = 'Подсказка "что такое" позволяет студенту увидеть текст лексемы вместо ее описания в сообщении об ошибке (работает в адаптивном поведении). Вы можете отключить подсказку установив штраф более 1.';
$string['wherepichint'] = 'где {$a} следует находится (в виде изображения)';
$string['wherepichintpenalty'] = 'Штраф за подсказку "где", в виде изображения';
$string['wherepichintpenalty_help'] = 'Подсказка "где", в виде изображения, показывает студенту сообщение о том, между каким лексемами должна быть помещена отсутствующая или перемещенная лексема. Если возможно, используются описания лексем, если нет - текстовые значения. Вы можете отключить подсказку установив штраф более 1.';
$string['wheretxtafter'] = 'сначала следует разместить {$a->after}, затем {$a->token}';
$string['wheretxtbefore'] = 'сначала следует разместить {$a->token}, затем {$a->before}';
$string['wheretxtbetween'] = 'сначала следует разместить {$a->after}, затем {$a->token}, затем {$a->before}';
$string['wheretxthint'] = 'где {$a} следует находится';
$string['wheretxthintpenalty'] = 'Штраф за текстовую подсказку "где"';
$string['wheretxthintpenalty_help'] = 'Текстовая подсказка "где" показывает студенту сообщение о том, между каким лексемами должна быть помещена отсутствующая или перемещенная лексема. Если возможно, используются описания лексем, если нет - текстовые значения. Вы можете отключить подсказку установив штраф более 1.';
$string['writelessdescriptions']  = 'Количество описаний больше количества лексем';
$string['writemoredescriptions']  = 'Количество описаний меньше количества лексем';
