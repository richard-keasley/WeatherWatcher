<?php $this->extend('template');

$this->section('header'); ?>
<h1>Testing</h1>
<?php $this->endSection();

$this->section('main'); ?>
<p><strong>Warning:</strong> Exit development mode once you have finished testing.</p> 

<ul><?php
$path = realpath(APPPATH . '/Controllers/Test');
foreach(glob("{$path}/*") as $file) {
	$label = strtolower(basename($file, '.php'));
	printf('<li>%s</li>', anchor("test/{$label}", $label));
}
?></ul>
<?php $this->endSection();
