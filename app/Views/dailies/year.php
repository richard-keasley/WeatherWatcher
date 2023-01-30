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

$this->section('main'); ?>
<div class="navbar">
<?php
echo anchor("dailies/year/{$nav_prev}", ' &lt; ');
echo anchor("dailies/year/{$nav_next}", ' &gt; ');
?>
</div>
<?php 
$this->endSection();

$this->section('main');
echo $this->include('dailies/graphs');
echo $this->include('dailies/nav');
# echo $this->include('dailies/table');
$this->endSection();
