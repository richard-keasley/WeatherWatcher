<?php namespace App\Controllers\Graph;

class Readings extends Home {

private function stroke($map, $options=[]) {
	$segments = $this->request->uri->getSegments();
	$date = $segments[3] ?? 'today' ;
	$datetime = $this->get_datetime($date, 'value');
	if(!$datetime) $datetime = new \DateTime;
	
	$string = $datetime->format('Y-m-d 00:00:00');
	$dt_start = new \DateTime($string);
	$interval = new \DateInterval('PT24H');
	$dt_next = new \DateTime($string);
	$dt_next->add($interval);
	# d($dt_start, $dt_next); return;
	
	// check for cached image
	$cache_name = $segments;
	$cache_name[3] = $dt_start->format('Ymd');
	$this->data['cache_name'] = implode('_', $cache_name);
	# d($this->data['cache_name']); return;
	$cache = \Config\Services::cache();
	$response = $cache->get($this->data['cache_name']);
	if(ENVIRONMENT=='production' && $response) {
		header('content-type: image/png');
		echo $response;
		die;
	}
	
	// load data
	$this->data['dt_start'] = $dt_start;
	$this->data['dt_next'] = $dt_next;
	$model = new \App\Models\Readings;
	$raw_data = $model
		->where('datetime >=', $dt_start->format('Y-m-d H:i:s'))
		->where('datetime <', $dt_next->format('Y-m-d H:i:s'))
		->findAll();
	# d($raw_data); return; 
	
	// apply map
	$data = ['label'=>[]];
	foreach($map as $source=>$dest) {
		$data[$dest] = [];
	}
	foreach($raw_data as $entity) {
		$datetime = new \DateTime($entity->datetime);
		$data['label'][] = $datetime->format('H:i');
		$readings = array_flatten_with_dots($entity->readings);
		# d($readings);
		foreach($map as $source=>$dest) {
			$data[$dest][] = $readings[$source];
		}
	}
	# d($data); die;
		
	// aggregate data 
	$data = \App\ThirdParty\jpgraph::aggregate($data);
	# d($data); die;
		
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
			$interval = intval(count($labels)/20) + 1;
			$graph->xaxis->SetTextLabelInterval($interval);
		}
		$graph->xaxis->SetPos("min");
		
		$ytitle = $options['ytitle'] ?? null;
		if($ytitle) {
			$graph->yaxis->title->Set($ytitle);
		}
		
		$title = $options['title'] ?? 'Current weather';
		if($title) $graph->title->Set($title);
				
		\App\ThirdParty\jpgraph::stroke($graph, $this->data['cache_name']);
		die;
	}
	
	\App\ThirdParty\jpgraph::blank();
	die;
}

public function getRain($start='', $end='') {
	$map = [
		'rain.day' => 'rain'
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
		'temperature.out' => 'temperature'
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
		'solar.radiation' => 'solar'
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
		'wind.speed' => 'speed',
		'wind.gust' => 'gust'
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
		'humidity.out' => 'humidity'
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
