<?php $this->extend('template');
helper('form');
helper('inflector');
$interval = new \DateInterval('P1Y');
$datetime = new \DateTime($start);
$title = $datetime->format('Y');
$datetime->sub($interval);
$nav_prev = $datetime->format('Y-01-01');

$datetime = new \DateTime($start);
$datetime->add($interval);
$nav_next = $datetime->format('Y-01-01');

$this->section('header'); ?>
<h1>Daily: <?php echo $title;?></h1>
<?php $this->endSection();

$this->section('top'); ?>
<div class="navbar"><?php
$anchors = [
	anchor("dailies/year/{$nav_prev}", '&lt;'),
	anchor("dailies/year/{$nav_next}", '&gt;')
];
foreach($anchors as $anchor) {
	printf('<button>%s</button>', $anchor);
}
?></div>
<?php $this->endSection();

$this->section('main');
echo $this->include('dailies/graphs');
echo $this->include('dailies/nav');
# echo $this->include('dailies/table');
$this->endSection();
