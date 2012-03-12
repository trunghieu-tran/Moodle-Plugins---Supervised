<?php
require_once(dirname(dirname(dirname(__FILE__))).'/model.php');
/**
 * Плагин для синхронизации с заданиями Аудитора
 */
class auditor_sync {

    // Singletone
    private static $instance = null;
    private function __construct() {

    }
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Импортировать задание из Аудитора
     *
     * @access public
     * @param $taskrecord данные о задании. Соответствуют записям таблицы poasassignment_tasks
     * с двумя дополнительными полями - auditorvariantid и comments     *
     * @return mixed идентификатор вставленной записи
     */
    public function import_task($taskrecord) {
        global $DB;

        $auditorrecord = new stdClass();
        $auditorrecord->auditorvariantid = $taskrecord->id;
        unset($taskrecord->id);
        $auditorrecord->comments = $taskrecord->comments;
        unset($taskrecord->comments);
        $auditorrecord->poasassignmenttaskid = poasassignment_model::get_instance()->add_task($taskrecord);

        $DB->insert_record('auditor_sync_tasks', $auditorrecord);

        return $auditorrecord->poasassignmenttaskid;
    }

    /**
     * Выполнить синхронизацию заданий (вызывается по cron)
     *
     * @access public
     */
    public function synchronize() {
        // Подключить файл с конфигурацией соединения с внешней базой данных
        require_once('config.php');
        $error = $this->connect_auditor($config['server'], $config['dbuser'], $config['dbpass'], $config['database']);
        if ($error == false) {
            global $DB;
            $storedtasks = $DB->get_records('auditor_sync_tasks');
            $sql = 'SELECT * FROM variants';
            if (!($result = mysql_query($sql))) {
                echo get_string('errorcantrunquery', 'poasassignment');
                echo ' '.$sql;
            }
            else{
                // Для каждого задания из аудитора
                while ($actualtask = mysql_fetch_assoc($result)) {
                    // найти те задания poasassignment, которые были импортироаны из него
                    $taskstosync = $this->get_stored_tasks_by_auditor_variant_id($actualtask['id'], $storedtasks);

                    // и для всех таких заданий обновить комментарии и описание задачи
                    foreach ($taskstosync as $tasktosync) {
                        if ($tasktosync->comments != $actualtask['comments']) {
                            $tasktosync->comments = $actualtask['comments'];
                            //echo 'UPDATE RECORD auditor_sync_tasks ',print_r($tasktosync);
                            $DB->update_record('auditor_sync_tasks', $tasktosync);
                        }
                        $poasassignmenttask = $DB->get_record('poasassignment_tasks', array('id' => $tasktosync->poasassignmenttaskid), 'id, description');
                        if ($poasassignmenttask) {
                            if ($poasassignmenttask->description != $actualtask['description']) {
                                $poasassignmenttask->description = $actualtask['description'];
                                //echo 'UPDATE RECORD poasassignment_tasks ',print_r($poasassignmenttask);
                                $DB->update_record('poasassignment_tasks', $poasassignmenttask);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Получить все задания в Moodle, которые базируются на заданиях аудитора
     *
     * @access public
     * @param $id идентификатор задания в Аудиторе
     * @param $storedtasks все импортированные задания
     * @return array задания poasassignment, базирующиеся на указанном задании Аудитора
     */
    private function get_stored_tasks_by_auditor_variant_id($id, $storedtasks) {
        $tasks = array();
        foreach ($storedtasks as $storedtask) {
            if ($storedtask->auditorvariantid == $id) {
                $tasks[$storedtask->id] = $storedtask;
            }
        }
        return $tasks;
    }

    /**
     * Подключение к серверу базы данных и базе данных
     *
     * @access public
     * @param $server адрес сервера
     * @param $user имя пользователя БД
     * @param $password пароль пользователя БД
     * @param $database имя БД
     * @return bool|string текст ошибки или false, если ошибок подключения не возникло
     */
    public function connect_auditor($server, $user, $password, $database) {
        if ($db = mysql_connect($server, $user, $password)) {
            mysql_set_charset('UTF8');
            if (!mysql_select_db($database, $db)) {
                return
                    get_string('errorcantconnecttodatabase', 'poasassignment').
                    ' - '.
                    mysql_error();
            }
            else {
                return false;
            }
        }
        else {
            return
                get_string('errorcantconnecttoserver', 'poasassignment').
                ' - '.
                mysql_error();
        }
    }

    /**
     * Получить массив полей экземпляра задания, которые могут хранить уровень сложности.
     * Такие поля имеют тип MULTILIST
     *
     * @access public
     * @param int $poasassignmentid идентификатор экземпляра задания
     * @return array массив вида [id] => название поля
     */
    public function get_possible_kc_fields($poasassignmentid) {
        global $DB;
        $fields = $DB->get_records(
            'poasassignment_fields',
            array('poasassignmentid' => $poasassignmentid, 'ftype' => MULTILIST),
            'id',
            'id, name'
        );
        foreach ($fields as $k => $v) {
            $fields[$k] = $v->name;
        }
        return $fields;
    }
}