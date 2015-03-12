<?php
global $CFG;
require_once('abstract_tab.php');
require_once($CFG->libdir.'/tablelib.php');
require_once('model.php');
class tasksfields_tab extends abstract_tab {
    var $poasassignment;
    
    function tasksfields_tab($cm,$poasassignment) {
        $this->poasassignment = $poasassignment;
        $this->cm=$cm;
    }
    function get_cap() {
        return 'mod/poasassignment:managetasksfields';
    }
    
    function has_satisfying_parameters() {
        $flag = $this->poasassignment->flags&ACTIVATE_INDIVIDUAL_TASKS;
        if(!$flag)
            return false;
        return true;
    }
    
    function get_error_satisfying_parameters() {
        $flag=$this->poasassignment->flags&ACTIVATE_INDIVIDUAL_TASKS;
        if(!$flag)
            return 'errorindtaskmodeisdisabled';
            //return get_string('errorindtaskmodeisdisabled','poasassignment');
    }
    
    function view() {
        global $DB,$OUTPUT;
        $poasmodel = poasassignment_model::get_instance();
        $table = new flexible_table('mod-poasassignment-tasksfields');
        
        $columns=array('name','ftype','showintable','searchparameter','secretfield','random','range');
        $headers=array(get_string('taskfieldname','poasassignment'),
                get_string('ftype','poasassignment'),
                get_string('showintable','poasassignment'),
                get_string('searchparameter','poasassignment'),
                get_string('secretfield','poasassignment'),
                get_string('random','poasassignment'),
                //get_string('maxvalue','poasassignment'),
                //get_string('minvalue','poasassignment'),
                //get_string('variants','poasassignment'));
                get_string('range','poasassignment'));
        $table->define_columns($columns);
        $table->define_headers($headers);
        //$table->sortable(true, 'name');
        $table->collapsible(true);
        $table->initialbars(true);
        $table->column_class('taskfieldname', 'name');
        $table->set_attribute('class', 'tasksfields');
        $table->set_attribute('border', '1');
        $table->set_attribute('width', '100%');
        
        $table->setup();
        
        $fields = $DB->get_records('poasassignment_fields',array('poasassignmentid'=>$this->poasassignment->id));
        foreach($fields as $field) {
        
            $updateurl = new moodle_url('/mod/poasassignment/tasksfieldsedit.php',array('id'=>$this->cm->id,'fieldid'=>$field->id,'mode'=>EDIT_MODE),'u','get');
            $deleteurl = new moodle_url('/mod/poasassignment/warning.php',array('id'=>$this->cm->id,'fieldid'=>$field->id,'action'=>'deletefield'),'d','get');
            $updateicon = '<a href="'.$updateurl.'">'.'<img src="'.$OUTPUT->pix_url('t/edit').
                            '" class="iconsmall" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
            $deleteicon = '<a href="'.$deleteurl.'">'.'<img src="'.$OUTPUT->pix_url('t/delete').
                            '" class="iconsmall" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>';
                            

            $name = $field->name.' '.$updateicon.' '.$deleteicon.' '.$poasmodel->help_icon($field->description);
            
            // $variants=$DB->get_records('poasassignment_variants',array('fieldid'=>$field->id),'sortorder','value');

            // $str='';
            // foreach ($variants as $variant) $str.=$variant->value."<br>";
            $range='';
            if($field->ftype==NUMBER || $field->ftype==FLOATING)
                $range='['.$field->minvalue.','.$field->maxvalue.']';
            if($field->ftype==MULTILIST || $field->ftype==LISTOFELEMENTS)
                $range=$poasmodel->get_field_variants($field->id,0,"<br>");
            
            $row = array($name,
                    $poasmodel->ftypes[$field->ftype],
                    $field->showintable,
                    $field->searchparameter,
                    $field->secretfield,
                    $field->random,
                    $range);
            $table->add_data($row);
        }
        

        $table->print_html();
        $id = $this->cm->id;
        echo $OUTPUT->single_button(new moodle_url('/mod/poasassignment/tasksfieldsedit.php?id='.$id.'?mode='.ADD_MODE), get_string('addbuttontext','poasassignment'));
    }
}