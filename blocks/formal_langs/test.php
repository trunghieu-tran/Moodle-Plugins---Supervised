<?
global $CFG;

define('MOODLE_INTERNAL', 1);

$CFG = new stdClass();
$CFG->dirroot = dirname(dirname(dirname(__FILE__)));
$CFG->libdir = $CFG->dirroot . '/lib';

require_once($CFG->dirroot.'/lib/classes/text.php');
require_once($CFG->dirroot .'/blocks/formal_langs/language_cpp_parseable_language.php');

$lang = new block_formal_langs_language_cpp_parseable_language();
$result = $lang->create_from_string('int main(int  argc, char ** argv) { for(i = 0; i < argc; i++) printf("%d", argv[i]); return 0;}');

function print_node($node, $paddingcount)
{
    $result = '';
	if ($node == null) {
        $result .= 'No tree!';
		return $result;
	}
	$padding = str_repeat(' ', $paddingcount);
    if (is_array($node)) {
        $result .= $padding . '[' . PHP_EOL;
        foreach($node as $i => $nodechild) {
            $result .= print_node($nodechild, $paddingcount + 1);
            if ($i != count($node) -1) {
                $result .= $padding . ',' . PHP_EOL;
            }
        }
        $result .= $padding . ']';
        return $result;
    }
	if (!method_exists($node, 'type')) {
		$result .= var_export($node, true);
        return $result;
	}
    $value = '';
    if (is_a($node, 'block_formal_langs_token_base')) {
        $value = $node->value();
    }
    if (textlib::strlen($value)) {
        $result .= $padding . $value . PHP_EOL;
    }
	//echo $padding . $node->type() . $value . PHP_EOL;
	if (count($node->childs()))  {
		$result .= $padding . '{' . PHP_EOL;
		foreach($node->childs() as $child) {
			$result .= print_node($child, $paddingcount + 1);
		}
		$result .= $padding . '}' . PHP_EOL;
	}
    return $result;
}

function optimize_tree($nodes) {
    if (is_a($nodes, 'block_formal_langs_processed_string')) {
        $nodes->set_syntax_tree(optimize_tree($nodes->syntaxtree));
    }
    if (is_array($nodes)) {
        $nodes = array_values($nodes);
        $changed = true;
        while($changed) {
            $changed = false;
            if (count($nodes)) {
                /** @var block_formal_langs_ast_node_base $node */
                foreach($nodes as $key => $node) {
                    if (count($node->childs()) == 1) {
                        $children = $node->childs();
                        /** @var block_formal_langs_ast_node_base $child */
                        $child = $children[0];
                        $nodes[$key] = $child;
                        $changed = true;
                    }
                }
            }
        }
        if (count($nodes)) {
            foreach($nodes as $node) {
                $node->set_childs(optimize_tree($node->childs()));
            }
        }
    }
    return $nodes;
}
optimize_tree($result);
echo print_node($result->syntaxtree, 0);

