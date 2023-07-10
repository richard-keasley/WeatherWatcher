<?php namespace App\ThirdParty;

class moonphase {

const version = '3.0.0';
const date = '2022-07-08';
	
static function load($datetime=null) {
	try { 
		$include = sprintf('%s/php-moon-phase-%s/src/MoonPhase.php' , __DIR__, self::version);
		require_once $include;
		return new \Solaris\MoonPhase($datetime);
	}
	catch(\Exception $e ) {
		echo new \App\Views\Htm\alert($e->getMessage());
	}
	die;
}

static function img($moonphase, $test=false) {
	$phase = $moonphase->getPhase();
	$phase_name = $moonphase->getPhaseName();
	$key = round($phase * 16) % 16; // 16 images
	
	if($test) {
		echo "{$phase}: {$key}<br>";
		return;
	}
	
	$img = [
		'src' => site_url(sprintf('app/moon/%02d.png', $key)),
		'alt' => $phase_name,
		'title' => $phase_name,
		'style' => "width: 6em;"
	];
	$attrs = [
		'style' => "
			background: #1b1b30;
			padding:.4em;
			margin: 0;
			border: 0;
			display:inline-block;
			"
	];
	return sprintf('<figure %s>%s</figure>', \stringify_attributes($attrs), \img($img));
}

}

