<section><?php
$datetime = null;
$moonphase = \App\ThirdParty\moonphase::load($datetime);
helper('html');

$timestamps = [
	'this_new'  => $moonphase->getPhaseNewMoon(),
	'this_full' => $moonphase->getPhaseFullMoon(),
	'next_new'  => $moonphase->getPhaseNextNewMoon(),
	'next_full' => $moonphase->getPhaseNextFullMoon(),
];
asort($timestamps);

$phase = $moonphase->getPhase();
$phase_name = $moonphase->getPhaseName();

$tbody = []; 
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

$tbody[] = ['Phase', sprintf('%s (%s%%)', $phase_name, round($phase * 100))];

echo \App\ThirdParty\moonphase::img($moonphase);

$table = new \CodeIgniter\View\Table();
$table->autoHeading = false;
echo $table->generate($tbody);

?></section>