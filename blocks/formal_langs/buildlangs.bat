REM Builds a lexer files, requires installed JLexPHP to run
java JLexPHP/Main langs_src/simple_english.lex
cd langs_src
ren  simple_english.lex.php language_simple_english.php
move /y language_simple_english.php ..\language_simple_english.php
cd ..
java JLexPHP/Main langs_src/c_language.lex
cd langs_src
ren  c_language.lex.php language_c_language.php
move /y language_c_language.php ..\language_c_language.php
cd ..
