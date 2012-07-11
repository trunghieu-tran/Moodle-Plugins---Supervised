<?php

    /**
     * Генератор отрезков для юникод-свойств.
     * Переменная $prop принимает нужное значение,
     * выходной файл out.txt в папке $PATH содержит массив
     * отрезков юникода, подходящих под данное свойство.
     * @author Valeriy Streltsov
     */

    $PATH = '/home/valeriy/';

    /*$props = array('Cc', 'Cf', 'Cn', 'Co', 'Cs',
                   'Ll', 'Lm', 'Lo', 'Lt', 'Lu',
                   'Mc', 'Me', 'Mn',
                   'Nd', 'Nl', 'No',
                   'Pc', 'Pd', 'Pe', 'Pf', 'Pi', 'Po', 'Ps',
                   'Sc', 'Sk', 'Sm', 'So',
                   'Zl', 'Zp', 'Zs');*/

    $prop = 'Z';

    /**
     * Returns the utf8 string corresponding to the unicode value
     * (from php.net, courtesy - romans@void.lv)
     *
     * @param  int    $num one unicode value
     * @return string the UTF-8 char corresponding to the unicode value
     */
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

    $pattern = '/(*UTF8)\p{' . $prop . '}/';

    $out = fopen($PATH . 'in.txt', 'w');
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
            fwrite($out, '0x'.$str.chr(0x000A));
        }
    }
    fclose($out);

    $in = fopen($PATH . 'in.txt', 'r');
    $out = fopen($PATH . 'out.txt', 'w');
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
