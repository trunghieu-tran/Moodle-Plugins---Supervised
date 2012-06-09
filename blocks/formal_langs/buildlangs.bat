REM Builds a lexer files, requires installed JLexPHP to run
java JLexPHP/Main langs_src/simple_english.lex
cd langs_src
ren  simple_english.lex.php language_simple_english.php
move /y language_simple_english.php ..\language_simple_english.php
cd ..
