<?php
Class MenusController extends AppController {
	public $uses = array('Menu');
	public $paginate = array();
    public $id = null;
    
		/*This below function is used to display information from database and pagination*/
		
	function admin_index($search=null){
		
		/*This below code is used for pagination*/
		
		$this->paginate = array();
		$condition = null;
		$this->paginate['limit']=20;
		if($this->request->is('post')){
			$this->redirect(array('controller'=>'menus','action'=>'index',$this->request->data['search']));
		}
		$this->paginate['order']=array('Menu.id'=>'ASC');		
		if($search!=null){
			$search = urldecode($search);
			$condition['Menu.name like'] = $search.'%';
		}
		$menus=$this->paginate("Menu", $condition);	

		/*End of pagination*/
		
		/*This below code is used for adding breadcrumbs in page*/
		
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Home'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/menus'),
			'name'=>'Menu Manager'
		);
		
		/*End of adding breadcrumbs in page*/
		
		/*variables are being set for pages to pass data*/
		
		$this->set('menus',$menus);
		$this->set('search',$search);
		$this->set('url','/'.$this->params->url);
		
		/*End of setting variable*/
		
		/***This below code is used to return values from database table using rendering a file when loader is working if needed***/
	
		if($this->request->is('ajax')){
		$this->layout = '';
		$this -> Render('ajax_admin_index');
		}
	
	/***End of ajax request***/ 
	   
	}
	
	/*End of function*/
		
	/*Function for add/update data*/
		 
	function admin_add($id=null){
		
		/*Below code to add breadcrumbs in page*/
		
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Home'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/menus'),
			'name'=>'Manage Menu'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/menus/add'),
			'name'=>($id==null)?'Add Menu':'Update Menu'
		);
		
		/*End of adding breadcrumbs*/
		
		/*This below code is used to add/update of data in table*/
		
		if(!empty($this->request->data)){
			 
			if(!$id){
			$this->request->data['Menu']['created_at']=date('Y-m-d H:i:s');
			}else{
			$this->request->data['Menu']['updated_at']=date('Y-m-d H:i:s');
			}
			$this->Menu->create();
			$dt=date("Y-m-d H:i:s");
			$this->Menu->save($this->request->data);
			if ($this->request->data['Menu']['id']) {
				$this->Session->setFlash(__('Menu has been updated successfully'));
				} 
				else {
					$this->Session->setFlash(__('Menu has been added successfully'));
				}
			$this->redirect(array('controller'=>'menus','action'=>'index'));
			
		}
		
		/*End of add/update of form record db table*/
		
		/*This below code is used to read data from db table if found and return in array*/
		
		else{
			if($id!=null){
				$this->request->data = $this->Menu->read(null,$id);
			}else{
				$this->request->data = array();
			}
		}
			  
		$this->set('url',Controller::referer());
	}
	
	/*This below function code is used to form input data validation*/
	 
	function validation(){
		$this->Menu->set($this->request->data);
		$result = array();
		if ($this->Menu->validates()){
			$result['error'] = 0;
		}else{
			$result['error'] = 1;
		}
		if($this->request->is('ajax')) {
			$this->autoRender = false;
			$result['errors'] = $this->Menu->validationErrors;
			$errors = array();
			foreach($result['errors'] as $field => $data){
				$errors['Menu'.Inflector::camelize($field)] = array_pop($data);
			}
			$result['errors'] = $errors;
			echo json_encode($result);
			return;
		}
		return $result['error'];
		  
	}
	
}
?>
