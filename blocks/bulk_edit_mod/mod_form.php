<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
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
 * A block which Bulk Edit Instances
 *
 * @package    bulk_edit_mod
 * @copyright  Bastrykin Sergey
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

class mod_bulkedit_mod_form extends moodleform_mod {

    protected $modform;

    function mod_bulkedit_mod_form(&$modform, $current, $section, $cm, $course) {
        $this->set_form($modform);
        parent::moodleform_mod($current, $section, $cm, $course);

        // We delete the button "Save and view" from the form
        foreach ($this->modform->_form->_elements as $element) {
            if ($element->getName() == 'buttonar') {
                unset($element->_elements[1]);
            }
        }
        // We change the script address on which the form will go
        $modform->_form->updateAttributes(array('action' => 'step4.php'));
    }

    /**
     * The method adds the hidden element on the form
     *
     * @param string $name This is a name element
     * @param array $array Array of values
     * @param string $type This is a type element
     *
     */
    function add_hidden_element($name, $array, $type) {
        $mform = &$this->is_form()->_form;
        if (is_array($array)) {
            foreach ($array as $key => $element) {
                $mform->addElement('hidden', $name.'['.$key.']', $element);
                $mform->setType($name.'['.$key.']', $type);
            }
        }
    }

    /**
     * The method installs object of the form in the current form
     *
     * @param object $modform This is a form
     *
     */
    function set_form(&$modform) {
        $this->modform = $modform;
    }

    /**
     * The method installs object of the form in the current form
     *
     * @param object $modform This is a form
     *
     */
    function set_data($data) {
        $this->modform->set_data($data);
    }
    /**
     * The method returns the installed form in the current form
     *
     * @return object This is a form
     *
     */
    function &is_form() {
        return $this->modform;
    }

    function definition() {
    }

    function data_preprocessing(&$default_values) {
    }

    function validation($data, $files) {
        return $_form->validation($data, $files);
    }

    /**
     * The method displays the installed form in the current form
     *
     */
    function display() {
        $this->modform->display();
    }

     /**
     * The method deletes from the installed form of a field missing in an array $fields
     *
     * @param array $fields The member list which should be displayed on the form
     *
     */
    function delete_element_without($fields) {
        // We receive the list of fields in form
        $allFields = $this->get_fields();
        foreach ($allFields as $groupnumber => $listelements) {
            $colFisibleElements = 0;
            $idHeaderElement = -1;
            foreach ($listelements as $elementnumber => $element) {
                if ($element['type'] == 'header') {
                    $idHeaderElement = $elementnumber;
                } elseif ($element['type'] != 'hidden') {
                    if (in_array($groupnumber.'-'.$elementnumber, $fields)) {
                        // The current field will be displayed as it is present at an array $fields
                        $colFisibleElements++;
                    } elseif ($element['type'] != 'repeatElements' && $element['type'] != 'group' && $element['name'] != 'buttonar') {
                        $this->is_form()->_form->removeElement($element['name']);
                        $subelemetns = $element['subElements'];
                        if (count($subelemetns) == 0) {
                            $this->is_form()->_form->addElement('hidden', $element['name'], '');
                        } else {
                            foreach ($subelemetns as $subelement) {
                                $this->is_form()->_form->addElement('hidden', $subelement['name'], $subelement['value']);
                            }
                        }
                    } elseif ($element['name'] != 'buttonar') {
                        if ($element['type'] == 'group') {
                            $this->is_form()->_form->removeElement($element['name']);
                        }
                        foreach ($element['elements'] as $value) {
                            $subelemetns = $value['subElements'];
                            if ($element['type'] == 'repeatElements') {
                                $this->is_form()->_form->removeElement($value['name']);
                            }
                            if (count($subelemetns) == 0) {
                                $this->is_form()->_form->addElement('hidden', $value['name'], '');
                            } else {
                                foreach ($subelemetns as $subelement) {
                                    $this->is_form()->_form->addElement('hidden', $subelement['name'], $subelement['value']);
                                }
                            }
                        }
                    }
                }
            }
            if ($colFisibleElements == 0 && $idHeaderElement != -1) {
                $this->is_form()->_form->removeElement($listelements[$idHeaderElement]['name']);
            }
        }

    }

    /**
     * The method replaces value of an element of the form
     *
     * @param array $element - Form element
     * @param object $fromrecord - Object storing values of the form
     * @param array $record - Array of values on which it is necessary to replace values in form
     *
     */
    function replase_element_data($element, &$fromrecord, $record) {
        $result = array();
        preg_match_all('/^([a-z0-9]+)\[([0-9]+)]$/s', $element['name'], $result);
        if ($element['type'] == 'group' || $element['type'] == 'repeatElements') {
            foreach ($element['elements'] as $value) {
                $this->replase_element_data($value, $fromrecord, $record);
            }
        } elseif (array_key_exists($element['name'], $record) && count($result[0]) == 0) {
            $fromrecord->$element['name'] = $record[$element['name']];
        } elseif (count($result[0]) && array_key_exists($element['name'], $record)) {
            $namefield = $result[1][0];
            $field = &$fromrecord->$namefield;
            $number = $result[2][0];
            $field[$number] = $record[$element['name']];
        }
        if ($element['type'] == 'checkbox' && !$fromrecord->$element['name']) {
            unset($fromrecord->$element['name']);
        }
    }

    /**
     * The method fills an array of fields from an array of elements of the form
     *
     * @param array $fields - Array of fields
     * @param array $elements - Array elements form
     *
     */
    function get_groups_element(&$fields, $elements) {
        foreach ($elements as $element) {
            $fields['elements'][] = array(
                'name' => $element->getName(),
                'type' => $element->getType(),
                'label' => $element->getLabel()
            );
            $current = count($fields['elements']);
            $current--;
            if ($element->getType() == 'group') {
                $this->get_groups_element($fields['elements'][$current], $element->getElements());
            } else {
                $this->get_sub_elements($fields['elements'][$current], $element);
            }
        }
    }

    /**
     * The method adds forms containing in an element in an array of fields of a field
     *
     * @param array $fields - Array of fields
     * @param object $element - Object element form
     *
     */
    function get_sub_elements(&$field, $element) {
        $html = $element->toHTML();
        $result = array();
        $pattern = '/(<input[^\\/>]*\\/>)|(<select[^>]*>)|(<textarea[^>]*>)/s';
        preg_match_all($pattern, $html, $result);
        $subelements = array();
        foreach ($result[0] as $value) {
            $result2 = array();
            $pattern = '/name *= *("[^"]+"|[^\\s>]+)/si';
            preg_match_all($pattern, $value, $result2);
            if (array_key_exists(1, $result2)) {
                if (array_key_exists(0, $result2[1])) {
                    $name = substr($result2[1][0], 1, -1);
                    $subelements[] = array(
                        'name' => $name,
                        'value' => ''
                    );
                }
            }
        }
        $field['subElements'] = $subelements;
    }

    /**
     * The method returns an array of fields of the form
     *
     * @return array - Array fields in form
     *
     */
    function get_fields() {
        // We select all fields of the form
        $form = $this->modform->_form;
        $elements = $form->_elements;
        $fields = array();
        foreach ($elements as $element) {
            $fields[] = array(
                'name' => $element->getName(),
                'type' => $element->getType(),
                'label' => $element->getLabel()
            );
            $current = count($fields);
            $current--;
            if ($element->getType() == 'group') {
                $this->get_groups_element($fields[$current], $element->getElements());
            } else {
                $this->get_sub_elements($fields[$current], $element);
            }
        }

        // We search for repeating elements in form
        $repeat = find_repeat_elements($fields);

        $elements = array();

        $j =-1;
        $size = count($fields);
        for ($i = 0; $i < $size; $i++) {
            foreach ($repeat as $r1) {
                $r = $r1['elements'];
                reset($r);
                if ($i == key($r)) {
                    end($r);
                    $i = key($r);
                    $i++;
                    $elements[$j][] = $r1;
                    break;
                }
            }
            if ($fields[$i]['type'] == 'header') {
                $j++;
            }
            if ($j == -1) {
                $j = 0;
            }
            $elements[$j][] = $fields[$i];
        }
        return $elements;
    }
}