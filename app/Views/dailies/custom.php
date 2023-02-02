<?php $this->extend('template');

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
$tbody[] = ['', '<button type="button" onclick="sendform()">OK</button>'];
$table = \App\Views\Htm\table::load('list');
$table->autoHeading = false;
echo $table->generate($tbody);

$tbody = [
	['Max span:', $max_range->format('%d days')],
	['This span:', $interval->days . ' days'],
	['First daily:', $dt_first->format('d/m/y')],
	['Last daily:', $dt_last->format('d/m/y')]
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
$anchors = [
	anchor("dailies/custom/{$prev_start}/{$prev_end}", ' &lt; '),
	anchor("dailies/custom/{$next_start}/{$next_end}", ' &gt; ')
];

$types = ['temperature', 'rain', 'wind', 'solar'];
foreach($types as $type) {
	$anchors[] = anchor("graph/dailies/{$type}/{$start}/{$end}", $type);
}
foreach($anchors as $anchor) {
	printf('<button>%s</button>', $anchor);
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
