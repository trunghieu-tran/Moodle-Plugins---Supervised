<?php
require_once(dirname(dirname(__FILE__)).'\grader\grader.php');

class autotester extends grader{
    
    public function get_test_mode() {
        return POASASSIGNMENT_GRADER_INDIVIDUAL_TESTS;
    }
    
    public static function name() {
        return get_string('autotester','poasassignment_autotester');
    }
    public static function prefix() {
        return __CLASS__;
    }
    public static function validation($data, &$errors) {
        if(isset($data[self::prefix()]) && !isset($data['answertext']))
            $errors['answertext'] = get_string('textanswermustbeenabled',
                                               'poasassignment_autotester');
        //TODO проверить режим инд. заданий
    }
    
    public function test_attempt($attemptid) {
        global $DB;
        
        // step 1: compile student's program
        $textanswerrec = $DB->get_record('poasassignment_answers', array('name' => 'answer_text'));
        if($textanswerrec) {
            $submission = $DB->get_record('poasassignment_submissions', array('attemptid' => $attemptid, 'answerid' => $textanswerrec->id));
            //echo $submission->value;
            $f = fopen('grader\autotester\attempts\attempt' . $attemptid . '.cpp', 'w+');
            fwrite($f, $submission->value);
            fclose($f);
            
            $runf = fopen('grader\autotester\runattempt' . $attemptid . '.bat', 'w+');
            $text = 'cd grader\autotester';  
            $text .= "\n";
            $text .= 'call C\vcvarsall.bat';
            $text .= "\n";
            $text .= 'C\bin\cl.exe ';
            $text .= '/Feattempts\attempt' . $attemptid . '.exe ';
            $text .= '/Foattempts\attempt' . $attemptid . '.obj ';
            $text .= '/Od /D "WIN32" /D "_DEBUG" /D "_CONSOLE" /D "_UNICODE" /D "UNICODE" /Gm /EHsc /RTC1 /MDd /W3 /nologo /ZI /TP /errorReport:prompt ';
            $text .= 'attempts\attempt' . $attemptid . '.cpp ';
            $text .= "\n";
            fwrite($runf, $text);
            fclose($runf);            
        }
        exec('Call grader\autotester\runattempt' . $attemptid . '.bat');
        // step 2: create test files
        
        // step 2.1 get task id
        $attempt = $DB->get_record('poasassignment_attempts', array('id' => $attemptid));
        $assignee = $DB->get_record('poasassignment_assignee', array('id' => $attempt->assigneeid));
        
        // step 2.2 get grader tests
        
        if(! $rec = $DB->get_record('poasassignment_gr_autotester', array('taskid' => $assignee->taskid))) {
            return 100;
        }
        $gradertestrec = $DB->get_record('question_gradertest', array('questionid' => $rec->questionid));
        $gradertests = $DB->get_records('question_gradertest_tests', array('gradertestid' => $gradertestrec->id));
        
        $this->create_test_files($gradertests, 'grader\autotester\attempts\tests\\', $attemptid);
        
        // step 3: call each test and update testing result table
        $results = $this->run_tests($gradertests, 'grader\autotester\attempts\tests\\', $attemptid);
        
        // step 4: compare student's output and test's output and produce grade
        $totalweight = 0;
        foreach($gradertests as $test) {
            $totalweight += $test->weight;
        }
        $grade = 0;
        foreach ($results as $result) {
            if($result->studentout == $gradertests[$result->testid]->testout) {
                $grade += 100 * ($gradertests[$result->testid]->weight) / ($totalweight);
            }
            //else {
            //    echo $result->studentout;
            //    echo '!=';
            //    echo $gradertests[$result->testid]->testout;
            //}
        }
        $this->clean_files($attemptid, 'grader\autotester\attempts\tests\\', $gradertests);
        return $grade;
    }
    private function run_tests($tests, $path, $attemptid) {
        global $DB;
        $testresults = array();
        //$tests = array_reverse($tests);
        foreach($tests as $test) {
            $out = array();
            $command = 'grader\autotester\attempts\attempt'. 
                        $attemptid . 
                        '.exe';
            $command .= '<';
            $command .= $path . 
                        'test_' . 
                        $test->id . 
                        '_' . 
                        $attemptid . 
                        '.txt';
            exec($command, $out);
            $testresult = new stdClass();
            $testresult->testid = $test->id;
            $testresult->attemptid = $attemptid;
            if(count($out) > 1) {
                $testresult->studentout = '';
                foreach ($out as $outline) {
                    $testresult->studentout .= $outline;
                    $testresult->studentout .= "\n";
                }
            }
            else {  
                $testresult->studentout = $out[0];
            }
            
            if($testresult->studentout == $test->testout) {
                $testresult->testpassed = 1;
            }
            else {
                $testresult->testpassed = 0;
            }
            $testresult->id = $DB->insert_record('poasassignment_gr_at_res', $testresult);
            $testresults[] = $testresult;
        }
        return $testresults;
    }
    private function create_test_files($tests, $path, $attemptid) {
        foreach($tests as $test) {
            $f = fopen($path . 
                    'test_' . 
                    $test->id . 
                    '_'. 
                    $attemptid .
                    '.txt', 'w+');
                    
            fwrite($f, $test->testin);
            fclose($f);
        }
    }
    private function clean_files($attemptid, $path, $tests) {
        unlink('grader\autotester\attempts\attempt' . $attemptid . '.cpp');
        unlink('grader\autotester\vc90.idb');
        unlink('grader\autotester\vc90.pdb');
        unlink('grader\autotester\runattempt' . $attemptid . '.bat');
        unlink('grader\autotester\attempts\attempt' . $attemptid . '.exe');
        unlink('grader\autotester\attempts\attempt' . $attemptid . '.obj');
        unlink('grader\autotester\attempts\attempt' . $attemptid . '.ilk');
        unlink('grader\autotester\attempts\attempt' . $attemptid . '.pdb');
        unlink('grader\autotester\attempts\attempt' . $attemptid . '.exe.manifest');
        
        foreach($tests as $test) {
            unlink($path . 'test_' . $test->id . '_'. $attemptid . '.txt');
        }
    }
    
    // Заполняются после выполнения оценивания (массив simple_test_result'ов)
    private $testresults;
    private $successfultestscount;
    
    public function show_result($options) {
    }
    
    // TODO: работа с тестами когда ими займусь ближе
        
    // Редактирование тестов( Отвечает за добавление новых тестов и редактирование существующих)
    function edit_tests($tests) {
        return null;
    }
    // Отключает тест
    function turn_off_test($testid) {
        return null;
    }
    // Полностью удаляет тест
    function delete_test($testid) {
        return null;
    }
    
    // Экспорт тестов
    function tests_export($exportParams) {
        return null;
    }
    
    // Импорт тестов
    function tests_import($importParams) {
        return null;
    }
        
    // Производит оценку ответа $submission на задание $taskid конкретного poasassignment'a
    function evaluate($submission, $poasassignmentid, $taskid = -1) {
        return array();
    }
        
    // Отображает список тестов
    function show_tests($poasassignmentid, $taskid=-1){
    
    }
    
    public static function show_settings($mform, $usedgraderid, $poasassignmentid) {
        global $DB;
        $tasksrecs = $DB->get_records('poasassignment_tasks', array('poasassignmentid' => $poasassignmentid));
        $testrecs = $DB->get_records('question_gradertest');
        $tests = array();
        foreach($testrecs as $testrec) {
            $question = $DB->get_record('question', array('id' => $testrec->questionid));
            $tests[$question->id] = $question->name;
        }
        foreach($tasksrecs as $taskrec) {
            $mform->addElement('select', 'autotester_task' . $taskrec->id, $taskrec->name, $tests);
        }
    }
    public static function save_settings($data, $poasassignmentid) {
        global $DB;
        $tasksrecs = $DB->get_records('poasassignment_tasks', array('poasassignmentid' => $poasassignmentid));
        foreach ($tasksrecs as $taskrec) {
            $DB->delete_records('poasassignment_gr_autotester', array('taskid' => $taskrec->id));
            $rec = new stdClass();
            $rec->taskid = $taskrec->id;
            $name = 'autotester_task' . $taskrec->id;
            $rec->questionid = $data->$name;
            $DB->insert_record('poasassignment_gr_autotester', $rec);
        }
    }
    public static function get_settings($poasassignmentid) {
        global $DB;
        $recs = $DB->get_records('poasassignment_gr_autotester', array());
        $data = array();
        foreach($recs as $rec) {
            $data['autotester_task' . $rec->taskid] = $rec->questionid;
        }
        return $data;
    }
    
    function have_test_results($attemptid) {
        global $DB;
        return $DB->record_exists('poasassignment_gr_at_res', array('attemptid' => $attemptid));
    }
    
    function show_test_results($attemptid, $context) {
        global $USER, $DB, $OUTPUT;
        $results = $DB->get_records('poasassignment_gr_at_res', array('attemptid' => $attemptid));
        $html = '';
        foreach ($results as $result) {
            // TODO capability
            $test = $DB->get_record('question_gradertest_tests', array('id' => $result->testid));
            $html .= $OUTPUT->heading(get_string('testname', 'poasassignment_autotester') . ' : ' . $test->name);
            $html .= '<b><big>' . get_string('testin', 'poasassignment_autotester') . '</big></b>';
            $html .= '<br>' . $test->testin;
            $html .= '<b><big>' . get_string('testout', 'poasassignment_autotester') . '</big></b>';
            $html .= '<br>' . $test->testout;
            $html .= '<br><b><big>' . get_string('studentout', 'poasassignment_autotester') . '</big></b>';
            
            if($test->testout == $result->studentout) {
                $html .= '<br><div style="background : LIME">' . $result->studentout . '</div><br>';
                $html .= '<b>' . get_string('testpassed', 'poasassignment_autotester') . '</b>';
            }
            else {
                $html .= '<br><div style="background : RED">' . $result->studentout . '</div><br>';
                $html .= '<b>' . get_string('testnotpassed', 'poasassignment_autotester') . '</b>';
            }
            $html .= '<br>';
        }
        $html = str_replace("\n", '<br>', $html);
        return $html;
    }
}