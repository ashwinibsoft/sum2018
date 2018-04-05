<?php
ob_start();
App::uses('Controller', 'Controller');
class AppController extends Controller {
	public $helpers = array('Form','Html','Menu','ShortLink','MemberAuth');
	public $heading = "";    /* use as a heading in front side & admin side */
	public $breadcrumbs = array();    /*use as a breadcrumbs for admin and front side*/
	public $setting = array();
	public $current_id = null;
	public $template = "";
	public $site_status="";
	public $admin_menus = array();
	public $back = "";
	public $theme = "Mango";
	public $header_modules = array();
	public $footer_modules = array();
	public $components = array(
		'Session','System','MyMail','MemberAuth',
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
	
	/*private function _conf_auth(){
		if(($this->params['controller']=='admin')||(!empty($this->params['admin']) && $this->params['admin']==1)){
			AuthComponent::$sessionKey = 'Auth.Admin';
			$this->Auth->loginRedirect = '/admin/home';
			$this->Auth->logoutRedirect = '/admin/index';
			$this->Auth->authError = 'Did you really think you are allowed to see that?';
			$this->Auth->authenticate =  array(
							'Form' => array(
								'userModel'=>'UserManager.User',
								'scope' => array('User.status'=>1,'UserGroups.status' => 1)
							)
						);
			
		}/*else if(in_array($this->params['controller'],array('suppliers','new_buyers'))){			
			$controll=$this->params['controller'];
			$singular= Inflector::singularize(Inflector::camelize($controll));		
			AuthComponent::$sessionKey = 'Auth.'.$singular;	
			$this->Auth->loginAction = array('plugin'=>$this->params["plugin"],'controller' => $this->params["controller"], 'action' => 'login');
			$this->Auth->logoutAction = array('plugin'=>$this->params["plugin"],'controller' => $this->params["controller"], 'action' => 'logout');
			$this->Auth->loginRedirect = array('plugin'=>$this->params["plugin"],'controller' => $this->params["controller"], 'action' => 'dashboard');		
			$this->Auth->logoutRedirect = '/';
			$this->Auth->authError = 'Did you really think you are allowed to see that?';
			$this->Auth->authenticate =  array(
						'Form' => array(
								'userModel'=>Inflector::camelize($this->params["plugin"]).'.'.$singular,
								'fields' => array('username' => 'email_id','password' => 'password'),				
								'scope' => array($singular.'.status' => 1)
								)
							);	
			  //$this->Auth->authorize = array('Controller');
			
			
		}*/
		
	//}
	
	/*private function _conf_auth(){
		if ($this->request->prefix == 'admin') {
            $this->layout = 'admin';
            // Specify which controller/action handles logging in:
            AuthComponent::$sessionKey = 'Auth.Admin'; // solution from http://stackoverflow.com/questions/10538159/cakephp-auth-component-with-two-models-session
            $this->Auth->loginAction = array('controller'=>'administrators','action'=>'login');
            $this->Auth->loginRedirect = array('controller'=>'some_other_controller','action'=>'index');
            $this->Auth->logoutRedirect = array('controller'=>'administrators','action'=>'login');
            $this->Auth->authenticate = array(
                'Form' => array(
                    'userModel' => 'User',
                )
            );
            $this->Auth->allow('login');

        }
		if ($this->request->prefix == 'admin') {
			AuthComponent::$sessionKey = 'Auth.Admin';
			 $this->Auth->loginAction = array('controller'=>'admin','action'=>'login');
			$this->Auth->loginRedirect = '/admin/home';
			$this->Auth->logoutRedirect = '/admin/index';
			$this->Auth->authError = 'Did you really think you are allowed to see that?';
			$this->Auth->authenticate =  array(
							'Form' => array(
								'userModel'=>'UserManager.User',
								'scope' => array('User.status'=>1,'UserGroups.status' => 1)
							)
						);
						
			$this->Auth->allow('login');
			
		}else if($this->request->prefix == 'supplier')){			
			$controll=$this->params['controller'];
			$singular= Inflector::singularize(Inflector::camelize($controll));		
			AuthComponent::$sessionKey = 'Auth.'.$singular;	
			$this->Auth->loginAction = array('plugin'=>$this->params["plugin"],'controller' => $this->params["controller"], 'action' => 'login');
			$this->Auth->logoutAction = array('plugin'=>$this->params["plugin"],'controller' => $this->params["controller"], 'action' => 'logout');
			$this->Auth->loginRedirect = array('plugin'=>$this->params["plugin"],'controller' => $this->params["controller"], 'action' => 'dashboard');		
			$this->Auth->logoutRedirect = '/';
			$this->Auth->authError = 'Did you really think you are allowed to see that?';
			$this->Auth->authenticate =  array(
						'Form' => array(
								'userModel'=>Inflector::camelize($this->params["plugin"]).'.'.$singular,
								'fields' => array('username' => 'email_id','password' => 'password'),				
								'scope' => array($singular.'.status' => 1)
								)
							);	
			  //$this->Auth->authorize = array('Controller');
			
			
		}
		
	}*/
	
	
	
	public function beforeFilter() {	
		
		/*$ud=array();
		self::_conf_auth();	
		$ud = $this->Session->read('Auth');
		//print_r($ud); die;
		*/
		/*if(!empty($ud['Supplier'])){
			$ud=$ud['Supplier'];
			$ud['logout-action']=array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'logout');
			$ud['dash-action']=array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'dashboard');
		}else if(!empty($ud['NewBuyer'])){
			$ud=$ud['NewBuyer'];
			$ud['logout-action']=array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'logout');
			$ud['dash-action']=array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'dashboard');
		}*/
		
		Configure::load('config');
		//print_r($this->params); die;
		if($this->params['controller']=="users" && ($this->params['action']=="login" || $this->params['action']=="admin_login" )){
			//print_r($this->params); die;
			$this->redirect('/admin/index');
		}
		$this->loadModel('UserManager.User');
		$this->loadModel('UserManager.UserGroups');
		$this->setPermissionAuth(Configure::read('Routing.auth_access'));
		
		$path = explode('_',$this->params['action']);
		$prefixs = Configure::read('Routing.request_prefix');
		if(!$this->params['admin'] && !in_array($path[0],$prefixs)){
			//self::loadFrontModule();
		}
		
			
		/*if (AuthComponent::user('id')){					
			$abc=AuthComponent::user('id');
		if(!in_array($this->params['controller'],array('suppliers','new_buyers'))){
			$loginuser = $this->User->find('first',array('fields'=>array('User.status'),'conditions'=>array('User.id'=>$abc)));
			if(!empty($loginuser)){
				if($loginuser['User']['status']==0){
					$this->Session->setFlash(__('Your account has been disabled.Please contact administrator.'), 'default', array(), 'auth');
					$this->redirect($this->Auth->logout());
				}
			}else{
					$this->Session->setFlash(__('Sorry! Your account does not exists. Please contact administrator.'), 'default', array(), 'auth');
					$this->redirect($this->Auth->logout());
				}
			}
		}*/
		
		if (AuthComponent::user('id')){ 
			$abc=AuthComponent::user('id');
			//echo $abc;die;
			$loginuser = $this->User->find('first',array('fields'=>array('User.status'),'conditions'=>array('User.id'=>$abc)));
			if(!empty($loginuser)){
				if($loginuser['User']['status']==0){
					$this->Session->setFlash(__('Your account has been disabled.Please contact administrator.'), 'default', array(), 'auth');
					$this->redirect($this->Auth->logout());
				}
			}else{
					$this->Session->setFlash(__('Sorry! Your account does not exists. Please contact administrator.'), 'default', array(), 'auth');
					$this->redirect($this->Auth->logout());
				}
		}
		
		if($this->name=="CakeError"){
			$this->loadModel('ContentManager.Page');
			$error = $this->Page->read(null,35);
			if($error['Page']['use_default_image'] || $this->System->get_setting('page','override_banner_image')){
				$error['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
			}
			$this->System->set_data('banner_image',$error['Page']['banner_image']);
			$this->System->set_seo('site_title',$error['Page']['page_title']);
			$this->System->set_seo('site_metakeyword',$error['Page']['page_metakeyword']);
			$this->System->set_seo('site_metadescription',$error['Page']['page_metadescription']);
			$this->set('page',$error);
		}
		
		//$this->set('userdata',$ud);
		self::__back();
		self::__contact_form();
		self::__get_request();
		
	}
	private function __back(){
		$this->back = $this->request->query('back');
		if(empty($this->back)){
			if($this->params['admin'] || $this->params['controller']=="admin"){
				$this->back=(Controller::referer()=="/")? Router::url('/admin/home',true):Controller::referer();
			}
		}
	}
	
	private function __get_request(){
		$this->loadModel('NewBuyerManager.NewBuyer');
		$request_count = $this->NewBuyer->find('count', array('conditions' => array('NewBuyer.request_status' => 1)));
		//print_r($request_count); die;
		$this->set('request_count',$request_count);
	}
	
	private function __contact_form(){
		if(!empty($this->request->data)  && self::__validation()){
			$this->loadModel('MailManager.Mail');
			$mail=$this->request->data['Page'];
				foreach($mail    as   $key=>$val)
				{
				if(empty($val)){
				$this->request->data['Page'][$key]="NA";			
		    	}
			}
			/*echo "<pre>";
			print_r($this->request->data);
			die;*/
			$body ='';
			
			if($this->request->data['Page']['form']=="contact_foot"){
							
				$mail=$this->request->data['Page'];
				foreach($mail    as   $key=>$val)
				{
				if(empty($val)){
				$this->request->data['Page'][$key]="NA";			
		    	}
			}
				$options = array();
				$options['replacement'] = array('{NAME}'=>$this->request->data['Page']['name'],'{EMAIL}'=>$this->request->data['Page']['email'],'{MESSAGE}'=>$this->request->data['Page']['message']);
			//print_r($this->request->data);die;
				$options['to'] = array($this->System->get_setting('site','site_contact_email')); //mixed
				$options['replyTo'] = array($this->request->data['Page']['email']); //mixed
				//$options['emailFormat'] = "html";
				//$options['viewVars'] = array('data'=>'This is test');
				//$options['message'] = "This is message my custom";
				//print_r($options);die;
				$this->MyMail->SendMail(16,$options);
				
				/*
				$Email = new CakeEmail('smtp');
				$Email->from(array('himanshum@burgeonsoft.net' => 'My Site'));
				$Email->to('himanshu.burgeon@gmail.com');
				$Email->subject('About');
				$Email->send('My message');
				die;
				*/
				
			}else{
				
				
				$mail=$this->request->data['Page'];
				foreach($mail    as   $key=>$val)
				{
				if(empty($val)){
				$this->request->data['Page'][$key]="NA";			
		    	}
			}
				// print_r($this->request->data['Page']);die;
				$options = array();
				$options['replacement'] = array(
		'{NAME}'=>$this->request->data['Page']['name'],
		'{EMAIL}'=>$this->request->data['Page']['email'],'{PHONE}'=>$this->request->data['Page']['phone'],'{Subject}'=>$this->request->data['Page']['subject'],'{MESSAGE}'=>$this->request->data['Page']['message']);
	     /*echo "<pre>";
			print_r($options);
			die;*/
				$options['to'] = array($this->System->get_setting('site','site_contact_email')); //mixed
				$options['replyTo'] = array($this->request->data['Page']['email']); //mixed
				//$options['emailFormat'] = "html";
				
				//$options['viewVars'] = array('data'=>'This is test');
				//$options['message'] = "This is message";
				$this->MyMail->SendMail(8,$options);
			}
			
			$options = array();
			$options['replacement'] = array('{NAME}'=>$this->request->data['Page']['name']);
			$options['to'] = $this->request->data['Page']['email']; 
			$options['replyTo'] = array($this->System->get_setting('site','site_contact_email')); //mixed
				
			$this->MyMail->SendMail(10,$options);
			$this->redirect(array('plugin'=>'content_manager','controller'=>'pages','action'=>'view',30));
		}
	}
	private function __validation(){
		$this->loadModel('ContentManager.Page');
		if(!empty($this->request->data['Page']['form']) && !$this->request->is('ajax')){
			if($this->request->data['Page']['form']=="contact_foot" || $this->request->data['Page']['form']=="contact" ){
				$this->Page->setValidation($this->request->data['Page']['form']);
				$this->Page->set($this->request->data);
				return $this->Page->validates();
			}else{
				return false;
			}
		}
		return false;
	}
	
	private function __setVar(){
		$this->set('breadcrumbs',$this->breadcrumbs);
		$this->set('back',$this->back);
		//$this->set('setting',$this->setting);
		$this->set('heading',$this->heading);
		$this->set('footer_modules',$this->footer_modules);
		$this->set('header_modules',$this->header_modules);
		$this->set('template',$this->template);
		$this->set('header',$this->header);
		$this->set('current_id',$this->current_id);
		$this->set('site_status',$this->site_status);
	}
	public function beforeRender() {
		self::__setVar();
		if($this->params['admin'] || $this->params['controller']=="admin"){
			self::checkAuthIpAddress();
			$this->disableCache();
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
	//	$this->mail_data['logo'] = Configure::read('Site.logo'); 
	
		$this->mail_data['logo'] = Configure::read('Path.site')."/".$this->System->get_setting('site','site_admin_logo');
		$this->mail_data['url'] = Configure::read('Site.url');
		$this->mail_data['data'] ="";
		
	}
	protected function loadAdminSettings() {
		Configure::load('Custom/admin_config');
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
	
	function captcha()	{
		$this->autoRender = false;
		$this->layout='ajax';
		if(!isset($this->Captcha))	{ //if Component was not loaded throug $components array()
			$this->Captcha = $this->Components->load('Captcha', array(
				'width' => 97,
				'height' => 38,
				'theme' => 'default', //possible values : default, random ; No value means 'default'
			)); //load it
			}
		$this->Captcha->create();
	}
	
	
	
}
