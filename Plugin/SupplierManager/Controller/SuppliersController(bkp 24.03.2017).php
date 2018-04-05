<?php
Class SuppliersController extends SupplierManagerAppController{
	public $uses = array('SupplierManager.Supplier','SupplierManager.NewbuyerExist','ContentManager.Page','Country','SupplierManager.SupplierBuyer','NewBuyerManager.NewBuyer','ExistingBuyerManager.ExistingBuyer','SupplierManager.FeedbackResponse','NewBuyerManager.NewBuyerQuestion','ExistingBuyerManager.EbLoginDetail','SupplierManager.FeedbackRequest','SupplierManager.FeedbackTemp','SupplierManager.Payment','QuestionManager.Question');
	public $components=array('Email','RequestHandler','Image','MyMail','ExportXls');
	var $helpers = array('Captcha','Csv');
	public $paginate = array();
	public $id = null;
	public $template=null;
	public $process_step; 
	public function beforeFilter() { 
			parent::beforeFilter();	
			
			/*$loguser = $this->Session->read('Auth.Supplier');
			$active_supplier_id=$loguser['id'];
			if(empty($active_supplier_id)){
				$this->Session->destroy();
				$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'login'));
			}
			$this->set('supplier_id', $active_supplier_id);		*/
			//$this->Auth->allow('login', 'registration','forgot');
			//$this->Auth->deny('profile', 'dashboard','edit_profile','add_new_buyer','eb_list','make_request','card_detail');
		}
		
	private function __get_process_step($member_id){
		$process_step = $this->Supplier->find('first',array('fields'=>array('Supplier.process_step'),'conditions'=>array('Supplier.id'=>$member_id)));
		$step = $process_step['Supplier']['process_step'];
		return $step;
	}
	private function __exist_buyer_response($member_id){
		
		$exist_id = $this->ExistingBuyer->find('list',array('fields'=>array('ExistingBuyer.id'),'conditions'=>array('ExistingBuyer.supplier_id'=>$member_id,'ExistingBuyer.status'=>1)));
		
		$response_id = $this->FeedbackResponse->find('list',array('fields'=>array('FeedbackResponse.id'),'conditions'=>array('FeedbackResponse.existing_buyer_id'=>$exist_id,'FeedbackResponse.response_status'=>2)));
		$exist_value=count($response_id);
		return $exist_value;
	}
	private function __exist_buyer_count($member_id){
		$exist_value = $this->ExistingBuyer->find('list',array('fields'=>array('ExistingBuyer.id'),'conditions'=>array('ExistingBuyer.supplier_id'=>$member_id,'ExistingBuyer.status'=>1)));
		$exist_value=count($exist_value);
		return $exist_value;
	}
	
	
	function admin_index($search=null,$limit=10){
		$this->paginate = array();
		$condition = null;
		if($search=="_blank"){
			$search=null;
		}
		$this->Supplier->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('Supplier.country = Country.country_code_char2')))));
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
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'index',$search,$limit));
		}
		if($search!=null){
			$search = urldecode($search);	
			$condition['OR'][]=array('OR'=>array('Supplier.first_name like'=>'%'.$search.'%','Supplier.last_name like'=>'%'.$search.'%','Supplier.title like'=>'%'.$search.'%'));
		}
		
		
		$suppliers = array();
		$this->paginate['order']=array('Supplier.id'=>'DESC');
		$suppliers= $results=$this->paginate("Supplier", $condition);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/supplier_manager/suppliers'),
			'name'=>'Manage Supplier'
		);
		
		$this->heading =  array("Manage","Supplier");

		$this->set('suppliers',$suppliers);
		$this->set('limit',$limit);
		$this->set('search',$search);
		$this->set('url','/'.$this->params->url);
		
	}
	
	function admin_add($id=null){
		
		$this->breadcrumbs[] = array(
		'url'=>Router::url('/admin/home'),
		'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/supplier_manager/suppliers'),
				'name'=>'Manage Suppliers'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/supplier_manager/suppliers/add/'.$id),
				'name'=>($id==null)?'Add Supplier':'Update Supplier'
		);
		if($id==null){
			$this->heading =  array("Add","Supplier");
		}else{
			$this->heading =  array("Update","Supplier");
		}
		$countries = $this->Country->country_list();
		
		if(!empty($this->request->data) && $this->validation()){
			
			if(!$id){
				$this->request->data['Supplier']['created_at']=date('Y-m-d H:i:s');
				
			}else{
				$this->request->data['Supplier']['updated_at']=date('Y-m-d H:i:s');
			}
			if(empty($this->request->data['Supplier']['id'])){
				if(isset($this->request->data['save']) && $this->request->data['save']=='Save'){
					$this->request->data['Supplier']['status'] = 1;
				}else{
					$this->request->data['Supplier']['status'] = 1;
				}
			}
				//echo "<pre>"; print_r(($this->request->data)); die;
			$this->Supplier->create();
			$this->Supplier->save($this->request->data,array('validate'=>false));
			$id = $this->Supplier->id;
			
			if ($this->request->data['Supplier']['id']) {
				$this->Session->setFlash(__('Record has been updated successfully'));
			} 
			else{
				$this->Session->setFlash(__('Record has been added successfully'));
			}
			$this->redirect(array('action'=>'add',$id,'?'=>array('back'=>$this->request->data['Supplier']['url_back_redirect'])));
		}
		else{
			if(!empty($this->request->data)){
				$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
			}
			
			if($id!=null){
				$this->request->data = $this->Supplier->read(null,$id);
			}else{
				$this->request->data = array();
			}
		}
		
		$referer_url = $this->request->query('back');
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/supplier_manager/suppliers',true) :Controller::referer();
		
		}
		$this->set('referer_url',$referer_url);
		$this->set('supplier_id',$id);
		$this->set('countries',$countries);
	}
	
	function admin_delete($id=null){
		$this->autoRender = false;
		//print_r($this->request->data); die;
		$data=$this->request->data['Supplier']['id'];
		//print_r($data); die;
		$action = $this->request->data['Supplier']['action'];
		$ans="0";
		foreach($data as $value){
			if($value!='0'){
				if($action=='Activate'){
					$supplier['Supplier']['id'] = $value;
					$supplier['Supplier']['status']=1;
					$this->Supplier->create();
					$this->Supplier->save($supplier);
					$ans="1";
				}
				if($action=='Disable'){
					$supplier['Supplier']['id'] = $value;
					$supplier['Supplier']['status']=0;
					$this->Supplier->create();
					$this->Supplier->save($supplier);
					$ans="1";
				}
				//$supplier=$this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$value)));
				//echo '<pre>';print_r($supplier);die;
				if($action=='Delete'){
					$this->FeedbackRequest->bindModel(array('hasMany' => array('FeedbackResponse'=>array(
					'foreignKey'=>'request_id',
					'dependent'=>true))), false);
					$this->ExistingBuyer->bindModel(array('hasMany' => array('EbLoginDetail'=>array(
					'foreignKey'=>'existing_buyer_id',
					'dependent'=>true))), false);
					$this->Supplier->bindModel(array('hasMany' => array(
					'FeedbackRequest'=>array('dependent'=>true),
					'Payment'=>array('dependent'=>true),					
					'ExistingBuyer'=>array('dependent'=>true),					
					)), false);
					$supplier=$this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$value)));
					$this->Supplier->delete($value);
					
					//$this->Supplier->delete_routes($value,'Supplier');
					$ans="2";
				}
			}
		}
		
		if($ans=="1"){
			$this->Session->setFlash(__('Supplier has been '.strtolower($this->data['Supplier']['action']).'ed successfully', true));
		}
		else if($ans=="2"){
			$this->Session->setFlash(__('Supplier has been '.strtolower($this->data['Supplier']['action']).'d successfully', true));
		}else{
			$this->Session->setFlash(__('Please Select any Supplier', true),'default','','error');
		}
		$this->redirect($this->request->data['Supplier']['redirect']);
                 
	}
	
	function validation2(){		
		if(!empty($this->request->data['SupplierBuyer']['form'])){
			if($this->request->data['SupplierBuyer']['form']=="supplier_add_buyer"){
				return true;
			}
			$this->SupplierBuyer->setValidation($this->request->data['SupplierBuyer']['form']);
		}else{
			throw new NotFoundException('404 Error - Supplier not found');
		}
		$this->SupplierBuyer->set($this->request->data);
		return $this->SupplierBuyer->validates();
	}
	
	function validation(){		
		if(!empty($this->request->data['Supplier']['form'])){
			if($this->request->data['Supplier']['form']=="supplier_add" && $this->request->data['Supplier']['status']==2){
				return true;
			}
			$this->Supplier->setValidation($this->request->data['Supplier']['form']);
		}else{
			throw new NotFoundException('404 Error - Supplier not found');
		}
		$this->Supplier->set($this->request->data);
		return $this->Supplier->validates();
	}
	
	function ajax_validation($returnType = 'json'){
		
		$this->autoRender = false;
		if(!empty($this->request->data)){
			if(!empty($this->request->data['Supplier']['form'])){
				$this->Supplier->setValidation($this->request->data['Supplier']['form']);
			}
			$this->Supplier->set($this->request->data);
			$result = array();
			if(($this->request->data['Supplier']['form']=="supplier_add") && $this->request->data['Supplier']['status']==2){
	///	if(!empty($this->request->data['Supplier']['status'])){
				//if(($this->request->data['Supplier']['form']=="supplier_add") && $this->request->data['Supplier']['status']==2){
					$result['error'] = 0;
				}else{
					if($this->Supplier->validates()){
						$result['error'] = 0;
					}else{
						$result['error'] = 1;
						$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
					}
				}
			//}
			
			$errors = array();
			$result['errors'] = $this->Supplier->validationErrors;
			foreach($result['errors'] as $field => $data){
			  $errors['Supplier'.Inflector::camelize($field)] = array_pop($data);
			}
			$result['errors'] = $errors;
			$view = new View();
			
			$result['error_message'] = $view->element('admin/message');
			echo json_encode($result);
			return;
		}
		echo json_encode(array());
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
	       
	      /* foreach($code as $temp)
	        {
	          $new_val = $temp ['phonecode'];
	         }
	         
	       echo  $new_val;
	    
			  die;*/
			  
			  
			}
			
			return $new_code;
		    
		}
	
	
	
	function admin_export(){
		$condition = array();												
		$options['group']=array('Supplier.id');
		$options['order']= array('Supplier.id'=>'DESC');
		$supplierInfos = $this->Supplier->find('all',$options);
		
		if(empty($supplierInfos)){
			$this->Session->setFlash(__('No data found to export!', true),'default','','error');
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'index'));
		}
		$this->set('supplierInfos', $supplierInfos);
		$this->layout = null;
		$this->autoLayout = false;
		Configure::write('debug','2');
		
		$currntdate=date('d/m/Y-H:i:s');  
		$fileName='supplier_list-'.$currntdate.'.xls';
		
		$headerRow = array("S.No.","First Name","Middle Name","Last Name","DOB","Contact No.","Email","Address","City","State/Province","Zip/Postcode","Country","Company Name","Industry","Service Category","Experience","Recieve Info","Account Status");
		
		$data='';
		$i = 0;
		
		foreach ($supplierInfos as $supplier)
		{
			 $i++;
			 if($supplier['Supplier']['status'] == 1){ 
						$status =  'Active'; 
			}elseif($supplier['Supplier']['status'] == 1){ 
				$status = 'Blocked'; 
			}else{
				$status = 'Inactive';
			}
			if($supplier['Supplier']['receive_info'] == 1){ 
				$info =  'Yes'; 
			}else{ 
				$info = 'No'; 
			}

			if($supplier['Supplier']['dob']){
				$dob  = date("m/d/Y", strtotime($supplier['Supplier']['dob']));
			}else{
				$dob = 'N/A';
			}
			if($supplier['Supplier']['country_code'] && $supplier['Supplier']['area_code'] && $supplier['Supplier']['contact_number']){
				$contact_no = $supplier['Supplier']['country_code'].' '.$supplier['Supplier']['area_code'].' '.$supplier['Supplier']['contact_number'];
			}else{
				$contact_no = 'N/A';
			}
			if($supplier['Supplier']['company_name']){
				
				$company = $supplier['Supplier']['company_name'];
			}else{
				$company = 'N/A';
			}
			if($supplier['Supplier']['industry']){
				
				$industry = $supplier['Supplier']['industry'];
			}else{
				$industry = 'N/A';
			}

			if($supplier['Supplier']['service_cat']){
				
				$service_cat = $supplier['Supplier']['service_cat'];
			}else{
				$service_cat = 'N/A';
			}
			if($supplier['Supplier']['experience']){
				
				$experience = $supplier['Supplier']['experience'].' years';
			}else{
				$experience = 'N/A';
			}
			$address = $supplier['Supplier']['address1'].' '.$supplier['Supplier']['address2'];
			//$action = ($supplierInfo['Supplier']['receive_info']==1) ? 'Yes' : 'No';

			$newd=array(
				$i,
				ucfirst($supplier['Supplier']['first_name']),
				ucfirst($supplier['Supplier']['middle_name']),
				ucfirst($supplier['Supplier']['last_name']),
				$dob,
				$contact_no,
				$supplier['Supplier']['email_id'],
				$address,
				$supplier['Supplier']['city'],
				$supplier['Supplier']['state'],
				$supplier['Supplier']['zipcode'],
				$supplier['Country']['country_name'],
				$company,
				$industry,
				$service_cat,
				$experience,
				$info,
				$status
			);
			//$newd=array($i,$name,$question,$action,$st,$status);
			$data[]=$newd;
		}
		$this->ExportXls->export($fileName, $headerRow, $data);	
	}
	
	public function admin_create_pdf(){
      
		$condition = array();			
		$options['order']= array('Supplier.id'=>'ASC');
		$suppliers = $this->Supplier->find('all',$options);
		if(empty($suppliers)){
			$this->Session->setFlash(__('No data found to export!', true),'default','','error');
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'index'));
		}
		
		$this->set('suppliers', $suppliers);
		//echo "<pre>"; print_r($suppliers); die;
		$this->layout = null;
		$this->autoLayout = false;
		Configure::write('debug','2');
		
		$currntdate=date('d-m-Y'); 
		$this->set('currntdate',$currntdate);
		$this->layout = '/pdf/default';
	
	}
	
	public function login($activate = null){
		
		$this->set('activate', $activate);
		$page = $this->__load_page(48);
		$this->set('page', $page);
		//if($this->Auth->user('id')){
			//$this->redirect($this->Auth->redirect());
		//}
		$memberType = $this->MemberAuth->get_member_type();
		if($this->MemberAuth->is_active_member() && $memberType ==1){
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'dashboard'));
		}
		
		if(!empty($this->request->data) && $this->validation()){
		
			if ($this->request->is('post')){
				$pass=Security::hash(Configure::read('Security.salt').$this->request->data['Supplier']['password']);
				
				$SupplierLogin=$this->Supplier->find('first',array('conditions'=>array('Supplier.email_id'=>$this->request->data['Supplier']['email_id'],'Supplier.password'=>$pass,'Supplier.status'=>array(0,1))));
				
				if (!empty($SupplierLogin)){
					
					 
					if($SupplierLogin['Supplier']['status'] == 1){
						$SupplierLogin['Supplier']['user_type']=1;
						$this->MemberAuth->updateMemberSession($SupplierLogin['Supplier']);
						$this->MemberAuth->updateMemberType(1); // for Supplier
						$user_info = $this->MemberAuth->get_user_detail();
						$status = '';
						if(!empty($user_info)){
							$status = $user_info['status'];
						}
						if($status==3){
							$this->Session->setFlash(__('Your Supplier account is closed. Please contact administrator.'),'default',array(),'error');
							
						}
					//	print_r($status);die;
					
						if($SupplierLogin['Supplier']['process_step'] == 5){
							$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'dashboard'));
						}elseif($SupplierLogin['Supplier']['process_step'] == ''){
							$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'edit_profile'));
						}elseif($SupplierLogin['Supplier']['process_step'] == 1){
							$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'add_new_buyer'));
						}elseif($SupplierLogin['Supplier']['process_step'] == 2){
							$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'add_new_buyer'));
						}elseif($SupplierLogin['Supplier']['process_step'] == 3){
							$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'assign_existing_buyer'));
						}elseif($SupplierLogin['Supplier']['process_step'] == 4){
							$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'card_detail'));
						}
					} else {
						$this->Session->setFlash(__('Your account is deactivated. Please contact administrator.'),'default',array(),'error');
						$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'login'));
					}
				}else{
					$this->Session->setFlash(__('Invalid username or password, try again'),'default',array(),'error');
					$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'login'));
				}
			}			
		}
	}
	
	public function enterpass($id=null){	
		$this->set('id', $id);
	}
	public function enterresp($id=null){
		$this->autoRender = false;		
		$result=array();
		$user = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$id)));
		//print_r($this->request->data);die;
		if(!empty($this->request->data['Supplier']['password'])){
			$this->request->data['Supplier']['password']=Security::hash(Configure::read('Security.salt').$this->request->data['Supplier']['password']); 			
			if(!empty($user)){
				if($user['Supplier']['password']===$this->request->data['Supplier']['password'])
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
	
	public function registration(){		
		
		$page = $this->__load_page(49);
		$this->set('page', $page);
		$countries = $this->Country->country_list();
		$this->set('countries',$countries);
		
		if(!empty($this->request->data) && $this->validation())
		{
			$realpassword = $this->request->data['Supplier']['password'];
			$this->request->data['Supplier']['password'] = Security::hash(Configure::read('Security.salt').$this->request->data['Supplier']['password']);
			$this->request->data['Supplier']['created_at']=date('Y-m-d H:i:s');
			$this->request->data['Member']['status']=0;
			$this->request->data['Supplier']['status']=0;
			$this->request->data['Supplier']['passwordurl']  = Security::hash(Configure::read('Security.salt').$this->RandomString());
		//	echo "<pre>"; print_r($this->request->data); die;
			$this->Supplier->create();
			if($this->Supplier->save($this->request->data,array('validate'=>false))){
				$this->__registration_mail_send($this->request->data,$realpassword);
				
				$this->request->data['Supplier']['password'] = $realpassword;//To Login member with Un-encrypted password
				$supplier  = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$this->MemberAuth->id)));
				
				
				//$this->Session->setFlash(__('Your account has been created successfully. Please check your mail inbox to activate your account.'),'default',array(),'success');
				$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'registration_success'));
				//$this->MemberAuth->updateMemberSession($supplier['Supplier']);
				//$this->MemberAuth->login();
			}else{
				$this->request->data['Supplier']['password'] = $realpassword;
			}
		}
	}
	
	public function registration_success(){
		
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_seo('site_title','Success');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		
		$message = 'Your account has been created successfully.<br>Please check your mail inbox to activate your account.<br>Also you can contact us for any query or suggestion.';
	
		$url = Router::url('/contact-us');
		
		$this->set('message', $message);
		$this->set('url', $url);
	}
	
	public function active_account($str=null)
	{	
		$this->autoRender = false;		
		$supplier = $this->Supplier->find('first',array('conditions'=>array('Supplier.passwordurl'=>$str)));
		if(!empty($supplier)){
			$this->Supplier->id = $supplier['Supplier']['id'];
			$this->Supplier->saveField('status', 1);
			$this->Session->setFlash(__('Your account has been activated successfully.'),'default',array(),'success');
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'login',1));
		}else {
			
			$this->Session->setFlash(__('Invalid link, try again.'),'default',array(),'error');
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'login'));
			
		}
		
	}
	
	public function logout() {
		$this->MemberAuth->removeMemberSession();
		$this->Session->delete('Request');
		//$this->Session->setFlash(__('You have logged out successfully.'));
		$this->Session->setFlash(__('You have logged out successfully.'),'default',array(),'success');
		$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'login'));
	}
	
	public function profile(){		
		$member_id = self::_check_member_login();
		$process_step = $this->__get_process_step($member_id);
		$page = $this->Page->find('first',array('conditions'=>array('Page.id'=>50,'Page.status'=>1)));
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
		$page['Page']['name'] = "Edit Profile";
		
		if(!empty($member_id)){		
			$active_supplier = $this->Session->read('supplier_email');
			$countries = $this->Country->country_list();
			$this->set('countries',$countries);
			
			$this->Supplier->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('Supplier.country = Country.country_code_char2')))));
			$supplier_info = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$member_id)));	
			//print_r($supplier_info);die;		
			$this->set('supplier_info',$supplier_info);
		}
		
		$this->set('id', $page['Page']['id']);
		$this->set('page', $page);
		$this->set('process_step', $process_step);
	}
	
	
	public function edit_profile(){
		$member_id = self::_check_member_login();		
		$page = $this->__load_page(50);
		$this->set('page', $page);
		$countries = $this->Country->country_list();
		$this->set('countries',$countries);
		$step = '';
		$data = $this->Supplier->read(null,$member_id);
		if(!empty($this->request->data['Supplier']['form']))
		{
				
			$this->request->data['Supplier']['updated_at']=date('Y-m-d H:i:s');
			
			if(!empty($this->request->data['Supplier']['company_name'])){
					$this->request->data['Supplier']['company_name']=strtoupper($this->request->data['Supplier']['company_name']);
				}	
			//$this->request->data['Supplier']['dob']=date('YYYY-MM-DD',strtotime($this->request->data['Supplier']['dob']));
			
			if(empty($this->request->data['Supplier']['process_step']) || $this->request->data['Supplier']['process_step'] < 1){
				$this->request->data['Supplier']['process_step']=1;
			}
		//print_r($this->request->data);die;
			$this->Supplier->create();
			$this->Supplier->save($this->request->data,array('validate'=>false));			
						
			//$this->redirect(array('action'=>'profile'));			
				
			
				$this->Session->setFlash(__('Profile has been updated successfully.'),'default',array(),'success');
				 if($data['Supplier']['process_step'] < 5 ){
				$this->redirect(array('action'=>'add_new_buyer'));
					} else {
					$this->redirect(array('action'=>'dashboard'));	
						
					}	
		
		}else{
			//$data = $this->Supplier->read(null,$member_id);
			//$data['Supplier']['dob']=date('m/d/Y',strtotime($data['Supplier']['dob']));
			$this->request->data = $data;
			$step = $data['Supplier']['process_step'];
		}
		
		$this->set('id',$member_id);
		$this->set('process_step',$step);
	}
	
	function get_country(){
		$member_id = self::_check_member_login();
		//$ud = $this->Session->read('Auth');
		$active_supplier_id = $member_id;
		$this->autoRender = false;
		$contry_arr = array();		
		if(isset($_POST["continent"])){
			$cont=$this->Country->country_list2($_POST["continent"]);
			$country = $_POST["continent"];				 
			if($country !== 'Select'){
				$data="<select><option value=''>Select Country</option>";
				foreach($cont as $key=>$value){
					$data.="<option value=".$key.">". $value . "</option>";
					$contry_arr[]=$key;
				}
				
				$data.="</select>";
			}
			
			$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));
	
			$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id), 'order'=>array('SupplierBuyer.id'=>'DESC')));	
			
			foreach($buyer_exist as $buyer_ex){
				$new_arr[]=$buyer_ex['SupplierBuyer']['buyer_id'];
			}	

			$condition["NOT"]=array("NewBuyer.required_feedback" =>'',"NewBuyer.email_id" =>'');
			if(!empty($contry_arr)){
				$condition['NewBuyer.country']=$contry_arr;
			}
			//$condition['NewBuyer.id']= $buyer_arr;
		 
			if(!empty($new_arr)){
				$condition["NOT"]=array("NewBuyer.required_feedback" =>'',"NewBuyer.id" =>$new_arr);
			}	
			$nb_list = $this->NewBuyer->find('all',array('conditions'=>$condition,'order' => array('NewBuyer.id'=>'DESC'),'contain' => array('NewBuyerQuestion','Country')));
			//print_r($nb_list); die;
			//$nb_list1 = json_decode($nb_list);
			$data1 = json_encode(array('country'=>$data,'nb_list'=>$nb_list));
			return $data1;
		}			
	}
		
	function get_new_buyer(){
		$this->autoRender = false;	
		$member_id = self::_check_member_login();	
		//print_r($_POST); die;
		if($_POST["continent"] != null && $_POST["filter"] != null && $_POST["filter"] == 'continent'){
			$this->SupplierBuyer->recursive = -1;
			$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$member_id)));	
				
			//print_r($buyer_exist); die; 
			foreach($buyer_exist as $buyer_ex){
				//print_r($buyer_ex); die; 
				$new_arr[]=$buyer_ex['SupplierBuyer']['buyer_id'];
			}	
				//print_r($new_arr); die;
			$condition["NOT"]=array("NewBuyer.required_feedback" =>'');

			$condition['NewBuyer.continent']=$_POST["filter"];
			if(!empty($new_arr)){
				$condition["NOT"]=array("NewBuyer.required_feedback" =>'',"NewBuyer.id" =>$new_arr);
			}	
		
			$nb_list = $this->NewBuyer->find('all',array('conditions'=>$condition, 'order'=>array('NewBuyer.id'=>'DESC')));	
			//print_r($nb_list); die;
			/*$this->paginate['NewBuyer'] = array(
				'conditions'=>$condition,
				'order' => array('Supplier.id'=>'DESC'),
				'limit' => 10,	
				'contain' => array('NewBuyerQuestion')	 
			);
			 $nb_list = $this->paginate('NewBuyer');
		
			$this->set('nb_list', $nb_list);
		
			*/
			
			//$cont=$this->Country->country_list2($_POST["continent"]);
			//$country = $_POST["continent"];				 
			//if($country !== 'Select'){
				//$data="<select><option value=''>Select Country</option>";
				/*foreach($nb_list as $list){
					$data.="<option value=".$key.">". $value . "</option>";
				}
				$data.="</select>";
				
				'<tr id="sort_"'.$list['NewBuyer']['id'].'>
					<td>
						<div class="checkboxFive">
							<div class="input checkbox">
							<input id="SupplierId'.$member_id.'_" type="hidden" value="0" name="data[Supplier][id]['.$member_id.']">
							<input id="SupplierId'.$member_id.'" type="checkbox" value="'.$list['NewBuyer']['id'].'" name="data[Supplier][id]['.$member_id.']">
							<label for="SupplierId'.$member_id.'">'.$i.'</label>
							</div>
						</div>
					</td>
					<td>'.$i.'</td>
					<td>'.$list['NewBuyer']['org_name'].'</td>
					<td>'.$list['NewBuyer']['first_name'].' '.$list['NewBuyer']['middle_name'].' '.$list['NewBuyer']['last_name'].'</td>
					<td class="ref_number"><input id="SupplierReferenceNum'.$member_id.'" class="form-control" type="text" size="45" maxlength="15" name="data[Supplier][reference_num]['.$member_id.']"></td>
				</tr>';
			//}*/
			if(!empty($nb_list)){
			return $nb_list;
			}else{
				return;
			}
		} 
	}
		
	
	public function delete_buyer($id){		
		$this->SupplierBuyer->delete($id);		
			$this->Session->setFlash(__('New buyer has been deleted successfully from your list.'),'default',array(),'success');
			$this->redirect(array('action'=>'add_new_buyer'));			
	}
	public function dash_delete_buyer($id){	
		$this->SupplierBuyer->delete($id);			
			$this->Session->setFlash(__('New buyer has been deleted successfully from your list.'),'default',array(),'success');
			$this->redirect(array('action'=>'dashboard'));			
	}
	public function e_delete($id){		
		$this->ExistingBuyer->delete($id);	
		$this->Session->setFlash(__('Buyer has been deleted successfully.'),'default',array(),'success');		
		$this->redirect(array('action'=>'eb_list'));			
	}
	public function dash_e_delete($id){
		//echo $id; die;
	$ex_tot = $this->EbLoginDetail->find('count', array('conditions' => array('EbLoginDetail.existing_buyer_id'=>$id,'EbLoginDetail.eb_status'=>array(1,3,4,5))));
		
		if(empty($ex_tot)){
		//	echo "test"; die;	
		$this->ExistingBuyer->delete($id);	
		
		$this->FeedbackResponse->deleteAll(array('FeedbackResponse.existing_buyer_id'=>$id));
		
		$this->EbLoginDetail->deleteAll(array('EbLoginDetail.existing_buyer_id'=>$id));
		
		$this->Session->setFlash(__('Existing Buyer has been deleted successfully.'),'default',array(),'success');		
		
		} else {	
		$this->Session->setFlash(__('Some feedbacks are pending from this existing buyer.'),'default',array(),'success');		
		}
			
		$this->redirect(array('action'=>'dashboard'));				
	}
	
	public function add_new_buyer($id=null,$conti=null){
		$member_id = self::_check_member_login();
		//$ud = $this->Session->read('Auth');
		$active_supplier_id = $member_id;
		$page = $this->__load_page(56);			
		$new_arr=array();		
		$default_co = $conti;
		$default_cu='';
		if(!empty($id)){
			$default_val=$this->Country->continent_list2($id);
			$default_co=$default_val['Continent']['code'];
			$default_cu=$default_val['Country']['country_code_char2'];
		}
		$continent=$this->Country->continent_list();		
		$countries=$this->Country->country_list2($default_co);
		
		if(!empty($conti)){
			if(!empty($id)){
				$condition['NewBuyer.country']=$id;
			}else{
				foreach($countries as $key=>$value){
					$contry_arr[] =  $key;
				}
				$condition['NewBuyer.country']=$contry_arr;
			}
		}
		
		$process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$active_supplier_id), 'fields'=>array('Supplier.process_step')));
		
		
		$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));
		
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id), 'order'=>array('SupplierBuyer.id'=>'DESC')));	
			
	
		foreach($buyer_exist as $buyer_ex){
			$new_arr[]=$buyer_ex['SupplierBuyer']['buyer_id'];
		}	
		$this->set('added_buyer', $new_arr);
		$buyer_ques = $this->NewBuyerQuestion->find('all',array('fields'=>array('DISTINCT new_buyer_id')));
		//echo '<pre>';print_r($buyer_ques);die;
		foreach($buyer_ques as $buyer){
			$buyer_arr[] = $buyer['NewBuyerQuestion']['new_buyer_id'];
		}
			
	//	$condition["NOT"]=array("NewBuyer.required_feedback" =>'',"NewBuyer.email_id" =>'');
		$condition["NOT"]=array("NewBuyer.required_feedback" =>'',"NewBuyer.email_id" =>'');
		
		//if(!empty($buyer_arr)){
			$condition['NewBuyer.id']= $buyer_arr;
			 //$condition["NOT"]=array("NewBuyer.id" =>$new_arr);
		//}
		
		//if(!empty($new_arr)){
		//	$condition["NOT"]=array("NewBuyer.required_feedback" =>'',"NewBuyer.id" =>$new_arr);
			 //$condition["NOT"]=array("NewBuyer.id" =>$new_arr);
		//}	
		
		$is_ajax = 0;
		if($this->request->is('ajax')){
			$this->layout='';
			$is_ajax =1;
		}
		
		if(!empty($this->request->data)){ //echo "ddd"; die;
			if(!empty($this->request->data['page'])){
				
				$this->paginate['NewBuyer'] = array(
					'page' => $this->request->data['page'],
					'conditions'=>$condition,
					'order' => array('NewBuyer.org_name'=>'asc','NewBuyer.id'=>'DESC',),
					'limit' => 5,	
					'contain' => array('NewBuyerQuestion','Country'),	
				);
				//echo 'test';
			}
		}else{
			$this->paginate['NewBuyer'] = array(
				 'conditions'=>$condition,
				'order' => array('NewBuyer.org_name'=>'asc','NewBuyer.id'=>'DESC'),
				'limit' => 5,	
				'contain' => array('NewBuyerQuestion','Country'),	
			);
			
		}
		
		//$this->NewBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));
		$nb_list = $this->paginate('NewBuyer');	
		/*$this->find('all',array(
        'fields'=>array('DISTINCT mobileNo','dateTime'),
        'order'=>'Message.idTextMessage DESC',
        'conditions' => array('Message.User_id' => $userid)));*/
	    //echo '<pre>';print_r($nb_list);die;
		$this->set('nb_list', $nb_list);
		/*------new-buyer-pagin-end-----------*/
		 //echo '<pre>';print_r($buyer_exist);die;
		/*------Supplier-Buyer-pagin-start-----------*/		
		$this->set('s_nb_list', $buyer_exist);
		
		
		if($process_step['Supplier']['process_step'] < 5){
			
			$tot_ex_buy = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id,'SupplierBuyer.round'=>1), 'order'=>array('SupplierBuyer.id'=>'DESC')));			
				
			
				} else {
			
			$tot_ex_buy = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id,'SupplierBuyer.round'=>2), 'order'=>array('SupplierBuyer.id'=>'DESC')));		
			
				
				}
		$this->set('t_nb_list', $tot_ex_buy);
		
		if(!empty($this->request->data['Supplier']))
		{	 
			$ok=0;	
		//	$merge=array_combine($this->request->data['Supplier']['id'],$this->request->data['Supplier']['reference_num']);	
			$merge=$this->request->data['Supplier']['id'];	
			
			//echo "<pre>"; print_r($this->request->data['Supplier']['id']); die;	
						
			foreach($merge as $key){
				$data['SupplierBuyer']['buyer_id']=$key;
				$this->NewBuyer->id = $key;
				$required_feedback = $this->NewBuyer->field('required_feedback');
			//	$data['SupplierBuyer']['reference_num']=$value;
				$data['SupplierBuyer']['required_feedback']=$required_feedback;
				if($process_step['Supplier']['process_step'] < 5){
				$data['SupplierBuyer']['round']=1;		
				} else {
				$data['SupplierBuyer']['round']=2;		
				}
				$data['SupplierBuyer']['supplier_id']=$this->request->data['Supplier']['s_id'];
				$data['SupplierBuyer']['created_date']=date('Y-m-d H:i:s');	
				if(!empty($data['SupplierBuyer']['buyer_id'])){
					$this->SupplierBuyer->create();
					if($this->SupplierBuyer->save($data,array('validate'=>false))){
						$ok=1;
					}				
				}
			}
				
			
			if($process_step['Supplier']['process_step'] == 1){
				$data1['Supplier']['id']=$active_supplier_id;
				$data1['Supplier']['process_step']=2;	
				$this->Supplier->create();
				$this->Supplier->save($data1,array('validate'=>false));
				//$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'success',2));
			}	
			if($ok==1){						
				$this->Session->setFlash(__('New buyer(s) added successfully to your list.'),'default',array(),'success');
				
			}else{
				$this->Session->setFlash(__('Something went wrong! Please try again.'),'default',array(),'error');
			}			
			$this->redirect(array('action'=>'add_new_buyer'));			
		}
		
		$this->set(compact('active_supplier_id','continent', 'page', 'id','countries','default_co','default_cu'));	
		$this->set('process_step',$process_step['Supplier']['process_step']);	
		$this->set('is_ajax',$is_ajax);
		if($this->request->is('ajax')){
			$this->render('Elements/new_buyer');
		}
	}
	
	
	
	
	
	public function required_feedback($id=null) {
		$loguser_id = self::_check_member_login();	
		$process_step = $this->__get_process_step($loguser_id);		
		$total_nb='';
		$ids='';		
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_seo('site_title','Supplier-Existing Buyer');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		if($process_step<5){
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$loguser_id), 'order'=>array('SupplierBuyer.id'=>'DESC')));	
	} else {
		
	$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$loguser_id,'SupplierBuyer.round'=>2), 'order'=>array('SupplierBuyer.id'=>'DESC')));		
		
	}
		$req_fed=array();
		foreach($buyer_exist as $_buyer_exist){
		$req_fed[]=$_buyer_exist['SupplierBuyer']['required_feedback'];
		}
		
		
		$tot_req_feed=(max($req_fed));
		$this->set('tot_req_feed', $tot_req_feed);
		if(empty($tot_req_feed)){
			
		$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'add_new_buyer'));	
			
		}
		//$exist_count = $this->__exist_buyer_count($loguser_id);	
		
		//if($tot_req_feed<=$exist_count){
		//	$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'assign_existing_buyer'));		
		// }
			
		$this->set('existing_b', $buyer_exist);
		if(!empty($this->request->data)){
			$ids=$this->request->data['NewBuyer']['id'];
			$total_nb=count($ids);	
		}
		$this->set('selected_nb', $ids);		
		$this->set('total_nb', $total_nb);
		$this->set('process_step', $process_step);
	  }
	
	
	
	public function eb_list($id=null) {
		$loguser_id = self::_check_member_login();	
		$process_step = $this->__get_process_step($loguser_id);		
		$total_nb='';
		$ids='';	
			
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_seo('site_title','Supplier-Existing Buyer');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		$this->paginate['ExistingBuyer'] = array(
		  'conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1),
		  'order' => array('ExistingBuyer.id'=>'DESC'),
		  'limit' => 10,		 
		);
		$eb = $this->paginate('ExistingBuyer');			
		$this->set('existing_b', $eb);
		if(!empty($this->request->data)){
			$ids=$this->request->data['NewBuyer']['id'];
			$total_nb=count($ids);	
		}
		$this->set('selected_nb', $ids);		
		$this->set('total_nb', $total_nb);
		$this->set('process_step', $process_step);
	  }
	  
	  public function existing_element($id=null) {
		$loguser_id = self::_check_member_login();		
		$req=0;
		if(!empty($_POST['required'])){
			$req=$_POST['required'];
		}
		if(!empty($_POST['nb_id'])){
			$nb_id=$_POST['nb_id'];
		}					
		$this->paginate['ExistingBuyer'] = array(
		  'conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1),
		  'order' => array('ExistingBuyer.id'=>'DESC'),
		  'limit' => 10,		 
		);
		$eb = $this->paginate('ExistingBuyer');			
		$this->set('existing_b', $eb);				
		$this->set('total_req',$req);				
		$this->set('nb_id',$nb_id);				
	  }
	  
	 public function cancel_request($id=null) {			
			$this->Session->delete('Request');
			if(isset($id) && $id=='cancel'){
				$this->Session->setFlash(__('You have cancelled the request. Please try again.'),'default',array(),'error');
			}
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'make_request'));
		
		}
			
	  public function make_request($id=null) {
		$loguser_id = self::_check_member_login();
		$loguser =$this->MemberAuth->get_active_member_detail();
		$process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$loguser_id),'fields'=>array('Supplier.process_step')));		
		$active_supplier_id=$loguser_id;
		if(isset($id) && $id=='cancel'){
			$this->Session->delete('Request');
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'make_request'));
		}
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_seo('site_title','Supplier-Existing Buyer');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id), 'order'=>array('SupplierBuyer.id'=>'DESC')));	
		
		$this->set('s_nb_list', $buyer_exist);
		$this->set('process_step',$process_step['Supplier']['process_step']);
	  }
	   
	   public function assign_existing_buyer($responce=null) {
		$loguser_id = self::_check_member_login();
			
		$loguser =$this->MemberAuth->get_active_member_detail();
		$process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$loguser_id),'fields'=>array('Supplier.process_step')));		
		$active_supplier_id=$loguser_id;
		if(isset($id) && $id=='cancel'){
			$this->Session->delete('Request');
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'make_request'));
		}
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_seo('site_title','Supplier-Existing Buyer');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		
		$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));
		
		
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id), 'order'=>array('SupplierBuyer.id'=>'DESC')));	
		
		if($process_step['Supplier']['process_step']==5){
		
		$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));
		
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id,'SupplierBuyer.round'=>2), 'order'=>array('SupplierBuyer.id'=>'DESC')));			
		}
	// for required feedback
		$req_fed=array();
		foreach($buyer_exist as $_buyer_exist){
		$req_fed[]=$_buyer_exist['SupplierBuyer']['required_feedback'];
		}
		
		$tot_req_feed=(max($req_fed));
		$this->set('tot_req', $tot_req_feed);	
		
	// End  for required feedback	

		$this->paginate['ExistingBuyer'] = array(
		  'conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1),
		  'order' => array('ExistingBuyer.id'=>'DESC'),
		  'limit' => 5,		 
		);
		$eb = $this->paginate('ExistingBuyer');		

			
		$this->set('existing_b', $eb);				
		$this->set('s_nb_list', $buyer_exist);
		$this->set('process_step',$process_step['Supplier']['process_step']);
	  }
	  
	 public function select_existing_buyer($responce=null) {	  	
		$loguser_id = self::_check_member_login();
			
		$loguser =$this->MemberAuth->get_active_member_detail();
		$process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$loguser_id),'fields'=>array('Supplier.process_step')));		
		$active_supplier_id=$loguser_id;
		if(isset($id) && $id=='cancel'){
			$this->Session->delete('Request');
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'make_request'));
		}
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_seo('site_title','Supplier-Existing Buyer');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		
		$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));
		
		
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id), 'order'=>array('SupplierBuyer.id'=>'DESC')));	
		
		if($process_step['Supplier']['process_step']==5){
		
		$eb_req = $this->EbLoginDetail->find('all',array('conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1,'EbLoginDetail.eb_status'=>2,'EbLoginDetail.is_link_expire'=>1),'order'=>array('EbLoginDetail.id'=>'DESC'),'fields'=>array('EbLoginDetail.request_id')));	
		
// for exired existing buyer

	$eb_expire = $this->EbLoginDetail->find('all',array('conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1,'EbLoginDetail.eb_status'=>2,'EbLoginDetail.is_link_expire'=>1),'fields'=>array('EbLoginDetail.existing_buyer_id')));	
	$al_eb_ex=array();	
	foreach($eb_expire  as $_eb_expire){
				$al_eb_ex[]=$_eb_expire['EbLoginDetail']['existing_buyer_id'];			
		} 
		$expire_all_eb=array_unique($al_eb_ex);
	
	
	 
	//	$all_req_id=array();
		$all_req_id=array();
		foreach($eb_req  as $_req_id){
				$all_req_id[]=$_req_id['EbLoginDetail']['request_id'];			
		} 
		
		$un_req_id=array_unique($all_req_id);
		// echo "<pre>"; print_r($un_req_id); die;
		
		$replace_ex_id = $this->ExistingBuyer->find('list',array('fields'=>array('ExistingBuyer.id'),'conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1,'ExistingBuyer.replace'=>1)));

		
	foreach($un_req_id  as $_req_id){
		
		$feedback=$this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$_req_id)));
		$nb_acc_ex=json_decode($feedback['FeedbackRequest']['selected_new_b_exist'],true);
			
	foreach($nb_acc_ex as $nb=>$exs){	
		
		$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));
		
		$all_buyer_exist['nb'] = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id,'SupplierBuyer.buyer_id'=>$nb), 'order'=>array('SupplierBuyer.id'=>'DESC')));	
			
			
		$all_ex=array_merge($replace_ex_id,$exs);
		$all_ext=array_unique($all_ex);
		
		
		$all_buyer_exist['nb']['ex']  = $this->ExistingBuyer->find('all',array('conditions'=>array('ExistingBuyer.supplier_id'=>$active_supplier_id,'ExistingBuyer.id'=>$all_ext), 'order'=>array('ExistingBuyer.id'=>'ASC')));	
		
		$all_buyer_exist['nb']['req']=$_req_id;
		
		$total_buyer_exist[]=$all_buyer_exist;
				
			}	
				
		} 
		
	//echo "<pre>"; print_r($total_buyer_exist); die;		
	
		 
		$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));
		
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id,'SupplierBuyer.round'=>2), 'order'=>array('SupplierBuyer.id'=>'DESC')));			
		
		}	
	// for  feedback existing  id  get
		$feedget_id = $this->EbLoginDetail->find('all',array('conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1,'EbLoginDetail.eb_status'=>array(1,3,4,5,6)),'order'=>array('EbLoginDetail.id'=>'DESC'),'fields'=>array('EbLoginDetail.existing_buyer_id')));	
	 	 foreach($feedget_id  as $feed_ex_id){
				$feed_id[]=$feed_ex_id['EbLoginDetail']['existing_buyer_id'];			
		} 
		$all_feed_id=array_unique($feed_id);
		
	 
	// for required feedback
		$req_fed=array();
		foreach($buyer_exist as $_buyer_exist){
		$req_fed[]=$_buyer_exist['SupplierBuyer']['required_feedback'];
		}
		
		$tot_req_feed=(max($req_fed));
		$this->set('tot_req', $tot_req_feed);	
		
	// End  for required feedback	

		$this->paginate['ExistingBuyer'] = array(
		  'conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1),
		  'order' => array('ExistingBuyer.id'=>'DESC'),
		  'limit' => 5,		 
		);
		$eb = $this->paginate('ExistingBuyer');		


		$expire_all_eb=array_values($expire_all_eb);
	//echo "<pre>"; print_r($expire_all_eb); die;		
	//echo "<pre>"; print_r($total_buyer_exist); die;		
		$this->set('existing_b', $eb);				
		$this->set('expire_all_eb', $expire_all_eb);				
		$this->set('feedback_ex_id', $all_feed_id);				
		$this->set('s_nb_list', $total_buyer_exist);
		$this->set('process_step',$process_step['Supplier']['process_step']);
	  }  
	  
	 public function assign_existing_buyer_feed($id=null) {
		$loguser_id = self::_check_member_login();
			
		$loguser =$this->MemberAuth->get_active_member_detail();
		$process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$loguser_id),'fields'=>array('Supplier.process_step')));		
		$active_supplier_id=$loguser_id;
		if(isset($id) && $id=='cancel'){
			$this->Session->delete('Request');
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'make_request'));
		}
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_seo('site_title','Supplier-Existing Buyer');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		
		$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));
		
		
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id), 'order'=>array('SupplierBuyer.id'=>'DESC')));	
		
		if($process_step['Supplier']['process_step']==5){
		
		$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));
		
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id,'SupplierBuyer.round'=>2), 'order'=>array('SupplierBuyer.id'=>'DESC')));			
		}
	// for required feedback
		$req_fed=array();
		foreach($buyer_exist as $_buyer_exist){
		$req_fed[]=$_buyer_exist['SupplierBuyer']['required_feedback'];
		}
		
		$tot_req_feed=(max($req_fed));
		$this->set('tot_req', $tot_req_feed);	
	
		
		$exist_id = $this->ExistingBuyer->find('list',array('fields'=>array('ExistingBuyer.id'),'conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1)));
		
		
		
		$response_id = $this->FeedbackResponse->find('list',array('fields'=>array('FeedbackResponse.existing_buyer_id'),'conditions'=>array('FeedbackResponse.existing_buyer_id'=>$exist_id,'FeedbackResponse.response_status'=>2)));
	
	
	
		$replace_ex_id = $this->ExistingBuyer->find('list',array('fields'=>array('ExistingBuyer.id'),'conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1,'ExistingBuyer.replace'=>1)));
	
		
		if(!empty($replace_ex_id)){
			
			$response_id=array_merge($replace_ex_id,$response_id);
		}
		
		
		
		$this->paginate['ExistingBuyer'] = array(
		  'conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.id'=>$response_id,'ExistingBuyer.status'=>1),
		  'order' => array('ExistingBuyer.id'=>'DESC'),
		  'limit' => 5,		 
		);
		$eb = $this->paginate('ExistingBuyer');		

			
		$this->set('existing_b', $eb);				
		$this->set('s_nb_list', $buyer_exist);
		$this->set('process_step',$process_step['Supplier']['process_step']);
	  }
	
	public function get_used_responce($id=null) {
		$this->autoRender = false;	
		$loguser_id = self::_check_member_login();
			
		$loguser =$this->MemberAuth->get_active_member_detail();
		$process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$loguser_id),'fields'=>array('Supplier.process_step')));		
		$active_supplier_id=$loguser_id;
		
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_seo('site_title','Supplier-Existing Buyer');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		
		
		if($process_step['Supplier']['process_step']==5){				
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id,'SupplierBuyer.round'=>2), 'order'=>array('SupplierBuyer.id'=>'DESC'),'fields'=>array('SupplierBuyer.buyer_id','SupplierBuyer.required_feedback')));			
		}
	
		foreach($buyer_exist  as  $_buyer_exist){	
			
			$total_ques = $this->NewBuyerQuestion->find('list',array('conditions'=>array('NewBuyerQuestion.new_buyer_id'=>$_buyer_exist['SupplierBuyer']['buyer_id']),'fields'=>array('NewBuyerQuestion.question_id'),'recursive'  => 2));
			
			$quer_array=array_values($total_ques);
			
			
			
			//echo "<pre>"; print_r($quer_array); die;
		}
		
	// for required feedback
	    
		$req_fed=array();
		foreach($buyer_exist as $_buyer_exist){
		$req_fed[]=$_buyer_exist['SupplierBuyer']['required_feedback'];
		}
		
		$tot_req_feed=(max($req_fed));
		$this->set('tot_req', $tot_req_feed);	
	
		//$this->set('existing_b', $eb);				
		$this->set('s_nb_list', $buyer_exist);
		$this->set('process_step',$process_step['Supplier']['process_step']);
	  }
	
	public function replace_assign_exist($id=null) {
		
		$loguser_id = self::_check_member_login();
		$loguser =$this->MemberAuth->get_active_member_detail();
		$process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$loguser_id),'fields'=>array('Supplier.process_step')));		
		$active_supplier_id=$loguser_id;
		if(isset($id) && $id=='cancel'){
			$this->Session->delete('Request');
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'make_request'));
		}
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_seo('site_title','Supplier-Existing Buyer');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		
		$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));
			
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id), 'order'=>array('SupplierBuyer.id'=>'DESC')));	
		
	// for required feedback
		$req_fed=array();
		foreach($buyer_exist as $_buyer_exist){
		$req_fed[]=$_buyer_exist['SupplierBuyer']['required_feedback'];
		}
		
		$tot_req_feed=(max($req_fed));
		$this->set('tot_req', $tot_req_feed);	
	// End  for required feedback	
		
		$this->paginate['ExistingBuyer'] = array(
		  'conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1),
		  'order' => array('ExistingBuyer.id'=>'DESC'),
		  'limit' => 10,		 
		);
		$eb = $this->paginate('ExistingBuyer');			
		$this->set('existing_b', $eb);				
		$this->set('s_nb_list', $buyer_exist);
		$this->set('process_step',$process_step['Supplier']['process_step']);
	  }  
	public function buyer_existing_list($id=null) {
		$loguser_id = self::_check_member_login();
		$loguser =$this->MemberAuth->get_active_member_detail();
		$process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$loguser_id),'fields'=>array('Supplier.process_step')));		
		$active_supplier_id=$loguser_id;
	if(!empty($this->request->data)){
	foreach($this->request->data['SupplierBuyer'] as $sbuy=>$ref){								
					$nb_refrence[]['SupplierBuyer']=$ref;
				}	
				//echo "<pre>"; print_r($nb_refrence); die;	
	foreach($nb_refrence  as  $_refrence){	
              $ref=  $_refrence['SupplierBuyer']['reference_num'];        
              $cat=  $_refrence['SupplierBuyer']['category'];        
			  $b_id=  $_refrence['SupplierBuyer']['buyer_id'];  
               $this->SupplierBuyer->updateAll(
                      array('SupplierBuyer.reference_num' => "'$ref'",'SupplierBuyer.category' =>"'$cat'"),
                      array('AND'=>array('SupplierBuyer.buyer_id'=>$b_id,'SupplierBuyer.supplier_id'=>$loguser_id))
                                     );	      
                                                       
		}	
}

		$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));	
		
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id), 'order'=>array('SupplierBuyer.id'=>'DESC')));	
	if($process_step['Supplier']['process_step']==5){
		
		$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));	
		
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id,'SupplierBuyer.round'=>2), 'order'=>array('SupplierBuyer.id'=>'DESC')));	
		
	}

		//echo "<pre>"; print_r($buyer_exist); die;	
		
		$this->set('s_nb_list', $buyer_exist);
		$this->set('process_step',$process_step['Supplier']['process_step']);
 }  
	 
	 
public function select_existing_list($id=null) {
		//echo "<pre>"; print_r($this->request->data); die;
	
		$loguser_id = self::_check_member_login();
		$loguser =$this->MemberAuth->get_active_member_detail();
		$process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$loguser_id),'fields'=>array('Supplier.process_step')));		
		$active_supplier_id=$loguser_id;
	if(!empty($this->request->data)){
	foreach($this->request->data['SupplierBuyer'] as $sbuy=>$ref){								
					$nb_refrence[]['SupplierBuyer']=$ref;
				}	
				//echo "<pre>"; print_r($nb_refrence); die;	
	foreach($nb_refrence  as  $_refrence){	
              $ref=  $_refrence['SupplierBuyer']['reference_num'];        
              $cat=  $_refrence['SupplierBuyer']['category'];        
			  $b_id=  $_refrence['SupplierBuyer']['buyer_id'];  
               $this->SupplierBuyer->updateAll(
                      array('SupplierBuyer.reference_num' => "'$ref'",'SupplierBuyer.category' =>"'$cat'"),
                      array('AND'=>array('SupplierBuyer.buyer_id'=>$b_id,'SupplierBuyer.supplier_id'=>$loguser_id))
                                     );	      
                                                       
		}	
}



$eb_req = $this->EbLoginDetail->find('all',array('conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1,'EbLoginDetail.eb_status'=>2,'EbLoginDetail.is_link_expire'=>1),'order'=>array('EbLoginDetail.id'=>'DESC'),'fields'=>array('EbLoginDetail.request_id')));	
		
// for exired existing buyer

	$eb_expire = $this->EbLoginDetail->find('all',array('conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1,'EbLoginDetail.eb_status'=>2,'EbLoginDetail.is_link_expire'=>1),'fields'=>array('EbLoginDetail.existing_buyer_id')));	
	$al_eb_ex=array();	
	foreach($eb_expire  as $_eb_expire){
				$al_eb_ex[]=$_eb_expire['EbLoginDetail']['existing_buyer_id'];			
		} 
		$expire_all_eb=array_unique($al_eb_ex);
	//	echo "<pre>"; print_r($expire_all_eb); die;
	
	
	//	$all_req_id=array();
		$all_req_id=array();
		foreach($eb_req  as $_req_id){
				$all_req_id[]=$_req_id['EbLoginDetail']['request_id'];			
		} 
		
		$un_req_id=array_unique($all_req_id);
		// echo "<pre>"; print_r($un_req_id); die;
		
	//	$replace_ex_id = $this->ExistingBuyer->find('list',array('fields'=>array('ExistingBuyer.id'),'conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1,'ExistingBuyer.replace'=>1)));

		
	foreach($un_req_id  as $_req_id){
		
		$feedback=$this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$_req_id)));
		$nb_acc_ex=json_decode($feedback['FeedbackRequest']['selected_new_b_exist'],true);
			
	foreach($nb_acc_ex as $nb=>$exs){	
		
		$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));
		
		$all_buyer_exist['nb'] = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id,'SupplierBuyer.buyer_id'=>$nb), 'order'=>array('SupplierBuyer.id'=>'DESC')));	
			
			
	//	$all_ex=array_merge($replace_ex_id,$exs);
	//	$all_ext=array_unique($all_ex);
		
		
	//	$all_buyer_exist['nb']['ex']  = $this->ExistingBuyer->find('all',array('conditions'=>array('ExistingBuyer.supplier_id'=>$active_supplier_id,'ExistingBuyer.id'=>$all_ext), 'order'=>array('ExistingBuyer.id'=>'ASC')));	
		
	//	$all_buyer_exist['nb']['req']=$_req_id;
		
		$total_buyer_exist[]=$all_buyer_exist;
				
			}	
				
		} 


		$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));	
		
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id), 'order'=>array('SupplierBuyer.id'=>'DESC')));	
	if($process_step['Supplier']['process_step']==5){
		
		$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));	
		
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$active_supplier_id,'SupplierBuyer.round'=>2), 'order'=>array('SupplierBuyer.id'=>'DESC')));	
		
	}

	//	echo "<pre>"; print_r($total_buyer_exist); die;	
		
	//	$this->set('s_nb_list', $buyer_exist);
		$this->set('s_nb_list', $total_buyer_exist);
		$this->set('process_step',$process_step['Supplier']['process_step']);
 }  	 
	 
	  
	  public function request_data($id=null){
		  
		  
		 $this->autoRender = false;		
		 $active_supplier_id = self::_check_member_login();
		 $process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$active_supplier_id), 'fields'=>array('Supplier.process_step')));
		 
		
			if(!empty($this->request->data)){
			$exist_acording_nb=array();
			$ex_by=array();
			$tot_exis=$this->request->data['Supplier']['eb_id'];
			 foreach($this->request->data['Supplier']['eb_id'] as  $nb_k=>$exb){		 		
						foreach($exb as $key=>$value){
								if($value == 0){unset($exb[$key]);}
								$ex_by[]=$value;
								}		
						$exist_acording_nb[$nb_k]=array_values($exb);
						
		 }
		 
		 //$exist_nb_json=json_encode($exist_acording_nb);
		//echo  $exist_nb_json;  die;	 
		
		
		$newex=array();	
		foreach($ex_by as $array_key=>$array_item){
				if($ex_by[$array_key] == 0){		unset($ex_by[$array_key]);}
			  } 
		
		$ex_by = array_unique($ex_by);
		
		$this->request->data['Supplier']['eb_id']=$ex_by;
	
// for reference num and category update 				
		foreach($this->request->data['SupplierBuyer'] as $sbuy=>$ref){								
					$nb_refrence[]['SupplierBuyer']=$ref;
				}	
	foreach($nb_refrence  as  $_refrence){	
              $ref=  $_refrence['SupplierBuyer']['reference_num'];        
              $cat=  $_refrence['SupplierBuyer']['category'];        
              $b_id=  $_refrence['SupplierBuyer']['buyer_id'];        
          $this->SupplierBuyer->updateAll(
                  array('SupplierBuyer.reference_num' =>"'$ref'",'SupplierBuyer.category' =>"'$cat'"),
                  array('AND'=>array('SupplierBuyer.buyer_id'=>$b_id,'SupplierBuyer.supplier_id'=>$active_supplier_id))
                                  );	                                                      
		
		}	
			
// for reference num and category update 	
				

			$use_eb_id=0;
			if(!empty($this->request->data['Supplier']['eb_send_again']) && $this->request->data['Supplier']['eb_send_again']==1){
				$this->request->data['Supplier']['eb_id']=array($this->request->data['Supplier']['eb_org']);
				$use_eb_id=$this->request->data['Supplier']['eb_org'];
				$request['eb_send_again']=$use_eb_id;
			}else{
				foreach($this->request->data['Supplier']['eb_id'] as $key=>$value){
					if($value == 0){unset($this->request->data['Supplier']['eb_id'][$key]);}
				  }
			 }
			  
			$total=count($this->request->data['Supplier']['eb_id']);
			$total_req=$this->request->data['Supplier']['total_req'];
					
			if($total<$total_req){
				$x=$total_req-$total;
				$this->Session->setFlash(__('You have selected less existing buyers. Please select '.$x.' existing buyers to proceed.'),'default',array(),'error');
				if($this->request->data['Supplier']['form']=='resend'){
					$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'assign_new_buyer'));
				}else{
					$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'assign_existing_buyer'));
				}
			}
				
				
			
		
			if(($this->request->data['Supplier']['form']=='resend') && (!empty($this->request->data['Supplier']['req_id']))){
			
				$feedback_data=$this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$this->request->data['Supplier']['req_id']),'recursive'=>-1));						
				$old_eb=json_decode($feedback_data['FeedbackRequest']['existing_buyers']);
				$new_eb=$this->request->data['Supplier']['eb_id'];
				$all_eb=$old_eb;
				if(!in_array($use_eb_id,$old_eb)){
					$all_eb=array_merge($old_eb, $new_eb);
				}
				//echo '<pre>';print_r($all_eb); die;	
				$request['selected_nb_eb'] =$tot_exis;
				$request['selected_nb'] =$feedback_data['FeedbackRequest']['new_buyers'];
				$request['selected_eb'] =json_encode(array_values($all_eb));
				$request['question_list'] =$feedback_data['FeedbackRequest']['questions'];
				$request['additional_eb'] =$new_eb;
				$request['is_resent'] =1;
				$request['old_eb'] =$this->request->data['Supplier']['eb_org'];
				$request['req_id'] =$this->request->data['Supplier']['req_id'];
				$request['back_url'] =Router::url(array('plugin'=>'supplier_manager','controller'=>'requests','action'=>'pending_request'), true );
				
			}else if($this->request->data['Supplier']['use_feed']=='use_feed'){
				
	 		

			$exist_id = $this->ExistingBuyer->find('list',array('fields'=>array('ExistingBuyer.id'),'conditions'=>array('ExistingBuyer.supplier_id'=>$active_supplier_id,'ExistingBuyer.status'=>1)));
		
			$response_id = $this->FeedbackResponse->find('list',array('fields'=>array('FeedbackResponse.existing_buyer_id'),'conditions'=>array('FeedbackResponse.existing_buyer_id'=>$exist_id,'FeedbackResponse.response_status'=>2)));
			
			//echo "<pre>"; print_r($response_id); die;
			
			$all_checked_eb=$this->request->data['Supplier']['eb_id'];
			
			$replaced_eb_id = array_diff($all_checked_eb,$response_id);
		
		//	echo "<pre>"; print_r($replaced_eb_id); die;
		
			if(!empty($replaced_eb_id)){
				$request['selected_used_eb'] =json_encode(array_values($response_id));		
				$request['selected_rs_eb'] =json_encode(array_values($replaced_eb_id));	
			}
				$request['selected_nb_eb'] =$tot_exis;		
				$data['FeedbackRequest']['existing_buyers']=json_encode(array_values($this->request->data['Supplier']['eb_id']));
				$data['FeedbackRequest']['new_buyers_exist']=json_encode($exist_acording_nb);
				$data['FeedbackRequest']['new_buyers']=json_encode(array_values($this->request->data['Supplier']['nb_id']));
				$data['FeedbackRequest']['supplier_id']=$active_supplier_id;
				
				$total_ques = $this->Question->find('all',array('conditions'=>array('Question.status'=>1),'fields'=>array('Question.id')));		
				 
					foreach($total_ques as $total_q){
						
							$question_arr[]=$total_q['Question']['id'];		
					}									
				//echo "<pre>"; print_r($question_arr); die;		
				$data['FeedbackRequest']['questions']=json_encode(array_values($question_arr));	
				$request['selected_nb_eb'] =$tot_exis;
				$request['selected_nb'] =$data['FeedbackRequest']['new_buyers'];
				$request['selected_nb_ex']=$data['FeedbackRequest']['new_buyers_exist'];
				$request['selected_eb'] =$data['FeedbackRequest']['existing_buyers'];
				$request['question_list'] =$data['FeedbackRequest']['questions'];
				$request['is_report_sent'] = json_encode($nb);
				$request['type'] = 'use_feed';
				
				if(!empty($replaced_eb_id)){
						$request['type'] = 'use_some_feed';
				}
				$request['is_resent'] =0;
			
			
			}else if($this->request->data['Supplier']['use_feed']!='use_feed'){	
				
				$request['selected_nb_eb'] =$tot_exis;		
				$data['FeedbackRequest']['existing_buyers']=json_encode(array_values($this->request->data['Supplier']['eb_id']));
				$data['FeedbackRequest']['new_buyers_exist']=json_encode($exist_acording_nb);
				$data['FeedbackRequest']['new_buyers']=json_encode(array_values($this->request->data['Supplier']['nb_id']));
				$data['FeedbackRequest']['supplier_id']=$active_supplier_id;
				
				$total_ques = $this->Question->find('all',array('conditions'=>array('Question.status'=>1),'fields'=>array('Question.id')));		
					foreach($total_ques as $total_q){
							$question_arr[]=$total_q['Question']['id'];
			
					}		
					
				//echo "<pre>"; print_r($question_arr); die;		
				$data['FeedbackRequest']['questions']=json_encode(array_values($question_arr));	
				$request['selected_nb_eb'] =$tot_exis;
				$request['selected_nb'] =$data['FeedbackRequest']['new_buyers'];
				$request['selected_nb_ex']=$data['FeedbackRequest']['new_buyers_exist'];
				$request['selected_eb'] =$data['FeedbackRequest']['existing_buyers'];
				$request['question_list'] =$data['FeedbackRequest']['questions'];
				$request['is_report_sent'] = json_encode($nb);
				$request['is_resent'] =0;
				$request['type'] = 'normal';
			}	
			$request['active_class'] =1;
	  //	echo '<pre>'; print_r($request); die;	
			$this->Session->write('Request', $request);	
		//	if($process_step['Supplier']['process_step'] == 3){				
				//$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'success',4));
			//}
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'card_detail'));
		}elseif(!empty($request_id)){
			$data=$this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$request_id),'recursive'  => 2));	
			$this->set('data',$data);
		}else{
			$this->Session->setFlash(__('You can make a feedback request by choosing the new buyer(s) added in your account.'),'default',array(),'success');
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'add_new_buyer'));	
		}		
	  }
	  
	public function select_request_data($id=null){	  
		 $this->autoRender = false;		
		 $active_supplier_id = self::_check_member_login();
		 $process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$active_supplier_id), 'fields'=>array('Supplier.process_step')));
		 
	$all_new_eb=array();	
if(!empty($this->request->data)){	
		
		$req_id=array_unique($this->request->data['Supplier']['req_id']);			
	
		$this->FeedbackRequest->recursive = -1; 	
		$feedback=$this->FeedbackRequest->find('all',array('conditions'=>array('FeedbackRequest.id'=>$req_id)));	
	
		$all_feed_data=array();		
	foreach($feedback as  $f_eedback){
		$exist_acording_nb=array();
		$nb=json_decode($f_eedback['FeedbackRequest']['new_buyers']);
		
	    $ex_by=array();
		foreach($this->request->data['Supplier']['eb_id'] as  $nb_k=>$exb){		 
			
			if(in_array($nb_k,$nb)){
					
						foreach($exb as $key=>$value){
								if($value == 0){unset($exb[$key]);}
								$ex_by[]=$value;
						}		
						$exist_acording_nb[$nb_k]=array_values($exb);
				}
						
		 }
		 
		 $newex=array();	
		foreach($ex_by as $array_key=>$array_item){
				if($ex_by[$array_key] == 0){  unset($ex_by[$array_key]);}
			  } 
		 
		$ex_by = array_unique($ex_by); 
	
	
	$old_ex=json_decode($f_eedback['FeedbackRequest']['existing_buyers']);
	$new_ex=array();
	foreach($ex_by  as  $_all_ex){
		if(!in_array($_all_ex,$old_ex)){
			
			$new_ex[]=$_all_ex;
			$all_new_eb[]=$_all_ex;
			
		}
		
		
	}
	
	$req_ex_id[$f_eedback['FeedbackRequest']['id']]=$new_ex;
	
	
		//	echo "<pre>"; print_r($all_feed_data); die;	
		 
	$all_feed_data['FeedbackTemp']['request_id'] = $f_eedback['FeedbackRequest']['id'];
	
	$all_feed_data['FeedbackTemp']['existing_buyers'] = json_encode(array_values($ex_by));
	$all_feed_data['FeedbackTemp']['new_buyers'] = $f_eedback['FeedbackRequest']['new_buyers'];
	$all_feed_data['FeedbackTemp']['selected_new_b_exist'] = json_encode($exist_acording_nb);
	$all_feed_data['FeedbackTemp']['supplier_id'] = $f_eedback['FeedbackRequest']['supplier_id'];
	$all_feed_data['FeedbackTemp']['payment_id'] = $f_eedback['FeedbackRequest']['payment_id'];
	$all_feed_data['FeedbackTemp']['questions'] = $f_eedback['FeedbackRequest']['questions'];
	$all_feed_data['FeedbackTemp']['descriptive_ques'] = $f_eedback['FeedbackRequest']['descriptive_ques'];
	$all_feed_data['FeedbackTemp']['request_status'] = $f_eedback['FeedbackRequest']['request_status'];
	
	$all_feed_data['FeedbackTemp']['request_use'] = $f_eedback['FeedbackRequest']['request_use'];	
	
	$all_feed_data['FeedbackTemp']['is_resent'] = $f_eedback['FeedbackRequest']['is_resent'];
	$all_feed_data['FeedbackTemp']['resend_date'] = $f_eedback['FeedbackRequest']['resend_date'];
	$all_feed_data['FeedbackTemp']['created_date'] = $f_eedback['FeedbackRequest']['created_date'];
	$all_feed_data['FeedbackTemp']['is_report_sent'] = $f_eedback['FeedbackRequest']['is_report_sent'];
	
	//	echo "<pre>"; print_r($all_feed_data); die;	
				
				$this->FeedbackTemp->create();
				$this->FeedbackTemp->save($all_feed_data,array('validate'=>false));
		 	
	}
	
	$all_new_eb=array_unique($all_new_eb);
	
	
		$total_ques = $this->Question->find('all',array('conditions'=>array('Question.status'=>1),'fields'=>array('Question.id')));				 
		foreach($total_ques as $total_q){
				$question_arr[]=$total_q['Question']['id'];		
		}									
		//echo "<pre>"; print_r($question_arr); die;		
		
	
		$expire_id=array_unique(array_values($this->request->data['Supplier']['expire']));
		
	
		
		
	$request['question_list']=json_encode(array_values($question_arr));		
	$request['req_id'] =json_encode(array_values($req_id));
	$request['new_eb_id'] = json_encode($all_new_eb);
	$request['selected_nb'] = json_encode(array_values($this->request->data['Supplier']['nb_id']));
	$request['expire_id'] = json_encode($expire_id);
	$request['new_eb_acc_req'] = json_encode($req_ex_id);
	$request['type'] = 'replace_eb';
		
		
// for reference num and category update 				
		foreach($this->request->data['SupplierBuyer'] as $sbuy=>$ref){								
					$nb_refrence[]['SupplierBuyer']=$ref;
				}	
	foreach($nb_refrence  as  $_refrence){	
              $ref=  $_refrence['SupplierBuyer']['reference_num'];        
              $cat=  $_refrence['SupplierBuyer']['category'];        
              $b_id=  $_refrence['SupplierBuyer']['buyer_id'];        
          $this->SupplierBuyer->updateAll(
                  array('SupplierBuyer.reference_num' =>"'$ref'",'SupplierBuyer.category' =>"'$cat'"),
                  array('AND'=>array('SupplierBuyer.buyer_id'=>$b_id,'SupplierBuyer.supplier_id'=>$active_supplier_id))
                                  );	                                                      
		
		}	
			
// for reference num and category update 	
			

			$this->Session->write('Request', $request);	
		//echo "<pre>"; print_r($request); die;		
			
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'card_detail'));
		
			
		}else{
			$this->Session->setFlash(__('You can make a feedback request by choosing the new buyer(s) added in your account.'),'default',array(),'success');
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'add_new_buyer'));	
		}		
	  }	  
	public function  save_card(){
			$this->autoRender = false;		
			if(!empty($this->request->data['Supplier']['form'])){
				$this->request->data['Supplier']['exp_date']=$this->request->data['Supplier']['exp_month'].'/'.$this->request->data['Supplier']['exp_year'];
					$this->Supplier->create();
					$this->Supplier->save($this->request->data,array('validate'=>false));
					$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'process_payment'));
			}else{
				$this->Session->setFlash(__('Something went wrong. Please try again.'),'default',array(),'error');
				$this->redirect( Router::url( $this->referer(), true ) );	
			}	
	}
	
	  function payment_cancel($supplier_id=null,$s2=null,$error=null){		
		$member_type = $this->MemberAuth->get_member_type();
		$active_user =$this->MemberAuth->get_active_member_detail();
		
		$page = $this->Page->find('first',array('conditions'=>array('Page.id'=>66,'Page.status'=>1)));
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
	//	$this->Session->delete('Request');		
		if(!empty($supplier_id)){
			if($s2==0 && $error==1){
				$this->Session->setFlash(__('Something went wrong! Go to homepage or contact administrator.'),'default',array(),'error');
			}else{			
				if(empty($member_type)){
					$this->Session->setFlash(__('Payment process cancelled, please login.'),'default',array(),'error');
					$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'logout'));
				}else{
					$this->Session->setFlash(__('Your request is not processed due to cancellation of payment. Please try again.'),'default',array(),'error');
					if(!empty($s2)){
						$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'card_detail'));
					}
					$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'card_detail'));
				}
			}
		}
		
		if(empty($supplier_id)){
			throw new UnauthorizedException('404 Error - You are not authorized to access that location.');
		}
	}
	  
	  
	public function process_payment($id=null,$new_eb_id=null){		
			
			
		$loguser_id = self::_check_member_login();	
		$active_user =$this->MemberAuth->get_active_member_detail();		
		$page = $this->Page->find('first',array('conditions'=>array('Page.id'=>50,'Page.status'=>1)));
		if (empty($page)) {
			throw new NotFoundException('404 Error - Page not found');
		}
         
         if(!empty($_SESSION['add_exbuyer']))
         {
			 unset($_SESSION['add_exbuyer']);
		   }		
         
		$this->System->set_seo('site_title','Payment Process');
		$this->System->set_seo('site_metakeyword','Payment Process');
		$this->System->set_seo('site_metadescription','Payment Process');
		
		if((int)Configure::read('Section.default_banner_image') && ($page['Page']['use_default_image'] || $this->System->get_setting('page','override_banner_image'))){
			$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		}
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		
		$page['Page']['name'] = "Payment Process";
		$send_again='';
		$quantity=0;
		$feedb_data=$this->Session->read('Request');
		

		$old_eb='';
		if(empty($feedb_data) && empty($new_eb_id)){
			$this->redirect( Router::url( $this->referer(), true ) );
		}		
		$price=$this->System->get_setting('site','site_payment_fee');			
		$sid=$loguser_id;	
				
		if(($feedb_data['is_resent']==1) && (!empty($feedb_data['additional_eb']))){
			foreach($feedb_data['additional_eb'] as $feedb_d){
				$new_eb_id=$feedb_d;
			}
			$quantity=1;
			$id=$feedb_data['req_id'];
			$old_eb=$feedb_data['old_eb'];
			if(!empty($feedb_data['eb_send_again'])){
				$send_again=$feedb_data['eb_send_again'];
			}
		}
	//	echo "<pre>";  print_r($feedb_data);die;		
		/*	if(empty($feedb_data['selected_eb']) && !empty($new_eb_id)){							
				$feedb_data=$this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$id)));
				$feedback_id=$feedb_data['FeedbackRequest']['id'];
				$existing_b=json_decode($feedb_data['FeedbackRequest']['existing_buyers']);
				$existing_b[]=$new_eb_id;
				$feedb_data['selected_nb']=$feedb_data['FeedbackRequest']['new_buyers'];
				$feedb_data['selected_eb']=json_encode($existing_b);
				$feedb_data['question_list']=$feedb_data['FeedbackRequest']['questions'];
				$feedb_data['is_resent']=1;
				$quantity=1;
				
			}*/
		
		
		
			$selected_nb_ex=json_decode($feedb_data['selected_nb_ex']);
			
			$request_type=$feedb_data['type'];
			
		$string_nb_ex='';
			foreach($selected_nb_ex as $k=>$_selected_nb_ex){
			
			$string_nb_ex.='@'.$k.'<'.implode('-',$_selected_nb_ex);
			
		}
		 
		if($request_type=='use_some_feed'){
			
			$s_use_nb=json_decode($feedb_data['selected_used_eb']);			
			$string_use_eb_list=implode('-',$s_use_nb);	
			
			$s_use_re_nb=json_decode($feedb_data['selected_rs_eb']);			
			$string_rs_eb_list=implode('-',$s_use_re_nb);	
			
		}
		if($request_type=='replace_eb'){
			
			$req_id=json_decode($feedb_data['req_id']);			
			$string_req_id=implode('-',$req_id);
			
			
			$new_eb_id=json_decode($feedb_data['new_eb_id']);			
			$string_new_eb_id=implode('-',$new_eb_id);
		
		
		
			$expire_id=json_decode($feedb_data['expire_id']);			
			$string_expire_id=implode('-',$expire_id);
			
			
			$new_eb_acc_req=json_decode($feedb_data['new_eb_acc_req']);	
			
			
			$string_eb_acc_req='';
			foreach($new_eb_acc_req as $k=>$_new_eb_acc_req){
						$string_eb_acc_req.='@'.$k.'<'.implode('-',$_new_eb_acc_req);
			}
		
			
			
					
			//$string_new_eb_acc_req=implode('-',$new_eb_acc_req);

		}
		
		
			$s_nb=json_decode($feedb_data['selected_nb']);			
			$string_nb_list=implode('-',$s_nb);
			
			$s_nb2=$feedb_data['selected_nb'];
			
			$s_eb=json_decode($feedb_data['selected_eb']);
			$string_s_eb=implode('-',$s_eb);
			
			$s_q_list=json_decode($feedb_data['question_list']);			
			$string_s_q_list=implode('-',$s_q_list);
			
			$is_resent=$feedb_data['is_resent'];			
	//		$plan_name = 'Feedback Request';	
			$plan_name = 'Feedback Request';	
			$all_nb_list=json_decode($s_nb2);
			
			$required_list=array();
			foreach($all_nb_list as $list){
				$sp=$this->NewBuyer->find('first', array('conditions'=>array('id'=>$list),'fields'=>array('required_feedback')));			
				$required_list[]=$sp['NewBuyer']['required_feedback'];
			}
			//$max_req_num=max($required_list);
			//$el=count($s_eb);
			//$var_quantity = ($el > $max_req_num ? $el : $max_req_num); 	
			$var_quantity=count($s_nb);	
			
			if($quantity==1){
				$var_quantity =1;
				$custom_variable = 'sid='.$sid.'~snb='.$string_nb_list.'~seb='.$string_s_eb.'~sqlist='.$string_s_q_list.'~isresent='.$is_resent.'~new_eb='.$new_eb_id.'~req_id='.$id.'~old_eb='.$old_eb.'~send_again='.$send_again;
			} else if($request_type=='normal'){				
				$custom_variable = 'sid='.$sid.'~snb='.$string_nb_list.'~snbex='.$string_nb_ex.'~seb='.$string_s_eb.'~sqlist='.$string_s_q_list.'~isresent='.$is_resent.'~r_type='.$request_type;
		
			} else if($request_type=='use_feed'){				
				$custom_variable = 'sid='.$sid.'~snb='.$string_nb_list.'~snbex='.$string_nb_ex.'~seb='.$string_s_eb.'~sqlist='.$string_s_q_list.'~isresent='.$is_resent.'~r_type='.$request_type;
		
			} else if($request_type=='use_some_feed') {
				$custom_variable = 'sid='.$sid.'~snb='.$string_nb_list.'~snbex='.$string_nb_ex.'~seb='.$string_s_eb.'~sqlist='.$string_s_q_list.'~isresent='.$is_resent.'~sueb='.$string_use_eb_list.'~sureb='.$string_rs_eb_list.'~r_type='.$request_type;
				
			} else if($request_type=='replace_eb') {
				$custom_variable = 'sid='.$sid.'~snb='.$string_nb_list.'~srid='.$string_req_id.'~sqlist='.$string_s_q_list.'~isresent='.$is_resent.'~nebid='.$string_new_eb_id.'~exid='.$string_expire_id.'~seidacreq='.$string_eb_acc_req.'~r_type='.$request_type;
				
			}
				
			
				
		//echo "<pre>"; print_r($all_eb_req);die;		
									  
		//$custom_variable=addslashes($custom_variable);
	//echo "<pre>"; print_r($custom_variable);die;					
				
				App::import('Vendor', 'paypal', array('file' => 'paypal' . DS . 'Paypal.php'));
				$siteurl = Router::url('/',true);
				$myPaypal = new Paypal();
				if(Configure::read('paypal.status') == 0){
					$myPaypal->enableTestMode();
				}
				$paypal_email = trim(Configure::read('paypal.business_email'));
				$myPaypal->addField('business', $paypal_email);
				$myPaypal->addField('lc', 'US');
				//$myPaypal->addField('cmd', '_xclick');
				$myPaypal->addField('cmd', '_ext-enter');
				$myPaypal->addField('redirect_cmd', '_xclick');
				$myPaypal->addField('currency_code', 'EUR');
				$myPaypal->addField('image_url', $siteurl.'/img/site/logo_1464770195_75501118.png');
			
				$myPaypal->addField('notify_url', Router::url(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'process_ipn',$sid), true));
				
				$myPaypal->addField('return', Router::url(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'payment_complete',$sid), true));	
				
				if($feedb_data['is_resent']==1){									
					$myPaypal->addField('cancel_return', Router::url(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'payment_cancel',$sid,$id), true));					
				}else{				
					$myPaypal->addField('cancel_return', Router::url(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'payment_cancel',$sid), true));
					
				}
				$myPaypal->addField('rm', 2);
				$myPaypal->addField('item_name', $plan_name);
				$myPaypal->addField('no_shipping', number_format('0'));
				$myPaypal->addField('amount', $price);
				$myPaypal->addField('quantity', $var_quantity);
				$myPaypal->addField('custom', $custom_variable);
				$myPaypal->addField('email', $active_user['MemberAuth']['email_id']);
				$myPaypal->addField('first_name', $active_user['MemberAuth']['first_name']);
				$myPaypal->addField('last_name', $active_user['MemberAuth']['last_name']);
				$myPaypal->addField('address1', $active_user['MemberAuth']['address1']);
				$myPaypal->addField('address2', $active_user['MemberAuth']['address2']);
				$myPaypal->addField('state', $active_user['MemberAuth']['last_name']);
				
			//	print_r($myPaypal->fields);die;
				
				$html='';
				$html.= "<html>\n";
				$html.= "<head><title>Processing Payment...</title></head>\n";
				$html.= "<body onLoad=\"document.forms['gateway_form'].submit();\">\n";
				$html.= "<p style=\"text-align:center;\"><h2>Please wait, your payment is under process you will be redirected to the payment gateway.</h2></p>\n";
				$html.= "<form method=\"POST\" name=\"gateway_form\" ";
				$html.= "action=\"" . $myPaypal->gatewayUrl . "\">\n";
				
				foreach ($myPaypal->fields as $name => $value)
				{
					//echo $name.'--'.$value.'<br />';
					$html.= "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
				}
				//$c = explode("-",$client_id);				
				$html.= "</form>\n";
				$html.= "</body></html>\n";
				$this->set('html',$html);
				
			
			$this->set('page', $page);		
	  }
	 
	function payment_complete($str=null){
	//	$this->layout='';		
		$this->autoRender = false;		
		$supplier_id=$str;
		if(empty($str)){
			$supplier_id = self::_check_member_login();
			$client = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$supplier_id)));			
		}else{
			$client = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$supplier_id)));	
		}	
		//print_r($_REQUEST); die;
		if($_REQUEST['txn_type']=='subscr_cancel' || $_REQUEST['txn_type']=='subscr_payment'){
			return false;
		}
		if($_REQUEST['txn_id']){
			$pay_detail = $this->Payment->find('first',array('conditions'=>array('Payment.txn_id'=>$_REQUEST['txn_id'])));
			if(empty($pay_detail)){
				//mail("jakegyl21@gmail.com","process_ipn",'error-txn-type-'.$_REQUEST['txn_id']);	
										
				/*Extract custom variable of paypal*/				
				
				$data['FeedbackRequest']['supplier_id']=$supplier_id;
				$pay['Payment']['supplier_id']=$supplier_id;
				
				/*Extract custom variable of paypal*/
				$custom_field = explode('~',$_REQUEST['custom']);				
				foreach ($custom_field as $param) {
					$item = explode('=', $param);
					$item_name=$item[0];
					$item_data=$item[1];
					if($item_name!='sid' && $item_name!='isresent' && $item_name!='new_eb' && $item_name!='req_id' && $item_name!='snbex' && $item_name!='r_type' && $item_name!='seidacreq'){				
						$item_data=json_encode(explode('-',$item_data));
					}
					$custom_variable[$item_name] = $item_data;				
				}
				
				
	if(!empty($custom_variable['seidacreq'])){
								$all_eb_req_list=explode('@',$custom_variable['seidacreq']);
								unset($all_eb_req_list[0]);	
								$all_eb_req=array();
								foreach($all_eb_req_list  as   $_all_eb_req_list){	
									$base_arr=explode('<',$_all_eb_req_list);
									$all_eb_req[$base_arr[0]]=explode('-',$base_arr[1]);	
			
									}
			
			}				
				
				
				
					
			//	echo '<pre>';print_r($custom_variable);die;
				$payment_date=date("Y-m-d H:i:s",strtotime($_REQUEST['payment_date']));
				$pay['Payment']['payment_status']=$_REQUEST['payment_status'];
				$pay['Payment']['txn_id']=$_REQUEST['txn_id'];
				$pay['Payment']['payer_id']=$_REQUEST['payer_id'];
				$pay['Payment']['payer_email']=$_REQUEST['payer_email'];
				$pay['Payment']['amount']=$_REQUEST['mc_gross'];
				//$pay['Payment']['amount']=$_REQUEST['payment_gross'];
				$pay['Payment']['quantity']=$_REQUEST['quantity'];			
				$pay['Payment']['item_name']=$_REQUEST['item_name'];
				$pay['Payment']['created_date']=$payment_date;	
					
				if(!empty($custom_variable['isresent'])){
					$data['FeedbackRequest']['is_resent']=$custom_variable['isresent'];
				} 
				if(!empty($custom_variable['snb'])){
					$nb_zero_set=json_decode($custom_variable['snb']);
					$nb_array=array();
					foreach($nb_zero_set as $nb_zero){
						$nb_array[$nb_zero]=0;
					}
					$data['FeedbackRequest']['new_buyers']=$custom_variable['snb'];
					$data['FeedbackRequest']['is_report_sent']=json_encode($nb_array);
				}
				if(!empty($custom_variable['seb'])){
					$data['FeedbackRequest']['existing_buyers']=$custom_variable['seb'];
				}
				
				if(!empty($custom_variable['snbex'])){
						$all_nb_ex_list=explode('@',$custom_variable['snbex']);
								unset($all_nb_ex_list[0]);	
						$nb_ex_json=array();
						foreach($all_nb_ex_list  as   $_all_nb_ex_list){	
								$base_arr=explode('<',$_all_nb_ex_list);
								$nb_ex_json[$base_arr[0]]=explode('-',$base_arr[1]);	
							}
						$nb_ex_js=json_encode($nb_ex_json);	
							
						$data['FeedbackRequest']['selected_new_b_exist']=$nb_ex_js;	
						}			
				
				
				
				if(!empty($custom_variable['sqlist'])){
					$data['FeedbackRequest']['questions']=$custom_variable['sqlist'];
				}
							
				$data['FeedbackRequest']['request_status']=1;
				$data['FeedbackRequest']['created_date']=$payment_date;
				$descriptive_questions=$this->Question->find('all',array('conditions'=>array('Question.is_descriptive'=>1),'fields'=>'Question.id'));
				
				foreach($descriptive_questions as $desc){
						$q[]=$desc['Question']['id'];
				}
				$data['FeedbackRequest']['descriptive_ques']=json_encode($q);
				
				if(!empty($custom_variable['req_id'])){
					$data['FeedbackRequest']['id']=$custom_variable['req_id'];
					$FeedbackRequest=$this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$data['FeedbackRequest']['id']),'fields'=>'FeedbackRequest.request_status'));
					$data['FeedbackRequest']['request_status']=$FeedbackRequest['FeedbackRequest']['request_status'];
					$data['FeedbackRequest']['resend_date']=$payment_date;
				}	
				
				
			if($custom_variable['r_type']=='use_feed'){
						$data['FeedbackRequest']['request_use']=1;
					}
					if($custom_variable['r_type']=='use_some_feed'){
						$data['FeedbackRequest']['request_use']=2;
					}	
				
				
if($custom_variable['r_type']=='replace_eb'){

				
			$all_ex_id=json_decode($custom_variable['exid']);
		foreach($all_ex_id  as  $_all_ex_id){

					$this->EbLoginDetail->deleteAll(array('EbLoginDetail.existing_buyer_id'=>$_all_ex_id,'EbLoginDetail.eb_status'=>2,'EbLoginDetail.is_link_expire'=>1));
		}
	
$all_req_id=json_decode($custom_variable['srid']);
			
foreach($all_req_id as  $_all_req_id){
				
					$feedback = $this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$_all_req_id)));	
				
					$feedbacktemp = $this->FeedbackTemp->find('first',array('conditions'=>array('FeedbackTemp.request_id'=>$_all_req_id)));	
	if(!empty($feedbacktemp)){
					$feedback['FeedbackRequest']['id']=$_all_req_id;	
					$feedback['FeedbackRequest']['selected_new_b_exist']=$feedbacktemp['FeedbackTemp']['selected_new_b_exist'];
					$feedback['FeedbackRequest']['existing_buyers']=$feedbacktemp['FeedbackTemp']['existing_buyers'];
					$feedback['FeedbackRequest']['request_use']=3;
					$feedback['FeedbackRequest']['updated_date']=date("Y-m-d H:i:s",strtotime($_REQUEST['payment_date']));
					$feedback['FeedbackRequest']['created_date']=date("Y-m-d H:i:s",strtotime($_REQUEST['payment_date']));
					$this->FeedbackRequest->create();
					$this->FeedbackRequest->save($feedback,array('validate'=>false));
				//	$feedback_id = $this->FeedbackRequest->id;		
					
					$this->FeedbackTemp->deleteAll(array('FeedbackTemp.request_id'=>$_all_req_id));
				}
			}
									
		} else {	
					
					
					$this->FeedbackRequest->create();
					$this->FeedbackRequest->save($data,array('validate'=>false));
					$feedback_id = $this->FeedbackRequest->id;
				}
				
			if($custom_variable['r_type']=='replace_eb')  {			
			
				$feedback_id=implode(',',$all_req_id);
				
			}	
			
				
				$pay['Payment']['feedback_request_id']=$feedback_id;
				$this->Payment->create();
				$this->Payment->save($pay,array('validate'=>false));
				$payment_id=$this->Payment->id;;
				if(!empty($client)){							
					$options = array();	     
					$options['replacement'] = array('{NAME}'=>$client['Supplier']['first_name']." ".$client['Supplier']['middle_name']." ".$client['Supplier']['last_name'],'{TXN}'=>$_REQUEST['txn_id'],'{PAYMENTSTATUS}'=>$_REQUEST['payment_status'],'{AMOUNT}'=>$_REQUEST['mc_gross'],'{QUANTITY}'=>$_REQUEST['quantity'],'{MODE}'=>'Paypal');				
					$options['to'] = $client['Supplier']['email_id']; 
					$options['from'] = $this->System->get_setting('site','site_contact_noreply');			
					$this->MyMail->SendMail(23,$options);
					
					$options2 = array();	     
					$options2['replacement'] = array('{NAME}'=>$client['Supplier']['first_name']." ".$client['Supplier']['middle_name']." ".$client['Supplier']['last_name'],'{EMAIL}'=>$client['Supplier']['email_id'],'{TXN}'=>$_REQUEST['txn_id'],'{PAYEREMAIL}'=>$_REQUEST['payer_email'],'{PAYMENTSTATUS}'=>$_REQUEST['payment_status'],'{AMOUNT}'=>$_REQUEST['mc_gross'],'{QUANTITY}'=>$_REQUEST['quantity'],'{MODE}'=>'Paypal');				
					$options2['to'] = $this->System->get_setting('site','site_contact_email'); 
					$options2['from'] = $this->System->get_setting('site','site_contact_noreply');			
					$this->MyMail->SendMail(24,$options2);
					
					$p_id = $this->Payment->find('first',array('conditions'=>array('Payment.id'=>$payment_id)));
				
					//$fr = $this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.payment_id'=>$p_id['Payment']['id'])));
				
					//$fr_id=$fr['FeedbackRequest']['id'];
					$fr_id=$p_id['Payment']['feedback_request_id'];
					
					
					
				  	if($custom_variable['r_type']=='use_feed'){
							
							$all_sb=explode('-',$custom_variable['seb']);				
								foreach($all_sb as $all_selected_eb){
									$all_selected_eb=json_decode($all_selected_eb);
									if(is_array($all_selected_eb)){
										foreach($all_selected_eb as $all_selected_e){
											$this->existing_send_mail_feed($all_selected_e,$fr_id);
										}							
									}else{
										$this->existing_send_mail_feed($all_selected_eb,$fr_id);
									}
								}
							
							
			} else if($custom_variable['r_type']=='use_some_feed'){
														
							$all_sb=explode('-',$custom_variable['sueb']);				
								foreach($all_sb as $all_selected_eb){
									$all_selected_eb=json_decode($all_selected_eb);
									if(is_array($all_selected_eb)){
										foreach($all_selected_eb as $all_selected_e){
											$this->existing_send_mail_feed($all_selected_e,$fr_id);
										}							
									}else{
										$this->existing_send_mail_feed($all_selected_eb,$fr_id);
									}
								}	
						
						
								
						
							$all_sb=explode('-',$custom_variable['sureb']);				
								foreach($all_sb as $all_selected_eb){
										$all_selected_eb=json_decode($all_selected_eb);
										if(is_array($all_selected_eb)){
											foreach($all_selected_eb as $all_selected_e){
												$this->existing_send_mail($all_selected_e,$fr_id);
											}							
										}else{
												$this->existing_send_mail($all_selected_eb,$fr_id);
										}
								 }
													
			} else if($custom_variable['r_type']=='normal')  {			
							
						if(!empty($custom_variable['seb'])){		
							if(!empty($custom_variable['new_eb'])){		
								$old_eb=$custom_variable['old_eb'];							
								$custom_variable['send_again']=json_decode($custom_variable['send_again'],true);	
								if(!empty($custom_variable['send_again'][0])){	
									//echo 11;die;					
									$this->existing_send_mail($old_eb,$fr_id);
								}else{	
									//echo $custom_variable['new_eb'].'<br>';							
									//echo $fr_id.'<br>';									
									//echo $old_eb.'<br>';die;									
									$this->existing_send_mail($custom_variable['new_eb'],$fr_id,$old_eb);
								}	
							}else{
								$all_sb=explode('-',$custom_variable['seb']);				
								foreach($all_sb as $all_selected_eb){
									$all_selected_eb=json_decode($all_selected_eb);
									if(is_array($all_selected_eb)){
										foreach($all_selected_eb as $all_selected_e){
											$this->existing_send_mail($all_selected_e,$fr_id);
										}							
									}else{
										$this->existing_send_mail($all_selected_eb,$fr_id);
									}
								}
							}
						}
		} else if($custom_variable['r_type']=='replace_eb')  {		
					
					
					$send_eb=array();		
						foreach($all_eb_req as $req=>$_new_eid){			
							if(is_array($_new_eid)){
										foreach($_new_eid as $all_selected_e){
											
											
											if(!in_array($all_selected_e,$send_eb)){
											$this->existing_send_mail($all_selected_e,$req);
											}
											$send_eb[]=$all_selected_e;
											
										}							
									}else{
										$this->existing_send_mail($all_selected_eb,$req);
									}						
							
						}			
			}
						
					$this->MemberAuth->updateMemberSession($client['Supplier']);
					$this->MemberAuth->updateMemberType(1);
					$active_user =$this->MemberAuth->get_active_member_detail();				
					$this->Session->delete('Request');  
					if($client['Supplier']['process_step']==3){					
						$data1['Supplier']['id']=$client['Supplier']['id'];
						$data1['Supplier']['process_step']=5;	
						$this->Supplier->create();
						$this->Supplier->save($data1,array('validate'=>false));
					}	
				
					
				$this->SupplierBuyer->updateAll(array('SupplierBuyer.round' =>1),array('SupplierBuyer.supplier_id'=>$client['Supplier']['id']));
						
						
				$this->ExistingBuyer->updateAll(array('ExistingBuyer.replace' =>0),array('ExistingBuyer.supplier_id'=>$client['Supplier']['id']));
					
					
					
					
							
					if($client['Supplier']['status']==1 && !empty($active_user)){
						$this->Session->setFlash(__('Your payment received successfully and feedback request has been sent, please check your mailbox.'),'default',array(),'success');
						$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'payment_success'));
					}	
			
				}else{
					mail("jakegyl21@gmail.com","process_ipn",'error-txn-'.$_REQUEST['txn_id']);			
				}
			}else{
				$this->Session->setFlash(__('Your payment received successfully and feedback request has been sent, please check your mailbox.'),'default',array(),'success');
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'payment_success'));
			}	
		}else{
			//$this->Session->setFlash(__('There is some issue with your payment, Please Try again.'),'default',array(),'error');
			//$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'payment_failed'));
			$this->Session->setFlash(__('Your payment received successfully and feedback request has been sent, please check your mailbox.'),'default',array(),'success');
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'payment_success'));
		}
	}
	  
	function payment_failed(){
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_seo('site_title','Payment View');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		$this->set('title', "Payment Failed");	
	}
	
	function process_ipn($supplier_id=null){
		$this->layout='';		
		$this->autoRender=false;
		$this->loadModel('QuestionManager.Question');
	
		App::import('Vendor', 'paypal', array('file' => 'paypal' . DS . 'Paypal.php'));
		$myPaypal = new Paypal();
		$myPaypal->ipnLogFile = APP.'Vendor/paypal/ipn_results.log';
		$siteurl=Router::url('/',true);
		//$this->loadModel('Payment');
		
		if(Configure::read('paypal.status') == 0){
			$myPaypal->enableTestMode();
		}	
				 
		if($myPaypal->validateIpn()){	
			mail("jakegyl21@gmail.com","process_ipn-hit",json_encode($_REQUEST)); 		
			if($_REQUEST['txn_type']=='subscr_cancel' || $_REQUEST['txn_type']=='subscr_payment'){
				return false;
			}							
			/*Extract custom variable of paypal*/				
			if($_REQUEST['txn_type']=='subscr_cancel' || $_REQUEST['txn_type']=='subscr_payment'){
				return false;
			}			
	
			//$_REQUEST['custom'] = "cid=170~amount=285.00~bcy=1~temp_id=41";
			if($_REQUEST['txn_id']){
				$pay_detail = $this->Payment->find('first',array('conditions'=>array('Payment.txn_id'=>$_REQUEST['txn_id'])));
				if(empty($pay_detail)){
					$client = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$supplier_id)));
					$data['FeedbackRequest']['supplier_id']=$supplier_id;
					$pay['Payment']['supplier_id']=$supplier_id;
					
					/*Extract custom variable of paypal*/
					$custom_field = explode('~',$_REQUEST['custom']);		
					
								
					
					foreach ($custom_field as $param) {
						$item = explode('=', $param);
						$item_name=$item[0];
						$item_data=$item[1];
						if($item_name!='sid' && $item_name!='isresent' && $item_name!='new_eb' && $item_name!='req_id' && $item_name!='snbex' && $item_name!='r_type' && $item_name!='seidacreq'){				
							$item_data=json_encode(explode('-',$item_data));
						}
						$custom_variable[$item_name] = $item_data;				
					}	
	
		if(!empty($custom_variable['seidacreq'])){
								$all_eb_req_list=explode('@',$custom_variable['seidacreq']);
								unset($all_eb_req_list[0]);	
								$all_eb_req=array();
								foreach($all_eb_req_list  as   $_all_eb_req_list){	
									$base_arr=explode('<',$_all_eb_req_list);
									$all_eb_req[$base_arr[0]]=explode('-',$base_arr[1]);	
			
									}
			
			}	
	
					
				//	echo '<pre>';print_r($custom_variable);die;
					$payment_date=date("Y-m-d H:i:s",strtotime($_REQUEST['payment_date']));
					$pay['Payment']['payment_status']=$_REQUEST['payment_status'];
					$pay['Payment']['txn_id']=$_REQUEST['txn_id'];
					$pay['Payment']['payer_id']=$_REQUEST['payer_id'];
					$pay['Payment']['payer_email']=$_REQUEST['payer_email'];
					$pay['Payment']['amount']=$_REQUEST['mc_gross'];
					//$pay['Payment']['amount']=$_REQUEST['payment_gross'];
					$pay['Payment']['quantity']=$_REQUEST['quantity'];			
					$pay['Payment']['item_name']=$_REQUEST['item_name'];
					$pay['Payment']['created_date']=$payment_date;	
						
					if(!empty($custom_variable['isresent'])){
						$data['FeedbackRequest']['is_resent']=$custom_variable['isresent'];
					} 
					if(!empty($custom_variable['snb'])){
						$nb_zero_set=json_decode($custom_variable['snb']);
						$nb_array=array();
						foreach($nb_zero_set as $nb_zero){
							$nb_array[$nb_zero]=0;
						}
						$data['FeedbackRequest']['new_buyers']=$custom_variable['snb'];
						$data['FeedbackRequest']['is_report_sent']=json_encode($nb_array);
					}
					
					
					if(!empty($custom_variable['seb'])){
						$data['FeedbackRequest']['existing_buyers']=$custom_variable['seb'];
					}
					
						if(!empty($custom_variable['snbex'])){
								$all_nb_ex_list=explode('@',$custom_variable['snbex']);
								unset($all_nb_ex_list[0]);	
								$nb_ex_json=array();
								foreach($all_nb_ex_list  as   $_all_nb_ex_list){	
									$base_arr=explode('<',$_all_nb_ex_list);
									$nb_ex_json[$base_arr[0]]=explode('-',$base_arr[1]);	
			
									}
							$nb_ex_js=json_encode($nb_ex_json);	
							
						$data['FeedbackRequest']['selected_new_b_exist']=$nb_ex_js;	
						}			
					
					if(!empty($custom_variable['sqlist'])){
						$data['FeedbackRequest']['questions']=$custom_variable['sqlist'];
					}
								
					$data['FeedbackRequest']['request_status']=1;
					$data['FeedbackRequest']['created_date']=$payment_date;
					$descriptive_questions=$this->Question->find('all',array('conditions'=>array('Question.is_descriptive'=>1),'fields'=>'Question.id'));
					
					foreach($descriptive_questions as $desc){
							$q[]=$desc['Question']['id'];
					}
					$data['FeedbackRequest']['descriptive_ques']=json_encode($q);
					
					if(!empty($custom_variable['req_id'])){
						$data['FeedbackRequest']['id']=$custom_variable['req_id'];
						$FeedbackRequest=$this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$data['FeedbackRequest']['id']),'fields'=>'FeedbackRequest.request_status'));
						$data['FeedbackRequest']['request_status']=$FeedbackRequest['FeedbackRequest']['request_status'];
						$data['FeedbackRequest']['resend_date']=$payment_date;
					}
					
					if($custom_variable['r_type']=='use_feed'){
						$data['FeedbackRequest']['request_use']=1;
					}
					if($custom_variable['r_type']=='use_some_feed'){
						$data['FeedbackRequest']['request_use']=2;
					}
						
					//print_r();die;
					//	echo '<pre>';print_r($custom_variable);	echo '<br>';print_r($data);die;
					
					
if($custom_variable['r_type']=='replace_eb'){

			$all_ex_id=json_decode($custom_variable['exid']);
		foreach($all_ex_id  as  $_all_ex_id){

					$this->EbLoginDetail->deleteAll(array('EbLoginDetail.existing_buyer_id'=>$_all_ex_id,'EbLoginDetail.eb_status'=>2,'EbLoginDetail.is_link_expire'=>1));
		}
	
	
	
	
		
$all_req_id=json_decode($custom_variable['srid']);
			
foreach($all_req_id as  $_all_req_id){
				
					$feedback = $this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$_all_req_id)));	
				
					$feedbacktemp = $this->FeedbackTemp->find('first',array('conditions'=>array('FeedbackTemp.request_id'=>$_all_req_id)));	
	if(!empty($feedbacktemp)){
					$feedback['FeedbackRequest']['id']=$_all_req_id;	
					$feedback['FeedbackRequest']['selected_new_b_exist']=$feedbacktemp['FeedbackTemp']['selected_new_b_exist'];
					$feedback['FeedbackRequest']['existing_buyers']=$feedbacktemp['FeedbackTemp']['existing_buyers'];
					$feedback['FeedbackRequest']['request_use']=3;
					$feedback['FeedbackRequest']['updated_date']=date("Y-m-d H:i:s",strtotime($_REQUEST['payment_date']));
					$feedback['FeedbackRequest']['created_date']=date("Y-m-d H:i:s",strtotime($_REQUEST['payment_date']));
					$this->FeedbackRequest->create();
					$this->FeedbackRequest->save($feedback,array('validate'=>false));
				//	$feedback_id = $this->FeedbackRequest->id;		
					
					$this->FeedbackTemp->deleteAll(array('FeedbackTemp.request_id'=>$_all_req_id));
				}
			}
									
		} else {	
					
					
					$this->FeedbackRequest->create();
					$this->FeedbackRequest->save($data,array('validate'=>false));
					$feedback_id = $this->FeedbackRequest->id;
				}
				
			if($custom_variable['r_type']=='replace_eb')  {			
			
				$feedback_id=implode(',',$all_req_id);
				
			}	
					
					$pay['Payment']['feedback_request_id']=$feedback_id;
					$this->Payment->create();
					$this->Payment->save($pay,array('validate'=>false));
					$payment_id=$this->Payment->id;
					if(!empty($client)){							
						$options = array();	     
						$options['replacement'] = array('{NAME}'=>$client['Supplier']['first_name']." ".$client['Supplier']['middle_name']." ".$client['Supplier']['last_name'],'{TXN}'=>$_REQUEST['txn_id'],'{PAYMENTSTATUS}'=>$_REQUEST['payment_status'],'{AMOUNT}'=>$_REQUEST['mc_gross'],'{QUANTITY}'=>$_REQUEST['quantity'],'{MODE}'=>'Paypal');				
						$options['to'] = $client['Supplier']['email_id']; 
						$options['from'] = $this->System->get_setting('site','site_contact_noreply');			
						$this->MyMail->SendMail(23,$options);
						
						$options2 = array();	     
						$options2['replacement'] = array('{NAME}'=>$client['Supplier']['first_name']." ".$client['Supplier']['middle_name']." ".$client['Supplier']['last_name'],'{EMAIL}'=>$client['Supplier']['email_id'],'{TXN}'=>$_REQUEST['txn_id'],'{PAYEREMAIL}'=>$_REQUEST['payer_email'],'{PAYMENTSTATUS}'=>$_REQUEST['payment_status'],'{AMOUNT}'=>$_REQUEST['mc_gross'],'{QUANTITY}'=>$_REQUEST['quantity'],'{MODE}'=>'Paypal');				
						$options2['to'] = $this->System->get_setting('site','site_contact_email'); 
						$options2['from'] = $this->System->get_setting('site','site_contact_noreply');			
						$this->MyMail->SendMail(24,$options2);
						
						$p_id = $this->Payment->find('first',array('conditions'=>array('Payment.id'=>$payment_id)));
					
						//$fr = $this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.payment_id'=>$p_id['Payment']['id'])));
					
						//$fr_id=$fr['FeedbackRequest']['id'];
						$fr_id=$p_id['Payment']['feedback_request_id'];
				
				
						
	    	if($custom_variable['r_type']=='use_feed'){
							
							$all_sb=explode('-',$custom_variable['seb']);				
								foreach($all_sb as $all_selected_eb){
									$all_selected_eb=json_decode($all_selected_eb);
									if(is_array($all_selected_eb)){
										foreach($all_selected_eb as $all_selected_e){
											$this->existing_send_mail_feed($all_selected_e,$fr_id);
										}							
									}else{
										$this->existing_send_mail_feed($all_selected_eb,$fr_id);
									}
								}
							
							
			} else if($custom_variable['r_type']=='use_some_feed'){
														
							$all_sb=explode('-',$custom_variable['sueb']);				
								foreach($all_sb as $all_selected_eb){
									$all_selected_eb=json_decode($all_selected_eb);
									if(is_array($all_selected_eb)){
										foreach($all_selected_eb as $all_selected_e){
											$this->existing_send_mail_feed($all_selected_e,$fr_id);
										}							
									}else{
										$this->existing_send_mail_feed($all_selected_eb,$fr_id);
									}
								}	
						
						
								
						
							$all_sb=explode('-',$custom_variable['sureb']);				
								foreach($all_sb as $all_selected_eb){
										$all_selected_eb=json_decode($all_selected_eb);
										if(is_array($all_selected_eb)){
											foreach($all_selected_eb as $all_selected_e){
												$this->existing_send_mail($all_selected_e,$fr_id);
											}							
										}else{
												$this->existing_send_mail($all_selected_eb,$fr_id);
										}
								 }
													
			} else if($custom_variable['r_type']=='normal')  {			
							
						if(!empty($custom_variable['seb'])){		
							if(!empty($custom_variable['new_eb'])){		
								$old_eb=$custom_variable['old_eb'];							
								$custom_variable['send_again']=json_decode($custom_variable['send_again'],true);	
								if(!empty($custom_variable['send_again'][0])){	
									//echo 11;die;					
									$this->existing_send_mail($old_eb,$fr_id);
								}else{	
									//echo $custom_variable['new_eb'].'<br>';							
									//echo $fr_id.'<br>';									
									//echo $old_eb.'<br>';die;									
									$this->existing_send_mail($custom_variable['new_eb'],$fr_id,$old_eb);
								}	
							}else{
								$all_sb=explode('-',$custom_variable['seb']);				
								foreach($all_sb as $all_selected_eb){
									$all_selected_eb=json_decode($all_selected_eb);
									if(is_array($all_selected_eb)){
										foreach($all_selected_eb as $all_selected_e){
											$this->existing_send_mail($all_selected_e,$fr_id);
										}							
									}else{
										$this->existing_send_mail($all_selected_eb,$fr_id);
									}
								}
							}
						}
		} else if($custom_variable['r_type']=='replace_eb')  {		
					
					
					$send_eb=array();		
						foreach($all_eb_req as $req=>$_new_eid){			
							if(is_array($_new_eid)){
										foreach($_new_eid as $all_selected_e){
											
											
											if(!in_array($all_selected_e,$send_eb)){
											$this->existing_send_mail($all_selected_e,$req);
											}
											$send_eb[]=$all_selected_e;
											
										}							
									}else{
										$this->existing_send_mail($all_selected_eb,$req);
									}						
							
						}			
			}
		
					
					
					
						$this->MemberAuth->updateMemberSession($client['Supplier']);
						$this->MemberAuth->updateMemberType(1);
						$active_user =$this->MemberAuth->get_active_member_detail();				
						$this->Session->delete('Request');  
						if($client['Supplier']['process_step']==3){					
							$data1['Supplier']['id']=$client['Supplier']['id'];
							$data1['Supplier']['process_step']=5;	
							$this->Supplier->create();
							$this->Supplier->save($data1,array('validate'=>false));
						}
						
						
							$sup['SupplierBuyer']['supplier_id']=$this->request->data['Supplier']['s_id'];
							$this->SupplierBuyer->create();
							$this->SupplierBuyer->save($sup,array('validate'=>false));
						
							$this->SupplierBuyer->updateAll(array('SupplierBuyer.round' =>1),array('SupplierBuyer.supplier_id'=>$supplier_id));
						
						
							$this->ExistingBuyer->updateAll(array('ExistingBuyer.replace' =>0),array('ExistingBuyer.supplier_id'=>$supplier_id));
						
						
									
						if($client['Supplier']['status']==1 && !empty($active_user)){
							$this->Session->setFlash(__('Your payment received successfully and feedback request has been sent, please check your mailbox.'),'default',array(),'success');
							$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'payment_success'));
						}	
				
					}else{
						mail("jakegyl21@gmail.com","process_ipn",'error-txn-'.$_REQUEST['txn_id']);			
					}
				}
			}
		}
    }
    
    public function payment_success(){
		$loguser_id = self::_check_member_login();
		$process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$loguser_id),'fields'=>array('Supplier.process_step')));		
		$page = $this->__load_page(67);
		$this->set('page', $page);	
		$this->set('title', "Payment Successful");	
		$this->set('process_step',$process_step['Supplier']['process_step']);
	}
	 public function resend_success(){
		$loguser_id = self::_check_member_login();
		$process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$loguser_id),'fields'=>array('Supplier.process_step')));		
		$page = $this->__load_page(70);
		$this->set('page', $page);	
		$this->set('title', "Resend Successful");	
		$this->set('process_step',$process_step['Supplier']['process_step']);
	}
	public function send_success(){
		$loguser_id = self::_check_member_login();
		$process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$loguser_id),'fields'=>array('Supplier.process_step')));		
		$page = $this->__load_page(71);
		$this->set('page', $page);	
		$this->set('title', "Feedback Send Successful");	
		$this->set('process_step',$process_step['Supplier']['process_step']);
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
	
	public function existing_send_mail($id=null,$request_id=null,$old_eb=null){
		$this->autoRender = false;
		// $loguser =$this->MemberAuth->get_active_member_detail();	
		$id=json_decode($id);
		if(is_array($id)){
			$id=$id[0];
		}	
		if(!empty($old_eb)){
			$old_eb=json_decode($old_eb);
			if(is_array($old_eb)){
				$old_eb=$old_eb[0];
			}
		}
	//	mail("jakegyl21@gmail.com","process_ipn",$id.'-'.$request_id);		
		if($id!=null){
			//$this->FeedbackRequest->bindModel(array('belongsTo' => array('Supplier')));
			//$loguser = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$supplier_id)));
			
			$user = $this->ExistingBuyer->find('first',array('conditions'=>array('ExistingBuyer.id'=>$id)));
			
			$randompassword=self::_randomPassword();		
			$pass = Security::hash(Configure::read('Security.salt').$randompassword);
			
			if(!empty($user)){
				$feedback = $this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$request_id)));
				
				$eb_detail=$this->EbLoginDetail->find('first',array('conditions'=>array('EbLoginDetail.request_id'=>$request_id,'EbLoginDetail.existing_buyer_id'=>$id,'NOT'=>array('EbLoginDetail.resend_date'=>''))));
				//echo '<pre>';print_r($eb_detail);die;
				
				$name = ucfirst($user['ExistingBuyer']['first_name']).' '.ucfirst($user['ExistingBuyer']['last_name']);
				$email = $user['ExistingBuyer']['email_id'];
				$urlValue=md5($this->_randomString());
				$supplier_name = $feedback['Supplier']['first_name']." ".$feedback['Supplier']['last_name'];
									
				$url=Router::url(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'login',$request_id,$id,$urlValue),true);	
							
				//$url = base64_encode($url);
				$options = array();
				$options['replacement'] = array('{NAME}'=>$name,'{SUPPLIER}'=>$supplier_name,'{URL}'=>$url ,'{EMAIL}'=>$email);
				$options['to'] = array($email); 
				//print_r($options);die;
				$this->MyMail->SendMail(21,$options);
				
				$options = array();
				$options['replacement'] = array('{NAME}'=>$name,'{SUPPLIER}'=>$supplier_name,'{PASSWORD}'=>$randompassword);
				$options['to'] = array($email); 
				$this->MyMail->SendMail(22,$options);
				
				$login_details = array();
				$link_expire_date = '';		
				//$feedback=$this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$request_id)));
			
				$expDate = strtotime($feedback["FeedbackRequest"]["created_date"].' + 2 week');				
				$expDate=date('Y-m-d H:i:s',$expDate);				
				$payment_date = $feedback['FeedbackRequest']['created_date'];
			
				if(!empty($eb_detail)){
					$login_details['EbLoginDetail']['id'] = $eb_detail['EbLoginDetail']['id'];				
					$login_details['EbLoginDetail']['resend_date'] ='';
				}
				$login_details['EbLoginDetail']['eb_status'] = 1;
				$login_details['EbLoginDetail']['link_expire_date'] = $expDate;
				$login_details['EbLoginDetail']['payment_date'] = $payment_date;
				$login_details['EbLoginDetail']['is_link_expire'] = 0;
				$login_details['EbLoginDetail']['request_id']= $request_id;
				$login_details['EbLoginDetail']['existing_buyer_id']= $id;
				$login_details['EbLoginDetail']['password']= $pass;
				$login_details['EbLoginDetail']['passwordurl'] =  $urlValue;
				
			//	echo '<pre>';print_r($login_details);echo '<br>';
				
				$this->EbLoginDetail->create();
				$this->EbLoginDetail->save($login_details,array('validate'=>false));
				//print_r($login_details);echo '<br>';
				if(!empty($old_eb)){
					$this->EbLoginDetail->updateAll(array('EbLoginDetail.is_link_expire' =>3,'EbLoginDetail.eb_status' =>7),array('EbLoginDetail.request_id' => $request_id,'EbLoginDetail.existing_buyer_id' =>$old_eb));
				}
				
			}
		}
	} 
	 
	public function existing_send_mail_feed($id=null,$request_id=null,$old_eb=null){
		$this->autoRender = false;
		// $loguser =$this->MemberAuth->get_active_member_detail();	
		$id=json_decode($id);
		if(is_array($id)){
			$id=$id[0];
		}	
		if(!empty($old_eb)){
			$old_eb=json_decode($old_eb);
			if(is_array($old_eb)){
				$old_eb=$old_eb[0];
			}
		}
	//	mail("jakegyl21@gmail.com","process_ipn",$id.'-'.$request_id);		
		if($id!=null){
			//$this->FeedbackRequest->bindModel(array('belongsTo' => array('Supplier')));
			//$loguser = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$supplier_id)));
			
			$user = $this->ExistingBuyer->find('first',array('conditions'=>array('ExistingBuyer.id'=>$id)));
			
			if(!empty($user)){
				$feedback = $this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$request_id)));
				
				$eb_detail=$this->EbLoginDetail->find('first',array('conditions'=>array('EbLoginDetail.request_id'=>$request_id,'EbLoginDetail.existing_buyer_id'=>$id,'NOT'=>array('EbLoginDetail.resend_date'=>''))));
				//echo '<pre>';print_r($eb_detail);die;
				
			//	$name = ucfirst($user['ExistingBuyer']['first_name']).' '.ucfirst($user['ExistingBuyer']['last_name']);
				
			//	$email = $user['ExistingBuyer']['email_id'];
		
		
			//	$supplier_name = $feedback['Supplier']['first_name']." ".$feedback['Supplier']['last_name'];
									
							
			//	$link_expire_date = '';		
				//$feedback=$this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$request_id)));
			
			//	$expDate = strtotime($feedback["FeedbackRequest"]["created_date"].' + 2 week');				
			//	$expDate=date('Y-m-d H:i:s',$expDate);				
			
				$payment_date = $feedback['FeedbackRequest']['created_date'];
			
				if(!empty($eb_detail)){
					$login_details['EbLoginDetail']['id'] = $eb_detail['EbLoginDetail']['id'];				
					$login_details['EbLoginDetail']['resend_date'] ='';
				}
				$login_details['EbLoginDetail']['eb_status'] = 6;
				$login_details['EbLoginDetail']['link_expire_date'] = '';
				$login_details['EbLoginDetail']['payment_date'] = $payment_date;
				$login_details['EbLoginDetail']['is_link_expire'] = 1;
				$login_details['EbLoginDetail']['request_id']= $request_id;
				$login_details['EbLoginDetail']['existing_buyer_id']= $id;
				$login_details['EbLoginDetail']['password']= '';
				$login_details['EbLoginDetail']['passwordurl'] = '';
				
			//	echo '<pre>';print_r($login_details);echo '<br>';
				
				$this->EbLoginDetail->create();
				$this->EbLoginDetail->save($login_details,array('validate'=>false));
				//print_r($login_details);echo '<br>';
				if(!empty($old_eb)){
					$this->EbLoginDetail->updateAll(array('EbLoginDetail.is_link_expire' =>3,'EbLoginDetail.eb_status' =>7),array('EbLoginDetail.request_id' => $request_id,'EbLoginDetail.existing_buyer_id' =>$old_eb));
				}
				
			}
		}
	} 
	public function card_detail($request_id=null) {
		if(!$this->Session->read('Request')){
			$this->Session->setFlash(__('Make a request here, then make payment.','default',array(),'error'));
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'assign_existing_buyer'));
			
		}
		$request = $this->Session->read('Request');	
		$loguser_id = self::_check_member_login();
		$loguser =$this->MemberAuth->get_active_member_detail();
		$process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$loguser_id),'fields'=>array('Supplier.process_step')));				
		$question_arr=array();
		$date=array();
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_seo('site_title','Save Card Details');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		$this->request->data = $this->Supplier->read(null,$loguser_id);
		if($this->request->data['Supplier']['exp_date']){
			$date=explode('/',$this->request->data['Supplier']['exp_date']);		
			$this->request->data['Supplier']['exp_month']=$date[0];
			$this->request->data['Supplier']['exp_year']=$date[1];
		}
		$this->set('supplier_id', $loguser_id);	
		if($process_step['Supplier']['process_step'] == 3){
			$process_step['Supplier']['process_step'] =4;
		}
		
		
		$price=$this->System->get_setting('site','site_payment_fee');		
		$feedb_data=$request;
		$s_nb2=$feedb_data['selected_nb'];
		$s_eb=json_decode($feedb_data['selected_eb']);
		
		$all_nb_list=json_decode($s_nb2);
	//	echo "<pre>"; print_r($s_eb); die;
		$required_list=array();
		foreach($all_nb_list as $list){
			$sp=$this->NewBuyer->find('first', array('conditions'=>array('id'=>$list),'fields'=>array('required_feedback')));			
			$required_list[]=$sp['NewBuyer']['required_feedback'];
		}
		
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$loguser_id,'SupplierBuyer.buyer_id'=>$all_nb_list),'order'=>array('SupplierBuyer.id'=>'DESC')));	
		
		//echo "<pre>"; print_r($buyer_exist); die;
		
				
		$max_req_num=max($required_list);
		$all_nb_count=count($all_nb_list);
		$price=$all_nb_count*$price;
		
	
		$this->set('process_step', $process_step['Supplier']['process_step']);	
		$this->set('price', $price);	
		$this->set('buyer_exist', $buyer_exist);	
		$this->set('request', $request);	
		//echo $process_step['Supplier']['process_step']; die;
	  }
	  
	  
	  public function check_eb($id=null) {
		 $this->autoRender = false;				
		if(!empty($this->request->data['nb_id'])){
			//print_r($this->request->data); die;
			foreach($this->request->data['nb_id'] as $nb_id){
				$r_feed='';
				//echo $nb_id;
				$r_feed=$this->NewBuyer->find('first', array('conditions'=>array('NewBuyer.id'=>$nb_id,'NewBuyer.status'=>1),'fields' => array('NewBuyer.required_feedback')));
				if(!empty($r_feed)){
					$count[]=$r_feed['NewBuyer']['required_feedback'];
				}
			}			
			$required = max($count);
		}
		$loguser_id = self::_check_member_login();	
		$eb = $this->ExistingBuyer->find('all', array('conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'status'=>1)));
		$total_exist = count($eb);
	
		if($total_exist>=$required){
			$eb_count['success']=1;
			$eb_count['req']=$required;
		}else{
			$r=$required-$total_exist;
			$eb_count['error']=1;
			$eb_count['msg']='You have less existing buyer added than required. Add '.$r.' more existing buyer(s) to complete the process.';
		}
		//print_r($eb_count);die;
		return json_encode($eb_count);
	  }
	
	 function __load_page($id=null){
		 		
		$this->loadModel('ContentManager.Page');
		$page = $this->Page->find('first',array('conditions'=>array('Page.id'=>$id,'Page.status'=>1)));
		if (empty($page)) {
			throw new NotFoundException('404 Error - Page not found');
		}
		//print_r($page);die;
		$this->System->set_seo('site_title',$page['Page']['page_title']);
		$this->System->set_seo('site_metakeyword',$page['Page']['page_metakeyword']);
		$this->System->set_seo('site_metadescription',$page['Page']['page_metadescription']);
		if((int)Configure::read('Section.default_banner_image') && ($page['Page']['use_default_image'] || $this->System->get_setting('page','override_banner_image'))){
			$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		}
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		
		return $page;
	}
	
	private function __registration_mail_send($mail_data,$password=null)
	{
		$linkmerge=Configure::read('Site.url').'/supplier_manager/suppliers/active_account/'.$mail_data['Supplier']['passwordurl'];
		//mail to user
		$options = array();
		$options['replacement'] = array(
		'{NAME}'=>$this->request->data['Supplier']['title']." ".$this->request->data['Supplier']['first_name']." ".$this->request->data['Supplier']['middle_name']." ".$this->request->data['Supplier']['last_name'],'{url}'=>$linkmerge);			
		$options['to'] = array($this->request->data['Supplier']['email_id']); 		
		$this->MyMail->SendMail(14,$options);
		
		//mail to admin
		$options = array();	
		$options['replacement'] = array('{NAME}'=>$this->request->data['Supplier']['title']." ".$this->request->data['Supplier']['first_name']." ".$this->request->data['Supplier']['middle_name']." ".$this->request->data['Supplier']['last_name'],'{ADDRESS}'=>$this->request->data['Supplier']['address1']." ".$this->request->data['Supplier']['address2'],'{CITY}'=>$this->request->data['Supplier']['city'],'{STATE}'=>$this->request->data['Supplier']['state'],'{ZIP}'=>$this->request->data['Supplier']['zipcode'],'{COUNTRY}'=>$this->request->data['Supplier']['country'],'{EMAIL}'=>$this->request->data['Supplier']['email_id']);		
		$options['to'] = $this->System->get_setting('site','site_contact_email'); 
		$options['from'] = $this->System->get_setting('site','site_contact_noreply');	
		$this->MyMail->SendMail(15,$options);
	}
	
	function RandomString() {
		$characters = '$&@!0123456789abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randstring = '';
		for ($i = 0; $i < 15; $i++) {
			$arr1 = str_split($characters);
			$randstring .= $arr1[rand(0, $i)];
		}
		return $randstring;
	}
	
	public function dashboard(){
		//$pDate = strtotime('2016-07-05 00:10:12 + 2 week');
		//echo date('Y-m-d H:i:s',$pDate);die;
		$loguser_id = self::_check_member_login();
		
/*====================GET SUPPLIER INFORMATION==========================*/	
		if(!empty($loguser_id)){		
			$active_supplier = $this->Session->read('supplier_email');
			$countries = $this->Country->country_list();
			$this->set('countries',$countries);
			
			$this->Supplier->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('Supplier.country = Country.country_code_char2')))));
			$supplier_info = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$loguser_id)));	
			//print_r($supplier_info);die;		
			$this->set('supplier_info',$supplier_info);
		}
/*====================GET SUPPLIER INFORMATION==========================*/		
		
		

/*====================GET NEW BUYER==========================*/
		$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));	
		$buyer_exist = $this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$loguser_id), 'order'=>array('SupplierBuyer.id'=>'DESC')));	
		//echo "<pre>"; print_r($buyer_exist);  die;	
	//	$buyer_exist='';
		$this->set('existing_b', $buyer_exist);
/*====================GET NEW BUYER==========================*/
	
	
		
/*================GET EXISTING BUYER===============*/
			$eb = $this->ExistingBuyer->find('all', array('conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'status'=>1)));
			$this->set('ex_b', $eb);
/*================GET EXISTING BUYER===============*/





/*================GET FEEDBACK===============*/
		$eb_list = self::_check_expiry();
		$this->FeedbackRequest->bindModel(array('hasMany' => array('Payment'=>array('order' => 'Payment.id DESC'))));
		$this->paginate['FeedbackRequest'] = array(
		  'conditions'=>array('FeedbackRequest.supplier_id'=>$loguser_id),
		  'order' => array('FeedbackRequest.id'=>'DESC'),
		  'limit' => 5,		 
		);
		$requests = $this->paginate('FeedbackRequest');	
			
   // echo "<pre>"; print_r($eb_list);  die;	
  
  /*   foreach($eb_list as $_eb_list){
		$ss= $this->FeedbackRequest->find('all', array('conditions'=>array('FeedbackRequest.id'=>$_eb_list['EbLoginDetail']['request_id'],'status'=>1))); 	 
	 }	
  */
		$this->set('eb_lists', $eb_list);
	
		
		$this->set('requests', $requests);
/*================GET FEEDBACK===============*/




		
/*=============================PAYMENT---History=========================*/
		$this->paginate = array();
	//	$this->paginate['limit']=10;
		//$payments = array();
		//$condition=array();
		//$condition['Payment.supplier_id'] = $loguser_id;
	
		//$this->paginate['order']=array('Payment.id'=>'ASC');
		//$paymen= $results=$this->paginate("Payment",$condition);
		
		$payments = $this->Payment->find('all', array('conditions'=>array('Payment.supplier_id'=>$loguser_id),'order'=>array('Payment.id'=>'DESC')));	
	//	$payments='';
		$this->set('payments', $payments);
/*============================PAYMENT---History==========================*/		

			
		
		$page = $this->__load_page(52);
		$process_step = $this->__get_process_step($loguser_id);
		$this->set('page', $page);
	
		//$active_user_id = self::_check_member_login();
		$this->set('process_step', $process_step);
			$this->set('id', $loguser_id);
	}
	public function change_password(){
		
		$page = $this->__load_page(54);
		$this->set('page', $page);
		$title = "Change Password";
		$member_id = self::_check_member_login();
		$loguser =$this->MemberAuth->get_active_member_detail();
		$process_step = $this->__get_process_step($member_id);	
		$this->set('process_step',$process_step);
		$user = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$member_id)));
		if(!empty($user)){
			if(!empty($this->request->data)){		
			
				if(Security::hash(Configure::read('Security.salt').$this->request->data['Supplier']['old_password']) == $user['Supplier']['password']){
					if($this->request->data['Supplier']['password']==$this->request->data['Supplier']['confirm_pass']){
						$this->request->data['Supplier']['id']=$user['Supplier']['id'];
						$this->request->data['Supplier']['password']=Security::hash(Configure::read('Security.salt').$this->request->data['Supplier']['password']); 
						//$this->NewBuyer->create();
						//$this->NewBuyer->save($this->request->data);
						//echo "<pre>"; print_r($this->request->data); die;
						$this->Supplier->id = $user['Supplier']['id'];
						$this->Supplier->saveField('password', $this->request->data['Supplier']['password']);
						
							$this->Session->setFlash(__('Your Password has been changed successfully.'),'default',array(),'pass');
							$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'dashboard#ch_pass'));
						
					}else{
						//$this->Session->setFlash('Password and Confirm password does not match, Please try again','default','msg','error');
						$this->Session->setFlash(__('Password and Confirm password does not match, Please try again.'),'default',array(),'error');
						$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'dashboard'));
					}
				}else{
					//$this->Session->setFlash('You have entered incorrect current password, Please try again','default','msg','error');
					$this->Session->setFlash(__('You have entered incorrect current password, Please try again.'),'default',array(),'error');
					$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'dashboard'));
				}
			}
			$this->set('member_id',$member_id);
		}
		else{
			//$this->Session->setFlash('Did you really think you are allowed to see that?');
			$this->Session->setFlash(__('Did you really think you are allowed to see that?'),'default',array(),'error');
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'login'));
		}
		$this->set('title', $title);
		$this->set('id', $member_id);
	}
	
	public function forgot(){
		/*$loguser_id = self::_check_member_login();	
		if($loguser_id){
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'logout'));
		}*/
		
		$page = $this->__load_page(53);
		$this->set('page', $page);
	
		if(!empty($this->request->data)){
			$name ='';
			$username = '';
			$email = '';
			$user = array();
			$url = '';
			$user_type = '';
			$urlValue=md5($this->_randomString());		
			$user = $this->Supplier->find('first',array('conditions'=>array('Supplier.email_id'=>$this->request->data['Supplier']['email_id'])));
			if(!empty($user)){
				$name = ucfirst($user['Supplier']['first_name']).' '.ucfirst($user['Supplier']['last_name']);
				$email = $user['Supplier']['email_id'];
				unset($user['Supplier']['password']);
				$user['Supplier']['passwordurl'] =  $urlValue;
				$this->Supplier->create();
				$this->Supplier->save($user,array('validate'=>false));
			}		
			if(!empty($user)){								
				$url=Router::url(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'passwordurl',$user['Supplier']['passwordurl']),true);				
				$options = array();
				$options['replacement'] = array('{NAME}'=>$name,'{USERNAME}'=>$email,'{URL}'=>$url);
				$options['to'] = array($email); 
				$this->MyMail->SendMail(17,$options);
				$this->Session->setFlash(__('Mail with reset password link will be sent to '.$email.'. Please follow the instructions to reset your password.'),'default',array(),'success');
				$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'login'));
			}else{
				$this->Session->setFlash(__('We are sorry for the inconvenience, the email address you entered is not registered with us.'),'default',array(),'error');
			}
		}
	}
	
	function validation1($returnType = 'json'){
		$this->autoRender = false;
		if(!empty($this->request->data)){
			$result = array();
			if(!empty($this->request->data['Supplier']['form'])){
				$this->Supplier->setValidation($this->request->data['Supplier']['form']);
			}
			$this->Supplier->set($this->request->data);
			if($this->Supplier->validates()){
					$result['error'] = 0;
				}else{
					$result['error'] = 1;
					$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
				}
			$errors = array();
			$result['errors'] = $this->Supplier->validationErrors;
			foreach($result['errors'] as $field => $data){
			  $errors['Supplier'.Inflector::camelize($field)] = array_pop($data);
			}
			$result['errors'] = $errors;
			$view = new View();
			
			$result['error_message'] = $view->element('admin/message');
			echo json_encode($result);
			return;
		}
		echo json_encode(array());
	}
	
	public function passwordurl($str=null){		
		//$this->layout='Payroll';
		$page = $this->__load_page(53);
		$this->set('page', $page);
		$user = $this->Supplier->find('first',array('conditions'=>array('Supplier.passwordurl'=>$str)));
	
	if(!empty($user) && !empty($str)){
		
		if(!empty($this->request->data)){
        //print_r($this->request->data);die;				
					if($this->request->data['Supplier']['password']==$this->request->data['Supplier']['confirm_pass']){
						$this->request->data['Supplier']['id']=$user['Supplier']['id'];
						$this->request->data['Supplier']['passwordurl']='';
						$this->request->data['Supplier']['password']=Security::hash(Configure::read('Security.salt').$this->request->data['Supplier']['password']); 
						$this->Supplier->create();
						$this->Supplier->save($this->request->data);
						$this->Session->setFlash(__('Your Password has been changed successfully. Please Login.'),'default',array(),'success');
						$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'login'));
					}else{
						$this->Session->setFlash(__('Password and Confirm password does not match, Please try again.'),'default',array(),'error');
					}
				}
		
			$this->set('str',$str);
		}else{
			//echo 21;die;
			//throw new NotFoundException('404 Error - Client not found');
			//$this->Session->setFlash('Invalid link, try again','default','msg','error');
			$this->Session->setFlash(__('Invalid link, Please try again.'),'default',array(),'error');
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'login'));
			
		}
	}
	
	public function payment_history($id=null) {	 
		$loguser_id = self::_check_member_login();
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_seo('site_title','Payment History');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		$process_step = $this->__get_process_step($loguser_id);
		
		$trans_id=array();
		 
	if(!empty($this->request->data)){
		
		$exb=$this->request->data['eb_id'];	
			foreach($exb as $key=>$value){
								if($value == 0){ unset($exb[$key]); }
					}	
							  		
								
		$this->paginate = array();
		//$this->paginate['limit']=10;
		$condition=array();
		$condition['Payment.supplier_id'] = $loguser_id;
		$condition['Payment.id'] = $exb;
		$payments = array();
		$this->paginate['order']=array('Payment.id'=>'DESC');
		$payments= $results=$this->paginate("Payment",$condition);
		$pay_list=array();
		foreach($payments  as $_payment){
			
		$existing_buyers=json_decode($_payment['FeedbackRequest']['existing_buyers']);
		$existing_buyers_list=$this->ExistingBuyer->find('all',array('conditions'=>array('ExistingBuyer.id'=>$existing_buyers)));
			$_payment['Payment']['exist']= $existing_buyers_list;
			
		$new_buyers=json_decode($_payment['FeedbackRequest']['new_buyers']);
		//$new_buyers_list=$this->NewBuyer->find('all',array('conditions'=>array('NewBuyer.id'=>$new_buyers)));	
	
	
		$this->SupplierBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('NewBuyer.country = Country.country_code_char2')))));
	
		$new_buyers_list=$this->SupplierBuyer->find('all',array('conditions'=>array('SupplierBuyer.supplier_id'=>$loguser_id,'SupplierBuyer.buyer_id'=>$new_buyers),'order'=>array('SupplierBuyer.id'=>'DESC')));
		
			$_payment['Payment']['new_b']= $new_buyers_list;
			
			$pay_list[]=$_payment;
		}
		
		
	} 
		//echo "<pre>"; print_r($pay_list); die;
		  
		$this->set('payments',$pay_list);
		$this->set('process_step',$process_step);
	}
	
	public function payment_view($id=null) {
		
		$loguser_id = self::_check_member_login();
		$process_step = $this->__get_process_step($loguser_id);
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_seo('site_title','Payment View');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		
		$payment=$this->Payment->find('first',array('conditions'=>array('Payment.id'=>$id)));
		
		$existing_buyers=json_decode($payment['FeedbackRequest']['existing_buyers']);
		$existing_buyers_list=$this->ExistingBuyer->find('all',array('conditions'=>array('ExistingBuyer.id'=>$existing_buyers)));

		$new_buyers=json_decode($payment['FeedbackRequest']['new_buyers']);
		$new_buyers_list=$this->NewBuyer->find('all',array('conditions'=>array('NewBuyer.id'=>$new_buyers)));
		//print_r($new_buyers_list);die;
		$this->set('existing_buyers_list',$existing_buyers_list);
		$this->set('new_buyers_list',$new_buyers_list);
		$this->set('payment',$payment);
		$this->set('process_step',$process_step);
	}
	
	public function success($step=null){
		
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_seo('site_title','Success');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		
		if($step == 1){
			$message = 'Profile has been updated successfully.';
		}elseif($step == 2){
			$message = 'New buyer(s) has been added successfully.';
		}elseif($step == 3){
			$message = 'Existing Buyer(s) has been Added Successfully';
		}elseif($step == 4){
			$message = 'Continue to make payment.';
		}
		
		
		if($step == 1){
			$url = Router::url(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'add_new_buyer'));
		}elseif($step == 2){
			$url = Router::url(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'add_existing_buyer'));
		}elseif($step == 3){
			
			//$url = Router::url(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'make_request'));
			$url = Router::url(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'card_detail'));
			$this->redirect($url);
		}elseif($step == 4){
			$url = Router::url(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'card_detail'));
		}
		
		$this->set('message', $message);
		$this->set('process_step', $step);
		$this->set('url', $url);
	}
	
	public function edit_reference(){
		$this->autoRender = false;
		if(!empty($this->request->data)){
			$reference_no = $this->request->data['reference'];
			$id = $this->request->data['id'];
			$detail['SupplierBuyer']['id']= $id;
			$detail['SupplierBuyer']['reference_num']= $reference_no;
			
			$this->SupplierBuyer->create();
			$this->SupplierBuyer->save($detail,array('validate'=>false));
		}
	}
}
?>
