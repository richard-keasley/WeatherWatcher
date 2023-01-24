<?php namespace App\Controllers\Graph;

class Home extends \App\Controllers\BaseController {

protected function init() {
}

public function getIndex() {
	$this->init();
	d($this->data);
}

}
