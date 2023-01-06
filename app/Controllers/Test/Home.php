<?php namespace App\Controllers\Test;

class Home extends \App\Controllers\BaseController {

function __construct() {
	if(ENVIRONMENT!='development') throw new \exception('Service closed', 404);
}

public function getIndex() {
	return view('test/index', $this->data);
}

}
