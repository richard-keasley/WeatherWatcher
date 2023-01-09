<?php $this->extend('template');

$this->section('header'); ?>
<h1>Daily reading</h1>
<?php $this->endSection();

$this->section('main'); 

$datetime = new \DateTime($date);
$interval = new \DateInterval("P1D");
$datetime->sub($interval);
$prev = $datetime->format('Y-m-d');
$datetime = new \DateTime($date);
$datetime->add($interval);
$next = $datetime->format('Y-m-d');

?>
<form method="GET">
<button><?php echo anchor("readings/daily/{$prev}", ' &lt; ');?></button>
<button><?php echo anchor("readings/daily/{$next}", ' &gt; ');?></button>
<button><?php echo anchor("readings", ' back ');?></button>	
</form>

<?php
echo $this->include('widgets/daily');

$this->endSection();
