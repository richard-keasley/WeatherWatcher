<?php namespace App\Controllers\Graph;

class Readings extends Home {

private function stroke($map, $options=[]) {
	$segments = $this->request->uri->getSegments();
	
	$string = $segments[3] ?? '' ;
	$dt_start = $this->get_datetime($string, 'value');
	$string = $segments[4] ?? '' ;
	$dt_end = $this->get_datetime($string, 'value');
	if(!$dt_start) $dt_start = new \DateTime('today');
	
	$title = $options['title'] ?? '' ;
	if(!$title) {
		$title = 'Station readings: ';
		$title .= $dt_end ? 
			$dt_start->format('d/m/y') . '-' . $dt_end->format('d/m/y') :
			$dt_start->format('j F Y');
	}
	// don't set dt_end until title is set
	if(!$dt_end) $dt_end = clone $dt_start; // get daily according to dt_start 
	# d($dt_start, $dt_end);
		
	if($dt_end<$dt_start) {
		$swap = $dt_end;
		$dt_end = $dt_start;
		$dt_start = $swap;
	}
	$oneday = new \DateInterval('PT24H');
	$dt_end->add($oneday);
	# d($dt_start, $dt_end); die;
		
	// check for cached image
	$cache = \Config\Services::cache();
	$segments[3] = $dt_start->format('YmdHi');
	$segments[4] = $dt_end->format('YmdHi');
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
	$model = new \App\Models\Readings;
	$raw_data = $model
		->where('datetime >=', $dt_start->format('Y-m-d H:i:s'))
		->where('datetime <', $dt_end->format('Y-m-d H:i:s'))
		->findAll();
	# d($raw_data); return; 
	
	// apply map
	$data = ['label'=>[]];
	foreach($map as $source=>$dest) $data[$dest] = [];
	foreach($raw_data as $entity) {
		$datetime = new \DateTime($entity->datetime);
		$data['label'][] = $entity->get_datetime();
		$readings = $entity->get_readings();
		foreach($map as $source=>$dest) {
			$data[$dest][] = $readings[$source];
		}
	}
	# d($data); die;
		
	// aggregate data
	$data = \App\ThirdParty\jpgraph::periodise($data, 'YmdH', 'H:00');
	# d($data); die;
	
	
	$colours = $options['colours'] ?? null;
	$type = $options['type'] ?? 'line';
	$fillcolor = $options['fillcolor'] ?? null;
	$y2 = $options['y2'] ?? '#none#';
		
	// send image back to browser
	$graph = \App\ThirdParty\jpgraph::load();
	$dataset_count = 0;
	$labels = null;
	foreach($data as $dataname=>$dataset) {
		if($dataname=='label') {
			$labels = $dataset;
		}
		else {
			$dataset_count++;
			$plot = \App\ThirdParty\jpgraph::plot($type, $dataset);
			if($dataname===$y2) {
				$graph->SetY2Scale('lin');
				$graph->AddY2($plot);
			}
			else {
				$graph->Add($plot);
			}
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
				if($fillcolor) $plot->Setfillcolor($fillcolor);				
			}
		}
	}
			
	if($dataset_count) {
		$graph->legend->SetPos(0.05, 0.01, 'left', 'top');
	
		if($labels) {
			$graph->xaxis->SetTickLabels($labels);
			$graph->xaxis->SetLabelAngle(90);
			$interval = intval(count($labels)/25) + 1;
			$graph->xaxis->SetTextLabelInterval($interval);
		}
		$graph->xaxis->SetPos("min");
		
		$ytitle = $options['ytitle'] ?? null;
		if($ytitle) {
			$graph->yaxis->title->Set($ytitle);
		}
		
		$graph->title->Set($title);
				
		\App\ThirdParty\jpgraph::stroke($graph, $cache_name);
		die;
	}
	
	\App\ThirdParty\jpgraph::blank();
	die;
}

public function getRain($start='', $end='') {
	$map = [
		'rain_day' => 'rain'
	];
	
	$options = [
		'ytitle' => 'Rainfall [mm]',
		'colours' => [
			'rain' => "#66F"
		],
		'fillcolor' => "#66F",
		# 'type' => 'bar'
	];
	$this->stroke($map, $options);		
}

public function getTemperature($start='', $end='') {
	$map = [
		'temperature_out' => 'temperature'
	];
	$options = [
		'ytitle' => 'Temperature [°C]',
		'colours' => [
			'temperature' => '#c11'
		]
	];
	$this->stroke($map, $options);
}

public function getSolar($start='', $end='') {
	// load data
	$map = [
		'solar_radiation' => 'solar'
	];
	
	$options = [
		'ytitle' => 'Solar [W/m²]',
		'colours' => [
			'solar' => '#c11'
		]
	];
	$this->stroke($map, $options);
}

public function getWind($start='', $end='') {
	$map = [
		'wind_speed' => 'speed',
		'wind_gust' => 'gust'
	];
	
	$options = [
		'ytitle' => 'Wind [mph]',
		'colours' => [
			'speed' => '#ccc',
			'gust' => '#FAA'
		]
	];
	$this->stroke($map, $options);
}

public function getHumidity($start='', $end='') {
	$map = [
		'humidity_out' => 'humidity'
	];
	
	$options = [
		'ytitle' => 'Humidity [%]',
		'colours' => [
			'humidity' => '#339'
		]
	];
	$this->stroke($map, $options);
}

public function getIndoors($start='', $end='') {
	$map = [
		'temperature_in' => 'temperature',
		'humidity_in' => 'humidity'
	];
	$options = [
		'ytitle' => 'Temperature [°C] / Humidity [%]',
		# 'y2' => 'humidity',
		'colours' => [
			'temperature' => '#c11',
			'humidity' => '#444'
		]
	];
	$this->stroke($map, $options);
}

}
