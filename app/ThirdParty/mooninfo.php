<?php namespace App\ThirdParty;

class mooninfo {

const version = '1.0.3';
const date = '2026-06-23';
	
static $registered = false;
static function autoload() {
	if(static::$registered) return true;
	spl_autoload_register(function($classname) {
		$target = 'basecamp\mooninfo';
		if($classname!==$target) return false;
		$suffix = '-' . static::version; 
		$docroot = __DIR__; 
		$include = sprintf('%s/mooninfo%s/mooninfo.php', $docroot, $suffix);
		$success = is_file($include);
		if($success) require $include;	
		return $success;
	});
	static::$registered = true;
}

}
