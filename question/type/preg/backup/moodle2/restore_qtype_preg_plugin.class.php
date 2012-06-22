<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/poasquestion/backup/moodle2/restore_poasquestion_preg_plugin.class.php');

class restore_qtype_preg_plugin extends restore_qtype_poasquestion_plugin {

    public function process_preg($data) {
        parent::process_poasquestion($data);
    }
}
