<?php
if(!$reading) return;
?>
<section>
<?php
printf('<p>Time: %s</p>', $reading->get_datetime('j M Y H:i'));

$tbody = [];
foreach($reading->readings as $section=>$values) {
	foreach($values as $key=>$value) {
		
		// remove this once readings are finished
		if(is_array($value)) {
			$value = $value['value'] ?? '';
		}
		
		$label = "{$section} {$key}:";
		$format = $reading::format($section, $key);
		$value = sprintf($format, $value);
		$tbody[] = [$label, $value];
	}
}

$table = \App\Views\Htm\table::load('list');
$table->autoHeading = false;
echo $table->generate($tbody);

?>
</section>