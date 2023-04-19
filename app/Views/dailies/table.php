<?php 
$tbody = []; 
foreach($dailies as $rowkey=>$daily) {
	$row = $daily->table_cells();
	if(!$rowkey) {
		$thead = $daily->table_head();
	}
	
	$tbody[] = $row;
}
if($tbody) {
	$table = \App\Views\Htm\table::load();
	foreach(array_keys($tbody[0]) as $key) {
		$column = array_column($tbody, $key);
		$col_data = is_array($column[0]) ? array_column($column, 'data') : $column;
		# d($key, $column, $col_data);
		
		$type = match($key) {
			'date' => 'count',
			'count' => 'sum',
			'rain_max' => 'sum',
			default => null
		};
		if(!$type) {
			$arr = explode('_', $key);
			$type = $arr[1] ?? '';
		}
		$tfoot[$key] = match($type) {
			'count' => count($col_data),
			'sum' => array_sum($col_data),
			'avg' => array_sum($col_data) / count($col_data),
			'min' => min($col_data),
			'max' => max($col_data),
			default => "?{$type}"
		};
	}
	$table->setFooting($tfoot);
	$table->setHeading($thead);
	
	echo $table->generate($tbody);
}
else {
	echo new \App\Views\Htm\alert('No data');

}