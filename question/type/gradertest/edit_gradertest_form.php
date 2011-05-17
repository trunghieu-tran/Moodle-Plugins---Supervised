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
    /* function definition() {
        global $COURSE, $CFG, $DB;

        $qtype = $this->qtype();
        $langfile = "qtype_$qtype";

        $mform =& $this->_form;

        // Standard fields at the start of the form.
        $mform->addElement('header', 'generalheader', get_string("general", 'form'));

        if (!isset($this->question->id)){
            // Adding question
            $mform->addElement('questioncategory', 'category', get_string('category', 'quiz'),
                    array('contexts' => $this->contexts->having_cap('moodle/question:add')));
        } elseif (!($this->question->formoptions->canmove || $this->question->formoptions->cansaveasnew)){
            // Editing question with no permission to move from category.
            $mform->addElement('questioncategory', 'category', get_string('category', 'quiz'),
                    array('contexts' => array($this->categorycontext)));
        } elseif ($this->question->formoptions->movecontext){
            // Moving question to another context.
            $mform->addElement('questioncategory', 'categorymoveto', get_string('category', 'quiz'),
                    array('contexts' => $this->contexts->having_cap('moodle/question:add')));

        } else {
            // Editing question with permission to move from category or save as new q
            $currentgrp = array();
            $currentgrp[0] =& $mform->createElement('questioncategory', 'category', get_string('categorycurrent', 'question'),
                    array('contexts' => array($this->categorycontext)));
            if ($this->question->formoptions->canedit || $this->question->formoptions->cansaveasnew){
                //not move only form
                $currentgrp[1] =& $mform->createElement('checkbox', 'usecurrentcat', '', get_string('categorycurrentuse', 'question'));
                $mform->setDefault('usecurrentcat', 1);
            }
            $currentgrp[0]->freeze();
            $currentgrp[0]->setPersistantFreeze(false);
            $mform->addGroup($currentgrp, 'currentgrp', get_string('categorycurrent', 'question'), null, false);

            $mform->addElement('questioncategory', 'categorymoveto', get_string('categorymoveto', 'question'),
                    array('contexts' => array($this->categorycontext)));
            if ($this->question->formoptions->canedit || $this->question->formoptions->cansaveasnew){
                //not move only form
                $mform->disabledIf('categorymoveto', 'usecurrentcat', 'checked');
            }
        }

        $mform->addElement('text', 'name', get_string('questionname', 'quiz'), array('size' => 50));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        
        // Any questiontype specific fields.
        $this->definition_inner($mform);

        if (!empty($this->question->id)){
            $mform->addElement('header', 'createdmodifiedheader', get_string('createdmodifiedheader', 'question'));
            $a = new stdClass();
            if (!empty($this->question->createdby)){
                $a->time = userdate($this->question->timecreated);
                $a->user = fullname($DB->get_record('user', array('id' => $this->question->createdby)));
            } else {
                $a->time = get_string('unknown', 'question');
                $a->user = get_string('unknown', 'question');
            }
            $mform->addElement('static', 'created', get_string('created', 'question'), get_string('byandon', 'question', $a));
            if (!empty($this->question->modifiedby)){
                $a = new stdClass();
                $a->time = userdate($this->question->timemodified);
                $a->user = fullname($DB->get_record('user', array('id' => $this->question->modifiedby)));
                $mform->addElement('static', 'modified', get_string('modified', 'question'), get_string('byandon', 'question', $a));
            }
        }

        // Standard fields at the end of the form.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'qtype');
        $mform->setType('qtype', PARAM_ALPHA);

        $mform->addElement('hidden', 'inpopup');
        $mform->setType('inpopup', PARAM_INT);

        $mform->addElement('hidden', 'versioning');
        $mform->setType('versioning', PARAM_BOOL);

        $mform->addElement('hidden', 'movecontext');
        $mform->setType('movecontext', PARAM_BOOL);

        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);
        $mform->setDefault('cmid', 0);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', 0);

        $mform->addElement('hidden', 'returnurl');
        $mform->setType('returnurl', PARAM_LOCALURL);
        $mform->setDefault('returnurl', 0);

        $mform->addElement('hidden', 'appendqnumstring');
        $mform->setType('appendqnumstring', PARAM_ALPHA);
        $mform->setDefault('appendqnumstring', 0);

        $buttonarray = array();
        if (!empty($this->question->id)){
            //editing / moving question
            if ($this->question->formoptions->movecontext){
                $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('moveq', 'question'));
            } elseif ($this->question->formoptions->canedit || $this->question->formoptions->canmove ||$this->question->formoptions->movecontext){
                $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
            }
            if ($this->question->formoptions->cansaveasnew){
                $buttonarray[] = &$mform->createElement('submit', 'makecopy', get_string('makecopy', 'quiz'));
            }
            $buttonarray[] = &$mform->createElement('cancel');
        } else {
            // adding new question
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));
            $buttonarray[] = &$mform->createElement('cancel');
        }
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');

        if ($this->question->formoptions->movecontext) {
            $mform->hardFreezeAllVisibleExcept(array('categorymoveto', 'buttonar'));
        } else if ((!empty($this->question->id)) && (!($this->question->formoptions->canedit || $this->question->formoptions->cansaveasnew))){
            $mform->hardFreezeAllVisibleExcept(array('categorymoveto', 'buttonar', 'currentgrp'));
        }
    }
     */function definition_inner(&$mform) {
        echo '<br>'.__FUNCTION__;
        
        $mform->removeElement('questiontext');
        
        $mform->removeElement('defaultgrade');
        $mform->removeElement('penalty');
        $mform->removeElement('generalfeedback');
        if (!empty($CFG->usetags)) {
            $mform->removeElement('tagsheader');
            $mform->removeElement('tags');
        }
        
        $mform->addElement('header', 'testheader', 'test');
        $mform->addElement('htmleditor', 'testtext', get_string('testtext', 'qtype_gradertest'), array('size'=>'64'));
        
        $filemanager_options = array();
        $filemanager_options['return_types'] = 3;
        $filemanager_options['accepted_types'] = '*';
        $filemanager_options['maxbytes'] = 0;
        $filemanager_options['maxfiles'] = -1;
        $filemanager_options['mainfile'] = true;
        $mform->addElement('filemanager', 'testfiles', get_string('testfiles', 'qtype_gradertest'), null, $filemanager_options);        
    }
    function set_data($question) {
        echo '<br>'.__FUNCTION__;
        $draftitemid = file_get_submitted_draft_itemid('testfiles');
        file_prepare_draft_area($draftitemid, 
                                $this->context->id, 
                                'question', 
                                'questiontext', 
                                ! empty($question->id) ? $question -> id : 30, 
                                array('subdirs'=>true));
        $default_values['testfiles'] = $draftitemid;
        $question->testfiles = $draftitemid;
        
        parent::set_data($question);
    }

    function validation($data) {
        echo '<br>'.__FUNCTION__;
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