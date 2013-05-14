%name File_ChessPGN_
%include {
// hi!
}
%include_class {
/* ?><?php */

    function __construct($lexer)
    {
        $this->_lexer = $lexer;
    }

    private $_lexer;

    function _validateFullmove($whitemove, $blacknumber, $blackmove)
    {
        if (!$blacknumber) {
            return; // always fine
        }
        if ($blacknumber != $whitemove['number']) {
            throw new Exception('white move number ' .
                $whitemove['number'] . ' is not the same as black\'s' .
                ' move number ' . $blacknumber . ' in move: ' .
                $whitemove['number'] . '. ' . $whitemove['move'] . ' ' .
                $blacknumber . '... ' . $blackmove);
        }
    }

    function _validateRav($number, $rav)
    {
        if (isset($rav['multi-rav'])) {
            $num = $rav['multi-rav'][0][0]['number'];
        } else {
            $num = $rav[0]['number'];
        }
        if ($number != $num) {
            throw new Exception('Recursive annotation variation at ' .
                'move number ' . $number . ' starts with move number ' .
                $num . ' and should start with ' . $number);
        }
    }

    function _validateMoves($moves, $move)
    {
        $lastmove = $moves[count($moves) - 1];
        $num = $lastmove['number'];
        if ($move['number'] != $num + 1) {
            throw new Exception('move number ' . $move['number'] .
                ' cannot follow move number ' . $num . ', it should ' .
                'be move number ' . ($num + 1));
        }
        if (!$lastmove['black']) {
            throw new Exception('move number ' . $num . ' does not' .
                ' have a black move, and must be the final move in' .
                ' the game');
        }
        if (!$move['white']) {
            throw new Exception('move number ' . $move['number'] .
                ' must have a white move');
        }
    }

    public $transTable =
        array(
            1 => self::TAGOPEN,
            2 => self::TAGNAME,
            3 => self::TAGCLOSE,
            4 => self::STRING,
            5 => self::NAG,
            6 => self::GAMEEND,
            7 => self::PAWNMOVE,
            8 => self::PIECEMOVE,
            9 => self::PLACEMENTMOVE,
            10 => self::CHECK,
            11 => self::MATE,
            12 => self::DIGIT,
            13 => self::MOVEANNOT,
            14 => self::RAVOPEN,
            15 => self::RAVCLOSE,
            16 => self::PERIOD,
            17 => self::COMMENTOPEN,
            18 => self::COMMENTCLOSE,
            19 => self::COMMENT,
            20 => self::CASTLE,
        );
}

%parse_accept {
    var_dump($this->games);
}

%syntax_error {
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = self::$yyTokenName[$token];
    }
    throw new Exception('Unexpected ' . $this->tokenName($yymajor) . '(' . $TOKEN
        . '), expected one of: ' . implode(',', $expect));
}
%right TAGOPEN.

start ::= games(B). {$this->games = B;}

games(A) ::= games_with_end(B). {A = B;}
games(A) ::= game(B). {A = array(B);}

games_with_end(A) ::= game_with_end(B). {A = array(B);}
games_with_end(A) ::= games_with_end(B) game_with_end(C). {A = B; A[] = C;}

game_with_end(A) ::= tags(B) moves(C) GAMEEND(D). {
    A = array('tags' => B, 'moves' => C, 'result' => D);
}
game(A) ::= tags(B) moves(C). {A = array('tags' => B, 'moves' => C);}

tags(A) ::= TAGOPEN TAGNAME(B) STRING(C) TAGCLOSE. {
    A = array();
    A[B][] = C;
}
tags(A) ::= tags(B) TAGOPEN TAGNAME(C) STRING(D) TAGCLOSE. {
    A = B;
    A[C][] = D;
}

moves(A) ::= fullmove(B). {
    A = array(B);
}
moves(A) ::= moves(B) fullmove(C). {
    $this->_validateMoves(B, C);
    A = B;
    A[] = C;
}

fullmove(A) ::= whitemove(B) rav(C) blackmovenumber(D) basicmove(E) blackrav(F). {
    $this->_validateFullmove(B, D, E);
    $this->_validateRav(B['number'], C);
    $this->_validateRav(B['number'], F);
    A = array(
        'number' => B['number'],
        'white' => B,
        'black' => E,
        'white_rav' => C,
        'black_rav' => F,
    );
}

fullmove(A) ::= whitemove(B) blackmovenumber_or_not(D) basicmove(E) blackrav(F). {
    $this->_validateFullmove(B, D, E);
    $this->_validateRav(B['number'], F);
    A = array(
        'number' => B['number'],
        'white' => B,
        'black' => E,
        'white_rav' => false,
        'black_rav' => F,
    );
}
fullmove(A) ::= whitemove(B) rav(C) blackmovenumber(D) basicmove(E). {
    $this->_validateFullmove(B, D, E);
    $this->_validateRav(B['number'], C);
    A = array(
        'number' => B['number'],
        'white' => B,
        'black' => E,
        'white_rav' => C,
        'black_rav' => false,
    );
}
fullmove(A) ::= whitemove(B) rav(C). {
    $this->_validateRav(B['number'], C);
    A = array(
        'number' => B['number'],
        'white' => B,
        'black' => false,
        'white_rav' => C,
        'black_rav' => false,
    );
}
fullmove(A) ::= whitemove(B) blackmovenumber_or_not(C) basicmove(D). {
    $this->_validateFullmove(B, C, D);
    A = array(
        'number' => B['number'],
        'white' => B,
        'black' => D,
        'white_rav' => false,
        'black_rav' => false,
    );
}
fullmove(A) ::= whitemove(B). {
    A = array(
        'number' => B['number'],
        'white' => B,
        'black' => false,
        'white_rav' => false,
        'black_rav' => false,
    );
}

whitemove(A) ::= whitemovenumber(B) basicmove(C). {
    A = C;
    A['number'] = B;
}

basicmove(A) ::= CASTLE|PAWNMOVE|PIECEMOVE|PLACEMENTMOVE(B) comment_or_not(C). {
    A = array(
        'move' => B,
        'annotation' => false,
        'mate' => false,
        'check' => false,
        'nags' => false,
        'comment' => C,
    );
}
basicmove(A) ::= CASTLE|PAWNMOVE|PIECEMOVE|PLACEMENTMOVE(B) nags(C) comment_or_not(D). {
    A = array(
        'move' => B,
        'annotation' => false,
        'mate' => false,
        'check' => false,
        'nags' => C,
        'comment' => D,
    );
}
basicmove(A) ::= CASTLE|PAWNMOVE|PIECEMOVE|PLACEMENTMOVE(B) CHECK comment_or_not(C). {
    A = array(
        'move' => B,
        'annotation' => false,
        'mate' => false,
        'check' => true,
        'nags' => false,
        'comment' => C,
    );
}
basicmove(A) ::= CASTLE|PAWNMOVE|PIECEMOVE|PLACEMENTMOVE(B) MATE comment_or_not(C). {
    A = array(
        'move' => B,
        'annotation' => false,
        'mate' => true,
        'check' => false,
        'nags' => false,
        'comment' => C,
    );
}
basicmove(A) ::= CASTLE|PAWNMOVE|PIECEMOVE|PLACEMENTMOVE(B) CHECK MOVEANNOT(C) comment_or_not(D). {
    A = array(
        'move' => B,
        'annotation' => C,
        'mate' => false,
        'check' => true,
        'nags' => false,
        'comment' => D,
    );
}
basicmove(A) ::= CASTLE|PAWNMOVE|PIECEMOVE|PLACEMENTMOVE(B) MATE MOVEANNOT(C) comment_or_not(D). {
    A = array(
        'move' => B,
        'annotation' => C,
        'mate' => true,
        'check' => false,
        'nags' => false,
        'comment' => D,
    );
}
basicmove(A) ::= CASTLE|PAWNMOVE|PIECEMOVE|PLACEMENTMOVE(B) CHECK nags(C) comment_or_not(D). {
    A = array(
        'move' => B,
        'annotation' => false,
        'mate' => false,
        'check' => true,
        'nags' => C,
        'comment' => D,
    );
}
basicmove(A) ::= CASTLE|PAWNMOVE|PIECEMOVE|PLACEMENTMOVE(B) MATE nags(C) comment_or_not(D). {
    A = array(
        'move' => B,
        'annotation' => false,
        'mate' => true,
        'check' => false,
        'nags' => C,
        'comment' => D,
    );
}

comment_or_not(A) ::= comment(B). {A = B;}
comment_or_not(A) ::= . {A = false;}

nags(A) ::= NAG(B). {A = array(B);}
nags(A) ::= nags(B) NAG(C). {A = B;A[] = C;}

whitemovenumber(A) ::= number(B) PERIOD. {A = B;}

blackmovenumber_or_not(A) ::= blackmovenumber(B). {A = B;}
blackmovenumber_or_not(A) ::= . {A = false;}

blackmovenumber(A) ::= number(B) PERIOD PERIOD PERIOD. {A = B;}

number(A) ::= DIGIT(B). {A = B;}
number(A) ::= DIGIT(B) DIGIT(C). {A = B . C;}
number(A) ::= DIGIT(B) DIGIT(C) DIGIT(D). {A = B . C . D;}

blackrav(A) ::= RAVOPEN blackmovenumber(B) basicmove(C) moves(D) RAVCLOSE. {
    A = D;
    array_unshift(A, array(
        'number' => B,
        'white' => false,
        'black' => C,
        'white_rav' => false,
        'black_rav' => false,
    ));
}
blackrav(A) ::= RAVOPEN blackmovenumber(B) basicmove(C) rav(RAV) moves(D) RAVCLOSE. {
    A = D;
    array_unshift(A, array(
        'number' => B,
        'white' => false,
        'black' => C,
        'white_rav' => RAV,
        'black_rav' => false,
    ));
}
blackrav(A) ::= RAVOPEN blackmovenumber(B) basicmove(C) RAVCLOSE. {
    A = array(array(
        'number' => B,
        'white' => false,
        'black' => C,
        'white_rav' => false,
        'black_rav' => false,
    ));
}
blackrav(A) ::= RAVOPEN blackmovenumber(B) basicmove(C) rav(RAV) RAVCLOSE. {
    A = array(array(
        'number' => B,
        'white' => false,
        'black' => C,
        'white_rav' => false,
        'black_rav' => RAV,
    ));
}
blackrav(A) ::= blackrav(PREV) RAVOPEN blackmovenumber(B) basicmove(C) moves(D) RAVCLOSE. {
    $c = D;
    array_unshift($c, array(
        'number' => B,
        'white' => false,
        'black' => C,
        'white_rav' => false,
        'black_rav' => false,
    ));
    if (isset(PREV['multi-rav'])) {
        $num = PREV['multi-rav'][0][0]['number'];
        A = PREV;
        A['multi-rav'][] = $c;
    } else {
        $num = B[0]['number'];
        A = array('multi-rav' => array(B, $c));
    }
    if (B != $num) {
        throw new Exception('Recursive Annotation Variation ' .
        'immediately following variation starting with move number ' .
        $num . ' is number ' . C[0]['number'] . ' and must be ' .
        $num);
    }
}
blackrav(A) ::= blackrav(PREV) RAVOPEN blackmovenumber(B) basicmove(C) rav(RAV) moves(D) RAVCLOSE. {
    $c = D;
    array_unshift($c, array(
        'number' => B,
        'white' => false,
        'black' => C,
        'white_rav' => RAV,
        'black_rav' => false,
    ));
    if (isset(PREV['multi-rav'])) {
        $num = PREV['multi-rav'][0][0]['number'];
        A = PREV;
        A['multi-rav'][] = $c;
    } else {
        $num = B[0]['number'];
        A = array('multi-rav' => array(B, $c));
    }
    if (B != $num) {
        throw new Exception('Recursive Annotation Variation ' .
        'immediately following variation starting with move number ' .
        $num . ' is number ' . C[0]['number'] . ' and must be ' .
        $num);
    }
}
blackrav(A) ::= blackrav(PREV) RAVOPEN blackmovenumber(B) basicmove(C) RAVCLOSE. {
    $c = array(array(
        'number' => B,
        'white' => false,
        'black' => C,
        'white_rav' => false,
        'black_rav' => false,
    ));
    if (isset(PREV['multi-rav'])) {
        $num = PREV['multi-rav'][0][0]['number'];
        A = PREV;
        A['multi-rav'][] = $c;
    } else {
        $num = B[0]['number'];
        A = array('multi-rav' => array(B, $c));
    }
    if (B != $num) {
        throw new Exception('Recursive Annotation Variation ' .
        'immediately following variation starting with move number ' .
        $num . ' is number ' . C[0]['number'] . ' and must be ' .
        $num);
    }
}
blackrav(A) ::= blackrav(PREV) RAVOPEN blackmovenumber(B) basicmove(C) rav(RAV) RAVCLOSE. {
    $c = array(array(
        'number' => B,
        'white' => false,
        'black' => C,
        'white_rav' => false,
        'black_rav' => RAV,
    ));
    if (isset(PREV['multi-rav'])) {
        $num = PREV['multi-rav'][0][0]['number'];
        A = PREV;
        A['multi-rav'][] = $c;
    } else {
        $num = B[0]['number'];
        A = array('multi-rav' => array(B, $c));
    }
    if (B != $num) {
        throw new Exception('Recursive Annotation Variation ' .
        'immediately following variation starting with move number ' .
        $num . ' is number ' . C[0]['number'] . ' and must be ' .
        $num);
    }
}

rav(A) ::= RAVOPEN moves(B) RAVCLOSE. {A = B;}
rav(A) ::= rav(B) RAVOPEN moves(C) RAVCLOSE. {
    if (isset(B['multi-rav'])) {
        $num = B['multi-rav'][0][0]['number'];
        A = B;
        A['multi-rav'][] = C;
    } else {
        $num = B[0]['number'];
        A = array('multi-rav' => array(B, C));
    }
    if (C[0]['number'] != $num) {
        throw new Exception('Recursive Annotation Variation ' .
        'immediately following variation starting with move number ' .
        $num . ' is number ' . C[0]['number'] . ' and must be ' .
        $num);
    }
}

comment(A) ::= COMMENTOPEN COMMENT(B) COMMENTCLOSE. {A = B;}