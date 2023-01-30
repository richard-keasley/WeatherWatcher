<?php
if(!$dailies) return;
$types = ['temperature', 'rain', 'humidity', 'wind', 'solar'];
foreach($types as $type) {
	$graph = new \App\Views\Htm\graph($type, $start, $end);
	echo $graph;
}