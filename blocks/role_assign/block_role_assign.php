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
require_once('lib.php');
/**
 * Block role_assign class definition.
 *
 * This block can be added to a course page to add/edit/display rules, which can be used for automatic changing role
 */
class block_role_assign extends block_base {
    /**
     * Set the initial properties for the block
     */
    public function init() {
        $this->title = get_string('role_assign', 'block_role_assign');
    }

    /**
     * Display the contents of the block
     */
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        global $COURSE, $DB, $USER, $PAGE, $OUTPUT;

        $this->content = new stdClass;

        // Display lin "current rules".
        if (has_capability('block/role_assign:viewcurrentrules', $PAGE->context)) {
            $currentrules = new moodle_url('/blocks/role_assign/current_rules.php', array('courseid' => $COURSE->id));
            $this->content->text .= html_writer::link($currentrules, get_string('currentrules', 'block_role_assign'));
            $this->content->text .= $OUTPUT->help_icon('currentrules', 'block_role_assign');
            $this->content->text .= "<br/>";
        }
        // Display link "active rules".
        if (has_capability('block/role_assign:viewactiverules', $PAGE->context)) {
            $currentrules = new moodle_url('/blocks/role_assign/active_rules.php', array('courseid' => $COURSE->id));
            $this->content->text .= html_writer::link($currentrules, get_string('activerules', 'block_role_assign'));
            $this->content->text .= $OUTPUT->help_icon('activerules', 'block_role_assign');
            $this->content->text .= "<br/>";
        }
        // Display current users role.
        $this->content->text .= get_string('currentrole', 'block_role_assign').': ';
        $this->content->text .= "<br/><span id='cur_role'>";
        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        if ($roles = get_user_roles($context, $USER->id)) {
            foreach ($roles as $role) {
                if (empty($role->name)) {
                    $this->content->text .= $role->shortname."<br/>";
                } else {
                    $this->content->text .= $role->name."<br/>";
                }
            }
        }
        $this->content->text .= '</span>';
        $PAGE->requires->js_init_call('M.block_role_assign.check_role', array('cur_role', $COURSE->id, $USER->id));
        $PAGE->requires->js('/blocks/role_assign/module.js');
        return $this->content;
    }
}   