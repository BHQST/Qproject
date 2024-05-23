<?php
// proof of concept on the kuns trip generator and how it was able to be
// cracked with the single # pass and the single ! trip
// this is the chunk of the open source code that the kuns are built on

function generate_tripcode($config) {
	// list of name and trip outputs
	//#Matlock       !ITPb.qbhqo
	//#M@tlock!      !UW.yye1fxo
	//#Freed@m-      !xowAT4Z3VQ
	//#F!ghtF!g      !2jsTvXXmXs
	//#NowC@mes      !4pRcUAOIBE           
	//#StoRMkiL      !Q8nQHG/Io6
	//#WeAReQ@Q      !A6yxsPKia

	// you can change the name to whatever password to test, or feed itma list to bruteforce
	$name = "#Freed@m-";
	
	// global salt is what was rotated to cause the trip switch issue on 8kun
	// you can reverse the algo with some knowns to solve for salt then push into
	// the ## passwords and !! trips with a wordlist to crack the new Q trip
	$global_salt = "1234567890";

	if (!preg_match('/^([^#]+)?(###|##|#)(.+)$/', $name, $match))
		return array($name);

	$name = $match[1];
	$secure = $match[2] == '##';
	$secure2 = $match[2] == '###';
	$trip = $match[3];

	// convert to SHIT_JIS encoding
	$trip = mb_convert_encoding($trip, 'Shift_JIS', 'UTF-8');

	// generate salt
	$salt = substr($trip . 'H..', 1, 2);
	$salt = preg_replace('/[^.-z]/', '.', $salt);
	$salt = strtr($salt, ':;<=>?@[\]^_`', 'ABCDEFGabcdef');

	if ($secure) {
		if (isset($config['custom_tripcode']["##{$trip}"]))
			$trip = $config['custom_tripcode']["##{$trip}"];
		else
			$trip = '!!' . substr(crypt($trip, str_replace('+', '.', '_..A.' . substr(base64_encode(sha1($trip . $global_salt, true)), 0, 4))), -10);
	} elseif ($secure2) {
		if (isset($config['custom_tripcode']["###{$trip}"]))
			$trip = $config['custom_tripcode']["###{$trip}"];
		else
			$trip = '!!!' . substr(base64_encode(hash('sha256', $trip . $input)), 0,16);
	} else {
		if (isset($config['custom_tripcode']["#{$trip}"]))
			$trip = $config['custom_tripcode']["#{$trip}"];
		else
			$trip = '!' . substr(crypt($trip, $salt), -10);
	}
	echo "\n";
	echo $trip; // prints out the trip generated
}

// runs the generator and uses name inputed at top
$config = array();
generate_tripcode($config); 

?>

