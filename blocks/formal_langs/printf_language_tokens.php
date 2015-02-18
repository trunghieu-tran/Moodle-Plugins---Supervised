<?php
/**
 * Defines printf language tokens for correctwriting question type.
 *
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Sergey Pashaev Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');

/**
 * A basic token for block_formal_langs_printf_token_base
 */
class block_formal_langs_printf_token_base extends block_formal_langs_token_base {

    public function name() {
        $name = parent::name();
        $name = str_replace('printf_token_','', $name);
        return $name;
    }
}

/**
 * A simple text data
 */
class block_formal_langs_token_printf_text extends block_formal_langs_token_base {

}
/**
 * A quote
 */
class block_formal_langs_token_printf_quote extends block_formal_langs_token_base {

}

/**
 * A specifier for token data
 */
class block_formal_langs_token_printf_specifier extends block_formal_langs_token_base {

}
