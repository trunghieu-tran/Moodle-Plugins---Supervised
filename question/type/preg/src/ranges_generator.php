<?php

/**
 * Generator of ranges for unicode properties, POSIX classes and same things.
 *
 * @package    qtype_preg
 * @copyright  2013 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Returns a utf8 character by the given code.
function code2utf8($num) {
    if ($num < 128) {
        return chr($num);
    }
    if ($num < 2048) {
        return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
    }
    if ($num < 65536) {
        return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
    }
    if ($num < 2097152) {
        return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
    }
    return '';
}

$FILE_NAME_TMP = '/home/in.txt';
$FILE_NAME_OUT = '/home/out.txt';
$prefix = 'public static function ';
$suffix = '_ranges() {';
$tab1 = '    ';
$tab2 = '                     ';
$eol = code2utf8(0x000A);

$props = array( 'dot'    => '.',

                'slashd' => '\d',
                'slashh' => '\h',
                'slashs' => '\s',
                'slashv' => '\v',
                'slashw' => '\w',

                'alnum'  => '[[:alnum:]]',
                'alpha'  => '[[:alpha:]]',
                'ascii'  => '[[:ascii:]]',
                'blank'  => '[[:blank:]]',
                'cntrl'  => '[[:cntrl:]]',
                'digit'  => '[[:digit:]]',
                'graph'  => '[[:graph:]]',
                'lower'  => '[[:lower:]]',
                'print'  => '[[:print:]]',
                'punct'  => '[[:punct:]]',
                'space'  => '[[:space:]]',
                'upper'  => '[[:upper:]]',
                'word'   => '[[:word:]]',
                'xdigit' => '[[:xdigit:]]',

                'C'   => '\p{C}',
                'Cc'  => '\p{Cc}',
                'Cf'  => '\p{Cf}',
                'Cn'  => '\p{Cn}',
                'Co'  => '\p{Co}',
                'Cs'  => '\p{Cs}',
                'L'   => '\p{L}',
                'Ll'  => '\p{Ll}',
                'Lm'  => '\p{Lm}',
                'Lo'  => '\p{Lo}',
                'Lt'  => '\p{Lt}',
                'Lu'  => '\p{Lu}',
                'M'   => '\p{M}',
                'Mc'  => '\p{Mc}',
                'Me'  => '\p{Me}',
                'Mn'  => '\p{Mn}',
                'N'   => '\p{N}',
                'Nd'  => '\p{Nd}',
                'Nl'  => '\p{Nl}',
                'No'  => '\p{No}',
                'P'   => '\p{P}',
                'Pc'  => '\p{Pc}',
                'Pd'  => '\p{Pd}',
                'Pe'  => '\p{Pe}',
                'Pf'  => '\p{Pf}',
                'Pi'  => '\p{Pi}',
                'Po'  => '\p{Po}',
                'Ps'  => '\p{Ps}',
                'S'   => '\p{S}',
                'Sc'  => '\p{Sc}',
                'Sk'  => '\p{Sk}',
                'Sm'  => '\p{Sm}',
                'So'  => '\p{So}',
                'Z'   => '\p{Z}',
                'Zl'  => '\p{Zl}',
                'Zp'  => '\p{Zp}',
                'Zs'  => '\p{Zs}',
                'Xan' => '\p{Xan}',
                'Xps' => '\p{Xps}',
                'Xsp' => '\p{Xsp}',
                'Xwd' => '\p{Xwd}',
                'Arabic' => '\p{Arabic}',
                'Armenian' => '\p{Armenian}',
                'Avestan' => '\p{Avestan}',
                'Balinese' => '\p{Balinese}',
                'Bamum' => '\p{Bamum}',
                'Bengali' => '\p{Bengali}',
                'Bopomofo' => '\p{Bopomofo}',
                'Braille' => '\p{Braille}',
                'Buginese' => '\p{Buginese}',
                'Buhid' => '\p{Buhid}',
                'Canadian_Aboriginal' => '\p{Canadian_Aboriginal}',
                'Carian' => '\p{Carian}',
                'Cham' => '\p{Cham}',
                'Cherokee' => '\p{Cherokee}',
                'Common' => '\p{Common}',
                'Coptic' => '\p{Coptic}',
                'Cuneiform' => '\p{Cuneiform}',
                'Cypriot' => '\p{Cypriot}',
                'Cyrillic' => '\p{Cyrillic}',
                'Deseret' => '\p{Deseret}',
                'Devanagari' => '\p{Devanagari}',
                'Egyptian_Hieroglyphs' => '\p{Egyptian_Hieroglyphs}',
                'Ethiopic' => '\p{Ethiopic}',
                'Georgian' => '\p{Georgian}',
                'Glagolitic' => '\p{Glagolitic}',
                'Gothic' => '\p{Gothic}',
                'Greek' => '\p{Greek}',
                'Gujarati' => '\p{Gujarati}',
                'Gurmukhi' => '\p{Gurmukhi}',
                'Han' => '\p{Han}',
                'Hangul' => '\p{Hangul}',
                'Hanunoo' => '\p{Hanunoo}',
                'Hebrew' => '\p{Hebrew}',
                'Hiragana' => '\p{Hiragana}',
                'Imperial_Aramaic' => '\p{Imperial_Aramaic}',
                'Inherited' => '\p{Inherited}',
                'Inscriptional_Pahlavi' => '\p{Inscriptional_Pahlavi}',
                'Inscriptional_Parthian' => '\p{Inscriptional_Parthian}',
                'Javanese' => '\p{Javanese}',
                'Kaithi' => '\p{Kaithi}',
                'Kannada' => '\p{Kannada}',
                'Katakana' => '\p{Katakana}',
                'Kayah_Li' => '\p{Kayah_Li}',
                'Kharoshthi' => '\p{Kharoshthi}',
                'Khmer' => '\p{Khmer}',
                'Lao' => '\p{Lao}',
                'Latin' => '\p{Latin}',
                'Lepcha' => '\p{Lepcha}',
                'Limbu' => '\p{Limbu}',
                'Linear_B' => '\p{Linear_B}',
                'Lisu' => '\p{Lisu}',
                'Lycian' => '\p{Lycian}',
                'Lydian' => '\p{Lydian}',
                'Malayalam' => '\p{Malayalam}',
                'Meetei_Mayek' => '\p{Meetei_Mayek}',
                'Mongolian' => '\p{Mongolian}',
                'Myanmar' => '\p{Myanmar}',
                'New_Tai_Lue' => '\p{New_Tai_Lue}',
                'Nko' => '\p{Nko}',
                'Ogham' => '\p{Ogham}',
                'Old_Italic' => '\p{Old_Italic}',
                'Old_Persian' => '\p{Old_Persian}',
                'Old_South_Arabian' => '\p{Old_South_Arabian}',
                'Old_Turkic' => '\p{Old_Turkic}',
                'Ol_Chiki' => '\p{Ol_Chiki}',
                'Oriya' => '\p{Oriya}',
                'Osmanya' => '\p{Osmanya}',
                'Phags_Pa' => '\p{Phags_Pa}',
                'Phoenician' => '\p{Phoenician}',
                'Rejang' => '\p{Rejang}',
                'Runic' => '\p{Runic}',
                'Samaritan' => '\p{Samaritan}',
                'Saurashtra' => '\p{Saurashtra}',
                'Shavian' => '\p{Shavian}',
                'Sinhala' => '\p{Sinhala}',
                'Sundanese' => '\p{Sundanese}',
                'Syloti_Nagri' => '\p{Syloti_Nagri}',
                'Syriac' => '\p{Syriac}',
                'Tagalog' => '\p{Tagalog}',
                'Tagbanwa' => '\p{Tagbanwa}',
                'Tai_Le' => '\p{Tai_Le}',
                'Tai_Tham' => '\p{Tai_Tham}',
                'Tai_Viet' => '\p{Tai_Viet}',
                'Tamil' => '\p{Tamil}',
                'Telugu' => '\p{Telugu}',
                'Thaana' => '\p{Thaana}',
                'Thai' => '\p{Thai}',
                'Tibetan' => '\p{Tibetan}',
                'Tifinagh' => '\p{Tifinagh}',
                'Ugaritic' => '\p{Ugaritic}',
                'Vai' => '\p{Vai}',
                'Yi' => '\p{Yi}'
                );

$out = fopen($FILE_NAME_OUT, 'w');
foreach ($props as $funcname => $regex) {
    $pattern = "/$regex/su";
    $exists = false;
    $tmp = fopen($FILE_NAME_TMP, 'w');
    for ($i = 0; $i <= 0x10FFFD; $i++) {
        $res = preg_match($pattern, code2utf8($i));
        if ($res) {
            $str = strtoupper(dechex($i));
            if (strlen($str) === 1)
                $str = '000'.$str;
            else if (strlen($str) === 2)
                $str = '00'.$str;
            else if (strlen($str) === 3)
                $str = '0'.$str;
            fwrite($tmp, '0x'.$str. $eol);
            $exists = true;
        }
    }
    fclose($tmp);
    $in = fopen($FILE_NAME_TMP, 'r');
    $previous = -1;
    $prevhex = -1;
    fwrite($out, $tab1 . $prefix . $funcname . $suffix . $eol);
    fwrite($out, $tab1 . $tab1 . 'return array(');
    if (!$exists) {
        fwrite($out, ');' . $eol);
    } else {
        while (!feof($in)) {
            $str = fgets($in);
            if (feof($in)) {
                break;
            }
            $str = substr($str, 0, strlen($str) - 1);
            $newnum = hexdec($str);
            if ($previous === -1) {
                fwrite($out, 'array(0=>' . $str . ', ');
            } else {
                if ($newnum !== $previous + 1) {
                    fwrite($out, '1=>' . $prevhex . '),' . $eol);
                    fwrite($out, $tab2 . 'array(0=>' . $str . ', ');
                }
            }
            $previous = $newnum;
            $prevhex = $str;
        }
        fwrite($out, '1=>' . $prevhex . '));' . $eol);
    }
    fwrite($out, $tab1 . '}' . $eol . $eol);
    fclose($in);
    echo 'Done with ' . $funcname . $eol;
}
echo 'Done!' . $eol;
fclose($out);
?>
