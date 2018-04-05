<?php
App::uses('Folder', 'Utility');
if(!file_exists(CACHE . 'posts')) {
	mkdir(CACHE . 'posts', 0777);
	$dir = new Folder();
	$dir->chmod(CACHE . 'posts', 0777, true, array());	
}


$prefix = "posts_";
$engine = "File";
$duration = '+999 days';
if (Configure::read('debug') > 0) {
	//$duration = '+10 seconds';
}
Cache::config('posts_admin_elements', array(
	'engine' => $engine,
	'prefix' => $prefix, // not working in this version
	'path' => CACHE . 'posts' . DS, //not working in this version
	'serialize' => ($engine === 'File'), 
	'duration' => $duration
));

?>
