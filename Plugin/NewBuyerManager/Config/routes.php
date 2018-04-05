<?php
$cmsPages = Cache::read('cake_page_routing');
	if(empty($cmsPages)) {
		App::import('Model', 'ContentManager.Page');
		$Page = new Page();
		//echo "<pre>";print_r($page);die;
		$cmsPages = $Page->find('all');
			//Cache::delete('pages_routes','default');
		Cache::write('cake_page_routing', $cmsPages);
	}
	if ($cmsPages)
	{
		foreach ($cmsPages as $key => $value)
		{
			if ($value['Page']['id'] != '/')
			{
				Router::connect('/'.$value['Page']['url_key'], array('plugin'=>'content_manager','controller' => 'pages', 'action' => 'view', $value['Page']['id']));
			}
		}
	}
	/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	//require CAKE . 'Config' . DS . 'routes.php';
