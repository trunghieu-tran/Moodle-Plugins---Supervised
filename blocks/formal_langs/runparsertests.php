<?php
global $CFG;

define('MOODLE_INTERNAL', 1);

$CFG = new stdClass();
$CFG->dirroot = dirname(dirname(dirname(__FILE__)));
$CFG->libdir = $CFG->dirroot . '/lib';

require_once($CFG->dirroot .'/blocks/formal_langs/language_cpp_parseable_language.php');
require_once($CFG->dirroot .'/lib/classes/text.php');


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
    if (core_text::strlen($value)) {
        $result .= $padding . $value . PHP_EOL;
    }
	//echo $padding . $node->type() . $value . PHP_EOL;
	if (count($node->childs()))  {
		$result .= $padding . '(' . $node->type()  . ') {' . PHP_EOL;
		foreach($node->childs() as $child) {
			$result .= print_node($child, $paddingcount + 1);
		}
		$result .= $padding . '}' . PHP_EOL;
	}
    return $result;
}

function build_recursive_list_of_files($dir) 
{
	$result = array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if ($file != '.' && $file != "..") {
					$fullname = $dir . DIRECTORY_SEPARATOR . $file;
					if (is_dir($fullname)) {
						$dirresult = build_recursive_list_of_files($fullname);
						if (count($result) == 0) {
							$result = $dirresult;
						} else {
							if (count($dirresult)) {
								$result = array_merge($dirresult, $result);
							}
						}
					} 
					else
					{
						$result[] = $fullname;
					}
				}
			}
			closedir($dh);
		}
	}
	return $result;
}

function build_recursive_list($dir)
{
	$length = strlen($dir . DIRECTORY_SEPARATOR);
	$files = build_recursive_list_of_files($dir);
	if (count($files))
	{
		$tmpfiles = array();
		foreach($files as $k => $v)
		{
			$tmpfiles[] = substr($v, $length);
		}
		$files = $tmpfiles;
	}
	return $files;
}


function put_result($parent, $filename, $string)
{
	$parts = explode(DIRECTORY_SEPARATOR, $filename);
	unset($parts[count($parts) - 1]);
	if (count($parts))
	{
		$currentpath = $parent;
		foreach($parts as $part)
		{
			$dir = $currentpath . DIRECTORY_SEPARATOR . $part;
			if (is_dir($dir) == false)
			{
				mkdir($dir);
			}
			$currentpath = $dir;
		}
	}
	
	file_put_contents($parent . DIRECTORY_SEPARATOR . $filename, trim($string));
}

$inputdir = "parsertests" . DIRECTORY_SEPARATOR .  "input";
$ethalondir = "parsertests" . DIRECTORY_SEPARATOR .  "ethalon";
$resultdir = "parsertests" . DIRECTORY_SEPARATOR .  "result";

$inputs = build_recursive_list($inputdir);
$state = false;
$buildethalon = false;
for($i = 1; $i < count($argv); $i++)
{
	if ($state)
	{
		$inputs = array( $argv[$i] );
		$state = false;
	}
	else
	{
		if ($argv[$i] == "--run-only")
		{
			$state = true;
		}
		
		if ($argv[$i] == "--build-ethalon")
		{
			$buildethalon = true;
		}
	}
}


if (!is_dir($ethalondir))
{
	mkdir($ethalondir);	
}

if (!is_dir($resultdir))
{
	mkdir($resultdir);	
}

$defaultstring = str_repeat(".", 80);

function implode_string ($string, $begin, $end)
{
	$result = $string;
	if (strlen($begin) + strlen($end) > strlen($string))
	{
		$result = $string . "\r\n" . $string;
	}
	
	for($i = 0; $i < strlen($begin); $i++)
	{
		$result[$i] = $begin[$i]; 
	}
	
	for($i = 0;  $i < strlen($end); $i++)
	{
		$result[strlen($result) - strlen($end) + $i] = $end[$i];
	}	
	return $result;
}
$failedethalons = false;
if (count($inputs)) {
	foreach($inputs as $file)
	{
		if (isset($donotstripcomments)) {
			unset($donotstripcomments);
		}
		include($inputdir . DIRECTORY_SEPARATOR . $file);		
		$name = preg_replace("/\.php$/", ".txt", $file);
		
		$lang = new block_formal_langs_language_cpp_parseable_language();
		$lang->parser()->set_namespace_tree($namespacetree);
		if (isset($donotstripcomments)) {
			$lang->parser()->set_strip_comments(false);
		}
		$result = $lang->create_from_string($string);
		$newstring = print_node($result->syntaxtree, 0);
		if ($buildethalon)
		{
			put_result($ethalondir, $name, $newstring);
			echo implode_string($defaultstring, $file, "OK");
		}		
		else
		{
			$name = preg_replace("/\.php$/", ".txt", $file);
			$ethalon = @file_get_contents($ethalondir . DIRECTORY_SEPARATOR . $name);
			if ($ethalon === false)
			{
				echo implode_string($defaultstring, $name, "N/A");
				$failedethalons = true;
			}
			else
			{
				put_result($resultdir, $name, $newstring);
				$status = "FAIL";
				if ($ethalon == $newstring)
				{
					$status = "OK";
				}
				echo implode_string($defaultstring, $file, $status);
			}
		}		
	}
}

if ($failedethalons)
{
	echo "\n";
	echo "Some of the ethalons are not found. Did you forgot to run runtests.php --build-ethalon on them?";
}
