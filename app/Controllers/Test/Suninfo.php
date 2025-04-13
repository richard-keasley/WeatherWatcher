<?php namespace App\Controllers\Test;

class Suninfo extends Home {

public function getIndex() {
	ob_start();
	include \App\ThirdParty\suninfo::example();
	return ob_get_clean();
}


}