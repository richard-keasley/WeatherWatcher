<?php
$datanames = ['temperature', 'rain', 'humidity', 'wind', 'solar'];
$dayspan = isset($end);

# d($start); if($dayspan) d($end);

foreach($datanames as $dataname) {
	if($dayspan) {
		echo new \App\Views\Htm\graph('readings', $dataname, $start, $end);
	}
	else {
		echo new \App\Views\Htm\graph('readings', $dataname, $start);
	}
}
