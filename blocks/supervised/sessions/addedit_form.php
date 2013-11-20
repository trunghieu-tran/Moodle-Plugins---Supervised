<?php

require_once("{$CFG->libdir}/formslib.php");
 
class addedit_session_form extends moodleform {
 
    function definition() {
        global $DB;

        $mform =& $this->_form;

        // TODO see report_log_print_selector_form function

        // TODO find only teachers
        if ($cteachers = $DB->get_records('user')) {
            foreach ($cteachers as $cteacher) {
                $teachers[$cteacher->id] = $cteacher->lastname . " " . $cteacher->firstname;
            }
        }

        // Find all courses.
        if ($ccc = $DB->get_records("course", null, "fullname", "id,shortname,fullname,category")) {
            foreach ($ccc as $cc) {
                if ($cc->category) {    // We don't add the main course (frontpage)
                    $courses["$cc->id"] = format_string(get_course_display_name_for_list($cc));     // TODO what is difference with course.name (or fullname)?
                }
            }
        }

        // Find all classrooms.
        if ($cclassrooms = $DB->get_records('block_supervised_classroom')) {
            foreach ($cclassrooms as $cclassroom) {
                $classrooms[$cclassroom->id] = $cclassroom->name;
            }
        }

        // Gets array of all groups in current course.
        /*$groups[0] = get_string('allgroups', 'block_supervised');
        if ($cgroups = groups_get_all_groups($this->_customdata['courseid'])) {
            foreach ($cgroups as $cgroup) {
                $groups[$cgroup->id] = $cgroup->name;
            }
        }

        // Find lessontypes in current course.
        $lessontypes[0] = get_string('notspecified', 'block_supervised');
        if ($clessontypes = $DB->get_records('block_supervised_lessontype', array('courseid'=>$this->_customdata['courseid']))) {
            foreach ($clessontypes as $clessontype) {
                $lessontypes[$clessontype->id] = $clessontype->name;
            }
        }*/

        // Gets array of groups in courses.
        foreach($courses as $id=>$coursename){
            $groups[$id][0] = get_string('allgroups', 'block_supervised');
            if ($cgroups = groups_get_all_groups($id)) {
                foreach ($cgroups as $cgroup) {
                    $groups[$id][$cgroup->id] = $cgroup->name;
                }
            }
        }

        // Gets array of lessontypes in courses.
        foreach($courses as $id=>$coursename){
            $lessontypes[$id][0] = get_string('notspecified', 'block_supervised');
            if ($clessontypes = $DB->get_records('block_supervised_lessontype', array('courseid'=>$id))) {
                foreach ($clessontypes as $clessontype) {
                    $lessontypes[$id][$clessontype->id] = $clessontype->name;
                }
            }
        }






        // add group
        $mform->addElement('header', 'general', get_string('general', 'form'));
        // add teacher combobox
        $mform->addElement('select', 'teacherid', get_string('teacher', 'block_supervised'), $teachers);
        // add send e-mail checkbox
        $mform->addElement('advcheckbox', 'sendemail', get_string("sendemail", 'block_supervised'));
        $mform->addHelpButton('sendemail', 'sendemail', 'block_supervised');
        // add course combobox
        //$mform->addElement('select', 'courseid', get_string('course', 'block_supervised'), $courses);
        // add classroom combobox
        $mform->addElement('select', 'classroomid', get_string('classroom', 'block_supervised'), $classrooms);
        // add group combobox
        //$mform->addElement('select', 'groupid', get_string('group', 'block_supervised'), $groups);
        // add lessontype combobox
        //$mform->addElement('select', 'lessontypeid', get_string('lessontype', 'block_supervised'), $lessontypes);

        //$mform->addElement('static', 'name_of_static_element', "LABEL", "TEXT");

        $sel1 =& $mform->addElement('hierselect', 'courses_groups', 'Choose course and group:');
        $sel1->setOptions(array($courses, $groups));
        $sel2 =& $mform->addElement('hierselect', 'courses_lessontypes', 'Choose lessontype and group:');
        $sel2->setOptions(array($courses, $lessontypes));




        // hidden elements
        $mform->addElement('hidden', 'id');
        
        $this->add_action_buttons();
    }
}