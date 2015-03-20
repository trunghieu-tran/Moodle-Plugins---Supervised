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
 * Contains constants and functions for rendering text in images
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2013 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/../../../lib/moodlelib.php');

//define('FONT' , dirname(__FILE__) . '/../../../lib/default.ttf');
define('FONT' , dirname(__FILE__) . '/fonts/PTM75F.ttf');

define('FONT_SIZE' , 17.0);

/**
 * Computes font metrics for specified font and size
 * @return array
 */
function qtype_correctwriting_compute_font_metrics() {
    $im = imagecreatetruecolor(1, 1);
    $withoutanything = imagettfbbox(FONT_SIZE, 0, FONT, 'a');
    $withoutanythingheight = $withoutanything[0] - $withoutanything[7];
    $withcapheight = imagettfbbox(FONT_SIZE, 0, FONT, 'ABCDEFGHI');
    $withcapheightheight = $withcapheight[0] - $withcapheight[7];
    $withdescentheight = imagettfbbox(FONT_SIZE, 0, FONT, 'gjpqy');
    $withdescentheightheight = $withdescentheight[0] - $withdescentheight[7];
    imagedestroy($im);
    $metrics = array();
    $metrics['commonheight'] =  $withoutanythingheight;
    $metrics['capheight'] = $withcapheightheight - $withoutanythingheight;
    $metrics['descentheight'] = $withdescentheightheight - $withoutanythingheight;
    $metrics['baseline'] = $metrics['capheight'] + $withoutanythingheight;
    $metrics['height'] = $metrics['baseline'] + $metrics['descentheight'];
    $metrics['widthcache'] = array();
    $wbox = imagettfbbox(FONT_SIZE, 0, FONT, 'w');
    $mbox = imagettfbbox(FONT_SIZE, 0, FONT, 'm');
    $metrics['width'] = max($wbox[2] - $wbox[0], $mbox[2] - $mbox[0]);
    $wbox = imagettfbbox(FONT_SIZE, 0, FONT, 'W');
    $mbox = imagettfbbox(FONT_SIZE, 0, FONT, 'M');
    $metrics['width'] = max($metrics['width'], $wbox[2] - $wbox[0], $mbox[2] - $mbox[0]);
    return $metrics;
}

/**
 * A metrics for data
 * @var array
 */
global $metrics;
$metrics = qtype_correctwriting_compute_font_metrics();


function qtype_correctwriting_letter_width($text) {
    global $metrics;
    if (array_key_exists($text, $metrics['widthcache'])) {
        return $metrics['widthcache'][$text];
    }
    $im = imagecreatetruecolor(1, 1);
    $wbox = imagettfbbox(FONT_SIZE, 0, FONT, $text);
    $metrics['widthcache'][$text] = $wbox[2] - $wbox[0] + TINY_SPACE;
    imagedestroy($im);
    return $metrics['widthcache'][$text];
}

/**
 * Returns text bounding box
 * @param string $text
 * @return stdClass <width, height>
 */
function qtype_correctwriting_get_text_bounding_box($text)  {
    global $metrics;
    // Now, we render this font as monospace
    // This is required to make transpose be rendered properly.
    // Yeah, it's not very good, but how you supposed to render
    // nice arc with radius of 1.5px and have an arrow attached
    // to it.
    $r = new stdClass();
    $r->height = $metrics['height'];
    $r->width = 0;
    for($i = 0; $i < core_text::strlen($text); $i++) {
        $letter = core_text::substr($text, $i, 1);
        $nominalletterwidth = qtype_correctwriting_letter_width($letter);
        $r->width += $nominalletterwidth;
    }
    return $r;
}

/**
 * Returns kerning for two strings
 * @param string $a first string
 * @param string $b second string
 * @return int kerning
 */
function qtype_correctwriting_compute_kerning($a, $b) {
    // Since we switched to monospace rendering, we do not need the kerning
    return 0;
}

/**
 * Renders text on image
 * @param resource $im image
 * @param int $x left corner coordinate of image
 * @param int $y top corner coordinate of image
 * @param string $text text data
 * @param int $color color
 */
function qtype_correctwriting_render_text(&$im, $x, $y, $text, $color) {
    global $metrics;
    $length = core_text::strlen($text);
    for($i = 0; $i < $length; $i++) {
        $letter = core_text::substr($text, $i, 1);
        $nominalletterwidth = qtype_correctwriting_letter_width($letter);
        $bbox = imagettfbbox(FONT_SIZE, 0, FONT, $letter);
        $letterwidth = $bbox[2] - $bbox[0];
        imagettftext($im, FONT_SIZE, 0.0, $x + ($nominalletterwidth - $letterwidth) / 2.0, $y + $metrics['baseline'], $color, FONT, $letter);
        $x += $nominalletterwidth;
    }
}
