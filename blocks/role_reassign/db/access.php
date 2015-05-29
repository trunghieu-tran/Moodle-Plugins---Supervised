<?php
//
// CAPABILITY NAMING CONVENTION
//
// It is important that capability names are unique. The naming convention
// for capabilities that are specific to modules and blocks is as follows:
//   [mod/block]/<plugin_name>:<capabilityname>
//
// component_name should be the same as the directory name of the mod or block.
//
// Core moodle capabilities are defined thus:
//    moodle/<capabilityclass>:<capabilityname>
//
// Examples: mod/forum:viewpost
//           block/recent_activity:view
//           moodle/site:deleteuser
//
// The variable name for the capability definitions array is $capabilities


$capabilities = array(
    'block/role_reassign:view' => array (
        
        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
			'coursecreator' => CAP_ALLOW,
            'student' => CAP_PREVENT,
            'guest' => CAP_PREVENT,
			'admin' => CAP_ALLOW
        )
    ),
    'block/role_reassign:edit' => array (
        'riskbitmask' => RISK_DATALOSS | RISK_CONFIG,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'teacher' => CAP_PREVENT,
            'editingteacher' => CAP_ALLOW,
			'coursecreator' => CAP_ALLOW,
            'student' => CAP_PREVENT,
            'guest' => CAP_PREVENT,
			'admin' => CAP_ALLOW
        )
    )
);