<?php
require_once('lib.php');
require_once('answer/answer.php');
require_once(dirname(dirname(dirname(__FILE__))).'/comment/lib.php');
/**
 * Main DB-work class. Singletone
 */
class poasassignment_model {

    /**
     * Poasassignment instance
     */
    var $poasassignment;

    /**
     * Types of fields of tasks
     * @var array
     */
    var $ftypes;

    /**
     * Context of poasassignment instance
     */
    var $context;

    private $cm;
    private $course;
     /**
     * Context of poasassignment instance
     */
    var $assignee;

    /**
     * Answer plugins array
     * @var array
     */
    private $plugins=array();

    /**
     * Grader plugins array
     * @var array
     */
    private $graders=array();

    private $usedgraders;

    /**
     * Saves object of poasassignment_model class
     * @var poasassignment_model
     */
    protected static $model;

    public static $extpages = array('tasksfields' => 'pages/tasksfields.php',
                                    'tasks' => 'pages/tasks.php',
                                    'taskgiversettings' => 'pages/taskgiversettings.php',
                                    'view' => 'pages/view.php',
                                    'attempts' => 'pages/attempts.php',
                                    'graderresults' => 'pages/graderresults.php',
                                    'criterions' => 'pages/criterions.php',
                                    'graders' => 'pages/graders.php',
                                    'submissions' => 'pages/submissions.php',
                                    'grade' => 'pages/grade.php',
                                    'taskview' => 'pages/taskview.php',
                                    'submission' => 'pages/submission.php',
                                    'taskfieldedit' => 'pages/taskfieldedit.php',
                                    'categoryedit' => 'pages/categoryedit.php',
                                    'taskedit' => 'pages/taskedit.php',
                                    'criterionproblem' => 'pages/criterionproblem.php'
                                    );
    private static $flags = array('preventlatechoice' => PREVENT_LATE_CHOICE,
                           'randomtasksafterchoicedate' => RANDOM_TASKS_AFTER_CHOICEDATE,
                           'preventlate' => PREVENT_LATE,
                           'severalattempts' => SEVERAL_ATTEMPTS,
                           'notifyteachers' => NOTIFY_TEACHERS,
                           'notifystudents' => NOTIFY_STUDENTS,
                           'activateindividualtasks' => ACTIVATE_INDIVIDUAL_TASKS,
                           'secondchoice' => SECOND_CHOICE,
                           'teacherapproval' => TEACHER_APPROVAL,
                           'newattemptbeforegrade' => ALL_ATTEMPTS_AS_ONE,
                           'finalattempts' => MATCH_ATTEMPT_AS_FINAL);

    /**
     * Constructor. Cannot be called outside of the class
     * @param $poasassignment module instance
     */
    private function __construct($poasassignment = null) {
        //echo 'creating';
        global $DB,$USER;
        $this->poasassignment = $poasassignment;
        if (isset($this->poasassignment->id)) {
            $this->assignee=$DB->get_record('poasassignment_assignee',array('userid'=>$USER->id,'poasassignmentid'=>$this->poasassignment->id));
        }
        else {
            //echo 'Constructing model without id';
        }
        if (!$this->assignee)
            $this->assignee->id=0;
        $this->initArrays();
    }
    /**
     * Method is used instead of constructor. If poasassignment_model
     * object exists, returns it, otherwise creates object and returns it.
     * @param $poasassignment module instance
     * @return poasassignment_model
     */
    static function &get_instance($poasassignment=null) {
        if (self::$model==null) {
            self::$model = new self($poasassignment);
        }
        return self::$model;
    }

    public function cash_instance($id) {
        global $DB;
        if (!$DB->record_exists('poasassignment', array('id' => $id))) {
            print_error('nonexistentmoduleinstance', 'poasassignment');
        }
        else {
            if(!isset($this->poasassignment)
               || $this->poasassignment->id !== $id) {
                $this->poasassignment = $DB->get_record('poasassignment', array('id' => $id));
                $this->course = $DB->get_record('course',
                                                array('id' => $this->poasassignment->course),
                                                '*',
                                                MUST_EXIST);
                $this->cm = get_coursemodule_from_instance('poasassignment',
                                                           $this->poasassignment->id,
                                                           $this->course->id,
                                                           false,
                                                           MUST_EXIST);
                $this->context = get_context_instance(CONTEXT_MODULE,$this->cm->id);
                //echo 'change';
            }
        }
        //echo "now i store instance $id";
    }
    public function cash_assignee_by_user_id($userid) {
        global $DB;
        $this->assignee=$DB->get_record('poasassignment_assignee',
                                        array('userid'=>$userid,'poasassignmentid'=>$this->poasassignment->id));
        if(!$this->assignee) {
            $this->assignee->id = 0;
        }
    }
    private function initArrays() {
        global $DB;
        $this->ftypes = array(get_string('char','poasassignment'),
                              get_string('text','poasassignment'),
                              get_string('float','poasassignment'),
                              get_string('int','poasassignment'),
                              get_string('date','poasassignment'),
                              get_string('file','poasassignment'),
                              get_string('list','poasassignment'),
                              get_string('multilist','poasassignment'));

        $this->plugins=$DB->get_records('poasassignment_answers');
        $this->graders = $DB->get_records('poasassignment_graders');
        $this->taskgivers = $DB->get_records('poasassignment_taskgivers');
    }
    /**
     * Returns poasassignment answer plugins
     * @return array
     */
    public function get_plugins() {
        if (!$this->plugins)
            $this->plugins=$DB->get_records('poasassignment_answers');
        return $this->plugins;
    }

    public function get_poasassignment() {
        return $this->poasassignment;
    }
    public function get_cm() {
        return $this->cm;
    }
    public function get_course() {
        return $this->course;
    }
    public function get_context() {
        return $this->context;
    }

    public function get_graders() {
        return $this->graders;
    }

    public function has_flag($flag) {
        return (isset($this->poasassignment) && $this->poasassignment->flags & $flag);
    }

    public function get_assigneeid() {
        return $this->assignee->id;
    }
    /**
     * Inserts poasassignment data into DB
     * @return int poasassignment id
     */
    function add_instance() {
        global $DB;
        $this->poasassignment->flags = self::configure_flags($this->poasassignment);
        $this->poasassignment->timemodified = time();
        if(!isset($this->poasassignment->taskgiverid)) {
            $this->poasassignment->taskgiverid = 0;
        }

        $this->poasassignment->id = $DB->insert_record('poasassignment', $this->poasassignment);
        foreach ($this->plugins as $plugin) {
            require_once($plugin->path);
            $poasassignmentplugin = new $plugin->name();
            $poasassignmentplugin->configure_flag($this->poasassignment);
            $poasassignmentplugin->save_settings($this->poasassignment,$this->poasassignment->id);
        }
        foreach ($this->graders as $graderrecord) {
            require_once($graderrecord->path);
            $gradername = $graderrecord->name;
            if (isset($this->poasassignment->$gradername)) {
                $rec = new stdClass();
                $rec->poasassignmentid = $this->poasassignment->id;
                $rec->graderid = $graderrecord->id;
                $DB->insert_record('poasassignment_used_graders',$rec);
            }
            unset($this->poasassignment->$gradername);
        }
        $this->context = get_context_instance(CONTEXT_MODULE, $this->poasassignment->coursemodule);
        $this->save_files($this->poasassignment->poasassignmentfiles, 'poasassignmentfiles', 0);

        // Create 1 criterion
        $criterion = new stdClass();
        $criterion->name = get_string('standardcriterionname', 'poasassignment');
        $criterion->description = get_string('standardcriteriondesc', 'poasassignment');
        $criterion->weight = 1;
        $criterion->graderid = 0;
        $criterion->poasassignmentid = $this->poasassignment->id;
        $DB->insert_record('poasassignment_criterions', $criterion);
        //$this->grade_item_update();
        return $this->poasassignment->id;
    }

    /**
     * Updates poasassignment data in DB
     * @return int poasassignment id
     */
    function update_instance() {
        global $DB;
        $this->poasassignment->flags = self::configure_flags($this->poasassignment);
        $this->poasassignment->timemodified = time();
        if(!isset($this->poasassignment->taskgiverid)) {
            $this->poasassignment->taskgiverid = 0;
        }


        foreach ($this->plugins as $plugin) {
            require_once($plugin->path);
            $poasassignmentplugin = new $plugin->name();
            $poasassignmentplugin->configure_flag($this->poasassignment);
            $poasassignmentplugin->update_settings($this->poasassignment);
        }
        foreach ($this->graders as $graderrecord) {
            require_once($graderrecord->path);
            $gradername = $graderrecord->name;

            $rec = new stdClass();
            $rec->poasassignmentid = $this->poasassignment->id;
            $rec->graderid = $graderrecord->id;

            $isgraderused = $DB->record_exists('poasassignment_used_graders',
                                               array('poasassignmentid' => $rec->poasassignmentid,
                                                     'graderid' => $rec->graderid));
            if (isset($this->poasassignment->$gradername)) {
                if (!$isgraderused)
                    $DB->insert_record('poasassignment_used_graders',$rec);
            }
            else {
                if ($isgraderused)
                    $DB->delete_records('poasassignment_used_graders',
                                               array('poasassignmentid' => $rec->poasassignmentid,
                                                     'graderid' => $rec->graderid));
            }
            unset($this->poasassignment->$gradername);
        }
        //$this->poasassignment->taskgiverid++;
        $oldpoasassignment = $DB->get_record('poasassignment', array('id' => $this->poasassignment->id));
        if($oldpoasassignment->taskgiverid != $this->poasassignment->taskgiverid && $oldpoasassignment->taskgiverid > 0) {
            $this->delete_taskgiver_settings($oldpoasassignment->id, $oldpoasassignment->taskgiverid);
        }
        $poasassignmentid = $DB->update_record('poasassignment', $this->poasassignment);

        $cm = get_coursemodule_from_instance('poasassignment', $this->poasassignment->id);
        $this->delete_files($cm->id, 'poasassignment', 0);
        $this->context = get_context_instance(CONTEXT_MODULE, $this->poasassignment->coursemodule);
        $this->save_files($this->poasassignment->poasassignmentfiles, 'poasassignmentfiles', 0);
        return $this->poasassignment->id;
    }

    /**
     * Deletes poasassignment data from DB
     * @param int $id id of poasassignment to be deleted
     * @return bool
     */
    function delete_instance($id) {
        global $DB;
        if (! $DB->record_exists('poasassignment', array('id' => $id))) {
            return false;
        }
        $cm = get_coursemodule_from_instance('poasassignment', $id);
        $this->poasassignment=$DB->get_record('poasassignment',array('id'=>$id));

        $poasassignment_answer= new poasassignment_answer();
        $poasassignment_answer->delete_settings($id);

        $this->delete_files($cm->id);
        $DB->delete_records('poasassignment', array('id' => $id));
        $DB->delete_records('poasassignment_tasks', array('poasassignmentid' => $id));
        $types=$DB->get_records('poasassignment_ans_stngs', array('poasassignmentid' => $id));
        foreach ( $types as $type) {
            $DB->delete_records('poasassignment_answers', array('id' => $type->answerid));
        }
        $DB->delete_records('poasassignment_used_graders',array('poasassignmentid' => $id));
        $DB->delete_records('poasassignment_ans_stngs', array('poasassignmentid' => $id));
        $DB->delete_records('poasassignment_criterions', array('poasassignmentid' => $id));
        $fields=$DB->get_records('poasassignment_fields', array('poasassignmentid' => $id));
        foreach ( $fields as $field) {
            $DB->delete_records('poasassignment_task_values', array('fieldid' => $field->id));
            $DB->delete_records('poasassignment_variants', array('fieldid' => $field->id));
        }
        $DB->delete_records('poasassignment_fields', array('poasassignmentid' => $id));
        $DB->delete_records('poasassignment_assignee',array('poasassignmentid' => $id));
        $this->delete_taskgiver_settings($id, $this->poasassignment->taskgiverid);
        //delete_course_module($cm->id);
        return true;
    }

    /**
     * Converts some poasassignments settings into one variable
     * @return int
     */
    private static function configure_flags($instance) {
        $flags = 0;
        foreach(self::$flags as $field => $flag) {
            if (isset($instance->$field)) {
                $flags += $flag;
            }
        }
        return $flags;
    }

    function save_files($draftitemid,$filearea,$itemid) {
        global $DB;
        $fs = get_file_storage();
        if (!isset($this->context)) {
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

    /**
     * Adds task into DB
     * @param $data
     */
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
            if (!$field->random)
                $fieldvalue->value=$data->$value;
            else
                $fieldvalue->value=null;
            $multilistvalue='';
            if ($field->ftype==MULTILIST) {
                for($i=0;$i<count($fieldvalue->value);$i++) $multilistvalue.=$fieldvalue->value[$i].',';
                $fieldvalue->value=$multilistvalue;

            }

            $taskvalueid=$DB->insert_record('poasassignment_task_values',$fieldvalue);
            if ($field->ftype==FILE) {
                $this->save_files($data->$value,'poasassignmenttaskfiles',$taskvalueid);
            }
        }
        return $taskid;
    }

    function update_task($taskid, $task) {
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
            if (!$field->random)
                $fieldvalue->value=$task->$value;
            else
                $fieldvalue->value=null;

            if ($field->ftype==MULTILIST) {
                $multilistvalue='';
                for($i=0;$i<count($fieldvalue->value);$i++) $multilistvalue.=$fieldvalue->value[$i].',';
                $fieldvalue->value=$multilistvalue;
            }

            if ($getrec=$DB->get_record('poasassignment_task_values',array('taskid'=>$taskid,'fieldid'=>$field->id))) {
                $fieldvalue->id=$getrec->id;
                $taskvalueid=$DB->update_record('poasassignment_task_values',$fieldvalue);
            }
            else
                $taskvalueid=$DB->insert_record('poasassignment_task_values',$fieldvalue);

            if ($field->ftype==5) {
                $cm = get_coursemodule_from_instance('poasassignment',$this->poasassignment->id);
                //$this->delete_files($cm->id,'poasassignmenttaskfiles',$taskvalueid);
                //$this->save_files($task->$value,'poasassignmenttaskfiles',$taskvalueid);
            }

        }
    }

    /**
     * Delete task from DB, it's taskfield values, connected data from students
     * @param int $taskid task id
     */
    function delete_task($taskid) {
        global $DB;
        // Delete task record
        $DB->delete_records('poasassignment_tasks',array('id'=>$taskid));
        
        // Delete task values
        $taskvalues = $DB->get_records('poasassignment_task_values',array('taskid'=>$taskid));
        $cm = get_coursemodule_from_instance('poasassignment',$this->poasassignment->id);
        foreach ($taskvalues as $taskvalue) {
            $field=$DB->get_record('poasassignment_fields',array('id'=>$taskvalue->fieldid));
            if ($field->ftype==FILE);
                $this->delete_files($cm->id,'poasassignmenttaskfiles',$taskvalue->id);
        }
        $DB->delete_records('poasassignment_task_values',array('taskid'=>$taskid));
        
        // Delete task from students
        $assignees = $DB->get_records('poasassignment_assignee', array('taskid' => $taskid), '', 'id, taskid, taskindex');
        $DB->delete_records('poasassignment_assignee', array('taskid' => $taskid));
        
        //TODO удалять попытки и оценки студента по этому заданию
    }
    
    function get_task_values($taskid) {
        global $DB;
        $task = $DB->get_record('poasassignment_tasks',array('id'=>$taskid));
        $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$this->poasassignment->id));
        foreach ($fields as $field) {
            $name='field'.$field->id;
            if ($field->ftype==STR || $field->ftype==TEXT ||
                        $field->ftype==FLOATING || $field->ftype==NUMBER ||
                        $field->ftype==DATE || !$field->random) {

                $value = $DB->get_record('poasassignment_task_values',array('fieldid'=>$field->id,'taskid'=>$taskid));
                if ($value)
                    $task->$name=$value->value;
            }
            if ($field->ftype==MULTILIST) {
                $value = $DB->get_record('poasassignment_task_values',array('fieldid'=>$field->id,'taskid'=>$taskid));
                if ($value) {
                    $tok = strtok($value->value,',');
                    $opts=array();
                    while (strlen($tok)>0) {
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
        $criterions = $DB->get_records('poasassignment_criterions',array('poasassignmentid'=>$this->poasassignment->id));
        if ($criterions) {
            $i = 0;
            foreach ($criterions as $criterion) {
                $data->name[$i] = $criterion->name;
                $data->description[$i] = $criterion->description;
                $data->weight[$i] = $criterion->weight;
                $data->source[$i] = $criterion->graderid;
                $data->criterionid[$i] = $criterion->id;
                $i++;
            }
            return $data;
        }
    }

    /**
     * Saves criterions into DB
     *
     * @param $data data from criterions moodleform
     * @return int POASASSIGNMENT_CRITERION_OK if there was no problem
     * while saving criterions, else
     * POASASSIGNMENT_CRITERION_CANT_BE_DELETED or
     * POASASSIGNMENT_CRITERION_CANT_BE_CHANGED or
     * POASASSIGNMENT_CRITERION_CANT_BE_CREATED
     */
    function save_criterion($data) {
        global $DB;
        for ($i = 0; $i < $data->option_repeats; $i++) {
            $rec->name = $data->name[$i];
            $rec->description = $data->description[$i];
            $rec->weight = $data->weight[$i];
            $rec->poasassignmentid = $this->poasassignment->id;

            // If grader is used, add criterion id to it's record in DB
            if ($data->source[$i] > 0) {
                $name = 'grader'.$data->source[$i];
                // $data->$name contains id of our used grader
                $rec->graderid = $data->$name;
            }
            else
                $rec->graderid = 0;

            $recordisempty = $rec->name == '';
            $recordisempty = $recordisempty && $rec->description == '';
            $recordisempty = $recordisempty && $rec->weight == '';
            if ($recordisempty) {
                if ($data->criterionid[$i] !== -1)
                    $DB->delete_records('poasassignment_criterions',
                                        array('id' => $data->criterionid[$i]));
            }
            else {
                if ($data->criterionid[$i] == -1) {
                    if ($this->instance_has_rated_attempts()) {
                        return POASASSIGNMENT_CRITERION_CANT_BE_CREATED;
                    }
                    else {
                        $DB->insert_record('poasassignment_criterions', $rec);
                    }
                }
                else {
                    $rec->id = $data->criterionid[$i];
                    $DB->update_record('poasassignment_criterions', $rec);
                }
            }

        }
        return POASASSIGNMENT_CRITERION_OK;
    }

    function get_rating_data($assigneeid) {
        global $DB;
        $attemptscount = $DB->count_records('poasassignment_attempts',array('assigneeid'=>$assigneeid));
        $attempt = $DB->get_record('poasassignment_attempts',array('assigneeid'=>$assigneeid,'attemptnumber'=>$attemptscount));
        $assignee = $DB->get_record('poasassignment_assignee',array('id'=>$attempt->assigneeid));
        $data->final = $assignee->finalized;
        if ($ratingvalues = $DB->get_records('poasassignment_rating_values',array('attemptid'=>$attempt->id))) {
            foreach ($ratingvalues as $ratingvalue) {
                $field = 'criterion' . $ratingvalue->criterionid;
                $data->$field = $ratingvalue->value;
            }
            return $data;
        }
    }

    /**
     * Saves student's grade in DB
     *
     * @param int $assigneeid
     * @param object $data
     */
    function save_grade($assigneeid, $data) {
        global $DB;
        $dfs = get_object_vars($data);
        foreach ($dfs as $dfk => $dfv) {
            //echo "$dfk=>$dfv<br>";
            //echo $data->criterion1.'<br>';
        }
        $criterions = $DB->get_records('poasassignment_criterions',
                                       array('poasassignmentid' => $this->poasassignment->id));
        $rating = 0;
        $cm = get_coursemodule_from_instance('poasassignment', $this->poasassignment->id);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        $options->area    = 'poasassignment_comment';
        $options->pluginname = 'poasassignment';
        $options->context = $context;
        $options->cm = $cm;
        $options->showcount = true;
        $options->component = 'mod_poasassignment';

        $attemptscount = $DB->count_records('poasassignment_attempts', array('assigneeid' => $assigneeid));
        $attempt = $DB->get_record('poasassignment_attempts',
                                   array('assigneeid' => $assigneeid, 'attemptnumber' => $attemptscount));
        foreach ($criterions as $criterion) {
            $elementname = 'criterion'.$criterion->id;
            $elementcommentname = 'criterion'.$criterion->id.'comment';
            if (!$DB->record_exists('poasassignment_rating_values', array('attemptid' => $attempt->id, 'criterionid' => $criterion->id))) {
                $rec = new stdClass();
                $rec->attemptid = $attempt->id;
                $rec->criterionid = $criterion->id;
                $rec->assigneeid = $assigneeid;
                if ($attempt->draft == 0)
                    $rec->value = $data->$elementname;
                $ratingvalueid = $DB->insert_record('poasassignment_rating_values', $rec);

                $options->itemid  = $ratingvalueid;
                $comment = new comment($options);
                $comment->add($data->$elementcommentname);
            }
            else {
                $ratingvalue = $DB->get_record('poasassignment_rating_values', array('attemptid' => $attempt->id, 'criterionid' => $criterion->id));
                if ($attempt->draft == 0)
                    $ratingvalue->value = $data->$elementname;
                $DB->update_record('poasassignment_rating_values', $ratingvalue);

                //$options->itemid  = $ratingvalue->id;
                //$comment = new comment($options);
                //$comment->add($data->$elementcommentname);
            }
            if ($attempt->draft == 0) {
                $rating += $data->$elementname * round($criterion->weight / $data->weightsum, 2);
			}
        }
        if ($attempt->draft == 0) {
            $attempt->rating = $rating;
		}
        $attempt->ratingdate = time();
        $DB->update_record('poasassignment_attempts', $attempt);
        $assignee = $DB->get_record('poasassignment_assignee', array('id'=>$assigneeid));
//        $assignee->rating=$rating;
        $assignee->finalized=isset($data->final);
        $DB->update_record('poasassignment_assignee', $assignee);
        if ($this->poasassignment->flags & ALL_ATTEMPTS_AS_ONE) {
            $this->disable_previous_attempts($assignee->id);
        }
        $this->save_files($data->commentfiles_filemanager, 'commentfiles', $attempt->id);

        // Update grade in gradebook
        $this->update_assignee_gradebook_grade($assignee);

    }

    function disable_previous_attempts($attemptid) {
        global $DB;
        $attempts=$DB->get_records('poasassignment_attempts',array('id'=>$attemptid),'attemptnumber');
        $attempts=array_reverse($attempts);
        $i=0;
        foreach ($attempts as $attempt) {
            if ($i==0)
                continue;
            if ($DB->record_exists('poasassignment_task_values',array('attemptid'=>$attempt->id)))
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
        $default_values['valuemin']=$field->valuemin;
        $default_values['valuemax']=$field->valuemax;
        $default_values['showintable']=$field->showintable;
        return $default_values;
    }

    function get_variant($index,$variants) {
        $tok = strtok($variants,"\n");
        while (strlen($tok)>0) {
            $opt[]=$tok;
            $tok=strtok("\n");
        }
        if ($index>=0 && $index <=count($opt) &&isset($index))
            return $opt[$index];
        else
            return get_string('erroroutofrange','poasassignment');
    }

    /**
     * Returns variants of the field by field id
     * @param int $fieldid field id
     * @param int $asarray
     * @param string $separator symbols to separate variants
     * @return mixed array with variants, if $asarray==1 or string
     * separated by $separator if $asarray != 1
     */
    function get_field_variants($fieldid, $asarray = 1, $separator = "\n") {
        global $DB;
        $variants = $DB->get_records('poasassignment_variants',
                                     array('fieldid' => $fieldid),
                                     'sortorder');
        if ($variants) {
            $variantvalues=array();
            foreach ($variants as $variant) {
                $variantvalues[] = $variant->value;
            }
            if ($asarray)
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
        //$data->searchparameter=isset($data->searchparameter);
        $data->secretfield=isset($data->secretfield);
        $data->random=isset($data->random);
        $data->assigneeid = 0;

        $fieldid= $DB->insert_record('poasassignment_fields',$data);
        if ($data->ftype==LISTOFELEMENTS || $data->ftype==MULTILIST) {
            $variants=explode("\n",$data->variants);
            $i=0;
            foreach ($variants as $variant) {
                $rec->fieldid=$fieldid;
                $rec->sortorder=$i;
                $rec->value=$variant;
                $DB->insert_record('poasassignment_variants',$rec);
                $i++;
            }
        }
        if ($data->ftype==FLOATING || $data->ftype==NUMBER) {
            if ($data->valuemax==$data->valuemin)
                $data->random=0;
        }
        $tasks=$DB->get_records('poasassignment_tasks',array('poasassignmentid'=>$this->poasassignment->id));
        foreach ($tasks as $task) {
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
        //$field->searchparameter=isset($field->searchparameter);
        $field->secretfield=isset($field->secretfield);
        $field->random=isset($field->random);
        if ($field->ftype==LISTOFELEMENTS || $field->ftype==MULTILIST) {
            $DB->delete_records('poasassignment_variants',array('fieldid'=>$field->id));

            $variants=explode("\n",$field->variants);
            $i=0;
            foreach ($variants as $variant) {
                $rec->fieldid=$field->id;
                $rec->sortorder=$i;
                $rec->value=$variant;
                $DB->insert_record('poasassignment_variants',$rec);
                $i++;
            }
        }
        if ($field->ftype==FLOATING || $field->ftype==NUMBER) {
            if ($field->valuemax==$field->valuemin)
                $field->random=0;
        }
        return $DB->update_record('poasassignment_fields',$field);
    }

    function delete_field($id) {
        global $DB;
        $cm = get_coursemodule_from_instance('poasassignment',$this->poasassignment->id);
        $taskvalues=$DB->get_records('poasassignment_task_values',array('fieldid'=>$id));
        $field=$DB->get_record('poasassignment_fields',array('id'=>$id));
        if ($field->ftype==LISTOFELEMENTS || $field->ftype==MULTILIST) {
            $DB->delete_records('poasassignment_variants',array('fieldid'=>$id));
        }
        foreach ($taskvalues as $taskvalue) {
            //echo $field->ftype;
            if ($field->ftype==FILE)
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
    public function get_files($filearea, $itemid) {
        $contextid = $this->get_context()->id;
        $fs = get_file_storage();
        $dir =$fs->get_area_tree($contextid, 'mod_poasassignment', $filearea, $itemid);
        $arr = $this->get_files_content($dir, $filearea, $itemid);
        return $arr;
        //print_r($arr);
    }

    public function get_files_content($dir, $filearea, $itemid) {
        global $CFG;
        $contextid = $this->get_context()->id;
        $arr = array();
        foreach ($dir['subdirs'] as $subdir) {
            $arr[$subdir['dirname']] = $this->get_files_content($subdir, $filearea, $itemid);
        }
        foreach ($dir['files'] as $file) {
            $filename = $file->get_filename();
            $contents = $file->get_content();
            $arr[$filename] = mb_convert_encoding($contents, 'utf8', 'windows-1251');;
        }
        return $arr;
    }
    public function save_attempt($data) {
        global $DB;
        $attempt = new stdClass();
        $attempt->draft = isset($data->draft);
        $attempt->final = isset($data->final);
        $attempt->assigneeid = $this->assignee->id;
        $attempt->attemptdate = time();
        $attempt->disablepenalty = 0;

        $attemptscount = $DB->count_records('poasassignment_attempts',array('assigneeid'=>$this->assignee->id));
        $attempt->attemptnumber = $attemptscount + 1;
        return $DB->insert_record('poasassignment_attempts', $attempt);
    }
    public function get_assignee($userid) {
        global $DB;
        if(!$DB->record_exists('poasassignment_assignee',
                array('userid' => $userid, 'poasassignmentid' => $this->poasassignment->id))) {
			$rec = $this->create_assignee($userid);
        }
        else {
            $rec = $DB->get_record('poasassignment_assignee',
                    array('userid' => $userid, 'poasassignmentid' => $this->poasassignment->id));
        }
        $this->assignee->id = $rec->id;

        return $rec;
    }
    
    /**
     * Create assignee record for user
     * 
     * @access private
     * @param int $userid user id
     * @return object record
     */
    private function create_assignee($userid) {
    	$rec = new stdClass();
    	$rec->userid = $userid;
    	$rec->poasassignmentid = $this->poasassignment->id;
    	$rec->taskid = 0;
    	$rec->taskindex = 0;
    	$rec->timetaken = 0;
    	$rec->id = $DB->insert_record('poasassignment_assignee', $rec);
    	return $rec; 
    }
    // Runs after adding submission. Calls all graders, used in module.
    public function test_attempt($attemptid) {
        //echo 'testing';
        global $DB;
        $usedgraders = $DB->get_records('poasassignment_used_graders',
                                        array('poasassignmentid' => $this->poasassignment->id));
        if(count($usedgraders) == 0) {
            return;
        }
        $attempt = $DB->get_record('poasassignment_attempts', array('id' => $attemptid));
        foreach ($usedgraders as $usedgrader) {
            //echo $usedgrader->id;
            $graderrecord = $DB->get_record('poasassignment_graders', array('id' => $usedgrader->graderid));

            require_once($graderrecord->path);
            $gradername = $graderrecord->name;
            $grader = new $gradername;
            $rating = $grader->test_attempt($attemptid);
            //echo $rating ;

            $criterions = $DB->get_records('poasassignment_criterions',
                                           array('poasassignmentid' => $this->poasassignment->id,
                                                 'graderid' => $usedgrader->graderid));
            foreach ($criterions as $criterion) {
                $ratingvalue = new stdClass();
                $ratingvalue->attemptid = $attemptid;
                $ratingvalue->criterionid = $criterion->id;

                $ratingvalue->assigneeid = $attempt->assigneeid;

                $ratingvalue->value = $rating;
                //if ($attempt->draft == 0)
                //    $ratingvalue->value = $data->$elementname;
                //echo 'adding grade';
                $ratingvalueid = $DB->insert_record('poasassignment_rating_values', $ratingvalue);
            }

        }
        // if attempt has grades for all criterions, caluclulate total grade
        $criterions = $DB->get_records('poasassignment_criterions', array('poasassignmentid' => $this->poasassignment->id));
        $allcriterions = true;
        $totalweight = 0;
        $criteriongrades = array();
        foreach($criterions as $criterion) {
            if(!$DB->record_exists('poasassignment_rating_values', array('criterionid' => $criterion->id, 'attemptid' => $attemptid))) {
                $allcriterions = false;
                break;
            }
            else {
                $rating = $DB->get_record('poasassignment_rating_values', array('criterionid' => $criterion->id, 'attemptid' => $attemptid));
                $criteriongrades[$criterion->id] = $rating->value;
                $totalweight += $criterion->weight;
            }
        }
        if ($allcriterions) {
            $grade = 0;
            foreach($criterions as $criterion) {
                $grade += $criteriongrades[$criterion->id] * round($criterion->weight / $totalweight, 2);
            }
            $attempt->rating = $grade;
            $attempt->ratingdate = time();
            $DB->update_record('poasassignment_attempts', $attempt);
            $this->update_assignee_gradebook_grade($DB->get_record('poasassignment_assignee', array('id' => $attempt->assigneeid)));
            // TODO Просто вызвать функцию, которая выставляет оценку
        }
    }
    function bind_task_to_assignee($userid,$taskid) {
        global $DB;
        $rec = $this->get_assignee($userid);
        //$rec->userid=$userid;
        //$rec->poasassignmentid=$this->poasassignment->id;
        $rec->taskid = $taskid;
        $rec->taskindex++;        
        $rec->timetaken = time();
        $DB->update_record('poasassignment_assignee', $rec);
        $this->assignee->id = $rec->id;

        $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$this->poasassignment->id));
        foreach ($fields as $field) {
            if ($field->random) {
                if (!($field->valuemin==0 && $field->valuemax==0)) {
                    if ($field->ftype==NUMBER)
                        $randvalue=rand($field->valuemin,$field->valuemax);
                    if ($field->ftype==FLOATING)
                        $randvalue=(float)rand($field->valuemin*100,$field->valuemax*100)/100;
                }
                else {
                    if ($field->ftype==NUMBER)
                        $randvalue=rand();
                    if ($field->ftype==FLOATING)
                        $randvalue=(float)rand()/100;
                }
                if ($field->ftype==LISTOFELEMENTS) {
                    $tok = strtok($field->variants,"\n");
                    $count=0;
                    while ($tok) {
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

    function cancel_task($assigneeid) {
        global $DB;
        $rec = $DB->get_record('poasassignment_assignee', array('id' => $assigneeid));
        $rec->taskid = 0;
        $rec->lastattemptid = 0;
        $DB->update_record('poasassignment_assignee', $rec);
    }

    function can_cancel_task($assigneeid, $context) {
        global $DB;
        $assignee = $DB->get_record('poasassignment_assignee', array('id' => $assigneeid));

        $has_cap = has_capability('mod/poasassignment:managetasks', $context);
        $has_ability = ($this->poasassignment->flags & SECOND_CHOICE) && ($assignee->taskindex < 2);
        return ($has_cap || $has_ability);
    }

    function help_icon($text) {
        global $CFG,$OUTPUT,$PAGE;
        if (empty($text)) {
            return;
        }
        $src = $OUTPUT->pix_url('help');
        $alt = $text;
        $attributes = array('src'=>$src, 'alt'=>$alt, 'class'=>'iconhelp');
        $output = html_writer::empty_tag('img', $attributes);
        $url = new moodle_url('/mod/poasassignment/showtext.php', array('text' => $text));
        $title = get_string('about','poasassignment');
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
        groups_print_activity_menu($cm, $CFG->wwwroot . '/mod/poasassignment/view.php?id=' . $cm->id.'&page=view');
        $context=get_context_instance(CONTEXT_MODULE,$cm->id);
        $notchecked=0;
        $count=0;

        if ($usersid = get_enrolled_users($context, 'mod/poasassignment:havetask', $currentgroup, 'u.id')) {
            $usersid = array_keys($usersid);
            $count=count($usersid);
            foreach ($usersid as $userid) {
                if ($assignee=$DB->get_record('poasassignment_assignee',array('userid'=>$userid,'poasassignmentid'=>$this->poasassignment->id))) {
                    $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assignee->id));
                    if ($attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assignee->id,'attemptnumber'=>$attemptscount))) {
                        if ($attempt->attemptdate>$attempt->ratingdate || !isset($attempt->rating))
                        $notchecked++;
                    }
                }
            }
        }

        /// If we know how much students are enrolled on this task show "$notchecked of $count need grade" message
        if ($count!=0) {
            $html = $notchecked.' '.get_string('of','poasassignment').' '.$count.' '.get_string('needgrade','poasassignment');
            $submissionsurl = new moodle_url('view.php',array('id'=>$cm->id,'page'=>'submissions'));
            return "<align='right'>".html_writer::link($submissionsurl,$html);
        }
        else {
            $notchecked=0;
            $assignees = $DB->get_records('poasassignment_assignee',array('poasassignmentid'=>$this->poasassignment->id));
            foreach ($assignees as $assignee) {
                $attemptscount=$DB->count_records('poasassignment_attempts',array('assigneeid'=>$assignee->id));
                if ($attempt=$DB->get_record('poasassignment_attempts',array('assigneeid'=>$assignee->id,'attemptnumber'=>$attemptscount))) {
                    if ($attempt->attemptdate>$attempt->ratingdate || !isset($attempt->rating))
                    $notchecked++;
                }
            }
            /// If there is no enrollment on this task but someone loaded anser show "$notchecked need grade" message
            if ($notchecked!=0) {
                $html = $notchecked.' '.get_string('needgrade','poasassignment');
                $submissionsurl = new moodle_url('view.php',array('id'=>$cm->id,'page'=>'submissions'));
                return "<align='right'>".html_writer::link($submissionsurl,$html);
            }
        }
        $html = get_string('noattempts','poasassignment');
        $submissionsurl = new moodle_url('view.php',array('id'=>$cm->id,'page'=>'submissions'));
        return "<align='right'>".html_writer::link($submissionsurl,$html);
    }

    function get_penalty($attemptid) {
        global $DB;
        $currentattempt = $DB->get_record('poasassignment_attempts',array('id'=>$attemptid));
        $attempts = $DB->get_records('poasassignment_attempts',array('assigneeid'=>$currentattempt->assigneeid), 'attemptnumber');
        $realnumber = $currentattempt->attemptnumber;
        foreach ($attempts as $attempt) {
            if ($attempt->disablepenalty == 1) {
                $realnumber--;
            }
        }
        if ($this->poasassignment->penalty * ($realnumber - 1) >= 0)
            return $this->poasassignment->penalty * ($realnumber - 1);
        else
			return 0;
        return ;
    }

    function grade_item_update($grades=NULL) {
        global $CFG;
        require_once($CFG->libdir.'/gradelib.php');

        if (!isset($this->poasassignment->courseid)) {
            $this->poasassignment->courseid = $this->poasassignment->course;
        }

        $params = array('itemname'=>$this->poasassignment->name, 'idnumber'=>$this->poasassignment->cmidnumber);

        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = 100;
        $params['grademin']  = 0;

        if ($grades  === 'reset') {
            $params['reset'] = true;
            $grades = NULL;
        }
        return grade_update('mod/poasassignment', $this->poasassignment->courseid, 'mod', 'poasassignment', $this->poasassignment->id, 0, $grades, $params);
    }
    function grade_item_delete() {
        global $CFG;
        require_once($CFG->libdir.'/gradelib.php');
        if (!isset($this->poasassignment->courseid)) {
            $this->poasassignment->courseid = $this->poasassignment->course;
        }

        return grade_update('mod/poasassignment', $this->poasassignment->courseid, 'mod', 'poasassignment', $this->poasassignment->id, 0, NULL, array('deleted'=>1));
    }

    function have_test_results($attempt) {
        global $DB;
        $usedgraders = $DB->get_records('poasassignment_used_graders', array('poasassignmentid' => $this->poasassignment->id));
        foreach($usedgraders as $usedgrader) {
            $graderrec = $this->graders[$usedgrader->graderid];
            require_once($graderrec->path);
            $gradername = $graderrec->name;
            //$grader = new $gradername;
            if($gradername::attempt_was_tested($attempt->id))
                return true;
        }
    }
    function show_test_results($attempt) {
        global $DB;
        $usedgraders = $DB->get_records('poasassignment_used_graders', array('poasassignmentid' => $this->poasassignment->id));
        $html = '';
        foreach($usedgraders as $usedgrader) {
            $graderrec = $this->graders[$usedgrader->graderid];
            require_once($graderrec->path);
            $gradername = $graderrec->name;
            $grader = new $gradername;
            if($gradername::attempt_was_tested($attempt->id))
                $html .= $grader->show_test_results($attempt->id, $this->context);
        }
        return $html;
    }
    function trigger_poasassignment_event($mode,$assigneeid) {
        $eventdata = new stdClass();
        $eventdata->student=$assigneeid;
        $eventdate->poasassignmentid=$this->poasassignment->id;
        if ($mode==TASK_RECIEVED) {
            events_trigger('poasassignment_task_recieved', $eventdata);
        }
        if ($mode==ATTEMPT_DONE) {
            events_trigger('poasassignment_attempt_done', $eventdata);
        }
        if ($mode==GRADE_DONE) {
            events_trigger('poasassignment_grade_done', $eventdata);
        }
    }
    function email_teachers($assignee) {
        global $DB;

        if (!($this->poasassignment->flags & NOTIFY_TEACHERS))
            return;

        $user = $DB->get_record('user', array('id'=>$assignee->userid));
        $eventdata= new stdClass();

        $teachers = $this->get_teachers($user);


        $eventdata->name = 'poasassignment_updates';
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessage= 'Student '.fullname($user,true).' uploaded his answer' ;
        $eventdata->fullmessagehtml   = '<b>'.$eventdata->fullmessage.'</b>';
        $eventdata->smallmessage = '';
        $eventdata->subject = 'Attempt done';
        $eventdata->component = 'mod_poasassignment';
        $eventdata->userfrom = $user;

        foreach ($teachers as $teacher) {
            $eventdata->userto = $teacher;
            message_send($eventdata);
        }

    }
    function get_teachers() {
        $cm = get_coursemodule_from_instance('poasassignment',$this->poasassignment->id);
        $context=get_context_instance(CONTEXT_MODULE,$cm->id);
        $potgraders = get_users_by_capability($context, 'mod/poasassignment:grade', '', '', '', '', '', '', false, false);
        return $potgraders;
    }

    /**
     * Saves assignee grade in gradebook
     *
     * @param object $assignee
     */
    function update_assignee_gradebook_grade($assignee) {
        global $CFG, $DB;
        require_once($CFG->libdir.'/gradelib.php');

        $grade = new stdClass();
        $grade->userid = $assignee->userid;
        $attempt = $DB->get_record('poasassignment_attempts',array('id'=>$assignee->lastattemptid));
        if ($attempt) {
            $grade->rawgrade = $attempt->rating - $this->get_penalty($attempt->id);
            $grade->dategraded = $attempt->ratingdate;
            $grade->datesubmitted = $attempt->attemptdate;
        }
        grade_update('mod/poasassignment', $this->poasassignment->course, 'mod', 'poasassignment', $this->poasassignment->id, 0, $grade, null);
    }
    static function user_have_active_task($userid, $poasassignmentid) {
		global $DB;
        if ($DB->record_exists('poasassignment_assignee',
                    array('userid'=>$userid,'poasassignmentid'=>$poasassignmentid))) {
            $assignee=$DB->get_record('poasassignment_assignee', array('userid'=>$userid,
                                                                            'poasassignmentid'=>$poasassignmentid));
            return ($assignee && $assignee->taskid>0);
        }
        return false;
    }
    public function delete_taskgiver_settings($poasassignmentid, $taskgiverid) {
        global $DB;
        if(!($this->poasassignment->flags & ACTIVATE_INDIVIDUAL_TASKS))
            return;
        if($taskgiverrec = $DB->get_record('poasassignment_taskgivers', array('id' => $taskgiverid))) {
            require_once($taskgiverrec->path);
            $taskgivername = $taskgiverrec->name;
            $tg = new $taskgivername();
            if($taskgivername::has_settings()) {
                $tg->delete_settings($poasassignmentid);
            }
        }
    }
    
    /**
     * Get user's groups
     * @param int $userid user id
     * @param int $courseid course id
     * @return array groups identoficators
     */
    public function get_user_groups($userid, $courseid) {
        global $DB;
        $groupmembers = $DB->get_records('groups_members', array('userid' => $userid));
        $ret = array();
        foreach($groupmembers as $groupmember) {
            // Get first user's groups within $courseid
            $groups = $DB->get_records('groups', array('id' => $groupmember->groupid,
                                                       'courseid' => $courseid));
            foreach($groups as $group) {
                $ret[] = $group->id;
            }
        }
        return $ret;
    }
    public function get_user_groupings($userid, $courseid) {
        global $DB;
        $groups = $this->get_user_groups($userid, $courseid);
        $ret = array();
        foreach($groups as $group) {
            $groupinggroups = $DB->get_records('groupings_groups', array('groupid' => $group));
            foreach($groupinggroups as $groupinggroup) {
                $groupings = $DB->get_records('groupings', array('id' => $groupinggroup->groupingid,'courseid' => $courseid));
                foreach($groupings as $grouping) {
                    $ret[] = $grouping->id;
                }
            }
        }
        return $ret;
    }
    /* Get all tasks that are available for current user
     * Method checks instance's uniqueness, visibility of all tasks
     * @param int $poasassignmentid
     * @param int $userid
     * @param int $givehidden
     * @return array array of available tasks
     */
    public function get_available_tasks($userid, $givehidden = 0) {
        // Get all tasks in instance at first
        global $DB;
        $values = array();
        $values['poasassignmentid'] = $this->poasassignment->id;
        if(!$givehidden) {
            $values['hidden'] = 0;
        }
        $tasks = $DB->get_records('poasassignment_tasks', $values);

        // If there is no tasks at this stage - return empty array
        if(count($tasks) == 0) {
            return $tasks;
        }

        // Filter tasks using 'uniqueness' field in poasassignment instance
        if($instance = $DB->get_record('poasassignment', array('id' => $this->poasassignment->id))) {
            // If no uniqueness required, return $tasks without changes
            if($instance->uniqueness == POASASSIGNMENT_NO_UNIQUENESS) {
                return $tasks;
            }
            // If uniqueness within groups or groupings required, filter tasks
            if($instance->uniqueness == POASASSIGNMENT_UNIQUENESS_GROUPS ||
               $instance->uniqueness == POASASSIGNMENT_UNIQUENESS_GROUPINGS) {
                foreach($tasks as $key => $task) {
                    // Get all assignees that have this task
                    $assignees = $DB->get_records('poasassignment_assignee', array('taskid' => $task->id));
                    // If nobody have this task continue
                    if(count($assignees) == 0) {
                        continue;
                    }
                    else {
                        foreach($assignees as $assignee) {
                            if($instance->uniqueness == POASASSIGNMENT_UNIQUENESS_GROUPS) {
                                // If current user and any owner of the task have common group within
                                // course remove this task from array

                                $commongroups = array_intersect($this->get_user_groups($userid, $instance->course),
                                                                $this->get_user_groups($assignee->userid, $instance->course));
                                if (count($commongroups) > 0) {
                                    unset($tasks[$key]);
                                }
                            }
                            if ($instance->uniqueness == POASASSIGNMENT_UNIQUENESS_GROUPINGS) {
                                // If current user and any owner of the task have common grouping within
                                // course remove this task from array

                                $commongroupings = array_intersect($this->get_user_groupings($userid, $instance->course),
                                                                  $this->get_user_groupings($assignee->userid, $instance->course));
                                if (count($commongroupings) > 0) {
                                    unset($tasks[$key]);
                                }
                            }
                        }
                    }
                }
                return $tasks;
            }
            if ($instance->uniqueness == POASASSIGNMENT_UNIQUENESS_COURSE) {
                foreach ($tasks as $key => $task) {
                    if ($DB->record_exists('poasassignment_assignee', array('taskid' => $task->id))) {
                        unset($tasks[$key]);
                    }
                }
                return $tasks;
            }

        }

    }
    
    /**
     * Проверить право пользователя на просмотр задания (проверка даты открытия)
     */
    public function is_opened() {
    	if ($this->get_poasassignment()->availabledate != 0) {
    		if (time() < $this->get_poasassignment()->availabledate) {
    			if (!has_capability('mod/poasassignment:managetasks', $this->get_context())) {
    				return false;
    			}
    		}
    	}
    	return true;
    }
	public function check_dates() {
		// Проверка параметров, связанных с датой выбора задания
		if (has_capability('mod/poasassignment:havetask', $this->get_context())
			&& $this->get_poasassignment()->choicedate != 0) {
			if (time() > $this->get_poasassignment()->choicedate) {
				global $USER;
				$assignee = $this->get_assignee($USER->id);
				// Если у студента нет задания
				if ($assignee->taskid == 0) {
					// Если требуется выдать случайное
					if ($this->has_flag(RANDOM_TASKS_AFTER_CHOICEDATE)) {
						// Попробовать выдать задание
						$taskid = poasassignment_model::get_random_task_id($this->get_available_tasks($USER->id));
						if ($taskid == -1 ) {
							return 'errormodulehavenotasktogiveyou';
						}
						$this->bind_task_to_assignee($USER->id, $taskid);
					}
					else {
						// Вернуть ошибку
						return 'erroryouhadtochoosetask';
					}
				}
			}
		}
	}
	static function get_random_task_id($tasks) {
		$tasksarray = array();
		foreach($tasks as $task)
			$tasksarray[] = $task->id;
		if(count($tasksarray) > 0) {
			return $tasksarray[rand(0, count($tasksarray) - 1)];
		}
		else {
			return -1;
		}
	}

	public static function time_difference($time) {
		$result = format_time(time() - $time);
		if (time() > $time) {
			$result .= ' ' . get_string('ago','poasassignment');
		}
		return $result;
	}
	
	 /**
     * Get last attempt record of the assignee
     * 
     * @access public
     * @param int $assigneeid assignee's id
     * @return object attempt record or null
     */
	public function get_last_attempt($assigneeid) {
		global $DB;
		$rec = $DB->get_record_sql("SELECT id, attemptdate, rating FROM {poasassignment_attempts} WHERE assigneeid = ? ORDER BY id DESC LIMIT 1;", array($assigneeid));
		return $rec;
	}
	/**
	 * Get last assignee's attempt with grade
	 * 
	 * @access public
	 * @param int $assigneeid assignee's id
	 * @return object attempt record or null
	 */
	public function get_last_graded_attempt($assigneeid) {
		global $DB;
		$rec = $DB->get_record_sql("SELECT * FROM {poasassignment_attempts} WHERE assigneeid = ? AND rating >= 0 AND ratingdate > 0 ORDER BY attemptnumber DESC LIMIT 1;", array($assigneeid));
		return $rec;
	}

	public function get_last_commented_attempt($assigneeid) {
		global $DB;
		$rec = $DB->get_record_sql("SELECT * FROM {poasassignment_attempts} WHERE assigneeid = ? AND ratingdate > 0 ORDER BY attemptnumber DESC LIMIT 1;", array($assigneeid));
		return $rec;
	}

    /**
     * Looks for any rated attempt
     *
     * @return boolean true, if instance has no rated attempts, else false
     */
    public function instance_has_rated_attempts() {
        global $DB;
        $assignees = $DB->get_records('poasassignment_assignee',array('poasassignmentid'=>$this->poasassignment->id));
        if ($assignees) {
            foreach ($assignees as $assignee) {
                $attempts = $DB->get_records('poasassignment_attempts',array('assigneeid' => $assignee->id));
                if ($attempts)
                {
                    foreach ($attempts as $attempt) {
                        if ($DB->get_records('poasassignment_rating_values',array('attemptid' => $attempt->id))) {
                            return true;
                        }
                    }
                    return false;
                }
                else {
                    return false;
                }
            }
        }
        else {
            return false;
        }
    }

    /**
     * Get students with graded attempts in the instance
     *
     * @return array of students or null
     */
    public function get_criterion_problem_students() {
        global $DB;
        $id = $this->poasassignment->id;
        $sql = "SELECT st.id, st.userid, att.id, att.rating
                FROM {poasassignment_assignee} st
                JOIN {poasassignment_attempts} att
                    ON  att.id = st.lastattemptid
                WHERE   st.poasassignmentid = $id";
        $students = $DB->get_records_sql($sql);
        if ($students)
            return $students;
        else
            return null;
    }
    
    /**
     *  Get owners of task
     * @param int $taskid task id
     * @return mixed array of students
     */
    public function get_task_owners($taskid) {
    	global $DB;
    	$assignees = $DB->get_records('poasassignment_assignee', array('taskid' => $taskid), 'userid', 'userid, id');
    	return $assignees;
    }
    
    public function get_users_info(array $assignees) {
    	global $DB;
    	foreach ($assignees as $assignee) {
    		$assignee->userinfo = $DB->get_record('user', array('id' => $assignee->userid), 'firstname, lastname');
    		$assignee->usergroups = $this->get_user_groups_extended($assignee->userid);
    	}
    	return $assignees;
    }
    
    /**
     *  Get id, name and description of all user's groups
     * @param int $userid user id
     * @return array groups
     */
    public function get_user_groups_extended($userid) {
    	global $DB;
    	$sql = "SELECT gr.name, gr.description, gr.id
		    	FROM {groups} gr
		    	JOIN {groups_members} grmem
		    	ON  grmem.groupid = gr.id
		    	WHERE   grmem.userid = $userid";
    	$groups = $DB->get_records_sql($sql);
    	return $groups;
    }

}