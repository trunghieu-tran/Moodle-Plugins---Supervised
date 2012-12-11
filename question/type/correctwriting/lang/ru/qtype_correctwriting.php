<?php

//Lang string file for the correct writing question type

$string['absentmistakemessage'] = '{$a->description} отсутствует в ответе';
$string['absentmistakemessagenodescription'] = '"{$a->value}" отсутствует в ответе';
$string['absentmistakeweight'] = 'Штраф за отсутствие лексемы';
$string['addedmistakemessage'] = '"{$a->value}" лишнее в ответе';
$string['addedmistakemessage_notexist'] = '"{$a->value}" не должно быть в ответе';
$string['addedmistakeweight'] = 'Штраф за лишнюю лексему';
$string['caseno'] = 'Нет, регистр не важен';
$string['casesensitive'] = 'Важен ли регистр?';
$string['caseyes'] = 'Да, регистр важен';
$string['correctwriting'] = 'Пишем правильно';
$string['enterlexemedescriptions']  = 'Пожалуйста, введите описания лексем';
$string['hintgradeborder'] = 'Минимальная оценка ответа для проверки при помощи анализа';
$string['langid'] = 'Язык ответов';
$string['foundlexicalerrors']  = 'В вашем ответе содержатся лексические ошибки. Пожалуйста, исправьте следующие ошибки:';
$string['foundmistakes'] = 'В вашем ответе содержится ошибка:';
$string['foundmistakes'] = 'В вашем ответе содержатся ошибки:';
$string['lexemedescriptions'] = 'Описания лексем';
$string['lexicalerrorthreshold'] = 'Порог ошибки в символах (как отношение к длине лексемы)';
$string['lexicalerrorweight'] = 'Штраф за лексическую ошибку';
$string['maxmistakepercentage'] = 'Максимальное отношение количества ошибок к количеству лексем в ответе для оценки';
$string['movedmistakemessage'] = '{$a->description} находится не на месте';
$string['movedmistakemessagenodescription'] = '{$a->value}, расположенное по {$a->line}:{$a->position} находится не на месте';
$string['movedmistakeweight'] = 'Штраф за перемещение лексемы';
$string['pleaseenterananswer'] = 'Пожалуйста, введите ответ.';
$string['pluginname'] = 'Пишем правильно';
$string['pluginname_help'] = 'Введите вопрос и (возможно несколько) вариантов ответа. При попытке сохранить вопрос в соответствии с выбранным вами языком, варианты ответа будут разбиты на элементарные смысловые единицы - <b>лексемы</b>. Вам необходимо описать грамматическую роль каждой лексемы, которая будет показываться в сообщении об ошибке. Если вы оставите строку описания лексемы пустой, в сообщении будет показано содержимое лексемы.';
$string['pluginname_link'] = 'question/type/correctwriting';
$string['pluginnameadding'] = 'Добавление вопроса типа "Пишем правильно"';
$string['pluginnameediting'] = 'Редактирование вопроса типа "Пишем правильно"';
$string['pluginnamesummary'] = 'Тип вопроса, который умеет находить ошибки в ответе студента и оценивать его соответственно. В настоящее время он может определять отсутствующие, перемещенные и лишние лексемы.';
$string['writelessdescriptions']  = 'Количество описаний больше количества лексем';
$string['writemoredescriptions']  = 'Количество описаний меньше количества лексем';
