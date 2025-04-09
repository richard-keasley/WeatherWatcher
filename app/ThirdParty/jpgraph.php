<?php namespace App\ThirdParty;

class jpgraph {

const version = '4.4.2';
const date = '2023-08-07';

const defaults = [
	'width' => 1280,
	'height' => 768
];

static $path = '';
	
static function load($width=0, $height=0, $scale='datlin') {
	try {
		self::$path = sprintf('%s/jpgraph-%s/src/' , __DIR__, self::version);
		
		if(!$width) $width = self::defaults['width'];
		if(!$height) $height = self::defaults['height'];
		
		self::include_file('jpgraph');
		self::include_file('jpgraph_date');
		$graph = new \Graph($width, $height);
		$graph->SetScale($scale);
		$graph->SetMargin(60, 20, 40, 80);
		return $graph;
	}
	catch(\Exception $e ) {
		echo new \App\Views\Htm\alert($e->getMessage());
	}
	die;
}

static function include_file($filename) {
	require_once self::$path . $filename . '.php';
}

static function plot($type, $ydata, $times) {
	$include = match($type) {
		default => "jpgraph_{$type}"
	};
	self::include_file($include);
	
	return match($type) {
		'bar' => new \BarPlot($ydata, $times),
		default => new \LinePlot($ydata, $times)
	};
}

// send image back to browser
static function stroke($jpgraph, $cache_data=[]) {
	# d($jpgraph->xaxis->ticks_label); return;
	# d($jpgraph); return;
	
	$cache_time = $cache_data['time'] ?? 0 ; 
	$cache_name = $cache_data['name'] ?? '' ; 
	if(!$cache_time) $cache_name = '';
	# d($cache_name, $cache_time); return;
	
	header('content-type: image/png');
				
	if($cache_name) {
		header("Cache-Control: max-age={$cache_time}");
		$cache = \Config\Services::cache();
		$response = $cache->get($cache_name);
		if($response) {
			echo $response; 
			die;
		}
	}
		
	ob_start();
	$jpgraph->stroke();
	$response = ob_get_flush();
	
	if($cache_name) {
		$success = $cache->save($cache_name, $response, $cache_time);
		$action = $success ? 'stored' : 'failed' ;
		log_message('debug', "cache: {$action} {$cache_time} / {$cache_name}");
	}
	die;
}

static function blank($width=0, $height=0) {
	//  send empty image back to browser
	if(!$width) $width = self::defaults['width'];
	if(!$height) $height = self::defaults['height'];
	$im = imagecreatetruecolor($width, $height);
	$colour = imagecolorallocate($im, 255, 255, 255);
	imagefill($im, 0, 0, $colour);
	header('Content-Type: image/png');
	imagepng($im);
	imagedestroy($im);	
	die;
}

static function periodise($data, $key_format='Ymd') {
	/*
	ensures each item of dataset covers the same amount of time
	aggregates datasets so there's not too many for graph
	ensure 'datetime' dataset items are datetime
	key_format: key for each aggregated data item
	*/

	// setup category keys
	$agg_keys = []; $timestamps = [];
	foreach($data['datetime'] as $data_key=>$datetime) {
		$agg_key = $datetime->format($key_format);
		$agg_keys[$data_key] = $agg_key;
		if(!isset($timestamps[$agg_key])) {
			$timestamps[$agg_key] = $datetime->format('U');
		}
	}
	# d($agg_keys); die;
	
	// aggregate data 	
	$aggregate = ['datetime'=>[]];
	foreach($data as $dataname=>$dataset) {
		$agg_series = [];
		foreach($dataset as $data_key=>$data_value) {
			$agg_key = $agg_keys[$data_key];
			if(!isset($agg_series[$agg_key])) {
				$agg_series[$agg_key] = [];
			}
			$agg_series[$agg_key][] = $data_value;
		}

		$dataset_buffer = [];
		$has_data = false;
		foreach($agg_series as $agg_key=>$values) {
			$buffer = [];
			foreach($values as $value) {
				// strip out missing readings
				if(!is_null($value)) $buffer[] = $value;
			}
			if($buffer) {
				$has_data = true;
				$value = match($dataname) {
					'datetime' => $timestamps[$agg_key],
					'min' => min($buffer),
					'max' => max($buffer),
					default => array_sum($buffer) / count($buffer)
				};
			}
			else $value = null;
			$dataset_buffer[] = $value;
			# d($buffer, $value);
		}
		// don't add empty datasets
		if($has_data) $aggregate[$dataname] = $dataset_buffer;
	}
	# d($data, $aggregate);  die;
	return $aggregate;
}

}
