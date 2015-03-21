<?php
// This file is part of CorrectWriting question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// CorrectWriting question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// CorrectWriting is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with CorrectWriting.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines an implementation of mistakes, that are determined by lexical analyzer
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author  Oleg Sychev, Dmitriy Mamontov,Birukova Maria Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/question/type/correctwriting/response_mistakes.php');

// A marker class to indicate errors from lexical analyzer. We need them to indicate
// what lexemes was corrected by analyzer.
class qtype_correctwriting_lexical_mistake extends qtype_correctwriting_response_mistake {

    public function mistake_key() {
        return '';//TODO - implement actually
    }
}

class qtype_correctwriting_scanning_mistake extends qtype_correctwriting_lexical_mistake {

}

?>