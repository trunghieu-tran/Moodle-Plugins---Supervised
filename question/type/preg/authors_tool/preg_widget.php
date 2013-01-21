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
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/form/text.php');

class MoodleQuickForm_text_button extends MoodleQuickForm_text{
    
    /** @var string html for help button, if empty then no help */
    var $_helpbutton = '';

    /** @var bool if true label will be hidden */
    var $_hiddenLabel = false;
    
    var $linkonpage = '';
    
    var $linkonbuttonimage = '';
    
    var $idbutton = '';
    
    /**
     * constructor
     *
     * @param string $elementName (optional) name of the text field
     * @param string $elementButtonName (optional) name of the button
     * @param string $elementLabel (optional) text field label
     * @param array $elementLinks (optional) link on button image and link on new page
     * @param array $attributes (optional) Either a typical HTML attribute string or an associative array
     */
    function MoodleQuickForm_text_button($elementName=null, $elementButtonName=null, $elementLabel=null, $elementLinks=null, $attributes=null) {
        $this->idbutton .= $elementButtonName;
        $this->linkonpage .= $elementLinks['link_on_page'];
        $this->linkonbuttonimage .= $elementLinks['link_on_button_image'];
        
        parent::MoodleQuickForm_text($elementName, $elementLabel, $attributes);
    }
    
    /**
     * Sets label to be hidden
     *
     * @param bool $hiddenLabel sets if label should be hidden
     */
    function setHiddenLabel($hiddenLabel){
        $this->_hiddenLabel = $hiddenLabel;
    }
    
    /**
     * Returns HTML for this form element.
     *
     * @return string
     */
    function toHtml(){
        return parent::toHtml() . '<input type="image" src="' . $this->linkonbuttonimage . '" name="button_'. $this->idbutton . '" id="' . $this->getAttribute('id') . '_test">';
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
