<?php namespace App\Controllers\Test;

class Readings extends Home {

public function getIndex() {
	return view('test/readings', $this->data);
}


}
