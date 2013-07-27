<?php
// This file is part of Poasquestion question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Poasquestion question type is free software: you can redistribute it and/or modify
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
 * Tests for poasquestion string class.
 *
 * @package    qtype_poasquestion
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');

class qtype_poasquestion_string_test extends PHPUnit_Framework_TestCase {

    public function test_string() {
        $str1 = new qtype_poasquestion_string('аzб');
        $str2 = new qtype_poasquestion_string('йц者');
        $str3 = new qtype_poasquestion_string($str1 . $str2);

        $this->assertTrue(is_a($str3, 'qtype_poasquestion_string'));
        $this->assertTrue($str3->string() === 'аzбйц者');
        $this->assertTrue($str3->length() === 6);
        $this->assertTrue($str3[-1] === null);
        $this->assertTrue($str3[0] === 'а');
        $this->assertTrue($str3[1] === 'z');
        $this->assertTrue($str3[2] === 'б');
        $this->assertTrue($str3[3] === 'й');
        $this->assertTrue($str3[4] === 'ц');
        $this->assertTrue($str3[5] === '者');
        $this->assertTrue($str3[6] === null);

        $str3[-1] = 'Q';
        $str3[0] = 'W';
        $str3[1] = 'E';
        $str3[4] = '者';
        $str3[6] = 'ه';
        $str3->concatenate('ab');
        $str3->concatenate(new qtype_poasquestion_string('ёя'));

        $this->assertTrue($str3->length() === 11);
        $this->assertTrue($str3[-1] === null);
        $this->assertTrue($str3[0] === 'W');
        $this->assertTrue($str3[1] === 'E');
        $this->assertTrue($str3[2] === 'б');
        $this->assertTrue($str3[3] === 'й');
        $this->assertTrue($str3[4] === '者');
        $this->assertTrue($str3[5] === '者');
        $this->assertTrue($str3[6] === 'ه');
        $this->assertTrue($str3[7] === 'a');
        $this->assertTrue($str3[8] === 'b');
        $this->assertTrue($str3[9] === 'ё');
        $this->assertTrue($str3[10] === 'я');
        $this->assertTrue($str3[11] === null);
    }

    public function test_replace() {
        $result = qtype_poasquestion_string::replace('abcdef', 'qwe', 'abcdef');
        $this->assertTrue($result === 'qwe');
        $result = qtype_poasquestion_string::replace('xyz', 'абв', 'abcdef');
        $this->assertTrue($result === 'abcdef');
        $result = qtype_poasquestion_string::replace('й', 'Ё', 'йж');
        $this->assertTrue($result === 'Ёж');
        $result = qtype_poasquestion_string::replace('abcdef', '', 'abcdef');
        $this->assertTrue($result === '');
        $result = qtype_poasquestion_string::replace('', 'qwe', 'abcdef');
        $this->assertTrue($result === 'abcdef');
        $result = qtype_poasquestion_string::replace('abcdef', 'abcdef', 'abcdef');
        $this->assertTrue($result === 'abcdef');
        $result = qtype_poasquestion_string::replace('abcdefabcdef', 'abcdef', 'abcdefabcdef');
        $this->assertTrue($result === 'abcdef');
        $result = qtype_poasquestion_string::replace('abcdef', 'abcdef', 'abcdefabcdef');
        $this->assertTrue($result === 'abcdefabcdef');
    }
}
