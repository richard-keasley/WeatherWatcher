<?php $this->extend('template');

$this->section('header'); ?>
<h1>Current conditions</h1>
<?php $this->endSection();

$this->section('top');
$this->endSection();

$this->section('main'); ?>
<div class="float-start">
<?php echo $this->include('readings/reading'); ?>
</div>
<?php 
echo $this->include('readings/graphs'); 
echo new \App\Views\Htm\graph('readings', 'indoors', $start, $end);
$this->endSection();

$this->section('bottom'); ?>
<nav class="navbar"><?php
$anchors = [];
$views = ['week', 'month', 'year'];
foreach($views as $view) {
	$anchors[] = anchor("dailies/{$view}/{$start}", humanize($view));
}
foreach($anchors as $anchor) {
	printf('<button>%s</button>', $anchor);
}

?></nav>
<div class="flex flex-border">
<?php 
echo $this->include('includes/sun');
echo $this->include('includes/tides');
echo $this->include('includes/forecast');
echo $this->include('includes/moon');
?>
</div>
<?php $this->endSection();
