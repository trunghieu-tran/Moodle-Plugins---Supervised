<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * English strings for poasassignment
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod_poasassignment
 * @copyright 2010 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/* $files = scandir( dirname(dirname(dirname(__FILE__))).'\\taskgivers');
foreach($files as $file) {
    if(is_dir(dirname(dirname(dirname(__FILE__))).'\\taskgivers\\'.$file) && $file !== '.' && $file !== '..') {
        require_once(dirname(dirname(dirname(__FILE__))).'\\taskgivers\\'.$file.'\\lang\\en.php');
    }
} */

$string['modulename'] = 'POAS assignment';
$string['modulenameplural'] = 'POAS assignments';
$string['poasassignmentfieldset'] = 'Individual tasks';
$string['poasassignmentname'] = 'POAS assignment name';
$string['poasassignment'] = 'POAS assignment';
$string['poasassignmentintro']='POAS assignment intro';
$string['pluginname'] = 'POAS assignment';
$string['taskdescription']='Task description';
$string['poasassignmentadministration']='Administration';
$string['availabledate']= 'Available from';
$string['choicedate']= 'Make choice before';
$string['deadline']= 'Due date';
$string['preventlate']='Prevent late submissions';
$string['preventlatechoice']='Prevent late choice';
$string['randomtasksafterchoicedate']='Set free random task to student after choice date';
$string['answers']='Answers';
$string['severalattempts']='Several attempts';
$string['severalattempts_help']='Student can do several attempts';
$string['notifyteachers']='Notify teachers';
$string['notifyteachers_help']='Send e-mail to teacher when student makes attempt';
$string['notifystudents']='Notify students';
$string['notifystudents_help']='Send e-mail to students when some changes made with this module';
$string['answerfile']='File upload';
$string['answerfile_help']='If this setting is checked student will be able to load files on his submission screen';
$string['answertext']='Online text';
$string['answertext_help']='If this setting is checked student will be able to type text in his submission screen';
$string['submissionfilemaxsize'] = 'Maximum file size';
$string['submissionfilemaxsize_help'] = 'Maximum file size of file that can be uploaded as submission';
$string['fileextensions']='File extensions';
$string['fileextensions_help']='Extensions of submision files that are allowed for students';
$string['activateindividualtasks']='Activate individual tasks';
$string['activateindividualtasks_help']='Check this if you need individual tasks mode. In other case module will work as standard assignment';
$string['howtochoosetask']='Chose mode';
$string['howtochoosetask_help']='This setting has three options:
    
* Random task - each student gets random task
* Using the parameters - each student specifies desired parameters
* Students choose their tasks - students can see all tasks and choose one of them';
$string['secondchoice']='Second choice';
$string['secondchoice_help']='Student can refuse task and make new choice';
$string['uniqueness']='Task uniqueness';
$string['uniqueness_help']='This setting has three options:

* No uniqueness - each student can choose any task
* Unique within group - student can choose task if only nobody in group choosed it
* Unique within course - student can choose task if only nobody in course choosed it';
$string['nouniqueness']='No uniqueness';
$string['uniquewithingroup']='Unique within group';
$string['uniquewithincourse']='Unique within course';
$string['teacherapproval']='Teacher approval';
$string['teacherapproval_help']='Student needs teacher approval to make submissions';
$string['poasassignmentfiles']='Attach file(s) to the assignment';
$string['attachedpoasassignmentfiles']='File(s) attached to the assignment';
$string['choicebeforeopen']='Choice date before available date';
$string['deadlinebeforeopen']='Deadline before available date';
$string['deadlinebeforechoice']='Deadline before choice date';
$string['submissionfilesamount']='Files amount';
$string['submissionfilesamount_help']='How much files can be uploaded as a submission';
$string['availablefrom']='Available from';
$string['selectbefore']='Select before';
$string['tasksfields']="Task's fields";
$string['tasks'] = 'Tasks';
$string['grades'] = 'Grades';
$string['ftype'] = 'Field type';
$string['taskfieldname'] = 'Field name';
$string['showintable'] = 'Show in table';
$string['searchparameter'] ="Parameter of student's search";
$string['maxvalue'] = 'Maximum value';
$string['minvalue']='Minimum value';
$string['view'] = 'View';
$string['poasassignment:managetasksfields'] = 'poasassignment:managetasksfields';
$string['taskfieldaddheader'] = 'Add new task field';
$string['taskfieldeditheader'] = 'Edit task field';
$string['criterionname'] = 'Criterion name';
$string['criterionweight'] = 'Criterion weight';
$string['criterionsource'] = 'Criterion source';
$string['criterionmanual'] = 'Manual criterion';
$string['gradesfields'] = "Grade's fields";
$string['secretfield'] = 'Secret field';
$string['random'] = 'Random field';
$string['variants'] = 'Variants';
$string['errornovariants'] = 'No variants';
$string['criterions'] = 'Rating criterions';
$string['criterionideditheader'] = 'Edit criterions';
$string['criterionidaddheader'] = 'Add new criterion';
$string['errorindtaskmodeisdisabled'] = 'Invividual task mode is disabled!';
$string['char']='String';
$string['text']='Text';
$string['float']='Fraction';
$string['int']='Number';
$string['date']='Date';
$string['file']='File';
$string['list']='List of elements';
$string['taskeditheader']='Edit task';
$string['taskaddheader']='Add new task';
$string['taskname']='Task name';
$string['taskintro']='Task description';
$string['errorfiledduplicatename'] = 'This name is used by another field';
$string['addbuttontext']='Add';
$string['erroroutofrange']='Value out of range';
$string['sendsubmission'] = 'Send submission';
$string['answertexteditor']='Type your answer here';
$string['task'] = 'Task';
$string['errormaxislessthenmin'] = 'Maximum value is less then minimum value';
$string['valuemustbe'] = 'Value must be';
$string['morethen'] = 'more then';
$string['and'] = 'and';
$string['lessthen'] = 'less then';
$string['invalidtaskid'] ='Invalid task id';
$string['invaliduserid']='Invalid user id';
$string['taketask'] = 'Take this task';
$string['taketaskconfirmation'] ='Are you sure you want to take this task?';
$string['alreadyhavetask'] = 'You have a task already';
$string['addsubmission'] = 'Add submission';
$string['youhavetask']= 'Your task is';
$string['youhavenotask']='You have no task';
$string['yoursubmissions']='Your submissions';
$string['yourfiles'] = 'Your files';
$string['editsubmission'] = 'Edit submission';
$string['owntasklink']='My task';
$string['submissions']='Submissions';
$string['fullname'] = 'Student';
$string['submission']='Submission';
$string['errorvariants'] = 'It must be at least two variants';
$string['variants_help'] = 'Input variants of list separated by enter';
$string['notask'] = 'No task';
$string['status']='Status';
$string['grade']='Grade';
$string['taskcompleted']='Task completed';
$string['taskinwork']='Task is in work';
$string['notask']='No task';
$string['addgrade']='Add grade';
$string['gradeeditheader']='Edit grade';
$string['errornocriterions']='No grade criteions. Add at least 1 criterion';
$string['normalizedcriterionweight']='Normalized criterion weignt';
$string['studentsubmission']="Student's submission";
$string['comment']='Comment';
$string['editgrade']='Edit grade';
$string['criteriondescription']='Criterion description';
$string['feedback']='Feedback';
$string['commentfiles']='Add comment file';
$string['studentsubmission']="Student's submission";
$string['getrandomtask']='Get random task';
$string['multilist']='Multi-list';
$string['finalgrade']='Finalize grade';
$string['invalidfieldid']='Invalid field id';
$string['deletefieldconfirmation']='Are you sure you want to delete this field?';
$string['taskhidden']='Task is hidden from students';
$string['errormustbefloat']='It must be fraction';
$string['errormustbeint']='It must be integer';
$string['nosatisfyingtasks']='There is no task that satisfies your requirements';
$string['inputparameters']='Input preferred parameters of task';
$string['taskunavailable']='Task is hidden';
$string['errornoname']='You must define name';
$string['invalidassigneeid']='Invalid assignee id';
$string['totalratingis']='Total grade is';
$string['thismoduleisntopenedyet']="This module isn't opened yet";
$string['gototasskpage']='Click here to take task';
$string['taskfielddescription']="Field's description";
$string['submissiondate']='Submission date';
$string['answerfiles']='Load submission files';
$string['picture']="User's picture";
$string['gradedate']='Grade date';
$string['attemptnumber']='Attempt number';
$string['oldfeedback']='Not actual';
$string['outdated']='Outdated';
$string['view_help']='On this page you can see the most important information about your task. 
You can see critical dates.';
$string['submissions_help']='On this page you can grade student works';
$string['tasks_help']='On this page you can manage assignment tasks';
$string['criterions_help']='On this page you can manage assignment criterions';
$string['tasksfields_help']='On this page you can manage fields of assignment tasks';
$string['of']='of';
$string['needgrade'] = 'need grade';
$string['penalty']='Penalty for attempt';
$string['penalty_help']='Penalty that will be deducted from final grade with each new attempt';
$string['ago'] = 'ago';
$string['newattemptbeforegrade']='All-as-one';
$string['newattemptbeforegrade_help']='Student can do another attempt without having penalty';
$string['draft']='Draft';
$string['myattempts']='My attempts';
$string['studentattempts']='Student attempts';
$string['disablepenalty']='Turn off penalty for this attempt';
$string['enablepenalty']='Turn on penalty for this attempt';
$string['range']='Range';
$string['pluginadministration']='Plugin administration';
$string['errornoid'] = 'You must specify a course_module ID or an instance ID';
$string['nostudents'] = 'Threre are no students on this task';
$string['noattempts'] ='No attempts have been made on this assignment';
$string['criterionname_help']='Name of the criterion';
$string['criteriondescription_help']='Description of the criterion that will be available for students to look';
$string['criterionweight_help']='Relative importance rating of criterion';
$string['criterionsource_help']='This setting has two options:

* manually - traditional grading
* module - if you have special module that checks submission';
$string['attempts']='Attempts';
$string['lastgraded']='Last graded attempt';
$string['lastattempt']='Last attempt';
$string['poasassignmentgraderslist']='Graders that will be used in this module';
$string['finalattempts']='Can finalize attempt';
$string['finalattempts_help']="Student cancan match his attempt as final - it means he can't reload his answer until teacher grades last attempt";
$string['final'] = 'I am sure in my answer, finilize';
$string['graders'] = 'Graders';
$string['graders_help'] = 'Graders help page';