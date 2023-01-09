<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Reading extends Entity {

protected $casts = [
	'server'	=> 'json-array',
	'readings'	=> 'json-array',
	'inputs'	=> 'json-array',
];

function get_datetime($format=null) {
	// datetime is saved as UTC/GMT string
	// convert to local time
	$tz_utc = new \DateTimeZone('UTC');
	$tz_local = new \DateTimeZone(app_timezone());
	$datetime = new \DateTime($this->datetime, $tz_utc);
	$datetime->setTimezone($tz_local);
	return $format ? $datetime->format($format) : $datetime ;
}

const formats = [
'temperature' => '%.1f&deg;C',
'pressure' => '%umbar',
'humidity' => '%u%%',
'wind' => [
	'bearing' => '%u&deg;',
	'speed' => '%.1f mph',
	'gust' => '%.1f mph',
	'daily_gust' => '%.1f mph'
],
'rain' => [
	'rate' => '%.1f mm/hr',
	'event' => '%.1f mm',
	'hour' => '%.1f mm',
	'day' => '%.1f mm',
	'week' => '%.1f mm',
	'month' => '%.1f mm',
	'year' => '%.1f mm',
	'total' => '%.1f mm'
],
'solar' => [
	'radiation' => '%.2f W/m<sup>2</sup>',
	'uv' => '%.2f'
]
];
static function format($section, $key) {
	$formats = self::formats[$section] ?? '%s';
	if(!is_array($formats)) return $formats;
	return $formats[$key] ?? '%s';
}

}