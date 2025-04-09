<section class="moon"><?php
$datetime = null;
$moonphase = \App\ThirdParty\moonphase::load($datetime);
?>
<div class="float-start mw-33"><?php 
echo \App\ThirdParty\moonphase::img($moonphase); 
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
		$datetime->setTimestamp((int) $timestamp);
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