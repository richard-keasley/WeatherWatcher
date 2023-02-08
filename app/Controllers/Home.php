<?php namespace App\Controllers;

class Home extends BaseController {

public function index() {
	$this->data['reading'] = $this->data['readings']->get_current();
	
	$end = $this->data['reading']->get_datetime();
	$interval = new \DateInterval('P1D');
	$this->data['end'] = $end->format('Y-m-d');
	$this->data['start'] = $end->sub($interval)->format('Y-m-d');
	return view('index', $this->data);
}

}
