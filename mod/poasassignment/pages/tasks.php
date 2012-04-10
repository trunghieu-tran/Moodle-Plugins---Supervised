<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '/model.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot.'/lib/tablelib.php');
class tasks_page extends abstract_page {
    var $poasassignment;

    function tasks_page($cm,$poasassignment) {
        $this->poasassignment = $poasassignment;
        $this->cm=$cm;
    }

    function has_satisfying_parameters() {
        global $DB,$USER;
        $flag = $this->poasassignment->flags & ACTIVATE_INDIVIDUAL_TASKS;
        if (!$flag) {
            $this->lasterror='errorindtaskmodeisdisabled';
            return false;
        }
        $model = poasassignment_model::get_instance();
        if ($assignee = $model->get_assignee($USER->id,$this->poasassignment->id)){
            if (isset($assignee->taskid) && $assignee->taskid > 0) {
                if (!has_capability('mod/poasassignment:managetasks',
                        get_context_instance(CONTEXT_MODULE,$this->cm->id))) {
                    $this->lasterror='alreadyhavetask';
                    return false;
                }
            }
        }
        return true;
    }
    function view() {
        global $DB,$OUTPUT,$USER,$PAGE;

        $hascapmanage=has_capability('mod/poasassignment:managetasks',
                            get_context_instance(CONTEXT_MODULE, $this->cm->id));

        $tg = $DB->get_record('poasassignment_taskgivers', array('id'=>$this->poasassignment->taskgiverid));
        require_once ($tg->path);
        $taskgivername = $tg->name;
        $taskgiver = new $taskgivername();
        $taskgiver->process_before_tasks($this->cm->id, $this->poasassignment);

        if ($hascapmanage || $taskgivername::show_tasks()) {
            $this->view_table($hascapmanage, $taskgiver);
            $taskgiver->process_after_tasks($this->cm->id, $this->poasassignment);
        }

        if ($hascapmanage) {
            $id = $this->cm->id;
            echo '<div align="center">';
            echo $OUTPUT->single_button(new moodle_url('view.php', array('id' => $id, 'page' => 'taskedit')),get_string('addtask','poasassignment'));
            echo '</div>';
        }

    }
    private function view_table($hascapmanage, $taskgiver) {
        global $DB, $OUTPUT, $PAGE, $USER;
        $poasmodel = poasassignment_model::get_instance($this->poasassignment);
        $table = new flexible_table('mod-poasassignment-tasks');
        $table->baseurl = $PAGE->url;
        $fields = $DB->get_records('poasassignment_fields', array('poasassignmentid' => $this->poasassignment->id));


        $columns[]=get_string('taskname', 'poasassignment');
        $columns[]=get_string('taskdescription', 'poasassignment');
        $headers[]=get_string('taskname', 'poasassignment');
        $headers[]=get_string('taskdescription', 'poasassignment');

        if (count($fields)) {
            foreach ($fields as $field) {
                if ($field->showintable>0) {
                    if ($hascapmanage ||(!$hascapmanage && !$field->secretfield)) {
                        $columns[] = $field->name;
                        if (has_capability('mod/poasassignment:seefielddescription', get_context_instance(CONTEXT_MODULE, $this->cm->id)))
                            $headers[] = $field->name . ' ' . $poasmodel->help_icon($field->description);
                        else
                            $headers[] = $field->name;
                    }
                }
            }
        }
        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->collapsible(true);
        $table->initialbars(true);
        $table->set_attribute('class', 'poasassignment-table tasks-table');
        $table->set_attribute('width', '100%');
        $table->setup();
        // Show all tasks if we can manage tasks
        if(has_capability('mod/poasassignment:managetasks',
                          get_context_instance(CONTEXT_MODULE, $this->cm->id))) {
            $tasks = $DB->get_records('poasassignment_tasks', array('poasassignmentid' => $this->poasassignment->id));
        }
        // Else show available for user tasks
        else {
            $tasks = $poasmodel->get_available_tasks($USER->id);
        }
        foreach ($tasks as $task) {
            if (!$hascapmanage && $task->hidden)
                continue;
            $row = array();

            $viewurl = new moodle_url('view.php',array('page' => 'taskview', 'taskid'=>$task->id,'id'=>$this->cm->id),'v','get');
            if ($task->hidden) {
                $namecolumn = html_writer::link(
                    $viewurl,
                    $task->name,
                    array('title' => get_string('view'), 'class' => 'hiddentask'));
            }
            else {
                $namecolumn = html_writer::link(
                    $viewurl,
                    $task->name,
                    array('title' => get_string('view')));
            }

            $namecolumn.=$taskgiver->get_task_extra_string($task->id,$this->cm->id);

            if ($hascapmanage) {

                $updateurl = new moodle_url('view.php',
                                            array('taskid'=>$task->id,'id'=>$this->cm->id,'page' => 'taskedit'),'u','get');
                $deleteurl = new moodle_url('warning.php',
                                            array('taskid'=>$task->id,
                                            		'action'=>'deletetask',
                                            		'id'=>$this->cm->id
                                            		),
                							'd',
                							'get');

                $showicon = '<a href="'.$updateurl.'">'.'<img src="'.$OUTPUT->pix_url('t/show').
                            '" class="iconsmall" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
                $hideicon = '<a href="'.$updateurl.'">'.'<img src="'.$OUTPUT->pix_url('t/hide').
                            '" class="iconsmall" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
                $updateicon = '<a href="'.$updateurl.'">'.'<img src="'.$OUTPUT->pix_url('t/edit').
                            '" class="iconsmall" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
                $deleteicon = '<a href="'.$deleteurl.'">'.'<img src="'.$OUTPUT->pix_url('t/delete').
                            '" class="iconsmall" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>';
                if ($task->hidden) {
                    $showurl = new moodle_url('view.php',
                                              array('taskid' => $task->id,
                                                    'mode' => SHOW_MODE,
                                                    'id' => $this->cm->id,
                                                    'page' => 'taskedit'),
                                              'u',
                                              'get');
                    $showicon = '<a href="'.$showurl.'">'.'<img src="'.$OUTPUT->pix_url('t/show').
                            '" class="iconsmall" alt="'.get_string('show').'" title="'.get_string('show').'" /></a>';
                    $namecolumn .= '&nbsp;' . $showicon;
                }
                else {
                    $hideurl = new moodle_url('view.php',
                                              array('taskid' => $task->id,
                                                    'mode' => HIDE_MODE,
                                                    'id' => $this->cm->id,
                                                    'page' => 'taskedit'),
                                              'u',
                                              'get');
                    $hideicon = '<a href="'.$hideurl.'">'.'<img src="'.$OUTPUT->pix_url('t/hide').
                            '" class="iconsmall" alt="'.get_string('hide').'" title="'.get_string('hide').'" /></a>';
                    $namecolumn .= '&nbsp;' . $hideicon;
                }
                $namecolumn.='&nbsp;'.$updateicon.'&nbsp;'.$deleteicon;

            }
            if ($task->hidden)
                $namecolumn.='</font>';
            $row[]=$namecolumn;
            $row[]=shorten_text($task->description);
            foreach ($fields as $field) {
            	$value = '<span class="poasassignment-critical">'.get_string('notdefined', 'poasassignment').'</span>';
                if ($field->showintable>0) {
                    if ($hascapmanage ||(!$hascapmanage && !$field->secretfield)) {
                        $taskvalue=$DB->get_record('poasassignment_task_values',
                                                    array('taskid'=>$task->id, 'fieldid'=>$field->id, 'assigneeid'=>0));
                        if ($field->random == 1) {
                        	$value= get_string('randomfield', 'poasassignment');
                        }
                        else {                        
	                        if ($taskvalue) {
                                if (isset($taskvalue->value)) {
                                	switch ($field->ftype) {
                                		case TEXT:
                                			$value = shorten_text($taskvalue->value);
                                			break;
                                		case LISTOFELEMENTS:
                                			$variants = $poasmodel->get_variants($field->id);
                                			$variant = $variants[$taskvalue->value];
                                			$value = $variant;
                                			break;
                                		case MULTILIST:
                                			$indexes = explode(',', $taskvalue->value);
                                			$variants = $poasmodel->get_variants($field->id);
                                			$value = '';
                                			foreach ($indexes as $index) {
                                				if (is_number($index)) {
                                					$value .= $variants[$index].'<br/>';
                                				}
                                			}
                                			break;
                                		case DATE:
                                			$value = userdate($taskvalue->value);
                                			break;
                                		case FILE:
                                			$context = get_context_instance(CONTEXT_MODULE, $this->cm->id);
                                			$value = $poasmodel->view_files($context->id,'poasassignmenttaskfiles',$task->id);
                                			break;
                                		default:
                                			$value = $taskvalue->value; 
                                			break;
                                	}
                                }
	                        }
                        }
                        $row[] = $value;
                    }
                }
            }
            $table->add_data($row);
        }
            $table->print_html();
    }
}
