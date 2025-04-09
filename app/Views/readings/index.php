<?php $this->extend('template');

$this->section('header'); ?>
<h1>Conditions (<?php echo $reading->get_datetime('j F Y H:i');?>)</h1>
<?php $this->endSection();

$this->section('top'); ?>
<form method="GET" class="navbar">
<?php
$input = [
	'type' => 'hidden',
	'name' => 'dt',
	'value' => $dt
];
echo form_input($input);
?>
<button><?php echo anchor("", 'home'); ?></button>
<button type="submit" name="nav" value="first">|&lt;</button>
<button type="submit" name="nav" value="prev">&lt;</button>
<button type="submit" name="nav" value="next">&gt;</button>
<button type="submit" name="nav" value="last">&gt;|</button>
<button><?php echo anchor("readings/daily/{$date}", 'daily');?></button>
</form>
<?php 
$this->endSection();

$this->section('main');
echo $this->include('readings/reading'); 
$this->endSection();
