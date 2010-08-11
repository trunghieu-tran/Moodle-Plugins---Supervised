<?php  //$Id: upgrade.php,v 1.2.2.2 2009/08/31 16:37:52 arborrow Exp $

// This file keeps track of upgrades to 
// the preg qtype plugin
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

function xmldb_qtype_preg_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result=true;
    if ($result && $oldversion < 2010072201) {

    /// Define field exactmatch to be added to question_preg
        $table = new XMLDBTable('question_preg');
        $field = new XMLDBField('exactmatch');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null, null, '0', 'rightanswer','usehint');
        /// Launch add field exactmatch
        $result = $result && add_field($table, $field);
        /// Define field usehint to be added to question_preg
        $field = new XMLDBField('usehint');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null, null, '0', 'exactmatch', 'hintpenalty');
        /// Launch add field usehint
        $result = $result && add_field($table, $field);
        /// Define field hintpenalty to be added to question_preg
        $field = new XMLDBField('hintpenalty');
        $field->setAttributes(XMLDB_TYPE_CHAR, '3', null, XMLDB_NOTNULL, null, null, null, '0', 'usehint');
        /// Launch add field hintpenalty
        $result = $result && add_field($table, $field);
    }


    return $result;

/// if ($result && $oldversion < YYYYMMDD00) { //New version in version.php
///     $result = result of "/lib/ddllib.php" function calls
/// }
}

?>
