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
java JLexPHP/Main langs_src/cpp_language.lex
cd langs_src
ren  cpp_language.lex.php language_cpp_language.php
move /y language_cpp_language.php ..\language_cpp_language.php
cd ..
java JLexPHP/Main langs_src/printf_language.lex
cd langs_src
ren  printf_language.lex.php language_printf_language.php
move /y language_printf_language.php ..\language_printf_language.php
cd ..
java JLexPHP/Main langs_src/attribute_grammar_rule.lex
cd langs_src
ren  attribute_grammar_rule.lex.php language_attribute_grammar_language.php
move /y language_attribute_grammar_language.php ..\anguage_attribute_grammar_language.php
cd ..