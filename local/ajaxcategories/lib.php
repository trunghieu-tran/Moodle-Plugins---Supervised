<?php

defined('MOODLE_INTERNAL') || die;


function local_ajaxcategories_extends_settings_navigation(settings_navigation $nav, context $context) {
    $coursenode = $nav->get('courseadmin');
    if ($coursenode) {
        //var_dump($coursenode);
	   $questionbank = $coursenode->find(12, navigation_node::TYPE_CONTAINER);
        if ($questionbank) {
    	   $params = array();
    	   if ($context->contextlevel == CONTEXT_COURSE) {
        	   $params = array('courseid'=>$context->instanceid);
    	   }
            $questionbank->add(get_string('pluginname', 'local_ajaxcategories'), new moodle_url('/local/ajaxcategories/index.php', $params), navigation_node::TYPE_SETTING);
        }
    }
}
