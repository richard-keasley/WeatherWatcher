<?php 
$data = [];
foreach($dailies as $daily) {
	$data[] = $daily->toArray();
}

if($data) {
	$table = \App\Views\Htm\table::load();
	
	$tfoot = []; $thead = [];
	foreach(array_keys($data[0]) as $key) {
		$thead[$key] = str_replace('_', '<br>', $key);
		
		$arr = explode('_', $key);
		$datatype = match($key) {
			'date' => 'number',
			default => $datatype = $arr[0]
		};
		
		$aggtype = match($key) {
			'date' => 'count',
			'count' => 'sum',
			'rain_max' => 'sum',
			default => false
		};
		if(!$aggtype) $aggtype = $arr[1] ?? '';
		
		$col_data = array_column($data, $key);
		$val = match($aggtype) {
			'count' => count($col_data),
			'sum' => array_sum($col_data),
			'avg' => array_sum($col_data) / count($col_data),
			'min' => min($col_data),
			'max' => max($col_data),
			default => "?{$aggtype}"
		};
		$tfoot[$key] = \App\Views\Htm\table::cell($val, $datatype);		
	}
	$table->setFooting($tfoot);
	$table->setHeading($thead);
	
	$tbody = []; $cells = [];
	foreach($data as $row) {
		foreach($row as $key=>$val) {
			$arr = explode('_', $key);
			$datatype = $arr[0];
			$cell = \App\Views\Htm\table::cell($val, $datatype);
			if($datatype=='date' && $val) {
				$cell['data'] = anchor("dailies/day/{$val}", $cell['data']);
			}
			$cells[$key] = $cell;
		}
		$tbody[] = $cells;
	}
		
	echo $table->generate($tbody);
}
else {
	echo new \App\Views\Htm\alert('No data');
}
