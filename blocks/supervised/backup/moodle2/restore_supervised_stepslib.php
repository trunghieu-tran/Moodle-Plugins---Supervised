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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that wll be used by the restore_rss_client_block_task
 */

/**
 * Define the complete rss_client  structure for restore
 */
class restore_supervised_block_structure_step extends restore_structure_step {

    protected function define_structure() {
        $paths = array();

        $userinfo = $this->get_setting_value('users');
        $log = $this->get_setting_value('logs');

        $paths[] = new restore_path_element('block',        '/block', true);
        $paths[] = new restore_path_element('supervised',   '/block/supervised');
        $paths[] = new restore_path_element('classperiod',  '/block/supervised/classperiods/classperiod');
        $paths[] = new restore_path_element('classroom',    '/block/supervised/classrooms/classroom');
        if ($userinfo) {
            $paths[] = new restore_path_element('lecturer',    '/block/supervised/lecturers/lecturer');
        }
        // if need backup log info
        if ($log) {
            $paths[] = new restore_path_element('log',    '/block/supervised/logs/log');
        }


        return $paths;
    }

    public function process_block($data) {
        global $DB;
        $data = (object)$data;
        $classperiodsarr     =  array(); 
        $twinkeyidarr        =  array();
        $classroomsarr       =  array();
        $classroomsidarray   =  array();
        $userinfoflag = $this->get_setting_value('users');
        $logflag = $this->get_setting_value('logs'); 

        if (isset($data->supervised['classrooms']['classroom'])) {
            foreach ($data->supervised['classrooms']['classroom'] as $classroom) {
                $classroom = (object)$classroom;
                $params = array('number'           =>  $classroom->number, 
                                'initialvalueip'   =>  $classroom->initialvalueip,
                                'finishvalueip'    =>  $classroom->finishvalueip);
                $classroomid = $DB->get_field_select('block_supervised_classroom', 'id', 'number=:number and initialvalueip=:initialvalueip and finishvalueip=:finishvalueip', $params);
                if ($classroomid != '') {
                    $classroomsarr[] = $classroomid;
                    $classroomsidarray[$classroom->id] = $classroomid;
                // The feed doesn't exist, create it
                } else {
                    $classroomid = $DB->insert_record('block_supervised_classroom', $classroom);
                    $classroomsarr[] = $classroomid;
                    $classroomsidarray[$classroom->id] = $classroomid;
                }
            }
        }
        
        $lecureridarray = array();
        if ($userinfoflag) {
            if (isset($data->supervised['lecturers']['lecturer'])) {
                foreach ($data->supervised['lecturers']['lecturer'] as $lecturer) {
                    $lecturer = (object)$lecturer;
                    $select ='';
                    $params = array('auth'              => $lecturer->auth,
                                    'confirmed'         => $lecturer->confirmed,
                                    'policyagreed'      => $lecturer->policyagreed,
                                    'deleted'           => $lecturer->deleted,
                                    'suspended'         => $lecturer->suspended,
                                    'mnethostid'        => $lecturer->mnethostid,
                                    'username'          => $lecturer->username,
                                    'password'          => $lecturer->password,
                                    'idnumber'          => $lecturer->idnumber,
                                    'firstname'         => $lecturer->firstname,
                                    'lastname'          => $lecturer->lastname,
                                    'email'             => $lecturer->email,
                                    'emailstop'         => $lecturer->emailstop,
                                    'icq'               => $lecturer->icq,
                                    'skype'             => $lecturer->skype,
                                    'yahoo'             => $lecturer->yahoo,
                                    'aim'               => $lecturer->aim,
                                    'msn'               => $lecturer->msn,
                                    'phone1'            => $lecturer->phone1,
                                    'phone2'            => $lecturer->phone2,
                                    'institution'       => $lecturer->institution,
                                    'department'        => $lecturer->department,
                                    'address'           => $lecturer->address,
                                    'city'              => $lecturer->city,
                                    'country'           => $lecturer->country,
                                    'lang'              => $lecturer->lang,
                                    'theme'             => $lecturer->theme,
                                    'timezone'          => $lecturer->timezone,
                                    'firstaccess'       => $lecturer->firstaccess,
                                    'lastaccess'        => $lecturer->lastaccess,
                                    'lastlogin'         => $lecturer->lastlogin,
                                    'currentlogin'      => $lecturer->currentlogin,
                                    'lastip'            => $lecturer->lastip,
                                    'secret'            => $lecturer->secret,
                                    'picture'           => $lecturer->picture,
                                    'url'               => $lecturer->url,
                                    'description'       => $lecturer->description,
                                    'descriptionformat' => $lecturer->descriptionformat,
                                    'mailformat'        => $lecturer->mailformat,
                                    'maildigest'        => $lecturer->maildigest,
                                    'maildisplay'       => $lecturer->maildisplay,
                                    'htmleditor'        => $lecturer->htmleditor,
                                    'ajax'              => $lecturer->ajax,
                                    'autosubscribe'     => $lecturer->autosubscribe,
                                    'trackforums'       => $lecturer->trackforums,
                                    'timecreated'       => $lecturer->timecreated,
                                    'timemodified'      => $lecturer->timemodified,
                                    'trustbitmask'      => $lecturer->trustbitmask,
                                    'imagealt'          => $lecturer->imagealt,
                                    'screenreader'      => $lecturer->screenreader);
                    $select = /*'auth=:auth and 
                                confirmed=:confirmed and 
                                policyagreed=:policyagreed and 
                                deleted=:deleted and 
                                suspended=:suspended and 
                                mnethostid=:mnethostid and'*/ 
                                'username=:username and ' . 
                                //'password=:password and ' .
                                //'idnumber=:idnumber and '.
                                'firstname=:firstname and 
                                lastname=:lastname and
                                email=:email';/* and 
                                emailstop=:emailstop and 
                                icq=:icq and 
                                skype=:skype and
                                yahoo=:yahoo and 
                                aim=:aim and 
                                msn=:msn and 
                                phone1=:phone1 and 
                                phone2=:phone2 and 
                                institution=:institution and 
                                department=:department and 
                                address=:address and 
                                city=:city and 
                                country=:country and 
                                lang=:lang and 
                                theme=:theme and 
                                timezone=:timezone and
                                firstaccess=:firstaccess and 
                                lastaccess=:lastaccess and 
                                lastlogin=:lastlogin and 
                                currentlogin=:currentlogin and
                                lastip=:lastip and 
                                secret=:secret and 
                                picture=:picture and
                                url=:url and 
                                description=:description and 
                                descriptionformat=:descriptionformat and 
                                mailformat=:mailformat and
                                maildigest=:maildigest and  
                                maildisplay=:maildisplay and 
                                htmleditor=:htmleditor and 
                                ajax=:ajax and 
                                autosubscribe=:autosubscribe and 
                                trackforums=:trackforums and 
                                timecreated=:timecreated and 
                                timemodified=:timemodified';*/
                    //$lecturerid = $DB->get_field_select('user', 'id', $select, $params);
                    $lecturerid = $DB->get_field_select('user', 'id', 'id=:id', array('id'=>$lecturer->id));
                    if ($lecturerid != '') {
                        $lecureridarray[$lecturer->id]  = $lecturerid;
                        $params['id'] = $lecturer->id;
                        $DB->update_record('user', $params);
                    } else {
                        $lecturerid = $DB->insert_record('user', $params);
                        $lecureridarray[$lecturer->id]  = $lecturerid;
                    }
                }

                // correct id for course (correct in table mdl_course_display)
                foreach ($lecureridarray as $key=>$item) {
                    if ($item != '') {
                        $coursedisplays = $DB->get_records('course_display', array('userid'=>$key));
                        foreach ($coursedisplays as $coursedisplay) {
                            //if ($coursedisplay->userid != $item) {
                                $coursedisplay->userid = $item;
                                $DB->update_record('course_display', $coursedisplay);
                            //}
                        }
                    }
                }
            }
        }

        if ($logflag) {
            $logidarray = array();
            // restore to mdl_log
            if (isset($data->supervised['logs']['log'])) {
                foreach ($data->supervised['logs']['log'] as $log) {
                    $log = (object)$log;
                    $select = '';
                    $params = array('time'      => $log->time,
                                    'userid'    => $log->userid, 
                                    'ip'        => $log->ip, 
                                    'course'    => $log->course, 
                                    'module'    => $log->module, 
                                    'cmid'      => $log->cmid, 
                                    'action'    => $log->action, 
                                    'url'       => $log->url, 
                                    'info'      => $log->info);
                    $select = 'time=:time and userid=:userid and ip=:ip and course=:course and module=:module and cmid=:cmid and action=:action and url=:url and info=:info';
                    $logid = $DB->get_field_select('log', 'id', $select, $params);
                    if ($logid != '') {
                        $logidarray[$log->id] = $logid;
                    } else {
                        $logid = $DB->insert_record('block_supervised_classroom', $log);
                        $logidarray[$log->id] = $logid;
                    }
                }
            }
        }

        if (isset($data->supervised['classperiods']['classperiod'])) {
            foreach ($data->supervised['classperiods']['classperiod'] as $classperiod) {
                $classperiod = (object)$classperiod;
                $select = 'logid=:logid and classroomid=:classroomid and courseid=:courseid and groupid=:groupid and starttimework=:starttimework and timework=:timework and lecturerid=:lecturerid and typeaction=:typeaction and twinkeyid=:twinkeyid';
                $params = array('logid'         =>  $classperiod->logid, 
                                'classroomid'   =>  $classperiod->classroomid,
                                'courseid'      =>  $classperiod->courseid,
                                'groupid'       =>  $classperiod->groupid,
                                'starttimework' =>  $classperiod->starttimework,
                                'timework'      =>  $classperiod->timework,    
                                'lecturerid'    =>  $classperiod->lecturerid,
                                'typeaction'    =>  $classperiod->typeaction,
                                'twinkeyid'     =>  $classperiod->twinkeyid);

                $classperiodid = $DB->get_field_select('block_supervised', 'id', $select , $params);
                if ($classperiodid != '') {
                    $classperiod->classroomid   = $classroomsidarray[$classperiod->classroomid];
                    if ($logflag) {
                        $classperiod->logid         = $logidarray[$classperiod->logid];
                    }
                    if ($userinfoflag) {
                        $classperiod->lecturerid    = $lecureridarray[$classperiod->lecturerid];
                    }
                    $classperiodid = $DB->update_record('block_supervised', $classperiod);
                // The feed doesn't exist, create it
                } else {
                    $oldid = $classperiod->id;
                    $classperiod->classroomid = $classroomsidarray[$classperiod->classroomid];
                    if ($logflag) {
                        $classperiod->logid         = $logidarray[$classperiod->logid];
                    }
                    if ($userinfoflag) {
                        $classperiod->lecturerid    = $lecureridarray[$classperiod->lecturerid];
                    }
                    $classperiodid = $DB->insert_record('block_supervised', $classperiod);
                    $twinkeyidarr[] = array('oldid'=>$oldid, 'newid'=>$classperiodid);
                    $classperiodsarr[] = $classperiodid;
                }

                // write twinkey for all classperiods
                foreach ($classperiodsarr as $classperiodid) {
                    $classperiod = $DB->get_record_select('block_supervised', 'id=:id', array('id'=>$classperiodid));
                    foreach ($twinkeyidarr as $twinkey) {
                        if ($twinkey['oldid'] == $classperiod->twinkeyid) {
                            $classperiod->twinkeyid = $twinkey['newid'];
                            break;
                        }
                    }
                    $DB->update_record('block_supervised', $classperiod);
                }
            }
        }
    }
}
