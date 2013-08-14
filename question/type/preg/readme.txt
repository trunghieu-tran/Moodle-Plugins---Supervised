Perl-compatible regular expression question
-------------------------------------------

Authors:
1. Idea, design, question type code, error reporting, hinting - Oleg Sychev
2. DFA regular expression matching engine (deprecated for now) - Dmitriy Kolesov
3. NFA regular expression matching engine, parsing regular expression, backreferences, matchers cross-testing, unicode support - Valeriy Streltsov
4. Explaining graph (authoring tool) - Vladimir Ivanov
5. Regular expression description (authoring tool) - Dmitriy Pahomov
6. Syntax tree, regex testing (authoring tools) - Grigoriy Terehov

Thanks going to:
* Joseph Rezeau - for been devoted tester of preg question type in several releases;
* Tim Hunt - for his polite and useful answers and commentaries that helped writing this question;
* Bondarenko Vitaly - for conversion of a vast set of regular expression matching tests from PCRE library.

1. Description
The question was primarily intended to allow the use of php perl-compatible regular expressions in short answer
questions. It supports full regular expression syntax and can be used with expressions of any complexity
that php preg extension can handle. Regular expressions can be used to specify wide array of patterns for student
answer to match. You can find some links to the descriptions of regular expression syntax in the question docs.

Preg question is also able to hint next character or lexem for a specified penalty in adaptive and interactive
behaviours if the student is stuck, which can be useful in training quizzes.

You could use hinting without the need to know anything about regular expressions. Choosing Moodle Shortanswer
notation allows you to just copy answers from shortanswer question and have hinting capabilities. '*' wildcard is supported.
NFA engine is preferable for such questions for now.


2. Installation
To work with hinting, Preg question type needs to behaviours: adaptivehints and adaptivehintsnopenalties.
They all need to be installed in order for question to work. You could download question type and behaviours
in one archive (from GoogleCode or GitHub) or separately.

If you downloaded one archive, unpacking it you should found "question" and "blocks" folders. Copy them in the main
directory of Moodle installation (the one containing config.php) - it will install everything in the correct places.

If you downloaded all parts separately (for example because Moodle Plugins Directory don't allow
them to be downloaded together), you should get 5 archives. Place "preg" and "poasquestion" folders in the question/type
folder of Moodle installation. "adaptivehints", "interactivehints" and "adaptivehintsnopenalties" should be placed
in question/behaviour folder. "formal_langs" should be placed in the blocks folder.

After having files in place login as administrator and go to the notifications page.

Pay heed to the default matching engine setting. Most of you teachers would use it, thought you could choose engine in every question.
See "Matching engines" section for more information.

In order to have Authoring Tools to work, you should also install Graphviz packet and made it accessible to Moodle, using admin setting
"Path to Dot". It is necessary for drawing Syntax Tree and Explaining Graph for regular expression. Graphviz packet is well known and
respected open source packet for drawing graphs and should not cause any trouble to you server.

3. Hinting
DFA and NFA matching engines support hinting for partial matching. If there is no full match in the response,
question chooses the partial match that has less characters to complete matching. It could also show
a next character (or lexem) that leads to the shortest path to complete matching as a hint (with adding hint penalty)
when using adaptive behaviour. Only answers with grade greater or equal hint grade border are used in hinting.


4. Matching engines
There is no 'best' matching enginge, so you could choose best fit for every question. Start by analyzing you needs and
choosing default engine, that could be set on the question type settings page.

If you primary need is to use very complex regular expressions, than "PHP preg extension" is you most likely choice.
By "very complex" we understand regular expression using complex assertions, conditional subexpressions, recursion etc.

If you regular expressions are not that hardcore, but you want to make heavy use of hinting facility, choose NFA engine instead.

Then just experiment. If some engine don't allow you to do something you need (you get messages that something is unsupported, or
regular expression is too complex or hinting is inactive etc) or just have bug in you particular regular expression - try another one.

5. Authoring tools
Authoring tools are there to help you with regular expression development. For now they are concentrated on the describing you regular
expression in different ways.

Syntax tree shows you an internal structure of expression - what is inside what. If you understand regular expressions throught operators
and operands, it can be of a great help to you. If not, it can help you determine if repeating or alternative applied to the correct parts
of the expression, did you need to use parenthesis and where they should be placed.

Explaining graph graphically shows you how expression works: it's nodes are characters or their sequences; concatenation, alternative and
simple assertions shown by edges, while rectangles show you repetition and subpattern capturing.

Description is an natural language sentence, generated from regular expression, which explain you how you expression will work.

To better understand how parts of the regular expression affects the entire picture, you could select part of regular expression to be 
highlighted on the syntax tree, explaining graph and description tools.

Testing tool allows you to see how you regexes matches with different strings. It expected to be much more convenient than question previewing.
Entered test strings will be saved in database with the question for future use.


6. Bugs, suggestions and giving back
Please report bugs in GoogleCode tracker (set Component-Preg label) - this is preferable way - or Moodle tracker.
Google Code tracker could be found there: http://code.google.com/p/oasychev-moodle-plugins/issues/list
You also may post a suggestions of how to develop question type in the future, but please accompany them with information of
who are you, how you are using question type and how you intend to use enchancements you suggest pedagogically.

If you like the project, please consider helping it too. That doesn't necessary means money or tough programming.

You could publish a paper or thesis about using this question type and send me reference I could quote to
improve rating of this project. If you could publish a scientific paper on this question and it's use with
me as co-author this would help much more, please inform me immediately if you choose to do so.

You could also help by testing - either manual or unit-testing. Unit-testing of regular expressions
is not that hard - look for cross-test files in "simpletest" subfolder of "preg" folder for examples and feel free to ask me questions
about them. You need not be much of a programmer to write them at all - you only need to know regular expressions.
