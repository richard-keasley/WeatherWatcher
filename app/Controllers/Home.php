<?php namespace App\Controllers;

class Home extends BaseController {

public function index() {
	$this->data['reading'] = $this->data['readings']->get_current();
	return view('index', $this->data);
}

}
