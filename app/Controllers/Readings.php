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
	
	$this->data['reading'] = match($nav) {
		'first' => $this->data['readings']->get_first(),
		'prev' => $this->data['readings']
			->where('datetime <', $this_dt)
			->orderBy('datetime', 'desc')
			->first(),
		'next' => $this->data['readings']
			->where('datetime >', $this_dt)
			->orderBy('datetime', 'asc')
			->first(),
		default => $this->data['readings']->get_current()
	};
	
	$datetime = new \datetime($this->data['reading']->datetime);
	$this->data['dt'] = $datetime->format('U');
	$this->data['date'] = $datetime->format('Y-m-d');
	
	return view('readings/index', $this->data);
}

function getDaily($date=null) {
	$this->data['date'] = $date;
	$this->data['daily'] = $this->data['readings']->get_daily($date);
	return view('readings/daily', $this->data);
}

}
