<?php $this->extend('template');

$this->section('header'); ?>
<h1>Daily: <?php echo $title;?></h1>
<?php $this->endSection();

$this->section('top'); 
$this->endSection();

$this->section('main');
echo $this->include('dailies/table');
$this->endSection();

$this->section('bottom');
echo $this->include('dailies/nav');
$this->endSection();
