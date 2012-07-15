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
require_once($CFG->dirroot.'/blocks/formal_langs/jlex.php');
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
// This wrapper is created because there is no way, we can create other lexer without stream
// And current architecture won't allow to do so, because mostly we need string.
class block_formal_langs_predefined_simple_english_lexer {
  public function tokenize(&$processedstring) {
        $string = $processedstring->string;
        $file = fopen('data://text/plain;base64,' . base64_encode($string), 'r');
        $lexer = new block_formal_langs_predefined_simple_english_lexer_raw($file);
        //Now, we are splitting text into lexemes
        $tokens = array();
        while ($token = $lexer->next_token()) {
            $tokens[] = $token;
        }
        // Due to some bugs in PHP, we will use this
        // to avoid errors
        $processedstring->get_stream()->tokens = $tokens;
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
  private function create_token($name, $value) {
        // get name of object
        $objectname = 'block_formal_langs_language_simple_english_' . $name;
        // create token object
        $res = new $objectname(null, strtoupper($name), $value, $this->return_pos(), $this->counter);
        // increase token count
        $this->counter++;
        return $res;
    }
  private function return_pos() {
        $begin_line = $this->yyline;
        $begin_col = $this->yycol;
        if(strpos($this->yytext(), '\n')) {
            $lines = explode("\n", $this->yytext());
            $num_lines = count($lines);
            $end_line = $begin_line + $num_lines - 1;
            $end_col = strlen($lines[$num_lines -1]);
        } else {
            $end_line = $begin_line;
            $end_col = $begin_col + strlen($this->yytext());
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
		/* 8 */ self::YY_NOT_ACCEPT,
		/* 9 */ self::YY_NO_ANCHOR,
		/* 10 */ self::YY_NO_ANCHOR,
		/* 11 */ self::YY_NOT_ACCEPT,
		/* 12 */ self::YY_NO_ANCHOR,
		/* 13 */ self::YY_NO_ANCHOR,
		/* 14 */ self::YY_NOT_ACCEPT,
		/* 15 */ self::YY_NO_ANCHOR,
		/* 16 */ self::YY_NO_ANCHOR
	);
		static $yy_cmap = array(
 14, 14, 14, 14, 14, 14, 14, 14, 14, 8, 0, 14, 14, 0, 14, 14, 14, 14, 14, 14,
 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 8, 11, 10, 13, 14, 13, 13, 2,
 10, 10, 13, 13, 10, 13, 9, 14, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 10, 10,
 13, 13, 13, 12, 13, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 14, 14, 14, 13, 14, 14, 1, 1, 1,
 1, 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 4, 3, 6, 1, 1, 1,
 1, 1, 1, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14,
 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14,
 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14,
 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14,
 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14,
 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14,
 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 14, 15, 15,);

		static $yy_rmap = array(
 0, 1, 2, 3, 2, 2, 2, 2, 4, 5, 6, 7, 2, 8, 9, 4, 10,);

		static $yy_nxt = array(
array(
 -1, 1, 2, 1, 1, 1, 1, 3, 4, 10, 2, 13, 16, 5, 6, 7,
),
array(
 -1, 1, 8, 9, 1, 1, 1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, 3, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, 12, 14, -1, 12, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 1, 15, 9, 1, 1, 1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 11, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 2, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 16, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 12, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 2, -1, -1, -1, -1,
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
							{ return $this->create_token("word",$this->yytext()); }
						case -2:
							break;
						case 2:
							{ return $this->create_token("punctuation",$this->yytext()); }
						case -3:
							break;
						case 3:
							{ return $this->create_token("numeric",$this->yytext()); }
						case -4:
							break;
						case 4:
							{  }
						case -5:
							break;
						case 5:
							{ return $this->create_token("typographic_mark",$this->yytext()); }
						case -6:
							break;
						case 6:
							{ return $this->create_token("other",$this->yytext());}
						case -7:
							break;
						case 7:
							
						case -8:
							break;
						case 9:
							{ return $this->create_token("word",$this->yytext()); }
						case -9:
							break;
						case 10:
							{ return $this->create_token("punctuation",$this->yytext()); }
						case -10:
							break;
						case 12:
							{ return $this->create_token("word",$this->yytext()); }
						case -11:
							break;
						case 13:
							{ return $this->create_token("punctuation",$this->yytext()); }
						case -12:
							break;
						case 15:
							{ return $this->create_token("word",$this->yytext()); }
						case -13:
							break;
						case 16:
							{ return $this->create_token("punctuation",$this->yytext()); }
						case -14:
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
