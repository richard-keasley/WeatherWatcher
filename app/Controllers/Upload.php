<?php namespace App\Controllers;

class Upload extends BaseController {

// NB: get / index is protected by config/routes
public function postIndex() {
	$listener = $this->data['listener'];
	$readings = $this->data['readings'];
	$logfile = WRITEPATH . 'upload.txt';
	$logtext = [date('Y-m-d H:i:s')];
		
	$listener->inputs = $this->request->getPost();
	$logtext[] = print_r($listener->inputs, 1);
	
	$sucess = $listener->check_keys();
	if($sucess) {
		$logtext[] = "check_keys OK";
		$sucess = $listener->process();
	}
	
	if($sucess) {
		$logtext[] = "process OK";
		$sucess = $readings->add_reading($sucess);
	}
	
	if($sucess) {
		$logtext[] = "add_reading OK";
	}
	
	if(ENVIRONMENT=='development') {
		file_put_contents($logfile, implode("\n\n", $logtext));
	}
	
	if($sucess) return 'OK';
	$this->response->setStatusCode(400);
	return 'fail';
}

function _getIndex() {
	return $this->postIndex();
}

}
