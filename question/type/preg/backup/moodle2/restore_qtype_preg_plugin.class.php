<?php

defined('MOODLE_INTERNAL') || die();

class restore_qtype_preg_plugin extends restore_qtype_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level
     */
    protected function define_question_plugin_structure() {

        $paths = array();

        // This qtype uses question_answers, add them
        $this->add_question_question_answers($paths);

        // Add own qtype stuff
        $elename = 'preg';
        // we used get_recommended_name() so this works
        $elepath = $this->get_pathfor('/preg');
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths
    }

    /**
     * Process the qtype/preg element
     */
    public function process_preg($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ? true : false;

        // If the question has been created by restore, we need to create its qtype_preg too
        if ($questioncreated) {
            // Adjust some columns
            $data->question = $newquestionid;
            // Map sequence of question_answer ids
            $answersarr = explode(',', $data->answers);
            foreach ($answersarr as $key => $answer) {
                $answersarr[$key] = $this->get_mappingid('question_answer', $answer);
            }
            $data->answers = implode(',', $answersarr);
            // Insert record
            $newitemid = $DB->insert_record('qtype_preg', $data);
            // Create mapping
            $this->set_mapping('qtype_preg', $oldid, $newitemid);
        }
    }
}
