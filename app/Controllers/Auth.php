<?php namespace App\Controllers;

class Auth extends BaseController {

public function getIndex() {
	$this->data['usr'] = '';
	$this->data['pwd'] = '';
	return view('auth', $this->data);
}

public function postIndex() {
	$this->data['usr'] = $this->request->getPost('usr');
	$this->data['pwd'] = $this->request->getPost('pwd');
	
	$config = config('App');
	$success = 
		$config->usr == $this->data['usr'] &&
		$config->pwd == $this->data['pwd'] ;

	if($success) {
		$session = session();
		$session->set(['usr'=>$this->data['usr']]);
		return redirect()->to('/');
	}
	return view('auth', $this->data);
}

}
