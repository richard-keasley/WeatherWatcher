<?php

namespace App\Filters;

class Auth implements \CodeIgniter\Filters\FilterInterface {
	
public function before($request, $arguments = null) {
	helper('cookie');
	$app = config('App');
	
	// user stored in either session or cookie
	$tests = [
		session('usr'),
		get_cookie('usr'),
	];
	foreach($tests as $usr) {
		if($usr===$app->usr) {
			set_cookie('usr', $app->usr, config('Cookie')->expires);
			return;
		}
	}
			
	// image 
	$segments = $request->getUri()->getSegments();
	$zone = $segments[0] ?? '' ;
	switch($zone) {
		case 'graph':
		\App\ThirdParty\jpgraph::blank(5, 5);
		die;
		
		default:
		return redirect()->to(site_url('auth'));
	}
}

public function after($request, $response, $arguments = null) {

}
	
}
