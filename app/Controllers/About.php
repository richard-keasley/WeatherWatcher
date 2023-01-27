<?php namespace App\Controllers;

class About extends BaseController {

public function getIndex() {
	return view('about/index', $this->data);
}

}
