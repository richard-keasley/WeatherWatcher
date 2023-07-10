<?php namespace App\Controllers\Test;

class Moonphase extends Home {

public function getIndex() {
	return view('test/moonphase', $this->data);
}


}