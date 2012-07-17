<?php

$string['addmoreanswerblanks'] = 'Adding a regular expression options';
$string['answersinstruct'] = '<p>Enter (at least one) regular expressions in the choosen notation as answers. If a correct answer is given, it should match at least one regular expression with 100% grade.</p><p>You can use placeholders like {$0} in the feedback to insert captured parts of a student\'s response. {$0} will be replaced by the whole match, {$1} with the first subpattern match etc. If the choosen engine doesn\'t support subpatterns capturing you should use only {$0}.</p>';
$string['answerno'] = 'Answer {$a}';
$string['asserttf'] = 'Positive lookahead assertion';
$string['assertff'] = 'Negative lookahead assertion';
$string['asserttb'] = 'Positive lookbehind assertion';
$string['assertfb'] = 'Negative lookbehind assertion';
$string['escg'] = 'Escape G';
$string['correctanswer'] = 'Correct answer';
$string['correctanswer_help'] = 'Enter a correct answer (not a regular expression) to be shown to students. If you leave it empty the matching engine will try to generate a correct answer itself, taking heed to get the closest one to the student\'s response. For now only NFA engine can generate correct answers.';
$string['debugheading'] = 'Debug settings';
$string['defaultenginedescription'] = 'Matching engine selected by default when creating a new question';
$string['defaultenginelabel'] = 'Default matching engine';
$string['defaultnotationdescription'] = 'Notation selected by default when creating a new question';
$string['defaultnotationlabel'] = 'Default notation';
$string['dfa_matcher'] = 'Deterministic finite state automata';
$string['dfaheading'] = 'Deterministic finite state automata engine settings';
$string['dfalimitsdescription'] = 'Allows you to tune time and memory limits for the DFA engine when matching complex regexes.';
$string['emptyparens'] = 'Regex syntax error: empty parentheses in position from {$a->indfirst} to {$a->indlast}';
$string['engine'] = 'Matching engine';
$string['engine_help'] = '<p>There is no \'best\' matching enginge, so you can choose the engine that fits the particular question best.</p><p>Native <b>PHP preg matching engine</b> works using preg_match() function from PHP langugage. It\'s almost 100% bug-free and able to work with full PCRE syntax, but can\'t support advanced features (showing partial matches and hinting).</p><p>The <b>NFA matching engine</b> and the <b>DFA matching engine</b> are engines that use custom matching code. They support partial matching and hinting, but don\'t support lookaround assertions (you\'ll be notified when trying to save a question with unsupported expressions) and potentially can contain bugs (different for each engine: regular expression matching is still a very complex thing).</p><p>If the difference between engines is too hard to you, just try them all to see how their capabilities suit your needs. If one engine fails in a question then try another engines to see if they can handle it better.</p><p>The NFA engine is probably the best choise if you don\'t use lookaround assertions.</p><p>Avoid using the DFA engine for the Moodle shortanswer notation.</p>';
$string['engineheadingdescriptions'] = 'Matching regular expressions can be time and memory consuming. These settings allow you to control limits of time and memory usage by the matching engines. Increase them when you get messages that the regular expression is too complex, but do mind your server\'s performance (you may also want to increase PHP time and memory limits). Decrease them if you get blank page when saving or running a preg question.';
$string['exactmatch'] = 'Exact matching';
$string['exactmatch_help'] = '<p>By default regular expression matching returns true if there is at least one match in the given string (answer). Exact matching means that the match must be the entire string.</p><p>Set this to Yes, if you write regular expressions for full student\'s answers. Setting this to No gives you additional flexibility: you can specify an answer with low (or zero) grade to catch common errors and give comments on them. You still can specify exact matches for some of your regular expressions if you start them with ^ and end with $.</p>';
$string['hintgradeborder'] = 'Hint grade border';
$string['hintgradeborder_help'] = 'Answers with the grade less then the hint grade border won\'t be used in hinting.';
$string['hintnextchar'] = 'next correct character';
$string['hintpenalty'] = 'Penalty for the next character hint';
$string['hintpenalty_help'] = 'Penalty for getting the one-character hint. Typically will be greater than usual Moodle question penalty (which applies to any new attempt to answer question without hints). These penalties are mutually exclusive.';
$string['incorrectrange'] = 'Incorrect range in position from  {$a->indfirst} to {$a->indlast}: the left border is greater then the right border';
$string['PCREincorrectregex'] = 'Incorrect regular expression - syntax error! Consult <a href="http://pcre.org/pcre.txt">PCRE documentation</a> for more information.';
$string['largefa'] = 'Too large finite automaton';
$string['leaf_assert'] = 'simple assertion';
$string['leaf_backref'] = 'backreference';
$string['leaf_charset'] = 'character set';
$string['leaf_meta'] = 'meta-character or escape-sequence';
$string['maxfasizestates'] = 'Automata size limit: states';
$string['maxfasizetransitions'] = 'Automata size limit: transitions';
$string['maxerrorsshowndescription'] = 'Maximum number of errors shown for each regular expression in the question editing form';
$string['maxerrorsshownlabel'] = 'Maximum number of errors shown';
$string['nfa_matcher'] = 'Nondeterministic finite state automata';
$string['nfaheading'] = 'Nondeterministic finite state automata engine settings';
$string['nfalimitsdescription'] = 'Allows you to tune time and memory limits for the NFA engine to use when matching complex regexes';
$string['nfasizelimit'] = 'Maximum size of an NFA';
$string['nfastatelimitdescription'] = 'Maximum count of states in an NFA';
$string['nfatransitionlimitdescription'] = 'Maximum count of transitions in an NFA';
$string['noabstractaccept'] = 'Matching by abstract matcher';
$string['nocorrectanswermatch'] = 'No maximum grade regular expression matches the correct answer';
$string['node_alt'] = 'alternative';
$string['node_assert'] = 'assertion';
$string['node_concat'] = 'concatenation';
$string['node_cond_subpatt'] = 'conditional subpattern';
$string['node_finite_quant'] = 'finite quantifier';
$string['node_infinite_quant'] = 'infinite quantifier';
$string['node_subpatt'] = 'subpattern';
$string['nohintgradeborderpass'] = 'No answer has a grade greater or equal the hint grade border. This disables hinting.';
$string['nohintsupport'] = '{$a} engine doesn\'t support hinting';
$string['notation'] = 'Regular expression notation';
$string['notation_help'] = '<p>You can choose the notation to enter regular expressions. If you just want to write a regular expression, please use the default, <b>Regular expression</b> notation which is very close to PCRE, but has additional error-proof capabilities.</p><p><b>Moodle shortanswer</b> notation allows you to use preg as a usual Moodle shortanswer question with the hinting capability - with no need to understand regular expressions. Just copy you answers from shortanswer question. The \'*\' wildcard is supported.</p>';
$string['notation_native'] = 'Regular expression';
$string['notation_mdlshortanswer'] = 'Moodle shortanswer';
$string['noregex'] = 'No regex supplied for matching';
$string['nosubpatterncapturing'] = '{$a} engine doesn\'t support subpattern capturing, please remove placeholders (except {$0}) from the feedback or choose another engine';
$string['pluginname'] = 'Regular expression';
$string['pluginname_help'] = '<p>Regular expressions are a form of writing patterns to match different strings. You can use it to verify answers in two ways: an expression to match with full (usually correct) answer, or an expression to match a part of the answer (which can be used, for example, to catch common errors and give appropriate comments).</p><p>This question uses the PHP perl-compatible regular expression syntax as the default notation. There are many tutorials about creating and using regular expression, here is one <a href="http://www.phpfreaks.com/content/print/126">example</a>. You can find detailed syntax of expression here: <a href="http://www.nusphere.com/kb/phpmanual/reference.pcre.pattern.syntax.htm">php manual</a>. Note that you should neither enclose regular expression in delimiters nor specify any modifiers - Moodle will do it for you.</p><p>You can also use this question as the advanced form of shortanswer with hinting, even if you don\'t know a bit about regular expressions! Just select <b>Moodle shortanswer</b> as notation for your questions.</p>';
$string['php_preg_matcher'] = 'PHP preg extension';
$string['pluginname_link'] = 'question/type/preg';
$string['pluginnameadding'] = 'Adding a regular expression question';
$string['pluginnameediting'] = 'Editing a regular expression question';
$string['pluginnamesummary'] = 'Enter a string response from student that can be matched against several regular expressions. Shows to the student the correct part of his response. Using behaviours with multiple tries can give a hint by telling a next correct character.<br/>You can use it without knowing regular expression to get hinting by using the \'Moodle shortanswer\' notation.';
$string['quantifieratstart'] = 'Regex syntax error: quantifier in position from {$a->indfirst} to {$a->indlast} doesn\'t have an operand - nothing to repeat';
$string['questioneditingheading'] = 'Question editing settings';
$string['subpattern'] = 'Subpattern';
$string['threealtincondsubpatt'] = 'Regex syntax error: three or more top-level alternatives in the conditional subpattern in position from {$a->indfirst} to {$a->indlast}. Use parentheses if you want to include alternatives in yes-expr on no-expr';
$string['tobecontinued'] = '...';
$string['toolargefa'] = 'Regular expression is too complex to be matched by {$a->engine} due to the time and/or memory limits. Please try another matching engine, ask your administrator to <a href="'.$CFG->wwwroot.'/admin/settings.php?section=qtypesettingpreg"> increase time and memory limits</a> or simplify you regular expression.';
$string['toomanyerrors'] = '.......{$a} more errors';
$string['unclosedparen'] = 'Regex syntax error: missing a closing parenthesis \')\' for the opening parenthesis in position {$a->indfirst}';
$string['unclosedsqbrackets'] = 'Regex syntax error: missing a closing bracket \']\' for the character set starting in position {$a->indfirst}';
$string['ungreedyquant'] = 'ungreedy quantifiers';
$string['unopenedparen'] = 'Regex syntax error: missing opening parenthesis \'(\' for the closing parenthesis in position {$a->indfirst}';
$string['setunsetmod'] = 'Setting and unsetting the {$a->addinfo} modifier at the same time in position from {$a->indfirst} to {$a->indlast}';
$string['unknownunicodeproperty'] = 'Unknown unicode property: {$a->addinfo}';
$string['unknownposixclass'] = 'Unknown posix class: {$a->addinfo}';
$string['unsupported'] = '{$a->nodename} in position from  {$a->indfirst} to {$a->indlast} is unsupported by the {$a->engine}';
$string['unsupportedmodifier'] = 'Error: modifier {$a->modifier} isn\'t supported by the {$a->classname}.';
$string['usehint'] = 'Allow next characters hinting';
$string['usehint_help'] = 'In behaviours which allow multiple tries (e.g. adaptive or interactive) show students the \'Hint next character\' button that allows to get a one-character hint with applying the \'Hint next character penalty\'. Not all matching engines support hinting.';

/* Strings for node description */

//TYPE_LEAF_META
$string['description_empty'] = 'nothing';
//TYPE_LEAF_ASSERT
$string['description_circumflex'] = 'beginning of the string';
$string['description_dollar'] = 'end of the string';
$string['description_wordbreak'] = 'at a word boundary';
$string['description_esc_a'] = 'at the start of the subject';
$string['description_esc_z'] = 'at the end of the subject';
$string['description_esc_g'] = 'at the first matching position in the subject';
//TYPE_LEAF_BACKREF
$string['description_backref'] = 'back reference to subpattern #%number';
$string['description_backref_name'] = 'back reference to subpattern �%name�';
//TYPE_LEAF_RECURSION
$string['description_recursion_all'] = 'recursive match with whole regular expression                                 ';
$string['description_recursion'] = 'recursive match with subpattern #%number';
$string['description_recursion_name'] = 'recursive match with subpattern  �%name�';
//TYPE_LEAF_OPTIONS
$string['description_option_i'] = 'caseless';
$string['description_option_s'] = 'dot metacharacter matches \n in following';
$string['description_option_m'] = 'multiline matching';
$string['description_option_x'] = 'ignore white space';
$string['description_option_U'] = 'quantifiers ungreedy by default';
$string['description_option_J'] = 'allow duplicate names';
//TYPE_NODE_FINITE_QUANT
$string['description_finite_quant'] = '%1 is repeated from %leftborder to %rightborder times (%greed)';
$string['description_finite_quant_0'] = '%1 is repeated no more %rightborder times or missing (%greed)';
$string['description_finite_quant_1'] = '%1 is repeated no more %rightborder times  (%greed)';
//TYPE_NODE_INFINITE_QUANT
$string['description_infinite_quant'] = '%1 is repeated at least %leftborder times (%greed)';
$string['description_infinite_quant_0'] = '%1 is repeated any number of times or missing (%greed)';
$string['description_infinite_quant_1'] = '%1 is repeated any number of times (%greed)';
//%greed
$string['description_quant_lazy'] = 'lazy quantifier';
$string['description_quant_greed'] = 'greed quantifier';
$string['description_quant_possessive'] = 'possessive quantifier';
//TYPE_NODE_CONCAT
$string['description_concat'] = '%1 then %2';
$string['description_concat_ notfirst'] = '%1 then %2';
$string['description_concat_wo_union'] = '%1  %2';
$string['description_concat_short'] = '%1%2';
//TYPE_NODE_ALT
$string['description_alt'] = '%1 or %2';
$string['description_alt_ notfirst'] = '%1 or %2';
//TYPE_NODE_ASSERT
$string['description_assert_pla'] = 'further text should match: "%1"';
$string['description_assert_nla'] = 'further text should not match: "%1"';
$string['description_assert_plb'] = 'preceding text should match: "%1"';
$string['description_assert_nlb'] = 'preceding text should not match: "%1"';
//TYPE_NODE_SUBPATT
$string['description_subpattern'] = 'subpattern #%number: %1;';
$string['description_subpattern_once'] = 'once checked subpattern #%number: %1;';
//TYPE_NODE_COND_SUBPATT
$string['description_cond_subpatt_pla'] = 'if the further text matches "%1" then check: %2 ; else check : %3;';
$string['description_cond_subpatt_nla'] = 'if the further text does not match "%1" then check: %2 ; else check: %3;';
$string['description_cond_subpatt_plb'] = 'if the preceding text matches "%1" then check: %2 ; else check: %3;';
$string['description_cond_subpatt_nlb'] = 'if the preceding text does not match "%1" then check: %2 ; else check: %3;';
$string['description_cond_subpatt_backref'] = 'If the subpattern #%number has been successfully matched then check: %2 ; else check: %3;';
$string['description_cond_subpatt_backref_name'] = 'If the subpattern �%name� has been successfully matched then check: %2 ; else check: %3;';
$string['description_cond_subpatt_recursive_all'] = 'If the whole pattern has been successfully recursively matched then check: %2 ; else check: %3;';
$string['description_cond_subpatt_recursive'] = 'If the pattern#%number has been successfully recursively matched then check: %2 ; else check: %3;';
$string['description_cond_subpatt_recursive_name'] = 'If the pattern �%name� has been successfully recursively matched then check: %2 ; else check: %3;';
//TYPE_LEAF_CONTROL
$string['description_control_accept'] = 'force successful subpattern match';
$string['description_control_fail'] = 'force fail';
$string['description_control_name'] = 'set name to %name to be passed back';
$string['description_control_backtrack'] = 'if the rest of the pattern does not match %what';
$string['description_control_commit'] = 'overall failure, no advance of starting point';
$string['description_control_commit_prune'] = 'advance to next starting character';
$string['description_control_skip'] = 'advance to current matching position';
$string['description_control_skip_name'] = 'advance to (*MARK:%name)';
$string['description_control_then'] = 'backtrack to next alternation';
$string['description_control_newline'] = 'newline matches %what';
$string['description_control_cr'] = 'carriage return only';
$string['description_control_lf'] = 'linefeed only';
$string['description_control_crlf'] = 'carriage return followed by linefeed';
$string['description_control_anycrlf'] = 'carriage return, linefeed or carriage return followed by linefeed';
$string['description_control_any'] = 'any Unicode newline sequence';
$string['description_control_r'] = '\R matches %what';
$string['description_control_bsr_anycrlf'] = 'CR, LF, or CRLF';
$string['description_control_bsr_unicode'] = 'any Unicode newline sequence';
$string['description_control_no_start_opt'] = 'no start-match optimization';
$string['description_control_utf8'] = 'UTF-8 mode';
$string['description_control_utf16'] = 'UTF-16 mode';
$string['description_control_ucp'] = 'PCRE_UCP';
//TYPE_LEAF_CHARSET
$string['description_charset'] = 'one of the following characters: %characters;';
$string['description_charset_negative'] = 'any symbol except the following: %characters;';
$string['description_charset_one'] = '%character';//??
$string['description_charset_char_neg'] = 'not %char';
//CHARSET FLAGS
// TODO correct charset flags
$string['description_charflag_DIGIT'] = '\d AND [:digit:]';
$string['description_charflag_XDIGIT'] = '[:xdigit:]';
$string['description_charflag_SPACE'] = '\s AND [:space:]';
$string['description_charflag_WORD'] = '\w AND [:word:]';
$string['description_charflag_ALNUM'] = '[:alnum:]';
$string['description_charflag_ALPHA'] = '[:alpha:]';
$string['description_charflag_ASCII'] = '[:ascii:]';
$string['description_charflag_CNTRL'] = '[:ctrl:]';
$string['description_charflag_GRAPH'] = '[:graph:]';
$string['description_charflag_LOWER'] = '[:lower:]';
$string['description_charflag_UPPER'] = '[:upper:]';
$string['description_charflag_PRIN'] = '[:print:] PRIN, because PRINT is php keyword';
$string['description_charflag_PUNCT'] = '[:punct:]';
$string['description_charflag_HSPACE'] = '\h';
$string['description_charflag_VSPACE'] = '\v';
$string['description_charflag_UPROPCC'] = 'control';
$string['description_charflag_UPROPCF'] = 'format';
$string['description_charflag_UPROPCN'] = 'unassigned';
$string['description_charflag_UPROPCO'] = 'private use';
$string['description_charflag_UPROPCS'] = 'surrogate';
$string['description_charflag_UPROPC'] = 'other';
$string['description_charflag_UPROPLL'] = 'lower case letter';
$string['description_charflag_UPROPLM'] = 'modifier letter';
$string['description_charflag_UPROPLO'] = 'other letter';
$string['description_charflag_UPROPLT'] = 'title case letter';
$string['description_charflag_UPROPLU'] = 'upper case letter';
$string['description_charflag_UPROPL'] = 'letter';
$string['description_charflag_UPROPMC'] = 'spacing mark';
$string['description_charflag_UPROPME'] = 'enclosing mark';
$string['description_charflag_UPROPMN'] = 'non-spacing mark';
$string['description_charflag_UPROPM'] = 'mark';
$string['description_charflag_UPROPND'] = 'decimal number';
$string['description_charflag_UPROPNL'] = 'letter number';
$string['description_charflag_UPROPNO'] = 'other number';
$string['description_charflag_UPROPN'] = 'number';
$string['description_charflag_UPROPPC'] = 'connector punctuation';
$string['description_charflag_UPROPPD'] = 'dash punctuation';
$string['description_charflag_UPROPPE'] = 'close punctuation';
$string['description_charflag_UPROPPF'] = 'final punctuation';
$string['description_charflag_UPROPPI'] = 'initial punctuation';
$string['description_charflag_UPROPPO'] = 'other punctuation';
$string['description_charflag_UPROPPS'] = 'open punctuation';
$string['description_charflag_UPROPP'] = 'punctuation';
$string['description_charflag_UPROPSC'] = 'currency symbol';
$string['description_charflag_UPROPSK'] = 'modifier symbol';
$string['description_charflag_UPROPSM'] = 'mathematical symbol';
$string['description_charflag_UPROPSO'] = 'other symbol';
$string['description_charflag_UPROPS'] = 'symbol';
$string['description_charflag_UPROPZL'] = 'line separator';
$string['description_charflag_UPROPZP'] = 'paragraph separator';
$string['description_charflag_UPROPZS'] = 'space separator';
$string['description_charflag_UPROPZ'] = 'separator';
$string['description_charflag_UPROPXAN'] = 'any alphanumeric character';
$string['description_charflag_UPROPXPS'] = 'any POSIX space character';
$string['description_charflag_UPROPXSP'] = 'any Perl space character';
$string['description_charflag_UPROPXWD'] = 'any Perl "word" character';
$string['description_charflag_ARABIC'] = 'Arabic caracter';
$string['description_charflag_ARMENIAN'] = 'Armenian caracter';
$string['description_charflag_AVESTAN'] = 'Avestan caracter';
$string['description_charflag_BALINESE'] = 'Balinese caracter';
$string['description_charflag_BAMUM'] = 'Bamum caracter';
$string['description_charflag_BENGALI'] = 'Bengali caracter';
$string['description_charflag_BOPOMOFO'] = 'Bopomofo caracter';
$string['description_charflag_BRAILLE'] = 'Braille caracter';
$string['description_charflag_BUGINESE'] = 'Buginese caracter';
$string['description_charflag_BUHID'] = 'Buhid caracter';
$string['description_charflag_CANADIAN_ABORIGINAL'] = 'Canadian Aboriginal caracter';
$string['description_charflag_CARIAN'] = 'Carian caracter';
$string['description_charflag_CHAM'] = 'Cham caracter';
$string['description_charflag_CHEROKEE'] = 'Cherokee caracter';
$string['description_charflag_COMMON'] = 'Common caracter';
$string['description_charflag_COPTIC'] = 'Coptic caracter';
$string['description_charflag_CUNEIFORM'] = 'Cuneiform caracter';
$string['description_charflag_CYPRIOT'] = 'Cypriot caracter';
$string['description_charflag_CYRILLIC'] = 'Cyrillic caracter';
$string['description_charflag_DESERET'] = 'Deseret caracter';
$string['description_charflag_DEVANAGARI'] = 'Devanagari caracter';
$string['description_charflag_EGYPTIAN_HIEROGLYPHS'] = 'Egyptian Hieroglyphs caracter';
$string['description_charflag_ETHIOPIC'] = 'Ethiopic caracter';
$string['description_charflag_GEORGIAN'] = 'Georgian caracter';
$string['description_charflag_GLAGOLITIC'] = 'Glagolitic caracter';
$string['description_charflag_GOTHIC'] = 'Gothic caracter';
$string['description_charflag_GREEK'] = 'Greek caracter';
$string['description_charflag_GUJARATI'] = 'Gujarati caracter';
$string['description_charflag_GURMUKHI'] = 'Gurmukhi caracter';
$string['description_charflag_HAN'] = 'Han caracter';
$string['description_charflag_HANGUL'] = 'Hangul caracter';
$string['description_charflag_HANUNOO'] = 'Hanunoo caracter';
$string['description_charflag_HEBREW'] = 'Hebrew caracter';
$string['description_charflag_HIRAGANA'] = 'Hiragana caracter';
$string['description_charflag_IMPERIAL_ARAMAIC'] = 'Imperial Aramaic caracter';
$string['description_charflag_INHERITED'] = 'Inherited caracter';
$string['description_charflag_INSCRIPTIONAL_PAHLAVI'] = 'Inscriptional Pahlavi caracter';
$string['description_charflag_INSCRIPTIONAL_PARTHIAN'] = 'Inscriptional Parthian caracter';
$string['description_charflag_JAVANESE'] = 'Javanese caracter';
$string['description_charflag_KAITHI'] = 'Kaithi caracter';
$string['description_charflag_KANNADA'] = 'Kannada caracter';
$string['description_charflag_KATAKANA'] = 'Katakana caracter';
$string['description_charflag_KAYAH_LI'] = 'Kayah_Li caracter';
$string['description_charflag_KHAROSHTHI'] = 'Kharoshthi caracter';
$string['description_charflag_KHMER'] = 'Khmer caracter';
$string['description_charflag_LAO'] = 'Lao caracter';
$string['description_charflag_LATIN'] = 'Latin caracter';
$string['description_charflag_LEPCHA'] = 'Lepcha caracter';
$string['description_charflag_LIMBU'] = 'Limbu caracter';
$string['description_charflag_LINEAR_B'] = 'Linear_B caracter';
$string['description_charflag_LISU'] = 'Lisu caracter';
$string['description_charflag_LYCIAN'] = 'Lycian caracter';
$string['description_charflag_LYDIAN'] = 'Lydian caracter';
$string['description_charflag_MALAYALAM'] = 'Malayalam caracter';
$string['description_charflag_MEETEI_MAYEK'] = 'Meetei Mayek caracter';
$string['description_charflag_MONGOLIAN'] = 'Mongolian caracter';
$string['description_charflag_MYANMAR'] = 'Myanmar caracter';
$string['description_charflag_NEW_TAI_LUE'] = 'New Tai_Lue caracter';
$string['description_charflag_NKO'] = 'Nko caracter';
$string['description_charflag_OGHAM'] = 'Ogham caracter';
$string['description_charflag_OLD_ITALIC'] = 'Old Italic caracter';
$string['description_charflag_OLD_PERSIAN'] = 'Old Persian caracter';
$string['description_charflag_OLD_SOUTH_ARABIAN'] = 'Old_South_Arabian caracter';
$string['description_charflag_OLD_TURKIC'] = 'Old_Turkic caracter';
$string['description_charflag_OL_CHIKI'] = 'Ol_Chiki caracter';
$string['description_charflag_ORIYA'] = 'Oriya caracter';
$string['description_charflag_OSMANYA'] = 'Osmanya caracter';
$string['description_charflag_PHAGS_PA'] = 'Phags_Pa caracter';
$string['description_charflag_PHOENICIAN'] = 'Phoenician caracter';
$string['description_charflag_REJANG'] = 'Rejang caracter';
$string['description_charflag_RUNIC'] = 'Runic caracter';
$string['description_charflag_SAMARITAN'] = 'Samaritan caracter';
$string['description_charflag_SAURASHTRA'] = 'Saurashtra caracter';
$string['description_charflag_SHAVIAN'] = 'Shavian caracter';
$string['description_charflag_SINHALA'] = 'Sinhala caracter';
$string['description_charflag_SUNDANESE'] = 'Sundanese caracter';
$string['description_charflag_SYLOTI_NAGRI'] = 'Syloti Nagri caracter';
$string['description_charflag_SYRIAC'] = 'Syriac caracter';
$string['description_charflag_TAGALOG'] = 'Tagalog caracter';
$string['description_charflag_TAGBANWA'] = 'Tagbanwa caracter';
$string['description_charflag_TAI_LE'] = 'Tai_Le caracter';
$string['description_charflag_TAI_THAM'] = 'Tai Tham caracter';
$string['description_charflag_TAI_VIET'] = 'Tai Viet caracter';
$string['description_charflag_TAMIL'] = 'Tamil caracter';
$string['description_charflag_TELUGU'] = 'Telugu caracter';
$string['description_charflag_THAANA'] = 'Thaana caracter';
$string['description_charflag_THAI'] = 'Thai caracter';
$string['description_charflag_TIBETAN'] = 'Tibetan caracter';
$string['description_charflag_TIFINAGH'] = 'Tifinagh caracter';
$string['description_charflag_UGARITIC'] = 'Ugaritic caracter';
$string['description_charflag_VAI'] = 'Vai caracter';
$string['description_charflag_YI'] = 'Yi caracter';