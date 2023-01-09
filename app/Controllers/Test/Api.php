<?php namespace App\Controllers\Test;

class Api extends Home {

public function getIndex() {
	$this->data['query'] = [
		'start_date' => '2022-12-15 12:00:00',
		'end_date' => '2022-12-16 01:00:00',
		'cycle_type' => 'auto',
		'call_back' => 'outdoor,indoor,solar_and_uvi,rainfall,rainfall_piezo,wind,pressure,lightning'
	];
	$this->data['pathkey'] = 0;

	return view('test/api', $this->data);
}

public function postIndex() {
	$string = $this->request->getpost('start_date') . ' ' .$this->request->getpost('start_time');
	$start_date = new \datetime($string);
	$string = $this->request->getpost('end_date') . ' ' .$this->request->getpost('end_time');
	$end_date = new \datetime($string);	
	
	$this->data['query'] = [
		'start_date' => $start_date->format('Y-m-d H:i:s'),
		'end_date' => $end_date->format('Y-m-d H:i:s'),
		'cycle_type' => $this->request->getpost('cycle_type'),
		'call_back' => $this->request->getpost('call_back')
	];
	
	$this->data['pathkey'] = $this->request->getpost('pathkey');
	$path = $this->data['api']::paths[$this->data['pathkey']] ?? null;
	# d($path, $this->data['query']);
	
	$this->data['daily'] = null;
	switch($this->request->getPost('cmd')) {
		case 'daily':
		$this->data['daily'] = $this->data['api']->get_daily($start_date);
		break;
		
		default:
		$this->data['api']->call($path, $this->data['query']);
	}
	
	return view('test/api', $this->data);
}

}
