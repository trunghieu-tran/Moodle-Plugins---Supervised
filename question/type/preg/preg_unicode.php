<?php

class qtype_preg_unicode extends textlib {

    public static $ranges = array('Basic Latin'                               => array(0x0000, 0x007F),
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

    public static $hspaces = array(0x0009,    // Horizontal tab.
                                   0x0020,    // Space.
                                   0x00A0,    // Non-break space.
                                   0x1680,    // Ogham space mark.
                                   0x180E,    // Mongolian vowel separator.
                                   0x2000,    // En quad.
                                   0x2001,    // Em quad.
                                   0x2002,    // En space.
                                   0x2003,    // Em space.
                                   0x2004,    // Three-per-em space.
                                   0x2005,    // Four-per-em space.
                                   0x2006,    // Six-per-em space.
                                   0x2007,    // Figure space.
                                   0x2008,    // Punctuation space.
                                   0x2009,    // Thin space.
                                   0x200A,    // Hair space.
                                   0x202F,    // Narrow no-break space.
                                   0x205F,    // Medium mathematical space.
                                   0x3000     // Ideographic space.
                                   );

    public static $vspaces = array(0x000A,    // Linefeed.
                                   0x000B,    // Vertical tab.
                                   0x000C,    // Formfeed.
                                   0x000D,    // Carriage return.
                                   0x0085,    // Next line.
                                   0x2028,    // Line separator.
                                   0x2029     // Paragraph separator.
                                   );

    /**
     * Returns unicode ranges which $utf8str characters belongs to.
     * @param utf8str UTF-8 string.
     * @return array of arrays['left'=>int, 'right'=>int] containing ranges.
     */
    public static function get_ranges($utf8str) {
        $result = array();
        for ($i = 0; $i < self::strlen($utf8str); $i++) {
            $code = self::ord(self::substr($utf8str, $i, 1));
            foreach (self::$ranges as $name => $range) {
                if ($code >= $range[0] && $code < $range[1]) {
                    if (!array_key_exists($name, $result)) {
                        $result[$name] = array('left' => $range[0], 'right' => $range[1]);
                    }
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Intersects ranges to get a one whole range.
     * @param ranges an array of ranges by "OR". Every range is represented as array('negative'=>bool, 'left'=>int, 'right'=>int).
     * @return an array of ranges where ranges represented as array ('left'=>int, 'right'=>int).
     */
    public static function intersect_ranges($ranges) {
        $result = array();
        foreach ($ranges as $tointersect) {
            $curresult = array(array('left' => 0, 'right' => 0x10FFFD));
            for ($i = 0; count($curresult) > 0 && $i < count($tointersect); $i++) {
                // $curresult is updated every iteration of this loop.
                $tmp = $tointersect[$i];
                // A negative range turns into two positive ranges.
                if (!$tmp['negative']) {
                    $currange = array(array('left' => $tmp['left'], 'right' => $tmp['right']));
                } else {
                    $currange = array();
                    if ($tmp['left'] > 0) {
                        $currange[] = array('left' => 0, 'right' => $tmp['left']);
                    }
                    if ($tmp['right'] < 0x10FFFD) {
                        $currange[] = array('left' => $tmp['right'], 'right' => 0x10FFFD);
                    }
                }

                // Process two current ranges.
                $tmp = array();
                //echo 'intersecting '; print_r($curresult); echo ' with '; print_r($currange); echo '<br/>';
                foreach ($curresult as $curresultpart) {
                    foreach ($currange as $currangepart) {
                        if ($curresultpart['left'] < $currangepart['left']) {
                            $left = $curresultpart;
                            $right = $currangepart;
                        } else {
                            $left = $currangepart;
                            $right = $curresultpart;
                        }
                        //echo $left['right'].'<br/>';
                        if ($right['left'] <= $left['right'] && $left['right'] >= $right['left']) {
                            $tmp[] = array('left' => $right['left'], 'right' => min($left['right'], $right['right']));
                        }
                    }
                }
                //echo 'result: '; print_r($tmp); echo '<br/><br/>';
                $curresult = $tmp;
            }
            if (count($curresult) > 0) {
                foreach ($curresult as $tmp) {
                    $result[] = $tmp;
                }
            }
        }
        return $result;
    }

    /**
     * Returns the code of a UTF-8 character.
     * @param utf8chr - a UTF-8 character.
     * @return its code.
     */
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

    /**
     * Checks if a character is an ascii character.
     */
    public static function is_ascii($utf8chr) {
        $ord = self::ord($utf8chr);
        return $ord >= 0 && $ord <= 127;
    }

    // TODO: unicode support for the functions below!

    /**
     * Checks if a character is a digit.
     */
    public static function is_digit($utf8chr) {
        return ctype_digit($utf8chr);
    }

    /**
     * Checks if a character is an xdigit.
     */
    public static function is_xdigit($utf8chr) {
        return ctype_xdigit($utf8chr);
    }

    /**
     * Checks if a character is a space.
     */
    public static function is_space($utf8chr) {
        return ctype_space($utf8chr);
    }

    /**
     * Checks if a character is a cntrl.
     */
    public static function is_cntrl($utf8chr) {
        return ctype_cntrl($utf8chr);
    }

    /**
     * Checks if a character is a graph.
     */
    public static function is_graph($utf8chr) {
        return ctype_graph($utf8chr);
    }

    /**
     * Checks if a character is lowercase.
     */
    public static function is_lower($utf8chr) {
        return ctype_lower($utf8chr);
    }

    /**
     * Checks if a character is uppercase.
     */
    public static function is_upper($utf8chr) {
        return ctype_upper($utf8chr);
    }

    /**
     * Checks if a character is printable.
     */
    public static function is_print($utf8chr) {
        return ctype_print($utf8chr);
    }

    /**
     * Checks if a character is non-space or alnum.
     */
    public static function is_punct($utf8chr) {
        return ctype_punct($utf8chr);
    }

    /**
     * Checks if a character is alphabetic.
     */
    public static function is_alpha($utf8chr) {
        return ctype_alpha($utf8chr);
    }

    /**
     * Checks if a character is alphanumeric.
     */
    public static function is_alnum($utf8chr) {
        return ctype_alnum($utf8chr);
    }

    /**
     * Checks if a character is alphabetic or '_'.
     */
    public static function is_wordchar($utf8chr) {
        return $utf8chr === '_' || self::is_alnum($utf8chr);
    }

    /**
     * Checks if a character is a horizontal space.
     */
    public static function is_hspace($utf8chr) {
        return in_array(self::ord($utf8chr), self::$hspaces);
    }

    /**
     * Checks if a character is a vertical space.
     */
    public static function is_vspace($utf8chr) {
        return in_array(self::ord($utf8chr), self::$vspaces);
    }

    /******************************************************************/

    public static function is_Cc($utf8chr) {    // Control
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Cf($utf8chr) {    // Format
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Cn($utf8chr) {    // Unassigned
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Co($utf8chr) {    // Private use
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Cs($utf8chr) {    // Surrogate
        throw new Exception('Unicode properties support is not implemented yet');
    }

    /******************************************************************/

    public static function is_Ll($utf8chr) {    // Lower case letter
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lm($utf8chr) {    // Modifier letter
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lo($utf8chr) {    // Other letter
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lt($utf8chr) {    // Title case letter
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lu($utf8chr) {    // Upper case letter
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_L($utf8chr) {     // Letter
        return self::is_Ll($utf8chr) || self::is_Lm($utf8chr) || self::is_Lo($utf8chr) ||
               self::is_Lt($utf8chr) || self::is_Lu($utf8chr);
    }

    /******************************************************************/

    public static function is_Mc($utf8chr) {    // Spacing mark
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Me($utf8chr) {    // Enclosing mark
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Mn($utf8chr) {    // Non-spacing mark
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_M($utf8chr) {     // Mark
        return self::is_Mc($utf8chr) || self::is_Me($utf8chr) || self::is_Mn($utf8chr);
    }

    /******************************************************************/

    public static function is_Nd($utf8chr) {    // Decimal number
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Nl($utf8chr) {    // Letter number
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_No($utf8chr) {    // Other number
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_N($utf8chr) {     // Number
        return self::is_Nd($utf8chr) || self::is_Nl($utf8chr) || self::is_No($utf8chr);
    }

    /******************************************************************/

    public static function is_Pc($utf8chr) {    // Connector punctuation
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Pd($utf8chr) {    // Dash punctuation
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Pe($utf8chr) {    // Close punctuation
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Pf($utf8chr) {    // Final punctuation
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Pi($utf8chr) {    // Initial punctuation
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Po($utf8chr) {    // Other punctuation
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Ps($utf8chr) {    // Open punctuation
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_P($utf8chr) {     // Punctuation
        return self::is_Pc($utf8chr) || self::is_Pd($utf8chr) || self::is_Pe($utf8chr) ||
               self::is_Pf($utf8chr) || self::is_Pi($utf8chr) || self::is_Po($utf8chr) ||
               self::is_Ps($utf8chr);
    }

    /******************************************************************/

    public static function is_Sc($utf8chr) {    // Currency symbol
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Sk($utf8chr) {    // Modifier symbol
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Sm($utf8chr) {    // Mathematical symbol
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_So($utf8chr) {    // Other symbol
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_S($utf8chr) {     // Symbol
        return self::is_Sc($utf8chr) || self::is_Sk($utf8chr) ||
               self::is_Sm($utf8chr) || self::is_So($utf8chr);
    }

    /******************************************************************/

    public static function is_Zl($utf8chr) {    // Line separator
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Zp($utf8chr) {    // Paragraph separator
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Zs($utf8chr) {    // Space separator
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Z($utf8chr) {     // Separator
        return self::is_Zl($utf8chr) || self::is_Zp($utf8chr) || self::is_Zs($utf8chr);
    }

    /******************************************************************/

    public static function is_C($utf8chr) {     // Other
        return !self::is_cC($utf8chr) && !self::is_Cf($utf8chr) && !self::is_Cn($utf8chr) &&
               !self::is_Co($utf8chr) && !self::is_Cs($utf8chr) && !self::is_L($utf8chr) &&
               !self::is_M($utf8chr) && !self::is_N($utf8chr) && !self::is_P($utf8chr) &&
               !self::is_S($utf8chr) && !self::is_Z($utf8chr);
    }

    /******************************************************************/

    public static function is_Arabic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Armenian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Avestan($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Balinese($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Bamum($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Bengali($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Bopomofo($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Braille($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Buginese($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Buhid($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Canadian_Aboriginal($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Carian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Cham($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Cherokee($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Common($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Coptic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Cuneiform($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Cypriot($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Cyrillic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Deseret($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Devanagari($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Egyptian_Hieroglyphs($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Ethiopic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Georgian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Glagolitic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Gothic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Greek($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Gujarati($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Gurmukhi($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Han($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Hangul($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Hanunoo($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Hebrew($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Hiragana($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Imperial_Aramaic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Inherited($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Inscriptional_Pahlavi($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Inscriptional_Parthian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Javanese($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Kaithi($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Kannada($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Katakana($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Kayah_Li($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Kharoshthi($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Khmer($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lao($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Latin($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lepcha($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Limbu($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Linear_B($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lisu($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lycian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Lydian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Malayalam($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Meetei_Mayek($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Mongolian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Myanmar($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_New_Tai_Lue($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Nko($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Ogham($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Old_Italic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Old_Persian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Old_South_Arabian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Old_Turkic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Ol_Chiki($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Oriya($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Osmanya($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Phags_Pa($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Phoenician($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Rejang($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Runic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Samaritan($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Saurashtra($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Shavian($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Sinhala($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Sundanese($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Syloti_Nagri($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Syriac($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Tagalog($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Tagbanwa($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Tai_Le($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Tai_Tham($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Tai_Viet($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Tamil($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Telugu($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Thaana($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Thai($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Tibetan($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Tifinagh($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Ugaritic($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Vai($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

    public static function is_Yi($utf8chr) {
        throw new Exception('Unicode properties support is not implemented yet');
    }

}
