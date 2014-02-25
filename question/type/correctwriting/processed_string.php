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

class qtype_correctwriting_proccesedstring extends block_formal_langs_processed_string {

    // Enumerations description in correct answer.
    protected $enums_description = null;

    public function __set($name, $value) {
        $settertable = array('string' => 'set_string', 'stream' => 'set_stream', 'syntaxtree' => 'set_syntax_tree');
        $settertable['descriptions'] = 'set_descriptions';
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
        $gettertable = array('string' => 'get_string', 'stream' => 'get_stream', 'syntaxtree' => 'get_syntax_tree');
        $gettertable['descriptions'] = 'node_descriptions_list';
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
        $getters = array('string', 'stream', 'syntaxtree', 'descriptions', 'enumeration');
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

    /**
     *  Returns a stream of tokens.
     *  @return stream of tokens
     */
    private function get_stream() {
        if ($this->tokenstream == null)
            $this->language->scan($this);
        return $this->tokenstream;
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
