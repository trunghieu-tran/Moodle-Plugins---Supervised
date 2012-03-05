<?php
require_once(dirname(dirname(dirname(__FILE__))).'/model.php');
class auditor_sync {
    private static $instance = null;
    private function __construct() {

    }
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function import_task($taskrecord) {
        global $DB;

        $auditorrecord = new stdClass();
        $auditorrecord->auditorvariantid = $taskrecord->id;
        unset($taskrecord->id);
        $auditorrecord->comments = $taskrecord->comments;
        unset($taskrecord->comments);
        $auditorrecord->poasassignmenttaskid = poasassignment_model::get_instance()->add_task($taskrecord);

        $DB->insert_record('auditor_sync_tasks', $auditorrecord);

        return $auditorrecord->poasassignmenttaskid;
    }

    public function synchronize() {
        global $DB;
        $auditortasks = $DB->get_records('auditor_sync_tasks');
        foreach ($auditortasks as $auditortask) {
            
        }
    }
}