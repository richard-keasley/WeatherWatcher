<?php $this->extend('template');
helper('form');
helper('inflector');
$interval = new \DateInterval('P7D');
$datetime = new \DateTime($start);
$title = $datetime->format('l j F Y');
$datetime->sub($interval);
$nav_prev = $datetime->format('Y-m-d');

$datetime = new \DateTime($start);
$datetime->add($interval);
$nav_next = $datetime->format('Y-m-d');

$this->section('header'); ?>
<h1>Dailies - week commencing <?php echo $title;?></h1>
<?php $this->endSection();

$this->section('top'); ?>
<div class="navbar">
<?php
echo anchor("dailies/week/{$nav_prev}", ' &lt; ');
echo anchor("dailies/week/{$nav_next}", ' &gt; ');

$types = ['temperature', 'rain'];
foreach($types as $type) {
	echo anchor("graph/dailies/{$type}/{$start}/{$end}", $type);
}
?>
</div>
<?php 
$this->endSection();

$this->section('main');
echo $this->include('dailies/table');
$this->endSection();

$this->section('bottom');
echo $this->include('dailies/nav');
$this->endSection();