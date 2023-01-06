<!DOCTYPE html>
<html>
<head>
<title>Sun-info</title>
</head>
<body>
<h1>Sun-info test page</h1>
<?php
// variables
$timestamp = time();
$latitude = -0.4319317;
$longitude = 51.5286051;
$dtz = new \DateTimeZone('Europe/London');

// get sun-info
include __DIR__ . '/suninfo.php';
$suninfo = new \basecamp\suninfo($timestamp, $latitude, $longitude);
$datetime = new \Datetime(null, $dtz);

// PHP date_sun_info
foreach($suninfo->info as $key=>$val) {
	$datetime->setTimeStamp($val);
	printf('%s: %s<br>', $key, $datetime->format('H:i'));
}

// next 4 solstice events
$events = [];
foreach($suninfo->solstices as $event) {
	if($event[1]>$timestamp) {
		$ev_type = match($event[0]) {
			3 => 'spring equinox',
			6 => 'summer solstice',
			9 => 'autumn equinox',
			12=> 'winter solstice',
			default => '??'
		};
		$events[] = $event;
		$ev_time = $event[1] ?? 0 ;
		$datetime->setTimeStamp($event[1]);
		printf('%s: %s<br>', $ev_type, $datetime->format('j M H:i'));
		if(count($events)>=4) break;
	}
}

echo $suninfo::credit;
?>
<hr>
<h3>Raw data</h3>
<pre>
<?php
print_r($suninfo->info);
foreach($suninfo->solstices as $event) {
	echo implode("\t", $event) . "\n";
}
?>
</pre>
</body>
</html>