<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
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
 * Defines templates, an abstraction over regular expressions.
 *
 * @package    qtype_preg
 * @copyright  2015 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace qtype_preg;

defined('MOODLE_INTERNAL') || die();

/**
 * Represents a named template. For example, a template named 'word' can correspond to '\w+'.
 * Don't bother yourself wrapping regex in (?:grouping), it will be done automatically during parsing.
 */
class template {

    /** Name of this template. */
    public $name;

    /** Actual template (regular expression) that can contain placeholders like $$1, $$2, ...*/
    public $regex;

    /** The regular expression above may need its own options. This is a string like 'imx'. */
    public $options;

    /** Number of such placeholders in this template. */
    public $placeholderscount;

    /**
     * All available templates. Can be changed from outside for testing purposes.
     * placeholders syntax:
     * same as in regex, but can contain required form definition for description generation
     * like $$g1 (g - is required form for description generation, and will be ignored in other functions)
     */
    private static $templates;

    /** Descriptions */
    private $descriptions;

    public function __construct($name = '', $regex = '', $options = '', $descriptions = array(), $placeholderscount = 0) {
        $this->name = $name;
        $this->regex = $regex;
        $this->options = $options;
        $this->descriptions = $descriptions;
        $this->placeholderscount = $placeholderscount;
    }

    private function get_options_str($positive = true) {
        $result = '';
        foreach (array('i', 'm', 's', 'x') as $option) {
            $contains = strstr($this->options, $option) !== false;
            if ($positive === $contains) {
                $result .= $option;
            }
        }
        return $result;
    }

    /**
     * Returns all templates that should be recognized by parser.
     */
    public static function available_templates() {
        if (template::$templates === null) {
            template::$templates = array(
                'word' => new template('word', '\w+', '', array('en' => 'word', 'ru' => 'слово')),
                'integer' => new template('integer', '[+-]?\d+', '', array('en' => 'integer', 'ru' => 'целое число')),
                'parens_req' => new template('parens_req', '(   \(    (?:(?-1)|$$1)   \)  )', 'x', array('en' => '$$1 in parens', 'ru' => '$$1 в скобках'), 1),
                'parens_opt' => new template('parens_opt', '$$1|(?###parens_req<)$$1(?###>)', '', array('en' => '$$1 in parens or not', 'ru' => '$$1 в скобках или без'), 1),
                'brackets_req' => new template('brackets_req', '(   \[   (?:(?-1)|$$1)   \]   )', 'x', array('en' => '$$1 in brackets', 'ru' => '$$1 в квадратных скобках'), 1),
                'brackets_opt' => new template('brackets_opt', '$$1|(?###brackets_req<)$$1(?###>)', '', array('en' => '$$1 in brackets or not', 'ru' => '$$1 в квадратных скобках или без'), 1),
            );
        }
        return template::$templates;
    }

    public function get_description() {
        $description = false;
        $mylang = current_language();
        $parentlang = get_parent_language($mylang);
        if (array_key_exists($mylang, $this->descriptions)) {
            $description = $this->descriptions[$mylang];
        } else if (array_key_exists($parentlang, $this->descriptions)) {
            $description = $this->descriptions[$parentlang];
        } else if (array_key_exists('en', $this->descriptions)) {
            $description = $this->descriptions['en'];
        } else {
            $description = reset($this->descriptions); // dont use in foreach
        }
        return $description;
    }

    /**
     * You are not supposed to call this one unless you are testing the parser.
     */
    public static function set_available_templates($value) {
        template::$templates = $value;
    }

    /**
     * Gets regex, returns tokens.
     */
    public static function process_regex($regex, $options, &$lexer, &$errors, &$resultregex) {
        $mods = $options->modifiers_to_string();
        $pos = '';
        $neg = '';
        foreach (array('i', 'm', 's', 'x') as $option) {
            $contains = strstr($mods, $option) !== false;
            if ($contains) {
                $pos .= $option;
            } else {
                $neg .= $option;
            }
        }


        $hastemplates = false;
        $tokens = self::tokenize_regex($regex, $options, $lexer, $hastemplates);
        $processedregex = self::process_regex_tokens($tokens, $pos, $neg, $errors);

        $newlexer = null;
        $newhastemplates = false;
        $newtokens = self::tokenize_regex($processedregex, $options, $newlexer, $newhastemplates);
        if ($newhastemplates) {
            // If there are still some templates, do a recursive call
            $newtokens = self::process_regex($processedregex, $options, $newlexer, $errors, $resultregex);
        } else {
            // Else save the result
            $resultregex = $processedregex;
        }
        return $newtokens;
    }

    private static function tokenize_regex($regex, $options, &$lexer, &$hastemplates) {
        \StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new \qtype_preg_lexer($pseudofile);
        $lexer->set_options($options);
        $hastemplates = false;

        $result = array();
        while (($token = $lexer->nextToken()) !== null) {
            if (is_array($token)) {
                foreach ($token as $curtoken) {
                    $result[] = $curtoken;
                    $hastemplates = $hastemplates || ($curtoken->type === \qtype_preg_parser::TEMPLATEPARSELEAF || $curtoken->type === \qtype_preg_parser::TEMPLATEOPENBRACK);
                }
            } else {
                $result[] = $token;
                $hastemplates = $hastemplates || ($token->type === \qtype_preg_parser::TEMPLATEPARSELEAF || $token->type === \qtype_preg_parser::TEMPLATEOPENBRACK);
            }
        }
        return $result;
    }

    /**
     * Gets tokens, returns regex.
     */
    private static function process_regex_tokens($tokens, $pos, $neg, &$errors) {
        $result = '';
        $errors = array();
        $templates = self::available_templates();
        $opentemplates = array(null);   // stack of opening 'parens' (?###template<). First one is a fictive element for the whole regex
        $operands = array(array(''));   // stack of lists of operands. First one is fictive element for the whole regex

        $firstopenparen = null;         // for syntax errors reporting

        foreach ($tokens as $token) {
            $node = $token->value;
            $counttemplates = count($opentemplates);
            $countoperands = count($operands);

            // There can be fictive lexemes
            if ($token->value === null) {
                continue;
            }

            // Non-template token?
            if ($token->type !== \qtype_preg_parser::TEMPLATEPARSELEAF &&
                $token->type !== \qtype_preg_parser::TEMPLATEOPENBRACK &&
                $token->type !== \qtype_preg_parser::TEMPLATESEP &&
                $token->type !== \qtype_preg_parser::TEMPLATECLOSEBRACK) {

                $operands[$countoperands - 1][count($operands[$countoperands - 1]) - 1] .= $node->plain_userinscription();
                continue;
            }

            $template = null;
            $positiveopts = '';
            $negativeopts = '';

            if ($token->type === \qtype_preg_parser::TEMPLATEPARSELEAF || $token->type === \qtype_preg_parser::TEMPLATEOPENBRACK) {
                // Is there a template with such name?
                if (!array_key_exists($node->name, $templates)) {
                    $error = new \qtype_preg_node_error(\qtype_preg_node_error::SUBTYPE_UNKNOWN_TEMPLATE, $node->name);
                    $error->set_user_info($node->position, $node->userinscription);
                    $errors[] = $error;
                } else {
                    $template = $templates[$node->name];
                    $positiveopts = $template->get_options_str(true);
                    $negativeopts = $template->get_options_str(false);
                }
            }

            // Is it simply a template leaf?
            if ($token->type === \qtype_preg_parser::TEMPLATEPARSELEAF && $template !== null) {
                // Check if a node called as a leaf
                if ($template->placeholderscount > 0) {
                    $error = new \qtype_preg_node_error(\qtype_preg_node_error::SUBTYPE_WRONG_TEMPLATE_PARAMS_COUNT, $node->plain_userinscription());
                    $error->set_user_info($node->position, $node->userinscription);
                    $errors[] = $error;
                }
                $operands[$countoperands - 1][count($operands[$countoperands - 1]) - 1] .= "(?{$positiveopts}-{$negativeopts}:{$template->regex})";
                continue;
            }

            // Is it an opening 'paren'?
            if ($token->type === \qtype_preg_parser::TEMPLATEOPENBRACK) {
                if ($firstopenparen === null) {
                    $firstopenparen = $token->value;
                }
                // Check if a leaf called as a node
                if ($template !== null && $template->placeholderscount === 0) {
                    $error = new \qtype_preg_node_error(\qtype_preg_node_error::SUBTYPE_WRONG_TEMPLATE_PARAMS_COUNT, $node->plain_userinscription());
                    $error->set_user_info($node->position, $node->userinscription);
                    $errors[] = $error;
                }
                // Push a new template and new operands list
                $tmp = new \stdClass();
                $tmp->token = $token;
                $tmp->template = $template;
                $opentemplates[] = $tmp;
                $operands[] = array('');
                continue;
            }

            // Is it a separator
            if ($token->type === \qtype_preg_parser::TEMPLATESEP) {
                // Handle situations like (?###,)(?###,)
                if ($operands[$countoperands - 1][count($operands[$countoperands - 1]) - 1] === '') {
                    $operands[$countoperands - 1][count($operands[$countoperands - 1]) - 1] = '(?:)';
                }
                $operands[$countoperands - 1][] = '';
                continue;
            }

            // Is it a closing 'paren'?
            if ($token->type === \qtype_preg_parser::TEMPLATECLOSEBRACK) {
                // Check for unopened paren
                if (count($opentemplates) === 1) {
                    $error = new \qtype_preg_node_error(\qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_OPEN_PAREN, $node->plain_userinscription());
                    $error->set_user_info($node->position, $node->userinscription);
                    $errors[] = $error;
                    break;
                }
                // Pop the template which is now ready; regardless possible error of wrong params count
                $template = array_pop($opentemplates);
                $lastoperands = array_pop($operands);

                if ($template->template !== null) {
                    // Substitute all actual operands to the template
                    $res = $template->template->regex;

                    // Check if the number of operands is correct
                    if ($template->template->placeholderscount !== count($lastoperands)) {
                        $position = new \qtype_preg_position($template->token->value->position->indfirst, $node->position->indlast,
                                                             $template->token->value->position->linefirst, $node->position->linelast,
                                                             $template->token->value->position->colfirst, $node->position->collast);

                        $error = new \qtype_preg_node_error(\qtype_preg_node_error::SUBTYPE_WRONG_TEMPLATE_PARAMS_COUNT, null);
                        $error->set_user_info($position, $template->token->value->userinscription);
                        $errors[] = $error;
                        continue;
                    }

                    // Because we wrap operands into (?mods:), no special actions neede for situations like (?###template<)(?###>)
                    for ($i = 1; $i <= $template->template->placeholderscount; ++$i) {
                        $res = str_replace("\$\${$i}", "(?{$pos}-{$neg}:{$lastoperands[$i - 1]})", $res);
                    }

                    // Concatenate the template to the previous operand
                    $positiveopts = $template->template->get_options_str(true);
                    $negativeopts = $template->template->get_options_str(false);
                    $operands[$countoperands - 2][count($operands[$countoperands - 2]) - 1] .= "(?{$positiveopts}-{$negativeopts}:{$res})";
                }

                continue;
            }
        }
        // Check for unclosed paren
        if (count($opentemplates) > 1) {
            $error = new \qtype_preg_node_error(\qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_CLOSE_PAREN, $firstopenparen->plain_userinscription());
            $error->set_user_info($firstopenparen->position, $firstopenparen->userinscription);
            $errors[] = $error;
            return '';
        }
        return $operands[0][0];
    }
}
