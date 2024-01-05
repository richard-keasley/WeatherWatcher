<?php $this->extend('template');

$datetime = new \DateTime($start);
$title = $datetime->format('Y');
$interval = new \DateInterval('P1Y');

$nav_prev = $datetime->sub($interval)->format('Y-01-01');
$datetime = new \DateTime($start);
$nav_next = $datetime->add($interval)->format('Y-01-01');

$this->section('header'); ?>
<h1>Daily: year <?php echo $title;?></h1>
<?php $this->endSection();

$this->section('top'); ?>
<div class="navbar"><?php

$input = [
	'type' => "date",
	'value' => $start,
	'onchange' => "getdate(this.value)"
];

$anchors = [
	anchor("dailies/year/{$nav_prev}", '&lt;'),
	anchor("dailies/year/{$nav_next}", '&gt;'),
	form_input($input)
];
foreach($anchors as $anchor) {
	printf('<button>%s</button>', $anchor);
}
?>
<script>
function getdate(value) {
	var href = '<?php echo base_url('dailies/year'); ?>/' + value;
	window.location.href = href;
}
</script>
</div>
<?php $this->endSection();

$this->section('main');
echo $this->include('dailies/graphs');
echo $this->include('dailies/nav');
# echo $this->include('dailies/table');
$this->endSection();
