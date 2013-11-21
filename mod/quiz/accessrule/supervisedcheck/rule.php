<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
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
 * Implementaton of the quizaccess_supervisedcheck plugin.
 *
 * @package   quizaccess_supervisedcheck
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Andrey Ushakov <andrey200964@yandex.ru>
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/accessrule/accessrulebase.php');


/**
 * A rule for supervised block.
 *
 * @package   quizaccess_supervisedcheck
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Andrey Ushakov <andrey200964@yandex.ru>
 */
class quizaccess_supervisedcheck extends quiz_access_rule_base {

    public static function add_settings_form_fields(
        mod_quiz_mod_form $quizform, MoodleQuickForm $mform) {
        global $DB, $COURSE;

        //Radiobuttons
        $radioarray=array();
        $radioarray[] =& $mform->createElement('radio', 'radiosupervised', '', 'No', 0);
        $radioarray[] =& $mform->createElement('radio', 'radiosupervised', '', 'Yes, for all lessontypes (include "Not specified" lessontype)', 1);
        $radioarray[] =& $mform->createElement('radio', 'radiosupervised', '', 'Yes, for custom lessontype list:', 2);
        $mform->addGroup($radioarray, 'radioar', 'Allow supervised access?', '<br/>', false);

        //print_object($radioarray);
        // Lessontypes checkboxes
        $mform->addElement('advcheckbox', 'supervisedlessontype_0', '', get_string("notspecified", 'block_supervised'));
        $lessontypes = $DB->get_records('block_supervised_lessontype', array('courseid'=>$COURSE->id));
        foreach($lessontypes as $id=>$lessontype){
            $mform->addElement('advcheckbox', 'supervisedlessontype_'.$id, '', $lessontype->name);
        }
    }

    public static function save_settings($quiz) {
        global $DB;
        //print_object($quiz);
        /*if (empty($quiz->honestycheckrequired)) {
            $DB->delete_records('quizaccess_honestycheck', array('quizid' => $quiz->id));
        } else {
            if (!$DB->record_exists('quizaccess_honestycheck', array('quizid' => $quiz->id))) {
                $record = new stdClass();
                $record->quizid = $quiz->id;
                $record->honestycheckrequired = 1;
                $DB->insert_record('quizaccess_honestycheck', $record);
            }
        }*/
    }
}
