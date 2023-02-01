<?php $this->extend('template');

$this->section('main'); 

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
	/*
	$readings = new \App\Models\Readings;
	$readings->add_reading($sucess);
	*/
}	

d($sucess);
#d($listener);






$this->endSection();

