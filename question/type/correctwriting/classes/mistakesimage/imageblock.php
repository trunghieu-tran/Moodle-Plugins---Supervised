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
require_once(dirname(__FILE__) . '/abstractlabel.php');
/**
 * An image block to be rendered
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg, Mamontov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_correctwriting_image_block  extends qtype_correctwriting_abstract_label {
    /**
     * List of labels in block
     * @var array of qtype_correctwriting_label
     */
    public $labels;

    /**
     * Constructs list of label
     * @param $labels array of qtype_correctwriting_label
     */
    public function __construct($labels) {
        $this->labels = $labels;
    }

    /** Returns a requested size of label for drawing
     *  @return array of two coordinates width and height as array(width,height)
     */
    public function get_size() {
        global $metrics;

        $width = 0;
        $height = 0;
        $baselineoffset = 0;
        for($i = 0; $i < count($this->labels); $i++) {
            /** @var qtype_correctwriting_abstract_label $label */
            $label = $this->labels[$i];
            $label->get_size();
            $baselineoffset = max($baselineoffset, $label->get_baseline_offset());
        }
        for($i = 0; $i < count($this->labels); $i++) {
            /** @var qtype_correctwriting_abstract_label $label */
            $label = $this->labels[$i];

            $label->set_baseline_offset($baselineoffset);

            /** @var qtype_correctwriting_abstract_label $label */
            $label = $this->labels[$i];

            $lbl = $label->get_size();

            $width += $lbl[0];
            if ($i != count($this->labels) - 1) {
                $width += $metrics['width'];
            }
            $height = max($lbl[1], $height);
        }
        $this->labelsize = array($width, $height);
        $this->baselineoffset = $baselineoffset;
        return $this->labelsize;
    }

    /**
     * Sets baseline offset for all nested labels
     * @param int $offset offset
     */
    public function set_baseline_offset($offset) {
        for($i = 0; $i < count($this->labels); $i++) {
            /** @var qtype_correctwriting_abstract_label $label */
            $label = $this->labels[$i];
            $label->set_baseline_offset($offset);
        }
        $this->baselineoffset = $offset;
    }

    /** Paints a label at specific position, specified by rectangle. If it set as fixed, we paint it as red.
     *  Label is painted at center of specified rectangle on a horizontal, and on top on vertical
     *  @param resource $im image resource, where it should be painted
     *  @param array    $palette palette of colors as associtive array. Currently with colors, can be accessed as 'black', 'red'
     *  @param stdClass $currentrect rectangle, where it should be painted with fields x,y,width, height.
     *  @param bool  $bottom         whether point should placed on bottom part of rectangle, or top
     */
    public function paint(&$im, $palette, $currentrect, $bottom) {
        global $metrics;
        // Set connection point
        parent::paint($im, $palette, $currentrect, $bottom);

        $this->connection = array();

        $size = $this->get_size();
        // Compute a middle parameter at center
        $x = $currentrect->x + $currentrect->width/2 - $size[0]/2;
        for($i = 0; $i < count($this->labels); $i++) {
            /** @var qtype_correctwriting_abstract_label $label */
            $label = $this->labels[$i];
            $size = $label->get_size();
            $copy = clone $currentrect;
            $copy->x = $x;
            $copy->width = $size[0];
            $label->paint($im, $palette, $copy, $bottom);

            $this->connection[] = $copy->x + $copy->width / 2;
            // If we must place it on bottom, than place it there (because we are in Decart space).
            if ($bottom == true) {
                $this->connection[] = $currentrect->y + $currentrect->height + TINY_SPACE;
            } else {
                $this->connection[] = $currentrect->y - TINY_SPACE;
            }

            $x += $size[0];
            if ($i != count($this->labels) - 1) {
                $x += $metrics['width'];
            }
        }

    }

    /**
     * Returns top connection fixed
     * @return int offset
     */
    public function get_top_connection_offset() {
        $result = array_map(function($a) { return $a->get_top_connection_offset(); }, $this->labels);
        return min($result);
    }

    /**
     * Whether label is several lexemes, linked together
     * @return bool
     */
    public function is_several_lexemes() {
        return true;
    }
}