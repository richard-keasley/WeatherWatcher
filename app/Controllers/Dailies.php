<?php namespace App\Controllers;

class Dailies extends \App\Controllers\BaseController {
	
function getIndex() {
	$model = new \App\Models\Dailies;
	$this->data['daily'] = $model->orderBy('date', 'desc')->first();
	$this->data['start'] = $this->data['daily']->date;
	$this->data['end'] = $this->data['start'];
	return view('dailies/day', $this->data);
}

function getDay($datetime=null) {
	$model = new \App\Models\Dailies;
	$datetime = $this->get_datetime($datetime, 'value');
	if(!$datetime) $datetime = new \DateTime('yesterday');
	$this->data['start'] = $datetime->format('Y-m-d');
	$this->data['end'] = $this->data['start'];
	$this->data['daily'] = $model->find($this->data['start']);
	return view('dailies/day', $this->data);	
}	

function getMonth($datetime=null) {
	$model = new \App\Models\Dailies;
	$datetime = $this->get_datetime($datetime, 'value');
	if(!$datetime) $datetime = new \DateTime('yesterday');
	$this->data['start'] = $datetime->format('Y-m-01');
	$this->data['end'] = $datetime->format('Y-m-t');

	$model = new \App\Models\Dailies;
	$this->data['dailies'] = $model
		->where('date >=', $this->data['start'])
		->where('date <=', $this->data['end'])
		->findAll();
	
	return view('dailies/month', $this->data);	
}

function getWeek($datetime=null) {
	$model = new \App\Models\Dailies;
	$datetime = $this->get_datetime($datetime, 'value');
	if(!$datetime) $datetime = new \DateTime('Monday');
	
	$week_day = intval($datetime->format('N'));
	if($week_day>1) {
		$week_day--;
		$interval = new \DateInterval("P{$week_day}D");
		$datetime->sub($interval);
	}
	
	$this->data['start'] = $datetime->format('Y-m-d');
	$interval = new \DateInterval('P6D');
	$datetime->add($interval);
	$this->data['end'] = $datetime->format('Y-m-d');

	$model = new \App\Models\Dailies;
	$this->data['dailies'] = $model
		->where('date >=', $this->data['start'])
		->where('date <=', $this->data['end'])
		->findAll();
	
	return view('dailies/week', $this->data);	
}

function getYear($datetime=null) {
	$model = new \App\Models\Dailies;
	$datetime = $this->get_datetime($datetime, 'value');
	if(!$datetime) $datetime = new \DateTime();
	
	$year = $datetime->format('Y');
	$this->data['start'] = "{$year}-01-01";
	$this->data['end'] = "{$year}-12-31";
	
	$model = new \App\Models\Dailies;
	$this->data['dailies'] = $model
		->where('date >=', $this->data['start'])
		->where('date <=', $this->data['end'])
		->findAll();
	
	return view('dailies/year', $this->data);	
}

function getCustom($start='', $end='') {
	// compare to App\Controllers\Graph\Dailies
	$dt_start = $this->get_datetime($start, 'value');
	if(!$dt_start) $dt_start = new \DateTime;
	$dt_end = $this->get_datetime($end, 'value');
	if(!$dt_end) $dt_end = new \DateTime;
	if($dt_end<$dt_start) {
		$swap = $dt_end;
		$dt_end = $dt_start;
		$dt_start = $swap;
	}	
	
	$model = new \App\Models\Dailies;
	$dt_first = $model->dt_first();
	$dt_last = $model->dt_last();
	if($dt_start<$dt_first) $dt_start = $dt_first;
	if($dt_start>$dt_last) $dt_start = $dt_last;

	$max_range = new \DateInterval('P90D');
	$dt_max = new \datetime($dt_start->format(DATE_W3C));
	$dt_max->add($max_range);
	if($dt_max>$dt_last) $dt_max = $dt_last;
	
	if($dt_end<$dt_first) $dt_end = $dt_first;
	if($dt_end>$dt_max) $dt_end = $dt_max;
			
	// view	
	$this->data['dt_first'] = $dt_first;
	$this->data['dt_last'] = $dt_last;
	$this->data['max_range'] = $max_range;
	$this->data['start'] = $dt_start->format('Y-m-d');
	$this->data['end'] = $dt_end->format('Y-m-d');
	
	$this->data['dailies'] = $model
		->where('date >=', $this->data['start'])
		->where('date <=', $this->data['end'])
		->findAll();
	
	return view('dailies/custom', $this->data);
}

function getAverages() {
	$datetime = new \DateTime;	
	$this->data['start'] = $datetime->format('Y-m-d');
	$this->data['end'] = $datetime->format('Y-m-d');
	return view('dailies/averages', $this->data);
}

}
