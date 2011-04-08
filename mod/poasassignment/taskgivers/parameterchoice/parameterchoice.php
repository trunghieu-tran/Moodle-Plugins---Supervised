<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of parameterchoice
 *
 * @author Arkanif
 */
global $CFG;
require_once dirname(dirname(__FILE__)).'\taskgiver.php';
require_once($CFG->libdir.'/formslib.php');
class parameterchoice extends taskgiver{

    public $showtasks = true;

    function parameter_search($cmid, $poasassignment) {
        global $DB,$USER;
        $poasmodel = poasassignment_model::get_instance($poasassignment);
        $mform=new parametersearch_form(null,array('poasassignmentid'=>$poasassignment->id,'id'=>$cmid));
        if($data=$mform->get_data()) {
            $tasks=$DB->get_records('poasassignment_tasks',array('poasassignmentid'=>$poasassignment->id));
            $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$poasassignment->id,'searchparameter'=>1));
            if($fields) {
                $satisfyingtasks=array();
                foreach($fields as $field) {
                    $fieldelementname='field'.$field->id;
                    $fieldvalues=$DB->get_records('poasassignment_task_values',
                                    array('fieldid'=>$field->id));
                    if($fieldvalues) {
                        if($field->ftype==LISTOFELEMENTS || $field->ftype==MULTILIST || $field->ftype==STR || $field->ftype==TEXT) {
                            foreach($fieldvalues as $fieldvalue) {
                                if($tasks[$fieldvalue->taskid]->hidden==0) {
                                    $contains=strpos($fieldvalue->value,$data->$fieldelementname);
                                    if($contains!==false) {
                                        for($i=0;$i<5;$i++)
                                            $satisfyingtasks[]=$fieldvalue->taskid;
                                    }
                                }
                            }
                        }
                        if($field->ftype==NUMBER || $field->ftype==FLOATING || $field->ftype==DATE) {
                            foreach($fieldvalues as $fieldvalue) {
                                if($tasks[$fieldvalue->taskid]->hidden==0) {
                                    if($data->$fieldelementname==$fieldvalue->value)
                                        for($i=0;$i<5;$i++)
                                            $satisfyingtasks[]=$fieldvalue->taskid;
                                    else
                                        if($data->$fieldelementname==0)
                                            continue;
                                        else
                                            for($dif=1;$dif<5;$dif++)
                                                if(abs($data->$fieldelementname-$fieldvalue->value)/$data->$fieldelementname<(0.1*$dif)) {
                                                    for($i=0;$i<5-$dif;$i++)
                                                        $satisfyingtasks[]=$fieldvalue->taskid;
                                                    break;
                                                }
                                }
                            }
                        }
                    }
                }

                //echo implode($satisfyingtasks).'<br>';
                if($satisfyingtasks) {
                    $taskid=$satisfyingtasks[0];
                    $tasktimesmet=1;
                    $tmp=0;
                    for($i=0;$i<count($satisfyingtasks);$i++) {
                        for($j=0;$j<count($satisfyingtasks);$j++) {
                            if($satisfyingtasks[$i]==$satisfyingtasks[$j])
                                $tmp++;
                            if($tmp>$tasktimesmet) {
                                $taskid=$satisfyingtasks[$i];
                                $tasktimesmet=$tmp;
                            }
                        }
                         $tasktimesmet=$tmp;
                         $tmp=0;
                    }
                    //echo 'task with id'.$taskid.' was met '.$tasktimesmet.' times';
                    $poasmodel->bind_task_to_assignee($USER->id,$taskid);
                    redirect(new moodle_url('view.php',array('id'=>$cmid,'page'=>'view')),null,0);
                }
                else echo get_string('nosatisfyingtasks','poasassignment');
            }
        }
        return $mform;
    }
    
    function process_after_tasks($cmid, $poasassignment) {
        $mform = $this->parameter_search($cmid, $poasassignment);
        $mform->display();
    }
    //put your code here
}

class parametersearch_form extends moodleform {
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        $poasmodel= poasassignment_model::get_instance();
        global $DB;
        $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$instance['poasassignmentid'],'searchparameter'=>1));
        if($fields) {
            $mform->addElement('header','header',get_string('inputparameters','poasassignment'));
            $poasmodel= poasassignment_model::get_instance();
            foreach($fields as $field) {
                if($field->ftype!=MULTILIST && $field->ftype!=LISTOFELEMENTS)
                    if($field->ftype==DATE)
                        $mform->addElement('date_selector','field'.$field->id,$field->name);
                    else {
                        if(has_capability('mod/poasassignment:seefielddescription',get_context_instance(CONTEXT_MODULE,$instance['id'])))
                            $mform->addElement('text','field'.$field->id,$field->name.'('.$poasmodel->ftypes[$field->ftype].')'.$poasmodel->help_icon($field->description));
                        else
                            $mform->addElement('text','field'.$field->id,$field->name.'('.$poasmodel->ftypes[$field->ftype].')');
                    }
                else {
                    $opt=$poasmodel->get_field_variants($field->id);
                    $mform->addElement('select','field'.$field->id,$field->name,$opt);
                }
            }

            $mform->addElement('hidden', 'id', $instance['id']);
            $mform->setType('id', PARAM_INT);

            $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
            $mform->setType('poasassignmentid', PARAM_INT);

            $mform->addElement('hidden', 'page', 'tasks');
            $mform->setType('page', PARAM_TEXT);

            $this->add_action_buttons(false, get_string('getrandomtask', 'poasassignment'));
        }
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        global $DB;
        $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$data['poasassignmentid'],'searchparameter'=>1));
        foreach($fields as $field) {
            if(($field->ftype==FLOATING || $field->ftype==NUMBER ) && !is_numeric($data['field'.$field->id])) {
                if(strlen($data['field'.$field->id])>0) {
                    $errors['field'.$field->id]=get_string('errormustbefloat','poasassignment');
                    return $errors;
                }
            }
            /* if($field->ftype==NUMBER && !is_int($data['field'.$field->id])) {
                if(strlen($data['field'.$field->id])>0) {
                    $errors['field'.$field->id]=get_string('errormustbeint','poasassignment');
                    return $errors;
                }
            } */

        }

        return true;
    }
}
?>
