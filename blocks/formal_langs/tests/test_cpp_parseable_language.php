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
class block_formal_langs_cpp_language_test extends PHPUnit_Framework_TestCase {
    /**
     * Optimizes tree replacing nodes with one child with their child
     * @param array|block_formal_langs_processed_string $nodes nodes
     * @return array optimized array of trees
     */
    static function optimize_tree($nodes) {
        if (is_a($nodes, 'block_formal_langs_processed_string')) {
            $nodes->set_syntax_tree(self::optimize_tree($nodes->syntaxtree));
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
                    $node->set_childs(self::optimize_tree($node->childs()));
                }
            }
        }
        return $nodes;
    }

    /**
     * Makes a tree from string
     * @param string $string
     * @return array
     */
    static function make_from_string($string) {
        $lang = new block_formal_langs_language_cpp_parseable_language();
        $processedstring = $lang->create_from_string($string);
        self::optimize_tree($processedstring);
        return $processedstring->syntaxtree;
    }

    /**
     * Converts a node tree to string
     * @param array|block_formal_langs_ast_node_base $node a node
     * @param int $paddingcount count of paddings
     * @return string
     */
    static function print_node($node, $paddingcount)
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
                $result .= self::print_node($nodechild, $paddingcount + 1);
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
                $result .= self::print_node($child, $paddingcount + 1);
            }
            $result .= $padding . '}' . PHP_EOL;
        }
        return $result;
    }

    function compare_trees($nodes, $string) {
        $originaltestedstring = self::print_node($nodes, 0);
        $testedstring = str_replace(array("\r", "\n", ' '), array('', '', ''), $originaltestedstring);
        $string = str_replace(array("\r", "\n", ' '), array('', '', ''), $string);
        $this->assertTrue($string == $testedstring, $originaltestedstring);
    }

    /**
     * Tests parsing simple literal
     */
    public function test_numeric_literal() {
        $trees = self::make_from_string('11');
        $this->assertTrue($trees[0]->value() == '11');
    }

    /**
     * Tests parsing empty string
     */
    public function test_empty() {
        $trees = self::make_from_string('');
        $this->assertTrue(count($trees) == 0);
    }

    /**
     * Tests parsing two simple literals
     */
    public function test_two_literals() {
        $trees = self::make_from_string('11  11');
        $this->assertTrue($trees[0]->value() == '11');
        $this->assertTrue($trees[1]->value() == '11');
    }

    /**
     * Tests simple statements
     */
    public function test_statement1() {
        $trees = self::make_from_string('11 + (11 * 11) % 11 << 11 >> 11;');
        $result = '
[
 {
  {
   {
    {
     11
     +
     {
      {
       (
       {
        11
        *
        11
       }
       )
      }
      %
      11
     }
    }
    <<
    11
   }
   >>
   11
  }
  ;
 }
]';
        $this->compare_trees($trees, $result);
    }

    /**
     * Tests variable definition
     */
    public function test_definition1() {
        $trees = self::make_from_string('int a = 1 + 1;');
        $result = '
[
 {
  {
   int
   a
   =
   {
    1
    +
    1
   }
  }
  ;
 }
]';
        $this->compare_trees($trees, $result);
    }

    /**
     * Tests typedef definitions
     */
    public function test_typedef1() {
        $trees = self::make_from_string('typedef int __int32; __int32 A = 32 + 64;');
        $result = '
[
 {
  {
   typedef
   int
   __int32
   ;
  }
  {
   {
    __int32
    A
    =
    {
     32
     +
     64
    }
   }
   ;
  }
 }
]';
        $this->compare_trees($trees, $result);
    }

    /**
     * Tests if-then-else condition
     */
    public function test_if_else1()  {
        $trees = self::make_from_string('if (a > 22) a+=5; else { int b = rand(); a += b; }');
        $result = '
[
 {
  {
   if
   (
   {
    a
    >
    22
   }
   )
   {
    {
     a
     +=
     5
    }
    ;
   }
  }
  else
  {
   {
   {
    {
     {
      int
      b
      =
      {
       rand
       (
       )
      }
     }
     ;
    }
    {
     {
      a
      +=
      b
     }
     ;
    }
   }
   }
  }
 }
]';
        $this->compare_trees($trees, $result);
    }

    public function test_empty_switch() {
        $trees = self::make_from_string('switch(a) { };');
        $result = '
[
 {
  {
   switch
   (
   a
   )
   {
   }
  }
  ;
 }
]
        ';
        $this->compare_trees($trees, $result);
    }

    public function test_switch_with_one_condition() {
        $trees = self::make_from_string('switch(a) { case 1: { int test = a + b; } };');
        $result = '
[
 {
  {
   switch
   (
   a
   )
   {
   {
    case
    1
    :
    {
     {
     {
      {
       int
       test
       =
       {
        a
        +
        b
       }
      }
      ;
     }
     }
    }
   }
   }
  }
  ;
 }
]
        ';
        $this->compare_trees($trees, $result);
    }

    public function test_switch_common() {
        $trees = self::make_from_string('switch(a) { case 1: { int test = a + b; } case 2: a+=x; default: a += x * 2; };');
        $result = '
[
 {
  {
   switch
   (
   a
   )
   {
   {
    {
     {
      case
      1
      :
      {
       {
       {
        {
         int
         test
         =
         {
          a
          +
          b
         }
        }
        ;
       }
       }
      }
     }
     {
      case
      2
      :
      {
       {
        a
        +=
        x
       }
       ;
      }
     }
    }
    {
     default
     :
     {
      {
       a
       +=
       {
        x
        *
        2
       }
      }
      ;
     }
    }
   }
   }
  }
  ;
 }
]
        ';
        $this->compare_trees($trees, $result);
    }

    public function test_try() {
        $trees = self::make_from_string('typedef int Exception; try { a = unescaped_call(); } catch(Exception ex) { int a = 2;} catch(...) {}');
        $result = '
[
 {
  {
   typedef
   int
   Exception
   ;
  }
  {
   {
    try
    {
    {
     {
      a
      =
      {
       unescaped_call
       (
       )
      }
     }
     ;
    }
    }
   }
   {
    {
     catch
     (
     {
      Exception
      ex
     }
     )
     {
     {
      {
       int
       a
       =
       2
      }
      ;
     }
     }
    }
    {
     catch
     (
     ...
     )
     {
     }
    }
   }
  }
 }
]
        ';
        $this->compare_trees($trees, $result);
    }
}