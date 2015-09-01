<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '/model.php');

class submission_page extends abstract_page {
    private $mform;
    function __construct() {
    }
    function get_cap() {
        return 'mod/poasassignment:havetask';
    }
    function has_satisfying_parameters() {
        // TODO
        return true;
    }
    public function pre_view() {
        global $DB, $OUTPUT, $USER;
        $model = poasassignment_model::get_instance();
        $poasassignmentid = $model->get_poasassignment()->id;
        $this->mform = new answer_form(null, array('poasassignmentid' => $poasassignmentid, 
                                           'userid' => $USER->id,
                                           'id' => $model->get_cm()->id));
        $plugins = $model->get_plugins();
        if (has_capability('mod/poasassignment:viewownsubmission', $model->get_context())) {
            foreach($plugins as $plugin) {
                if (poasassignment_answer::used_in_poasassignment($plugin->id, $poasassignmentid)) {
                    require_once($plugin->path);
                    $poasassignmentplugin = new $plugin->name();
                    $preloadeddata = $poasassignmentplugin->get_answer_values($poasassignmentid);
                    $this->mform->set_data($preloadeddata);
                }
            }
        }
        if ($this->mform->is_cancelled()) {
            redirect(new moodle_url('view.php', array('id' => $model->get_cm()->id,'page' => 'view')), null, 0);
        }
        else {
            if ($this->mform->get_data()) {
                $data = $this->mform->get_data();
                //save data
                $assignee = $model->get_assignee($USER->id);
                $model->cash_assignee_by_user_id($USER->id);
                $attemptid = $model->save_attempt($data);
                foreach($plugins as $plugin) {
                    if(poasassignment_answer::used_in_poasassignment($plugin->id, $poasassignmentid)) {
                        require_once($plugin->path);
                        $answerplugin = new $plugin->name();
                        $answerplugin->save_submission($attemptid, $data);
                    }
                }
                // save attempt as last attempt of this assignee
                $model->assignee->lastattemptid = $attemptid;
                $DB->update_record('poasassignment_assignee', $model->assignee);
                
                // Trigger assessable_submitted event.
                $params = array(
                    'context'  => context_module::instance($model->get_cm()->id),
                    'objectid' => $attemptid
                );
                $assessable_submitted_event = \mod_poasassignment\event\assessable_submitted::create($params);
                $assessable_submitted_event->trigger();
                
                //noitify teacher if needed
                $model->email_teachers($model->assignee);
                
                $model->evaluate_attempt($attemptid);
                
                redirect(new moodle_url('view.php', 
                                        array('id'=>$model->get_cm()->id, 'page'=>'view')), 
                                        null, 
                                        0);
            }
        }
    }
    function view() {
        global $OUTPUT;
        $model = poasassignment_model::get_instance();
        echo $OUTPUT->box_start('generalbox boxaligncenter', 'intro');
        echo $model->get_grader_notes($model->assignee->id);
        $this->mform->display();
        echo $OUTPUT->box_end();
    }
    public static function display_in_navbar() {
        return false;
    }
}