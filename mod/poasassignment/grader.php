<?php
abstract class grader {
    
    // Переопределяется в потомках, возвращает истину, если
    // оценщик использует тесты при проверке ответов
    function has_tests() {
        return false;
    }
    
    // Возвращает 1, если тест для каждого задания имеет свой набор тестов
    // иначе 2
    function test_mode() {
        return 1;
    }
    
    // Заполняется после выполнения оценивания
    private $testresults;
    
    function set_test_mode($testMode) {
        if($testMode<0 || $testMode>3)
            return;
        $this->testMode=$testMode;
    }
    function get_test_mode() {
        return $this->testMode;
    }
    
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
    
    // Проверяет введенные данные в окне редактирования модуля
    function validation($data, &$errors) {
        return null;
    }
    
    // Производит оценку ответа $submission на задание $taskid конкретного poasassignment'a
    function evaluate($submission,$poasassignmentid,$taskid=-1) {
        return array();
    }
    
    // Отображает результаты тестирования ответа
    function show_result($resultmode) {
        // В зависимости от параметра, функция отображает:
        // * оценку
        // * статистику успешных/неуспешных тестов
        // * названия этих тестов
        // * разницу между тестовыми данными и полученными из ответа
        // * входные данные тестов
        // * сообщения программы-тестера
       
        }
        if(isset($resultmode["studentoutput"]) && $resultmode["studentoutput"]==true) {
            // print student's answer output data 
        }
        if(isset($resultmode["diff"]) && $resultmode["diff"]==true) {
            // print difference between test answer and student's answers
        }
        if(isset($resultmode["testinput"]) && $resultmode["testinput"]==true) {
            // print tests input data 
        }
        if(isset($resultmode["messages"]) && $resultmode["messages"]==true) {
            // print messages from testing program
        }        
    }
    
    // Отображает список тестов
    function show_tests($poasassignmentid,$taskid=-1){
    
    }

        //display_question_editing_page ?
        // save_question ?
        // save_question_options get_question_options ?
}