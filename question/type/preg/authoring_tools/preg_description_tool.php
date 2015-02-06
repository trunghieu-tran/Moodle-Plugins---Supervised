<?php
// This file is part of Moodle - http:// moodle.org/
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
// along with Moodle.  If not, see <http:// www.gnu.org/licenses/>.

/**
 * Defines handler for regex description tool
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Pahomov Dmitry
 * @license http:// www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_authoring_tool.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_description_nodes.php');

/**
 * State of description generating
 */
class qtype_preg_description_state {

    /** @var bool is (?i) set */
    public $caseless = false;

    /** @var bool is (?s) set */
    public $singleline = false;

    /** @var bool is (?m) set */
    public $multilineline = false;

    /** @var bool is (?x) set */
    public $extended = false;

    /** @var bool is (?U) set */
    public $ungreedy = false;

    /** @var bool is (?J) set */
    public $duplicate = false;

    public $forceunsetmodifiers = false;

    /**
     * set default values to all state variables
     */
    public function reset() {
        $this->caseless        = false;
        $this->singleline      = false;
        $this->multilineline   = false;
        $this->extended        = false;
        $this->ungreedy        = false;
        $this->duplicate       = false;
        $this->forceunsetmodifiers = false;
    }

    /**
     * Set or unsets the flag meaning that $modifier is (un)set
     *
     * @param string $modifier modifier to (un)set
     */
    public function set_modifier($modifier, $value) {
        switch ($modifier) {
            case 'i':
                $this->caseless = $value;
                break;
            case 's':
                $this->singleline = $value;
                break;
            case 'm':
                $this->multilineline = $value;
                break;
            case 'x':
                $this->extended = $value;
                break;
            case 'U':
                $this->ungreedy = $value;
                break;
            case 'J':
                $this->duplicate = $value;
                break;
        }
    }
}

/**
 * Options, for generating description - affects scanning, parsing, description genetating.
 */
class qtype_preg_description_options extends qtype_preg_authoring_tools_options {

    /** @var bool use userinscription for charset description instead of flags */
    public $charsetuserinscription = false;

    /** @var int limit for charset in which it is displayed as a enum of characters */
    public $rangelengthmax = 5;

    public function __construct() {
        $this->preserveallnodes = true;
    }
}

/**
 * Handler, generating information for regular expression
 */
class qtype_preg_description_tool extends qtype_preg_authoring_tool {

    /** @var qtype_preg_description_state state of description generating */
    public $state;

    /*
     * Construct of parent class parses the regex and does all necessary preprocessing.
     *
     * @param string $regex - regular expression to handle.
     * @param object $options - options to handle regex, i.e. any necessary additional parameters.
     */
    public function __construct($regex = null, $options = null) {
        if ($options === null) {
            $options = new qtype_preg_description_options();
        }
        parent::__construct($regex, $options);
        $this->state = new qtype_preg_description_state();
    }

    /**
     * Overloaded from qtype_preg_regex_handler.
     */
    public function name() {
        return 'description_tool';
    }

    /**
     * Overloaded from qtype_preg_regex_handler.
     */
    protected function node_infix() {
        return 'description';
    }

    /**
     * Overloaded from qtype_preg_regex_handler.
     */
    protected function is_preg_node_acceptable($pregnode) {
        return true;
    }

    /**
     * Overloaded from qtype_preg_authoring_tool.
     */
    public function json_key() {
        return 'description';
    }

    public function generate_html() {
        if ($this->regex->string() == '') {
            return $this->data_for_empty_regex();
        } else if ($this->errors_exist() || $this->get_ast_root() == null) {
            return $this->data_for_unaccepted_regex();
        }
        return $this->data_for_accepted_regex();
    }

    /**
     * Overloaded from qtype_preg_authoring_tool.
     */
    public function data_for_accepted_regex() {
        return $this->default_description();
    }

    public function options() {
        return $this->options;
    }

    /**
     * Genegates description of regexp
     * Example of calling:
     *
     * description('<span class="description_node_%n">%s</span>', '<span class="description">%s</span>');
     *
     * Operator with id=777 will be plased into: <span class="description_node_777">abc</span>.
     * User defined parts of regex with id=777 will be placed id: <span class="description_node_777">%1 or %2</span>.
     * Whole string will be placed into <span class="description">string</span>
     *
     * @param string $wholepattern Pattern for whole decription. Must contain %s - description.
     * @param string $numbering_pattern Pattern to track numbering.
     * Must contain: %s - description of node;
     * May contain:  %n - node id.
     * @param bool $charsetuserinscr use userinscription for charset description instead of flags
     * @param int $rangelengthmax limit for charset ranges in which it is displayed as a enum of characters
     * @return string description.
     */
    public function description($numbering_pattern, $wholepattern = null, $charsetuserinscr = false, $rangelengthmax = 5) {
        $this->state->reset();// restore default state
        $backupoptions = $this->options;// save original options
        $this->options->charsetuserinscription = (bool)$charsetuserinscr;
        $this->options->rangelengthmax = (int)$rangelengthmax;

        $string = '';
        if (isset($this->dstroot)) {
            $string = $this->dstroot->description($numbering_pattern, null, null);
            $string = preg_replace('%;((?:</span>)?)]%u', '\1]', $string);   // Postprocessing
        }
        if (!empty($wholepattern)) {
            $string = qtype_poasquestion\string::replace('%content', $string, $wholepattern);
        }
        $this->options = $backupoptions; // restore original options
        return $string;
    }

    /**
     * Calling default description() with default params
     */
    public function default_description() {
        return $this->description('<span class="%idclass%optionalclassesdevider%optionalclasses" style="%style" >%content</span>', '<span style="background: white">%content</span>');
    }

    /**
     * for testing
     */
    public function form_description($form) {
        $result = $this->dstroot->description('%s', null, $form);
        return $result;
    }
}
