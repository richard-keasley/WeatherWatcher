<?php $this->extend('template');

$view = realpath(__DIR__ . '/../dailies/daily.php');

$this->section('main'); ?>

<p>Dailies for <?php echo $datetime->format('d M Y');?>.</p>

<p>Enter test date as yyyy-mm-dd (e.g. <em>/test/readings/2023-11-09</em>).</p>

<div class="flex">

<div>
<h3>dailies</h3><?php
// current 
$daily = $dailies; 
include($view);
d($dailies);
?></div>

<div>
<h3>readings</h3><?php
// from readings
$daily = $readings;
include($view);
d($readings);

echo form_open();
echo '<button type="submit" name="update" value="readings">update</button>';
echo form_close();

?></div>

</div>

<?php
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

