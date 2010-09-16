<?php # vim:ft=php
require_once($CFG->dirroot . '/question/type/preg/jlex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_parser.php');
require_once($CFG->dirroot . '/question/type/preg/node.php');
function form_node($type, $subtype, $charclass = null, $leftborder = null, $rightborder = null, $greed = true) {
    $result = new node;
    $result->type = $type;
    $result->subtype = $subtype;
    $result->greed = $greed;
    if (isset($charclass)) {
        $result->chars = $charclass;
    }
    if (isset($leftborder)) {
        $result->leftborder = $leftborder;
    }
    if (isset($rightborder)) {
        $result->rightborder = $rightborder;
    }
    $result->direction = true;
    return $result;
}
function form_res($type, $value) {
    $result->type = $type;
    $result->value = $value;
    return $result;
}
function form_num_interval(&$cc, $startchar, $endchar) {
    if(ord($startchar) < ord($endchar)) {
        $char = ord($startchar);
        while($char <= ord($endchar)) {
            $cc->chars .= chr($char);
            $char++;
        }
    } else {
        $cc->error = 1;
    }
}


class Yylex extends JLexBase  {
	const YY_BUFFER_SIZE = 512;
	const YY_F = -1;
	const YY_NO_STATE = -1;
	const YY_NOT_ACCEPT = 0;
	const YY_START = 1;
	const YY_END = 2;
	const YY_NO_ANCHOR = 4;
	const YY_BOL = 128;
	var $YY_EOF = 129;
	protected $yy_count_chars = true;
	protected $yy_count_lines = true;

	function __construct($stream) {
		parent::__construct($stream);
		$this->yy_lexical_state = self::YYINITIAL;
	}

	const YYINITIAL = 0;
	const CHARCLASS = 1;
	static $yy_state_dtrans = array(
		0,
		76
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
		/* 10 */ self::YY_NO_ANCHOR,
		/* 11 */ self::YY_NO_ANCHOR,
		/* 12 */ self::YY_NO_ANCHOR,
		/* 13 */ self::YY_NO_ANCHOR,
		/* 14 */ self::YY_NO_ANCHOR,
		/* 15 */ self::YY_NO_ANCHOR,
		/* 16 */ self::YY_NO_ANCHOR,
		/* 17 */ self::YY_NO_ANCHOR,
		/* 18 */ self::YY_NO_ANCHOR,
		/* 19 */ self::YY_NO_ANCHOR,
		/* 20 */ self::YY_NO_ANCHOR,
		/* 21 */ self::YY_NO_ANCHOR,
		/* 22 */ self::YY_NO_ANCHOR,
		/* 23 */ self::YY_NO_ANCHOR,
		/* 24 */ self::YY_NO_ANCHOR,
		/* 25 */ self::YY_NO_ANCHOR,
		/* 26 */ self::YY_NO_ANCHOR,
		/* 27 */ self::YY_NO_ANCHOR,
		/* 28 */ self::YY_NO_ANCHOR,
		/* 29 */ self::YY_NO_ANCHOR,
		/* 30 */ self::YY_NO_ANCHOR,
		/* 31 */ self::YY_NO_ANCHOR,
		/* 32 */ self::YY_NO_ANCHOR,
		/* 33 */ self::YY_NO_ANCHOR,
		/* 34 */ self::YY_NO_ANCHOR,
		/* 35 */ self::YY_NO_ANCHOR,
		/* 36 */ self::YY_NO_ANCHOR,
		/* 37 */ self::YY_NO_ANCHOR,
		/* 38 */ self::YY_NO_ANCHOR,
		/* 39 */ self::YY_NO_ANCHOR,
		/* 40 */ self::YY_NO_ANCHOR,
		/* 41 */ self::YY_NO_ANCHOR,
		/* 42 */ self::YY_NO_ANCHOR,
		/* 43 */ self::YY_NO_ANCHOR,
		/* 44 */ self::YY_NO_ANCHOR,
		/* 45 */ self::YY_NO_ANCHOR,
		/* 46 */ self::YY_NO_ANCHOR,
		/* 47 */ self::YY_NO_ANCHOR,
		/* 48 */ self::YY_NO_ANCHOR,
		/* 49 */ self::YY_NO_ANCHOR,
		/* 50 */ self::YY_NO_ANCHOR,
		/* 51 */ self::YY_NO_ANCHOR,
		/* 52 */ self::YY_NO_ANCHOR,
		/* 53 */ self::YY_NO_ANCHOR,
		/* 54 */ self::YY_NO_ANCHOR,
		/* 55 */ self::YY_NO_ANCHOR,
		/* 56 */ self::YY_NO_ANCHOR,
		/* 57 */ self::YY_NO_ANCHOR,
		/* 58 */ self::YY_NO_ANCHOR,
		/* 59 */ self::YY_NO_ANCHOR,
		/* 60 */ self::YY_NO_ANCHOR,
		/* 61 */ self::YY_NOT_ACCEPT,
		/* 62 */ self::YY_NO_ANCHOR,
		/* 63 */ self::YY_NO_ANCHOR,
		/* 64 */ self::YY_NO_ANCHOR,
		/* 65 */ self::YY_NOT_ACCEPT,
		/* 66 */ self::YY_NO_ANCHOR,
		/* 67 */ self::YY_NO_ANCHOR,
		/* 68 */ self::YY_NOT_ACCEPT,
		/* 69 */ self::YY_NOT_ACCEPT,
		/* 70 */ self::YY_NOT_ACCEPT,
		/* 71 */ self::YY_NOT_ACCEPT,
		/* 72 */ self::YY_NOT_ACCEPT,
		/* 73 */ self::YY_NOT_ACCEPT,
		/* 74 */ self::YY_NOT_ACCEPT,
		/* 75 */ self::YY_NOT_ACCEPT,
		/* 76 */ self::YY_NOT_ACCEPT,
		/* 77 */ self::YY_NOT_ACCEPT,
		/* 78 */ self::YY_NOT_ACCEPT,
		/* 79 */ self::YY_NOT_ACCEPT,
		/* 80 */ self::YY_NOT_ACCEPT,
		/* 81 */ self::YY_NOT_ACCEPT,
		/* 82 */ self::YY_NOT_ACCEPT,
		/* 83 */ self::YY_NOT_ACCEPT,
		/* 84 */ self::YY_NOT_ACCEPT,
		/* 85 */ self::YY_NO_ANCHOR,
		/* 86 */ self::YY_NO_ANCHOR,
		/* 87 */ self::YY_NOT_ACCEPT
	);
		static $yy_cmap = array(
 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17,
 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 14, 17, 17, 33, 17, 17, 17,
 9, 10, 2, 3, 6, 34, 16, 17, 21, 5, 5, 5, 5, 5, 5, 5, 5, 5, 12, 17,
 15, 13, 11, 1, 17, 36, 24, 36, 26, 36, 36, 36, 36, 36, 36, 36, 36, 36, 36, 36,
 36, 36, 36, 30, 36, 36, 36, 28, 36, 36, 36, 8, 19, 20, 32, 17, 17, 35, 23, 35,
 25, 35, 35, 35, 35, 35, 35, 35, 35, 35, 35, 35, 35, 35, 35, 29, 31, 35, 35, 27,
 22, 35, 35, 4, 18, 7, 17, 17, 0, 0,);

		static $yy_rmap = array(
 0, 1, 2, 3, 4, 5, 1, 6, 1, 1, 1, 1, 1, 1, 1, 1, 7, 1, 8, 1,
 1, 1, 1, 1, 1, 1, 1, 1, 1, 9, 1, 1, 1, 1, 1, 10, 1, 11, 1, 1,
 1, 12, 1, 1, 1, 1, 1, 13, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
 1, 14, 1, 1, 15, 16, 17, 18, 19, 20, 17, 21, 22, 23, 24, 25, 26, 27, 28, 29,
 30, 31, 32, 33, 34, 35, 36, 37,);

		static $yy_nxt = array(
array(
 1, 2, 3, 4, 61, 5, 62, -1, 6, 7, 8, 62, 62, 62, 62, 62, 9, 62, 10, 65,
 -1, 5, 62, 62, 62, 62, 62, 62, 62, 62, 62, 62, 11, 12, 62, 62, 62,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 13, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 14, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 15, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 70, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 70, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 16, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 31, 32, 33, 34, 73, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 63, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 63, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 36, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 42, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 43, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 44, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 57, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 68, 69, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 68, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 78, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 78, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 79, -1, -1,
),
array(
 -1, 17, 17, 17, 17, 18, -1, 17, 17, 17, 17, -1, -1, -1, -1, -1, 17, -1, 17, 19,
 17, 85, 87, 20, 21, 22, 23, 24, 25, 26, 27, 28, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 30, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 30, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 82, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 68, 71, 29, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 68, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 72, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 72, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 75, -1, 35, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 75, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 72, -1, 37, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 72, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 38, 39, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 40, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 40, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 75, -1, 41, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 75, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 1, 45, 45, 45, 45, 64, 45, 45, -1, 45, 45, 45, 45, 45, 45, 45, 45, 45, 45, 77,
 46, 64, 67, 67, 86, 67, 86, 67, 86, 67, 86, 67, 47, 45, 48, 67, 86,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, 49, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 50,
 51, 80, 81, -1, -1, 52, -1, 53, -1, 54, -1, 55, -1, -1, 56, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 58, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 58, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 59, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 59, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 78, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 78, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 84, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 84, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, 59, 59, -1, 59, -1, 59, -1, 59, -1, 59, -1, -1, -1, 59, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, 59, -1, 59, -1, 59, -1, 59, -1, -1, -1, -1, -1, 59,
),
array(
 -1, -1, -1, -1, -1, 60, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 60, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 66, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 66, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 83, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 74, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 74, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
);

	public function /*Yytoken*/ nextToken ()
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
							{
    $res = form_res(preg_parser_yyParser::QUEST, 0);
    return $res;
}
						case -3:
							break;
						case 3:
							{
    $res = form_res(preg_parser_yyParser::ITER, 0);
    return $res;
}
						case -4:
							break;
						case 4:
							{
    $res = form_res(preg_parser_yyParser::PLUS, 0);
    return $res;
}
						case -5:
							break;
						case 5:
							{
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, $this->yytext()));
    return $res;
}
						case -6:
							break;
						case 6:
							{
    $this->cc = new node;
    $this->cc->direction = true;
    $this->cc->type = LEAF;
    $this->cc->subtype = LEAF_CHARCLASS;
    $this->cccharnumber = 0;
    $this->yybegin(self::CHARCLASS);
}
						case -7:
							break;
						case 7:
							{
    $res = form_res(preg_parser_yyParser::OPENBRACK, 0);
    return $res;
}
						case -8:
							break;
						case 8:
							{
    $res = form_res(preg_parser_yyParser::CLOSEBRACK, 0);
    return $res;
}
						case -9:
							break;
						case 9:
							{
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_METASYMBOLDOT));
    return $res;
}
						case -10:
							break;
						case 10:
							{
    $res = form_res(preg_parser_yyParser::ALT, 0);
    return $res;
}
						case -11:
							break;
						case 11:
							{
    $res = form_res(preg_parser_yyParser::STARTANCHOR, 0);
    return $res;
}
						case -12:
							break;
						case 12:
							{
    $res = form_res(preg_parser_yyPARSER::ENDANCHOR, 0);
    return $res;
}
						case -13:
							break;
						case 13:
							{
    $res = form_res(preg_parser_yyParser::LAZY_QUEST, 0);
    return $res;
}
						case -14:
							break;
						case 14:
							{
    $res = form_res(preg_parser_yyParser::LAZY_ITER, 0);
    return $res;
}
						case -15:
							break;
						case 15:
							{
    $res = form_res(preg_parser_yyParser::LAZY_PLUS, 0);
    return $res;
}
						case -16:
							break;
						case 16:
							{
    $res = form_res(preg_parser_yyParser::CONDSUBPATT, 0);
    return $res;
}
						case -17:
							break;
						case 17:
							{
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, $text[1]));
    return $res;
}
						case -18:
							break;
						case 18:
							{
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_LINK, substr($this->yytext(), 1)));
    return $res;
}
						case -19:
							break;
						case 19:
							{
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, '\\'));
    return $res;
}
						case -20:
							break;
						case 20:
							{
    $res = form_res(preg_parser_yyParser::WORDBREAK, 0);
    return $res;
}
						case -21:
							break;
						case 21:
							{
    $res = form_res(preg_parser_yyParser::WORDNOTBREAK, 0);
    return $res;
}
						case -22:
							break;
						case 22:
							{
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, '0123456789'));
    return $res;
}
						case -23:
							break;
						case 23:
							{
    $PARSLEAF = form_node(LEAF, LEAF_CHARCLASS, '0123456789');
    $PARSLEAF->direction = false;
    $res = form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
						case -24:
							break;
						case 24:
							{
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, 'qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPASDFGHJKLMNBVCXZ_0123456789'));
    return $res;
}
						case -25:
							break;
						case 25:
							{
    $PARSLEAF = form_node(LEAF, LEAF_CHARCLASS, 'qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPASDFGHJKLMNBVCXZ_0123456789');
    $PARSLEAF->direction = false;
    $res = form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
						case -26:
							break;
						case 26:
							{
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, ' '));
    return $res;
}
						case -27:
							break;
						case 27:
							{
    $PARSLEAF = form_node(LEAF, LEAF_CHARCLASS, ' ');
    $PARSLEAF->direction = false;
    $res = form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
						case -28:
							break;
						case 28:
							{
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, chr(9)));
    return $res;
}
						case -29:
							break;
						case 29:
							{
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::QUANT, form_node(NODE, NODE_QUANT, null, substr($text, 1, strpos($text, ',') -1), substr($text, 1, strpos($text, ',') -1)));
    return $res;
}
						case -30:
							break;
						case 30:
							{
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, chr(octdec(substr($this->yytext(), 1)))));
    return $res;
}
						case -31:
							break;
						case 31:
							{
    $res = form_res(preg_parser_yyParser::ONETIMESUBPATT, 0);
    return $res;
}
						case -32:
							break;
						case 32:
							{
    $res = form_res(preg_parser_yyParser::GROUPING, 0);
    return $res;
}
						case -33:
							break;
						case 33:
							{
    $res = form_res(preg_parser_yyParser::ASSERT_TF, 0);
    return $res;
}
						case -34:
							break;
						case 34:
							{
    $res = form_res(preg_parser_yyParser::ASSERT_FF, 0);
    return $res;
}
						case -35:
							break;
						case 35:
							{
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::QUANT, form_node(NODE, NODE_QUANT, null, substr($text, 1, strpos($text, ',') -1), -1));
    return $res;
}
						case -36:
							break;
						case 36:
							{
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::LAZY_QUANT, form_node(NODE, NODE_QUANT, null, substr($text, 1, strpos($text, ',') -1), substr($text, 1, strpos($text, ',') -1), false));
    return $res;
}
						case -37:
							break;
						case 37:
							{
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::QUANT, form_node(NODE, NODE_QUANT, null, 0, substr($text, 2, strlen($text) - 3)));
    return $res;
}
						case -38:
							break;
						case 38:
							{
    $res = form_res(preg_parser_yyParser::ASSERT_TB, 0);
    return $res;
}
						case -39:
							break;
						case 39:
							{
    $res = form_res(preg_parser_yyParser::ASSERT_FB, 0);
    return $res;
}
						case -40:
							break;
						case 40:
							{
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, chr(hexdec(substr($this->yytext(), 1)))));
    return $res;
}
						case -41:
							break;
						case 41:
							{
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::QUANT, form_node(NODE, NODE_QUANT, null, substr($text, 1, strpos($text, ',') -1), substr($text, strpos($text, ',')+1, strlen($text)-2-strpos($text, ','))));
    return $res;
}
						case -42:
							break;
						case 42:
							{
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::LAZY_QUANT, form_node(NODE, NODE_QUANT, null, substr($text, 1, strpos($text, ',') -1), -1, false));
    return $res;
}
						case -43:
							break;
						case 43:
							{
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::LAZY_QUANT, form_node(NODE, NODE_QUANT, null, 0, substr($text, 2, strlen($text) - 3), false));
    return $res;
}
						case -44:
							break;
						case 44:
							{
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::LAZY_QUANT, form_node(NODE, NODE_QUANT, null, substr($text, 1, strpos($text, ',') -1), substr($text, strpos($text, ',')+1, strlen($text)-2-strpos($text, ',')), false));
    return $res;
}
						case -45:
							break;
						case 45:
							{
    $this->cc->chars .= $this->yytext();
    $this->cccharnumber++;
}
						case -46:
							break;
						case 46:
							{
    $res= form_res(preg_parser_yyParser::PARSLEAF, $this->cc);
    $this->yybegin(self::YYINITIAL);
    return $res;
}
						case -47:
							break;
						case 47:
							{
    if ($this->cccharnumber) {
        $this->cc .= '^';
    } else {
        $this->cc->direction = false;
    }
    $this->cccharnumber++;
}
						case -48:
							break;
						case 48:
							{
    if (!$this->cccharnumber) {
        $this->cc->chars .= '-';
    }
    $this->cccharnumber++;
}
						case -49:
							break;
						case 49:
							{
    $this->cc->chars .= '[';
    $this->cccharnumber++;
}
						case -50:
							break;
						case 50:
							{
    $this->cc->chars .= '\\';
    $this->cccharnumber++;
}
						case -51:
							break;
						case 51:
							{
    $this->cc->chars .= ']';
    $this->cccharnumber++;
}
						case -52:
							break;
						case 52:
							{
    $this->cccharnumber++;
    $this->cc->chars .= '0123456789';
}
						case -53:
							break;
						case 53:
							{
    $this->cccharnumber++;
    $this->cc->chars .= 'qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPASDFGHJKLMNBVCXZ_0123456789';
}
						case -54:
							break;
						case 54:
							{
    $this->cccharnumber++;
    $this->cc->chars .= ' ';
}
						case -55:
							break;
						case 55:
							{
    $this->cccharnumber++;
    $this->cc->chars .= chr(9);
}
						case -56:
							break;
						case 56:
							{
    $this->cc->chars .= '-';
    $this->cccharnumber++;
}
						case -57:
							break;
						case 57:
							{
    if (!$this->cccharnumber) {
        $this->cc->chars .= '-';
        $this->cc->direction;
        $this->cccharnumber++;
    }
}
						case -58:
							break;
						case 58:
							{
    $this->cc->chars .= chr(octdec(substr($this->yytext(), 1)));
    $this->cccharnumber++;
}
						case -59:
							break;
						case 59:
							{
    $text = $this->yytext();
    form_num_interval($this->cc, $text[0], $text[2]);
}
						case -60:
							break;
						case 60:
							{
    $this->cccharnumber++;
    $this->cc->chars .= chr(hexdec(substr($this->yytext(), 1)));
}
						case -61:
							break;
						case 62:
							{
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, $this->yytext()));
    return $res;
}
						case -62:
							break;
						case 63:
							{
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_LINK, substr($this->yytext(), 1)));
    return $res;
}
						case -63:
							break;
						case 64:
							{
    $this->cc->chars .= $this->yytext();
    $this->cccharnumber++;
}
						case -64:
							break;
						case 66:
							{
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_LINK, substr($this->yytext(), 1)));
    return $res;
}
						case -65:
							break;
						case 67:
							{
    $this->cc->chars .= $this->yytext();
    $this->cccharnumber++;
}
						case -66:
							break;
						case 85:
							{
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_LINK, substr($this->yytext(), 1)));
    return $res;
}
						case -67:
							break;
						case 86:
							{
    $this->cc->chars .= $this->yytext();
    $this->cccharnumber++;
}
						case -68:
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
