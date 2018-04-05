<?php
App::uses('Folder', 'Utility');
if(!file_exists(CACHE . 'pages')) {
	mkdir(CACHE . 'pages', 0777);
	$dir = new Folder();
	$dir->chmod(CACHE . 'pages', 0777, true, array());	
}


$prefix = "pages_";
$engine = "File";
$duration = '+999 days';
if (Configure::read('debug') > 0) {
	//$duration = '+10 seconds';
}
Cache::config('pages_admin_elements', array(
	'engine' => $engine,
	'prefix' => $prefix, // not working in this version
	'path' => CACHE . 'pages' . DS, //not working in this version
	'serialize' => ($engine === 'File'), 
	'duration' => $duration
));

?>
