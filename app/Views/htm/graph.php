<?php namespace App\Views\Htm;

class graph implements \stringable {

public $type = null;
public $start = null;
public $end = null;
public $template = 
'<figure class="graph">
<a href="{src}"><img src="{src}"></a>
<figcaption>{caption}</figcaption>
</figure>';

function __construct($type='temperature', $start=null, $end=null) {
	$this->type = $type;
	$this->start = $start;
	$this->end = $end;
}

function __toString() {
	$type = $this->type;
	if(!$type) return '';
	$start = $this->start;
	if(!$start) return '';
	$end = $this->end; 
	if(!$end) return '';
	
	if($end<$start) {
		$swap = $end;
		$end = $start;
		$start = $swap;
	}
	
	$trans = [
		'{src}' => base_url("graph/dailies/{$type}/{$start}/{$end}"),
		'{caption}' => $type

	];
	
	return strtr($this->template, $trans);
	
	
	
	
	
	
}

}
