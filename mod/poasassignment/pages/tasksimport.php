<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '/model.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot.'/lib/tablelib.php');
class tasksimport_page extends abstract_page {
    var $poasassignment;
    var $mform;

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
        $model = poasassignment_model::get_instance();
        $poasassignmentid = $model->get_poasassignment()->id;
        $this->mform = new tasksimport_form(
            null,
            array(
                'id' => $model->get_cm()->id,
                'poasassignmentid' => $poasassignmentid)
        );
    }
    /**
     * Показать форму для настройки соединения с сервером
     */
    function view() {
        global $DB;
        $model = poasassignment_model::get_instance();
        if ($this->mform->get_data()) {
            $data = $this->mform->get_data();
            if ($db = mysql_connect($data->server, $data->dbuser, $data->dbpass)) {
                print_string('serverconnected', 'poasassignment');
                echo '</br>';

                mysql_set_charset('UTF8');
                if (!mysql_select_db($data->database, $db)) {
                    print_error('errorcantconnecttodatabase', 'poasassignment');
                }
                else {
                    print_string('databaseconnected', 'poasassignment');
                    echo '</br>';
                    $sql = "SELECT * FROM variants WHERE lessontypeid=".mysql_real_escape_string($data->lessontypeid);
                    if (!($result = mysql_query($sql))) {
                        echo get_string('errorcantrunquery', 'poasassignment');
                        echo $sql;
                    }
                    else{
                        // Узнать заранее идентификатор поля, в котором poasassignment хранит уровень сложности
                        $complexityfield = $DB->get_record(
                            'poasassignment_fields',
                            array('poasassignmentid' =>$model->get_poasassignment()->id, 'name' => $data->kcfieldname),
                            'id');

                        // Узнать все варианты для поля уровня сложности
                        $levels = $DB->get_records('poasassignment_variants', array('fieldid' => $complexityfield->id));

                        // Получить все уровни сложности из внешней базы данных
                        $sql = "SELECT * FROM mod_complexities";
                        if (!($complexities = mysql_query($sql))) {
                            echo get_string('errorcantrunquery', 'poasassignment');
                            echo $sql;
                        }
                        else {
                            while ($variant = mysql_fetch_assoc($result)) {
                                echo 'Задание<br/>';
                                echo '<pre>',print_r($variant),'</pre>';

                                $taskrecord = new stdClass();
                                $taskrecord->name = get_string('defaulttaskname', 'poasassignment') .' №' . $variant['num'];
                                $taskrecord->description = $variant['description'];

                                $kcfieldname = 'field' . $complexityfield->id;
                                $taskrecord->$kcfieldname = array();
                                // Для каждого задания получить список модификаций
                                $sql = "SELECT id, kc FROM modifications WHERE variantid=".mysql_real_escape_string($variant['id']);
                                if (!($modifications = mysql_query($sql))) {
                                    echo get_string('errorcantrunquery', 'poasassignment');
                                    echo $sql;
                                }
                                else {
                                    while ($modification = mysql_fetch_assoc($modifications)) {
                                        while($complexitiy = mysql_fetch_assoc($complexities)) {
                                            if ($modification['kc'] >= $complexitiy['kcmin']
                                                && $modification['kc'] <= $complexitiy['kcmax']) {
                                                echo "это " . $complexitiy['name'];
                                            }
                                        }
                                        echo 'Модификация<br/>';
                                        echo '<pre>',print_r($modification),'</pre>';
                                    }
                                }
                            }
                        }

                    }
                }
            }
            else {
                print_error('errorcantconnecttoserver', 'poasassignment');
            }
            echo '<pre>',print_r($data),'</pre>';
        }
        else {
            $this->mform->display();
        }
    }
}
class tasksimport_form extends moodleform {
    function definition(){
        global $DB;
        $mform = $this->_form;
        $instance = $this->_customdata;

        $mform->addElement('text','server',get_string('server','poasassignment'),array('size'=>45));
        $mform->addRule('server', null, 'required', null, 'client');

        $mform->addElement('text','database',get_string('database','poasassignment'),array('size'=>45));
        $mform->addRule('database', null, 'required', null, 'client');

        $mform->addElement('text','dbuser',get_string('dbuser','poasassignment'),array('size'=>45));
        $mform->addRule('dbuser', null, 'required', null, 'client');

        $mform->addElement('password','dbpass',get_string('dbpass','poasassignment'),array('size'=>45));

        $mform->addElement('text','lessontypeid','lessontypeid', array('size'=>45));
        $mform->addRule('lessontypeid', null, 'required', null, 'client');

        $mform->addElement('text','kcfieldname',get_string('kcfieldname', 'poasassignment'), array('size'=>45));
        $mform->addRule('kcfieldname', null, 'required', null, 'client');


        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);

        $mform->addElement('hidden', 'page', 'tasksimport');
        $mform->setType('page', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('import', 'poasassignment'));
    }
}
