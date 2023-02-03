<?php namespace App\Controllers\Graph;

class Readings extends Home {

private function stroke($map, $options=[]) {
	$segments = $this->request->uri->getSegments();
	$date = $segments[3] ?? 'today' ;
	$datetime = $this->get_datetime($date, 'value');
	if(!$datetime) $datetime = new \DateTime;
	$string = $datetime->format('Y-m-d 00:00:00');
	if(empty($options['title'])) {
		$options['title'] = sprintf('Station readings for %s', $datetime->format('j F Y'));
	}
	$dt_start = new \DateTime($string);

	// check for cached image
	$cache = \Config\Services::cache();
	$segments[3] = $dt_start->format('Ymd');
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
	$interval = new \DateInterval('PT25H');
	$dt_end = new \DateTime($string);
	$dt_end->add($interval);
	# d($dt_start, $dt_end); return;
	$model = new \App\Models\Readings;
	$raw_data = $model
		->where('datetime >=', $dt_start->format('Y-m-d H:i:s'))
		->where('datetime <=', $dt_end->format('Y-m-d H:i:s'))
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
	
	// send image back to browser
	$graph = \App\ThirdParty\jpgraph::load();
	$dataset_count = 0;
	
	$colours = $options['colours'] ?? null;
	$type = $options['type'] ?? 'line';
	$fillcolor = $options['fillcolor'] ?? null;
	
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
				if($fillcolor) $plot->Setfillcolor($fillcolor);				
			}
		}
	}
			
	if($dataset_count) {
		$graph->legend->SetPos(0.05, 0.01, 'left', 'top');
	
		if($labels) {
			$graph->xaxis->SetTickLabels($labels);
			$graph->xaxis->SetLabelAngle(90);
			$interval = 1;#intval(count($labels)/24) + 1;
			$graph->xaxis->SetTextLabelInterval($interval);
		}
		$graph->xaxis->SetPos("min");
		
		$ytitle = $options['ytitle'] ?? null;
		if($ytitle) {
			$graph->yaxis->title->Set($ytitle);
		}
		
		$graph->title->Set($options['title']);
				
		\App\ThirdParty\jpgraph::stroke($graph, $cache_name);
		die;
	}
	
	\App\ThirdParty\jpgraph::blank();
	die;
}

public function getRain($date='') {
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

public function getTemperature($date='') {
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

public function getSolar($date='') {
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

public function getWind($date='') {
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

public function getHumidity($date='') {
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

}
