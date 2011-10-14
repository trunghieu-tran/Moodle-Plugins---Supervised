<?php
global $CFG;
require_once('abstract_page.php');
require_once(dirname(dirname(__FILE__)) . '\model.php');
require_once($CFG->libdir.'/tablelib.php');
class tasksfields_page extends abstract_page {
    var $poasassignment;
    
    function tasksfields_page($cm,$poasassignment) {
        $this->poasassignment = $poasassignment;
        $this->cm=$cm;
    }
    function get_cap() {
        return 'mod/poasassignment:managetasksfields';
    }
    
    function has_satisfying_parameters() {
        $flag = $this->poasassignment->flags & ACTIVATE_INDIVIDUAL_TASKS;
        if (!$flag) {
            $this->lasterror = 'errorindtaskmodeisdisabled';
            return false;
        }
        return true;
    }
        
    function view() {
        $id = $this->cm->id;
		$this->view_controls($id);
		
		echo '<div id="poasassignment-taskfields">';
        $count = $this->view_table();
		echo '</div>';
		if ($count > 4) {
			$this->view_controls($id);
		}
        
    }
    private function view_controls($id) {		
        global $OUTPUT;
		echo '<div align="center">';
        echo $OUTPUT->single_button(new moodle_url('view.php', array('id' => $id, 'page' => 'taskfieldedit')), 
                                    get_string('addtaskfield','poasassignment'));
        /*echo $OUTPUT->single_button(new moodle_url('view.php', array('id' => $id, 'page' => 'categoryedit')), 
                                    get_string('addcategoryfield','poasassignment'));*/
        echo '</div>';
	}
    private function view_table() {   
        global $DB, $OUTPUT, $PAGE;
        $poasmodel = poasassignment_model::get_instance();
        $table = new flexible_table('mod-poasassignment-tasksfields');
        $table->baseurl = $PAGE->url;
        $columns=array('name','ftype','showintable','secretfield','random','range');
        $headers=array(get_string('taskfieldname','poasassignment'),
                get_string('ftype','poasassignment'),
                get_string('showintable','poasassignment'),
                get_string('secretfield','poasassignment'),
                get_string('random','poasassignment'),
                get_string('range','poasassignment'));
        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->collapsible(true);
        $table->initialbars(true);
        $table->column_class('taskfieldname', 'name');
        $table->set_attribute('class', 'poasassignment-table');
        //$table->set_attribute('border', '1');
        //$table->set_attribute('width', '100%');
        
		$table->sortable(true, 'lastname');
		$table->no_sorting('range');
        $table->setup();
		
        if ($table->get_sql_sort()) {
			$sort = $table->get_sql_sort();
        }
		else {			
			$sort = '';
		}
        $fields = $DB->get_records('poasassignment_fields', array('poasassignmentid'=>$this->poasassignment->id), $sort);
        foreach($fields as $field) {
        
            $updateurl = new moodle_url('view.php',
                                        array('id' => $this->cm->id,
                                              'fieldid' => $field->id,
                                              'page' => 'taskfieldedit'), 
                                        'u',
                                        'get');
            $deleteurl = new moodle_url('/mod/poasassignment/warning.php',
                                        array('id' => $this->cm->id,
                                              'fieldid' => $field->id,
                                              'action' => 'deletefield'),
                                        'd',
                                        'get');
            $updateicon = '<a href="' . $updateurl . '">' . '<img src="' . 
                          $OUTPUT->pix_url('t/edit') . '" class="iconsmall" alt="' .
                          get_string('edit') . '" title="' . get_string('edit') .'" /></a>';
            $deleteicon = '<a href="'.$deleteurl.'">'.'<img src="'.$OUTPUT->pix_url('t/delete').
                            '" class="iconsmall" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>';
                            

            $name = $field->name.' '.$updateicon.' '.$deleteicon.' '.$poasmodel->help_icon($field->description);
            
            // $variants=$DB->get_records('poasassignment_variants',array('fieldid'=>$field->id),'sortorder','value');

            // $str='';
            // foreach ($variants as $variant) $str.=$variant->value."<br>";
            $range='';
            if($field->ftype==NUMBER || $field->ftype==FLOATING)
                $range='['.$field->valuemin.','.$field->valuemax.']';
            if($field->ftype==MULTILIST || $field->ftype==LISTOFELEMENTS)
                $range=$poasmodel->get_field_variants($field->id,0,"<br>");
            
            $row = array($name,
                    $poasmodel->ftypes[$field->ftype],
                    $field->showintable == 1 ? get_string('yes') : get_string('no'),
                    $field->secretfield == 1 ? get_string('yes') : get_string('no'),
                    $field->random == 1 ? get_string('yes') : get_string('no'),
                    $range);
            $table->add_data($row);
        }       		
        $table->print_html();
		return count($fields);
    }
}