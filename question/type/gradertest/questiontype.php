<?
class gradertest_qtype extends default_questiontype {

    public $fileoptions = array('subdirs' => false,
                                'maxfiles' => -1,
                                'maxbytes' => 0);
    function name() {
        return 'gradertest';
    }
    /* Saving test data
     */
    function save_question_options($question) {
        global $DB;
        $gradertest = new stdClass();
        $gradertest->questionid = $question->id;
        
        if (!$DB->record_exists('question_gradertest', array('questionid' => $question->id))) {
            $gradertest->id = $DB->insert_record('question_gradertest', $gradertest);
        }
        else {
            $rec = $DB->get_record('question_gradertest', array('questionid' => $question->id));
            $gradertest->id = $rec->id;
            $DB->update_record('question_gradertest', $gradertest);
        }
        
        $DB->delete_records('question_gradertest_tests', array('gradertestid' => $gradertest->id));
        for ($i = 0; $i < $question->option_repeats; $i++) {
            if (empty($question->testname[$i])) {
                continue;
            }
            $testrec = new stdClass();
            $testrec->name = $question->testname[$i];
            $testrec->weight = $question->testweight[$i];
            $testrec->testin = $question->testin[$i];
            $testrec->testout = $question->testout[$i];
            $testrec->gradertestid = $gradertest->id;
            $DB->insert_record('question_gradertest_tests', $testrec);            
        }
        return null;
        //$this->save_testfiles($question->testfiles, $question->context, 'questiontext', $question->id);
        
        //return null;
    }
    function save_testfiles($element, $context, $area = 'questiontext', $id) {
        global $DB;
        $fs = get_file_storage();
        file_save_draft_area_files($element, 
                                   $context->id, 
                                   'question', 
                                   $area, 
                                   $id, 
                                   $this->fileoptions);
    }
    function get_question_options(&$question) {
        global $DB;
        $gradertest = $DB->get_record('question_gradertest', array('questionid' => $question->id));
        $tests = $DB->get_records('question_gradertest_tests', array('gradertestid' => $gradertest->id));
        $i = 0;
        foreach($tests as $test) {
            $question->testname[$i] = $test->name;
            $question->testin[$i] = $test->testin;
            $question->testout[$i] = $test->testout;
            $question->testweight[$i] = $test->weight;
            $i++;
        }
    }

}
question_register_questiontype(new gradertest_qtype());