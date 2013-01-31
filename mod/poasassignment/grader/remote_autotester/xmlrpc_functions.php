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
function notify_test_started($loginHash, $passwordHash, $attemptid, $test) {
    $config = get_config("poasassignment_remote_autotester");
    if (md5($config->login) != $loginHash || md5($config->password) != $passwordHash) {
        XMLRPC_response(XMLRPC_prepare("401 Unauthorized"));
    }
    else {
        global $DB;
        $records = $DB->get_records('poasassignment_gr_ra', array('attemptid' => $attemptid), 'id DESC', 'id, attemptid');
        if (count($records) > 0) {
            $recordremote = array_shift($records);

            $testinfo = new stdClass();
            $testinfo->remote_id = $recordremote->id;
            $testinfo->test = $test;
            $testinfo->timeteststarted = time();
            $DB->insert_record('poasassignment_gr_ra_tests', $testinfo);
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
function notify_tested($loginHash, $passwordHash, $attemptid, $test, $istested, $studentout) {
    $config = get_config("poasassignment_remote_autotester");
    if (md5($config->login) != $loginHash || md5($config->password) != $passwordHash) {
        XMLRPC_response(XMLRPC_prepare("401 Unauthorized"));
    }
    else {
        global $DB;
        $records = $DB->get_records('poasassignment_gr_ra', array('attemptid' => $attemptid), 'id DESC', 'id, attemptid');
        if (count($records) > 0) {
            $recordremote = array_shift($records);
            $testresults = $DB->get_records('poasassignment_gr_ra_tests', array('remote_id' => $recordremote->id, 'test' => $test), 'id DESC', 'id');
            $testresult = array_shift($testresults);
            $testresult->timetested = time();
            if ($istested)
                $testresult->testpassed = 1;
            else
                $testresult->testpassed = 0;
            $testresult->studentout = $studentout;
            $DB->update_record('poasassignment_gr_ra_tests', $testresult);
        }
        XMLRPC_response(XMLRPC_prepare("200 OK"));
    }
}