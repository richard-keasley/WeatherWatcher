<?php $this->extend('template');

$this->section('header'); ?>
<h1>Moon phase</h1>
<?php $this->endSection();

$this->section('main'); ?>

<div class="flex"><?php 

$datetime = new \datetime();
$interval = new \DateInterval('PT2H');
$count = 0;
do { 
	$datetime->add($interval);
	$moonphase = \App\ThirdParty\moonphase::load($datetime);
	echo '<div>' . 
		$datetime->format('d-M H:i') .
		'<br>' . 
		\App\ThirdParty\moonphase::img($moonphase) . 
		'</div>';
	$count++;
} while ($count<40);

?></div>

<?php
$tbody = []; 

$datetime = null;
$moonphase = \App\ThirdParty\moonphase::load($datetime);
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
	$timestamp = intval($timestamp);
	$arr = explode('_', $key);
	$label = "Next {$arr[1]} moon";
	$datetime->setTimestamp($timestamp);
	$tbody[] = [$label, $timestamp, $datetime->format('j M Y H:i')];
}

$phase = $moonphase->getPhase();
$phase_name = $moonphase->getPhaseName();
$tbody[] = ['Phase', sprintf('%s (%s%%)', $phase_name, round($phase * 100))];

$table = \App\Views\Htm\table::load('list');
$table->autoHeading = false;
echo $table->generate($tbody);

$this->endSection();
