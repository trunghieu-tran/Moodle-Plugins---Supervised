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
    /**
     * Tests, whether redaction matches
     * @param string $result resulting redaction
     * @param string $ethalon an ethalon string
     */
    protected function assert_redaction($result, $ethalon) {
        $this->assertTrue($result == $ethalon, $result);
    }
    public function test_redaction() {
        $this->assert_redaction(block_formal_langs_token_base::redaction('ggg', 'ggg'), 'mmm');
        $this->assert_redaction(block_formal_langs_token_base::redaction('eh', 'helllo'),'tiiii');
        $this->assert_redaction(block_formal_langs_token_base::redaction('helllo', 'eh'),'tdddd');
        $this->assert_redaction(block_formal_langs_token_base::redaction('helllo', 'hlello'),'mtmmm');
        $this->assert_redaction(block_formal_langs_token_base::redaction('hlello', 'helllo'),'mtmmm');
        $this->assert_redaction(block_formal_langs_token_base::redaction('sunday', 'snuday'),'mtmmm');
        $this->assert_redaction(block_formal_langs_token_base::redaction('hlelo', 'hello'),'mtmm');
        $this->assert_redaction(block_formal_langs_token_base::redaction('hello', 'hllo'),'mdmmm');
        $this->assert_redaction(block_formal_langs_token_base::redaction('hlla', 'hello'),'mimmr');
        $this->assert_redaction(block_formal_langs_token_base::redaction('paratim', 'separator'),'iimmmmmrr');
        $this->assert_redaction(block_formal_langs_token_base::redaction('paratim', 'rator'),'ddmmmrr');
    }
}
?>