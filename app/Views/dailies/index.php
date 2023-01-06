<?php $this->extend('template');
helper('form');
helper('inflector');

$this->section('header'); ?>
<h1>Daily archive</h1>
<?php $this->endSection();

$this->section('main'); ?>
<form method="GET">
<?php
$inputs = [
	'start' => [
		'name' => "start",
		'type' => "date",
		'value' => $start 
	],
	'end' => [
		'name' => "end",
		'type' => "date",
		'value' => $end 
	]
];
foreach($inputs as $label=>$input) {
	printf('<p><label>%s</label> %s</p>', 
	humanize($label),
	form_input($input));
}
?>
<fieldset>
	<button type="submit">OK</button>
</fieldset>
</form>
<?php 

$tbody = []; $thead = false;
foreach($datarows as $daily) {
	$row = $daily->table_cells();
	if(!$thead) $thead = $daily->table_head();
	$tbody[] = $row;
}
$table = new \CodeIgniter\View\Table();
$table->setHeading($thead);
echo $table->generate($tbody);

$this->endSection();