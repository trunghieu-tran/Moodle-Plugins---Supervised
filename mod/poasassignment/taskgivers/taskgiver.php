<?php

/**
 * Description of taskgiver
 *
 * @author Arkanif
 */
abstract class taskgiver {
    public function process_before_output($cmid, $poasassignment) {}
    public function process_before_tasks($cmid, $poasassignment) {}
    public function get_task_extra_string($taskid, $cmid){}
    public function process_after_tasks() {}
    public function get_settings_form($id, $poasassignmentid) {}
    public function get_settings($poasassignmentid) {}
    public function save_settings($data) {}
    public function delete_settings($poasassignmentid) {}
    public static function has_settings() {
        return false;
    }
    public static function show_tasks() {
        return false;
    }
}
?>