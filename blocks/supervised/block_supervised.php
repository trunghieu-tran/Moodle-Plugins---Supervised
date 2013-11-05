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

function save_rules($quizid, $lessontypes) {
    global $DB;
    
    $oldrules = $DB->get_records('quizaccess_supervisedcheck', array(quizid=>$quizid));
    
    for ($i=0; $i<count($lessontypes); $i++) {
        // Update an existing rule if possible.
        $rule = array_shift($oldrules);
        if (!$rule) {
            $rule = new stdClass();
            $rule->quizid = $quizid;
            $rule->lessontypeid = 0;
            $rule->id = $DB->insert_record('quizaccess_supervisedcheck', $rule);
        }
        $rule->lessontypeid = $lessontypes[$i];
        $DB->update_record('quizaccess_supervisedcheck', $rule);
    }
    // Delete any remaining old rules.
    foreach ($oldrules as $oldrule) {
        $DB->delete_records('quizaccess_supervisedcheck', array('id' => $oldrule->id));
    }
}

function supervisedblock_get_logs($sessionid, $timefrom, $timeto, $userid=0) {
    global $DB;
    
    $session = $DB->get_record('block_supervised_session', array('id'=>$sessionid));
    $classroom = $DB->get_record('block_supervised_classroom', array('id'=>$session->classroomid));
    
    // Prepare query
    $params = array();
    $selector = "(l.time BETWEEN :timefrom AND :timeto) AND l.course = :courseid";
    $params['timefrom'] = $timefrom;
    $params['timeto']   = $timeto;
    $params['courseid'] = $session->courseid;
    if($userid != 0) {
        $selector .= " AND l.userid = :userid";
        $params['userid'] = $userid;
    }
    // Get logs
    $logs = get_logs($selector, $params);
    
    // Filter logs by classroom ip subnet
    $logs_filtered = array();
    foreach ($logs as $id=>$log) {
        if(address_in_subnet($log->ip, $classroom->iplist))
            $logs_filtered[$id] = $log;
    } 
        
    return $logs_filtered;
}


class block_supervised extends block_base {
    public function init() {
        $this->title = get_string('supervised', 'block_supervised');
    }

    public function get_content() {
        global $DB;

        if ($this->content !== null) {
            return $this->content;
        }
        
        
        /*///////////////////
        
        $timefrom = new DateTime();
        $timefrom->setDate(2013,10,5);
        $timefrom->setTime(17,40,00);
        $timefrom = $timefrom->getTimestamp();
        
        $timeto = new DateTime();
        $timeto->setDate(2013,10,5);
        $timeto->setTime(18,00,00);
        $timeto = $timeto->getTimestamp();
        $userid  = 2;
        $sessionid = 3;
        $logs = supervisedblock_get_logs($sessionid, $timefrom, $timeto, $userid);
        
        //////////////////////
        echo("<pre>");
        var_dump($logs);
        echo("</pre>");
        //////////////////////*/
        
        $this->content         =  new stdClass;
        $this->content->text   = 'The content of supervised block!';
        $this->content->footer = 'Footer here...';
        

        return $this->content;
    }
}