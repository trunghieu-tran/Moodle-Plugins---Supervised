<?php

defined('MOODLE_INTERNAL') || die();

// Set templates for testing purposes
qtype_preg\template::set_available_templates(array(
    'word' => new qtype_preg\template('word', '\w+', 'i', array('en' => 'word', 'ru' => 'слово')),
    'two_words' => new qtype_preg\template('two_words', '(?###word)(?###word)', '', array('en' => 'two words', 'ru' => 'два слова')),
    'integer' => new qtype_preg\template('integer', '[+-]?\d+', '', array('en' => 'integer', 'ru' => 'integer')),
    'word_and_integer' => new qtype_preg\template('word_and_integer', '(?###word)(?###integer)', '' , array('en' => 'word', 'ru' => 'слово')),
    'parens_req' => new qtype_preg\template('parens_req', '(   \(    (?:(?-1)|$$1)   \)  )', 'x', array('en' => '$$1 in parens', 'ru' => '$$1 в скобках'), 1),
    'parens_opt' => new qtype_preg\template('parens_opt', '$$1|(?###parens_req<)$$1(?###>)', '', array('en' => '$$1 in parens or not', 'ru' => '$$1 в скобках или без'), 1),
    'brackets_req' => new qtype_preg\template('brackets_req', '(   \[   (?:(?-1)|$$1)   \]   )', 'x', array('en' => '$$1 in brackets', 'ru' => '$$1 в квадратных скобках'), 1),
    'custom_parens_req' => new qtype_preg\template('custom_parens_req', '(   $$1    (?:(?-1)|$$2)   $$3  )', 'x', array('en' => '$$2 in custom parens', 'ru' => '$$1 в особых скобках'), 3),
    'custom_parens_opt' => new qtype_preg\template('custom_parens_opt', '$$2|(?###custom_parens_req<)$$1(?###,)$$2(?###,)$$3(?###>)', 'x', array('en' => '$$2 in optional custom parens', 'ru' => '$$1 в особых скобках или без'), 3),
    'word_in_parens' => new qtype_preg\template('word_in_parens', '(?###parens_req<)(?###word)(?###>)', '', array('en' => 'word in parens', 'ru' => 'слово в скобках')),
    'word_in_parens_in_brackets' => new qtype_preg\template('word_in_parens_in_brackets', '(?###brackets_req<)(?###parens_req<)(?###word)(?###>)(?###>)', '', array('en' => 'word in parens in brackets', 'ru' => 'слово в квадратных и обычных скобках')),
));
