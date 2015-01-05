<?php


define('AJAX_SCRIPT', true);

require_once("../../config.php");
require_once($CFG->dirroot."/question/editlib.php");
require_once($CFG->dirroot."/local/ajaxcategories/category_class.php");

$movingid = $_REQUEST['movingid'];
$before = $_REQUEST['before'];
$after = $_REQUEST['after'];
$level = $_REQUEST['level'];
$dest = $_REQUEST['dest'];

    list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) =
        question_edit_setup('categories', '/local/ajaxcategories/index.php');

    // Get values from form for actions on this page.
    $param = new stdClass();
    $param->moveup = optional_param('moveup', 0, PARAM_INT);
    $param->movedown = optional_param('movedown', 0, PARAM_INT);
    $param->moveupcontext = optional_param('moveupcontext', 0, PARAM_INT);
    $param->movedowncontext = optional_param('movedowncontext', 0, PARAM_INT);
    $param->tocontext = optional_param('tocontext', 0, PARAM_INT);
    $param->left = optional_param('left', 0, PARAM_INT);
    $param->right = optional_param('right', 0, PARAM_INT);
    $param->delete = optional_param('delete', 0, PARAM_INT);
    $param->confirm = optional_param('confirm', 0, PARAM_INT);
    $param->cancel = optional_param('cancel', '', PARAM_ALPHA);
    $param->move = optional_param('move', 0, PARAM_INT);
    $param->moveto = optional_param('moveto', 0, PARAM_INT);
    $param->edit = optional_param('edit', 0, PARAM_INT);

    $qcobject = new ajax_question_category_object($pagevars['cpage'], $thispageurl,
        $contexts->having_one_edit_tab_cap('categories'), $param->edit,
        $pagevars['cat'], $param->delete, $contexts->having_cap('moodle/question:add'));

    $fromlist = $qcobject->find_list($movingid);
    $environment = array();
    $environment['before'] = $before;
    $environment['after'] = $after;
    $environment['level'] = $level;
    $environment['dest'] = $dest;
    if ($environment['after'] !== -1) {
        $tolist = $qcobject->find_list($environment['after']);
    } else {
        $tolist = $qcobject->find_list($environment['before']);
    }
    $environment['dest'] = $tolist;
    $fromlist->change_category_list($movingid, $environment);
