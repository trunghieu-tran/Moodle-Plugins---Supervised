// This file is part of Student Access Control Kit - https://code.google.com/p/oasychev-moodle-plugins/
//
// Access Control Kit is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Access Control Kit is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     quizaccess_supervisedcheck
 * @author      Andrey Ushakov <andrey200964@yandex.ru>
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$(document).ready(function(){
    function updateLessontypes(){
        if ($('#id_supervisedmode_2').is(':checked')) {
            $('#fgroup_id_lessontypesgroup :input').removeAttr('disabled');
        } else {
            $('#fgroup_id_lessontypesgroup :input').attr('disabled', true);
        }
    }

    $('#fgroup_id_radioar :input').change(
        function(){
            updateLessontypes();
        }
    );

    updateLessontypes();
});