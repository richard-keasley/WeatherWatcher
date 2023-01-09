<?php $this->extend('template');

$this->section('header'); ?>
<h1>Readings</h1>
<?php $this->endSection();

$this->section('main'); ?>


<form method="GET">
<?php 
helper('form');
$input = [
	'type' => 'hidden',
	'name' => 'dt',
	'value' => $dt
];
echo form_input($input);
?>
<button type="submit" name="nav" value="first"> |&lt; </button>
<button type="submit" name="nav" value="prev"> &lt; </button>
<button type="submit" name="nav" value="next"> &gt; </button>
<button type="submit" name="nav" value="last"> &gt;| </button>
<button type="button"> <?php 
	echo anchor("readings/daily/{$date}", 'daily');
?> </button>
</form>

<section>
<?php echo $this->include('widgets/reading'); ?>
</section>

<?php 
$this->endSection();
