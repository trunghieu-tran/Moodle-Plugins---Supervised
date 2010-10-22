<?php
/**
 * Defines class of node, uses in dfa_preg_matcher class
 *
 * @copyright &copy; 2010  Kolesov Dmitriy 
 * @author Kolesov Dmitriy, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
if (!defined('PREG_NODE')) {
    define('PREG_NODE','preg_node');
    define('LEAF',0);
    define('NODE',1);
    define('LEAF_CHARCLASS',2);
    define('LEAF_EMPTY',3);
    define('LEAF_END',4);
    define('LEAF_LINK',5);
    define('LEAF_METASYMBOLDOT',6);
    define('LEAF_WORDBREAK',20);
    define('LEAF_WORDNOTBREAK',21);
    define('NODE_CONC',7);
    define('NODE_ALT',8);
    define('NODE_ITER',9);
    define('NODE_SUBPATT',10);
    define('NODE_CONDSUBPATT',11);
    define('NODE_QUESTQUANT',12);
    define('NODE_PLUSQUANT',13);
    define('NODE_QUANT',14);
    define('NODE_ASSERTTF',15);
    define('NODE_ASSERTTB',16);
    define('NODE_ASSERTFF',17);
    define('NODE_ASSERTFB',18);
    define('NODE_ONETIMESUBPATT',19);
    define('ASSERT',1073741824);
    define('DOT',987654321);
    define('STREND',123456789);
    define('ERROR',999999);

    class node {
        var $type;
        var $subtype;
        var $firop;
        var $secop;
        var $thirdop;
        var $nullable;
        var $number;
        var $firstpos;
        var $lastpos;
        var $direction;
        var $greed;
        var $chars;
        var $leftborder;
        var $rightborder;
        var $error;/*
                   * 0 - no error
                   * 1 - incorrect interval in character class
                   */
    
        function name() {
            return 'node';
        }
    }
//разобраться с ф-цией копирование поддерева
  /*  class preg_node {
        //constants for number of special leaf types
        const dot = 987654321;
        const strend = 123456789;
        const assert = 1073741824;
        const error = 999999;
        //constants for node types
        const leaf_charclass = 0;
        const leaf_strend = 1;
        const leaf_link = 2;
        const leaf_empty = 3;
        const leaf_dot = 4;
        const leaf_wordbreak = 5;
        const leaf_wordnotbreak = 6;
        const node_conc = 7;
        const node_alt = 8;
        const node_iter = 9;
        const node_quest = 10;
        const node_quant = 11;
        const node_subpatt = 12;
        const node_condsubpatt = 13;
        const node_onetimesubpatt = 14;
        const node_asserttf = 15;
        const node_assertff = 16;
        const node_asserttb = 17;
        const node_assertfb = 18;
        var $subtype;
        var $error;
    }
    class preg_node_leaf extends preg_node {
        var $direction;
        var $chars;
        /**
        *Function determine character included in this character class, or not
        *@param $char character which need search in this charclass
        *@return true if included else false
        *//*
        public function is_character_included($char) {
            switch ($this->subtype) {
                case preg_node::leaf_charclass:
                    if (strpos($this->chars, $char) !== false) {
                        $result = true;
                    } else {
                        $result = false;
                    }
                    break;
                case preg_node::leaf_dot:
                    $result = true;
                    break;
                case preg_node::leaf_strend:
                    $result = $char === 123456789 || $char === 0;
                    break;
                default:
                    $result = false;
                    break;
            }
            return $result;
        }
    }
    class preg_node_conc extends preg_node {
        var $firop;
        var $secop;
    }
    class preg_node_alt extends preg_node {
        var $firop;
        var $secop;
    }
    class preg_node_iter extends preg_node {
        var $firop;
        var $greed;
    }
    class preg_node_quest extends preg_node {
        var $firop;
        var $greed;
    }
    class preg_node_quant extends preg_node {
        var $firop;
        var $greed;
        var $leftborder;
        var $rightborder;
    }
    class preg_node_subpatt extends preg_node {
        var $firop;
    }
    class preg_node_condsubpatt extends preg_node {
        var $firop;
        var $secop;
        var $thirdop;
    }
    class preg_node_onetimesubpatt extends preg_node {
        var $firop;
    }
    class preg_node_assert extends preg_node {
        var $firop;
    }*/
}
?>