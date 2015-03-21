<?php

require_once($CFG->libdir.'/formslib.php');
require_once('lib.php');

class tasksfieldsedit_form extends moodleform {
    function definition(){
        $mform = $this->_form;
        $instance = $this->_customdata;
        if($instance['fieldid']>0)
            $mform->addElement('header','taskfieldeditheader',get_string('taskfieldeditheader','poasassignment'));
        else
            $mform->addElement('header','taskfieldaddheader',get_string('taskfieldaddheader','poasassignment'));
        
        $mform->addElement('text','name',get_string('taskfieldname','poasassignment'),array('size'=>45));
        $mform->addElement('textarea','description',get_string('taskfielddescription','poasassignment'),'rows="5" cols="50"');
        $mform->addRule('name', null, 'required', null, 'client');        
        $ftypes = array(get_string('char','poasassignment'),
                        get_string('text','poasassignment'),
                        get_string('float','poasassignment'),
                        get_string('int','poasassignment'),
                        get_string('date','poasassignment'),
                        get_string('file','poasassignment'),
                        get_string('list','poasassignment'),
                        get_string('multilist','poasassignment'));
        $mform->addElement('select','ftype',get_string('ftype','poasassignment'),$ftypes);
        $mform->addElement('checkbox','showintable',get_string('showintable','poasassignment'));
        $mform->addElement('checkbox','searchparameter',get_string('searchparameter','poasassignment'));
        $mform->disabledIf('searchparameter','ftype','eq',FILE);
        $mform->disabledIf('searchparameter','random','checked');        
        $mform->addElement('checkbox','secretfield',get_string('secretfield','poasassignment'));
        $mform->addElement('checkbox','random',get_string('random','poasassignment'));
        $mform->disabledIf('random','searchparameter','checked');
        $mform->disabledIf('random','ftype','eq',STR);
        $mform->disabledIf('random','ftype','eq',TEXT);
        $mform->disabledIf('random','ftype','eq',DATE);
        $mform->disabledIf('random','ftype','eq',FILE);
        $mform->disabledIf('random','ftype','eq',MULTILIST);
        //$mform->disabledIf('random','minvalue','eq','maxvalue');
       
        
        $mform->addElement('text','minvalue',get_string('minvalue','poasassignment'),10);
        $mform->setDefault('minvalue', 0);
        $mform->disabledIf('minvalue','ftype','eq',STR);
        $mform->disabledIf('minvalue','ftype','eq',TEXT);
        $mform->disabledIf('minvalue','ftype','eq',DATE);
        $mform->disabledIf('minvalue','ftype','eq',FILE);
        $mform->disabledIf('minvalue','ftype','eq',LISTOFELEMENTS);
        $mform->disabledIf('minvalue','ftype','eq',MULTILIST);
        
        $mform->addElement('text','maxvalue',get_string('maxvalue','poasassignment'),10);
        $mform->setDefault('maxvalue', 100);
        $mform->disabledIf('maxvalue','ftype','eq',STR);
        $mform->disabledIf('maxvalue','ftype','eq',TEXT);
        $mform->disabledIf('maxvalue','ftype','eq',DATE);
        $mform->disabledIf('maxvalue','ftype','eq',FILE);
        $mform->disabledIf('maxvalue','ftype','eq',LISTOFELEMENTS);
        $mform->disabledIf('maxvalue','ftype','eq',MULTILIST);
        
        $mform->addElement('textarea','variants',get_string('variants','poasassignment'),'rows="10" cols="50"');
        $mform->addHelpButton('variants', 'variants', 'poasassignment');
        $mform->disabledIf('variants','ftype','eq',STR);
        $mform->disabledIf('variants','ftype','eq',TEXT);
        $mform->disabledIf('variants','ftype','eq',FLOATING);
        $mform->disabledIf('variants','ftype','eq',NUMBER);
        $mform->disabledIf('variants','ftype','eq',DATE);
        $mform->disabledIf('variants','ftype','eq',FILE);
        
        // hidden params
        $mform->addElement('hidden', 'fieldid', $instance['fieldid']);
        $mform->setType('fieldid', PARAM_INT);
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'mode', $instance['mode']);
        $mform->setType('mode', PARAM_INT);
        $mform->addElement('hidden', 'poasassignmentid', $instance['poasassignmentid']);
        $mform->setType('poasassignmentid', PARAM_INT);
        
        $this->add_action_buttons(true, get_string('savechanges', 'admin'));
    }
    
    function data_preprocessing(&$default_values){
        //if($instance['mode']==1) {
            echo 'start';
            if(1) { 
            echo 'start';
            $poasmodel = poasassignment_model::get_instance();
            $default_values['maxvalue']=100;
            $default_values=$poasmodel->set_default_values_taskfields($default_values,$instance['fieldid']);
        }
        //if ($this->current->instance) {
        
        //}
    }
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if($data['ftype']==LISTOFELEMENTS || $data['ftype']==MULTILIST) {
            $tok = strtok($data['variants'],"\n");
            $count=0;
            while($tok) {
                $count++;
                $tok=strtok("\n");
            }
            if($count<2) {
                $errors['variants'] = get_string('errorvariants', 'poasassignment');
                return $errors;
            }
        }
        if(isset($data['maxvalue']) && isset($data['minvalue'])) {
            if($data['maxvalue']<$data['minvalue']) {
                $errors['maxvalue'] = get_string('errormaxislessthenmin', 'poasassignment');
                return $errors;
            }
        }
        
        /* global $DB;
        $recexist=$DB->record_exists('poasassignment_fields',array('name'=>$data['name'],'poasassignmentid'=>$data['poasassignmentid']));
        $getrec=$DB->get_record('poasassignment_fields',array('name'=>$data['name'],'poasassignmentid'=>$data['poasassignmentid']));
        if(($data['mode']==ADD_MODE && $recexist) || ($data['mode']==EDIT_MODE && $getrec->id!=$data['fieldid'])) {
                $errors['name'] = get_string('errorfiledduplicatename','poasassignment');
                return $errors;
        } */
        return true;
    }
}
