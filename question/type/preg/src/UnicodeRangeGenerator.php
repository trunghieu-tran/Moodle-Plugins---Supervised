<?php

    $props = array('Cc', 'Cf', 'Cn', 'Co', 'Cs',
                   'Ll', 'Lm', 'Lo', 'Lt', 'Lu',
                   'Mc', 'Me', 'Mn',
                   'Nd', 'Nl', 'No',
                   'Pc', 'Pd', 'Pe', 'Pf', 'Pi', 'Po', 'Ps',
                   'Sc', 'Sk', 'Sm', 'So',
                   'Zl', 'Zp', 'Zs');

    $prop = 'Z';
    $pattern = '/(*UTF8)\p{' . $prop . '}/';

    $out = fopen('/home/valeriy/in.txt', 'w');
    for ($i = 0; $i <= 0x10FFFD; $i++) {
        $res = preg_match($pattern, qtype_preg_unicode::code2utf8($i));
        if ($res) {
            $str = strtoupper(dechex($i));
            if (strlen($str) === 1)
                $str = '000'.$str;
            else if (strlen($str) === 2)
                $str = '00'.$str;
            else if (strlen($str) === 3)
                $str = '0'.$str;
            fwrite($out, '0x'.$str.chr(0x000A));
                }
    }
    fclose($out);

    $in = fopen('/home/valeriy/in.txt', 'r');
    $out = fopen('/home/valeriy/out.txt', 'w');
    $tab = '                     ';
    $previous = -1;
    $prevhex = -1;
    fwrite($out, "return array(");
    while (!feof($in)) {
        $str = fgets($in);
        if (feof($in)) {
            break;
        }
        $str = substr($str, 0, strlen($str) - 1);
        $newnum = hexdec($str);
        if ($previous === -1) {
            fwrite($out, "array(0=>" . $str . ', ');
        } else {
            if ($newnum !== $previous + 1) {
                fwrite($out, "1=>" . $prevhex . '),' . chr(0x000A));
                fwrite($out, $tab."array(0=>" . $str . ', ');
            }
        }
        $previous = $newnum;
        $prevhex = $str;
    }
    fwrite($out, "1=>" . $prevhex . '));');
    echo "DONE";
    fclose($in);
    fclose($out);
?>
