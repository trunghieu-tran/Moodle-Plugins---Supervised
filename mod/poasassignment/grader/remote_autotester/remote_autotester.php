<?php
require_once(dirname(dirname(__FILE__)).'/grader/grader.php');
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/comment/lib.php');
class remote_autotester extends grader{
    
    public function get_test_mode() {
        return POASASSIGNMENT_GRADER_INDIVIDUAL_TESTS;
    }
    
    public static function name() {
        return get_string('remote_autotester','poasassignment_remote_autotester');
    }
    public static function prefix() {
        return __CLASS__;
    }
    public static function validation($data, &$errors) {
        if(isset($data[self::prefix()]) && !isset($data['answerfile'])) {
            $errors['answerfile'] = get_string('fileanswermustbeenabled',
                                               'poasassignment_remote_autotester');
        }
        if (isset($data[self::prefix()]) && !isset($data['activateindividualtasks'])) {
            $errors['activateindividualtasks'] = get_string('individualtasksmustbeactivated',
                'poasassignment_remote_autotester');
        }
    }

    public function evaluate_attempt($attemptid) {
        $error = $this->check_remote_server();
        if ($error !== TRUE) {
            return;
        }
        global $DB;
        // get attempt files
        $attempt = $DB->get_record('poasassignment_attempts', array('id' => $attemptid));
        $assignee = $DB->get_record('poasassignment_assignee', array('id' => $attempt->assigneeid));
        $answerrec = $DB->get_record('poasassignment_answers', array('name' => 'answer_file'));
        if($submission = $DB->get_record('poasassignment_submissions', array('attemptid' => $attemptid, 'answerid' => $answerrec->id))) {
            $model = poasassignment_model::get_instance();
            $files = $model->get_files('submissionfiles', $submission->id);
            $tests = $this->get_task_tests($assignee->taskid);
            $this->send_xmlrpc($assignee->taskid, $attemptid, $files, $tests[0]);
        }
    }

    private function special_iconv($param)
    {
        return iconv("CP1251", "UTF-8", $param);
    }

    private function send_xmlrpc($taskid, $attemptid, $files, $testdirs = array(), $testcases = FALSE)
    {
        global $DB;
        $record = new stdClass();
        $record->attemptid = $attemptid;
        $record->timecreated = time();
        $record->id = $DB->insert_record('poasassignment_gr_ra', $record);

        $config = get_config("poasassignment_remote_autotester");
        if (!$config->ip || !$config->port || !$config->login || !$config->password) {
            print_error('connectionisntconfigured', 'poasassignment_remote_autotester');
            return;
        }

        require_once('kd_xmlrpc.php');
        list($success, $response) = XMLRPC_request(
            $config->ip . ':' . $config->port,
            'xmlrpc',
            'TestServer.getAttemptFiles',
            array(
                XMLRPC_prepare(md5($config->login)),
                XMLRPC_prepare(md5($config->password)),
                XMLRPC_prepare(intval($taskid)),
                XMLRPC_prepare(intval($attemptid)),
                XMLRPC_prepare($files, 'struct'),
                XMLRPC_prepare($testdirs, 'struct'),
            )
        );
        if (!$success) {
            $response =  'Error ['. $response['faultCode'] . ']: ' . $response['faultString'];
        }
        $record->serverresponse = $response;
        $DB->update_record('poasassignment_gr_ra', $record);
        if (!$success) {
            print_error('errorwhilesendingxmlrpc', 'poasassignment_remote_autotester');
        }
        if (strpos($response, "401") !== FALSE) {
            print_error('xmlrpc401', 'poasassignment_remote_autotester');
        }
    }

    private function get_my_id() {
        global $DB;
        return $DB->get_record('poasassignment_graders', array('name' => 'remote_autotester'))->id;
    }

    /**
     * Get tests for tester.
     *
     * Returns array of two elements - element 0 is an array of test dirs and array element 1 is an array of testcases.
     *
     * @param $taskid task ID
     * @return array
     */
    private function get_task_tests($taskid) {
        global $DB;
        $testdirs = array();
        $testcases = array();
        $tasktest = $DB->get_record('question_gradertest_tasktest', array("poasassignmenttaskid" => $taskid));
        if ($tasktest) {
            $tests = $DB->get_records('question_gradertest_tests', array("questionid" => $tasktest->questionid));
            foreach ($tests as $test) {
                if ($test->testdirpath) {
                    $testdirs[] = $test->testdirpath;
                }
                else {
                    $testcases[] = array("name" => $test->name, "in" => $test->testin, "out" => $test->out);
                }
            }
        }
        return array($testdirs, $testcases);
    }

    /**
     * Check remote test server via socket
     *
     * @param $site
     * @param $port
     * @return bool|string TRUE if server is on and text of error if occured
     */
    private function check_remote_server() {
        $errno = FALSE;
        $errstr = FALSE;
        $config = get_config("poasassignment_remote_autotester");
        $conn = @fsockopen($config->ip, $config->port, $errno, $errstr, 10);
        if (!$conn) {
            return '[' . $errno . '] ' . $errstr;
        }
        else {
            fclose($conn);
            return TRUE;
        }
    }
}
