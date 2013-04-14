<?php
require_once(dirname(dirname(__FILE__)).'/grader/grader.php');
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/comment/lib.php');

define("POASASSIGNMENT_REMOTE_AUTOTESTER_IGNORE", 0);
define("POASASSIGNMENT_REMOTE_AUTOTESTER_FAIL", -1);
define("POASASSIGNMENT_REMOTE_AUTOTESTER_OK", 1);
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
        poasassignment_model::disable_attempt_penalty($attemptid);
        $error = $this->check_remote_server();
        if ($error !== TRUE) {
            return;
        }
        global $DB;
        // If server is online, prepare for sending and testing
        // Disable penalty for attempt

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

    private function send_xmlrpc($taskid, $attemptid, $files, $testdirs = array(), $testcases = FALSE) {
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
     * @param $timeout timeout
     * @return bool|string TRUE if server is on and text of error if occured
     */
    private function check_remote_server($timeout = 3) {
        $errno = FALSE;
        $errstr = FALSE;
        $config = get_config("poasassignment_remote_autotester");
        $conn = @fsockopen($config->ip, $config->port, $errno, $errstr, $timeout);
        if (!$conn) {
            return '[' . $errno . '] ' . $errstr;
        }
        else {
            fclose($conn);
            return TRUE;
        }
    }

    public static function get_attempts_results($assigneeid) {
        global $DB;
        $sql = "SELECT ra.*, att.attemptnumber, att.attemptdate, att.disablepenalty, att.draft, att.final
            FROM {poasassignment_gr_ra} ra
            JOIN {poasassignment_attempts} att
            ON att.id = ra.attemptid
            JOIN {poasassignment_assignee} assign
            ON assign.id = att.assigneeid
            WHERE assign.id = $assigneeid
            ORDER BY ra.id DESC";
        $attemptresults = $DB->get_records_sql($sql);

        $sql = "SELECT testresult.*
            FROM {poasassignment_gr_ra} ra
            JOIN {poasassignment_gr_ra_tests} testresult
            ON testresult.remote_id = ra.id
            JOIN {poasassignment_attempts} att
            ON att.id = ra.attemptid
            JOIN {poasassignment_assignee} assign
            ON assign.id = att.assigneeid
            WHERE assign.id = $assigneeid
            ORDER BY testresult.test ASC";
        $testresults = $DB->get_records_sql($sql);
        foreach ($testresults as $testresult) {
            foreach ($attemptresults  as $j => $attempresult) {
                if ($attempresult->id == $testresult->remote_id)
                {
                    $attemptresults[$j]->tests[] = $testresult;
                }
            }

        }
        return $attemptresults;
    }

    /**
     * Get attempt status in human-friendly view and flag, if attempt can be graded.
     *
     * @param object $attempt attempt as object
     * @return stdClass object describing attempt status
     */
    public static function get_attempt_status($attempt) {
        $status = get_string("codeuploadfail", "poasassignment_remote_autotester");
        $finalized = false;
        if (isset($attempt->serverresponse) && $attempt->serverresponse == "200 OK") {
            $status = get_string("codeuploaded", "poasassignment_remote_autotester");
            if (isset($attempt->timecompilestarted) && $attempt->timecompilestarted) {
                $status = get_string("codeiscompiling", "poasassignment_remote_autotester");
                if (isset($attempt->timecompiled) && $attempt->timecompiled) {
                    if (isset($attempt->compiled) && $attempt->compiled == 1) {
                        $status = get_string("compiledsuccessfully", "poasassignment_remote_autotester");
                        if (isset($attempt->testsfound) && $attempt->testsfound > 0) {
                            if (isset($attempt->timeteststart) && $attempt->timeteststart) {
                                $status = get_string("testarerunning", "poasassignment_remote_autotester");
                                if (isset($attempt->tests) && $attempt->tests) {
                                    $status =
                                        get_string('testscompleted', 'poasassignment_remote_autotester') .
                                            count($attempt->tests) .
                                            ' ' .
                                            get_string('of', 'poasassignment_remote_autotester') .
                                            ' '.
                                            $attempt->testsfound;
                                    if (isset($attempt->testsfound) && $attempt->testsfound) {
                                        if ($attempt->testsfound > 0 && count($attempt->tests) == $attempt->testsfound) {
                                            $status = get_string('alltestscompleted', 'poasassignment_remote_autotester');
                                            $finalized = true;
                                        }
                                    }
                                }
                            }
                        }
                        elseif (isset($attempt->testsfound) && $attempt->testsfound == 0) {
                            $status = get_string('notestfilesfound', 'poasassignment_remote_autotester');
                        }
                    }
                    else {
                        $status = get_string("compilefailed", "poasassignment_remote_autotester");
                    }
                }
                elseif($attempt->timeclosed) {
                    $status = get_string('unexpectedfailed', 'poasassignment_remote_autotester');
                }
            }
            elseif($attempt->timeclosed) {
                $status = get_string('unexpectedfailed', 'poasassignment_remote_autotester');
            }
        }
        $res = new stdClass();
        $res->status = $status;
        $res->canbegraded = $finalized;
        return $res;
    }

    /**
     * Make a decision and grade attempt
     *
     * @param $attemptid
     */
    public static function grade_attempt($attemptid)
    {
        global $DB;
        $record = $DB->get_record('poasassignment_gr_ra', array('attemptid' => $attemptid), 'id, attemptid, testsfound');
        if (isset($record->id)) {
            $oktestscount = $DB->count_records('poasassignment_gr_ra_tests', array('remote_id' => $record->id, 'testpassed' => 1));
            if ($record->testsfound > $oktestscount) {
                // fail attempt
                self::set_result($attemptid, 0);
            }
            else {
                // submit attempt
                self::set_result($attemptid, 1);
            }
        }
    }

    /**
     * Update attempt and RA attempt, enable penalty and put 1 or 0 in
     * `result` field
     *
     * @param object $raattempt RA attempt
     * @param $result 0 to fail test, other value will submit it
     */
    public static function set_result($attemptid, $result) {
        global $DB;
        $raattempt = $DB->get_record('poasassignment_gr_ra', array('attemptid' => $attemptid), 'id, result');
        if ($result)
            $result = 1;
        $raattempt->result = $result;
        $DB->update_record('poasassignment_gr_ra', $raattempt);

        $attempt = new stdClass();
        $attempt->id = $attemptid;
        $attempt->disablepenalty = 0;
        $DB->update_record('poasassignment_attempts', $attempt);
    }

    /**
     * Get RA notes - if server is offline or not
     *
     * @param $assigneeid int assignee id
     * @return bool
     */
    public function get_grader_notes($assigneeid) {
        if ($this->check_remote_server(1) === true) {
            $img = '<img src="/mod/poasassignment/grader/remote_autotester/pix/green.gif" alt="online"/>';
            $status = get_string('serverisonline', 'poasassignment_remote_autotester');
        }
        else {
            $img = '<img src="/mod/poasassignment/grader/remote_autotester/pix/grey.gif" alt="offline"/>';
            $status = get_string('serverisoffline', 'poasassignment_remote_autotester');
        }
        return '<div class="ra-status">' . $img . ' ' . $status . '</div>';
    }

    public static function get_diff_comment($diff, $strudentoutarray, $testoutarray) {
        if (is_array($diff)) {
            $s = array();
            foreach ($strudentoutarray as $studentout) {
                $s = array_merge($s, explode(' ', $studentout));
            }
            $t = array();
            foreach ($testoutarray as $testout) {
                $t = array_merge($t, explode(' ', $testout));
            }
            if (count($s) > 0 && count($t) > 0) {
                $unordereddiff = array_diff($s, $t);
                if (count($unordereddiff) == 0) {
                    return '(' . get_string('differentorder', 'poasassignment_remote_autotester') . ') ';
                }
            }
        }
        return FALSE;
    }

    public static function put_rating($poasassignmentid, $assigneeid) {
        $model = poasassignment_model::get_instance();
        // to aviod problems with submission page, grade passed attempt AND last attempt
        $statistics = self::get_grade_statistics(self::get_attempts_results($assigneeid));
        if ($statistics['firstpassedattempt'] && $statistics['lastattempt']) {
            // If has passed attempt - update graderbook
            $criterions = $model->get_criterions($poasassignmentid, self::get_my_id());
            $criterionids = array();
            foreach ($criterions as $criterion) {
                $criterionids[] = $criterion->id;
            }
            $model->delete_rating_values($criterionids, $statistics['firstpassedattempt']);
            $model->delete_rating_values($criterionids, $statistics['lastattempt']);
            foreach ($criterions as $criterion) {
                $model->put_rating($criterion->id, $statistics['firstpassedattempt'], 100, '');
                $model->put_rating($criterion->id, $statistics['lastattempt'], 100, '');
            }
            $model->recalculate_rating($assigneeid);
        }
    }

    /**
     * Get statistics array to display in table in top of the page
     *
     * @param $attemptsresult array of results to analyze
     * @return array statistics
     */
    public static function get_statistics($attemptsresult, $assigneeid) {
        $assignee = poasassignment_model::get_instance()->assignee_get_by_id($assigneeid);
        $statistics = array();
        if (isset($assignee)) {
            $statistics['assignee'] = $assignee->firstname . ' ' . $assignee->lastname;
        }
        $statistics['firstpassedattempt'] = '-';
        $statistics['failedtestattempts'] = 0;
        $statistics['totalpenalty'] = 0;
        $statistics['totaltestattempts'] = count($attemptsresult);
        $statistics['ignoredtestattempts'] = 0;
        $statistics['bestresult'] = false;
        $statistics['worstresult'] = false;

        $i = count ($attemptsresult);
        foreach ($attemptsresult as $attemptresult) {
            if (isset($attemptresult->disablepenalty) && $attemptresult->disablepenalty == 1) {
                $statistics['ignoredtestattempts']++;
            }
            else {
                if ($attemptresult->result == 1) {
                    $statistics['firstpassedattempt'] = $i;
                }
                elseif ($attemptresult->result == 0) {
                    $statistics['failedtestattempts']++;
                }
                $oktest = 0;
                foreach ($attemptresult->tests as $test) {
                    if ($test->testpassed == 1) {
                        $oktest++;
                    }
                }
                if ($statistics['worstresult'] === false || $oktest < $statistics['worstresult']) {
                    $statistics['worstresult'] = $oktest;
                }
                if ($statistics['bestresult'] === false || $oktest > $statistics['bestresult']) {
                    $statistics['bestresult'] = $oktest;
                }
            }
            $i--;
        }
        $penalty = poasassignment_model::get_instance()->poasassignment->penalty;
        if ($statistics['failedtestattempts'] > 0 && $penalty > 0) {
            $statistics['totalpenalty'] = $statistics['failedtestattempts'] * $penalty;
        }
        return $statistics;
    }
    /**
     * Get grade statistics array
     *
     * @param $attemptsresult array of results to analyze
     * @return array statistics
     */
    public static function get_grade_statistics($attemptsresult) {
        $statistics = array();
        $statistics['firstpassedattempt'] = false;
        $statistics['penalty'] = 0;
        $statistics['lastattempt'] = false;

        foreach ($attemptsresult as $attemptresult) {
            if ($statistics['lastattempt'] === false)
                $statistics['lastattempt'] = $attemptresult->attemptid;
            if (!isset($attemptresult->disablepenalty) || $attemptresult->disablepenalty == 0) {
                if ($attemptresult->result == 1) {
                    $statistics['firstpassedattempt'] = $attemptresult->attemptid;
                }
                elseif ($attemptresult->result == 0) {
                    $statistics['penalty']++;
                }
            }
        }
        $penalty = poasassignment_model::get_instance()->poasassignment->penalty;
        if ($statistics['penalty'] > 0 && $penalty > 0) {
            $statistics['penalty'] = $statistics['penalty'] * $penalty;
        }
        return $statistics;
    }
}