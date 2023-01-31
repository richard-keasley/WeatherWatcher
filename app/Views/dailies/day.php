<?php $this->extend('template');
helper('form');
helper('inflector');

$interval = new \DateInterval('P1D');
$datetime = new \DateTime($start);
$title = $datetime->format('j F Y');
$datetime->sub($interval);
$nav_prev = $datetime->format('Y-m-d');

$datetime = new \DateTime($start);
$datetime->add($interval);
$nav_next = $datetime->format('Y-m-d');

$this->section('header'); ?>
<h1>Daily archive - <?php echo $title;?></h1>
<?php $this->endSection();

$this->section('top'); ?>
<div class="navbar">
<?php
echo anchor("dailies/day/{$nav_prev}", ' &lt; ');
echo anchor("dailies/day/{$nav_next}", ' &gt; ');
?>
</div>
<?php 
$this->endSection();

$this->section('bottom');
echo $this->include('dailies/nav');
$this->endSection();

$this->section('main');
echo $this->include('widgets/daily');
$this->endSection();