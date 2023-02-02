<?php $this->extend('template');

$this->section('header'); ?>
<h1>Current conditions</h1>
<?php $this->endSection();

$this->section('top'); ?>
<p>This site is under construction. View the 
<a href ="https://wow.metoffice.gov.uk/observations/details?site_id=b6e970c1-ced2-e611-9400-0003ff5991ab">Met. Office website</a>
for live updates from this weather station.</p> 
 
<?php $this->endSection();

$this->section('main'); ?>
<div class="float-start">
<?php echo $this->include('readings/reading'); ?>
</div>
<?php 
echo $this->include('readings/graphs'); 
$this->endSection();

$this->section('bottom'); ?>
<div class="flex flex-border">
<?php 
echo $this->include('includes/sun');
echo $this->include('includes/tides');
echo $this->include('includes/forecast');
echo $this->include('includes/moon');
?>
</div>
<?php $this->endSection();
