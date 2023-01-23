<?php namespace App\ThirdParty;

class suninfo {
	
const version = '1.0';
const date = '2023-01-06';

static function load($timestamp=null, $latitude=0, $longitude=0) {
$path = __DIR__ . '/suninfo-1.0/suninfo.php';
include $path;
return new \basecamp\suninfo($timestamp, $latitude, $longitude);
}

}
