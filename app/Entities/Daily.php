<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Daily extends Entity {
    
function table_cells() {
	$cells = [];
	foreach($this->toArray() as $key=>$val) {
		$arr = explode('_', $key);
		$datatype = $arr[0];
		$cells[$key] = \App\Views\Htm\table::cell($val, $arr[0]);
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