Correct writing question
-------------------------------------------

Authors:
1. Idea, string analysis method, general architecture, hints implementation - Oleg Sychev.
2. Question type code, sequence analysis, picture generation - Dmitry Mamontov.
3. Typo analysis - Maria Birukova with help from Dmitry Mamontov.
4. Enumeration analysis - Vadim Klevtsov.

1. Description
When you teach the syntax of a new language - either a human or a programming one - you often finding
youself teaching to write correct words (symbols etc) in a correct order. This question will help you with it.

The question contains several analyzers and could be used in different ways.

Typo analysis used to find typos (inserted, deleted, replaced characters and transpositions), missed and extraneous separators.

Seqence analyzes student's response on words (well, actuall tokens or lexeme) level, find the closest possible match with
you answers and shows messages about mistakes to the students. Currently supported mistakes are:
 - misplaced word;
 - missing word;
 - extraneous word.

Enumeration analyzer helps sequence: if you have a part of you enumeration, where elements could be written in any order,
you could leave question to find the order maximally resebling student's response, instead of entering every possible order manually.

In order to teach student to think grammaticaly, you must not reveal the actual mistaken words, but their
grammatical roles when possible (i.e. except extraneous word). Consider student thinking about such messages:
 - "cat" misplaced OR subject misplaced
 - "int" missing OR variable type missing
So for each word (lexeme) of you answer you must enter a grammatical description to be showed to the student.



2. Installation
To work CorrectWriting question type needs some additional components.
They all need to be installed in order for question to work. You could download all them in one archive 
(from GoogleCode) or separately.

If you downloaded one archive, unpacking it you should found "question" and "blocks" folders. Copy them in the main 
directory of Moodle installation (the one containing config.php) - it will install everything in the correct places.

If you downloaded all parts separately (for example because Moodle Plugins Directory don't allow
them to be downloaded together), you should get 3 archives. Place "correctwriting" and "poasquestion" folders in the question/type
folder of Moodle installation. "formal_langs" should be placed in the blocks folder.

After having files in place login as administrator and go to the notifications page.


3. Bugs, suggestions and giving back
Please report bugs in GoogleCode tracker (set Component-WritingCompetently label) - this is preferable way - or Moodle tracker.
Google Code tracker could be found there: http://code.google.com/p/oasychev-moodle-plugins/issues/list
You also may post a suggestions of how to develop question type in the future, but please accompany them with information of
who are you, how you are using question type and how you intend to use enchancements you suggest pedagogically.

If you like the project, please consider helping it too. That doesn't necessary means money or tough programming.

You could publish a paper or thesis about using this question type and send me reference I could quote to
improve rating of this project. If you could publish a scientific paper on this question and it's use with
me as co-author this would help much more, please inform me immediately if you choose to do so.

When writing about using this question, you may wish to cite following sources:
 -  Oleg Sychev, Dmitry Mamontov.  Determining token sequence mistakes in responses to questions with open
  text answer. Arxiv e-print. URL: http://arxiv.org/abs/1301.2466

You could also help by testing - either manual or unit-testing.