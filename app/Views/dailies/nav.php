<nav class="navbar"><?php
$anchors = [];
$views = ['day', 'week', 'month', 'year'];
foreach($views as $view) {
	$anchors[] = anchor("dailies/{$view}/{$start}", humanize($view));
}
$views = ['custom'];
foreach($views as $view) {
	$anchors[] = anchor("dailies/{$view}/{$start}/{$end}", humanize($view));
}
foreach($anchors as $anchor) {
	printf('<button>%s</button>', $anchor);
}

?></nav>