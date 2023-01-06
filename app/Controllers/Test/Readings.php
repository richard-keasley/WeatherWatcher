<?php namespace App\Controllers\Test;

class Readings extends Home {

private function init() {

}

public function getIndex() {
	return view('test/readings', $this->data);
}


}
