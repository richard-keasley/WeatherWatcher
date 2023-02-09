<?php namespace App\Views\Htm;

class custom implements \stringable {

private $template = null;
private $path = ROOTPATH . '/custom';

function __construct($template) {
	$this->template = $template;
}

function __toString() {
	$realpath = realpath($this->path);
	if(!$realpath) return "<p>Custom path <code>{$this->path}</code> does not exist!</p>";
	# d($realpath);
	$filename = $realpath . '/' . $this->template . '.php';
	# d($filename);
	if(!file_exists($filename)) return "<p>Custom file <code>{$filename}</code> does not exist!</p>";
	ob_start();
	include $filename;
	return ob_get_clean();
}

}
