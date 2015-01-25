<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for matching options for different matchers.
 *
 * We need tests to ensure that any matcher work correctly
 * under any settings. There are too many of them.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/question.php');
require_once($CFG->dirroot . '/question/type/preg/fa_matcher/fa_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/php_preg_matcher/php_preg_matcher.php');

class qtype_preg_matching_options_test extends PHPUnit_Framework_TestCase {

    protected $question;
    protected $options;
    protected $matchers;

    /**
     * Creates basic options object for testing.
     */
    public function setUp() {
        $this->question = new qtype_preg_question();
        $this->options = new qtype_preg_matching_options();
        $this->options->modifiers = $this->question->get_modifiers(); // Usecase = true by default.
        $this->options->extensionneeded = false; // No character generation for PHP preg matcher.
        $this->options->capturesubexpressions = true;
        $this->matchers = array('php_preg_matcher', 'fa_matcher');
    }

    public function test_all_notations() {
        foreach ($this->matchers as $matchername) {
            $classname = 'qtype_preg_' . $matchername;
            $failmsg = $matchername . ' has failed';
            // Native notation.
            $options = clone $this->options;
            $options->notation = 'native';
            $matcher = new $classname("1aA", $options);
            // Simple match.
            $results = $matcher->match('1aA');
            $this->assertTrue($results->full, $failmsg);
            // Match inside string - no exact by default.
            $results = $matcher->match('01aA4');
            $this->assertTrue($results->full, $failmsg);
            // No match.
            $results = $matcher->match('1AA');
            $this->assertFalse($results->full, $failmsg);
            $results = $matcher->match('1aa');
            $this->assertFalse($results->full, $failmsg);

            // Extended notation.
            // Whitespaces not inside character classes are ignored.
            // Anything from unescaped # to line break is a comment.
            $options = clone $this->options;
            $options->notation = 'pcreextended';
            // Regex ends not in the comment.
            $matcher = new $classname("1[ ] #comment\na  A", $options);
            // Simple match.
            $results = $matcher->match('1 aA');
            $this->assertTrue($results->full, $failmsg);
            // Match inside string - no exact by default.
            $results = $matcher->match('01 aA4');
            $this->assertTrue($results->full, $failmsg);
            // No match.
            $results = $matcher->match('1 AA');
            $this->assertFalse($results->full, $failmsg);
            $results = $matcher->match('1 aa');
            $this->assertFalse($results->full, $failmsg);
            // Regex ends in the comment.
            $matcher = new $classname("1[ ] #comment\na  A#comment2", $options);
            // Simple match.
            $results = $matcher->match('1 aA');
            $this->assertTrue($results->full, $failmsg);
            // Match inside string - no exact by default.
            $results = $matcher->match('01 aA4');
            $this->assertTrue($results->full, $failmsg);
            // No match.
            $results = $matcher->match('1 AA');
            $this->assertFalse($results->full, $failmsg);
            $results = $matcher->match('1 aa');
            $this->assertFalse($results->full, $failmsg);

            // Moodle shortanswer notation.
            // Just string with * wildcard matching any number of any characters.
            $options = clone $this->options;
            $options->notation = 'mdlshortanswer';
            $matcher = new $classname("1+*aA", $options);
            // Simple match.
            $results = $matcher->match('1+ aA');
            $this->assertTrue($results->full, $failmsg);
            // Match inside string - no exact by default.
            $results = $matcher->match('01+    aA4');
            $this->assertTrue($results->full, $failmsg);
            // No match.
            $results = $matcher->match('1+??AA');
            $this->assertFalse($results->full, $failmsg);
            $results = $matcher->match('1+??aa');
            $this->assertFalse($results->full, $failmsg);
        }
    }

    public function test_exact_matching() {
        foreach ($this->matchers as $matchername) {
            $classname = 'qtype_preg_' . $matchername;
            $failmsg = $matchername . ' has failed';
            // Native notation.
            $options = clone $this->options;
            $options->exactmatch = true;
            $options->notation = 'native';
            $matcher = new $classname("1aA", $options);
            // Simple match.
            $results = $matcher->match('1aA');
            $this->assertTrue($results->full, $failmsg);
            // Match inside string - not on exact.
            $results = $matcher->match('01aA4');
            $this->assertFalse($results->full, $failmsg);
            $results = $matcher->match('1aA4');
            $this->assertFalse($results->full, $failmsg);
            $results = $matcher->match('0aA3');
            $this->assertFalse($results->full, $failmsg);
            // No match.
            $results = $matcher->match('1AA');
            $this->assertFalse($results->full, $failmsg);
            $results = $matcher->match('1aa');
            $this->assertFalse($results->full, $failmsg);

            // Extended notation.
            // Whitespaces not inside character classes are ignored.
            // Anything from unescaped # to line break is a comment.
            $options = clone $this->options;
            $options->exactmatch = true;
            $options->notation = 'pcreextended';
            // Regex ends not in the comment.
            $matcher = new $classname("1[ ] #comment\na  A", $options);
            // Simple match.
            $results = $matcher->match('1 aA');
            $this->assertTrue($results->full, $failmsg);
            // Match inside string - not on exact.
            $results = $matcher->match('01 aA4');
            $this->assertFalse($results->full, $failmsg);
            $results = $matcher->match('1 aA4');
            $this->assertFalse($results->full, $failmsg);
            $results = $matcher->match('01 aA');
            $this->assertFalse($results->full, $failmsg);
            // No match.
            $results = $matcher->match('1 AA');
            $this->assertFalse($results->full, $failmsg);
            $results = $matcher->match('1 aa');
            $this->assertFalse($results->full, $failmsg);
            // Regex ends in the comment.
            $matcher = new $classname("1[ ] #comment\na  A#comment2", $options);
            // Simple match.
            $results = $matcher->match('1 aA');
            $this->assertTrue($results->full, $failmsg);
            // Match inside string - not on exact.
            $results = $matcher->match('01 aA4');
            $this->assertFalse($results->full, $failmsg);
            $results = $matcher->match('1 aA4');
            $this->assertFalse($results->full);
            $results = $matcher->match('01 aA');
            $this->assertFalse($results->full, $failmsg);
            // No match.
            $results = $matcher->match('1 AA');
            $this->assertFalse($results->full, $failmsg);
            $results = $matcher->match('1 aa');
            $this->assertFalse($results->full, $failmsg);

            // Moodle shortanswer notation.
            // Just string with * wildcard matching any number of any characters.
            $options = clone $this->options;
            $options->exactmatch = true;
            $options->notation = 'mdlshortanswer';
            $matcher = new $classname("1+*aA", $options);
            // Simple match.
            $results = $matcher->match('1+ aA');
            $this->assertTrue($results->full, $failmsg);
            // Match inside string - not on exact.
            $results = $matcher->match('01+    aA4');
            $this->assertFalse($results->full, $failmsg);
            $results = $matcher->match('1+    aA4');
            $this->assertFalse($results->full, $failmsg);
            $results = $matcher->match('01+    aA');
            $this->assertFalse($results->full);
            // No match.
            $results = $matcher->match('1+??AA');
            $this->assertFalse($results->full, $failmsg);
            $results = $matcher->match('1+??aa');
            $this->assertFalse($results->full, $failmsg);
        }
    }

    public function test_case_insensitive_matching() {
        foreach ($this->matchers as $matchername) {
            $classname = 'qtype_preg_' . $matchername;
            $failmsg = $matchername . ' has failed';
            // Native notation.
            $options = clone $this->options;
            $options->modifiers = $this->question->get_modifiers(false);
            $options->notation = 'native';
            $matcher = new $classname("abc", $options);
            // Simple match.
            $results = $matcher->match('abc');
            $this->assertTrue($results->full, $failmsg);
            // Match inside string - no exact by default.
            $results = $matcher->match('Abc');
            $this->assertTrue($results->full, $failmsg);
            // No match.
            $results = $matcher->match('AbA');
            $this->assertFalse($results->full, $failmsg);

            // Extended notation.
            // Whitespaces not inside character classes are ignored.
            // Anything from unescaped # to line break is a comment.
            $options = clone $this->options;
            $options->modifiers = $this->question->get_modifiers(false);
            $options->notation = 'pcreextended';
            // Regex ends not in the comment.
            $matcher = new $classname("a[ ] #comment\nb  c", $options);
            // Simple match.
            $results = $matcher->match('a Bc');
            $this->assertTrue($results->full, $failmsg);
            // Match inside string - no exact by default.
            $results = $matcher->match('0A bC4');
            $this->assertTrue($results->full, $failmsg);
            // No match.
            $results = $matcher->match('A BD');
            $this->assertFalse($results->full, $failmsg);
            // Regex ends in the comment.
            $matcher = new $classname("A[ ] #comment\nB  C#comment2", $options);
            // Simple match.
            $results = $matcher->match('a bc');
            $this->assertTrue($results->full, $failmsg);
            // Match inside string - no exact by default.
            $results = $matcher->match('0A BC4');
            $this->assertTrue($results->full, $failmsg);
            // No match.
            $results = $matcher->match('A BD');
            $this->assertFalse($results->full, $failmsg);

            // Moodle shortanswer notation.
            // Just string with * wildcard matching any number of any characters.
            $options = clone $this->options;
            $options->modifiers = $this->question->get_modifiers(false);
            $options->notation = 'mdlshortanswer';
            $matcher = new $classname("a+*bc", $options);
            // Simple match.
            $results = $matcher->match('A+ bC');
            $this->assertTrue($results->full, $failmsg);
            // Match inside string - no exact by default.
            $results = $matcher->match('0a+    Bc4');
            $this->assertTrue($results->full, $failmsg);
            // No match.
            $results = $matcher->match('A+??BD');
            $this->assertFalse($results->full, $failmsg);
        }
    }
}
