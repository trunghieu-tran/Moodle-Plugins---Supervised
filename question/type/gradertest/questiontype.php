<?
class gradertest_qtype extends default_questiontype {

    public $fileoptions = array(
        'subdirs' => false,
        'maxfiles' => -1,
        'maxbytes' => 0,
        );
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
        $this->save_testfiles($question->testfiles, $question->context, 'questiontext', $question->id);
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
        if ($DB->record_exists('gradertest', array('questionid' => $question->id))) {
            $test = $DB->get_record('gradertest', array('questionid' => $question->id));
            $question->testtext = $test->text;
            
            //$draftitemid = file_get_submitted_draft_itemid('testfiles');
            //file_prepare_draft_area($draftitemid, $question->context->id, 'question', 'gradertests', 0, array('subdirs'=>true));
            //$question->testfiles = $draftitemid;
        }
    }

}
question_register_questiontype(new gradertest_qtype());