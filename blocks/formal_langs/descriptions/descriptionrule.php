<?php
// This file is part of Formal Languages block - https://code.google.com/p/oasychev-moodle-plugins/
//
// Formal Languages block is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Formal Languages block is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Formal Languages block.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A main class of block
 *
 * @package    formal_langs
 * @copyright  2012 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** A rule for generating a description for a node of AST
 *
 */
class block_formal_langs_description_rule {
    /**
     * Left part of rules
     * @var string
     */
    public $left;
    /**
     * Right part of rules as string
     * @var array
     */
    public $right;

    /**
     * Constructs new description rule
     * @param string $left left part of rule
     * @param string $right right right part of rule
     */
    public function __construct($left, $right) {
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * Returns left part of description rule
     */
    public function left_part() {
        return $this->left;
    }

    /**
     * Returns right part of description rule
     * @return array
     */
    public function right_part() {
        return $this->right;
    }
}
