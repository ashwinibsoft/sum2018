<?php 
class AdminController extends AppController {
	public $uses = array('UserManager.User');
	var $components = array('Email');
	var $helpers = array('Captcha');

	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->deny('home','adminprofile');
	}
	public function index(){
		$this->layout='admin2';
		$login_attempt = (int)$this->Session->read('login_attempt');
		 $this->Captcha = $this->Components->load('Captcha', array('captchaType'=>'image', 'jquerylib'=>true, 'modelName'=>'User','theme'=>'default', 'fieldName'=>'captcha')); //load it
		$this->User->setCaptcha($this->Captcha->getVerCode()); 
		$this->disableCache();
		if($this->Auth->user('id')){
			$this->redirect($this->Auth->redirect());
		}
		if ($this->request->is('post')){
			
			$this->request->data['User']['username'] = trim($this->request->data['User']['username']);
			if(self::__validation() && $this->Auth->login()) {
				$this->Session->delete('login_attempt');
				$this->redirect($this->Auth->redirect());
			} else {
				$login_attempt +=1;
				$this->Session->write('login_attempt',$login_attempt);
				
				if($login_attempt==3){ //check number of attempts is reach 3
					$this->request->data = array();
					$this->Session->setFlash(__('Invalid username or password. Try again entering security code along with username and password.'), 'default', array(), 'auth');
				}else{
					if(!isset($this->request->data['User']['captcha'])){
						$this->Session->setFlash(__('Invalid username or password, try again'), 'default', array(), 'auth');
					}else{
						$this->Session->setFlash(__('Invalid username or password. Try again entering security code along with username and password.'), 'default', array(), 'auth');
					}
				}
				$this->request->data['User']['username'] = '';
				$this->request->data['User']['password'] = '';
				//$this->redirect(array('action'=>'index'));
			}
		}
	}
	function captcha(){
		$this->autoRender = false;
		$this->layout='ajax';
		if(!isset($this->Captcha))	{ //if Component was not loaded throug $components array()
			$this->Captcha = $this->Components->load('Captcha', array(
				'width' => 150,
				'height' => 40,
				'theme' => 'default', //possible values : default, random ; No value means 'default'
			)); //load it
			}
		$this->Captcha->create();
	}
	private function _manage_image($image = array()) {
		if ($image['error'] > 0) {
			return null;
		}else{
			$destination = Configure::read('Path.user');
			return $this->System->Image->upload(array('destination'=>$destination,'image'=>$image));
		}
	}
	public function adminprofile($id=null){
		$this->layout='admin';
		$id = $this->Session->read('Auth.User.id');
		
		if(!empty($this->request->data) && self::__validation()){
			$this->request->data['User']['id']=$this->Session->read('Auth.User.id');
			$this->request->data['User']['photo'] = self::_manage_image($this->request->data['User']['image']);
			$photo = $this->User->find('first',array('fields'=>array('User.photo'),'conditions'=>array('User.id'=>$id)));
			if($this->request->data['User']['photo']!=''){
				if(file_exists(Configure::read('Path.user').$photo['User']['photo'])){
					unlink(Configure::read('Path.user').$photo['User']['photo']);
				}
			}
			if(empty($this->request->data['User']['photo'])){
				$this->request->data['User']['photo'] = $photo['User']['photo'];
			}
			$this->User->create();
			$this->User->save($this->request->data);
			$this->Session->write('Auth.User.name',$this->request->data['User']['name']);
			$this->Session->write('Auth.User.lname',$this->request->data['User']['lname']);
			if($this->request->data['User']['photo']!=''){
				$this->Session->write('Auth.User.photo',$this->request->data['User']['photo']);
			}
			$this->Session->write('Auth.User.email',$this->request->data['User']['email']);
			if($this->request->data['User']['id']!=''){
				$this->Session->setFlash('Admin profile successfully updated');
			}
			else{
				$this->Session->setFlash('Admin profile add successfully ');
			}
			$this->redirect(array('controller'=>'admin','action'=>'adminprofile',$id,'?'=>array('back'=>$this->request->data['User']['url_back_redirect'])));
		}else{
			if(!empty($this->request->data)){
				$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
			}
		}
		if(empty($this->request->data)){
			$this->request->data=$this->User->read(null,$this->Session->read('Auth.User.id'));
		}
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/settings/site'),
			'name'=>'Change Profile'
		);
		
		$referer_url = $this->request->query('back');
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/profile/'.$id,true) :Controller::referer();
		
		}
		
		$this->heading =  array("Change","Profile");
		$this->set('referer_url',$referer_url);
	}
	public function home(){ 
		$this->layout = 'admin';
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$plugins = Cache::read('plugins');
		if(empty($plugins)){
			$this->loadModel('Plugin');
			$plugins = $this->Plugin->find('all',array('conditions'=>array('Plugin.status'=>1)));
			Cache::write('plugins',$plugins);
		}
		//print_r($plugins);
		$dashboard = array();
		$active_user_group_id = $this->UserLib->get_active_user_group_id();
		$active_user_id = $this->UserLib->get_active_user_id();
		foreach($plugins as $_plugin){
			
			if(empty($_plugin['Plugin']['title'])){
				continue;
			}
			
			if(!$this->UserLib->is_user_full_permission($active_user_id) && !$this->UserLib->is_group_full_permission($active_user_group_id)){
				if(!$this->UserLib->check_access_permission($_plugin['Plugin']['title'])){
					continue;
				}
			}
			//print_r($_plugin);
			$path = CakePlugin::path($_plugin['Plugin']['title']);
			
			if(!file_exists($path.'Config'.DS.'config.php')){
				continue;
			}
			
			Configure::load($_plugin['Plugin']['title'].'.config');
			if(Configure::check('Request.Dashboard')){
				$dashboard[] = Configure::read('Request.Dashboard');
			}
			Configure::delete('Request.Dashboard');
		}
		usort($dashboard,'SortByPosition');
		
		$this->set('dashboard',$dashboard);
	}
	public function logout() {
		$this->redirect($this->Auth->logout());
	}
	public function profile($id=null){
		$this->layout='admin';
		if(!empty($this->request->data)){
			$this->request->data['User']['id']=$this->Session->read('Auth.User.id');
			$this->User->create();
			$this->User->save($this->request->data);
			$this->Session->write('Auth.User.name',$this->request->data['User']['name']);
			$this->Session->write('Auth.User.lname',$this->request->data['User']['lname']);
			if($this->request->data['User']['id']!=''){
				$this->Session->setFlash('Admin profile successfully updated');
			}
			else{
				$this->Session->setFlash('Admin profile add successfully ');
			}
		}
		$this->request->data=$this->User->read(null,$this->Session->read('Auth.User.id'));
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/settings/site'),
			'name'=>'Admin'
		);
	}
	public function ajax_check_validation(){
		$this->autoRender = false;
		$this->User->set($this->request->data);
		$error = 0;
		if ($this->User->validates()) {
			$error = 0;
		}else{
			$error = 1;
			$message = __('Please fill required fields', true);
		}	
		$User= $this->User->validationErrors;
		$errors = compact('User');
		$data = compact('message', 'errors','error');
		echo json_encode($data);
	}
	function ajax_validation($returnType = 'json'){
		$this->loadModel('UserManager.User');
		$this->autoRender = false;
		if(!empty($this->request->data)){
			if(!empty($this->request->data['User']['form'])){
				if($this->request->data['User']['form']=="AdminLogin" && !empty($this->request->data['User']['captcha'])){
					if(!isset($this->Captcha)){ //if Component was not loaded throug $components array()
						$this->Captcha = $this->Components->load('Captcha', array('captchaType'=>'image', 'jquerylib'=>true, 'modelName'=>'User','theme'=>'default', 'fieldName'=>'captcha')); //load it
						$this->User->setCaptcha($this->Captcha->getVerCode());
					}
				}
				$this->User->setValidation($this->request->data['User']['form']);
			}
			$this->User->set($this->request->data);
			$result = array();
			if($this->User->validates()){
				$result['error'] = 0;
			}else{
				$result['error'] = 1;
				$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
			}
			
			$errors = array();
			$result['errors'] = $this->User->validationErrors;
			foreach($result['errors'] as $field => $data){
				$errors['User'.Inflector::camelize($field)] = array_pop($data);
			}
			$result['errors'] = $errors;
			$view = new View();
			$result['error_message'] = $view->element('admin/message');
			
			
			echo json_encode($result);
			return;
		}
		echo json_encode(array());
	}
	public function resetpassword(){
		$this->layout='admin2';
		
		if(!empty($this->request->data)){
			$user = $this->User->find('first',array('conditions'=>array('User.email'=>$this->request->data['User']['email'])));
			if(!empty($user)){
				unset($user['User']['password']);
				$user['User']['passwordurl'] = md5($this->_randomString());
				$this->User->create();
				$this->User->save($user,array('validate'=>false));
				
				$options = array();
				$options['replacement'] = array('{NAME}'=>$user['User']['name'].' '.$user['User']['lname'],'{USERNAME}'=>$user['User']['username'],'{url}'=>Router::url(array('admin'=>false,'plugin'=>false,'controller'=>'admin','action'=>'passwordurl',$user['User']['passwordurl']),true));
				$options['to'] = array($user['User']['email']); //mixed
				//$options['replyTo'] = "";
				//$options['emailFormat'] = "html";
				
				//$options['viewVars'] = array('data'=>'This is test');
				//$options['message'] = "This is message";
				$this->MyMail->SendMail(4,$options);
				
				
				//$this->__mail_send(4,$user); 
				
				
				$this->Session->setFlash('Mail with password reset link has been sent. Please follow the instructions to reset your password');
				$this->redirect(array('controller'=>'admin','action'=>'resetpassword'));
			}
			else{
				$this->Session->setFlash('Sorry! We cannot complete your request, the email address you entered is not registered with us. Please try again using a different email address. We are sorry for the inconvenience.','default','','error');
			}
		}
		else{
			//$this->Session->setFlash('Sorry! We cannot complete your request,please enter email address.','default','','error');
		}
	}
	private function __validation(){
		$this->loadModel('UserManager.User');
		if(!empty($this->request->data['User']['form'])){
			$this->User->setValidation($this->request->data['User']['form']);
		}
		$this->User->set($this->request->data);
		return $this->User->validates();
	}

	public function passwordurl($str=null){
		//echo "<pre>";print_r($this->request->data);die;
		$this->layout='admin2';
		$user = $this->User->find('first',array('conditions'=>array('User.passwordurl'=>$str)));
		
		if(!empty($user)){
			if(!empty($this->request->data)){
				
				
				
				
				if(!empty($user)){
					if($this->request->data['User']['password']==$this->request->data['User']['password2']){
						$this->request->data['User']['id']=$user['User']['id'];
						$this->request->data['User']['passwordurl']='';
						$this->User->create();
						$this->User->save($this->request->data);
						$this->Session->setFlash('Password has been changed successfully.');
						$this->redirect(array('controller'=>'admin','action'=>'index'));
					}else{
						$this->Session->setFlash('password and confirm password not match, try again','default','msg','error');
					}
				}
			}
			$this->set('str',$str);
		}else{
			$this->redirect(array('controller'=>'admin','action'=>'index'));
			$this->Session->setFlash('Invalid link, try again','default','msg','error');
		}
	}
	
	
	
	function delete_image($id= null){ 
		$this->User = $this->User->read(null,$id);
		$this->User->updateAll(
				array('User.photo' => null),
				array('User.id'=>$id)
			);
		self::__delete_banner_image();
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
		}else{
			$this->redirect(array('action'=>'add',$id));
		}
	}
	
	private function __delete_banner_image(){ 
		App::uses('ImageResizeHelper', 'View/Helper');
		$ImageResize = new ImageResizeHelper();
		$imgArr = array('source_path'=>Configure::read('Path.Banner'),'img_name'=>$this->page['Page']['banner_image'],'width'=>Configure::read('image_edit_width'),'height'=>Configure::read('image_edit_height'));
		$ImageResize->deleteThumbImage($imgArr);
		
		$imgArr = array('source_path'=>Configure::read('Path.Banner'),'img_name'=>$this->page['Page']['banner_image'],'width'=>Configure::read('banner_image_width'),'height'=>Configure::read('banner_image_height'));
		$ImageResize->deleteThumbImage($imgArr);
		
		@unlink(Configure::read('Path.Banner'). $this->page['Page']['banner_image']);
		
	}
	
	function admin_delete_admin_image($id=null)
	{
		
			$this->autoRender = false;
		
	$this->User->updateAll(array('User.photo' => null),array('User.id'=>$id));	
	$this->redirect(array('action'=>'adminprofile','admin'=>false));
			
	}
	
	

	
	
	
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
