<?php

// This file is part of CorrectWriting question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// CorrectWriting question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// CorrectWriting is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with CorrectWriting.  If not, see <http://www.gnu.org/licenses/>.

require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');

class qtype_correctwriting_processed_string extends block_formal_langs_processed_string {

    // Enumerations description in correct answer.
    protected $enums_description = null;

    public function __set($name, $value) {
        $isset = parent::__isset($name);
        if ($isset) {
            parent::__set($name, $value);
            return;
        }
        $settertable = array();
        $settertable['enumerations'] = 'set_enums_descriptions';

        if (array_key_exists($name, $settertable)) {
            $method = $settertable[$name];
            $this->$method($value);
        } else {
            $trace = debug_backtrace();
            $error  = 'Unknown property: ' . $name . ' in file: ' . $trace[0]['file'] . ', line: ' . $trace[0]['line'];
            trigger_error($error, E_USER_NOTICE);
        }
    }

    public function __get($name) {
        $isset = parent::__isset($name);
        if ($isset) {
            return parent::__get($name);
        }
        $gettertable = array();
        $gettertable['enumerations'] = 'node_enums_descriptions';

        if (array_key_exists($name, $gettertable)) {
            $method = $gettertable[$name];
            return $this->$method();
        } else {
            $trace = debug_backtrace();
            $error  = 'Unknown property: ' . $name . ' in file: ' . $trace[0]['file'] . ', line: ' . $trace[0]['line'];
            trigger_error($error, E_USER_NOTICE);
        }
    }

    public function __isset($name) {
        $result = parent::__isset($name);
        if ($result) {
            return $result;
        }   
        $getters = array('enumerations');
        return in_array($name, $getters);
    }

    // Assign $enum_description field by $description
    protected function set_enums_descriptions($description) {
        $this->enums_description = $description;
    }

    //  Return $enum_description field.
    public function node_enums_descriptions() {
        return $this->enums_description;
    }

    public function __clone() {	
        $this->tokenstream = clone $this->tokenstream;
        if($this->enums_description!=null) {
            foreach ($this->enums_description as $i=>$enumeration) {
                foreach ($enumeration as $j=>$element) {
                    $this->enums_description[$i][$j] = clone $this->enums_description[$i][$j];
                }
                $j++;
            }
        }
    }
}
