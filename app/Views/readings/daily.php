<?php $this->extend('template');
$datetime = new \DateTime($start);
$interval = new \DateInterval("P1D");
$title = $datetime->format('j M Y');

$datetime->sub($interval);
$prev = $datetime->format('Y-m-d');

$datetime = new \DateTime($start);
$datetime->add($interval);
$next = $datetime->format('Y-m-d');

$reading = $readings->get_current();
$value = $reading->datetime ?? null;
$nav = new \DateTime($value);
$last = $nav ? $nav->format('Y-m-d') : '#' ;

$reading = $readings->get_first();
$value = $reading->datetime ?? null;
$nav = new \DateTime($value);
$first = $nav ? $nav->format('Y-m-d') : '#' ;

$this->section('header'); ?>
<h1>Readings for <?php echo $title;?></h1>
<?php $this->endSection();

$this->section('top'); ?>
<form method="GET" class="navbar">
<button><?php echo anchor("readings", ' back ');?></button>	
<button><?php echo anchor("readings/daily/{$first}", ' |&lt; ');?></button>
<button><?php echo anchor("readings/daily/{$prev}", ' &lt; ');?></button>
<button><?php echo anchor("readings/daily/{$next}", ' &gt; ');?></button>
<button><?php echo anchor("readings/daily/{$last}", ' &gt;| ');?></button>
</form>
<?php $this->endSection();

$this->section('main'); ?>
<div class="float-start">
<?php echo $this->include('dailies/daily'); ?>
</div>
<?php 
if($daily->count > 3) {
	echo $this->include('readings/graphs');
}
$this->endSection();
