<?php
global $CFG;
require_once('abstract_tab.php');
require_once($CFG->libdir.'/tablelib.php');
require_once('criterionsedit_form.php');
require_once('model.php');
class criterions_tab extends abstract_tab {
    var $poasassignment;
    
    function criterions_tab($cm,$poasassignment) {
        $this->poasassignment = $poasassignment;
        $this->cm=$cm;
    }
    
    function get_cap() {
        return 'mod/poasassignment:managecriterions';
    }
    
    function view() {
        global $DB,$OUTPUT;
        /* $table = new flexible_table('mod-poasassignment-criterions');
        
        $columns=array('name','description','weight','source');
        $headers=array(get_string('criterionname','poasassignment'),
                get_string('criteriondescription','poasassignment'),
                get_string('criterionweight','poasassignment'),
                get_string('criterionsource','poasassignment'));
        $table->define_columns($columns);
        $table->define_headers($headers);
        //$table->sortable(true, 'name');
        $table->collapsible(true);
        $table->initialbars(true);
        $table->set_attribute('border', '1');
        $table->set_attribute('width', '100%');
        
        $table->setup();
        
        $criterions = $DB->get_records('poasassignment_criterions',array('poasassignmentid'=>$this->poasassignment->id));
        foreach($criterions as $criterion) {
            $updateurl = new moodle_url('criterionsedit.php',array('id'=>$this->cm->id,'criterionid'=>$criterion->id,'mode'=>EDIT_MODE),'u','get');
            $deleteurl = new moodle_url('criterionsedit.php',array('id'=>$this->cm->id,'criterionid'=>$criterion->id,'mode'=>DELETE_MODE),'d','get');
            $updateicon = '<a href="'.$updateurl.'">'.'<img src="'.$OUTPUT->pix_url('t/edit').
                            '" class="iconsmall" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
            $deleteicon = '<a href="'.$deleteurl.'">'.'<img src="'.$OUTPUT->pix_url('t/delete').
                            '" class="iconsmall" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>';
            $name = $criterion->name.' '.$updateicon.' '.$deleteicon;
            if(!isset($criterion->sourceid))
                $criterion->sourceid=0;
            $row = array($name,shorten_text($criterion->description),$criterion->weight,$criterion->sourceid);
            $table->add_data($row);
        }
        

        $table->print_html();*/
        $id = $this->cm->id; 
        //echo $OUTPUT->single_button(new moodle_url('criterionsedit.php?id='.$id.'?mode='.ADD_MODE), get_string('addbuttontext','poasassignment'));
        $poasmodel = poasassignment_model::get_instance($this->poasassignment);
        $mform = new criterionsedit_form(null,array('id'=>$id,'poasassignmentid'=>$this->poasassignment->id));
        if($mform->get_data()) {
                $data=$mform->get_data();    
                $poasmodel->save_criterion($data);
                redirect(new moodle_url('view.php',array('id'=>$id,'tab'=>'criterions')),null,0);
        }
        $mform->set_data($poasmodel->get_criterions_data());
        $mform->set_data(array('id'=>$id));
        $mform->display();
    }
}