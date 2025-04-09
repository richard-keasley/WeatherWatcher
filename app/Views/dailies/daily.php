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
		
		# d($key, $rsec, $rkey);
		$format = \App\Entities\Reading::format($rsec, $rkey);
		$value = sprintf($format, $value);	
					
		$label = humanize($key);
		
		$tbody[] = [$label, $value];
	}
	
	$table = \App\Views\Htm\table::load('list');
	$table->autoHeading = false;
	echo $table->generate($tbody);
}
?></div>