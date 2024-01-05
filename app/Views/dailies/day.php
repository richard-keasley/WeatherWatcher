<?php $this->extend('template');

$datetime = new \DateTime($start);
$title = $datetime->format('j F Y');
$interval = new \DateInterval('P1D');

$nav_prev = $datetime->sub($interval)->format('Y-m-d');
$datetime = new \DateTime($start);
$nav_next = $datetime->add($interval)->format('Y-m-d');

$this->section('header'); ?>
<h1>Daily archive - <?php echo $title;?></h1>
<?php $this->endSection();

$this->section('top'); ?>
<div class="navbar"><?php

$input = [
	'type' => "date",
	'value' => $start,
	'onchange' => "getdate(this.value)"
];

$anchors = [
	anchor("dailies/day/{$nav_prev}", '&lt;'),
	anchor("dailies/day/{$nav_next}", '&gt;'),
	form_input($input)
];
foreach($anchors as $anchor) {
	printf('<button>%s</button>', $anchor);
}
?>
<script>
function getdate(value) {
	var href = '<?php echo base_url('dailies/day'); ?>/' + value;
	window.location.href = href;
}
</script>

</div>
<?php $this->endSection();

$this->section('bottom');
echo $this->include('dailies/nav');
$this->endSection();

$this->section('main');
echo $this->include('dailies/daily');
$this->endSection();