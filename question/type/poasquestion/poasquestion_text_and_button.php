<?php
// This file is part of Poasquestion question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Poasquestion question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines authors tool widgets class.
 *
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author Pahomov Dmitry, Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

//defined('MOODLE_INTERNAL') || die();

global $CFG;
global $PAGE;
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/form/textarea.php');

MoodleQuickForm::registerElementType('text_and_button',
    $CFG->dirroot.'/question/type/poasquestion/poasquestion_text_and_button.php',
    'MoodleQuickForm_text_and_button');

class MoodleQuickForm_text_and_button extends MoodleQuickForm_textarea{

    /** @var string html for help button, if empty then no help */
    var $_helpbutton = '';

    /** @var bool if true label will be hidden */
    var $_hiddenLabel = false;

    var $linktopage = '';

    var $linktobuttonimage = '';

    var $idbutton = '';

    private $_btn_id_postfix = '_btn';

    private $_jsmodule = null;

    protected $_dialog_title = 'someone forget to set dialog title :(';

    private static $_poasquestion_text_and_button_included = false;

    /**
     * Constructor
     * @param string $elementName (optional) name of the text field
     * @param string $elementButtonName (optional) name of the button
     * @param string $elementLabel (optional) text field label
     * @param array $elementLinks (optional) link on button image and link on new page
     * @param array $attributes (optional) Either a typical HTML attribute string or an associative array
     */
    function MoodleQuickForm_text_and_button($elementName=null, $elementButtonName=null, $elementLabel=null, $elementLinks=null, $dialogWidth=null, $attributes=null) {
        global $PAGE;
        parent::MoodleQuickForm_textarea($elementName, $elementLabel, $attributes);
        $this->idbutton = $elementButtonName;
        $this->linktopage = $elementLinks['link_to_page'];
        $this->linktobuttonimage = $elementLinks['link_to_button_image'];
        if($dialogWidth===null) {
            $dialogWidth = '90%';
        }
        $this->_jsmodule = array('name'     => 'poasquestion_text_and_button',
                                'fullpath' => '/question/type/poasquestion/poasquestion_text_and_button.js');
        $jsargs = array(
            $dialogWidth,
            $this->_dialog_title
        );
        $PAGE->requires->jquery();
        $PAGE->requires->jquery_plugin('ui');
        $PAGE->requires->jquery_plugin('ui-css');
        if(self::$_poasquestion_text_and_button_included===false) {
            $PAGE->requires->js_init_call('M.poasquestion_text_and_button.init', $jsargs, true, $this->_jsmodule);
            self::$_poasquestion_text_and_button_included=true;
        }
    }

    function getInputId() {
        return $this->getAttribute('id');
    }

    function getButtonId() {
        return $this->getAttribute('id') . $this->_btn_id_postfix;
    }

    /**
     * Sets label to be hidden
     *
     * @param bool $hiddenLabel sets if label should be hidden
     */
    function setHiddenLabel($hiddenLabel) {
        $this->_hiddenLabel = $hiddenLabel;
    }

    /**
     * Returns HTML for this form element.
     *
     * @return string
     */
    function toHtml() {
        global $CFG;
        global $PAGE;
        //var_dump($CFG);
        $parenthtml = parent::toHtml();

        $jsargs = array(
            $this->getButtonId(),
            $this->getInputId()
        );

        $PAGE->requires->js_init_call('M.poasquestion_text_and_button.set_handler', $jsargs, true, $this->_jsmodule);
        return $parenthtml . '<a href="#" name="button_'. $this->getInputId() . '" id="' . $this->getButtonId() . '" >' .
                                 '<img src="' . $this->linktobuttonimage . '" />' .
                             '</a>';
    }

    /**
     * set html for help button
     *
     * @param array $helpbuttonargs array of arguments to make a help button
     * @param string $function function name to call to get html
     * @deprecated since Moodle 2.0. Please do not call this function any more.
     * @todo MDL-31047 this api will be removed.
     * @see MoodleQuickForm::setHelpButton()
     */
    function setHelpButton($helpbuttonargs, $function='helpbutton') {
        debugging('component setHelpButton() is not used any more, please use $mform->setHelpButton() instead');
    }

    /**
     * get html for help button
     *
     * @return string html for help button
     */
    function getHelpButton(){
        return $this->_helpbutton;
    }

    /**
     * get html for help button
     *
     * @return string html for help button
     */
    function getElementTemplateType(){
        if ($this->_flagFrozen){
            return 'static';
        } else {
            return 'default';
        }
    }
}
