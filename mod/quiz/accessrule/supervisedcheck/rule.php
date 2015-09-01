<?php
// This file is part of Student Access Control Kit - https://code.google.com/p/oasychev-moodle-plugins/
//
// Student Access Control Kit is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Student Access Control Kit is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Implementaton of the quizaccess_supervisedcheck plugin.
 *
 * @package     quizaccess_supervisedcheck
 * @author      Andrey Ushakov <andrey200964@yandex.ru>
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/accessrule/accessrulebase.php');
require_once($CFG->dirroot . '/blocks/supervised/sessions/sessionstate.php');

/**
 * A rule for supervised block.
 *
 * @package   quizaccess_supervisedcheck
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Andrey Ushakov <andrey200964@yandex.ru>
 */
class quizaccess_supervisedcheck extends quiz_access_rule_base {

    public function prevent_access() {
        global $DB, $COURSE, $USER;
        require_once('../../blocks/supervised/lib.php');

        // Check capabilities: teachers always have an access.
        if (has_capability('block/supervised:supervise', context_course::instance($COURSE->id))) {
            return false;
        }

        // Check if current user can start the quiz.
        $lessontypesdb = array();
        switch ($this->quiz->supervisedmode) {
            case 0:     // No check required.
                return false;
                break;
            case 1:     // Check for all lesson types.
                $lessontypesdb = $DB->get_records('block_supervised_lessontype', array('courseid' => $COURSE->id));
                // We need to add special lessontype 'Not specified' to have really all lesson types.
                $lessontypesdb[0] = new stdClass();
                $lessontypesdb[0]->id = 0;
                break;
            case 2:     // Check for custom lesson types.
                $lessontypesdb = $DB->get_records('quizaccess_supervisedcheck',
                    array('quizid' => $this->quiz->id), null, 'lessontypeid as id');
                break;
        }

        // If we are here, check is required.
        // Reorganize $lessontypesdb array.
        $lessontypes = array_keys($lessontypesdb);
        $error = "";
        // Get user's active sessions.
        $sessions = user_active_sessions($lessontypes,$error);
        
        if (!empty($sessions)) {
            return false;
        } else {
            if (strcmp($error,"iperror") == 0) {
                // We havn't active sessions with current user ip.
                return get_string('iperror', 'quizaccess_supervisedcheck');
            }
            else if (strcmp($error, "grouperror") == 0) {
                // We havn't active sessions with current user ip.
                if($this->quiz->supervisedmode == 2) {
                    return get_string('noaccess', 'quizaccess_supervisedcheck');
                } else {
                    return get_string('noaccessall', 'quizaccess_supervisedcheck');
                }
            }
            else if (strcmp($error, "lessontypeerror") == 0) {
                // We havn't active sessions with current user lesson type.
                return get_string('lessontypeerror', 'quizaccess_supervisedcheck');
            }
        }
    }

    public static function make(quiz $quizobj, $timenow, $canignoretimelimits) {
        if (empty($quizobj->get_quiz()->supervisedmode)) {
            return null;
        }

        return new self($quizobj, $timenow);
    }


    public static function add_settings_form_fields(
        mod_quiz_mod_form $quizform, MoodleQuickForm $mform) {
        global $DB, $COURSE, $PAGE, $CFG;

        $lessontypes = $DB->get_records('block_supervised_lessontype', array('courseid' => $COURSE->id));

        // Radiobuttons (modes).
        $radioarray = array();
        $radioarray[] =& $mform->createElement('radio', 'supervisedmode', '',
            get_string('checknotrequired', 'quizaccess_supervisedcheck'), 0);
        if (count($lessontypes) > 0) {  // Render 3rd mode only if we have some lesson types in course.
            $radioarray[] =& $mform->createElement('radio', 'supervisedmode', '',
                get_string('checkforall', 'quizaccess_supervisedcheck'), 1);
            $radioarray[] =& $mform->createElement('radio', 'supervisedmode', '',
                get_string('customcheck', 'quizaccess_supervisedcheck'), 2);
        } else { // No lesson types, so just it's just yes/no.
            $radioarray[] =& $mform->createElement('radio', 'supervisedmode', '',
                get_string('checkrequired', 'quizaccess_supervisedcheck'), 1);
        }
        $mform->addGroup($radioarray, 'radioar',
            get_string('allowcontrol', 'quizaccess_supervisedcheck'), '<br/>', false);

        // Checkboxes with lessontypes for 3rd mode.
        if (count($lessontypes) > 0) {
            $cbarray = array();
            foreach ($lessontypes as $id => $lessontype) {
                $cbarray[] =& $mform->createElement('advcheckbox', 'supervisedlessontype_'.$id, '', $lessontype->name);
            }
            $mform->addGroup($cbarray, 'lessontypesgroup', '', '<br/>', false);
        }

        $PAGE->requires->jquery();
        $PAGE->requires->js( new moodle_url($CFG->wwwroot . '/mod/quiz/accessrule/supervisedcheck/lib.js') );
        $PAGE->requires->css( new moodle_url($CFG->wwwroot . '/mod/quiz/accessrule/supervisedcheck/style.css') );
    }

    public static function save_settings($quiz) {
        global $DB, $COURSE;
        $oldrules = $DB->get_records('quizaccess_supervisedcheck', array('quizid' => $quiz->id));

        if ($quiz->supervisedmode == 2) {
            // Find checked lessontypes.
            $lessontypesincourse = $DB->get_records('block_supervised_lessontype', array('courseid' => $COURSE->id));
            $lessontypesinquiz = array();

            // Checks for all lesson types.
            foreach ($lessontypesincourse as $id => $lessontype) {
                if ($quiz->{'supervisedlessontype_'.$id}) {
                    $lessontypesinquiz[] = $id;
                }
            }

            // Update rules.
            if (empty($lessontypesinquiz)) {
                // If user didn't check any lessontype - add special lessontype with id = -1.
                $lessontypesinquiz[] = -1;
            }

            for ($i = 0; $i < count($lessontypesinquiz); $i++) {
                // Update an existing rule if possible.
                $rule = array_shift($oldrules);
                if (!$rule) {
                    $rule                   = new stdClass();
                    $rule->quizid           = $quiz->id;
                    $rule->lessontypeid     = -1;
                    $rule->supervisedmode   = $quiz->supervisedmode; // ...must be 2.
                    $rule->id               = $DB->insert_record('quizaccess_supervisedcheck', $rule);
                }
                $rule->lessontypeid         = $lessontypesinquiz[$i];
                $rule->supervisedmode       = $quiz->supervisedmode; // ...must be 2.
                $DB->update_record('quizaccess_supervisedcheck', $rule);
            }
            $oldrulesids = array();
            // Delete any remaining old rules.
            if(!empty($oldrules)) {
                foreach ($oldrules as $oldrule) {
                    $oldrulesids[] = $oldrule->id;
                }
                list($insql, $inparams) = $DB->get_in_or_equal($oldrulesids);
                $sqlstring = " id ";
                $sqlstring .= $insql;
                $DB->delete_records_select('quizaccess_supervisedcheck', $sqlstring, $inparams);
            }
        } else {
            // Update an existing rule if possible.
            $rule = array_shift($oldrules);
            if (!$rule) {
                $rule                   = new stdClass();
                $rule->quizid           = $quiz->id;
                $rule->lessontypeid     = -1;
                $rule->supervisedmode   = $quiz->supervisedmode;   // ...0 or 1.
                $rule->id               = $DB->insert_record('quizaccess_supervisedcheck', $rule);
            }
            $rule->lessontypeid         = -1;
            $rule->supervisedmode       = $quiz->supervisedmode;   // ...0 or 1.
            $DB->update_record('quizaccess_supervisedcheck', $rule);
            $oldrulesids = array();
            // Delete any remaining old rules.
            if(!empty($oldrules)) {
                foreach ($oldrules as $oldrule) {
                    $oldrulesids[] = $oldrule->id;
                }
                list($insql, $inparams) = $DB->get_in_or_equal($oldrulesids);
                $sqlstring = " id ";
                $sqlstring .= $insql;
                $DB->delete_records_select('quizaccess_supervisedcheck', $sqlstring, $inparams);
            }
        }
    }

    /**
     * We do not use get_settings_sql because we can have more than one rule for the quiz.
     *
     * @param int $quizid the quiz id.
     * @return array setting value name => value.
     */
    public static function get_extra_settings($quizid) {
        global $DB;
        // Load lesson type fields.
        $res = array();
        $rules = $DB->get_records('quizaccess_supervisedcheck', array('quizid' => $quizid));
        foreach ($rules as $rule) {
            $res['supervisedmode'] = $rule->supervisedmode;
            if ($rule->supervisedmode == 2) {
                $res['supervisedlessontype_'.$rule->lessontypeid] = 1;
            }
        }
        return $res;
    }

    public static function validate_settings_form_fields(array $errors,
                                                         array $data, $files, mod_quiz_mod_form $quizform) {
        global $DB, $COURSE;
        // Teacher can't use supervised access for quiz without supervised block instance in the course.
        if ($data['supervisedmode'] == 1) {
            $coursecontext = context_course::instance($COURSE->id);
            $isblockincourse = $DB->record_exists('block_instances',
                array('blockname' => 'supervised', 'parentcontextid' => $coursecontext->id));
            if (! $isblockincourse) {
                $errors['radioar'] = get_string('noblockinstance', 'quizaccess_supervisedcheck');
            }
        }

        // For custom lesson type selection mode user must check at least one lesson type.
        if ($data['supervisedmode'] == 2) {
            $isallunchecked = true;
            foreach ($data as $key => $value) {
                if (  (substr($key, 0, 21) == 'supervisedlessontype_') && ($value == 1)  ) {
                    $isallunchecked = false;
                }
            }

            if ($isallunchecked) {
                $errors['radioar'] = get_string('uncheckedlessontypes', 'quizaccess_supervisedcheck');
            }
        }

        return $errors;
    }
    
    public static function delete_settings($quiz) {
        global $DB;
        $DB->delete_records('quizaccess_supervisedcheck', array('quizid' => $quiz->id));
    }
}