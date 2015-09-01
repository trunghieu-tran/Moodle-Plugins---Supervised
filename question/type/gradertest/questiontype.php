<?
class qtype_gradertest extends question_type {

    function name() {
        return 'gradertest';
    }

    /* Saving test data
     */
    function save_question_options($question) {
        global $DB;
        
        $DB->delete_records('question_gradertest_tests', array('questionid' => $question->id));
        for ($i = 0; $i < $question->option_repeats; $i++) {
            $gradertest = new stdClass();
            $gradertest->questionid = $question->id;
            if ($question->testdirpath[$i]) {
                // Тест как путь к папке
                $gradertest->testdirpath = $question->testdirpath[$i];
            }
            elseif ($question->testname[$i]) {
                // Тест как набор тестовых данных
                $gradertest->name = $question->testname[$i];
                $gradertest->testin = $question->testin[$i];
                $gradertest->testout = $question->testout[$i];
            }
            else {
                continue;
            }
            $DB->insert_record('question_gradertest_tests', $gradertest);
        }

        $DB->delete_records('question_gradertest_tasktest', array('questionid' => $question->id));
        foreach ($question->task as $taskID => $val) {
            if ($val == 1) {
                $tasktest = new stdClass();
                $tasktest->poasassignmenttaskid = $taskID;
                $tasktest->questionid = $question->id;

                $DB->insert_record('question_gradertest_tasktest', $tasktest);
            }
        }
        $DB->delete_records('question_gradertest', array('questionid' => $question->id));
        $gradertest = new stdClass();
        $gradertest->questionid = $question->id;
        if ($question->availablefromhome) {
            $gradertest->availablefromhome = 1;
        }
        else {
            $gradertest->availablefromhome = 0;
        }
        $DB->insert_record('question_gradertest', $gradertest);
        return null;
    }

    function get_question_options($question) {
        global $DB;
        $gradertests = $DB->get_records('question_gradertest_tests', array('questionid' => $question->id), 'id asc');
        $i = 0;
        foreach($gradertests as $gradertest) {
            $question->testdirpath[$i] = $gradertest->testdirpath;
            $question->testname[$i] = $gradertest->name;
            $question->testin[$i] = $gradertest->testin;
            $question->testout[$i] = $gradertest->testout;
            $i++;
        }
        $tasktests = $DB->get_records('question_gradertest_tasktest', array('questionid' => $question->id), 'id asc');
        foreach ($tasktests as $tasktest) {
            $question->task[$tasktest->poasassignmenttaskid] = 1;
        }
        $gradertest = $DB->get_record('question_gradertest', array('questionid' => $question->id));
        if ($gradertest->availablefromhome == 1)
            $question->availablefromhome = 1;

        return true;
    }

    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('question_gradertest_tests', array('questionid' => $questionid));
        $DB->delete_records('question_gradertest_tasktest', array('questionid' => $questionid));
        $DB->delete_records('question_gradertest', array('questionid' => $questionid));

        parent::delete_question($questionid, $contextid);
    }
}