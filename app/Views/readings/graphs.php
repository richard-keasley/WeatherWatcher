<section class="flex"><?php
# if(count($dailies) < 3) return; // no graphs for small datasets

$datanames = ['temperature', 'rain', 'humidity', 'wind', 'solar'];
foreach($datanames as $dataname) {
	echo new \App\Views\Htm\graph('readings', $dataname, $date);
}
?></section>
