<section class="moon flex"><?php

\App\ThirdParty\mooninfo::autoload();
$mooninfo = new \basecamp\mooninfo;

$items = [];
$timestamps = [
	'this_new'  => $mooninfo->getPhaseNewMoon(),
	'this_full' => $mooninfo->getPhaseFullMoon(),
	'next_new'  => $mooninfo->getPhaseNextNewMoon(),
	'next_full' => $mooninfo->getPhaseNextFullMoon(),
];
asort($timestamps);
$datetime = new \DateTime;
// future lunar events
foreach($timestamps as $key=>$timestamp) {
	if($timestamp<=$mooninfo->timestamp) continue; // in the past
	$arr = explode('_', $key);
	$label = "Next {$arr[1]} moon";
	$datetime->setTimestamp((int) $timestamp);
	$items[] = [$label, $datetime->format('j M Y H:i')];
	if(count($items)>1) break; // all done
}
//  current phase
$phase = round($mooninfo->phase * 100);
$items[] = ['Phase', "{$mooninfo->phase_name} ({$phase}%)"];
// moon name
$name = $mooninfo->name;
$blue = $mooninfo->blue;
if($blue) $name .= " (blue {$blue})";
$items[] = ['Name', $name];


$format = '<div style="width:5em;background:#112;padding:0.5em;">%s</div>';
printf($format, $mooninfo->image);

$table = \App\Views\Htm\table::load('list');
$table->autoHeading = false;
echo $table->generate($items);

?></section>
