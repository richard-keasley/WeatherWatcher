<?php namespace App\Controllers\Graph;

class Averages extends Home {

private function stroke($map, $options=[]) {
	$segments = $this->request->uri->getSegments();
	
	// check for cached image
	$cache = \Config\Services::cache();
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
	$model = new \App\Models\Dailies;	
	$raw_data = $model->orderBy('date')->findAll();
	
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
	$key_format = 'W';
	$label_format = 'W';
	$data = \App\ThirdParty\jpgraph::periodise($data, $key_format, $label_format);
	# d($data); # die;

	// sort data
	foreach($data as $dataname=>$values) {
		if($dataname=='label') continue;
		$order = $data['label'];
		array_multisort($order, $data[$dataname]);
	}
	sort($data['label']);
	
	// find key for today
	$datetime = new \DateTime();
	$now_label = $datetime->format('W');
	$start_key = array_search($now_label, $data['label']);
	
	// friendly labels
	$dt_label = new \DateTime('1999-12-25'); 
	$week = new \DateInterval('P7D');
	foreach($data['label'] as $key=>$value) {
		$data['label'][$key] = $dt_label->add($week)->format('d M');
	}
	
	// re-sort to make today last
	$sorted = [];
	foreach($data as $dataname=>$values) {
		unset($values['52']); // discard last day, too much noise
		$last_key = count($values) - 1;
		for($key=$start_key+1; $key<=$last_key; $key++) {
			$sorted[$dataname][] = $values[$key];
		}
		for($key=0; $key<=$start_key; $key++) {
			$sorted[$dataname][] = $values[$key];		}
	}
	# d($sorted);d($data); die;
	$data = $sorted;

	// get current data
	$mapkey = array_key_first($map);
	$datetime = new \DateTime();
	$year = new \DateInterval('P1Y');
	$start = $datetime->sub($year)->format('Y-m-d');
	$current = [];
	$raw_data = $model->orderBy('date')->where('date >', $start)->findAll();
	foreach($raw_data as $daily) {
		$current[] = $daily->$mapkey;
		
	}
	# d($data, $current); 	die;

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
			$interval = 4; #intval(count($labels)/17.5) + 1;
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
		'temperature_avg' => 'avg',
		'temperature_max' => 'max',
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
		'solar_avg' => 'avg',
		'solar_max' => 'max'
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
		'wind_avg' => 'avg',
		'wind_max' => 'max'
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
		'humidity_avg' => 'avg',
		'humidity_max' => 'max'
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
