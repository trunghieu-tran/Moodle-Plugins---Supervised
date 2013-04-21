<?php
/**
 * Defines authors tool widgets class.
 *
 * @copyright &copy; 2012  Terechov Grigory
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

//defined('MOODLE_INTERNAL') || die();

global $CFG;
global $PAGE;
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/form/text.php');

MoodleQuickForm::registerElementType('text_and_button',
    $CFG->dirroot.'/question/type/poasquestion/poasquestion_text_and_button.php',
    'MoodleQuickForm_text_and_button');

class MoodleQuickForm_text_and_button extends MoodleQuickForm_text{

    /** @var string html for help button, if empty then no help */
    var $_helpbutton = '';

    /** @var bool if true label will be hidden */
    var $_hiddenLabel = false;

    var $linkonpage = '';

    var $linkonbuttonimage = '';

    var $idbutton = '';

    private $_btn_id_postfix = '_btn';

    private $_jsmodule = null;

    /**
     * constructor
     * TODO : nojs activity (may be input shold be replace with <a> ?)
     * @param string $elementName (optional) name of the text field
     * @param string $elementButtonName (optional) name of the button
     * @param string $elementLabel (optional) text field label
     * @param array $elementLinks (optional) link on button image and link on new page
     * @param array $attributes (optional) Either a typical HTML attribute string or an associative array
     */
    function MoodleQuickForm_text_and_button($elementName=null, $elementButtonName=null, $elementLabel=null, $elementLinks=null, $attributes=null) {
        global $PAGE;
        if ($attributes === null) {
            $attributes = array();
        }
        if (!array_key_exists('width', $attributes)) {
            $attributes['width'] = '1000px';
        }
        parent::MoodleQuickForm_text($elementName, $elementLabel, $attributes);
        $this->idbutton = $elementButtonName;
        $this->linkonpage = $elementLinks['link_on_page'];
        $this->linkonbuttonimage = $elementLinks['link_on_button_image'];

        $this->_jsmodule = array('name'     => 'poasquestion_text_and_button',
                                'fullpath' => '/question/type/poasquestion/poasquestion_text_and_button.js',
                                'requires' => array('node', 'panel', 'node-load', 'get', 'io-xdr', 'substitute'));
        $PAGE->requires->js_init_call('M.poasquestion_text_and_button.init', null, true, $this->_jsmodule);

    }

    function getInputId() {
        return $this->getAttribute('id');
    }

    function getButtonId() {
        return $this->getAttribute('id') . $this->_btn_id_postfix;
    }

    function getWidth() {
        return $this->getAttribute('width');
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
            $this->getInputId(),
            $this->getWidth()
        );
        //var_dump($jsargs);
        $PAGE->requires->js_init_call('M.poasquestion_text_and_button.set_handler', $jsargs, true, $this->_jsmodule);

        return $parenthtml . '<input type="image" src="' . $this->linkonbuttonimage . '" name="button_'. $this->getInputId() . '" id="' . $this->getButtonId() .'" />';
        //return parent::toHtml() . '<input type="image" src="' . $this->linkonbuttonimage . '" name="button_'. $this->idbutton . '" id="id_button_' . $this->idbutton . '">';
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
    function setHelpButton($helpbuttonargs, $function='helpbutton'){
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
