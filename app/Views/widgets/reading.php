<?php
if(!$reading) return;

printf('<p>Time: %s</p>', $reading->get_datetime('j M Y H:i'));

$table = new \CodeIgniter\View\Table();
$tbody = [];
foreach($reading->readings as $section=>$values) {
	foreach($values as $key=>$value) {
		
		// remove this once readings are finished
		if(is_array($value)) {
			$value = $value['value'] ?? '';
		}
		
		$label = "{$section} {$key}";
		$format = $reading::format($section, $key);
		$value = sprintf($format, $value);
		$tbody[] = [$label, $value];
	}
}
$table->autoHeading = false;
echo $table->generate($tbody);