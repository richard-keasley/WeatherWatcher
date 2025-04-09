<?php namespace App\Controllers\Graph;

class Averages extends Home {

private function stroke($map, $options=[]) {
	$segments = $this->getSegments($options);
	
	// valid dates (no options allowed)
	$segments['dt_start'] = new \DateTime('today');
	$segments['dt_end'] = '';
	
	# d($segments); return;
	$cache_data = $this->check_cache($segments);
	
	// load data
	$model = new \App\Models\Dailies;	
	$raw_data = $model->orderBy('date')->findAll();
	
	// apply map
	$data = ['datetime'=>[]];
	foreach($map as $source=>$dest) $data[$dest] = [];
	foreach($raw_data as $daily) {
		$data['datetime'][] = $daily->get_date();
		foreach($map as $source=>$dest) {
			$data[$dest][] = $daily->$source;
		}
	}
	# d($data); return;
	
	// get current data
	$mapkey = array_key_first($map);
	
	$datetime = new \DateTime('today');
	$end = $datetime->format('Y-m-d');
	$year = new \DateInterval('P1Y');
	$start = $datetime->sub($year)->format('Y-m-d');
	$current = ['datetime'=>[], 'current'=>[]];
	$raw_data = $model->orderBy('date')->where('date >=', $start)->where('date <', $end)->findAll();
	foreach($raw_data as $daily) {
		$current['datetime'][] = $daily->get_date();
		$current['current'][] = $daily->$mapkey;
	}
			
	// aggregate data
	$key_format = 'W';
	$data = \App\ThirdParty\jpgraph::periodise($data, $key_format);
	$current = \App\ThirdParty\jpgraph::periodise($current, $key_format);
	
	// sort and join datasets, starting a year ago
	$combined = [];
	$dt = new \DateTime;
	foreach($current['current'] as $key=>$value) {
		$wknum = $dt->setTimestamp($current['datetime'][$key])->format($key_format);
		$combined[$wknum]['datetime'] = $current['datetime'][$key];
		$combined[$wknum]['current'] = $value;
		
	}
	foreach($data as $dataname=>$dataset) {
		if($dataname=='datetime') continue;
		foreach($dataset as $key=>$value) {
			$wknum = $dt->setTimestamp($data['datetime'][$key])->format($key_format);
			$combined[$wknum][$dataname] = $value;
		}
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
	
	// display data
	$display = $segments['display'];
	if($display=='table') {
		$this->data['data'] = $data;
		return view('data', $this->data);
	}

	// send image back to browser
	$graph = \App\ThirdParty\jpgraph::load();
	$colours = $options['colours'] ?? null;
	$data_count = count($data['datetime']);
	$bar_width = $data_count ? $graph->img->plotwidth / $data_count : 1;
	$dataset_count = 0;
	foreach($data as $dataname=>$dataset) {
		if($dataname=='datetime') continue;
		$dataset_count++;
		$plot = \App\ThirdParty\jpgraph::plot($display, $dataset, $data['datetime']);
		$graph->Add($plot);
		$plot->SetLegend($dataname);
		$colour = $colours[$dataname] ?? null;
		switch($display) {
			case 'bar':
			$plot->SetWidth($bar_width);
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
			
	if($dataset_count) {
		$graph->legend->SetPos(0.05, 0.01, 'left', 'top');
	
		$graph->xaxis->SetLabelAngle(90);
		$interval = 4; #intval(count($labels)/17.5) + 1;
		$graph->xaxis->SetTextLabelInterval($interval);
		$graph->xaxis->scale->SetDateFormat('d-M');
		$graph->xaxis->SetPos("min");

		$graph->xgrid->SetColor('#dde', '#99F');
		$graph->xgrid->Show(true);
	
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

public function getRain($display=null) {
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
	return $this->stroke($map, $options);		
}

public function getTemperature($display=null) {
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
	return $this->stroke($map, $options);
}

public function getSolar($display=null) {
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
	return $this->stroke($map, $options);
}

public function getWind($display=null) {
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
	return $this->stroke($map, $options);
}

public function getHumidity($display=null) {
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
	return $this->stroke($map, $options);
}

}
