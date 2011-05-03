<?php

/**
 * Description of taskgiver
 *
 * @author Arkanif
 */
abstract class taskgiver {
    public function process_before_tasks() {}
    public function get_task_extra_string(){}
    public function process_after_tasks() {}
    public function get_settings_form($id, $poasassignmentid) {}
    public function save_settings($data) {}
    public $showtasks;
    public $hassettings;
}
?>
