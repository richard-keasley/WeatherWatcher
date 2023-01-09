<div><?php
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
	$val = sprintf($format, $val);
	echo "<strong>{$key}:</strong> {$val}<br>";
}
?></div>