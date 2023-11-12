<?php namespace App\Controllers\Test;

class Readings extends Home {

public function getIndex($date='') {
	try {
		$datetime = new \datetime($date);
	}
	catch(\exception $ex) {
		$datetime = new \datetime();
	}

	$this->data['datetime'] = $datetime;
	return view('test/readings', $this->data);
}

}
