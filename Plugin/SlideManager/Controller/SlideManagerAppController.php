<?php

Class SlideManagerAppController extends AppController{
	
	function beforeFilter() {
		parent::beforeFilter();	
		Configure::load('SlideManager.config');
		 
		}
	}


?>
