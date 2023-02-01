<?php namespace App\ThirdParty;

class moonphase {

const version = '2.1.0';
const date = '2022-05-20';
	
static function load($datetime=null) {
	$include = sprintf('%s/php-moon-phase-%s/src/MoonPhase.php' , __DIR__, self::version);
	require_once $include;
	return new \Solaris\MoonPhase($datetime);
}

static function img($moonphase, $test=false) {
	\helper('html');
	
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
			display:inline-block;
			"
	];
	return sprintf('<div %s>%s</div>', \stringify_attributes($attrs), \img($img));
}

}

