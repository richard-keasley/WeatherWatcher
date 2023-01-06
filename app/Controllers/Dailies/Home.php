<?php namespace App\Controllers\Dailies;

class Home extends \App\Controllers\BaseController {

public function getIndex() {
	$model = new \App\Models\Dailies;
	$last = $model
		->orderBy('date', 'desc')
		->first();
	$last_daily = new \datetime($last->date);
	$yesterday = new \datetime('yesterday'); 
	d($last);
	
	/*
	if($last_daily<$yesterday) {
		$api = new \App\Libraries\Apis\Ecowitt;
		 d($yesterday->format('Y-m-d'));
		 d($last_daily->format('Y-m-d'));
		
		$get_daily = $last_daily;
		$one_day = new \DateInterval('P1D');
		
		while($get_daily<$yesterday) {
			$get_daily->add($one_day);
			$results = $api->get_daily($get_daily);
			d($results);
		}
	}
	*/
	
	
	
	
	
	$string = $this->request->getGet('start');
	$datetime = new \datetime($string);
	$this->data['start'] = $datetime->format('Y-m-d');
	$string = $this->request->getGet('end');
	$datetime = new \datetime($string);
	$this->data['end'] = $datetime->format('Y-m-d');
	
	$model = new \App\Models\Dailies;
	$this->data['datarows'] = $model
		->where('date >=', $this->data['start'])
		->where('date <=', $this->data['end'])
		->findAll();
	
	return view('dailies/index', $this->data);
}

}
