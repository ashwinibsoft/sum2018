<?php
Class PaymentsController extends SupplierManagerAppController{
	public $uses = array('SupplierManager.Supplier','ContentManager.Page','Country','SupplierManager.SupplierBuyer','NewBuyerManager.NewBuyer','ExistingBuyerManager.ExistingBuyer','NewBuyerManager.NewBuyerQuestion','ExistingBuyerManager.EbLoginDetail','SupplierManager.FeedbackRequest','SupplierManager.Payment');
	public $components=array('Email','RequestHandler','Image');
	var $helpers = array('Captcha','Csv');
	public $paginate = array();
	public $id = null;
	public $template=null;
	

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
			$this->redirect(array('plugin'=>'supplier_manager','controller'=>'payments','action'=>'index',$search,$limit));
		}
		if($search!=null){
			$search = urldecode($search);	
			$condition['OR'][]=array('OR'=>array('Supplier.first_name like'=>'%'.$search.'%','Supplier.last_name like'=>'%'.$search.'%','Supplier.title like'=>'%'.$search.'%'));
		}
		/*$this->paginate['joins'] = array( 
					array(
						'table' => 'suppliers',
						'alias' => 'Supplier',
						'type' => 'LEFT',
						'conditions' => array('`Payment`.`supplier_id` = `Supplier`.`id`')
						),
                    
                    );*/
		
		$payments = array();
		$this->paginate['order']=array('Payment.id'=>'DESC');
		$payments= $results=$this->paginate("Payment",$condition);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/supplier_manager/suppliers/payments'),
			'name'=>'Manage Payment'
		);
		
		$this->heading =  array("Manage","Payment");

		$this->set('payments',$payments);
		$this->set('limit',$limit);
		$this->set('search',$search);
		$this->set('url','/'.$this->params->url);
	}
	
	function admin_delete($id=null){
		$this->autoRender = false;
		//print_r($this->request->data); die;
		$data=$this->request->data['Payment']['id'];
		//print_r($data); die;
		$action = $this->request->data['Payment']['action'];
		$ans="0";
		foreach($data as $value){
			if($value!='0'){
				if($action=='Delete'){
					$this->Payment->delete($value);
					$ans="2";
				}
			}
		}
		
		if($ans=="1"){
			$this->Session->setFlash(__('Payment record has been '.strtolower($this->data['Payment']['action']).'ed successfully', true));
		}
		else if($ans=="2"){
			$this->Session->setFlash(__('Payment record has been '.strtolower($this->data['Payment']['action']).'d successfully', true));
		}else{
			$this->Session->setFlash(__('Please Select any Payment record', true),'default','','error');
		}
		$this->redirect($this->request->data['Payment']['redirect']);
                 
	}
	
	function admin_view($id=null){
		
		$this->breadcrumbs[] = array(
		'url'=>Router::url('/admin/home'),
		'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/supplier_manager/payments'),
				'name'=>'Manage Payments'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/supplier_manager/payments/view/'.$id),
				'name'=>'View Payment Detail'
		);
		
		$this->heading =  array("View","Payment Detail");
		
		$payment=$this->Payment->find('first',array('conditions'=>array('Payment.id'=>$id)));	
	//	echo '<pre>';print_r($payment);die;	
		$existing_buyers=json_decode($payment['FeedbackRequest']['existing_buyers']);
		
		$existing_buyers_list=$this->ExistingBuyer->find('all',array('conditions'=>array('ExistingBuyer.id'=>$existing_buyers)));

		$new_buyers=json_decode($payment['FeedbackRequest']['new_buyers']);
		$new_buyers_list=$this->NewBuyer->find('all',array('conditions'=>array('NewBuyer.id'=>$new_buyers)));
		//print_r($new_buyers_list);die;
		$this->set('existing_buyers_list',$existing_buyers_list);
		$this->set('new_buyers_list',$new_buyers_list);
		$this->set('payment',$payment);
		
	}
}
?>	
