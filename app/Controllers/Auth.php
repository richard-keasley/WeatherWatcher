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
	
	$app = config('App');
	$success = 
		$app->usr == $this->data['usr'] &&
		$app->pwd == $this->data['pwd'] ;

	if($success) {
		$session = session();
		$session->set(['usr'=>$app->usr]);
		return redirect()->to('/');
	}
	return view('auth', $this->data);
}

}
