<?php


define('AJAX_SCRIPT', true);

require_once("../../config.php");
require_once($CFG->dirroot."/question/editlib.php");
require_once($CFG->dirroot."/local/ajaxcategories/category_class.php");

$movingid = optional_param('movingid', 0, PARAM_INT);
$before = optional_param('before', 0, PARAM_INT);
$after = optional_param('after', 0, PARAM_INT);
$level = optional_param('level', '',PARAM_ALPHA);
$dest = optional_param('dest', 0, PARAM_INT);

    list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) =
        question_edit_setup('categories', '/local/ajaxcategories/index.php');

    // Get values from form for actions on this page.
    $param = new stdClass();

    $param->delete = optional_param('delete', 0, PARAM_INT);
    $param->edit = optional_param('edit', 0, PARAM_INT);

    $qcobject = new ajax_question_category_object($pagevars['cpage'], $thispageurl,
        $contexts->having_one_edit_tab_cap('categories'), $param->edit,
        $pagevars['cat'], $param->delete, $contexts->having_cap('moodle/question:add'));


    $fromlist = $qcobject->find_list($movingid);
    //var_dump($fromlist);
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