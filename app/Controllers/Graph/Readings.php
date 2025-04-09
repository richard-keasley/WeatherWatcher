<?php namespace App\Controllers\Graph;

class Readings extends Home {

private function stroke($map, $options) {
	$segments = $this->getSegments($options);
	
	// valid dates
	$dt_start = $segments['dt_start'] ?? new \DateTime('today');
	$dt_end = $segments['dt_end'] ?? null;
		
	$title = $options['title'] ?? '' ;
	if(!$title) {
		$title = 'Station readings: ';
		$title .= $dt_end ? 
			$dt_start->format('d/m/y') . '-' . $dt_end->format('d/m/y') :
			$dt_start->format('j F Y');
	}
	// don't set dt_end until title is set
	if(!$dt_end) $dt_end = clone $dt_start; // get daily according to dt_start 
	# d($segments, $dt_start, $dt_end); return;
		
	$oneday = new \DateInterval('PT24H');
	$dt_end->add($oneday);
	
	$segments['dt_start'] = $dt_start;
	$segments['dt_end'] = $dt_end;
	
	# d($segments); return;
	$cache_data = $this->check_cache($segments);
	# d($cache_data); return;
	
	// load data
	$model = new \App\Models\Readings;
	$raw_data = $model
		->where('datetime >=', $dt_start->format('Y-m-d H:i:s'))
		->where('datetime <', $dt_end->format('Y-m-d H:i:s'))
		->orderBy('datetime')
		->findAll();
	$dt_period = $dt_end->format('U') - $dt_start->format('U');
	# d($dt_period, $raw_data); return; 
	
	// apply map
	$data = ['datetime'=>[]];
	foreach($map as $source=>$dest) $data[$dest] = [];
	foreach($raw_data as $entity) {
		$datetime = new \DateTime($entity->datetime);
		$data['datetime'][] = $entity->get_datetime();
		$readings = $entity->get_readings();
		foreach($map as $source=>$dest) {
			$data[$dest][] = $readings[$source];
		}
	}
	# d($data); return;
		
	// aggregate data
	$data = \App\ThirdParty\jpgraph::periodise($data, 'YmdH');
	# d($data); return;
		
	$colours = $options['colours'] ?? null;
	$fillcolor = $options['fillcolor'] ?? null;
	$y2 = $options['y2'] ?? '#none#';
	
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
		if($dataname===$y2) {
			$graph->SetY2Scale('lin');
			$graph->AddY2($plot);
		}
		else {
			$graph->Add($plot);
		}
		$plot->SetLegend($dataname);
		$colour = $colours[$dataname] ?? null;
		switch($display) {
			case 'bar':
			$plot->SetWidth($bar_width);
			if($colour) $plot->SetFillColor($colour);
			$plot->SetColor('#666');
			$plot->SetWeight(1);
			$graph->xaxis->scale->SetTimeAlign(HOURADJ_1);
			break;
			
			case 'line':
			default:
			$plot->SetWeight(2);
			if($colour) $plot->SetColor($colour);
			if($fillcolor) $plot->Setfillcolor($fillcolor);				
		}
	}
			
	if($dataset_count) {
		$tick_set = 3600;
		if($dt_period>86400) $tick_set = 7200;
		if($dt_period>172400) $tick_set = 21600;
		if($dt_period>689600) $tick_set = 86400;
		# d($dt_period, $tick_set); return;
		
		$graph->legend->SetPos(0.05, 0.01, 'left', 'top');
		$graph->xaxis->SetTickLabels($data['datetime']);
		$graph->xaxis->SetLabelAngle(90);
		$graph->xaxis->scale->ticks->Set($tick_set);
		$graph->xaxis->scale->SetDateFormat('j H:i');
		$graph->xaxis->SetPos("min");
		
		$ytitle = $options['ytitle'] ?? null;
		if($ytitle) {
			$graph->yaxis->title->Set($ytitle);
		}
		
		$graph->title->Set($title);
		#d($graph->yaxis); return;		
		\App\ThirdParty\jpgraph::stroke($graph, $cache_data);
		die;
	}
	
	\App\ThirdParty\jpgraph::blank();
	die;
}

public function getRain($start='', $end='', $display=null) {
	$map = [
		'rain_day' => 'rain'
	];
	
	$options = [
		'ytitle' => 'Rainfall [mm]',
		'colours' => [
			'rain' => "#66F"
		],
		'fillcolor' => "#DDF"
	];
	return $this->stroke($map, $options);		
}

public function getTemperature($start='', $end='', $display=null) {
	$map = [
		'temperature_out' => 'temperature'
	];
	$options = [
		'ytitle' => 'Temperature [°C]',
		'colours' => [
			'temperature' => '#c11'
		]
	];
	return $this->stroke($map, $options);
}

public function getSolar($start='', $end='', $display=null) {
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
	return $this->stroke($map, $options);
}

public function getWind($start='', $end='', $display=null) {
	$map = [
		'wind_speed' => 'speed',
		'wind_gust' => 'gust'
	];
	
	$options = [
		'ytitle' => 'Wind [mph]',
		'colours' => [
			'speed' => '#666',
			'gust' => '#C66'
		]
	];
	return $this->stroke($map, $options);
}

public function getHumidity($start='', $end='', $display=null) {
	$map = [
		'humidity_out' => 'humidity'
	];
	
	$options = [
		'ytitle' => 'Humidity [%]',
		'colours' => [
			'humidity' => '#339'
		]
	];
	return $this->stroke($map, $options);
}

public function getIndoors($start='', $end='', $display=null) {
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
	return $this->stroke($map, $options);
}

}
