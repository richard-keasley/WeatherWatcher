<?php $this->extend('template');
helper('form');
helper('inflector');
$interval = new \DateInterval('P1M');
$datetime = new \DateTime($start);
$title = $datetime->format('F Y');
$datetime->sub($interval);
$nav_prev = $datetime->format('Y-m-01');

$datetime = new \DateTime($start);
$datetime->add($interval);
$nav_next = $datetime->format('Y-m-01');

$this->section('header'); ?>
<h1>Dailies - <?php echo $title;?></h1>
<?php $this->endSection();

$this->section('main'); ?>
<div class="navbar">
<?php
echo anchor("dailies/month/{$nav_prev}", ' &lt; ');
echo anchor("dailies/month/{$nav_next}", ' &gt; ');

$types = ['temperature', 'rain'];
foreach($types as $type) {
	echo anchor("graph/dailies/{$type}/{$start}/{$end}", $type);
}
?>
</div>
<?php 

$tbody = []; $thead = false;
foreach($dailies as $daily) {
	$row = $daily->table_cells();
	if(!$thead) $thead = $daily->table_head();
	$tbody[] = $row;
}
$table = \App\Views\Htm\table::load();
$table->setHeading($thead);
echo $table->generate($tbody);

$this->endSection();

$this->section('bottom');
echo $this->include('dailies/nav');
$this->endSection();