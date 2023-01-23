<?php namespace App\Views\Htm;

class table {
const templates = [
'default' => [
	'table_open' => '<div class="table-responsive"><table class="table">',
	'table_close' => '</table></div>'
],
'list' => [
	'table_open' => '<table class="table-list">'
],
'striped' => [
	'table_open' => '<table class="table table-striped">'
],
'bordered' => [
	'table_open' => '<table class="table">',
	'table_close' => '</table>'
],
'fixed' => [
	'table_open' => '<table class="table text-center" style="table-layout:fixed;">'
]
];

static function load($tkey = 'default') {
	return new \CodeIgniter\View\Table(self::templates[$tkey]);
}

/* return formatted table cell  */
static function cell($value, $format='') {
	switch($format) {
		case 'number':
		return [
			'data' => intval($value),
			'class' => "text-end"
		];
		
		case 'wind':
		case 'uvi':
		case 'solar':
		case 'rain':
		case 'temperature':
		return [
			'data' => sprintf('%.1f', $value),
			'class' => "text-end"
		];
		
		case 'humidity':
		return [
			'data' => $value ? round($value) : '-' ,
			'class' => "text-end"
		];
		
		case 'datetime':
		if(!$value) return '';
		$datetime = new \datetime($value);
		return $datetime->format('j M Y H:i');
		
		case 'date':
		if(!$value) return '';
		$datetime = new \datetime($value);
		return [
			'data' => $datetime->format('j M Y'),
			'class' => "text-end"
		];
		
		case 'bool': 
		return $value ? 'yes' : 'no' ;
	}
	return $value;
}
	
}
