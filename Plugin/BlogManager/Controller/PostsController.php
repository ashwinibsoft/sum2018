<?php
Class PostsController extends BlogManagerAppController{
	public $uses = array('BlogManager.Post','BlogManager.BlogCategoryLink');
	public $components=array('Email','RequestHandler','Image');
	public $paginate = array();
	public $id = null;
	public $template=null;
	
	public function admin_index($search=null,$limit=10,$category=0){
		$this->paginate = array();
		$condition = null;
		if($search=="_blank"){
			$search=null;
		}
		$this->paginate['limit']=$limit;
		if($this->request->is('post')){
			if(!empty($this->request->data['category'])){
				$category = $this->request->data['category'];
			}else{
				$category = 0;
			}
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
			$this->redirect(array('plugin'=>'blog_manager','controller'=>'posts','action'=>'index',$search,$limit,$category));
		}
		if($search!=null){
			$search = urldecode($search);
			$condition['Post.post_name like'] = '%'.$search.'%';
		}
		if($category!=0){
			$condition['Post.blog_categorie_id like'] ='%'.$category.'%';
		}
		$posts = array();
		$this->paginate['fields'] = array('Post.id','Post.post_name','Post.status','Post.created_at','Post.post_image','Post.blog_categorie_id');
		
		$this->paginate['order']=array('Post.id'=>'DESC');
		$posts= $results=$this->paginate("Post", $condition);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/blog_manager/posts'),
			'name'=>'Manage Post'
		);
		
		$this->heading =  array("Manage","Post");
		
		$this->loadModel('BlogManager.BlogCategorie');
		$cat_list = $this->BlogCategorie->find('list',array('fields'=>array('id','cat_name'),'order'=>array('BlogCategorie.id'=>'DESC')));
		
		$this->set('cat_list',$cat_list);
		$this->set('category',$category);
		$this->set('posts',$posts);
		$this->set('limit',$limit);
		$this->set('search',$search);
		$this->set('url','/'.$this->params->url);
	
	}
	
	function admin_default_image_crop($post_id=null){
		$path  = $this->webroot;
		$this->Image = $this->Components->load('Image');
		$this->Image->startup($this);
		$post_detail = $this->Post->find('first', array('conditions' => array('Post.id' => $post_id)));
		if($this->request->is('post')){
			
			$org_image_breaks = explode('.',$post_detail['Post']['post_image']);
			$ext = array_pop($org_image_breaks);
			$origFile = $post_detail['Post']['post_image'];

			$src = Configure::read('Path.Post').$post_detail['Post']['post_image'];
			$old_banner = Configure::read('Path.Post').$post_detail['Post']['post_image'];
			$org_image_breaks = implode('.',$org_image_breaks);
			$org_image_breaks = explode('_',$org_image_breaks);
			array_pop($org_image_breaks);
			$org_image_breaks = implode('_',$org_image_breaks);
			$new_name =$org_image_breaks.'_'.time().'.'.$ext;
			$dst =  Configure::read('Path.Post').$new_name;
			
			$start_width = $this->data['x'];
			$start_height = $this->data['y'];
			$width = $this->data['width'];
			$height = $this->data['height'];
			$key = 'post_image';
			$thumb = $this->Image->crop($src,$dst,$width,$height,$start_width,$start_height,$this->data['scale']);
			$post_data = array();
			$post_data['Post']['id'] = $post_id;
			$post_data['Post']['post_image'] = $new_name;
			$post_data['Post']['is_cropped'] = 1;
			
			$_options = array(
						'destination'=>Configure::read('Path.Post'),
						); 
			if($this->Post->save($post_data,array('validate'=>false))){
				if($post_detail['Post']['post_image']!='' && file_exists($old_banner)){
					unlink($old_banner);
				}
				$this->Session->setFlash('Image cropped and saved.');
				//$this->redirect(array('action'=>'admin_crop_success',$post_id));
				$this->redirect(array('controller' => 'posts', 'action' => 'admin_add',$post_id));
			}
			Cache::delete('site');
			$this->redirect(array('action'=>'admin_add',$post_id));
		}
		$this->set('post_detail',$post_detail);
		$this->set('post_id',$post_id);
	}
	
	
	function admin_crop_success($post_id=null){
		$post_detail = $this->Post->find('first', array(
			'conditions' => array('Post.id' => $post_id)
		));
		$this->set('post_detail',$post_detail);
		$this->set('post_id',$post_id);
	}
	
	
	function admin_add($id=null){
		$path  = $this->webroot;
		$galleries = array();
		if($this->_is_active_plugins('GalleryManager')){
			$this->loadModel('GalleryManager.Gallery');
			$galleries = $this->Gallery->find('list',array('fields'=>array('id','name'),'order'=>array('Gallery.id'=>'DESC')));
		}
		$this->loadModel('BlogManager.BlogCategorie');
		$cat_list = $this->BlogCategorie->find('list',array('fields'=>array('id','cat_name'),'order'=>array('BlogCategorie.id'=>'DESC')));
		
		$this->breadcrumbs[] = array(
		'url'=>Router::url('/admin/home'),
		'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/blog_manager/posts'),
				'name'=>'Manage Post'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/blog_manager/posts/add/'.$id),
				'name'=>($id==null)?'Add Content':'Update Post'
		);
		if($id==null){
			$this->heading =  array("Add","Post");
		}else{
			$this->heading =  array("Update","Post");
		}
		
		
		
		if(!empty($this->request->data) && $this->validation()){
			$existing_image='';
			if($this->request->data['Post']['id']){
				$post_image = $this->Post->find('first',array('fields'=>array('Post.post_image'),'conditions'=>array('Post.id'=>$this->request->data['Post']['id'])));
				$existing_image = $post_image['Post']['post_image'];
			}
			
			$_options = array(
			'destination'=>Configure::read('Path.Post'),
			'image'=>$this->request->data['Post']['post_image']
			);
			if($this->request->data['Post']['post_image']['error'] > 0 && !empty($this->request->data['Post']['id'])){
				$this->request->data['Post']['post_image'] = $existing_image;
			}else{
				if($this->request->data['Post']['post_image']['error'] < 1){
				$this->request->data['Post']['post_image'] = $this->System->Image->upload($_options);
				$this->request->data['Post']['is_cropped'] = 0;
				}else{
					$this->request->data['Post']['post_image'] = "";
				}
			}
			
			if(!$id){
				$this->request->data['Post']['created_at']=date('Y-m-d H:i:s');
				//$this->request->data['Post']['status'] = 1;
			}else{
				$this->request->data['Post']['updated_at']=date('Y-m-d H:i:s');
			}
			if(empty($this->request->data['Post']['id'])){
				if(isset($this->request->data['save']) && $this->request->data['save']=='Save'){
					$this->request->data['Post']['status'] = 2;
				}else{
					$this->request->data['Post']['status'] = 1;
				}
			}
			if(!empty($this->request->data['Post']['blog_categorie_id']))
			{
				$pcat=$this->request->data['Post']['blog_categorie_id'];
				$this->request->data['Post']['blog_categorie_id']=json_encode($this->request->data['Post']['blog_categorie_id']);
				}
				else
				{
				$this->request->data['Post']['blog_categorie_id']="";
					}
			
			
					
			$this->Post->create();
			$this->Post->save($this->request->data,array('validate'=>false));
			$id = $this->Post->id;
			
			$catid = $this->Post->id;
			$this->BlogCategoryLink->deleteAll(array('BlogCategoryLink.post_id' => $id));
			if(!empty($pcat))
			{
			foreach($pcat as $pcatg){
							$datas['post_id']=$catid;
							$datas['blog_categorie_id']=$pcatg;
							$this->BlogCategoryLink->create();
							$this->BlogCategoryLink->save($datas,array('validate'=>false));
					}
				}
			/* Slug URL Logic Start*/
			$slug_url = '';
			if(empty($this->request->data['Post']['id'])){
				if($this->request->data['Post']['slug_url']==''){
					$string = strtolower($this->request->data['Post']['post_name']);
					$slug_url = Inflector::slug($string, '-');
				}else{
					$slug_url = $this->request->data['Post']['slug_url'];
				}
			}else{
				$slug_url = $this->request->data['Post']['slug_url'];
			}
			/* Slug URL Logic END*/
			
			$route = array();
			$route['request_uri'] = trim($slug_url);
			$route['object'] = 'Post';
			$route['object_id'] = $this->Post->id;
			$route['object_name'] = $this->request->data['Post']['post_name'];
			$route['values'] = json_encode(array('plugin'=>'blog_manager','controller'=>'posts','action'=>'view','id'=>$this->Post->id));
			
			$this->Post->save_routes($route);
			
			
			if ($this->request->data['Post']['id']) {
				$this->Session->setFlash(__('Record has been updated successfully'));
			} 
			else{
				$this->Session->setFlash(__('Record has been added successfully'));
			}
			$this->redirect(array('action'=>'add',$id,'?'=>array('back'=>$this->request->data['Post']['url_back_redirect'])));
		}
		else{
			if(!empty($this->request->data)){
				$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
			}
		if($id!=null){
				$this->request->data = $this->Post->read(null,$id);	
				$this->request->data['Post']['blog_categorie_id']=json_decode($this->request->data['Post']['blog_categorie_id'], true);
				$this->request->data['Post']['slug_url'] = $this->Post->get_uri('Post',$id);
					
				//print_r($this->request->data);die;
			}else{
				$this->request->data = array();
			}	
			
				
			
		}
		
		$referer_url = $this->request->query('back');
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/blog_manager/posts',true) :Controller::referer();
		
		}
		$this->set('referer_url',$referer_url);
		$this->set('galleries',$galleries);
		$this->set('cat_list',$cat_list);
		$this->set('post_id',$id);
		
	}
	public function admin_one_delete($post_id = null){
		$this->autoRender = false;
		if($post_id==null){
			$this->redirect(Controller::referer());
		}
		$post = $this->Post->find('first', array('conditions'=> array('Post.id' => $post_id)));
		if (!empty($post['Post']['post_image'])) {
			   @unlink(WWW_ROOT."img/post/". $post['Post']['post_image']);
		}
		$this->Post->delete($post_id);
		$this->Post->delete_routes($post_id,'Post');
		$options = array(
		'ref_id'=>$post_id,
		'module'=>'Post',
		);
		$this->Post->delete_menu($options);
		$this->Session->setFlash(__('Record has been deleted successfully'));
		$redirect_url = $this->request->query('back');
		if(!empty($redirect_url)){
			$this->redirect($redirect_url);
		}else{
			$this->redirect(array('action'=>'admin_index'));
		}
		
		
		
	}
	
	public function admin_settings(){
		$this->UserLib->add_authorize_action(array('admin_settings'));
		$this->UserLib->add_authenticate_action(array('admin_settings'));
		$this->UserLib->check_authenticate($this);
		$this->UserLib->check_authorize($this);
		
		
		$post_list = $this->Post->find('threaded',array('fields'=>array('Post.id','Post.post_name')));
		
		$this->loadModel('Setting');
		if(!empty($this->request->data)){
			if($this->Session->check('delete_default_post_image')){
				$action = (int)$this->Session->read('delete_default_post_image');
				if($action){
					self::__admin_delete_default_post_image();
				}
			}
			
			foreach($this->request->data['Post'] as $key => $value){
				if($key == 'url_back_redirect' || $key == 'form' ){
					continue;
				}
				if(is_array($value)){
					
					if($key=='post_image'){
						
						$_options = array(
										'destination'=>Configure::read('Path.Post'),
										'image'=>$value,
									);
						$_file = $this->System->get_setting('post',$key);
						if(!empty($_file) && !empty($value['name'])){
							if(file_exists(Configure::read('Path.Post').$this->System->get_setting('post',$key))){
								unlink(Configure::read('Path.Post').$this->System->get_setting('post',$key));
							}
						}
						if($value['error'] > 0){
							$value=$_file;
						}else{
							//$value = $this->Upload->move_uploaded_file($value,$_options);
							$value = $this->System->Image->upload($_options);
						}
					}else if ($key=='home_post_blocks'){
						$value = json_encode($value);
					}else{
						continue;
					}
				}
				$value = addslashes($value);
				
				if($this->Setting->find('count',array('conditions'=>array('Setting.key'=>$key,'Setting.module'=>'post')))){
					$this->Setting->query("UPDATE `settings` SET `values`=\"$value\" , module=\"post\" WHERE `key`=\"$key\"");
				} else{
					$this->Setting->query("INSERT `settings` SET `values`=\"$value\"  , `key`=\"$key\" , module=\"post\"");
				}
				$this->Session->setFlash(__('Post Setting(s) has been saved successfully'));
			}
			$this->redirect(array('action'=>'settings','?'=>array('back'=>$this->request->data['Post']['url_back_redirect'])));
		}
		$this->Session->delete('delete_default_post_image');
		Cache::delete('site');
		$this->request->data['Post'] = $this->Setting->find('list',array('fields'=>array('Setting.key','Setting.values'),'conditions'=>array('Setting.module'=>'post')));
		$referer_url = $this->request->query('back');
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/blog_manager/settings',true) :Controller::referer();
		
		} 
		$this->breadcrumbs[] = array(
		'url'=>Router::url('/admin/home'),
		'name'=>'Dashboard'
		);
			$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/blog_manager/settings'),
				'name'=>'Post Settings'
		);
		$this->heading =  array("Manage","Post Settings");
		$this->set('referer_url',$referer_url);
		$this->set('post_list',$post_list);
	}
	
	
	function admin_delete_post_image($id= null){
		$this->post = $this->Post->read(null,$id);
		$this->Post->updateAll(
				array('Post.post_image' => null),
				array('Post.id'=>$id)
			);
		self::__delete_post_image();
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
		}else{
			$this->redirect(array('action'=>'add',$id));
		}
	}
	
	
	private function __delete_post_image(){
		App::uses('ImageResizeHelper', 'View/Helper');
		$ImageResize = new ImageResizeHelper();
		$imgArr = array('source_path'=>Configure::read('Path.Post'),'img_name'=>$this->post['Post']['post_image'],'width'=>Configure::read('image_edit_width'),'height'=>Configure::read('image_edit_height'));
		$ImageResize->deleteThumbImage($imgArr);
		
		$imgArr = array('source_path'=>Configure::read('Path.Post'),'img_name'=>$this->post['Post']['post_image'],'width'=>Configure::read('post_image_width'),'height'=>Configure::read('post_image_height'));
		$ImageResize->deleteThumbImage($imgArr);
		
		@unlink(Configure::read('Path.Post'). $this->post['Post']['post_image']);
		
	}
	
	
	function admin_delete($id=null){
		$this->autoRender = false;
		$data=$this->request->data['Post']['id'];
		$action = $this->request->data['Post']['action'];
		$ans="0";
		foreach($data as $value){
			
			if($value!='0'){
				$post = $this->Post->find('first', array('conditions'=> array('Post.id' => $value)));
				if((int)$post['Post']['status']==2){
					continue;
				}
				
				if($action=='Publish'){
					$post['Post']['id'] = $value;
					$post['Post']['status']=1;
					$this->Post->create();
					$this->Post->save($post);
					$ans="1";
				}
				if($action=='Unpublish'){
					$post['Post']['id'] = $value;
					$post['Post']['status']=0;
					$this->Post->create();
					$this->Post->save($post);
					$ans="1";
				}
				if($action=='Delete'){
					if (!empty($post['Post']['post_image'])) {
						   @unlink(WWW_ROOT."img/post/". $post['Post']['post_image']);
					}
					$this->Post->delete($value);
					$this->Post->delete_routes($value,'Post');
					$options = array(
					'ref_id'=>$value,
					'module'=>'Post',
					);
					$this->Post->delete_menu($options);
					
					
					$ans="2";
				}
			}
		}
		if($ans=="1"){
			$this->Session->setFlash(__('Post has been '.strtolower($this->data['Post']['action']).'ed successfully', true));
		}
		else if($ans=="2"){
			$this->Session->setFlash(__('Post has been '.strtolower($this->data['Post']['action']).'d successfully', true));
		}else{
			$this->Session->setFlash(__('Please select posts', true),'default','','error');
		}
		$this->redirect($this->request->data['Post']['redirect']);
                 
	}
	private function __load_page($id = null){
		
		
		$post =  array();
		$post = $this->Post->find('first',array('conditions'=>array('Post.id'=>$id,'Post.status'=>1)));
		if(empty($post)){
			return null;
		}
		
		$this->System->set_data('post_image',$post['Post']['post_image']);
		$post['Gallery'] = array();
		if((int)Configure::read('Section.gallery') && (int)$this->_is_active_plugins('GalleryManager')){
			
			$this->loadModel('GalleryManager.Gallery');
			if($post['Post']['post_gallery']!=""){	
				$this->Gallery->bindModel(
					array('hasMany' => array(
							'GalleryImage'
						)
					)
				);
				$gallery=$this->Gallery->find('first',array('conditions'=>array('Gallery.id'=>$post['Post']['post_gallery'],'Gallery.status'=>1)));
				$post = array_merge($post,$gallery);
			}
		}else{
			$post['Post']['gallery'] = null;
		}
		
		return $post;
	}
	function home(){
		
		$post = self::__load_page((int)$this->System->get_setting('post','default_home_post'));
		if (empty($post)) {
			throw new NotFoundException('404 Error - Post not found');
		}
		
		//print_r($post);die;
		$this->System->set_seo('site_title',$post['Post']['post_title']);
		$this->System->set_seo('site_metakeyword',$post['Post']['post_metakeyword']);
		$this->System->set_seo('site_metadescription',$post['Post']['post_metadescription']);
		$this->set('post',$post);
	}
	public function get($id = null){
		$this->autoRender = false;
		$post=$this->Post->find('first',array('conditions'=>array('Post.id'=>(int)$id,'Post.status'=>1)));
		return $post;
	}
	public function view($id=null){
		
		$post = self::__load_page($id);
		if (empty($post)){
			if($this->request->is('requested')){
				$this->autoRender =false;
				return "";
			}
			throw new NotFoundException('404 Error - Post not found');
		}
		
		$this->set('id', $id);
		$this->set('post', $post);
		$this->System->set_seo('site_title',$post['Post']['post_title']);
		$this->System->set_seo('site_metakeyword',$post['Post']['post_metakeyword']);
		$this->System->set_seo('site_metadescription',$post['Post']['post_metadescription']);
		
	}
	
	
	function validation(){
		
		if(!empty($this->request->data['Post']['form'])){
			if($this->request->data['Post']['form']=="post_add" && $this->request->data['Post']['status']==2){
				return true;
			}
			$this->Post->setValidation($this->request->data['Post']['form']);
		}else{
			throw new NotFoundException('404 Error - Post not found');
		}
		$this->Post->set($this->request->data);
		return $this->Post->validates();
	}
	public function ajax_validation($returnType = 'json'){
		//print_r($this->request->data);die;
		$this->autoRender = false;
		if(!empty($this->request->data)){
			if(!empty($this->request->data['Post']['form'])){
				$this->Post->setValidation($this->request->data['Post']['form']);
			}
			$this->Post->set($this->request->data);
			$result = array();
			if($this->request->data['Post']['form']=="post_add" && $this->request->data['Post']['status']==2){
				$result['error'] = 0;
			}else{
				if($this->Post->validates()){
					$result['error'] = 0;
				}else{
					$result['error'] = 1;
					$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
				}
			}
			$errors = array();
			$result['errors'] = $this->Post->validationErrors;
			foreach($result['errors'] as $field => $data){
			  $errors['Post'.Inflector::camelize($field)] = array_pop($data);
			}
			$result['errors'] = $errors;
			$view = new View();
			
			$result['error_message'] = $view->element('admin/message');
			echo json_encode($result);
			return;
		}
		echo json_encode(array());
	}
	public function admin_dashboard(){
		$posts=$this->Post->find('all',array('limit'=>5,'order'=>array('Post.system_post'=>'ASC','Post.id'=>'desc')));
		$this->set('posts',$posts);
	}
}
?>
