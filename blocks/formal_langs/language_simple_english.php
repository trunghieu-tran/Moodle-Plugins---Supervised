<?php 
/**
 * Defines a simple  english language lexer for correctwriting question type.
 *
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Dmitriy Mamontov, Sergey Pashaev Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
require_once($CFG->dirroot.'/blocks/formal_langs/tokens_base.php');
require_once($CFG->dirroot.'/blocks/formal_langs/language_base.php');
require_once($CFG->dirroot.'/question/type/poasquestion/jlex.php');
require_once($CFG->dirroot.'/blocks/formal_langs/simple_english_tokens.php');
class block_formal_langs_language_simple_english extends block_formal_langs_predefined_language
{
    public function __construct() {
        parent::__construct(null,null);
    }
    public function name() {
        return 'simple_english';
    }
}


class block_formal_langs_predefined_simple_english_lexer_raw extends JLexBase  {
	const YY_BUFFER_SIZE = 512;
	const YY_F = -1;
	const YY_NO_STATE = -1;
	const YY_NOT_ACCEPT = 0;
	const YY_START = 1;
	const YY_END = 2;
	const YY_NO_ANCHOR = 4;
	const YY_BOL = 256;
	var $YY_EOF = 257;

    // @var int number of  current parsed lexeme.
    private  $counter = 0;
    public function get_errors() {
        return array();
    }
    private function create_token($name, $value) {
        // get name of object
        $objectname = 'block_formal_langs_token_simple_english_' . $name;
        // create token object
        $res = new $objectname(null, strtoupper($name), $value, $this->return_pos(), $this->counter);
        // increase token count
        $this->counter++;
        return $res;
    }
    private function is_white_space($string) {
        $whitespace = array(' ', "\t", "\n", "\r", "f", "\v");
        return in_array($string[0], $whitespace);
    }
    private function return_pos() {
        $begin_line = $this->yyline;
        $begin_col = $this->yycol;
        if(strpos($this->yytext(), '\n')) {
            $lines = explode("\n", $this->yytext());
            $num_lines = count($lines);
            $end_line = $begin_line + $num_lines - 1;
            $end_col = strlen($lines[$num_lines - 1]) - 1;
        } else {
            $end_line = $begin_line;
            $end_col = $begin_col + strlen($this->yytext()) - 1;
        }
        $res = new block_formal_langs_node_position($begin_line, $end_line, $begin_col, $end_col);
        return $res;
    }
	protected $yy_count_chars = true;
	protected $yy_count_lines = true;

	function __construct($stream) {
		parent::__construct($stream);
		$this->yy_lexical_state = self::YYINITIAL;
	}

	const YYINITIAL = 0;
	static $yy_state_dtrans = array(
		0
	);
	static $yy_acpt = array(
		/* 0 */ self::YY_NOT_ACCEPT,
		/* 1 */ self::YY_NO_ANCHOR,
		/* 2 */ self::YY_NO_ANCHOR,
		/* 3 */ self::YY_NO_ANCHOR,
		/* 4 */ self::YY_NO_ANCHOR,
		/* 5 */ self::YY_NO_ANCHOR,
		/* 6 */ self::YY_NO_ANCHOR,
		/* 7 */ self::YY_NO_ANCHOR,
		/* 8 */ self::YY_NO_ANCHOR,
		/* 9 */ self::YY_NO_ANCHOR,
		/* 10 */ self::YY_NOT_ACCEPT,
		/* 11 */ self::YY_NO_ANCHOR,
		/* 12 */ self::YY_NO_ANCHOR,
		/* 13 */ self::YY_NO_ANCHOR,
		/* 14 */ self::YY_NO_ANCHOR,
		/* 15 */ self::YY_NO_ANCHOR,
		/* 16 */ self::YY_NO_ANCHOR,
		/* 17 */ self::YY_NOT_ACCEPT,
		/* 18 */ self::YY_NO_ANCHOR,
		/* 19 */ self::YY_NO_ANCHOR,
		/* 20 */ self::YY_NO_ANCHOR,
		/* 21 */ self::YY_NO_ANCHOR,
		/* 22 */ self::YY_NOT_ACCEPT,
		/* 23 */ self::YY_NO_ANCHOR,
		/* 24 */ self::YY_NO_ANCHOR,
		/* 25 */ self::YY_NO_ANCHOR,
		/* 26 */ self::YY_NOT_ACCEPT,
		/* 27 */ self::YY_NO_ANCHOR,
		/* 28 */ self::YY_NO_ANCHOR,
		/* 29 */ self::YY_NOT_ACCEPT,
		/* 30 */ self::YY_NO_ANCHOR,
		/* 31 */ self::YY_NOT_ACCEPT,
		/* 32 */ self::YY_NO_ANCHOR,
		/* 33 */ self::YY_NOT_ACCEPT,
		/* 34 */ self::YY_NO_ANCHOR,
		/* 35 */ self::YY_NOT_ACCEPT,
		/* 36 */ self::YY_NOT_ACCEPT,
		/* 37 */ self::YY_NOT_ACCEPT,
		/* 38 */ self::YY_NOT_ACCEPT,
		/* 39 */ self::YY_NOT_ACCEPT,
		/* 40 */ self::YY_NOT_ACCEPT,
		/* 41 */ self::YY_NOT_ACCEPT,
		/* 42 */ self::YY_NOT_ACCEPT,
		/* 43 */ self::YY_NOT_ACCEPT,
		/* 44 */ self::YY_NOT_ACCEPT,
		/* 45 */ self::YY_NOT_ACCEPT,
		/* 46 */ self::YY_NOT_ACCEPT,
		/* 47 */ self::YY_NOT_ACCEPT,
		/* 48 */ self::YY_NOT_ACCEPT,
		/* 49 */ self::YY_NOT_ACCEPT,
		/* 50 */ self::YY_NOT_ACCEPT,
		/* 51 */ self::YY_NOT_ACCEPT,
		/* 52 */ self::YY_NOT_ACCEPT,
		/* 53 */ self::YY_NOT_ACCEPT,
		/* 54 */ self::YY_NOT_ACCEPT,
		/* 55 */ self::YY_NOT_ACCEPT,
		/* 56 */ self::YY_NOT_ACCEPT,
		/* 57 */ self::YY_NOT_ACCEPT,
		/* 58 */ self::YY_NOT_ACCEPT,
		/* 59 */ self::YY_NOT_ACCEPT,
		/* 60 */ self::YY_NOT_ACCEPT,
		/* 61 */ self::YY_NOT_ACCEPT,
		/* 62 */ self::YY_NOT_ACCEPT,
		/* 63 */ self::YY_NOT_ACCEPT,
		/* 64 */ self::YY_NOT_ACCEPT,
		/* 65 */ self::YY_NOT_ACCEPT,
		/* 66 */ self::YY_NOT_ACCEPT,
		/* 67 */ self::YY_NOT_ACCEPT,
		/* 68 */ self::YY_NOT_ACCEPT,
		/* 69 */ self::YY_NOT_ACCEPT,
		/* 70 */ self::YY_NOT_ACCEPT,
		/* 71 */ self::YY_NOT_ACCEPT,
		/* 72 */ self::YY_NO_ANCHOR,
		/* 73 */ self::YY_NO_ANCHOR,
		/* 74 */ self::YY_NOT_ACCEPT,
		/* 75 */ self::YY_NO_ANCHOR,
		/* 76 */ self::YY_NOT_ACCEPT,
		/* 77 */ self::YY_NOT_ACCEPT,
		/* 78 */ self::YY_NOT_ACCEPT,
		/* 79 */ self::YY_NOT_ACCEPT,
		/* 80 */ self::YY_NOT_ACCEPT,
		/* 81 */ self::YY_NOT_ACCEPT,
		/* 82 */ self::YY_NOT_ACCEPT,
		/* 83 */ self::YY_NOT_ACCEPT,
		/* 84 */ self::YY_NO_ANCHOR,
		/* 85 */ self::YY_NOT_ACCEPT,
		/* 86 */ self::YY_NO_ANCHOR
	);
		static $yy_cmap = array(
 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 0, 34, 34, 0, 34, 34, 34, 34, 34, 34,
 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 31, 30, 33, 33, 33, 33, 1,
 30, 30, 33, 33, 30, 21, 29, 34, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 30, 30,
 33, 33, 33, 32, 33, 26, 26, 26, 26, 26, 26, 26, 27, 26, 26, 26, 26, 26, 26, 27,
 26, 26, 26, 27, 26, 26, 26, 26, 26, 26, 26, 34, 34, 34, 33, 34, 34, 12, 24, 20,
 6, 8, 15, 26, 17, 11, 26, 13, 9, 22, 7, 4, 23, 26, 16, 10, 2, 5, 25, 3,
 18, 14, 19, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34,
 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34,
 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34,
 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34,
 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34,
 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34,
 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 34, 35, 35,);

		static $yy_rmap = array(
 0, 1, 2, 3, 4, 5, 5, 6, 5, 5, 7, 8, 9, 5, 10, 11, 12, 13, 5, 14,
 5, 15, 16, 17, 18, 12, 19, 20, 21, 22, 10, 23, 24, 25, 26, 27, 10, 28, 29, 30,
 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50,
 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70,
 71, 72, 73, 25, 10, 74, 75,);

		static $yy_nxt = array(
array(
 -1, 1, 2, 12, 19, 12, 12, 12, 12, 12, 12, 86, 86, 12, 12, 12, 12, 12, 12, 12,
 12, 3, 73, 73, 12, 12, 12, 12, 4, 11, 18, 23, 27, 13, 5, 6,
),
array(
 -1, -1, 7, -1, -1, 10, 8, 17, 22, -1, 15, -1, -1, 26, -1, 29, 31, 33, -1, 74,
 79, -1, 8, 76, -1, 35, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 14, 12, 12, 24, 12, 12, 12, 12, 12, 24, 12, 12, 12, 12, 12, 12, 72, 12, 12,
 12, 36, 12, 12, 12, 12, 12, 24, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 37, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, 4, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, 39, -1, -1, -1, -1, -1, -1, 40, 41, 42, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, 20, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 38, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 36, 12, 12, 24, 12, 12, 12, 12, 12, 24, 12, 12, 12, 12, 12, 12, 24, 12, 12,
 12, 36, 12, 12, 12, 12, 12, 24, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28,
 28, -1, 28, 28, 28, 28, 28, 28, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 44, -1, -1, -1, -1, -1, -1, -1, -1, -1, 85, -1, -1, -1, -1,
 45, -1, -1, -1, 46, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, 63, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 9, -1, -1, -1, -1, -1, -1, 81, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 21, 12, 12, 24, 12, 12, 12, 12, 12, 24, 12, 12, 12, 12, 12, 12, 24, 12, 12,
 12, 36, 12, 12, 12, 12, 12, 24, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, 75, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28,
 28, -1, 28, 28, 28, 28, 28, 28, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 43, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 27, -1, -1, -1, -1,
),
array(
 -1, 30, 12, 12, 24, 12, 12, 12, 12, 12, 24, 12, 12, 12, 12, 12, 12, 24, 12, 12,
 12, 36, 12, 12, 12, 12, 12, 24, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 47, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 18, -1, -1, -1, -1,
),
array(
 -1, 36, 28, 28, 32, 28, 28, 28, 28, 28, 32, 28, 28, 28, 28, 28, 28, 32, 28, 28,
 28, 36, 28, 28, 28, 28, 28, 32, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 48, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, 77, -1, -1, -1, 8, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 30, 28, 28, 32, 28, 28, 28, 28, 28, 32, 28, 28, 28, 28, 28, 28, 32, 28, 28,
 28, 36, 28, 28, 28, 28, 28, 32, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, 80, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 84, 28, 28, 32, 28, 28, 28, 28, 28, 32, 28, 28, 28, 28, 28, 28, 32, 28, 28,
 28, 36, 28, 28, 28, 28, 28, 32, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, 20, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, 51, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 18, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, 52, -1, -1, -1, 53, -1, -1, 54, 41, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 78, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 25, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 55, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 57, 9, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, 8, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 58, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 83, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 9, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 59, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, 9, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, 8, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 8, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 62, -1, 63, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, 64, -1, -1, -1, -1, -1, -1, -1, 65, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 66, -1, -1, -1, -1, -1, -1, -1, -1, 49, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, 49, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, 68, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 9, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 69, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 61, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, 61, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, 8, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 70, -1, -1, -1, -1, -1, -1, -1, 71, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 49, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, 20, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, 25, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 20, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 57, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 8, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, 8, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, 16, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, 25, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 14, 12, 12, 24, 12, 12, 12, 12, 12, 24, 12, 12, 12, 12, 12, 12, 24, 12, 12,
 12, 36, 12, 12, 12, 12, 12, 24, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 36, 12, 12, 86, 12, 12, 12, 12, 12, 24, 12, 12, 12, 12, 12, 12, 24, 12, 12,
 12, 36, 12, 12, 12, 12, 12, 24, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 49, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 36, 28, 28, 32, 28, 28, 28, 28, 28, 32, 28, 28, 28, 28, 28, 28, 34, 28, 28,
 28, 36, 28, 28, 28, 28, 28, 32, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, 50, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 60, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 67, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 45, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, 61, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 56, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, 49, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, 82, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 84, 12, 12, 24, 12, 12, 12, 12, 12, 24, 12, 12, 12, 12, 12, 12, 24, 12, 12,
 12, 36, 12, 12, 12, 12, 12, 24, -1, -1, -1, -1, -1, -1, -1, -1,
),
);

	public function /*Yytoken*/ next_token ()
 {
		$yy_anchor = self::YY_NO_ANCHOR;
		$yy_state = self::$yy_state_dtrans[$this->yy_lexical_state];
		$yy_next_state = self::YY_NO_STATE;
		$yy_last_accept_state = self::YY_NO_STATE;
		$yy_initial = true;

		$this->yy_mark_start();
		$yy_this_accept = self::$yy_acpt[$yy_state];
		if (self::YY_NOT_ACCEPT != $yy_this_accept) {
			$yy_last_accept_state = $yy_state;
			$this->yy_mark_end();
		}
		while (true) {
			if ($yy_initial && $this->yy_at_bol) $yy_lookahead = self::YY_BOL;
			else $yy_lookahead = $this->yy_advance();
			$yy_next_state = self::$yy_nxt[self::$yy_rmap[$yy_state]][self::$yy_cmap[$yy_lookahead]];
			if ($this->YY_EOF == $yy_lookahead && true == $yy_initial) {
				return null;
			}
			if (self::YY_F != $yy_next_state) {
				$yy_state = $yy_next_state;
				$yy_initial = false;
				$yy_this_accept = self::$yy_acpt[$yy_state];
				if (self::YY_NOT_ACCEPT != $yy_this_accept) {
					$yy_last_accept_state = $yy_state;
					$this->yy_mark_end();
				}
			}
			else {
				if (self::YY_NO_STATE == $yy_last_accept_state) {
					throw new Exception("Lexical Error: Unmatched Input.");
				}
				else {
					$yy_anchor = self::$yy_acpt[$yy_last_accept_state];
					if (0 != (self::YY_END & $yy_anchor)) {
						$this->yy_move_end();
					}
					$this->yy_to_mark();
					switch ($yy_last_accept_state) {
						case 1:
							{ return $this->create_token('punctuation',$this->yytext()); }
						case -2:
							break;
						case 2:
							{ return $this->create_token('word',$this->yytext()); }
						case -3:
							break;
						case 3:
							{ return $this->create_token('typographic_mark',$this->yytext()); }
						case -4:
							break;
						case 4:
							{ return $this->create_token('numeric',$this->yytext()); }
						case -5:
							break;
						case 5:
							{ if (!$this->is_white_space($this->yytext())) return $this->create_token('other',$this->yytext());}
						case -6:
							break;
						case 6:
							
						case -7:
							break;
						case 7:
							{ return $this->create_token('word',$this->yytext()); }
						case -8:
							break;
						case 8:
							{ return $this->create_token('word',$this->yytext()); }
						case -9:
							break;
						case 9:
							{ return $this->create_token('word',$this->yytext()); }
						case -10:
							break;
						case 11:
							{ return $this->create_token('punctuation',$this->yytext()); }
						case -11:
							break;
						case 12:
							{ return $this->create_token('word',$this->yytext()); }
						case -12:
							break;
						case 13:
							{ return $this->create_token('typographic_mark',$this->yytext()); }
						case -13:
							break;
						case 14:
							{ return $this->create_token('word',$this->yytext()); }
						case -14:
							break;
						case 15:
							{ return $this->create_token('word',$this->yytext()); }
						case -15:
							break;
						case 16:
							{ return $this->create_token('word',$this->yytext()); }
						case -16:
							break;
						case 18:
							{ return $this->create_token('punctuation',$this->yytext()); }
						case -17:
							break;
						case 19:
							{ return $this->create_token('word',$this->yytext()); }
						case -18:
							break;
						case 20:
							{ return $this->create_token('word',$this->yytext()); }
						case -19:
							break;
						case 21:
							{ return $this->create_token('word',$this->yytext()); }
						case -20:
							break;
						case 23:
							{ return $this->create_token('punctuation',$this->yytext()); }
						case -21:
							break;
						case 24:
							{ return $this->create_token('word',$this->yytext()); }
						case -22:
							break;
						case 25:
							{ return $this->create_token('word',$this->yytext()); }
						case -23:
							break;
						case 27:
							{ return $this->create_token('punctuation',$this->yytext()); }
						case -24:
							break;
						case 28:
							{ return $this->create_token('word',$this->yytext()); }
						case -25:
							break;
						case 30:
							{ return $this->create_token('word',$this->yytext()); }
						case -26:
							break;
						case 32:
							{ return $this->create_token('word',$this->yytext()); }
						case -27:
							break;
						case 34:
							{ return $this->create_token('word',$this->yytext()); }
						case -28:
							break;
						case 72:
							{ return $this->create_token('word',$this->yytext()); }
						case -29:
							break;
						case 73:
							{ return $this->create_token('word',$this->yytext()); }
						case -30:
							break;
						case 75:
							{ return $this->create_token('word',$this->yytext()); }
						case -31:
							break;
						case 84:
							{ return $this->create_token('word',$this->yytext()); }
						case -32:
							break;
						case 86:
							{ return $this->create_token('word',$this->yytext()); }
						case -33:
							break;
						default:
						$this->yy_error('INTERNAL',false);
					case -1:
					}
					$yy_initial = true;
					$yy_state = self::$yy_state_dtrans[$this->yy_lexical_state];
					$yy_next_state = self::YY_NO_STATE;
					$yy_last_accept_state = self::YY_NO_STATE;
					$this->yy_mark_start();
					$yy_this_accept = self::$yy_acpt[$yy_state];
					if (self::YY_NOT_ACCEPT != $yy_this_accept) {
						$yy_last_accept_state = $yy_state;
						$this->yy_mark_end();
					}
				}
			}
		}
	}
}
