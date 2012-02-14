<?php // $Id: qtype_preg.php,v 1.1.2.2 2009/08/31 16:37:53 arborrow Exp $

$string['addmoreanswerblanks'] = 'Adding regular expression options';
$string['answersinstruct'] = '<p>Enter a regular expression in choosen notation as an answer. You should enter at least one expression.
 If correct answer is given, it should match at least one regular expression with 100% grade. </p>
<p>You could use placeholders like {$0} in feedback to insert captured parts of student response there. {$0} will be replaced by the whole match,
{$1} with first subpattern match etc. If choosed engine doesn\'t support subpatterns extraction (like DFA) you should use only {$0} thought.</p>.';
$string['answerno'] = 'Answer {$a}';
$string['asserttf'] = 'Positive lookahead assert';
$string['assertff'] = 'Negative lookahead assert';
$string['asserttb'] = 'Positive lookbehind assert';
$string['assertfb'] = 'Negative lookbehind assert';
$string['escg'] = 'Escape G';
$string['correctanswer'] = 'Correct answer';
$string['correctanswer_help'] = 'Enter one of the correct answer in user-readable form to be shown to the student.';
$string['debugheading'] = 'Debug settings';
$string['defaultenginedescription'] = 'Matching engine, selected by default when creating a new question';
$string['defaultenginelabel'] = 'Default matching engine';
$string['defaultnotationdescription'] = 'Notation, selected by default when creating a new question';
$string['defaultnotationlabel'] = 'Default notation';
$string['dfa_matcher'] = 'Deterministic finite state automata';
$string['dfaheading'] = 'Deterministic finite state automata engine settings';
$string['dfalimitsdescription'] = 'Allows you to tune time and memory limits for DFA engine to use when matching complex regexes.';
$string['emptylexem'] = 'Regex syntax error: empty lexem in position from {$a->indfirst} to {$a->indlast}';
$string['emptyparens'] = 'Regex syntax error: empty parenthesis in position from {$a->indfirst} to {$a->indlast}';
$string['engine'] = 'Matching engine';
$string['engine_help'] = '<p>There is no \'best\' matching enginge, so you could choose engine that best fits particular question. </p>
<p> Native <b>PHP preg matching engine</b> works using preg_match() function from PHP langugage (internally calling PCRE
library, which uses backtracking method). It\'s almost 100% bug-free and able to work with full PCRE syntax,
but can\'t support advanced features like showing partial matches and hinting. </p>
<p><b>NFA matching engine</b> and <b>DFA matching engine</b> are engines using custom matching code.
They support partial matching and hinting, but don\'t support full PCRE syntax (you\'ll be notified when
trying to saving question with unsupported expressions) and potentially may contain bugs (different for each engine^ 
regular expression matching is still a very complex thing).</p>
<p> If difference between engines is too hard to you, just try them all to see how their capabilities suit
you needs, than if one fails in particular question try another engines to see if they could handle it better.</p>
<p> Avoid using DFA engine for Moodle shortanswer notation for now thought.</p>';
$string['engineheadingdescriptions'] = 'Matching regular expressions could be a time and memory consuming. These settings allows you to control limits of time and memory usage by matching engine. Increase them when you get messages that regular expression is too complex, but mind you server performance (you may also wish to increase PHP time and memory limits). Decrease them if you get blank page when saving or trying a preg question.';
$string['exactmatch'] = 'Exact matching';
$string['exactmatch_help'] = '<p>By default regular expression matching return true if there is at least one match 
for the regular expression in given string (answer). Exact matching means match must be an entire string.</p>
<p>Set this to Yes, if you write regular expressions for full student\'s answers. Setting this to No gives
you additional flexibility: you can specify some answer with low (or zero) grade to catch common errors and give
comments on them. You still can specify exact matches for some of you regular expressions if you start them with ^ and end with $.</p>';
$string['gvpath'] = 'Path to the bin folder of GraphViz installation';
$string['gvdescription'] = 'Graphviz is used for debug output of finite automatas and syntax trees in human readable form';
$string['hintgradeborder'] = 'Hint grade border';
$string['hintgradeborder_help'] = 'Answers with grade less then hint grade border wouldn\'t be used in hinting.';
$string['hintnextchar'] = 'next correct character';
$string['hintpenalty'] = 'Penalty for next character hint';
$string['hintpenalty_help'] = 'Penalty for getting one-character hint. Typically would be greater 
than usual Moodle question penalty (which applies to any new attempt to answer question without hint). 
These penalties are mutually exclusive.';
$string['PCREincorrectregex'] = 'Incorrect regular expression - syntax error! Consult <a href="http://pcre.org/pcre.txt">PCRE documentation</a> for more information.';
$string['largefa'] = 'Too large fa';
$string['leaf_assert'] = 'simple assertion';
$string['leaf_backref'] = 'backreference';
$string['leaf_charset'] = 'character class';
$string['leaf_meta'] = 'meta-character or escape-sequence';
$string['maxfasizestates'] = 'Automata size limit: states';
$string['maxfasizetransitions'] = 'Automata size limit: transitions';
$string['maxerrorsshowndescription'] = 'Maximum number of errors shown for each regular expression on question editing form';
$string['maxerrorsshownlabel'] = 'Maximum number of errors shown';
$string['nfa_matcher'] = 'Nondeterministic finite state automata';
$string['nfaheading'] = 'Nondeterministic finite state automata engine settings';
$string['nfalimitsdescription'] = 'Allows you to tune time and memory limits for NFA engine to use when matching complex regexes.';
$string['nfasizelimit'] = 'Maximum size of nfa';
$string['nfastatelimitdescription'] = 'Maximum count of states in nfa';
$string['nfatransitionlimitdescription'] = 'Maximum count of transitions in nfa';
$string['noabstractaccept'] = 'Matching by abstract matcher';
$string['nocorrectanswermatch'] = 'No maximum grade regular expression matches correct answer.';
$string['node_alt'] = 'alternative';
$string['node_assert'] = 'assertion';
$string['node_concat'] = 'concatenation';
$string['node_cond_subpatt'] = 'conditional subpattern';
$string['node_finite_quant'] = 'finite quantifier';
$string['node_infinite_quant'] = 'infinite quantifier';
$string['node_subpatt'] = 'subpattern';
$string['nohintgradeborderpass'] = 'No answer has a grade greater or equal hint grade border. This effectively disables hinting.';
$string['nohintsupport'] = '{$a} engine doesn\'t support hinting';
$string['notation'] = 'Regular expression notation';
$string['notation_help'] = '<p>You could choose notation to enter regular expression with. If you just want to write regular expression, please use default, <b>Regular expression</b> notation which is very close to PCRE, but has additional error-proof capabilities. </p>
<p><b>Moodle shortanswer</b> notation allows you to use preg as usual Moodle shortanswer question with hinting capability - with no need to understand regular expressions at all. Just copy you answers from shortanswer question. \'*\' wildcard is supported.</p>';
$string['notation_native'] = 'Regular expression';
$string['notation_mdlshortanswer'] = 'Moodle shortanswer';
$string['noregex'] = 'No regex supplied for matching';
$string['nosubpatterncapturing'] = '{$a} engine doesn\'t support subpattern capturing, please remove placeholders (except {$0}) from feedback or choose another engine.';
$string['pluginname'] = 'Regular expression';
$string['pluginname_help'] = '<p> Regular expression is a form of writing a pattern to match different strings. You can use it to verify answers in two ways: an expression to match with full (usually correct) answer, or an expression to match part of the answer (which can be used, for example, to catch common errors and give appropriate comments).</p>
<p>This question use php perl-compatible regular expression syntax as default notation. There is many tutorials about creating and using regular expression, here is one <a href="http://www.phpfreaks.com/content/print/126">example</a>. You can find detailed syntax of expression there: <a href="http://www.nusphere.com/kb/phpmanual/reference.pcre.pattern.syntax.htm">php manual</a>. Note that you should neither enclose regular expression in delimiters nor specify any modifiers - Moodle will do it for you.</p>
<p>You could also use this question as advanced form of shortanswer with hinting, even if you don\'t know a bit about regular expressions at all! Just select <b>Moodle shortanswer</b> as notation for you questions.</p>';
$string['php_preg_matcher'] = 'PHP preg extension';
$string['pluginname_link'] = 'question/type/preg';
$string['pluginnameadding'] = 'Adding regular expression question';
$string['pluginnameediting'] = 'Editing regular expression question';
$string['pluginnamesummary'] = 'Enter a string response from student that can be matched against several regular expressions. Shows to the student the correct part of his response. In behaviours with multiple tries could give hint by telling next correct character. <br /> You could use it without knowing regular expression to get hinting using \'Moodle shortanswer\' notation.';
$string['quantifieratstart'] = 'Regex syntax error: quantifier in position from {$a->indfirst} to {$a->indlast} doesn\'t have operand - nothing to repeat';
$string['questioneditingheading'] = 'Question editing settings';
$string['subpattern'] = 'Subpattern';
$string['threealtincondsubpatt'] = 'Regex syntax error: three or more top-level alternative in conditional subpattern in position from {$a->indfirst} to {$a->indlast}. Use parenthesis if you want include alternatives in yes-expr on no-expr';
$string['tobecontinued'] = '...';
$string['toolargefa'] = 'Regular expression is too complex to be matched by {$a->engine} due to the time and/or memory limits. Please try another matching engine, ask your administrator to <a href="'.$CFG->wwwroot.'/admin/settings.php?section=qtypesettingpreg">increase time and memory limits</a> or simplify you regular expression.';
$string['toomanyerrors'] = '.......{$a} more errors';
$string['unclosedlexem'] = 'Regex syntax error: missing ending of the lexem \'(?#}})\' for the beginning of the lexem in position {$a->indfirst}';
$string['unclosedparen'] = 'Regex syntax error: missing closing parenthesis \')\' for the opening parenthesis in position {$a->indfirst}';
$string['unclosedsqbrackets'] = 'Regex syntax error: missing a closing bracket \']\' for the character class starting in position {$a->indfirst}';
$string['ungreedyquant'] = 'ungreedy quantifiers';
$string['unopenedlexem'] = 'Regex syntax error: missing beginning of the lexem \'(?#{{)\' for the ending of the lexem in position {$a->indfirst}';
$string['unopenedparen'] = 'Regex syntax error: missing opening parenthesis \'(\' for the closing parenthesis in position {$a->indfirst}';
$string['setunsetmod'] = 'Regex syntax error: set and unset same modifier between {$a->indfirst} {$a->indlast}';
$string['unsupported'] = '{$a->nodename} in position from  {$a->indfirst} to {$a->indlast} is unsupported by {$a->engine}';
$string['unsupportedmodifier'] = 'Error: modifier {$a->modifier} isn\'t supported by engine {$a->classname}.';
$string['usehint'] = 'Allow next characters hinting';
$string['usehint_help'] = 'In behaviours, which allow multiple tries (e.g. adaptive or interactive) show student Hint next character button allowing getting one-character hint with applying Hint next character penalty. Not all matching engines support hinting.';
?>

