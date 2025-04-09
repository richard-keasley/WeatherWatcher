<?php 
namespace App\Libraries\Listeners;

class Ecowitt extends Listener {

protected $map = [
'temperature' => [
	'in' => 'tempinf',
	'out' => 'tempf'
],

'pressure' => [
	'rel' => 'baromrelin',
	'abs' => 'baromabsin'
],

'humidity' => [
	'in' => 'humidityin',
	'out' => 'humidity',
],

'wind' => [
	'bearing' => 'winddir',
	'dir' => 'winddir',
	'speed' => 'windspeedmph',
	'gust' => 'windgustmph',
	'daily_gust' => 'maxdailygust'
],

'rain' => [
	'rate' => 'rainratein',
	'event' => 'eventrainin',
	'hour' => 'hourlyrainin',
	'day' => 'dailyrainin',
	'week' => 'weeklyrainin',
	'month' => 'monthlyrainin',
	'year' => 'yearlyrainin',
	'total' => 'totalrainin'
],

'solar' => [
	'radiation' => 'solarradiation',
	'uv' => 'uv'
],

'station' => [
	'battery' => 'wh65batt',
	'frequency' => 'freq',
	'model' => 'model',
	'type' => 'stationtype',
	'datetime' => 'dateutc',
	'runtime' => 'runtime',
]
];

protected $units = [
'temperature' => 'degf',
'pressure' => 'inhg',
'wind' => [
	'bearing' => 'bearing',
	'dir' => 'compass',
	'speed' => 'mph',
	'gust' => 'mph',
	'daily_gust' => 'mph'
],
'rain' => [
	'rate' => 'in/hr',
	'event' => 'in',
	'hour' => 'in',
	'day' => 'in',
	'week' => 'in',
	'month' => 'in',
	'year' => 'in',
	'total' => 'in'
]
];

function get_datetime() {
	// datetime is saved as UTC/GMT string
	// don't convert it to local time 
	$datetime = $this->inputs['dateutc'] ?? null;
	return date(self::dt_format, strtotime($datetime));
}

}
