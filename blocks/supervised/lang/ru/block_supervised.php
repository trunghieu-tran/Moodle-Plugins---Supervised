<?php
// This file is part of Student Access Control Kit - https://code.google.com/p/oasychev-moodle-plugins/
//
// Student Access Control Kit is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Student Access Control Kit is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     block
 * @subpackage  supervised
 * @author      Andrey Ushakov <andrey200964@yandex.ru>
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['active']                               = 'Использовать эту аудиторию в сессиях?';
$string['active_help']                          = 'If enabled, you can start and plan new sessions in this classroom';
$string['activesessionsstudenttitle']           = 'У вас {$a} активных занятий';
$string['activesessiontitle']                   = 'У вас есть активное занятие';
$string['addclassroom']                         = 'Добавить аудиторию ...';
$string['addclassroomnavbar']                   = 'Добавить аудиторию';
$string['addclassroompagetitle']                = 'Добавить аудиторию';
$string['addingnewclassroom']                   = 'Добавление аудитории';
$string['addingnewlessontype']                  = 'Добавление типа занятия';
$string['addingnewsession']                     = 'Запланировать занятие';
$string['addlessontype']                        = 'Добавить тип занятия ...';
$string['addlessontypenavbar']                  = 'Добавить тип занятия';
$string['addlessontypepagetitle']               = 'Добавить тип занятия';
$string['addsessionpagetitle']                  = 'Запланировать занятие';
$string['allclassrooms']                        = 'Все аудитории';
$string['allgroups']                            = 'Все группы';
$string['alllessontypes']                       = 'Все типы занятий';
$string['allstates']                            = 'Все состояния';
$string['allsupervisers']                       = 'Все преподаватели';
$string['allusers']                             = 'Все пользователи';
$string['blocktitle']                           = 'Supervised';
$string['cannotdeleteclassroom']                = 'There were sessions in this classroom. Delete sessions using this classroom first. You can hide classroom';
$string['cannotdeletelessontype']               = 'You can not delete this lesson type, because it\'s used in sessions or quizzes';
$string['classroom']                            = 'Аудитория';
$string['classroomsbreadcrumb']                 = 'Аудитории';
$string['classroomsdefinition']                 = 'Classrooms definition';
$string['classroomsdefinition_help']            = 'Any supervised sessions takes place somewhere - i.e. in the classroom. The classroom is defined by an IP subnet (ask you admin for IP range for you classroom, if you don\'t know what it is). Only students working on computers with specified IPs participate in supervised session, so students from the group can not cheat you acessing site from other places when you supervise their classmates in some classroom. Classrooms are acessible from all courses.';
$string['classroomsheader']                     = 'Список аудиторий';
$string['classroomspagetitle']                  = 'Список аудиторий';
$string['classroomsurl']                        = 'Аудитории';
$string['coursesettings']                       = 'Специфичные настройки курса';
$string['createclassroom']                      = 'To plan or start a session you must create (or make visible) at least one classroom!';
$string['deleteclassroomcheck']                 = 'Вы уверены, что хотите удалить эту аудиторию из всех курсов на этом сайте?';
$string['deletelessontypecheck']                = 'Вы уверены что хотите удалить этот тип занятия?';
$string['deletesessionnavbar']                  = 'Удалить занятие?';
$string['duration']                             = 'Длительность (мин)';
$string['durationvalidationerror']              = 'Duration must be greater than zero value.';
$string['editclassroomnavbar']                  = 'Редактировать аудиторию';
$string['editclassroompagetitle']               = 'Редактировать аудиторию';
$string['editingclassroom']                     = 'Редактирование аудитории';
$string['editinglessontype']                    = 'Редактирование типа занятия';
$string['editingsession']                       = 'Редактирование сессии';
$string['editlessontypenavbar']                 = 'Редактировать тип занятия';
$string['editlessontypepagetitle']              = 'Редактировать тип занятия';
$string['editsessionnavbar']                    = 'Редактировать занятие';
$string['editsessionpagetitle']                 = 'Редактировать запланированное занятие';
$string['emaildeletesessionurl']                = 'You can delete this session: {$a}';
$string['emaileditedsession']                   = 'Hi {$a->teachername},

Your session at \'{$a->sitename}\' has been edited.
Editor:        {$a->editorname}

Updated session information:
{$a->sessioninfo}

{$a->editsession}
{$a->deletesession}

{$a->haveaniceday}';
$string['emaileditedsessionsubject']            = '{$a->sitename}: session has been edited on {$a->timestart}';
$string['emaileditsessionurl']                  = 'You can edit this session: {$a}';
$string['emailnewsession']                      = 'Hi {$a->teachername},

A new session has been created for you at \'{$a->sitename}\'.
Creator:        {$a->creatorname}

{$a->sessioninfo}

{$a->editsession}
{$a->deletesession}

{$a->haveaniceday}';
$string['emailnewsessionsubject']               = '{$a->sitename}: new session on {$a->timestart}';
$string['emailremovedsession']                  = 'Hi {$a->teachername},

Your session at \'{$a->sitename}\' has been removed.
Remover:        {$a->removername}

{$a->sessioninfo}

{$a->custommessage}

{$a->haveaniceday}';
$string['emailremovedsessionmsg']               = 'A person who removed this session leaved a message for you:
--------------------------------------------------
{$a}
--------------------------------------------------
';
$string['emailremovedsessionsubject']           = '{$a->sitename}: session has been removed on {$a->timestart}';
$string['emailsessioncomment']                  = 'Session comment:
--------------------------------------------------
{$a}
--------------------------------------------------
';
$string['emailsessioninfo']                     = 'Course:         {$a->course}
Classroom:      {$a->classroom}
Group:          {$a->group}
Lesson type:    {$a->lessontype}
Time start:     {$a->timestart}
Duration (min): {$a->duration}
Time end:       {$a->timeend}

{$a->comment}';
$string['enrollteacher']                        = 'To plan a session you must enroll at least one user with ability to supervise sessions to the course!';
$string['filterlogsbyuser']                     = 'Показать логи пользователя';
$string['finishedstate']                        = 'Завершено';
$string['finishsession']                        = 'Завершить занятие';
$string['gotoclassrooms']                       = 'Go to classrooms page';
$string['gotoenrollment']                       = 'Go to user enrollment page';
$string['haveaniceday']                         = 'Have a nice day!';
$string['increaseduration']                     = 'Session time end must be greater than current time.';
$string['insertclassroomerror']                 = 'Database error! Can not insert classroom into database';
$string['insertlessontypeerror']                = 'Database error! Can not insert lesson type into database';
$string['insertsessionerror']                   = 'Database error! Can not insert session into database';
$string['invalidclassroomid']                   = 'You are trying to use an invalid classroom ID';
$string['invalidlessontypeid']                  = 'You are trying to use an invalid lesson type ID';
$string['invalidsessionid']                     = 'You are trying to use an invalid session ID';
$string['iplist']                               = 'IP диапазон';
$string['iplist_help']                          =
'IP list is a comma separated string of subnet definitions.

Subnet strings can be in one of those formats:

* xxx.xxx.xxx.xxx     (full IP address)

* xxx.xxx.xxx.xxx/nn  (number of bits in net mask)

* xxx.xxx.xxx.xxx-yyy (a range of IP addresses in the last group)

* xxx.xxx or xxx.xxx. (incomplete address)';
$string['lessontype']                           = 'Тип занятия';
$string['lessontypesbreadcrumb']                = 'Типы занятий';
$string['lessontypesdefinition']                = 'Lesson types definition';
$string['lessontypesdefinition_help']           = 'Consider you have several different lesson types in the courses (i.e. exam, colloquim etc), and want certaing things only on certain lessons (i.e. exam quiz shoudn\'t be accessible on colloquim). You can use lesson types to sort them out. Session creates always for some lesson type. You can limit quiz acessibility and other features to the sessions of certain lesson type. Unlike classrooms, lesson types created separately for each course.';
$string['lessontypespagetitle']                 = 'Типы занятий';
$string['lessontypesurl']                       = 'Типы занятий';
$string['lessontypesview']                      = 'Типы занятий в текущем курсе';
$string['logsbreadcrumb']                       = 'Логи занятия';
$string['logspagetitle']                        = 'Логи занятия';
$string['logsview']                             = 'Логи занятия';
$string['messageforteacher']                    = 'Сообщение для преподавателя';
$string['nosessionsstudenttitle']               = 'У вас нет активных занятий...';
$string['nosessionstitle']                      = 'У вас нет активных или запланированных занятий. Вы можете начать новое занятие прямо сейчас';
$string['notifyteacher']                        = 'Оповестить по e-mail';
$string['notifyteacher_help']                   = 'If checked, selected teacher will be notified about creation, removing and any changes in his session. If the teacher will be changed, both will be notified.';
$string['notspecified']                         = 'Not specified';
$string['pagesizevalidationerror']              = 'Page size must be greater than zero value.';
$string['plannedsessiontitle']                  = 'У вас есть запланированное занятие';
$string['plannedstate']                         = 'Запланировано';
$string['plansession']                          = 'Запланировать занятие ...';
$string['plansessionnavbar']                    = 'Запланировать занятие';
$string['pluginname']                           = 'Supervised';
$string['sessiondeleteerror']                   = 'You can\'t delete active session. End session first.';
$string['sessiondeleteheader']                  = 'Вы действительно хотите удалить это занятие?';
$string['sessiondeletetitle']                   = 'Удалить занятие';
$string['sessiondurationcourse']                = 'Длительность сессии по умолчанию для данного курса (мин)';
$string['sessionediterror']                     = 'You can edit only planned sessions';
$string['sessionendsbefore']                    = 'Занятие завершается до';
$string['sessioninfo']                          = 'Информация о занятии';
$string['sessionlogserror']                     = 'You can\'t view logs of planned session - there is nothing to view';
$string['sessionsbreadcrumb']                   = 'Занятия';
$string['sessionsdefinition']                   = 'Sessions definition';
$string['sessionsdefinition_help']              = 'The course teachers creates sessions specifying the group, lesson type (e.g. laboratory work, exam, etc.), classroom and duration. After that students will be able to start quizzes from this course according next conditions:

- the session is active;

- student is in a group for which the session was created;

- student is in session\'s classroom (the teacher can specify the ip subnet for each classroom);

- the session was created for the lesson type which is specified for the quiz (go to quiz settings -> Extra restrictions on attempts).';
$string['sessionsheader']                       = 'Список занятий';
$string['sessionspagetitle']                    = 'Занятия';
$string['sessionstartsafter']                   = 'Занятие начинается после';
$string['sessionsurl']                          = 'Занятия';
$string['settingsdayspastdesc']                 = 'Количество дней (от текущей даты) за которые показывать сессии при открытии таблицы занятий.';
$string['settingsdayspasttitle']                = 'Количество дней, за которые показывать занятия';
$string['settingsdurationdesc']                 = 'Длительность занятия по умолчанию (в минутах).';
$string['settingsdurationtitle']                = 'Длительность занятия';
$string['showlogs']                             = 'Показать логи';
$string['showlogsbutton']                       = 'Показать логи';
$string['showsessions']                         = 'Показать занятия';
$string['startsession']                         = 'Начать';
$string['supervised:addinstance']               = 'Add a new Supervised block';
$string['supervised:besupervised']              = 'Participate in the supervised session (intended for students etc)';
$string['supervised:editclassrooms']            = 'Edit classrooms';
$string['supervised:editlessontypes']           = 'Edit lesson types';
$string['supervised:manageallsessions']         = 'Manage all sessions: plan, edit and remove unfinished sessions';
$string['supervised:managefinishedsessions']    = 'Remove finished sessions';
$string['supervised:manageownsessions']         = 'Manage own sessions: plan, edit and remove unfinished sessions';
$string['supervised:supervise']                 = 'Ability to supervised sessions: start planned and new ones, edit and finish active ones, view logs.';
$string['supervised:viewallsessions']           = 'View all sessions (planned, active and finished) and their logs';
$string['supervised:viewownsessions']           = 'View own sessions (planned, active and finished) and their logs';
$string['supervisedsettings']                   = 'Supervised settings';
$string['superviser']                           = 'Преподаватель';
$string['teacherhassession']                    = 'Teacher already has a session in this time.';
$string['teachervalidationerror']               = 'You can plan session only for yourself.';
$string['timeend']                              = 'Время окончания';
$string['timeendvalidationerror']               = 'The session must be active before {$a} at least.';
$string['timestart']                            = 'Время начала';
$string['timetovalidationerror']                = 'Session\'s end must be greater or equal to session\'s start.';

$string['eventaddclassroom']                    = 'Добавлена новая ауитория';
$string['eventdeleteclassroom']                 = 'Аудитория была удалена';
$string['eventupdateclassroom']                 = 'Аудитория изменена';
$string['eventaddlessontype']                   = 'Добавлен новый тип занятия';
$string['eventdeletelessontype']                = 'Тип занятия удален';
$string['eventupdatelessontype']                = 'Тип занятия изменен';
$string['eventhideclassroom']                   = 'Аудитория была скрыта';
$string['eventunhideclassroom']                 = 'Аудитория перестала быть скрытой';
$string['eventaddsession']                      = 'Добавлена новая сессия';
$string['eventdeletesession']                   = 'Сессия удалена';
$string['eventupdatesession']                   = 'Сессия изменена';
$string['eventupdateactivesession']             = 'Активная сессия изменена';
$string['eventstartsession']                    = 'Сессия начата';
$string['eventfinishsession']                   = 'Сессия завершена';
$string['eventstartplannedsession']             = 'Запланированная сессия начата';