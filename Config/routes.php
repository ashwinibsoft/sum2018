<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	Router::connect('/', array('plugin'=>'content_manager','controller' => 'pages', 'action' => 'home'));
	Router::connect('/news-events',array('plugin'=>'news_manager','controller'=>'news_articles', 'action'=>'index'));
	Router::connect('/news-events/*',array('plugin'=>'news_manager','controller'=>'news_articles','action'=>'index'),array(
    'named' =>
      array('page' => '[\d]+'),
  ));
	Router::connect('/search-results/:search',array('plugin'=>'content_manager','controller'=>'pages', 'action'=>'search'),
    array(
        'pass' => array('search'),
        'search' => '[a-zA-Z0-9_]+',
        ));
	Router::connect('/search-results/*',array('plugin'=>'content_manager','controller'=>'pages','action'=>'search'),array(
	'named' =>
      array('serach'=>'[a-zA-Z0-9_]+','page' => '[\d]+'),
  ));
	Router::connect('/faq',array('plugin'=>'faq_manager','controller'=>'faqs', 'action'=>'index'));
	Router::connect('/faq/*',array('plugin'=>'faq_manager','controller'=>'faqs','action'=>'index'),array(
    'named' =>
      array('page' => '[\d]+'),
  ));
	Router::connect('/admin', array('controller' => 'admin', 'action' => 'index'));
	Router::connect('/admin/index', array('controller' => 'admin', 'action' => 'index'));
	Router::connect('/admin/captcha', array('controller' => 'admin', 'action' => 'captcha'));
	Router::connect('/admin/logout', array('controller' => 'admin', 'action' => 'logout'));
	Router::connect('/admin/home', array('controller' => 'admin', 'action' => 'home'));
	Router::connect('/admin/profile/*', array('controller' => 'admin', 'action' => 'adminprofile'));
	Router::connect('/admin/passwordurl/*',array('controller'=>'admin', 'action'=>'passwordurl'));
	Router::connect('/admin/validation/*',array('controller'=>'admin', 'action'=>'validation'));
	Router::connect('/admin/ajax_validation',array('controller'=>'admin', 'action'=>'ajax_validation'));
	Router::connect('/admin/resetpassword',array('controller'=>'admin', 'action'=>'resetpassword'));
	Router::connect('/admin/settings/validation/*',array('controller'=>'settings', 'action'=>'validation'));
	//Router::connect('/login', array('plugin' => 'AccountManager','controller' => 'owners', 'action' => 'login'));
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	//Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	$rewrite_urls = Cache::read('routes');
	
	if(empty($rewrite_urls)){
		App::uses('Route', 'Model');
		$route = new Route();
		$rewrite_urls = $route->find('all',array('conditions'=>array()));
		Cache:: write('routes',$rewrite_urls);
	}
 
	foreach($rewrite_urls as $_result){
		$values = json_decode($_result['Route']['values'],true);
		if($_result['Route']['redirect_type']==301){
			Router::redirect('/'.$_result['Route']['request_uri'], array('plugin'=>$values['plugin'],'controller' => $values['controller'], 'action' => $values['action'], $values['id']), array('status' => 302));
		}else{
			Router::connect('/'.$_result['Route']['request_uri'].'/*', array('plugin'=>$values['plugin'],'controller' => $values['controller'], 'action' => $values['action'], $values['id']));
		}
	}
 
 
	CakePlugin::routes();
	
	Router::parseExtensions('pdf');

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
