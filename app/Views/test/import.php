<?php $this->extend('template');
helper('form');
helper('inflector');

$table = new \CodeIgniter\View\Table();

$this->section('header'); ?>
<h1>Import data</h1>
<p>Importing from old website. Disable this page once we're done.</p>
<?php $this->endSection();

$this->section('main');

echo form_open(); ?>
<fieldset>
	<button type="submit">RUN</button>
	<button name="commit" value="1" type="submit">COMMIT</button>
</fieldset>
<?php echo form_close();

if($datarows) {
	$thead = array_keys($datarows[0]);
	$table = new \CodeIgniter\View\Table();
	$table->autoHeading = false;
	$table->setHeading($thead);
	echo $table->generate($datarows);
} 
else { ?>
	<ol><?php
	foreach($datafiles as $file) {
		printf('<li>%s</li>', $file->getBasename());
	} ?></ol>
<?php }

$this->endSection();
