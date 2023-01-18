<?php $this->extend('template');
helper('form');
helper('inflector');

$this->section('header'); ?>
<h1>Daily archive</h1>
<?php $this->endSection();

$this->section('main'); ?>
<form method="GET">
<fieldset>
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
	printf('<span><label>%s</label> %s</span> ', 
	humanize($label),
	form_input($input));
}
?>
<span>Max time span: <?php echo $max_range->format('%d days');?>.</span> 
<span>This range: <?php echo $this_range->format('%d days');?>.</span> 
</fieldset>

<fieldset>
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
$table = new \CodeIgniter\View\Table();
$table->setHeading($thead);
echo $table->generate($tbody);

$this->endSection();