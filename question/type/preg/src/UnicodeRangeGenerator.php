<?php

	$in = fopen('in.txt', 'r');
	$out = fopen('out.txt', 'w');
	$tab = '                     ';
	$previous = -1;
	$prevhex = -1;
	fwrite($out, "return array(");
	//echo "return array(";
	
	while (!feof($in)) {
		$str = fgets($in);
		$res = preg_match('(U\\+[\\dabcdefABCDEF]+)', $str, $matches);
		if ($res) {
			$str = '0x' . substr($matches[0], 2);
			$newnum = hexdec($str);
			if ($previous === -1) {
				fwrite($out, "array('left'=>" . $str . ', ');
				//echo "array('left'=>" . $str . ', ';
			} else {
				if ($newnum !== $previous + 1) {
					fwrite($out, "'right'=>" . $prevhex . '),' . chr(0x000A));
					fwrite($out, $tab."array('left'=>" . $str . ', ');
					//echo "'right'=>" . $prevhex . '),<br/>';
					//echo $tab."array('left'=>" . $str . ', ';
				}
			}
			$previous = $newnum;
			$prevhex = $str;
		}
	}
	fwrite($out, "'right'=>" . $prevhex . '));' . chr(0x000A));
	//echo "'right'=>" . $prevhex . '));';
	echo "DONE";
	fclose($in);
	fclose($out);

?>
