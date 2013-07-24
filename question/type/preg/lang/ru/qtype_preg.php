<?php

/**
 * Language strings for the Preg question type.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['answersinstruct'] = '<p>Введите регулярные выражения (как минимум одно) в выбранной нотации в качестве ответов. Если дан корректный ответ, он должен совпадать минимум с одним 100% ответом.</p><p>Вы можете использовать конструкцию вида {$0} в отзывах для того, чтобы вставить захваченные части ответа студента. {$0} будет заменено совпадением в целом, {$1} - совпадением с первым подвыражением и т.д. Если выбранный движок поиска не поддерживает подвыражения, вы должны использовать только {$0}.</p>';
$string['answerno'] = 'Ответ {$a}';
$string['charhintpenalty'] = 'Штраф за подсказку следующего символа';
$string['charhintpenalty_help'] = 'Штраф за подсказку следующего символа. Обычно должен быть больше, чем штраф, даваемый за каждую новую попытку ответа на вопрос без подсказки. Эти штрафы взаимоисключающие.';
$string['lexemhintpenalty'] = 'Штраф за подсказку следующей лексемы';
$string['lexemhintpenalty_help'] = 'Штраф за подсказку следующей лексемы. Обычно должен быть больше, чем штраф, даваемый за каждую новую попытку ответа на вопрос без подсказки. Эти штрафы взаимоисключающие.';
$string['correctanswer'] = 'Правильный ответ';
$string['correctanswer_help'] = 'Введите правильный ответ (не регулярное выражение) для показа студентам. Если вы оставите его пустым, preg попытается его сгенерировать сам, пытаясь сделать его наиболее похожим на ответ студента. На данный момент генерировать ответы может только НКА движок.';
$string['debugheading'] = 'Отладочные настройки';
$string['defaultenginedescription'] = 'Движок поиска совпадений, используемый по умолчанию при создании нового вопроса';
$string['defaultenginelabel'] = 'Движок поиска совпадений, используемый по умолчанию';
$string['defaultlangdescription'] = 'Язык, используемый по умолчанию при создании нового вопроса';
$string['defaultlanglabel'] = 'Язык, используемый по умолчанию';
$string['defaultnotationdescription'] = 'Нотация, используемая по умолчанию при создании нового вопроса';
$string['defaultnotationlabel'] = 'Нотация, используемая по умолчанию';
$string['dfa_matcher'] = 'Детерминированные конечные автоматы (ДКА)';
$string['engine'] = 'Движок поиска совпадений';
$string['engine_help'] = '<p>Не существует лучшего движка поиска совпадений, поэтому вы можете выбирать тот, который подходит для конкретного вопроса.</p><p>Стандартный движок <b>PHP</b> работает через функцию preg_match() языка PHP. В нем, скорее всего, нет ошибок, он полностью поддерживает синтаксис PCRE, но не поддерживает частичные совпадения и генерацию подсказок.</p><p>Движки <b>НКА</b> и <b>ДКА</b> написаны самостоятельно; они поддерживают частичные совпадения и генерацию подсказок, но не поддерживают сложные утверждения (вы будете уведомлены, если попытаетесь сохранить вопрос с неподдерживаемыми возможностями) и могут содержать ошибки.</p><p>Если вам трудно понять разницу между движками поиска совпадений, попробуйте их все и проверьте, насколько они вам подходят. Если один движок не подходит, возможно, подойдет другой.</p><p>Движок НКА, скорее всего, является наилучшим выбором, если вы не используете сложные утверждения.</p><p>Не рекомендуется использовать движок ДКА в новых вопросах, т.к. он устрел, имеет наибольшее количество ошибок и плохо поддерживается в последнее время. Используйте его только если вы не можете добиться нужных результатов с помощью других движков.</p>';
$string['exactmatch'] = 'Точное совпадение';
$string['exactmatch_help'] = '<p>По умолчанию поиск совпадений с регулярным выражением возвращает истину, если в ответе есть хотя бы одно совпадение. Точное совпадение означает, что совпадать должна строка целиком.</p><p>Установите значение Да, если вы пишете регулярное выражение для ответа целиком. Установка значения Нет дает дополнительную гибкость: вы можете указать ответ с низкой (или нулевой) оценкой, чтобы "отловить" частые ошибки студентов и дать на них отзыв. Вы также можете указывть режим точного совпадения, начиная регулярное выражение символом ^ и заканчивая его символом $.</p>';
$string['hintcolouredstring'] = ' совпавшая часть ответа';
$string['hintgradeborder'] = 'Граница показа подсказок';
$string['hintgradeborder_help'] = 'Ответы с оценкой ниже границы показа подсказок не будут использоваться для дачи подсказок.';
$string['hintnextchar'] = 'cледующий правильный символ';
$string['hintnextlexem'] = 'пока не закончится {$a}';
$string['langselect'] = 'Язык';
$string['langselect_help'] = 'Для подсказки лексем вам нужно выбрать язык, который разбивает ответ на лексемы. Каждый язык имеет свои правила. Языки определяются с помощью \'Блока формальных языков\'';
$string['largefa'] = 'Слишком большой конечный автомат';
$string['lexemusername'] = 'Синоним слова "лексема", отображаемый студентам';
$string['lexemusername_help'] = 'Возможно, студенты не знают, что атомарная часть языка называется <b>лексемой</b>. Они могут называть ее "словом", "цифрой" или чем-то другим. Вы можете задать слово, которое студенты будут видеть на кнопке подсказки лексемы.';
$string['maxerrorsshowndescription'] = 'Максимальное число показываемых ошибок для каждого регулярного выражения в форме редактирования';
$string['maxerrorsshownlabel'] = 'Максимальное число показываемых ошибок';
$string['nfa_matcher'] = 'Недетерминированные конечные автоматы (НКА)';
$string['nocorrectanswermatch'] = 'Не указано ни одного 100%-правильного ответа';
$string['nohintgradeborderpass'] = 'Не указано ни одного с оценкой выше границы подсказок. Это отключает подсказки.';
$string['notation'] = 'Нотация регулярных выражений';
$string['notation_help'] = '<p>Вы можете указать нотацию регулярных выражений. Если вы хотите просто использовать регулярные выражения, используйте нотацию <b>Регулярное выражение</b>.</p><p>Нотация <b>Регулярное выражение (расширенная)</b> удобнее для больших регулярных выражений. Она эквивалентна опции PCRE_EXTENDED (модификатор "x" в PHP). Игнорирует неэкранированные пробелы, не находящиеся внутри символных классов и считает комментарием все от неэкранированного знака # до конца строки.</p><p>Нотация <b>Moodle shortanswer</b> позволяет использовать preg как обычный вопрос Moodle shortanswer, но с поддержкой подсказок - вам не нужно понимать регулярные выражения. Просто скопируйте ваши ответы из shortanswer вопросов. Поддерживается \'*\'.</p>';
$string['notation_native'] = 'Регулярное выражение';
$string['notation_mdlshortanswer'] = 'Короткий вопрос Moodle';
$string['notation_pcreextended'] = 'Регулярное выражение (расширенная)';
$string['nosubexprcapturing'] = 'Движок {$a} не поддерживает захват подвыражений. Пожалуйста, удалите конструкции вида {$1...9} (кроме {$0}) из отзывов, или выберите другой движок поиска совпадений';
$string['objectname'] = 'вопроса';
$string['pluginname'] = 'Регулярное выражение';
$string['pluginname_help'] = '<p>Регулярные выражения - это форма записи шаблонов, совпадающих с разными строками. Вы можете использовать их для проверки ответов студентов двумя способамиs: для указания полностью правильных ответов или для отлова наиболее частых ошибок и выдачи соответствующих отзывов.</p><p>Этот тип вопросов по умолчанию использует синтаксис PCRE. Существует множество уроков по созданию регулярных выражений, например, <a href="http://www.phpfreaks.com/content/print/126">example</a>. Детальное описание вы можете найти здесь: <a href="http://www.nusphere.com/kb/phpmanual/reference.pcre.pattern.syntax.htm">php manual</a>. Вам не нужно заключать регулярные выражения в разделители или указывать модификаторы - Moodle сделает это сам.</p><p>Вы также можете использовать этот тип вопросов как улучшенный вариант shortanswer (с подсказками), даже если вы ничего не знаете про регулярные выражения! Просто выберите нотацию <b>Moodle shortanswer</b> для ваших вопросов.</p>';
$string['php_preg_matcher'] = 'PHP preg extension';
$string['pluginname_link'] = 'question/type/preg';
$string['pluginnameadding'] = 'Добавление вопроса с регулярными выражениями';
$string['pluginnameediting'] = 'Редактирование вопроса с регулярными выражениями';
$string['pluginnamesummary'] = 'Введите ответ в виде строки, который может быть сопоставлен с несколькими регулярными выражениями. Показываются правильные части ответов студентов. Используются поведения с несколькими попытками, которые могут дать подсказку следующего символа или лексемы.<br/>Вы можете использовать этот тип вопросов не зная регулярные выражения, но имея возможность подсказок с помощью использования нотации \'Moodle shortanswer\'.';
$string['questioneditingheading'] = 'Настройки редактирования вопроса';
$string['regex_handler'] = 'Обработчик регулярных выражений';
$string['subexpression'] = 'Подвыражение';
$string['tobecontinued'] = '...';
$string['toolargequant'] = 'Слишком большой квантификатор';
$string['toomanyerrors'] = '.......{$a} more errors';
$string['lazyquant'] = 'Ленивые квантификаторы';
$string['greedyquant'] = 'Жадные квантификаторы';
$string['possessivequant'] = 'Ревнивые квантификаторы';
$string['ungreedyquant'] = 'Нежадные квантификаторы';
$string['unsupported'] = '{$a->nodename} в позиции с {$a->linefirst}:{$a->indfirst} по {$a->linelast}:{$a->indlast} не поддерживается {$a->engine}.';
$string['unsupportedmodifier'] = 'Ошибка: модификатор {$a->modifier} не поддерживается {$a->classname}.';
$string['usehint_help'] = 'В поведениях, разрешающих несколько попыток, показывать студентам кнопку подсказки следующего символа или следующей лексемы. Не все движки поиска совпадений поддерживают подсказки.';
$string['usecharhint'] = 'Разрешить подсказку следующего символа';
$string['usecharhint_help'] = 'В поведениях, разрешающих несколько попыток, показывать студентам кнопку подсказки следующего символа, которая показывает один правильный символ после правильной части ответа, давая штраф за эту посказку. Не все движки поиска совпадений поддерживают подсказки.';
$string['uselexemhint'] = 'Разрешить подсказку следующей лексемы (слова, числа, знака пунктуации)';
$string['uselexemhint_help'] = '<p>В поведениях, разрешающих несколько попыток, показывать студентам кнопку подсказки следующей лексемы, которая позволяет либо завершить текущую лексему, либо показать следующую, давая штраф за эту посказку. Не все движки поиска совпадений поддерживают подсказки.</p><p><b>Лексема</b> - это атомарная часть языка: слово, число, знак препинания и т.д.</p>';

/******* Abstract syntax tree nodes descriptions *******/
// Types.
$string['leaf_charset']                = 'character set';
$string['leaf_meta']                   = 'meta-character or escape-sequence';
$string['leaf_assert']                 = 'simple assertion';
$string['leaf_backref']                = 'backreference';
$string['leaf_recursion']              = 'recursion';
$string['leaf_control']                = 'control sequence';
$string['leaf_options']                = 'modifier';   // TODO: remove?
$string['node_finite_quant']           = 'finite quantifier';
$string['node_infinite_quant']         = 'infinite quantifier';
$string['node_concat']                 = 'concatenation';
$string['node_alt']                    = 'alternation';
$string['node_assert']                 = 'lookaround assertion';
$string['node_subexpr']                = 'subexpression';
$string['node_cond_subexpr']           = 'conditional subexpression';
$string['node_error']                  = 'syntax error';

// Subtypes.
$string['empty_leaf_meta']             = 'emptiness';
$string['esc_b_leaf_assert']           = 'word boundary assertion';
$string['esc_a_leaf_assert']           = 'start of the subject assertion';
$string['esc_z_leaf_assert']           = 'end of the subject assertion';
$string['esc_g_leaf_assert']           = '';
$string['circumflex_leaf_assert']      = 'start of the subject assertion';
$string['dollar_leaf_assert']          = 'end of the subject assertion';
$string['accept_leaf_control']         = '';   // TODO
$string['fail_leaf_control']           = '';
$string['mark_name_leaf_control']      = '';
$string['commit_leaf_control']         = '';
$string['prune_leaf_control']          = '';
$string['skip_leaf_control']           = '';
$string['skip_name_leaf_control']      = '';
$string['then_leaf_control']           = '';
$string['cr_leaf_control']             = '';
$string['lf_leaf_control']             = '';
$string['crlf_leaf_control']           = '';
$string['anycrlf_leaf_control']        = '';
$string['any_leaf_control']            = '';
$string['bsr_anycrlf_leaf_control']    = '';
$string['bsr_unicode_leaf_control']    = '';
$string['no_start_opt_leaf_control']   = '';
$string['utf8_leaf_control']           = '';
$string['utf16_leaf_control']          = '';
$string['ucp_leaf_control']            = '';
$string['pla_node_assert']             = 'positive lookahead assert';
$string['nla_node_assert']             = 'negative lookahead assert';
$string['plb_node_assert']             = 'positive lookbehind assert';
$string['nlb_node_assert']             = 'negative lookbehind assert';
$string['subexpr_node_subexpr']        = 'subexpression';
$string['onceonly_node_subexpr']       = 'once-only subexpression';
$string['subexpr_node_cond_subexpr']   = '"subexpression"-conditional subexpression';
$string['recursion_node_cond_subexpr'] = 'recursive conditional subexpression';
$string['define_node_cond_subexpr']    = '"define"-conditional subexpression';
$string['pla_node_cond_subexpr']       = 'positive lookahead conditional subexpression';
$string['nla_node_cond_subexpr']       = 'negative lookahead conditional subexpression';
$string['plb_node_cond_subexpr']       = 'positive lookbehind conditional subexpression';
$string['nlb_node_cond_subexpr']       = 'negative lookbehind conditional subexpression';

$string['unknown_error_node_error']                = 'unknown error';
$string['missing_open_paren_node_error']           = 'Syntax error: missing opening parenthesis \'(\' for the closing parenthesis in position {$a->indfirst}.';
$string['missing_close_paren_node_error']          = 'Syntax error: missing a closing parenthesis \')\' for the opening parenthesis in position {$a->indfirst}.';
$string['missing_comment_ending_node_error']       = 'Syntax error: missing closing parenthesis for the comment in position from {$a->indfirst} to {$a->indlast}.';
$string['missing_condsubexpr_ending_node_error']   = 'Unclosed conditional subexpression name.';
$string['missing_callout_ending_node_error']       = 'Unclosed callout.';
$string['missing_control_ending_node_error']       = 'Missing closing parenthesis after control sequence.';
$string['missing_subexpr_name_ending_node_error']  = 'Syntax error in subexpression name';
$string['missing_brackets_for_g_node_error']       = '\g is not followed by a braced, angle-bracketed, or quoted name/number or by a plain number.';
$string['missing_brackets_for_k_node_error']       = '\k is not followed by a braced, angle-bracketed, or quoted name/number or by a plain number.';
$string['unclosed_charset_node_error']             = 'Syntax error: missing a closing bracket \']\' for the character set starting in position {$a->indfirst}.';
$string['posix_class_outside_charset_node_error']  = 'POSIX classes are not allowed outside character sets.';
$string['quantifier_without_parameter_node_error'] = 'Syntax error: quantifier in position from {$a->indfirst} to {$a->indlast} doesn\'t have an operand - nothing to repeat.';
$string['incorrect_quant_range_node_error']        = 'Incorrect quantifier range in position from  {$a->indfirst} to {$a->indlast}: the left border is greater than the right one.';
$string['incorrect_charset_range_node_error']      = 'Incorrect character range in position from  {$a->indfirst} to {$a->indlast}: the left character is "greater" than the right one.';
$string['set_unset_same_modifier_node_error']      = 'Setting and unsetting the {$a->addinfo} modifier at the same time in position from {$a->indfirst} to {$a->indlast}.';
$string['unsupported_modifier_node_error']         = 'Unknown, wrong or unsupported modifier(s): {$a->addinfo}.';
$string['unknown_unicode_property_node_error']     = 'Unknown unicode property: {$a->addinfo}.';
$string['unknown_posix_class_node_error']          = 'Unknown posix class: {$a->addinfo}.';
$string['unknown_control_sequence_node_error']     = 'Unknown control sequence: {$a->addinfo}.';
$string['condsubexpr_too_much_alter_node_error']   = 'Syntax error: three or more top-level alternations in the conditional subexpression in position from {$a->indfirst} to {$a->indlast}. Use parentheses if you want to include alternations in yes-expr on no-expr.';
$string['condsubexpr_assert_expected_node_error']  = 'Assertion or condition expected.';
$string['condsubexpr_zero_condition_node_error']   = 'Invalid condition (?(0).';
$string['slash_at_end_of_pattern_node_error']      = 'Syntax error: \ at end of pattern.';
$string['c_at_end_of_pattern_node_error']          = 'Syntax error: \c at end of pattern.';
$string['cx_should_be_ascii_node_error']           = '\c should be followed by an ascii character.';
$string['unexisting_subexpr_node_error']           = 'The subexpression "{$a->addinfo}" does not exist.';
$string['duplicate_subexpr_names_node_error']      = 'Two named subexpressions have the same name.';
$string['different_subexpr_names_node_error']      = 'Different subexpression names for subexpressions of the same number.';
$string['subexpr_name_expected_node_error']        = 'subexpression name expected.';
$string['unrecognized_pqh_node_error']             = 'Unrecognized character after (? or (?-';
$string['unrecognized_pqlt_node_error']            = 'Unrecognized character after (?<';
$string['unrecognized_pqp_node_error']             = 'Unrecognized character after (?P';
$string['char_code_too_big_node_error']            = 'The character code {$a->addinfo} is too big.';
$string['char_code_disallowed_node_error']         = 'Unicode code points 0xd800 ... 0xdfff are now allowed.';
$string['callout_big_number_node_error']           = 'The number {$a->addinfo} in the callout is too big, should not be greater than 255.';
$string['lnu_unsupported_node_error']              = 'Sequences \L, \l, \N{name}, \U, and \u are not supported.';

// Types and subtypes needed for authoring tools
$string['leaf_charset_negative'] = 'negative character set';
$string['leaf_charset_error']    = 'incorrect character set';

/******* Error messages *******/
$string['error_PCREincorrectregex']             = 'Incorrect regular expression - syntax error! Consult <a href="http://pcre.org/pcre.txt">PCRE documentation</a> for more information.';

/******* DFA and NFA limitations *******/
$string['engine_heading_descriptions'] = 'Matching regular expressions can be time and memory consuming. These settings allow you to control limits of time and memory usage by the matching engines. Increase them when you get messages that the regular expression is too complex, but do mind your server\'s performance (you may also want to increase PHP time and memory limits). Decrease them if you get blank page when saving or running a preg question.';
$string['too_large_fa'] = 'Regular expression is too complex to be matched by {$a->engine} due to the time and/or memory limits. Please try another matching engine, ask your administrator to <a href="{$a->link}"> increase time and memory limits</a> or simplify you regular expression.';
$string['fa_state_limit'] = 'Automata size limit: states';
$string['fa_transition_limit'] = 'Automata size limit: transitions';
$string['dfa_settings_heading'] = 'Deterministic finite state automata engine settings';
$string['nfa_settings_heading'] = 'Nondeterministic finite state automata engine settings';
$string['dfa_state_limit_description'] = 'Allows you to tune time and memory limits for the DFA engine when matching complex regexes';
$string['nfa_state_limit_description'] = 'Allows you to tune time and memory limits for the NFA engine when matching complex regexes';
$string['dfa_transition_limit_description'] = 'Maximum number of transitions in DFA';
$string['nfa_transition_limit_description'] = 'Maximum number of transitions in NFA';

/********** Strings for authoring tools **********************/
$string['authoring_tool_page_header'] = 'Test regex';
$string['authoring_form_charset_mode'] = 'Режим отображения сложных символьных классов:';
$string['authoring_form_charset_flags'] = 'точное значение (унифицированный формат)';
$string['authoring_form_charset_userinscription'] = 'как написано в регулярном выражении';
$string['authoring_form_tree_horiz'] = 'горизонтальное';
$string['authoring_form_tree_vert'] = 'вертикальное';
$string['regex_edit_header_text'] = 'Regex';
$string['regex_edit_header'] = 'Input regex';
$string['regex_edit_header_help'] = 'Here you can input regular expression for which will be draw interactive tree, explaining graph and description. In the field "Input regex" you can input/edit regular expression. Pushing "check" button redraws new image with tree, graph and discription.';
$string['regex_text_text'] = 'Input regex';
$string['regex_show_selection'] = 'show selection';
$string['regex_check_text'] = 'Update';
$string['regex_cancel_text'] = 'Cancel';
$string['regex_back_text'] = 'Save';
$string['regex_tree_build'] = 'Build tree...';
$string['regex_tree_header'] = 'Interactive tree';
$string['regex_tree_header_help'] = 'Here you can see interactive tree. Pressing the node of tree marks corresponding subtree, subgraph and corresponding part of description.';
$string['regex_graph_build'] = 'Build graph...';
$string['regex_graph_header'] = 'Explaining graph';
$string['regex_graph_header_help'] = 'Here you can see explaining graph. Pressing the node of tree marks corresponding subtree, subgraph and corresponding part of description.';
$string['regex_description_header'] = 'Description';
$string['regex_description_header_help'] = 'Here you can see description of regular expression.';
$string['regex_match_header'] = 'Input string for check';
$string['regex_match_header_help'] = 'Here you can input string for matching. In field "Input string" you can input string to varify coincidence whith regular expression (in new string coincidence substring will be marked of greed, don\'t coincidence substring will be marked of reed) or generate one character of continuation. In field "Must match" and "Must not match" you can input strings to varify coincidence/don\'t coincidence whith regular expression (coincidence string will be marked of greed, don\'t coincidence string will be marked of reed).';

// Strings for node description

// TYPE_LEAF_META
$string['description_empty'] = 'ничего';
// TYPE_LEAF_ASSERT
$string['description_circumflex'] = 'начало строки';
$string['description_dollar'] = 'конец строки';
$string['description_wordbreak'] = 'на границе слова';
$string['description_wordbreak_neg'] = 'не на границе слова';
$string['description_esc_a'] = 'в начале текста';
$string['description_esc_z'] = 'в конце текста';
// TYPE_LEAF_BACKREF
$string['description_backref'] = 'обратная ссылка на подмаску №{$a->number}';
$string['description_backref_name'] = 'обратная ссылка на подмаску "{$a->name}"';
// TYPE_LEAF_RECURSION
$string['description_recursion_all'] = 'рекурсивное совпадение со всем регулярным выражением';
$string['description_recursion'] = 'рекурсивное совпадение с подмаской  №{$a->number}';
$string['description_recursion_name'] = 'рекурсивное совпадение с подмаской "{$a->name}"';
// TYPE_LEAF_OPTIONS
$string['description_option_i'] = 'регистронезависимо:';
$string['description_unsetoption_i'] = 'регистрозависимо:';
$string['description_option_s'] = 'точка захватывает \n:';
$string['description_unsetoption_s'] = 'точка не захватывает \n:';
$string['description_option_m'] = 'многострочный режим:';
$string['description_unsetoption_m'] = 'не многострочный режим:';
$string['description_option_x'] = 'пробелы в выражении были проигнорированы:';
$string['description_unsetoption_x'] = 'пробелы в выражении не были проигнорированы:';
$string['description_option_U'] = 'квантификаторы не жадные:';
$string['description_unsetoption_U'] = 'квантификаторы жадные:';
$string['description_option_J'] = 'повторение имен разрешено:';
$string['description_unsetoption_J'] = 'повторение имен запрещено:';
// TYPE_NODE_FINITE_QUANT
$string['description_finite_quant'] = '{$a->firstoperand} повторяется от {$a->leftborder} до {$a->rightborder} раз(а){$a->greedy}';
$string['description_finite_quant_strict'] = '{$a->firstoperand} повторяется {$a->count} раз(а){$a->greedy}';
$string['description_finite_quant_0'] = '{$a->firstoperand} повторяется не более {$a->rightborder} раз или отсутствует{$a->greedy}';
$string['description_finite_quant_1'] = '{$a->firstoperand} повторяется не более {$a->rightborder} раз{$a->greedy}';
$string['description_finite_quant_01'] = '{$a->firstoperand} может отсутствовать{$a->greedy}';
$string['description_finite_quant_borders_err'] = ' (некорректные границы у квантификатора)';
// TYPE_NODE_INFINITE_QUANT
$string['description_infinite_quant'] = '{$a->firstoperand} повторяется хотябы {$a->leftborder} раз(а){$a->greedy}';
$string['description_infinite_quant_0'] = '{$a->firstoperand} повторяется любое количество раз или отсутствует{$a->greedy}';
$string['description_infinite_quant_1'] = '{$a->firstoperand} повторяется любое количество раз{$a->greedy}';
// {$a->greedy}
$string['description_quant_lazy'] = ' (ленивый квантификатор)';
$string['description_quant_greedy'] = '';
$string['description_quant_possessive'] = ' (сверхжадный квантификатор)';
// TYPE_NODE_CONCAT
$string['description_concat'] = '{$a->firstoperand} затем {$a->secondoperand}';
$string['description_concat_wcomma'] = '{$a->firstoperand}, затем {$a->secondoperand}';
$string['description_concat_space'] = '{$a->firstoperand} {$a->secondoperand}';
$string['description_concat_and'] = '{$a->firstoperand} и {$a->secondoperand}';
$string['description_concat_short'] = '{$a->firstoperand}{$a->secondoperand}';
// TYPE_NODE_ALT
$string['description_alt'] = '{$a->firstoperand} или {$a->secondoperand}';
$string['description_alt_wcomma'] = '{$a->firstoperand}, или {$a->secondoperand}';
// TYPE_NODE_ASSERT
$string['description_pla_node_assert'] = 'текст далее должен соответствовать: [{$a->firstoperand}]';
$string['description_nla_node_assert'] = 'текст далее не должен соответствовать: [{$a->firstoperand}]';
$string['description_plb_node_assert'] = 'предыдущий текст должен соответствовать: [{$a->firstoperand}]';
$string['description_nlb_node_assert'] = 'предыдущий текст не должен соответствовать: [{$a->firstoperand}]';
$string['description_pla_node_assert_cond'] = 'текст далее соответствует: [{$a->firstoperand}]';
$string['description_nla_node_assert_cond'] = 'текст далее не соответсвует: [{$a->firstoperand}]';
$string['description_plb_node_assert_cond'] = 'предшествующий текст соответсвует: [{$a->firstoperand}]';
$string['description_nlb_node_assert_cond'] = 'предшествующий текст не соответствует: [{$a->firstoperand}]';
// TYPE_NODE_SUBEXPR
$string['description_subexpression'] = 'подмаска №{$a->number}: [{$a->firstoperand}]';
$string['description_subexpression_once'] = 'однократная подмаска №{$a->number}: [{$a->firstoperand}]';
$string['description_subexpression_name'] = 'подмаска "{$a->name}": [{$a->firstoperand}]';
$string['description_subexpression_once_name'] = 'однократная подмаска "{$a->name}": [{$a->firstoperand}]';
$string['description_grouping'] = 'группировка: [{$a->firstoperand}]';
$string['description_grouping_duplicate'] = 'группировка (номера подмасок сбрасываются в каждой из альтернатив): [{$a->firstoperand}]';
// TYPE_NODE_COND_SUBEXPR ({$a->firstoperand} - first option; {$a->secondoperand} - second option; {$a->cond} - condition )
$string['description_node_cond_subexpr'] = 'если {$a->cond}, тогда проверить: [{$a->firstoperand}]{$a->else}';
$string['description_node_cond_subexpr_else'] = ' иначе проверить: [{$a->secondoperand}]';
$string['description_backref_node_cond_subexpr'] = 'если подмаска №{$a->number} была успешно сопоставлена, тогда проверить: [{$a->firstoperand}]{$a->else}';
$string['description_backref_node_cond_subexpr_name'] = 'если подмаска "{$a->name}" была успешно сопоставлена, тогда проверить: [{$a->firstoperand}]{$a->else}';
$string['description_recursive_node_cond_subexpr_all'] = 'если весь шаблон был рекурсивно сопоставлен тогда проверить: [{$a->firstoperand}]{$a->else}';
$string['description_recursive_node_cond_subexpr'] = 'если подмаска №{$a->number} была успешно рекурсивно сопоставлена, тогда проверить: [{$a->firstoperand}]{$a->else}';
$string['description_recursive_node_cond_subexpr_name'] = 'если подмаска "{$a->name}" была успешно рекурсивно сопоставлена, тогда проверить: [{$a->firstoperand}]{$a->else}';
$string['description_define_node_cond_subexpr'] = 'описание {$a->firstoperand}';
// TYPE_LEAF_CONTROL
$string['description_accept_leaf_control'] = 'спровоцировать удачное совпадение';
$string['description_fail_leaf_control'] = 'спровоцировать неудачу';
$string['description_mark_name_leaf_control'] = 'set name to {$a->name} to be passed back';
$string['description_control_backtrack'] = 'if the rest of the pattern does not match {$a->what}';
$string['description_commit_leaf_control'] = 'overall failure, no advance of starting point';
$string['description_prune_leaf_control'] = 'advance to next starting character';
$string['description_skip_leaf_control'] = 'advance to current matching position';
$string['description_skip_name_leaf_control'] = 'advance to (*MARK:{$a->name})';
$string['description_then_leaf_control'] = 'backtrack to next alternation';
$string['description_control_newline'] = 'newline matches {$a->what}';
$string['description_cr_leaf_control'] = 'carriage return only';
$string['description_lf_leaf_control'] = 'linefeed only';
$string['description_crlf_leaf_control'] = 'carriage return followed by linefeed';
$string['description_anycrlf_leaf_control'] = 'carriage return, linefeed or carriage return followed by linefeed';
$string['description_any_leaf_control'] = 'any Unicode newline sequence';
$string['description_control_r'] = '\R matches {$a->what}';
$string['description_bsr_anycrlf_leaf_control'] = 'CR, LF, or CRLF';
$string['description_bsr_unicode_leaf_control'] = 'any Unicode newline sequence';
$string['description_no_start_opt_leaf_control'] = 'no start-match optimization';
$string['description_utf8_leaf_control'] = 'UTF-8 mode';
$string['description_utf16_leaf_control'] = 'UTF-16 mode';
$string['description_ucp_leaf_control'] = 'PCRE_UCP';
// TYPE_LEAF_CHARSET
$string['description_charset'] = 'один из следующих символов: {$a->characters};';
$string['description_charset_negative'] = 'любой из символов кроме следующих: {$a->characters};';
$string['description_charset_one_neg'] = 'не {$a->characters}';
$string['description_charset_range'] = 'любой символ от {$a->start} до {$a->end}';
$string['description_char'] = '<span style="color:blue">{$a->char}</span>';
$string['description_char_16value'] = 'символ с кодом 0x{$a->code}';
//$string['description_charset_one'] = '{$a->characters}';
// non-printing characters
$string['description_charflag_dot'] = 'any character';
$string['description_charflag_slashd'] = 'decimal digit';
$string['description_charflag_slashh'] = 'horizontal white space character';
$string['description_charflag_slashs'] = 'white space';
$string['description_charflag_slashv'] = 'vertical white space character';//TODO - third string for description \v is it good?
$string['description_charflag_slashw'] = 'word character';
$string['description_char0'] = 'ноль-символ(NUL)';
$string['description_char1'] = 'символ начала заголовка(SOH)';
$string['description_char2'] = 'символ начала такста(STX)';
$string['description_char3'] = 'символ конца текста(ETX)';
$string['description_char4'] = 'символ конца передачи(EOT)';
$string['description_char5'] = 'символ запроса подтверждения(ENQ)';
$string['description_char6'] = 'символ подтверждения(ACK)';// ?! ВОТАФАКИЗЗИС?!
$string['description_char7'] = 'вукового сигнала(BEL)';
$string['description_char8'] = 'символ удаления(BS)';
$string['description_char9'] = 'табуляция(HT)';
$string['description_charA'] = 'перевод строки(LF)';
$string['description_charB'] = 'вертикальная табуляция(VT)'; // TODO - \v already has a string but this string is used when user type \xb ?
$string['description_charC'] = 'символ новой страницы(FF)';
$string['description_charD'] = 'символ возврата каретки(CR)';
$string['description_charE'] = 'shift out character(SO)';
$string['description_charF'] = 'shift in character(SI)';
$string['description_char10'] = 'символ освобождения канала данных(DLE)';
$string['description_char11'] = 'символ управления устройством(DC1)';
$string['description_char12'] = 'символ управления устройством(DC2)';
$string['description_char13'] = 'символ управления устройством(DC3)';
$string['description_char14'] = 'символ управления устройством(DC4)';
$string['description_char15'] = 'символ неподтверждения(NAK)';
$string['description_char16'] = 'символ синхронизации(SYN)';
$string['description_char17'] = 'конца текстового блока(ETB)';
$string['description_char18'] = 'символ отмены(CAN)';
$string['description_char19'] = 'конец носителя(EM)';
$string['description_char1A'] = 'подставитель(SUB)';
$string['description_char1B'] = 'esc-символ(ESC)';
$string['description_char1C'] = 'разделитель файлов(FS)';
$string['description_char1D'] = 'разделитель групп(GS)';
$string['description_char1E'] = 'разделитель записей(RS)';
$string['description_char1F'] = 'разделитель юнитов(US)';
$string['description_char20'] = 'пробел';
$string['description_char7F'] = 'символ удаления(DEL)';
$string['description_charA0'] = 'неразрывный пробел';
$string['description_charAD'] = 'символ мягкого переноса';
$string['description_char2002'] = 'en пробел';
$string['description_char2003'] = 'em пробел';
$string['description_char2009'] = 'тонкий пробел';
$string['description_char200C'] = 'zero width non-joiner';
$string['description_char200D'] = 'zero width joiner';
//CHARSET FLAGS
$string['description_charflag_digit'] = 'десятичное число';
$string['description_charflag_xdigit'] = 'шестнадцатиричное число';
$string['description_charflag_space'] = 'пробел';
$string['description_charflag_word'] = 'символ-слово';
$string['description_charflag_alnum'] = 'буква или цифра';
$string['description_charflag_alpha'] = 'буква';
$string['description_charflag_ascii'] = 'символы с кодом 0-127';
$string['description_charflag_cntrl'] = 'служебный символ';
$string['description_charflag_graph'] = 'печатный символ';
$string['description_charflag_lower'] = 'строчная буква';
$string['description_charflag_upper'] = 'заглавная буква';
$string['description_charflag_print'] = 'печатный символ (включая пробел)';
$string['description_charflag_punct'] = 'печатный символ (исключая буквы, цифры и пробел)';
$string['description_charflag_hspace'] = 'горизонтальный пробел'; // ??
$string['description_charflag_vspace'] = 'вертикальный пробел';// ??!!
$string['description_charflag_Cc'] = 'ASCII или Latin-1 служебный символ';
$string['description_charflag_Cf'] = 'непечатные символы форматирования (Unicode)';
$string['description_charflag_Cn'] = 'символ, отсутствующий в юникоде,';// ??
$string['description_charflag_Co'] = 'символ с кодом, выделенным для приватного использования,';
$string['description_charflag_Cs'] = 'surrogate';
$string['description_charflag_C'] = 'непечатный символ или неиспользуемый код символа';
$string['description_charflag_Ll'] = 'буква в нижнем регистре';
$string['description_charflag_Lm'] = 'спец. символ, используемый как буква,';
$string['description_charflag_Lo'] = 'буква без заглавного варианта';
$string['description_charflag_Lt'] = 'буква в заглавном регистре';
$string['description_charflag_Lu'] = 'буква в верхнем регистре';
$string['description_charflag_L'] = 'буква';
$string['description_charflag_Mc'] = 'spacing mark';
$string['description_charflag_Me'] = 'enclosing mark';
$string['description_charflag_Mn'] = 'non-spacing mark';
$string['description_charflag_M'] = 'mark';
$string['description_charflag_Nd'] = 'decimal number';
$string['description_charflag_Nl'] = 'letter number';
$string['description_charflag_No'] = 'other number';
$string['description_charflag_N'] = 'number';
$string['description_charflag_Pc'] = 'connector punctuation';
$string['description_charflag_Pd'] = 'dash punctuation';
$string['description_charflag_Pe'] = 'close punctuation';
$string['description_charflag_Pf'] = 'final punctuation';
$string['description_charflag_Pi'] = 'initial punctuation';
$string['description_charflag_Po'] = 'other punctuation';
$string['description_charflag_Ps'] = 'open punctuation';
$string['description_charflag_P'] = 'punctuation';
$string['description_charflag_Sc'] = 'денежный символ';
$string['description_charflag_Sk'] = 'символ-модификатор';// ??
$string['description_charflag_Sm'] = 'математический символ';
$string['description_charflag_So'] = 'символ (не математический, денежный)';
$string['description_charflag_S'] = 'символ';
$string['description_charflag_Zl'] = 'разделитель строк';
$string['description_charflag_Zp'] = 'разделитель параграфов';
$string['description_charflag_Zs'] = 'пробельный разделитель';
$string['description_charflag_Z'] = 'разделитель';
$string['description_charflag_Xan'] = 'алфавитно-числовой символ';
$string['description_charflag_Xps'] = 'любой POSIX пробельный символ';
$string['description_charflag_Xsp'] = 'любой Perl пробельный символ';
$string['description_charflag_Xwd'] = 'любой Perl символ-слово';
$string['description_charflag_Arabic'] = 'Arabic character';
$string['description_charflag_Armenian'] = 'Armenian character';
$string['description_charflag_Avestan'] = 'Avestan character';
$string['description_charflag_Balinese'] = 'Balinese character';
$string['description_charflag_Bamum'] = 'Bamum character';
$string['description_charflag_Bengali'] = 'Bengali character';
$string['description_charflag_Bopomofo'] = 'Bopomofo character';
$string['description_charflag_Braille'] = 'Braille character';
$string['description_charflag_Buginese'] = 'Buginese character';
$string['description_charflag_Buhid'] = 'Buhid character';
$string['description_charflag_Canadian_Aboriginal'] = 'Canadian Aboriginal character';
$string['description_charflag_Carian'] = 'Carian character';
$string['description_charflag_Cham'] = 'Cham character';
$string['description_charflag_Cherokee'] = 'Cherokee character';
$string['description_charflag_Common'] = 'Common character';
$string['description_charflag_Coptic'] = 'Coptic character';
$string['description_charflag_Cuneiform'] = 'Cuneiform character';
$string['description_charflag_Cypriot'] = 'Cypriot character';
$string['description_charflag_Cyrillic'] = 'Cyrillic character';
$string['description_charflag_Deseret'] = 'Deseret character';
$string['description_charflag_Devanagari'] = 'Devanagari character';
$string['description_charflag_Egyptian_Hieroglyphs'] = 'Egyptian Hieroglyphs character';
$string['description_charflag_Ethiopic'] = 'Ethiopic character';
$string['description_charflag_Georgian'] = 'Georgian character';
$string['description_charflag_Glagolitic'] = 'Glagolitic character';
$string['description_charflag_Gothic'] = 'Gothic character';
$string['description_charflag_Greek'] = 'Greek character';
$string['description_charflag_Gujarati'] = 'Gujarati character';
$string['description_charflag_Gurmukhi'] = 'Gurmukhi character';
$string['description_charflag_Han'] = 'Han character';
$string['description_charflag_Hangul'] = 'Hangul character';
$string['description_charflag_Hanunoo'] = 'Hanunoo character';
$string['description_charflag_Hebrew'] = 'Hebrew character';
$string['description_charflag_Hiragana'] = 'Hiragana character';
$string['description_charflag_Imperial_Aramaic'] = 'Imperial Aramaic character';
$string['description_charflag_Inherited'] = 'Inherited character';
$string['description_charflag_Inscriptional_Pahlavi'] = 'Inscriptional Pahlavi character';
$string['description_charflag_Inscriptional_Parthian'] = 'Inscriptional Parthian character';
$string['description_charflag_Javanese'] = 'Javanese character';
$string['description_charflag_Kaithi'] = 'Kaithi character';
$string['description_charflag_Kannada'] = 'Kannada character';
$string['description_charflag_Katakana'] = 'Katakana character';
$string['description_charflag_Kayah_Li'] = 'Kayah Li character';
$string['description_charflag_Kharoshthi'] = 'Kharoshthi character';
$string['description_charflag_Khmer'] = 'Khmer character';
$string['description_charflag_Lao'] = 'Lao character';
$string['description_charflag_Latin'] = 'Latin character';
$string['description_charflag_Lepcha'] = 'Lepcha character';
$string['description_charflag_Limbu'] = 'Limbu character';
$string['description_charflag_Linear_B'] = 'Linear B character';
$string['description_charflag_Lisu'] = 'Lisu character';
$string['description_charflag_Lycian'] = 'Lycian character';
$string['description_charflag_Lydian'] = 'Lydian character';
$string['description_charflag_Malayalam'] = 'Malayalam character';
$string['description_charflag_Meetei_Mayek'] = 'Meetei Mayek character';
$string['description_charflag_Mongolian'] = 'Mongolian character';
$string['description_charflag_Myanmar'] = 'Myanmar character';
$string['description_charflag_New_Tai_Lue'] = 'New Tai Lue character';
$string['description_charflag_Nko'] = 'Nko character';
$string['description_charflag_Ogham'] = 'Ogham character';
$string['description_charflag_Old_Italic'] = 'Old Italic character';
$string['description_charflag_Old_Persian'] = 'Old Persian character';
$string['description_charflag_Old_South_Arabian'] = 'Old South_Arabian character';
$string['description_charflag_Old_Turkic'] = 'Old_Turkic character';
$string['description_charflag_Ol_Chiki'] = 'Ol_Chiki character';
$string['description_charflag_Oriya'] = 'Oriya character';
$string['description_charflag_Osmanya'] = 'Osmanya character';
$string['description_charflag_Phags_Pa'] = 'Phags_Pa character';
$string['description_charflag_Phoenician'] = 'Phoenician character';
$string['description_charflag_Rejang'] = 'Rejang character';
$string['description_charflag_Runic'] = 'Runic character';
$string['description_charflag_Samaritan'] = 'Samaritan character';
$string['description_charflag_Saurashtra'] = 'Saurashtra character';
$string['description_charflag_Shavian'] = 'Shavian character';
$string['description_charflag_Sinhala'] = 'Sinhala character';
$string['description_charflag_Sundanese'] = 'Sundanese character';
$string['description_charflag_Syloti_Nagri'] = 'Syloti_Nagri character';
$string['description_charflag_Syriac'] = 'Syriac character';
$string['description_charflag_Tagalog'] = 'Tagalog character';
$string['description_charflag_Tagbanwa'] = 'Tagbanwa character';
$string['description_charflag_Tai_Le'] = 'Tai_Le character';
$string['description_charflag_Tai_Tham'] = 'Tai_Tham character';
$string['description_charflag_Tai_Viet'] = 'Tai_Viet character';
$string['description_charflag_Tamil'] = 'Tamil character';
$string['description_charflag_Telugu'] = 'Telugu character';
$string['description_charflag_Thaana'] = 'Thaana character';
$string['description_charflag_Thai'] = 'Thai character';
$string['description_charflag_Tibetan'] = 'Tibetan character';
$string['description_charflag_Tifinagh'] = 'Tifinagh character';
$string['description_charflag_Ugaritic'] = 'Ugaritic character';
$string['description_charflag_Vai'] = 'Vai character';
$string['description_charflag_Yi'] = 'Yi character';
// description errors
$string['description_errorbefore'] = '<span style="color:red">';
$string['description_errorafter'] = '</span>';
// for testing
$string['description_charflag_word_g'] = 'word character(form g)';//for testing only
$string['description_char_g'] = '<span style="color:blue">{$a->char}</span>(form g)';//for testing only
$string['description_dollar_g'] = 'end of the string(form g)';//for testing
$string['description_concat_g'] = '{$a->g1} then {$a->g2}';
$string['description_concat_short_g'] = '{$a->g1}{$a->g2}';
$string['description_alt_g'] = '{$a->g1} or {$a->g2}';
$string['description_alt_wcomma_g'] = '{$a->g1} or {$a->g2}';
$string['description_empty_g'] = 'nothing(form g)';

// Strings for explaining graph

$string['authoring_tool_explain_graph'] = 'explain graph';

$string['explain_subexpression'] = 'подвыражение №';
$string['explain_backref'] = 'результат подвыражения №';
$string['explain_recursion'] = 'рекурсия';
$string['explain_unknow_node'] = 'неизвестный узел';
$string['explain_unknow_meta'] = 'неизвестный мета-узел';
$string['explain_unknow_assert'] = 'неизвестное утверждение';
$string['explain_unknow_charset_flag'] = 'неизвестный флаг набора символов';
$string['explain_not'] = 'не ';
$string['explain_any_char'] = 'Любой символ из';
$string['explain_any_char_except'] = 'Любой символ кроме';
$string['explain_to'] = ' по ';
$string['explain_from'] = ' c ';
