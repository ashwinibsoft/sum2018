<?php
class NewBuyerManagerAppController extends AppController{
	public $components=array('MemberAuth');
	function beforeFilter() {
		parent::beforeFilter();
		Configure::load('NewBuyerManager.config');
		$memberType = (int)$this->MemberAuth->get_member_type();
	}
	
	function _check_member_login(){
		$memberId = $this->Session->read('MemberAuth.MemberAuth.id');
		if(empty($memberId)){
			$this->Session->setFlash(__('You are not authorized to access that location.'),'default',array(),'error');
			$this->redirect(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'login'));
		} else {
			return $memberId;
		}
	}
}
?>
