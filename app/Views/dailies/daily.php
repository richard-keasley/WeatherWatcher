<div><?php 
if(empty($daily)) { 
	echo '<p class="error">No data</p>';
} else {
	helper('inflector');
	$tbody = [];
	foreach($daily->toArray() as $key=>$val) {
		$parts = explode('_', $key);
		$rsec = match($parts[0]) {
			'uvi' => 'solar',
			default => $parts[0]
		};
		$rkey = match($parts[0]) {
			'rain' => 'day',
			'solar' => 'radiation',
			'uvi' => 'uv',
			'wind' => 'speed',
			default => ''
		};
		$format = \App\Entities\Reading::format($rsec, $rkey);
		$label = humanize($key) . ':';
		$val = sprintf($format, $val);
		$tbody[] = [$label, $val];
	}
	
	$table = \App\Views\Htm\table::load('list');
	$table->autoHeading = false;
	echo $table->generate($tbody);
}
?></div>