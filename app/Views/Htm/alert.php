<?php namespace App\Views\Htm;

class alert implements \stringable {

public $template = '<div %s>%s</div>';
public $message = '';
public $attrs = [
	'role' => "alert",
	'style' => "padding:.5em;
		margin:.3em;
		background:#fbe2cc;
		border:1px solid #c00;
		font-weight:bold;"
];

function __construct($message) {
	$this->message = $message;
}

function __toString() {
	ob_start();
	if($this->message) {
		printf($this->template, \stringify_attributes($this->attrs), $this->message);
	}
	return ob_get_clean();
}

}
