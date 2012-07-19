<?php

$in = fopen('testinput1', 'r');
$out = fopen('pcre_lexer_testinput1.txt', 'w');

$eol = chr(0x000A);

$started = false;
$tmp = '';

while (!feof($in)) {
    $ch = fgetc($in);
    if ($ch === $eol)
        continue;
    if ($ch === '\\') {
        $ch = fgetc($in);
        $tmp .= $ch;
    } else if ($ch === '/') {
        if ($started) {
            $started = false;
            if ($tmp !== '') {
                fwrite($out, $tmp.$eol);
            }
            $tmp = '';
        } else {
            $started = true;
        }
    } else {
        if ($started)
            $tmp .= $ch;
    }
        
}

fclose($in);
fclose($out);


?>
