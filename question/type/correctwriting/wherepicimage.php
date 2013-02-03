<?
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
 * Where pic image generator, used in correctwriting to show where pic image hint
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg, Mamontov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Used font size
define('FONT_SIZE', 4);
// Defines a width for drawing lines of moving, removing or adding
define('LINE_WIDTH', 2);
// Defines a border between label and image border on X axis
define('IMAGE_LABEL_BORDER_X', 4);
// Defines a border between label and image border on Y axis
define('IMAGE_LABEL_BORDER_Y', 4);
// Defines an insertion mark width
define('INSERTION_MARK_WIDTH', 10);
// Defines an insertion mark height
define('INSERTION_MARK_HEIGHT', 10);
// Defines an insertion mark, how it must be offset on y
define('INSERTION_MARK_Y_OFFSET', 2);
// Defines, a space between insertion mark and inserted label
define('INSERTION_MARK_TOP_PADDING', 2);
// Defines  a space, where marked label should be placed
define('INSERTION_MARK_LABEL_PADDING', 2);
// A length of arrow end part
define('ARROW_LENGTH', 5);
// An arrow angle in radians
define('ARROW_ANGLE', 0.5);
// A space between arrow langind point and labels (used for moving lexeme)
define('ARROW_INSERT_PADDING', 2);
// A padding between middle part of token and starting of arrow for moving lexeme
define('ARROW_TOP_PADDING', 2);
// A padding for moving mistake height
define('MOVING_LINE_HEIGHT', 2);


/**
 * Decodes a wherepic request data
 */
class qtype_correctwriting_wherepic_data_decoder {

    /**
     * Decodes a wherepic hint image
     * @param string $data  image request data
     * @param bool $success  whether decoding was successfull
     * @return array data from wherepic
     */
    public static function decode($data, &$success) {
        $result = array();
        $success = false;
        if (strlen($data) != 0) {
            $data = base64_decode($data);
            if ($data != false) {
                $data = explode(',,,', $data);
                if (count($data) >= 5) {
                    if ($data[0] == 'absent') {
                        $success = true;
                        $result['type'] = $data[0];
                        $result['token'] = base64_decode($data[1]);
                        $result['insert_position'] = $data[2];
                        $result['insert_relative'] = $data[3];
                        $result['response'] = array_map('base64_decode', explode('|', $data[4]));
                    }
                    if ($data[0] == 'moved') {
                        $success = true;
                        $result['type'] = $data[0];
                        $result['source_position'] = $data[1];
                        $result['insert_position'] = $data[2];
                        $result['insert_relative'] = $data[3];
                        $result['response'] = array_map('base64_decode', explode('|', $data[4]));
                    }
                }
            }
        }
        return $result;
    }
}

$success = true;
$result = qtype_correctwriting_wherepic_data_decoder::decode($_REQUEST['data'], $success);

if ($success) {
    print_r($result);
} else {
    echo 'Error: malformed data!';
}

