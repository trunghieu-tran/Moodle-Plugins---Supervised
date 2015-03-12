<?php

require_once("../../config.php");
$courseid = optional_param('courseid', 0, PARAM_INT);
$PAGE->set_context(get_context_instance(CONTEXT_COURSE, $courseid));
$editrule = optional_param('id', 0, PARAM_INT);
$PAGE->set_url('/blocks/role_reassign/editrule.php', array('id' => $editrule, 'courseid' => $courseid));
global $COURSE;
$PAGE->set_title(get_string('addnewrule', 'block_role_reassign'));
$PAGE->set_heading(get_string('addnewrule', 'block_role_reassign'));

$PAGE->set_pagelayout('standard');

require_capability('block/role_reassign:edit', get_context_instance(CONTEXT_SYSTEM));
require_once($CFG->dirroot.'/blocks/role_reassign/addrule_form.php');
echo $OUTPUT->header();
$mform = new role_reassign_addrule_form();

if ($editrule > 0) {    // editing rule
    $record = $DB->get_record_select('role_reassign_rules', "id = $editrule");
    $defaultdata = array('editnumber'  => $editrule, 
                        'rulename'  => $record->name, 
                        'destrole'  => $record->destroleid, 
                        'event'  => $record->eventname, 
                        'restorable'  =>  $record->restorable, 
                        'reverseevent'  => $record->restoreeventname);
    
    // get affected roles from database
    $roles = $DB->get_fieldset_select('role_reassign_source_roles', 'roleid', "ruleid = $record->id");
    foreach ($roles as $role) {
        $roleshortname = $DB->get_field_select('role', 'shortname', "id = $role");
        $defaultdata[$roleshortname] = 1;
    }
    
    // get affected instances from database
    $instances = $DB->get_fieldset_select('role_reassign_instances', 'instanceid', "ruleid = $record->id");
    foreach ($instances as $instance) {
        $elementname='instance'.$instance;
        $defaultdata[$elementname] = 1;
    }
    $mform->set_data($defaultdata);
} else {
    $defaultdata = array('editnumber'  => -1);
    $mform->set_data($defaultdata);
}

if ($mform->is_cancelled()) {
    echo '<meta http-equiv = "Refresh" content = "5; url = status.php"> ';
} else if ($fromform = $mform->get_data()) {
    $editrule = $fromform->courseid;
    $instances = $mform->get_instances();
    $rule_record = new stdClass();
    $rule_record->name = $mform->get_rule_name();
    $rule_record->destroleid = $mform->get_destrole();
    $editrule = $fromform->editnumber;
    $rule_record->eventname = $mform->get_event();
    $rule_record->courseid = $courseid;
    if ($fromform->reversable ==  0) {
        $rule_record->restorable = 0;
        $rule_record->restoreeventname='';
    } else {
        echo 'reversable';
        $rule_record->restorable = 1;
        $rule_record->restoreeventname = $fromform->reverseevent;
    }
    if ($editrule > 0) {
        // update existing record
        $rule_record->id = $editrule;
        $ruleid = $editrule;
        $DB->update_record('role_reassign_rules', $rule_record);
        // update other tables:
        // (1) source_roles table
        // 1. get records corresponding to editable rule
        $oldroles = array();
        $oldroles = $DB->get_fieldset_select('role_reassign_source_roles', 'roleid', 'ruleid='.$ruleid);
        $newroles = array();
        $newroles = $mform->get_affected_roles();
        
        // 2. diff by value (which is roleid now) with input roles and instances
        $diffadd = array();
        $diffremove = array();
        $diffadd = array_diff($newroles, $oldroles);
        $diffremove = array_diff($oldroles, $newroles);

        // 3. remove and add neccesery roles
        foreach ($diffremove as $toremove) {
            $DB->delete_records_select('role_reassign_source_roles', 'ruleid='.$editrule.' AND roleid='.$toremove);
        }
        $addrec = new stdClass();
        $addrec->ruleid = $editrule;
        foreach ($diffadd as $toadd) {
            $addrec->roleid = $toadd;
            $DB->insert_record('role_reassign_source_roles', $addrec);
        }
        
        // (2) isntances table
        $oldinstances = array();
        $oldinstances = $DB->get_fieldset_select('role_reassign_instances', 'instanceid', 'ruleid='.$ruleid);
        $newinstances = array();
        $newinstances = $mform->get_instances();
        // 2. diff by value (which is roleid now) with input roles and instances
        $diffadd = array();
        $diffremove = array();
        $diffadd = array_diff($newinstances, $oldinstances);
        $diffremove = array_diff($oldinstances, $newinstances);

        // 3. remove and add neccesery roles
        foreach ($diffremove as $toremove) {
            $DB->delete_records_select('role_reassign_instances', 'ruleid='.$editrule.' AND instanceid='.$toremove);
        }
        $addrec = new stdClass();
        $addrec->ruleid = $editrule;
        foreach ($diffadd as $toadd) {
            $addrec->instanceid = $toadd;
            $DB->insert_record('role_reassign_instances', $addrec);
        }
        
        // (3) groups table
        $oldgroups = array();
        $oldgroups = $DB->get_fieldset_select('role_reassign_groups', 'groupid', 'ruleid='.$ruleid);
        $newgroups = array();
        $newgroups = $mform->get_groups();

        // 2. diff by value (which is groupid now) with input groups
        $diffadd = array();
        $diffremove = array();
        $diffadd = array_diff($newgroups, $oldgroups);
        $diffremove = array_diff($oldgroups, $newgroups);

        // 3. remove and add neccesery groups
        foreach ($diffremove as $toremove) {
            $DB->delete_records_select('role_reassign_groups', 'ruleid='.$editrule.' AND groupid='.$toremove);
        }
        $addrec = new stdClass();
        $addrec->ruleid = $editrule;
        foreach ($diffadd as $toadd) {
            $addrec->groupid = $toadd;
            $DB->insert_record('role_reassign_groups', $addrec);
        }
        
    } else {
        // add new record
        $ruleid = $DB->insert_record('role_reassign_rules', $rule_record);
        //instances database output:
        $instancerecord = new stdClass();
        foreach ($instances as $instance) {
            $instancerecord->ruleid = $ruleid;
            $instancerecord->instanceid = $instance;
            $DB->insert_record('role_reassign_instances', $instancerecord);
        }
        
        
        // affected roles datbase output
        $affectedroles = $mform->get_affected_roles();
        $rolerecord = new stdClass();
        $rolerecord->ruleid = $ruleid;
        foreach ($affectedroles as $affectedrole) {
            $rolerecord->roleid = $affectedrole;
            $DB->insert_record('role_reassign_source_roles', $rolerecord);
        }
        
        // groups database output
        $affectedgroups = $mform->get_groups();
        $grouprecord = new stdClass();
        $grouprecord->ruleid = $ruleid;
        foreach ($affectedgroups as $group) {
            $grouprecord->groupid = $group;
            $DB->insert_record('role_reassign_groups', $grouprecord);
        }
        
    }
    
    
    redirect('status.php?courseid='.$courseid);
    
    
} else {    // data doesn't validate and for should be redisplayed
    
    $mform->display();
     
}
echo $OUTPUT->footer();


