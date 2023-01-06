<?php

/**
 * Sun info
 * PHP's native functions
 * https://www.php.net/manual/en/function.date-sun-info.php
 * solstice times
 * http://www.astropixels.com/ephemeris/soleq2001.html
 */

namespace basecamp;

class suninfo {
	
private $solstices = [];
private $params = [];
private $info = [];

function __construct($timestamp=null, $latitude=0, $longitude=0) {
	if(!$timestamp) $timestamp = time();
	$this->params = [
		'timestamp' => $timestamp,
		'latitude' => $latitude,
		'longitude' => $longitude
	];
	$this->info = \date_sun_info($timestamp, $latitude, $longitude);
	
	$this->datetime = new \DateTime();
	
	$source = __DIR__ . '/solstices.json';
	try {
		$string = file_get_contents($source);
		$this->solstices = json_decode($string);
	}
	catch (\Exception $e){
		echo $e->getMessage();
		$this->solstices = [];
	}
}

function __get($key) {
	if($key=='solstices') return $this->solstices;
	if($key=='info') return $this->info;
	if(isset($this->info[$key])) return $this->info[$key];
	return $this->params[$key] ?? null;
}

const credit = '<p>Solstice and Equinox table courtesy of Fred Espenak <a href="http://www.astropixels.com/ephemeris/soleq2001.html">www.astropixels.com</a>.</p>';

static function compile() {
/*
read solstices.txt into json array

All calculations are by Fred Espenak, and he assumes full 
responsibility for their accuracy. Algorithms used in 
predicting Earth's solstices and equinoxes are based on 
Astronomical Algorithms by Jean Meeus (Willmann-Bell, 
Inc., Richmond, 1998). 

Permission is freely granted to reproduce this data when 
accompanied by the acknowledgment: 
"Solstice and Equinox Table Courtesy of Fred Espenak, 
www.Astropixels.com".
*/
	
	// [date parts], [time parts]
	$map = [
		3 => [[2, 1, 0], [3]],
		6 => [[5, 4, 0], [6]],
		9 => [[8, 7, 0], [9]],
		12=> [[11, 10, 0], [12]]
	];
	$solstices = [];
	$dt_arr = [];
	$dt_zone = new \DateTimeZone('UTC');
	$source = __DIR__ . '/solstices.txt';
	$dest = __DIR__ . '/solstices.json';
	
	try {		
		foreach(file($source) as $line) {
			$line = trim($line);
			if(!$line) continue; // blanks
			if($line[0]=='#') continue; // comments
			$arr = preg_split("/[\s,]+/", $line);
			foreach($map as $evtype=>$parts) {
				foreach([0,1] as $dt_part) {
					foreach($parts[$dt_part] as $datekey=>$key) {
						$dt_arr[$dt_part][$datekey] = $arr[$key] ?? '';	
					}
				}
				$dt_str = implode('-', $dt_arr[0]) . ' ' . implode(':', $dt_arr[1]);
				$datetime = new \DateTime($dt_str, $dt_zone);
				$solstices[] = [$evtype, $datetime->getTimestamp()];
			}
		}
		$success = file_put_contents($dest, json_encode($solstices));
		if(!$success) throw new \Exception("Error writing to file");
	}
	catch (\Exception $e){
		echo $e->getMessage();
	}
}

}

