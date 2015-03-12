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
 * Defines a page for editing language
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Mamontov Dmitriy Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package blocks
 */

require_once('../../config.php');
require_once($CFG->libdir.'/accesslib.php');
require_once($CFG->dirroot.'/blocks/formal_langs/block_formal_langs.php');

global $USER;

$url = new moodle_url('/blocks/formal_langs/globalsettings.php');

require_login();
$PAGE->requires->jquery();
$PAGE->set_url($url);
$context = context_system::instance();
$PAGE->set_context($context);

$PAGE->set_pagelayout('admin');

$heading = get_string('formallangsglobalsettings', 'block_formal_langs');
$PAGE->set_title($heading);
$PAGE->set_heading($heading);
$PAGE->navbar->add($heading);

require_capability('moodle/site:config', $context);
$permissions = block_formal_langs::build_visibility_for_all_languages(array( $context->id ));
$block = new block_formal_langs();
$block->page = $PAGE;
$block->context = $context;
$block->instance = (object)array('id' => 0);
$pagecontent =  $block->formatted_contents($OUTPUT);

echo $OUTPUT->header();

$contentdiv = html_writer::tag('div', $pagecontent, array('class' => 'content'));
echo html_writer::tag(
    'div',
    $contentdiv,
    array('class'=> 'block_formal_langs  block list_block block_with_controls yui3-dd-drop')
);
echo $OUTPUT->footer();
