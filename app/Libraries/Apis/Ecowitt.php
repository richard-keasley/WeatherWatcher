<?php 
namespace App\Libraries\Apis;

class Ecowitt  {
	
const baseURI = 'https://api.ecowitt.net/api/v3/';
	
const unitids = [
'temperature_C' => 1,
'temperature_F' => 2,
'pressure_hPa' => 3,
'pressure_inHg' => 4,
'pressure_mmHg' =>5,
'wind_speed_mps' => 6,
'wind_speed_kmph' => 7,
'wind_speed_knots' => 8,
'wind_speed_mph' => 9,
'wind_speed_BFT' => 10,
'wind_speed_fpm' => 11,
'rainfall_mm'  => 12,
'rainfall_in' => 13,
'solar_irradiance_lux' => 14,
'solar_irradiance_Wpm' => 16,
'solar_irradiance_fc' => 15
];

const paths = [
'device/real_time',
'device/history',
'device/info',
'device/list'
];
	
private $config = null;
private $response = [];
public $path = '';
public $query = [];

function __construct() {
	$this->config = config('Ecowitt');
}

function __get($key) {
	switch($key) {
		case 'response':
		return $this->response;
	}
	// default
	return $this->response[$key] ?? null;
}

public function call($path, $query) {
	$this->path = $path;
	$this->query = $query;
	
	$query['api_key'] = $this->config->api_key;
	$query['application_key'] = $this->config->application_key;
	$query['mac'] = $this->config->mac;
	
	$query['temp_unitid'] = self::unitids['temperature_C'];
	$query['pressure_unitid'] = self::unitids['pressure_hPa'];
	$query['rainfall_unitid'] = self::unitids['rainfall_mm'];

	$options = [
		'baseURI' => self::baseURI
	];
	$client = \Config\Services::curlrequest($options);
	$options = [
		'query' => $query
	];
	$response = $client->get($path, $options);
	$this->response = json_decode($response->getBody(), 1);
}

public function get_data() {
	if(!$this->data) return [];
	$ret = [];
	switch($this->path) {
		case 'device/history':
		foreach($this->data as $zone=>$types) {
			foreach($types as $type=>$readings) {
				$key = "{$zone}.{$type}";
				foreach($readings['list'] as $time=>$val) {
					$ret[$time][$key] = floatval($val);
				}
			}
		}
		return $ret;
	}
	return $this->data;
}

function get_daily($datetime) {
	$path = 'device/history';
	$query = [
		'start_date' => $datetime->format('Y-m-d 00:00:00'),
		'end_date' => $datetime->format('Y-m-d 23:59:59'),
		'cycle_type' => 'auto',
		'call_back' => 'outdoor,solar_and_uvi,rainfall,wind,pressure'
	];
	$this->call($path, $query);

	$day_data = $this->get_data();
	# d($day_data);
	
	$map = [
	'temperature' => ['outdoor.temperature', ['min', 'avg', 'max']],
	'humidity' => ['outdoor.humidity', ['min', 'avg', 'max']],
	'rain' => ['rainfall.daily', ['max']],
	'solar' => ['solar_and_uvi.solar', ['avg', 'max']],
	'uvi' => ['solar_and_uvi.uvi', ['avg', 'max']],
	'wind' => ['wind.wind_speed', ['avg', 'max']],
	];
	
	$daily = [
		'date' => $datetime->format('Y-m-d'),
		'count' => count($day_data)
	];
	if($daily['count']) {
		foreach($map as $dest=>$info) {
			$column = array_column($day_data, $info[0]);
			$sum = array_sum($column);
			foreach($info[1] as $param) {
				$daily["{$dest}_{$param}"] = match($param) {
					'min' => min($column),
					'avg' => $sum / $daily['count'],
					'max' => max($column)
				};
			}
		}
	}
	return new \App\Entities\Daily($daily);
}

}