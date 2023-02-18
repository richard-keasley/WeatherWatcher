<?php namespace App\Controllers\Graph;

class Averages extends Home {

private function stroke($map, $options=[]) {
	$segments = $this->getSegments();
	$segments['dt_start'] = new \DateTime('today');
	
	$cache_data = $this->check_cache($segments);
	# d($segments, $cache_data); die;
	
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
	
	// get current data
	$mapkey = array_key_first($map);
	
	$datetime = new \DateTime('today');
	$end = $datetime->format('Y-m-d');
	$year = new \DateInterval('P1Y');
	$start = $datetime->sub($year)->format('Y-m-d');
	$current = ['label'=>[], 'current'=>[]];
	$raw_data = $model->orderBy('date')->where('date >=', $start)->where('date <', $end)->findAll();
	foreach($raw_data as $daily) {
		$current['label'][] = $daily->get_date();
		$current['current'][] = $daily->$mapkey;
	}
			
	// aggregate data
	$key_format = 'W';
	$label_format = 'W';
	$data = \App\ThirdParty\jpgraph::periodise($data, $key_format, $label_format);
	$current = \App\ThirdParty\jpgraph::periodise($current, $key_format, $label_format);
	
	// sort and join datasets, starting a year ago
	$combined = [];
	$wk_format = '%02u';
	foreach($current['current'] as $key=>$value) {	
		$wknum = sprintf($wk_format, $current['label'][$key]);
		$combined[$wknum]['current'] = $value;
	}
	foreach($data as $dataname=>$dataset) {
		if($dataname=='label') continue;
		foreach($dataset as $key=>$value) {
			$wknum = sprintf($wk_format, $data['label'][$key]);
			$combined[$wknum][$dataname] = $value;
		}
	}
	// friendly labels
	$dt_label = new \DateTime('1999-12-25'); 
	$week = new \DateInterval('P7D');
	for($key=1; $key<53; $key++) {
		$wknum = sprintf($wk_format, $key);
		$combined[$wknum]['label'] = $dt_label->add($week)->format('d M');
	}
	unset($combined['53']); // discard week 53, too much noise
	# d($data, $current, $combined); return;

	// convert so graph can read it
	$data = [];
	foreach($combined as $key=>$row) {
		foreach($row as $dataname=>$value) {
			$data[$dataname][] = $value;
		}
	}
	# d($data); return;
	

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
				
		\App\ThirdParty\jpgraph::stroke($graph, $cache_data);
		die;
	}
	
	\App\ThirdParty\jpgraph::blank();
	die;
}

public function getRain() {
	$map = [
		'rain_max' => 'rain'
	];
	
	$options = [
		'ytitle' => 'Rainfall [mm]',
		'colours' => [
			'rain' => '#CCE',
			'current' => '#090'
		]
	];
	$this->stroke($map, $options);		
}

public function getTemperature() {
	$map = [
		'temperature_avg' => 'avg',
		'temperature_max' => 'max',
		'temperature_min' => 'min'
	];
	$options = [
		'ytitle' => 'Temperature [°C]',
		'colours' => [
			'max' => '#E99',
			'avg' => '#C8C8C8',
			'min' => '#99E',
			'current' => '#090'
		]
	];
	$this->stroke($map, $options);
}

public function getSolar() {
	$map = [
		'solar_avg' => 'avg'
	];
	
	$options = [
		'ytitle' => 'Solar [W/m²]',
		'colours' => [
			'max' => '#Fcc',
			'avg' => '#C8C8C8',
			'current' => '#090'
		]
	];
	$this->stroke($map, $options);
}

public function getWind() {
	$map = [
		'wind_avg' => 'avg'
	];
	
	$options = [
		'ytitle' => 'Wind [mph]',
		'colours' => [
			'avg' => '#C8C8C8',
			'current' => '#090'
		]
	];
	$this->stroke($map, $options);
}

public function getHumidity() {
	$map = [
		'humidity_avg' => 'avg'
	];
	
	$options = [
		'ytitle' => 'Humidity [%]',
		'colours' => [
			'max' => '#c11',
			'avg' => '#C8C8C8',
			'current' => '#090'
		]
	];
	$this->stroke($map, $options);
}

}
