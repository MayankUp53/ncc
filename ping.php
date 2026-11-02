<?php
$fd = fopen("crmprops", 'r');
if ($fd !== false) {
	$sep = false;
	while ($line = fgets($fd)) {
		if ($sep) {
			echo ";;";
		} else {
			$sep = true;
		}
		echo trim($line);
	}
} else {
	echo "Error = Could not open file\n";
}
?>

