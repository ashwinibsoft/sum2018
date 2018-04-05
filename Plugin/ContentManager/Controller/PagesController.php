<?php
Class PagesController extends ContentManagerAppController{
	public $uses = array('ContentManager.Page','FaqManager.Faq','NewsManager.NewsArticle','ContentManager.Search',);
	public $components=array('Email','RequestHandler','Image');
	public $paginate = array();
	public $id = null;
	public $template=null;
	
	public function admin_index($parent_id = 0 , $search=null,$limit=10){
		$this->paginate = array();
		$condition = null;
		if($search=="_blank"){
			$search=null;
		}
		$this->paginate['limit']=$limit;
		if($this->request->is('post')){
			if(!empty($this->request->data['parent_id'])){
				$parent_id = $this->request->data['parent_id'];
			}else{
				$parent_id = 0;
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
			$this->redirect(array('plugin'=>'content_manager','controller'=>'pages','action'=>'index',$parent_id,$search,$limit));
		}
		if($search!=null){
			$search = urldecode($search);
			$condition['Page.name like'] = '%'.$search.'%';
		}
		if($parent_id!=0){
			$condition['Page.parent_id'] = $parent_id;
		}
		
		$pages = array();
		$this->paginate['fields'] = array('Page.id','Page.name','Page.parent_id','Page.status','Page.system_page','Page.created_at');
		
		$this->paginate['order']=array('Page.system_page'=>'ASC','Page.id'=>'DESC');
		$pages= $results=$this->paginate("Page", $condition);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/content_manager/pages'),
			'name'=>'Manage Content'
		);
		
		$this->heading =  array("Manage","Content");
		
		$page_list = $this->Page->find('threaded',array('fields'=>array('Page.id','Page.parent_id','Page.name','Page.system_page'),'conditions'=>array('OR'=>array('Page.system_page IS NULL','Page.system_page'=>0))));
		
		
		$this->set('parent_id',$parent_id);
		$this->set('pages',$pages);
		$this->set('limit',$limit);
		$this->set('search',$search);
		$this->set('url','/'.$this->params->url);
		$this->set('page_list',$page_list);
	}
	function admin_default_image_crop($page_id=null){
		$path  = $this->webroot;
		$this->Image = $this->Components->load('Image');
		$this->Image->startup($this);
		$page_detail = $this->Page->find('first', array('conditions' => array('Page.id' => $page_id)));
		if($this->request->is('post')){
			
			$org_image_breaks = explode('.',$page_detail['Page']['banner_image']);
			$ext = array_pop($org_image_breaks);
			$origFile = $page_detail['Page']['banner_image'];

			$src = Configure::read('Path.Banner').$page_detail['Page']['banner_image'];
			$old_banner = Configure::read('Path.Banner').$page_detail['Page']['banner_image'];
			$org_image_breaks = implode('.',$org_image_breaks);
			$org_image_breaks = explode('_',$org_image_breaks);
			array_pop($org_image_breaks);
			$org_image_breaks = implode('_',$org_image_breaks);
			$new_name =$org_image_breaks.'_'.time().'.'.$ext;
			$dst =  Configure::read('Path.Banner').$new_name;
			
			$start_width = $this->data['x'];
			$start_height = $this->data['y'];
			$width = $this->data['width'];
			$height = $this->data['height'];
			$key = 'banner_image';
			$thumb = $this->Image->crop($src,$dst,$width,$height,$start_width,$start_height,$this->data['scale']);
			$page_data = array();
			$page_data['Page']['id'] = $page_id;
			$page_data['Page']['banner_image'] = $new_name;
			$page_data['Page']['is_cropped'] = 1;
			
			$_options = array(
						'destination'=>Configure::read('Path.Banner'),
						); 
			if($this->Page->save($page_data,array('validate'=>false))){
				if($page_detail['Page']['banner_image']!='' && file_exists($old_banner)){
					unlink($old_banner);
				}
				$this->Session->setFlash('Image cropped and saved.');
				//$this->redirect(array('action'=>'admin_crop_success',$page_id));
				$this->redirect(array('controller' => 'pages', 'action' => 'admin_add',$page_id));
			}
			Cache::delete('site');
			$this->redirect(array('action'=>'admin_add',$page_id));
		}
		$this->set('page_detail',$page_detail);
		$this->set('page_id',$page_id);
	}
	
	
	function admin_calender_demo(){
	}
	
	function admin_crop_image($page_id=null){
		$path  = $this->webroot;
		$this->Img = $this->Components->load('Image');
		$this->Img->startup($this);
		$page_detail = $this->Page->find('first', array(
			'conditions' => array('Page.id' => $page_id)
		));
		
		if($this->request->is('post')){
			$org_image_breaks = explode('.',$page_detail['Page']['banner_image']);
			$ext = array_pop($org_image_breaks);
			$origFile = $page_detail['Page']['banner_image'];

			$src = Configure::read('Path.Banner').$page_detail['Page']['banner_image'];
			$old_banner = Configure::read('Path.Banner').$page_detail['Page']['banner_image'];
			$org_image_breaks = implode('.',$org_image_breaks);
			$org_image_breaks = explode('_',$org_image_breaks);
			array_pop($org_image_breaks);
			$org_image_breaks = implode('_',$org_image_breaks);
			$new_name =$org_image_breaks.'_'.time().'.'.$ext;
			$dst =  Configure::read('Path.Banner').$new_name;
			
			$start_width = $this->data['start_width'];
			$start_height = $this->data['start_height'];
			$width = $this->data['width'];
			$height = $this->data['height'];
			
			$thumb = $this->Img->cropimage($src,$dst,$width,$height,$start_width,$start_height,1);
			//$this->Img->resampleGD($src, $dst, 800, 800, 1, 0);
			$page_data = array();
			$page_data['Page']['id'] = $page_id;
			$page_data['Page']['banner_image'] = $new_name;
			
			if($this->Page->save($page_data,array('validate'=>false))){
				if($page_detail['Page']['banner_image']!='' && file_exists($old_banner)){
					unlink($old_banner);
				}
				$this->Session->setFlash('Image croped and saved.');
				$this->redirect(array('action'=>'admin_crop_success',$page_id));
			}
			
			//redirect to crop success action
		}
		$this->set('page_detail',$page_detail);
		$this->set('page_id',$page_id);
	}
	function admin_crop_success($page_id=null){
		$page_detail = $this->Page->find('first', array(
			'conditions' => array('Page.id' => $page_id)
		));
		$this->set('page_detail',$page_detail);
		$this->set('page_id',$page_id);
	}
	function admin_default_banner_image_crop(){
		$path  = $this->webroot;
		$this->loadModel('Setting');
		//$this->autoRender = false;
		//$this->layout = 'crop';
		if($this->request->is('post')){
			$image = $this->System->get_setting('page','banner_image');
			$org_image_breaks = explode('.',$image);
			$ext = array_pop($org_image_breaks);
			$origFile = $image;

			$src = Configure::read('Path.Banner').$image;
			$old_banner = Configure::read('Path.Banner').$image;
			$org_image_breaks = implode('.',$org_image_breaks);
			$org_image_breaks = explode('_',$org_image_breaks);
			array_pop($org_image_breaks);
			$org_image_breaks = implode('_',$org_image_breaks);
			$new_name =$org_image_breaks.'_'.time().'.'.$ext;
			$dst =  Configure::read('Path.Banner').$new_name;
			
			$start_width = $this->data['x'];
			$start_height = $this->data['y'];
			$width = $this->data['width'];
			$height = $this->data['height'];
			$key = 'banner_image';
			
			$thumb = $this->System->Image->crop($src,$dst,$width,$height,$start_width,$start_height,$this->data['scale']);
			
			$_options = array(
						'destination'=>Configure::read('Path.Banner'),
						);
			//$value = $this->Upload->move_uploaded_file($new_name,$_options);
			//move_uploaded_file($new_file['tmp_name'], $destination . $image_name);
			if($this->Setting->find('count',array('conditions'=>array('Setting.key'=>$key,'Setting.module'=>'page')))){
					$this->Setting->query("UPDATE `settings` SET `values`=\"$new_name\" , module=\"page\" WHERE `key`=\"$key\"");
			} else{
				$this->Setting->query("INSERT `settings` SET `values`=\"$new_name\"  , `key`=\"$key\" , module=\"page\"");
			}
			$this->System->set_setting('page',$key,$new_name);
			Cache::delete('site');
			if(file_exists($old_banner)){
				unlink($old_banner);
			}
			
			//echo json_encode($response);
			$this->redirect(array('action'=>'admin_settings'));
			//redirect to crop success action
		}
	}
	function admin_delete_default_banner_image(){
		$this->Session->write('delete_default_banner_image',1);
		
		$this->autoRender = false;
		$this->loadModel('Setting');
		$image = $this->System->get_setting('page','banner_image');
		$b= substr($image,0,5);
		if($b=="crop_")
		{
		$oldimage=substr($image,5);
	     }
	    else
	    {
			$oldimage=$image;
		} 
		/*echo "<pre>";
		print_r($image);
		echo "<br/>";
		print_r($oldimage); die;*/
		$new_name = '';
		$key = 'banner_image';
		$this->Setting->query("UPDATE `settings` SET `values`=\"$new_name\" WHERE `key`=\"$key\" and module=\"page\"");
		$this->Setting->query("UPDATE `settings` SET `values`=0 WHERE `key`=\"override_banner_image\" and module=\"page\"");
		$old_banner = Configure::read('Path.Banner').$image;
		$old_bannerimage = Configure::read('Path.Banner').$oldimage;
		
		Cache::delete('site');
		if(file_exists($old_banner))
		{
			if($old_banner==$old_bannerimage)
			{	
			unlink($old_banner);
		    }
		   else
		   {
			 unlink($old_banner);  
			 unlink($old_bannerimage);  
		   }
		    
		}
		$this->redirect(array('action'=>'admin_settings'));
		
	}
	
	private function __admin_delete_default_banner_image(){
		//$this->autoRender = false;
		$this->loadModel('Setting');
		$image = $this->System->get_setting('page','banner_image');
		$new_name = '';
		$key = 'banner_image';
		
		$this->Setting->query("UPDATE `settings` SET `values`=\"\" WHERE `key`=\"$key\"");
		$this->Setting->query("UPDATE `settings` SET `values`= \"0\" WHERE `key`=\"override_banner_image\"");
		$old_banner = Configure::read('Path.Banner').$image;
		//Cache::delete('site');
		if(file_exists($old_banner)){
			unlink($old_banner);
		}
	}
	
	function admin_add($id=null){
		// echo "<pre>"; print_r($this->request->data);  die;
		$path  = $this->webroot;
		$this->helpers = array('ContentManager.Page');
		$galleries = array();
		if($this->_is_active_plugins('GalleryManager')){
			$this->loadModel('GalleryManager.Gallery');
			$galleries = $this->Gallery->find('list',array('fields'=>array('id','name'),'order'=>array('Gallery.id'=>'DESC')));
		}
		
		$this->breadcrumbs[] = array(
		'url'=>Router::url('/admin/home'),
		'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/content_manager/pages'),
				'name'=>'Manage Content'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/content_manager/pages/add/'.$id),
				'name'=>($id==null)?'Add Content':'Update Content'
		);
		if($id==null){
			$this->heading =  array("Add","Content");
		}else{
			$this->heading =  array("Update","Content");
		}
		$page_list = $this->Page->find('threaded',array('fields'=>array('Page.id','Page.parent_id','Page.name'),'conditions'=>array('NOT'=>array('Page.id'=>array((int)$id)))));
		
		
		if(!empty($this->request->data) && $this->validation()){
				
			$existing_image='';
			if($this->request->data['Page']['id']){
				$banner_image = $this->Page->find('first',array('fields'=>array('Page.banner_image'),'conditions'=>array('Page.id'=>$this->request->data['Page']['id'])));
				$existing_image = $banner_image['Page']['banner_image'];
			}
			
			$_options = array(
			'destination'=>Configure::read('Path.Banner'),
			'image'=>$this->request->data['Page']['banner_image']
			);
			
			if($this->request->data['Page']['banner_image']['error'] > 0 && !empty($this->request->data['Page']['id'])){
				$this->request->data['Page']['banner_image'] = $existing_image;
			}else{
				if($this->request->data['Page']['banner_image']['error'] < 1){
				$this->request->data['Page']['banner_image'] = $this->System->Image->upload($_options);
				$this->request->data['Page']['is_cropped'] = 0;
				}else{
					$this->request->data['Page']['banner_image'] = "";
				}
			}
			$existing_featureimage='';
			if($this->request->data['Page']['id']){
				$feature_image = $this->Page->find('first',array('fields'=>array('Page.featureimage'),'conditions'=>array('Page.id'=>$this->request->data['Page']['id'])));
			$existing_featureimage = $feature_image['Page']['featureimage'];
			}
			
			$_options1 = array(
			'destination'=>Configure::read('Path.Blog'),
			'image'=>$this->request->data['Page']['featureimage']
			);
			if($this->request->data['Page']['featureimage']['error'] > 0 && !empty($this->request->data['Page']['id'])){
				$this->request->data['Page']['featureimage'] = $existing_featureimage;
			}else{
				if($this->request->data['Page']['featureimage']['error'] < 1){
				$this->request->data['Page']['featureimage'] = $this->System->Image->upload($_options1);
				$this->request->data['Page']['is_cropped'] = 0;
				}else{
					$this->request->data['Page']['featureimage'] = "";
				}
			}
			
			
			
			
			
			
			
			
			
			if($this->request->data['Page']['parent_id']==''){
				$this->request->data['Page']['parent_id']=null;
			}
			if(!$id){
				$this->request->data['Page']['created_at']=date('Y-m-d H:i:s');
				//$this->request->data['Page']['status'] = 1;
			}else{
				$this->request->data['Page']['updated_at']=date('Y-m-d H:i:s');
			}
			if(empty($this->request->data['Page']['id'])){
				if(isset($this->request->data['save']) && $this->request->data['save']=='Save'){
					$this->request->data['Page']['status'] = 2;
				}else{
					$this->request->data['Page']['status'] = 1;
				}
			}
			
				
			$this->Page->create();
			$this->Page->save($this->request->data,array('validate'=>false));
			$id = $this->Page->id;
			
			/* Slug URL Logic Start*/
			$slug_url = '';
			if(empty($this->request->data['Page']['id'])){
				if($this->request->data['Page']['slug_url']==''){
					$string = strtolower($this->request->data['Page']['name']);
					$slug_url = Inflector::slug($string, '-');
				}else{
					$slug_url = $this->request->data['Page']['slug_url'];
				}
			}else{
				$slug_url = $this->request->data['Page']['slug_url'];
			}
			/* Slug URL Logic END*/
			
			$route = array();
			$route['request_uri'] = trim($slug_url);
			$route['object'] = 'Page';
			$route['object_id'] = $this->Page->id;
			$route['object_name'] = $this->request->data['Page']['name'];
			$route['values'] = json_encode(array('plugin'=>'content_manager','controller'=>'pages','action'=>'view','id'=>$this->Page->id));
			
			$this->Page->save_routes($route);
			
			/* Code for add page in menu navigation start*/
			$_new = 0;
			if(empty($this->request->data['Page']['id'])){
				$_new = 1;
			}
			$options = array(
				'name'=>$this->request->data['Page']['name'],
				'ref_id'=>$this->Page->id,
				'parent_id'=>(int)$this->request->data['Page']['parent_id'],
				'module'=>'Page',
				'new'=>$_new
				);
				$this->Page->add_menu($options);
			/* Code for add page in menu navigation end*/
			
			
			if ($this->request->data['Page']['id']) {
				$this->Session->setFlash(__('Record has been updated successfully'));
			} 
			else{
				$this->Session->setFlash(__('Record has been added successfully'));
			}
			$this->redirect(array('action'=>'add',$id,'?'=>array('back'=>$this->request->data['Page']['url_back_redirect'])));
		}
		else{
			if(!empty($this->request->data)){
				$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
			}
			
			if($id!=null){
				$this->request->data = $this->Page->read(null,$id);
				$this->request->data['Page']['slug_url'] = $this->Page->get_uri('Page',$id);
			}else{
				$this->request->data = array();
			}
		}
		
		$referer_url = $this->request->query('back');
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/content_manager/pages',true) :Controller::referer();
		
		}
		$this->set('referer_url',$referer_url);
		$this->set('page_list',$page_list);
		$this->set('galleries',$galleries);
		$this->set('page_id',$id);
	}
	public function admin_one_delete($page_id = null){
		$this->autoRender = false;
		if($page_id==null){
			$this->redirect(Controller::referer());
		}
		$page = $this->Page->find('first', array('conditions'=> array('Page.id' => $page_id)));
		if (!empty($page['Page']['banner_image'])) {
			   @unlink(WWW_ROOT."img/banner/". $page['Page']['banner_image']);
		}
		$this->Page->delete($page_id);
		$this->Page->delete_routes($page_id,'Page');
		$options = array(
		'ref_id'=>$page_id,
		'module'=>'Page',
		);
		$this->Page->delete_menu($options);
		$this->Session->setFlash(__('Record has been deleted successfully'));
		$redirect_url = $this->request->query('back');
		if(!empty($redirect_url)){
			$this->redirect($redirect_url);
		}else{
			$this->redirect(array('action'=>'admin_index'));
		}
		
		
		
	}
	
	
	public function admin_settings(){
		//echo $random=mt_rand(10000000, 99999999);
		$this->UserLib->add_authorize_action(array('admin_settings'));
		$this->UserLib->add_authenticate_action(array('admin_settings'));
		$this->UserLib->check_authenticate($this);
		$this->UserLib->check_authorize($this);
		
		
		$page_list = $this->Page->find('threaded',array('fields'=>array('Page.id','Page.parent_id','Page.name','Page.system_page'),'conditions'=>array('OR'=>array('Page.system_page IS NULL','Page.system_page'=>0))));
		
		$this->loadModel('Setting');
		if(!empty($this->request->data)){
			//echo "<pre>";//print_r($this->request->data);
			if($this->Session->check('delete_default_banner_image')){
				$action = (int)$this->Session->read('delete_default_banner_image');
				if($action){
					self::__admin_delete_default_banner_image();
				}
			}
			
			foreach($this->request->data['Page'] as $key => $value){
				if($key == 'url_back_redirect' || $key == 'form' ){
					continue;
				}
				if(is_array($value)){
					
					if($key=='banner_image'){
						
				$_options = array(
				'destination'=>Configure::read('Path.Banner'),
						'image'=>$value,
						);
						$_file = $this->System->get_setting('page',$key);
						if(!empty($_file) && !empty($value['name'])){
							if(file_exists(Configure::read('Path.Banner').$this->System->get_setting('page',$key))){
								unlink(Configure::read('Path.Banner').$this->System->get_setting('page',$key));
							}
						}
						if($value['error'] > 0){
							$value=$_file;
						}else{
							//$value = $this->Upload->move_uploaded_file($value,$_options);
							$value = $this->System->Image->upload($_options);
							if(!empty($this->request->data['imgWidth'])){
							$targ_w = $targ_h = 480;
							$jpeg_quality = 100;
			
							$src = Configure::read('Path.Banner').$value;
							$system=explode(".",$src);
							if (preg_match("/jpg|jpeg/",$system[1])){$src_img=imagecreatefromjpeg($src);}
							if (preg_match("/png/",$system[1])){$src_img=imagecreatefrompng($src);}
							if (preg_match("/gif/",$system[1])){$src_img=imagecreatefromgif($src);}
							$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

							imagecopyresampled($dst_r,$src_img,0,0,$this->request->data['imgX1'],$this->request->data['imgY1'],
							$targ_w,$targ_h,$this->request->data['imgWidth'],$this->request->data['imgHeight']);
							
							$new_image='crop_'.$value;
							header('Content-type: image/jpeg');
							imagejpeg($dst_r,Configure::read('Path.Banner').$new_image,$jpeg_quality);
							$value=$new_image;
							}
						
						}
					}else if ($key=='home_page_blocks'){
						$value = json_encode($value);
					}else{
						continue;
					}
				}
				$value = addslashes($value);
				
				if($this->Setting->find('count',array('conditions'=>array('Setting.key'=>$key,'Setting.module'=>'page')))){
					$this->Setting->query("UPDATE `settings` SET `values`=\"$value\" , module=\"page\" WHERE `key`=\"$key\"");
				} else{
					$this->Setting->query("INSERT `settings` SET `values`=\"$value\"  , `key`=\"$key\" , module=\"page\"");
				}
				$this->Session->setFlash(__('Page Setting(s) has been saved successfully'));
			}
			$this->redirect(array('action'=>'settings','?'=>array('back'=>$this->request->data['Page']['url_back_redirect'])));
		}
		$this->Session->delete('delete_default_banner_image');
		Cache::delete('site');
		$this->request->data['Page'] = $this->Setting->find('list',array('fields'=>array('Setting.key','Setting.values'),'conditions'=>array('Setting.module'=>'page')));
		//echo "<pre>";print_r($this->request->data['Page']);die;
		$this->request->data['Page']['home_page_blocks'] = json_decode($this->request->data['Page']['home_page_blocks'],true);
		$referer_url = $this->request->query('back');
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/content_manager/settings',true) :Controller::referer();
		
		} 
		$this->breadcrumbs[] = array(
		'url'=>Router::url('/admin/home'),
		'name'=>'Dashboard'
		);
			$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/content_manager/settings'),
				'name'=>'Page Settings'
		);
		$this->heading =  array("Manage","Page Settings");
		$this->set('referer_url',$referer_url);
		$this->set('page_list',$page_list);
	}
	function admin_delete_banner_image($id= null){
		$this->page = $this->Page->read(null,$id);
		$this->Page->updateAll(
				array('Page.banner_image' => null),
				array('Page.id'=>$id)
			);
		self::__delete_banner_image();
		if ($this->request->is('ajax')) {
			$this->autoRender = false;
		}else{
			$this->redirect(array('action'=>'add',$id));
		}
	}
	private function __delete_banner_image(){
		App::uses('ImageResizeHelper', 'View/Helper');
		$ImageResize = new ImageResizeHelper();
		$imgArr = array('source_path'=>Configure::read('Path.Banner'),'img_name'=>$this->page['Page']['banner_image'],'width'=>Configure::read('image_edit_width'),'height'=>Configure::read('image_edit_height'));
		$ImageResize->deleteThumbImage($imgArr);
		
		$imgArr = array('source_path'=>Configure::read('Path.Banner'),'img_name'=>$this->page['Page']['banner_image'],'width'=>Configure::read('banner_image_width'),'height'=>Configure::read('banner_image_height'));
		$ImageResize->deleteThumbImage($imgArr);
		
		@unlink(Configure::read('Path.Banner'). $this->page['Page']['banner_image']);
		@unlink(Configure::read('Path.Banner'). 'crop_'.$this->page['Page']['banner_image']);
		
	}
	function admin_delete($id=null){
		$this->autoRender = false;
		$data=$this->request->data['Page']['id'];
		$action = $this->request->data['Page']['action'];
		$ans="0";
		foreach($data as $value){
			
			if($value!='0'){
				$page = $this->Page->find('first', array('conditions'=> array('Page.id' => $value)));
				if((int)$page['Page']['system_page']==1 || (int)$page['Page']['status']==2){
					continue;
				}
				
				if($action=='Publish'){
					$page['Page']['id'] = $value;
					$page['Page']['status']=1;
					$this->Page->create();
					$this->Page->save($page);
					$ans="1";
				}
				if($action=='Unpublish'){
					$page['Page']['id'] = $value;
					$page['Page']['status']=0;
					$this->Page->create();
					$this->Page->save($page);
					$ans="1";
				}
				if($action=='Delete'){
					if (!empty($page['Page']['banner_image'])) {
						   @unlink(WWW_ROOT."img/banner/". $page['Page']['banner_image']);
					}
					$this->Page->delete($value);
					$this->Page->delete_routes($value,'Page');
					$options = array(
					'ref_id'=>$value,
					'module'=>'Page',
					);
					$this->Page->delete_menu($options);
					
					$this->loadModel('Link');	
					$this->Link->query("Delete FROM`links` WHERE `ref_id`=\"$value\" AND module=\"Page\"");
					
					$ans="2";
				}
			}
		}
		if($ans=="1"){
			$this->Session->setFlash(__('Page has been '.strtolower($this->data['Page']['action']).'ed successfully', true));
		}
		else if($ans=="2"){
			$this->Session->setFlash(__('Page has been '.strtolower($this->data['Page']['action']).'d successfully', true));
		}else{
			$this->Session->setFlash(__('Please select pages', true),'default','','error');
		}
		$this->redirect($this->request->data['Page']['redirect']);
                 
	}
	private function __load_page($id = null){
		
		
		$page =  array();
		$page = $this->Page->find('first',array('conditions'=>array('Page.id'=>$id,'Page.status'=>1)));
		if(empty($page)){
			return null;
		}
		$this->current_id = $id;
		$page['Pages'] = $this->Page->find('all',array('conditions'=>array('Page.parent_id'=>$id,'Page.status'=>1),'order'=>array('Page.page_order'=>'ASC','Page.id'=>'DESC')));
		
		if((int)Configure::read('Section.default_banner_image') && ($page['Page']['use_default_image'] || $this->System->get_setting('page','override_banner_image'))){
			$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		}
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		$page['Gallery'] = array();
		if((int)Configure::read('Section.gallery') && (int)$this->_is_active_plugins('GalleryManager')){
			
			$this->loadModel('GalleryManager.Gallery');
			if($page['Page']['gallery']!=""){	
				$this->Gallery->bindModel(
					array('hasMany' => array(
							'GalleryImage'
						)
					)
				);
				$gallery=$this->Gallery->find('first',array('conditions'=>array('Gallery.id'=>$page['Page']['gallery'],'Gallery.status'=>1)));
				$page = array_merge($page,$gallery);
			}
		}else{
			$page['Page']['gallery'] = null;
		}
		
		return $page;
	}
	function home(){
		
		$page = self::__load_page((int)$this->System->get_setting('page','default_home_page'));
		//print_r($page);die;
		if (empty($page)) {
			throw new NotFoundException('404 Error - Page not found');
		}
		
		//print_r($page);die;
		$this->System->set_seo('site_title',$page['Page']['page_title']);
		$this->System->set_seo('site_metakeyword',$page['Page']['page_metakeyword']);
		$this->System->set_seo('site_metadescription',$page['Page']['page_metadescription']);
		$this->set('page',$page);
	}
	
	
	public function search($search=null){
		$page = self::__load_page(68);
		
		$this->paginate = array();
		$condition = null;
		if($search=="_blank"){
			$search=null;
		}else if(empty($search)){
			$search=null;
		}
		$this->paginate['limit']=10;
		if($this->request->is('post')){
			if(!empty($this->request->data['search'])){
				$search = $this->request->data['search'];
			}else{
				$search = '_blank';
			}
			$this->redirect(array('plugin'=>'content_manager','controller'=>'pages','action'=>'search',$search));
		}
		
		$search_results = array();
		
		// Do not forgot to set this, not sure why
		$this->Search->recursive = 0;

		// Setting up paging parameters
		$this->paginate = array('Search'=>array('limit'=>5,'conditions'=>$search));
		if(!empty($search)){
			$search_results =$this->paginate('Search');
		}
		$this->set('search', $search);
		$this->set('search_results', $search_results);
		$this->System->set_seo('site_title','Search Results');
		$this->System->set_seo('site_metakeyword','Search Results');
		$this->System->set_seo('site_metadescription','Search Results');
	}
	
	public function get($id = null){
		$this->autoRender = false;
		$page=$this->Page->find('first',array('conditions'=>array('Page.id'=>(int)$id,'Page.status'=>1)));
		return $page;
	}
	public function view($id=null){
		
		if(!empty($this->request->data)){
			 
			$this->contact();
		}
		if($id==(int)$this->System->get_setting('page','default_home_page')){
			//echo $id;die;
			$this->redirect(array('plugin'=>'content_manager','controller'=>'pages','action'=>'home'));
			//exit();
		}
		
		$page = self::__load_page($id);
		if (empty($page)){
			if($this->request->is('requested')){
				$this->autoRender =false;
				return "";
			}
			throw new NotFoundException('404 Error - Page not found');
		}
		
		$this->set('id', $id);
		$this->set('page', $page);
		$this->System->set_seo('site_title',$page['Page']['page_title']);
		$this->System->set_seo('site_metakeyword',$page['Page']['page_metakeyword']);
		$this->System->set_seo('site_metadescription',$page['Page']['page_metadescription']);
		$path = App::path('View','ContentManager');
		$view_path = array_pop($path);
		
		if(!empty($page['Page']['page_template']) && file_exists($view_path.'Pages'.DS.'templates'.DS.$page['Page']['page_template'].'.ctp')){
			$this->render('Pages/templates/'.$page['Page']['page_template']);
		}else{
			 $this->render('Pages/templates/default');
		}
	}
	public function preview($id=null){
		if(!$this->Auth->user('id')){
			throw new NotFoundException('404 Error - Page not found');
		}
		
		
		
		if(empty($this->request->data)){
		$this->request->data = self::__load_page($id); 
	    }

	    $existing_image='';
			if($this->request->data['Page']['id']){
				$banner_image = $this->Page->find('first',array('fields'=>array('Page.banner_image'),'conditions'=>array('Page.id'=>$this->request->data['Page']['id'])));
				$existing_image = $banner_image['Page']['banner_image'];
			}
			
			$_options = array(
			'destination'=>Configure::read('Path.Banner'),
			'image'=>$this->request->data['Page']['banner_image']
			);
			if($this->request->data['Page']['banner_image']['error'] > 0 && !empty($this->request->data['Page']['id'])){
				$this->request->data['Page']['banner_image'] = $existing_image;
			}else{
				if($this->request->data['Page']['banner_image']['error'] < 1){
				$this->request->data['Page']['banner_image'] = $this->System->Image->upload($_options);
				$this->request->data['Page']['is_cropped'] = 0;
				}else{
					$this->request->data['Page']['banner_image'] = "";
				}
			}
		if((int)Configure::read('Section.default_banner_image') && ($this->request->data['Page']['use_default_image'] || $this->System->get_setting('page','override_banner_image'))){
			$this->request->data['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
			}	
		if (empty($this->request->data)){
			if($this->request->is('requested')){
				$this->autoRender =false;
				return "";
			}
			throw new NotFoundException('404 Error - Page not found');
		}
		$this->set('id', $id);
		$this->set('page', $this->request->data);
		$this->System->set_data('banner_image',$this->request->data['Page']['banner_image']);
		$this->System->set_seo('site_title','Preview : '.$this->request->data['Page']['page_title']);
		$this->System->set_seo('site_metakeyword',$this->request->data['Page']['page_metakeyword']);
		$this->System->set_seo('site_metadescription',$this->request->data['Page']['page_metadescription']);
		
		
		$path = App::path('View','ContentManager');
		$view_path = array_pop($path);
		
		
		if($id!=null){
			if(!empty($this->request->data['Page']['page_template']) && file_exists($view_path.'Pages'.DS.'templates'.DS.$this->request->data['Page']['page_template'].'.ctp')){
				$this->render('Pages/templates/'.$this->request->data['Page']['page_template']); 
			}else{
				 $this->render('Pages/templates/default');
			}
		}else{
		
			if(!empty($this->request->data['Page']['page_template']) && file_exists($view_path.'Pages'.DS.'templates'.DS.'preview_'.$this->request->data['Page']['page_template'].'.ctp')){
				$this->render('Pages/templates/preview_'.$this->request->data['Page']['page_template']); 
			}else{
				 $this->render('Pages/templates/default');
			}
		}
		
	}
	public function contact(){
	
		
		//pr($this->request->data);die; 
		$options = array();
		$options['replacement'] = array('{NAME}'=>$this->request->data['Page']['name']);
		
		$options['to'] = $this->request->data['Page']['email']; //mixed
		
		//$options['emailFormat'] = "html";
		
		//$options['viewVars'] = array('data'=>'This is test');
		//$options['message'] = "This is message";
		$this->MyMail->SendMail(10,$options);
		
		$options = array();
		$options['replacement'] = array('{NAME}'=>$this->request->data['Page']['name'],'{EMAIL}'=>$this->request->data['Page']['email'],'{PHONE}'=>$this->request->data['Page']['phone'],'{SUBJECT}'=>$this->request->data['Page']['subject'],'{MESSAGE}'=>$this->request->data['Page']['message']);
		$options['to'] = $this->System->get_setting('site','site_contact_email'); 
		$options['from'] = $this->System->get_setting('site','site_contact_noreply');
		//$options['replyTo'] = array($this->request->data['Page']['email']); 
		//$options['replyTo'] = $this->System->get_setting('site','site_contact_replyto');
		$this->MyMail->SendMail(8,$options);
		$this->redirect(array('action'=>'view',30));
		
		
		//$this->EmailSend->SendMail($this->request->data);
	}
	
	function validation(){
		
		if(!empty($this->request->data['Page']['form'])){
			if($this->request->data['Page']['form']=="page_add" && $this->request->data['Page']['status']==2){
				return true;
			}
			$this->Page->setValidation($this->request->data['Page']['form']);
		}else{
			throw new NotFoundException('404 Error - Page not found');
		}
		$this->Page->set($this->request->data);
		return $this->Page->validates();
	}
	public function ajax_validation($returnType = 'json'){
		//print_r($this->request->data);die;
		$this->autoRender = false;
		if(!empty($this->request->data)){
			if(!empty($this->request->data['Page']['form'])){
				$this->Page->setValidation($this->request->data['Page']['form']);
			}
			$this->Page->set($this->request->data);
			$result = array();
			if($this->request->data['Page']['form']=="page_add" && $this->request->data['Page']['status']==2){
				$result['error'] = 0;
			}else{
				if($this->Page->validates()){
					$result['error'] = 0;
				}else{
					$result['error'] = 1;
					$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
				}
			}
			$errors = array();
			$result['errors'] = $this->Page->validationErrors;
			foreach($result['errors'] as $field => $data){
			  $errors['Page'.Inflector::camelize($field)] = array_pop($data);
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
		$pages=$this->Page->find('all',array('limit'=>5,'order'=>array('Page.system_page'=>'ASC','Page.id'=>'desc')));
		$this->set('pages',$pages);
	}
	
	public function get_sub_page($id=null){
		
		$this->autoRender = false;
		$page=$this->Page->find('all',array('conditions'=>array('Page.parent_id'=>(int)$id,'Page.status'=>1)));
		return $page;
	}
	
	public function play_video($video = null){
		
		$this->System->set_seo('site_title','Video');
		$page['Page']['banner_image'] = $this->System->get_setting('page','banner_image');
		$this->System->set_data('banner_image',$page['Page']['banner_image']);
		$this->set('video',$video);
	}
}
?>
