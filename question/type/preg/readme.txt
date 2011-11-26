Perl-compatible regular expression question
-------------------------------------------

Authors:
1. Idea, design, question type code, error reporting, hinting behaviours - Oleg Sychev
2. Parsing regular expression, DFA regular expression matching engine - Dmitriy Kolesov
3. NFA regular expression matching engine, backreferences, matchers cross-testing - Valeriy Streltsov

1. Description
The question intended to allow the use of php perl-compatible regular expressions in short answer
questions. It supports full regular expression syntax and can be used with expressions of any complexity
that php preg extension can handle.

You can find some links to the descriptions of regular expression syntax in help files.

2. Installation
Just copy preg directory in the question/type and enjoy!

3. Hinting
DFA and NFA matching engines support hinting for partial matching. If there is no full match in the response,
question chooses the partial match that has less characters to complete matching. It could also show
a next character that leads to the shortest path to complete matching as a hint (with adding hint penalty)
in adaptive mode. Only answers with grade greater or equal hint grade border are used in hinting.

4. Matching engines
There is no 'best' matching enginge, so you could choose best fit for every question.

Native PHP preg matching engine works using preg_match() function from PHP langugage (internally calling PCRE
library, which uses backtracking method). It supports full regular expression syntax, but doesn't allow
partial matches - so no hinting. Also, due to backtracking algorithm problems it could give wrong answers
on some regular expression like a?NaN (see http://swtch.com/~rsc/regexp/regexp1.html) - NFA engine are
best for this type of regexes. It's almost 100% debugged.

DFA matching algorithm supports partial matching and could tell us shortest path to complete a match. However, it 
by nature can't support backreferences in regular expressions. It also doesn't handle well quantifiers with
wide but finite limits like a{2,2000} - they generate too much edges on the DFA graph. DFA matching engine also
doesn't support subpattern extraction. Subpatterns and backreferences are supported by the NFA engine.