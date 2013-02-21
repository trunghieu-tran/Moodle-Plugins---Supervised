<?php
require_once(dirname(dirname(__FILE__)).'/grader/grader.php');

class simple_grader extends grader{
    
    public function get_test_mode() {
        return POASASSIGNMENT_GRADER_COMMON_TEST;
    }
    
    public static function name() {
        return get_string('simple_grader','poasassignment_simple_grader');
    }
    public static function prefix() {
        return __CLASS__;
    }
    public static function validation($data, &$errors) {
        if(isset($data[self::prefix()]) && !isset($data['answerfile']))
            $errors['answerfile'] = get_string('fileanswermustbeenabled',
                                               'poasassignment_simple_grader');
    }
    
    public function test_attempt($attemptid) {
    
        //TODO: get path to file from database
        $path = 'main.cpp';
        
        exec('cppcheck -v '.$path.' 2>&1', $arr);
        for($i = 1; $i < count($arr); $i++) {
            echo $arr[$i];
        }
        if(count($arr) == 0)
            return 100;
        else
            return 50;
    }
    
    // Заполняются после выполнения оценивания (массив simple_test_result'ов)
    private $testresults;
    private $successfultestscount;
    
    public function show_result($options) {
        //TODO: вернемся когда это можно будет увидеть, остальные флаги нужно доработать еще будет
        $html = "";
        if($options & POASASSIGNMENT_GRADER_SHOW_RATING) 
            $html += "<br>Rating : ".(100 * $successfultestscount / count($testresults));
        if($options & POASASSIGNMENT_GRADER_SHOW_NUMBER_OF_PASSED_TESTS)
            $html += "<br>Passed tests : ".$successfultestscount;
        
        foreach ($testresults as $testresult) {
            if($options & POASASSIGNMENT_GRADER_SHOW_TESTS_NAMES)
                $html += "<br>".$testresult->testname;
            if($options & POASASSIGNMENT_GRADER_SHOW_TEST_INPUT_DATA)
                $html += "<br>".$testresult->testinputdata;
        }
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
    function evaluate($submission,$poasassignmentid,$taskid=-1) {
        return array();
    }
        
    // Отображает список тестов
    function show_tests($poasassignmentid,$taskid=-1){
    
    }
}