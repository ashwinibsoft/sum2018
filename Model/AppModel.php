<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	
	
	function save_routes($fields = array()){
		Cache:: delete('routes');
		App::uses('SessionComponent', 'Controller/Component');
		App::uses('Route', 'Model');
		
		$route = new Route();
		$Session = new SessionComponent(new ComponentCollection);
		
		if(!empty($fields['object_name'])){
			$db = $this->getDataSource();
			//$fields['object_name'] = $db->value($fields['object_name'], 'string');
		}else{
			$fields['object_name'] = null;
		}
		
			
		
		
		$route->updateAll(
				array('Route.redirect_type' => 301),
				array('Route.object'=>$fields['object'],'Route.object_id'=>$fields['object_id'],'Route.redirect_type'=>null,'NOT'=>array('Route.request_uri'=>$fields['request_uri']))
			);
		$message = "";
		if($Session->check('Message.wraning')){
			$message = $Session->read('Message.wraning.message').'<br />';
		}
		
		if(empty($fields)){
			$Session->setFlash($message.__('Slug url is not configured', true),'default','','wraning');
			return false;
		}
		if(empty($fields['request_uri'])){
			$Session->setFlash($message.__('Slug url is not configured properly', true),'default','','wraning');
			return false;
		}
		
		
		$_count = $route->find('count',array('conditions'=>array('OR'=>array(array('request_uri'=>$fields['request_uri'],'object'=>$fields['object'],'NOT'=>array('object_id'=>$fields['object_id'])),array('request_uri'=>$fields['request_uri'],'NOT'=>array('object'=>$fields['object']))))));
		
		if($_count > 0){
			$Session->setFlash($message.__('This slug url ('.$fields['request_uri'].') is already associated with other page', true),'default','','wraning');
			return false;
		}
		
		$_count = $route->find('count',array('conditions'=>array('request_uri'=>$fields['request_uri'],'object'=>$fields['object'],'object_id'=>$fields['object_id'])));
		
		
		
		
		if($_count > 0){
			$db = $this->getDataSource();
			$fields['object_name'] = $db->value($fields['object_name'], 'string');
			
			$route->updateAll(
				array('Route.redirect_type' => null,'Route.object_name'=>$fields['object_name']),
				array('Route.request_uri =' => $fields['request_uri'],'Route.object'=>$fields['object'],'Route.object_id'=>$fields['object_id'])
			);
			return true;
		}else{
			$route->updateAll(
				array('Route.redirect_type' => 301),
				array('Route.object'=>$fields['object'],'Route.object_id'=>$fields['object_id'],'Route.redirect_type'=>null,'NOT'=>array('Route.request_uri'=>$fields['request_uri']))
			);
		}
		$route->create();
		$route->save($fields);
		return true; 
	}
	
	
	protected function _check_uri_exist_on_other($request_uri='',$object= '', $object_id = null){
		App::uses('Route', 'Model');
		$route = new Route();
		
		$_count = $route->find('count',array('conditions'=>array('OR'=>array(array('request_uri'=>$request_uri,'object'=>$object,'NOT'=>array('object_id'=>$object_id)),array('request_uri'=>$request_uri,'NOT'=>array('object'=>$object))))));
		if($_count > 0){
			return true;
		}else{
			return false;
		}
	}
	public function get_uri($object = '', $object_id = null){
		App::uses('Route', 'Model');
		$route = new Route();
		
		$results = $route->find('first',array('conditions'=>array('object'=>$object,'object_id'=>$object_id,'redirect_type'=>null)));
		if(!empty($results)){
			return $results['Route']['request_uri'];
		}
		return '';
	}
	
	public function delete_routes($object_id ='', $object = ''){
		App::uses('Route', 'Model');
		$route = new Route();
		Cache:: delete('routes');
		
		$route->deleteAll(array('Route.object' => $object,'Route.object_id'=>$object_id), false);
	}
	public function matchCaptcha($inputValue)	{
		return $inputValue['captcha']==$this->getCaptcha(); //return true or false after comparing submitted value with set value of captcha
	}
	function setCaptcha($value)	{
		$this->captcha = $value; //setting captcha value
	}

	function getCaptcha()	{
		return $this->captcha; //getting captcha value
	}
	public function phone_dummy($check) {
		if(is_array($check)) {
		$value = array_shift($check);	
		} else {
		$value = $check;
		}
		if(strlen($value) == 0) {
		return true;
		}
		return preg_match('/^[0-9-+()# ]{6,12}+$/', $value);
	}
	
	public function add_menu($options = array()){
		if(empty($options['module']) || empty($options['ref_id']) || empty($options['name']) ){
			return;
		}
		
		if(!isset($options['new'])){
			
			$options['new'] = true;
		}
		
		
		App::uses('Menu', 'Model');
		App::uses('Link', 'Model');
		$Menu = new Menu();
		$Link =  new Link();
		$menus = $Menu->find('all');
		foreach($menus as $_menu){
			if((int)$_menu['Menu']['default_menu']==0){
				continue;
			}
			
			
			if(!empty($options['parent_id']) && $options['parent_id']!=0){
				$parent = $Link->find('first',array('conditions'=>array('Link.menu_id'=>$_menu['Menu']['id'],'Link.ref_id'=>$options['parent_id'],'Link.module'=>$options['module'])));
				if(!empty($parent)){
					$options['parent_id'] = (int)$parent['Link']['id'];
				}else{
					continue;
				}
			}
			$options['id'] = "";
			/* Check whether link is updated from menu manager or not. if yes then it will not add this*/
			if(!(bool)$options['new']){
				$page = $Link->find('first',array('conditions'=>array('Link.menu_id'=>$_menu['Menu']['id'],'Link.ref_id'=>$options['ref_id'],'Link.auto_add'=>1,'Link.module'=>$options['module'])));
				if(!empty($page)){
					$options['id'] = $page['Link']['id'];
					if(($page['Link']['name']!=trim($options['name'])) || ($page['Link']['parent_id']!=$options['parent_id'])){
						//echo 1;
						//continue;
					}else{
						//echo 2;
						$options['id'] = $page['Link']['id'];
					}
				}else{
					continue;
				}
			}
			
			/* Check whether link is updated from menu manager or not. if yes then it will not add this*/
			
			$Link->virtualFields['max_reorder'] = 'MAX(Link.reorder)';
			$link = $Link->find('first',array('conditions'=>array('Link.menu_id'=>$_menu['Menu']['id'],'Link.parent_id'=>$options['parent_id'],'Link.module'=>$options['module']),'fields'=>array('Link.max_reorder','Link.max_reorder'),'group'=>array('Link.parent_id')));
			
			
			$options['reorder'] = 0;
			$options['status'] = 1;
			$options['new_window'] = 0;
			$options['tag_title'] = ''; //$options['name'];
			$options['menu_id'] = $_menu['Menu']['id'];
			$options['auto_add'] = 1;
			if(!empty($link)){
				$options['reorder'] = (int)$link['Link']['max_reorder'] + 1;
			}
			
			$Link->create();
			$Link->save($options);
		}
		
	}
	
	public function delete_menu($options = array()){
		App::uses('Menu', 'Model');
		App::uses('Link', 'Model');
		$Menu = new Menu();
		$Link =  new Link();
		$Link->deleteAll(array('Link.auto_add'=>1,'Link.ref_id'=>$options['ref_id'],'Link.module'=>$options['module']),false);
	}

	
	
	
	
}
