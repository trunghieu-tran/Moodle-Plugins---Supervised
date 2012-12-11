<?php

//Lang string file for the correct writing question type

$string['absentmistakemessage'] = '{$a->description} is missing in response ';
$string['absentmistakemessagenodescription'] = '"{$a->value}" is missing in response ';
$string['absentmistakeweight'] = 'Penalty for missing token in student\'s response ';
$string['addedmistakemessage'] = 'There is extra "{$a->value}" in response';
$string['addedmistakemessage_notexist'] = '"{$a->value}" should not be in response';
$string['addedmistakeweight'] = 'Penalty for odd token in student\'s response';
$string['caseno'] = 'No, case is unimportant';
$string['casesensitive'] = 'Case sensitivity';
$string['caseyes'] = 'Yes, case is important';
$string['correctwriting'] = 'Correct writing';
$string['enterlexemedescriptions']  = 'Please enter token descriptions';
$string['foundlexicalerrors']  = 'There are lexical errors in your answer. Please consider fixing following errors: ';
$string['foundmistakes'] = 'There are mistakes in your response:';
$string['hintgradeborder'] = 'Minimum grade for answer to find and display mistakes';
$string['langid'] = 'Language of the answer';
$string['lexemedescriptions'] = 'Descriptions for tokens';
$string['lexicalerrorthreshold'] = 'Lexical error threshold (as fraction to length of word)';
$string['lexicalerrorweight'] = 'Penalty for lexical error';
$string['maxmistakepercentage'] = 'Maximum percent of mistakes in student\'s response';
$string['movedmistakemessage'] = '{$a->description} misplaced ';
$string['movedmistakemessagenodescription'] = 'The "{$a->value}" at {$a->line}:{$a->position} is misplaced ';
$string['movedmistakeweight'] = 'Penalty for misplaced token in student\'s response';
$string['pleaseenterananswer'] = 'Please enter an answer.';
$string['pluginname'] = 'Correct writing';
$string['pluginname_help'] = 'Test';
$string['pluginname_link'] = 'question/type/correctwriting';
$string['pluginnameadding'] = 'Adding a correct writing  question';
$string['pluginnameediting'] = 'Editing a correct writing  question';
$string['pluginnamesummary'] = 'Question type that can automatically find mistakes in the string response and grade it with penalties. It currently supports token sequence errors: finding misplaced, absent and extra tokens.';
$string['writelessdescriptions']  = 'Supplied amount of descriptions are more than amount of tokens';
$string['writemoredescriptions']  = 'Supplied amount of descriptions are less than amount of tokens';
