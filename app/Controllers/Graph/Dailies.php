<?php namespace App\Controllers\Graph;

class Dailies extends Home {

private $model = null;

public function getIndex() {
	d($this->data);
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
	
	// check image has not been cached
	$cache_name = $segments;
	$cache_name[3] = $dt_start->format('Ymd');
	$cache_name[4] = $dt_end->format('Ymd');
	$this->data['cache_name'] = implode('_', $cache_name);
	$cache = \Config\Services::cache();
	$image = $cache->get($this->data['cache_name']);
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
	$retval = [
		'labels' => [],
		'series' => []
	]; 
	$tr = [];
	foreach($data as $daily) {
		foreach($map as $source=>$dest) $tr[$dest] = $daily->$source;
		$retval['labels'][] = $daily->date;
		$retval['series'][] = $tr;
	}
		
	// aggregate data for large data sets
	// look in jpgraph 
	
	
	
	
	
	# d($data); die;
	# if(!$data) die;
	
	
	
	
	return $retval;
}

public function getRain($start='', $end='') {
	// load data
	$map = [
		'rain_max' => 'rain'
	];
	$data = $this->load_data($map);
		
	$graph = \App\ThirdParty\jpgraph::load();
	foreach(array_keys(end($data['series'])) as $series_key) {
		$ydata = array_column($data['series'], $series_key);
		$plot = \App\ThirdParty\jpgraph::plot('bar', $ydata);
		$graph->Add($plot);
		$plot->SetColor('blue');
	}
	\App\ThirdParty\jpgraph::stroke($graph, $this->data['cache_name']);
}

public function getTemperature($start='', $end='') {
	// load data
	$map = [
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
	
	$graph = \App\ThirdParty\jpgraph::load();
	foreach(array_keys(end($data['series'])) as $series_key) {
		$ydata = array_column($data['series'], $series_key);
		$plot = \App\ThirdParty\jpgraph::plot('line', $ydata);
		$graph->Add($plot);
		$plot->SetColor($colours[$series_key]);
	}
	# return;
	\App\ThirdParty\jpgraph::stroke($graph, $this->data['cache_name']);
}

}