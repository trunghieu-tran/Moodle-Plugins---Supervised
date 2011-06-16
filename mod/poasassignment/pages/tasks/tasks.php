<?php
require_once(dirname(dirname(__FILE__)) . '\abstract_page.php');
require_once(dirname(dirname(dirname(__FILE__))) . '\model.php');   
require_once($CFG->libdir . '\formslib.php');
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
        if ($assignee=$DB->get_record('poasassignment_assignee',array('userid'=>$USER->id, 'poasassignmentid'=>$this->poasassignment->id))) 
            if (isset($assignee->taskid) && $assignee->taskid > 0) {
                if (!has_capability('mod/poasassignment:managetasks',
                        get_context_instance(CONTEXT_MODULE,$this->cm->id))) {
                    $this->lasterror='alreadyhavetask';
                    return false;
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
        
        

        if ($hascapmanage || $taskgivername::show_tasks) {
            $this->view_table($hascapmanage, $taskgiver);
            $taskgiver->process_after_tasks($this->cm->id, $this->poasassignment);
        }
        if ($hascapmanage) {
            $id = $this->cm->id;
            echo '<div align="center">';
            echo $OUTPUT->single_button(new moodle_url('/mod/poasassignment/pages/tasks/taskedit.php?id='.$id), 
                                                        get_string('addbuttontext','poasassignment'));
            echo '</div>';
        }
        
    }
    private function view_table($hascapmanage, $taskgiver) {
        global $DB, $OUTPUT, $PAGE, $USER;
        $poasmodel = poasassignment_model::get_instance($this->poasassignment);
        $table = new flexible_table('mod-poasassignment-tasks');
        $table->baseurl = $PAGE->url;
        $fields = $DB->get_records('poasassignment_fields', array('poasassignmentid' => $this->poasassignment->id));
        
        
        $columns[]='name';
        $columns[]='description';
        $headers[]='name';
        $headers[]='description';
        
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
        // $table->column_class('taskfieldname', 'name');
        $table->set_attribute('class', 'tasksfields');
        $table->set_attribute('border', '1');
        $table->set_attribute('width', '100%');
        
        $table->setup();
        // Show all tasks if we can manage tasks
        if(has_capability('mod/poasassignment:managetasks',
                          get_context_instance(CONTEXT_MODULE, $this->cm->id))) {
            $tasks = $DB->get_records('poasassignment_tasks', array('poasassignmentid' => $this->poasassignment->id));
        }
        // Else show available for user tasks 
        else {
            $tasks = $poasmodel->get_available_tasks($this->poasassignment->id, $USER->id);
        }
        foreach ($tasks as $task) {
            if (!$hascapmanage && $task->hidden)
                continue;
            $row = array();

            // Adding view icon
            $viewurl = new moodle_url('/mod/poasassignment/pages/tasks/taskview.php',array('taskid'=>$task->id,'id'=>$this->cm->id),'v','get');
            $viewicon = '<a href="'.$viewurl.'">'.'<img src="'.$OUTPUT->pix_url('view','poasassignment').
                            '" class="iconsmall" alt="'.get_string('view').'" title="'.get_string('view').'" /></a>';
            $namecolumn=$task->name.' '.$viewicon;

            if ($task->hidden)
                $namecolumn='<font color="#AAAAAA">' . $namecolumn;

            $namecolumn.=$taskgiver->get_task_extra_string($task->id,$this->cm->id);

            if ($hascapmanage) {
               
                $updateurl = new moodle_url('/mod/poasassignment/pages/tasks/taskedit.php',
                                            array('taskid'=>$task->id,'id'=>$this->cm->id),'u','get');
                $deleteurl = new moodle_url('/mod/poasassignment/pages/tasks/taskedit.php',
                                            array('taskid'=>$task->id,'mode'=>DELETE_MODE,'id'=>$this->cm->id),'d','get');
                
                $showicon = '<a href="'.$updateurl.'">'.'<img src="'.$OUTPUT->pix_url('t/show').
                            '" class="iconsmall" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
                $hideicon = '<a href="'.$updateurl.'">'.'<img src="'.$OUTPUT->pix_url('t/hide').
                            '" class="iconsmall" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
                $updateicon = '<a href="'.$updateurl.'">'.'<img src="'.$OUTPUT->pix_url('t/edit').
                            '" class="iconsmall" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
                $deleteicon = '<a href="'.$deleteurl.'">'.'<img src="'.$OUTPUT->pix_url('t/delete').
                            '" class="iconsmall" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>';
                 if ($task->hidden) {
                    $showurl = new moodle_url('/mod/poasassignment/pages/tasks/taskedit.php',
                                              array('taskid' => $task->id,
                                                    'mode' => SHOW_MODE,
                                                    'id' => $this->cm->id),
                                              'u',
                                              'get');
                    $showicon = '<a href="'.$showurl.'">'.'<img src="'.$OUTPUT->pix_url('t/show').
                            '" class="iconsmall" alt="'.get_string('show').'" title="'.get_string('show').'" /></a>';
                    $namecolumn .= $showicon;
                }
                else {
                    $hideurl = new moodle_url('/mod/poasassignment/pages/tasks/taskedit.php',
                                              array('taskid' => $task->id,
                                                    'mode' => HIDE_MODE,
                                                    'id' => $this->cm->id),
                                              'u',
                                              'get');
                    $hideicon = '<a href="'.$hideurl.'">'.'<img src="'.$OUTPUT->pix_url('t/hide').
                            '" class="iconsmall" alt="'.get_string('hide').'" title="'.get_string('hide').'" /></a>';
                    $namecolumn .= $hideicon;
                }
                $namecolumn.=' '.$updateicon.' '.$deleteicon;
                
            }
            if ($task->hidden)
                $namecolumn.='</font>';
            $row[]=$namecolumn;
            $row[]=shorten_text($task->description);
            foreach ($fields as $field) {
                if ($field->showintable>0) {
                    if ($hascapmanage ||(!$hascapmanage && !$field->secretfield)) {
                        $taskvalue=$DB->get_record('poasassignment_task_values',
                                                    array('taskid'=>$task->id,'fieldid'=>$field->id,'assigneeid'=>null));
                        if (!$taskvalue)
                            $taskvalue->value='null';
                        else {
                            if ($field->random)
                                $taskvalue->value= 'random';
                            else {
                                if (isset($taskvalue->value)) {
                                    if ($field->ftype==TEXT)
                                        $taskvalue->value=shorten_text($taskvalue->value);
                                    if ($field->ftype==LISTOFELEMENTS) {
                                        $variants=$poasmodel->get_field_variants($field->id);
                                        $variant=$variants[$taskvalue->value];
                                        //$variant = $poasmodel->get_variant($taskvalue->value,$field->variants);
                                        $taskvalue->value=$variant;
                                    }
                                    if ($field->ftype==MULTILIST) {
                                        $tok = strtok($taskvalue->value,',');
                                        $opts=array();
                                        while(strlen($tok)>0) {
                                            $opts[]=$tok;
                                            $tok=strtok(',');
                                        }
                                        $taskvalue->value='';
                                        $variants=$poasmodel->get_field_variants($field->id);
                                        foreach ($opts as $opt) {
                                            //$variant = $poasmodel->get_variant($opt,$poasmodel->get_field_variants($field->id));
                                            $variant=$variants[$opt];
                                            $taskvalue->value.=$variant.'<br>';
                                        }
                                    }
                                    if ($field->ftype==DATE) {
                                        $taskvalue->value=userdate($taskvalue->value);
                                    }
                                    if ($field->ftype==FILE) {
                                        $context= get_context_instance(CONTEXT_MODULE, $this->cm->id);
                                        $taskvalue->value=$poasmodel->view_files($context->id,'poasassignmenttaskfiles',$taskvalue->id);
                                    }
                                }
                            }
                        }
                        $row[] = $taskvalue->value;
                    }
                }
            }
            $table->add_data($row);
        }
            $table->print_html();
    }
}
