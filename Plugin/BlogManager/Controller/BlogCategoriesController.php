<?php
Class BlogCategoriesController extends BlogManagerAppController{
	public $uses = array('BlogManager.BlogCategorie');
	public $helpers = array('Form','ImageResize');
	public $components=array('Email','RequestHandler','Image');
	public $paginate = array();
	public $id = null;
	
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
			$this->redirect(array('plugin'=>'blog_manager','controller'=>'blog_categories','action'=>'index' ,$search,$limit));
		}
		$this->paginate['order']=array('BlogCategorie.id'=>'DESC');		
		
		if($search!=null){
			$search = urldecode($search);
			$condition['BlogCategorie.cat_name like'] = '%'.$search.'%';
		}
		$categories=$this->paginate("BlogCategorie", $condition);		 
		
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/blog_manager/blog_categories'),
			'name'=>'Manage Categories'
		);
		$this->heading =  array("Manage","Blog Category");
		$this->set('categories', $categories);
		$this->set('search',$search);
		$this->set('url','/'.$this->params->url);
		$this->set('limit',$limit);
	}

	function admin_add($id=null){
		$this->helpers = array('BlogManager.BlogCategorie');
		$cat_list = $this->BlogCategorie->find('threaded',array('fields'=>array('BlogCategorie.id','BlogCategorie.cat_parent','BlogCategorie.cat_name'),'conditions'=>array('NOT'=>array('BlogCategorie.id'=>array((int)$id)))));
		$this->breadcrumbs[] = array(
		'url'=>Router::url('/admin/home'),
		'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/blog_manager/blog_categories'),
				'name'=>'Manage Category'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/blog_manager/blog_categories/add'),
				'name'=>($id==null)?'Add Category':'Update Category'
		);
		if($id==null){
			$this->heading =  array("Add","Category");
		}else{
			$this->heading =  array("Update","Category");
		}
		
		if(!empty($this->request->data) && $this->validation()){
			$destination = Configure::read('Path.PostCategory');
			if($this->request->data['BlogCategorie']['id']){
				$cat_image = $this->BlogCategorie->find('first',array('fields'=>array('BlogCategorie.cat_image'),'conditions'=>array('BlogCategorie.id'=>$this->request->data['BlogCategorie']['id'])));
			}
			$image_name='';
			if($this->request->data['BlogCategorie']['id']){
				$image_name = $cat_image['BlogCategorie']['cat_image'];
			}
			
			
			if($this->request->data['BlogCategorie']['cat_image']['error'] < 1){
					$_options = array(
				'destination'=>Configure::read('Path.PostCategory'),
				'image'=>$this->request->data['BlogCategorie']['cat_image']
				);
				$this->request->data['BlogCategorie']['cat_image'] = $this->System->Image->upload($_options);
				if($image_name!=''){
					unlink($destination.$image_name);
				}
			}else{
				$this->request->data['BlogCategorie']['cat_image'] = $image_name;
			}
			
			if(!$id){
				$this->request->data['BlogCategorie']['created_at']=date('Y-m-d H:i:s');
				$this->request->data['BlogCategorie']['status']=1;
			}else{
				
				$this->request->data['BlogCategorie']['updated_at']=date('Y-m-d H:i:s');
			}
			$this->BlogCategorie->create();
			//print_r($this->request->data);die;
			$this->BlogCategorie->save($this->request->data,array('validate'=>false));
			$id = $this->BlogCategorie->id;	
			Cache::delete('blogcat');
			if ($this->request->data['BlogCategorie']['id']) {
				$this->Session->setFlash(__('Category has been updated successfully'));
			} 
			else {
				$this->Session->setFlash(__('Category has been added successfully'));
			}
			if(isset($this->request->data['save'])){
				$this->redirect(array('controller' => 'blog_categories', 'action' => 'admin_add',$id));
			
			}else{
				$this->redirect(array('controller' => 'blog_categories','action'=>'admin_index'));
			}
		}
		else{
			if($id!=null){
				$this->request->data = $this->BlogCategorie->read(null,$id);
			}else{
				$this->request->data = array();
			   
			}
		} 
		$this->set('url',Controller::referer());
		$referer_url = $this->request->query('back');
		
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/blog_manager/blog_categories/add_actegory/'.$id,true) :Controller::referer();
			
		}
		$this->set('referer_url',$referer_url);
		$this->set('cat_list',$cat_list);
		
	}
	
	function admin_default_image_crop($id=null){
		$path  = $this->webroot;
		$this->Image = $this->Components->load('Image');
		$this->Image->startup($this);
		$cat_image = $this->BlogCategorie->find('first',array('fields'=>array('BlogCategorie.cat_image'),'conditions'=>array('BlogCategorie.id'=>$id)));
		if($this->request->is('post')){
			
			$org_image_breaks = explode('.',$cat_image['BlogCategorie']['cat_image']);
			$ext = array_pop($org_image_breaks);
			$origFile = $cat_image['BlogCategorie']['cat_image'];

			$src = Configure::read('Path.PostCategory').$cat_image['BlogCategorie']['cat_image'];
			$old_slide = Configure::read('Path.PostCategory').$cat_image['BlogCategorie']['cat_image'];
			$org_image_breaks = implode('.',$org_image_breaks);
			$org_image_breaks = explode('_',$org_image_breaks);
			array_pop($org_image_breaks);
			$org_image_breaks = implode('_',$org_image_breaks);
			$new_name =$org_image_breaks.'_'.time().'.'.$ext;
			$dst =  Configure::read('Path.PostCategory').$new_name;
			
			$start_width = $this->data['x'];
			$start_height = $this->data['y'];
			$width = $this->data['width'];
			$height = $this->data['height'];
			$key = 'cat_image';
			$thumb = $this->Image->crop($src,$dst,$width,$height,$start_width,$start_height,$this->data['scale']);
			$cat_data = array();
			$cat_data['BlogCategorie']['id'] = $id;
			$cat_data['BlogCategorie']['cat_image'] = $new_name;
			
			$_options = array(
						'destination'=>Configure::read('Path.PostCategory'),
						);
			if($this->BlogCategorie->save($cat_data,array('validate'=>false))){
				if($cat_image['BlogCategorie']['cat_image']!='' && file_exists($old_slide)){
					unlink($old_slide);
				}
				$this->Session->setFlash('Image cropped and saved.');
				$this->redirect(array('controller' => 'blog_categories', 'action' => 'admin_add',$id));
			}
			Cache::delete('site');
			$this->redirect(array('action'=>'admin_add',$id));
		}
		$this->set('cat_image',$cat_image);
	}
	
	function admin_delete($id=null){
		$this->autoRender = false;
		$destination = Configure::read('Path.PostCategory');
	  
		$data=$this->request->data['BlogCategorie']['id'];
		$action = $this->request->data['BlogCategorie']['action'];
		$ans="0";
		if(!empty($data)){
			foreach($data as $value){
				if($value!='0'){
					if($action=='Publish'){
						$cat['BlogCategorie']['id'] = $value;
						$cat['BlogCategorie']['status']=1;
						$this->BlogCategorie->create();
						$this->BlogCategorie->save($cat);
						$ans="1";
					}
					if($action=='Unpublish'){
						$cat['BlogCategorie']['id'] = $value;
						$cat['BlogCategorie']['status']=0;
						$this->BlogCategorie->create();
						$this->BlogCategorie->save($cat);
						$ans="1";
					}
					if($action=='Delete'){
						$cat = $this->BlogCategorie->find('first', array('conditions'=> array('BlogCategorie.id' => $value),'fields' => array('BlogCategorie.cat_image')));
						if (!empty($cat['BlogCategorie']['cat_image'])){
						   @unlink($destination. $cat['BlogCategorie']['cat_image']);
						}
							
						$this->BlogCategorie->delete($value);
						$ans="2";
					}
				}
			}
			if($ans=="1"){
				$this->Session->setFlash(__('Category has been '.strtolower($this->data['BlogCategorie']['action']).'ed successfully', true));
			}
			else if($ans=="2"){
				$this->Session->setFlash(__('Category has been '.strtolower($this->data['BlogCategorie']['action']).'d successfully', true));
			}else{
				$this->Session->setFlash(__('Please Select any Category', true),'default','','error');
			}
			
			$this->redirect($this->request->data['BlogCategorie']['redirect']);
		}
		
			 
	}
	function validation(){
		if(!empty($this->request->data['BlogCategorie']['cat_add'])){
			$this->BlogCategorie->setValidation($this->request->data['BlogCategorie']['cat_add']);
		}
		$this->BlogCategorie->set($this->request->data);
		if($this->BlogCategorie->validates()){
			return true;
		}else{
			$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
			return false;
		}
	}
	public function ajax_validation($returnType = 'json'){
		
		$this->autoRender = false;
		if(!empty($this->request->data)){
			//print_r($this->request->data);die;
			if(!empty($this->request->data['BlogCategorie']['cat_add'])){
				$this->BlogCategorie->setValidation($this->request->data['BlogCategorie']['cat_add']);
			}
			$this->BlogCategorie->set($this->request->data);
			$result = array();
			if($this->BlogCategorie->validates()){
					$result['error'] = 0;
			}else{
				$result['error'] = 1;
				$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
			}
			$errors = array();
			$result['errors'] = $this->BlogCategorie->validationErrors;
			foreach($result['errors'] as $field => $data){
			  $errors['BlogCategorie'.Inflector::camelize($field)] = array_pop($data);
			}
			$result['errors'] = $errors;
			$view = new View();
			
			$result['error_message'] = $view->element('admin/message');
			echo json_encode($result);
			return;
		}
		echo json_encode(array());
	}

	

	
}
?>
