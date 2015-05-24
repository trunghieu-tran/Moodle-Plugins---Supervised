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

/**
 * A defines for rendering mistake image
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg, Mamontov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// A vertical space between answer and response
define('ANSWER_RESPONSE_VERTICAL_SPACE', 50);
// A horizontal space between two lexemes on image
define('ROW_HORIZONTAL_SPACE', 7);
// A tiny space between arrow connector and label
define('TINY_SPACE', 2);
// Defines a width for drawing lines of moving, removing or adding
define('LINE_WIDTH', 2);
// A line width for strikethrought
define('STRIKETHROUGH_LINE_WIDTH', 3);
// A length of arrow end part
define('ARROW_LENGTH', 5);
// A transpose arrow length
define('TRANSPOSE_ARROW_LENGTH', 10);
// An arrow angle in radians
define('ARROW_ANGLE', 0.5);
// Define a space for frame
define('FRAME_SPACE', 5);
// A thickness of drawn frame
define('FRAME_THICKNESS', 1);
// Defines a padding for absent frame
define('ABSENT_FRAME_PADDING', 2);
// Additional length for big strkethrough  inner lines
define('BIG_STRIKETHROUGH_ADDITIONAL_LENGTH', 2);
// Padding for move items
define('MOVE_GROUP_PADDING', 2);
// A missing separator width
define('MISSING_SEPARATOR_WIDTH', 4);
// A maximal arc offset for transpose
define('TRANSPONSE_MAX_ARC_OFFSET', 20);