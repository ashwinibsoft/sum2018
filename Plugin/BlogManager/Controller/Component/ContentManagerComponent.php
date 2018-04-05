<?php

/**
 * Wordpress Component
 *
 * Loads the latest posts from a wordpress database so that you can use them
 * in your cakePHP application.
 *
 * Do not use tag, category or author in your permalinks because this
 * Helper won't replace those tags for now. It's a huge database slowdown
 * to fetch all the records for categories, tags or authors just to display
 * a short feed of posts on another site.
 *
 * @author    Henning Stein, www.atomtigerzoo.com
 * @copyright Copyright (c) 2010, Henning Stein
 * @version   0.4
 */

class ContentManagerComponent extends Component {
	var $components = array('System');
	public function startup(Controller $controller){
		//$this->Upload->startup($controller);
		//$this->System->add_shortcode('{test12}','test123');
		
		if($this->System->is_admin($controller)){
			///echo "Is_admin";
		}
		
		//self::__load_shortcode();
		parent::startup($controller);
	}
	private function __load_shortcode(){
		echo "test";
	}
	
	 

}

?>
