<?php
require_once('../../config.php');
global $CFG, $PAGE, $OUTPUT;

require_login();

$strviewfeed = get_string('listclassroom', 'block_supervised');
$PAGE->set_pagelayout('standart');
$context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
$PAGE->set_context($context);
require_capability('block/supervised:changeclassroom', $context);
$PAGE->set_url('/blocks/supervised/listclassroom.php');
$PAGE->set_heading($strviewfeed);
$PAGE->set_title($strviewfeed);
$PAGE->requires->css('/blocks/supervised/styles.css');

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$courseid = optional_param('courseid', 0, PARAM_INTEGER);
$numclassroom = optional_param('numclassroom', '', PARAM_TEXT);
$initialvalueip = optional_param('initialvalueip', '', PARAM_TEXT);
$finishvalueip = optional_param('finishvalueip', '', PARAM_TEXT);

if ($numclassroom != '' && $initialvalueip != '' && $finishvalueip != '') {
    $record = array('number' => $numclassroom, 'initialvalueip'=>$initialvalueip, 'finishvalueip'=>$finishvalueip);
    $searchintable = $DB->get_records_select('block_supervised_classroom', 'number = :number and initialvalueip = :initialvalueip and finishvalueip = :finishvalueip', $record);
    if ($searchintable == null) {
        $DB->insert_record('block_supervised_classroom', $record);
    }
}

// if we delete records from table
$ids = $DB->get_records_sql("SELECT `id` FROM {block_supervised_classroom}");
foreach ($ids as $id) {
    $name_field = 'rec_' . $id->id;
    $valuenamefield = optional_param($name_field, -1, PARAM_INT);
    if ($valuenamefield != -1) {
        $DB->delete_records('block_supervised_classroom', array('id'=>$id->id));
        $redirect = true;
    }
}

echo $OUTPUT->header();
$tableip = new html_table();
$tableip->head = array(get_string('numberclassroom', 'block_supervised'), get_string('startip', 'block_supervised'), get_string('endip', 'block_supervised'), '');
$tableip->align = array('center', 'center', 'center', 'center');
$tableip->width = '100%';

// load from DB
$return = $DB->get_records_sql("SELECT * FROM {block_supervised_classroom}");
foreach ($return as $record) {
                $checkbox = html_writer::checkbox('rec_'.$record->id, 'yes', false);
                $row = array(
                    $record->number, 
                    $record->initialvalueip,
                    $record->finishvalueip,
                    $checkbox
                );
                $tableip->data[] = $row;
}

$row = array('', '', '', '<input type="submit" value="' . get_string('delip', 'block_supervised') . '" />');
$tableip->data[] = $row;

$numclassroom = '<span>';
$numclassroom .= html_writer::label(get_string('labelroom', 'block_supervised'), 'num_classroom') . ':&nbsp;' ;
$numclassroom .= '<input type="text" id="numclassroom" name="numclassroom" size="3" maxlength="8"/>';
$numclassroom .= '</span>';

$initialvalueip = '<span>';
$initialvalueip .= html_writer::label(get_string('initialvalueip', 'block_supervised'), 'initial_value_ip') . ':&nbsp;' ;
$initialvalueip .= '<input type="text" id="initialvalueip" name="initialvalueip" size="11" maxlength="15"/>';
$initialvalueip .= '</span>';

$finishvalueip = '<span>';
$finishvalueip .= html_writer::label(get_string('finishvalueip', 'block_supervised'), 'finish_value_ip') . ':&nbsp;' ;
$finishvalueip .= '<input type="text" id="finishvalueip" name="finishvalueip" size="11" maxlength="15"/>';
$finishvalueip .= '</span>';

// output table with ip 
echo '<form id="table_ip" method="post" action="'. qualified_me().'">';
echo html_writer::table($tableip);
echo '</form>';

// output form for input ip address
echo '<form id="add_ip" method="post" action="'. qualified_me().'">';
    echo '<div class="inputip">';
        echo $numclassroom;
        echo '&nbsp;';
        echo $initialvalueip;
        echo '&nbsp;';
        echo $finishvalueip;
    echo '</div>';
    echo '<div class="addbutton">';
        echo '<input type="submit" value="' . get_string('addip', 'block_supervised') . '" />';
    echo '</div>';
echo '</form>';
echo $OUTPUT->footer();