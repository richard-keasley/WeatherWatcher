<?php $this->extend('template');

$this->section('header'); 
$datetime = new \DateTime($start);
$title = $datetime->format('j F Y');
?>
<h1>Daily archive - <?php echo $title;?></h1>
<?php $this->endSection();

$this->section('top'); ?>
<div class="navbar"><?php
$interval = new \DateInterval('P1D');
$datetime = new \DateTime($start);
$nav_prev = $datetime->sub($interval)->format('Y-m-d');
$datetime = new \DateTime($start);
$nav_next = $datetime->add($interval)->format('Y-m-d');

$anchors = [
	anchor("dailies/day/{$nav_prev}", '&lt;'),
	anchor("dailies/day/{$nav_next}", '&gt;')
];
foreach($anchors as $anchor) {
	printf('<button>%s</button>', $anchor);
}
?></div>
<?php $this->endSection();

$this->section('bottom');
echo $this->include('dailies/nav');
$this->endSection();

$this->section('main');
echo $this->include('dailies/daily');
$this->endSection();