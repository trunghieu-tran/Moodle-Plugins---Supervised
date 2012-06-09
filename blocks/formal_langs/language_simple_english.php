<? 
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
  public function tokenize($processedstring) {
        $lexer = new block_formal_langs_predefined_simple_english_lexer_raw(fopen('data://text/plain;base64,' . base64_encode($processedstring->string), 'r'));
        //Now, we are splitting text into lexemes
        $tokens = array();
        while ($token = $lexer->next_token()) {
            $tokens[] = $token;
        }
        $stream = new block_formal_langs_token_stream();
        $stream->tokens = $tokens;
        $processedstring->stream = $stream;
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
  private function create_token($type, $value) {
        // create token object
        $res = new block_formal_langs_token_base(null, $type, $value, $this->return_pos(), $this->counter);
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
		/* 7 */ self::YY_NO_ANCHOR
	);
		static $yy_cmap = array(
 6, 6, 6, 6, 6, 6, 6, 6, 6, 3, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6,
 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 3, 4, 6, 6, 6, 6, 6, 6,
 6, 6, 6, 5, 4, 6, 4, 5, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 4, 4,
 6, 6, 6, 4, 6, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 6, 6, 6, 6, 6, 6, 1, 1, 1,
 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
 1, 1, 1, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6,
 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6,
 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6,
 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6,
 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6,
 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6,
 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 0, 0,);

		static $yy_rmap = array(
 0, 1, 2, 3, 1, 1, 1, 1,);

		static $yy_nxt = array(
array(
 1, 2, 3, 4, 5, 6, 7,
),
array(
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 2, -1, -1, -1, -1, -1,
),
array(
 -1, -1, 3, -1, -1, -1, -1,
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
							
						case -2:
							break;
						case 2:
							{ return $this->create_token("word",$this->yytext()); }
						case -3:
							break;
						case 3:
							{ return $this->create_token("number",$this->yytext()); }
						case -4:
							break;
						case 4:
							{  }
						case -5:
							break;
						case 5:
							{ return $this->create_token("sign",$this->yytext()); }
						case -6:
							break;
						case 6:
							{ return $this->create_token("mathsigns",$this->yytext()); }
						case -7:
							break;
						case 7:
							{ return $this->create_token("other",$this->yytext());}
						case -8:
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
