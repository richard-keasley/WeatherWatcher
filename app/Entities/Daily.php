<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Daily extends Entity {
    
function table_cells() {
	$cells = [];
	foreach($this->toArray() as $key=>$value) {
		$arr = explode('_', $key);
		$datatype = $arr[0];
		$cell = \App\Views\Htm\table::cell($value, $datatype);
		if($datatype=='date' && $value) {
			$cell['data'] = anchor("dailies/day/{$value}", $cell['data']);
		}
		$cells[$key] = $cell;
	}
	return $cells;
}

function table_head() {
	$cells = [];
	foreach(array_keys($this->toArray()) as $key) {
		$arr = explode('_', $key);
		$datatype = $arr[0];
		$cells[$key] = implode('<br>', $arr);
	}
	return $cells;
}
}