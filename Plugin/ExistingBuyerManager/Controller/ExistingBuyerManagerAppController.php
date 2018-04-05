<?php
class ExistingBuyerManagerAppController extends AppController{
	public $page = array();
	function beforeFilter() {
		parent::beforeFilter();	
		Configure::load('ExistingBuyerManager.config');
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
}
?>
