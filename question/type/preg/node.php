<?php
/**
 * Defines class of node, uses in dfa_preg_matcher class
 *
 * @copyright &copy; 2010  Kolesov Dmitriy 
 * @author Kolesov Dmitriy, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
if (!defined('NODE')) {
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
    define('ASSERT','1073741824');
    define('DOT','987654321');
    define('STREND','123456789');

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
}
?>