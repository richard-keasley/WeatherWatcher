<?php namespace App\Controllers\Graph;

class Dailies extends Home {

private function stroke($map, $options) {
	$segments = $this->getSegments($options);
		
	// valid dates
	$dt_start = $segments['dt_start'] ?? new \DateTime();
	$dt_end = $segments['dt_end'] ?? new \DateTime();
	$model = new \App\Models\Dailies;
	$dt_first = $model->dt_first();
	$dt_last = $model->dt_last();
	if($dt_start<$dt_first) $dt_start = $dt_first;
	if($dt_start>$dt_last) $dt_start = $dt_last;
	if($dt_end<$dt_first) $dt_end = $dt_first;
	if($dt_end>$dt_last) $dt_end = $dt_last;
	$segments['dt_start'] = $dt_start;
	$segments['dt_end'] = $dt_end;
	
	# d($segments); return;
	$cache_data = $this->check_cache($segments);
		
	// load data
	$raw_data = $model
		->where('date >=', $dt_start->format('Y-m-d'))
		->where('date <=', $dt_end->format('Y-m-d'))
		->findAll();
	
	// apply map
	$data = ['datetime'=>[]];
	foreach($map as $source=>$dest) $data[$dest] = [];
	foreach($raw_data as $daily) {
		$data['datetime'][] = $daily->get_date();
		foreach($map as $source=>$dest) {
			$data[$dest][] = $daily->$source;
		}
	}
	# d($raw_data, $data); return;
		
	// aggregate data
	$span = intval($dt_start->diff($dt_end)->format('%a'));
	// NB: key_format used for graph x-axis format below
	$key_format = $span>100 ? 'Y_W' : 'Ymd' ; // either weekly or daily
	$data = \App\ThirdParty\jpgraph::periodise($data, $key_format);
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
			$tick_align = $key_format == 'Y_W' ? DAYADJ_7 : DAYADJ_1;
			$graph->xaxis->scale->SetDateAlign($tick_align);
			break;
			
			case 'line':
			default:
			$plot->SetWeight(2);
			if($colour) $plot->SetColor($colour);
		}		
	}

	if($dataset_count) {
		$graph->legend->SetPos(0.05, 0.01, 'left', 'top');
			
		\App\ThirdParty\jpgraph::include_file('jpgraph_utils.inc');
		$dateUtils = new \DateScaleUtils();
		$tick_pos = $key_format == 'Y_W' ? DSUTILS_WEEK1 : DSUTILS_DAY1;
		list($tickPositions, $minTickPositions) = $dateUtils->GetTicks($data['datetime'], $tick_pos);
		$graph->xaxis->SetTickPositions($tickPositions, $minTickPositions);
		$graph->xaxis->scale->SetDateFormat('j M');
		$graph->xaxis->SetPos("min");
		$graph->xaxis->SetLabelAngle(90);
		
		$graph->xgrid->SetColor('#dde', '#99F');
		$graph->xgrid->Show(true);
				
		$ytitle = $options['ytitle'] ?? null;
		if($ytitle) {
			$graph->yaxis->title->Set($ytitle);
		}
		
		$title = $options['title'] ?? 'Daily averages';
		if($title) $graph->title->Set($title);
		# d($graph); return;
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
