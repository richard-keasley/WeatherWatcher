<?php namespace App\Controllers\Graph;

class Dailies extends Home {

private $model = null;

public function getIndex() {
	die;
}

protected function load_data($map) {
	parent::init();
	
	// compare to App\Controllers\Dailies\home
	$segments = $this->request->uri->getSegments();
	$start = $segments[3] ?? '' ;
	$end = $segments[4] ?? '' ;
	
	try {
		$dt_start = new \datetime($start);
		$dt_end = new \datetime($end);
	}
	catch(\Exception $e) {
		$dt_start = new \datetime();
		$dt_end = new \datetime();
	}
	
	if($dt_end<$dt_start) {
		$swap = $dt_end;
		$dt_end = $dt_start;
		$dt_start = $swap;
	}
		
	// check valid dates
	$model = new \App\Models\Dailies;
	$dt_first = $model->dt_first();
	$dt_last = $model->dt_last();
	if($dt_start<$dt_first) $dt_start = $dt_first;
	if($dt_start>$dt_last) $dt_start = $dt_last;
	if($dt_end<$dt_first) $dt_end = $dt_first;
	if($dt_end>$dt_last) $dt_end = $dt_last;
	
	// check for cached image
	$cache_name = $segments;
	$cache_name[3] = $dt_start->format('Ymd');
	$cache_name[4] = $dt_end->format('Ymd');
	$this->data['cache_name'] = implode('_', $cache_name);
	$cache = \Config\Services::cache();
	$image = $cache->get($this->data['cache_name']);
	# $image = null;
	if($image) {
		header('content-type: image/png');
		echo $image; die;
	}
		
	// load data
	$this->data['dt_start'] = $dt_start;
	$this->data['dt_end'] = $dt_end;
	$data = $model
		->where('date >=', $dt_start->format('Y-m-d'))
		->where('date <=', $dt_end->format('Y-m-d'))
		->findAll();
	
	// apply map
	$retval = []; 
	foreach($map as $source=>$dest) {
		$retval[$dest] = [];
		foreach($data as $daily) {
			$retval[$dest][] = $daily->$source;
		}
	}
	# d($retval); die;
	return $retval;
}

private function stroke($data, $colours=null, $type='line') {
	# if(!$data) die;
	
	// aggregate data for large data sets
	$data = \App\ThirdParty\jpgraph::aggregate($data);
	# d($data); die;
		
	$graph = \App\ThirdParty\jpgraph::load();
	foreach($data as $series_key=>$series) {
		if($series_key=='label') {
		}
		else {
			$plot = \App\ThirdParty\jpgraph::plot($type, $series);
			$graph->Add($plot);
			$colour = $colours[$series_key] ?? null;
			if($colour) $plot->SetColor($colour);
		}
	}
	\App\ThirdParty\jpgraph::stroke($graph, $this->data['cache_name']);
}

public function getRain($start='', $end='') {
	// load data
	$map = [
		'date' => 'label',
		'rain_max' => 'rain'
	];
	$data = $this->load_data($map);
	
	$colours = [
		'rain' => 'blue'
	];
	$this->stroke($data, $colours, 'bar');		
}

public function getTemperature($start='', $end='') {
	// load data
	$map = [
		'date' => 'label',
		'temperature_max' => 'max',
		'temperature_avg' => 'avg',
		'temperature_min' => 'min'
	];
	$data = $this->load_data($map);
	
	$colours = [
		'max' => '#c11',
		'avg' => '#ccc',
		'min' => '#11c'
	];
	$this->stroke($data, $colours, 'line');
}

public function getSolar($start='', $end='') {
	// load data
	$map = [
		'date' => 'label',
		'solar_max' => 'max',
		'solar_avg' => 'avg'
	];
	$data = $this->load_data($map);
	
	$colours = [
		'max' => '#c11',
		'avg' => '#ccc'
	];
	$this->stroke($data, $colours, 'line');
}

public function getWind($start='', $end='') {
	// load data
	$map = [
		'date' => 'label',
		'wind_max' => 'max',
		'wind_avg' => 'avg'
	];
	$data = $this->load_data($map);
	
	$colours = [
		'max' => '#c11',
		'avg' => '#ccc'
	];
	$this->stroke($data, $colours, 'line');
}

public function getHumidity($start='', $end='') {
	// load data
	$map = [
		'date' => 'label',
		'humidity_max' => 'max',
		'humidity_avg' => 'avg'
	];
	$data = $this->load_data($map);
	
	$colours = [
		'max' => '#c11',
		'avg' => '#ccc'
	];
	$this->stroke($data, $colours, 'line');
}

}
