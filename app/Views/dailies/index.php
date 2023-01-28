<?php $this->extend('template');
helper('form');
helper('inflector');

$this->section('header'); ?>
<h1>Daily archive</h1>
<?php $this->endSection();

$this->section('bottom');
echo $this->include('dailies/nav');
$this->endSection();

$this->section('main');
echo $this->include('widgets/daily');
$this->endSection();