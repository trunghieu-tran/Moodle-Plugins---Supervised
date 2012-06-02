<?php

class qtype_preg_unicode extends textlib {

    var $RANGES = array(
    'Basic Latin'                               => array(0x0000, 0x007F),
    'C1 Controls and Latin-1 Supplement'        => array(0x0080, 0x00FF),
    'Latin Extended-A'                          => array(0x0100, 0x017F),
    'Latin Extended-B'                          => array(0x0180, 0x024F),
    'IPA Extensions'                            => array(0x0250, 0x02AF),
    'Spacing Modifier Letters'                  => array(0x02B0, 0x02FF),
    'Combining Diacritical Marks'               => array(0x0300, 0x036F),
    'Greek/Coptic'                              => array(0x0370, 0x03FF),
    'Cyrillic'                                  => array(0x0400, 0x04FF),
    'Cyrillic Supplement'                       => array(0x0500, 0x052F),
    'Armenian'                                  => array(0x0530, 0x058F),
    'Hebrew'                                    => array(0x0590, 0x05FF),
    'Arabic'                                    => array(0x0600, 0x06FF),
    'Syriac'                                    => array(0x0700, 0x074F),
    //'Undefined'                               => array(0x0750, 0x077F),
    'Thaana'                                    => array(0x0780, 0x07BF),
    //'Undefined'                               => array(0x07C0, 0x08FF),
    'Devanagari'                                => array(0x0900, 0x097F),
    'Bengali/Assamese'                          => array(0x0980, 0x09FF),
    'Gurmukhi'                                  => array(0x0A00, 0x0A7F),
    'Gujarati'                                  => array(0x0A80, 0x0AFF),
    'Oriya'                                     => array(0x0B00, 0x0B7F),
    'Tamil'                                     => array(0x0B80, 0x0BFF),
    'Telugu'                                    => array(0x0C00, 0x0C7F),
    'Kannada'                                   => array(0x0C80, 0x0CFF),
    'Malayalam'                                 => array(0x0D00, 0x0DFF),
    'Sinhala'                                   => array(0x0D80, 0x0DFF),
    'Thai'                                      => array(0x0E00, 0x0E7F),
    'Lao'                                       => array(0x0E80, 0x0EFF),
    'Tibetan'                                   => array(0x0F00, 0x0FFF),
    'Myanmar'                                   => array(0x1000, 0x109F),
    'Georgian'                                  => array(0x10A0, 0x10FF),
    'Hangul Jamo'                               => array(0x1100, 0x11FF),
    'Ethiopic'                                  => array(0x1200, 0x137F),
    //'Undefined'                               => array(0x1380, 0x139F),
    'Cherokee'                                  => array(0x13A0, 0x13FF),
    'Unified Canadian Aboriginal Syllabics'     => array(0x1400, 0x167F),
    'Ogham'                                     => array(0x1680, 0x169F),
    'Runic'                                     => array(0x16A0, 0x16FF),
    'Tagalog'                                   => array(0x1700, 0x171F),
    'Hanunoo'                                   => array(0x1720, 0x173F),
    'Buhid'                                     => array(0x1740, 0x175F),
    'Tagbanwa'                                  => array(0x1760, 0x177F),
    'Khmer'                                     => array(0x1780, 0x17FF),
    'Mongolian'                                 => array(0x1800, 0x18AF),
    //'Undefined'                               => array(0x18B0, 0x18FF),
    'Limbu'                                     => array(0x1900, 0x194F),
    'Tai Le'                                    => array(0x1950, 0x197F),
    //'Undefined'                               => array(0x1980, 0x19DF),
    'Khmer Symbols'                             => array(0x19E0, 0x19FF),
    //'Undefined'                               => array(0x1A00, 0x1CFF),
    'Phonetic Extensions'                       => array(0x1D00, 0x1D7F),
    //'Undefined'                               => array(0x1D80, 0x1DFF),
    'Latin Extended Additional'                 => array(0x1E00, 0x1EFF),
    'Greek Extended'                            => array(0x1F00, 0x1FFF),
    'General Punctuation'                       => array(0x2000, 0x206F),
    'Superscripts and Subscripts'               => array(0x2070, 0x209F),
    'Currency Symbols'                          => array(0x20A0, 0x20CF),
    'Combining Diacritical Marks for Symbols'   => array(0x20D0, 0x20FF),
    'Letterlike Symbols'                        => array(0x2100, 0x214F),
    'Number Forms'                              => array(0x2150, 0x218F),
    'Arrows'                                    => array(0x2190, 0x21FF),
    'Mathematical Operators'                    => array(0x2200, 0x22FF),
    'Miscellaneous Technical'                   => array(0x2300, 0x23FF),
    'Control Pictures'                          => array(0x2400, 0x243F),
    'Optical Character Recognition'             => array(0x2440, 0x245F),
    'Enclosed Alphanumerics'                    => array(0x2460, 0x24FF),
    'Box Drawing'                               => array(0x2500, 0x257F),
    'Block Elements'                            => array(0x2580, 0x259F),
    'Geometric Shapes'                          => array(0x25A0, 0x25FF),
    'Miscellaneous Symbols'                     => array(0x2600, 0x26FF),
    'Dingbats'                                  => array(0x2700, 0x27BF),
    'Miscellaneous Mathematical Symbols-A'      => array(0x27C0, 0x27EF),
    'Supplemental Arrows-A'                     => array(0x27F0, 0x27FF),
    'Braille Patterns'                          => array(0x2800, 0x28FF),
    'Supplemental Arrows-B'                     => array(0x2900, 0x297F),
    'Miscellaneous Mathematical Symbols-B'      => array(0x2980, 0x29FF),
    'Supplemental Mathematical Operators'       => array(0x2A00, 0x2AFF),
    'Miscellaneous Symbols and Arrows'          => array(0x2B00, 0x2BFF),
    //'Undefined'                               => array(0x2C00, 0x2E7F),
    'CJK Radicals Supplement'                   => array(0x2E80, 0x2EFF),
    'Kangxi Radicals'                           => array(0x2F00, 0x2FDF),
    //'Undefined'                               => array(0x2FE0, 0x2FEF),
    'Ideographic Description Characters'        => array(0x2FF0, 0x2FFF),
    'CJK Symbols and Punctuation'               => array(0x3000, 0x303F),
    'Hiragana'                                  => array(0x3040, 0x309F),
    'Katakana'                                  => array(0x30A0, 0x30FF),
    'Bopomofo'                                  => array(0x3100, 0x312F),
    'Hangul Compatibility Jamo'                 => array(0x3130, 0x318F),
    'Kanbun (0xKunten),'                        => array(0x3190, 0x319F),
    'Bopomofo Extended'                         => array(0x31A0, 0x31BF),
    //'Undefined'                               => array(0x31C0, 0x31EF),
    'Katakana Phonetic Extensions'              => array(0x31F0, 0x31FF),
    'Enclosed CJK Letters and Months'           => array(0x3200, 0x32FF),
    'CJK Compatibility'                         => array(0x3300, 0x33FF),
    'CJK Unified Ideographs Extension A'        => array(0x3400, 0x4DBF),
    'Yijing Hexagram Symbols'                   => array(0x4DC0, 0x4DFF),
    'CJK Unified Ideographs'                    => array(0x4E00, 0x9FAF),
    //'Undefined'                               => array(0x9FB0, 0x9FFF),
    'Yi Syllables'                              => array(0xA000, 0xA48F),
    'Yi Radicals'                               => array(0xA490, 0xA4CF),
    //'Undefined'                               => array(0xA4D0, 0xABFF),
    'Hangul Syllables'                          => array(0xAC00, 0xD7AF),
    //'Undefined'                               => array(0xD7B0, 0xD7FF),
    'High Surrogate Area'                       => array(0xD800, 0xDBFF),
    'Low Surrogate Area'                        => array(0xDC00, 0xDFFF),
    'Private Use Area'                          => array(0xE000, 0xF8FF),
    'CJK Compatibility Ideographs'              => array(0xF900, 0xFAFF),
    'Alphabetic Presentation Forms'             => array(0xFB00, 0xFB4F),
    'Arabic Presentation Forms-A'               => array(0xFB50, 0xFDFF),
    'Variation Selectors'                       => array(0xFE00, 0xFE0F),
    //'Undefined'                               => array(0xFE10, 0xFE1F),
    'Combining Half Marks'                      => array(0xFE20, 0xFE2F),
    'CJK Compatibility Forms'                   => array(0xFE30, 0xFE4F),
    'Small Form Variants'                       => array(0xFE50, 0xFE6F),
    'Arabic Presentation Forms-B'               => array(0xFE70, 0xFEFF),
    'Halfwidth and Fullwidth Forms'             => array(0xFF00, 0xFFEF),
    'Specials'                                  => array(0xFFF0, 0xFFFF),
    'Linear B Syllabary'                        => array(0x10000, 0x1007F),
    'Linear B Ideograms'                        => array(0x10080, 0x100FF),
    'Aegean Numbers'                            => array(0x10100, 0x1013F),
    //'Undefined'                               => array(0x10140, 0x102FF),
    'Old Italic'                                => array(0x10300, 0x1032F),
    'Gothic'                                    => array(0x10330, 0x1034F),
    'Ugaritic'                                  => array(0x10380, 0x1039F),
    'Deseret'                                   => array(0x10400, 0x1044F),
    'Shavian'                                   => array(0x10450, 0x1047F),
    'Osmanya'                                   => array(0x10480, 0x104AF),
    //'Undefined'                               => array(0x104B0, 0x107FF),
    'Cypriot Syllabary'                         => array(0x10800, 0x1083F),
    //'Undefined'                               => array(0x10840, 0x1CFFF),
    'Byzantine Musical Symbols'                 => array(0x1D000, 0x1D0FF),
    'Musical Symbols'                           => array(0x1D100, 0x1D1FF),
    //'Undefined'                               => array(0x1D200, 0x1D2FF),
    'Tai Xuan Jing Symbols'                     => array(0x1D300, 0x1D35F),
    //'Undefined'                               => array(0x1D360, 0x1D3FF),
    'Mathematical Alphanumeric Symbols'         => array(0x1D400, 0x1D7FF),
    //'Undefined'                               => array(0x1D800, 0x1FFFF),
    'CJK Unified Ideographs Extension B'        => array(0x20000, 0x2A6DF),
    //'Undefined'                               => array(0x2A6E0, 0x2F7FF),
    'CJK Compatibility Ideographs Supplement'   => array(0x2F800, 0x2FA1F),
    //'Unused'                                  => array(0x2FAB0, 0xDFFFF),
    'Tags'                                      => array(0xE0000, 0xE007F),
    //'Unused'                                  => array(0xE0080, 0xE00FF),
    'Variation Selectors Supplement'            => array(0xE0100, 0xE01EF),
    //'Unused'                                  => array(0xE01F0, 0xEFFFF),
    'Supplementary Private Use Area-A'          => array(0xF0000, 0xFFFFD),
    //'Unused'                                  => array(0xFFFFE, 0xFFFFF),
    'Supplementary Private Use Area-B'          => array(0x100000, 0x10FFFD)
    );

    public static function ord($utf8chr) {
        if ($utf8chr === '') {
            return 0;
        }

        $ord0 = ord($utf8chr{0});
        if ($ord0 >= 0 && $ord0 <= 127) {
            return $ord0;
        }

        $ord1 = ord($utf8chr{1});
        if ($ord0 >= 192 && $ord0 <= 223) {
            return ($ord0 - 192) * 64 + ($ord1 - 128);
        }

        $ord2 = ord($utf8chr{2});
        if ($ord0 >= 224 && $ord0 <= 239) {
            return ($ord0 - 224) * 4096 + ($ord1 - 128) * 64 + ($ord2 - 128);
        }

        $ord3 = ord($utf8chr{3});
        if ($ord0 >= 240 && $ord0 <= 247) {
            return ($ord0 - 240) * 262144 + ($ord1 - 128 )* 4096 + ($ord2 - 128) * 64 + ($ord3 - 128);
        }

        return false;
    }

    /*public static function ctype_alnum($str) {
        $ord = self::ord($str);
        foreach ($this->RANGES as $name => $range) {
            if ($ord >= $range[0] && $ord <= $range[1]) {
                echo $name;
            }
        }
    }*/
}

?>