<?php
/**
 * Defines patterns class for lexer.
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright &copy; 2011 Sergey Pashaev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

/**
 * Class for lexer patterns.
 *
 * Class for storing patterns.
 *
 * @copyright  &copy; 2011 Sergey Pashaev, Volgograd State Technical University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class qtype_correctwriting_lexer_pattern {

    /**
     * Pattern name.
     * @var string
     */
    private $name;

    /**
     * Pattern regexp.
     * @var string
     */
    private $regexp;

    /**
     * Active condition. If lexer current condition == $activecondition then this
     * pattern can be used. Initial value: "DEFAULT".
     * @var string
     */
    private $activecondition;

    /**
     * Next condition. Condition in which lexer will get after using this
     * pattern.
     * @var string
     */
    private $nextcondition;

    /**
     * Pattern actions.
     * @var string
     */
    private $actions;

    /**
     * Basic pattern constructor.
     *
     * @param string $name - pattern name
     * @param string $regexp - pattern regexp
     * @param string $activecondition - active condition
     * @param string $nextcondition - next condition
     */
    public function __construct($name,
                                $regexp,
                                $actions,
                                $activecondition = 'DEFAULT',
                                $nextcondition = null) {
        if (strlen($name) == 0 or strlen($regexp) == 0) {
            throw new Exception('Pattern constructor: empty name or regexp');
        } else {
            $this->name = $name;
            $this->regexp = $regexp;
        }

        $this->activecondition = $activecondition;
        $this->nextcondition = $nextcondition;
        $this->actions = $actions;

        // TODO: check and add /^( )/ to regexp
    }

    /**
     * Parse pattern action string.
     */
    public function parse_actions() {
        // TODO: parse action string
    }

    /**
     * This function gets actionstring and "execute" it.
     */
    public function execute_actions() {
        // TODO: eval action string
    }

    public function name() {
        return $this->name;
    }
}

class qtype_correctwriting_lexer_action {
    private $type;
    private $errorstr;

    public function __construct($type, $errorstr = null) {
        $this->$type = $type;
        $this->errorstr = $errorstr;
    }
}

?>