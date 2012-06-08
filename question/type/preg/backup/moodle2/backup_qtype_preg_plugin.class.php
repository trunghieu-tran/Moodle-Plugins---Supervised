<?php

defined('MOODLE_INTERNAL') || die();

class backup_qtype_preg_plugin extends backup_qtype_plugin {

    /**
     * Returns the qtype information to attach to question element
     */
    protected function define_question_plugin_structure() {

        // Define the virtual plugin element with the condition to fulfill
        $plugin = $this->get_plugin_element(null, '../../qtype', 'preg');

        // Create one standard named plugin element (the visible container)
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // connect the visible container ASAP
        $plugin->add_child($pluginwrapper);

        // This qtype uses standard question_answers, add them here
        // to the tree before any other information that will use them
        $this->add_question_question_answers($pluginwrapper);

        // Now create the qtype own structures
        $preg = new backup_nested_element('preg', array('id'), array('answers', 'usecase', 'correctanswer', 'exactmatch', 'usehint', 'hintpenalty', 'hintgradeborder', 'engine', 'notation'));

        // Now the own qtype tree
        $pluginwrapper->add_child($preg);

        // set source to populate the data
        $preg->set_source_table('qtype_preg', array('question' => backup::VAR_PARENTID));

        // don't need to annotate ids nor files

        return $plugin;
    }
}
