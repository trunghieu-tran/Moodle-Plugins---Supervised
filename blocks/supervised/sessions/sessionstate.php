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
 * Class StateSession
 *
 * Describes possible session states
 */
class StateSession
{
    const PLANNED   = 1;
    const ACTIVE    = 2;
    const FINISHED  = 3;

    /**
     * Converts session state to string
     *
     * @param $val  integer session state
     * @return string string representation of the session state
     */
    public static function get_state_name($val) {
        switch($val) {
            case 1:
                return get_string('plannedstate', 'block_supervised');
                break;
            case 2:
                return get_string('activestate', 'block_supervised');
                break;
            case 3:
                return get_string('finishedstate', 'block_supervised');
                break;
            default:
                return get_string('unknownstate', 'block_supervised');
        }
    }
}