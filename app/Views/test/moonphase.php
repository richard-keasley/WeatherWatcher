<?php $this->extend('template');

$this->section('header'); ?>
<h1>Moon phase</h1>
<?php $this->endSection();

$this->section('main'); ?>

<div class="flex"><?php 

$max = 30; // number of steps 
$month = 708; // 708 hours (29.5 days) in a lunar month

$last = new \DateInterval("PT{$month}H"); // 1 lunar month (29.5 days)
$last = (new \datetime())->add($last);

$step = ceil($month/$max); 
$step = new \DateInterval("PT{$step}H"); 

$datetime = new \datetime();
do { 
	$moonphase = \App\ThirdParty\moonphase::load($datetime);

	echo '<div>' . 
		$datetime->format('d-M H:i') .
		'<br>' . 
		\App\ThirdParty\moonphase::img($moonphase) . 
		'</div>';
	
	$datetime->add($step);
} while ($datetime<$last);

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
