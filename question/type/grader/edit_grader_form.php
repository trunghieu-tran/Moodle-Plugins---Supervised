<?php
/**
 * The editing form code for this question type.
 *
 * @copyright &copy; 2006 YOURNAME
 * @author YOUREMAILADDRESS
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package YOURPACKAGENAME
 *//** */

require_once($CFG->dirroot.'/question/type/edit_question_form.php');

/**
 * grader editing form definition.
 * 
 * See http://docs.moodle.org/en/Development:lib/formslib.php for information
 * about the Moodle forms library, which is based on the HTML Quickform PEAR library.
 */
class question_edit_grader_form extends question_edit_form {
    function definition_inner(&$mform) {
        echo '<br>'.__FUNCTION__;
        // TODO, add any form fields you need.
        // $mform->addElement( ... );
        $mform->addElement('header', 'showingresults', get_string('showingresults', 'qtype_grader'));
        $studentshowoptionsgrp=array();
        $studentshowoptionsgrp[] = &$mform->createElement('checkbox', 'studentshowfeedback', '', get_string('showfeedback', 'qtype_grader'));
        $studentshowoptionsgrp[] = &$mform->createElement('checkbox', 'studentshowtestinputdata', '', get_string('showtestinputdata', 'qtype_grader'));
        $studentshowoptionsgrp[] = &$mform->createElement('checkbox', 'studentshowtestoutputdata', '', get_string('showtestoutputdata', 'qtype_grader'));
        $studentshowoptionsgrp[] = &$mform->createElement('checkbox', 'studentshowdiff', '', get_string('showdiff', 'qtype_grader'));
        $studentshowoptionsgrp[] = &$mform->createElement('checkbox', 'studentshowtestsnames', '', get_string('showtestsnames', 'qtype_grader'));
        $studentshowoptionsgrp[] = &$mform->createElement('checkbox', 'studentshownumberofpassedtest', '', get_string('shownumberofpassedtest', 'qtype_grader'));
        $studentshowoptionsgrp[] = &$mform->createElement('checkbox', 'studentshowrating', '', get_string('showrating', 'qtype_grader'));
        $mform->addGroup($studentshowoptionsgrp, 'studentshowoptionsgrp', get_string('studentshowoptionsgrp', 'qtype_grader'), '<br>', false);

        $teachershowoptionsgrp=array();
        $teachershowoptionsgrp[] = &$mform->createElement('checkbox', 'teachershowfeedback', '', get_string('showfeedback', 'qtype_grader'));
        $teachershowoptionsgrp[] = &$mform->createElement('checkbox', 'teachershowtestinputdata', '', get_string('showtestinputdata', 'qtype_grader'));
        $teachershowoptionsgrp[] = &$mform->createElement('checkbox', 'teachershowtestoutputdata', '', get_string('showtestoutputdata', 'qtype_grader'));
        $teachershowoptionsgrp[] = &$mform->createElement('checkbox', 'teachershowdiff', '', get_string('showdiff', 'qtype_grader'));
        $teachershowoptionsgrp[] = &$mform->createElement('checkbox', 'teachershowtestsnames', '', get_string('showtestsnames', 'qtype_grader'));
        $teachershowoptionsgrp[] = &$mform->createElement('checkbox', 'teachershownumberofpassedtest', '', get_string('shownumberofpassedtest', 'qtype_grader'));
        $teachershowoptionsgrp[] = &$mform->createElement('checkbox', 'teachershowrating', '', get_string('showrating', 'qtype_grader'));
        $mform->addGroup($teachershowoptionsgrp, 'teachershowoptionsgrp', get_string('teachershowoptionsgrp', 'qtype_grader'), '<br>', false);

               
    }

    function set_data($question) {
        echo '<br>'.__FUNCTION__;
        // TODO, preprocess the question definition so the data is ready to load into the form.
        // You may not need this method at all, in which case you can delete it.

        // For example:
        // if (!empty($question->options)) {
        //     $question->customfield = $question->options->customfield;
        // }
        parent::set_data($question);
    }

    function validation($data) {
        echo '<br>'.__FUNCTION__;
        $errors = array();

        // TODO, do extra validation on the data that came back from the form. E.g.
        // if (/* Some test on $data['customfield']*/) {
        //     $errors['customfield'] = get_string( ... );
        // }

        if ($errors) {
            return $errors;
        } else {
            return true;
        }
    }

    function qtype() {
        return 'grader';
    }
}
?>