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
    $result = str_replace('\\n', "\n", $string);
    $match = array();
    while (preg_match('/[\\\\][x]..?/', $result, $match) > 0) {
        $hex = substr($match[0], 2);
        $code = hexdec($hex);
        $ch = code2utf8($code);
        $result = str_replace($match[0], $ch, $result);
    }
    return $result;
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
    $OUTPUT_FILENAME = "cross_tests_from_att_$INPUT_SET.php";
    $FUNCTION_PREFIX = "function data_for_test_att_{$INPUT_SET}_";

    echo $filename . '... ';

    $in = @fopen($filename, 'r');
    $out = @fopen($OUTPUT_FILENAME, 'w');
    if (!$in) {
        echo "not found\n";
        return;
    }
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

        // Split the line by tabs; skip if it's empty or if it starts with "NOTE".
        $parts = explode("\t", $line);
        if (count($parts) < 4 || $parts[0] == 'NOTE') {
            continue;
        }

        // Get modifiers.
        $modifiers = $parts[0];

        // Get regex.
        $regex = $parts[1];
        if ($regex == 'SAME') {
            $regex = $lastregex;
        }
        if (strstr($modifiers, 'B')) {
            // Convert BRE to ERE syntax.
            $regex = replace_bre_characters($regex);
        }
        $regex = php_escape($regex);
        $lastregex = $regex;

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
            if ($pair[0] != '?') {
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
        if ($modifiers != '' && strpos($modifiers, 'i') != false) {
            fwrite($out, "                     'modifiers'=>'i',\n");
        }
        fwrite($out, "                     'tests'=>array(" . '$test1' . "),\n");
        fwrite($out, "                     'tags'=>" . $tags . ");\n");
        fwrite($out, "    }\n");
    }
    fwrite($out, "}\n");
    fclose($in);
    fclose($out);
    echo "converted\n";

}

$filenames = array('basic.dat',
                   //'categorize.dat',
                   'forcedassoc.dat',
                   'interpretation.dat',
                   'leftassoc.dat',
                   'nullsubexpr.dat',
                   'repetition.dat',
                   'rightassoc.dat'
                   );
foreach ($filenames as $filename) {
    process_file($filename);
}
echo "Done!\n";
