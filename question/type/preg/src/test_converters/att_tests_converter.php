<?php

/**
 * Test converter from the AT&T format to the Preg cross-test format.
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

// Replaces escape sequences with real characters.
function replace_esc_sequences($string) {
    $dummy = array('\\a' => chr(0x07),
                   '\\b' => chr(0x08),
                   //'\\c'
                   '\\e' => chr(033),
                   '\\E' => chr(033),
                   '\\f' => chr(0x0C),
                   '\\n' => chr(0x0A),
                   '\\r' => chr(0x0D),
                   '\\s' => chr(0x20),
                   '\\t' => chr(0x09),
                   '\\v' => chr(0x0B),
                   );

    foreach ($dummy as $key => $ch) {
        $string = str_replace($key, $ch, $string);
    }

    $match = array();

    // \cx
    /*while (preg_match('/\\Q\\c\E./', $string, $match) > 0) {
        $x = ord(substr($match[0], 1));
        $x &= 037;
        $ch = code2utf8($x);
        $string = str_replace($match[0], $ch, $string);
    }*/

    // \xhh
    while (preg_match('/\\x..?/', $string, $match) > 0) {
        $hex = substr($match[0], 2);
        $code = hexdec($hex);
        $ch = code2utf8($code);
        $string = str_replace($match[0], $ch, $string);
    }
    return $string;
}

function replace_bre_characters($regex) {
    $result = str_replace('\\(', '(', $regex);
    $result = str_replace('\\)', ')', $result);
    $result = str_replace('\\{', '{', $result);
    $result = str_replace('\\}', '}', $result);
    return $result;
}

function php_escape($str) {
    $str = str_replace("\\", "\\\\", $str);
    $str = str_replace("$", "\\$", $str);
    $str = str_replace("\"", "\\\"", $str);
    return $str;
}

// There are some incompatibilites in the files with cross-tester checking algorithm.
function do_correction($regex, $string, &$index2write, &$length2write) {
    if ($regex == "(^|[ (,;])((([Ff]eb[^ ]* *|0*2/|\\\\* */?)0*[6-7]))([^0-9]|\\\$)") {
        if ($string == 'feb 6,') {
            $index2write = 'array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>5)';
            $length2write = 'array(0=>6,1=>0,2=>5,3=>5,4=>4,5=>1)';
        }
        if ($string == '2/7') {
            $index2write = 'array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>3)';
            $length2write = 'array(0=>3,1=>0,2=>3,3=>3,4=>2,5=>0)';
        }
        if ($string == 'feb 1,Feb 6') {
            $index2write = 'array(0=>5,1=>5,2=>6,3=>6,4=>6,5=>11)';
            $length2write = 'array(0=>6,1=>1,2=>5,3=>5,4=>4,5=>0)';
        }
    }

    if ($regex == "((((((((((((((((((((((((((((((x))))))))))))))))))))))))))))))" && $string == 'x') {
        $index2write = 'array(0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0,13=>0,14=>0,15=>0,16=>0,17=>0,18=>0,19=>0,20=>0,21=>0,22=>0,23=>0,24=>0,25=>0,26=>0,27=>0,28=>0,29=>0,30=>0)';
        $length2write = 'array(0=>1,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1,11=>1,12=>1,13=>1,14=>1,15=>1,16=>1,17=>1,18=>1,19=>1,20=>1,21=>1,22=>1,23=>1,24=>1,25=>1,26=>1,27=>1,28=>1,29=>1,30=>1)';
    }
    if ($regex == "((((((((((((((((((((((((((((((x))))))))))))))))))))))))))))))*" && $string == 'xx') {
        $index2write = 'array(0=>0,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1,11=>1,12=>1,13=>1,14=>1,15=>1,16=>1,17=>1,18=>1,19=>1,20=>1,21=>1,22=>1,23=>1,24=>1,25=>1,26=>1,27=>1,28=>1,29=>1,30=>1)';
        $length2write = 'array(0=>2,1=>1,2=>1,3=>1,4=>1,5=>1,6=>1,7=>1,8=>1,9=>1,10=>1,11=>1,12=>1,13=>1,14=>1,15=>1,16=>1,17=>1,18=>1,19=>1,20=>1,21=>1,22=>1,23=>1,24=>1,25=>1,26=>1,27=>1,28=>1,29=>1,30=>1)';
    }
}

function process_file($filename) {
    $INPUT_SET = explode('.', $filename)[0];
    $INPUT_SET = str_replace('-', '_', $INPUT_SET);
    $INPUT_SET = str_replace('+', 'plus', $INPUT_SET);
    $OUTPUT_FILENAME = "cross_tests_from_att_$INPUT_SET.php";
    $FUNCTION_PREFIX = "function data_for_test_att_{$INPUT_SET}_";

    echo $filename . '... ';

    $in = @fopen($filename, 'r');
    if (!$in) {
        echo "not found\n";
        return;
    }
    $out = @fopen($OUTPUT_FILENAME, 'w');
    if (!$out) {
        echo "can't create output file\n";
        return;
    }

    fwrite($out, "<?php\n\n");
    //fwrite($out, "defined('NOMATCH') || define('NOMATCH', qtype_preg_matching_results::NO_MATCH_FOUND);\n\n");
    fwrite($out, "class qtype_preg_cross_tests_from_att_$INPUT_SET {\n");

    $counter = 0;
    $lastregex = '';
    while (!feof($in)) {
        $line = fgets($in);
        if (feof($in)) {
            break;
        }

        // Chop the \n at the end; minimize consequent tabs to one.
        $line = substr($line, 0, strlen($line) - 1);
        $line = preg_replace("/\t+/", "\t", $line);
        if ($line == '') {
            continue;
        }

        // Split the line by tabs; skip if it's empty or if it starts with "#" or ":" or "NOTE".
        $parts = explode("\t", $line);
        if (count($parts) < 4 || $parts[0][0] == '#' || $parts[0][0] == ':' || $parts[0][0] == 'N') {
            continue;
        }

        // Get the flags.
        $flags = $parts[0];

        // Check if this test is supported.
        static $unsupported = array(
                //'B',//    basic           BRE (grep, ed, sed)
                //'E',//    REG_EXTENDED        ERE (egrep)
                'A',//    REG_AUGMENTED       ARE (egrep with negation)
                'S',//    REG_SHELL       SRE (sh glob)
                'K',//    REG_SHELL|REG_AUGMENTED KRE (ksh glob)
                'L',//    REG_LITERAL     LRE (fgrep)

                'a',//    REG_LEFT|REG_RIGHT  implicit ^...$
                'b',//    REG_NOTBOL      lhs does not match ^
                'c',//    REG_COMMENT     ignore space and #...\\n
                'd',//    REG_SHELL_DOT       explicit leading . match
                'e',//    REG_NOTEOL      rhs does not match $
                'f',//    REG_MULTIPLE        multiple \\n separated patterns
                'g',//    FNM_LEADING_DIR     testfnmatch only -- match until /
                'h',//    REG_MULTIREF        multiple digit backref
                'i',//    REG_ICASE       ignore case
                'j',//    REG_SPAN        . matches \\n
                'k',//    REG_ESCAPE      \\ to ecape [...] delimiter
                'l',//    REG_LEFT        implicit ^...
                'm',//    REG_MINIMAL     minimal match
                'n',//    REG_NEWLINE     explicit \\n match
                'o',//    REG_ENCLOSED        (|&) magic inside [@|&](...)
                'p',//    REG_SHELL_PATH      explicit / match
                'q',//    REG_DELIMITED       delimited pattern
                'r',//    REG_RIGHT       implicit ...$
                's',//    REG_SHELL_ESCAPED   \\ not special
                't',//    REG_MUSTDELIM       all delimiters must be specified
                'u',//    standard unspecified behavior -- errors not counted
                'v',//    REG_CLASS_ESCAPE    \\ special inside [...]
                'w',//    REG_NOSUB       no subexpression match array
                'x',//    REG_LENIENT     let some errors slide
                'y',//    REG_LEFT        regexec() implicit ^...
                'z',//    REG_NULL        NULL subexpressions ok
                '$',//                            expand C \\c escapes in fields 2 and 3
                '/',//                            field 2 is a regsubcomp() expression
                '=',//                            field 3 is a regdecomp() expression
            );
        $skip = false;
        foreach ($unsupported as $flag) {
            if (strpos($flags, $flag) !== false) {
                $skip = true;
                break;
            }
        }

        // Get regex despite possibly unsupported flags, cuz there can be 'SAME' reference in the next lines.
        $regex = $parts[1];
        if ($regex == 'SAME') {
            $regex = $lastregex;
        }
        if (strstr($flags, 'B')) {
            // Convert BRE to ERE syntax.
            $regex = replace_bre_characters($regex);
        }
        $regex = php_escape($regex);
        $lastregex = $regex;

        // Now can skip the unsupported test.
        if ($skip) {
            echo "skipping unsupported test: $line\n";
            continue;
        }

        // Get string.
        $string = $parts[2];
        if ($string == 'NULL') {
            $string = '';
        }
        $string = replace_esc_sequences($string);
        $string = php_escape($string);

        // Get matches.
        $indexes = $parts[3];
        if ($indexes == '' || $indexes == 'NOMATCH') {
            $indexes = '';
        } else if ($indexes[0] == '(' ) {
            $indexes = substr($indexes, 1, strlen($indexes) - 2);    // For splitting by ')('.
        } else {
            continue;   // Skip errors?
        }

        // Get subexpression pairs.
        $subexpressions = array();
        if ($indexes != '') {
            $subexpressions = explode(')(', $indexes);
        }

        // Form strings to be written to the file.
        $is_match = 'true';
        $full = 'true';
        $index2write = '';
        $length2write = '';
        foreach ($subexpressions as $key => $subexpression) {
            $pair = explode(',', $subexpression);
            if ($pair[0] != '?' && $pair[0] != '-1') {
                $index2write .= $key . '=>' . (int)($pair[0]) . ',';
                $length2write .= $key . '=>' . ((int)($pair[1]) - (int)($pair[0])) . ',';
            } else {
                // Specifying nomatch values is not necessary.
            }
        }
        $index2write = 'array(' . substr($index2write, 0, strlen($index2write) - 1) . ')';
        $length2write = 'array(' . substr($length2write, 0, strlen($length2write) - 1) . ')';

        do_correction($regex, $string, $index2write, $length2write);

        // Define tags.
        $tags = 'qtype_preg_cross_tester::TAG_FROM_ATT, qtype_preg_cross_tester::TAG_MODE_POSIX';
        if ($indexes == '') {
            $is_match = 'false';
            $full = 'false';
            $tags .= ', qtype_preg_cross_tester::TAG_DONT_CHECK_PARTIAL';   // AT&T tests don't check partial matches.
        }

        if ($INPUT_SET == 'categorize') {
            $tags .= ', qtype_preg_cross_tester::TAG_CATEGORIZE';
        }
        if ($INPUT_SET == 'leftassoc') {
            $tags .= ', qtype_preg_cross_tester::TAG_ASSOC_LEFT';
        }
        if ($INPUT_SET == 'rightassoc') {
            $tags .= ', qtype_preg_cross_tester::TAG_ASSOC_RIGHT';
        }
        $tags = 'array(' . $tags . ')';

        // Write to file.
        fwrite($out, "\n");
        fwrite($out,      "    " . $FUNCTION_PREFIX . $counter++ . "() {\n");
        fwrite($out, '        $test1' . " = array('str'=>\"" . $string . "\",\n");
        fwrite($out,      "                       'is_match'=>$is_match,\n");
        fwrite($out,      "                       'full'=>$full,\n");
        fwrite($out,      "                       'index_first'=>" . $index2write . ",\n");
        fwrite($out,      "                       'length'=>" . $length2write . ");\n\n");
        fwrite($out,      "        return array('regex'=>\"" . $regex . "\",\n");
        if ($flags != '' && strpos($flags, 'i') != false) {
            fwrite($out, "                     'modifiers'=>'i',\n");
        }
        fwrite($out, "                     'tests'=>array(" . '$test1' . "),\n");
        fwrite($out, "                     'tags'=>" . $tags . ");\n");
        fwrite($out, "    }\n");
    }
    fwrite($out, "}\n");
    fclose($in);
    fclose($out);
    if ($counter > 0) {
        echo "converted\n";
    } else {
        echo "no tests were converted, deleting file $OUTPUT_FILENAME\n";
        unlink($OUTPUT_FILENAME);
    }

}

$filenames = array('austin.dat',
                   'basic.dat',
                   'callout.dat',
                   //'categorize.dat',
                   'cut.dat',
                   'escape.dat',
                   'forcedassoc.dat',
                   'group.dat',
                   'haskell.dat',
                   'interpretation.dat',
                   'iso8859-1.dat',
                   'leftassoc.dat',
                   'libtre.dat',
                   'locale.dat',
                   'minimal.dat',
                   'nested.dat',
                   'noop.dat',
                   //'nullsubexpr-A.dat',
                   'nullsubexpr.dat',
                   'pcre-1.dat',
                   'pcre-2.dat',
                   'pcre-3.dat',
                   'pcre-4.dat',
                   'pcre-5.dat',
                   'perl.dat',
                   'reg.dat',
                   'regex++.dat',
                   'repetition.dat',
                   'rightassoc.dat',
                   'rxposix.dat',
                   'subexpr.dat',
                   'testdecomp.dat',
                   'testfmt.dat',
                   'testfnmatch.dat',
                   'testglob.dat',
                   'testmatch.dat',
                   'testregex.dat',
                   'testsub.dat',
                   'type.dat',
                   'unknownassoc.dat',
                   'xopen.dat',
                   'zero.dat'
                   );
foreach ($filenames as $filename) {
    process_file($filename);
}
echo "Done!\n";
