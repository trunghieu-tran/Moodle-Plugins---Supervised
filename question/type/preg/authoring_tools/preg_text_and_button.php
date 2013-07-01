<?php
/**
 * Defines button-with-text-input widget, parent of abstract poasquestion
 * text-and-button widget. This class extends parent class with javascript
 * callbacks for button clicks.
 *
 * @package    qtype_preg
 * @copyright  &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author     Pahomov Dmitry <topt.iiiii@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/question/type/poasquestion/poasquestion_text_and_button.php');

MoodleQuickForm::registerElementType('preg_text_and_button',
    $CFG->dirroot.'/question/type/preg/authoring_tools/preg_text_and_button.php',
    'MoodleQuickForm_preg_text_and_button');

class MoodleQuickForm_preg_text_and_button extends MoodleQuickForm_text_and_button {

    //private $parentjsobjname = 'M.poasquestion_text_and_button';

    private static $_preg_authoring_tools_script_included = false;

    function MoodleQuickForm_preg_text_and_button($elementName=null, $elementButtonName=null, $elementLabel=null) {
        global $CFG;
        $this->_dialog_title = get_string('authoring_tool_page_header', 'qtype_preg');
        $elementLinks = array(
                'link_to_button_image' => $CFG->wwwroot . '/theme/image.php/standard/core/1359744739/t/edit',
                'link_to_page' => $CFG->wwwroot . '/question/type/preg/authoring_tools/ast_preg_form.php'
                );
        parent::__construct($elementName, $elementButtonName, $elementLabel, $elementLinks, '90%', array('rows' => 1, 'cols' => 80));
        $this->include_preg_authoring_tools_script();
    }

    function toHtml() {
        $parenthtml = parent::toHtml();
        return $parenthtml;
    }

    private function include_preg_authoring_tools_script() {
        global $CFG;
        global $PAGE;
        if(self::$_preg_authoring_tools_script_included===false) {
            $jsmodule = array(  'name' => 'preg_authoring_tools_script',
                                'fullpath' => '/question/type/preg/authoring_tools/preg_authoring_tools_script.js'
            );
            $jsargs = array(
                $CFG->wwwroot,
                'todo - poasquestion_text_and_button_objname',
            );
            $PAGE->requires->js_init_call('M.preg_authoring_tools_script.init', $jsargs, true, $jsmodule);
            self::$_preg_authoring_tools_script_included=true;
        }
    }
}
