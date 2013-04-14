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

    function __construct() {
        $this->assigneeid = optional_param('assigneeid', 0, PARAM_INT);
        $this->groupid = optional_param('groupid', 0, PARAM_INT);
        $this->groupname = optional_param('groupname', '', PARAM_TEXT);
        $this->id = optional_param('id', 0, PARAM_INT);
    }

    function has_satisfying_parameters() {
        $poasmodel = poasassignment_model::get_instance();
        $graders = $poasmodel->get_used_graders();
        foreach ($graders as $grader) {
            if ($grader->name == 'remote_autotester') {
                if (has_capability('mod/poasassignment:grade', $poasmodel->get_context())) {
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

    function view() {
        $poasmodel = poasassignment_model::get_instance();
        if (has_capability('mod/poasassignment:grade', $poasmodel->get_context())) {
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

    private function get_statistics($attemptsresult) {
        $assignee = poasassignment_model::get_instance()->assignee_get_by_id($this->realassigneeid);
        $statistics = array();
        if (isset($assignee)) {
            $statistics['assignee'] = $assignee->firstname . ' ' . $assignee->lastname;
        }
        $statistics['firstpassedattempt'] = false;
        $statistics['totalpenalty'] = 0;
        $statistics['totaltestattempts'] = count($attemptsresult);
        $statistics['ignoredtestattempts'] = 0;
        $statistics['failedtestattempts'] = 0;
        $statistics['bestresult'] = false;
        $statistics['worstresult'] = false;

        $i = count ($attemptsresult);
        foreach ($attemptsresult as $attemptresult) {
            if (isset($attemptresult->disablepenalty) && $attemptresult->disablepenalty == 1) {
                $statistics['ignoredtestattempts']++;
            }
            else {
                if ($attemptresult->result == 1) {
                    $statistics['firstpassedattempt'] = $i;
                }
                elseif ($attemptresult->result == 0) {
                    $statistics['failedtestattempts']++;
                }
                $oktest = 0;
                foreach ($attemptresult->tests as $test) {
                    if ($test->testpassed == 1) {
                        $oktest++;
                    }
                }
                if ($statistics['worstresult'] === false || $oktest < $statistics['worstresult']) {
                    $statistics['worstresult'] = $oktest;
                }
                if ($statistics['bestresult'] === false || $oktest > $statistics['bestresult']) {
                    $statistics['bestresult'] = $oktest;
                }
            }
            $i--;
        }
        $penalty = poasassignment_model::get_instance()->poasassignment->penalty;
        if ($statistics['failedtestattempts'] > 0 && $penalty > 0) {
            $statistics['totalpenalty'] = $statistics['failedtestattempts'] * $penalty;
        }
        return $statistics;
    }

    private function show_attempts_result($attemptsresult)
    {
        global $PAGE;
        $PAGE->requires->js('/mod/poasassignment/grader/remote_autotester/jquery-1.9.1.min.js');
        $PAGE->requires->js('/mod/poasassignment/grader/remote_autotester/testresults.js');
        ?>
        <div class="testresults">
            <form action="">
                <div class="report">
                    <?
                        $statistics = $this->get_statistics($attemptsresult);
                    ?>
                    <table class="poasassignment-table">
                        <?foreach ($statistics as $key => $value): ?>
                            <?if ($value !== false): ?>
                                <tr>
                                    <td class="header"><?=get_string($key, 'poasassignment_remote_autotester')?></td>
                                    <td><?=$value?></td>
                                </tr>
                            <? endif?>
                        <? endforeach?>
                    </table>
                </div>
                <span><?=get_string('allattemptsactions', 'poasassignment_remote_autotester')?>:</span>
                <span class="hideall"><a href="javascript:void(0)">[<?=get_string('hideall', 'poasassignment_remote_autotester')?>]</a></span>
                <span class="showall"><a href="javascript:void(0)">[<?=get_string('showall', 'poasassignment_remote_autotester')?>]</a></span>
                <table>
                    <thead>
                    <tr>
                        <td>№</td>
                        <td><?=get_string('attemptdate', 'poasassignment_remote_autotester')?></td>
                        <td><?=get_string('raattemptstatus', 'poasassignment_remote_autotester')?></td>
                        <td><?=get_string('attemptresult', 'poasassignment_remote_autotester')?></td>
                        <td class="fail"><?=get_string('attemptfail', 'poasassignment_remote_autotester')?></td>
                        <td class="ignor"><?=get_string('attemptignore', 'poasassignment_remote_autotester')?></td>
                        <td class="ok"><?=get_string('attemptok', 'poasassignment_remote_autotester')?></td>
                    </tr>
                    </thead>
                    <tbody>
                        <?
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
                        <td><?=get_string('attemptdate', 'poasassignment_remote_autotester')?></td>
                        <td><?=get_string('raattemptstatus', 'poasassignment_remote_autotester')?></td>
                        <td><?=get_string('attemptresult', 'poasassignment_remote_autotester')?></td>
                        <td class="fail"><?=get_string('attemptfail', 'poasassignment_remote_autotester')?></td>
                        <td class="ignor"><?=get_string('attemptignore', 'poasassignment_remote_autotester')?></td>
                        <td class="ok"><?=get_string('attemptok', 'poasassignment_remote_autotester')?></td>
                    </tr>
                    </tfoot>
                </table>
            </form>
        </div>
        <?
        return;
    }
    private function show_attempt_html($attemptresult, $i)
    {
        $totaltests = FALSE;
        $oktest = 0;
        $class = FALSE;
        if (isset($attemptresult->tests) && is_array($attemptresult->tests)) {
            $totaltests = count($attemptresult->tests);
            foreach ($attemptresult->tests as $test) {
                if ($test->testpassed == 1)
                    $oktest++;
            }
            if ($totaltests > 0) {
                $percent = $oktest / $totaltests;
                if ($percent == 1) {
                    $class = "ideal";
                }
                elseif ($percent > 0.75) {
                    $class = "good";
                }
                elseif ($percent > 0.5) {
                    $class = "normal";
                }
                else {
                    $class = "bad";
                }
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
        <tr class="<?/*=$class*/?> attemptinfo" data-attempt="<?=$i?>">
            <td><a name="att<?=$i?>"></a><?=$i?></td>
            <td><?=date("d.m.Y H:i:s", $attemptresult->attemptdate)?></td>
            <td><?=remote_autotester::get_attempt_status($attemptresult)->status?></td>
            <td>
                <?
                    if ($totaltests !== FALSE) {
                        echo $oktest . ' / ' . $totaltests;
                    }
                    else {
                        echo 'n/a';
                    }
                ?>
            </td>
            <td class="fail">
                <input
                    type="radio"
                    value="fail"
                    title="<?=get_string('attemptfail', 'poasassignment_remote_autotester')?>"
                    <?if ($graderesult === POASASSIGNMENT_REMOTE_AUTOTESTER_FAIL): ?>
                        checked="checked"
                    <? endif?>
                    name="attemptaction[<?=$attemptresult->attemptid?>]"/>
            </td>
            <td class="ignor">
                <input
                    type="radio"
                    value="ignor"
                    title="<?=get_string('attemptignore', 'poasassignment_remote_autotester')?>"
                    <?if ($graderesult === POASASSIGNMENT_REMOTE_AUTOTESTER_IGNORE): ?>
                        checked="checked"
                    <? endif?>
                    name="attemptaction[<?=$attemptresult->attemptid?>]"/>
            </td>
            <td class="ok">
                <input
                    type="radio"
                    value="ok"
                    title="<?=get_string('attemptok', 'poasassignment_remote_autotester')?>"
                    <?if ($graderesult === POASASSIGNMENT_REMOTE_AUTOTESTER_OK): ?>
                        checked="checked"
                    <? endif?>
                    name="attemptaction[<?=$attemptresult->attemptid?>]"/>
            </td>
        </tr>
        <tr class="attemptservice" data-for-attempt="<?=$i?>">
            <td colspan="7">
                <?if ($attemptresult->compilemessage): ?>
                    <span class="showcompileerror"><a href="javascript:void(0)">[<?=get_string('showcompileerror', 'poasassignment_remote_autotester')?>]</a></span>
                    <span class="hidecompileerror"><a href="javascript:void(0)">[<?=get_string('hidecompileerror', 'poasassignment_remote_autotester')?>]</a></span>
                <? endif?>
                <?if (isset($attemptresult->tests) && is_array($attemptresult->tests)): ?>
                    <span class="showtests"><a href="javascript:void(0)">[<?=get_string('showtests', 'poasassignment_remote_autotester')?>]</a></span>
                    <span class="hidetests"><a href="javascript:void(0)">[<?=get_string('hidetests', 'poasassignment_remote_autotester')?>]</a></span>
                    <? if (TRUE): ?>
                        <span class="showallinput"><a href="javascript:void(0)">[<?=get_string('showallinput', 'poasassignment_remote_autotester')?>]</a></span>
                        <span class="hideallinput"><a href="javascript:void(0)">[<?=get_string('hideallinput', 'poasassignment_remote_autotester')?>]</a></span>
                    <? endif ?>
                <? endif?>
            </td>
        </tr>
        <? if ($attemptresult->compilemessage || (isset($attemptresult->tests) && is_array($attemptresult->tests))): ?>
            <tr class="other" data-for-attempt="<?=$i?>">
                <td colspan="7">
                    <? if ($attemptresult->compilemessage): ?>
                        <div class="compileerror">
                            <pre><?=$attemptresult->compilemessage?></pre>
                        </div>
                    <? endif ?>
                    <? if (isset($attemptresult->tests) && is_array($attemptresult->tests)): ?>
                        <div class="tests">
                            <? foreach ($attemptresult->tests as $test): ?>
                                <?$this->show_test_html($test)?>
                            <? endforeach ?>
                        </div>
                    <? endif ?>
                </td>
            </tr>
        <? endif ?>
        <?
    }

    /**
     * Show test data
     *
     * @param $test test object from DB
     * @param $attemptnumber index number of attempt
     */
    private function show_test_html($test) {
        $class = $test->testpassed ?  "testpassed" : "testfailed";
        ?>
        <div class="test">
            <div class="testinfo <?=$class?>">
                <span class="caption"><?=$test->test?></span>
                <span class="decision">
                    <?if ($test->testpassed): ?>
                        <img src="/mod/poasassignment/pix/yes.png" alt=""/> <span><?=get_string('testpassed', 'poasassignment_remote_autotester')?></span>
                    <? else: ?>
                        <img src="/mod/poasassignment/pix/no.png" alt=""/> <span><?=get_string('testnotpassed', 'poasassignment_remote_autotester')?></span>
                    <? endif?>
                </span>
            </div>
            <div class="testservice">
                <?if (TRUE): ?>
                    <span class="showinput"><a href="javascript:void(0)">[<?=get_string('showinput', 'poasassignment_remote_autotester')?>]</a></span>
                    <span class="hideinput"><a href="javascript:void(0)">[<?=get_string('hideinput', 'poasassignment_remote_autotester')?>]</a></span>
                <? endif?>
            </div>
            <? if (TRUE): // TODO Проверка капабилити ?>
                <div class="input">
                    <div class="caption"><?=get_string('inputdata', 'poasassignment_remote_autotester')?>:</div>
                    <pre><?=$test->testin?></pre>
                </div>
            <? endif ?>
            <?
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
            <? if ($diff): // TODO?>
                <div class="diff">
                    <div class="caption"><?=get_string('outdiff', 'poasassignment_remote_autotester')?> <?=remote_autotester::get_diff_comment($diff, $strudentoutarray, $testoutarray)?>:</div>
                    <?if (is_array($diff)): ?>
                        <? foreach ($diff as $element): ?>
                            <? if (is_array($element)): ?>
                                <? foreach ($element["i"] as $sub): ?>
                                    <pre class="test"><span>[<?=get_string('difftestsymbol', 'poasassignment_remote_autotester')?>]:</span> <?=$sub?></pre>
                                <? endforeach ?>
                                <? foreach ($element["d"] as $sub): ?>
                                    <pre class="student"><span>[<?=get_string('diffstudentsymbol', 'poasassignment_remote_autotester')?>]:</span> <?=$sub?></pre>
                                <? endforeach ?>
                            <? else: ?>
                                <pre class="same"><span>[<?=get_string('diffsamesymbol', 'poasassignment_remote_autotester')?>]:</span> <?=$element?></pre>
                            <? endif ?>
                        <? endforeach ?>
                    <? else: ?>
                        <div class="same"><?=get_string('sameout', 'poasassignment_remote_autotester')?></div>
                    <?endif?>
                </div>
            <? endif ?>
        </div>
        <?
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