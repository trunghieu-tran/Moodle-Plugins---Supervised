<?php
/**
 * Defines button-with-text-input widget, parent of abstract poasquestion
 * text-and-button widget. This class extends parent class with javascript
 * callbacks for button clicks.
 *
 * @package    qtype_preg
 * @copyright  &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/form/textarea.php');

MoodleQuickForm::registerElementType('preg_textarea',
    $CFG->dirroot.'/question/type/preg/authoring_tools/preg_textarea.php',
    'MoodleQuickForm_preg_textarea');

class MoodleQuickForm_preg_textarea extends MoodleQuickForm_textarea {


    function MoodleQuickForm_preg_textarea($elementName=null, $elementLabel=null, $attributes=null) {
        parent::MoodleQuickForm_textarea($elementName, $elementLabel, $attributes);
    }

    /**
     * Returns HTML for this form element.
     *
     * @return string
     */
    function toHtml() {
        return '<div style="width:100%; display:inline-block">' .
                    parent::toHTML() .
                '&nbsp;<div style="display:inline-block" id="id_test_regex" class="que"></div></div>';
    }
}
