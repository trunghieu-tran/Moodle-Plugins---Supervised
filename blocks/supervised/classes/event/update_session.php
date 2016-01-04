<?php
// This file is part of Student Access Control Kit - https://bitbucket.org/oasychev/moodle-plugins/overview
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
 * The supervised update session event.
 *
 * @package     block
 * @subpackage  supervised
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_supervised\event;

defined('MOODLE_INTERNAL') || die();
/**
 * The update_session event class.
 * @since     Moodle 2.7
 * @copyright 2014 Oleg Sychev, Volgograd State Technical University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
class update_session extends update_object {

    public static function get_name() {
        return get_string('eventupdatesession', 'block_supervised');
    }

    public function get_description() {
        return "The user with id '$this->userid' updated session.";
    }

    public function get_url() {
        return new \moodle_url('/blocks/supervised/sessions/view.php', array('courseid' => $this->other['courseid']));
    }

    public function get_legacy_logdata() {
        return array($this->other['courseid'], 'role', 'edit session',
        "blocks/supervised/sessions/view.php?&courseid={$this->other['courseid']}", '');
    }
}