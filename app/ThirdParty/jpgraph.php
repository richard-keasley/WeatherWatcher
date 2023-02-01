<?php namespace App\ThirdParty;

class jpgraph {

const version = '4.4.1';
const date = '2022-05-12';

const defaults = [
	'width' => 1280,
	'height' => 768
];

static $path = '';
	
static function load($width=0, $height=0, $scale='textlin') {
	self::$path = sprintf('%s/jpgraph-%s/src/' , __DIR__, self::version);
	
	if(!$width) $width = self::defaults['width'];
	if(!$height) $height = self::defaults['height'];
	
	self::include_file('jpgraph');
	$graph = new \Graph($width, $height);
	$graph->SetScale($scale);
	$graph->SetMargin(60, 20, 40, 80);
	return $graph;
}

static function include_file($filename) {
	require_once self::$path . $filename . '.php';
}

static function plot($type, $ydata) {
	$include = match($type) {
		default => "jpgraph_{$type}"
	};
	self::include_file($include);
	
	return match($type) {
		'bar' => new \BarPlot($ydata),
		default => new \LinePlot($ydata)
	};
}

static function stroke($jpgraph, $cache_name=null) {
	// send image back to browser
	# d($jpgraph); return;
	
	header('content-type: image/png');
	
	if(ENVIRONMENT!='production') $cache_name = null;
	$cache_time = 14400;
			
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

static function aggregate($data, $maxrows=96, $minrows=3) {
	// aggregate large datasets
	// prevent small datasets from being plotted
	
	if(!$data) return [];
	$dataset = current($data);
	$data_count = count($dataset);
	if($data_count < $minrows) return []; // no graph for small datasets
	// remove empty datasets
	foreach($data as $dataname=>$dataset) {
		$nodata = (is_null(min($dataset)) && is_null(max($dataset)));
		if($nodata) unset($data[$dataname]);
	}
	
	if(!$maxrows) return $data; // no aggregation
	if($data_count <= $maxrows) return $data; // no aggregation
	$agg_ratio = $maxrows / $data_count;
	# d($data_count, $maxrows, $agg_ratio);
		
	$aggregate = [];
	foreach($data as $dataname=>$dataset) {
		$agg_series = [];
		foreach($dataset as $data_key=>$data_value) {
			$agg_key = floor($agg_ratio * $data_key);
			if(!isset($agg_series[$agg_key])) {
				$agg_series[$agg_key] = [];
			}
			$agg_series[$agg_key][] = $data_value;
		}
		$aggregate[$dataname] = [];
		foreach($agg_series as $values) {
			$agg_count = count($values);
			$aggregate[$dataname][] = match($dataname) {
				'label' => $values[0],
				'min' => min($values),
				'max' => max($values),
				default => array_sum($values) / count($values)
			};
		}
	}
	# d($data, $aggregate);  die;	
	
	return $aggregate;
}

}
