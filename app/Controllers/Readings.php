<?php namespace App\Controllers;

class Readings extends BaseController {

public function getIndex() {
	$dt = intval($this->request->getGet('dt'));
	if($dt) {
		$datetime = new \datetime();
		$datetime->setTimeStamp($dt);
		$this_dt = $datetime->format('Y-m-d H:i:s');
		$nav = $this->request->getGet('nav');
	}
	else $nav = 'current';
	
	$val = match($nav) {
		'prev' => $this->data['readings']
			->where('datetime <', $this_dt)
			->orderBy('datetime', 'desc')
			->first(),
		'next' => $this->data['readings']
			->where('datetime >', $this_dt)
			->orderBy('datetime', 'asc')
			->first(),
		default => null
	};
	if(!$val) {
		$val = match($nav) {
			'first' => $this->data['readings']->get_first(),
			'prev' => $this->data['readings']->get_first(),
			default => $this->data['readings']->get_current()
		};
	}
	$this->data['reading'] = $val;
		
	$datetime = new \datetime($this->data['reading']->datetime);
	$this->data['dt'] = $datetime->format('U');
	$this->data['date'] = $datetime->format('Y-m-d');
	
	return view('readings/index', $this->data);
}

function getDaily($date=null) {
	try {
		$datetime = new \DateTime($date);
	}
	catch(\Exception $ex) {
		$datetime = new \DateTime();
	}	
	$this->data['date'] = $datetime->format('Y-m-d');
	$this->data['daily'] = $this->data['readings']->get_daily($datetime);
	return view('readings/daily', $this->data);
}

}
