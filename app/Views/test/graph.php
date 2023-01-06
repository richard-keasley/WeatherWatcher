<?php $this->extend('template');
helper('form');
helper('inflector');

$table = new \CodeIgniter\View\Table();

$this->section('header'); ?>
<h1>JP graph</h1>
<?php $this->endSection();

$this->section('main');



$this->endSection();
