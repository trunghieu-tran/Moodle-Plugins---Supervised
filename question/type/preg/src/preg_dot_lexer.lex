<?php
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/jlex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');

%%
%function nextToken
%line
%char
%state STARTSTATES
%state ENDSTATES
%state TRANSITION
%state OPENTAGS
%state CLOSETAGS
%state PREGLEAF
%class qtype_preg_dot_lexer

ALPHA=[A-Za-z_]
DIGIT=[0-9]
ALPHA_NUMERIC={ALPHA}|{DIGIT}
IDENT={ALPHA}({ALPHA_NUMERIC})*
NUMBER=({DIGIT})+
STATEFIRST = "\""{NUMBER}",\""
STATESECOND = "\","{NUMBER}"\""
STATESTWO = "\""{NUMBER}","{NUMBER}"\""
FASTATE = {NUMBER}|{STATESECOND}|{STATEFIRST}|{STATESTWO}
WHITESPACE = [\x09\x0A\x0B\x0C\x0D\x20\x85\xA0]         // Whitespace character.

%{
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
%}
%%
<YYINITIAL> "digraph" {return $this->createToken(qtype_preg_dot_parser::DIGRAPH);}
<YYINITIAL> "{"[\n] {
					$this->startstates = array();
					$this->yybegin(self::STARTSTATES);
				}

<YYINITIAL> "}" {return $this->createToken(qtype_preg_dot_parser::CLOSEBODY);}


<YYINITIAL> "["({WHITESPACE})*"label"({WHITESPACE})*"="({WHITESPACE})*"<" {$this->yybegin(self::TRANSITION);}
<TRANSITION> (">")?({WHITESPACE})*","({WHITESPACE})*"color" {return $this->createToken(qtype_preg_dot_parser::COLOR);}
<TRANSITION> "violet" {return $this->createToken(qtype_preg_dot_parser::VIOLET);}
<TRANSITION> "red" {return $this->createToken(qtype_preg_dot_parser::RED);}
<TRANSITION> "blue" {return $this->createToken(qtype_preg_dot_parser::BLUE);}
<TRANSITION> "style" {return $this->createToken(qtype_preg_dot_parser::STYLE);}
<TRANSITION> "dotted" {return $this->createToken(qtype_preg_dot_parser::DOTTED);}
<TRANSITION> "," {return $this->createToken(qtype_preg_dot_parser::COMMA);}
<TRANSITION> "=" {return $this->createToken(qtype_preg_dot_parser::EQUALS);}
<TRANSITION> "<B>" {return $this->createToken(qtype_preg_dot_parser::MAINSTART);}
<TRANSITION> "/B>" {return $this->createToken(qtype_preg_dot_parser::MAINEND);}
<TRANSITION> "<BR/>" {}
<TRANSITION> ">];" {$this->yybegin(self::YYINITIAL);}
<TRANSITION> "];" {$this->yybegin(self::YYINITIAL);}
<TRANSITION> {WHITESPACE} {}
<TRANSITION> "o:" {
                        $this->opentags = array();
                        $this->yybegin(self::OPENTAGS);
                    }

<OPENTAGS> {NUMBER}"," {
                            $this->opentags[] = (int)$this->yytext();
                        }
<OPENTAGS> " " {
                    $this->yybegin(self::PREGLEAF);
                    $this->chars = "";
                    return $this->createToken(qtype_preg_dot_parser::OPEN, $this->opentags);
                }

<OPENTAGS> "," {}
<PREGLEAF> " c:" {
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
<PREGLEAF> . {
                    $this->chars .= $this->yytext();

                }

<CLOSETAGS> {NUMBER}"," {
                            $this->closetags[] = (int)$this->yytext();
                        }

<CLOSETAGS> "," {}

<CLOSETAGS> "<BR/>" {
                    $this->yybegin(self::TRANSITION);
                    return $this->createToken(qtype_preg_dot_parser::CLOSE, $this->closetags);
                }

<CLOSETAGS> ">" {
                    $this->yybegin(self::TRANSITION);
                    return $this->createToken(qtype_preg_dot_parser::CLOSE, $this->closetags);
                }

<CLOSETAGS> "<" {
                    $this->yybegin(self::TRANSITION);
                    return $this->createToken(qtype_preg_dot_parser::CLOSE, $this->closetags);
                }

<STARTSTATES> {FASTATE}";" {
                                $state = trim($this->yytext(), ';');
                                $this->startstates[] = trim($state, '"');
                            }
<STARTSTATES> [\n] {
                    $this->endstates = array();
                    $this->yybegin(self::ENDSTATES);
                    return $this->createToken(qtype_preg_dot_parser::START, $this->startstates);
                }

<STARTSTATES> {WHITESPACE}|";" {}

<ENDSTATES> {FASTATE}";" {
                            $state = trim($this->yytext(), ';');
                            $this->endstates[] = trim($state, '"');
                        }
<ENDSTATES> [\n] {
                    $this->fromstate = null;
                    $this->yybegin(self::YYINITIAL);
                    return $this->createToken(qtype_preg_dot_parser::END, $this->endstates);
                }

<ENDSTATES> {WHITESPACE}|";" {}

<YYINITIAL> ({FASTATE}"->"{FASTATE}) {
                                        if ($this->fromstate === null) {
                                            $this->fromstate = trim($this->yytext(), '"');
                                        } else {
                                            $fromstate = $this->fromstate;
                                            $this->fromstate = null;
                                            return $this->createToken(qtype_preg_dot_parser::TRANSITIONSTATES, array($fromstate, trim($this->yytext(), '"')));
                                        }

                                    }
<YYINITIAL> "->"|[\n]|{WHITESPACE} {}
