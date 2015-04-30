<?php
// This file is part of Formal Languages block - https://code.google.com/p/oasychev-moodle-plugins/
//
// Formal Languages block is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Formal Languages block is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Formal Languages block.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Defines unit-tests for C++ language parser
 *
 * For a complete info, see qtype_correctwriting_token_base
 *
 * @copyright &copy; 2011 Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
global $CFG;
require_once($CFG->dirroot.'/blocks/formal_langs/language_cpp_parseable_language.php');
require_once($CFG->dirroot.'/blocks/formal_langs/tests/test_utils.php');


/**
 * Tests C++ parseable language
 */
class block_formal_langs_cpp_parseable_language_test extends PHPUnit_Framework_TestCase {
    
    /**
     * Prints tests for external testing format
     * @param $node
     * @param $paddingcount
     * @return string
     */
    public static function print_node_for_external($node, $paddingcount)
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
                $result .= self::print_node_for_external($nodechild, $paddingcount + 1);
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
            /** @noinspection PhpUndefinedMethodInspection */
            $value = $node->value();
        }
        if (core_text::strlen($value)) {
            $result .= $padding . $value . PHP_EOL;
        }
        //echo $padding . $node->type() . $value . PHP_EOL;
        /** @noinspection PhpUndefinedMethodInspection */
        if (count($node->childs()))  {
            $result .= $padding . '(' . $node->type()  . ') {' . PHP_EOL;
            /** @noinspection PhpUndefinedMethodInspection */
            foreach($node->childs() as $child) {
                $result .= self::print_node_for_external($child, $paddingcount + 1);
            }
            $result .= $padding . '}' . PHP_EOL;
        }
        return $result;
    }

    /**
     * Builds a recursive list of files
     * @param string $dir directory
     * @return array an array list
     */
    public function build_recursive_list_of_files($dir)
    {
        $result = array();
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != '.' && $file != "..") {
                        $fullname = $dir . DIRECTORY_SEPARATOR . $file;
                        if (is_dir($fullname)) {
                            $dirresult = self::build_recursive_list_of_files($fullname);
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

    /**
     * Builds a list for directory
     * @param string $dir directory
     * @return array list
     */
    public function build_recursive_list($dir)
    {
        $length = strlen($dir . DIRECTORY_SEPARATOR);
        $files = self::build_recursive_list_of_files($dir);
        if (count($files))
        {
            $tmpfiles = array();
            foreach($files as $v)
            {
                $tmpfiles[] = substr($v, $length);
            }
            $files = $tmpfiles;
        }
        return $files;
    }

    /**
     * Puts result to path
     * @param $parent
     * @param $filename
     * @param $string
     */
    public function put_result($parent, $filename, $string)
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

    /**
     * Run tests from external files
     */
    public function test_external_files() {
        $parent = dirname(dirname(__FILE__));
        $inputdir = $parent . DIRECTORY_SEPARATOR . "parsertests" . DIRECTORY_SEPARATOR .  "input";
        $ethalondir =  $parent . DIRECTORY_SEPARATOR . "parsertests" . DIRECTORY_SEPARATOR .  "ethalon";
        $resultdir =  $parent . DIRECTORY_SEPARATOR . "parsertests" . DIRECTORY_SEPARATOR .  "result";
        $inputs = self::build_recursive_list($inputdir);

        if (!is_dir($ethalondir))
        {
            mkdir($ethalondir);
        }

        if (!is_dir($resultdir))
        {
            mkdir($resultdir);
        }

        if (count($inputs)) {
            foreach($inputs as $file)
            {
                /** @noinspection PhpIncludeInspection */
                include($inputdir . DIRECTORY_SEPARATOR . $file);

                $name = preg_replace("/\\.php$/", ".txt", $file);

                $lang = new block_formal_langs_language_cpp_parseable_language();
                /** @noinspection PhpUndefinedMethodInspection */
                /** @noinspection PhpUndefinedVariableInspection */
                $lang->parser()->set_namespace_tree($namespacetree);
                /** @noinspection PhpUndefinedVariableInspection */
                $result = $lang->create_from_string($string);
                $newstring = self::print_node_for_external($result->syntaxtree, 0);
                $name = preg_replace("/\\.php$/", ".txt", $file);
                $ethalonfile = $ethalondir . DIRECTORY_SEPARATOR . $name;
                $ethalon = @file_get_contents($ethalonfile);
                if ($ethalon === false) {
                    /** @noinspection PhpUndefinedMethodInspection */
                    $this->assertTrue(false, 'Can\'t find some ethalon file' .  $ethalonfile);
                } else {
                    self::put_result($resultdir, $name, $newstring);
                    $status = false;
                    $ethalon = str_replace("\r\n", "\n", $ethalon);
                    $newstring = str_replace("\r\n", "\n", $newstring);
                    if ($ethalon == $newstring) {
                        $status = true;
                    }
                    /** @noinspection PhpUndefinedMethodInspection */
                    $this->assertTrue($status, 'Failed test ' .  $inputdir . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
    }
}