<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '/model.php');

class testresults_page extends abstract_page {
    private $assigneeid;
    private $attemptid;
    private $groupid;
    private $groupname;
    private $realassigneeid;
    private $id;
    private $context;

    function __construct() {
        $this->assigneeid = optional_param('assigneeid', 0, PARAM_INT);
        $this->groupid = optional_param('groupid', 0, PARAM_INT);
        $this->groupname = optional_param('groupname', '', PARAM_TEXT);
        $this->id = optional_param('id', 0, PARAM_INT);

        $poasmodel = poasassignment_model::get_instance();
        $this->context = $poasmodel->get_context();
    }

    function has_satisfying_parameters() {
        $poasmodel = poasassignment_model::get_instance();
        $graders = $poasmodel->get_used_graders();
        foreach ($graders as $grader) {
            if ($grader->name == 'remote_autotester') {
                if (has_capability('mod/poasassignment:grade', $this->context)) {
                    return true;
                }
                elseif ($poasmodel->assignee->id) {
                    if ($poasmodel->get_last_attempt($poasmodel->assignee->id)) {
                        return true;
                    }
                    else {
                        $this->lasterror = 'nothingtoshow';
                        return false;
                    }
                }
                break;
            }
        }
        $this->lasterror = 'raisnotinstalled';
        return false;
    }

    function pre_view() {
        global $USER;
        if (has_capability('mod/poasassignment:grade', $this->context)) {
            if ($USER->sesskey == $_POST['sesskey']) {
                if ($_POST['attemptaction'] && is_array($_POST['attemptaction'])) {
                    require_once(dirname(dirname(__FILE__)) . '/grader/remote_autotester/remote_autotester.php');
                    foreach ($_POST['attemptaction'] as $attemptid => $action) {
                        if ($action == 'ignor') {
                            poasassignment_model::disable_attempt_penalty($attemptid);
                        }
                        elseif ($action == 'fail') {
                            remote_autotester::set_result($attemptid, 0);
                        }
                        elseif ($action == 'ok') {
                            remote_autotester::set_result($attemptid, 1);
                        }
                    }
                    remote_autotester::put_rating(poasassignment_model::get_instance()->poasassignment->id, $this->assigneeid);
                    // Redirect with OK message
                    $params = $_REQUEST;
                    unset($params['attemptaction']);
                    unset($params['save']);
                    $params['saved'] = 'ok';
                    redirect(new moodle_url('view.php',$params));
                }
            }
        }
    }

    function view() {
        $poasmodel = poasassignment_model::get_instance();
        if (has_capability('mod/poasassignment:grade', $this->context)) {
            $dataassignees = array(0 => '-');
            $datagroups = array(0 => '-');
            $attemptsresult = array();

            // Get all assignees
            $assignees = $this->smart_get_assignees();
            foreach ($assignees as $assignee) {
                $dataassignees[$assignee->id] = $assignee->lastname . ' ' . $assignee->firstname;
            }

            // Get assignees groups
            $groups = $this->get_all_groups();
            foreach ($groups as $group) {
                $datagroups[$group->id] = $group->name;
            }

            // Get attempts
            if ($this->assigneeid) {
                $this->realassigneeid = $this->assigneeid;
                require_once(dirname(dirname(__FILE__)) . '/grader/remote_autotester/remote_autotester.php');
                $attemptsresult = remote_autotester::get_attempts_results($this->assigneeid);
            }

            $mform = new attempt_choose_ext_form(null,
                array(
                    'groups' => $datagroups,
                    'assignees' => $dataassignees,
                    'id' => $this->id),
                'get');
            $mform->set_data(array('groupid' => $this->groupid, 'assigneeid' => $this->assigneeid));
            $mform->display();
        }
        else {
            // Get current assignee id and show it's results
            if ($poasmodel->assignee->id) {
                $this->realassigneeid = $poasmodel->assignee->id;
                require_once(dirname(dirname(__FILE__)) . '/grader/remote_autotester/remote_autotester.php');
                $attemptsresult = remote_autotester::get_attempts_results($poasmodel->assignee->id);
            }
        }
        if (isset($attemptsresult)) {
            $this->show_attempts_result($attemptsresult);
        }
    }



    /**
     * Show all attempts
     *
     * @param $attemptsresult array of results
     */
    private function show_attempts_result($attemptsresult)
    {
        global $PAGE, $USER;
        $PAGE->requires->js('/mod/poasassignment/grader/remote_autotester/jquery-1.9.1.min.js');
        $PAGE->requires->js('/mod/poasassignment/grader/remote_autotester/testresults.js');
        $poasmodel = poasassignment_model::get_instance();
        ?>
        <div class="testresults">
            <form action="" method="post">
                <?php if ($_GET['saved'] == 'ok' && has_capability('mod/poasassignment:grade', $this->context)): ?>
                    <div class="saved">
                        <?php echo get_string('testresultsweresaved', 'poasassignment_remote_autotester') ?>
                    </div>
                <?php endif ?>
                <?php if ($this->realassigneeid): ?>
                    <div class="report">
                        <?php
                            $statistics = remote_autotester::get_statistics($attemptsresult, $this->realassigneeid);
                        ?>
                        <table class="poasassignment-table">
                            <?php foreach ($statistics as $key => $value): ?>
                                <?php if ($value !== false): ?>
                                    <tr>
                                        <td class="header"><?php echo get_string($key, 'poasassignment_remote_autotester') ?></td>
                                        <td><?php echo $value ?></td>
                                    </tr>
                                <?php endif ?>
                            <?php endforeach ?>
                        </table>
                    </div>
                <?php endif ?>
                <span><?php echo get_string('allattemptsactions', 'poasassignment_remote_autotester')?>:</span>
                <span class="hideall"><a href="javascript:void(0)">[<?php echo get_string('hideall', 'poasassignment_remote_autotester')?>]</a></span>
                <span class="showall"><a href="javascript:void(0)">[<?php echo get_string('showall', 'poasassignment_remote_autotester')?>]</a></span>
                <table>
                    <thead>
                    <tr>
                        <td>№</td>
                        <td><?php echo get_string('attemptdate', 'poasassignment_remote_autotester') ?></td>
                        <td><?php echo get_string('raattemptstatus', 'poasassignment_remote_autotester') ?></td>
                        <td><?php echo get_string('attemptresult', 'poasassignment_remote_autotester') ?></td>
                        <?php $this->get_results_thead_td() ?>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = count ($attemptsresult);
                        foreach ($attemptsresult as $attemptresult) {
                            $this->show_attempt_html($attemptresult, $i);
                            $i--;
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>№</td>
                            <td><?php echo get_string('attemptdate', 'poasassignment_remote_autotester') ?></td>
                            <td><?php echo get_string('raattemptstatus', 'poasassignment_remote_autotester') ?></td>
                            <td><?php echo get_string('attemptresult', 'poasassignment_remote_autotester') ?></td>
                            <?php $this->get_results_thead_td() ?>
                        </tr>
                    </tfoot>
                </table>
                <?php if (has_capability('mod/poasassignment:grade', $this->context)): ?>
                    <div class="submit">
                        <?php foreach ($_GET as $k => $v): ?>
                            <?php if ($_GET['saved']): ?>
                                <?php continue; ?>
                            <?php endif ?>
                            <input type="hidden" name="<?php echo $k ?>" value="<?php echo htmlspecialchars($v) ?>"/>
                        <?php endforeach ?>
                        <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey ?>"/>
                        <input type="submit" name="save" value="<?php echo get_string('submittestresult', 'poasassignment_remote_autotester') ?>"/>
                    </div>
                <?php endif ?>
            </form>
        </div>
        <?php
        return;
    }

    /**
     * Depending on capability, show three column headers for grading or
     * single column with info
     */
    private function get_results_thead_td() {
        if (has_capability('mod/poasassignment:grade', $this->context)) {
            ?>
            <td class="fail"><?php echo get_string('attemptfail', 'poasassignment_remote_autotester') ?></td>
            <td class="ignor"><?php echo get_string('attemptignore', 'poasassignment_remote_autotester') ?></td>
            <td class="ok"><?php echo get_string('attemptok', 'poasassignment_remote_autotester') ?></td>
            <?php
        }
        else {
            ?>
            <td colspan="3"><?php echo get_string('finaldecision', 'poasassignment_remote_autotester') ?></td>
            <?php
        }
    }

    /**
     * Show single attempt info
     *
     * @param $attemptresult array of attempt's result
     * @param $i number of attempt
     */
    private function show_attempt_html($attemptresult, $i)
    {
        $totaltests = FALSE;
        $oktest = 0;
        if (isset($attemptresult->tests) && is_array($attemptresult->tests)) {
            $totaltests = count($attemptresult->tests);
            foreach ($attemptresult->tests as $test) {
                if ($test->testpassed == 1)
                    $oktest++;
            }
        }
        $graderesult = false;
        if (isset($attemptresult->disablepenalty) && $attemptresult->disablepenalty == 1) {
            $graderesult = POASASSIGNMENT_REMOTE_AUTOTESTER_IGNORE;
        }
        elseif (isset($attemptresult->disablepenalty) && $attemptresult->disablepenalty == 0) {
            if ($attemptresult->result == 1)
                $graderesult = POASASSIGNMENT_REMOTE_AUTOTESTER_OK;
            else
                $graderesult = POASASSIGNMENT_REMOTE_AUTOTESTER_FAIL;
        }
        ?>
        <tr attemptinfo data-attempt="<?php echo $i ?>">
            <td><a name="att<?php echo $i ?>"></a><?php echo $i ?></td>
            <td><?php echo date("d.m.Y H:i:s", $attemptresult->attemptdate) ?></td>
            <td><?php echo remote_autotester::get_attempt_status($attemptresult)->status ?></td>
            <td>
                <?php
                    if ($totaltests !== FALSE) {
                        echo $oktest . ' / ' . $totaltests;
                    }
                    else {
                        echo 'n/a';
                    }
                ?>
            </td>
            <?php $this->get_results_attempt_td($graderesult, $attemptresult->attemptid) ?>
        </tr>
        <tr class="attemptservice" data-for-attempt="<?php echo $i ?>">
            <td colspan="7">
                <?php if ($attemptresult->compilemessage): ?>
                    <span class="showcompileerror"><a href="javascript:void(0)">[<?php echo get_string('showcompileerror', 'poasassignment_remote_autotester')?>]</a></span>
                    <span class="hidecompileerror"><a href="javascript:void(0)">[<?php echo get_string('hidecompileerror', 'poasassignment_remote_autotester')?>]</a></span>
                <?php endif ?>
                <?php if (isset($attemptresult->tests) && is_array($attemptresult->tests)): ?>
                    <span class="showtests"><a href="javascript:void(0)">[<?php echo get_string('showtests', 'poasassignment_remote_autotester')?>]</a></span>
                    <span class="hidetests"><a href="javascript:void(0)">[<?php echo get_string('hidetests', 'poasassignment_remote_autotester')?>]</a></span>
                    <?php if (has_capability('poasassignment/remote_autotester:seetestinput', $this->context)): ?>
                        <span class="showallinput"><a href="javascript:void(0)">[<?php echo get_string('showallinput', 'poasassignment_remote_autotester')?>]</a></span>
                        <span class="hideallinput"><a href="javascript:void(0)">[<?php echo get_string('hideallinput', 'poasassignment_remote_autotester')?>]</a></span>
                    <?php endif ?>
                <?php endif ?>
            </td>
        </tr>
        <?php if ($attemptresult->compilemessage || (isset($attemptresult->tests) && is_array($attemptresult->tests))): ?>
            <tr class="other" data-for-attempt="<?php echo $i ?>">
                <td colspan="7">
                    <?php if ($attemptresult->compilemessage): ?>
                        <div class="compileerror">
                            <pre><?php echo $attemptresult->compilemessage ?></pre>
                        </div>
                    <?php endif ?>
                    <?php if (isset($attemptresult->tests) && is_array($attemptresult->tests)): ?>
                        <div class="tests">
                            <?php foreach ($attemptresult->tests as $test): ?>
                                <?php $this->show_test_html($test) ?>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>
                </td>
            </tr>
        <?php endif ?>
        <?php
    }

    private function get_results_attempt_td($graderesult, $attemptid) {
        if (has_capability('mod/poasassignment:grade', $this->context)) {
            ?>
                <td class="fail">
                    <input
                        type="radio"
                        value="fail"
                        title="<?php echo get_string('attemptfail', 'poasassignment_remote_autotester')?>"
                        <?php if ($graderesult === POASASSIGNMENT_REMOTE_AUTOTESTER_FAIL): ?>
                        checked="checked"
                    <?php endif?>
                        name="attemptaction[<?php echo $attemptid?>]"/>
                </td>
                <td class="ignor">
                    <input
                        type="radio"
                        value="ignor"
                        title="<?php echo get_string('attemptignore', 'poasassignment_remote_autotester')?>"
                        <?php if ($graderesult === POASASSIGNMENT_REMOTE_AUTOTESTER_IGNORE): ?>
                        checked="checked"
                    <?php endif ?>
                        name="attemptaction[<?php echo $attemptid?>]"/>
                </td>
                <td class="ok">
                    <input
                        type="radio"
                        value="ok"
                        title="<?php echo get_string('attemptok', 'poasassignment_remote_autotester')?>"
                        <?php if ($graderesult === POASASSIGNMENT_REMOTE_AUTOTESTER_OK): ?>
                        checked="checked"
                    <?php endif?>
                        name="attemptaction[<?php echo $attemptid ?>]"/>
                </td>
            <?php
        }
        else {
            switch($graderesult) {
                case POASASSIGNMENT_REMOTE_AUTOTESTER_FAIL:
                    $message = get_string('attemptfail', 'poasassignment_remote_autotester');
                    $class = 'fail';
                    break;
                case POASASSIGNMENT_REMOTE_AUTOTESTER_IGNORE:
                    $message = get_string('attemptignore', 'poasassignment_remote_autotester');
                    $class = 'ignor';
                    break;
                case POASASSIGNMENT_REMOTE_AUTOTESTER_OK:
                    $message = get_string('attemptok', 'poasassignment_remote_autotester');
                    $class = 'ok';
                    break;
            }
            ?>
            <td colspan="3" class="<?php echo $class ?> result-for-student">
                <?php echo $message?>
            </td>
            <?php
        }
    }

    /**
     * Show test data
     *
     * @param $test test object from DB
     * @param $attemptnumber index number of attempt
     */
    private function show_test_html($test) {
        $class = $test->testpassed ?  "testpassed" : "testfailed";
        $poasmodel = poasassignment_model::get_instance();
        ?>
        <div class="test">
            <div class="testinfo <?php echo $class?>">
                <span class="caption">
                    <?php if (has_capability('poasassignment/remote_autotester:seetestnames', $this->context)): ?>
                        <?php echo $test->test ?>
                    <?php else: ?>
                        -
                    <?php endif ?>
                </span>
                <span class="decision">
                    <?php if ($test->testpassed): ?>
                        <img src="/mod/poasassignment/pix/yes.png" alt=""/> <span><?php echo get_string('testpassed', 'poasassignment_remote_autotester')?></span>
                    <?php else: ?>
                        <img src="/mod/poasassignment/pix/no.png" alt=""/> <span><?php echo get_string('testnotpassed', 'poasassignment_remote_autotester')?></span>
                    <?php endif ?>
                </span>
            </div>
            <div class="testservice">
                <?php if (has_capability('poasassignment/remote_autotester:seetestinput', $this->context)): ?>
                    <span class="showinput"><a href="javascript:void(0)">[<?php echo get_string('showinput', 'poasassignment_remote_autotester')?>]</a></span>
                    <span class="hideinput"><a href="javascript:void(0)">[<?php echo get_string('hideinput', 'poasassignment_remote_autotester')?>]</a></span>
                <?php endif ?>
            </div>
            <?php if (has_capability('poasassignment/remote_autotester:seetestinput', $this->context)): ?>
                <div class="input">
                    <div class="caption"><?php echo get_string('inputdata', 'poasassignment_remote_autotester') ?>:</div>
                    <pre><?php echo $test->testin ?></pre>
                </div>
            <?php endif ?>
            <?php
                $diff = FALSE;
                if ($test->testpassed != 1) {
                    if (isset($test->studentout) && isset($test->testout)) {
                        $testoutarray = explode("\n", $test->testout);
                        $testoutarray = array_values(array_diff($testoutarray, array("")));
                        $strudentoutarray = explode("\n", $test->studentout);
                        $strudentoutarray = array_values(array_diff($strudentoutarray, array("")));
                        $diff = $this->diff($strudentoutarray, $testoutarray);
                    }
                }
                else {
                    $diff = TRUE;
                }

            ?>
            <?php if ($diff && has_capability('poasassignment/remote_autotester:seediff', $this->context)):?>
                <div class="diff">
                    <div class="caption"><?php echo get_string('outdiff', 'poasassignment_remote_autotester')?> <?php echo remote_autotester::get_diff_comment($diff, $strudentoutarray, $testoutarray)?>:</div>
                    <?php if (is_array($diff)): ?>
                        <?php foreach ($diff as $element): ?>
                            <?php if (is_array($element)): ?>
                                <?php foreach ($element["i"] as $sub): ?>
                                    <pre class="test"><span>[<?php echo get_string('difftestsymbol', 'poasassignment_remote_autotester')?>]:</span> <?php echo $sub?></pre>
                                <?php endforeach ?>
                                <?php foreach ($element["d"] as $sub): ?>
                                    <pre class="student"><span>[<?php echo get_string('diffstudentsymbol', 'poasassignment_remote_autotester')?>]:</span> <?php echo $sub?></pre>
                                <?php endforeach ?>
                            <?php else: ?>
                                <pre class="same"><span>[<?php echo get_string('diffsamesymbol', 'poasassignment_remote_autotester')?>]:</span> <?php echo $element?></pre>
                            <?php endif ?>
                        <?php endforeach ?>
                    <?php else: ?>
                        <div class="same"><?php echo get_string('sameout', 'poasassignment_remote_autotester')?></div>
                    <?php endif ?>
                </div>
            <?php endif ?>
        </div>
        <?php
    }

    private function diff($old, $new){
        $maxlen = 0;
        foreach($old as $oindex => $ovalue){
            $nkeys = array_keys($new, $ovalue);
            foreach($nkeys as $nindex){
                $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                    $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
                if($matrix[$oindex][$nindex] > $maxlen){
                    $maxlen = $matrix[$oindex][$nindex];
                    $omax = $oindex + 1 - $maxlen;
                    $nmax = $nindex + 1 - $maxlen;
                }
            }
        }
        if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
        return array_merge(
            $this->diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
            array_slice($new, $nmax, $maxlen),
            $this->diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
    }

    private function get_all_groups() {
        global $DB;
        $model = poasassignment_model::get_instance();

        $where = array();
        $where[] = '{poasassignment_assignee}.cancelled = 0';
        $where[] = '{poasassignment_assignee}.poasassignmentid = ' . $model->poasassignment->id;

        $sql = 'SELECT {groups}.id, {groups}.name
            FROM {poasassignment_assignee}
            JOIN {user} on {poasassignment_assignee}.userid={user}.id
            JOIN {groups_members} on {poasassignment_assignee}.userid={groups_members}.userid
            JOIN {groups} on {groups_members}.groupid={groups}.id
            WHERE ' . implode(' AND ', $where) . '
            ORDER BY {groups}.name ASC
            ';
        $result = $DB->get_records_sql($sql);
        return $result;
    }
    private function smart_get_assignees() {
        global $DB;
        $model = poasassignment_model::get_instance();

        $where = array();
        if ($this->groupid > 0) {
            $where[] = '{groups}.id=' . $this->groupid;
        }
        if (strlen($this->groupname) > 0) {
            $where[] = '{groups}.name=`' . $this->groupname. '`';
        }
        $where[] = '{poasassignment_assignee}.cancelled = 0';
        $where[] = '{poasassignment_assignee}.poasassignmentid = ' . $model->poasassignment->id;
        $sql = 'SELECT {poasassignment_assignee}.*, firstname, lastname
            FROM {poasassignment_assignee}
            JOIN {user} on {poasassignment_assignee}.userid={user}.id
            JOIN {groups_members} on {poasassignment_assignee}.userid={groups_members}.userid
            JOIN {groups} on {groups_members}.groupid={groups}.id
            WHERE ' . implode(' AND ', $where) . '
            ORDER BY lastname ASC, firstname ASC, {user}.id ASC
            ';
        $result = $DB->get_records_sql($sql);
        return $result;
    }
}
class attempt_choose_ext_form extends moodleform {
    function definition() {
        global $DB;
        $mform = $this->_form;
        $instance = $this->_customdata;

        $mform->addElement('header', 'assigneefilter', get_string('assigneefilter', 'poasassignment_remote_autotester'));
        $mform->addElement('select', 'groupid', get_string('group', 'poasassignment_remote_autotester'), $instance['groups']);

        $mform->addElement('select', 'assigneeid', get_string('assignee', 'poasassignment_remote_autotester'), $instance['assignees']);

        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'page', 'testresults');
        $mform->setType('page', PARAM_ALPHA);

        $mform->addElement('submit', null, get_string('show'));
    }
}