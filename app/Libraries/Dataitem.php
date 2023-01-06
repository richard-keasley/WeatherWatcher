<?php

// obsolete
// delete this file 


namespace App\Libraries;

class Dataitem implements \stringable {

private $start = [];
private $value = [];

function __construct($value, $unit='') {
	$this->start = ['value'=>$value, 'unit'=>$unit];
	$this->value = self::convert($this->start);
}

function __get($key) {
	return match($key) {
		'start' => $this->start,
		'value' => $this->value,
		default => null
	};
}

function __toString() {
	return implode('', $this->value);
}

static function convert($start) {
	$startval = $start['value'] ?? null;
	if(is_null($startval)) return $start;
	$startunit = $start['unit'] ?? null;
	if(is_null($startunit)) $start;

	return match($startunit) {
		'pc' => [
			'value' => round($startval), 
			'unit' => '%'
		],
		'W_m' => [
			'value' => $startval, 
			'unit' => 'W/m<sup>2</sup>'
		],
		'compass' => [
			'value' => self::deg2dir($startval), 
			'unit' => ''
		],
		'bearing' => [
			'value' => $startval, 
			'unit' => '&deg;'
		],
		'degf' => [
			'value' => round(($startval - 32) * 5/9, 1), 
			'unit'=>'&deg;C'
		],
		'in' => [
			'value'=>round(25.4 * $startval, 1), 
			'unit'=>'mm'
		],
		'in_hr' => [
			'value'=>round(25.4 * $startval, 1), 
			'unit'=>'mm/hr'
		],
		'inhg' => [
			'value'=>round(33.8638816 * $startval), 
			'unit'=>'mbar'
		],
		default => [
			'value'=>$startval, 
			'unit'=>$startunit
		]
	};
}

static function deg2dir($deg) {
	$directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW', 'N'];
	// correct angles outside range 0-360
	$deg = ($deg - 360 * floor($deg / 360));
	$key = round($deg / 22.5);
	return $directions[$key] ?? '?';
}

}