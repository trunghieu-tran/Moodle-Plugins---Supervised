<?

/**
 * Contains constants and functions for rendering text in images
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg, Mamontov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/../../../lib/moodlelib.php');

define('FONT' , dirname(__FILE__) . '/../../../lib/default.ttf');
define('FONT_SIZE' , 10.0);

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
    return $metrics;
}

/**
 * A metrics for data
 * @var array
 */
$metrics = qtype_correctwriting_compute_font_metrics();

/**
 * Returns text bounding box
 * @param string $text
 * @return stdClass <width, height>
 */
function qtype_correctwriting_get_text_bounding_box($text)  {
    global $metrics;
    $im = imagecreatetruecolor(1, 1);
    $bbox = imagettfbbox(FONT_SIZE, 0, FONT, $text);
    imagedestroy($im);
    $r = new stdClass();
    $r->height = $metrics['height'];
    $r->width = $bbox[2] - $bbox[0];
    return $r;
}

/**
 * Renders text on image
 * @param resource $im image
 * @param int $x left corner coordinate of image
 * @param int $y top corner coordinate of image
 * @param string $text text data
 * @param resource $color color
 */
function qtype_correctwriting_render_text(&$im, $x, $y, $text, $color) {
    global $metrics;
    imagettftext($im, FONT_SIZE, 0.0, $x, $y + $metrics['baseline'], $color, FONT, $text);
}