<?php
App::uses('Controller', 'Controller');
class AppController extends Controller {
	public $helpers = array('Form','Html','Menu','ShortLink');
	public $title_for_layout = "";     /*use as a html title tag content for admin and front side*/
	public $metakeyword = "";     /* use as a metakeyword in front side */
	public $metadescription = ""; /* use as a metadescription in front side */
	public $heading = "";    /* use as a heading in front side & admin side */
	public $breadcrumbs = array();    /*use as a breadcrumbs for admin and front side*/
	public $setting = array();
	public $current_id = "";
	public $template = "";
	public $admin_menus = array();
	public static $script_for_layout = array();
	public static $css_for_layout = array();
	public static $scriptBlocks = array();
	public static $cssBlocks = array();
	public $back = "";
	public $header_modules = array();
	public $footer_modules = array();
	public $components = array(
		'Session', 'DebugKit.Toolbar','System',
		'Auth' => array(
			'loginRedirect' => '/admin/home',
			'logoutRedirect' => '/admin/index',
			'authError' => 'Did you really think you are allowed to see that?',
			'authenticate' => array(
				'Form' => array(
					'userModel'=>'UserManager.User',
					'scope' => array('User.status' => 1,'UserGroups.status'=>1)
				)
			)
		),'UserManager.UserLib'
	);
	public function beforeFilter() {
		Configure::load('config');
		if($this->params['controller']=="users" && ($this->params['action']=="login" || $this->params['action']=="admin_login" )){
			$this->redirect('/admin/index');
		}
		
		$this->loadModel('UserManager.User');
		$this->setPermissionAuth(Configure::read('Routing.auth_access'));
		$this->loadSettings();
		$path = explode('_',$this->params['action']);
		$prefixs = Configure::read('Routing.request_prefix');
		if(!$this->params['admin'] && !in_array($path[0],$prefixs)){
			//self::loadFrontModule();
		}
		
		//self::loadMailData();
		//self::load_permission();
		self::__back();
		self::__setTheme();
	}
	private function __back(){
		$this->back = $this->request->query('back');
		if(empty($this->back)){
			if($this->params['admin'] || $this->params['controller']=="admin"){
				$this->back=(Controller::referer()=="/")? Router::url('/admin/home',true):Controller::referer();
			}
		}
	}
	
	private function __setVar(){
		$this->set('title_for_layout',$this->title_for_layout);
		$this->set('breadcrumbs',$this->breadcrumbs);
		$this->set('back',$this->back);
		$this->set('setting',$this->setting);
		$this->set('heading',$this->heading);
		$this->set('script_for_layout',self::$script_for_layout);
		$this->set('css_for_layout',self::$css_for_layout);
		$this->set('scriptBlocks',self::$scriptBlocks);
		$this->set('cssBlocks',self::$cssBlocks);
		$this->set('metakeyword',$this->metakeyword);
		$this->set('metadescription',$this->metadescription);
		$this->set('footer_modules',$this->footer_modules);
		$this->set('header_modules',$this->header_modules);
		$this->set('template',$this->template);
		$this->set('header',$this->header);
		$this->set('current_id',$this->current_id);
		$this->set('commercial',$this->commercial);
		$this->set('apartments',$this->commercial);
	}
	public function beforeRender() {
		self::__setVar();
		if($this->params['admin'] || $this->params['controller']=="admin"){
			self::checkAuthIpAddress();
			
		}
	}
	protected function loadFrontModule(){}
	protected function setPermissionAuth($prefixs){
		$this->Auth->deny('*');
		$path = explode('_',$this->params['action']);
		if(!in_array($path[0],$prefixs)){
			$this->Auth->allow($this->params['action']);
		}
	}
	protected function loadMailData(){
		$this->mail_data['logo'] = Configure::read('Site.logo');
		$this->mail_data['url'] = Configure::read('Site.url');
		$this->mail_data['data'] ="";
		
	}
	protected function loadAdminSettings() {
		Configure::load('Custom/admin_config');
	}
	protected function loadSettings(){
		$settings = Cache::read('site');
		$settings = array();
		if(empty($settings)){
			$this->loadModel('Setting');
			$results = $this->Setting->find('all',array('fields'=>array('Setting.key','Setting.values','Setting.module')));
			$settings = array();
			foreach($results as $result){
				$settings[$result['Setting']['module']][$result['Setting']['key']] = $result['Setting']['values'];
			}
			/*Not in use Configure::write('Site.logo',Configure::read('Site.url').$this->webroot.'img'.DS.'site'.DS.$settings['site']['site_logo']);
			Configure::write('Site.admin_logo',Configure::read('Site.logo'));
			Configure::write('Site.site_name',$settings['site']['site_name']);
			* */
			$settings['social']['facebook'] = $settings['social']['facebook'];
			$settings['social']['twitter'] = $settings['social']['twitter'];
			$settings['social']['google'] = $settings['social']['google'];
			$this->title_for_layout = $settings['seo']['site_title'];
			Cache::write('site', $settings);
		}
		$this->setting = $settings;
	}
	private function __setTheme() {
		if($this->params['admin'] || $this->params['controller']=="admin"){
			$this->layout="admin";
			if($this->name=="CakeError"){
				$this->layout="admin2";
			}
			$this->title_for_layout = $this->setting['site']['site_name'];
		}
	}
	protected function _randomString(){
		$characters = '$&@!0123456789abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randstring = '';
		for ($i = 0; $i < 15; $i++) {
			$arr1 = str_split($characters);
			$randstring .= $arr1[rand(0, $i)];
		}
		return $randstring;
	}
	private function checkAuthIpAddress(){
		$allow_ip = Configure::read('ipaddres.allow');
		$disallow_ip = Configure::read('ipaddres.disallow');
		if(!empty($allow_ip)){
			$allow=(in_array ($_SERVER['REMOTE_ADDR'], $allow_ip))?1:0;
		}else{
			$allow=1;
		}
		$allow=(in_array ($_SERVER['REMOTE_ADDR'], $disallow_ip))?0:$allow;
		if($allow==0){
			throw new NotFoundException('This page is restricted');
		}else{
			return true;
		}
	}
}
