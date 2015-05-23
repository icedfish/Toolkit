<?php
	ini_set('memory_limit', '4G');	

	$height  = $width = 1024;
	$output = './baidu_map.png';
	$pngs = glob("./map/*.png");
	$matrix = [];
	foreach ($pngs as $_file) {
		preg_match("#^(\d+)-(\d+)-#", basename($_file), $m);
		$matrix[$m[1]][$m[2]] = $_file;
	}
	$im = imagecreate($height * count($matrix[0]), $width * count($matrix))
	or die("Cannot Initialize new GD image stream");

	foreach ($matrix as $row => $_arr) {
		foreach ($_arr as $line => $png_file) {
			echo "@ $line-$row\n";
			$_png = imagecreatefrompng($png_file);
			imagecopy($im, $_png, $line * $width, $row * $height, 0, 0, $width, $height);
			imagedestroy($_png);
			unset($_png);
		}
	}
	imagepng($im, $output);
	echo "done @ {$output}\n";

