<?php
 
require_once($CFG->dirroot . '/blocks/role_reassign/backup/moodle2/backup_role_reassign_stepslib.php'); // Because it exists (must)
require_once($CFG->dirroot . '/blocks/role_reassign/backup/moodle2/backup_role_reassign_settingslib.php'); // Because it exists (optional)
 
/**
 * role_reassign backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */
class backup_role_reassign_block_task extends backup_block_task {
 
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
        // role_reassign only has one structure step
        $this->add_step(new backup_role_reassign_block_structure_step('role_reassign_structure', 'role_reassign.xml'));
    }
 
    // some stubs
    public function get_fileareas() {
        return array(); // No associated fileareas
    }
    public function get_configdata_encoded_attributes() {
        return array(); // No special handling of configdata
    }
    
    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     */
    static public function encode_content_links($content) {
        return $content;
    }
}