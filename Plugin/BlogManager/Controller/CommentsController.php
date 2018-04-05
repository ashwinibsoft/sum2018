<?php
Class CommentsController extends BlogManagerAppController{
	public $uses = array('BlogManager.Comment');
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
			$this->redirect(array('plugin'=>'blog_manager','controller'=>'comments','action'=>'index' ,$search,$limit));
		}
		$this->paginate['order']=array('Comment.id'=>'DESC');		
		
		if($search!=null){
			$search = urldecode($search);
			$condition['Comment.comment_title like'] = '%'.$search.'%';
		}
		
		$comments=$this->paginate("Comment", $condition);		 
		
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/blog_manager/comments'),
			'name'=>'Manage Comments'
		);
		$this->heading =  array("Manage","Blog Comment");
		$this->set('comments', $comments);
		$this->set('search',$search);
		$this->set('url','/'.$this->params->url);
		$this->set('limit',$limit);
	}
	
	function admin_delete($id=null){
		$this->autoRender = false;
		$data=$this->request->data['Comment']['id'];
		$action = $this->request->data['Comment']['action'];
		$ans="0";
		if(!empty($data)){
			foreach($data as $value){
				if($value!='0'){
					if($action=='Publish'){
						$cat['Comment']['id'] = $value;
						$cat['Comment']['status']=1;
						$this->Comment->create();
						$this->Comment->save($cat);
						$ans="1";
					}
					if($action=='Unpublish'){
						$cat['Comment']['id'] = $value;
						$cat['Comment']['status']=0;
						$this->Comment->create();
						$this->Comment->save($cat);
						$ans="1";
					}
					if($action=='Approve'){
						$cat['Comment']['id'] = $value;
						$cat['Comment']['approve']=1;
						$this->Comment->create();
						$this->Comment->save($cat);
						$ans="1";
					}
					if($action=='Disapprove'){
						$cat['Comment']['id'] = $value;
						$cat['Comment']['approve']=0;
						$this->Comment->create();
						$this->Comment->save($cat);
						$ans="1";
					}
					if($action=='Delete'){
						$cat = $this->Comment->find('first', array('conditions'=> array('Comment.id' => $value)));
							
						$this->Comment->delete($value);
						$ans="2";
					}
				}
			}
			if($ans=="1"){
				$this->Session->setFlash(__('Comment has been '.strtolower($this->data['Comment']['action']).'ed successfully', true));
			}
			else if($ans=="2"){
				$this->Session->setFlash(__('Comment has been '.strtolower($this->data['Comment']['action']).'d successfully', true));
			}else{
				$this->Session->setFlash(__('Please Select any Comment', true),'default','','error');
			}
			
			$this->redirect($this->request->data['Comment']['redirect']);
		}
		
			 
	}
	
	

	
}
?>
