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


class block_supervised extends block_base {
    public function init() {
        $this->title = get_string('blocktitle', 'block_supervised');
    }

    /**
    */
    public function applicable_formats() {
        return array(
            'all' => false,
            'course-view' => true);
    }

    public function get_content() {
        global $DB;
        global $COURSE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content         =  new stdClass;
        $this->content->text   = 'The content of supervised block!';

        
        $classroomsurl = new moodle_url('/blocks/supervised/classrooms/view.php', array('courseid' => $COURSE->id));
        $links[] = html_writer::link($classroomsurl, get_string('classroomsurl', 'block_supervised'));
        
        $lessontypesurl = new moodle_url('/blocks/supervised/lessontypes/view.php', array('courseid' => $COURSE->id));
        $links[] = html_writer::link($lessontypesurl, get_string('lessontypesurl', 'block_supervised'));

        $this->content->footer = join(' ', $links);

        return $this->content;
    }
}