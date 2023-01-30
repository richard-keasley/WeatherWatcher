<?php $this->extend('template');
helper('form');
helper('inflector');

$dt_start = new \DateTime($start);
$dt_end = new \DateTime($end);
$format = 'j M y';
$title = sprintf('%s - %s', $dt_start->format($format), $dt_end->format($format));

$interval = $dt_start->diff($dt_end);
$oneday = new \DateInterval('P1D');

$datetime = new \DateTime($dt_start->format(DATE_W3C));
$prev_end = $datetime->sub($oneday)->format('Y-m-d');
$prev_start = $datetime->sub($interval)->format('Y-m-d');

$datetime = new \DateTime($dt_end->format(DATE_W3C));
$next_start = $datetime->add($oneday)->format('Y-m-d');
$next_end = $datetime->add($interval)->format('Y-m-d');

$this->section('header'); ?>
<h1>Daily: <?php echo $title;?></h1>
<?php $this->endSection();

$this->section('top'); ?>
<form method="GET">
<fieldset class="flex">
<?php

$inputs = [
	'start' => [
		'id' => "start",
		'type' => "date",
		'value' => $start
	],
	'end' => [
		'id' => "end",
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

echo '<button type="button" onclick="sendform()">OK</button>';

$tbody = [
	['Max span', $max_range->format('%d days')],
	['This span', $interval->days . ' days']
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

<script>
function sendform() {
var start = document.getElementById("start").value; 
var end = document.getElementById("end").value; 
window.location.href = '/dailies/custom/' + start + '/' + end;
}
</script>

</form>

<div class="navbar">
<?php
echo anchor("dailies/custom/{$prev_start}/{$prev_end}", ' &lt; ');
echo anchor("dailies/custom/{$next_start}/{$next_end}", ' &gt; ');

$types = ['temperature', 'rain', 'wind', 'solar'];
foreach($types as $type) {
	echo anchor("graph/dailies/{$type}/{$start}/{$end}", $type);
}
?>
</div>

<?php 
$this->endSection();

$this->section('main');
echo $this->include('dailies/table');
$this->endSection();

$this->section('bottom');
echo $this->include('dailies/nav');
$this->endSection();
