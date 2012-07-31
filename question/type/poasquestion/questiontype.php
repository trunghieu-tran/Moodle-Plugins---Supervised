<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/questiontypebase.php');

class qtype_poasquestion extends question_type {
    public function menu_name() {
        // Don't include this question type in the 'add new question' menu.
        return false;
    }
}
