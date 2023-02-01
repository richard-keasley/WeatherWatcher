<section><?php 
try {
	$config = config('App');
	$id = $config->bbc;
	if(!$id) throw new \Exception("No BBC id in config");
	
	$source = 'https://weather-broker-cdn.api.bbci.co.uk/en/forecast/rss/3day/' . $id;
	$xml = simplexml_load_file($source);
	if(!$xml) throw new \Exception("Couldn't load {$source}");
	
	$channel = $xml->channel;
	
	printf('<h2><a href="%s">%s</a></h2>', $channel->link, $channel->title);
	
	$image = $channel->image;
		
	helper('html');
	$src = new \CodeIgniter\HTTP\URI($image->url ?? '');
	$src->setScheme('https');
	$img = [
		'src' => strval($src),
		'style' => "float:right; width:4rem"
	];
	$attrs = [
		'title' => $image->title ?? '',
	];
	$img_link = anchor($image->link, img($img), $attrs);
	
	printf('<p>%s%s (%s)</p>', $img_link, $channel->description, $channel->pubDate);
		
	foreach($channel->item as $item) {
		printf('<p><a href="%s" title="%s">%s</a></p>', $item->link, $item->description, $item->title);
		
	}
	
	/*
	echo '<pre>'; 
	var_dump($xml);
	echo '</pre>';
	return;
	// */
	
}
catch(\Exception $e) {
	echo $e->getMessage();
}

?></section>

