<?php

/**
 * tests for /question/type/preg/author_tool_description/preg_description.php'
 *
 * @copyright &copy; 2012 Pahomov Dmitry
 * @author Pahomov Dmitry
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authors_tool/preg_description.php');

class qtype_preg_description_test extends PHPUnit_Framework_TestCase {
    
    /**
     * @dataProvider charset_provider
     */
    public function test_charset($regex, $expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    } 
    public function charset_provider()
    {
        return array(
          array('[^[:^word:]abc\pL]','any symbol except the following: not word character, letter, <span style="color:blue">a</span>, <span style="color:blue">b</span>, <span style="color:blue">c</span>;'),
          array('a','<span style="color:blue">a</span>'),
          array('[^a]','not <span style="color:blue">a</span>'),
          array('\w','word character'),
          array('\W','not word character'),
          //array('[\x00-\xff]','one of the following characters: null character(NUL), start of header character (SOH), start of text character(STX), end of text character(ETX), end of transmission character(EOT), enquiry character(ENQ), acknowledgment character(ACK), bell character(BEL), backspace character(BS), tabulation(HT), line feed(LF), vertical tabulation(VT), form feed(FF), carriage return character(CR), shift out character (SO), shift in character (SI), data link escape character (DLE), device control 1 (oft. XON) character (DC1), device control 2 character (DC2), device control 3 (oft. XOFF) character (DC3), device control 4 character (DC4), negative acknowledgement character (NAK), synchronous idle character (SYN), end of transmission block character (ETB), cancel character (CAN), end of medium character (EM), substitute character (SUB), escape(ESC), file separator character (FS), group separator character (GS), record separator character (RS), unit separator character (US), space, <span style="color:blue">!</span>, <span style="color:blue">"</span>, <span style="color:blue">#</span>, <span style="color:blue">$</span>, <span style="color:blue">%</span>, <span style="color:blue">&</span>, <span style="color:blue">\'</span>, <span style="color:blue">(</span>, <span style="color:blue">)</span>, <span style="color:blue">*</span>, <span style="color:blue">+</span>, <span style="color:blue">,</span>, <span style="color:blue">-</span>, <span style="color:blue">.</span>, <span style="color:blue">/</span>, <span style="color:blue">0</span>, <span style="color:blue">1</span>, <span style="color:blue">2</span>, <span style="color:blue">3</span>, <span style="color:blue">4</span>, <span style="color:blue">5</span>, <span style="color:blue">6</span>, <span style="color:blue">7</span>, <span style="color:blue">8</span>, <span style="color:blue">9</span>, <span style="color:blue">:</span>, <span style="color:blue">;</span>, <span style="color:blue"><</span>, <span style="color:blue">=</span>, <span style="color:blue">></span>, <span style="color:blue">?</span>, <span style="color:blue">@</span>, <span style="color:blue">A</span>, <span style="color:blue">B</span>, <span style="color:blue">C</span>, <span style="color:blue">D</span>, <span style="color:blue">E</span>, <span style="color:blue">F</span>, <span style="color:blue">G</span>, <span style="color:blue">H</span>, <span style="color:blue">I</span>, <span style="color:blue">J</span>, <span style="color:blue">K</span>, <span style="color:blue">L</span>, <span style="color:blue">M</span>, <span style="color:blue">N</span>, <span style="color:blue">O</span>, <span style="color:blue">P</span>, <span style="color:blue">Q</span>, <span style="color:blue">R</span>, <span style="color:blue">S</span>, <span style="color:blue">T</span>, <span style="color:blue">U</span>, <span style="color:blue">V</span>, <span style="color:blue">W</span>, <span style="color:blue">X</span>, <span style="color:blue">Y</span>, <span style="color:blue">Z</span>, <span style="color:blue">[</span>, <span style="color:blue">\</span>, <span style="color:blue">]</span>, <span style="color:blue">^</span>, <span style="color:blue">_</span>, <span style="color:blue">`</span>, <span style="color:blue">a</span>, <span style="color:blue">b</span>, <span style="color:blue">c</span>, <span style="color:blue">d</span>, <span style="color:blue">e</span>, <span style="color:blue">f</span>, <span style="color:blue">g</span>, <span style="color:blue">h</span>, <span style="color:blue">i</span>, <span style="color:blue">j</span>, <span style="color:blue">k</span>, <span style="color:blue">l</span>, <span style="color:blue">m</span>, <span style="color:blue">n</span>, <span style="color:blue">o</span>, <span style="color:blue">p</span>, <span style="color:blue">q</span>, <span style="color:blue">r</span>, <span style="color:blue">s</span>, <span style="color:blue">t</span>, <span style="color:blue">u</span>, <span style="color:blue">v</span>, <span style="color:blue">w</span>, <span style="color:blue">x</span>, <span style="color:blue">y</span>, <span style="color:blue">z</span>, <span style="color:blue">{</span>, <span style="color:blue">|</span>, <span style="color:blue">}</span>, <span style="color:blue">~</span>, delete character (DEL), character with code 0x80, character with code 0x81, character with code 0x82, character with code 0x83, character with code 0x84, character with code 0x85, character with code 0x86, character with code 0x87, character with code 0x88, character with code 0x89, character with code 0x8A, character with code 0x8B, character with code 0x8C, character with code 0x8D, character with code 0x8E, character with code 0x8F, character with code 0x90, character with code 0x91, character with code 0x92, character with code 0x93, character with code 0x94, character with code 0x95, character with code 0x96, character with code 0x97, character with code 0x98, character with code 0x99, character with code 0x9A, character with code 0x9B, character with code 0x9C, character with code 0x9D, character with code 0x9E, character with code 0x9F, non-breaking space, <span style="color:blue">¡</span>, <span style="color:blue">¢</span>, <span style="color:blue">£</span>, <span style="color:blue">¤</span>, <span style="color:blue">¥</span>, <span style="color:blue">¦</span>, <span style="color:blue">§</span>, <span style="color:blue">¨</span>, <span style="color:blue">©</span>, <span style="color:blue">ª</span>, <span style="color:blue">«</span>, <span style="color:blue">¬</span>, soft hyphen character, <span style="color:blue">®</span>, <span style="color:blue">¯</span>, <span style="color:blue">°</span>, <span style="color:blue">±</span>, <span style="color:blue">²</span>, <span style="color:blue">³</span>, <span style="color:blue">´</span>, <span style="color:blue">µ</span>, <span style="color:blue">¶</span>, <span style="color:blue">·</span>, <span style="color:blue">¸</span>, <span style="color:blue">¹</span>, <span style="color:blue">º</span>, <span style="color:blue">»</span>, <span style="color:blue">¼</span>, <span style="color:blue">½</span>, <span style="color:blue">¾</span>, <span style="color:blue">¿</span>, <span style="color:blue">À</span>, <span style="color:blue">Á</span>, <span style="color:blue">Â</span>, <span style="color:blue">Ã</span>, <span style="color:blue">Ä</span>, <span style="color:blue">Å</span>, <span style="color:blue">Æ</span>, <span style="color:blue">Ç</span>, <span style="color:blue">È</span>, <span style="color:blue">É</span>, <span style="color:blue">Ê</span>, <span style="color:blue">Ë</span>, <span style="color:blue">Ì</span>, <span style="color:blue">Í</span>, <span style="color:blue">Î</span>, <span style="color:blue">Ï</span>, <span style="color:blue">Ð</span>, <span style="color:blue">Ñ</span>, <span style="color:blue">Ò</span>, <span style="color:blue">Ó</span>, <span style="color:blue">Ô</span>, <span style="color:blue">Õ</span>, <span style="color:blue">Ö</span>, <span style="color:blue">×</span>, <span style="color:blue">Ø</span>, <span style="color:blue">Ù</span>, <span style="color:blue">Ú</span>, <span style="color:blue">Û</span>, <span style="color:blue">Ü</span>, <span style="color:blue">Ý</span>, <span style="color:blue">Þ</span>, <span style="color:blue">ß</span>, <span style="color:blue">à</span>, <span style="color:blue">á</span>, <span style="color:blue">â</span>, <span style="color:blue">ã</span>, <span style="color:blue">ä</span>, <span style="color:blue">å</span>, <span style="color:blue">æ</span>, <span style="color:blue">ç</span>, <span style="color:blue">è</span>, <span style="color:blue">é</span>, <span style="color:blue">ê</span>, <span style="color:blue">ë</span>, <span style="color:blue">ì</span>, <span style="color:blue">í</span>, <span style="color:blue">î</span>, <span style="color:blue">ï</span>, <span style="color:blue">ð</span>, <span style="color:blue">ñ</span>, <span style="color:blue">ò</span>, <span style="color:blue">ó</span>, <span style="color:blue">ô</span>, <span style="color:blue">õ</span>, <span style="color:blue">ö</span>, <span style="color:blue">÷</span>, <span style="color:blue">ø</span>, <span style="color:blue">ù</span>, <span style="color:blue">ú</span>, <span style="color:blue">û</span>, <span style="color:blue">ü</span>, <span style="color:blue">ý</span>, <span style="color:blue">þ</span>, <span style="color:blue">ÿ</span>;'),
          array('[\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0A\x0B\x0C\x0D\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F\x20\x21\x22\x23\x24\x25\x26\x27\x28\x29\x2A\x2B\x2C\x2D\x2E\x2F\x30\x31\x32\x33\x34\x35\x36\x37\x38\x39\x3A\x3B\x3C\x3D\x3E\x3F\x40\x41\x42\x43\x44\x45\x46\x47\x48\x49\x4A\x4B\x4C\x4D\x4E\x4F\x50\x51\x52\x53\x54\x55\x56\x57\x58\x59\x5A\x5B\x5C\x5D\x5E\x5F\x60\x61\x62\x63\x64\x65\x66\x67\x68\x69\x6A\x6B\x6C\x6D\x6E\x6F\x70\x71\x72\x73\x74\x75\x76\x77\x78\x79\x7A\x7B\x7C\x7D\x7E\x7F\x80\x81\x82\x83\x84\x85\x86\x87\x88\x89\x8A\x8B\x8C\x8D\x8E\x8F\x90\x91\x92\x93\x94\x95\x96\x97\x98\x99\x9A\x9B\x9C\x9D\x9E\x9F\xA0\xA1\xA2\xA3\xA4\xA5\xA6\xA7\xA8\xA9\xAA\xAB\xAC\xAD\xAE\xAF\xB0\xB1\xB2\xB3\xB4\xB5\xB6\xB7\xB8\xB9\xBA\xBB\xBC\xBD\xBE\xBF\xC0\xC1\xC2\xC3\xC4\xC5\xC6\xC7\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1\xD2\xD3\xD4\xD5\xD6\xD7\xD8\xD9\xDA\xDB\xDC\xDD\xDE\xDF\xE0\xE1\xE2\xE3\xE4\xE5\xE6\xE7\xE8\xE9\xEA\xEB\xEC\xED\xEE\xEF\xF0\xF1\xF2\xF3\xF4\xF5\xF6\xF7\xF8\xF9\xFA\xFB\xFC\xFD\xFE\xFF]','one of the following characters: null character(NUL), start of header character (SOH), start of text character(STX), end of text character(ETX), end of transmission character(EOT), enquiry character(ENQ), acknowledgment character(ACK), bell character(BEL), backspace character(BS), tabulation(HT), line feed(LF), vertical tabulation(VT), form feed(FF), carriage return character(CR), shift out character (SO), shift in character (SI), data link escape character (DLE), device control 1 (oft. XON) character (DC1), device control 2 character (DC2), device control 3 (oft. XOFF) character (DC3), device control 4 character (DC4), negative acknowledgement character (NAK), synchronous idle character (SYN), end of transmission block character (ETB), cancel character (CAN), end of medium character (EM), substitute character (SUB), escape(ESC), file separator character (FS), group separator character (GS), record separator character (RS), unit separator character (US), space, <span style="color:blue">!</span>, <span style="color:blue">"</span>, <span style="color:blue">#</span>, <span style="color:blue">$</span>, <span style="color:blue">%</span>, <span style="color:blue">&</span>, <span style="color:blue">\'</span>, <span style="color:blue">(</span>, <span style="color:blue">)</span>, <span style="color:blue">*</span>, <span style="color:blue">+</span>, <span style="color:blue">,</span>, <span style="color:blue">-</span>, <span style="color:blue">.</span>, <span style="color:blue">/</span>, <span style="color:blue">0</span>, <span style="color:blue">1</span>, <span style="color:blue">2</span>, <span style="color:blue">3</span>, <span style="color:blue">4</span>, <span style="color:blue">5</span>, <span style="color:blue">6</span>, <span style="color:blue">7</span>, <span style="color:blue">8</span>, <span style="color:blue">9</span>, <span style="color:blue">:</span>, <span style="color:blue">;</span>, <span style="color:blue"><</span>, <span style="color:blue">=</span>, <span style="color:blue">></span>, <span style="color:blue">?</span>, <span style="color:blue">@</span>, <span style="color:blue">A</span>, <span style="color:blue">B</span>, <span style="color:blue">C</span>, <span style="color:blue">D</span>, <span style="color:blue">E</span>, <span style="color:blue">F</span>, <span style="color:blue">G</span>, <span style="color:blue">H</span>, <span style="color:blue">I</span>, <span style="color:blue">J</span>, <span style="color:blue">K</span>, <span style="color:blue">L</span>, <span style="color:blue">M</span>, <span style="color:blue">N</span>, <span style="color:blue">O</span>, <span style="color:blue">P</span>, <span style="color:blue">Q</span>, <span style="color:blue">R</span>, <span style="color:blue">S</span>, <span style="color:blue">T</span>, <span style="color:blue">U</span>, <span style="color:blue">V</span>, <span style="color:blue">W</span>, <span style="color:blue">X</span>, <span style="color:blue">Y</span>, <span style="color:blue">Z</span>, <span style="color:blue">[</span>, <span style="color:blue">\</span>, <span style="color:blue">]</span>, <span style="color:blue">^</span>, <span style="color:blue">_</span>, <span style="color:blue">`</span>, <span style="color:blue">a</span>, <span style="color:blue">b</span>, <span style="color:blue">c</span>, <span style="color:blue">d</span>, <span style="color:blue">e</span>, <span style="color:blue">f</span>, <span style="color:blue">g</span>, <span style="color:blue">h</span>, <span style="color:blue">i</span>, <span style="color:blue">j</span>, <span style="color:blue">k</span>, <span style="color:blue">l</span>, <span style="color:blue">m</span>, <span style="color:blue">n</span>, <span style="color:blue">o</span>, <span style="color:blue">p</span>, <span style="color:blue">q</span>, <span style="color:blue">r</span>, <span style="color:blue">s</span>, <span style="color:blue">t</span>, <span style="color:blue">u</span>, <span style="color:blue">v</span>, <span style="color:blue">w</span>, <span style="color:blue">x</span>, <span style="color:blue">y</span>, <span style="color:blue">z</span>, <span style="color:blue">{</span>, <span style="color:blue">|</span>, <span style="color:blue">}</span>, <span style="color:blue">~</span>, delete character (DEL), character with code 0x80, character with code 0x81, character with code 0x82, character with code 0x83, character with code 0x84, character with code 0x85, character with code 0x86, character with code 0x87, character with code 0x88, character with code 0x89, character with code 0x8A, character with code 0x8B, character with code 0x8C, character with code 0x8D, character with code 0x8E, character with code 0x8F, character with code 0x90, character with code 0x91, character with code 0x92, character with code 0x93, character with code 0x94, character with code 0x95, character with code 0x96, character with code 0x97, character with code 0x98, character with code 0x99, character with code 0x9A, character with code 0x9B, character with code 0x9C, character with code 0x9D, character with code 0x9E, character with code 0x9F, non-breaking space, <span style="color:blue">¡</span>, <span style="color:blue">¢</span>, <span style="color:blue">£</span>, <span style="color:blue">¤</span>, <span style="color:blue">¥</span>, <span style="color:blue">¦</span>, <span style="color:blue">§</span>, <span style="color:blue">¨</span>, <span style="color:blue">©</span>, <span style="color:blue">ª</span>, <span style="color:blue">«</span>, <span style="color:blue">¬</span>, soft hyphen character, <span style="color:blue">®</span>, <span style="color:blue">¯</span>, <span style="color:blue">°</span>, <span style="color:blue">±</span>, <span style="color:blue">²</span>, <span style="color:blue">³</span>, <span style="color:blue">´</span>, <span style="color:blue">µ</span>, <span style="color:blue">¶</span>, <span style="color:blue">·</span>, <span style="color:blue">¸</span>, <span style="color:blue">¹</span>, <span style="color:blue">º</span>, <span style="color:blue">»</span>, <span style="color:blue">¼</span>, <span style="color:blue">½</span>, <span style="color:blue">¾</span>, <span style="color:blue">¿</span>, <span style="color:blue">À</span>, <span style="color:blue">Á</span>, <span style="color:blue">Â</span>, <span style="color:blue">Ã</span>, <span style="color:blue">Ä</span>, <span style="color:blue">Å</span>, <span style="color:blue">Æ</span>, <span style="color:blue">Ç</span>, <span style="color:blue">È</span>, <span style="color:blue">É</span>, <span style="color:blue">Ê</span>, <span style="color:blue">Ë</span>, <span style="color:blue">Ì</span>, <span style="color:blue">Í</span>, <span style="color:blue">Î</span>, <span style="color:blue">Ï</span>, <span style="color:blue">Ð</span>, <span style="color:blue">Ñ</span>, <span style="color:blue">Ò</span>, <span style="color:blue">Ó</span>, <span style="color:blue">Ô</span>, <span style="color:blue">Õ</span>, <span style="color:blue">Ö</span>, <span style="color:blue">×</span>, <span style="color:blue">Ø</span>, <span style="color:blue">Ù</span>, <span style="color:blue">Ú</span>, <span style="color:blue">Û</span>, <span style="color:blue">Ü</span>, <span style="color:blue">Ý</span>, <span style="color:blue">Þ</span>, <span style="color:blue">ß</span>, <span style="color:blue">à</span>, <span style="color:blue">á</span>, <span style="color:blue">â</span>, <span style="color:blue">ã</span>, <span style="color:blue">ä</span>, <span style="color:blue">å</span>, <span style="color:blue">æ</span>, <span style="color:blue">ç</span>, <span style="color:blue">è</span>, <span style="color:blue">é</span>, <span style="color:blue">ê</span>, <span style="color:blue">ë</span>, <span style="color:blue">ì</span>, <span style="color:blue">í</span>, <span style="color:blue">î</span>, <span style="color:blue">ï</span>, <span style="color:blue">ð</span>, <span style="color:blue">ñ</span>, <span style="color:blue">ò</span>, <span style="color:blue">ó</span>, <span style="color:blue">ô</span>, <span style="color:blue">õ</span>, <span style="color:blue">ö</span>, <span style="color:blue">÷</span>, <span style="color:blue">ø</span>, <span style="color:blue">ù</span>, <span style="color:blue">ú</span>, <span style="color:blue">û</span>, <span style="color:blue">ü</span>, <span style="color:blue">ý</span>, <span style="color:blue">þ</span>, <span style="color:blue">ÿ</span>;'),
          //array('[\x{ff}-\x{1ff}]','one of the following characters: <span style="color:blue">ÿ</span>, <span style="color:blue">Ā</span>, <span style="color:blue">ā</span>, <span style="color:blue">Ă</span>, <span style="color:blue">ă</span>, <span style="color:blue">Ą</span>, <span style="color:blue">ą</span>, <span style="color:blue">Ć</span>, <span style="color:blue">ć</span>, <span style="color:blue">Ĉ</span>, <span style="color:blue">ĉ</span>, <span style="color:blue">Ċ</span>, <span style="color:blue">ċ</span>, <span style="color:blue">Č</span>, <span style="color:blue">č</span>, <span style="color:blue">Ď</span>, <span style="color:blue">ď</span>, <span style="color:blue">Đ</span>, <span style="color:blue">đ</span>, <span style="color:blue">Ē</span>, <span style="color:blue">ē</span>, <span style="color:blue">Ĕ</span>, <span style="color:blue">ĕ</span>, <span style="color:blue">Ė</span>, <span style="color:blue">ė</span>, <span style="color:blue">Ę</span>, <span style="color:blue">ę</span>, <span style="color:blue">Ě</span>, <span style="color:blue">ě</span>, <span style="color:blue">Ĝ</span>, <span style="color:blue">ĝ</span>, <span style="color:blue">Ğ</span>, <span style="color:blue">ğ</span>, <span style="color:blue">Ġ</span>, <span style="color:blue">ġ</span>, <span style="color:blue">Ģ</span>, <span style="color:blue">ģ</span>, <span style="color:blue">Ĥ</span>, <span style="color:blue">ĥ</span>, <span style="color:blue">Ħ</span>, <span style="color:blue">ħ</span>, <span style="color:blue">Ĩ</span>, <span style="color:blue">ĩ</span>, <span style="color:blue">Ī</span>, <span style="color:blue">ī</span>, <span style="color:blue">Ĭ</span>, <span style="color:blue">ĭ</span>, <span style="color:blue">Į</span>, <span style="color:blue">į</span>, <span style="color:blue">İ</span>, <span style="color:blue">ı</span>, <span style="color:blue">Ĳ</span>, <span style="color:blue">ĳ</span>, <span style="color:blue">Ĵ</span>, <span style="color:blue">ĵ</span>, <span style="color:blue">Ķ</span>, <span style="color:blue">ķ</span>, <span style="color:blue">ĸ</span>, <span style="color:blue">Ĺ</span>, <span style="color:blue">ĺ</span>, <span style="color:blue">Ļ</span>, <span style="color:blue">ļ</span>, <span style="color:blue">Ľ</span>, <span style="color:blue">ľ</span>, <span style="color:blue">Ŀ</span>, <span style="color:blue">ŀ</span>, <span style="color:blue">Ł</span>, <span style="color:blue">ł</span>, <span style="color:blue">Ń</span>, <span style="color:blue">ń</span>, <span style="color:blue">Ņ</span>, <span style="color:blue">ņ</span>, <span style="color:blue">Ň</span>, <span style="color:blue">ň</span>, <span style="color:blue">ŉ</span>, <span style="color:blue">Ŋ</span>, <span style="color:blue">ŋ</span>, <span style="color:blue">Ō</span>, <span style="color:blue">ō</span>, <span style="color:blue">Ŏ</span>, <span style="color:blue">ŏ</span>, <span style="color:blue">Ő</span>, <span style="color:blue">ő</span>, <span style="color:blue">Œ</span>, <span style="color:blue">œ</span>, <span style="color:blue">Ŕ</span>, <span style="color:blue">ŕ</span>, <span style="color:blue">Ŗ</span>, <span style="color:blue">ŗ</span>, <span style="color:blue">Ř</span>, <span style="color:blue">ř</span>, <span style="color:blue">Ś</span>, <span style="color:blue">ś</span>, <span style="color:blue">Ŝ</span>, <span style="color:blue">ŝ</span>, <span style="color:blue">Ş</span>, <span style="color:blue">ş</span>, <span style="color:blue">Š</span>, <span style="color:blue">š</span>, <span style="color:blue">Ţ</span>, <span style="color:blue">ţ</span>, <span style="color:blue">Ť</span>, <span style="color:blue">ť</span>, <span style="color:blue">Ŧ</span>, <span style="color:blue">ŧ</span>, <span style="color:blue">Ũ</span>, <span style="color:blue">ũ</span>, <span style="color:blue">Ū</span>, <span style="color:blue">ū</span>, <span style="color:blue">Ŭ</span>, <span style="color:blue">ŭ</span>, <span style="color:blue">Ů</span>, <span style="color:blue">ů</span>, <span style="color:blue">Ű</span>, <span style="color:blue">ű</span>, <span style="color:blue">Ų</span>, <span style="color:blue">ų</span>, <span style="color:blue">Ŵ</span>, <span style="color:blue">ŵ</span>, <span style="color:blue">Ŷ</span>, <span style="color:blue">ŷ</span>, <span style="color:blue">Ÿ</span>, <span style="color:blue">Ź</span>, <span style="color:blue">ź</span>, <span style="color:blue">Ż</span>, <span style="color:blue">ż</span>, <span style="color:blue">Ž</span>, <span style="color:blue">ž</span>, <span style="color:blue">ſ</span>, <span style="color:blue">ƀ</span>, <span style="color:blue">Ɓ</span>, <span style="color:blue">Ƃ</span>, <span style="color:blue">ƃ</span>, <span style="color:blue">Ƅ</span>, <span style="color:blue">ƅ</span>, <span style="color:blue">Ɔ</span>, <span style="color:blue">Ƈ</span>, <span style="color:blue">ƈ</span>, <span style="color:blue">Ɖ</span>, <span style="color:blue">Ɗ</span>, <span style="color:blue">Ƌ</span>, <span style="color:blue">ƌ</span>, <span style="color:blue">ƍ</span>, <span style="color:blue">Ǝ</span>, <span style="color:blue">Ə</span>, <span style="color:blue">Ɛ</span>, <span style="color:blue">Ƒ</span>, <span style="color:blue">ƒ</span>, <span style="color:blue">Ɠ</span>, <span style="color:blue">Ɣ</span>, <span style="color:blue">ƕ</span>, <span style="color:blue">Ɩ</span>, <span style="color:blue">Ɨ</span>, <span style="color:blue">Ƙ</span>, <span style="color:blue">ƙ</span>, <span style="color:blue">ƚ</span>, <span style="color:blue">ƛ</span>, <span style="color:blue">Ɯ</span>, <span style="color:blue">Ɲ</span>, <span style="color:blue">ƞ</span>, <span style="color:blue">Ɵ</span>, <span style="color:blue">Ơ</span>, <span style="color:blue">ơ</span>, <span style="color:blue">Ƣ</span>, <span style="color:blue">ƣ</span>, <span style="color:blue">Ƥ</span>, <span style="color:blue">ƥ</span>, <span style="color:blue">Ʀ</span>, <span style="color:blue">Ƨ</span>, <span style="color:blue">ƨ</span>, <span style="color:blue">Ʃ</span>, <span style="color:blue">ƪ</span>, <span style="color:blue">ƫ</span>, <span style="color:blue">Ƭ</span>, <span style="color:blue">ƭ</span>, <span style="color:blue">Ʈ</span>, <span style="color:blue">Ư</span>, <span style="color:blue">ư</span>, <span style="color:blue">Ʊ</span>, <span style="color:blue">Ʋ</span>, <span style="color:blue">Ƴ</span>, <span style="color:blue">ƴ</span>, <span style="color:blue">Ƶ</span>, <span style="color:blue">ƶ</span>, <span style="color:blue">Ʒ</span>, <span style="color:blue">Ƹ</span>, <span style="color:blue">ƹ</span>, <span style="color:blue">ƺ</span>, <span style="color:blue">ƻ</span>, <span style="color:blue">Ƽ</span>, <span style="color:blue">ƽ</span>, <span style="color:blue">ƾ</span>, <span style="color:blue">ƿ</span>, <span style="color:blue">ǀ</span>, <span style="color:blue">ǁ</span>, <span style="color:blue">ǂ</span>, <span style="color:blue">ǃ</span>, <span style="color:blue">Ǆ</span>, <span style="color:blue">ǅ</span>, <span style="color:blue">ǆ</span>, <span style="color:blue">Ǉ</span>, <span style="color:blue">ǈ</span>, <span style="color:blue">ǉ</span>, <span style="color:blue">Ǌ</span>, <span style="color:blue">ǋ</span>, <span style="color:blue">ǌ</span>, <span style="color:blue">Ǎ</span>, <span style="color:blue">ǎ</span>, <span style="color:blue">Ǐ</span>, <span style="color:blue">ǐ</span>, <span style="color:blue">Ǒ</span>, <span style="color:blue">ǒ</span>, <span style="color:blue">Ǔ</span>, <span style="color:blue">ǔ</span>, <span style="color:blue">Ǖ</span>, <span style="color:blue">ǖ</span>, <span style="color:blue">Ǘ</span>, <span style="color:blue">ǘ</span>, <span style="color:blue">Ǚ</span>, <span style="color:blue">ǚ</span>, <span style="color:blue">Ǜ</span>, <span style="color:blue">ǜ</span>, <span style="color:blue">ǝ</span>, <span style="color:blue">Ǟ</span>, <span style="color:blue">ǟ</span>, <span style="color:blue">Ǡ</span>, <span style="color:blue">ǡ</span>, <span style="color:blue">Ǣ</span>, <span style="color:blue">ǣ</span>, <span style="color:blue">Ǥ</span>, <span style="color:blue">ǥ</span>, <span style="color:blue">Ǧ</span>, <span style="color:blue">ǧ</span>, <span style="color:blue">Ǩ</span>, <span style="color:blue">ǩ</span>, <span style="color:blue">Ǫ</span>, <span style="color:blue">ǫ</span>, <span style="color:blue">Ǭ</span>, <span style="color:blue">ǭ</span>, <span style="color:blue">Ǯ</span>, <span style="color:blue">ǯ</span>, <span style="color:blue">ǰ</span>, <span style="color:blue">Ǳ</span>, <span style="color:blue">ǲ</span>, <span style="color:blue">ǳ</span>, <span style="color:blue">Ǵ</span>, <span style="color:blue">ǵ</span>, <span style="color:blue">Ƕ</span>, <span style="color:blue">Ƿ</span>, <span style="color:blue">Ǹ</span>, <span style="color:blue">ǹ</span>, <span style="color:blue">Ǻ</span>, <span style="color:blue">ǻ</span>, <span style="color:blue">Ǽ</span>, <span style="color:blue">ǽ</span>, <span style="color:blue">Ǿ</span>, <span style="color:blue">ǿ</span>;'),
          array('[\x{FF}\x{100}\x{101}\x{102}\x{103}\x{104}\x{105}\x{106}\x{107}\x{108}\x{109}\x{10A}\x{10B}\x{10C}\x{10D}\x{10E}\x{10F}\x{110}\x{111}\x{112}\x{113}\x{114}\x{115}\x{116}\x{117}\x{118}\x{119}\x{11A}\x{11B}\x{11C}\x{11D}\x{11E}\x{11F}\x{120}\x{121}\x{122}\x{123}\x{124}\x{125}\x{126}\x{127}\x{128}\x{129}\x{12A}\x{12B}\x{12C}\x{12D}\x{12E}\x{12F}\x{130}\x{131}\x{132}\x{133}\x{134}\x{135}\x{136}\x{137}\x{138}\x{139}\x{13A}\x{13B}\x{13C}\x{13D}\x{13E}\x{13F}\x{140}\x{141}\x{142}\x{143}\x{144}\x{145}\x{146}\x{147}\x{148}\x{149}\x{14A}\x{14B}\x{14C}\x{14D}\x{14E}\x{14F}\x{150}\x{151}\x{152}\x{153}\x{154}\x{155}\x{156}\x{157}\x{158}\x{159}\x{15A}\x{15B}\x{15C}\x{15D}\x{15E}\x{15F}\x{160}\x{161}\x{162}\x{163}\x{164}\x{165}\x{166}\x{167}\x{168}\x{169}\x{16A}\x{16B}\x{16C}\x{16D}\x{16E}\x{16F}\x{170}\x{171}\x{172}\x{173}\x{174}\x{175}\x{176}\x{177}\x{178}\x{179}\x{17A}\x{17B}\x{17C}\x{17D}\x{17E}\x{17F}\x{180}\x{181}\x{182}\x{183}\x{184}\x{185}\x{186}\x{187}\x{188}\x{189}\x{18A}\x{18B}\x{18C}\x{18D}\x{18E}\x{18F}\x{190}\x{191}\x{192}\x{193}\x{194}\x{195}\x{196}\x{197}\x{198}\x{199}\x{19A}\x{19B}\x{19C}\x{19D}\x{19E}\x{19F}\x{1A0}\x{1A1}\x{1A2}\x{1A3}\x{1A4}\x{1A5}\x{1A6}\x{1A7}\x{1A8}\x{1A9}\x{1AA}\x{1AB}\x{1AC}\x{1AD}\x{1AE}\x{1AF}\x{1B0}\x{1B1}\x{1B2}\x{1B3}\x{1B4}\x{1B5}\x{1B6}\x{1B7}\x{1B8}\x{1B9}\x{1BA}\x{1BB}\x{1BC}\x{1BD}\x{1BE}\x{1BF}\x{1C0}\x{1C1}\x{1C2}\x{1C3}\x{1C4}\x{1C5}\x{1C6}\x{1C7}\x{1C8}\x{1C9}\x{1CA}\x{1CB}\x{1CC}\x{1CD}\x{1CE}\x{1CF}\x{1D0}\x{1D1}\x{1D2}\x{1D3}\x{1D4}\x{1D5}\x{1D6}\x{1D7}\x{1D8}\x{1D9}\x{1DA}\x{1DB}\x{1DC}\x{1DD}\x{1DE}\x{1DF}\x{1E0}\x{1E1}\x{1E2}\x{1E3}\x{1E4}\x{1E5}\x{1E6}\x{1E7}\x{1E8}\x{1E9}\x{1EA}\x{1EB}\x{1EC}\x{1ED}\x{1EE}\x{1EF}\x{1F0}\x{1F1}\x{1F2}\x{1F3}\x{1F4}\x{1F5}\x{1F6}\x{1F7}\x{1F8}\x{1F9}\x{1FA}\x{1FB}\x{1FC}\x{1FD}\x{1FE}\x{1FF}]','one of the following characters: <span style="color:blue">ÿ</span>, <span style="color:blue">Ā</span>, <span style="color:blue">ā</span>, <span style="color:blue">Ă</span>, <span style="color:blue">ă</span>, <span style="color:blue">Ą</span>, <span style="color:blue">ą</span>, <span style="color:blue">Ć</span>, <span style="color:blue">ć</span>, <span style="color:blue">Ĉ</span>, <span style="color:blue">ĉ</span>, <span style="color:blue">Ċ</span>, <span style="color:blue">ċ</span>, <span style="color:blue">Č</span>, <span style="color:blue">č</span>, <span style="color:blue">Ď</span>, <span style="color:blue">ď</span>, <span style="color:blue">Đ</span>, <span style="color:blue">đ</span>, <span style="color:blue">Ē</span>, <span style="color:blue">ē</span>, <span style="color:blue">Ĕ</span>, <span style="color:blue">ĕ</span>, <span style="color:blue">Ė</span>, <span style="color:blue">ė</span>, <span style="color:blue">Ę</span>, <span style="color:blue">ę</span>, <span style="color:blue">Ě</span>, <span style="color:blue">ě</span>, <span style="color:blue">Ĝ</span>, <span style="color:blue">ĝ</span>, <span style="color:blue">Ğ</span>, <span style="color:blue">ğ</span>, <span style="color:blue">Ġ</span>, <span style="color:blue">ġ</span>, <span style="color:blue">Ģ</span>, <span style="color:blue">ģ</span>, <span style="color:blue">Ĥ</span>, <span style="color:blue">ĥ</span>, <span style="color:blue">Ħ</span>, <span style="color:blue">ħ</span>, <span style="color:blue">Ĩ</span>, <span style="color:blue">ĩ</span>, <span style="color:blue">Ī</span>, <span style="color:blue">ī</span>, <span style="color:blue">Ĭ</span>, <span style="color:blue">ĭ</span>, <span style="color:blue">Į</span>, <span style="color:blue">į</span>, <span style="color:blue">İ</span>, <span style="color:blue">ı</span>, <span style="color:blue">Ĳ</span>, <span style="color:blue">ĳ</span>, <span style="color:blue">Ĵ</span>, <span style="color:blue">ĵ</span>, <span style="color:blue">Ķ</span>, <span style="color:blue">ķ</span>, <span style="color:blue">ĸ</span>, <span style="color:blue">Ĺ</span>, <span style="color:blue">ĺ</span>, <span style="color:blue">Ļ</span>, <span style="color:blue">ļ</span>, <span style="color:blue">Ľ</span>, <span style="color:blue">ľ</span>, <span style="color:blue">Ŀ</span>, <span style="color:blue">ŀ</span>, <span style="color:blue">Ł</span>, <span style="color:blue">ł</span>, <span style="color:blue">Ń</span>, <span style="color:blue">ń</span>, <span style="color:blue">Ņ</span>, <span style="color:blue">ņ</span>, <span style="color:blue">Ň</span>, <span style="color:blue">ň</span>, <span style="color:blue">ŉ</span>, <span style="color:blue">Ŋ</span>, <span style="color:blue">ŋ</span>, <span style="color:blue">Ō</span>, <span style="color:blue">ō</span>, <span style="color:blue">Ŏ</span>, <span style="color:blue">ŏ</span>, <span style="color:blue">Ő</span>, <span style="color:blue">ő</span>, <span style="color:blue">Œ</span>, <span style="color:blue">œ</span>, <span style="color:blue">Ŕ</span>, <span style="color:blue">ŕ</span>, <span style="color:blue">Ŗ</span>, <span style="color:blue">ŗ</span>, <span style="color:blue">Ř</span>, <span style="color:blue">ř</span>, <span style="color:blue">Ś</span>, <span style="color:blue">ś</span>, <span style="color:blue">Ŝ</span>, <span style="color:blue">ŝ</span>, <span style="color:blue">Ş</span>, <span style="color:blue">ş</span>, <span style="color:blue">Š</span>, <span style="color:blue">š</span>, <span style="color:blue">Ţ</span>, <span style="color:blue">ţ</span>, <span style="color:blue">Ť</span>, <span style="color:blue">ť</span>, <span style="color:blue">Ŧ</span>, <span style="color:blue">ŧ</span>, <span style="color:blue">Ũ</span>, <span style="color:blue">ũ</span>, <span style="color:blue">Ū</span>, <span style="color:blue">ū</span>, <span style="color:blue">Ŭ</span>, <span style="color:blue">ŭ</span>, <span style="color:blue">Ů</span>, <span style="color:blue">ů</span>, <span style="color:blue">Ű</span>, <span style="color:blue">ű</span>, <span style="color:blue">Ų</span>, <span style="color:blue">ų</span>, <span style="color:blue">Ŵ</span>, <span style="color:blue">ŵ</span>, <span style="color:blue">Ŷ</span>, <span style="color:blue">ŷ</span>, <span style="color:blue">Ÿ</span>, <span style="color:blue">Ź</span>, <span style="color:blue">ź</span>, <span style="color:blue">Ż</span>, <span style="color:blue">ż</span>, <span style="color:blue">Ž</span>, <span style="color:blue">ž</span>, <span style="color:blue">ſ</span>, <span style="color:blue">ƀ</span>, <span style="color:blue">Ɓ</span>, <span style="color:blue">Ƃ</span>, <span style="color:blue">ƃ</span>, <span style="color:blue">Ƅ</span>, <span style="color:blue">ƅ</span>, <span style="color:blue">Ɔ</span>, <span style="color:blue">Ƈ</span>, <span style="color:blue">ƈ</span>, <span style="color:blue">Ɖ</span>, <span style="color:blue">Ɗ</span>, <span style="color:blue">Ƌ</span>, <span style="color:blue">ƌ</span>, <span style="color:blue">ƍ</span>, <span style="color:blue">Ǝ</span>, <span style="color:blue">Ə</span>, <span style="color:blue">Ɛ</span>, <span style="color:blue">Ƒ</span>, <span style="color:blue">ƒ</span>, <span style="color:blue">Ɠ</span>, <span style="color:blue">Ɣ</span>, <span style="color:blue">ƕ</span>, <span style="color:blue">Ɩ</span>, <span style="color:blue">Ɨ</span>, <span style="color:blue">Ƙ</span>, <span style="color:blue">ƙ</span>, <span style="color:blue">ƚ</span>, <span style="color:blue">ƛ</span>, <span style="color:blue">Ɯ</span>, <span style="color:blue">Ɲ</span>, <span style="color:blue">ƞ</span>, <span style="color:blue">Ɵ</span>, <span style="color:blue">Ơ</span>, <span style="color:blue">ơ</span>, <span style="color:blue">Ƣ</span>, <span style="color:blue">ƣ</span>, <span style="color:blue">Ƥ</span>, <span style="color:blue">ƥ</span>, <span style="color:blue">Ʀ</span>, <span style="color:blue">Ƨ</span>, <span style="color:blue">ƨ</span>, <span style="color:blue">Ʃ</span>, <span style="color:blue">ƪ</span>, <span style="color:blue">ƫ</span>, <span style="color:blue">Ƭ</span>, <span style="color:blue">ƭ</span>, <span style="color:blue">Ʈ</span>, <span style="color:blue">Ư</span>, <span style="color:blue">ư</span>, <span style="color:blue">Ʊ</span>, <span style="color:blue">Ʋ</span>, <span style="color:blue">Ƴ</span>, <span style="color:blue">ƴ</span>, <span style="color:blue">Ƶ</span>, <span style="color:blue">ƶ</span>, <span style="color:blue">Ʒ</span>, <span style="color:blue">Ƹ</span>, <span style="color:blue">ƹ</span>, <span style="color:blue">ƺ</span>, <span style="color:blue">ƻ</span>, <span style="color:blue">Ƽ</span>, <span style="color:blue">ƽ</span>, <span style="color:blue">ƾ</span>, <span style="color:blue">ƿ</span>, <span style="color:blue">ǀ</span>, <span style="color:blue">ǁ</span>, <span style="color:blue">ǂ</span>, <span style="color:blue">ǃ</span>, <span style="color:blue">Ǆ</span>, <span style="color:blue">ǅ</span>, <span style="color:blue">ǆ</span>, <span style="color:blue">Ǉ</span>, <span style="color:blue">ǈ</span>, <span style="color:blue">ǉ</span>, <span style="color:blue">Ǌ</span>, <span style="color:blue">ǋ</span>, <span style="color:blue">ǌ</span>, <span style="color:blue">Ǎ</span>, <span style="color:blue">ǎ</span>, <span style="color:blue">Ǐ</span>, <span style="color:blue">ǐ</span>, <span style="color:blue">Ǒ</span>, <span style="color:blue">ǒ</span>, <span style="color:blue">Ǔ</span>, <span style="color:blue">ǔ</span>, <span style="color:blue">Ǖ</span>, <span style="color:blue">ǖ</span>, <span style="color:blue">Ǘ</span>, <span style="color:blue">ǘ</span>, <span style="color:blue">Ǚ</span>, <span style="color:blue">ǚ</span>, <span style="color:blue">Ǜ</span>, <span style="color:blue">ǜ</span>, <span style="color:blue">ǝ</span>, <span style="color:blue">Ǟ</span>, <span style="color:blue">ǟ</span>, <span style="color:blue">Ǡ</span>, <span style="color:blue">ǡ</span>, <span style="color:blue">Ǣ</span>, <span style="color:blue">ǣ</span>, <span style="color:blue">Ǥ</span>, <span style="color:blue">ǥ</span>, <span style="color:blue">Ǧ</span>, <span style="color:blue">ǧ</span>, <span style="color:blue">Ǩ</span>, <span style="color:blue">ǩ</span>, <span style="color:blue">Ǫ</span>, <span style="color:blue">ǫ</span>, <span style="color:blue">Ǭ</span>, <span style="color:blue">ǭ</span>, <span style="color:blue">Ǯ</span>, <span style="color:blue">ǯ</span>, <span style="color:blue">ǰ</span>, <span style="color:blue">Ǳ</span>, <span style="color:blue">ǲ</span>, <span style="color:blue">ǳ</span>, <span style="color:blue">Ǵ</span>, <span style="color:blue">ǵ</span>, <span style="color:blue">Ƕ</span>, <span style="color:blue">Ƿ</span>, <span style="color:blue">Ǹ</span>, <span style="color:blue">ǹ</span>, <span style="color:blue">Ǻ</span>, <span style="color:blue">ǻ</span>, <span style="color:blue">Ǽ</span>, <span style="color:blue">ǽ</span>, <span style="color:blue">Ǿ</span>, <span style="color:blue">ǿ</span>;'),
        );
    }
    
    //------------------------------------------------------------------
    
    public function test_meta()
    {
        $handler = new qtype_preg_author_tool_description('a|b|',null,null);
        $result = $handler->description('%s','%s');
        $expected = '<span style="color:blue">a</span> or <span style="color:blue">b</span> or nothing';
        $this->assertEquals($result, $expected);
    }
    
    //------------------------------------------------------------------
 
    /**
     * @dataProvider assert_provider
     */
    public function test_assert($regex, $expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }
    public function assert_provider()
    {
        return array(
          array('^','beginning of the string'),
          array('$','end of the string'),
          array('\b','at a word boundary'),
          array('\B','not at a word boundary'),
          array('\A','at the start of the subject'),
          array('\Z','at the end of the subject'),
          array('\G','at the first matching position in the subject')
        );
    }
 
    //------------------------------------------------------------------
    
    public function test_backref()
    {
        $handler = new qtype_preg_author_tool_description('(a)\1',null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $expected = 'subpattern #1: [<span style="color:blue">a</span>] then back reference to subpattern #1';
        $this->assertEquals($result, $expected);
    }
    
    //------------------------------------------------------------------
    
    /**
     * @dataProvider concat_provider
     */
    public function test_concat($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }
    
    public function concat_provider()
    {
        return array(
          array('ab','<span style="color:blue">a</span><span style="color:blue">b</span>'),
          array('[a|b]c','one of the following characters: <span style="color:blue">a</span>, <span style="color:blue">|</span>, <span style="color:blue">b</span>; then <span style="color:blue">c</span>'),
          array('abc','<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>'),
          array(' \t\n\r','space then tabulation(HT) then line feed(LF) then carriage return character(CR)'),
          array('\0113','tabulation(HT) then <span style="color:blue">3</span>'),
        );
    }
    
    //------------------------------------------------------------------
    
    /**
     * @dataProvider alt_provider
     */
    public function test_alt($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }
    
    public function alt_provider()
    {
        return array(
          array('a|b','<span style="color:blue">a</span> or <span style="color:blue">b</span>'),
          array('a|b|','<span style="color:blue">a</span> or <span style="color:blue">b</span> or nothing'),
          array('a|b|c','<span style="color:blue">a</span> or <span style="color:blue">b</span> or <span style="color:blue">c</span>'),
        );
    }
    
    //------------------------------------------------------------------
    
    /**
     * @dataProvider nassert_provider
     */
    public function test_nassert($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }
    
    public function nassert_provider()
    {
        return array(
          array('(?=abc)g','further text should match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] and <span style="color:blue">g</span>'),
          array('(?!abc)g','further text should not match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] and <span style="color:blue">g</span>'),
          array('(?<=abc)g','preceding text should match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] then <span style="color:blue">g</span>'),
          array('(?<!abc)g','preceding text should not match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] then <span style="color:blue">g</span>'),
          array('a(?=abc)g','<span style="color:blue">a</span> then further text should match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] and <span style="color:blue">g</span>'),
          array('a(?!abc)g','<span style="color:blue">a</span> then further text should not match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] and <span style="color:blue">g</span>'),
          array('a(?<=abc)g','<span style="color:blue">a</span> and preceding text should match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] then <span style="color:blue">g</span>'),
          array('a(?<!abc)g','<span style="color:blue">a</span> and preceding text should not match: [<span style="color:blue">a</span><span style="color:blue">b</span><span style="color:blue">c</span>] then <span style="color:blue">g</span>'),
        );
    }
    
    //------------------------------------------------------------------
    
    /**
     * @dataProvider quant_provider
     */
    public function test_quant($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }
    
    public function quant_provider()
    {
        return array(
          array('g{,1}','<span style="color:blue">g</span> may be missing'),
          array('g+','<span style="color:blue">g</span> is repeated any number of times'),
          array('g*','<span style="color:blue">g</span> is repeated any number of times or missing'),
          array('g?','<span style="color:blue">g</span> may be missing'),
          array('g{0,1}','<span style="color:blue">g</span> may be missing'),
          array('g{0,}','<span style="color:blue">g</span> is repeated any number of times or missing'),
          array('g{1,}','<span style="color:blue">g</span> is repeated any number of times'),
          array('g{2,5}','<span style="color:blue">g</span> is repeated from 2 to 5 times'),
        );
    }
    
    //------------------------------------------------------------------    
    
    /**
     * @dataProvider option_provider
     */
    public function test_option($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }
    
    public function option_provider()
    {
        return array(
          array('(a(?i)b)c','subpattern #1: [<span style="color:blue">a</span>caseless: <span style="color:blue">b</span>] then case sensitive: <span style="color:blue">c</span>'),
          //array('(?i)a|b[a]c','subpattern #1: [<span style="color:blue">a</span>caseless: <span style="color:blue">b</span>] then case sensitive: <span style="color:blue">c</span>'),
        );
    }

    //------------------------------------------------------------------
    
    public function test_numbering()
    {
        $handler = new qtype_preg_author_tool_description('([a|b]|)\W+',null,null);
        //var_dump($handler);
        $result = $handler->default_description();
        $expected = '<span class="description_node_6"><span class="description_node_3">subpattern #1: [<span class="description_node_2"><span class="description_node_0">one of the following characters: <span style="color:blue">a</span>, <span style="color:blue">|</span>, <span style="color:blue">b</span>;</span> or <span class="description_node_1">nothing</span></span>]</span> then <span class="description_node_5"><span class="description_node_4">not word character</span> is repeated any number of times</span></span>';
        $this->assertEquals($result, $expected);
    }
    
    //------------------------------------------------------------------
    
    /**
     * @dataProvider condmask_provider
     */
    public function test_condmask($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }
    
    public function condmask_provider()
    {
        return array(
          array('(?(?=a)a|b)','if further text should match: [<span style="color:blue">a</span>] then check: [<span style="color:blue">a</span>] else check: [<span style="color:blue">b</span>]'),
          array('(?(?!a)a|b)','if further text should not match: [<span style="color:blue">a</span>] then check: [<span style="color:blue">a</span>] else check: [<span style="color:blue">b</span>]'),
          array('(?(?<=a)a|b)','if preceding text should match: [<span style="color:blue">a</span>] then check: [<span style="color:blue">a</span>] else check: [<span style="color:blue">b</span>]'),
          array('(?(?<!a)a|b)','if preceding text should not match: [<span style="color:blue">a</span>] then check: [<span style="color:blue">a</span>] else check: [<span style="color:blue">b</span>]'),
          array('(?(?=a)a)','if further text should match: [<span style="color:blue">a</span>] then check: [<span style="color:blue">a</span>]'),
          array('(?(1)a)','if the subpattern #1 has been successfully matched then check: [<span style="color:blue">a</span>]'),
          array('(?(name)a)','if the subpattern "name" has been successfully matched then check: [<span style="color:blue">a</span>]'),
          array('(?(<name>)a)','if the subpattern "name" has been successfully matched then check: [<span style="color:blue">a</span>]'),
          array('(?(<name>)a|b)','if the subpattern "name" has been successfully matched then check: [<span style="color:blue">a</span>] else check: [<span style="color:blue">b</span>]'),
          array('(?(DEFINE)(?<name>a))','definition of subpattern #1: [<span style="color:blue">a</span>]'),
        );
    }
    
    //------------------------------------------------------------------
    
    /**
     * @dataProvider postprocessing_provider
     */
    public function test_postprocessing($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }
    
    public function postprocessing_provider()
    {
        return array(
          array('([abc])','subpattern #1: [one of the following characters: <span style="color:blue">a</span>, <span style="color:blue">b</span>, <span style="color:blue">c</span>]'),
          array('[^\S]','white space'),
        );
    }
    
    //------------------------------------------------------------------
    
    /**
     * @dataProvider err_provider
     */
    public function test_err($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        //var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }
    
    public function err_provider()
    {
        return array(
          array('a{9,0}','<span style="color:blue">a</span><span style="color:red"> is repeated from 9 to 0 times (incorrect quantifier borders)</span>'),
          array('(a','<span style="color:red">Regex syntax error: missing a closing parenthesis \')\' for the opening parenthesis in position 0.</span> Operands: <span style="color:blue">a</span>'),
        );
    }
    
}

class qtype_preg_description_form_test extends PHPUnit_Framework_TestCase {    
    /**
     * @dataProvider form_provider
     */
    public function test_form($regex,$expected)
    {
        $handler = new qtype_preg_author_tool_description($regex,null,null);
        $result = $handler->form_description('g');
        $this->assertEquals($result, $expected);
    }
    
    public function form_provider()
    {
        return array(
          array('a','<span style="color:blue">a</span>(form g)'),
          array('\w','word character(form g)'),
          array('$','end of the string(form g)'),
          array('a|bc|','<span style="color:blue">a</span>(form g) or <span style="color:blue">b</span>(form g)<span style="color:blue">c</span>(form g) or nothing(form g)'),
        );
    }
}


/*class qtype_preg_description_dumping_test extends PHPUnit_Framework_TestCase {
    /**
     * @dataProvider vardump_provider
     */
    /*public function test_vardump($regex,$expected)
    {
        $options = new qtype_preg_handling_options();
        $options->preserveallnodes = true;
        //var_dump($options);
        $handler = new qtype_preg_author_tool_description($regex,null,$options);
        var_dump($handler);
        $result = $handler->description('%s','%s');
        $this->assertEquals($result, $expected);
    }
    
    public function vardump_provider()
    {
        return array(
          array('(?i)[a]','')
        );
    }
}*/

