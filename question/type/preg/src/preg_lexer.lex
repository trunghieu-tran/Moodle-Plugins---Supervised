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
%%
%function nextToken
%line
%char
%state CHARCLASS
%{
    protected $errors = array();

    public function get_errors() {
        return $this->errors;
    }
%}
%eof{
        if (isset($this->cc) && is_object($this->cc)) {//End of expression inside character class
            $this->errors[] = 'unclosedsqbrackets';
            $this->cc = null;
        }
%eof}
%%

<YYINITIAL> \? {
    $res = form_res(preg_parser_yyParser::QUEST, 0);
    return $res;
}
<YYINITIAL> \* {
    $res = form_res(preg_parser_yyParser::ITER, 0);
    return $res;
}
<YYINITIAL> \+ {
    $res = form_res(preg_parser_yyParser::PLUS, 0);
    return $res;
}
<YYINITIAL> \?\? {
    $res = form_res(preg_parser_yyParser::LAZY_QUEST, 0);
    return $res;
}
<YYINITIAL> \*\? {
    $res = form_res(preg_parser_yyParser::LAZY_ITER, 0);
    return $res;
}
<YYINITIAL> \+\? {
    $res = form_res(preg_parser_yyParser::LAZY_PLUS, 0);
    return $res;
}
<YYINITIAL> \{[0-9]+,[0-9]+\} {
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::QUANT, form_node(NODE, NODE_QUANT, null, substr($text, 1, strpos($text, ',') -1), substr($text, strpos($text, ',')+1, strlen($text)-2-strpos($text, ','))));
    return $res;
}
<YYINITIAL> \{[0-9]+,\} {
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::QUANT, form_node(NODE, NODE_QUANT, null, substr($text, 1, strpos($text, ',') -1), -1));
    return $res;
}
<YYINITIAL> \{,[0-9]+\} {
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::QUANT, form_node(NODE, NODE_QUANT, null, 0, substr($text, 2, strlen($text) - 3)));
    return $res;
}
<YYINITIAL> \{[0-9]+\} {
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::QUANT, form_node(NODE, NODE_QUANT, null, substr($text, 1, strpos($text, ',') -1), substr($text, 1, strpos($text, ',') -1)));
    return $res;
}
<YYINITIAL> \{[0-9]+,[0-9]+\}\? {
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::LAZY_QUANT, form_node(NODE, NODE_QUANT, null, substr($text, 1, strpos($text, ',') -1), substr($text, strpos($text, ',')+1, strlen($text)-2-strpos($text, ',')), false));
    return $res;
}
<YYINITIAL> \{[0-9]+,\}\? {
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::LAZY_QUANT, form_node(NODE, NODE_QUANT, null, substr($text, 1, strpos($text, ',') -1), -1, false));
    return $res;
}
<YYINITIAL> \{,[0-9]+\}\? {
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::LAZY_QUANT, form_node(NODE, NODE_QUANT, null, 0, substr($text, 2, strlen($text) - 3), false));
    return $res;
}
<YYINITIAL> \{[0-9]+\}\? {
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::LAZY_QUANT, form_node(NODE, NODE_QUANT, null, substr($text, 1, strpos($text, ',') -1), substr($text, 1, strpos($text, ',') -1), false));
    return $res;
}
<YYINITIAL> \[ {
    $this->cc = new node;
    $this->cc->direction = true;
    $this->cc->type = LEAF;
    $this->cc->subtype = LEAF_CHARCLASS;
    $this->cccharnumber = 0;
    $this->yybegin(self::CHARCLASS);
}
<YYINITIAL> \( {
    $res = form_res(preg_parser_yyParser::OPENBRACK, 0);
    return $res;
}
<YYINITIAL> \) {
    $res = form_res(preg_parser_yyParser::CLOSEBRACK, 0);
    return $res;
}
<YYINITIAL> \(\?> {
    $res = form_res(preg_parser_yyParser::ONETIMESUBPATT, 0);
    return $res;
}
<YYINITIAL> \(\?: {
    $res = form_res(preg_parser_yyParser::GROUPING, 0);
    return $res;
}
<YYINITIAL> \(\?\(\?= {
    $res = form_res(preg_parser_yyParser::CONDSUBPATT, NODE_ASSERTTF);
    return $res;
}
<YYINITIAL> \(\?\(\?! {
    $res = form_res(preg_parser_yyParser::CONDSUBPATT, NODE_ASSERTFF);
    return $res;
}
<YYINITIAL> \(\?\(\?<= {
    $res = form_res(preg_parser_yyParser::CONDSUBPATT, NODE_ASSERTTB);
    return $res;
}
<YYINITIAL> \(\?\(\?<! {
    $res = form_res(preg_parser_yyParser::CONDSUBPATT, NODE_ASSERTFB);
    return $res;
}
<YYINITIAL> \(\?= {
    $res = form_res(preg_parser_yyParser::ASSERT, NODE_ASSERTTF);
    return $res;
}
<YYINITIAL> \(\?! {
    $res = form_res(preg_parser_yyParser::ASSERT, NODE_ASSERTFF);
    return $res;
}
<YYINITIAL> \(\?<= {
    $res = form_res(preg_parser_yyParser::ASSERT, NODE_ASSERTTB);
    return $res;
}
<YYINITIAL> \(\?<! {
    $res = form_res(preg_parser_yyParser::ASSERT, NODE_ASSERTFB);
    return $res;
}
<YYINITIAL> \. {
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_METASYMBOLDOT));
    return $res;
}
<YYINITIAL> [^\[\]\\*+?{}()|.^$] {
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, $this->yytext()));
    return $res;
}
<YYINITIAL> \| {
    $res = form_res(preg_parser_yyParser::ALT, 0);
    return $res;
}
<YYINITIAL> \\[\[\]?*+{}|().] {
    $text = $this->yytext();
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, $text[1]));
    return $res;
}
<YYINITIAL> \\[0-9][0-9]? {
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_LINK, substr($this->yytext(), 1)));
    return $res;
}
<YYINITIAL> \\0[0-9][0-9]?|[0-9][0-9][0-9] {
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, chr(octdec(substr($this->yytext(), 1)))));
    return $res;
}
<YYINITIAL> \\x[0-9][0-9] {
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, chr(hexdec(substr($this->yytext(), 1)))));
    return $res;
}
<YYINITIAL> \\\\ {
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, '\\'));
    return $res;
}
<YYINITIAL> \\b {
    $res = form_res(preg_parser_yyParser::WORDBREAK, 0);
    return $res;
}
<YYINITIAL> \\B {
    $res = form_res(preg_parser_yyParser::WORDNOTBREAK, 0);
    return $res;
}
<YYINITIAL> \\d {
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, '0123456789'));
    return $res;
}
<YYINITIAL> \\D {
    $PARSLEAF = form_node(LEAF, LEAF_CHARCLASS, '0123456789');
    $PARSLEAF->direction = false;
    $res = form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
<YYINITIAL> \\w {
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, 'qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPASDFGHJKLMNBVCXZ_0123456789'));
    return $res;
}
<YYINITIAL> \\W {
    $PARSLEAF = form_node(LEAF, LEAF_CHARCLASS, 'qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPASDFGHJKLMNBVCXZ_0123456789');
    $PARSLEAF->direction = false;
    $res = form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
<YYINITIAL> \\s {
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, ' '));
    return $res;
}
<YYINITIAL> \\S {
    $PARSLEAF = form_node(LEAF, LEAF_CHARCLASS, ' ');
    $PARSLEAF->direction = false;
    $res = form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
<YYINITIAL> \\t {
    $res = form_res(preg_parser_yyParser::PARSLEAF, form_node(LEAF, LEAF_CHARCLASS, chr(9)));
    return $res;
}
<YYINITIAL> "^" {
    $res = form_res(preg_parser_yyParser::STARTANCHOR, 0);
    return $res;
}
<YYINITIAL> "$" {
    $res = form_res(preg_parser_yyPARSER::ENDANCHOR, 0);
    return $res;
}
<CHARCLASS> \\\\ {
    $this->cc->chars .= '\\';
    $this->cccharnumber++;
}
<CHARCLASS> \\\[ {
    $this->cc->chars .= '[';
    $this->cccharnumber++;
}
<CHARCLASS> \\\] {
    $this->cc->chars .= ']';
    $this->cccharnumber++;
}
<CHARCLASS> \\0[0-9][0-9]|[0-9][0-9][0-9] {
    $this->cc->chars .= chr(octdec(substr($this->yytext(), 1)));
    $this->cccharnumber++;
}
<CHARCLASS> \\x[0-9][0-9] {
    $this->cccharnumber++;
    $this->cc->chars .= chr(hexdec(substr($this->yytext(), 1)));
}
<CHARCLASS> \\d {
    $this->cccharnumber++;
    $this->cc->chars .= '0123456789';
}
<CHARCLASS> \\w {
    $this->cccharnumber++;
    $this->cc->chars .= 'qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPASDFGHJKLMNBVCXZ_0123456789';
}
<CHARCLASS> \\s {
    $this->cccharnumber++;
    $this->cc->chars .= ' ';
}
<CHARCLASS> \\t {
    $this->cccharnumber++;
    $this->cc->chars .= chr(9);
}
<CHARCLASS> "^" {
    if ($this->cccharnumber) {
        $this->cc .= '^';
    } else {
        $this->cc->direction = false;
    }
    $this->cccharnumber++;
}
<CHARCLASS> "^-" {
    if (!$this->cccharnumber) {
        $this->cc->chars .= '-';
        $this->cc->direction;
        $this->cccharnumber++;
    }
}
<CHARCLASS> - {
    if (!$this->cccharnumber) {
        $this->cc->chars .= '-';
    }
    $this->cccharnumber++;
}
<CHARCLASS> [0-9]-[0-9]|[a-z]-[a-z]|[A-Z]-[A-Z] {
    $text = $this->yytext();
    form_num_interval($this->cc, $text[0], $text[2]);
}
<CHARCLASS> \\- {
    $this->cc->chars .= '-';
    $this->cccharnumber++;
}
<CHARCLASS> [^-\[\]\\^] {
    $this->cc->chars .= $this->yytext();
    $this->cccharnumber++;
}
<CHARCLASS> \] {
    $res = form_res(preg_parser_yyParser::PARSLEAF, $this->cc);
    $this->yybegin(self::YYINITIAL);
    $this->cc = null;
    return $res;
}