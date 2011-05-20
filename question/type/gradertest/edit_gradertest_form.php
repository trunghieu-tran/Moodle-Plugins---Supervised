<?php
/**
 * The editing form code for this question type.
 *
 * @copyright &copy; 2006 YOURNAME
 * @author YOUREMAILADDRESS
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package YOURPACKAGENAME
 *//** */

require_once($CFG->dirroot.'/question/type/edit_question_form.php');
/**
 * grader editing form definition.
 * 
 * See http://docs.moodle.org/en/Development:lib/formslib.php for information
 * about the Moodle forms library, which is based on the HTML Quickform PEAR library.
 */
class question_edit_gradertest_form extends question_edit_form {
    function definition_inner(&$mform) {
        
        $mform->removeElement('questiontext');
        
        $mform->removeElement('defaultgrade');
        $mform->removeElement('penalty');
        $mform->removeElement('generalfeedback');
        if (!empty($CFG->usetags)) {
            $mform->removeElement('tagsheader');
            $mform->removeElement('tags');
        }
        $repeatarray = array();
        $label = '123';
        $repeatarray[] = MoodleQuickForm::createElement('header');
        $repeatarray[] = MoodleQuickForm::createElement('text', 
                                                        'testname', 
                                                        get_string('testname', 'qtype_gradertest'));
                                                        
        $repeatarray[] = MoodleQuickForm::createElement('textarea', 
                                                        'testin', 
                                                        get_string('testin', 'qtype_gradertest'), 
                                                        array('cols'=>'40', 'rows'=>'5'));
        $repeatarray[] = MoodleQuickForm::createElement('textarea', 
                                                        'testout', 
                                                        get_string('testout', 'qtype_gradertest'), 
                                                        array('cols'=>'40', 'rows'=>'5'));
        
        $repeatarray[] = MoodleQuickForm::createElement('text', 
                                                        'testweight', 
                                                        get_string('testweight', 'qtype_gradertest'));
        $repeateoptions = array();
        $repeatno = 2;
        
        $this->repeat_elements($repeatarray, 
                               $repeatno,
                               $repeateoptions, 
                               'option_repeats', 
                               'option_add_fields', 
                               2);
                                     
    }
    /**
     *    Accessor for an attribute.
     *    @param string $label    Attribute name.
     *    @return string          Attribute value.
     *    @access public
     */
    function getAttribute($label) {
        $label = strtolower($label);
        if (! isset($this->_attributes[$label])) {
            return false;
        }
        return (string)$this->_attributes[$label];
    }
    function set_data($question) {
        global $CFG, $DB;
        //$draftitemid = file_get_submitted_draft_itemid('testfiles');
        //file_prepare_draft_area($draftitemid, 
        //                        $this->context->id, 
        //                        'question', 
        //                        'questiontext', 
        //                        ! empty($question->id) ? $question -> id : 30, 
        //                        array('subdirs'=>true));
        //$default_values['testfiles'] = $draftitemid;
        //$question->testfiles = $draftitemid;
        ////print_r($question->testfiles);
        //$fs = get_file_storage();
        //$files = $fs->get_area_files($this->context->id,'question', 
        //                        'questiontext', 
        //                        ! empty($question->id) ? $question -> id : 30);
        //foreach($files as $file) {
        //    if (!$file->is_directory()) {
        //        $url = new moodle_url('/draftfile.php/'.($this->context->id - 1).'/user/draft/'.$draftitemid.'/'.$file->get_filename());
        //        //echo '<a href="'.$url.'">file</a><br>';
        //        $file->copy_content_to("$CFG->dataroot\\temp\\".$file->get_filename());
        //    }
        //}
        
        parent::set_data($question);
    }

    function validation($data) {
        $errors = array();

        // TODO, do extra validation on the data that came back from the form. E.g.
        // if (/* Some test on $data['customfield']*/) {
        //     $errors['customfield'] = get_string( ... );
        // }

        if ($errors) {
            return $errors;
        } else {
            return true;
        }
    }

    function qtype() {
        return 'gradertest';
    }
}
?>