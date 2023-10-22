<?php $this->extend('template');

$this->section('main'); 

$readings = new \App\Models\Readings;
$dailies = new \App\Models\Dailies;
$view = realpath(__DIR__ . '/../dailies/daily.php');

$datetime = new \datetime('2023-10-12');
// current 
$daily = $dailies->find($datetime->format('Y-m-d'));
include($view);

// from readings
$daily = $readings->get_daily($datetime);
include($view);
# $dailies->save($daily);



$this->endSection();


/*
$reading = $readings->get_current();
$listener->inputs = $reading->inputs;

$sucess = $listener->check_keys();
if(!$sucess) {
	echo $listener->error;
}
else {
	$sucess = $listener->process();
}
if($sucess) {
	// save to database
	# $readings = new \App\Models\Readings;
	# $readings->add_reading($sucess);
}	

d($sucess);
#d($listener);

*/

