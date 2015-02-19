<?php
/**
 * Defines unit-tests for token_base
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

class block_formal_langs_tokens_base_test extends UnitTestCase {
    function test_redaction() {
        $this->assertTrue(block_formal_langs_token_base::redaction('ggg', 'ggg')=='mmm');
        $this->assertTrue(block_formal_langs_token_base::redaction('eh', 'helllo')=='imriii');
        $this->assertTrue(block_formal_langs_token_base::redaction('helllo', 'eh')=='dmrddd');
        $this->assertTrue(block_formal_langs_token_base::redaction('helllo', 'hlello')=='mimmmdm');
        $this->assertTrue(block_formal_langs_token_base::redaction('hlello', 'helllo')=='mdmmmim');
        $this->assertTrue(block_formal_langs_token_base::redaction('sunday', 'snuday')=='mimdmmm');
        $this->assertTrue(block_formal_langs_token_base::redaction('hlelo', 'hello')=='mdmmim');
        $this->assertTrue(block_formal_langs_token_base::redaction('hello', 'hllo')=='mdmmm');
        $this->assertTrue(block_formal_langs_token_base::redaction('hlla', 'hello')=='mimmr');

    }
}
?>