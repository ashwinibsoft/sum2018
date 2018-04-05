<?php
class BlogManagerAppController extends AppController{
	public $post = array();
	function beforeFilter() {
		parent::beforeFilter();	
		Configure::load('BlogManager.config');
	}
}
?>
