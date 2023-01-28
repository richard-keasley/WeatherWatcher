<?php namespace App\Controllers;

class Dailies extends \App\Controllers\BaseController {
	
function getIndex() {
	$model = new \App\Models\Dailies;
	$this->data['daily'] = $model->orderBy('date', 'desc')->first();
	$this->data['navdate'] = $this->data['daily']->date;
	return view('dailies/index', $this->data);
}

function getDay($datetime=null) {
	$model = new \App\Models\Dailies;
	$datetime = $this->get_datetime($datetime, 'value');
	if(!$datetime) $datetime = new \DateTime('yesterday');
	$this->data['navdate'] = $datetime->format('Y-m-d');
	$this->data['daily'] = $model->find($this->data['navdate']);
	return view('dailies/index', $this->data);	
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
	$this->data['navdate'] = $this->data['start'];
	
	return view('dailies/month', $this->data);	
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

	$max_range = new \DateInterval('P60D');
	$dt_max = new \datetime($dt_start->format(DATE_W3C));
	$dt_max->add($max_range);
	if($dt_max>$dt_last) $dt_max = $dt_last;
	
	if($dt_end<$dt_first) $dt_end = $dt_first;
	if($dt_end>$dt_max) $dt_end = $dt_max;
		
	$nav = $this->request->getGet('nav');
	$this_range = $dt_start->diff($dt_end);
	if($nav) {
		$interval = new \DateInterval('P1D');
		switch($nav) {
			case 'prev':
			$dt_end = new \datetime($dt_start->format(DATE_W3C));
			$dt_end->sub($interval);
			$dt_start = new \datetime($dt_end->format(DATE_W3C));
			$dt_start->sub($this_range);
			if($dt_start<$dt_first) $dt_start = $dt_first;
			if($dt_end<$dt_first) $dt_end = $dt_first;
			break;
			
			case 'next':
			$dt_start = new \datetime($dt_end->format(DATE_W3C));
			$dt_start->add($interval);
			$dt_end = new \datetime($dt_start->format(DATE_W3C));
			$dt_end->add($this_range);
			if($dt_end>$dt_last) $dt_end = $dt_last;
			if($dt_start>$dt_last) $dt_start = $dt_last;
			break;
		}		
	}
		
	// view	
	$this->data['dt_first'] = $dt_first;
	$this->data['dt_last'] = $dt_last;
	$this->data['this_range'] = $this_range;
	$this->data['max_range'] = $max_range;
	$this->data['start'] = $dt_start->format('Y-m-d');
	$this->data['end'] = $dt_end->format('Y-m-d');
	
	$this->data['datarows'] = $model
		->where('date >=', $this->data['start'])
		->where('date <=', $this->data['end'])
		->findAll();
	
	return view('dailies/custom', $this->data);
}

}
