<?php namespace App\ThirdParty;

class suninfo {
	
const version = '1.1';
const date = '2025-04-09';

static function load($timestamp=null, $latitude=0, $longitude=0) {
	try { 
		$include = sprintf('%s/suninfo-%s/suninfo.php' , __DIR__, self::version);
		require_once $include;
		return new \basecamp\suninfo($timestamp, $latitude, $longitude);
	}
	catch(\Exception $e ) {
		echo new \App\Views\Htm\alert($e->getMessage());
	}
	die; 
}

}
