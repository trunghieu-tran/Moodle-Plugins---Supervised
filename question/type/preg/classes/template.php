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

    /** Descriptions. */
    private $descriptions;

    /** Descriptions of template parameters (if any exist). */
    private $parametersdescriptions;

    public function __construct($name = '', $regex = '', $options = '', $descriptions = array(), $placeholderscount = 0, $parametersdescriptions = array()) {
        $this->name = $name;
        $this->regex = $regex;
        $this->options = $options;
        $this->descriptions = $descriptions;
        $this->placeholderscount = $placeholderscount;
        $this->parametersdescriptions = $parametersdescriptions;
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
     * Returns list of template names, called from this template.
     */
    public function get_dependency_templates() {
        $pattern = '/[(][?]###([^<,>)]+)[<]?[)]/';    // What a mess :(
        $matches = array();
        preg_match_all($pattern, $this->regex, $matches);
        return array_unique($matches[1]);
    }

    /**
     * Returns all templates that should be recognized by parser.
     */
    public static function available_templates() {
        if (template::$templates === null) {
            template::$templates = array(
                'word' => new template('word', '\w+', '', array('en' => 'word', 'ru' => 'слово')),
                'integer' => new template('integer', '[+-]?\d+', '', array('en' => 'integer', 'ru' => 'целое число')),
                'cpp_id' => new template('cpp_id', '[_a-zA-Z]\w*', '', array('en' => 'C++ id', 'ru' => 'идентификатор в C++')),
                'parens_req' => new template(
                    'parens_req',
                    '(   \s*\(\s*    (?:(?-1)|$$1)   \s*\)\s*  )',
                    'x',
                    array(
                        'en' => '%1 in parens',
                        'ru' => '%1 в скобках'
                    ),
                    1,
                    array(
                        'en' => array(
                            'text inside brackets'
                        ),
                        'ru' => array(
                            'текст внутри скобок'
                        )
                    )
                ),
                'parens_opt' => new template(
                    'parens_opt',
                    '$$1|(?###parens_req<)$$1(?###>)',
                    '',
                    array(
                        'en' => '%1 in optional parens',
                        'ru' => '%1 в скобках или без'
                    ),
                    1,
                    array(
                        'en' => array(
                            'text inside brackets'
                        ),
                        'ru' => array(
                            'текст внутри скобок'
                        )
                    )
                ),
                'brackets_req' => new template(
                    'brackets_req',
                    '(   \s*\[\s*   (?:(?-1)|$$1)   \s*\]\s*   )',
                    'x',
                    array(
                        'en' => '%1 in brackets',
                        'ru' => '%1 в квадратных скобках'
                    ),
                    1,
                    array(
                        'en' => array(
                            'text inside brackets'
                        ),
                        'ru' => array(
                            'текст внутри скобок'
                        )
                    )
                ),
                'brackets_opt' => new template(
                    'brackets_opt',
                    '$$1|(?###brackets_req<)$$1(?###>)',
                    '',
                    array(
                        'en' => '%1 in optional brackets',
                        'ru' => '%1 в квадратных скобках или без'
                    ),
                    1,
                    array(
                        'en' => array(
                            'text inside brackets'
                        ),
                        'ru' => array(
                            'текст внутри скобок'
                        )
                    )
                ),
                'custom_parens_req' => new template(
                    'custom_parens_req',
                    '(   \s*$$1\s*    (?:(?-1)|$$2)   \s*$$3\s*  )',
                    'x',
                    array(
                        'en' => '%2 in any number of evenly opened %1 and closed %3',
                        'ru' => '%2 в любом количестве открытых %1 и закрытых %3'
                    ),
                    3,
                    array(
                        'en' => array(
                            'opening bracket',
                            'text inside brackets',
                            'closing bracket'
                        ),
                        'ru' => array(
                            'открывающаяся скобка',
                            'текст внутри скобок',
                            'закрывающаяся скобка'
                        )
                    )
                ),
                'custom_parens_opt' => new template(
                    'custom_parens_opt',
                    '$$2|(?###custom_parens_req<)$$1(?###,)$$2(?###,)$$3(?###>)',
                    'x',
                    array(
                        'en' => '%2 in any number of evenly opened %1 and closed %3 or without them',
                        'ru' => '%2 в любом количестве открытых %1 и закрытых %3'),
                    3,
                    array(
                        'en' => array(
                            'opening bracket',
                            'text inside brackets',
                            'closing bracket'
                        ),
                        'ru' => array(
                            'открывающаяся скобка',
                            'текст внутри скобок',
                            'закрывающаяся скобка'
                        )
                    )
                ),
            );
        }
        return template::$templates;
    }

    /**
     * Checks if a list of templates has recursive dependencies, or unexisting dependencies.
     * $templates should be a superset of available templates.
     * Call this function before adding user templates
     */
    public static function check_dependencies($templates) {
        foreach ($templates as $template) {
            $checked = array();
            $alldependencies = array();
            $curdependencies = $template->get_dependency_templates();
            while (!empty($curdependencies)) {
                $curdependency = array_pop($curdependencies);
                if (in_array($curdependency, $checked)) {
                    continue;
                }
                // Check if the dependency exists
                if (!array_key_exists($curdependency, $templates)) {
                    return false;
                }
                // Add to all dependencies
                if (!in_array($curdependency, $alldependencies)) {
                    $alldependencies[] = $curdependency;
                }
                // Mark as checked
                if (!in_array($curdependency, $checked)) {
                    $checked[] = $curdependency;
                }
                // Process child rependencies
                $deptemplate = $templates[$curdependency];
                $depdependencies = $deptemplate->get_dependency_templates();
                $curdependencies = array_merge($curdependencies, $depdependencies);
            }
            if (in_array($template->name, $alldependencies)) {
                return false;
            }
        }
        return true;
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

    public function get_parametersdescription() {
        $parametersdescription = false;
        $mylang = current_language();
        $parentlang = get_parent_language($mylang);
        if (array_key_exists($mylang, $this->parametersdescriptions)) {
            $parametersdescription = $this->parametersdescriptions[$mylang];
        } else if (array_key_exists($parentlang, $this->parametersdescriptions)) {
            $parametersdescription = $this->descriptions[$parentlang];
        } else if (array_key_exists('en', $this->parametersdescriptions)) {
            $parametersdescription = $this->descriptions['en'];
        } else {
            $parametersdescription = reset($this->parametersdescriptions); // dont use in foreach
        }
        return $parametersdescription;
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
    public static function process_regex($regex, $options, &$lexer, &$resultregex) {
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
        $tokens = \qtype_preg_lexer::tokenize_regex($regex, $options, $lexer, $hastemplates);
        if (!$hastemplates) {
            // Original regex does not contain templates. Return these tokens.
            $resultregex = $regex;
            return $tokens;
        }

        $processedregex = self::process_regex_tokens($tokens, $pos, $neg);

        $newlexer = null;
        $newhastemplates = false;
        $newtokens = \qtype_preg_lexer::tokenize_regex($processedregex, $options, $newlexer, $newhastemplates);
        if ($newhastemplates) {
            // If there are still some templates, do a recursive call
            $newtokens = self::process_regex($processedregex, $options, $newlexer, $resultregex);
        } else {
            // Else save the result
            $resultregex = $processedregex;
        }

        // Remember the actual lexer
        $lexer = $newlexer;

        return $newtokens;
    }

    /**
     * Gets tokens, returns regex.
     */
    private static function process_regex_tokens($tokens, $pos, $neg) {
        $result = '';
        $templates = self::available_templates();
        $opentemplates = array(null);   // stack of opening 'parens' (?###template<). First one is a fictive element for the whole regex
        $operands = array(array(''));   // stack of lists of operands. First one is fictive element for the whole regex

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
                $template = $templates[$node->name];
                $positiveopts = $template->get_options_str(true);
                $negativeopts = $template->get_options_str(false);
            }

            // Is it simply a template leaf?
            if ($token->type === \qtype_preg_parser::TEMPLATEPARSELEAF && $template !== null) {
                $operands[$countoperands - 1][count($operands[$countoperands - 1]) - 1] .= "(?{$positiveopts}-{$negativeopts}:{$template->regex})";
                continue;
            }

            // Is it an opening 'paren'?
            if ($token->type === \qtype_preg_parser::TEMPLATEOPENBRACK) {
                // Push a new template and new operands list
                $opentemplates[] = $template;
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
                // Pop the template which is now ready
                $template = array_pop($opentemplates);
                $lastoperands = array_pop($operands);

                if ($template !== null) {
                    // Substitute all actual operands to the template
                    $res = $template->regex;

                    // Because we wrap operands into (?mods:), no special actions neede for situations like (?###template<)(?###>)
                    for ($i = 1; $i <= $template->placeholderscount; ++$i) {
                        $res = str_replace("\$\${$i}", "(?{$pos}-{$neg}:{$lastoperands[$i - 1]})", $res);
                    }

                    // Concatenate the template to the previous operand
                    $positiveopts = $template->get_options_str(true);
                    $negativeopts = $template->get_options_str(false);
                    $operands[$countoperands - 2][count($operands[$countoperands - 2]) - 1] .= "(?{$positiveopts}-{$negativeopts}:{$res})";
                }

                continue;
            }
        }
        return $operands[0][0];
    }
}
