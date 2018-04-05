<?php
class CustomFieldsController extends AppController {
	
	public function admin_index(){
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/content_manager/pages'),
			'name'=>'Custom Fields'
		);
		
		$this->heading =  array("Custom","Fields");
	}
	
	public function admin_add(){
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/content_manager/pages'),
			'name'=>'Custom Fields'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/content_manager/pages'),
			'name'=>'Add Field'
		);
		$this->heading =  array("Add Custom","Fields");
		
		$referer_url = $this->request->query('back');
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/custom_fields/add',true) :Controller::referer();
		
		}
		$this->set('referer_url',$referer_url);
		
	}
	
	
}
?>
