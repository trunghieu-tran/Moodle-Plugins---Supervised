<?php


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/poasassignment/backup/moodle2/restore_poasassignment_stepslib.php'); // Because it exists (must)

class restore_poasassignment_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Choice only has one structure step
        $this->add_step(new restore_poasassignment_activity_structure_step('poasassignment_structure', 'poasassignment.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('poasassignment', array('intro'), 'poasassignment');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('ASSIGNMENTVIEWBYID', '/mod/poasassignment/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('ASSIGNMENTINDEX', '/mod/poasassignment/index.php?id=$1', 'course');

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * assignment logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();

        //$rules[] = new restore_log_rule('assignment', 'add', 'view.php?id={course_module}', '{assignment}');
        //$rules[] = new restore_log_rule('assignment', 'update', 'view.php?id={course_module}', '{assignment}');
        //$rules[] = new restore_log_rule('assignment', 'view', 'view.php?id={course_module}', '{assignment}');
        //$rules[] = new restore_log_rule('assignment', 'upload', 'view.php?a={assignment}', '{assignment}');
        //$rules[] = new restore_log_rule('assignment', 'view submission', 'submissions.php.php?id={course_module}', '{assignment}');
        //$rules[] = new restore_log_rule('assignment', 'update grades', 'submissions.php.php?id={course_module}&user={user}', '{user}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        //$rules[] = new restore_log_rule('assignment', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
