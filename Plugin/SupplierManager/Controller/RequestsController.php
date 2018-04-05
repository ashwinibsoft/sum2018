<?php App::import('Controller', 'SupplierManager.Suppliers');
Class RequestsController extends SupplierManagerAppController{
	public $uses = array('SupplierManager.Supplier','Country','SupplierManager.SupplierBuyer','NewBuyerManager.NewBuyer','ExistingBuyerManager.EbLoginDetail','ExistingBuyerManager.ExistingBuyer','NewBuyerManager.NewBuyerQuestion','SupplierManager.FeedbackRequest','SupplierManager.FeedbackResponse','QuestionManager.Question','QuestionManager.QuestionCategorie');
	public $components=array('Email','RequestHandler','Image','MyMail');
	var $helpers = array('Captcha','Csv');
	public $paginate = array();
	public $id = null;
	public $template=null;
	
	public function beforeFilter() {
			parent::beforeFilter();	
	}
	
	public function pending_request(){
		$loguser_id = self::_check_member_login();	
		$this->Session->delete('Request');
		$this->System->set_seo('site_title','Pending Request');
		$this->System->set_data('banner_image',$this->System->get_setting('page','banner_image'));			
				
		$nb_list = $this->NewBuyer->find('all',array('conditions'=>array('NewBuyer.status'=>1,'NewBuyer.pass_update'=>1),'fields'=>array('id','first_name','last_name','email_id','required_feedback')));	
	
		$eb_list = self::_check_expiry();
		
		//$this->FeedbackRequest->unbindModel(array('belongsTo' => array('Supplier')));
		
		$this->FeedbackRequest->bindModel(array('hasMany' => array('Payment'=>array('order' => 'Payment.id DESC'))));
		$this->paginate['FeedbackRequest'] = array(
		  'conditions'=>array('FeedbackRequest.supplier_id'=>$loguser_id),
		  'order' => array('FeedbackRequest.id'=>'DESC'),
		  'limit' => 5,		 
		);
		$requests = $this->paginate('FeedbackRequest');		
	
		$this->set('nb_lists', $nb_list);
		$this->set('eb_lists', $eb_list);
		$this->set('requests', $requests);
		
	}
	
	
	public function fancy_eb_list($id=null,$eb_org=null){
		//$this->autoRender = false ;
		
		$feedback=$this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$id)));
		$all_ebs=json_decode($feedback['FeedbackRequest']['existing_buyers']);
		$all_nbs=json_decode($feedback['FeedbackRequest']['new_buyers']);
		$s_id=$feedback['FeedbackRequest']['supplier_id'];
		$nb_id=$feedback['FeedbackRequest']['supplier_id'];
		
		$eb_list = $this->ExistingBuyer->find('all',array('conditions'=>array('NOT'=>array('ExistingBuyer.id'=>$all_ebs),'ExistingBuyer.supplier_id'=>$s_id)));
		//echo '<pre>';print_r($eb_list);die;
		$this->set('nb_id', $all_nbs);	
		$this->set('total_req', 1);	
		$this->set('req_id', $id);	
		$this->set('eb_org', $eb_org);	
		
		$this->set('existing_b', $eb_list);
	}
	
	
	public function res_eb_list($id=null){
		//$this->autoRender = false ;
		$loguser_id = self::_check_member_login();
		
	//	$feedback=$this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$id)));
	//	$all_ebs=json_decode($feedback['FeedbackRequest']['existing_buyers']);
	//	$all_nbs=json_decode($feedback['FeedbackRequest']['new_buyers']);
	//	$s_id=$feedback['FeedbackRequest']['supplier_id'];
	//	$nb_id=$feedback['FeedbackRequest']['supplier_id'];
		
	//	$eb_exp_data=$this->EbLoginDetail->find('list',array('conditions'=>array('EbLoginDetail.is_link_expire'=>1,'EbLoginDetail.eb_status'=>2),'fields'=>array('existing_buyer_id')));
		
		//echo "<pre>"; print_r($eb_exp_data); die;
		
	//	$eb_list = $this->ExistingBuyer->find('all',array('conditions'=>array('ExistingBuyer.supplier_id'=>$s_id,'ExistingBuyer.id'=>$eb_exp_data)));
		
		
		
		
		$eb_listss = $this->EbLoginDetail->find('all',array('conditions'=>array('ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1,'EbLoginDetail.eb_status'=>2,'EbLoginDetail.is_link_expire'=>1),'order'=>array('EbLoginDetail.id'=>'DESC'),'fields'=>array('EbLoginDetail.id','EbLoginDetail.eb_status','ExistingBuyer.first_name','ExistingBuyer.middle_name','ExistingBuyer.last_name','ExistingBuyer.org_name','ExistingBuyer.email_id','EbLoginDetail.request_id')));	
		
		
	//	echo "<pre>"; print_r($eb_listss); die;
	
		
		
		
	//	$this->set('nb_id', $all_nbs);	
		$this->set('total_req', 1);	
		$this->set('req_id', $id);	
	//	$this->set('eb_org', $eb_org);	
		
	$this->set('existing_b', $eb_listss);
	}
	
	public function confirm_resend($req_id=null,$eb_id=null){
		$this->System->set_seo('site_title','Confirm Payment');
		$this->System->set_data('banner_image',$this->System->get_setting('page','banner_image'));	
		$message = 'Link sent to this existing buyer is completely expired. Now you need to make a payment to resend the request to this existing buyer.';
		$feedback=$this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$req_id)));
		$all_ebs=json_decode($feedback['FeedbackRequest']['existing_buyers']);
		$all_nbs=json_decode($feedback['FeedbackRequest']['new_buyers']);
		$s_id=$feedback['FeedbackRequest']['supplier_id'];
		$nb_id=$feedback['FeedbackRequest']['supplier_id'];
		
		$eb_list = $this->ExistingBuyer->find('all',array('conditions'=>array('NOT'=>array('ExistingBuyer.id'=>$all_ebs),'ExistingBuyer.supplier_id'=>$s_id)));
		//echo '<pre>';print_r($eb_list);die;
		$this->set('nb_id', $all_nbs);	
		$this->set('total_req', 1);	
		$this->set('req_id', $req_id);	
		$this->set('eb_org', $eb_id);	
		
		$this->set('existing_b', $eb_list);
		$this->set('message', $message);
	}
	
	public function check_link_expire($id=null){
		$date=date('Y-m-d H:i:s');
		$today=strtotime(date('Y-m-d H:i:s'));
		$eb_login = $this->EbLoginDetail->find('all',array('conditions'=>array('EbLoginDetail.link_expire_date >='=>$date),'fields'=>array('id','link_expire_date','request_id')));	
		foreach($eb_login as $eb_log){
			$ex_date=strtotime($eb_log['EbLoginDetail']['link_expire_date']);
			if($today>=$ex_date){
				$this->EbLoginDetail->updateAll(array('EbLoginDetail.is_link_expire' =>3),array('EbLoginDetail.id' => $eb_log['EbLoginDetail']['id']));
			}
		}
		return true;
	}
	
	
	/*public function complete_request(){
		$loguser_id = self::_check_member_login();	
		$this->System->set_seo('site_title','Pending Request');
		$this->System->set_data('banner_image',$this->System->get_setting('page','banner_image'));			
				
		$nb_list = $this->NewBuyer->find('all',array('conditions'=>array('NewBuyer.status'=>1,'NewBuyer.pass_update'=>1),'fields'=>array('id','first_name','last_name','email_id','required_feedback')));	
	
		$eb_list = $this->EbLoginDetail->find('all',array('conditions'=>array('NOT'=>array('EbLoginDetail.payment_date'=>''),'ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1),'fields'=>array('EbLoginDetail.id','EbLoginDetail.is_link_expire','EbLoginDetail.payment_date','EbLoginDetail.resend_date','EbLoginDetail.link_expire_date','ExistingBuyer.id','ExistingBuyer.first_name','ExistingBuyer.first_name','ExistingBuyer.first_name','ExistingBuyer.last_name','ExistingBuyer.email_id','EbLoginDetail.request_id','FeedbackResponse.*')));
		
		foreach($eb_list as $eb){
			if(empty($eb['FeedbackResponse']['response_status'])){
				if(isset($eb['EbLoginDetail']['resend_date']) && (strtotime($eb['EbLoginDetail']['resend_date'])<=strtotime(date('Y-m-d H:i:s')))){
					$this->EbLoginDetail->updateAll(array('EbLoginDetail.is_link_expire' =>3), array('EbLoginDetail.id' =>$eb['EbLoginDetail']['id']));
				}
			}
		}
		//print_r($eb_list);die;
		//$this->FeedbackRequest->unbindModel(array('belongsTo' => array('Supplier')));
		$this->FeedbackRequest->bindModel(array('belongsTo' => array('Payment')));
		$this->paginate['FeedbackRequest'] = array(
		  'conditions'=>array('FeedbackRequest.supplier_id'=>$loguser_id,'FeedbackRequest.'),
		  'order' => array('FeedbackRequest.id'=>'DESC'),
		  'limit' => 5,		 
		);
		$requests = $this->paginate('FeedbackRequest');		
		
		$this->set('nb_lists', $nb_list);
		$this->set('eb_lists', $eb_list);
		$this->set('requests', $requests);
		
	}*/
	public function resend_request($eb_id=null,$req_id=null,$new_eb_id=null){
		$this->autoRender = false;			
		$Suppliers = new SuppliersController;
		$loguser_id = self::_check_member_login();	
		$date=date("Y-m-d H:i:s");
		
		if(!empty($this->request->data)){
		
			$expire_id=$this->request->data['ebs_id'];
			foreach($expire_id as $key=>$value){
								if($value == 0){ unset($expire_id[$key]); }
				}
			
	//	echo "<pre>"; print_r($expire_id); die;
		
		
				
	//	$req_id=$this->request->data['Supplier']['req_id'];
	/*
		$feedback_data=$this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$this->request->data['Supplier']['req_id']),'recursive'=>-1));
				
				
		$old_eb=json_decode($feedback_data['FeedbackRequest']['existing_buyers']);
		$new_eb=$this->request->data['Supplier']['eb_id'];
		foreach($new_eb as $key=>$value){
			if($value == 0){unset($new_eb[$key]);}
		 }
				  
		$all_eb=array_merge($old_eb, $new_eb);
		$all_u_eb=array_unique($all_eb);
		
		$all_ex_by=json_encode($all_u_eb);
		$this->FeedbackRequest->updateAll(array("FeedbackRequest.resend_date" =>"'$date'","FeedbackRequest.existing_buyers" =>"'$all_ex_by'"), array('FeedbackRequest.id' =>$req_id));		;		
	*/	
		
		//echo $loguser_id;die;
		foreach($expire_id   as   $eblog_id){
			
			$eb_log_data=$this->EbLoginDetail->find('first',array('conditions'=>array('EbLoginDetail.id'=>$eblog_id),'fields'=>array('id','request_id','existing_buyer_id')));
			
		if(!empty($eb_log_data)){
		
			$req_id=$eb_log_data['EbLoginDetail']['request_id'];
			$elog_id=$eb_log_data['EbLoginDetail']['id'];
			$eb_id=$eb_log_data['EbLoginDetail']['existing_buyer_id'];
			
					$expDate = strtotime($date.' + 1 week');
					$expDate=date('Y-m-d H:i:s',$expDate);	
					//print_r($feedback);die;
					
					$this->EbLoginDetail->updateAll(array("EbLoginDetail.resend_date" =>"'$date'","EbLoginDetail.link_expire_date" =>"'$expDate'"), array("EbLoginDetail.id" =>$elog_id,"EbLoginDetail.request_id" =>$req_id));
					
					$this->FeedbackRequest->updateAll(array("FeedbackRequest.resend_date" =>"'$date'"), array('FeedbackRequest.id' =>$req_id));				
					
					if($this->existing_send_mail($eb_id,$req_id)){
						$this->Session->setFlash(__('Your request has been resent to the selected existing buyer(s).'),'default',array(),'success');			
					}else{
						$this->Session->setFlash(__('Your request not resent to the selected existing buyer(s). Please try again.'),'default',array(),'error');
					}
			    }
			}
			   $this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'resend_success')); 
			    
			 }	
	}
	
	
	public function resend_request1($eb_id=null,$req_id=null,$new_eb_id=null){
		$this->autoRender = false;			
		$Suppliers = new SuppliersController;
		//$page = $Suppliers->__load_page(52);
		$loguser_id = self::_check_member_login();	
		$date=date("Y-m-d H:i:s");
		
		//echo $loguser_id;die;
		if(!empty($eb_id) && !empty($req_id)){
			$feedback=$this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$req_id)));
			$existing_b=json_decode($feedback['FeedbackRequest']['existing_buyers']);
			
			if(in_array($eb_id,$existing_b)){			
			$eb_data=$this->EbLoginDetail->find('first',array('conditions'=>array('EbLoginDetail.existing_buyer_id'=>$eb_id,'EbLoginDetail.request_id'=>$req_id),'fields'=>array('id','is_link_expire')));
			//print_r($eb_data);die;
			if(!empty($eb_data['EbLoginDetail']['is_link_expire']) && $eb_data['EbLoginDetail']['is_link_expire']==3){
				$this->redirect(array('plugin'=>'supplier_manager','controller'=>'requests','action'=>'confirm_resend',$req_id,$eb_id));	
			}else{
				//echo 11;die;
					$expDate = strtotime($date.' + 1 week');
					$expDate=date('Y-m-d H:i:s',$expDate);	
					//print_r($feedback);die;
					
					$this->EbLoginDetail->updateAll(array("EbLoginDetail.resend_date" =>"'$date'","EbLoginDetail.link_expire_date" =>"'$expDate'"), array("EbLoginDetail.existing_buyer_id" =>$eb_id,"EbLoginDetail.request_id" =>$req_id));
					
					$this->FeedbackRequest->updateAll(array("FeedbackRequest.resend_date" =>"'$date'"), array('FeedbackRequest.id' =>$req_id));				
					
					if($this->existing_send_mail($eb_id,$req_id)){
						$this->Session->setFlash(__('Your request has been resent to the selected existing buyer(s).'),'default',array(),'success');			
					}else{
						$this->Session->setFlash(__('Your request not resent to the selected existing buyer(s). Please try again.'),'default',array(),'error');
					}
			    }
			 }/*else{
					$expDate = strtotime($date.' + 2 week');
					$epDate=date('Y-m-d H:i:s',$expDate);		
					
					$Suppliers->save_card($req_id,$eb_id);
					
					$this->EbLoginDetail->updateAll(array('EbLoginDetail.resend_date' =>$date,'EbLoginDetail.link_expire_date' =>$expDate), array('EbLoginDetail.existing_buyer_id' =>$eb_id));
					$this->FeedbackRequest->updateAll(array('FeedbackRequest.resend_date' =>$date), array('FeedbackRequest.id' =>$req_id));
					if($this->existing_send_mail($eb_id,$req_id)){						
						$this->Session->setFlash(__('Your request has been resent to the selected existing buyer(s).'),'default',array(),'success');			
					}else{
						
						$this->Session->setFlash(__('Your request not resent to the selected existing buyer(s). Please try again.'),array(),'error');
					}
				}*/
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'requests','action'=>'pending_request'));
		}else{
			$this->redirect( Router::url( $this->referer(), true ) );
		}
		
		//$this->set('page', $page);		
	}
	
	public function forward_request($req_id=null){
		//$this->autoRender = false;	
		$loguser_id = self::_check_member_login();
		$this->System->set_seo('site_title','Forward Feedback');
		$this->System->set_data('banner_image',$this->System->get_setting('page','banner_image'));
		
		$fb_req=$this->FeedbackRequest->read(null,$req_id);
		
		$nbs=json_decode($fb_req['FeedbackRequest']['new_buyers']);
		$this->NewBuyer->unbindModel(array('hasMany' => array('NewBuyerQuestion')));
		$nb_list = $this->NewBuyer->find('all',array('conditions'=>array('NewBuyer.status'=>1,'NewBuyer.id'=>$nbs),'fields'=>array('id','first_name','last_name','email_id','required_feedback')));
		
		$response=array();
		$fb_count=$this->FeedbackResponse->find('count',array('conditions'=>array('FeedbackResponse.request_id'=>$req_id,'FeedbackResponse.response_status'=>2)));
		
		$report_status = json_decode($fb_req['FeedbackRequest']['is_report_sent'],true);
		
		//$count = count($nb_list);
		//echo $count; die;
		//if(!empty($this->request->data['Request']['form'])){			
		//	$nb1=$this->request->data['NewBuyer']['id'];
			//$response=array();
			//$fb_res=$this->FeedbackResponse->find('all',array('conditions'=>array('FeedbackResponse.request_id'=>$this->request->data['Request']['id'],'FeedbackResponse.response_status'=>2)));	
		//	print_r($fb_res);die;
		
		/*$read_datas=$this->NewBuyer->find('all',array('conditions'=>array('NewBuyer.id'=>$nb1),'fields'=>array('NewBuyer.id','NewBuyer.org_name','NewBuyer.first_name','NewBuyer.last_name','NewBuyer.required_feedback')));
		
			foreach($fb_res as $fb_resp){				
				foreach($read_datas as $read_data){
					foreach($read_data['NewBuyerQuestion'] as $read_dataNewBuyerQuestion){
						$question_id[]=$read_dataNewBuyerQuestion['question_id'];
					}
				}
				$answers=json_decode($fb_resp['FeedbackResponse']['answers']);
				foreach($answers as $key=>$value){
					if(in_array($key,$question_id)){
						$response[$key]=$value;
					}
				}
				
				$descriptive_ans=json_decode($fb_resp['FeedbackResponse']['descriptive_ans']);			
				$fb_resp['FeedbackResponse']['existing_buyer_id'];
				$fb_resp['FeedbackResponse']['answers'];
				$fb_resp['FeedbackResponse']['descriptive_ans'];			
			}
			
		//	echo '<pre>';print_r($question_id);die;  
		}		*/
		$this->set('report_status', $report_status);
		$this->set('req_id', $req_id);		
		$this->set('nb_lists', $nb_list);
		$this->set('count', $fb_count);	
		//echo "response=>".$count;	
		//echo $fb_count; die;
	}
	public function forward_req($req_id=null){
		
	  /*====================GET DATA FROM URL AS ARRAY INDEX 0 HAVE REQUEST ID AND INDEX 1 HAVE NEWBUYER ID===============*/
		
		if(!empty($req_id)){
			$requestIdArray = explode('-',base64_decode($req_id));
			$requestId = $requestIdArray[0];
			$newBuyerId = $requestIdArray[1];
		 }
		 
		$loguser_id = self::_check_member_login();
		
		$this->System->set_seo('site_title','Forward Feedback');
		
		$this->System->set_data('banner_image',$this->System->get_setting('page','banner_image'));
		
		$fb_req=$this->FeedbackRequest->read(null,$requestId);
		
		$this->NewBuyer->unbindModel(array('hasMany' => array('NewBuyerQuestion')));
		
		$newBuyerDetails = $this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.status'=>1,'NewBuyer.id'=>$newBuyerId)));		
		
		$cat_ref=$this->SupplierBuyer->find('first',array('conditions'=>array('SupplierBuyer.supplier_id'=>$loguser_id,'SupplierBuyer.buyer_id'=>$newBuyerId),'fields'=>array('SupplierBuyer.category','SupplierBuyer.reference_num')));
		
		
		$totalFeedbackRequired = $fb_req['SupplierBuyer']['required_feedback'];
		
		$nb_eb_format=json_decode($fb_req['FeedbackRequest']['selected_new_b_exist'],true);
		
		$selectedExistingBuyer = $nb_eb_format[$newBuyerDetails['NewBuyer']['id']];
		
		$existing_buyers_list = $this->ExistingBuyer->find('all',array('conditions'=>array('ExistingBuyer.id'=>$selectedExistingBuyer)));
		
		$responseStatusOfExistingBuyer = $this->EbLoginDetail->find('all',array('conditions'=>array('EbLoginDetail.existing_buyer_id'=>$selectedExistingBuyer) ,'fields'=>array('eb_status')));	
        
        $newResponseArr = array_column(array_column($responseStatusOfExistingBuyer , 'EbLoginDetail') , 'eb_status');
        
        $getButtonAction = min($newResponseArr);
	
		if($getButtonAction == 6){
			$needToNext  = 1;
				
		}else{
			$needToNext  = 0;			
		}
			
		$this->set('button_next', $needToNext);
		
		$_nb_list = $newBuyerDetails;
		$_nb_list['NewBuyer']['exist'] = $existing_buyers_list;
		$_nb_list['NewBuyer']['category'] = $cat_ref['SupplierBuyer']['category']; 
		$_nb_list['NewBuyer']['reference_num']=$cat_ref['SupplierBuyer']['reference_num']; 
		
		
		/*$nbs=json_decode($fb_req['FeedbackRequest']['new_buyers']);
		
		$this->NewBuyer->unbindModel(array('hasMany' => array('NewBuyerQuestion')));
		
		//$nb_list = $this->NewBuyer->find('all',array('conditions'=>array('NewBuyer.status'=>1,'NewBuyer.id'=>$nbs)));
		
		$nb_list = $this->NewBuyer->find('all',array('conditions'=>array('NewBuyer.status'=>1,'NewBuyer.id'=>$newBuyerId)));
		
		$new_nb_list=array();
		
		foreach($nb_list as $_nb_list){
			
			$eb_id=json_decode($fb_req['FeedbackRequest']['existing_buyers']);
		
		$cat_ref=$this->SupplierBuyer->find('first',array('conditions'=>array('SupplierBuyer.supplier_id'=>$loguser_id,'SupplierBuyer.buyer_id'=>$_nb_list['NewBuyer']['id']),'fields'=>array('SupplierBuyer.category','SupplierBuyer.reference_num')));
			
			$_nb_list['NewBuyer']['category']=$cat_ref['SupplierBuyer']['category']; 
			
			$_nb_list['NewBuyer']['reference_num']=$cat_ref['SupplierBuyer']['reference_num']; 
					
			$nb_eb_format=json_decode($fb_req['FeedbackRequest']['selected_new_b_exist'],true);
			
			$eb_added_id=$nb_eb_format[$_nb_list['NewBuyer']['id']];
			
			$existing_buyers_lis=$this->ExistingBuyer->find('all',array('conditions'=>array('ExistingBuyer.id'=>$eb_added_id)));		
						
			$_nb_list['NewBuyer']['exist']=$existing_buyers_lis;
			
			$new_nb_list[]=$_nb_list;
		}*/
		
		//$response=array();
		//$fb_count=$this->FeedbackResponse->find('count',array('conditions'=>array('FeedbackResponse.request_id'=>$req_id,'FeedbackResponse.response_status'=>2)));
		
		$fb_count=$this->FeedbackResponse->find('count',array('conditions'=>array('FeedbackResponse.request_id'=>$requestId,'FeedbackResponse.response_status'=>2)));
		
		if($fb_req['FeedbackRequest']['request_use']==1){
			$ebs=json_decode($fb_req['FeedbackRequest']['existing_buyers']);
			
			$fb_count=$this->FeedbackResponse->find('count',array('conditions'=>array('FeedbackResponse.existing_buyer_id'=>$ebs,'FeedbackResponse.response_status'=>2)));
				
		} else if($fb_req['FeedbackRequest']['request_use']==2){
			
			$ebs=json_decode($fb_req['FeedbackRequest']['existing_buyers']);
			
			$fb_count=$this->FeedbackResponse->find('count',array('conditions'=>array('FeedbackResponse.existing_buyer_id'=>$ebs,'FeedbackResponse.response_status'=>2)));
		}
		
		$report_status = json_decode($fb_req['FeedbackRequest']['is_report_sent'],true);
		
		$cat_arr = array('t1'=>'Tier1','t2'=>'Tier2','t3'=>'Tier3','t4'=>'Tier4');
		
		//$count = count($nb_list);
		//echo $count; die;
		//if(!empty($this->request->data['Request']['form'])){			
		//	$nb1=$this->request->data['NewBuyer']['id'];
			//$response=array();
			//$fb_res=$this->FeedbackResponse->find('all',array('conditions'=>array('FeedbackResponse.request_id'=>$this->request->data['Request']['id'],'FeedbackResponse.response_status'=>2)));	
		//	print_r($fb_res);die;
		
		/*$read_datas=$this->NewBuyer->find('all',array('conditions'=>array('NewBuyer.id'=>$nb1),'fields'=>array('NewBuyer.id','NewBuyer.org_name','NewBuyer.first_name','NewBuyer.last_name','NewBuyer.required_feedback')));
		
			foreach($fb_res as $fb_resp){				
				foreach($read_datas as $read_data){
					foreach($read_data['NewBuyerQuestion'] as $read_dataNewBuyerQuestion){
						$question_id[]=$read_dataNewBuyerQuestion['question_id'];
					}
				}
				$answers=json_decode($fb_resp['FeedbackResponse']['answers']);
				foreach($answers as $key=>$value){
					if(in_array($key,$question_id)){
						$response[$key]=$value;
					}
				}
				
				$descriptive_ans=json_decode($fb_resp['FeedbackResponse']['descriptive_ans']);			
				$fb_resp['FeedbackResponse']['existing_buyer_id'];
				$fb_resp['FeedbackResponse']['answers'];
				$fb_resp['FeedbackResponse']['descriptive_ans'];			
			}
			
		//	echo '<pre>';print_r($question_id);die;  
		}		*/
		
		//$referer_url=Router::url('/admin/new_buyer_manager/new_buyers',true);
		
		
		$this->set('report_status', $report_status);
		$this->set('req_id', $req_id);		
		$this->set('nb_lists', $_nb_list);
		$this->set('count', $fb_count);	
		$this->set('categoryArr', $cat_arr);	
	}
	
	
	//public function report($request_id=null,$nb_id=null){
	
	public function report($request_id=null){
		
		/*====================GET DATA FROM URL AS ARRAY INDEX 0 HAVE REQUEST ID AND INDEX 1 HAVE NEWBUYER ID===============*/
		
		$requestInfo = base64_decode($request_id);
		
		$requestInfoArr = explode('-',$requestInfo);
		
		$request_id = $requestInfoArr[0];
		
		$nb_id = $requestInfoArr[1];
		
		$loguser_id = self::_check_member_login();
		
		$request_detail = $this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$request_id)));
		
		
		if(!empty($request_detail)){
			
			$getSelectedExistingBuyer = json_decode($request_detail['FeedbackRequest']['selected_new_b_exist'],true);
			
			$getExistingBuyerId = $getSelectedExistingBuyer[$nb_id];
			
			$this->EbLoginDetail->updateAll(array('EbLoginDetail.eb_status' =>7),array('EbLoginDetail.request_id' => $request_id,'EbLoginDetail.existing_buyer_id'=>$getExistingBuyerId));
		   		    
			$country = $this->Country->find('first',array('conditions'=>array('Country.country_code_char2'=>$request_detail['Supplier']['country']),'fields'=>array('Country.country_name')));
			
			$newbuyer_detail = $this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.id'=>$nb_id)));
			
			$nb_detail = $this->SupplierBuyer->find('first',array('conditions'=>array('SupplierBuyer.buyer_id'=>$nb_id,'SupplierBuyer.supplier_id'=>$loguser_id)));
			
			$limit = $newbuyer_detail['NewBuyer']['required_feedback'];
			
			$nb_logo = $newbuyer_detail['NewBuyer']['logo'];
			
			$profile_ques = $this->NewBuyerQuestion->find('all',array('conditions'=>array('NewBuyerQuestion.new_buyer_id'=>$nb_id)));
			
			foreach($profile_ques as $pro_ques){
				 $ques_tier=explode(',',$pro_ques['NewBuyerQuestion']['question_tier']);
				 
				if(in_array($nb_detail['SupplierBuyer']['category'],$ques_tier)){
					$profile_questions[] = $pro_ques['NewBuyerQuestion']['question_id'];
				}
			}
		
			//$eb_list = json_decode($request_detail['FeedbackRequest']['existing_buyers']);
			
			//$eb_details = $this->ExistingBuyer->find('all',array('conditions'=>array('ExistingBuyer.id'=>$eb_list)));	
			
			$currntdate = date('Y-m-d');
			$this->set('limit',$limit);
			$this->set('country',$country);
			$this->set('currntdate',$currntdate);
			
			$cat_list = array();
			$des_ques = json_decode($request_detail['FeedbackRequest']['descriptive_ques']);
			$des_questions = array();
			foreach($des_ques as $ques){
				$result= $this->Question->findById($ques);
				$des_questions[$ques] = $result['Question']['question'];
				if(!(in_array($result['Question']['category_id'], $cat_list))){
					$cat_list[] = $result['Question']['category_id'];
				}
			}
			$this->set('des_questions',$des_questions);
			
			$responses = $this->FeedbackResponse->find('all',array('conditions'=>array('FeedbackResponse.request_id'=>$request_id,'FeedbackResponse.response_status'=>2),'limit'=>$limit));
			
			
	if($request_detail['FeedbackRequest']['request_use']==1){
		
			$ebs=json_decode($request_detail['FeedbackRequest']['existing_buyers']);
			
			$responses=$this->FeedbackResponse->find('all',array('conditions'=>array('FeedbackResponse.existing_buyer_id'=>$ebs,'FeedbackResponse.response_status'=>2),'limit'=>$limit));
			
	} else if($request_detail['FeedbackRequest']['request_use']==2){
		
			$ebs=json_decode($request_detail['FeedbackRequest']['existing_buyers']);
			
			$responses=$this->FeedbackResponse->find('all',array('conditions'=>array('FeedbackResponse.existing_buyer_id'=>$ebs,'FeedbackResponse.response_status'=>2),'limit'=>$limit));
			
		}	
		
		
		//	echo "<pre>";  print_r($responses); die;
			
			$e_buyers = array();
			foreach($responses as $response){
				$e_buyers[] = $response['FeedbackResponse']['existing_buyer_id'];
				
			}
			$eb_details = $this->ExistingBuyer->find('all',array('conditions'=>array('ExistingBuyer.id'=>$e_buyers)));
			$this->set('eb_details',$eb_details);
			$this->set('responses',$responses);
			//pr($responses); die;
			
			
			
			$scale_ques = json_decode($request_detail['FeedbackRequest']['questions']);
			$cat_list1 = array();
			foreach($scale_ques as $ques){
				if(in_array($ques,$profile_questions)){
					$result= $this->Question->findById($ques);
					$scale_questions[$ques] = array('id'=>$result['Question']['id'],'question'=>$result['Question']['question'],'options'=>json_decode($result['Question']['options'],true),'ques_cat'=>$result['Question']['category_id']);
					if(!(in_array($result['Question']['category_id'], $cat_list1))){
						$cat_list1[] = $result['Question']['category_id'];
					}
				}
			}
			$des_categories = $this->QuestionCategorie->find('all',array('conditions'=>array('QuestionCategorie.id'=>$cat_list)));
			$categories = $this->QuestionCategorie->find('all',array('conditions'=>array('QuestionCategorie.id'=>$cat_list1)));
			
			$no_comment = array();
			$no_comment_ebuyer = array();
			$no_comment_cat = array();
			foreach($scale_questions as $s_ques){
				$s_ans = array();
				foreach($responses as $response){
					$ans = json_decode($response['FeedbackResponse']['answers'],true);
					if($ans[$s_ques['id']] !=6){   //no comment option is not included
						$s_ans[] = $ans[$s_ques['id']];
					}else{
						$no_comment[] = array('question' => $s_ques['id'],'ebuyer'=>$response['FeedbackResponse']['existing_buyer_id']) ;
					}
				}
				//pr($s_ans);
				$avrage_ans = array_sum($s_ans) / count($s_ans);
				$avrage_value[$s_ques['id']] = $avrage_ans;
				$average[$s_ques['id']] = $this->__get_average($avrage_ans,$s_ques['id']);
			}
			$highest = $this->__get_highest($avrage_value);
			
			
			if(!empty($no_comment)){
				foreach($no_comment as $value){
					$result= $this->Question->findById($value['question']);
					$result1= $this->ExistingBuyer->findById($value['ebuyer']);
					
					if (!(array_key_exists($result1['ExistingBuyer']['id'],$no_comment_ebuyer))){
						
							$no_comment_ebuyer[$result1['ExistingBuyer']['id']] = $result1;		
					}	
					if (!(array_key_exists($result['QuestionCategorie']['id'],$no_comment_cat))){
						$no_comment_cat[$result['QuestionCategorie']['id']] = $result['QuestionCategorie']['name'];
					}
				}

			}
			//echo "<pre>";print_r($request_detail);die;
			$this->set('no_comment_cat',$no_comment_cat);
			$this->set('no_comment_ebuyer',$no_comment_ebuyer);
			$this->set('supplier_detail',$supplier_detail);
			$this->set('highest',$highest);
			$this->set('nb_detail',$nb_detail);
			$this->set('categories',$categories);
			$this->set('des_categories',$des_categories);
			$this->set('scale_questions',$scale_questions);
			//pr($responses); die;
			$this->set('request_id',$request_id);
			$this->set('request_detail',$request_detail);
			$this->set('nb_id',$nb_id);
			$this->set('average',$average);
			$this->set('nb_logo',$nb_logo);
		}
	}
	
	
	private function __get_highest($avrage_value = array()){
		$temp = array();
		if(!empty($avrage_value)){		
			foreach($avrage_value as $key=>$value ){
					
				$result= $this->Question->findById($key);
				//print_r($result); die;
				//$option = $this->__get_average($value);
				if (array_key_exists($result['QuestionCategorie']['name'],$temp)){
					if( $value > $temp[$result['QuestionCategorie']['name']]){
						
						$temp[$result['QuestionCategorie']['name']] = $value;			
					}
				}else{
					$temp[$result['QuestionCategorie']['name']] = $value;
				}
				//$data[$key] = array('category'=> $result['QuestionCategorie']['name'],'avg'=>$value);
			}
			return $temp;
			
		}
	}
	private function __get_average($value =null, $question_id){
		if($value){
			$option;
			$result= $this->Question->findById($question_id);
			//pr($result); die;
			$q_options = json_decode($result['Question']['options'],true);
			//pr($q_options); die;
			$option_value;
			if ($value >= 0 && $value <=1) {
				$option_value = $q_options[1];
				$option = $option_value;
			
			}elseif($value > 1 && $value <= 1.5){
				$option_value = $q_options[1];
				$option = 'slightly better than '.$option_value;
				
			}elseif($value > 1.5 && $value < 2){
				$option_value = $q_options[2];
				$option = 'almost '.$option_value;
				
			}elseif($value == 2){
				$option_value = $q_options[2];
				$option = $option_value;
				
			}elseif($value > 2 && $value <= 2.5){
				$option_value = $q_options[2];
				$option = 'slightly better than '.$option_value;
				
			}elseif($value > 2.5 && $value < 3){
				$option_value = $q_options[3];
				$option = 'almost '.$option_value;
				
			}elseif($value == 3){
				$option_value = $q_options[3];
				$option = $option_value;
				
			}elseif($value > 3 && $value <= 3.5){
				$option_value = $q_options[3];
				$option = 'slightly better than '.$option_value;
				
			}elseif($value > 3.5 && $value < 4){
				$option_value = $q_options[4];
				$option = 'almost '.$option_value;
				
			}elseif($value == 4){
				$option_value = $q_options[4];
				$option = $option_value;
				
			}elseif($value > 4 && $value <= 4.5){
				$option_value = $q_options[4];
				$option = 'slightly better than '.$option_value;
				
			}elseif($value > 4.5 && $value < 5){
				$option_value = $q_options[5];
				$option = 'almost '.$option_value;
				
			}elseif($value == 5){
				$option_value = $q_options[5];
				$option = $option_value;
				
			}
			return $option;
		}
	}
	
	public function send_report($request_id=null,$nb_id=null){
		
		$this->autoRender = false;
		
		$request_detail = $this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$request_id)));
		// pr($request_detail); die;
		$nb_detail = $this->NewBuyer->find('first',array('conditions'=>array('NewBuyer.id'=>$nb_id)));
		//mail to newbuyer
		$options = array();
		$options['replacement'] = array(
		'{Supplier}'=>$request_detail['Supplier']['title']." ".$request_detail['Supplier']['first_name']." ".$request_detail['Supplier']['middle_name']." ".$request_detail['Supplier']['last_name'],
		'{New_Buyer}'=>$nb_detail['NewBuyer']['title']." ".$nb_detail['NewBuyer']['first_name']." ".$nb_detail['NewBuyer']['middle_name']." ".$nb_detail['NewBuyer']['last_name']);	
		$options['attachments'] = array(
							'Feedback_Report.pdf' =>WWW_ROOT. 'files/pdf' . DS . 'feedback_report_'.$request_id.$nb_id.'.pdf',
					);		
		$options['to'] = array($nb_detail['NewBuyer']['email_id']); 		
		$this->MyMail->SendMail(25,$options);
		
		//mail to supplier
		$options = array();	
		$options['replacement'] = array('{Supplier}'=>$request_detail['Supplier']['title']." ".$request_detail['Supplier']['first_name']." ".$request_detail['Supplier']['middle_name']." ".$request_detail['Supplier']['last_name'],'{New_Buyer}'=>$nb_detail['NewBuyer']['title']." ".$nb_detail['NewBuyer']['first_name']." ".$nb_detail['NewBuyer']['middle_name']." ".$nb_detail['NewBuyer']['last_name']);		
		$options['to'] = $request_detail['Supplier']['email_id']; 
		$options['from'] = $this->System->get_setting('site','site_contact_noreply');	
		$this->MyMail->SendMail(26,$options);
		
		$request_detail = $this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$request_id),'fields'=>array('FeedbackRequest.id','FeedbackRequest.is_report_sent')));
		
		$report_status = json_decode($request_detail['FeedbackRequest']['is_report_sent'],true);
		if($report_status[$nb_id] == 0){
			$report_status[$nb_id] = 1;
		
			$data['FeedbackRequest']['id'] = $request_id;
			$data['FeedbackRequest']['is_report_sent'] = json_encode($report_status);
			$this->FeedbackRequest->create();
			$this->FeedbackRequest->save($data,array('validate'=>false));
		}
		
	//$this->Session->setFlash(__('Your feedback report has been sent successfully to the buyer.'),'default',array(),'success');
		
	//	$this->redirect(array('plugin'=>'supplier_manager','controller'=>'requests','action'=>'forward_req',$request_id));
	
		$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'send_success'));
	}

	public function existing_send_mail($id=null,$request_id=null){		
		$this->autoRender = false;
		$Suppliers = new SuppliersController;
		$this->loadModel('SupplierManager.FeedbackRequest');
		if($id != null){
			
			$user = $this->ExistingBuyer->find('first',array('conditions'=>array('ExistingBuyer.id'=>$id)));
			$randompassword=$Suppliers->_randomPassword();		
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
				$options['replacement'] = array('{NAME}'=>$name,'{SUPPLIER}'=>$supplier_name,'{EMAIL}'=>$email,'{URL}'=>$url);
				$options['to'] = array($email); 
				
				$this->MyMail->SendMail(21,$options);
				
				$options = array();
				$options['replacement'] = array('{NAME}'=>$name,'{SUPPLIER}'=>$supplier_name,'{PASSWORD}'=>$randompassword);
				$options['to'] = array($email); 
				$this->MyMail->SendMail(22,$options);
				
				$login_details = array();
				$link_expire_date = '';
				
				$feedback=$this->FeedbackRequest->find('first',array('conditions'=>array('FeedbackRequest.id'=>$request_id)));
				
				$eb_detail=$this->EbLoginDetail->find('first',array('conditions'=>array('EbLoginDetail.request_id'=>$request_id,'EbLoginDetail.existing_buyer_id'=>$id,'NOT'=>array('EbLoginDetail.resend_date'=>''))));
			
				$expDate = strtotime($feedback["FeedbackRequest"]["created_date"].' + 2 week');				
				$expDate=date('Y-m-d H:i:s',$expDate);				
				$payment_date = $feedback['FeedbackRequest']['created_date'];
			
				if(!empty($eb_detail)){
					$login_details['EbLoginDetail']['id'] = $eb_detail['EbLoginDetail']['id'];
					$login_details['EbLoginDetail']['is_link_expire'] = 2;
					$login_details['EbLoginDetail']['eb_status'] = 3;
				}else{
					$login_details['EbLoginDetail']['is_link_expire'] = 0;
					$login_details['EbLoginDetail']['eb_status'] = 1;
					$login_details['EbLoginDetail']['link_expire_date'] = $expDate;
					$login_details['EbLoginDetail']['payment_date'] = $payment_date;
				}
				$login_details['EbLoginDetail']['request_id']= $request_id;
				$login_details['EbLoginDetail']['existing_buyer_id']= $id;
				$login_details['EbLoginDetail']['password']= $pass;
				$login_details['EbLoginDetail']['passwordurl'] =  $urlValue;
				 
				$this->EbLoginDetail->create();
				$this->EbLoginDetail->save($login_details,array('validate'=>false));
				return true;
				//$this->redirect('/');
			}
		}
	} 

}
?>
