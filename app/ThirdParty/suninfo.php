<?php namespace App\ThirdParty;
class suninfo { 
static function load($timestamp=null, $latitude=0, $longitude=0) {$path = __DIR__ . '/suninfo-1.0/suninfo.php';include $path;return new \basecamp\suninfo($timestamp, $latitude, $longitude);}}