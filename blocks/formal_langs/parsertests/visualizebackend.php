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
 * A backend for parsing string
 *
 * @package    formal_langs
 * @copyright  2012 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('AJAX_SCRIPT', true);
require_once('../../../config.php');
require_once($CFG->libdir.'/accesslib.php');
require_once($CFG->dirroot .'/blocks/formal_langs/language_cpp_parseable_language.php');

global $USER;

require_login();


$text  = optional_param('text', '', PARAM_RAW);

$namespacetree = array(
    'vector' => array(

    ),
    'std' => array(
        'vector' => array(
            'inner' => array(
            )
        )
    )
);
/**
 * @param array|block_formal_langs_token_base $node a nodes
 * @param int $parent parent node (-1 if not found)
 * @param int $maxkey
 * @return array
 */
function node_to_array($node, $parent, &$maxkey)
{
    $result = array();
    if ($node == null) {
        return $result;
    }
    if (is_array($node)) {
        foreach($node as $i => $nodechild) {
           $tmp = node_to_array($nodechild, $parent, $maxkey);
            if (count($result)) {
                if (count($tmp)) {
                    $result = array_merge($result, $tmp);
                }
            } else {
                $result = $tmp;
            }
        }
        return $result;
    }

    if (!method_exists($node, 'type')) {
        $result = array(array('key' => $maxkey, 'name' => 'Not a node!'));
        if ($parent >= 0) {
            $result[0]['parent'] = $parent;
        }
        $maxkey += 1;
        return $result;
    }

    if (is_a($node, 'block_formal_langs_token_base')) {
        $value = $node->value();
        $result = array(array('key' => $maxkey, 'name' => (string)($value)));
        if ($parent >= 0) {
            $result[0]['parent'] = $parent;
        }
        $maxkey += 1;
        return $result;
    }

    if (count($node->childs()))  {
        $result = array(array('key' => $maxkey, 'name' => (string)($node->type())));
        if ($parent >= 0) {
            $result[0]['parent'] = $parent;
        }
        $mykey = $maxkey;
        $maxkey += 1;
        foreach($node->childs() as $child) {
            $tmp = node_to_array($child, $mykey, $maxkey);
            if (count($tmp)) {
                $result = array_merge($result, $tmp);
            }
        }

    }
    return $result;
}


if (core_text::strlen($text)) {
    $lang = new block_formal_langs_language_cpp_parseable_language();
    $lang->parser()->setNamespaceTree($namespacetree);
    $result = $lang->create_from_string($text);
    $maxkey = 0;
    $tree = $result->syntaxtree;
    $array = node_to_array($tree, -1, $maxkey);
    echo json_encode($array);
} else {
    echo "[]";
}
