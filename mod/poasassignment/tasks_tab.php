<?php
require_once('abstract_tab.php');
require_once('lib.php');
require_once('model.php');      
require_once($CFG->libdir.'/formslib.php');
class tasks_tab extends abstract_tab {
    var $poasassignment;
    
    function tasks_tab($cm,$poasassignment) {
        global  $PAGE;
        
        
        $this->poasassignment = $poasassignment;
        $this->cm=$cm;
        
    }
    
    function has_satisfying_parameters() {
        global $DB,$USER;
        $flag = $this->poasassignment->flags&ACTIVATE_INDIVIDUAL_TASKS;
        if($assignee=$DB->get_record('poasassignment_assignee',array('userid'=>$USER->id,'poasassignmentid'=>$this->poasassignment->id))) 
            if(isset($assignee->taskid) && $assignee->taskid>0) {
                if(!has_capability('mod/poasassignment:managetasks',
                        get_context_instance(CONTEXT_MODULE,$this->cm->id))) {
                    $this->lasterror='alreadyhavetask';
                    return false;
                }
            }
        if(!$flag) {
            $this->lasterror='errorindtaskmodeisdisabled';
            return false;
        }
        return true;
    }
    function parameter_search() {
        global $DB,$USER;
        $poasmodel = poasassignment_model::get_instance($this->poasassignment);
        if($this->poasassignment->howtochoosetask==PARAMETERRANDOM) {
            $mform=new parametersearch_form(null,array('poasassignmentid'=>$this->poasassignment->id,'id'=>$this->cm->id));
            if($data=$mform->get_data()) {
                // подбор задания
                //$tasks=$DB->get_records('poasassignment_tasks',array('poasassignmentid'=>$this->poasassignment->id,'hidden'=>0));
                // if($tasks) {
                    // foreach($tasks as $task) {
                        // $fieldvalues=$DB->get_records('poasassignment_task_values',array('taskid'=>$task->id));
                        // if($fieldvalues) {
                            // foreach($fieldvalues as $fieldvalue) {
                                // if(
                            
                            // }
                        // }
                    // }
                // }
                $tasks=$DB->get_records('poasassignment_tasks',array('poasassignmentid'=>$this->poasassignment->id));
                $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$this->poasassignment->id,'searchparameter'=>1));
                if($fields) {
                    $satisfyingtasks=array();
                    foreach($fields as $field) {
                        $fieldelementname='field'.$field->id;
                        $fieldvalues=$DB->get_records('poasassignment_task_values',
                                        array('fieldid'=>$field->id));
                        if($fieldvalues) {
                            if($field->ftype==LISTOFELEMENTS || $field->ftype==MULTILIST || $field->ftype==STR || $field->ftype==TEXT) {
                                foreach($fieldvalues as $fieldvalue) {
                                    if($tasks[$fieldvalue->taskid]->hidden==0) {
                                        $contains=strpos($fieldvalue->value,$data->$fieldelementname);
                                        if($contains!==false) {
                                            for($i=0;$i<5;$i++)
                                                $satisfyingtasks[]=$fieldvalue->taskid;
                                        }
                                    }
                                }
                            }
                            if($field->ftype==NUMBER || $field->ftype==FLOATING || $field->ftype==DATE) {
                                foreach($fieldvalues as $fieldvalue) {
                                    if($tasks[$fieldvalue->taskid]->hidden==0) {
                                        if($data->$fieldelementname==$fieldvalue->value)
                                            for($i=0;$i<5;$i++)
                                                $satisfyingtasks[]=$fieldvalue->taskid;
                                        else
                                            if($data->$fieldelementname==0)
                                                continue;
                                            else
                                                for($dif=1;$dif<5;$dif++)
                                                    if(abs($data->$fieldelementname-$fieldvalue->value)/$data->$fieldelementname<(0.1*$dif)) {
                                                        for($i=0;$i<5-$dif;$i++)
                                                            $satisfyingtasks[]=$fieldvalue->taskid;                                                                                   
                                                        break;
                                                    }
                                    }
                                }                            
                            }
                        }
                    }
                    
                    //echo implode($satisfyingtasks).'<br>';
                    if($satisfyingtasks) {
                        $taskid=$satisfyingtasks[0];
                        $tasktimesmet=1;
                        $tmp=0;
                        for($i=0;$i<count($satisfyingtasks);$i++) {
                            for($j=0;$j<count($satisfyingtasks);$j++) {
                                if($satisfyingtasks[$i]==$satisfyingtasks[$j])
                                    $tmp++;
                                if($tmp>$tasktimesmet) {
                                    $taskid=$satisfyingtasks[$i];
                                    $tasktimesmet=$tmp;
                                }
                            }
                             $tasktimesmet=$tmp;
                             $tmp=0;
                        }
                        //echo 'task with id'.$taskid.' was met '.$tasktimesmet.' times';
                        $poasmodel->bind_task_to_assignee($USER->id,$taskid);
                        redirect(new moodle_url('view.php',array('id'=>$this->cm->id,'tab'=>'view')),null,0);
                    }
                    else echo get_string('nosatisfyingtasks','poasassignment');
                }
            }
            return $mform;
            
        }
    }
    function view() {
        global $DB,$OUTPUT,$USER,$PAGE;
        $hascapmanage=has_capability('mod/poasassignment:managetasks',
                            get_context_instance(CONTEXT_MODULE,$this->cm->id));
        $mform=$this->parameter_search();
        $poasmodel = poasassignment_model::get_instance($this->poasassignment);
        if($this->poasassignment->howtochoosetask==STUDENTSCHOICE || $hascapmanage) {
            $table = new flexible_table('mod-poasassignment-tasks');
            $fields = $DB->get_records('poasassignment_fields',array('poasassignmentid'=>$this->poasassignment->id));
            
            
            $columns[]='name';
            $columns[]='description';
            $headers[]='name';
            $headers[]='description';
            
            if(count($fields)) {
                foreach($fields as $field) {
                    if($field->showintable>0) {
                        if($hascapmanage ||(!$hascapmanage && !$field->secretfield)) {
                            $columns[]=$field->name;
                            if(has_capability('mod/poasassignment:seefielddescription',get_context_instance(CONTEXT_MODULE,$this->cm->id)))
                                $headers[]=$field->name.' '.$poasmodel->help_icon($field->description);
                            else
                                $headers[]=$field->name;
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
            $tasks = $DB->get_records('poasassignment_tasks',array('poasassignmentid'=>$this->poasassignment->id));
            foreach($tasks as $task) {
                if(!$hascapmanage && $task->hidden)
                    continue;
                $row=array();
                $viewurl = new moodle_url('taskview.php',array('taskid'=>$task->id,'id'=>$this->cm->id),'v','get');
                /* $viewicon = '<a href="'.$viewurl.'">'.'<img src="'.$OUTPUT->pix_url('t/hide').
                                '" class="iconsmall" alt="'.get_string('view').'" title="'.get_string('view').'" /></a>'; */
                $viewicon = '<a href="'.$viewurl.'">'.'<img src="'.$OUTPUT->pix_url('view','poasassignment').
                                '" class="iconsmall" alt="'.get_string('view').'" title="'.get_string('view').'" /></a>';
                $namecolumn=$task->name.' '.$viewicon;
                if($task->hidden)
                    $namecolumn='<font color="#AAAAAA">'.$namecolumn;
                if($this->poasassignment->howtochoosetask==STUDENTSCHOICE) {
                    $takeurl = new moodle_url('warning.php?id='.$this->cm->id.'&action=taketask&taskid='.$task->id.'&userid='.$USER->id);
                    $takeicon= '<a href="'.$takeurl.'">'.'<img src="'.$OUTPUT->pix_url('taketask','poasassignment').
                                '" class="iconsmall" alt="'.get_string('view').'" title="'.get_string('taketask','poasassignment').'" /></a>';
                    $namecolumn.=' '.$takeicon;
                }
                if($hascapmanage) {
                   
                    $updateurl = new moodle_url('taskedit.php',array('taskid'=>$task->id,'mode'=>EDIT_MODE,'id'=>$this->cm->id),'u','get');
                    $deleteurl = new moodle_url('taskedit.php',array('taskid'=>$task->id,'mode'=>DELETE_MODE,'id'=>$this->cm->id),'d','get');
                    
                    $showicon = '<a href="'.$updateurl.'">'.'<img src="'.$OUTPUT->pix_url('t/show').
                                '" class="iconsmall" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
                    $hideicon = '<a href="'.$updateurl.'">'.'<img src="'.$OUTPUT->pix_url('t/hide').
                                '" class="iconsmall" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
                    $updateicon = '<a href="'.$updateurl.'">'.'<img src="'.$OUTPUT->pix_url('t/edit').
                                '" class="iconsmall" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
                    $deleteicon = '<a href="'.$deleteurl.'">'.'<img src="'.$OUTPUT->pix_url('t/delete').
                                '" class="iconsmall" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>';
                     if($task->hidden) {
                        $showurl= new moodle_url('taskedit.php',array('taskid'=>$task->id,'mode'=>SHOW_MODE,'id'=>$this->cm->id),'u','get');
                        $showicon = '<a href="'.$showurl.'">'.'<img src="'.$OUTPUT->pix_url('t/show').
                                '" class="iconsmall" alt="'.get_string('show').'" title="'.get_string('show').'" /></a>';
                        $namecolumn.=$showicon;
                    }
                    else {
                        $hideurl = new moodle_url('taskedit.php',array('taskid'=>$task->id,'mode'=>HIDE_MODE,'id'=>$this->cm->id),'u','get');
                        $hideicon = '<a href="'.$hideurl.'">'.'<img src="'.$OUTPUT->pix_url('t/hide').
                                '" class="iconsmall" alt="'.get_string('hide').'" title="'.get_string('hide').'" /></a>';
                         $namecolumn.=$hideicon;
                    }
                    $namecolumn.=' '.$updateicon.' '.$deleteicon;
                    
                }
                if($task->hidden)
                    $namecolumn.='</font>';
                $row[]=$namecolumn;
                $row[]=shorten_text($task->description);
                foreach ($fields as $field) {
                    if($field->showintable>0) {
                        if($hascapmanage ||(!$hascapmanage && !$field->secretfield)) {
                            $taskvalue=$DB->get_record('poasassignment_task_values',
                                                        array('taskid'=>$task->id,'fieldid'=>$field->id,'assigneeid'=>null));
                            if(!$taskvalue)
                                $taskvalue->value='null';
                            else {
                                if($field->random)
                                    $taskvalue->value= 'random';
                                else {
                                    if(isset($taskvalue->value)) {
                                        if($field->ftype==TEXT)
                                            $taskvalue->value=shorten_text($taskvalue->value);
                                        if($field->ftype==LISTOFELEMENTS) {
                                            $variants=$poasmodel->get_field_variants($field->id);
                                            $variant=$variants[$taskvalue->value];
                                            //$variant = $poasmodel->get_variant($taskvalue->value,$field->variants);
                                            $taskvalue->value=$variant;
                                        }
                                        if($field->ftype==MULTILIST) {
                                            $tok = strtok($taskvalue->value,',');
                                            $opts=array();
                                            while(strlen($tok)>0) {
                                                $opts[]=$tok;
                                                $tok=strtok(',');
                                            }
                                            $taskvalue->value='';
                                            $variants=$poasmodel->get_field_variants($field->id);
                                            foreach($opts as $opt) {
                                                //$variant = $poasmodel->get_variant($opt,$poasmodel->get_field_variants($field->id));
                                                $variant=$variants[$opt];
                                                $taskvalue->value.=$variant.'<br>';
                                            }
                                        }
                                        if($field->ftype==DATE) {
                                            $taskvalue->value=userdate($taskvalue->value);
                                        }
                                        if($field->ftype==FILE) {
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
                //echo format_module_intro('poassignment', $task->description, $this->cm->id);
                $table->add_data($row);
            }
            

            $table->print_html();
        }
        if($hascapmanage) {
            $id = $this->cm->id;
            echo $OUTPUT->single_button(new moodle_url('taskedit.php?id='.$id.'?mode='.ADD_MODE), 
                                                        get_string('addbuttontext','poasassignment'));
        }
        if($this->poasassignment->howtochoosetask==FULLRANDOM) {
            if(!$DB->record_exists('poasassignment_assignee',array('poasassignmentid'=>$this->poasassignment->id,'userid'=>$USER->id))) {
                $tasks=$DB->get_records('poasassignment_tasks',array('poasassignmentid'=>$this->poasassignment->id,'hidden'=>0));
                $tasksarray=array();
                foreach($tasks as $task) $tasksarray[]=$task->id;
                if(count($tasksarray)>0) {
                    $taskid=$tasksarray[rand(0,count($tasksarray)-1)];
                    $poasmodel->bind_task_to_assignee($USER->id,$taskid);
                    redirect(new moodle_url('view.php',array('id'=>$this->cm->id,'tab'=>'view')),null,0);
                    /* echo $OUTPUT->single_button(new moodle_url('warning.php?id='.$id.
                                                        '&action=taketaskconfirmed&userid='.$USER->id.
                                                        '&taskid='.$taskid), 
                                                            get_string('getrandomtask','poasassignment'),'post'); */
                }
            }
        }
        if($this->poasassignment->howtochoosetask==PARAMETERRANDOM)
            $mform->display();
        
    }
}

class parametersearch_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        $poasmodel= poasassignment_model::get_instance();
        global $DB;
        $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$instance['poasassignmentid'],'searchparameter'=>1));
        if($fields) {
            $mform->addElement('header','header',get_string('inputparameters','poasassignment'));
            $poasmodel= poasassignment_model::get_instance();
            foreach($fields as $field) {
                if($field->ftype!=MULTILIST && $field->ftype!=LISTOFELEMENTS)
                    if($field->ftype==DATE) 
                        $mform->addElement('date_selector','field'.$field->id,$field->name);
                    else {
                        if(has_capability('mod/poasassignment:seefielddescription',get_context_instance(CONTEXT_MODULE,$instance['id'])))
                            $mform->addElement('text','field'.$field->id,$field->name.'('.$poasmodel->ftypes[$field->ftype].')'.$poasmodel->help_icon($field->description));
                        else
                            $mform->addElement('text','field'.$field->id,$field->name.'('.$poasmodel->ftypes[$field->ftype].')');
                    }
                else {                    
                    $opt=$poasmodel->get_field_variants($field->id);
                    $mform->addElement('select','field'.$field->id,$field->name,$opt);                
                }
            }
            
            $mform->addElement('hidden', 'id', $instance['id']);
            $mform->setType('id', PARAM_INT);
            
            $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
            $mform->setType('poasassignmentid', PARAM_INT);
            
            $mform->addElement('hidden', 'tab', 'tasks');
            $mform->setType('tab', PARAM_TEXT);
            
            $this->add_action_buttons(false, get_string('getrandomtask', 'poasassignment'));
        }
    }
    
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        global $DB;
        $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$data['poasassignmentid'],'searchparameter'=>1));
        foreach($fields as $field) {
            if($field->ftype==FLOATING && !is_numeric($data['field'.$field->id])) {
                if(strlen($data['field'.$field->id])>0) {
                    $errors['field'.$field->id]=get_string('errormustbefloat','poasassignment');
                    return $errors;
                }
            }
            if($field->ftype==NUMBER && !is_int($data['field'.$field->id])) {
                if(strlen($data['field'.$field->id])>0) {
                    $errors['field'.$field->id]=get_string('errormustbeint','poasassignment');
                    return $errors;
                }
            }
            
        }
       
        return true;
    }
}