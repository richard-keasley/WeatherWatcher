<?php $this->extend('template');

$this->section('main');

$img = [
	'src' => 'app/header.png',
	'class' => "mw-33 float-end"
];
	echo anchor('/', img($img));


echo new \App\Views\Htm\custom('about');
$this->endSection();

$this->section('header'); ?>
<h1>About this station</h1>
<?php $this->endSection();

$this->section('bottom');
echo $this->include('includes/credits'); 
?>
<section>
<h4>Set-up info</h4>
<?php 
$tbody = [];

$config = config('App');
# d($config);
$arr = [
	'baseURL' => 'URL',
	'appTimezone' => "time-zone",
	'bbc' => "BBC weather ID",
	'latitude' => "Station latitude",
	'longitude' => "Station longitude",
];
foreach($arr as $key=>$label) {
	$tbody[] = [$label, $config->$key ?? '??'];
}

$table = \App\Views\Htm\table::load('list');
$table->autoHeading = false;
echo $table->generate($tbody);
# d($tbody);

?>
</section>
<?php

$this->endSection();
