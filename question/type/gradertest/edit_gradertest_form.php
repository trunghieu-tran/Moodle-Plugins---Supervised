<?php

require_once($CFG->dirroot.'/question/type/edit_question_form.php');

class qtype_gradertest_edit_form extends question_edit_form {
    function definition_inner($mform) {
        global $DB;

        $mform->removeElement('defaultmark');

        $mform->removeElement('generalfeedback');
        $mform->addElement('hidden', 'generalfeedback', 'generalfeedback');

        $mform->removeElement('questiontext');
        $mform->addElement('hidden', 'questiontext', 'questiontext');

        $mform->addHelpButton('questiontext', 'tasktext', 'qtype_gradertest');

        $mform->addElement('checkbox', 'availablefromhome', get_string('availablefromhome', 'qtype_gradertest'));

        $repeatarray = array();
        $repeatarray[] = $mform->createElement('header', 'testdata', get_string('testdata', 'qtype_gradertest'));
        $repeatarray[] = $mform->createElement('text', 'testdirpath', get_string('testdirpath', 'qtype_gradertest'));
        $repeatarray[] = $mform->createElement('static', 'or', '', '<b>' . get_string('or', 'qtype_gradertest') . '</b>');
        $repeatarray[] = $mform->createElement('text', 'testname', get_string('testname', 'qtype_gradertest'));
        $repeatarray[] = $mform->createElement(
            'textarea',
            'testin',
            get_string('testin', 'qtype_gradertest'),
            array('cols' => '40', 'rows' => '5')
        );
        $repeatarray[] = $mform->createElement(
            'textarea',
            'testout',
            get_string('testout', 'qtype_gradertest'),
            array('cols' => '40', 'rows' => '5')
        );
        $repeateoptions = array();
        $repeatno = 2;

        if (isset($this->question->id)) {
            $repeatno = $DB->count_records('question_gradertest_tests', array('questionid' => $this->question->id)) + 1;
        }

        for ($i = 0; $i < $repeatno + 2; $i++)
        {
            // Нельзя задать путь к папке с тестом, если заданы сами тестовые наборы
            $mform->disabledIf('testdirpath[' . $i . ']', 'testname[' . $i . ']', 'neq', '');
            $mform->disabledIf('testdirpath[' . $i . ']', 'testin[' . $i . ']', 'neq', '');
            $mform->disabledIf('testdirpath[' . $i . ']', 'testout[' . $i . ']', 'neq', '');

            // Нельзя задать тест если задан путь к папке с тестом
            $mform->disabledIf('testname[' . $i . ']', 'testdirpath[' . $i . ']', 'neq', '');
            $mform->disabledIf('testin[' . $i . ']', 'testdirpath[' . $i . ']', 'neq', '');
            $mform->disabledIf('testout[' . $i . ']', 'testdirpath[' . $i . ']', 'neq', '');
        }
        
        $this->repeat_elements($repeatarray, 
            $repeatno,
            $repeateoptions,
            'option_repeats',
            'option_add_fields',
            2);


        $poasassignments = self::get_poasassignment_instances_and_tasks();

        foreach ($poasassignments as $i => $poasassignment) {
            $mform->addElement('header', 'poasassignmenttask' . $i, $poasassignment['name']);
            $mform->addElement('static', 'poasassignmenttask' . $i . 'static', get_string('poasassignmentid', 'qtype_gradertest'), '<b>' . $poasassignment['name'] . '</b><br>');
            $columncount = 4;
            $maxlength = 0;
            foreach ($poasassignment['tasks'] as $task) {
                if (strlen($task) > $maxlength)
                    $maxlength = strlen($task);
            }
            $poasassignment['tasks'] = array_chunk($poasassignment['tasks'], $columncount, true);
            foreach ($poasassignment['tasks'] as $group) {
                $elementsgroup = array();
                foreach ($group as $j => $task) {
                    $name = str_pad('_' . $task . '_', $maxlength + 8, '_');
                    $elementsgroup[] = $mform->createElement('checkbox', 'task[' . $j . ']', $task, str_replace('_', '&nbsp;', $name));
                }
                $mform->addGroup($elementsgroup);
            }
        }
    }

    function validation($data, $files) {
        $errors = array();

        $pattern = '/^([a-zA-Z0-9_]+\/)+$/';
        // Провести валидацию введенных путей
        for ($i = 0; $i < $data['option_repeats']; $i++) {
            if ($data['testdirpath'][$i]) {
                // Тест задан как путь к папке на сервере
                if (!preg_match($pattern, $data['testdirpath'][$i])) {
                    $errors['testdirpath[' . $i . ']'] = get_string('testdirpathformat', 'qtype_gradertest');
                }
            }
            if ($data['testname'][$i] || $data['testin'][$i] || $data['testout'][$i]) {
                // Тест задан как набор тестовых файлов

                // Проверить, что все данные введены
                if (!$data['testname'][$i])
                    $errors['testname[' . $i . ']'] = get_string('completetestdata', 'qtype_gradertest');

                if (!$data['testin'][$i])
                    $errors['testin[' . $i . ']'] = get_string('completetestdata', 'qtype_gradertest');

                if (!$data['testout'][$i])
                    $errors['testout[' . $i . ']'] = get_string('completetestdata', 'qtype_gradertest');
            }
        }

        if ($errors) {
            return $errors;
        } else {
            return true;
        }
    }

    function qtype() {
        return 'gradertest';
    }

    private static function get_poasassignment_instances_and_tasks() {
        global $DB;
        $poasassignments = array();
        $dbman = $DB->get_manager();
        if ($dbman->table_exists('poasassignment')) {
            $instances = $DB->get_records('poasassignment', null, 'name asc, id asc', 'id, name, course');
            $tasks = $DB->get_records('poasassignment_tasks', null, 'name asc, id asc', 'id, name, poasassignmentid');

            $courses = array();
            foreach ($instances as $instance) {
                $courses[] = $instance->course;
            }
            $courses = array_unique($courses);

            $inorequal = $DB->get_in_or_equal($courses);
            $sql = 'SELECT id, shortname from {course} where id ' . $inorequal[0] . 'ORDER BY shortname asc, id asc';
            $courses = $DB->get_records_sql($sql, $inorequal[1]);

            foreach ($instances as $instance) {
                $poasassignments[$instance->id]['name'] = $courses[$instance->course]->shortname . ': ' . $instance->name;
            }
            foreach ($tasks as $task) {
                $poasassignments[$task->poasassignmentid]['tasks'][$task->id] = $task->name . ' [' . $task->id . ']';
            }

            return $poasassignments;
        }
        return array();
    }
}
?>