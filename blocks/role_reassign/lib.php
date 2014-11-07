<?php
require_once("../../config.php");
/*
function role_reassign_supports($feature) {
    switch($feature) {
        case FEATURE_BACKUP_MOODLE2:  echo 'supports';        return true;
        default: return null;
    }
}
*/
function quiz_attempt_started_handler($eventdata) {
    handle_event($eventdata, 'quiz_attempt_started');
}

function quiz_attempt_processed_handler($eventdata) {
    handle_event($eventdata, 'quiz_attempt_processed');
}

function handle_event($eventdata, $eventname) {
    global $DB, $USER, $COURSE;
    $switched = false; 
   
    $context = get_context_instance(CONTEXT_SYSTEM);
    // restoring the role
    {
        $userid = $eventdata->user->id;
        $affecteduser = new stdClass();
        $affecteduser = $DB->get_record_select('role_reassign_user', "attemptid = $eventdata->attempt AND userid = $userid AND eventname='$eventname' AND instanceid = $eventdata->quiz");
        if ($affecteduser !=  false) {
            role_unassign($affecteduser->roleid, $eventdata->user->id, $context->id);
            $DB->delete_records_select('role_reassign_user', "id = $affecteduser->userid");
            $switched = true;
        }
    }
    
    if (!$switched){
        $rules  = array();
        $rules = $DB->get_records_select('role_reassign_rules', "eventname='$eventname'");
        if (count($rules) > 0) {   // if this event has rules, responding to it
            // get all roles of current user
            $context1 = get_context_instance(CONTEXT_COURSE, $eventdata->course);
            $roles = get_user_roles($context1, $USER->id); 
            if (count($roles)>0) {
            // for each rule, responding to this event
                foreach ($rules as $rule) {
					// get roles and compare with roles of this rule
                    $sourceroleids = $DB->get_fieldset_select('role_reassign_source_roles', 'roleid', 'ruleid='.$rule->id);
                    foreach ($roles as $role) {
                        if (in_array($role->roleid, $sourceroleids)) { // role found
                            // check for group (groups are optional so no check occures when no groups are defined in rule)
                            $carryon = true;
                            $rulegroups = $DB->get_fieldset_select('role_reassign_groups', 'groupid', 'ruleid='.$rule->id);
                            if ( count($rulegroups) > 0) {
                                file_put_contents('mylog.txt', 'groups in effect ', FILE_APPEND);
                                $carryon = false;
                                foreach ($rulegroups as $rulegroup) {
                                    if (groups_is_member($rulegroup)) {
                                        file_put_contents('mylog.txt', 'group found ', FILE_APPEND);
                                        $carryon = true;
                                    }
                                }
                            }
                            file_put_contents('mylog.txt', $carryon, FILE_APPEND);
                            if ($carryon ==  true) {
                                // check for instance
                                $affectedinstances = $DB->get_fieldset_select('role_reassign_instances', 'instanceid', "ruleid = $rule->id");   // вынести из цикла
                                if (in_array($eventdata->quiz, $affectedinstances)) {
                                    $context = get_context_instance(CONTEXT_SYSTEM);
                                    $destroleid = $DB->get_field_select('role_reassign_rules', 'destroleid', "id = $rule->id");
                                    role_assign($destroleid, $eventdata->user->id, $context->id);
                                    $isreversable = $DB->get_field_select('role_reassign_rules', 'restorable', "id = $rule->id");
                                    if ($isreversable ==  1) {
                                        // add user to database
                                        $userrecord = new stdClass();
                                        $userrecord->attemptid = $eventdata->attempt;
                                        $userrecord->instanceid = $eventdata->quiz;
                                        $userrecord->userid = $eventdata->user->id;
                                        $userrecord->roleid = $destroleid;
                                        $userrecord->eventname = $rule->restoreeventname;
                                        $DB->insert_record('role_reassign_user', $userrecord);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}