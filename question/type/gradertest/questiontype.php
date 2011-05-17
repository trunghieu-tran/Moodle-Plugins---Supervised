<?
class gradertest_qtype extends default_questiontype {
    function name() {
        return 'gradertest';
    }
    /* Saving test data
     */
    function save_question_options($question) {
        global $DB;
        $gradertest = new stdClass();
        $gradertest->questionid = $question->id;
        $gradertest->text = $question->testtext;
        $gradertest->files = 42;
        if (!$DB->record_exists('gradertest', array('questionid' => $question->id))) {
            $DB->insert_record('gradertest', $gradertest);
        }
        else {
            $rec = $DB->get_record('gradertest', array('questionid' => $question->id));
            $gradertest->id = $rec->id;
            $DB->update_record('gradertest', $gradertest);
        }
        return null;
    }
    function get_question_options(&$question) {
        global $DB;
        if ($DB->record_exists('gradertest', array('questionid' => $question->id))) {
            $test = $DB->get_record('gradertest', array('questionid' => $question->id));
            $question->testtext = $test->text;
        }
    }

}
question_register_questiontype(new gradertest_qtype());