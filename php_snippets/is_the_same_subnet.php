<?

function is_the_same_subnet($ip_a, $ip_b) {
	$mask = 8; //A段24 B段16 C段8
	return (ip2long($ip_a) >> $mask) == (ip2long($ip_b) >> $mask);
}

var_dump(is_the_same_subnet('1.1.1.1', '1.1.2.0'));

?>