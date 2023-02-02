<?php
$datanames = ['temperature', 'rain', 'humidity', 'wind', 'solar'];
foreach($datanames as $dataname) {
	echo new \App\Views\Htm\graph('readings', $dataname, $date);
}
