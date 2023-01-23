<section><?php
helper('inflector');
$config = config('App');
$now = time();
$suninfo = App\ThirdParty\suninfo::load($now, $config->latitude, $config->longitude);

# $suninfo::compile();

$dt_format = 'j M Y H:i';
$t_format = 'H:i';

$datetime = new \DateTime();
$info = [];
$wanted = ['sunrise', 'transit', 'sunset'];
foreach($wanted as $ev_type) {
	$timestamp = $suninfo->info[$ev_type];
	$datetime->setTimestamp($timestamp);
	$info[] = [humanize($ev_type), $datetime->format($t_format)];
}

/*
foreach($suninfo->info as $ev_type=>$timestamp) {
	$datetime->setTimestamp($timestamp);
	$info[] = [humanize($ev_type), $datetime->format($t_format)];
}
*/

$events = [];
foreach($suninfo->solstices as $event) {
	if($event[1]>$now) {
		$ev_type = match($event[0]) {
			3 => 'spring equinox',
			6 => 'summer solstice',
			9 => 'autumn equinox',
			12=> 'winter solstice',
			default => '??'
		};
		$ev_time = $event[1] ?? 0 ;
		$datetime->setTimestamp($event[1]);
		$events[] = [$ev_type, $datetime->format($dt_format)];
		if(count($events)>3) break;
	}
}

$tbody = array_merge($info, $events);

$table = \App\Views\Htm\table::load('list');
$table->autoHeading = false;
echo $table->generate($tbody);

echo $suninfo::credit;
?>
</section>