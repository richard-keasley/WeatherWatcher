<?php $this->extend('template');

$this->section('header'); ?>
<h1>Current conditions</h1>
<?php $this->endSection();

$this->section('main'); ?>

<p>This site is under construction. View the 
<a href ="https://wow.metoffice.gov.uk/observations/details?site_id=b6e970c1-ced2-e611-9400-0003ff5991ab">Met. Office website</a>
for live updates from this weather station.</p> 
 
<section>
<h4><?php echo anchor('readings', 'Current readings');?></h4>
<?php 
echo view('widgets/reading', ['reading'=>$readings->get_current()]);
?>
</section>

<?php 

echo $this->include('widgets/sun');

echo $this->include('widgets/tides');
echo $this->include('widgets/weather');
echo $this->include('widgets/moon');

 ?>
<section>
<h4>Navigation</h4>
<ul>
<li><?php echo anchor('readings', 'Current readings');?></li>
<li><?php echo anchor('dailies', 'Dailies');?></li>
</ul>
</section>

<section>
<h4>credits</h4>
<ul>
<?php
$ul = [
	['https://moonphases.co.uk/', 'moon images'],
	['http://www.astropixels.com/', 'Solstice info'],
	['https://jpgraph.net/', 'JP graph'],
	['https://github.com/Moskito89/php-moon-phase', 'PHP moon phase calcuations']
];
foreach($ul as $li) {
	printf('<li>%s</li>', \anchor($li[0], $li[1]));
}
?></ul>
</section>

<?php 

echo anchor('dailies', 'Dailies');



$this->endSection();
