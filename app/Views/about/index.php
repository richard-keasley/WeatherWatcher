<?php $this->extend('template');

$this->section('main'); ?>
<p>View the
<a href ="https://wow.metoffice.gov.uk/observations/details?site_id=b6e970c1-ced2-e611-9400-0003ff5991ab">Met. Office website</a>
for live updates from this weather station.</p> 
<?php $this->endSection();

$this->section('header'); ?>
<h1>About this station</h1>
<?php $this->endSection();

$this->section('bottom');
echo $this->include('includes/credits'); 
$this->endSection();
