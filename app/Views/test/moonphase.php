<?php $this->extend('template');

$this->section('header'); ?>
<h1>Moon phase</h1>
<?php $this->endSection();

$this->section('main');
\App\ThirdParty\mooninfo::autoload();
echo \basecamp\mooninfo::example(0);
$this->endSection();
