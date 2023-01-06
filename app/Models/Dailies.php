<?php namespace App\Models;

use CodeIgniter\Model;

class Dailies extends Model {

protected $table = 'dailies';
protected $returnType = \App\Entities\Daily::class;
protected $allowedFields = [
	'date', 'count', 
	'temperature_min', 'temperature_avg', 'temperature_max',
	'humidity_min', 'humidity_avg', 'humidity_max',
	'rain_max',
	'solar_avg', 'solar_max',
	'uvi_avg', 'uvi_max',
	'wind_avg', 'wind_max'
];
  
}
