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

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');


/**
 * Class language_editing_form
 */
class language_editing_form extends moodleform {


    public function definition() {
        global $OUTPUT;
        $mform =&$this->_form;

        $mform->addElement('hidden', 'new', 1);
        $mform->setType('new', PARAM_BOOL);
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $textfields = array(
            // Commented, because users don't care about  description
            // 'name' => PARAM_RAW,
            'uiname' => PARAM_RAW,
            'version' => PARAM_RAW,
            'lexemname' => PARAM_RAW,
        );
        foreach($textfields as $name => $type) {
            $mform->addElement('text', $name,
                get_string('language_editing_field_' . $name, 'block_formal_langs'),
                array('size' => 78),
                ''
            );
            $mform->setType($name, $type);
            if ($name != 'name') {
                $mform->addRule($name, null, 'required', null, 'client');
            }
        }

        $mform->addElement('textarea', 'description',
            get_string('language_editing_field_description', 'block_formal_langs'),
            array('cols' => 80, 'rows' => 2),
            ''
        );
        $mform->setType('description', PARAM_RAW);

        $mform->addElement('checkbox', 'visible',
            get_string('language_editing_field_visible', 'block_formal_langs'),
            ''
        );
        $mform->setType('visible', PARAM_BOOL);
        $mform->setDefault('visible', true);


        $mform->addElement('textarea', 'scanrules',
            get_string('language_editing_field_scanrules', 'block_formal_langs'),
            array('cols' => 80, 'rows' => 20),
            ''
        );
        $mform->setType('scanrules', PARAM_RAW);
        $mform->addRule('scanrules', null, 'required', null, 'client');

        $mform->addElement('textarea', 'parserules',
            get_string('language_editing_field_parserules', 'block_formal_langs'),
            array('cols' => 80, 'rows' => 20),
            ''
        );
        $mform->setType('parserules', PARAM_RAW);


        // Convert visible option to eyed checkbox
        $icon = new pix_icon('t/show', get_string('hide'));
        $hidesrc = $OUTPUT->pix_url($icon->pix, $icon->component);
        $icon = new pix_icon('t/hide', get_string('hide'));
        $showsrc = $OUTPUT->pix_url($icon->pix, $icon->component);
        $mform->addElement('html', '
            <script type="text/javascript">
                $(document).ready(function() {
                      var showsrc = "' . $showsrc . '";
                      var hidesrc = "' . $hidesrc . '";
                      var cb = $("input[name=visible]");
                      var img;
                      var parent = cb.parent();
                      cb.css("display", "none");
                      parent.html(parent.html() + "<img />");
                      img = parent.find("img");

                      var computeAndSetSourceImage = function() {
                            var cb = $("input[name=visible]");
                            var src;
                            if (cb.attr("checked"))
                                src = showsrc;
                            else
                                src = hidesrc;
                            cb.parent().find("img").attr("src", src);
                      }

                      img.click(function() {
                          var src = showsrc;
                          var cb = $("input[name=visible]");
                          if (cb.attr("checked")) {
                             cb.removeAttr("checked");
                          } else {
                             cb.attr("checked", true);
                          }
                          computeAndSetSourceImage();
                      });
                      computeAndSetSourceImage()
                });
            </script>
        ');

    }

    public function validate_defined_fields($validateonnosubmit=false) {
        $result = parent::validate_defined_fields($validateonnosubmit);
        return $result;
    }

    public function definition_after_data() {
        $new  = !array_key_exists('id', $this->_form->_defaultValues);
        $submit = ($new) ? 'language_editing_submit_add' : 'language_editing_submit_edit';
        $mform =& $this->_form;
        $buttonarray=array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string($submit, 'block_formal_langs'));
        if ($new == false) {
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('language_editing_submit_save_as_new', 'block_formal_langs'));
        }
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }
}