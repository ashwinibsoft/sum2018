<?php
class ContentManagerAppController extends AppController{
	public $page = array();
	function beforeFilter() {
		parent::beforeFilter();	
		//Configure::load('ContentManager.config');
	}
}
?>
