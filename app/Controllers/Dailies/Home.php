<?php namespace App\Controllers\Dailies;

class Home extends \App\Controllers\BaseController {
	
private $model = null;

function getIndex() {
	// compare to App\Controllers\Graph\Dailies
	$start = $this->request->getGet('start');
	$end = $this->request->getGet('end');
	$this->model = new \App\Models\Dailies;
	
	try {
		$dt_start = new \datetime($start);
		$dt_end = new \datetime($end);
	}
	catch(\Exception $e) {
		$dt_start = new \datetime();
		$dt_end = new \datetime();
	}
	
	if($dt_end<$dt_start) {
		$swap = $dt_end;
		$dt_end = $dt_start;
		$dt_start = $swap;
	}	
	
	$dt_first = $this->model->dt_first();
	$dt_last = $this->model->dt_last();
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
	
	$this->data['datarows'] = $this->model
		->where('date >=', $this->data['start'])
		->where('date <=', $this->data['end'])
		->findAll();
	
	return view('dailies/index', $this->data);
}

}
