<?php namespace App\ThirdParty;

class moonphase {

static $version = '2.1.0';
static $date = '2022-05-20';
	
static function load($datetime=null) {
	$path = __DIR__ . '/php-moon-phase-' . self::$version;
	include "{$path}/src/MoonPhase.php";
	return new \Solaris\MoonPhase($datetime);
}

static function img($moonphase) {
	\helper('html');
	
	$phase = $moonphase->getPhase();
	$phase_name = $moonphase->getPhaseName();

	$key = $phase - floor($phase); // get decimal part
	$key = round($key * 16); // 16 images 
	$img = [
		'src' => site_url(sprintf('app/moon/%02d.png', $key)),
		'alt' => $phase_name,
		'style' => "width: 6em;"
	];
	$attrs = [
		'title' => $phase_name,
		'style' => "
			background: #1b1b30;
			display: inline-block;
			padding:.4em;
			"
	];
	return sprintf('<figure %s>%s</figure>', \stringify_attributes($attrs), \img($img));
}

}

