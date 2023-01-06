<?php namespace App\Controllers;

class Upload extends BaseController {

// NB: get / index is protected by config/routes
public function postIndex() {
	$listener = $this->data['listener'];
	$readings = $this->data['readings'];

	// version 1
	/*
	$success = $this->data['listener']->read_array($this->request->getPost());
	if($success) return 'OK';
	$this->response->setStatusCode(400);
	return 'fail';
	// */
	
	// version 2
	$listener->inputs = $this->request->getPost();
	$sucess = $listener->check_keys();
	if($sucess) $sucess = $listener->process();
	if($sucess) $sucess = $readings->add_reading($sucess);
	if($sucess) return 'OK';
	$this->response->setStatusCode(400);
	return 'fail';
}

}
