<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '\model.php');
class criterionproblem_page extends abstract_page{
    //put your code here
    function get_cap() {
        return 'mod/poasassignment:managecriterions';
    }

    function pre_view() {

    }
    public static function display_in_navbar() {
        return false;
    }
    function view() {
        $errorcode = optional_param('code', -1, PARAM_INT);
        echo 'problem?<br>';
        switch($errorcode) {
            case POASASSIGNMENT_CRITERION_CANT_BE_DELETED:
                echo 'POASASSIGNMENT_CRITERION_CANT_BE_DELETED';
                break;
            case POASASSIGNMENT_CRITERION_CANT_BE_CHANGED:
                echo 'POASASSIGNMENT_CRITERION_CANT_BE_CHANGED';
                break;
            case POASASSIGNMENT_CRITERION_CANT_BE_CREATED:
                $this->creating_problem();
                break;
            default:
                echo '<p>' . get_string('unknowncriterionerror', 'poasassignment') . '</p>';

                break;
        }
    }
    private function creating_problem() {
        global $DB;
        $model = poasassignment_model::get_instance();
        echo get_string('criterioncantbecreated','poasassignment');
        echo '<br>';
        if ($problemstudents = $model->get_criterion_problem_students()) {
            echo '<form action="view.php?id=' . $model->get_cm()->id . '&page=criterionproblem&code='.POASASSIGNMENT_CRITERION_CREATE.'" method=post>';
            echo '<table class=poasassignment-table>';
            echo '<tr>';
            echo '<th>'.get_string('fullname', 'poasassignment') . '</th>';
            echo '<th>'.get_string('grade', 'poasassignment') . '</th>';
            echo '<th>'.get_string('putzero', 'poasassignment') . '</th>';
            echo '<th>'.get_string('putmax', 'poasassignment') . '</th>';
            echo '<th>'.get_string('puttotal', 'poasassignment') . '</th>';
            echo '<th>'.get_string('putcustom', 'poasassignment') . '</th>';
            echo '</tr>';

            foreach ($problemstudents as $problemstudent) {
                echo '<tr>';
                $user = $DB->get_record('user', array('id' => $problemstudent->userid));
                echo '<td>' . fullname($user, true) . '</td>';
                echo '<td>'.$problemstudent->rating.'</td>';
                echo '<td class=center><input type=radio value=putzero name=user'.$problemstudent->userid.'_action></td>';
                echo '<td class=center><input type=radio value=putmax name=user'.$problemstudent->userid.'_action></td>';
                echo '<td class=center><input type=radio value=puttotal name=user'.$problemstudent->userid.'_action></td>';
                echo '<td class=center>';
                echo '<input type=radio valut=putcustom name=user'.$problemstudent->userid.'_action>';
                echo '<input type=text name=user'.$problemstudent->userid.'_custom>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '<input type=submit value="'.get_string('createcriterions','poasassignment').'">';
            echo '</form>';
        }
    }
}

?>
