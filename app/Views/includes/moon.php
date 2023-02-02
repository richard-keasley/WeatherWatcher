<section class="moon"><?php
$datetime = null;
$moonphase = \App\ThirdParty\moonphase::load($datetime);
?>
<div class="float-start"><?php 
echo \App\ThirdParty\moonphase::img($moonphase); 
/*
$datetime = new \datetime('2023-02-19');
$interval = new \DateInterval('PT2H');
$count = 0;
do {
	$datetime->add($interval);
	$test = \App\ThirdParty\moonphase::load($datetime);
	echo $datetime->format('Y-m-d H:i ');
	echo \App\ThirdParty\moonphase::img($test, 'test');
	$count++;
} while ($count<40);
*/
?></div>
<?php

$tbody = []; 

$timestamps = [
	'this_new'  => $moonphase->getPhaseNewMoon(),
	'this_full' => $moonphase->getPhaseFullMoon(),
	'next_new'  => $moonphase->getPhaseNextNewMoon(),
	'next_full' => $moonphase->getPhaseNextFullMoon(),
];
asort($timestamps);
$now = time();
$datetime = new \DateTime;
foreach($timestamps as $key=>$timestamp) {
	if($timestamp>=$now && count($tbody)<2) {
		$arr = explode('_', $key);
		$label = "Next {$arr[1]} moon";
		$datetime->setTimestamp($timestamp);
		$tbody[] = [$label, $datetime->format('j M Y H:i')];
	}
}

$phase = $moonphase->getPhase();
$phase_name = $moonphase->getPhaseName();
$tbody[] = ['Phase', sprintf('%s (%s%%)', $phase_name, round($phase * 100))];

$table = \App\Views\Htm\table::load('list');
$table->autoHeading = false;
echo $table->generate($tbody);

?></section>