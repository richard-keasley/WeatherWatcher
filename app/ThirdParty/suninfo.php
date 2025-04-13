<?php namespace App\ThirdParty;

class suninfo {
	
const version = '1.1';
const date = '2025-04-09';

static function load($timestamp='', $latitude=0, $longitude=0) {
	try { 
		$include = sprintf('%s/suninfo-%s/suninfo.php' , __DIR__, self::version);
		return new \basecamp\suninfo($timestamp, $latitude, $longitude);
	}
	catch(\Exception $e ) {
		echo new \App\Views\Htm\alert($e->getMessage());
	}
	die; 
}

static function example() {
	return sprintf('%s/suninfo-%s/example.php' , __DIR__, self::version);
}

}
