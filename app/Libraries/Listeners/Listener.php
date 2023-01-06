<?php 
namespace App\Libraries\Listeners;

class Listener {

// only collecting these fields
const fields = [
'temperature' => ['in', 'out'],
'pressure' => ['rel', 'abs'],
'humidity' => ['in', 'out'],
'wind' => ['bearing', 'dir', 'speed', 'gust', 'daily_gust'],
'rain' => ['rate', 'event', 'hour', 'day', 'week', 'month', 'year', 'total'],
'solar' => ['radiation', 'uv'],
'station' => ['type', 'model', 'datetime', 'battery', 'frequency', 'runtime']
];
const dt_format = 'Y-m-d H:i:s';

protected $map = []; // input field to output field
protected $units = []; // input units (converted when received)
protected $keys = []; // check these fields to allow POST

protected $data = []; // data from POST

protected $readonly = [
	'error' => ''
];

public $inputs = []; // process these inputs

function __construct() {
	$config = config('Listeners');
	
	// set keys
	$cfg = $config->keys;
	$names = explode($cfg['separator'], $cfg['names']);
	$values = explode($cfg['separator'], $cfg['values']);
	foreach($names as $key=>$name) {
		$name = trim($name);
		if($name) $this->keys[$name] = $values[$key] ?? '' ;
	}
}

function __get($key) {
	return $this->readonly[$key] ?? null;
}

function set_keys($keys) {
	$this->keys = $keys;
}

function get_keys() {
	return ENVIRONMENT==='development' ? $this->keys : [] ;
}

function get_datetime() {
	// datetime is saved as UTC/GMT string
	// don't convert it to local time 
	return date(self::dt_format);
}

function check_keys() {
	foreach($this->keys as $key=>$d_val) {
		$s_val = $this->inputs[$key] ?? null;
		if($s_val!==$d_val) {
			$this->readonly['error'] = 'Unauthorised';
			return false;
		}
	}
	return true;
}

function process() {
	$retval = [
		'datetime' => $this->get_datetime(),
		'inputs' => $this->inputs,
		'readings' => []
	];
	foreach(self::fields as $d_sec=>$section) {
		$units = $this->units[$d_sec] ?? null;
		foreach($section as $d_key) {
			$s_key = $this->map[$d_sec][$d_key] ?? null;
			$s_val = $retval['inputs'][$s_key] ?? null ;
			$unit = is_array($units) ? $units[$d_key] ?? null : $units;
			$retval['readings'][$d_sec][$d_key] = self::convert($s_val, $unit);
		}
	}
	return $retval;
}

static function convert($startval, $unit=null) {
	if(is_null($startval)) return $startval;
	if(!is_numeric($startval)) return $startval;
	
	$startval = floatval($startval);
	return match($unit) {
		'compass' => self::deg2dir($startval), // angle to direction
		'degf' => round(($startval - 32) * 5/9, 1), // F to C
		'in' => round(25.4 * $startval, 1), // in to mm
		'in/hr' => round(25.4 * $startval, 1),  // in/hr to mm/hr
		'inhg' => round(33.8638816 * $startval), // in Hg to mbar
		default => $startval // untouched 
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