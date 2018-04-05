<?php
Class ExistingBuyersController extends ExistingBuyerManagerAppController{
	public $uses = array('ExistingBuyerManager.ExistingBuyer','Country','SupplierManager.SupplierBuyer','ExistingBuyerManager.EbLoginDetail','SupplierManager.FeedbackRequest','SupplierManager.FeedbackResponse','QuestionManager.Question','QuestionManager.QuestionCategorie','NewBuyerManager.NewBuyer','SupplierManager.Supplier','SupplierManager.Payment');
	public $components=array('Email','RequestHandler','Image');
	var $helpers = array('Captcha','Csv');
	public $paginate = array();
	public $id = null;
	public $template=null;
	
	public function beforeFilter() {
			parent::beforeFilter();			
			//$this->Auth->deny('add_existing_buyer');
		}
	
	function admin_index($search=null,$limit=10){
		$this->paginate = array();
		$condition = null;
		if($search=="_blank"){
			$search=null;
		}
		$this->ExistingBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('ExistingBuyer.country = Country.country_code_char2')))));
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
			$this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'index',$parent_id,$search,$limit));
		}
		if($search!=null){
			$search = urldecode($search);	
			$condition['OR'][]=array('ExistingBuyer.first_name like'=>'%'.$search.'%');
		}
		
		
		$suppliers = array();
		$this->paginate['order']=array('ExistingBuyer.id'=>'DESC');
		$existing_buyers = $results=$this->paginate("ExistingBuyer", $condition);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/existing_buyer_manager/existing_buyers'),
			'name'=>'Manage Existing Buyer'
		);
		
		$this->heading =  array("Manage","Existing Buyer");

		$this->set('existing_buyers',$existing_buyers);
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
				'url'=>Router::url('/admin/existing_buyer_manager/existing_buyers'),
				'name'=>'Manage Existing Buyer'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/existing_buyer_manager/existing_buyers/add/'.$id),
				'name'=>($id==null)?'Add Existing Buyer':'Update Existing Buyer'
		);
		if($id==null){
			$this->heading =  array("Add","Existing Buyer");
		}else{
			$this->heading =  array("Update","Existing Buyer");
		}
	
	$countries = $this->Country->find('list',array('fields' => array('Country.country_code_char2','Country.country_name'),'order'=>array('Country.country_name'=>'ASC')));
	
		if(!empty($this->request->data) && $this->validation()){
			
			if(!$id){
				$this->request->data['ExistingBuyer']['created_at']=date('Y-m-d H:i:s');
				
			}else{
				$this->request->data['ExistingBuyer']['updated_at']=date('Y-m-d H:i:s');
			}
			if(empty($this->request->data['ExistingBuyer']['id'])){
				if(isset($this->request->data['save']) && $this->request->data['save']=='Save'){
					$this->request->data['ExistingBuyer']['status'] = 1;
				}else{
					$this->request->data['ExistingBuyer']['status'] = 1;
				}
			}
				//echo "<pre>"; print_r(($this->request->data)); die;
			$this->ExistingBuyer->create();
			$this->ExistingBuyer->save($this->request->data,array('validate'=>false));
			$id = $this->ExistingBuyer->id;
			
			if ($this->request->data['ExistingBuyer']['id']) {
				$this->Session->setFlash(__('Record has been updated successfully'));
			} 
			else{
				$this->Session->setFlash(__('Record has been added successfully'));
			}
			$this->redirect(array('action'=>'add',$id,'?'=>array('back'=>$this->request->data['ExistingBuyer']['url_back_redirect'])));
		}else{
			if(!empty($this->request->data)){
				$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
			}
			
			if($id!=null){
				$this->request->data = $this->ExistingBuyer->read(null,$id);
			}else{
				$this->request->data = array();
			}
		}
		
		$referer_url = $this->request->query('back');
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/existing_buyer_manager/existing_buyers',true) :Controller::referer();
		
		}
		$this->set('referer_url',$referer_url);
		$this->set('id',$id);
		$this->set('countries',$countries);
	}
	
	private function __exist_buyer_count($member_id){
		$exist_value = $this->ExistingBuyer->find('list',array('fields'=>array('ExistingBuyer.id'),'conditions'=>array('ExistingBuyer.supplier_id'=>$member_id,'ExistingBuyer.status'=>1)));
		$exist_value=count($exist_value);
		return $exist_value;
	}
	
	private function __exist_buyer_response($member_id){
		
		$exist_id = $this->ExistingBuyer->find('list',array('fields'=>array('ExistingBuyer.id'),'conditions'=>array('ExistingBuyer.supplier_id'=>$member_id,'ExistingBuyer.status'=>1)));
		
		//$response_id = $this->FeedbackResponse->find('list',array('fields'=>array('FeedbackResponse.existing_buyer_id'),'conditions'=>array('FeedbackResponse.existing_buyer_id'=>$exist_id,'FeedbackResponse.response_status'=>2)));
		
		$response_id = $this->FeedbackResponse->find('list',array('fields'=>array('FeedbackResponse.existing_buyer_id'),'conditions'=>array('FeedbackResponse.existing_buyer_id'=>$exist_id)));
		
		$res_uni_id=array_unique($response_id);
	
		//echo "<pre>"; print_r($res_uni_id); die;
		
	//	echo "<pre>"; print_r($response_id); die;
		$exist_value=count($res_uni_id);
		return $exist_value;
	}
	
	private function __exist_buyer_pending($member_id){
		$check_expire = array();
		$record = array();
		
		$val = $this->FeedbackResponse->query("SELECT `existing_buyer_id` FROM `feedback_responses` c  INNER JOIN `existing_buyers`  ca  ON ca.`id` = c.`existing_buyer_id` WHERE c.`response_status` =\" 2 \" AND ca.`supplier_id` = \"$member_id \" LIMIT 5 ");
		       
       foreach ($val as $_value){
		   $record[] = $_value['c']['existing_buyer_id'];
		 }  
	 $feedback_submit =  count($record);  
	 $in_progress = $this->FeedbackRequest->find('all',array('conditions'=>array('FeedbackRequest.supplier_id'=>$member_id),'fields'=>array('id'))); 
	 
	foreach($in_progress as $getVal){
		$temp_allexpire = $this->EbLoginDetail->find('list',array('conditions'=>array('EbLoginDetail.request_id'=>$getVal['FeedbackRequest']['id'],'EbLoginDetail.eb_status !=' =>6,'NOT'=>array('EbLoginDetail.is_link_expire' =>array(1,3))),'limit' => 5));  
		  if(!empty($temp_allexpire)) {
			  foreach($temp_allexpire as $pending){
				   $check_expire[] = $pending;  
			  }
		     }
		}
		 
		$feedback_pending =  count($check_expire); 
		
		return $feedback_pending;
	}
		
	private function __exist_buyer_both($member_id){
		$member_id = $member_id;	
		$exist_complete = $this->__exist_buyer_response($member_id);
		$exist_process = $this->__exist_buyer_pending($member_id);
		$val = $exist_complete + $exist_process;
		return $val;
	}
	
	
	public function retrive_your_password($id =null){
		
	$this->autoRender = false;
	 if(!empty($id)){	
	 $existing_login= $this->EbLoginDetail->find('all',array('conditions'=>array('EbLoginDetail.existing_buyer_id'=> $id))); 	
	$randompassword=self::_randomPassword();		
	$pass = Security::hash(Configure::read('Security.salt').$randompassword);
	$name = $existing_login [0]['ExistingBuyer']['first_name']." ".$existing_login [0]['ExistingBuyer']['last_name'] ;
	$email= $existing_login [0]['ExistingBuyer']['email_id']; 
	$eid = $existing_login [0]['EbLoginDetail']['id']; 
	$re_id = $existing_login [0]['EbLoginDetail']['request_id']; 
	$p_url = $existing_login [0]['EbLoginDetail']['passwordurl']; 
	$expDate = strtotime(date('Y-m-d H:i:s').' + 2 week');				
	$expDate = date('Y-m-d H:i:s',$expDate);
	
	$url=Router::url(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'login',$re_id,$id,$p_url),true);
	
	 $this->EbLoginDetail->updateAll(array('EbLoginDetail.link_expire_date' =>"'$expDate'",'EbLoginDetail.password' =>"'$pass'"),array('EbLoginDetail.id' => $eid));
	
	  $options = array();
	  $options['replacement'] = array(
	  '{NAME}'=>$name,'{USERID}'=>$email, '{PASSWORD}'=>$randompassword,'{URL}'=>$url );
	  $options['to'] = array($email);  
	  $this->MyMail->SendMail(28,$options);
	  
	  $this->Session->setFlash(__('Your request submitted, Please check your email for find detail. '));
	  
	 $this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'login',$re_id,$id,$p_url));
		
	  }else{
		  //$this->redirect(array('plugin'=>'content_manager','controller'=>'pages','action'=>'view',37));
		  
		   $this->redirect('/contact-us');
		  
		  }
		
		}
		
	public function admin_reset_expire($id=null){
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/existing_buyer_manager/existing_buyers'),
			'name'=>'Manage Expire Date'
		);
		
		$this->heading =  array("Manage","Expire Date");
		
		$this->set('id', $id);
		
		$ex_details = $this->EbLoginDetail->find('all', array('conditions'=>array('EbLoginDetail.existing_buyer_id'=>$id)));
		
		 $this->set('details', $ex_details);
		 
		 $back_url ='/admin/existing_buyer_manager/existing_buyers';
		 
		 $this->set('back_url', $back_url);
		 
		 if(!empty($this->request->data))
		 {
			$uid = $this->request->data['ExistingBuyer']['id'];
			
			$originalDate = $this->request->data['ExistingBuyer']['link_expire_date'];
            $newDate = date("Y-m-d", strtotime($originalDate));
			 
		    $format = 'Y-m-d';
            $date = DateTime::createFromFormat($format, $newDate);
            $var = $date->format('Y-m-d H:i:s'); 
			
		    $this->EbLoginDetail->updateAll(array('EbLoginDetail.link_expire_date' =>"'$var'"),array('EbLoginDetail.existing_buyer_id' =>$uid));
		    
			$this->Session->setFlash(__('Record has been reset successfully'));   
			
			$this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'index'));
			
			 
			 }
		
		}	
	
	
	private function __check_eb($id=null) {	
		
		$process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$id),'fields'=>array('Supplier.process_step')));
		
		if($process_step['Supplier']['process_step'] == 5){
		
		//$new_buyers = $this->SupplierBuyer->find('list', array('conditions'=>array('SupplierBuyer.supplier_id'=>$id,'SupplierBuyer.round'=>2),'fields' => array('SupplierBuyer.buyer_id')));
				
		$new_buyers = $this->SupplierBuyer->find('list', array('conditions'=>array('SupplierBuyer.supplier_id'=>$id,'SupplierBuyer.round'=>array(1,2),'SupplierBuyer.reference_num IS  NULL','SupplierBuyer.category IS  NULL'),'fields' => array('SupplierBuyer.buyer_id')));		
		}  else  {      
		$new_buyers = $this->SupplierBuyer->find('list', array('conditions'=>array('SupplierBuyer.supplier_id'=>$id,'SupplierBuyer.reference_num IS  NULL','SupplierBuyer.category IS  NULL'),'fields' => array('SupplierBuyer.buyer_id')));	
		}
		if(!empty($new_buyers)){
			foreach($new_buyers as $key=>$nb_id){
				$r_feed='';
				$r_feed=$this->NewBuyer->find('first', array('conditions'=>array('NewBuyer.id'=>$nb_id,'NewBuyer.status'=>1),'fields' => array('NewBuyer.required_feedback')));
				if(!empty($r_feed)){
					$count[]=$r_feed['NewBuyer']['required_feedback'];
				}
			}	
			
					
			$required = max($count);
		}
		
		//echo $required; exit;
		return $required;
		
	  }
	
	  public function add_existing_buyer($nb_button1=null,$nb_button2=null) {
		  
		  $member_id = self::_check_member_login();
		  $loguser =$this->MemberAuth->get_active_member_detail();
		  $required_feedback = $this->__check_eb($member_id);
		  
		  $process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$member_id),'fields'=>array('Supplier.process_step')));
		  
		  $countries = $this->Country->country_list();
		  $page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
			
			if($process_step['Supplier']['process_step'] < 5){
				
				$this->set('required_feedback',  $required_feedback);
			}
			
			if(!empty($this->request->data['ExistingBuyer']) && $this->validation())
			{ 
			
				$eb_loop=1;
				foreach($this->request->data['ExistingBuyer'] as $_ex_buyer){	
					if($eb_loop!=1 && $eb_loop!=2){			
					$_eb_buyer[]['ExistingBuyer']=$_ex_buyer;
				}
				$eb_loop++;
				}
		
			//echo "<pre>"; print_r($_eb_buyer); die;
			
	/*   validate from controller of dynamic form field  start */		
			$i=1;
		    $erro = array();
			 foreach($_eb_buyer as $all_eb){
				 
			  foreach($all_eb['ExistingBuyer'] as $key=> $e_buyer ){
				   
				if($key == 'title'){
					 if((empty($e_buyer))){
						 
						$erro[$i][$key] = 'Please select title for existing buyer '.$i;
					          }  
					  }
					  
				 if($key == 'first_name') {
					if((empty($e_buyer)) || (strlen($e_buyer) > 20)){
						  
						 $erro[$i][$key] = 'Please enter first name for existing buyer '.$i;
					            } 
					 
					 }
					 
					 
				 if($key == 'last_name') {
					if((empty($e_buyer)) || (strlen($e_buyer) > 20)){
						  
						 $erro[$i][$key] = 'Please enter last name for existing buyer '.$i;
					            } 
					 
					}		 	  
				 	
				 if($key == 'job_title'){
					if((empty($e_buyer)) || (strlen($e_buyer) > 30)){
						  
						 $erro[$i][$key] = 'Please enter job title for existing buyer '.$i;
					            } 
					 
					}
					
				if($key == 'org_name'){
					if((empty($e_buyer)) || (strlen($e_buyer) > 50)){
						  
						 $erro[$i][$key] = 'Please enter organization name for existing buyer '.$i;
					            } 
					 
					}
					
			  if($key == 'address1'){
					if((empty($e_buyer))){
						  
						 $erro[$i][$key] = 'Please enter address for existing buyer '.$i;
					            } 
					}
					
			if($key == 'city') {
					if((empty($e_buyer))){
						  
						 $erro[$i][$key] = 'Please enter city for existing buyer '.$i;
					            } 
					 
					}	
							
		if($key == 'zipcode'){
			
		    // if((empty($e_buyer)) || (strlen($e_buyer) > 10) || (!is_numeric($e_buyer))){
		    
		     if((empty($e_buyer)) || (strlen($e_buyer) > 10)){
						  
					$erro[$i][$key] = 'Please enter valid zip code for existing buyer '.$i;
					            } 
					 
					}	
		if($key == 'country'){
		     if((empty($e_buyer))){
						  
					$erro[$i][$key] = 'Please select country for existing buyer '.$i;
					            } 
					 
					}			
				
		if($key == 'relationship'){
		     if((empty($e_buyer))){
						  
					$erro[$i][$key] = 'Please select relationship for existing buyer '.$i;
					            } 
					 
					}											  
			
	  if($key == 'email_id'){
		     if((empty($e_buyer)) ||(!filter_var($e_buyer, FILTER_VALIDATE_EMAIL)) ){
						  
					$erro[$i][$key] = 'Please enter valid email id for existing buyer '.$i;
					       } 
					}	
			     }
			    $i++;
			  
			 } 
			
		
		if(!empty($erro)) {
				  
				  $_SESSION['pre_fill'] = $this->request->data;
				  
				  $_SESSION['ex_er'] = $erro;	
				  
				  $this->ExistingBuyer->validationErrors = $erro;
				  
				  $this->set('accordian_active',1);
				
				  $this->Session->setFlash(__('Please fill all the required fields.'),'default',array(),'error');
				  
				// $this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'add_existing_buyer')); 
				
				  }
		
		
		
		
		/*   validate from controller of dynamic form field  end*/
			
			
			$check='';
			foreach($_eb_buyer   as   $all_eb){
				$this->ExistingBuyer->set($all_eb);
					if (!$this->ExistingBuyer->validates()) {					
					$check='not_val';	
					$this->set('validated',$check);
					}	
			}
		
		$_SESSION['add_exbuyer'] = array();	
			
		if($check!='not_val'){	
			foreach($_eb_buyer   as   $all_eb){
				
		        if(empty($all_eb['ExistingBuyer']['id'])){
			//	if(!$id){
						$all_eb['ExistingBuyer']['created_at']=date('Y-m-d H:i:s');
						$all_eb['ExistingBuyer']['status']=1;
					
						$options = array();
						$options['replacement'] = array(
						'{NAME}'=>$all_eb['ExistingBuyer']['first_name']." ".$all_eb['ExistingBuyer']['last_name'],'{SUPPLIER}'=>$loguser['MemberAuth']['first_name'].' '.$loguser['MemberAuth']['last_name']);			
						$options['to'] = array($all_eb['ExistingBuyer']['email_id']); 		
						$this->MyMail->SendMail(18,$options);
				    }	
					//}else{
					//	$all_eb['ExistingBuyer']['modified_at']=date('Y-m-d H:i:s');
					//}
					$data['SupplierBuyer']['created_at']=date('Y-m-d H:i:s');	
					if(!empty($all_eb['ExistingBuyer']['org_name'])){
						//$all_eb['ExistingBuyer']['org_name']=strtoupper($all_eb['ExistingBuyer']['org_name']);
						
						$all_eb['ExistingBuyer']['org_name'] = ucfirst($all_eb['ExistingBuyer']['org_name']);
					}	
		 
		 
							if($all_eb['ExistingBuyer']['replace']==1){
								$redirec_to='feed'; 
							}
		 
		 
					$this->ExistingBuyer->create();
					$this->ExistingBuyer->save($all_eb,array('validate'=>false)); 
					$id = $this->ExistingBuyer->id;
					
					$_SESSION['add_exbuyer'][] = $id;
					
				}
				if($process_step['Supplier']['process_step'] == 2){
					
						$data1['Supplier']['id']= $member_id;
						$data1['Supplier']['process_step']= 3;						
						$this->Supplier->create();
						$this->Supplier->save($data1,array('validate'=>false));
					}
				$this->Session->setFlash(__('Existing buyer(s) has been added successfully'),'default',array(),'success');
			
			
							if($redirec_to=='feed'){
									$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'assign_existing_buyer_feed'));
										} else {
									$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'assign_existing_buyer'));
									}
			
			
			} else {
			
				$this->Session->setFlash(__('Please fill all the required fields.'),'default',array(),'error');
				//$this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'add_existing_buyer',$nb_button));				
		}		
	
				
				//	if ($this->request->data['ExistingBuyer']['id']) {
						//$this->Session->setFlash(__('Existing buyer has been updated successfully'));
						//$this->Session->setFlash(__('Existing buyer has been updated successfully.'),'default',array(),'success');
						//$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'eb_list'));
				//	}else{
						//$this->Session->setFlash(__('New existing buyer has been added successfully.'),'default',array(),'success');
						//$this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'add_eb_success',$process_step['Supplier']['process_step']));
				//	}
					
					
			}else{
					if(!empty($this->request->data)){
						$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
					}
					
				   if(!empty($_SESSION['add_exbuyer']))
				   {
					   $t_id = count($_SESSION['add_exbuyer']);
					  if($t_id > 0){  
					   $u_details = array(); 
					   
					   for($a=0; $a< $t_id;$a++)
					    {
						   $uid = $_SESSION['add_exbuyer'][$a];
					       $u_details = $this->ExistingBuyer->read(null,$uid);
					       $u_detail = array();
					       foreach($u_details['ExistingBuyer'] as $key =>$value){	
								$u_detail[$key] = $value; 
							}
					         $this->request->data['ExistingBuyer'][$a+1] = $u_detail; 
				         }				         
				        }				         
					   }
					   										
				/*	if($id!=null){
						$this->request->data = $this->ExistingBuyer->read(null,$id);
					}else{
						$this->request->data = array();
					}
				*/ 
			}
			
			
			$bk =  $this->referer('/', true); 
			
			
			$this->System->set_seo('site_title','Supplier-Existing Buyer');
			$this->System->set_data('banner_image',$page['Page']['banner_image']);
			$this->set('countries', $countries);
			$this->set('supplier_id',  $member_id);
			$this->set('process_step',$process_step['Supplier']['process_step']);
			
			$req_fed = $this->__check_eb($member_id);
			
	      
 	  if($process_step['Supplier']['process_step']==5){
			
				if(($nb_button1==1) && ($nb_button2 == 0)){
					
					$exist_count=$this->__exist_buyer_response($member_id);
				
					if($req_fed<=$exist_count){
						
						$_SESSION['nb_button'] = array('nb_button1');
						 $_SESSION['back_key'] = 1;
						
						$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'assign_existing_buyer_feed'));		
							} else {
						$_SESSION['nb_button'] = array('nb_button1');
						$this->set('replace','replace');						
						$req_fed=$req_fed-$exist_count;	
						
								}
				}
			if(($nb_button1==0) && ($nb_button2 == 2)){
				$exist_pending = $this->__exist_buyer_pending($member_id);
				
				if($req_fed<=$exist_pending){
					
					   $_SESSION['nb_button'] = array('nb_button2');
					   $_SESSION['back_key'] = 1;
					    
						$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'assign_existing_buyer_feed'));		
							} else {
								
						$_SESSION['nb_button'] = array('nb_button2');		
						$this->set('replace2','replace2');						
						$req_fed = $req_fed-$exist_pending;	
								}
					
			  }	
			  
			if(($nb_button1==1) && ($nb_button2 == 2)){
				
				$exist_both = $this->__exist_buyer_both($member_id);
				
				if($req_fed<=$exist_both){
					
					 $_SESSION['nb_button'] = array('nb_button1','nb_button2');
					  $_SESSION['back_key'] = 1;
					
					$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'assign_existing_buyer_feed'));		
					} else {
						$_SESSION['nb_button'] = array('nb_button1','nb_button2');
						$this->set('replace3','replace3');						
						$req_fed = $req_fed-$exist_both;	
					}
			  } 			  		
		}
		
		    
			
			$this->set('req_fed',$req_fed);
			$this->set('nb_button',$nb_button1);
		}
		
	public function new_add_existing(){
		
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		
		 $member_id = self::_check_member_login();
		 
		 $eb_listss = $this->EbLoginDetail->find('all',array('conditions'=>array('ExistingBuyer.supplier_id'=>$member_id,'ExistingBuyer.status'=>1,'EbLoginDetail.eb_status'=>2,'EbLoginDetail.is_link_expire'=>1),'order'=>array('EbLoginDetail.id'=>'DESC'),'fields'=>array('EbLoginDetail.id','EbLoginDetail.eb_status','ExistingBuyer.id','ExistingBuyer.first_name','ExistingBuyer.middle_name','ExistingBuyer.last_name','ExistingBuyer.org_name','ExistingBuyer.email_id','EbLoginDetail.request_id')));
		 
		 // echo "<pre>"; print_r($eb_listss); die;
		 
		  $this->set('existing_b', $eb_listss);
		  $this->System->set_seo('site_title','Supplier-Existing Buyer');
		  $this->System->set_data('banner_image',$page['Page']['banner_image']);
		
		}	
		
		
	public function replace_existing_info($id=null) {
		  $member_id = self::_check_member_login();
		  
		  $loguser =$this->MemberAuth->get_active_member_detail();
		  
		  $process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$member_id),'fields'=>array('Supplier.process_step')));
		  
		  $countries = $this->Country->country_list();
		  
		  $page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		  
		  /*==========================GET EXPIRED EXISTING BUYER==================================*/
		
		  $eb_exp_data=$this->EbLoginDetail->find('list',array('conditions'=>array('EbLoginDetail.is_link_expire'=>array(1,3),'EbLoginDetail.eb_status'=>2),'fields'=>array('existing_buyer_id')));
				
		  $eb_list = $this->ExistingBuyer->find('all',array('conditions'=>array('ExistingBuyer.supplier_id'=>$member_id,'ExistingBuyer.id'=>$eb_exp_data,'ExistingBuyer.status'=>1)));
	       
	      $t_rep_existingbuyer = count($eb_list);
	       
	      $this->request->data['ExistingBuyer'] =  $eb_list;
		
		  $this->set('eb', $eb_list);
		
		  $this->System->set_seo('site_title','Supplier-Existing Buyer');
		  
		  $this->System->set_data('banner_image',$page['Page']['banner_image']);
		  
		  $this->set('countries', $countries);
		  
		  $this->set('supplier_id',  $member_id);
		  
		  $this->set('process_step',$process_step['Supplier']['process_step']);
			
		  $req_fed = $this->__check_eb($member_id);
			
		  $this->set('req_fed',$req_fed);
		}
			
	public function replace_exist_ajax($id=null) {
		  
		 $member_id = self::_check_member_login();
		 
		 $this->autoRender = false;
		
		if(!empty($this->request->data)){
			
		 if(!empty($this->request->data['ExistingBuyer']['id'])){
				
			 $e_id = $this->request->data['ExistingBuyer']['id'];
				
			 $eu_details = $this->ExistingBuyer->read(null,$e_id);
			  
			 if($eu_details['ExistingBuyer']['email_id'] == $this->request->data['ExistingBuyer']['email_id'] ){
					
			 $this->request->data['ExistingBuyer']['modified_at'] = date('Y-m-d H:i:s');
		
			 $this->ExistingBuyer->create();
		 
			 $this->ExistingBuyer->save($this->request->data,array('validate'=>false));
				
			 $replace_exbuyer_status = 2;
			 
		    }else {
				
				$n_id = $this->request->data['ExistingBuyer']['id'] ;
				
			    unset($this->request->data['ExistingBuyer']['id']);
			      
			    $this->request->data['ExistingBuyer']['created_at']= date('Y-m-d H:i:s');
			      
			    $this->request->data['ExistingBuyer']['org_name']=ucfirst($this->request->data['ExistingBuyer']['org_name']);
			
			    $this->request->data['ExistingBuyer']['replace']=1;
			      
			    $this->ExistingBuyer->create();
			      
			    $this->ExistingBuyer->save($this->request->data,array('validate'=>false));
			      
			    $n_exp_date = date('Y-m-d h:i:s', strtotime("+14 days"));
			      
			    /*$this->EbLoginDetail->updateAll(
                      array('EbLoginDetail.link_expire_date' => "'$n_exp_date'",'EbLoginDetail.eb_status'=> NULL,'EbLoginDetail.is_replace'=>1,'EbLoginDetail.is_link_expire'=>0),
                      array('EbLoginDetail.existing_buyer_id'=>$n_id));	 */
                      
                $this->EbLoginDetail->updateAll( array('EbLoginDetail.is_replace'=>1),array('EbLoginDetail.existing_buyer_id'=>$n_id));	          
			      
			    $replace_exbuyer_status = 3;
					
					}
				}
			}		
		
		 $exist_id = $this->ExistingBuyer->find('count', array('conditions' => array('ExistingBuyer.status'=>1,'ExistingBuyer.replace'=>1,'ExistingBuyer.supplier_id'=>$member_id)));
		
		//$return_val = array($exist_id,$replace_exbuyer_status);
		
		echo json_encode(array($exist_id, $replace_exbuyer_status));
		
		return ;
		}
		 
	public function update_existing_info($id=null) {
		  $member_id = self::_check_member_login();
		  $loguser =$this->MemberAuth->get_active_member_detail();
		  $process_step = $this->Supplier->find('first',array('conditions'=>array('Supplier.id'=>$member_id),'fields'=>array('Supplier.process_step')));
			$countries = $this->Country->country_list();
			$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
			
		
		
		$eb = $this->ExistingBuyer->find('all',array('conditions'=>array('ExistingBuyer.supplier_id'=>$member_id,'ExistingBuyer.status'=>1), 'order'=>array('ExistingBuyer.id'=>'DESC')));	
		//echo "<pre>"; print_r($eb); die;	
		
		$this->set('eb', $eb);
		
		
			$this->System->set_seo('site_title','Supplier-Existing Buyer');
			$this->System->set_data('banner_image',$page['Page']['banner_image']);
			$this->set('countries', $countries);
			$this->set('supplier_id',  $member_id);
			$this->set('process_step',$process_step['Supplier']['process_step']);
			
			$req_fed = $this->__check_eb($member_id);
	
			$this->set('req_fed',$req_fed);
		}
		
		
				
	public function update_exist_ajax($id=null) { 
		 
			$this->autoRender = false;
			if(!empty($this->request->data)){		
	//echo "<pre>"; print_r($this->request->data); die;
						$this->request->data['ExistingBuyer']['modified_at']=date('Y-m-d H:i:s');
						$this->request->data['ExistingBuyer']['id']=$this->request->data['ExistingBuyer']['id'];
					
						if(!empty($this->request->data['ExistingBuyer']['org_name'])){
						//$this->request->data['ExistingBuyer']['org_name']=strtoupper($this->request->data['ExistingBuyer']['org_name']);
						$this->request->data['ExistingBuyer']['org_name']=ucfirst($this->request->data['ExistingBuyer']['org_name']);
						}	
		 
						$this->ExistingBuyer->create();
						$this->ExistingBuyer->save($this->request->data,array('validate'=>false)); 
						//$id = $this->ExistingBuyer->id;
				}
			
		//	echo "test"; 
			return;
			//$this->set('req_fed',$req_fed);
		}		
					
	public function existing_buyer_list($id=null) {
		  $loguser = $this->Session->read('Auth.Supplier');		 
		  if(!$loguser){
				$this->redirect($this->Auth->redirect());
			}
			$countries = $this->Country->country_list();
			$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
			if(!empty($this->request->data['ExistingBuyer']) && $this->validation())
			{	
			
				if(!$id){
						$this->request->data['ExistingBuyer']['created_at']=date('Y-m-d H:i:s');	
					}else{
						$this->request->data['ExistingBuyer']['updated_at']=date('Y-m-d H:i:s');
					}
					$data['SupplierBuyer']['created_at']=date('Y-m-d H:i:s');						
					$this->ExistingBuyer->create();
					$this->ExistingBuyer->save($this->request->data,array('validate'=>false));
					$id = $this->ExistingBuyer->id;
					
					if ($this->request->data['ExistingBuyer']['id']) {
						//$this->Session->setFlash(__('Existing buyer has been updated successfully'));
						$this->Session->setFlash(__('Existing buyer has been updated successfully.'),'default',array(),'success');
					}else{
						//$this->Session->setFlash(__('New Existing buyer has been added successfully'));
						$this->Session->setFlash(__('New Existing buyer has been added successfully.'),'default',array(),'success');
					}
					$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'eb_list'));
			}else{
					if(!empty($this->request->data)){
						$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
					}					
					if($id!=null){
						$this->request->data = $this->ExistingBuyer->read(null,$id);
					}else{
						$this->request->data = array();
					}
			}
			
			$this->System->set_seo('site_title','Supplier-Existing Buyer');
			$this->System->set_data('banner_image',$page['Page']['banner_image']);
			$this->set('countries', $countries);
			$this->set('supplier_id', $loguser['id']);
		}
	
	function admin_delete($id=null){
		$this->autoRender = false;
		//print_r($this->request->data); die;
		$data=$this->request->data['ExistingBuyer']['id'];
		//print_r($data); die;
		$action = $this->request->data['ExistingBuyer']['action'];
		$ans="0";
		foreach($data as $value){

			if($value!='0'){
				if($action=='Publish'){
					$supplier['ExistingBuyer']['id'] = $value;
					$supplier['ExistingBuyer']['status']=1;
					$this->ExistingBuyer->create();
					$this->ExistingBuyer->save($supplier);
					$ans="1";
				}
				if($action=='Unpublish'){
					$supplier['ExistingBuyer']['id'] = $value;
					$supplier['ExistingBuyer']['status']=0;
					$this->ExistingBuyer->create();
					$this->ExistingBuyer->save($supplier);
					$ans="1";
				}
				if($action=='Delete'){
					$this->ExistingBuyer->delete($value);
					//$this->Supplier->delete_routes($value,'Supplier');
					$ans="2";
				}
			}
		}
		
		if($ans=="1"){
			$this->Session->setFlash(__('Existing Buyer has been '.strtolower($this->data['ExistingBuyer']['action']).'ed successfully', true));
		}
		else if($ans=="2"){
			$this->Session->setFlash(__('Existing Buyer has been '.strtolower($this->data['ExistingBuyer']['action']).'d successfully', true));
		}else{
			$this->Session->setFlash(__('Please Select any Existing Buyer', true),'default','','error');
		}
		$this->redirect($this->request->data['ExistingBuyer']['redirect']);
                 
	}
	
	function validation(){
		
		if(!empty($this->request->data['ExistingBuyer']['form'])){
			if($this->request->data['ExistingBuyer']['form']=="ebuyer_add" && $this->request->data['ExistingBuyer']['status']==2){
				return true;
			}
			$this->ExistingBuyer->setValidation($this->request->data['ExistingBuyer']['form']);
		}else{
			throw new NotFoundException('404 Error - Team not found');
		}
		$this->ExistingBuyer->set($this->request->data);
		return $this->ExistingBuyer->validates();
	}
	
	function validation1(){
		if(!empty($this->request->data['ExistingBuyer']['form'])){
		$this->ExistingBuyer->setValidation($this->request->data['ExistingBuyer']['form']);
		}
		$this->ExistingBuyer->set($this->request->data);
		return $this->ExistingBuyer->validates();
	}
	
	function ajax_validation($returnType = 'json'){
		
		$this->autoRender = false;
		if(!empty($this->request->data)){
			//print_r($this->request->data); die;
			if(!empty($this->request->data['ExistingBuyer']['form'])){
				$this->ExistingBuyer->setValidation($this->request->data['ExistingBuyer']['form']);
			}
			$this->ExistingBuyer->set($this->request->data);
			$result = array();
			if($this->request->data['ExistingBuyer']['form']=="ebuyer_add" && $this->request->data['ExistingBuyer']['status']==2){
				$result['error'] = 0;
			}else{
				if($this->ExistingBuyer->validates()){
					$result['error'] = 0;
				}else{
					$result['error'] = 1;
					$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
				}
			}
			$errors = array();
			$result['errors'] = $this->ExistingBuyer->validationErrors;
			foreach($result['errors'] as $field => $data){
			  $errors['ExistingBuyer'.Inflector::camelize($field)] = array_pop($data);
			}
			$result['errors'] = $errors;
			$view = new View();
			
			$result['error_message'] = $view->element('admin/message');
			echo json_encode($result);
			return;
		}
		echo json_encode(array());
	}
	
	
  function ajax_emailvalidation($returnType = 'json'){	
 	$this->autoRender = false;
 	$flag = array();	
	 if(!empty($this->request->data))
		{		
		 foreach($this->request->data['ExistingBuyer'] as $mail)
		  {
			  $temp_val = $mail['email_id'];   
			}			   
			 $this->request->data  = null;
			 $trim_mail = trim($temp_val);
		     $e_mail = $this->ExistingBuyer->find('count', array('conditions' => array('ExistingBuyer.email_id' => $trim_mail)));
             $email_check = preg_match('/^[A-z0-9_\-\.]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z.]{2,4}$/', $trim_mail);
			/* if($e_mail >0)
			 {
				   $result['error'] = 1;
				   $this->Session->setFlash(__('Email already exist.'),'default',array(),'error');
				 }
			 else if($email_check){
				  $email_check1 = preg_match('/^[_\-\.]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z.]{2,4}$/', $trim_mail); 
				  
				    $result['error'] = 1;
					$this->Session->setFlash(__('Please fenter valid email address.'),'default',array(),'error'); 
				  }	 
			 else{
				   $result['error'] = 0;
				 }*/
				 
			  if($e_mail >0){
				   return 1;}
			 else{
				    return 0;}	 				
			}	
	}		
  function admin_export()
	{
		$condition = array();		
		$this->ExistingBuyer->bindModel(array('belongsTo' => array('Country' => array('foreignKey' => false,'conditions' => array('ExistingBuyer.country = Country.country_code_char2')))));
		$options['group']=array('ExistingBuyer.id');
		$options['order']= array('ExistingBuyer.id'=>'DESC');
		$ExistingBuyerInfos = $this->ExistingBuyer->find('all',$options);
		
		if(empty($ExistingBuyerInfos)){
			$this->Session->setFlash(__('No data found to export!', true),'default','','error');
			$this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'index'));
		}
	//	echo "<pre>";	print_r($ExistingBuyerInfos); die;
		$this->set('ExistingBuyerInfos', $ExistingBuyerInfos);
		$this->layout = null;
		$this->autoLayout = false;
		Configure::write('debug','2');
	
	}
	
	public function admin_create_pdf(){ //echo "ddd"; die;
      
		$condition = array();			
		$options['order']= array('ExistingBuyer.id'=>'DESC');
		$e_buyers = $this->ExistingBuyer->find('all',$options);
		if(empty($e_buyers)){
			$this->Session->setFlash(__('No data found to export!', true),'default','','error');
			$this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'index'));
		}
	//	echo "<pre>";	print_r($e_buyers); die;
		$this->set('e_buyers', $e_buyers);
	//	print_r($suppliers); die;
		$this->layout = null;
		$this->autoLayout = false;
		Configure::write('debug','2');
		
		$currntdate=date('d-m-Y'); 
		$this->set('currntdate',$currntdate);
		$this->layout = '/pdf/default';
	
	}

	/*function ajax_sort(){
		Cache::delete('teams'); 
		$this->autoRender = false;
		foreach($_POST['sort'] as $order => $id){
			$slide= array();
			$slide['Team']['id'] = $id;
			$slide['Team']['team_order'] = $order;
		  
			$this->Team->create();
			$this->Team->save($slide);
		}
	}*/
	
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
	
	public function login($request_id=null,$eb_id=null,$passurl=null){
		$page = $this->__load_page(61);
		$this->set('page', $page);
		
		$active_eb = $this->Session->read('eb_id');
		if(!empty($active_eb)){
			if($active_eb == $eb_id){
				$this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'feedback'));
			}else{
				$this->Session->destroy();
			}
		}else{
			$this->Session->destroy();
		}
		//echo "pssurl=".$passurl." ebid=".$eb_id." rid=".$request_id; die;
		$EbLogin = $this->EbLoginDetail->find('first',array('conditions'=>array('EbLoginDetail.request_id'=>$request_id,'EbLoginDetail.existing_buyer_id'=>$eb_id,'EbLoginDetail.passwordurl'=>$passurl)));
		//echo "<pre>"; print_r($EbLogin); die;
		if(!empty($EbLogin)){
			$exp=$EbLogin['EbLoginDetail']['link_expire_date'];			
			if($EbLogin['EbLoginDetail']['is_link_expire'] == 1 || (strtotime($exp) <= strtotime('now'))){
				$message = "Sorry! Your Link has been expired.";
				$this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'check_link',1));
			}
		}else{
			$message = "This link is not available now.";
			$this->set('message', $message);
			$this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'check_link',2));
		}
		
		
		
		if(!empty($this->request->data))
		{
			$Eb_Detail = $this->ExistingBuyer->find('first',array('conditions'=>array('ExistingBuyer.id'=>$EbLogin['EbLoginDetail']['existing_buyer_id'])));
			
			if(($this->request->data['ExistingBuyer']['email_id'] == $Eb_Detail['ExistingBuyer']['email_id']) && (Security::hash(Configure::read('Security.salt').$this->request->data['ExistingBuyer']['password']) == $EbLogin['EbLoginDetail']['password']))
			{
				if($Eb_Detail['ExistingBuyer']['status']==1){
					if($Eb_Detail['ExistingBuyer']['status']!=2){
						$this->Session->setFlash(__('Logged in sucessfully'));
						$this->Session->write('eb_id',$Eb_Detail['ExistingBuyer']['id']);
						$this->Session->write('eb_email',$Eb_Detail['ExistingBuyer']['email_id']); 
						$write=$this->Session->write('eb_name',$Eb_Detail['ExistingBuyer']['first_name']);
						
						$active_existing_buyer =$this->Session->read('eb_email');
						
						$this->Session->write('feedback_request_id',$request_id); 
						
						$details['EbLoginDetail']['id'] = $EbLogin['EbLoginDetail']['id'];
						if($EbLogin['EbLoginDetail']['eb_status'] != 5){
							$details['EbLoginDetail']['eb_status'] = 4; 
						}
						if($EbLogin['EbLoginDetail']['is_access'] != 1){
							$details['EbLoginDetail']['is_access'] = 1;
						}
						$this->EbLoginDetail->create();
						$this->EbLoginDetail->save($details,array('validate'=>false));
						
						$this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'info'));	
					}else{
						//$this->Session->setFlash(__('Your account is blocked please contact to admin.'));
						$this->Session->setFlash(__('Your account is blocked please contact to admin.'),'default',array(),'error');
						$this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'login',$request_id,$eb_id,$passurl));
						//$this->redirect('/');
					}
				}else{
					//$this->Session->setFlash(__('Your account not activated please contact to admin.'));
					$this->Session->setFlash(__('Your account not activated please contact to admin.'),'default',array(),'error');
					
					$this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'login',$request_id,$eb_id,$passurl));
				}
				
			}else{	
				
				$this->Session->setFlash(__('Invalid username or password, try again.'),'default',array(),'error');
			 	//$this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'login',$request_id,$eb_id,$passurl));	
			 	unset($this->request->data);
			}	
		}
		$this->set('eb_id', $eb_id);
		$this->set('request_id', $request_id);
		$this->set('passurl', $passurl);
	}
	
	public function logout()
	{
		$this->Session->destroy();
		$this->redirect('/');
	}
	
	private function _manage_video($video = array()){
		if ($video['error'] > 0) {
			return null;
		}else{
			if ($video['error'] > 0) {
				return $existing_video['ExistingBuyer']['video'];
			} else {
				$destination = WWW_ROOT."Video/";
				
				if(!file_exists($destination)) {
					App::uses('Folder', 'Utility');
					mkdir($destination, 0755);
					$dir = new Folder();
					$dir->chmod($destination, 0755, true, array());	
				}
				$ext = explode('.', $video['name']);
				$video_name = time() . '_' . time() . '.' . array_pop($ext);
				$vn=time() . '_' . time();
				
				move_uploaded_file($video['tmp_name'], $destination . $video_name);
				exec('/usr/bin/ffmpeg  -i '.$destination.$video_name.' '.$destination.$vn.'.ogg');
				if (!empty($existing_video)) {
					unlink($destination . $existing_video['ExistingBuyer']['video']);
				}
				//move_uploaded_file($filename, $destination);
				return $video_name;
			}
		}
	}
	
	public function feedback()
	{
		$page = $this->__load_page(62);
		$this->set('page', $page);
		$request_id =$this->Session->read('feedback_request_id');
		$active_eb_id =$this->Session->read('eb_id');
		$video = '';
		//echo $request_id; die;
		//$request_id = 1;
		//$active_eb_id = 2;
		if(!empty($active_eb_id)){
			$request_details = $this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$request_id)));
			$payment_details = $this->Payment->find('first',array('conditions'=>array('Payment.feedback_request_id'=>$request_id),'order' => array('Payment.id' => 'asc'),'fields'=>array('Payment.txn_id')));	
		//	pr($payment_details); die;
			if(!empty($request_details)){		
				$questions = json_decode($request_details['FeedbackRequest']['questions']);
				//print_r($questions); die;
				$question_details = $this->Question->find('all',array('conditions'=>array('Question.id'=>$questions)));
				$cat_list = array();
				foreach($question_details as $ques){
					if(!(in_array($ques['Question']['category_id'], $cat_list))){
						$cat_list[] = $ques['Question']['category_id'];
					}
				}
				$categories = $this->QuestionCategorie->find('all',array('conditions'=>array('QuestionCategorie.id'=>$cat_list)));
				
				$this->set('categories', $categories);
				$this->set('question_details', $question_details);
			
				$descriptive_ques = json_decode($request_details['FeedbackRequest']['descriptive_ques']);
				$des_questions = $this->Question->find('all',array('conditions'=>array('Question.id'=>$descriptive_ques)));
				$this->set('des_questions', $des_questions);
				
				$response_details = $this->FeedbackResponse->find('first',array('conditions'=>array('FeedbackResponse.request_id'=>$request_id,'FeedbackResponse.existing_buyer_id'=>$active_eb_id)));
				
				$eb_login_details = $this->EbLoginDetail->find('first',array('conditions'=>array('EbLoginDetail.request_id'=>$request_id,'EbLoginDetail.existing_buyer_id'=>$active_eb_id)));		
				
			    $this->set('r_status', $response_details);
				
				
				//pr($eb_login_details); die;
				if(!empty($this->request->data))
				{
					$data=array();
				//	echo "<pre>";  print_r($this->request->data); die;
					foreach($this->request->data['ExistingBuyer'] as $key=>$value){
						if (strpos($key, 'answer') !== false) {
							$key1 = explode("answer",$key);
							$data[$key1[0]] = $value;
						}
					}
					foreach($this->request->data['ExistingBuyer'] as $key=>$value){
						if (strpos($key, 'descriptive-ans') !== false) {
							$key1 = explode("descriptive-ans",$key);
							$data1[$key1[0]] = $value;
						}
					}
					//if(!empty($response_details)){
					//if(!empty($data)){}
					$ques_data['FeedbackResponse']['answers'] = json_encode($data);
					$ques_data['FeedbackResponse']['descriptive_ans'] = json_encode($data1);
					if(!empty($response_details)){
						$ques_data['FeedbackResponse']['id'] = $response_details['FeedbackResponse']['id'];
					}else{
						$ques_data['FeedbackResponse']['request_id'] = $request_id;
						$ques_data['FeedbackResponse']['existing_buyer_id'] = $active_eb_id;
					
					}	
					if($this->request->data['ExistingBuyer']['submit'] == 'Send Feedback'){
						$ques_data['FeedbackResponse']['response_status'] = 2;
					}else{
						$ques_data['FeedbackResponse']['response_status'] = 1;
					}
					
					$video_name = '';
					
					if($this->request->data['ExistingBuyer']['video']['error'] < 1){
						$video_name =self::_manage_video($this->request->data['ExistingBuyer']['video']);
						$ques_data['FeedbackResponse']['video'] = $video_name;	
					}
					
					$this->FeedbackResponse->create();
					$this->FeedbackResponse->save($ques_data,array('validate'=>false));						
					
					if($this->request->data['ExistingBuyer']['submit'] == 'Send Feedback'){ 
						$all_ebs = json_decode($request_details['FeedbackRequest']['existing_buyers'],true);
						foreach($all_ebs as $key=>$value){
							if($value == $active_eb_id){
								unset($all_ebs[$key]);
								
							}
						}
						
						$res = count($all_ebs);
						$response_count = $this->FeedbackResponse->find('count',array('conditions'=>array('FeedbackResponse.request_id'=>$request_id,'FeedbackResponse.response_status'=>2,'Not'=>array('FeedbackResponse.existing_buyer_id'=>$active_eb_id))));
						
						//echo $active_eb_id; die;
						//print_r($response_count); die;
						if($res == $response_count){
							
							$req_data['FeedbackRequest']['id'] = $request_id;
							$req_data['FeedbackRequest']['request_status'] = 5;
							
							
						}else{
							if($request_details['FeedbackRequest']['request_status']<3){
								$req_data['FeedbackRequest']['id'] = $request_id;
								$req_data['FeedbackRequest']['request_status'] = 3;
							}	
						}				
						if(!empty($req_data)){
							$this->FeedbackRequest->create();
							$this->FeedbackRequest->save($req_data,array('validate'=>false));
						}
											
						$eb_data['EbLoginDetail']['id'] = $eb_login_details['EbLoginDetail']['id'];
						$eb_data['EbLoginDetail']['is_link_expire'] = 1;
						$eb_data['EbLoginDetail']['passwordurl'] = '';
						$this->EbLoginDetail->create();
						$this->EbLoginDetail->save($eb_data,array('validate'=>false));
						
						/*  send mail to supplier */
						$options = array();
						$supplier_name = $request_details['Supplier']['title']." ".$request_details['Supplier']['first_name']." ".$request_details['Supplier']['middle_name']." ".$request_details['Supplier']['last_name'];
						$ebuyer_name = $eb_login_details['ExistingBuyer']['title']." ".$eb_login_details['ExistingBuyer']['first_name']." ".$eb_login_details['ExistingBuyer']['middle_name']." ".$eb_login_details['ExistingBuyer']['last_name'];
						
						$options['replacement'] = array('{Supplier}'=>$supplier_name,'{E_Buyer}'=>$ebuyer_name,'{Req_ID}'=>$payment_details['Payment']['txn_id']);
						
						$options['to'] = array($request_details['Supplier']['email_id']); 
						$this->MyMail->SendMail(27,$options);
						
						$eb_login_data['EbLoginDetail']['id'] = $eb_login_details['EbLoginDetail']['id'];
						$eb_login_data['EbLoginDetail']['eb_status'] = 6;
						$this->EbLoginDetail->create();
						$this->EbLoginDetail->save($eb_login_data,array('validate'=>false));
						
					/*-- check if any eb is completely expired and replaced---*/					
						
						
						$eb_login_check_expired = $this->EbLoginDetail->find('all',array('conditions'=>array('EbLoginDetail.request_id'=>$request_id),'fields'=>array('EbLoginDetail.id','EbLoginDetail.eb_status')));
						$check=array();
						$check2=array();
						foreach($eb_login_check_expired as $eb_login_check_exp){
							if($eb_login_check_exp['EbLoginDetail']['eb_status']==7){$check[]=$eb_login_check_exp['EbLoginDetail']['id'];}elseif($eb_login_check_exp['EbLoginDetail']['eb_status']==6){$check2[]=$eb_login_check_exp['EbLoginDetail']['id'];}
						}
						
						$nbs=json_decode($request_details['FeedbackRequest']['new_buyers'],true);
						$req_feedback=0;
						
						if(!empty($nbs)){
							foreach($nbs as $nb){
								$nb_det= $this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.id'=>$nb),'fields'=>array('NewBuyer.id','NewBuyer.required_feedback'),'recursive'=>-1));
								
								//$req_feedback=$nb_det['NewBuyer']['required_feedback'];
								if(!empty($req_feedback) && ($req_feedback<$nb_det['NewBuyer']['required_feedback'])){
									$req_feedback=$nb_det['NewBuyer']['required_feedback'];
								}elseif($req_feedback==0){
									$req_feedback=$nb_det['NewBuyer']['required_feedback'];
								}
								
							}
						}
						$count_check1=count($check);					
						$count_check2=count($check2);
					//	echo $count_check1.'<br>';
						//echo $count_check2.'<br>'.$req_feedback.'<br>';
						if(!empty($count_check1) && !empty($count_check2)){						
							$request_status=0;
							if($count_check2==$count_check1){$request_status=5;}						
							if(!empty($req_feedback) && $count_check2>=$req_feedback){$request_status=5;}
						//	echo $request_status;						
							if($request_status==5){	
								$req_data1['FeedbackRequest']['id'] =$request_id;
								$req_data1['FeedbackRequest']['request_status'] = $request_status;
								$this->FeedbackRequest->create();
								$this->FeedbackRequest->save($req_data1,array('validate'=>false));
							}
						}
				//	die;
						
			/*-- check if any eb is completely expired and replaced ends here---*/
						
						$this->Session->destroy();
						//$this->Session->setFlash(__('Your feedback has been submitted successfully.'));
						$this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'feedback_success'));		
					}elseif($this->request->data['ExistingBuyer']['submit'] == 'Save Feedback'){	
					
				
						$eb_login_data['EbLoginDetail']['id'] = $eb_login_details['EbLoginDetail']['id'];
						$eb_login_data['EbLoginDetail']['eb_status'] = 5;
						$this->EbLoginDetail->create();
						$this->EbLoginDetail->save($eb_login_data,array('validate'=>false));					
						if($request_details['FeedbackRequest']['request_status']<2){
							$req_data['FeedbackRequest']['id'] = $request_id;
							$req_data['FeedbackRequest']['request_status'] = 2;
							$this->FeedbackRequest->create();
							$this->FeedbackRequest->save($req_data,array('validate'=>false));
						}
						
					}
					
					//$this->Session->setFlash(__('Your feedback saved successfully.'));
					$r_details = $this->FeedbackResponse->find('first',array('conditions'=>array('FeedbackResponse.request_id'=>$request_id,'FeedbackResponse.existing_buyer_id'=>$active_eb_id),'fields'=>array('FeedbackResponse.video')));
					$this->set('video',$r_details['FeedbackResponse']['video']);
					
					$this->Session->setFlash(__('Your feedback saved successfully.'),'default',array(),'success');
					
						//echo $video; die;
						
				}else{
					
					if(!empty($response_details)){
						$answer = json_decode($response_details['FeedbackResponse']['answers']);
						$des_answer = json_decode($response_details['FeedbackResponse']['descriptive_ans']);
						foreach($answer as $key=>$value){	
							$this->request->data['ExistingBuyer'][$key.'answer'] = $value;
						}
						
						foreach($des_answer as $key=>$value){	
							$this->request->data['ExistingBuyer'][$key.'descriptive-ans'] = $value;
						} 
						
						$video = $response_details['FeedbackResponse']['video'];
						//echo $video; die;
						$this->set('video',$video);
					}
				}
			}
			
		}else{
			//$this->Session->setFlash(__('Your session has been expired, please login again by follow link and credentials provided in mail.'));
			$this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'check_link',3));
			
		}
	}
	
	public function validation2(){
		
	}
	
	public function add_eb_success($step = null){
		$this->System->set_seo('site_title','Success');
		$this->System->set_data('banner_image',$this->System->get_setting('page','banner_image'));
		$page['Page']['name'] = "Existing Buyer";
		$this->set('page', $page);
		$this->set('step', $step);
		$this->set('process_step', $step);		
	}
	
	public function feedback_success(){
		$page = $this->__load_page(65);
		$this->set('page', $page);	
		//$this->set('message', $message);
	}
	
	public function check_link($message_id=null){
		$page = $this->__load_page(62);
		$this->set('page', $page);	
		if($message_id == 1){
			$message = "Sorry! Your Link has been expired.";
			
		}elseif($message_id == 2){
			$message = "This link is not available now.";
		}elseif($message_id == 3){
			$message = "Your session has been expired,<br>please login again by follow link and credentials provided in mail.";
		}
		$this->set('message', $message);
	}
	
	
	public function send_mail($id=null,$request_id=null){
		$this->autoRender = false;
		$this->loadModel('SupplierManager.FeedbackRequest');
		if($id != null){
			
			$user = $this->ExistingBuyer->find('first',array('conditions'=>array('ExistingBuyer.id'=>$id)));
			$randompassword=self::_randomPassword();		
			$pass = Security::hash(Configure::read('Security.salt').$randompassword);
			
			if(!empty($user)){
				$request_details = $this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$request_id)));
				
				$name = ucfirst($user['ExistingBuyer']['first_name']).' '.ucfirst($user['ExistingBuyer']['last_name']);
				$email = $user['ExistingBuyer']['email_id'];
				$urlValue=md5($this->_randomString());
				
				$supplier_name = $request_details['Supplier']['first_name']." ".$request_details['Supplier']['last_name'];
											
				$url=Router::url(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'login',$request_id,$id,$urlValue),true);	
							
				//$url = base64_encode($url);
				$options = array();
				$options['replacement'] = array('{NAME}'=>$name,'{SUPPLIER}'=>$supplier_name,'{URL}'=>$url);
				$options['to'] = array($email); 
				$this->MyMail->SendMail(21,$options);
				
				$options = array();
				$options['replacement'] = array('{NAME}'=>$name,'{SUPPLIER}'=>$supplier_name,'{EMAIL}'=>$email,'{PASSWORD}'=>$randompassword);
				$options['to'] = array($email); 
				$this->MyMail->SendMail(22,$options);
				
				$login_details = array();
				$login_details['EbLoginDetail']['request_id']= $request_id;
				$login_details['EbLoginDetail']['existing_buyer_id']= $id;
				$login_details['EbLoginDetail']['password']= $pass;
				$login_details['EbLoginDetail']['passwordurl'] =  $urlValue;
				$login_details['EbLoginDetail']['is_link_expire'] = 0;
				$this->EbLoginDetail->create();
				$this->EbLoginDetail->save($login_details,array('validate'=>false));
				
				$this->redirect('/');
			}
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
	
	
	function info(){
		
		$page = $this->__load_page(64);
		$this->set('page', $page);
		$active_eb_id = $this->Session->read('eb_id');
		if(!empty($active_eb_id)){
			
			$Details = $this->ExistingBuyer->find('first',array('conditions'=>array('ExistingBuyer.id'=>$active_eb_id)));
			$this->set('Details',$Details);
		
			if(!empty($this->request->data) && $this->validation())
			{
				$this->request->data['ExistingBuyer']['updated_at']=date('Y-m-d H:i:s');
				$this->request->data['ExistingBuyer']['id'] = $Details['ExistingBuyer']['id'];
				if(!empty($this->request->data['ExistingBuyer']['org_name'])){
					$this->request->data['ExistingBuyer']['org_name']=strtoupper($this->request->data['ExistingBuyer']['org_name']);
				}else{
					$this->request->data['ExistingBuyer']['org_name']=strtoupper($Details['ExistingBuyer']['org_name']);
				}	
					
				$this->ExistingBuyer->create();
				$this->ExistingBuyer->save($this->request->data,array('validate'=>false));
				
				//$this->Session->setFlash(__('Information saved successfully'));
				$this->Session->setFlash(__('Information saved successfully.'),'default',array(),'success');
				
				$this->redirect(array('action'=>'feedback'));
				
			}else{
				$this->request->data = $Details;
			}
		}else{
			$this->redirect(array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'check_link',3));
		}
	}
	
	public function report($request_id=null,$nb_id=null){
		$request_detail = $this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$request_id)));
		//print_r($request_detail); die;
		if(!empty($request_detail)){
			$eb_list = json_decode($request_detail['FeedbackRequest']['existing_buyers']);
			$eb_details = $this->ExistingBuyer->find('all',array('conditions'=>array('ExistingBuyer.id'=>$eb_list)));	
			//print_r($eb_details); die;
			$currntdate = date('Y-m-d');
			$this->set('currntdate',$currntdate);
			$this->set('eb_details',$eb_details);
			
			$des_ques = json_decode($request_detail['FeedbackRequest']['descriptive_ques']);
			
			foreach($des_ques as $ques){
				
				$result= $this->Question->findById($ques);
				$des_questions[$ques] = $result['Question']['question'];
			}
			$this->set('des_questions',$des_questions);
			
			$responses = $this->FeedbackResponse->find('all',array('conditions'=>array('FeedbackResponse.request_id'=>$request_id)));
			//print_r($responses); die;
			$this->set('responses',$responses);
			
			
			$scale_ques = json_decode($request_detail['FeedbackRequest']['questions']);
			
			foreach($scale_ques as $ques){
				
				$result= $this->Question->findById($ques);
				$scale_questions[$ques] = array('id'=>$result['Question']['id'],'question'=>$result['Question']['question'],'options'=>json_decode($result['Question']['options'],true));
			}
			$this->set('scale_questions',$scale_questions);
			//pr($scale_questions); die;
		}
		
	}
	
	/*public function admin_create_pdf(){
        $cat_list = $this->QuestionCategorie->find('all',array('order'=>array('QuestionCategorie.id'=>'DESC')));
        $this->Question->bindModel(array('belongsTo' => array('QuestionCategorie' => array('foreignKey' => false,'conditions' => array('Question.category_id = QuestionCategorie.id')))));
		$condition = array();			
												
		$options['group']=array('Question.id');
		$options['order']= array('Question.category_id'=>'ASC');
		$questionInfos = $this->Question->find('all',$options);
		if(empty($questionInfos)){
			$this->Session->setFlash(__('No data found to export!', true),'default','','error');
			$this->redirect(array('plugin'=>'question_manager','controller'=>'questions','action'=>'index'));
		}
		
		$this->set('questionInfos', $questionInfos);
		$this->set('cat_list', $cat_list);
		$this->layout = null;
		$this->autoLayout = false;
		Configure::write('debug','2');
		
		$currntdate=date('d-m-Y'); 
		$this->set('currntdate',$currntdate);
		$this->layout = '/pdf/default';
	
	}*/
}
?>
