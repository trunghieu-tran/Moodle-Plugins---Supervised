<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Update compiled info in DB.
 *
 * @param $attemptid
 * @param $compiled
 */
function notify_compiled($loginHash, $passwordHash, $attemptid, $compiled) {
    $config = get_config("poasassignment_remote_autotester");
    if (md5($config->login) != $loginHash || md5($config->password) != $passwordHash) {
        XMLRPC_response(XMLRPC_prepare("401 Unauthorized"));
    }
    else {
        global $DB;
        $records = $DB->get_records('poasassignment_gr_ra', array('attemptid' => $attemptid), 'id DESC', 'id, attemptid');
        if (count($records) > 0) {
            $record = array_shift($records);
            if ($compiled)
                $record->compiled = 1;
            else
                $record->compiled = 0;
            $record->timecompiled = time();
            $DB->update_record('poasassignment_gr_ra', $record);
        }
        XMLRPC_response(XMLRPC_prepare("200 OK"));
    }
}

/**
 * Insert time when compile started
 *
 * @param $loginHash
 * @param $passwordHash
 * @param $attemptid
 */
function notify_compile_started($loginHash, $passwordHash, $attemptid) {
    $config = get_config("poasassignment_remote_autotester");
    if (md5($config->login) != $loginHash || md5($config->password) != $passwordHash) {
        XMLRPC_response(XMLRPC_prepare("401 Unauthorized"));
    }
    else {
        global $DB;
        $records = $DB->get_records('poasassignment_gr_ra', array('attemptid' => $attemptid), 'id DESC', 'id, attemptid');
        if (count($records) > 0) {
            $record = array_shift($records);
            $record->timecompilestarted = time();
            $DB->update_record('poasassignment_gr_ra', $record);
        }
        XMLRPC_response(XMLRPC_prepare("200 OK"));
    }
}

/**
 * Insert attempt test info
 *
 * @param $attemptid
 * @param $compiled
 */
function notify_test_started($loginHash, $passwordHash, $attemptid) {
    $config = get_config("poasassignment_remote_autotester");
    if (md5($config->login) != $loginHash || md5($config->password) != $passwordHash) {
        XMLRPC_response(XMLRPC_prepare("401 Unauthorized"));
    }
    else {
        global $DB;
        $records = $DB->get_records('poasassignment_gr_ra', array('attemptid' => $attemptid), 'id DESC', 'id, attemptid');
        if (count($records) > 0) {
            $record = array_shift($records);
            $record->timeteststart = time();
            $DB->update_record('poasassignment_gr_ra', $record);
        }
        XMLRPC_response(XMLRPC_prepare("200 OK"));
    }
}

/**
 * Update attempt test result info
 *
 * @param $attemptid
 * @param $compiled
 */
function notify_tested($loginHash, $passwordHash, $attemptid, $test, $testin, $testout, $studentout, $istested) {
    $config = get_config("poasassignment_remote_autotester");
    if (md5($config->login) != $loginHash || md5($config->password) != $passwordHash) {
        XMLRPC_response(XMLRPC_prepare("401 Unauthorized"));
    }
    else {
        global $DB;
        $records = $DB->get_records('poasassignment_gr_ra', array('attemptid' => $attemptid), 'id DESC', 'id, attemptid');
        if (count($records) > 0) {
            $recordremote = array_shift($records);

            $testresult = new stdClass();
            $testresult->remote_id = $recordremote->id;
            $testresult->test = $test;
            $testresult->timetested = time();
            $testresult->testin = $testin;
            $testresult->testout = $testout;
            $testresult->studentout = $studentout;

            if ($istested) {
                $testresult->testpassed = 1;
            }
            else {
                $testresult->testpassed = 0;
            }
            $DB->insert_record('poasassignment_gr_ra_tests', $testresult);
        }
        XMLRPC_response(XMLRPC_prepare("200 OK"));
    }
}