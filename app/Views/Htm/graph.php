<?php namespace App\Views\Htm;

class graph implements \stringable {

public $dataname = null;
public $start = null;
public $end = null;
public $template = 
'<figure class="graph">
<a href="{src}"><img src="{src}"></a>
<figcaption>{caption}</figcaption>
</figure>';

function __construct($controller='dailies', $dataname='temperature', $start=null, $end=null) {
	$this->controller = $controller;
	$this->dataname = $dataname;
	$this->start = $start;
	$this->end = $end;
}

function __toString() {
	$controller = $this->controller;
	if(!$controller) return '';
	$dataname = $this->dataname;
	if(!$dataname) return '';
	$start = $this->start;
	if(!$start) return '';
	$end = $this->end; 
	if(!$end) return '';
	
	if($end<$start) {
		$swap = $end;
		$end = $start;
		$start = $swap;
	}
	
	$translate = [
		'{src}' => base_url("graph/{$controller}/{$dataname}/{$start}/{$end}"),
		'{caption}' => $dataname
	];
	
	return strtr($this->template, $translate);
}

}
