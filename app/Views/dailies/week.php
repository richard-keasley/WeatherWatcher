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
<h1>Daily: Week commencing <?php echo $title;?></h1>
<?php $this->endSection();

$this->section('top'); ?>
<div class="navbar"><?php
$anchors = [
	anchor("dailies/week/{$nav_prev}", '&lt;'),
	anchor("dailies/week/{$nav_next}", '&gt;')
];
foreach($anchors as $anchor) {
	printf('<button>%s</button>', $anchor);
}
?></div>
<?php $this->endSection();

$this->section('main');
echo $this->include('dailies/graphs');
echo $this->include('dailies/nav');
echo $this->include('dailies/table');
$this->endSection();