<?php
/**
 * Defines unit-tests for block_formal_langs_token_stream::token_base
 *
 * For a complete info, see qtype_correctwriting_token_base
 *
 * @copyright &copy; 2012
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package
 */
global $CFG;
require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');

class blocks_formal_langs_token_base_look_for_token_pairs_test extends UnitTestCase {

    function test_look_for_token_pairs_1() {
        $options=new block_formal_langs_comparing_options();
        $options->usecase=true;

        $correctcoverage=array(0, 1);
        $comparedcoverage=array(0, 1);

        $lexem1=new block_formal_langs_token_base(null, 'type', 'winter', null, 0);
        $lexem2=new block_formal_langs_token_base(null, 'type', 'map', null, 1);
        $lexem3=new block_formal_langs_token_base(null, 'type', 'winte', null, 0);
        $lexem4=new block_formal_langs_token_base(null, 'type', 'map', null, 1);

        $stream1=new block_formal_langs_token_stream();
        $stream1->tokens = array($lexem1,$lexem2);
        $stream2=new block_formal_langs_token_stream();
        $stream2->tokens = array($lexem3, $lexem4);
        list($result) = $stream1->look_for_token_pairs($stream2, 0.6, $options, false);
        $this->assertTrue($result->mistakeweight==1);
        $this->assertTrue($result->correctcoverage==$correctcoverage);
        $this->assertTrue($result->comparedcoverage==$comparedcoverage);
    }
}