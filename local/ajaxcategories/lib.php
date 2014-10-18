<?php

defined('MOODLE_INTERNAL') || die;


function local_ajaxcategories_extends_settings_navigation(settings_navigation $nav, context $context) {
    $coursenode = $nav->get('courseadmin');
    if ($coursenode) {
    	$params = array();
    	if ($context->contextlevel == CONTEXT_COURSE) {
        	$params = array('courseid'=>$context->instanceid);
    	}
        $coursenode->add(get_string('pluginname', 'local_ajaxcategories'), new moodle_url('/local/ajaxcategories/index.php', $params), navigation_node::TYPE_SETTING);
    }
}
