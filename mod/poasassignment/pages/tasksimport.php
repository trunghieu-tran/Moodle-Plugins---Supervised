<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '/model.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot.'/lib/tablelib.php');

require_once(dirname(dirname(__FILE__)).'/additional/auditor_sync/auditor_sync.php');
class tasksimport_page extends abstract_page {
    var $poasassignment;
    var $mform;
    private $taskrecords = array();

    function __construct($cm, $poasassignment) {
        $this->poasassignment = $poasassignment;
        $this->cm=$cm;
    }

    function get_cap() {
        return 'mod/poasassignment:managetasks';
    }

    function has_satisfying_parameters() {
        global $DB,$USER;
        $flag = $this->poasassignment->flags & ACTIVATE_INDIVIDUAL_TASKS;
        if (!$flag) {
            $this->lasterror='errorindtaskmodeisdisabled';
            return false;
        }
        return true;
    }

    function pre_view() {
        $confirmed = optional_param('confirmed', false, PARAM_ALPHA);
        if ($confirmed && $confirmed == 'confirmed') {
            $this->add_tasks_confirmed();
        }
        $model = poasassignment_model::get_instance();
        $poasassignmentid = $model->get_poasassignment()->id;
        $this->mform = new tasksimport_form(
            null,
            array(
                'id' => $model->get_cm()->id,
                'poasassignmentid' => $poasassignmentid,
                'options' => auditor_sync::get_instance()->get_possible_kc_fields($poasassignmentid))
        );
    }

    /**
     * Добавить задания в базу данных
     *
     * @access private
     */
    private function add_tasks_confirmed() {
        $count = optional_param('count', 0, PARAM_INTEGER);
        $this->taskrecords = array();
        for ($i = 0; $i < $count; $i++) {
            $taskrecord = new stdClass();
            $taskrecord->name = $_POST['name_'.$i];
            $taskrecord->description = $_POST['description_'.$i];
            $taskrecord->comments = $_POST['comments_'.$i];
            $taskrecord->id = required_param('id_'.$i, PARAM_INT);
            $kcfieldname = required_param('kcfieldname', PARAM_ALPHANUMEXT);
            $taskrecord->$kcfieldname = optional_param('levels_'.$i, array(), PARAM_INT);
            $this->taskrecords[] = $taskrecord;
        }
    }
    /**
     * Показать форму для настройки соединения с сервером
     */
    function view() {
        global $DB;
        $model = poasassignment_model::get_instance();
        if (count($this->taskrecords) > 0) {
            foreach ($this->taskrecords as $taskrecord) {
                $taskrecord->id = auditor_sync::get_instance()->import_task($taskrecord);
                echo get_string('taskimported', 'poasassignment');
                echo " (id=$taskrecord->id, $taskrecord->name, ".shorten_text($taskrecord->description).")";
                echo '<br/>';
            }
            $this->taskrecords = array();
            $url = new moodle_url('view.php',
                        array(  'page' => 'tasks',
                                'id' => required_param('id', PARAM_INT)
                        )
                    );
            echo html_writer::link($url, 'К списку заданий');
            return;
        }
        if ($this->mform->get_data()) {
            $data = $this->mform->get_data();
            if ($data->submitbutton == get_string('sync', 'poasassignment')) {
                $this->synchronize($data);
                return;
            }
            $error = auditor_sync::get_instance()->connect_auditor($data->server, $data->dbuser, $data->dbpass, $data->database);
            if (($error == false)) {
                $sql = "SELECT * FROM variants WHERE lessontypeid=".mysql_real_escape_string($data->lessontypeid);
                if (!($result = mysql_query($sql))) {
                    echo get_string('errorcantrunquery', 'poasassignment');
                    echo $sql;
                }
                else{
                    // Узнать все варианты для поля уровня сложности
                    $variants = $DB->get_records('poasassignment_variants', array('fieldid' => $data->kcfield), 'sortorder');
                    $levels = array();
                    foreach ($variants as $id => $level) {
                        array_push($levels, $level->value);
                    }

                    // Получить все уровни сложности из внешней базы данных
                    $sql = "SELECT * FROM mod_complexities";
                    if (!($complexities = mysql_query($sql))) {
                        echo get_string('errorcantrunquery', 'poasassignment');
                        echo $sql;
                    }
                    else {
                        $newcomplexities = array();
                        while($complexity = mysql_fetch_assoc($complexities)) {
                            array_push($newcomplexities, $complexity);
                        }
                        $taskrecords = array();
                        while ($variant = mysql_fetch_assoc($result)) {

                            $taskrecord = new stdClass();
                            $taskrecord->name = get_string('defaulttaskname', 'poasassignment') .' №' . $variant['num'];
                            $taskrecord->description = $variant['description'];
                            $taskrecord->comments = $variant['comments'];
                            $taskrecord->id = $variant['id'];

                            $kcfieldname = 'field' . $data->kcfield;
                            $taskrecord->$kcfieldname = array();
                            $taskrecord->modifications = array();
                            // Для каждого задания получить список модификаций
                            $sql = "SELECT id, num, kc FROM modifications WHERE variantid=".mysql_real_escape_string($variant['id']);
                            if (!($modifications = mysql_query($sql))) {
                                echo get_string('errorcantrunquery', 'poasassignment');
                                echo $sql;
                            }
                            else {
                                while ($modification = mysql_fetch_assoc($modifications)) {
                                    array_push(
                                        $taskrecord->modifications,
                                        array('id' => $modification['id'], 'num' => $modification['num'], 'kc' => $modification['kc'])
                                    );
                                    foreach ($newcomplexities as $complexity) {
                                        if ($modification['kc'] >= $complexity['kcmin']
                                            && $modification['kc'] <= $complexity['kcmax']) {

                                            // Узнать id этого уровня сложности в таблице poasassignment_variants
                                            // Иногда последний символ в таблице - №13. Учитывать это
                                            if (($index = array_search($complexity['name'], $levels)) === false) {
                                                $index = array_search($complexity['name'].chr(13), $levels);
                                            }
                                            if ( $index !== false) {
                                                if (array_search($index, $taskrecord->$kcfieldname) === false) {
                                                    array_push($taskrecord->$kcfieldname, $index);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            array_push($taskrecords, $taskrecord);
                        }
                        //echo '<pre>',print_r($levels),'</pre>';
                        //echo '<pre>',print_r($taskrecords),'</pre>';

                        $this->show_preview($taskrecords, $kcfieldname, $levels);
                        /*foreach ($taskrecords as $taskrecord) {*/
                            /*$taskrecord->id = auditor_sync::get_instance()->import_task($taskrecord);
                            echo get_string('taskimported', 'poasassignment');
                            echo " (id=$taskrecord->id, $taskrecord->name, ".shorten_text($taskrecord->description).")";
                            echo '<br/>';*/
                        /*}*/
                    }
                }
            }
            else {
                echo $error;
            }
        }
        else {
            $this->mform->display();
        }
    }

    private function synchronize($data) {
        global $DB;
        $error = auditor_sync::get_instance()->connect_auditor($data->server, $data->dbuser, $data->dbpass, $data->database);
        if (($error == false)) {
            // Получить все уровни сложности из внешней базы данных
            $newcomplexities = array();
            $sql = "SELECT * FROM mod_complexities";
            if (!($complexities = mysql_query($sql))) {
                echo get_string('errorcantrunquery', 'poasassignment');
                echo $sql;
                return;
            }
            else {
                while($complexity = mysql_fetch_assoc($complexities)) {
                    array_push($newcomplexities, $complexity);
                }
            }

            // Узнать все варианты для поля уровня сложности
            $variants = $DB->get_records('poasassignment_variants', array('fieldid' => $data->kcfield), 'sortorder');
            $levels = array();
            foreach ($variants as $id => $level) {
                array_push($levels, $level->value);
            }

            $sql = 'SELECT * FROM variants WHERE lessontypeid='.mysql_real_escape_string($data->lessontypeid);
            if (!($result = mysql_query($sql))) {
                echo get_string('errorcantrunquery', 'poasassignment');
                echo $sql;
                return;
            }
            else{
                $storedtasks = $DB->get_records('auditor_sync_tasks');
                // Для каждого задания из аудитора
                while ($actualtask = mysql_fetch_assoc($result)) {
                    // найти те задания poasassignment, которые были импортироаны из него
                    $taskstosync = auditor_sync::get_instance()->get_stored_tasks_by_auditor_variant_id($actualtask['id'], $storedtasks);

                    // Если задания есть
                    if ($taskstosync) {
                        echo '<br/> Обновление задания '.$actualtask['id'];
                        // и для всех таких заданий обновить комментарии и описание задачи
                        foreach ($taskstosync as $tasktosync) {
                            if ($tasktosync->comments != $actualtask['comments']) {
                                $tasktosync->comments = $actualtask['comments'];
                                //echo '<br/>UPDATE RECORD auditor_sync_tasks ',print_r($tasktosync);
                                $DB->update_record('auditor_sync_tasks', $tasktosync);
                            }
                            $poasassignmenttask = $DB->get_record('poasassignment_tasks', array('id' => $tasktosync->poasassignmenttaskid), 'id, description');
                            if ($poasassignmenttask) {
                                if ($poasassignmenttask->description != $actualtask['description']) {
                                    $poasassignmenttask->description = $actualtask['description'];
                                    //echo '<br/>UPDATE RECORD poasassignment_tasks ',print_r($poasassignmenttask);
                                    $DB->update_record('poasassignment_tasks', $poasassignmenttask);
                                }
                            }
                        }
                    }
                    // Если такого задания в moodle нет, добавить его
                    else {
                        echo '<br/> Импорт задания '.$actualtask['id'];
                        $taskrecord = new stdClass();
                        $taskrecord->name = get_string('defaulttaskname', 'poasassignment') .' №' . $actualtask['num'];
                        $taskrecord->description = $actualtask['description'];
                        $taskrecord->comments = $actualtask['comments'];
                        $taskrecord->id = $actualtask['id'];

                        $kcfieldname = 'field' . $data->kcfield;
                        $taskrecord->$kcfieldname = array();
                        $taskrecord->modifications = array();

                        // Для каждого задания получить список модификаций
                        $sql = "SELECT id, num, kc FROM modifications WHERE variantid=".mysql_real_escape_string($actualtask['id']);
                        if (!($modifications = mysql_query($sql))) {
                            echo get_string('errorcantrunquery', 'poasassignment');
                            echo $sql;
                            return;
                        }
                        else {
                            while ($modification = mysql_fetch_assoc($modifications)) {
                                array_push(
                                    $taskrecord->modifications,
                                    array('id' => $modification['id'], 'num' => $modification['num'], 'kc' => $modification['kc'])
                                );
                                foreach ($newcomplexities as $complexity) {
                                    if ($modification['kc'] >= $complexity['kcmin']
                                        && $modification['kc'] <= $complexity['kcmax']) {

                                        // Узнать id этого уровня сложности в таблице poasassignment_variants
                                        // Иногда последний символ в таблице - №13. Учитывать это
                                        if (($index = array_search($complexity['name'], $levels)) === false) {
                                            $index = array_search($complexity['name'].chr(13), $levels);
                                        }
                                        if ( $index !== false) {
                                            if (array_search($index, $taskrecord->$kcfieldname) === false) {
                                                array_push($taskrecord->$kcfieldname, $index);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        auditor_sync::get_instance()->import_task($taskrecord);
                    }
                }
                $url = new moodle_url('view.php',
                    array(  'page' => 'tasks',
                            'id' => required_param('id', PARAM_INT)
                    )
                );
                echo '<br/>'.html_writer::link($url, 'К списку заданий');
            }
        }
        else {
            echo $error;
        }
    }

    private function show_preview($tasks, $kcfieldname, $levels) {
        //echo '<pre>',print_r($tasks),'</pre>';
        echo '<form action="" method="post">';
        global $PAGE, $CFG;
        require_once($CFG->libdir . '/tablelib.php');
        $table = new flexible_table('mod-poasassignment-task-import');
        $table->baseurl = $PAGE->url;
        $columns = array(
            'id',
            'name',
            'description',
            'comments',
            'kc',
            'modifications');
        $headers = array(
            'id',
            'Название задания',
            'Описание задания',
            'Комментарии',
            'Сложность',
            'Модификации'
        );
        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->collapsible(false);
        $table->initialbars(false);
        $table->set_attribute('class', 'poasassignment-table task-import');

        $table->setup();
        for ($i = 0; $i < count($tasks); $i++) {
            $row = array();
            // id
            $row[] = $tasks[$i]->id.'<input name="id_'.$i.'" type="hidden" value="'.$tasks[$i]->id.'" size="5"/>';
            // name
            $row[] = '<input name="name_'.$i.'" type="text" value="'.$tasks[$i]->name.'"/>';
            // description
            $row[] = '<textarea name="description_'.$i.'"/>'.$tasks[$i]->description.'</textarea>';
            // comments
            $row[] = '<textarea name="comments_'.$i.'"/>'.$tasks[$i]->comments.'</textarea>';
            // complexity levels
            $select = '<select name="levels_'.$i.'[]" multiple="multiple">';
            foreach ($levels as $k=>$level) {
                if (array_search($k, $tasks[$i]->$kcfieldname) !== false) {
                    $select .= '<option selected="selected" value="'.$k.'">'.$level.'</option>';
                }
                else {
                    $select .= '<option value="'.$k.'">'.$level.'</option>';
                }
            }
            $select .= '</select>';
            $row[] = $select;
            // modifications
            $mod = '';
            foreach ($tasks[$i]->modifications as $modification) {
                $mod .= '['.$modification['id'].'] с коэффициентом '.$modification['kc'].'<br/>';
            }
            $row[] = $mod;
            $table->add_data($row);
        }
        $table->print_html();
        echo '<input type="hidden" name="count" value="'.count($tasks).'"/>';
        echo '<input type="hidden" name="page" value="tasksimport"/>';
        $model = poasassignment_model::get_instance();
        $poasassignmentid = $model->get_poasassignment()->id;
        echo '<input type="hidden" name="poasassignmentid" value="'.$poasassignmentid.'"/>';
        echo '<input type="hidden" name="id" value="'.$model->get_cm()->id.'"/>';
        echo '<input type="hidden" name="confirmed" value="confirmed"/>';
        echo '<input type="hidden" name="kcfieldname" value="' . $kcfieldname . '"/>';
        echo '<input type="submit" name="submit" value="Импорт скачанных заданий"/>';
        echo '</form>';

    }
}
class tasksimport_form extends moodleform {
    function definition(){
        $mform = $this->_form;
        $instance = $this->_customdata;

        // Подключить файл с конфигурацией соединения с внешней базой данных
        require_once(dirname(dirname(__FILE__)).'/additional/auditor_sync/config.php');
        //print_r($config);

        $mform->addElement('text','server',get_string('server','poasassignment'),array('size'=>45));
        $mform->setDefault('server', $config['server']);
        $mform->addRule('server', null, 'required', null, 'client');

        $mform->addElement('text','database',get_string('database','poasassignment'),array('size'=>45));
        $mform->setDefault('database', $config['database']);
        $mform->addRule('database', null, 'required', null, 'client');

        $mform->addElement('text','dbuser',get_string('dbuser','poasassignment'),array('size'=>45));
        $mform->setDefault('dbuser', $config['dbuser']);
        $mform->addRule('dbuser', null, 'required', null, 'client');

        $mform->addElement('password','dbpass',get_string('dbpass','poasassignment'),array('size'=>45));
        $mform->setDefault('dbpass', $config['dbpass']);

        $mform->addElement('text','lessontypeid','lessontypeid', array('size'=>45));
        $mform->addRule('lessontypeid', null, 'required', null, 'client');

        $mform->addElement('select', 'kcfield', 'Поле уровня сложности', $instance['options']);


        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);

        $mform->addElement('hidden', 'page', 'tasksimport');
        $mform->setType('page', PARAM_TEXT);


        $this->add_action_buttons(true, get_string('import', 'poasassignment'));
        $mform->addElement('submit', 'submitbutton', get_string('sync', 'poasassignment'));
    }
}