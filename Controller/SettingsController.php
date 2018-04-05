<?php
class SettingsController extends AppController {
	public $uses = array('Setting');
	var $components = array('Auth','Email');
	public $paginate = array();
	public $id = null;
	
	
	public function beforeFilter(){
		parent::beforeFilter();
		
		if($this->params['action']!="admin_changepassword" && (int)$this->UserLib->get_active_user_id()){
			if(!(int)$this->Session->read('Auth.User.UserGroups.is_access_settings')){
				$this->UserLib->add_authorize_action(array($this->params['action']));
				$this->UserLib->add_authenticate_action(array($this->params['action']));
				$this->UserLib->check_authenticate($this);
				$this->UserLib->check_authorize($this);
			}
		}
		
		
	}    

	public function admin_social(){
		$this->loadModel('Setting');
		if(!empty($this->request->data) && $this->validation()){
			foreach($this->request->data['Setting'] as $key => $value){
				if($this->Setting->find('count',array('conditions'=>array('Setting.key'=>$key,'Setting.module'=>'social')))){
					$this->Setting->query("UPDATE `settings` SET `values`=\"$value\" , module=\"social\" WHERE `key`=\"$key\"");
				} else{
					$this->Setting->query("INSERT `settings` SET `values`=\"$value\"  , `key`=\"$key\" , module=\"social\"");
				}
				$this->Session->setFlash(__('Social Media Link(s) has been saved successfully'));
			}
			Cache::delete('site');
			$this->redirect(array('action'=>'social','?'=>array('back'=>$this->request->data['Setting']['url_back_redirect'])));
		}
		if(empty($this->request->data)){
			$this->request->data['Setting'] = $this->Setting->find('list',array('fields'=>array('Setting.key','Setting.values')));
		}else{
			$data = $this->Setting->find('list',array('fields'=>array('Setting.key','Setting.values')));
			$this->request->data['Setting']['facebook'] = $data['facebook'];
			$this->request->data['Setting']['google'] = $data['google'];
		}

		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/settings/site'),
			'name'=>'Social Media Settings'
		);
		
		$referer_url = $this->request->query('back');
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/settings/site',true) :Controller::referer();
		
		}
		$this->heading =  array("Social Media","Settings");
		$this->set('referer_url',$referer_url);
	}
	public function admin_site(){
		$this->loadModel('Setting');
		
		//echo "<pre>";
		//print_r($this->request->data);die;
		
		$data = $this->Setting->find('list',array('fields'=>array('Setting.key','Setting.values')));
		//echo "<pre>"; print_r($data);   die;
		foreach($data   as $k=>$v){
			
			
			if($k=='site_front_logo'){
				$front_logo=$v;
				}
			if($k=='site_admin_logo'){
				$admin_logo=$v;
				}
				
				if($k=='site_mail_logo'){
				$mail_logo=$v;
				}
		}
		if(!empty($this->request->data) && $this->validation()){ // echo "<pre>"; print_r($this->request->data); die;
			foreach($this->request->data['Setting'] as $key => $value){
				
				
		// for   Front logo 
		 if($key=='site_front_logo'){
		    if(!empty($value['name'])){	
			 	  if($value['error']==0){
					  
						$_options = array('destination'=> Configure::read('Path.site'),'image'=>$value);
					$value=$this->System->Image->upload($_options);	
										} 
									} else  {
										
			      $value=$front_logo; 
			       }
					}
		 
		 
		//  for   admin logo
		
		if($key=='site_admin_logo'){
		    if(!empty($value['name'])){	
			 	  if($value['error']==0){
						$_options = array('destination'=> Configure::read('Path.site'),'image'=>$value);
					$value=$this->System->Image->upload($_options);	
										} 
									} else  {
										
			      $value=$admin_logo; 
			       }
					}
		 
		 
		 //  for   mail logo
		
		if($key=='site_mail_logo'){
		    if(!empty($value['name'])){	
			 	  if($value['error']==0){
						$_options = array('destination'=> Configure::read('Path.site'),'image'=>$value);
					$value=$this->System->Image->upload($_options);	
										} 
									} else  {
										
			      $value=$mail_logo; 
			       }
					}
	
		 
		 
		 
				
				
				if($this->Setting->find('count',array('conditions'=>array('Setting.key'=>$key,'Setting.module'=>'site')))){
					$this->Setting->query("UPDATE `settings` SET `values`=\"$value\" , module=\"site\" WHERE `key`=\"$key\"");
				}else{
					$this->Setting->query("INSERT `settings` SET `values`=\"$value\"  , `key`=\"$key\" , module=\"site\"");
				}
				$this->Session->setFlash(__('Settings has been Saved Successfully'));
			}
			Cache::delete('site');
			$this->redirect(array('action'=>'site','?'=>array('back'=>$this->request->data['Setting']['url_back_redirect'])));
		}
		if(empty($this->request->data)){
			$data=$this->request->data['Setting'] = $this->Setting->find('list',array('fields'=>array('Setting.key','Setting.values')));
		}else{
			$data = $this->Setting->find('list',array('fields'=>array('Setting.key','Setting.values')));
		}

		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/settings/site'),
			'name'=>'General Settings'
		);
		
		//echo "<pre>";  print_r($data);   die; 
		
		$referer_url = $this->request->query('back');
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/settings/site',true) :Controller::referer();
		
		}
		
		$this->heading =  array("General","Settings");
		$this->set('referer_url',$referer_url);
		$this->set('themes',$this->System->get_themes());
		
		$this->set('data',$data);
		/*if(!empty($this->request->data['Setting']['site_status']) && ($this->request->data['Setting']['site_status']== "unpublish"))
		{
			$this->redirect('/orders/thanks');
		}
		else{
				echo "I am publish";
			} */
	}
	public function admin_seo(){
		$this->loadModel('Setting');
		if(!empty($this->request->data) && $this->validation()){
			/*
			if($this->request->data['Setting']['site_google_analytic_code']!=''){
				$this->request->data['Setting']['site_google_analytic_code']=strip_tags($this->request->data['Setting']['site_google_analytic_code']);
			}
			*/
			foreach($this->request->data['Setting'] as $key => $value){
				if(is_array($value)){
					if($value['error']==0){
						$ext = explode(".",$value['name']);
					
						$name = explode("_",$key);
						if(!file_exists(Configure::read('Path.seo'))) {
							App::uses('Folder', 'Utility');
							mkdir(Configure::read('Path.seo'), 0777);
							$dir = new Folder();
							$dir->chmod(Configure::read('Path.seo'), 0777, true, array());	
						}
						$img_name = array_pop($name).".".array_pop($ext);
						move_uploaded_file($value['tmp_name'],Configure::read('Path.seo').$img_name);
						$value = $img_name;
					}else{
						continue;
						//	$value;
					}
				}
				if($this->Setting->find('count',array('conditions'=>array('Setting.key'=>$key,'Setting.module'=>'seo')))){
					
					$this->Setting->query("UPDATE `settings` SET `values`=\"$value\" , module=\"seo\" WHERE `key`=\"$key\"");
				
				}else{
					$this->Setting->query("INSERT `settings` SET `values`=\"$value\"  , `key`=\"$key\" , module=\"seo\"");
				}
				
				$this->Session->setFlash(__('Settings has been Saved Successfully'));
			}
			Cache::delete('site');
			//$this->redirect(array('action'=>'seo'));
			$this->redirect(array('action'=>'seo','?'=>array('back'=>$this->request->data['Setting']['url_back_redirect'])));
		}
		if(empty($this->request->data)){
			$this->request->data['Setting'] = $this->Setting->find('list',array('fields'=>array('Setting.key','Setting.values')));
		}else{
			$data = $this->Setting->find('list',array('fields'=>array('Setting.key','Setting.values')));
		}

		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/settings/seo'),
			'name'=>'SEO Settings'
		);
		$referer_url = $this->request->query('back');
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/settings/seo',true) :Controller::referer();
		
		}
		
		$this->set('referer_url',$referer_url);
		$this->heading =  array("SEO","Settings");
	}
	public function admin_goancode(){
		$this->loadModel('Setting');
		if(!empty($this->request->data) && $this->validation()){
			if($this->request->data['Setting']['site_google_analytic_code']!=''){
				$this->request->data['Setting']['site_google_analytic_code']=strip_tags($this->request->data['Setting']['site_google_analytic_code']);
				//$this->request->data['Setting']['site_google_analytic_code']=$this->request->data['Setting']['site_google_analytic_code'];
			}
			foreach($this->request->data['Setting'] as $key => $value){
				if($key == 'url_back_redirect' || $key == 'form' ){
					continue;
				}
				if(is_array($value)){
					if($value['error']==0){
						$ext = explode(".",$value['name']);
					
						$name = explode("_",$key);
						if(!file_exists(Configure::read('Path.goancode'))) {
							App::uses('Folder', 'Utility');
							mkdir(Configure::read('Path.goancode'), 0777);
							$dir = new Folder();
							$dir->chmod(Configure::read('Path.goancode'), 0777, true, array());	
						}
						$img_name = array_pop($name).".".array_pop($ext);
						move_uploaded_file($value['tmp_name'],Configure::read('Path.goancode').$img_name);
						$value = $img_name;
					}else{
						continue;
						//	$value;
					}
				}
				if($this->Setting->find('count',array('conditions'=>array('Setting.key'=>$key,'Setting.module'=>'goancode')))){
					
					$this->Setting->query("UPDATE `settings` SET `values`=\"$value\" , module=\"goancode\" WHERE `key`=\"$key\"");
				
				}else{
					$this->Setting->query("INSERT `settings` SET `values`=\"$value\"  , `key`=\"$key\" , module=\"goancode\"");
				}
				
				$this->Session->setFlash(__('Settings has been Saved Successfully'));
			}
			Cache::delete('site');
			$this->redirect(array('action'=>'goancode','?'=>array('back'=>$this->request->data['Setting']['url_back_redirect'])));
		}
		if(empty($this->request->data)){
			$this->request->data['Setting'] = $this->Setting->find('list',array('fields'=>array('Setting.key','Setting.values')));
		}else{
			$data = $this->Setting->find('list',array('fields'=>array('Setting.key','Setting.values')));
		}

		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/settings/seo'),
			'name'=>'Google Analytic Code'
		);
		$referer_url = $this->request->query('back');
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/settings/goancode',true) :Controller::referer();
		
		}
		
		$this->set('referer_url',$referer_url);
		$this->heading =  array("Google Analytic ","Code");
	}
	public function admin_smtp(){
		$this->loadModel('Setting');
		if(!empty($this->request->data) && $this->validation()){
			foreach($this->request->data['Setting'] as $key => $value){
				if($key == 'url_back_redirect' || $key == 'form' ){
					continue;
				}
				if(is_array($value)){
					if($value['error']==0){
						$ext = explode(".",$value['name']);
						$name = explode("_",$key);
						
					}else{
						continue;
						////	$value;
					}
				}
				if($this->Setting->find('count',array('conditions'=>array('Setting.key'=>$key,'Setting.module'=>'smtp')))){

					$this->Setting->query("UPDATE `settings` SET `values`=\"$value\" , module=\"smtp\" WHERE `key`=\"$key\"");
				
				} else{
					$this->Setting->query("INSERT `settings` SET `values`=\"$value\"  , `key`=\"$key\" , module=\"smtp\"");
				}
				
				$this->Session->setFlash(__('Settings has been saved successfully'));
			}
			Cache::delete('site');
			$this->redirect(array('action'=>'smtp','?'=>array('back'=>$this->request->data['Setting']['url_back_redirect'])));
		}
		if(empty($this->request->data)){
			$this->request->data['Setting'] = $this->Setting->find('list',array('fields'=>array('Setting.key','Setting.values')));
		}else{
			$data = $this->Setting->find('list',array('fields'=>array('Setting.key','Setting.values')));
			 
		}

		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/settings/smtp'),
			'name'=>'SMTP Settings'
		);
		$referer_url = $this->request->query('back');
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/settings/smtp',true) :Controller::referer();
		
		}
		
		$this->set('referer_url',$referer_url);
		$this->heading =  array("SMTP","Settings");
	}
	function user_validation(){
		$this->loadModel('UserManager.User');
		if($this->request->data['User']['form-name']=='PasswordChange'){
			$this->User->setValidation('PasswordChangeAdmin');
		}
		$this->User->set($this->request->data);
		$result = array();
		if ($this->User->validates()) {
			$result['error'] = 0;
		}else{
			$result['error'] = 1;
			$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
		}
		if($this->request->is('ajax')){
			$this->autoRender = false;
			$result['errors'] = $this->User->validationErrors;
			$errors = array();
		 
		foreach($result['errors'] as $field => $data){
			$errors['User'.Inflector::camelize($field)] = array_pop($data);
		}
		$result['errors'] = $errors;
			$view = new View();
			$result['error_message'] = $view->element('admin/message');
			echo json_encode($result);
			return;
		}
		return (int)($result['error'])?0:1; 
	}
	function validation(){
		if(empty($this->request->data)){
			throw new NotFoundException('404 Error - Page not found');
			return;
		}
		if(empty($this->request->data['Setting']['form-name'])){
			throw new NotFoundException('404 Error - Page not found');
			return;
		}
		if($this->request->data['Setting']['form-name']=='SiteSetting'){
			$this->Setting->setValidation('SiteSetting');
		}
		else if($this->request->data['Setting']['form-name']=='googleac'){
			$this->Setting->setValidation('googleac');
		}
		else if($this->request->data['Setting']['form-name']=='SmtpSetting'){
			$this->Setting->setValidation('SmtpSetting');
		}
		else if($this->request->data['Setting']['form-name']=='SeoSetting'){
			$this->Setting->setValidation('SeoSetting');
		}
		else if($this->request->data['Setting']['form-name']=='SocialMedia'){
			$this->Setting->setValidation('SocialMedia');
		}else{
			throw new NotFoundException('404 Error - Page not found');
			return;
		}
		$this->Setting->set($this->request->data);
		$result = array();
		if ($this->Setting->validates()) {
			$result['error'] = 0;
		}else{
			$result['error'] = 1;
			$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
		}
		
		if($this->request->is('ajax')){
			$this->autoRender = false;
			$result['errors'] = $this->Setting->validationErrors;
			$errors = array();
		 
		foreach($result['errors'] as $field => $data){
			$errors['Setting'.Inflector::camelize($field)] = array_pop($data);
		}
		 
			$result['errors'] = $errors;
			$view = new View();
			$result['error_message'] = $view->element('admin/message');
			echo json_encode($result);
			return;
		}
		return (int)($result['error'])?0:1; 
	}
	function admin_changepassword(){
		if (!empty($this->request->data) && $this->user_validation()){
			$data = $this->User->read(null, $this->Auth->user('id'));
			$data['User']['password'] = $this->request->data['User']['password'];
			$this->User->create();
			$this->User->save($data);
			$this->Session->setFlash('Password has been changed successfully.');
		}
		$this->request->data = array();
            $this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/settings/changepassword'),
				'name'=>'Change Password'
		);
		$referer_url = $this->request->query('back');
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/settings/site',true) :Controller::referer();
		
		}
		
		$this->set('referer_url',$referer_url);
		$this->heading =  array("Change","Password");    
	}
	function admin_sitesatus()
	{
		
	}
	public function admin_load_theme_prev_image($theme = ''){
		
		$dir = new Folder(App::themePath($theme).'webroot/img/screen');
		$files = $dir->find('.*\.jpg|.*\.jpeg|.*\.gif|.*\.png');
		$this->layout ='';
		$this->theme = $theme;
		$this->set('theme',$theme);
		$this->set('theme_image',$theme.'.jpg');
		$this->set('files',$files);
	}
	public function admin_cache($type = ""){
		$this->heading =  array("Manage","Cache"); 
		$this->request->data = array();
            $this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/settings/cache'),
				'name'=>'Manage Cache'
		);
		if($type=="data"){
			/*Remove TMP cache START*/
			$dir = new Folder(CACHE);
			$files = $dir->findRecursive('.*');
			foreach($files as $_file){
				unlink($_file);
			}
			/*Remove TMP cache START*/
			$this->Session->setFlash(__('All the files or data that store in temp folder are removed'));
		}
		
		
		if($type=="image"){
			$dir = new Folder(IMAGES);
			$files = $dir->findRecursive('.*');
			$result = $dir->read(); 
			foreach($result[0] as $_folder){
				if($_folder=="admin"){
					continue;
				}
				$_folder = IMAGES.$_folder.DS.'temp'.DS;
				$temp_folder = new Folder($_folder);
				$temp_files = $temp_folder->findRecursive(".*");
				
				foreach($temp_files as $_file){
					unlink($_file);
				}
			}
			$this->Session->setFlash(__('All the resized images that store in temp folder are removed'));
			
		}
		
		
		
		
		
	}
	
	
 }

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
