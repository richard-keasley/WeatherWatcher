<section class="flex"><?php
// no graphs for small datasets
if(count($dailies) > 3) {
	$datanames = ['temperature', 'rain', 'humidity', 'wind', 'solar'];
	foreach($datanames as $dataname) {
		echo new \App\Views\Htm\graph('dailies', $dataname, $start, $end);
	}
}
?></section>
