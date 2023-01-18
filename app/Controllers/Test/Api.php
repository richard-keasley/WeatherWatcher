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
	$this->data['daily'] = null;

	return view('test/api', $this->data);
}

public function postIndex() {
	$dt_format = 'Y-m-d H:i:s';
	$string = $this->request->getpost('start_date') . ' ' .$this->request->getpost('start_time');
	$dt_start = new \datetime($string);
	$string = $this->request->getpost('end_date') . ' ' .$this->request->getpost('end_time');
	$dt_end = new \datetime($string);	
		
	$this->data['query'] = [
		'start_date' => $dt_start->format($dt_format),
		'end_date' => $dt_end->format($dt_format),
		'cycle_type' => $this->request->getpost('cycle_type'),
		'call_back' => $this->request->getpost('call_back')
	];
	
	$this->data['pathkey'] = $this->request->getpost('pathkey');
	$path = $this->data['api']::paths[$this->data['pathkey']] ?? null;
	# d($path, $this->data['query']);
	
	$this->data['daily'] = null;
	switch($this->request->getPost('cmd')) {
		case 'daily':
		$dt_interval = new \DateInterval('P1D');
		$dailies = new \App\Models\Dailies;
		$dt_request = $dailies->dt_last()->add($dt_interval);
		$dt_last = new \DateTime();
		$dt_last->setTime(0, 0);
		
		$daily = null;
		while($dt_request<$dt_last) {
			d($dt_request);
			# $daily = $this->data['api']->get_daily($dt_request);
			# d($daily);
			# $dailies->insert($daily);
			$dt_request->add($dt_interval);
		}
		$this->data['daily'] = $daily;
		
		break;
		
		default:
		$this->data['api']->call($path, $this->data['query']);
	}
	
	return view('test/api', $this->data);
}

}
