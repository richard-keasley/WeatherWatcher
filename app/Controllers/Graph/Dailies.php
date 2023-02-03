<?php namespace App\Controllers\Graph;

class Dailies extends Home {

private function stroke($map, $options=[]) {
	// compare to App\Controllers\Dailies::index
	$segments = $this->request->uri->getSegments();
	$value = $segments[3] ?? '' ;
	$dt_start = $this->get_datetime($value, 'value');
	if(!$dt_start) $dt_start = new \DateTime();
	
	$value = $segments[4] ?? '' ;
	$dt_end = $this->get_datetime($value, 'value');
	if(!$dt_end) $dt_end = new \DateTime();
		
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
	$cache = \Config\Services::cache();
	$segments[3] = $dt_start->format('Ymd');
	$segments[4] = $dt_end->format('Ymd');
	$cache_name = implode('_', $segments);
	$version = $this->request->getGet('v');
	$response = $version ? false : $cache->get($cache_name);
	# d($cache_name); echo $response ? 'cached' : 'not cached'; return;
	if(ENVIRONMENT=='production' && $response) {
		header('content-type: image/png');
		echo $response;
		die;
	}
		
	// load data
	$raw_data = $model
		->where('date >=', $dt_start->format('Y-m-d'))
		->where('date <=', $dt_end->format('Y-m-d'))
		->findAll();
	
	// apply map
	$data = ['label'=>[]];
	foreach($map as $source=>$dest) $data[$dest] = [];
	foreach($raw_data as $daily) {
		$data['label'][] = $daily->get_date();
		foreach($map as $source=>$dest) {
			$data[$dest][] = $daily->$source;
		}
	}
	# d($data); die;
		
	// aggregate data
	$key_format = 'Ymd';
	$label_format = 'd/m/y';
	$span = intval($dt_start->diff($dt_end)->format('%a'));
	if($span>100) {
		$key_format = 'Y_W';
	}
	$data = \App\ThirdParty\jpgraph::periodise($data, $key_format, $label_format);
	# d($span, $data); die;
	
	// send image back to browser
	$graph = \App\ThirdParty\jpgraph::load();
	$dataset_count = 0;
	
	$colours = $options['colours'] ?? null;
	$type = $options['type'] ?? 'line';
	$labels = null;
	foreach($data as $dataname=>$dataset) {
		if($dataname=='label') {
			$labels = $dataset;
		}
		else {
			$dataset_count++;
			$plot = \App\ThirdParty\jpgraph::plot($type, $dataset);
			$graph->Add($plot);
			$plot->SetLegend($dataname);
			$colour = $colours[$dataname] ?? null;
			switch($type) {
				case 'bar':
				$plot->SetWidth(1);
				if($colour) $plot->SetFillColor($colour);
				$plot->SetColor('#666');
				$plot->SetWeight(1);
				break;
				
				case 'line':
				default:
				$plot->SetWeight(2);
				if($colour) $plot->SetColor($colour);
			}
		}
	}
			
	if($dataset_count) {
		$graph->legend->SetPos(0.05, 0.01, 'left', 'top');
	
		if($labels) {
			$graph->xaxis->SetTickLabels($labels);
			$graph->xaxis->SetLabelAngle(90);
			$interval = intval(count($labels)/17.5) + 1;
			$graph->xaxis->SetTextLabelInterval($interval);
		}
		$graph->xaxis->SetPos("min");
		
		$ytitle = $options['ytitle'] ?? null;
		if($ytitle) {
			$graph->yaxis->title->Set($ytitle);
		}
		
		$title = $options['title'] ?? 'Daily averages';
		if($title) $graph->title->Set($title);
				
		\App\ThirdParty\jpgraph::stroke($graph, $cache_name);
		die;
	}
	
	\App\ThirdParty\jpgraph::blank();
	die;
}

public function getRain($start='', $end='') {
	$map = [
		'rain_max' => 'rain'
	];
	
	$options = [
		'ytitle' => 'Rainfall [mm]',
		'colours' => [
			'rain' => '#66F'
		],
		'type' => 'bar'
	];
	$this->stroke($map, $options);		
}

public function getTemperature($start='', $end='') {
	$map = [
		'temperature_max' => 'max',
		'temperature_avg' => 'avg',
		'temperature_min' => 'min'
	];
	$options = [
		'ytitle' => 'Temperature [°C]',
		'colours' => [
			'max' => '#c11',
			'avg' => '#ccc',
			'min' => '#11c'
		]
	];
	$this->stroke($map, $options);
}

public function getSolar($start='', $end='') {

	$map = [
		'solar_max' => 'max',
		'solar_avg' => 'avg'
	];
	
	$options = [
		'ytitle' => 'Solar [W/m²]',
		'colours' => [
			'max' => '#c11',
			'avg' => '#ccc'
		]
	];
	$this->stroke($map, $options);
}

public function getWind($start='', $end='') {
	$map = [
		'wind_max' => 'max',
		'wind_avg' => 'avg'
	];
	
	$options = [
		'ytitle' => 'Wind [mph]',
		'colours' => [
			'max' => '#c11',
			'avg' => '#ccc'
		]
	];
	$this->stroke($map, $options);
}

public function getHumidity($start='', $end='') {
	$map = [
		'humidity_max' => 'max',
		'humidity_avg' => 'avg'
	];
	
	$options = [
		'ytitle' => 'Humidity [%]',
		'colours' => [
			'max' => '#c11',
			'avg' => '#ccc'
		]
	];
	$this->stroke($map, $options);
}

}
