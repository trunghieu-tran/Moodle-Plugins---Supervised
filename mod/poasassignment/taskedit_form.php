<?php

require_once($CFG->libdir.'/formslib.php');
require_once('model.php');

class taskedit_form extends moodleform {

    function definition(){
        global $DB;
        $mform = $this->_form;
        $instance = $this->_customdata;
        if($instance['taskid']>0)
            $mform->addElement('header','taskeditheader',get_string('taskeditheader','poasassignment'));
        else
            $mform->addElement('header','taskaddheader',get_string('taskaddheader','poasassignment'));
        
        $mform->addElement('text','name',get_string('taskname','poasassignment'),array('size'=>45));
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addElement('htmleditor','description',get_string('taskintro', 'poasassignment'));
        $mform->addElement('checkbox','hidden',get_string('taskhidden', 'poasassignment'));
        $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$instance['poasassignmentid']));
        $poasmodel= poasassignment_model::get_instance();
        foreach($fields as $field) {
            $name = $field->name.' '.$poasmodel->help_icon($field->description);
            if($field->ftype==STR)
                $mform->addElement('text','field'.$field->id,$name,array('size'=>45));
                
            if($field->ftype==TEXT)
                $mform->addElement('htmleditor','field'.$field->id,$name);
                
            if( ($field->ftype==FLOATING || $field->ftype==NUMBER) && $field->random) {
                $mform->addElement('static','field'.$field->id,$name,'random field');
            }
            
            if( ($field->ftype==FLOATING || $field->ftype==NUMBER) && !$field->random) {
                $mform->addElement('text','field'.$field->id,$name,array('size'=>10));
            }

            if($field->ftype==DATE) {
                $mform->addElement('date_selector','field'.$field->id,$name);
            }
            
            if($field->ftype==FILE) {
                $mform->addElement('filemanager','field'.$field->id,$name);
            }
            if($field->ftype==LISTOFELEMENTS || $field->ftype==MULTILIST) {
                if($field->random==0) {
                    /* $tok = strtok($field->variants,"\n");
                    while($tok) {
                        $opt[]=$tok;
                        $tok=strtok("\n");
                    } */
                    $opt=$poasmodel->get_field_variants($field->id);
                    $select=&$mform->addElement('select','field'.$field->id,$name,$opt);
                    if($field->ftype==MULTILIST)
                        $select->setMultiple(true);
                }
                else
                    $mform->addElement('static','field'.$field->id,$name,'random field');
            }
        }
        
        // hidden params
        $mform->addElement('hidden', 'taskid', $instance['taskid']);
        $mform->setType('taskid', PARAM_INT);
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'mode', $instance['mode']);
        $mform->setType('mode', PARAM_INT);
        
        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);
        
        
        $this->add_action_buttons(true, get_string('savechanges', 'admin'));
    }
    
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        global $DB;
        $fields=$DB->get_records('poasassignment_fields',array('poasassignmentid'=>$data['poasassignmentid']));
        foreach($fields as $field) {
            if(!$field->random &&($field->ftype==FLOATING || $field->ftype==NUMBER)) {
                if(!($field->minvalue==0 && $field->maxvalue==0 )) {
                    if($data['field'.$field->id]>$field->maxvalue || $data['field'.$field->id]<$field->minvalue) {
                    $errors['field'.$field->id]=get_string('valuemustbe','poasassignment').' '.
                                                get_string('morethen','poasassignment').' '.
                                                $field->minvalue.' '.
                                                get_string('and','poasassignment').' '.
                                                get_string('lessthen','poasassignment').' '.
                                                $field->maxvalue;
                    return $errors;
                    }
                }
            }
            if($field->ftype==MULTILIST && !isset($data['field'.$field->id])) {
                $errors['field'.$field->id]=get_string('errornovariants','poasassignment');
                return $errors;
            }
            // if($field->ftype==FLOATING && !is_numeric($data['field'.$field->id])) {
                // $errors['field'.$field->id]=get_string('errormustbefloat','poasassignment');
                // return $errors;
            // }
            // if($field->ftype==NUMBER && !is_int($data['field'.$field->id])) {
                // $errors['field'.$field->id]=get_string('errormustbeint','poasassignment');
                // return $errors;
            // }
            
        }
       
        return true;
    }
}
