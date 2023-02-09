<?php $this->extend('template');

$this->section('main');
echo new \App\Views\Htm\custom('about');
$this->endSection();

$this->section('header'); ?>
<h1>About this station</h1>
<?php $this->endSection();

$this->section('bottom');
echo $this->include('includes/credits'); 
$this->endSection();
