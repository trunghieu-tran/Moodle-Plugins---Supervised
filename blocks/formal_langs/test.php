<?
global $CFG;

define('MOODLE_INTERNAL', 1);

$CFG = new stdClass();
$CFG->dirroot = dirname(dirname(dirname(__FILE__)));
$CFG->libdir = $CFG->dirroot . '/lib';

require_once($CFG->dirroot.'/lib/classes/text.php');
require_once($CFG->dirroot .'/blocks/formal_langs/language_cpp_parseable_language.php');

$lang = new block_formal_langs_language_cpp_parseable_language();
$result = $lang->create_from_string('struct A {}; A::~A() {}');

function print_node($node, $paddingcount)
{
	if ($node == null) {
		echo 'No tree!';
		return;
	}
	
	$padding = str_repeat(' ', $paddingcount);
	
	$value = '';
	if (is_a($node, 'block_formal_langs_token_base')) {
		$value = '(' . $node->value() . ')';
	}
	if (!method_exists($node, 'type')) {
		var_dump($node);
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

