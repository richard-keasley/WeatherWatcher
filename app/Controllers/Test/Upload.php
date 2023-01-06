<?php namespace App\Controllers\Test;

class Upload extends Home {

public function getIndex() {
	return view('test/upload', $this->data);
}

public function postIndex() {
	$success = $this->data['listener']->read_array($this->request->getPost());
	$this->data['message'] = $success ? 'OK' : $this->data['listener']->error;
	return view('upload/test', $this->data);
}

}
