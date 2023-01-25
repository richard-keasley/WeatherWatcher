<?php namespace App\ThirdParty;

class jpgraph {

const version = '4.4.1';
const date = '2022-05-12';

static $path = '';
	
static function load($width=1024, $height=768, $scale='textlin') {
	self::$path = sprintf('%s/jpgraph-%s/src/' , __DIR__, self::version);
	
	self::include_file('jpgraph');
	$graph = new \Graph($width, $height);
	$graph->SetScale('textlin');
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
	
	ob_start();
	$jpgraph->stroke();
	$image = ob_get_flush();
	if($cache_name) {
		$cache = \Config\Services::cache();
		$success = $cache->save($cache_name, $image, 14400);
	}
	die;
}

static function aggregate($data, $maxrows=96) {
	// aggregate large data sets to ensure it's not too many points
	
	if(!$maxrows) return $data;
	$series = current($data);
	$data_count = count($series);
	if($data_count <= $maxrows) return $data;
	$agg_ratio = $maxrows / $data_count;
	# d($data_count, $maxrows, $agg_ratio);
		
	$aggregate = [];
	foreach($data as $series_key=>$series) {
		$agg_series = [];
		foreach($series as $data_key=>$data_value) {
			$agg_key = floor($agg_ratio * $data_key);
			if(!isset($agg_series[$agg_key])) {
				$agg_series[$agg_key] = [];
			}
			$agg_series[$agg_key][] = $data_value;
		}
		$aggregate[$series_key] = [];
		foreach($agg_series as $values) {
			$agg_count = count($values);
			switch($series_key) {
				case 'label':
				$value = $values[0];
				break;
				
				default:
				$total = array_sum($values);
				$value = $total / $agg_count;
			}
			$aggregate[$series_key][] = $value;
		}
	}
	# d($data, $aggregate);  die;	
	
	return $aggregate;
}

}
