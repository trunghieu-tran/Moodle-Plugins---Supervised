<?php
require_once("../../config.php");
$courseid = optional_param('courseid', 0, PARAM_INT);
$PAGE->set_context(get_context_instance(CONTEXT_COURSE, $courseid));
global $DB, $COURSE;
$PAGE->set_title(get_string('statusheader', 'block_role_reassign'));
$PAGE->set_heading(get_string('statusheader', 'block_role_reassign'));
$PAGE->set_url('/blocks/role_reassign/status.php', array('courseid' => $courseid));

$PAGE->set_pagelayout('standard');

require_capability('block/role_reassign:edit', get_context_instance(CONTEXT_SYSTEM));
echo $OUTPUT->header();

$table = new html_table();

$table->head = array(get_string('tablename', 'block_role_reassign'), get_string('tableinstances', 'block_role_reassign'), get_string('tabletargetrole', 'block_role_reassign'), get_string('tableaffectedroles', 'block_role_reassign'), get_string('tableaffectedgroups', 'block_role_reassign'), get_string('tablestartevent', 'block_role_reassign'), get_string('tablerestorable', 'block_role_reassign'), get_string('tablerestoreevent', 'block_role_reassign'), '', '');
$table->width='95%';
$delete = optional_param('delete', 0, PARAM_INT);
if ($delete > -1) {
    // Delete rule. But delete records from additional tables first
    $DB->delete_records_select('role_reassign_source_roles', 'ruleid='.$delete);
    $DB->delete_records_select('role_reassign_instances', 'ruleid='.$delete);
    $DB->delete_records_select('role_reassign_rules', 'id='.$delete);
}

// get rule records:
$rulerecords = $DB->get_records_select('role_reassign_rules', 'courseid='.$courseid, null, 'id');

foreach ($rulerecords as $record) {
    // affected instances
    $instancerecords = $DB->get_records_select('role_reassign_instances', 'ruleid='.$record->id, null, 'id');
    $instancesstring = '';
    if (count($instancerecords)) {
        foreach ($instancerecords as $instance) {
            $instancename = $DB->get_field_select('quiz', 'name', 'id='.$instance->instanceid, null);
            if ($instancename) {
                $instancesstring = $instancesstring.'<a href='.$CFG->wwwroot.'/mod/quiz/view.php?q='.$instance->instanceid.'/>'.$instancename.'</a><br>';
            } else {
                // deleting instance record
                $DB->delete_records_select('role_reassign_instances', 'id='.$instance->instanceid, null);
            }
        }
    } else {
        $instancesstring = 'N/A';
    }
    
    
    $targetrole = $DB->get_field_select('role', 'name', 'id='.$record->destroleid);
    // affected roles list:
    $affectedroles = $DB->get_fieldset_select('role_reassign_source_roles', 'roleid', 'ruleid='.$record->id);
    $rolesstring='';
    foreach ($affectedroles as $roleid) {
        $str = $DB->get_field_select('role', 'name', 'id='.$roleid);
        $rolesstring = $rolesstring.', '.$str;
    }
    $rolesstring = ltrim($rolesstring, ', ');
    // groups string
    $groupsstring='';
    $affectedgroups = $DB->get_fieldset_select('role_reassign_groups', 'groupid', 'ruleid='.$record->id);
    if (count($affectedgroups) > 0) {
        foreach ($affectedgroups as $groupid) {
            $str = $DB->get_field_select('groups', 'name', 'id='.$groupid);
            $groupsstring = $groupsstring.', '.$str; 
        }
        $groupsstring = ltrim($groupsstring, ', ');
    } else {
        $groupsstring='N/A';
    }    
    if ($record->restorable ==  1) {
        $restorable='Yes';
    } else {
        $restorable='No';
    }
    $editstr = get_string('edit', 'block_role_reassign');
    $deletestr = get_string('delete', 'block_role_reassign');
    $editlink = "<a href='editrule.php?id = $record->id'>$editstr</a>";
    if ( has_capability('block/role_reassign:edit', get_context_instance(CONTEXT_SYSTEM)) ) {
        $editlink='<a href=\'editrule.php?id='.$record->id.'&courseid='.$courseid.'\'><img'.
                     ' src="'.$OUTPUT->pix_url('t/edit').'" class="iconsmall" '.'" /></a>'.
                     '<a href=\'status.php?delete='.$record->id.'&courseid='.$courseid.'\'><img'.
                     ' src="'.$OUTPUT->pix_url('t/delete').'" class="iconsmall"'.'/></a> ';
    } else {
        $editlink='no capability';
    }
    
    $table->data[] = array( $record->name, 
                            $instancesstring,
                            $targetrole, 
                            $rolesstring, 
                            $groupsstring, 
                            $record->eventname, 
                            $restorable, 
                            $record->restoreeventname, 
                            $editlink );
}
// (other table properties here)
$addstr = get_string('addnewrule', 'block_role_reassign');
echo $OUTPUT->heading("<a href='/blocks/role_reassign/editrule.php?id=-1".'&courseid='.$courseid."'>$addstr</a>");
if (!empty($table)) {
        echo html_writer::table($table);
    }
echo $OUTPUT->footer();