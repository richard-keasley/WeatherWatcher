<?php
if(!$reading) return;
?>
<section>
<?php
printf('<p>Time: %s</p>', $reading->get_datetime('j M Y H:i'));

$tbody = [];
foreach($reading->get_readings(true) as $key=>$value) {
	$tbody[] = [humanize($key), $value];
}

$table = \App\Views\Htm\table::load('list');
$table->autoHeading = false;
echo $table->generate($tbody);

?>
</section>