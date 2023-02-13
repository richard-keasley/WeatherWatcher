<div><?php 
if(empty($daily)) { 
	echo new \App\Views\Htm\alert('No data');
} 
else {
	printf('<p><strong>%s</strong></p>', $daily->get_date('j F Y'));
	
	$tbody = [];
	foreach($daily->toArray() as $key=>$value) {
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
		
		#d($rsec, $rkey, $format);
		
		$label = humanize($key);
		$value = sprintf($format, $value);
		$tbody[] = [$label, $value];
	}
	
	$table = \App\Views\Htm\table::load('list');
	$table->autoHeading = false;
	echo $table->generate($tbody);
}
?></div>