<?php
$capabilities = array(
    'mod/poasassignment:view' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'guest' => CAP_ALLOW,
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
    'mod/poasassignment:havetask' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'student' => CAP_ALLOW
        )
    ),
    'mod/poasassignment:grade' => array(
        'riskbitmask' => RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
    'mod/poasassignment:managetasksfields'=> array(
        'captype'=>'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array (
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
    'mod/poasassignment:addinstance' => array(
        'captype'=>'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array (
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
    'mod/poasassignment:managetasks'=> array(
        'captype'=>'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array (
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
    'mod/poasassignment:managecriterions'=> array(
        'captype'=>'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array (
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
    'mod/poasassignment:viewownsubmission'=> array(
        'captype'=>'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array (
            'student' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
    'mod/poasassignment:finalgrades'=> array(
        'riskbitmask' => RISK_XSS,
        'captype'=>'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array (
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
    'mod/poasassignment:seeotherstasks'=> array(
        'captype'=>'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array (
            'student' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
    'mod/poasassignment:seecriteriondescription'=> array(
        'captype'=>'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array (
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
    'mod/poasassignment:seefielddescription'=> array(
        'captype'=>'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array (
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),
    'mod/poasassignment:manageanything'=> array(
        'captype'=>'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array (
            'manager' => CAP_ALLOW
        )
    )
);

