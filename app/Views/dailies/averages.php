<?php $this->extend('template');

$this->section('header'); ?>
<h1>Daily Averages</h1>
<?php $this->endSection();

$this->section('top'); 
$this->endSection();

$this->section('main'); ?>
<p>Averages over the year.</p>
<section class="flex"><?php
$datanames = ['temperature', 'rain', 'humidity', 'wind', 'solar'];
foreach($datanames as $dataname) {
	echo new \App\Views\Htm\graph('averages', $dataname);
}
?></section>
<?php $this->endSection();

$this->section('bottom');
echo $this->include('dailies/nav');
$this->endSection();
