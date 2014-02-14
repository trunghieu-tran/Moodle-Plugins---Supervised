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

$string['pluginname']                           = 'Supervised';
$string['blocktitle']                           = 'Supervised';

$string['plannedsessiontitle']                  = 'You have a planned session';
$string['activesessiontitle']                   = 'You have an active session';
$string['nosessionstitle']                      = 'You don\'t have any planned or active sessions. You can start new session right now';
$string['activesessionsstudenttitle']           = 'You have {$a} active session(s)';
$string['nosessionsstudenttitle']               = 'You don\'t have any active session...';


// Capabilities description.
$string['supervised:addinstance']               = 'Add a new Supervised block';
$string['supervised:besupervised']              = 'Participate in the supervised session (intended for students etc)';
$string['supervised:supervise']                 = 'Ability to supervised sessions: start planned and new ones, edit and finish active ones, view logs.';
$string['supervised:editclassrooms']            = 'Edit classrooms';
$string['supervised:editlessontypes']           = 'Edit lesson types';
$string['supervised:viewownsessions']           = 'View own sessions (planned, active and finished) and their logs';
$string['supervised:viewallsessions']           = 'View all sessions (planned, active and finished) and their logs';
$string['supervised:manageownsessions']         = 'Manage own sessions: plan, edit and remove unfinished sessions';
$string['supervised:manageallsessions']         = 'Manage all sessions: plan, edit and remove unfinished sessions';
$string['supervised:managefinishedsessions']    = 'Remove finished sessions';


$string['classroomsurl']                        = 'Classrooms';
$string['lessontypesurl']                       = 'Lesson types';
$string['sessionsurl']                          = 'Sessions';
$string['plannedstate']                         = 'Planned';
$string['activestate']                          = 'Active';
$string['finishedstate']                        = 'Finished';
$string['unknownstate']                         = 'Unknown';


$string['classroomspagetitle']                  = 'Classrooms list';
$string['classroomsheader']                     = 'Classrooms list';
$string['classroomsdefinition']                 = 'Classrooms definition';
$string['classroomsdefinition_help']            = 'Any supervised sessions takes place somewhere - i.e. in the classroom. The classroom is defined by an IP subnet (ask you admin for IP range for you classroom, if you don\'t know what it is). Only students working on computers with specified IPs participate in supervised session, so students from the group can not cheat you acessing site from other places when you supervise their classmates in some classroom. Classrooms are acessible from all courses.';
$string['supervisedsettings']                   = 'Supervised settings';
$string['classroomsbreadcrumb']                 = 'Classrooms';
$string['classroom']                            = 'Classroom';
$string['allclassrooms']                        = 'All classrooms';
$string['iplist']                               = 'IP list';
$string['iplist_help']                          =
'IP list is a comma separated string of subnet definitions.

Subnet strings can be in one of those formats:

* xxx.xxx.xxx.xxx     (full IP address)

* xxx.xxx.xxx.xxx/nn  (number of bits in net mask)

* xxx.xxx.xxx.xxx-yyy (a range of IP addresses in the last group)

* xxx.xxx or xxx.xxx. (incomplete address)';
$string['active']                               = 'Use this classroom in sessions?';
$string['active_help']                          = 'If enabled, you can start and plan new sessions in this classroom';
$string['invalidclassroomid']                   = 'You are trying to use an invalid classroom ID';
$string['insertclassroomerror']                 = 'Database error! Can not insert classroom into database';
$string['cannotdeleteclassroom']                = 'There were sessions in this classroom. Delete sessions using this classroom first. You can hide classroom';
$string['deleteclassroomcheck']                 = 'Are you sure you want to completely delete this classroom from every course on the site?';
$string['addclassroompagetitle']                = 'Add classroom';
$string['addingnewclassroom']                   = 'Adding new classroom';
$string['editclassroompagetitle']               = 'Edit classroom';
$string['editingclassroom']                     = 'Editing classroom';
$string['addclassroom']                         = 'Add a classroom ...';
$string['addclassroomnavbar']                   = 'Add classroom';
$string['editclassroomnavbar']                  = 'Edit classroom';

$string['lessontypespagetitle']                 = 'Lesson types';
$string['lessontypesdefinition']                = 'Lesson types definition';
$string['lessontypesdefinition_help']           = 'Consider you have several different lesson types in the courses (i.e. exam, colloquim etc), and want certaing things only on certain lessons (i.e. exam quiz shoudn\'t be accessible on colloquim). You can use lesson types to sort them out. Session creates always for some lesson type. You can limit quiz acessibility and other features to the sessions of certain lesson type. Unlike classrooms, lesson types created separately for each course.';
$string['addlessontypepagetitle']               = 'Add lesson type';
$string['addingnewlessontype']                  = 'Adding new lesson type';
$string['lessontypesview']                      = 'Lesson types in current course';
$string['lessontypesbreadcrumb']                = 'Lesson types';
$string['lessontype']                           = 'Lesson type';
$string['editlessontypepagetitle']              = 'Edit lesson type';
$string['editinglessontype']                    = 'Editing lesson type';
$string['invalidlessontypeid']                  = 'You are trying to use an invalid lesson type ID';
$string['cannotdeletelessontype']               = 'You can not delete this lesson type, because it\'s used in sessions or quizzes';
$string['deletelessontypecheck']                = 'Are you sure you want to completely delete this lesson type?';
$string['insertlessontypeerror']                = 'Database error! Can not insert lesson type into database';
$string['addlessontype']                        = 'Add a lesson type ...';
$string['addlessontypenavbar']                  = 'Add lesson type';
$string['editlessontypenavbar']                 = 'Edit lesson type';
$string['alllessontypes']                       = 'All lesson types';

$string['sessionspagetitle']        = 'Sessions';
$string['sessionsbreadcrumb']       = 'Sessions';
$string['sessionsheader']           = 'Sessions list';
$string['invalidsessionid']         = 'You are trying to use an invalid session ID';
$string['insertsessionerror']       = 'Database error! Can not insert session into database';
$string['sessionediterror']         = 'You can edit only planned sessions';
$string['sessiondeleteerror']       = 'You can\'t delete active session. End session first.';
$string['sessionlogserror']         = 'You can\'t view logs of planned session - there is nothing to view';
$string['addsessionpagetitle']      = 'Plan new session';
$string['addingnewsession']         = 'Plan new session';
$string['editsessionpagetitle']     = 'Edit planned session';
$string['editingsession']           = 'Editing session';
$string['sendemail']                = 'Send e-mail';
$string['sendemail_help']           = 'If checked, selected teacher will be notified about creation, removing and any changes in his session. If the teacher will be changed, both will be notified.';
$string['sessiondeleteheader']      = 'Do you want to delete this session?';
$string['deletesessionnavbar']      = 'Delete session?';
$string['plansessionnavbar']        = 'Plan session';
$string['editsessionnavbar']        = 'Edit session';
$string['sessiondeletetitle']       = 'Delete session';
$string['notifyteacher']            = 'Notify teacher by e-mail';
$string['notifyteacher_help']       = 'If checked, the teacher will be notified about session removing. You can add message for the teacher';
$string['messageforteacher']        = 'Message for the teacher';
$string['timeendvalidationerror']   = 'The session must be active before {$a} at least.';
$string['increaseduration']         = 'Session time end must be greater than current time.';
$string['durationvalidationerror']  = 'Duration must be greater than zero value.';
$string['pagesizevalidationerror']  = 'Page size must be greater than zero value.';
$string['teacherhassession']        = 'Teacher already has a session in this time.';
$string['teachervalidationerror']   = 'You can plan session only for yourself.';
$string['plansession']              = 'Plan a new session ...';
$string['showsessions']             = 'Show sessions';
$string['sessionstartsafter']       = 'Session starts after';
$string['sessionendsbefore']        = 'Session ends before';
$string['timetovalidationerror']    = 'Session\'s end must be greater or equal to session\'s start.';
$string['enrollteacher']            = 'To plan a session you must enroll at least one teacher to course!';
$string['createclassroom']          = 'To plan or start a session you must create (or make visible) at least one classroom!';
$string['gotoenrollment']           = 'Go to user enrollment page';
$string['gotoclassrooms']           = 'Go to classrooms page';

$string['sessionsdefinition']       = 'Sessions definition';
$string['sessionsdefinition_help']  = 'The course teachers creates sessions specifying the group, lesson type (e.g. laboratory work, exam, etc.), classroom and duration. After that students will be able to start quizzes from this course according next conditions:

- the session is active;

- student is in a group for which the session was created;

- student is in session\'s classroom (the teacher can specify the ip subnet for each classroom);

- the session was created for the lesson type which is specified for the quiz (go to quiz settings -> Extra restrictions on attempts).';

$string['logspagetitle']            = 'Session logs';
$string['logsview']                 = 'Session logs';
$string['logsbreadcrumb']           = 'Session logs';
$string['filterlogsbyuser']         = 'Filter logs by user';
$string['allusers']                 = 'All users';
$string['showlogsbutton']           = 'Show logs';

$string['course']                   = 'Course';
$string['group']                    = 'Group';
$string['teacher']                  = 'Teacher';
$string['timestart']                = 'Time start';
$string['duration']                 = 'Duration (min)';
$string['timeend']                  = 'Time end';
$string['state']                    = 'State';
$string['allstates']                = 'All states';
$string['logs']                     = 'Logs';
$string['showlogs']                 = 'Show logs';

$string['allgroups']                = 'All groups';
$string['notspecified']             = 'Not specified';
$string['sessioncomment']           = 'Comment';
$string['sessioninfo']              = 'Session information';

$string['startsession']             = 'Start session';
$string['updatesession']            = 'Update';
$string['finishsession']            = 'Finish session';
$string['savechanges']              = 'Save changes';


$string['emailnewsessionsubject']   = '{$a->sitename}: new session on {$a->timestart}';
$string['emailnewsession']          = 'Hi {$a->teachername},

A new session has been created for you at \'{$a->sitename}\'.
Creator:        {$a->creatorname}

Course:         {$a->course}
Classroom:      {$a->classroom}
Group:          {$a->group}
Lesson type:    {$a->lessontype}
Time start:     {$a->timestart}
Duration (min): {$a->duration}
Time end:       {$a->timeend}

{$a->comment}

You can edit this session: {$a->editurl}
Or you can delete it: {$a->deleteurl}

Have a nice day!';

$string['emailsessioncomment']       = 'Session comment:
--------------------------------------------------
{$a}
--------------------------------------------------
';


$string['emailremovedsessionsubject']   = '{$a->sitename}: session has been removed on {$a->timestart}';
$string['emailremovedsession']          = 'Hi {$a->teachername},

Your session at \'{$a->sitename}\' has been removed.
Remover:        {$a->removername}

Session state:  {$a->state}
Course:         {$a->course}
Classroom:      {$a->classroom}
Group:          {$a->group}
Lesson type:    {$a->lessontype}
Time start:     {$a->timestart}
Duration (min): {$a->duration}
Time end:       {$a->timeend}

{$a->comment}

{$a->custommessage}

Have a nice day!';

$string['emailremovedsessionmsg']       = 'A person who removed this session leaved a message for you:
--------------------------------------------------
{$a}
--------------------------------------------------
';

$string['emaileditedsessionsubject']   = '{$a->sitename}: session has been edited on {$a->timestart}';
$string['emaileditedsession']          = 'Hi {$a->teachername},

Your session at \'{$a->sitename}\' has been edited.
Editor:        {$a->editorname}

Updated session information:
Course:         {$a->course}
Classroom:      {$a->classroom}
Group:          {$a->group}
Lesson type:    {$a->lessontype}
Time start:     {$a->timestart}
Duration (min): {$a->duration}
Time end:       {$a->timeend}

{$a->comment}

You can edit this session: {$a->editurl}
Or you can delete it: {$a->deleteurl}

Have a nice day!';