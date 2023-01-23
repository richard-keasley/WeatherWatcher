<?php $this->extend('template');
helper('form');
helper('inflector');

$this->section('header'); ?>
<h1>Daily archive</h1>
<?php $this->endSection();

$this->section('main'); ?>
<form method="GET">
<fieldset class="flex">
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
$tbody = [];
foreach($inputs as $label=>$input) {
	$tbody[] = [
		"<label>{$label}</label>",
		form_input($input)
	];
}
$table = \App\Views\Htm\table::load('list');
$table->autoHeading = false;
echo $table->generate($tbody);

$tbody = [
	['Max span', $max_range->format('%d days')],
	['This span', $this_range->days . ' days']
];
$table->autoHeading = false;
echo $table->generate($tbody);

$tbody = [
	['First daily:', $dt_first->format('Y-m-d')],
	['Last daily:', $dt_last->format('Y-m-d')]
];
$table->autoHeading = false;
echo $table->generate($tbody);
?>
</fieldset>

<fieldset class="navbar">
<button type="submit">OK</button>
<button type="submit" name="nav" value="prev"> &lt; </button>
<button type="submit" name="nav" value="next"> &gt; </button>
</fieldset>

</form>
<?php 

$tbody = []; $thead = false;
foreach($datarows as $daily) {
	$row = $daily->table_cells();
	if(!$thead) $thead = $daily->table_head();
	$tbody[] = $row;
}
$table = \App\Views\Htm\table::load();
$table->setHeading($thead);
echo $table->generate($tbody);

$this->endSection();