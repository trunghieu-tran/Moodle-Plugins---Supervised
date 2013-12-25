<?php
require_once('../../../config.php');
require_once('../lib.php');
 
global $DB, $OUTPUT, $PAGE;

$courseid   = required_param('courseid', PARAM_INT);
$site = get_site();

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error("invalidcourseid");
}
if ($site->id == $course->id) {
    // block can not work in the main course (frontpage)
    print_error("invalidcourseid");
}

require_login($course);
require_capability('block/supervised:editclassrooms', $PAGE->context);

$PAGE->set_url('/blocks/supervised/classrooms/view.php', array('courseid' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('classroomspagetitle', 'block_supervised'));
include("breadcrumbs.php");

// Display header.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("classroomsheader", 'block_supervised'), 3);

// Prepare table data
$classrooms = $DB->get_records('block_supervised_classroom', null, 'name');
$tabledata = array();
foreach ($classrooms as $id=>$classroom) {
    // Prepare icons.
    $editurl = new moodle_url('/blocks/supervised/classrooms/addedit.php', array('id' => $id, 'courseid' => $courseid));
    $iconedit = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('edit')));
    $deleteurl = new moodle_url('/blocks/supervised/classrooms/delete.php', array('courseid' => $courseid, 'id' => $id));
    $icondelete = '';
    if(can_delete_classroom($id)){
        $icondelete = $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', get_string('delete')));
    }

    $iconshowhide = '';
    if(can_showhide_classroom($id)){
        if($classroom->active){
            $showhide = "hide";
        }
        else{
            $showhide = "show";
        }
        $showhideurl = new moodle_url('/blocks/supervised/classrooms/showhide.php', array('courseid' => $courseid, 'id' => $id));
        $iconshowhide = $OUTPUT->action_icon($showhideurl, new pix_icon('t/'.$showhide, get_string($showhide)));
    }

    // Combine new row.
    $tabledata[] = array(
        $classroom->name,
        $classroom->iplist,
        $iconedit . $icondelete . $iconshowhide
    );
}

$addurl = new moodle_url('/blocks/supervised/classrooms/addedit.php', array('courseid' => $courseid));
echo ('<a href="'.$addurl.'">' . get_string('addclassroom', 'block_supervised') . '</a>');

// Build table.
$table = new html_table();
$headclassroom = get_string('classroom', 'block_supervised');
$headiplist = get_string('iplist', 'block_supervised');
$headedit = get_string('edit');
$table->head = array($headclassroom, $headiplist, $headedit);
$table->data = $tabledata;
echo html_writer::table($table);

// Display footer.
echo $OUTPUT->footer();