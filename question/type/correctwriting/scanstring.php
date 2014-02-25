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
 * Defines a scanning string  options
 *
 * Abstract analyzer class defines an interface any analyzer should implement.
 * Analyzers have state, i.e. for each analyzed pair of strings there will be differrent analyzer
 *
 * @copyright &copy; 2013  Oleg Sychev
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
define('AJAX_SCRIPT', true);
require_once('../../../config.php');
require_once($CFG->dirroot . '/blocks/formal_langs/block_formal_langs.php');
require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->libdir  . '/filelib.php');
require_once($CFG->libdir  . '/formslib.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/correctwriting/edit_correctwriting_form.php');

$PAGE->set_context(context_system::instance());
require_login();

$langid  =  required_param('lang', PARAM_INT);
$text = required_param('scannedtext', PARAM_RAW);

$language = block_formal_langs::lang_object($langid);

if ($language == null) {
    echo '{"tokens": [], "errors": ""}';
} else {
    $stream = $language->create_from_string($text)->stream;
    $tokens = $stream->tokens;
    if(count($tokens)) {
        $tokenvalues = array();
        foreach($tokens as $token) {
            $tokenvalues[] = (string)($token->value());
        }
        $form = 'qtype_correctwriting_edit_form';
        $errormessages = $form::convert_tokenstream_errors_to_formatted_messages($text, $stream);
        $result = (object)array('tokens' => $tokenvalues, "errors" => $errormessages);
        echo json_encode($result);
    } else {
        echo '{"tokens": [], "errors": ""}';
    }
}