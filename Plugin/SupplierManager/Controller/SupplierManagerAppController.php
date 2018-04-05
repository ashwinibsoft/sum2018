<?php
class SupplierManagerAppController extends AppController{
	public $page = array();
	function beforeFilter() {
		parent::beforeFilter();	
		Configure::load('SupplierManager.config');
		$memberType = (int)$this->MemberAuth->get_member_type();
	}
	
	function _check_member_login(){
		$memberId = $this->Session->read('MemberAuth.MemberAuth.id');
		if(empty($memberId)){
			$this->Session->setFlash(__('You are not authorized to access that location.'),'default',array(),'error');
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'login'));
		} else {
			return $memberId;
		}
	}
	
	public function _check_expiry($request_id=null,$self_id=null){
		
		$loguser_id=$this->_check_member_login();
	//echo $loguser_id;die;
		//$eb_list1 = $this->EbLoginDetail->find('all',array('conditions'=>array('NOT'=>array('EbLoginDetail.payment_date'=>''))));
	
	//echo var_dump( $request_id);die;
		$this->ExistingBuyer->updateAll(array('ExistingBuyer.replace' =>0),array('ExistingBuyer.supplier_id'=>$loguser_id));
		 
		if($request_id!=null): 
		$eb_list = $this->EbLoginDetail->find('all',array('conditions'=>array('NOT'=>array('EbLoginDetail.payment_date'=>''),'EbLoginDetail.request_id'=>$request_id,'ExistingBuyer.id'=>$self_id,'ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1),'order'=>array('EbLoginDetail.id'=>'DESC'),'fields'=>array('EbLoginDetail.id','EbLoginDetail.is_link_expire','EbLoginDetail.payment_date','EbLoginDetail.resend_date','EbLoginDetail.link_expire_date','EbLoginDetail.eb_status','EbLoginDetail.password','EbLoginDetail.is_access','ExistingBuyer.id','ExistingBuyer.first_name','ExistingBuyer.first_name','ExistingBuyer.first_name','ExistingBuyer.last_name','ExistingBuyer.email_id','EbLoginDetail.request_id','FeedbackResponse.*')));	
		else:
		$eb_list = $this->EbLoginDetail->find('all',array('conditions'=>array('NOT'=>array('EbLoginDetail.payment_date'=>''),'ExistingBuyer.supplier_id'=>$loguser_id,'ExistingBuyer.status'=>1),'order'=>array('EbLoginDetail.id'=>'DESC'),'fields'=>array('EbLoginDetail.id','EbLoginDetail.is_link_expire','EbLoginDetail.payment_date','EbLoginDetail.resend_date','EbLoginDetail.link_expire_date','EbLoginDetail.eb_status','EbLoginDetail.password','EbLoginDetail.is_access','ExistingBuyer.id','ExistingBuyer.first_name','ExistingBuyer.first_name','ExistingBuyer.first_name','ExistingBuyer.last_name','ExistingBuyer.email_id','EbLoginDetail.request_id','FeedbackResponse.*')));	
		endif;
	//	echo '<pre>'; print_r($eb_list);die;	
		foreach($eb_list as $eb){ 
			if(!empty($eb['EbLoginDetail']['password'])){
			//		echo "pp"; die;
			if(empty($eb['FeedbackResponse']['response_status']) || $eb['FeedbackResponse']['response_status'] == 1){
				
				if(isset($eb['EbLoginDetail']['resend_date']) && (strtotime($eb['EbLoginDetail']['link_expire_date'])<=strtotime(date('Y-m-d H:i:s')))){
				
					$eb_status=2;
					if($eb['EbLoginDetail']['eb_status']==7){$eb_status=7;}
					$this->EbLoginDetail->updateAll(array('EbLoginDetail.is_link_expire' =>3,'EbLoginDetail.eb_status' =>$eb_status,'EbLoginDetail.passwordurl' =>"''"), array('EbLoginDetail.id' =>$eb['EbLoginDetail']['id']));
						
				}elseif(empty($eb['EbLoginDetail']['resend_date']) && (strtotime($eb['EbLoginDetail']['link_expire_date'])<=strtotime(date('Y-m-d H:i:s')))){
			
					$eb_status=2;
					if($eb['EbLoginDetail']['eb_status']==7){$eb_status=7;}
					$this->EbLoginDetail->updateAll(array('EbLoginDetail.is_link_expire' =>1,'EbLoginDetail.eb_status' =>$eb_status,'EbLoginDetail.passwordurl' =>"''"), array('EbLoginDetail.id' =>$eb['EbLoginDetail']['id']));
				}
			}
		}
	}
		//echo '<pre>';print_r($eb_list);die;
		return $eb_list;
		
	}
}
?>

