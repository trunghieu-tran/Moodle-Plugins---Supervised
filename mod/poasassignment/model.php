<?php
require_once('lib.php');
require_once('answer/answer.php');
require_once(dirname(dirname(dirname(__FILE__))).'/comment/lib.php');
class poasassignment_model {
    protected static $instance;
    private function __construct($poasassignment=null) {
        global $DB,$USER;
        $this->poasassignment = $poasassignment;
        $this->ftypes = array(get_string('char','poasassignment'),
                        get_string('text','poasassignment'),
                        get_string('float','poasassignment'),
                        get_string('int','poasassignment'),
                        get_string('date','poasassignment'),
                        get_string('file','poasassignment'),
                        get_string('list','poasassignment'),
                        get_string('multilist','poasassignment'));
        if(isset($this->poasassignment->id))
            $this->assignee=$DB->get_record('poasassignment_assignee',array('userid'=>$USER->id,'poasassignmentid'=>$this->poasassignment->id));
        if(!$this->assignee)
            $this->assignee->id=0;
    }
    
    static function &get_instance($poasassignment=null) {
        if(self::$instance==null) {
            self::$instance = new self($poasassignment);
        }
        return self::$instance;
    }
    var $poasassignment;
    var $ftypes;
    var $context;
    var $assignee;
    
    function add_instance() {
        global $DB;
        $this->poasassignment->flags=$this->configure_flags();
        $this->poasassignment->timemodified=time();
        $this->poasassignment->id = $DB->insert_record('poasassignment', $this->poasassignment);
        $plugins = $DB->get_records('poasassignment_plugins');
        foreach($plugins as $plugin) {
            require_once($plugin->path);
            $poasassignmentplugin = new $plugin->name();
            $poasassignmentplugin->configure_flag($this->poasassignment);
            $poasassignmentplugin->save_settings($this->poasassignment,$this->poasassignment->id);
        }
        $this->context = get_context_instance(CONTEXT_MODULE, $this->poasassignment->coursemodule);
        $this->save_files($this->poasassignment->poasassignmentfiles,'poasassignmentfiles',0);
        return $this->poasassignment->id;
    }
    function update_instance() {
        global $DB;
        $this->poasassignment->flags=$this->configure_flags();

        $plugins = $DB->get_records('poasassignment_plugins');
        foreach($plugins as $plugin) {
            require_once($plugin->path);
            $poasassignmentplugin = new $plugin->name();
            $poasassignmentplugin->configure_flag($this->poasassignment);
            $poasassignmentplugin->update_settings($this->poasassignment);
        }
        
        $poasassignmentid=$DB->update_record('poasassignment', $this->poasassignment);
        
        $cm = get_coursemodule_from_instance('poasassignment',$this->poasassignment->id);
        $this->delete_files($cm->id,'poasassignment',0);
        $this->context = get_context_instance(CONTEXT_MODULE, $this->poasassignment->coursemodule);
        $this->save_files($this->poasassignment->poasassignmentfiles,'poasassignmentfiles',0);
        return $this->poasassignment->id;
    }
    
    function delete_instance($id) {
        global $DB;
        if (! $this->poasassignment = $DB->get_record('poasassignment', array('id' => $id))) {
            return false;
        }
        $cm = get_coursemodule_from_instance('poasassignment',$id);
        $this->poasassignment=$DB->get_record('poasassignment',array('id'=>$id));
        
        $poasassignment_answer= new poasassignment_answer();
        $poasassignment_answer->delete_settings($this->poasassignment->id);
        
        $this->delete_files($cm->id);
        $DB->delete_records('poasassignment', array('id' => $this->poasassignment->id));
        $DB->delete_records('poasassignment_tasks', array('poasassignmentid' => $this->poasassignment->id));
        $types=$DB->get_records('poasassignment_type_settings', array('poasassignmentid' => $this->poasassignment->id));
        foreach( $types as $type) {
            $DB->delete_records('poasassignment_plugins', array('id' => $type->pluginid));
        }
        $DB->delete_records('poasassignment_type_settings', array('poasassignmentid' => $this->poasassignment->id));
        $DB->delete_records('poasassignment_criterions', array('poasassignmentid' => $this->poasassignment->id));
        $fields=$DB->get_records('poasassignment_fields', array('poasassignmentid' => $this->poasassignment->id));
        foreach( $fields as $field) {
            $DB->delete_records('poasassignment_task_values', array('fieldid' => $field->id));
        }
        $DB->delete_records('poasassignment_fields', array('poasassignmentid' => $this->poasassignment->id));
        return true;
    }
    
    function configure_flags() {
        $flags = 0;
        if (isset($this->poasassignment->preventlatechoice)) {
            $flags+=PREVENT_LATE_CHOICE;
            unset($this->poasassignment->preventlatechoice);
        }
        if (isset($this->poasassignment->randomtasksafterchoicedate)) {
            $flags+=RANDOM_TASKS_AFTER_CHOICEDATE;
            unset($this->poasassignment->randomtasksafterchoicedate);
        }
        if (isset($this->poasassignment->preventlate)) {
            $flags+=PREVENT_LATE;
            unset($this->poasassignment->preventlate);
        }
        if (isset($this->poasassignment->severalattempts)) {
            $flags+=SEVERAL_ATTEMPTS;
            unset($this->poasassignment->severalattempts);
        }
        if (isset($this->poasassignment->notifyteachers)) {
            $flags+=NOTIFY_TEACHERS;
            unset($this->poasassignment->notifyteachers);
        }
        if (isset($this->poasassignment->notifystudents)) {
            $flags+=NOTIFY_STUDENTS;
            unset($this->poasassignment->notifystudents);
        }
        if (isset($this->poasassignment->activateindividualtasks)) {
            $flags+=ACTIVATE_INDIVIDUAL_TASKS;
            unset($this->poasassignment->activateindividualtasks);
        }
        if (isset($this->poasassignment->secondchoice)) {
            $flags+=SECOND_CHOICE;
            unset($this->poasassignment->secondchoice);
        }
        if (isset($this->poasassignment->teacherapproval)) {
            $flags+=TEACHER_APPROVAL;
            unset($this->poasassignment->teacherapproval);
        }
        if (isset($this->poasassignment->newattemptbeforegrade)) {
            $flags+=ALL_ATTEMPTS_AS_ONE;
            unset($this->poasassignment->newattemptbeforegrade);
        }
        return $flags;
    }
    
    function save_files($draftitemid,$filearea,$itemid) {
        global $DB;
        $fs = get_file_storage();
        if(!isset($this->context)) {
            $cm = get_coursemodule_from_instance('poasassignment',$this->poasassignment->id);
            //echo $this->poasassignment->id;
            $this->context = get_context_instance(CONTEXT_MODULE, $cm->id);
        }
        //$this->context = get_context_instance(CONTEXT_MODULE, $this->poasassignment->coursemodule);
        if ($draftitemid) {
            file_save_draft_area_files($draftitemid, $this->context->id, 
                    'mod_poasassignment', 
                    $filearea, 
                    $itemid, 
                    array('subdirs'=>true));
                    }
    }
    
    function delete_files($cmid,$filearea=false,$itemid=false) {
        global $DB;
        $fs = get_file_storage();
        $this->context = get_context_instance(CONTEXT_MODULE, $cmid);
        return $fs->delete_area_files($this->context->id,$filearea,$itemid);
    }
    
    function get_poasassignments_files_urls($cm) {
        $fs = get_file_storage();
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        $dir =$fs->get_area_tree($context->id, 'mod_poasassignment', 'poasassignmentfiles', 0);
        $files = $fs->get_area_files($context->id, 'mod_poasassignment', 'poasassignmentfiles', 0, 'sortorder');
        if (count($files) >= 1) {
            $file = array_pop($files);
        }
        $urls;
        $urls[]=$this->view_poasassignment_file($dir,$urls);
    }
    function view_poasassignment_file($dir,$urls) {
        global $CFG;
        foreach ($dir['subdirs'] as $subdir) {
            $urls[]=$this->view_poasassignment_file($subdir,$urls);
            return $urls;
        }
        foreach ($dir['files'] as $file) {

            $path = '/'.$this->context->id.'/mod_poasassignment/poasassignmentfiles/0'.$file->get_filepath().$file->get_filename();
            $url = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path, false);
            $filename = $file->get_filename();
            $file->fileurl = html_writer::link($url, $filename);
            return $file->fileurl.'<br>';
        }
    }
    function add_task($data) {
        global $DB;
        $data->poasassignmentid=$this->poasassignment->id;
        //$poasassignment = $DB->get_record('poasassignment',array('id'=>$this->poasassignment->id));
        $data->deadline = $this->poasassignment->deadline;
        $data->hidden = isset($data->hidden);
        $taskid=$DB->insert_record('poasassignment_tasks',$data);
        $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$this->poasassignment->id));
        foreach ($fields as $field) {
            $fieldvalue->taskid=$taskid;
            $fieldvalue->fieldid=$field->id;
            $value = 'field'.$field->id;
            if(!$field->random)
                $fieldvalue->value=$data->$value;
            else    
                $fieldvalue->value=null;
            $multilistvalue='';
            if($field->ftype==MULTILIST) {
                for($i=0;$i<count($fieldvalue->value);$i++) $multilistvalue.=$fieldvalue->value[$i].',';
                $fieldvalue->value=$multilistvalue;
                
            }
                
            $taskvalueid=$DB->insert_record('poasassignment_task_values',$fieldvalue);
            if($field->ftype==FILE) {
                $this->save_files($data->$value,'poasassignmenttaskfiles',$taskvalueid);
            }
        }
        return $taskid;        
    }
    
    function update_task($taskid,$task) {
        global $DB;
        $task->id=$taskid;
        $task->poasassignmentid=$this->poasassignment->id;
        $task->deadline = $this->poasassignment->deadline;
        $task->hidden = isset($task->hidden);
        $DB->update_record('poasassignment_tasks',$task);
        $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$this->poasassignment->id));
        foreach ($fields as $field) {
            $fieldvalue->taskid=$taskid;
            $fieldvalue->fieldid=$field->id;
            $value = 'field'.$field->id;
            if(!$field->random)
                $fieldvalue->value=$task->$value;
            else    
                $fieldvalue->value=null;
            
            if($field->ftype==MULTILIST) {
                $multilistvalue='';
                for($i=0;$i<count($fieldvalue->value);$i++) $multilistvalue.=$fieldvalue->value[$i].',';
                $fieldvalue->value=$multilistvalue;                
            }
            
            if($getrec=$DB->get_record('poasassignment_task_values',array('taskid'=>$taskid,'fieldid'=>$field->id))) {
                $fieldvalue->id=$getrec->id;
                $taskvalueid=$DB->update_record('poasassignment_task_values',$fieldvalue);
            }
            else
                $taskvalueid=$DB->insert_record('poasassignment_task_values',$fieldvalue);
                
            if($field->ftype==5) {
                $cm = get_coursemodule_from_instance('poasassignment',$this->poasassignment->id);
                //$this->delete_files($cm->id,'poasassignmenttaskfiles',$taskvalueid);
                //$this->save_files($task->$value,'poasassignmenttaskfiles',$taskvalueid);
            }
            
        }
    }
    
    function delete_task($taskid) {
        global $DB;
        $DB->delete_records('poasassignment_tasks',array('id'=>$taskid));
        $taskvalues = $DB->get_records('poasassignment_task_values',array('taskid'=>$taskid));
        $cm = get_coursemodule_from_instance('poasassignment',$this->poasassignment->id);
        foreach($taskvalues as $taskvalue) {
            $field=$DB->get_record('poasassignment_fields',array('id'=>$taskvalue->fieldid));
            if($field->ftype==FILE);
                $this->delete_files($cm->id,'poasassignmenttaskfiles',$taskvalue->id);
        }
        $DB->delete_records('poasassignment_task_values',array('taskid'=>$taskid));
    }
    
    function get_task_values($taskid) {
        global $DB;
        $task = $DB->get_record('poasassignment_tasks',array('id'=>$taskid));
        $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$this->poasassignment->id));
        foreach($fields as $field) {
            $name='field'.$field->id;
            if($field->ftype==STR || $field->ftype==TEXT || 
                        $field->ftype==FLOATING || $field->ftype==NUMBER ||
                        $field->ftype==DATE || !$field->random) {
                
                $value = $DB->get_record('poasassignment_task_values',array('fieldid'=>$field->id,'taskid'=>$taskid));
                if($value)
                    $task->$name=$value->value;
            }
            if($field->ftype==MULTILIST) {
                $value = $DB->get_record('poasassignment_task_values',array('fieldid'=>$field->id,'taskid'=>$taskid));
                if($value) {
                    $tok = strtok($value->value,',');
                    $opts=array();
                    while(strlen($tok)>0) {
                        $opts[]=$tok;
                        $tok=strtok(',');
                    }
                    $task->$name=$opts;
                }
            }
        }
        return $task;
    }
    function get_criterions_data() {
        global $DB;
        $criterions=$DB->get_records('poasassignment_criterions',array('poasassignmentid'=>$this->poasassignment->id));
        if($criterions) {
            $i=0;
            foreach($criterions as $criterion) {
                //echo $criterion->name;
                $data->name[$i]=$criterion->name;
                $data->description[$i]=$criterion->description;
                $data->weight[$i]=$criterion->weight;
                $data->source[$i]=$criterion->sourceid;
                $i++;
            }
            return $data;
        }
    }
    function save_criterion($data) {
        global $DB;
        $DB->delete_records('poasassignment_criterions',array('poasassignmentid'=>$this->poasassignment->id));
        $assignees=$DB->get_records('poasassignment_assignee',array('poasassignmentid'=>$this->poasassignment->id));
        foreach($assignees as $assignee) {
            $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assignee->id));
            $attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assignee->id,'attemptnumber'=>$attemptscount));
            $attempt->rating=null;
            $DB->update_record('poasassignment_attempts',$attempt);
        }
        for($i=0;$i<count($data->name);$i++) {
            if(isset($data->name[$i]) && strlen($data->name[$i])>0) {
                $rec->name=$data->name[$i];
                $rec->description=$data->description[$i];
                $rec->weight=$data->weight[$i];
                $rec->poasassignmentid=$this->poasassignment->id;
                //echo $data->source[$i];
                $rec->sourceid=$data->source[$i];
                $DB->insert_record('poasassignment_criterions',$rec);
            }
        }
        //return $DB->insert_record('poasassignment_criterions',$data);
    }
    
    function update_criterion($data) {
        global $DB;
        
        for($i=0;$i<count($data->name);$i++) {
            if(isset($data->name[$i]) && strlen($data->name[$i])>0) {
                $rec->name=$data->name[$i];
                $rec->description=$data->description[$i];
                $rec->weight=$data->weight[$i];
                $rec->poasassignmentid=$this->poasassignment->id;
                if(!isset($data->source[$i]))
                    $data->source[$i]=0;
                $DB->insert_record('poasassignment_criterions',$rec);
            }
        }
        $criterion->id=$criterionid;
        return $DB->update_record('poasassignment_criterions',$criterion);
    }
    function get_rating_data($assigneeid) {
        global $DB;
        $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assigneeid));
        $attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assigneeid,'attemptnumber'=>$attemptscount));
        $assignee=$DB->get_record('poasassignment_assignee',array('id'=>$attempt->assigneeid));
        $data->final=$assignee->finalized;
        if($ratingvalues=$DB->get_records('poasassignment_rating_values',array('attemptid'=>$attempt->id))) {
            foreach ($ratingvalues as $ratingvalue) {
                $field='criterion'.$ratingvalue->criterionid;
                $data->$field=$ratingvalue->value;    
            }
            return $data;
        }
    }
   
    function save_grade($assigneeid,$data) {
        echo 'save_grade';
        global $DB;
        $criterions=$DB->get_records('poasassignment_criterions',array('poasassignmentid'=>$this->poasassignment->id));
        $rating=0;
        $cm = get_coursemodule_from_instance('poasassignment',$this->poasassignment->id);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        
        $options->area    = 'poasassignment_comment';
        $options->pluginname = 'poasassignment';
        $options->context = $context;
        $options->showcount = true;
        
        $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assigneeid));
        $attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assigneeid,'attemptnumber'=>$attemptscount));
        foreach( $criterions as $criterion) {
            $elementname='criterion'.$criterion->id;
            $elementcommentname='criterion'.$criterion->id.'comment';
            
            //$elementweightname='criterion'.$criterion->id.'weight';
            if(!$DB->record_exists('poasassignment_rating_values',array('attemptid'=>$attempt->id,'criterionid'=>$criterion->id))) {
                echo 'new_rtask_value';
                $rec->attemptid=$attempt->id;
                $rec->criterionid=$criterion->id;
                $rec->assigneeid=$assigneeid;
                if($attempt->draft==0)
                    $rec->value=$data->$elementname;
                $ratingvalueid=$DB->insert_record('poasassignment_rating_values',$rec);
                
                
                $options->itemid  = $ratingvalueid;
                $comment = new comment($options);
                $comment->add($data->$elementcommentname);
                
            }
            else {
                echo 'updater_task value';
                $ratingvalue=$DB->get_record('poasassignment_rating_values',array('attemptid'=>$attempt->id,'criterionid'=>$criterion->id));
                if($attempt->draft==0)
                    $ratingvalue->value=$data->$elementname;
                $DB->update_record('poasassignment_rating_values',$ratingvalue);
                
                //$options->itemid  = $ratingvalue->id;
                //$comment = new comment($options);
                //$comment->add($data->$elementcommentname);
            }
            if($attempt->draft==0)
                $rating+=$data->$elementname*round($criterion->weight/$data->weightsum,2);
        }
        if($attempt->draft==0)
            $attempt->rating=$rating;
            echo $attempt->draft;
            echo $attempt->rating;
        $attempt->ratingdate=time();
        $DB->update_record('poasassignment_attempts',$attempt);
        $assignee=$DB->get_record('poasassignment_assignee',array('id'=>$assigneeid));
//        $assignee->rating=$rating;
        $assignee->finalized=isset($data->final);
        $DB->update_record('poasassignment_assignee',$assignee);
        if($this->poasassignment->flags&ALL_ATTEMPTS_AS_ONE) {
            $this->disable_previous_attempts($assignee->id);
        }
        $this->save_files($data->commentfiles_filemanager,'commentfiles',$attempt->id);
    }
    
    function disable_previous_attempts($attemptid) {
        global $DB;
        $attempts=$DB->get_records('poasassignment_attempts',array('id'=>$attemptid),'attemptnumber');
        $attempts=array_reverse($attempts);
        $i=0;   
        foreach($attempts as $attempt) {
            if($i==0)
                continue;
            if($DB->record_exists('poasassignment_task_values',array('attemptid'=>$attempt->id)))
                break;
            $attempt->disablepenalty=1;
            
            $DB->update_record('poasassignment_attempts',$attempt);
            $i++;
        }
        
    
    }
    function set_default_values_taskfields($default_values,$fieldid) {
        global $DB;
        $field = $DB->get_record('poasassignment_fields',array('id'=>$fieldid));
        $default_values['name']=$field->name;
        $default_values['ftype']=$field->ftype;
        $default_values['minvalue']=$field->minvalue;
        $default_values['maxvalue']=$field->maxvalue;
        $default_values['showintable']=$field->showintable;
        $default_values['searchparameter']=$field->searchparameter;
        return $default_values;
    }
    
    function get_variant($index,$variants) {
        $tok = strtok($variants,"\n");
        while(strlen($tok)>0) {
            $opt[]=$tok;
            $tok=strtok("\n");
        }
        if($index>=0 && $index <=count($opt) &&isset($index))
            return $opt[$index];
        else
            return get_string('erroroutofrange','poasassignment');
    }
    
    function get_field_variants($fieldid,$return=1,$separator="\n") {
        global $DB;
        $variants=$DB->get_records('poasassignment_variants',array('fieldid'=>$fieldid),'sortorder');
        if($variants) {
            $variantvalues=array();
            foreach ($variants as $variant) $variantvalues[]=$variant->value;
            if($return)
                return $variantvalues;
            else    
                return implode($separator,$variantvalues);
        }
        return '';
        
    }
    
     function add_task_field($data) {
        global $DB;
        $data->poasassignmentid=$this->poasassignment->id;
        $data->showintable=isset($data->showintable);
        $data->searchparameter=isset($data->searchparameter);
        $data->secretfield=isset($data->secretfield);
        $data->random=isset($data->random);
        
        $fieldid= $DB->insert_record('poasassignment_fields',$data);
        if($data->ftype==LISTOFELEMENTS || $data->ftype==MULTILIST) {
            $variants=explode("\n",$data->variants);
            $i=0;
            foreach($variants as $variant) {
                $rec->fieldid=$fieldid;
                $rec->sortorder=$i;
                $rec->value=$variant;
                $DB->insert_record('poasassignment_variants',$rec);
                $i++;
            }
        }
        if($data->ftype==FLOATING || $data->ftype==NUMBER) {
            if($data->maxvalue==$data->minvalue)
                $data->random=0;
        }
        $tasks=$DB->get_records('poasassignment_tasks',array('poasassignmentid'=>$this->poasassignment->id));
        foreach($tasks as $task) {
            $taskvalue->fieldid=$fieldid;
            $taskvalue->taskid=$task->id;
            $DB->insert_record('poasassignment_task_values',$taskvalue);
        }
        return $fieldid;
    }
    
    function update_task_field($fieldid,$field) {
        global $DB;
        $field->id=$fieldid;
        $field->showintable=isset($field->showintable);
        $field->searchparameter=isset($field->searchparameter);
        $field->secretfield=isset($field->secretfield);
        $field->random=isset($field->random);
        if($field->ftype==LISTOFELEMENTS || $field->ftype==MULTILIST) {
            $DB->delete_records('poasassignment_variants',array('fieldid'=>$field->id));
            
            $variants=explode("\n",$field->variants);
            $i=0;
            foreach($variants as $variant) {
                $rec->fieldid=$field->id;
                $rec->sortorder=$i;
                $rec->value=$variant;
                $DB->insert_record('poasassignment_variants',$rec);
                $i++;
            }
        }
        if($field->ftype==FLOATING || $field->ftype==NUMBER) {
            if($field->maxvalue==$field->minvalue)
                $field->random=0;
        }
        return $DB->update_record('poasassignment_fields',$field);
    }
    
    function delete_field($id) {
        global $DB;
        $cm = get_coursemodule_from_instance('poasassignment',$this->poasassignment->id);
        $taskvalues=$DB->get_records('poasassignment_task_values',array('fieldid'=>$id));
        $field=$DB->get_record('poasassignment_fields',array('id'=>$id));
        if($field->ftype==LISTOFELEMENTS || $field->ftype==MULTILIST) {
            $DB->delete_records('poasassignment_variants',array('fieldid'=>$id));
        }
        foreach($taskvalues as $taskvalue) {
            echo $field->ftype;
            if($field->ftype==FILE)
                $this->delete_files($cm->id,'poasassignmenttaskfiles',$taskvalue->id);
        }
        $DB->delete_records('poasassignment_fields',array('id'=>$id));
        $DB->delete_records('poasassignment_task_values',array('fieldid'=>$id));
        
        
    }
    
    function prepare_files($dir,$contextid,$filearea,$itemid) {
        global $CFG;
        foreach ($dir['subdirs'] as $subdir) {
            $this->prepare_files($subdir,$contextid,$filearea,$itemid);
        }
        foreach ($dir['files'] as $file) {
            $path = '/'.$contextid.'/mod_poasassignment/'.$filearea.'/'.$itemid.$file->get_filepath().$file->get_filename();
            $url = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path, false);
            $filename = $file->get_filename();
            $file->fileurl = html_writer::link($url, $filename);
        }
    }
    
    function htmllize_tree($dir) {
        global $CFG,$OUTPUT;
        $yuiconfig = array();
        $yuiconfig['type'] = 'html';

        if (empty($dir['subdirs']) and empty($dir['files'])) {
            return '';
        }
        $result = '<ul>';
        foreach ($dir['subdirs'] as $subdir) {
            $image = $OUTPUT->pix_icon("/f/folder", $subdir['dirname'], 'moodle', array('class'=>'icon'));
            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.$image.' '.s($subdir['dirname']).'</div> '.$this->htmllize_tree($subdir).'</li>';
        }

        foreach ($dir['files'] as $file) {
            $filename = $file->get_filename();
            $icon = substr(mimeinfo("icon", $filename), 0, -4);
            $image = $OUTPUT->pix_icon("/f/$icon", $filename, 'moodle', array('class'=>'icon'));
            $result .= '<li yuiConfig=\''.json_encode($yuiconfig).'\'><div>'.$image.' '.$file->fileurl.' </div></li>';
        }
        $result .= '</ul>';
        return $result;    
    }
    
    function view_files($contextid,$filearea,$itemid) {
        global $PAGE;
        $PAGE->requires->js('/mod/poasassignment/poasassignment.js');
        $fs = get_file_storage();
        $dir =$fs->get_area_tree($contextid, 'mod_poasassignment', $filearea, $itemid);
        $files = $fs->get_area_files($contextid, 'mod_poasassignment', $filearea, $itemid, 'sortorder');
        if (count($files) <1) 
            return;
        
        $this->prepare_files($dir,$contextid,$filearea,$itemid);
        $htmlid = 'poasassignment_files_tree_'.uniqid();        
        $PAGE->requires->js_init_call('M.mod_poasassignment.init_tree', array(true, $htmlid));
        $html = '<div id="'.$htmlid.'">';
        $html.=$this->htmllize_tree($dir);        
        $html .= '</div>';
        return $html;
    }
    
    function bind_task_to_assignee($userid,$taskid) {
        global $DB;
        $rec->userid=$userid;
        $rec->poasassignmentid=$this->poasassignment->id;
        $rec->taskid=$taskid;
        $this->assignee->id=$DB->insert_record('poasassignment_assignee',$rec);

        $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$this->poasassignment->id));
        foreach($fields as $field) {
            if($field->random) {
                if(!($field->minvalue==0 && $field->maxvalue==0)) {
                    if($field->ftype==NUMBER)
                        $randvalue=rand($field->minvalue,$field->maxvalue);
                    if($field->ftype==FLOATING)
                        $randvalue=(float)rand($field->minvalue*100,$field->maxvalue*100)/100;
                }
                else {
                    if($field->ftype==NUMBER)
                        $randvalue=rand();
                    if($field->ftype==FLOATING)
                        $randvalue=(float)rand()/100;
                }
                if($field->ftype==LISTOFELEMENTS) {
                    $tok = strtok($field->variants,"\n");
                    $count=0;
                    while($tok) {
                        $count++;
                        $tok=strtok("\n");
                    }
                        $randvalue=rand(0,$count-1);
                }
                $randrec->taskid=$taskid;
                $randrec->fieldid=$field->id;
                $randrec->value=$randvalue;
                $randrec->assigneeid=$this->assignee->id;                
                $DB->insert_record('poasassignment_task_values',$randrec);
            }
        }
    }
    
    function help_icon($text) {
        global $CFG,$OUTPUT,$PAGE;
        if(empty($text))
            return;
        $src = $OUTPUT->pix_url('help');
        //$title = $text;
        $alt = $text;
        
        $attributes = array('src'=>$src, 'alt'=>$alt, 'class'=>'iconhelp');
        $output = html_writer::empty_tag('img', $attributes);
        
        $url = new moodle_url('/mod/poasassignment/taskfielddescription.php', array('text' => $text));
        //$title = get_string('helpprefix2', '', trim($title, ". \t"));
        $title = get_string('taskfielddescription','poasassignment');
        $attributes = array('href'=>$url, 'title'=>$title);
        $id = html_writer::random_id('helpicon');
        $attributes['id'] = $id;
        $output = html_writer::tag('a', $output, $attributes);

        $PAGE->requires->js_init_call('M.util.help_icon.add', array(array('id'=>$id, 'url'=>$url->out(false))));

        return html_writer::tag('span', $output, array('class' => 'helplink'));
    }
  
    function get_statistics() {
        global $DB,$OUTPUT,$CFG;
        $html;
        $cm = get_coursemodule_from_instance('poasassignment',$this->poasassignment->id);
        $groupmode = groups_get_activity_groupmode($cm);
        $currentgroup = groups_get_activity_group($cm, true);
        groups_print_activity_menu($cm, $CFG->wwwroot . '/mod/poasassignment/view.php?id=' . $cm->id.'&tab=view');
        $context=get_context_instance(CONTEXT_MODULE,$cm->id);
        $notchecked=0;
        /// Get all ppl that are allowed to submit assignments
        if ($usersid = get_enrolled_users($context, 'mod/assignment:submit', $currentgroup, 'u.id')) {
            $usersid = array_keys($usersid);
            $count=count($usersid);
            foreach($usersid as $userid) {
                if($assignee=$DB->get_record('poasassignment_assignee',array('userid'=>$userid,'poasassignmentid'=>$this->poasassignment->id))) {
                    $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assignee->id));
                    if($attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assignee->id,'attemptnumber'=>$attemptscount))) {
                        if($attempt->attemptdate>$attempt->ratingdate || !isset($attempt->rating))
                        $notchecked++;
                    }
                }
            }
        }
        $html = $notchecked.' '.get_string('of','poasassignment').' '.$count.' '.get_string('needgrade','poasassignment');
        $submissionsurl = new moodle_url('view.php',array('id'=>$cm->id,'tab'=>'submissions')); 
        return "<align='right'>".html_writer::link($submissionsurl,$html);
    }
    
    function get_penalty($attemptid) {
        global $DB;
        $currentattempt=$DB->get_record('poasassignment_attempts',array('id'=>$attemptid));
        $attempts=$DB->get_records('poasassignment_attempts',array('assigneeid'=>$currentattempt->assigneeid),'attemptnumber');
        $realnumber=$currentattempt->attemptnumber;
        foreach($attempts as $attempt) {
            if($attempt->disablepenalty==1) {
                $realnumber--;
            }
        }
        if($this->poasassignment->penalty*($realnumber-1)>=0)
            return $this->poasassignment->penalty*($realnumber-1);
        else return 0;
        return ;
    }
}