<?php
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/jlex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');


class qtype_preg_dot_lexer extends JLexBase  {
	const YY_BUFFER_SIZE = 512;
	const YY_F = -1;
	const YY_NO_STATE = -1;
	const YY_NOT_ACCEPT = 0;
	const YY_START = 1;
	const YY_END = 2;
	const YY_NO_ANCHOR = 4;
	const YY_BOL = 128;
	var $YY_EOF = 129;

    protected $startstates;
    protected $endstates;
    protected $opentags;
    protected $closetags;
    protected $pregleaf;
    protected $fromstate;
    protected $chars;
    private function createToken($type, $value = null) {
        return new JLexToken($type, $value);
    }
	protected $yy_count_chars = true;
	protected $yy_count_lines = true;

	function __construct($stream) {
		parent::__construct($stream);
		$this->yy_lexical_state = self::YYINITIAL;
	}

	const OPENTAGS = 4;
	const TRANSITION = 3;
	const STARTSTATES = 1;
	const PREGLEAF = 6;
	const YYINITIAL = 0;
	const CLOSETAGS = 5;
	const ENDSTATES = 2;
	static $yy_state_dtrans = array(
		0,
		66,
		73,
		80,
		110,
		112,
		117
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
		/* 39 */ self::YY_NOT_ACCEPT,
		/* 40 */ self::YY_NO_ANCHOR,
		/* 41 */ self::YY_NO_ANCHOR,
		/* 42 */ self::YY_NO_ANCHOR,
		/* 43 */ self::YY_NO_ANCHOR,
		/* 44 */ self::YY_NOT_ACCEPT,
		/* 45 */ self::YY_NO_ANCHOR,
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
		/* 85 */ self::YY_NOT_ACCEPT,
		/* 86 */ self::YY_NOT_ACCEPT,
		/* 87 */ self::YY_NOT_ACCEPT,
		/* 88 */ self::YY_NOT_ACCEPT,
		/* 89 */ self::YY_NOT_ACCEPT,
		/* 90 */ self::YY_NOT_ACCEPT,
		/* 91 */ self::YY_NOT_ACCEPT,
		/* 92 */ self::YY_NOT_ACCEPT,
		/* 93 */ self::YY_NOT_ACCEPT,
		/* 94 */ self::YY_NOT_ACCEPT,
		/* 95 */ self::YY_NOT_ACCEPT,
		/* 96 */ self::YY_NOT_ACCEPT,
		/* 97 */ self::YY_NOT_ACCEPT,
		/* 98 */ self::YY_NOT_ACCEPT,
		/* 99 */ self::YY_NOT_ACCEPT,
		/* 100 */ self::YY_NOT_ACCEPT,
		/* 101 */ self::YY_NOT_ACCEPT,
		/* 102 */ self::YY_NOT_ACCEPT,
		/* 103 */ self::YY_NOT_ACCEPT,
		/* 104 */ self::YY_NOT_ACCEPT,
		/* 105 */ self::YY_NOT_ACCEPT,
		/* 106 */ self::YY_NOT_ACCEPT,
		/* 107 */ self::YY_NOT_ACCEPT,
		/* 108 */ self::YY_NOT_ACCEPT,
		/* 109 */ self::YY_NOT_ACCEPT,
		/* 110 */ self::YY_NOT_ACCEPT,
		/* 111 */ self::YY_NOT_ACCEPT,
		/* 112 */ self::YY_NOT_ACCEPT,
		/* 113 */ self::YY_NOT_ACCEPT,
		/* 114 */ self::YY_NOT_ACCEPT,
		/* 115 */ self::YY_NOT_ACCEPT,
		/* 116 */ self::YY_NOT_ACCEPT,
		/* 117 */ self::YY_NOT_ACCEPT,
		/* 118 */ self::YY_NOT_ACCEPT,
		/* 119 */ self::YY_NOT_ACCEPT,
		/* 120 */ self::YY_NOT_ACCEPT,
		/* 121 */ self::YY_NOT_ACCEPT,
		/* 122 */ self::YY_NOT_ACCEPT,
		/* 123 */ self::YY_NOT_ACCEPT,
		/* 124 */ self::YY_NOT_ACCEPT,
		/* 125 */ self::YY_NOT_ACCEPT,
		/* 126 */ self::YY_NOT_ACCEPT,
		/* 127 */ self::YY_NOT_ACCEPT
	);
		static $yy_cmap = array(
 35, 35, 35, 35, 35, 35, 35, 35, 35, 36, 9, 36, 36, 12, 35, 35, 35, 35, 35, 35,
 35, 35, 35, 35, 35, 35, 35, 35, 35, 35, 35, 35, 34, 35, 37, 35, 35, 35, 35, 35,
 35, 35, 35, 35, 19, 38, 35, 28, 33, 33, 33, 33, 33, 33, 33, 33, 33, 33, 32, 31,
 17, 16, 18, 35, 35, 35, 27, 35, 35, 35, 35, 35, 35, 35, 35, 35, 35, 35, 35, 35,
 35, 35, 29, 35, 35, 35, 35, 35, 35, 35, 35, 11, 35, 30, 35, 35, 35, 5, 14, 20,
 1, 15, 35, 3, 7, 2, 35, 35, 13, 35, 35, 21, 6, 35, 4, 25, 23, 24, 22, 35,
 35, 26, 35, 8, 35, 10, 35, 35, 0, 0,);

		static $yy_rmap = array(
 0, 1, 1, 1, 2, 1, 1, 1, 1, 1, 3, 1, 1, 4, 5, 1, 6, 1, 1, 1,
 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 7, 1, 1, 1, 1, 1, 1, 8,
 1, 1, 1, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25,
 26, 27, 28, 29, 30, 2, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44,
 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 5, 6, 56, 57, 58, 59, 60, 61, 62,
 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82,
 83, 84, 85, 86, 87, 88, 89, 90,);

		static $yy_nxt = array(
array(
 1, 39, -1, -1, -1, -1, -1, -1, 44, 2, 3, 46, 2, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 4, 2, -1, 2, 47, 48,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 4, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 10, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 13, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 91, -1, -1, 91, -1, -1, -1, -1, -1, -1, 92,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 91, -1, 91, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 92, -1, -1, 92, -1, -1, -1, -1, -1, -1, -1,
 96, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 92, -1, 92, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, 114, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, 49, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 118, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 5, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 63,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 46, -1, -1, 46, 50, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 46, -1, 46, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 51,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 52, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 2, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, 53, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 54, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 55, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 119,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 52, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, 56, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 57, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 55, -1, -1, -1, 40, -1,
),
array(
 -1, -1, -1, -1, -1, 59, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 60, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 58, -1, -1, -1, 45, -1,
),
array(
 -1, -1, -1, -1, -1, -1, 61, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, 6, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, 62, -1, -1, -1, 64, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, 62, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 65, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 64, -1, -1, 64, -1, -1, -1, -1, 7, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 64, -1, 64, -1, -1,
),
array(
 1, -1, -1, -1, -1, -1, -1, -1, -1, 8, -1, -1, 9, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 9, -1, 10, 9, -1, 9, 67, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 68,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 69, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 70, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 120,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 69, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 70, -1, -1, -1, 41, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 71, -1, -1, -1, 72, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 41, -1, -1, -1, -1, -1, -1, -1,
),
array(
 1, -1, -1, -1, -1, -1, -1, -1, -1, 11, -1, -1, 12, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 12, -1, 13, 12, -1, 12, 74, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 75,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 76, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 77, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 121,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 76, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 77, -1, -1, -1, 42, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 78, -1, -1, -1, 79, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 42, -1, -1, -1, -1, -1, -1, -1,
),
array(
 1, 81, -1, -1, 82, -1, -1, -1, -1, 14, -1, -1, 14, -1, 83, -1, 15, 84, 85, 16,
 -1, 86, 87, -1, -1, 88, -1, -1, 122, -1, 89, -1, -1, -1, 14, -1, 14, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 123, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 90, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 93, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, 94, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 91, -1, -1, 91, -1, -1, -1, -1, -1, -1, 92,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 95, -1, -1, -1, 91, -1, 91, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 17, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, 127, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, 97, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 18, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 19, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, 100, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 20, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 101, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 21, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 102, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, 126, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 22, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, 103, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 23, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, 104, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 105, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 107, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 24, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 108, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 25, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 26, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, 27, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, 28, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 29,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 111, 30, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 31,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 111, -1, -1, -1, -1, -1,
),
array(
 1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 32, 33, 34,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 113, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 35,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 113, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, 115, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, 116, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 36, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 1, 37, 37, 37, 37, 37, 37, 37, 37, -1, 37, 37, -1, 37, 37, 37, 37, 37, 37, 37,
 37, 37, 37, 37, 37, 37, 37, 37, 37, 37, 37, 37, 37, 37, 43, 37, 37, 37, 37,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 38, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 58, -1, -1, -1, 40, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 71, -1, -1, -1, 41, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 78, -1, -1, -1, 42, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, 98, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, 99, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 125, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 109, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 106, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 124, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
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
							{}
						case -3:
							break;
						case 3:
							{return $this->createToken(qtype_preg_dot_parser::CLOSEBODY);}
						case -4:
							break;
						case 4:
							{
                                        if ($this->fromstate === null) {
                                            $this->fromstate = trim($this->yytext(), '"');
                                        } else {
                                            $fromstate = $this->fromstate;
                                            $this->fromstate = null;
                                            return $this->createToken(qtype_preg_dot_parser::TRANSITIONSTATES, array($fromstate, trim($this->yytext(), '"')));
                                        }
                                    }
						case -5:
							break;
						case 5:
							{
					$this->startstates = array();
					$this->yybegin(self::STARTSTATES);
				}
						case -6:
							break;
						case 6:
							{return $this->createToken(qtype_preg_dot_parser::DIGRAPH);}
						case -7:
							break;
						case 7:
							{$this->yybegin(self::TRANSITION);}
						case -8:
							break;
						case 8:
							{
                    $this->endstates = array();
                    $this->yybegin(self::ENDSTATES);
                    return $this->createToken(qtype_preg_dot_parser::START, $this->startstates);
                }
						case -9:
							break;
						case 9:
							{}
						case -10:
							break;
						case 10:
							{
                                $state = trim($this->yytext(), ';');
                                $this->startstates[] = trim($state, '"');
                            }
						case -11:
							break;
						case 11:
							{
                    $this->fromstate = null;
                    $this->yybegin(self::YYINITIAL);
                    return $this->createToken(qtype_preg_dot_parser::END, $this->endstates);
                }
						case -12:
							break;
						case 12:
							{}
						case -13:
							break;
						case 13:
							{
                            $state = trim($this->yytext(), ';');
                            $this->endstates[] = trim($state, '"');
                        }
						case -14:
							break;
						case 14:
							{}
						case -15:
							break;
						case 15:
							{return $this->createToken(qtype_preg_dot_parser::EQUALS);}
						case -16:
							break;
						case 16:
							{return $this->createToken(qtype_preg_dot_parser::COMMA);}
						case -17:
							break;
						case 17:
							{
                        $this->opentags = array();
                        $this->yybegin(self::OPENTAGS);
                    }
						case -18:
							break;
						case 18:
							{$this->yybegin(self::YYINITIAL);}
						case -19:
							break;
						case 19:
							{return $this->createToken(qtype_preg_dot_parser::RED);}
						case -20:
							break;
						case 20:
							{return $this->createToken(qtype_preg_dot_parser::MAINSTART);}
						case -21:
							break;
						case 21:
							{$this->yybegin(self::YYINITIAL);}
						case -22:
							break;
						case 22:
							{return $this->createToken(qtype_preg_dot_parser::MAINEND);}
						case -23:
							break;
						case 23:
							{return $this->createToken(qtype_preg_dot_parser::BLUE);}
						case -24:
							break;
						case 24:
							{}
						case -25:
							break;
						case 25:
							{return $this->createToken(qtype_preg_dot_parser::STYLE);}
						case -26:
							break;
						case 26:
							{return $this->createToken(qtype_preg_dot_parser::DOTTED);}
						case -27:
							break;
						case 27:
							{return $this->createToken(qtype_preg_dot_parser::COLOR);}
						case -28:
							break;
						case 28:
							{return $this->createToken(qtype_preg_dot_parser::VIOLET);}
						case -29:
							break;
						case 29:
							{}
						case -30:
							break;
						case 30:
							{
                    $this->yybegin(self::PREGLEAF);
                    $this->chars = "";
                    return $this->createToken(qtype_preg_dot_parser::OPEN, $this->opentags);
                }
						case -31:
							break;
						case 31:
							{
                            $this->opentags[] = (int)$this->yytext();
                        }
						case -32:
							break;
						case 32:
							{
                    $this->yybegin(self::TRANSITION);
                    return $this->createToken(qtype_preg_dot_parser::CLOSE, $this->closetags);
                }
						case -33:
							break;
						case 33:
							{
                    $this->yybegin(self::TRANSITION);
                    return $this->createToken(qtype_preg_dot_parser::CLOSE, $this->closetags);
                }
						case -34:
							break;
						case 34:
							{}
						case -35:
							break;
						case 35:
							{
                            $this->closetags[] = (int)$this->yytext();
                        }
						case -36:
							break;
						case 36:
							{
                    $this->yybegin(self::TRANSITION);
                    return $this->createToken(qtype_preg_dot_parser::CLOSE, $this->closetags);
                }
						case -37:
							break;
						case 37:
							{
                    $this->chars .= $this->yytext();
                }
						case -38:
							break;
						case 38:
							{
                    StringStreamController::createRef('regex', $this->chars);
                    $pseudofile = fopen('string://regex', 'r');
                    $lexer = new qtype_preg_lexer($pseudofile);
                    $this->pregleaf = $lexer->nextToken()->value;
                    if ($this->chars == "$") {
                        $this->pregleaf = new qtype_preg_leaf_assert_dollar();
                    }
                    if ($this->chars == "^") {
                        $this->pregleaf = new qtype_preg_leaf_assert_circumflex();
                    }
                    if ($this->chars == "ε") {
                        $this->pregleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
                    }
                    $this->closetags = array();
                    $this->yybegin(self::CLOSETAGS);
                    return $this->createToken(qtype_preg_dot_parser::LEAF, $this->pregleaf);
}
						case -39:
							break;
						case 40:
							{
                                        if ($this->fromstate === null) {
                                            $this->fromstate = trim($this->yytext(), '"');
                                        } else {
                                            $fromstate = $this->fromstate;
                                            $this->fromstate = null;
                                            return $this->createToken(qtype_preg_dot_parser::TRANSITIONSTATES, array($fromstate, trim($this->yytext(), '"')));
                                        }
                                    }
						case -40:
							break;
						case 41:
							{
                                $state = trim($this->yytext(), ';');
                                $this->startstates[] = trim($state, '"');
                            }
						case -41:
							break;
						case 42:
							{
                            $state = trim($this->yytext(), ';');
                            $this->endstates[] = trim($state, '"');
                        }
						case -42:
							break;
						case 43:
							{
                    $this->chars .= $this->yytext();
                }
						case -43:
							break;
						case 45:
							{
                                        if ($this->fromstate === null) {
                                            $this->fromstate = trim($this->yytext(), '"');
                                        } else {
                                            $fromstate = $this->fromstate;
                                            $this->fromstate = null;
                                            return $this->createToken(qtype_preg_dot_parser::TRANSITIONSTATES, array($fromstate, trim($this->yytext(), '"')));
                                        }
                                    }
						case -44:
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
