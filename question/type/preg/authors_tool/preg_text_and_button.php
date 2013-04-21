<?php
/**
 * Defines button-with-text-input widget, parent of abstract poasquestion
 * text-and-button widget. This class extends parent class with javascript
 * callbacks for button clicks.
 *
 * @package    qtype_preg
 * @copyright  &copy; 2013 Pahomov Dmitry <topt.iiiii@gmail.com>
 * @author     Pahomov Dmitry <topt.iiiii@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/question/type/poasquestion/poasquestion_text_and_button.php');

MoodleQuickForm::registerElementType('preg_text_and_button',
    $CFG->dirroot.'/question/type/preg/authors_tool/preg_text_and_button.php',
    'MoodleQuickForm_preg_text_and_button');

class MoodleQuickForm_preg_text_and_button extends MoodleQuickForm_text_and_button {

    //private $parentjsobjname = 'M.poasquestion_text_and_button';

    function MoodleQuickForm_preg_text_and_button($elementName=null, $elementButtonName=null, $elementLabel=null, $elementLinks=null, $attributes=null) {
        global $CFG;
        if ($attributes === null) {
            $attributes = array();
        }
        $attributes['width'] = '90%';
        $elementLinks = array(
                'link_on_button_image' => $CFG->wwwroot . '/theme/image.php/standard/core/1359744739/t/edit',
                'link_on_page' => $CFG->wwwroot . '/question/type/preg/authors_tool/ast_preg_form.php'
                );
        parent::__construct($elementName, $elementButtonName, $elementLabel, $elementLinks, $attributes);
    }

    function toHtml() {
        global $CFG;
        global $PAGE;
        $parenthtml = parent::toHtml();
        $jsmodule = array(  'name' => 'preg_text_and_button',
                            'fullpath' => '/question/type/preg/authors_tool/preg_authors_tool_script.js',
                            'requires' => array('node', 'io-base')
        );
        $jsargs = array(
            $CFG->wwwroot,
            'todo',
        );
        $PAGE->requires->js_init_call('M.preg_authors_tool_script.init', $jsargs, true, $jsmodule);

        return $parenthtml;
    }

}
