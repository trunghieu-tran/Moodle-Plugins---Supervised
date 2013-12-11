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

$string['pluginname']               = 'Supervised';
$string['blocktitle']               = 'Supervised';

$string['plannedsessiontitle']              = 'You have a planned session';
$string['activesessiontitle']               = 'You have an active session';
$string['nosessionstitle']                  = 'You don\'t have any planned or active sessions. You can start new session right now';
$string['activesessionsstudenttitle']       = 'You have {$a} active session(s)';
$string['nosessionsstudenttitle']           = 'You don\'t have any active session...';


// Capabilities description
$string['supervised:teachermode']       = 'Teacher access mode. Can create sessions for himself';
$string['supervised:studentmode']       = 'Student access mode';
$string['supervised:readclassrooms']    = 'View classrooms';
$string['supervised:writeclassrooms']   = 'Manage classrooms';
$string['supervised:readlessontypes']   = 'View lessontypes';
$string['supervised:writelessontypes']  = 'Manage lessontypes';
$string['supervised:readsessions']      = 'View sessions';
$string['supervised:writesessions']     = 'Manage sessions. Can create sessions for any teacher';
$string['supervised:readlogs']          = 'View session\'s logs';


$string['classroomsurl']            = '[Classrooms]';
$string['lessontypesurl']           = '[Lesson types]';
$string['sessionsurl']              = '[Sessions]';

$string['classroomspagetitle']      = 'Classrooms list';
$string['classroomsheader']         = 'Classrooms list';
$string['supervisedsettings']       = 'Supervised settings';
$string['classroomsbreadcrumb']     = 'Classrooms';
$string['classroom']                = 'Classroom';
$string['iplist']                   = 'IP list';
$string['iplist_help']              = 
'IP list is a comma separated string of subnet definitions.

Subnet strings can be in one of those formats:

* xxx.xxx.xxx.xxx     (full IP address)

* xxx.xxx.xxx.xxx/nn  (number of bits in net mask)

* xxx.xxx.xxx.xxx-yyy (a range of IP addresses in the last group)

* xxx.xxx or xxx.xxx. (incomplete address)';
$string['active']                   = 'Use this classroom in sessions?';
$string['active_help']              = 'If enabled, you can create new sessions in this classroom';
$string['invalidclassroomid']       = 'You are trying to use an invalid classroom ID';
$string['insertclassroomerror']     = 'Database error! Can not insert classroom into database';
$string['cannotdeleteclassroom']    = 'You can not remove classroom used in session(s)';
$string['deleteclassroomcheck']     = 'Are you absolutely sure you want to completely delete this classroom?';
$string['addclassroompagetitle']    = 'Add classroom';
$string['addingnewclassroom']       = 'Adding new classroom';
$string['editclassroompagetitle']   = 'Edit classroom';
$string['editingclassroom']         = 'Editing classroom';
$string['addclassroom']             = '[Add classroom]';

$string['lessontypespagetitle']     = 'Lesson types';
$string['addlessontypepagetitle']   = 'Add lesson type';
$string['addingnewlessontype']      = 'Adding new lesson type';
$string['lessontypesview']          = 'Lesson types in current course';
$string['lessontypesbreadcrumb']    = 'Lesson types';
$string['lessontype']               = 'Lesson type';
$string['editlessontypepagetitle']  = 'Edit lesson type';
$string['editinglessontype']        = 'Editing lesson type';
$string['invalidlessontypeid']      = 'You are trying to use an invalid lesson type ID';
$string['cannotdeletelessontype']   = 'You can not delete this lesson type, it\'s used in sessions or quizzes';
$string['deletelessontypecheck']    = 'Are you absolutely sure you want to completely delete this lessontype?';
$string['insertlessontypeerror']    = 'Database error! Can not insert lessontype into database';
$string['addlessontype']            = '[Add lessontype]';

$string['sessionspagetitle']        = 'Sessions';
$string['sessionsbreadcrumb']       = 'Sessions';
$string['sessionsheader']           = 'Sessions list';
$string['invalidsessionid']         = 'You are trying to use an invalid session ID';
$string['insertsessionerror']       = 'Database error! Can not insert session into database';
$string['sessionediterror']         = 'You can edit only Planned session';
$string['sessiondeleteerror']       = 'You can delete only Planned session';
$string['addsessionpagetitle']      = 'Plan new session';
$string['addingnewsession']         = 'Plan new session';
$string['editsessionpagetitle']     = 'Edit planned session';
$string['editingsession']           = 'Editing planned session';
$string['sendemail']                = 'Send e-mail';
$string['sendemail_help']           = 'If checked, selected teacher will be notified about creation, removing and any changes in his session. If teacher will be changed, both will be notified.';
$string['sessiondeleteheader']      = 'Do you want delete this session?';
$string['deletesessionnavbar']      = 'Delete session?';
$string['sessiondeletetitle']       = 'Delete session';
$string['notifyteacher']            = 'Notify teacher by e-mail';
$string['notifyteacher_help']       = 'If checked, the teacher will be notified about session removing. You can add message for teacher';
$string['messageforteacher']        = 'Message for teacher';
$string['timeendvalidationerror']   = 'The session must be active before {$a} at least.';
$string['durationvalidationerror']  = 'Duration must be greater than zero value.';
$string['teacherhassession']        = 'Teacher has an other session in this period.';
$string['teachervalidationerror']   = 'You can plane session only for yourself.';
$string['plansession']              = '[Plan new session]';

$string['logspagetitle']            = 'Session logs';
$string['logsview']                 = 'Session logs';
$string['logsbreadcrumb']           = 'Session logs';

$string['course']       = 'Course';
$string['group']        = 'Group';
$string['teacher']      = 'Teacher';
$string['timestart']    = 'Time start';
$string['duration']     = 'Duration (min)';
$string['timeend']      = 'Time end';
$string['state']        = 'State';
$string['logs']         = 'Logs';
$string['showlogs']     = '[Show logs]';

$string['allgroups']        = 'All groups';
$string['notspecified']     = 'Not specified';
$string['sessioncomment']   = 'Comment';
$string['sessioninfo']      = 'Session information';



$string['startsession']     = 'Start session';
$string['updatesession']    = 'Update';
$string['finishsession']    = 'Finish session';
$string['savechanges']      = 'Save changes';

