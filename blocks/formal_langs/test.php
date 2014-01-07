<?
global $CFG;

define('MOODLE_INTERNAL', 1);

$CFG = new stdClass();
$CFG->dirroot = dirname(dirname(dirname(__FILE__)));
$CFG->libdir = $CFG->dirroot . '/lib';

require_once($CFG->dirroot.'/lib/classes/text.php');
require_once($CFG->dirroot .'/blocks/formal_langs/language_cpp_parseable_language.php');

$lang = new block_formal_langs_language_cpp_parseable_language();
$result = $lang->create_from_string('% 11');

function print_node($node, $paddingcount)
{
	if ($node == null) {
		echo 'No tree!';
		return;
	}
	$padding = str_repeat(' ', $paddingcount);
    if (is_array($node)) {
        echo $padding . '[' . PHP_EOL;
        foreach($node as $i => $nodechild) {
            echo $padding . (int)$i . ':';
            echo $padding;
            print_node($nodechild, $paddingcount + 1);
            echo PHP_EOL;
        }
        echo $padding . ']';
        return;
    }
	if (!method_exists($node, 'type')) {
		var_dump($node);
        return;
	}
    $value = '';
    if (is_a($node, 'block_formal_langs_token_base')) {
        $value = '(' . $node->value() . ')';
    }
	echo $padding . $node->type() . $value . PHP_EOL;
	if (count($node->childs()))  {
		echo $padding . '{' . PHP_EOL;
		foreach($node->childs() as $child) {
			print_node($child, $paddingcount + 1);
		}
		echo $padding . '}' . PHP_EOL;
	}
}
print_node($result->syntaxtree, 0);

