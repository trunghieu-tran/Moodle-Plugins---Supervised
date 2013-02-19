Perl-compatible regular expression question
-------------------------------------------

Authors:
1. Idea, design, question type code, error reporting, hinting - Oleg Sychev
2. Parsing regular expression, DFA regular expression matching engine - Dmitriy Kolesov
3. NFA regular expression matching engine, backreferences, matchers cross-testing, Unicode support - Valeriy Streltsov

Thanks going to:
* Joseph Rezeau - for been devoted tester of preg question type in several releases;
* Tim Hunt - for his polite and useful answers and commentaries that helped writing this question.

1. Description
The question was primarily intended to allow the use of php perl-compatible regular expressions in short answer
questions. It supports full regular expression syntax and can be used with expressions of any complexity
that php preg extension can handle. Regular expressions can be used to specify wide array of patterns for student
answer to match. You can find some links to the descriptions of regular expression syntax in the question docs.

Preg question is also able to hint next character or lexem for a specified penalty in adaptive behaviour if the student is stuck.

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
folder of Moodle installation. "adaptivehints" and "adaptivehintsnopenalties" should be placed in question/behaviour folder.
"formal_langs" should be placed in the blocks folder.

After having files in place login as administrator and go to the notifications page.

Pay heed to the default matching engine setting. Most of you teachers would use it, thought you could choose engine in every question.
See "Matching engines" section for more information.


3. Hinting
DFA and NFA matching engines support hinting for partial matching. If there is no full match in the response,
question chooses the partial match that has less characters to complete matching. It could also show
a next character (or lexem) that leads to the shortest path to complete matching as a hint (with adding hint penalty)
when using adaptive behaviour. Only answers with grade greater or equal hint grade border are used in hinting.


4. Matching engines
There is no 'best' matching enginge, so you could choose best fit for every question. Start by analyzing you needs and
choosing default engine, that could be set on the question type settings page.

If you primary need is to use very complex regular expressions, than "PHP preg extension" is you most likely choice.
By "very complex" we understand regular expression using complex assertions, conditional subpatterns, recursion etc.

If you regular expressions are not that hardcore, but you want to make heavy use of hinting facility, choose NFA engine instead.

Then just experiment. If some engine don't allow you to do something you need (you get messages that something is unsupported, or
regular expression is too complex or hinting is inactive etc) or just have bug in you particular regular expression - try another one.


5. Bugs, suggestions and giving back
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