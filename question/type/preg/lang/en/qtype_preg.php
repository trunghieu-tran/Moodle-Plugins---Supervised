<?php // $Id: qtype_preg.php,v 1.1.2.2 2009/08/31 16:37:53 arborrow Exp $

$string['addingpreg'] = 'Adding regular expression question';
$string['addmoreanswerblanks'] = 'Adding regular expression options';
$string['answersinstruct'] = 'Enter a perl-compatible regular expression as an answer. You should enter at least one expression. If correct answer is given, it should match at least one regular expression with 100% grade.';
$string['answerno'] = 'Answer {$a}';
$string['asserttf'] = 'Positive lookahead assert';
$string['assertff'] = 'Negative lookahead assert';
$string['asserttb'] = 'Positive lookbehind assert';
$string['assertfb'] = 'Negative lookbehind assert';
$string['escg'] = 'Escape G';
$string['correctanswer'] = 'Correct answer';
$string['correctanswer_help'] = 'Enter one of the correct answer in user-readable form to be shown to the student.';
$string['dfa_preg_matcher'] = 'Deterministic finite state automata';
$string['editingpreg'] = 'Editing regular expression question';
$string['emptyparens'] = 'Regex syntax error: empty parenthesis in position from {$a->indfirst} to {$a->indlast}';
$string['engine'] = 'Matching engine';
$string['engine_help'] = 'There is no \'best\' matching enginge, so you could choose best fit for every question.<br/>
<b>Native PHP preg matching engine</b> works using preg_match() function from PHP langugage (internally calling PCRE
library, which uses backtracking method). It supports full regular expression syntax, but don\'t allow
partial matches - so no hinting. Also, due to backtracking algorithm problems it could give wrong answers
on some regular expression like a?NaN (see http://swtch.com/~rsc/regexp/regexp1.html) - NFA engine are
best for this type of regexes. It\'s almost 100% debugged.<br/>
<b>DFA matching algorithm</b> support partial matching and could tell us shortest path to complete a match. However, it 
by nature can\'t support backreferences in regular expressions. It also doesn\'t handle well quantificators with
wide but finite limits like a{2,2000} - they generates too much edges on the DFA graph. DFA matching engine also
doesn\'t support submask extraction.';
$string['exactmatch'] = 'Exact matching';
$string['exactmatch_help'] = 'By default regular expression matching return true if there is at least one match 
for the regular expression in given string (answer). Exact matching means match must be an entire string.<br/>
Set this to Yes, if you write regular expressions for full student\'s answers. Setting this to No gives
you additional flexibility: you can specify some answer with low (or zero) grade to catch common errors and give
comments on them. You still can specify exact matches for some of you regular expressions if you start them with ^ and end with $.';
$string['gvpath'] = 'Path to dot.exe of GraphViz';
$string['gvdescription'] = 'Graphviz will be used for debug printing DFA and syntax tree in human readable form.';
$string['hintbutton'] = 'Hint';
$string['hintgradeborder'] = 'Hint grade border';
$string['hintgradeborder_help'] = 'Answers with grade less then hint grade border won\'t used in hinting.';
$string['hintpenalty'] = 'Penalty for hint';
$string['hintpenalty_help'] = 'Penalty for the pressing Hint button and getting one-character hint. Typically would be greater 
than usual Moodle question penalty (which applies to any new attempt to answer question without hint). 
These penalties are mutually exclusive.';
$string['incorrectregex'] = 'Incorrect regular expression - syntax error!';
$string['largedfa'] = 'Too large dfa';
$string['lazyquant'] = 'Lazy quantificators';
$string['leaf_assert'] = 'Simple assertion';
$string['leaf_backref'] = 'Backreference';
$string['leaf_charset'] = 'Character class';
$string['leaf_meta'] = 'Meta-character or escape-sequence';
$string['noabstractaccept'] = 'Matching by abstract matcher';
$string['nocorrectanswermatch'] = 'No maximum grade regular expression matches correct answer.';
$string['node_alt'] = 'Alternative';
$string['node_assert'] = 'Assertion';
$string['node_concat'] = 'Concatenation';
$string['node_cond_subpatt'] = 'Conditional subpattern';
$string['node_finite_quant'] = 'Finite quantificator';
$string['node_infinite_quant'] = 'Infinite quantificator';
$string['node_subpatt'] = 'Subpattern';
$string['nohintgradeborderpass'] = 'No answer has a grade greater or equal hint grade border. This effectively disables hinting.';
$string['nohintsupport'] = '{$a} engine doesn\'t support hinting';
$string['nfa_preg_matcher'] = 'Nondeterministic finite state automata';
$string['noregex'] = 'No regex supplied for matching';
$string['nosubpatterncapturing'] = '{$a} engine doesn\'t support subpattern capturing, please remove placeholders (except {$0}) from feedback or choose another engine.';
$string['pluginname'] = 'Regular expression';
$string['preg'] = 'Regular expression';
$string['preg_help'] = 'Regular expression is a form of writing a pattern to match different strings. You can use it to verify answers in two ways: an expression to match with full (usually correct) answer, or an expression to match part of the answer (which can be used, for example, to catch common errors and give appropriate comments).<br/>This question use php perl-compatible regular expression syntax. There is many tutorials about creating and using regular expression, here is one <a href="http://www.phpfreaks.com/content/print/126">example</a>. You can find detailed syntax of expression there: <a href="http://www.nusphere.com/kb/phpmanual/reference.pcre.pattern.syntax.htm">php manual</a>. Note that you should neither enclose regular expression in delimiters nor specify any modifiers - Moodle will do it for you.<br/>';
$string['preg_php_matcher'] = 'PHP preg extension';
$string['pregsummary'] = 'Enter a string response from student that can be matched against several regular expressions (non-anchored matching is possible). Shows to the student the correct part of his response. In adaptive mode could give to the student hint by telling next correct character.';
$string['quantifieratstart'] = 'Regex syntax error: quantifier in position from {$a->indfirst} to {$a->indlast} doesn\'t have operand';
$string['subpattern'] = 'Subpattern';
$string['threealtincondsubpatt'] = 'Regex syntax error: three or more top-level alternative in conditional subpattern in position from {$a->indfirst} to {$a->indlast}. Use parenthesis if you want include alternatives in yes-expr on no-expr';
$string['unclosedparen'] = 'Regex syntax error: closing parenthesis \')\' missing for opening parenthesis in position {$a->indfirst}';
$string['unclosedsqbrackets'] = 'Regex syntax error: closing brackets \']\' missing for character class starting at position {$a->indfirst}';
$string['unopenedparen'] = 'Regex syntax error: opening parenthesis \'(\' missing for closing parenthesis in position {$a->indfirst}';
$string['unsupported'] = '{$a->nodename} in position from  {$a->indfirst} to {$a->indlast} is unsupported by {$a->engine}';
$string['unsupportedmodifier'] = 'Error: modifier {$a->modifier} isn\'t supported by engine {$a->classname}.';
$string['usehint'] = 'Use hint';
$string['usehint_help'] = 'In adaptive mode show student Hint button allowing getting one-character hint with applying Hint penalty. Not all matching engines support hinting.';
$string['toolargedfa'] = 'Too large dfa, because too complex regex. Building is too long.';
?>

