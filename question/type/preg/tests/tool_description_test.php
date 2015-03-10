<?php

/**
 * Tests for /question/type/preg/authoring_tools/preg_description_tool.php.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Pahomov Dmitry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_description_tool.php');

class qtype_preg_tool_description_test extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider charset_provider
     */
    public function test_charset($regex,$expected_en,$expected_ru)
    {
        $handler = new qtype_preg_description_tool($regex);
        $result = $handler->description('%content','%content');
        $this->assertEquals($expected_en, $result);
    }
    public function charset_provider()
    {
        return array(
          array('[^[:^word:]abc\pL]','any character except the following: not a word character, letter, <span style="color:blue">a</span>, <span style="color:blue">b</span>, <span style="color:blue">c</span>;','рус - TODO'),
          array('a','<span style="color:blue">a</span>','рус - TODO'),
          array('[^a]','not <span style="color:blue">a</span>','рус - TODO'),
          array('\w','a word character','рус - TODO'),
          array('\W','not a word character','рус - TODO'),
          //array('[\x00-\xff]','one of the following characters: null character(NUL), start of header character (SOH), start of text character(STX), end of text character(ETX), end of transmission character(EOT), enquiry character(ENQ), acknowledgment character(ACK), bell character(BEL), backspace character(BS), tabulation(HT), line feed(LF), vertical tabulation(VT), form feed(FF), carriage return character(CR), shift out character (SO), shift in character (SI), data link escape character (DLE), device control 1 (oft. XON) character (DC1), device control 2 character (DC2), device control 3 (oft. XOFF) character (DC3), device control 4 character (DC4), negative acknowledgement character (NAK), synchronous idle character (SYN), end of transmission block character (ETB), cancel character (CAN), end of medium character (EM), substitute character (SUB), escape(ESC), file separator character (FS), group separator character (GS), record separator character (RS), unit separator character (US), space, <span style="color:blue">!</span>, <span style="color:blue">"</span>, <span style="color:blue">#</span>, <span style="color:blue">$</span>, <span style="color:blue">%</span>, <span style="color:blue">&</span>, <span style="color:blue">\'</span>, <span style="color:blue">(</span>, <span style="color:blue">)</span>, <span style="color:blue">*</span>, <span style="color:blue">+</span>, <span style="color:blue">,</span>, <span style="color:blue">-</span>, <span style="color:blue">.</span>, <span style="color:blue">/</span>, <span style="color:blue">0</span>, <span style="color:blue">1</span>, <span style="color:blue">2</span>, <span style="color:blue">3</span>, <span style="color:blue">4</span>, <span style="color:blue">5</span>, <span style="color:blue">6</span>, <span style="color:blue">7</span>, <span style="color:blue">8</span>, <span style="color:blue">9</span>, <span style="color:blue">:</span>, <span style="color:blue">;</span>, <span style="color:blue"><</span>, <span style="color:blue">=</span>, <span style="color:blue">></span>, <span style="color:blue">?</span>, <span style="color:blue">@</span>, <span style="color:blue">A</span>, <span style="color:blue">B</span>, <span style="color:blue">C</span>, <span style="color:blue">D</span>, <span style="color:blue">E</span>, <span style="color:blue">F</span>, <span style="color:blue">G</span>, <span style="color:blue">H</span>, <span style="color:blue">I</span>, <span style="color:blue">J</span>, <span style="color:blue">K</span>, <span style="color:blue">L</span>, <span style="color:blue">M</span>, <span style="color:blue">N</span>, <span style="color:blue">O</span>, <span style="color:blue">P</span>, <span style="color:blue">Q</span>, <span style="color:blue">R</span>, <span style="color:blue">S</span>, <span style="color:blue">T</span>, <span style="color:blue">U</span>, <span style="color:blue">V</span>, <span style="color:blue">W</span>, <span style="color:blue">X</span>, <span style="color:blue">Y</span>, <span style="color:blue">Z</span>, <span style="color:blue">[</span>, <span style="color:blue">\</span>, <span style="color:blue">]</span>, <span style="color:blue">^</span>, <span style="color:blue">_</span>, <span style="color:blue">`</span>, <span style="color:blue">a</span>, <span style="color:blue">b</span>, <span style="color:blue">c</span>, <span style="color:blue">d</span>, <span style="color:blue">e</span>, <span style="color:blue">f</span>, <span style="color:blue">g</span>, <span style="color:blue">h</span>, <span style="color:blue">i</span>, <span style="color:blue">j</span>, <span style="color:blue">k</span>, <span style="color:blue">l</span>, <span style="color:blue">m</span>, <span style="color:blue">n</span>, <span style="color:blue">o</span>, <span style="color:blue">p</span>, <span style="color:blue">q</span>, <span style="color:blue">r</span>, <span style="color:blue">s</span>, <span style="color:blue">t</span>, <span style="color:blue">u</span>, <span style="color:blue">v</span>, <span style="color:blue">w</span>, <span style="color:blue">x</span>, <span style="color:blue">y</span>, <span style="color:blue">z</span>, <span style="color:blue">{</span>, <span style="color:blue">|</span>, <span style="color:blue">}</span>, <span style="color:blue">~</span>, delete character (DEL), character with code 0x80, character with code 0x81, character with code 0x82, character with code 0x83, character with code 0x84, character with code 0x85, character with code 0x86, character with code 0x87, character with code 0x88, character with code 0x89, character with code 0x8A, character with code 0x8B, character with code 0x8C, character with code 0x8D, character with code 0x8E, character with code 0x8F, character with code 0x90, character with code 0x91, character with code 0x92, character with code 0x93, character with code 0x94, character with code 0x95, character with code 0x96, character with code 0x97, character with code 0x98, character with code 0x99, character with code 0x9A, character with code 0x9B, character with code 0x9C, character with code 0x9D, character with code 0x9E, character with code 0x9F, non-breaking space, <span style="color:blue">¡</span>, <span style="color:blue">¢</span>, <span style="color:blue">£</span>, <span style="color:blue">¤</span>, <span style="color:blue">¥</span>, <span style="color:blue">¦</span>, <span style="color:blue">§</span>, <span style="color:blue">¨</span>, <span style="color:blue">©</span>, <span style="color:blue">ª</span>, <span style="color:blue">«</span>, <span style="color:blue">¬</span>, soft hyphen character, <span style="color:blue">®</span>, <span style="color:blue">¯</span>, <span style="color:blue">°</span>, <span style="color:blue">±</span>, <span style="color:blue">²</span>, <span style="color:blue">³</span>, <span style="color:blue">´</span>, <span style="color:blue">µ</span>, <span style="color:blue">¶</span>, <span style="color:blue">·</span>, <span style="color:blue">¸</span>, <span style="color:blue">¹</span>, <span style="color:blue">º</span>, <span style="color:blue">»</span>, <span style="color:blue">¼</span>, <span style="color:blue">½</span>, <span style="color:blue">¾</span>, <span style="color:blue">¿</span>, <span style="color:blue">À</span>, <span style="color:blue">Á</span>, <span style="color:blue">Â</span>, <span style="color:blue">Ã</span>, <span style="color:blue">Ä</span>, <span style="color:blue">Å</span>, <span style="color:blue">Æ</span>, <span style="color:blue">Ç</span>, <span style="color:blue">È</span>, <span style="color:blue">É</span>, <span style="color:blue">Ê</span>, <span style="color:blue">Ë</span>, <span style="color:blue">Ì</span>, <span style="color:blue">Í</span>, <span style="color:blue">Î</span>, <span style="color:blue">Ï</span>, <span style="color:blue">Ð</span>, <span style="color:blue">Ñ</span>, <span style="color:blue">Ò</span>, <span style="color:blue">Ó</span>, <span style="color:blue">Ô</span>, <span style="color:blue">Õ</span>, <span style="color:blue">Ö</span>, <span style="color:blue">×</span>, <span style="color:blue">Ø</span>, <span style="color:blue">Ù</span>, <span style="color:blue">Ú</span>, <span style="color:blue">Û</span>, <span style="color:blue">Ü</span>, <span style="color:blue">Ý</span>, <span style="color:blue">Þ</span>, <span style="color:blue">ß</span>, <span style="color:blue">à</span>, <span style="color:blue">á</span>, <span style="color:blue">â</span>, <span style="color:blue">ã</span>, <span style="color:blue">ä</span>, <span style="color:blue">å</span>, <span style="color:blue">æ</span>, <span style="color:blue">ç</span>, <span style="color:blue">è</span>, <span style="color:blue">é</span>, <span style="color:blue">ê</span>, <span style="color:blue">ë</span>, <span style="color:blue">ì</span>, <span style="color:blue">í</span>, <span style="color:blue">î</span>, <span style="color:blue">ï</span>, <span style="color:blue">ð</span>, <span style="color:blue">ñ</span>, <span style="color:blue">ò</span>, <span style="color:blue">ó</span>, <span style="color:blue">ô</span>, <span style="color:blue">õ</span>, <span style="color:blue">ö</span>, <span style="color:blue">÷</span>, <span style="color:blue">ø</span>, <span style="color:blue">ù</span>, <span style="color:blue">ú</span>, <span style="color:blue">û</span>, <span style="color:blue">ü</span>, <span style="color:blue">ý</span>, <span style="color:blue">þ</span>, <span style="color:blue">ÿ</span>;','рус - TODO'),
          array('[\x00-\xFF]','any character from null character(NUL) to <span style="color:blue">ÿ</span>','рус - TODO'),
          //array('[\x{ff}-\x{1ff}]','one of the following characters: <span style="color:blue">ÿ</span>, <span style="color:blue">Ā</span>, <span style="color:blue">ā</span>, <span style="color:blue">Ă</span>, <span style="color:blue">ă</span>, <span style="color:blue">Ą</span>, <span style="color:blue">ą</span>, <span style="color:blue">Ć</span>, <span style="color:blue">ć</span>, <span style="color:blue">Ĉ</span>, <span style="color:blue">ĉ</span>, <span style="color:blue">Ċ</span>, <span style="color:blue">ċ</span>, <span style="color:blue">Č</span>, <span style="color:blue">č</span>, <span style="color:blue">Ď</span>, <span style="color:blue">ď</span>, <span style="color:blue">Đ</span>, <span style="color:blue">đ</span>, <span style="color:blue">Ē</span>, <span style="color:blue">ē</span>, <span style="color:blue">Ĕ</span>, <span style="color:blue">ĕ</span>, <span style="color:blue">Ė</span>, <span style="color:blue">ė</span>, <span style="color:blue">Ę</span>, <span style="color:blue">ę</span>, <span style="color:blue">Ě</span>, <span style="color:blue">ě</span>, <span style="color:blue">Ĝ</span>, <span style="color:blue">ĝ</span>, <span style="color:blue">Ğ</span>, <span style="color:blue">ğ</span>, <span style="color:blue">Ġ</span>, <span style="color:blue">ġ</span>, <span style="color:blue">Ģ</span>, <span style="color:blue">ģ</span>, <span style="color:blue">Ĥ</span>, <span style="color:blue">ĥ</span>, <span style="color:blue">Ħ</span>, <span style="color:blue">ħ</span>, <span style="color:blue">Ĩ</span>, <span style="color:blue">ĩ</span>, <span style="color:blue">Ī</span>, <span style="color:blue">ī</span>, <span style="color:blue">Ĭ</span>, <span style="color:blue">ĭ</span>, <span style="color:blue">Į</span>, <span style="color:blue">į</span>, <span style="color:blue">İ</span>, <span style="color:blue">ı</span>, <span style="color:blue">Ĳ</span>, <span style="color:blue">ĳ</span>, <span style="color:blue">Ĵ</span>, <span style="color:blue">ĵ</span>, <span style="color:blue">Ķ</span>, <span style="color:blue">ķ</span>, <span style="color:blue">ĸ</span>, <span style="color:blue">Ĺ</span>, <span style="color:blue">ĺ</span>, <span style="color:blue">Ļ</span>, <span style="color:blue">ļ</span>, <span style="color:blue">Ľ</span>, <span style="color:blue">ľ</span>, <span style="color:blue">Ŀ</span>, <span style="color:blue">ŀ</span>, <span style="color:blue">Ł</span>, <span style="color:blue">ł</span>, <span style="color:blue">Ń</span>, <span style="color:blue">ń</span>, <span style="color:blue">Ņ</span>, <span style="color:blue">ņ</span>, <span style="color:blue">Ň</span>, <span style="color:blue">ň</span>, <span style="color:blue">ŉ</span>, <span style="color:blue">Ŋ</span>, <span style="color:blue">ŋ</span>, <span style="color:blue">Ō</span>, <span style="color:blue">ō</span>, <span style="color:blue">Ŏ</span>, <span style="color:blue">ŏ</span>, <span style="color:blue">Ő</span>, <span style="color:blue">ő</span>, <span style="color:blue">Œ</span>, <span style="color:blue">œ</span>, <span style="color:blue">Ŕ</span>, <span style="color:blue">ŕ</span>, <span style="color:blue">Ŗ</span>, <span style="color:blue">ŗ</span>, <span style="color:blue">Ř</span>, <span style="color:blue">ř</span>, <span style="color:blue">Ś</span>, <span style="color:blue">ś</span>, <span style="color:blue">Ŝ</span>, <span style="color:blue">ŝ</span>, <span style="color:blue">Ş</span>, <span style="color:blue">ş</span>, <span style="color:blue">Š</span>, <span style="color:blue">š</span>, <span style="color:blue">Ţ</span>, <span style="color:blue">ţ</span>, <span style="color:blue">Ť</span>, <span style="color:blue">ť</span>, <span style="color:blue">Ŧ</span>, <span style="color:blue">ŧ</span>, <span style="color:blue">Ũ</span>, <span style="color:blue">ũ</span>, <span style="color:blue">Ū</span>, <span style="color:blue">ū</span>, <span style="color:blue">Ŭ</span>, <span style="color:blue">ŭ</span>, <span style="color:blue">Ů</span>, <span style="color:blue">ů</span>, <span style="color:blue">Ű</span>, <span style="color:blue">ű</span>, <span style="color:blue">Ų</span>, <span style="color:blue">ų</span>, <span style="color:blue">Ŵ</span>, <span style="color:blue">ŵ</span>, <span style="color:blue">Ŷ</span>, <span style="color:blue">ŷ</span>, <span style="color:blue">Ÿ</span>, <span style="color:blue">Ź</span>, <span style="color:blue">ź</span>, <span style="color:blue">Ż</span>, <span style="color:blue">ż</span>, <span style="color:blue">Ž</span>, <span style="color:blue">ž</span>, <span style="color:blue">ſ</span>, <span style="color:blue">ƀ</span>, <span style="color:blue">Ɓ</span>, <span style="color:blue">Ƃ</span>, <span style="color:blue">ƃ</span>, <span style="color:blue">Ƅ</span>, <span style="color:blue">ƅ</span>, <span style="color:blue">Ɔ</span>, <span style="color:blue">Ƈ</span>, <span style="color:blue">ƈ</span>, <span style="color:blue">Ɖ</span>, <span style="color:blue">Ɗ</span>, <span style="color:blue">Ƌ</span>, <span style="color:blue">ƌ</span>, <span style="color:blue">ƍ</span>, <span style="color:blue">Ǝ</span>, <span style="color:blue">Ə</span>, <span style="color:blue">Ɛ</span>, <span style="color:blue">Ƒ</span>, <span style="color:blue">ƒ</span>, <span style="color:blue">Ɠ</span>, <span style="color:blue">Ɣ</span>, <span style="color:blue">ƕ</span>, <span style="color:blue">Ɩ</span>, <span style="color:blue">Ɨ</span>, <span style="color:blue">Ƙ</span>, <span style="color:blue">ƙ</span>, <span style="color:blue">ƚ</span>, <span style="color:blue">ƛ</span>, <span style="color:blue">Ɯ</span>, <span style="color:blue">Ɲ</span>, <span style="color:blue">ƞ</span>, <span style="color:blue">Ɵ</span>, <span style="color:blue">Ơ</span>, <span style="color:blue">ơ</span>, <span style="color:blue">Ƣ</span>, <span style="color:blue">ƣ</span>, <span style="color:blue">Ƥ</span>, <span style="color:blue">ƥ</span>, <span style="color:blue">Ʀ</span>, <span style="color:blue">Ƨ</span>, <span style="color:blue">ƨ</span>, <span style="color:blue">Ʃ</span>, <span style="color:blue">ƪ</span>, <span style="color:blue">ƫ</span>, <span style="color:blue">Ƭ</span>, <span style="color:blue">ƭ</span>, <span style="color:blue">Ʈ</span>, <span style="color:blue">Ư</span>, <span style="color:blue">ư</span>, <span style="color:blue">Ʊ</span>, <span style="color:blue">Ʋ</span>, <span style="color:blue">Ƴ</span>, <span style="color:blue">ƴ</span>, <span style="color:blue">Ƶ</span>, <span style="color:blue">ƶ</span>, <span style="color:blue">Ʒ</span>, <span style="color:blue">Ƹ</span>, <span style="color:blue">ƹ</span>, <span style="color:blue">ƺ</span>, <span style="color:blue">ƻ</span>, <span style="color:blue">Ƽ</span>, <span style="color:blue">ƽ</span>, <span style="color:blue">ƾ</span>, <span style="color:blue">ƿ</span>, <span style="color:blue">ǀ</span>, <span style="color:blue">ǁ</span>, <span style="color:blue">ǂ</span>, <span style="color:blue">ǃ</span>, <span style="color:blue">Ǆ</span>, <span style="color:blue">ǅ</span>, <span style="color:blue">ǆ</span>, <span style="color:blue">Ǉ</span>, <span style="color:blue">ǈ</span>, <span style="color:blue">ǉ</span>, <span style="color:blue">Ǌ</span>, <span style="color:blue">ǋ</span>, <span style="color:blue">ǌ</span>, <span style="color:blue">Ǎ</span>, <span style="color:blue">ǎ</span>, <span style="color:blue">Ǐ</span>, <span style="color:blue">ǐ</span>, <span style="color:blue">Ǒ</span>, <span style="color:blue">ǒ</span>, <span style="color:blue">Ǔ</span>, <span style="color:blue">ǔ</span>, <span style="color:blue">Ǖ</span>, <span style="color:blue">ǖ</span>, <span style="color:blue">Ǘ</span>, <span style="color:blue">ǘ</span>, <span style="color:blue">Ǚ</span>, <span style="color:blue">ǚ</span>, <span style="color:blue">Ǜ</span>, <span style="color:blue">ǜ</span>, <span style="color:blue">ǝ</span>, <span style="color:blue">Ǟ</span>, <span style="color:blue">ǟ</span>, <span style="color:blue">Ǡ</span>, <span style="color:blue">ǡ</span>, <span style="color:blue">Ǣ</span>, <span style="color:blue">ǣ</span>, <span style="color:blue">Ǥ</span>, <span style="color:blue">ǥ</span>, <span style="color:blue">Ǧ</span>, <span style="color:blue">ǧ</span>, <span style="color:blue">Ǩ</span>, <span style="color:blue">ǩ</span>, <span style="color:blue">Ǫ</span>, <span style="color:blue">ǫ</span>, <span style="color:blue">Ǭ</span>, <span style="color:blue">ǭ</span>, <span style="color:blue">Ǯ</span>, <span style="color:blue">ǯ</span>, <span style="color:blue">ǰ</span>, <span style="color:blue">Ǳ</span>, <span style="color:blue">ǲ</span>, <span style="color:blue">ǳ</span>, <span style="color:blue">Ǵ</span>, <span style="color:blue">ǵ</span>, <span style="color:blue">Ƕ</span>, <span style="color:blue">Ƿ</span>, <span style="color:blue">Ǹ</span>, <span style="color:blue">ǹ</span>, <span style="color:blue">Ǻ</span>, <span style="color:blue">ǻ</span>, <span style="color:blue">Ǽ</span>, <span style="color:blue">ǽ</span>, <span style="color:blue">Ǿ</span>, <span style="color:blue">ǿ</span>;','рус - TODO'),
          array('[\x{FF}-\x{1FF}]','any character from <span style="color:blue">ÿ</span> to <span style="color:blue">ǿ</span>','рус - TODO'),
          array('[a-z]','any character from <span style="color:blue">a</span> to <span style="color:blue">z</span>','рус - TODO'),
          array('[a-c]','one of the following characters: <span style="color:blue">a</span>, <span style="color:blue">b</span>, <span style="color:blue">c</span>;','рус - TODO'),
          array('[\x00\r\x22-\x3C\t]','one of the following characters: null character(NUL), carriage return character(CR), any character from <span style="color:blue">&#34;</span> to <span style="color:blue">&#60;</span>, tabulation(HT);','рус - TODO'),
          array('[ÿ-ƎƏ-ǿ]','any character from <span style="color:blue">ÿ</span> to <span style="color:blue">ǿ</span>','рус - TODO'),
          array('[dcab]','one of the following characters: <span style="color:blue">d</span>, <span style="color:blue">c</span>, <span style="color:blue">a</span>, <span style="color:blue">b</span>;','рус - TODO'),
        );
    }

    //------------------------------------------------------------------
    public function test_meta()
    {
        $handler = new qtype_preg_description_tool('a|b|');
        $result = $handler->description('%content','%content');
        $expected = '<span style="color:blue">a</span> or <span style="color:blue">b</span> or nothing';
        $this->assertEquals($expected, $result);
    }

    //------------------------------------------------------------------

    /**
     * @dataProvider assert_provider
     */
    public function test_assert($regex,$expected_en,$expected_ru)
    {
        $handler = new qtype_preg_description_tool($regex);
        //var_dump($handler);
        $result = $handler->description('%content','%content');
        $this->assertEquals($expected_en, $result);
    }
    public function assert_provider()
    {
        return array(
          array('^','start of the string','рус - TODO'),
          array('$','end of the string','рус - TODO'),
          array('\b','a word boundary','рус - TODO'),
          array('\B','not a word boundary','рус - TODO'),
          array('\A','start of the string','рус - TODO'),
          array('\Z','end of the string','рус - TODO'),
          //array('\G','at the first matching position in the subject','рус - TODO')
        );
    }

    //------------------------------------------------------------------

    public function test_backref()
    {
        $handler = new qtype_preg_description_tool('(a)\1');
        //var_dump($handler);
        $result = $handler->description('%content','%content');
        $expected = 'subpattern #1: [ <span style="color:blue">a</span> ] then text matched by subpattern #1';
        $this->assertEquals($expected, $result);
    }

    //------------------------------------------------------------------

    public function test_recursion()
    {
        $handler = new qtype_preg_description_tool('(?(R)a|b)');
        //var_dump($handler);
        $result = $handler->description('%content','%content');
        $expected = 'if the whole pattern is in recursive matching then check: [<span style="color:blue">a</span>] else check: [<span style="color:blue">b</span>]';
        $this->assertEquals($expected, $result);
    }

    //------------------------------------------------------------------

    /**
     * @dataProvider concat_provider
     */
    public function test_concat($regex,$expected_en,$expected_ru)
    {
        $handler = new qtype_preg_description_tool($regex);
        //var_dump($handler);
        $result = $handler->description('%content','%content');
        $this->assertEquals($expected_en, $result);
    }

    public function concat_provider()
    {
        return array(
          array('ab','<span style="color:blue">a</span><span style="color:blue">b</span>','рус - TODO'),
          array('[a|b]c','one of the following characters: <span style="color:blue">a</span>, <span style="color:blue">&#124;</span>, <span style="color:blue">b</span>; then <span style="color:blue">c</span>','рус - TODO'),
          array('abc','<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>','рус - TODO'),
          array(' \t\n\r','space then tabulation(HT) then line feed(LF) then carriage return character(CR)','рус - TODO'),
          array('\0113','tabulation(HT) then <span style="color:blue">3</span>','рус - TODO'),
          array('bcab','<span style="color:blue">b</span><span style="color:blue">c</span><span style="color:blue">a</span><span style="color:blue">b</span>','рус - TODO'),
          array('\x{2002}\x{2003}\x{2009}\x{200C}\x{200D}','en space then em space then thin space then zero width non-joiner then zero width joiner','рус - TODO'),
        );
    }

    //------------------------------------------------------------------

    /**
     * @dataProvider alt_provider
     */
    public function test_alt($regex,$expected_en,$expected_ru)
    {
        $handler = new qtype_preg_description_tool($regex);
        //var_dump($handler);
        $result = $handler->description('%content','%content');
        $this->assertEquals($expected_en, $result);
    }

    public function alt_provider()
    {
        return array(
          array('a|b','<span style="color:blue">a</span> or <span style="color:blue">b</span>','рус - TODO'),
          array('a|b|','<span style="color:blue">a</span> or <span style="color:blue">b</span> or nothing','рус - TODO'),
          array('a|b|c','<span style="color:blue">a</span> or <span style="color:blue">b</span> or <span style="color:blue">c</span>','рус - TODO'),
        );
    }

    //------------------------------------------------------------------

    /**
     * @dataProvider nassert_provider
     */
    public function test_nassert($regex,$expected_en,$expected_ru)
    {
        $handler = new qtype_preg_description_tool($regex);
        //var_dump($handler);
        $result = $handler->description('%content','%content');
        $this->assertEquals($expected_en, $result);
    }

    public function nassert_provider()
    {
        return array(
          array('(?=abc)g','further text should match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] and <span style="color:blue">g</span>','рус - TODO'),
          array('(?!abc)g','further text should not match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] and <span style="color:blue">g</span>','рус - TODO'),
          array('(?<=abc)g','preceding text should match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] then <span style="color:blue">g</span>','рус - TODO'),
          array('(?<!abc)g','preceding text should not match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] then <span style="color:blue">g</span>','рус - TODO'),
          array('a(?=abc)g','<span style="color:blue">a</span> then further text should match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] and <span style="color:blue">g</span>','рус - TODO'),
          array('a(?!abc)g','<span style="color:blue">a</span> then further text should not match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] and <span style="color:blue">g</span>','рус - TODO'),
          array('a(?<=abc)g','<span style="color:blue">a</span> and preceding text should match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] then <span style="color:blue">g</span>','рус - TODO'),
          array('a(?<!abc)g','<span style="color:blue">a</span> and preceding text should not match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] then <span style="color:blue">g</span>','рус - TODO'),
        );
    }

    //------------------------------------------------------------------

    /**
     * @dataProvider quant_provider
     */
    public function test_quant($regex,$expected_en,$expected_ru)
    {
        $handler = new qtype_preg_description_tool($regex);
        //var_dump($handler);
        $result = $handler->description('%content','%content');
        $this->assertEquals($expected_en, $result);
    }

    public function quant_provider()
    {
        return array(
          array('g{0,1}','<span style="color:blue">g</span> may be missing','рус - TODO'),
          array('g+','<span style="color:blue">g</span> repeated any number of times','рус - TODO'),
          array('g*','<span style="color:blue">g</span> repeated any number of times or missing','рус - TODO'),
          array('g?','<span style="color:blue">g</span> may be missing','рус - TODO'),
          array('g{0,1}','<span style="color:blue">g</span> may be missing','рус - TODO'),
          array('g{0,}','<span style="color:blue">g</span> repeated any number of times or missing','рус - TODO'),
          array('g{1,}','<span style="color:blue">g</span> repeated any number of times','рус - TODO'),
          array('g{3}','<span style="color:blue">g</span> repeated 3 times','рус - TODO'),
          array('g{2,5}','<span style="color:blue">g</span> repeated from 2 to 5 times','рус - TODO'),
        );
    }

    //------------------------------------------------------------------

    /**
     * @dataProvider option_provider
     */
    public function test_option($regex,$expected_en,$expected_ru)
    {
        $handler = new qtype_preg_description_tool($regex);
        //var_dump($handler);
        $result = $handler->description('%content','%content');
        $this->assertEquals($expected_en, $result);
    }

    public function option_provider()
    {
        return array(
          array('(?i)b','caseless: <span style="color:blue">b</span>','рус - TODO'),
          array('(?i:b)','grouping: [ caseless: <span style="color:blue">b</span> ]','рус - TODO'),
          array('(a(?i)b)c','case sensitive: subpattern #1: [ <span style="color:blue">a</span> then caseless: <span style="color:blue">b</span> ] then <span style="color:blue">c</span>','рус - TODO'),
          //array('(?i)a(?u)b(?-i)c(?-u)d','subexpression #1: [<span style="color:blue">a</span>caseless: <span style="color:blue">b</span>] then case sensitive: <span style="color:blue">c</span>','рус - TODO'),
        );
    }

    //------------------------------------------------------------------

    public function test_numbering()
    {
        $handler = new qtype_preg_description_tool('([a|b]|)\W+');
        //var_dump($handler);
        $result = $handler->default_description();
        //$expected = '<span class="description_node_6"><span class="description_node_3">subexpression #1: [<span class="description_node_2"><span class="description_node_0">one of the following characters: <span style="color:blue">a</span>, <span style="color:blue">|</span>, <span style="color:blue">b</span>;</span> or <span class="description_node_1">nothing</span></span>]</span> then <span class="description_node_5"><span class="description_node_4">not word character</span> is repeated any number of times</span></span>';
        $expected = '<span style="background: white"><span class="description_node_1" style="background: white" ><span class="description_node_2" style="background: white" >subpattern #1: [ <span class="description_node_3" style="background: white" ><span class="description_node_4" style="background: white" >one of the following characters: <span style="color:blue">a</span>, <span style="color:blue">&#124;</span>, <span style="color:blue">b</span>;</span> or <span class="description_node_5" style="background: white" >nothing</span></span> ]</span> then <span class="description_node_6" style="background: white" ><span class="description_node_7" style="background: white" >not a word character</span> repeated any number of times</span></span></span>';
        $this->assertEquals($expected, $result);
    }

    //------------------------------------------------------------------

    /**
     * @dataProvider condmask_provider
     */
    public function test_condmask($regex,$expected_en,$expected_ru)
    {
        $handler = new qtype_preg_description_tool($regex);
        //if($regex == '(?(?=a)b)' )var_dump($handler->dstroot);
        $result = $handler->description('%content','%content');
        $this->assertEquals($expected_en, $result);
    }

    public function condmask_provider()
    {
        return array(
          array('(?(?=a)b|c)','if further text matches: [<span style="color:blue">a</span>] then check: [<span style="color:blue">b</span>] else check: [<span style="color:blue">c</span>]','рус - TODO'),
          array('(?(?!a)b|c)','if further text does not match: [<span style="color:blue">a</span>] then check: [<span style="color:blue">b</span>] else check: [<span style="color:blue">c</span>]','рус - TODO'),
          array('(?(?<=a)b|c)','if preceding text matches: [<span style="color:blue">a</span>] then check: [<span style="color:blue">b</span>] else check: [<span style="color:blue">c</span>]','рус - TODO'),
          array('(?(?<!a)b|c)','if preceding text does not match: [<span style="color:blue">a</span>] then check: [<span style="color:blue">b</span>] else check: [<span style="color:blue">c</span>]','рус - TODO'),
          array('(?(?=a)b)','if further text matches: [<span style="color:blue">a</span>] then check: [<span style="color:blue">b</span>]','рус - TODO'),
          array('(?(1)a)','if the subpattern #1 has been successfully matched then check: [<span style="color:blue">a</span>]','рус - TODO'),
          array('(?(name)a)','if the subpattern "name" has been successfully matched then check: [<span style="color:blue">a</span>]','рус - TODO'),
          array('(?(<name>)a)','if the subpattern "name" has been successfully matched then check: [<span style="color:blue">a</span>]','рус - TODO'),
          array('(?(<name>)a|b)','if the subpattern "name" has been successfully matched then check: [<span style="color:blue">a</span>] else check: [<span style="color:blue">b</span>]','рус - TODO'),
          array('(?(DEFINE)(?<name>a))','definition of subpattern "name" #1: [ <span style="color:blue">a</span> ]','рус - TODO'),
        );
    }

    //------------------------------------------------------------------

    /**
     * @dataProvider postprocessing_provider
     */
    public function test_postprocessing($regex,$expected_en,$expected_ru)
    {
        $handler = new qtype_preg_description_tool($regex);
        //var_dump($handler);
        $result = $handler->description('%content','%content');
        $this->assertEquals($expected_en, $result);
    }

    public function postprocessing_provider()
    {
        return array(
          array('([abc])','subpattern #1: [ one of the following characters: <span style="color:blue">a</span>, <span style="color:blue">b</span>, <span style="color:blue">c</span>; ]','рус - TODO'),
          array('[^\S]','a white space','рус - TODO'),
        );
    }

    //------------------------------------------------------------------

    /**
     * @dataProvider subexpression_provider
     */
    public function test_subexpression($regex,$expected_en,$expected_ru)
    {
        $handler = new qtype_preg_description_tool($regex);
        //var_dump($handler);
        $result = $handler->description('%content','%content');
        $this->assertEquals($expected_en, $result);
    }

    public function subexpression_provider()
    {
        return array(
          array('(?:[abc])','grouping: [ one of the following characters: <span style="color:blue">a</span>, <span style="color:blue">b</span>, <span style="color:blue">c</span>; ]','рус - TODO'),
          //array('(?|(a)|(b))','1','рус - TODO'), doesnt work now
        );
    }

    /**
     * @dataProvider templates_provider
     */
    public function test_templates($regex,$expected_en,$expected_ru)
    {
        $handler = new qtype_preg_description_tool($regex);
        $root = $handler->get_ast_root();
        //var_dump($handler);
        $result = $handler->description('%content','%content');
        $this->assertEquals($expected_en, $result);
        //$this->assertTrue(false);
    }

    public function templates_provider()
    {
        return array(
            array('(?###word)', 'word', 'tbd'),
            array('(?###integer)', 'integer', 'tbd'),
            array('(?###parens_req<)a(?###>)', '$$1 in parens', 'tbd'),
            array('(?###parens_opt<)a(?###>)', '$$1 in optional parens', 'tbd')
        );
    }

    public function test_template_errors() {
        $handler = new qtype_preg_description_tool('(?###wtf)');
        $root = $handler->get_ast_root();
        $whatever = $handler->description('%content','%content');
    }

    //------------------------------------------------------------------

    /**
     * @dataProvider err_provider
     */
    /*public function test_err($regex,$expected_en,$expected_ru)
    {
        $handler = new qtype_preg_description_tool($regex);
        //var_dump($handler);
        $result = $handler->description('%content','%content');
        $this->assertEquals($expected_en, $result);
    }

    public function err_provider()
    {
        return array(
          array('a{9,0}','<span style="color:blue">a</span><span style="color:red"> is repeated from 9 to 0 times (incorrect quantifier borders)</span>','рус - TODO'),
          array('(a','<span style="color:red">Syntax error: missing a closing parenthesis \')\' for the opening parenthesis in position 0.</span> Operands: <span style="color:blue">a</span>','рус - TODO'),
        );
    }*/

    /**
     * @dataProvider form_provider
     */
    /*public function test_form($regex,$expected_en,$expected_ru)
    {
        $handler = new qtype_preg_description_tool($regex);
        $result = $handler->form_description('g');
        $this->assertEquals($expected_en, $result);
    }

    public function form_provider()
    {
        return array(
          array('a','<span style="color:blue">a</span>(form g)','рус - TODO'),
          array('\w','word character(form g)','рус - TODO'),
          array('$','end of the string(form g)','рус - TODO'),
          array('a|bc|','<span style="color:blue">a</span>(form g) or <span style="color:blue">b</span>(form g)<span style="color:blue">c</span>(form g) or nothing(form g)','рус - TODO'),
        );
    }*/

    /*public function test_vardump()
    {
        $regex = '(?i)[\xff\x00-\x1fA-B\t\n]';
        $expected = '000';
        //var_dump($options);
        $handler = new qtype_preg_description_tool($regex);
        //var_dump($handler);
        $result = $handler->description('%content','%content');
        $this->assertEquals($expected_en, $result);
    }*/
}

/*
[\xff\x00-\x1fA-B\t\n]

  ["userinscription"]=>
  array(3) {
    [0]=>
    string(14) "\xff\x00-\\t\n"
    [1]=>
    string(3) "-"
    [2]=>
    string(3) "A-B"
  }
*/

