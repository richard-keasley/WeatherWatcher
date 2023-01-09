<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Daily extends Entity {
	
const data_formats = [
	'temperature' => '%.1f',
	'wind' => '%.1f',
	'humidity' => '%.0f',
	'rain' => '%.1f'
];
    
function table_cells() {
	$cells = [];
	foreach($this->toArray() as $key=>$val) {
		$arr = explode('_', $key);
		$datatype = $arr[0];
		$format = self::data_formats[$datatype] ?? null;
		
		if(is_null($val)) {
			$val = '-';
		}
		elseif($format) {
			$val = sprintf($format, $val);
		}
		$attrs = $format ? ['style'=>"text-align:right;"] : [] ;
		$attrs['data'] = $val;
		
		$cells[$key] = $attrs;
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