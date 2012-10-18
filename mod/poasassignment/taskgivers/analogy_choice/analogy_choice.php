<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of analogy_choice
 *
 * @author Arkanif
 */
global $CFG;
require_once dirname(dirname(__FILE__)).'/taskgiver.php';
class analogy_choice extends taskgiver{

    public static function has_settings() {
        return true;
    }
    public static function show_tasks() {
        return true;
    }

    public function get_settings_form($id, $poasassignmentid) {
        return new taskgiver_form(null,
            array('id' => $id,
                'poasassignmentid' => $poasassignmentid));
    }

    public function get_settings($poasassignmentid) {
        global $DB;
        $data = new stdClass();
        $data->originalinstance = 0;
        $record = $DB->get_record('poasassignment_analogych', array('additionalid' => $poasassignmentid));
        if ($record) {
            $data->originalinstance = $record->originalid;
        }
        return $data;
    }

    public function save_settings($data){
        global $DB;
        if ($data->originalinstance != 0) {
            $record = new stdClass();
            $record->originalid = $data->originalinstance;
            $record->additionalid = $data->poasassignmentid;
            if (!$DB->record_exists('poasassignment_analogych', array('additionalid' => $record->additionalid))) {
                $DB->insert_record('poasassignment_analogych', $record);
                echo get_string('originalinstancesaved', 'poasassignmenttaskgivers_analogy_choice');
            }
            else {
                $oldrec = $DB->get_record('poasassignment_analogych', array('additionalid' => $record->additionalid));
                $record->id = $oldrec->id;
                $DB->update_record('poasassignment_analogych', $record);
                echo get_string('originalinstancesaved', 'poasassignmenttaskgivers_analogy_choice');
            }
        }
        else {
            $DB->delete_records('poasassignment_analogych', array('additionalid' => $data->poasassignmentid));
            echo get_string('originalinstancedeleted', 'poasassignmenttaskgivers_analogy_choice');
        }
    }

    public function delete_settings($poasassignmentid) {
        global $DB;
        $DB->delete_records('poasassignment_analogych', array('additionalid' => $poasassignmentid));
    }

    function process_before_tasks($cmid, $poasassignment) {
        global $USER, $DB;
        $hascaptohavetask = has_capability('mod/poasassignment:havetask', poasassignment_model::get_instance()->get_context());
        $error = poasassignment_model::get_instance()->check_dates();
        if ($hascaptohavetask && !$error) {
            if (!poasassignment_model::user_have_active_task($USER->id, $poasassignment->id)) {
                $data = $this->get_settings($poasassignment->id);
                if ($data->originalinstance > 0) {
                    $baseassignee = $DB->get_record(
                        'poasassignment_assignee',
                        array(
                            'userid' => $USER->id,
                            'poasassignmentid' => $data->originalinstance,
                            'cancelled' => 0
                        ),
                        'id, taskid'
                        );
                    if ($baseassignee->taskid < 1) {
                        print_error(
                            'errornobasetasktaken',
                            'poasassignmenttaskgivers_analogy_choice',
                            new moodle_url('/mod/poasassignment/view.php',
                                array(
                                    'id' => poasassignment_model::get_instance()->get_cm()->id,
                                    'page' => 'view')));
                    }
                    else {
                        $basetask = $DB->get_record('poasassignment_tasks', array('id' => $baseassignee->taskid), 'name');
                        $task = $DB->get_record('poasassignment_tasks', array('name' => $basetask->name, 'poasassignmentid' => $poasassignment->id));
                        if (!$task) {
                            print_error(
                                'errornotaskwithsamename',
                                'poasassignmenttaskgivers_analogy_choice',
                                new moodle_url('/mod/poasassignment/view.php',
                                    array(
                                        'id' => poasassignment_model::get_instance()->get_cm()->id,
                                        'page' => 'view')));
                        }
                        poasassignment_model::get_instance()->bind_task_to_assignee($USER->id, $task->id);
                        redirect(new moodle_url('view.php',array('id' => $cmid,'page' => 'view')));
                    }
                } else {
                    print_error(
                        'errornobaseinstanceselected',
                        'poasassignmenttaskgivers_analogy_choice',
                        new moodle_url('/mod/poasassignment/view.php',
                            array(
                                'id' => poasassignment_model::get_instance()->get_cm()->id,
                                'page' => 'view')));
                }
            }
        }

    }
}
class taskgiver_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        $poasmodel= poasassignment_model::get_instance();
        global $DB;

        $mform->addElement('header', 'header', get_string('chosebaseinstance','poasassignmenttaskgivers_analogy_choice'));


        $siblings = poasassignment_model::get_sibling_instances($instance['poasassignmentid']);
        $options = array(0 => '-');
        foreach ($siblings as $key => $instanceid) {
            // Get all instances with individual tasks mode activated
            $inst = poasassignment_model::get_poasassignment_by_id($instanceid);

            if ($inst && ($inst->flags & ACTIVATE_INDIVIDUAL_TASKS)) {
                $options[$inst->id] = '[' . $inst->id . '] ' . $inst->name;
            }
        }


        $select = $mform->addElement('select', 'originalinstance', get_string('originalinstance', 'poasassignmenttaskgivers_analogy_choice'), $options);
        $mform->addHelpButton('originalinstance', 'originalinstance', 'poasassignmenttaskgivers_analogy_choice');

        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);

        $mform->addElement('hidden', 'page', 'taskgiversettings');
        $mform->setType('page', PARAM_TEXT);

        $this->add_action_buttons(false, get_string('savechanges', 'admin'));
    }
}