<?php
global $CFG;
require_once('abstract_page.php');
require_once($CFG->libdir . '\tablelib.php');
require_once(dirname(dirname(__FILE__)) . '\model.php');
require_once($CFG->libdir.'/formslib.php');
class criterions_page extends abstract_page {
    private $mform;
    function criterions_page() {        
    }
    
    /*function get_cap() {
        return 'mod/poasassignment:managecriterions';
    }
	*/
    public function pre_view() {        
        $poasmodel = poasassignment_model::get_instance();
        $id = $poasmodel->get_cm()->id;
        $this->mform = new criterionsedit_form(null, array('id' => $id, 'poasassignmentid' => $poasmodel->get_poasassignment()->id));
        if($this->mform->get_data()) {
                $data = $this->mform->get_data();
                $poasmodel->save_criterion($data);
                redirect(new moodle_url('view.php', array('id' => $id, 'page' => 'criterions')), null, 0);
        }
    }
    function view() {
        global $DB, $OUTPUT;
        $poasmodel = poasassignment_model::get_instance();
        $id = $poasmodel->get_cm()->id;
		$context = $poasmodel->get_context();
        //$mform = new criterionsedit_form(null, array('id' => $id, 'poasassignmentid' => $poasmodel->get_poasassignment()->id));
		if (has_capability('mod/poasassignment:managecriterions', $context)) {
			$this->mform->set_data($poasmodel->get_criterions_data());
			//$this->mform->set_data(array('id' => $id));        
			$this->mform->display();
		}
		else {
			$this->show_read_only_criterions();
		}
        
    }
	private function show_read_only_criterions() {
		global $OUTPUT, $DB;
		$poasmodel = poasassignment_model::get_instance();
		$criterions = $DB->get_records('poasassignment_criterions', array('poasassignmentid' =>$poasmodel->poasassignment->id));
		$weightsum = 0;
		foreach($criterions as $criterion) {
            $weightsum += $criterion->weight;
		}
		$canseedescription = has_capability('mod/poasassignment:seecriteriondescription', 
											$poasmodel->get_context());
		foreach ($criterions as $criterion) {
			echo '<table class="poasassignment-table">';
			
			echo '<tr>';
			echo '<td class = "header">' . get_string('criterionname', 'poasassignment') . '</td>';
			echo '<td>' . $criterion->name . '</td>';
			echo '</tr>';
			if ($canseedescription) {
				echo '<tr>';
				echo '<td class = "header">' . get_string('criteriondescription', 'poasassignment') . '</td>';
				echo '<td>' . $criterion->description . '</td>';
				echo '</tr>';
			}
			
			echo '<tr>';
			echo '<td class = "header">' . get_string('criterionweight', 'poasassignment') . '</td>';
			echo '<td>' . round($criterion->weight / $weightsum, 2) . '</td>';
			echo '</tr>';
			
			echo '</table>';
			echo '<br>';
		}
	}
}
class criterionsedit_form extends moodleform {
    function definition(){
        global $DB;
        $mform = $this->_form;
        $instance = $this->_customdata;
        
        $repeatarray = array();
        $repeatarray[] = &MoodleQuickForm::createElement('header', 'criterionheader');
        $repeatarray[] = &MoodleQuickForm::createElement('text', 'name', get_string('criterionname','poasassignment'),array('size'=>45));
        $repeatarray[] = $mform->createElement('textarea', 'description', get_string('criteriondescription','poasassignment'));
        $repeatarray[] = &MoodleQuickForm::createElement('text', 'weight', get_string('criterionweight','poasassignment'));
        $sources[0] = 'manually';
        //TODO cash used graders in model class
        $usedgraders = $DB->get_records('poasassignment_used_graders',array('poasassignmentid' => $instance['poasassignmentid']));
        foreach($usedgraders as $usedgraderrecord) {
            $grader = $DB->get_record('poasassignment_graders',array('id' => $usedgraderrecord->graderid));
            $gradername = $grader->name;
            require_once($grader->path);
            $sources[$usedgraderrecord->graderid] = $gradername::name();
            
            // adding graders identificators - hidden elements to form
            
            $mform->addElement('hidden', 'grader' . (count($sources) - 1), $usedgraderrecord->graderid);
            $mform->setType('grader' . (count($sources) - 1), PARAM_INT);
        }
        $repeatarray[] = &MoodleQuickForm::createElement('select', 'source', get_string('criterionsource','poasassignment'),$sources);
        
        if ($instance){
            $repeatno = $DB->count_records('poasassignment_criterions', array('poasassignmentid'=>$instance['poasassignmentid']));
            $repeatno += 1;
        } else {
            $repeatno = 2;
        }
        
        $repeateloptions = array();

        $repeateloptions['name']['helpbutton'] = array('criterionname', 'poasassignment');
        $repeateloptions['description']['helpbutton'] = array('criteriondescription', 'poasassignment');
        $repeateloptions['weight']['helpbutton'] = array('criterionweight', 'poasassignment');
        $repeateloptions['source']['helpbutton'] = array('criterionsource', 'poasassignment');
        
        //$repeateloptions['weight']['default'] = 1.00;
        
        
        $this->repeat_elements($repeatarray, $repeatno,
                    $repeateloptions, 'option_repeats', 'option_add_fields', 2);
        
        $mform->addElement('hidden', 'id', $instance['id']);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'page', 'criterions');
        $mform->setType('page', PARAM_TEXT);
        
        $this->add_action_buttons(false, get_string('savechanges', 'admin'));
    }
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $i = 0;
        while (!empty($data['name'][$i] )) {
            if(!isset($data['name'][$i])) {
                $errors["name[$i]"] = get_string('errornoname', 'poasassignment');
            }
            if(!isset($data['weight'][$i])) {
                $errors["weight[$i]"] = get_string('errornoweight', 'poasassignment');
            }
            if($data['weight'][$i] <= 0) {
                $errors["weight[$i]"] = get_string('errornotpositiveweight', 'poasassignment');
            }
            $i++;
        }
        if(count($errors) > 0) {
            return $errors;
        }
        else {
            return true;
        }
    }
}
