<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '/model.php');

class testresults_page extends abstract_page {
    private $assigneeid;
    private $attemptid;
    private $groupid;
    private $id;

    function __construct() {
        $this->attemptid = optional_param('attemptid', 0, PARAM_INT);
        $this->assigneeid = optional_param('assigneeid', 0, PARAM_INT);
        $this->groupid = optional_param('groupid', 0, PARAM_INT);
        $this->id = optional_param('id', 0, PARAM_INT);
    }

    function view() {
        global $DB;
        $poasmodel = poasassignment_model::get_instance();
        $dataassignees = array(0 => '-');
        $dataattempts = array(0 => '-');
        $attemptsresult = array();

        // Always get all groups
        $assignees = $poasmodel->get_assignees_ext($poasmodel->get_poasassignment()->id);
        $datagroups = $this->get_groups($assignees);

        if ($this->attemptid) {
            $attempt = $DB->get_record('poasassignment_attempts', array('id' => $this->attemptid));
            $this->assigneeid = $attempt->assigneeid;
        }
        if ($this->assigneeid) {
            require_once(dirname(dirname(__FILE__)) . '/grader/remote_autotester/remote_autotester.php');
            $attemptsresult = remote_autotester::get_attempts_results($this->assigneeid);
        }
        // If user group id is set, get all users, then assignees of that group
        if ($this->groupid) {
            $users = $poasmodel->get_users_by_groups(array($this->groupid));
            $usersids = array();
            foreach ($users as $user) {
                $usersids[] = $user->userid;
            }
            $assignees = $poasmodel->get_assignees_ext($poasmodel->get_poasassignment()->id, $usersids);
            foreach ($assignees as $assignee) {
                $dataassignees[$assignee->id] = $assignee->lastname . ' ' . $assignee->firstname;
            }
        }

        $mform = new attempt_choose_ext_form(null,
            array(
                'groups' => $datagroups,
                'assignees' => $dataassignees,
                'attempts' => $dataattempts,
                'id' => $this->id),
            'get');
        $mform->set_data(array('groupid' => $this->groupid));
        $mform->display();
        if ($attemptsresult) {
            $this->show_attempts_result($attemptsresult);
        }
    }

    function get_groups($assignees) {
        $datagroups = array();
        $poasmodel = poasassignment_model::get_instance();
        $userids = array();
        foreach ($assignees as $assignee) {
            $userids[] = $assignee->userid;
        }

        // Divide assignees by groups, create array of used groups
        $groups = $poasmodel->get_users_groups($userids);

        $wogroup = new stdClass();
        $wogroup->name = get_string('wogroup', 'poasassignment');
        $wogroup->id = -2;

        $nogroup = new stdClass();
        $nogroup->name = '-';
        $nogroup->id = 0;

        array_unshift($groups, $wogroup);
        array_unshift($groups, $nogroup);
        foreach ($groups as $group) {
            $datagroups[$group->id] = $group->name;
        }
        return $datagroups;
    }

    private function show_attempts_result($attemptsresult)
    {
        global $PAGE;
        $PAGE->requires->js('/mod/poasassignment/grader/remote_autotester/jquery-1.9.1.min.js');
        $PAGE->requires->js('/mod/poasassignment/grader/remote_autotester/testresults.js');
        ?>
        <div class="testresults">
            <form action="">
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
        <tr class="<?=$class?> attemptinfo" data-attempt="<?=$i?>">
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
                        <img src="/mod/poasassignment/pix/yes.png" alt=""/> <span>Пройден</span>
                    <? else: ?>
                        <img src="/mod/poasassignment/pix/no.png" alt=""/> <span>Провален</span>
                    <? endif?>
                </span>
            </div>
            <div class="testservice">
                <?if (TRUE): ?>
                    <span class="showinput"><a href="javascript:void(0)">[показать входные данные]</a></span>
                    <span class="hideinput"><a href="javascript:void(0)">[скрыть входные данные]</a></span>
                <? endif?>
            </div>
            <? if (TRUE): // TODO Проверка капабилити ?>
                <div class="input">
                    <div class="caption">Входные данные:</div>
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
                        $diff = $this->diff($strudentoutarray, $testoutarray);;
                    }
                }
                else {
                    $diff = TRUE;
                }

            ?>
            <? if ($diff): // TODO?>
                <div class="diff">
                    <div class="caption">Разница в ответах:</div>
                    <?if (is_array($diff)): ?>
                        <? foreach ($diff as $element): ?>
                            <? if (is_array($element)): ?>
                                <? foreach ($element["i"] as $sub): ?>
                                    <pre class="test"><span>[t]:</span> <?=$sub?></pre>
                                <? endforeach ?>
                                <? foreach ($element["d"] as $sub): ?>
                                    <pre class="student"><span>[p]:</span> <?=$sub?></pre>
                                <? endforeach ?>
                            <? else: ?>
                                <pre class="same"><span>[s]:</span> <?=$element?></pre>
                            <? endif ?>
                        <? endforeach ?>
                    <? else: ?>
                        <div class="same">Ответы совпадают</div>
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
}
class attempt_choose_ext_form extends moodleform {
    function definition() {
        global $DB;
        $mform = $this->_form;
        $instance = $this->_customdata;
        $mform->addElement('select', 'groupid', get_string('group', 'poasassignment'), $instance['groups']);

        $mform->addElement('select', 'assigneeid', get_string('assignee', 'poasassignment'), $instance['assignees']);

        $mform->addElement('select', 'attemptid', get_string('attempt', 'poasassignment'), $instance['attempts']);

        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'page', 'testresults');
        $mform->setType('page', PARAM_ALPHA);

        $mform->addElement('submit', null, get_string('show'));
    }
}