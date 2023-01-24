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

static function aggregate($data, $maxrows=24) {
	// aggregate large data sets to ensure it's not too many points
	if(!$maxrows || !count($data)) return;
	$row_ratio = $maxrows / count($data) ;
	if($row_ratio>=1) return $data;
	
	$buffer = array();
	foreach($data as $data_key=>$data_row) {
		$buffer_key = intval($data_key * $row_ratio);
		$buffer[$buffer_key][] = $data_row;
	}
	
	$new_data = array();
	$new_row = array();
	foreach($buffer as $buffer_key=>$buffer_row) {
		// compile buffer_row
		$buffer_count = count($buffer_row);
		if($buffer_count) { // add buffer_row to new_data
			foreach(array_keys($data[0]) as $this_key) {
				$this_val = column_merge($buffer_row, $this_key);
				$new_row[$this_key] = $this_val;
			}
			$new_data[] = $new_row;
		}
	}
	return $new_data;
	
}

}
