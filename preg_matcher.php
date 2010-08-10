<?php
/**
 * Defines abstract class of matcher, extend it for get matcher
 *
 * @copyright &copy; 2010  Kolesov Dmitriy 
 * @author Kolesov Dmitriy, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');
require_once($CFG->dirroot . '/question/type/preg/stringstream/stringstream.php');

class preg_matcher {
    function name() {
        return 'preg_matcher';
    }
    function preprocess($regex) {
        echo 'Error: preprocess has not been implemented for', $this->name(), 'class';
    }
    function match($response) {
        echo 'Error: geting result has not been implemented for', $this->name(), 'class';
    }
    function get_index() {
        echo 'Error: geting index has not been implemented for', $this->name(), 'class';
    }
    function get_full() {
        echo 'Error: getting fullness has not been implemented for', $this->name(), 'class';
    }
    function get_next_char() {
        echo 'Error: getting next character has not been implemented for', $this->name(), 'class';
    }
    static function validate($regex) {
        echo 'Error: validation has not been implemented for', $this->name(), 'class';
    }
    static function list_of_supported_operations_and_operands() {
        echo 'Error: list of supported operation has not been implemented for', $this->name(), 'class';
    }
    /**
    *function do lexical and syntaxical analyze of regex and build tree, root saving in $this->roots[0]
    @param $regex - regular expirience for building tree
    */
    function build_tree($regex) {
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new Yylex($pseudofile);
        $parser = new preg_parser_yyParser;
        while ($token = $lexer->nextToken()) {
            $prev = $curr;
            $curr = $token->type;//var_dump($token); echo '<br/>';
            if (preg_parser_yyParser::is_conc($prev, $curr)) {
                $parser->doParse(preg_parser_yyParser::CONC, 0);
                $parser->doParse($token->type, $token->value);
            } else {
                $parser->doParse($token->type, $token->value);
            }
        }
        $parser->doParse(0, 0);
        $this->roots[0] = $parser->get_root();
        fclose($pseudofile);
    }
}
?>