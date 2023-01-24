<?php namespace App\ThirdParty;

class suninfo {
	
const version = '1.0';
const date = '2023-01-06';

static function load($timestamp=null, $latitude=0, $longitude=0) {
	$include = sprintf('%s/suninfo-%s/suninfo.php' , __DIR__, self::version);
	require_once $include;
	return new \basecamp\suninfo($timestamp, $latitude, $longitude);
}

}
