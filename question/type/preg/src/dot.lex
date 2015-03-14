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
%}
%%
<YYINITIAL> "digraph" {return $this->createToken(qtype_preg_dot_parser::DIGRAPH);}
<YYINITIAL> {IDENT} {return $this->createToken(qtype_preg_dot_parser::NAME);}
<YYINITIAL> "{" {
					$this->startstates = array();
					$this->yybegin(self::STARTSTATES);
				}

<YYINITIAL> "}" {return $this->createToken(qtype_preg_dot_parser::CLOSEBODY);}
<YYINITIAL> "color" {return $this->createToken(qtype_preg_dot_parser::COLOR);}
<YYINITIAL> "violet" {return $this->createToken(qtype_preg_dot_parser::VIOLET);}
<YYINITIAL> "red" {return $this->createToken(qtype_preg_dot_parser::RED);}
<YYINITIAL> "blue" {return $this->createToken(qtype_preg_dot_parser::BLUE);}
<YYINITIAL> "style" {return $this->createToken(qtype_preg_dot_parser::STYLE);}
<YYINITIAL> "dotted" {return $this->createToken(qtype_preg_dot_parser::DOTTED);}
<YYINITIAL> "," {return $this->createToken(qtype_preg_dot_parser::COMMA);}
<YYINITIAL> "=" {return $this->createToken(qtype_preg_dot_parser::EQUALS);}

<YYINITIAL> "["({WHITESPACE})*"label"({WHITESPACE})*"="({WHITESPACE})*"<"{$this->yybegin(self::TRANSITION);}
<TRANSITION> "<B>" {return $this->createToken(qtype_preg_dot_parser::MAINSTART);}
<TRANSITION> "</B>" {return $this->createToken(qtype_preg_dot_parser::MAINEND);}
<TRANSITION> "o:" {
                        $this->opentags = array();
                        $this->yybegin(self::OPENTAGS);
                    }

<OPENTAGS> {NUMBER}"," {
                            $str = $this->yytext();
                            $this->opentags[] = substr($str, 0, -1);
                        }
<OPENTAGS> " " {
                    $this->yybegin(self::PREGLEAF);
                    return $this->createToken(qtype_preg_dot_parser::OPEN, $this->opentags);
                }

<PREGLEAF> " c:" {
                    $this->yybegin(self::CLOSETAGS);
                    return $this->createToken(qtype_preg_dot_parser::LEAF, $this->pregleaf);
                }

<PREGLEAF> (.)+ {
                    $chars = $this->yytext();
                    StringStreamController::createRef('regex', $chars);
                    $pseudofile = fopen('string://regex', 'r');
                    $lexer = new qtype_preg_lexer($pseudofile);
                    $this->pregleaf = $lexer->nextToken()->value;
                }

<CLOSETAGS> {NUMBER}"," {
                            $str = $this->yytext();
                            $this->closetags[] = substr($str, 0, -1);
                        }
<CLOSETAGS> "<BR/>" {
                    $this->yybegin(self::TRANSITION);
                    return $this->createToken(qtype_preg_dot_parser::CLOSE, $this->closetags);
                }

<CLOSETAGS> ">" {
                    $this->yybegin(self::YYINITIAL);
                    return $this->createToken(qtype_preg_dot_parser::CLOSE, $this->closetags);
                }

<YYINITIAL> {NUMBER} {
      return $this->createToken();
}

<STARTSTATES> {FASTATE}";" {
                                $str = $this->yytext();
                                $this->startstates[] = substr($str, 0, -1);
                            }
<STARTSTATES> \n {
                    $this->endstates = array();
                    $this->yybegin(self::ENDSTATES);
                    return $this->createToken(qtype_preg_dot_parser::START, $this->startstates);
                }

<ENDSTATES> {FASTATE}";" {
                            $str = $this->yytext();
                            $this->endstates[] = substr($str, 0, -1);
                        }
<ENDSTATES> \n {
                    $this->yybegin(self::YYINITIAL);
                    return $this->createToken(qtype_preg_dot_parser::END, $this->endstates);
                }

<YYINITIAL> {FASTATE}"->"{FASTATE} {
                                        $str = $this->yytext();
                                        return $this->createToken(qtype_preg_dot_parser::TRANSITIONSTATES, explode("->", $str));
                                    }
<YYINITIAL> .           { /* ignore bad characters */ }