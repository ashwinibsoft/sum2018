<?php 
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
Class NewBuyersController extends NewBuyerManagerAppController{
	public $uses = array('NewBuyerManager.NewBuyer','Country','QuestionManager.Question','QuestionManager.QuestionCategorie','NewBuyerManager.NewBuyerQuestion','SupplierManager.FeedbackRequest','ExistingBuyerManager.EbLoginDetail','ExistingBuyerManager.ExistingBuyer');
	public $components=array('Email','RequestHandler','Image','MemberAuth','ExportXls');
	var $helpers = array('Captcha','Csv');
	public $paginate = array();
	public $id = null;	
	public $template=null;
	
	public function beforeFilter() {
			parent::beforeFilter();			
			//$this->Auth->allow('login','forgot','request');
			//$this->Auth->deny('profile', 'dashboard','edit_profile','questions','index','question_remove','supplier_list','settings');
	}
		
	function admin_index($search=null,$limit=10){
		
		$this->paginate = array();
		$condition = null;
		if($search=="_blank"){
			$search=null;
		}
		$this->paginate['limit']=$limit;
		if($this->request->is('post')){
			if(!empty($this->request->data['search'])){
				$search = $this->request->data['search'];
			}else{
				$search = '_blank';
			}
			if(!empty($this->request->data['limit'])){
				$limit = $this->request->data['limit'];
			}else{
				$limit = '10';
			}
			$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'index',$parent_id,$search,$limit));
		}
		if($search!=null){
			$search = urldecode($search);	
			$condition['OR'][]=array('NewBuyer.first_name like'=>'%'.$search.'%');
			$condition['OR'][]=array('NewBuyer.contact_person like'=>'%'.$search.'%');
			$condition['OR'][]=array('NewBuyer.title like'=>'%'.$search.'%');
			$condition['OR'][]=array('NewBuyer.org_name like'=>'%'.$search.'%');
		}
		
		
		$new_buyers = array();
		$condition['NewBuyer.request_status'] = 0;
		$condition['NewBuyer.process_step >='] = 2;
		$this->paginate['order']=array('NewBuyer.id'=>'DESC');
		$new_buyers= $results=$this->paginate("NewBuyer", $condition);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/new_buyer_manager/new_buyers'),
			'name'=>'Manage New Buyer'
		);
		
		$this->heading =  array("Manage","New Buyer");
	//	print_r($new_buyers);die;
		//$this->set('parent_id',$parent_id);
		$this->set('new_buyers',$new_buyers);
		$this->set('limit',$limit);
		$this->set('search',$search);
		$this->set('url','/'.$this->params->url);
		
	}
	
	function admin_export()
	{
		$condition = array();														
		$options['group']=array('NewBuyer.id');
		$options['order']= array('NewBuyer.id'=>'DESC');
		$newbuyerInfos = $this->NewBuyer->find('all',$options);
		
		if(empty($newbuyerInfos)){
			$this->Session->setFlash(__('No data found to export!', true),'default','','error');
			$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'index'));
		}
		$this->set('newbuyerInfos', $newbuyerInfos);
		$this->layout = null;
		$this->autoLayout = false;
		Configure::write('debug','2');	
		
		$currntdate=date('d/m/Y-H:i:s');  
		$fileName='new_buyer_list-'.$currntdate.'.xls';
		
		$headerRow = array("S.No","Organisation Name","State","Country","First Contact Person's Full Name","First Contact Person's Email"," First Contact Person's Contact Number"," First Contact Person's Designation","Second Contact Person's Full Name"," Second Contact Person's Email"," Second Contact Person's Contact Number","Second Contact Person's Designation","Account Status");
		
		$data='';
		$i = 0;
		
		foreach ($newbuyerInfos as $n_buyer)
		{
			 
			$i++;
			if($n_buyer['NewBuyer']['s_title'] || $n_buyer['NewBuyer']['s_first_name'] || $n_buyer['NewBuyer']['s_middle_name'] || $n_buyer['NewBuyer']['s_last_name']){	
				$name = $n_buyer['NewBuyer']['s_title'].' '.$n_buyer['NewBuyer']['s_first_name'].' '.$n_buyer['NewBuyer']['s_middle_name'].' '.$n_buyer['NewBuyer']['s_last_name'];
			}else{
				$name = 'N/A';
			}
			if($n_buyer['NewBuyer']['status'] == 1){ 
				$status =  'Active'; 
			}elseif($n_buyer['NewBuyer']['status'] == 1){ 
				$status = 'Blocked'; 
			}else{
				$status = 'Inactive';
			}

			if($n_buyer['NewBuyer']['s_email']){	
				$s_email = $n_buyer['NewBuyer']['s_email'];
			}else{
				$s_email = 'N/A';
			}

			if($n_buyer['NewBuyer']['s_designation']){	
				$s_designation = $n_buyer['NewBuyer']['s_designation'];
			}else{
				$s_designation = 'N/A';
			}

			if($n_buyer['NewBuyer']['s_country_code'] && $n_buyer['NewBuyer']['s_area_code'] && $n_buyer['NewBuyer']['s_contact_number']){	
				$s_contact = $n_buyer['NewBuyer']['s_country_code'].' '.$n_buyer['NewBuyer']['s_area_code'].' '.$n_buyer['NewBuyer']['s_contact_number'];
			}else{
				$s_contact = 'N/A';
			}
			//$action = ($newbuyerInfo['NewBuyer']['status']==1) ? 'Yes' : 'No';

			$fullname1=$n_buyer['NewBuyer']['title'].' '.ucfirst($n_buyer['NewBuyer']['first_name']).' '.ucfirst($n_buyer['NewBuyer']['middle_name']).' '.ucfirst($n_buyer['NewBuyer']['last_name']);

			$contact1=$n_buyer['NewBuyer']['country_code'].' '.$n_buyer['NewBuyer']['area_code'].' '.$n_buyer['NewBuyer']['contact_number'];

			$fullname2=$n_buyer['NewBuyer']['s_title'].' '.ucfirst($n_buyer['NewBuyer']['s_first_name']).' '.ucfirst($n_buyer['NewBuyer']['s_middle_name']).' '.ucfirst($n_buyer['NewBuyer']['s_last_name']);

			$contact2=$n_buyer['NewBuyer']['s_country_code'].' '.$n_buyer['NewBuyer']['s_area_code'].' '.$n_buyer['NewBuyer']['s_contact_number'];

			$newd=array(
				$i,
				$n_buyer['NewBuyer']['org_name'],
				$n_buyer['NewBuyer']['state'],
				$n_buyer['Country']['country_name'],
				$fullname1,
				$n_buyer['NewBuyer']['email_id'],
				$contact1,
				ucfirst($n_buyer['NewBuyer']['designation']),
				$fullname2,
				$n_buyer['NewBuyer']['s_email'],
				$contact2,
				$n_buyer['NewBuyer']['s_designation'],
				$status
			);
			$data[]=$newd;
		}
		$this->ExportXls->export($fileName, $headerRow, $data);
	}
	
	public function admin_create_pdf(){
      
		//$condition = array();			
		//$options['order']= array('NewBuyer.id'=>'ASC');
		//$options['NewBuyer.request_status'] = 0;
		//$options['NewBuyer.process_step >='] = 2;
		$n_buyers = $this->NewBuyer->find('all',array('conditions'=>array('NewBuyer.request_status'=>0,'NewBuyer.process_step >=' =>2)));
		//if(empty($n_buyers)){
		//	$this->Session->setFlash(__('No data found to export!', true),'default','','error');
		//	$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'index'));
		//}
		
		$this->set('n_buyers', $n_buyers);
		//print_r($n_buyers); die;
		$this->layout = null;
		$this->autoLayout = false;
		Configure::write('debug','2');
		
		$currntdate=date('d-m-Y'); 
		$this->set('currntdate',$currntdate);
		$this->layout = '/pdf/default';
	
	}
	
	function admin_add($id=null){
		$countries = $this->Country->country_list();
		//print_r($countries);die;
		$path  = $this->webroot;	
		
		$this->breadcrumbs[] = array(
		'url'=>Router::url('/admin/home'),
		'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/new_buyer_manager/new_buyers'),
				'name'=>'Manage New Buyer'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/new_buyer_manager/new_buyers/add/'.$id),
				'name'=>($id==null)?'Add New Buyer':'Update New Buyer'
		);
		if($id==null){
			$this->heading =  array("Add","New Buyer");
		}else{
			$this->heading =  array("Update","New Buyer");
		}
	
		if(!empty($this->request->data) && $this->validation()){
			
			if(!$id){
				$this->request->data['NewBuyer']['created_at']=date('Y-m-d H:i:s');
				$this->request->data['NewBuyer']['pass_update'] = 0;
				
			}else{
				$this->request->data['NewBuyer']['modified_at']=date('Y-m-d H:i:s');
			}
			if(empty($this->request->data['NewBuyer']['id'])){
				if(isset($this->request->data['save']) && $this->request->data['save']=='Save'){
					$this->request->data['NewBuyer']['status'] = 1;
				}else{
					$this->request->data['NewBuyer']['status'] = 1;
				}
				$randompassword=self::_randomPassword(); 				
				$pass = Security::hash(Configure::read('Security.salt').$randompassword);
				$this->request->data['NewBuyer']['passwordurl']='';
				$this->request->data['NewBuyer']['password']= $pass; 
			}
		//	echo "<pre>"; print_r($randompassword); die;
			$this->NewBuyer->create();
			$this->NewBuyer->save($this->request->data,array('validate'=>false));
			Cache::delete('new_buyers');
			$id = $this->NewBuyer->id;
			
			if ($this->request->data['NewBuyer']['id']) {
				$this->Session->setFlash(__('Record has been updated successfully'));
			} 
			else{
				$this->Session->setFlash(__('Record has been added successfully'));
			}
			if(empty($this->request->data['NewBuyer']['id'])){
				$options = array();
				$options['replacement'] = array('{PASSWORD}'=>$randompassword,'{USERNAME}'=>$this->request->data['NewBuyer']['email_id'],'{url}'=>Configure::read('Site.url')."login");
				$options['to'] = array($this->request->data['NewBuyer']['email_id']); 				
				$this->MyMail->SendMail(13,$options);			
			}
			
			$this->redirect(array('action'=>'add',$id,'?'=>array('back'=>$this->request->data['NewBuyer']['url_back_redirect'])));
		}
		else{
			if(!empty($this->request->data)){
				$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
			}
			
			if($id!=null){
				$this->request->data = $this->NewBuyer->read(null,$id);
			}else{
				$this->request->data = array();
			}
		}
		
		$referer_url = $this->request->query('back');
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/new_buyer_manager/new_buyers',true) :Controller::referer();
		
		}
		$this->set('countries',$countries);
		$this->set('referer_url',$referer_url);
		$this->set('nb_id',$id);
	}
	
	function admin_delete($id=null){
		$this->autoRender = false;
		$data=$this->request->data['NewBuyer']['id'];
		$action = $this->request->data['NewBuyer']['action'];
		$ans="0";
		foreach($data as $value){

			if($value!='0'){
				if($action=='Publish'){
					$team['NewBuyer']['id'] = $value;
					$team['NewBuyer']['status']=1;
					$this->NewBuyer->create();
					$this->NewBuyer->save($team);
					$ans="1";
				}
				if($action=='Unpublish'){
					$team['NewBuyer']['id'] = $value;
					$team['NewBuyer']['status']=0;
					$this->NewBuyer->create();
					$this->NewBuyer->save($team);
					$ans="1";
				}
				if($action=='Delete'){
					$team = $this->NewBuyer->find('first', array('conditions'=> array('NewBuyer.id' => $value),'fields' => array('NewBuyer.logo')));
					if (!empty($team['NewBuyer']['logo'])) {
						   @unlink(WWW_ROOT."img/newbuyer/logo/". $team['NewBuyer']['logo']);
					}
					$this->NewBuyer->delete($value);
					$this->NewBuyer->delete_routes($value,'NewBuyer');
					$ans="2";
				}
			}
		}
		Cache::delete('new_buyers');
		if($ans=="1"){
			$this->Session->setFlash(__('NewBuyer has been '.strtolower($this->data['NewBuyer']['action']).'ed successfully', true));
		}
		else if($ans=="2"){
			$this->Session->setFlash(__('NewBuyer has been '.strtolower($this->data['NewBuyer']['action']).'d successfully', true));
		}else{
			$this->Session->setFlash(__('Please Select any NewBuyer', true),'default','','error');
		}
		$this->redirect($this->request->data['NewBuyer']['redirect']);
                 
	}
	
	function admin_request(){
		$this->paginate = array();
		//$condition = array();
		//$condition['OR'] = array('NewBuyer.request_status' => 1,'NewBuyer.process_step <' => 2)
		//$condition['NewBuyer.request_status'] = 1;
		//$condition['NewBuyer.process_step < '] = 2;
        //$this->paginate['fields'] = array('NewBuyer.org_name','NewBuyer.email_id','NewBuyer.id');
        $this->paginate['order']=array('NewBuyer.id'=>'DESC');
		$requests = $this->paginate("NewBuyer",array('OR' =>array('NewBuyer.request_status'=>1,'NewBuyer.process_step < ' => 2 ,'NewBuyer.process_step ' => NULL )));
		 
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/new_buyer_manager/new_buyers'),
			'name'=>'New Buyer Requests'
		);
		
		$this->heading =  array("New Buyer","Requests");
	    //print_r($new_buyers);die;

		$this->set('requests',$requests);
	}
	
	public function admin_request_response($id=null){
		
		$this->autoRender = false;
		if($id != null){
			$buyer_info = $this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.id'=>$id)));
		
			$randompassword=self::_randomPassword(); 				
			$pass = Security::hash(Configure::read('Security.salt').$randompassword);
			
			$options = array();
			$options['replacement'] = array('{PASSWORD}'=>$randompassword,'{USERNAME}'=>$buyer_info['NewBuyer']['email_id'],'{url}'=>Configure::read('Site.url')."new_buyer_manager/new_buyers/login/1");
			$options['to'] = array($buyer_info['NewBuyer']['email_id']); 				
			$this->MyMail->SendMail(13,$options);
			
			$user['NewBuyer']['id']=$buyer_info['NewBuyer']['id'];
			$user['NewBuyer']['passwordurl']='';
			$user['NewBuyer']['password']= $pass;
			$user['NewBuyer']['request_status']= 0;
			$user['NewBuyer']['status']= 1; 
			
			$this->NewBuyer->create();
			$this->NewBuyer->save($user,array('validate'=>false));
			Cache::delete('new_buyers');
			
			$this->Session->setFlash(__('New buyer added successfully, Email with USERID and a temporary password sent to user successfully.'));
			
			$this->redirect(array('action'=>'admin_request'));
		}
	}

	function _randomPassword() {
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = '';
		$alphaLength = strlen($alphabet) - 1;
			for ($i = 0; $i < 8; $i++) {
				$n = rand(0, $alphaLength);
				$pass.= $alphabet[$n];
			}
		return $pass;
	}
	
	/*function captcha()	{
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
	}*/
	
	/*public function login(){			
		$page = $this->__load_page(51);
		$this->set('page', $page);
		if($this->Auth->user('id')){
			$this->redirect($this->Auth->redirect());
		}
		if(!empty($this->request->data) && $this->validation())
		{
			if ($this->request->is('post')){	
						
				if($this->Auth->login()){					
					$this->redirect($this->Auth->redirect());
				}else{
					$this->Session->setFlash(__('Invalid username or password, try again.'), 'default', array(), 'error');
					$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'login'));
				}		
			}
		}
	}*/
	
	public function logout() {
		$this->MemberAuth->removeMemberSession();
		//$this->Session->setFlash(__('You have logged out successfully.'));
		$this->Session->setFlash(__('You have logged out successfully.'),'default',array(),'success');
		$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'login'));
	}
	

	function ajax_sort(){
		Cache::delete('new_buyers'); 
		$this->autoRender = false;
		foreach($_POST['sort'] as $order => $id){
			$slide= array();
			$slide['NewBuyer']['id'] = $id;
			$slide['NewBuyer']['team_order'] = $order;
		  
			$this->NewBuyer->create();
			$this->NewBuyer->save($slide);
		}
	}
	
	function validation(){
		
		if(!empty($this->request->data['NewBuyer']['form'])){
			if($this->request->data['NewBuyer']['form']=="team_add" && $this->request->data['NewBuyer']['status']==2){
				return true;
			}
			$this->NewBuyer->setValidation($this->request->data['NewBuyer']['form']);
		}else{
			throw new NotFoundException('404 Error - NewBuyer not found');
		}
		$this->NewBuyer->set($this->request->data);
		return $this->NewBuyer->validates();
	}
		
	function ajax_validation($returnType = 'json'){
		
		$this->autoRender = false;
		if(!empty($this->request->data)){
			$result = array();
				if(!empty($this->request->data['NewBuyer']['s_title']) && !empty($this->request->data['NewBuyer']['s_title']) && !empty($this->request->data['NewBuyer']['s_first_name']) && !empty($this->request->data['NewBuyer']['s_last_name']) && !empty($this->request->data['NewBuyer']['s_email']) && !empty($this->request->data['NewBuyer']['s_contact_number']) && !empty($this->request->data['NewBuyer']['s_designation'])){
			$flag = 0;	
				
			}elseif(empty($this->request->data['NewBuyer']['s_title']) && empty($this->request->data['NewBuyer']['s_title']) && empty($this->request->data['NewBuyer']['s_first_name']) && empty($this->request->data['NewBuyer']['s_last_name']) && empty($this->request->data['NewBuyer']['s_email']) && empty($this->request->data['NewBuyer']['s_contact_number']) && empty($this->request->data['NewBuyer']['s_designation'])){
			$flag = 0;		
			}else{
				$flag = 1;	
			}
			if(!empty($this->request->data['NewBuyer']['form'])){
				$this->NewBuyer->setValidation($this->request->data['NewBuyer']['form']);
			}
			$this->NewBuyer->set($this->request->data);
			
		
			if($this->request->data['NewBuyer']['form']=="new_buyer_add" && $this->request->data['NewBuyer']['status']==2){
				$result['error'] = 0;
			}else{
				if($this->NewBuyer->validates() && $flag == 0){
					$result['error'] = 0;
				}elseif($flag == 1){
					$result['error'] = 1;
					$result['s_person']= 1;
				}else{
					$result['error'] = 1;
					$this->Session->setFlash(__('Please fill all the required fields.'),'default',array(),'error');
				}
			}
			
			$errors = array();
			$result['errors'] = $this->NewBuyer->validationErrors;
			foreach($result['errors'] as $field => $data){
			  $errors['NewBuyer'.Inflector::camelize($field)] = array_pop($data);
			}
			$result['errors'] = $errors;
			$view = new View();
			
			$result['error_message'] = $view->element('admin/message');
			echo json_encode($result);
			return;
		}
		echo json_encode(array());
	}
	
	private function __load_page($id=null){
		
		$this->loadModel('ContentManager.Page');
		$page = $this->Page->find('first',array('conditions'=>array('Page.id'=>$id,'Page.status'=>1)));
		if (empty($page)) {
		throw new NotFoundException('404 Error - Page not found');
		}
		$this->System->set_seo('site_title',$page['Page']['page_title']);
		$this->System->set_seo('site_metakeyword',$page['Page']['page_metakeyword']);
		$this->System->set_seo('site_metadescription',$page['Page']['page_metadescription']);
		if((int)Configure::read('Section.default_banner_image') && ($page['Page']['use_default_image'] || $this->System->get_setting('page','override_banner_image'))){
			$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		}
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		
		return $page;
	}
	
	private function __get_continent($country=null){
		$continent = $this->Country->find('first',array('conditions'=>array('Country.country_code_char2'=>$country),'fields'=>array('Country.continent_code')));
		return $continent['Country']['continent_code'];
	}
	
	private function __get_process_step($member_id){
		$process_step = $this->NewBuyer->find('first',array('fields'=>array('NewBuyer.process_step'),'conditions'=>array('NewBuyer.id'=>$member_id)));
		$step = $process_step['NewBuyer']['process_step'];
		return $step;
	}
	public function dummy(){
		
	}
	
	public function dashboard($id=null){
		
	   $member_id = self::_check_member_login();
	   
	   $this->loadModel('SupplierManager.SupplierBuyer');
	  	
	   if(!empty($member_id)){
		   
	   /*========================GET CURRENT NEWBUYER INFO =================================*/
		   
		  $buyer_info = $this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.id'=>$member_id)));
		  
		  $this->set('info', $buyer_info);
	
	   /*========================REDIRECTION BASED ON COMPLETE STEP BY NEWBUYER=================================*/
	     
		  if($buyer_info['NewBuyer']['pass_update'] == 0){
				//$this->Session->setFlash(__('Please change your password'));
				$this->Session->setFlash(__('Please change your password.'),'default',array(),'info');
				$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'resetpassword'));
		  }elseif($buyer_info['NewBuyer']['pass_update'] == 1 && $buyer_info['NewBuyer']['process_step'] == 1 ){
				$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'edit_profile'));	
		  } elseif($buyer_info['NewBuyer']['pass_update'] == 1 && $buyer_info['NewBuyer']['process_step'] == 2 ){
				$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'settings'));
		  }elseif($buyer_info['NewBuyer']['pass_update'] == 1 && $buyer_info['NewBuyer']['process_step'] == 3 ){
				
				$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'index'));
		  }
			
		 if($id){
		 $this->Session->setFlash(__('Congratulations, You have completed all the required steps.'),'default',array(),'success');
		 }
		  
		 $this->set('process_step', $buyer_info['NewBuyer']['process_step']);
		 
		/*========================GET ALL CONTINENTS =================================*/
			
		  $Document = ClassRegistry::init('Continents');
          $Document->find('all');
            
          $this->loadModel('Continents');
          $temp_val = $this->Continents->find('first',array('conditions'=>array('Continents.code'=>$buyer_info['NewBuyer']['continent'])));
		  
		  $this->set('continent_name', $temp_val);
		  
		/*========================GET ALL QUESTION RELATED TO CURRENT NEWBUYER =================================*/
			
		  $this->NewBuyerQuestion->bindModel(array('belongsTo' => array('Question' => array('foreignKey' => false,'conditions' => array('NewBuyerQuestion.question_id = Question.id')))));
			
		  $questions = $this->NewBuyerQuestion->find('all',array('conditions'=>array('NewBuyerQuestion.new_buyer_id'=>$member_id),'order' => array('NewBuyerQuestion.id' => 'ASC')));

		  $this->set('ques', $questions);  
		  $category = array();
		   foreach($questions as $question){
				if(!(in_array($question['Question']['category_id'],$category))){
					$category[] = $question['Question']['category_id'];
				}		
		   }
		   $categories = $this->QuestionCategorie->find('all',array('fields'=>array('QuestionCategorie.name','QuestionCategorie.id'),'order'=>array('QuestionCategorie.name'=>'ASC'),'conditions'=>array('QuestionCategorie.id'=>$category)));
		   //~ $categories = $this->QuestionCategorie->find('all',array('fields'=>array('QuestionCategorie.name','QuestionCategorie.id'),'conditions'=>array('QuestionCategorie.id'=>$category),'order' => array('QuestionCategorie.id' => 'DESC')));
			
		   $this->set('categories',$categories);
		   
		   /*========================GET ALL SUPPLIER RELATED TO CURRENT NEWBUYER=================================*/
			
		   $this->loadModel('SupplierManager.SupplierBuyer');
		  		
		   $suppliers = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.buyer_id'=>$member_id)));

		   $this->set('suppliers',$suppliers);
		   
		   /*========================GET ALL REQUEST RELATED TO CURRENT NEWBUYER=================================*/
		  
		   $this->FeedbackRequest->bindModel(array('hasMany' => array('Payment'=>array('order' => 'Payment.id DESC'))));
		   
		   //$requests = $this->FeedbackRequest->find('all',array('order' => 'FeedbackRequest.id DESC'));
		   
		   $requests = $this->FeedbackRequest->find('all',array('conditions' => array('SupplierBuyer.buyer_id' => $member_id),'order' => 'FeedbackRequest.id DESC'));	 
		  	  
		   $result = $all_requests = $req_id = array();
		   
		   foreach($suppliers as $supplier){
			
			/*foreach($requests as $request){
				
				if($request['FeedbackRequest']['supplier_id'] == $supplier['SupplierBuyer']['supplier_id']){
					$nb_list = json_decode($request['FeedbackRequest']['new_buyers'],true);
					
					if(in_array($member_id,$nb_list)){
						$all_requests[] = $request;
					}
				}
				
		    }*/
		 
		// foreach($all_requests as $request_one){
		 foreach($requests as $request_one){
				if (!in_array($request_one['FeedbackRequest']['id'], $req_id)) {
					$req_id[] = $request_one['FeedbackRequest']['id'];
				}
		  }
		  
		  $status = $this->__get_status($req_id);
			
		  $this->set('status',$status);
		}
		
		//$this->set('all_requests',$all_requests);
		$this->set('all_requests',$requests);
		
	    //foreach($all_requests as $r_all){
	    foreach($requests as $r_all){
		  $eb_arr=json_decode($r_all['FeedbackRequest']['existing_buyers']);
			foreach($eb_arr as $eb) {
			    $result['existingBuyer'][$eb] = $this->ExistingBuyer->find('first',array('conditions' => array('ExistingBuyer.id' => $eb)));
			    
					}
			  }
			
		 }
		  
		$existing_buyer_id = array();
		foreach($result['existingBuyer'] as $arr1){
			foreach ($arr1 as $value) {
				if(empty($existing_buyer_id)){
					$existing_buyer_id[$value['supplier_id']] .=$value['id'];
				} else {
					$existing_buyer_id[$value['supplier_id']] .=','.$value['id'];
				}
			 }
		}
		
		if(!empty($req_id)){
		  foreach ($req_id as $reqid) {
			 if($existing_buyer_id!=""){
			  $db = ConnectionManager::getDataSource('default');
			  $query="SELECT `supplier_id`,`selected_new_b_exist` FROM `feedback_requests` WHERE `id`='$reqid'";
			  $supplier = $db->query($query);
			  //print_r($supplier);
			  $current_active = json_decode($supplier[0]['feedback_requests']['selected_new_b_exist'],true);
			   //print_r($current_active[3]);
			  
			  $active_ids = $current_active[$member_id];
			  //print_r($active_ids);
			  foreach($active_ids as $active_id){
			  $currnrt_active_existing_buyer[] = $active_id;
			  $q = "SELECT `existing_buyer_id`,`eb_status` FROM `eb_login_details` WHERE `request_id` = $reqid AND `existing_buyer_id` = $active_id";
			
	         
	          $existing_buyer_status[$supplier[0]['feedback_requests']['supplier_id']] = $db->query($q);
	         }
		    }	
		  }	
		}
	//print_r($currnrt_active_existing_buyer); 
		//~ print_r(json_decode($supplier[0]['feedback_requests']['selected_new_b_exist'],true));
	    //~ print_r($member_id);
	  //~ $existing_buyer_status = $this->FeedbackRequest->query('SELECT *  FROM `feedback_responses` WHERE request_id IN ('.$existing_buyer_id.')');
		
		$existing_buyer_name_info = $result['existingBuyer'];
	    //~ print_r($existing_buyer_name_info);
		//~ print_r($currnrt_active_existing_buyer);
		
		$existing_buyer_name = array();
		foreach($currnrt_active_existing_buyer as $ca)
		{
		  $existing_buyer_name[$ca] =  $existing_buyer_name_info[$ca];
		}
		//print_r($existing_buyer_name);
		$page = $this->__load_page(52);
		$this->set('page', $page);
		$this->set('member_id', $member_id);
		$this->set('existing_buyer_name',$existing_buyer_name);
		$this->set('existing_buyer_status',$existing_buyer_status);
	}
	
	public function profile_edit() {
	 $member_id = self::_check_member_login();
		$page = $this->__load_page(50);
		$this->set('page', $page);
		$countries = $this->Country->country_list();
		$this->set('countries',$countries);
				
		$details = $this->NewBuyer->read(null,$member_id);
		
		if($details['NewBuyer']['pass_update'] == 0){
			$title = "Create Profile";
		}else{
			$title = "Edit Profile";
		}
		$this->set('title',$title);
		if(!empty($this->request->data) && $this->validation())
		{
			if($details['NewBuyer']['process_step'] == 1){
				$this->request->data['NewBuyer']['process_step'] = 2;
			}
			
			
			$existing_image='';
			$logo_image = $this->NewBuyer->find('first',array('fields'=>array('NewBuyer.logo','NewBuyer.process_step'),'conditions'=>array('NewBuyer.id'=>$member_id)));
			$existing_image = $logo_image['NewBuyer']['logo'];
				
			$_options = array(
			'destination'=>Configure::read('Path.NewBuyerLogo'),
			'image'=>$this->request->data['NewBuyer']['logo']
			);
		
			if($this->request->data['NewBuyer']['logo']['error'] > 0){
				$this->request->data['NewBuyer']['logo'] = $existing_image;
				
			}else{
				
				if($this->request->data['NewBuyer']['logo']['error'] < 1){
				
				$this->request->data['NewBuyer']['logo'] = $this->System->Image->upload($_options);
				
				
				}else{
					$this->request->data['NewBuyer']['logo'] = "";
				}
			}
			
			
			$this->request->data['NewBuyer']['continent'] = $this->__get_continent($this->request->data['NewBuyer']['country']);
			$this->request->data['NewBuyer']['updated_at']=date('Y-m-d H:i:s');
			$this->request->data['NewBuyer']['id'] = $member_id;
			
			//~ if(!empty($this->request->data['NewBuyer']['s_title']) && !empty($this->request->data['NewBuyer']['s_title']) && !empty($this->request->data['NewBuyer']['s_first_name']) && !empty($this->request->data['NewBuyer']['s_last_name']) && !empty($this->request->data['NewBuyer']['s_email']) && !empty($this->request->data['NewBuyer']['s_contact_number']) && !empty($this->request->data['NewBuyer']['s_designation'])){
			//~ echo 1;
			//~ }
			//~ elseif(empty($this->request->data['NewBuyer']['s_title']) && empty($this->request->data['NewBuyer']['s_title']) && empty($this->request->data['NewBuyer']['s_first_name']) && empty($this->request->data['NewBuyer']['s_last_name']) && empty($this->request->data['NewBuyer']['s_email']) && empty($this->request->data['NewBuyer']['s_contact_number']) && empty($this->request->data['NewBuyer']['s_designation'])){
			//~ echo 2;
		    //~ }
		    //~ echo "<pre>";
			//~ print_r($this->request->data);exit;
			$this->NewBuyer->create();
			$this->NewBuyer->save($this->request->data,array('validate'=>false));
			
			$NewBuyer=$this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.id'=>$member_id)));
			$this->MemberAuth->updateMemberSession($NewBuyer['NewBuyer']);
			
			//$this->Session->setFlash(__('Profile has been updated successfully.'),'default',array(),'success');
			
			$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'NewBuyers','action'=>'dashboard#dnbprofile'));
			
		}else{
			
			if(!empty($member_id)){
			$this->request->data = $details;
			
			$existing_logo = $this->request->data['NewBuyer']['logo'];
			$this->set('existing_logo',$existing_logo);
		    }else{
				$this->request->data = array();
				
			}
		}
		$this->set('id',$member_id);
		$this->set('process_step',$details['NewBuyer']['process_step']);
		$this->set('pass_update',$details['NewBuyer']['pass_update']);	
		
	}
	
	
	public function resetpassword($str=null)
	{		
		if($str){	
			$page = $this->__load_page(55);
			$this->set('page', $page);
			$title = "Reset Password";
			$user = $this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.passwordurl'=>$str)));
			if(!empty($user)){
				if(!empty($this->request->data && $this->validation1())){
				
				//	echo 1;die;
					if($this->request->data['NewBuyer']['password']==$this->request->data['NewBuyer']['confirm_pass']){
						$nb_id=$user['NewBuyer']['id'];
						//$this->request->data['NewBuyer']['passwordurl']='';
						$password=Security::hash(Configure::read('Security.salt').$this->request->data['NewBuyer']['password']);
						
						if($password == $user['NewBuyer']['password'])
						{
							
						$this->Session->setFlash(__('Password must be different from your current password.Please try again'),'default',array(),'error');
						
						$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'resetpassword'));
							
							
					    }else {
						
						$this->NewBuyer->updateAll(array('NewBuyer.password' =>"'$password'",'NewBuyer.passwordurl'=>"''"),array('NewBuyer.id' =>$nb_id));
						
						//print_r($this->request->data);die; 
					//	$this->NewBuyer->create();
						//$this->NewBuyer->save($this->request->data);
						//$this->Session->setFlash('Your Password has been changed successfully. Please Login.');
						$this->Session->setFlash(__('Your Password has been changed successfully. Please Login.'),'default',array(),'success');
						$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'login'));
					   }
					}else{
						//$this->Session->setFlash('Password and Confirm password does not match, Please try again','default','msg','error');
						$this->Session->setFlash(__('Password and Confirm password does not match, Please try again.'),'default',array(),'error');
					}
				}
			}else{
				$this->Session->setFlash(__('This link has been expired.'),'default',array(),'error');
				$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'login'));		
			}		
		}else{
			$page = $this->__load_page(54);
			$this->set('page', $page);
			$title = "Change Password";
			$member_id = self::_check_member_login();
			$user = $this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.id'=>$member_id)));
			
			if(!empty($user)){
				if(!empty($this->request->data)){		
					//print_r($this->request->data); die;
					
					if($this->request->data['NewBuyer']['password']==$this->request->data['NewBuyer']['confirm_pass']){
						$this->request->data['NewBuyer']['id']=$user['NewBuyer']['id'];
						$this->request->data['NewBuyer']['password']=Security::hash(Configure::read('Security.salt').$this->request->data['NewBuyer']['password']); 
						//$this->NewBuyer->create();
						//$this->NewBuyer->save($this->request->data);
						//$this->NewBuyer->id = $user['NewBuyer']['id'];
						//$this->NewBuyer->saveField('password', $this->request->data['NewBuyer']['password']);
						$buyer_data['NewBuyer']['id'] = $user['NewBuyer']['id'];
						$buyer_data['NewBuyer']['password']=$this->request->data['NewBuyer']['password'];
						
						if($this->request->data['NewBuyer']['password'] == $user['NewBuyer']['password']){
							
						$this->Session->setFlash(__('Password must be different from your current password.Please try again'),'default',array(),'error');
						
						$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'resetpassword'));	
							
						}else{
						$buyer_data['NewBuyer']['process_step'] = 1;
						$buyer_data['NewBuyer']['pass_update'] = 1;
						$this->NewBuyer->create();
						$this->NewBuyer->save($buyer_data,array('validate'=>false));
						if($user['NewBuyer']['pass_update'] == 0){
							$this->Session->setFlash(__('Your Password has been changed successfully. Please Enter Your Details.'),'default',array(),'success');
							//$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'success',1));
							
							$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'edit_profile'));
							
						}else{
							
							$this->Session->setFlash(__('Your Password has been changed successfully.'),'default',array(),'success');
							$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'dashboard'));
						  }
					   }
					}else{
						//$this->Session->setFlash('Password and Confirm password does not match, Please try again','default','msg','error');
						
						$this->Session->setFlash(__('Password and Confirm password does not match, Please try again.'),'default',array(),'error');
					}
				}
				$this->set('member_id',$member_id);
				$this->set('process_step',$user['NewBuyer']['process_step']);
			}else{
				$this->Session->setFlash(__('Did you really think you are allowed to see that?.'),'default',array(),'error');
				$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'login'));
			}
		}
		
		$this->set('title', $title);
		$this->set('str', $str);
	}
	
	public function enterpass($id=null){	
		$this->set('id', $id);
	}
	
	public function change_password(){
		
		$page = $this->__load_page(54);
		$this->set('page', $page);
		$title = "Change Password";
		$member_id = self::_check_member_login();
		$process_step = $this->__get_process_step($member_id);	
		$this->set('process_step',$process_step);
		$user = $this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.id'=>$member_id)));
		if(!empty($user)){
			if(!empty($this->request->data)){		
				//print_r($this->request->data); die;
				if(Security::hash(Configure::read('Security.salt').$this->request->data['NewBuyer']['old_password']) == $user['NewBuyer']['password']){
					if($this->request->data['NewBuyer']['password']==$this->request->data['NewBuyer']['confirm_pass']){
						$this->request->data['NewBuyer']['id']=$user['NewBuyer']['id'];
						$this->request->data['NewBuyer']['password']=Security::hash(Configure::read('Security.salt').$this->request->data['NewBuyer']['password']); 
						//$this->NewBuyer->create();
						//$this->NewBuyer->save($this->request->data);
						$this->NewBuyer->id = $user['NewBuyer']['id'];
						$this->NewBuyer->saveField('password', $this->request->data['NewBuyer']['password']);
						if($user['NewBuyer']['pass_update'] == 0){
							$this->Session->setFlash(__('Your Password has been changed successfully. Please Enter Your Details.'),'default',array(),'success');
							$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'edit_profile'));
						}else{
							$this->Session->setFlash(__('Your Password has been changed successfully.'),'default',array(),'success');
							$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'dashboard#nbPass'));
						}
					}else{
						//$this->Session->setFlash('Password and Confirm password does not match, Please try again','default','msg','error');
						$this->Session->setFlash(__('Password and Confirm password does not match, Please try again.'),'default',array(),'error');
					}
				}else{
					//$this->Session->setFlash('You have entered incorrect current password, Please try again','default','msg','error');
					$this->Session->setFlash(__('You have entered incorrect current password, Please try again.'),'default',array(),'error');
				}
			}
			$this->set('member_id',$member_id);
		}else{
			//$this->Session->setFlash('Did you really think you are allowed to see that?');
			$this->Session->setFlash(__('Did you really think you are allowed to see that?'),'default',array(),'error');
			$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'login'));
		}
		$this->set('title', $title);
		$this->set('id', $member_id);
	}
	
	public function enterresp($id=null){
		$this->autoRender = false;		
		$user = $this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.id'=>$id)));
		if(!empty($this->request->data['NewBuyer']['password'])){
			$this->request->data['NewBuyer']['password']=Security::hash(Configure::read('Security.salt').$this->request->data['NewBuyer']['password']); 			
			if(!empty($user)){
				if($user['NewBuyer']['password']===$this->request->data['NewBuyer']['password'])
				{
					$result['error'] = 0;
					$result['msg']='';
				}else{
					$result['error'] = 1;
					$result['msg'] = 'Incorrect Password. Please enter your correct existing password.';
				}
			}			
						
		}else{
			$result['error'] = 1;
			$result['msg'] = 'Please enter existing password.';
		}
		return json_encode($result);
	}
	
	public function edit_profile(){
		 //~ echo "<pre>";
			 //~ print_r($this->request->data); die;
		$member_id = self::_check_member_login();
		$page = $this->__load_page(50);
		$this->set('page', $page);
		$countries = $this->Country->country_list();
		$this->set('countries',$countries);
				
		$details = $this->NewBuyer->read(null,$member_id);
		
		if($details['NewBuyer']['pass_update'] == 0){
			$title = "Create Profile";
		}else{
			$title = "Edit Profile";
		}
		$this->set('title',$title);
		if(!empty($this->request->data) && $this->validation())
		{
			 //~ echo "<pre>";
			 //~ print_r($this->request->data); die;
			 
			//print_r($this->request->data); die;
			/*if($details['NewBuyer']['pass_update'] == 0){
				$this->request->data['NewBuyer']['pass_update'] = 1;
			}*/
			if($details['NewBuyer']['process_step'] == 1){
				$this->request->data['NewBuyer']['process_step'] = 2;
			}
			$this->request->data['NewBuyer']['continent'] = $this->__get_continent($this->request->data['NewBuyer']['country']);
			$this->request->data['NewBuyer']['updated_at']=date('Y-m-d H:i:s');
			$this->request->data['NewBuyer']['id'] = $member_id;
			
			//echo "<pre>"; print_r($this->request->data); die;
			
			$this->NewBuyer->create();
			$this->NewBuyer->save($this->request->data,array('validate'=>false));
			
			$NewBuyer=$this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.id'=>$member_id)));
			$this->MemberAuth->updateMemberSession($NewBuyer['NewBuyer']);
			
			$this->Session->setFlash(__('Profile has been updated successfully. Update other informations here.'),'default',array(),'success');
			/*if($details['NewBuyer']['process_step'] == 1){
				$this->redirect(array('action'=>'success',2));
			}
			$this->redirect(array('action'=>'settings'));*/
			
			$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'NewBuyers','action'=>'settings'));
			
		}else{
			$this->request->data = $details;
		}
		$this->set('id',$member_id);
		$this->set('process_step',$details['NewBuyer']['process_step']);
		$this->set('pass_update',$details['NewBuyer']['pass_update']);
	}
	
	public function profile(){
		//print_r($this->request->data());exit;
		$member_id = self::_check_member_login();
		$page = $this->__load_page(50);
		$this->set('page', $page);
		$image_path = Configure::read('Path.NewBuyerLogo');
		$buyer_info = $this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.id'=>$member_id)));
		$process_step = $this->__get_process_step($member_id);	
		$this->set('process_step',$process_step);
		$this->set('buyer_info',$buyer_info);
		$this->set('image_path',$image_path);
	}
	
	public function forgot(){
		
		$page = $this->__load_page(53);
		$this->set('page', $page);
		
		if(!empty($this->request->data) && $this->validation1()){
			$name ='';
			$username = '';
			$email = '';
			$user = array();
			$url = '';
			$user_type = '';
			$urlValue=md5($this->_randomString());		
			$user = $this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.email_id'=>$this->request->data['NewBuyer']['email_id'])));
			if(!empty($user)){
				$name = ucfirst($user['NewBuyer']['first_name']).' '.ucfirst($user['NewBuyer']['last_name']);
				$email = $user['NewBuyer']['email_id'];
				unset($user['NewBuyer']['password']);
				$user['NewBuyer']['passwordurl'] =  $urlValue;
				$this->NewBuyer->create();
				$this->NewBuyer->save($user,array('validate'=>false));
			}		
			if(!empty($user)){								
				$url=Router::url(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'resetpassword',$user['NewBuyer']['passwordurl']),true);				
				$options = array();
				$options['replacement'] = array('{NAME}'=>$name,'{USERNAME}'=>$email,'{URL}'=>$url);
				$options['to'] = array($email); 
				$this->MyMail->SendMail(17,$options);
				//$this->Session->setFlash('Reset password link will be sent to '.$email.'. Please follow the instructions to reset your password');
				$this->Session->setFlash(__('Reset password link will be sent to '.$email.'. Please follow the instructions to reset your password.'),'default',array(),'success');
				$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'login'));
			}else{
				//$this->Session->setFlash('We are sorry for the inconvenience, the email address you entered is not registered with us.','default','','error');
				$this->Session->setFlash(__('We are sorry for the inconvenience, the email address you entered is not registered with us.'),'default',array(),'error');
			}
		}
	}
	
	function validation1(){
		if(!empty($this->request->data['NewBuyer']['form'])){
		$this->NewBuyer->setValidation($this->request->data['NewBuyer']['form']);
		}
		$this->NewBuyer->set($this->request->data);
		return $this->NewBuyer->validates();
	}
	
	
	function questions(){
		
		$member_id = self::_check_member_login();
		$page = $this->__load_page(58);
		$this->set('page', $page);		
		$process_step = $this->__get_process_step($member_id);	
		$this->set('process_step',$process_step);
		$conditions = array();
		if(!empty($member_id)){
			$this->NewBuyerQuestion->bindModel(array('belongsTo' => array('Question' => array('foreignKey' => false,'conditions' => array('NewBuyerQuestion.question_id = Question.id')))));
			$questions = $this->NewBuyerQuestion->find('all',array('conditions'=>array('NewBuyerQuestion.new_buyer_id'=>$member_id)));
			
			//pr($questions); die;
			$cat_list= array();
			foreach($questions as $question){
				
				if(!(in_array($question['Question']['category_id'], $cat_list))){
					$cat_list[] = $question['Question']['category_id'];
				}
			}
			$categories = $this->QuestionCategorie->find('all',array('order'=>array('QuestionCategorie.name'=>'ASC'),'conditions'=>array('QuestionCategorie.id'=>$cat_list)));
			//$categories = $this->QuestionCategorie->find('all',array('conditions'=>array('QuestionCategorie.id'=>$cat_list)));
		}
		
		$this->set('questions',$questions);
		$this->set('categories',$categories);
		//print_r($categories); die;
	}
	
	
	
	function add_question($returnType = 'json'){
		$this->autoRender = false;
		$member_id = self::_check_member_login();
		$process_step = $this->__get_process_step($member_id);
		if(!empty($this->request->data)){
			
			//echo "<pre>"; print_r($this->request->data); die;
			
		
		/*	foreach($this->request->data['sub_sec'] as $tier_val)
			{
				if(!empty($tier_val['tier']))
				{
				
					$my_str = implode(',',$tier_val['tier']);
					
					 echo $tier_val['ques'];// question id
					 echo $my_str; //selcted tier with , seperated value
					 echo $member_id;	// mew_buyer id	 
					 
					}
				
				}
			
			 die;*/
			
			//echo "<pre>"; print_r($this->request->data); die;
			
			
			$question_count = $this->NewBuyerQuestion->find('count', array('conditions' => array('NewBuyerQuestion.new_buyer_id'=>$member_id)));
			$select_count = count($this->request->data['ques']);
			//echo $select_count; die;
			if($question_count != 15){
				if(!(($question_count+$select_count) > 15)){
					if($this->request->data['ques']){
						
		
						
				//else{	
						
								$profile_questions = $this->NewBuyerQuestion->find('list',array('fields'=>array('id','question_id'),'conditions'=>array('NewBuyerQuestion.new_buyer_id'=>$member_id)));
		$result['error'] = 0;
		if(isset($profile_questions)){
		 $get_data = $this->request->data['ques'];
		 $set_data = $profile_questions;
		 //print_r
		 //$check_value = array();
		 $comman_aary=array_intersect($get_data,$set_data);
		 //$result;
			  if(empty($comman_aary)){
				  foreach($this->request->data['ques'] as $question){
							$user['NewBuyerQuestion']['question_id'] =  $question;
							$user['NewBuyerQuestion']['new_buyer_id'] = $member_id;
				            $this->NewBuyerQuestion->create();
							$this->NewBuyerQuestion->save($user,array('validate'=>false));
						}
				}	
						}
						
						
						if($process_step !=4){
							$buyer_data['NewBuyer']['id'] = $member_id;
							$buyer_data['NewBuyer']['process_step'] = 4;
							$this->NewBuyer->create();
							$this->NewBuyer->save($buyer_data,array('validate'=>false));
							if($process_step < 4){	
							    $result['step'] = 4;
					     	}
					       else
					       {
							   $result['step'] = 5;
							   }	
						}
						$result['error'] = 0;
					//}
				}
					if($this->request->data['sub_sec']){
						
						foreach($this->request->data['sub_sec'] as $tier_val)
			                {
				               if(!empty($tier_val['tier']))
				                 {
									$arr = array_map('trim',$tier_val['tier']);  
				                	$my_str = implode(',', $arr);
				                	$q_id = $tier_val['ques'];
				                	
				                	$this->NewBuyerQuestion->updateAll(
                                    array('NewBuyerQuestion.question_tier' =>"'$my_str'"),
                                    array('AND' => array('NewBuyerQuestion.new_buyer_id'=>$member_id,'NewBuyerQuestion.question_id' => $q_id))
                                     );
					               }
				            }
				          $result['error'] = 0; 
					}
					
				}else{
						$result['error'] = 1;
						$result['error_message'] = "You can only add ".(15 - $question_count)." more questions to your profile. If you want to add more please remove questions from your list.";
				}
			}else{
				
				$result['error'] = 1;
				$result['error_message'] = "You have added maximum number of questions, if you want to add more please delete questions from your list";
			}
			
			//$result['count'] = $question_count;
			echo json_encode($result);
			return;
		}	
	}
	
	public function index(){
		$page = $this->__load_page(57);
		$this->set('page', $page);
		$member_id = self::_check_member_login();
		
		$process_step = $this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.status'=>1,'NewBuyer.id'=>$member_id),'fields'=>array('NewBuyer.process_step')));
		
		$cat_list = $this->QuestionCategorie->find('all',array('fields'=>array('id','name'),'order'=>array('QuestionCategorie.name'=>'ASC'),'conditions'=>array('QuestionCategorie.status' =>1)));
		
		$questions = $this->Question->find('all',array('conditions'=>array('Question.status'=>1,'Question.is_descriptive'=>0)));
		
		$profile_questions = $this->NewBuyerQuestion->find('list',array('fields'=>array('id','question_id'),'conditions'=>array('NewBuyerQuestion.new_buyer_id'=>$member_id)));
		//print_r($questions); die;
		
		$categories = array();
		foreach($questions as $question){
			//print_r($question); die;
			if(!in_array($question['Question']['id'],$profile_questions)){
				if(!in_array($question['Question']['category_id'],$categories)){
					$categories[] = $question['Question']['category_id'];
				}
			}
		}
		//print_r($question1); die;
		$this->set('cat_list',$cat_list);
		$this->set('questions',$questions);
		$this->set('categories',$categories);
		$this->set('profile_questions',$profile_questions);
		$this->set('process_step',$process_step['NewBuyer']['process_step']);
	}
	
	public function question_remove(){
		$member_id = self::_check_member_login();
		$this->autoRender = false;
		
		$action = $this->request->data['NewBuyer']['action'];
		$data = $this->request->data['Question']['id'];
		//print_r($data); die;
		$ans="0";
		foreach($data as $value){

			if($value!='0'){
				if($action=='Delete'){
					
					//$this->NewBuyerQuestion->delete($value);
					$this->NewBuyerQuestion->deleteAll(['NewBuyerQuestion.new_buyer_id'=>$member_id,'NewBuyerQuestion.question_id'=>$value]);
					$ans="2";
				}
			}
		}
		if($ans=="2"){
			$this->Session->setFlash(__('Question(s) deleted  successfully.'),'default',array(),'success');
		}else{
			
			$this->Session->setFlash(__('Please select question(s).'),'default',array(),'error');
			}
	
		/*if($id){
			$this->NewBuyerQuestion->deleteAll(['NewBuyerQuestion.new_buyer_id'=>$member_id,'NewBuyerQuestion.question_id'=>$id]);
			$this->Session->setFlash(__('Question has been removed successfully.'),'default',array(),'success');
		}else{
			$this->Session->setFlash(__('Question does not exists.'),'default',array(),'error');
		}*/
		$this->redirect(array('action'=>'questions'));
	}
	
	public function question_update(){
		$this->autoRender = false;
		$member_id = self::_check_member_login();
		
		$profile_questions = $this->NewBuyerQuestion->find('all',array('conditions'=>array('NewBuyerQuestion.new_buyer_id'=>$member_id))); 
		
		$ans="0";
		if(!empty($this->request->data)){
			    
	     foreach($this->request->data['sub_sec'] as $tier_val) {
			  if(!empty($tier_val['Item'])){
					 $arr = array_map('trim',$tier_val['Item']);  
				     $my_str = implode(',', $arr);
				     $q_id = $tier_val['ques_id'];
				     $this->NewBuyerQuestion->updateAll(
                        array('NewBuyerQuestion.question_tier' =>"'$my_str'"),
                        array('AND' => array('NewBuyerQuestion.new_buyer_id'=>$member_id,'NewBuyerQuestion.question_id' => $q_id))
                                     );
					               }
					             $ans="1";  
				      }  
	       
	       
	       
	         $this->Session->setFlash(__('Question(s) updated successfully.'),'default',array(),'success');
			}
			
			if($ans=="1")
			{
				return 0;
				}
				else{
				return 1;
		        }		
		}
	
	public function supplier_list(){
		$member_id = self::_check_member_login();
		$page = $this->__load_page(59);
		$this->set('page', $page);
		$process_step = $this->__get_process_step($member_id);	
		$this->set('process_step',$process_step);
		$this->loadModel('SupplierManager.SupplierBuyer');		
		$suppliers = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.buyer_id'=>$member_id)));
		$this->set('suppliers',$suppliers);
		//$this->FeedbackRequest->recursive = -1;
		//$this->FeedbackRequest->bindModel(array('belongsTo' => array('Payment')));
		//$requests = $this->FeedbackRequest->find('all',array('fields'=>array('Supplier.id','Supplier.email_id','*','Payment.id','Payment.txn_id')));	
		$this->FeedbackRequest->bindModel(array('hasMany' => array('Payment'=>array('order' => 'Payment.id DESC'))));
		$requests = $this->FeedbackRequest->find('all',array('order' => 'FeedbackRequest.id DESC'));
		//pr($requests); die;
		$result = array();
		$all_requests = array();
		foreach($suppliers as $supplier){
			
			foreach($requests as $request){
				
				if($request['FeedbackRequest']['supplier_id'] == $supplier['SupplierBuyer']['supplier_id']){
					$nb_list = json_decode($request['FeedbackRequest']['new_buyers'],true);
					//print_r($nb_list); die;
					if(in_array($member_id,$nb_list)){
						$all_requests[] = $request;
					}
				}
				
			}
			$req_id = array();
			foreach($all_requests as $request){
				$req_id[] = $request['FeedbackRequest']['id'];
			}
			$status = $this->__get_status($req_id);
			$this->set('status',$status);
		}
	//	echo '<pre>';print_r($all_requests); die;
		
		//pr($all_requests); die;
		$this->set('all_requests',$all_requests);
	}
	
	
	private function __get_status($requests = array()){
		
		if(!empty($requests)){
			
			$EbLogin = $this->EbLoginDetail->find('all',array('conditions'=>array('EbLoginDetail.request_id'=>$requests),'fields'=>array('EbLoginDetail.request_id','EbLoginDetail.existing_buyer_id','EbLoginDetail.is_link_expire','EbLoginDetail.eb_status','EbLoginDetail.resend_date')));
			
			$data = array();
			 foreach($requests as $key=>$value){
				 $f_status= array();
				 $status = $resend_date =  '';
				 
				 foreach($EbLogin as $eb_value){
					if($eb_value['EbLoginDetail']['request_id'] == $value){
						
						$current_status = $eb_value['EbLoginDetail']['eb_status'];
						
						if($eb_value['EbLoginDetail']['resend_date'] != ''){
							$resend_date = $eb_value['EbLoginDetail']['resend_date'];
						}
						
						if($current_status == 1 && $status < 1){
							$status = 1;
						}elseif($current_status == 2){
							$status = 2;
						}elseif($current_status == 3){
							$status = 3;
						}elseif($current_status == 4 && $status != 2 && $status != 3 && $status <=4){
							$status = 4;
						}elseif($current_status == 5 && $status != 2 && $status != 3 && $status <=5){
							$status = 5;
						}elseif($current_status == 6 && ($status == '' || $status ==6 )){
							$status = 6;
						}
						$f_status[$eb_value['EbLoginDetail']['existing_buyer_id']] = $status;
					} 
				}
				
				$data[$value] = array('status' => $f_status, 'resend_date'=>$resend_date);
			}
			return $data;
		}
	}
	
	function export_list(){
		$member_id = self::_check_member_login();
		$user = $this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.id'=>$member_id),'fields'=>array('NewBuyer.first_name')));
		$this->loadModel('SupplierManager.SupplierBuyer');		
		$suppliers = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.buyer_id'=>$member_id),'fields'=>array('SupplierBuyer.id','SupplierBuyer.reference_num','Supplier.id','Supplier.title','Supplier.first_name','Supplier.middle_name','Supplier.last_name','Supplier.email_id','NewBuyer.id','NewBuyer.org_name')));
		//$this->set('suppliers',$suppliers);
		
		if(empty($suppliers)){
			//$this->Session->setFlash(__('No data found to export!', true),'default','','error');
			$this->Session->setFlash(__('No data found to export!'),'default',array(),'error');
			$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'supplier_list'));
		}
		//$this->set('suppliers',$suppliers);
		$this->layout = null;
		$this->autoLayout = false;
		Configure::write('debug', '2');
		
		$currntdate=date('d/m/Y-H:i:s'); 
		if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') OR stristr($_SERVER['HTTP_USER_AGENT'],     'iphone') OR stristr($_SERVER['HTTP_USER_AGENT'], 'ipod')) 
{ 
  $fileName=$user['NewBuyer']['first_name'].'-supplier_list-'.$currntdate.'.xlsx';
} 
else{
		$fileName=$user['NewBuyer']['first_name'].'-supplier_list-'.$currntdate.'.xlsx';
	}
		$headerRow = array("Reference Number","Supplier Name","Supplier Email");
		
		$data='';
		$i = 0;
		
		foreach ($suppliers as $supplier)
		{
			$newd = array(
				$supplier['SupplierBuyer']['reference_num'],
				ucfirst($supplier['Supplier']['first_name'])." ".ucfirst($supplier['Supplier']['middle_name'])." ".ucfirst($supplier['Supplier']['last_name']),
				$supplier['Supplier']['email_id']
			);
		
			$data[]=$newd;
		}
		//	echo "ggg"; die;
		$this->ExportXls->export($fileName, $headerRow, $data);
	
	}
	
/*	public function export_pdf(){
		$member_id = self::_check_member_login();
		$this->loadModel('SupplierManager.SupplierBuyer');		
		//$suppliers = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.buyer_id'=>$member_id),'fields'=>array('SupplierBuyer.id','SupplierBuyer.reference_num','Supplier.id','Supplier.title','Supplier.first_name','Supplier.middle_name','Supplier.last_name','Supplier.email_id','NewBuyer.id','NewBuyer.first_name','NewBuyer.org_name')));
		
		$suppliers = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.buyer_id'=>$member_id)));
		//print_r($suppliers); die;
		if(empty($suppliers)){
			//$this->Session->setFlash(__('No data found to export!', true),'default','','error');
			$this->Session->setFlash(__('No data found to export!'),'default',array(),'error');
			$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'supplier_list'));
		}
		
		$nb_info = $this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.id'=>$member_id),'fields'=>array('NewBuyer.first_name','NewBuyer.last_name')));
		$this->set('nb_info',$nb_info);
		$this->set('suppliers',$suppliers);
		$currntdate=date('d-m-Y'); 
		$this->set('currntdate',$currntdate);
		//$this->layout = null;
		$this->autoLayout = false;
		Configure::write('debug', '2');
		$this->layout = '/pdf/default';
		
	}
	*/
	
	
	public function export_pdf(){
		
		$member_id = self::_check_member_login();
		$this->loadModel('SupplierManager.SupplierBuyer');	
		$suppliers = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.buyer_id'=>$member_id)));
		foreach($suppliers as $supplier){
			$country_arr = $this->Country->find('first',array('conditions'=>array('Country.country_code_char2'=>$supplier['Supplier']['country'])));
			$supplier['Country'] = $country_arr['Country'];
			
			$sup[]=$supplier;
		}
		
		$supplier_arr[] = $supplier;
	//	echo "<pre>";print_r($sup); die;
		if(empty($suppliers)){
			//$this->Session->setFlash(__('No data found to export!', true),'default','','error');
			$this->Session->setFlash(__('No data found to export!'),'default',array(),'error');
			$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'supplier_list'));
		}
		
		$nb_info = $this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.id'=>$member_id),'fields'=>array('NewBuyer.first_name','NewBuyer.last_name')));
		$this->set('nb_info',$nb_info);
		$this->set('suppliers',$sup);
		$currntdate=date('d-m-Y'); 
		$this->set('currntdate',$currntdate);
		
		$this->autoLayout = false;
		Configure::write('debug', '2');
		$this->layout = '/pdf/default';
		
	}	
	
	function settings(){
		$member_id = self::_check_member_login();
		$page = $this->__load_page(60);
		$this->set('page', $page);
		if(!empty($this->request->data) && $this->validation()){
			//print_r($this->request->data); die;
			$existing_image='';
			$logo_image = $this->NewBuyer->find('first',array('fields'=>array('NewBuyer.logo','NewBuyer.process_step'),'conditions'=>array('NewBuyer.id'=>$member_id)));
			$existing_image = $logo_image['NewBuyer']['logo'];
				
			$_options = array(
			'destination'=>Configure::read('Path.NewBuyerLogo'),
			'image'=>$this->request->data['NewBuyer']['logo']
			);
			//print_r($_options); die;
			if($this->request->data['NewBuyer']['logo']['error'] > 0){
				$this->request->data['NewBuyer']['logo'] = $existing_image;
				
			}else{
				
				if($this->request->data['NewBuyer']['logo']['error'] < 1){
					//echo "megha"; die;
				$this->request->data['NewBuyer']['logo'] = $this->System->Image->upload($_options);
				
				//print_r($megha); die;
				}else{
					$this->request->data['NewBuyer']['logo'] = "";
				}
			}
			if($logo_image['NewBuyer']['process_step'] == 2){
				$this->request->data['NewBuyer']['process_step'] =3;
			}
			$this->request->data['NewBuyer']['id'] =$member_id;
			$this->NewBuyer->create();
			$this->NewBuyer->save($this->request->data,array('validate'=>false));
			
			/*$this->Session->setFlash(__('Your Settings updated successfully. Add questions in your profile here.'),'default',array(),'success');*/
	    	/*	if($logo_image['NewBuyer']['process_step'] == 2){
				$this->redirect(array('action'=>'success',3));
			}
			$this->redirect(array('action'=>'questions'));*/
			
			if($logo_image['NewBuyer']['process_step'] < 4){
			$this->Session->setFlash(__('Your Settings updated successfully. Add questions in your profile here.'),'default',array(),'success');	
				
			$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'NewBuyers','action'=>'index'));
		    }else{
				$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'NewBuyers','action'=>'dashboard'));	
			}
			
		}else{
			if(!empty($member_id)){
				$this->request->data = $this->NewBuyer->read(null,$member_id);
				$existing_logo = $this->request->data['NewBuyer']['logo'];
				$this->set('existing_logo',$existing_logo);
				$this->set('process_step',$this->request->data['NewBuyer']['process_step']);
			}else{
				$this->request->data = array();
			}
		}
	}
	
	public function request(){
		
		$page = $this->__load_page(63);
		$this->set('page', $page);
		
		if(!empty($this->request->data) && $this->validation()){
			
			
			$this->request->data['NewBuyer']['created_at']=date('Y-m-d H:i:s');
			$this->request->data['NewBuyer']['pass_update'] = 0;
			$this->request->data['NewBuyer']['status'] = 0;
			$this->request->data['NewBuyer']['request_status'] = 1;
			
			$this->NewBuyer->create();
			$this->NewBuyer->save($this->request->data,array('validate'=>false));
			Cache::delete('new_buyers');
			$id = $this->NewBuyer->id;
		
			$options = array();
			$options['replacement'] = array('{ORG_NAME}'=>$this->request->data['NewBuyer']['org_name'],'{EMAIL}'=>$this->request->data['NewBuyer']['email_id']);
			$options['to'] = $this->System->get_setting('site','site_contact_email'); 
		    $options['from'] = $this->System->get_setting('site','site_contact_noreply'); 				
			$this->MyMail->SendMail(19,$options);			
			
			$options = array();
			$options['replacement'] = array('{NAME}'=>$this->request->data['NewBuyer']['email_id']);
			$options['to'] = array($this->request->data['NewBuyer']['email_id']); 		
			$this->MyMail->SendMail(20,$options);
			
			//$this->Session->setFlash(__('Your request has been submitted successfully. Please check your mailbox for further details.'),'default',array(),'success');
			$this->redirect(array('action'=>'request_success'));
		}
		
	}
	
	function question_list(){
		
		//$this->autoRender = false;
		$member_id = self::_check_member_login();
		if(!empty($this->request->data)){
				
		  //echo "<pre>"; print_r($this->request->data);  die;
		  
		 // pr($this->request->data); die;
		  
		  $tier_arr = array();  
		   
		  foreach($this->request->data['tier'] as $test){
			  
		          $tier_arr[$test['ques_id']]=$test['Item'];    
	         }
	         
	       //pr($tier_arr); die;
			
			
			$questions = $this->Question->find('all',array('fields'=>array('Question.question','Question.id','Question.category_id'),'conditions'=>array('Question.id'=>$this->request->data['ques'])));
			
			$this->set('questions',$questions);
			$category = array();
			foreach($questions as $question){
				if(!(in_array($question['Question']['category_id'],$category))){
					$category[] = $question['Question']['category_id'];
				}		
			}
			
			$categories = $this->QuestionCategorie->find('all',array('fields'=>array('QuestionCategorie.name','QuestionCategorie.id'),'order'=>array('QuestionCategorie.name'=>'ASC'),'conditions'=>array('QuestionCategorie.id'=>$category)));
			
			$process_step = $this->NewBuyer->find('first',array('fields'=>array('NewBuyer.process_step'),'conditions'=>array('NewBuyer.id'=>$member_id)));
			
			
			$this->set('process_step',$process_step);
			$this->set('categories',$categories);
			$this->set('questions',$questions);
			$this->set('tier_arr',$tier_arr);
			//print_r($questions); die;			
		}
	}
	
	public function login($activate = null){
		
		$page = $this->__load_page(51);
		$this->set('page', $page);
		$this->set('activate', $activate);
		//if($this->Auth->user('id')){
			//$this->redirect($this->Auth->redirect());
		//}
		$memberType = $this->MemberAuth->get_member_type();
		//echo $memberType; die;
		if($this->MemberAuth->is_active_member() && $memberType ==2){
			$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'dashboard'));
		}
		
		if(!empty($this->request->data) && $this->validation1()){
			
			//print_r($this->request->data); die;
		
			if ($this->request->is('post')){
				$pass=Security::hash(Configure::read('Security.salt').$this->request->data['NewBuyer']['password']);
				
				$NewBuyerLogin=$this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.email_id'=>$this->request->data['NewBuyer']['email_id'],'NewBuyer.password'=>$pass,'NewBuyer.status'=>array(0,1))));
				if (!empty($NewBuyerLogin)){				
					//if($this->MemberAuth->is_active_user($NewBuyerLogin)){
					if($NewBuyerLogin['NewBuyer']['status'] == 1){
						$NewBuyerLogin['NewBuyer']['user_type']=2;
						$this->MemberAuth->updateMemberSession($NewBuyerLogin['NewBuyer']);
						$this->MemberAuth->updateMemberType(2); // for New Buyer
						$user_info = $this->MemberAuth->get_user_detail();
						//print_r($user_info);die;
						$status = '';
						if(!empty($user_info)){
							$status = $user_info['status'];
						}
						if($status==3){
							$this->Session->setFlash(__('Your NewBuyer account is closed. Please contact administrator.'),'default',array(),'error');
						}
					//	print_r($status);die;
						$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'dashboard'));
					} else {
						$this->Session->setFlash(__('Your account is deactivated.'),'default',array(),'error');
						$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'login'));
					}
				}else{
					$this->Session->setFlash(__('Invalid username or password, try again'),'default',array(),'error');
					$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'login'));
				}
			}
			
		}
	}
	
	public function step_bar(){
		$page = $this->__load_page(51);
		$this->set('page', $page);
		
	}
	
	public function question_thankyou()
	{
		$member_id = self::_check_member_login();
		
		$process_step = $this->NewBuyer->find('first',array('fields'=>array('NewBuyer.process_step'),'conditions'=>array('NewBuyer.id'=>$member_id)));
		
		if(!empty($member_id)){
		
		$this->set('process_step',$process_step);
		$this->System->set_seo('site_title','Thank you');
	      }
		}
	
	public function success($step=null){
		
		$member_id = self::_check_member_login();
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_seo('site_title','Success');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		
		$process_step = $this->NewBuyer->find('first',array('fields'=>array('NewBuyer.process_step'),'conditions'=>array('NewBuyer.id'=>$member_id)));
		/*if($type == 1){
			$message = $this->Session->flash('success');
		}elseif($type == 2){
			$message = $this->Session->flash('error');
		}*/
		if($step == 1){
			$message = 'Your Password has been changed successfully. Please Enter Your Details.';
		}elseif($step == 2){
			$message = 'Profile has been updated successfully. Update other informations here.';
		}elseif($step == 3){
			$message = 'Your Settings updated successfully. Add questions in your profile here.';
		}elseif($step == 4){
			$message = 'Your request has been submitted successfully.<br>You will receive a username and password in your mailbox.<br>In the mean time you can leave any suggestion.';
		}elseif($step == 5){
			$message = 'Congratulations, You have completed all the required steps.';
		}
		
		
		if($step == 1){
			$url = Router::url(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'edit_profile'));
		}elseif($step == 2){
			$url = Router::url(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'settings'));
		}elseif($step == 3){
			$url = Router::url(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'index'));
		}elseif($step == 4){
			$url = Router::url('/contact-us');
		}elseif($step == 5){
			$url =  Router::url(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'dashboard'));
		}
		
		$this->set('process_step', $process_step['NewBuyer']['process_step']);
		$this->set('message', $message);
		$this->set('url', $url);
	}
	public function request_success(){
		
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_seo('site_title','Success');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		
		$message = '<p class="txt-bold">Your request has been submitted successfully.<p>You will receive a username and password in your mailbox. <br>In the mean time you can leave any suggestion.';
	    $url = Router::url(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'login'), true );
		$backurl = Router::url(array('plugin'=>'content_manager','controller' => 'pages', 'action' => 'home'), true);
		//$url = Router::url('/contact-us');
		
		$this->set('message', $message);
		$this->set('url', $url);
		$this->set('backurl', $backurl);
	}
	
	public function get_phonecode($returnType = 'json')
	{
		$this->autoRender = false;
		
		if(!empty($this->request->data))
		{
			$cont_cod=$this->request->data;
			foreach($cont_cod as $key=>$val){
				$g_code =  $key; 
			}
			
			
	   $code = $this->Country->find('first',array('conditions'=>array('Country.country_code_char2'=>$g_code),'fields'=>array('Country.phonecode')));
	   
	      $new_code = $code['Country']['phonecode'];
	       	  
			}
			
			return $new_code;
		    
		}
	
	
	
}
?>
