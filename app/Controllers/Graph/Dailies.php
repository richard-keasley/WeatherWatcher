<?php namespace App\Controllers\Graph;

class Dailies extends Home {

private function stroke($map, $options=[]) {
	// compare to App\Controllers\Dailies::index
	$segments = $this->getSegments();
	
	$dt_start = $segments['dt_start'] ?? new \DateTime();
	$dt_end = $segments['dt_end'] ?? new \DateTime();
			
	// check valid dates
	$model = new \App\Models\Dailies;
	$dt_first = $model->dt_first();
	$dt_last = $model->dt_last();
	if($dt_start<$dt_first) $dt_start = $dt_first;
	if($dt_start>$dt_last) $dt_start = $dt_last;
	if($dt_end<$dt_first) $dt_end = $dt_first;
	if($dt_end>$dt_last) $dt_end = $dt_last;
	
	$segments['dt_start'] = $dt_start;
	$segments['dt_end'] = $dt_end;
	$cache_data = $this->check_cache($segments);
		
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
	# d($raw_data, $data); return;
		
	// aggregate data
	$key_format = 'Ymd';
	$label_format = 'd/m/y';
	$span = intval($dt_start->diff($dt_end)->format('%a'));
	if($span>100) {
		$key_format = 'Y_W';
	}
	$data = \App\ThirdParty\jpgraph::periodise($data, $key_format, $label_format);
	# d($options, $data); return;
	
	$display = $segments['display'] ?? $options['display'] ?? null;
	# d($display); return;
	$displays = ['line', 'bar', 'table'];
	if(!in_array($display, $displays)) $display = $displays[0];
	
	if($display=='table') {
		$this->data['data'] = $data;
		return view('data', $this->data);
	}
	
	// send image back to browser
	$colours = $options['colours'] ?? null;
	$graph = \App\ThirdParty\jpgraph::load();
	$dataset_count = 0;
	$labels = null;
	foreach($data as $dataname=>$dataset) {
		if($dataname=='label') {
			$labels = $dataset;
		}
		else {
			$dataset_count++;
			$plot = \App\ThirdParty\jpgraph::plot($display, $dataset);
			$graph->Add($plot);
			$plot->SetLegend($dataname);
			$colour = $colours[$dataname] ?? null;
			switch($display) {
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
			# d($interval); return;
			$interval = 2;
			$graph->xaxis->SetTextLabelInterval($interval);
			$graph->xgrid->SetColor('#dde', '#99F');
			$graph->xgrid->Show(true);
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

public function getRain($start='', $end='', $display=null) {
	$map = [
		'rain_max' => 'rain'
	];
	
	$options = [
		'ytitle' => 'Rainfall [mm]',
		'colours' => [
			'rain' => '#66F'
		],
		'display' => 'bar'
	];
	return $this->stroke($map, $options);		
}

public function getTemperature($start='', $end='', $display=null) {
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
	return $this->stroke($map, $options);
}

public function getSolar($start='', $end='', $display=null) {

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
	return $this->stroke($map, $options);
}

public function getWind($start='', $end='', $display=null) {
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
	return $this->stroke($map, $options);
}

public function getHumidity($start='', $end='', $display=null) {
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
	return $this->stroke($map, $options);
}

}
