<?php $this->extend('template');

$interval = new \DateInterval('P1M');
$datetime = new \DateTime($start);
$title = $datetime->format('F Y');
$datetime->sub($interval);
$nav_prev = $datetime->format('Y-m-01');

$datetime = new \DateTime($start);
$datetime->add($interval);
$nav_next = $datetime->format('Y-m-01');

$this->section('header'); ?>
<h1>Daily: month <?php echo $title;?></h1>
<?php $this->endSection();

$this->section('top'); ?>
<div class="navbar"><?php
$anchors = [
	anchor("dailies/month/{$nav_prev}", '&lt;'),
	anchor("dailies/month/{$nav_next}", '&gt;')
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
