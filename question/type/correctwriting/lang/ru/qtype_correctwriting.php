<?php

//Lang string file for the correct writing question type

$string['correctwriting'] = 'Correct writing';
$string['movedmistakemessage'] = '{$a->description} перемещен ';
$string['movedmistakemessagenodescription'] = '{$a->value}, которая находится {$a->line}:{$a->position} перемещена ';
$string['addedmistakemessage'] = '"{$a->value}" at {$a->line}:{$a->position} - лишняя в ответе ';
$string['absentmistakemessage'] = '{$a->description} отсутствует в ответе ';
$string['absentmistakemessagenodescription'] = '"{$a->value}" отсутствует в ответе ';
$string['pleaseenterananswer'] = 'Пожалуйста, введите ответ.';
$string['caseyes'] = 'Да, регистр важен';
$string['caseno'] = 'Нет, регистр не важен';
$string['casesensitive'] = 'Важен ли регистр?';
$string['lexicalerrorthreshold'] = 'Порог ошибки в символах (как отношение к длине лексемы)';
$string['lexicalerrorweight'] = 'Штраф за лексическую ошибку';
$string['absentmistakeweight'] = 'Штраф за отсутствие лексемы';
$string['addedmistakeweight'] = 'Штраф за лишнюю лексему';
$string['movedmistakeweight'] = 'Штраф за перемещение лексемы';
$string['hintgradeborder'] = 'Минимальная оценка ответа для проверки при помощи анализа';
$string['maxmistakepercentage'] = 'Максимальное отношение количества ошибок к количеству лексем в ответе для оценки';
$string['lexemedescriptions'] = 'Описания лексем';
$string['pluginname'] = 'Correct writing';
$string['pluginname_help'] = 'Test';
$string['pluginname_link'] = 'question/type/correctwriting';
$string['pluginnameadding'] = 'Добавление вопроса типа "CorrectWriting"';
$string['pluginnameediting'] = 'Редактирование вопроса типа "CorrectWriting"';
$string['pluginnamesummary'] = 'Тип вопроса, который умеет находить ошибки в ответе студента и оценивать его соответственно';
$string['langid'] = 'Язык ответов';
$string['enterlexemedescriptions']  = 'Пожалуйста, введите описания лексем';
$string['writemoredescriptions']  = 'Количество описаний меньше количества лексем';
$string['writelessdescriptions']  = 'Количество описаний больше количества лексем';
$string['foundlexicalerrors']  = 'В вашем ответе обнаружены лексические ошибки. Пожалуйста, исправьте следующие ошибки: ';
$string['foundmistakes'] = 'В вашем ответе обнаружены следующие ошибки:';
$string['clanguageunmatchedquote'] = 'Не закрыта кавычка на позиции {$a->line}:{$a->col}';
$string['clanguageunmatchedsquote'] = 'Не закрыта кавычка на позиции {$a->line}:{$a->col}';
$string['clanguageunknownsymbol'] = 'Неизвестный символ "{$a->value}" на позиции {$a->line}:{$a->col}';
$string['clanguagemulticharliteral'] = 'Символьный литерал из нескольких символов на позиции {$a->line}:{$a->col}';
