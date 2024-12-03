<?php

namespace App\Filters;

class Auth implements \CodeIgniter\Filters\FilterInterface {
	
public function before($request, $arguments = null) {
	$segments = $request->uri->getSegments();
	$zone = $segments[0] ?? '' ;
	// allowed for everyone
	$allowed = ['upload', 'auth'];
	if(in_array($zone, $allowed)) return;

	// logged in user
	if(session('usr')===config('App')->usr) return;
	
	// image 
	switch($zone) {
		case 'graph':
		\App\ThirdParty\jpgraph::blank(5, 5);
		break;
		
		default:
		return redirect()->to(site_url('auth'));
	}
}

public function after($request, $response, $arguments = null) {

}
	
}
