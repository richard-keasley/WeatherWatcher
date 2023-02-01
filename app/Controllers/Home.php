<?php namespace App\Controllers;

class Home extends BaseController {

public function index() {
	$this->data['reading'] = $this->data['readings']->get_current();
	$this->data['date'] = $this->data['reading']->get_datetime('Y-m-d');
	return view('index', $this->data);
}

}
