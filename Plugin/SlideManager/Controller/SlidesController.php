<?php
Class SlidesController extends SlideManagerAppController{
	public $uses = array('SlideManager.Slide');
	public $helpers = array('Form','ImageResize');
	public $components=array('Email','RequestHandler');
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
			$this->redirect(array('plugin'=>'slide_manager','controller'=>'slides','action'=>'index' ,$search,$limit));
		}
		$this->paginate['order']=array('Slide.reorder'=>'ASC','Slide.id'=>'DESC');		
		
		if($search!=null){
			$search = urldecode($search);
			$condition['Slide.name like'] = '%'.$search.'%';
		}
		
		$slides=$this->paginate("Slide", $condition);	
		// echo "<pre>"; print_r($slides); die;
		
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/slide_manager/slides'),
			'name'=>'Manage Slide'
		);
		$this->heading =  array("Manage","Slide");
		$this->set('slides', $slides);
		$this->set('search',$search);
		$this->set('limit',$limit);
		$this->set('url','/'.$this->params->url);
		$this->set('themes',$this->System->get_themes());
	}
	function ajax_sort(){
		$this->autoRender = false;
		foreach($_POST['sort'] as $order => $id){
			$slide= array();
			$slide['Slide']['id'] = $id;
			$slide['Slide']['reorder'] = $order;
		  
			$this->Slide->create();
			$this->Slide->save($slide);
		}
	}
	function show(){
		//$slider = Cache::read('slides');
		//if(empty($slider)){
			$slider=$this->Slide->find('all',array('conditions'=>array('Slide.status'=>'1', 'OR'=>array('Slide.theme'=>$this->System->get_theme(),'Slide.theme IS NULL')),'order'=>array('Slide.reorder'=>'ASC')));
			//Cache::write('slides',$slider);
		//}
		//print_r($slider);die;
		$this->set('slider',$slider);
		$this->set('id',1);
	}
	function admin_add($id=null){
		$this->breadcrumbs[] = array(
		'url'=>Router::url('/admin/home'),
		'name'=>'Dashboard'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/slide_manager/slides'),
				'name'=>'Manage Slide'
		);
		$this->breadcrumbs[] = array(
				'url'=>Router::url('/admin/slide_manager/slides/add'),
				'name'=>($id==null)?'Add Slide':'Update Slide'
		);
		if($id==null){
			$this->heading =  array("Add","Slide");
		}else{
			$this->heading =  array("Update","Slide");
		}
		
		if(!empty($this->request->data) && $this->validation()){
			$destination = Configure::read('Path.Slide');
			if($this->request->data['Slide']['id']){
				$slide_image = $this->Slide->find('first',array('fields'=>array('Slide.image','Slide.logo'),'conditions'=>array('Slide.id'=>$this->request->data['Slide']['id'])));
			}
			$image_name='';
			if($this->request->data['Slide']['id']){
				$image_name = $slide_image['Slide']['image'];
			}
			if(empty($this->request->data['Slide']['theme'])){
				$this->request->data['Slide']['theme'] = null;
			}
			
			if($this->request->data['Slide']['image']['error'] < 1){
					$_options = array(
				'destination'=>Configure::read('Path.Slide'),
				'image'=>$this->request->data['Slide']['image']
				);
				$this->request->data['Slide']['image'] = $this->System->Image->upload($_options);
			////  start block for crop img before save	
		$value = $this->request->data['Slide']['image'];
		//echo "<pre>";print_r($value); die;
		if(!empty($this->request->data['imgWidth'])){
		$targ_w = $targ_h = 481;
		$jpeg_quality = 100;
		$src = Configure::read('Path.Slide').$value;
		$system=explode(".",$src);
		if (preg_match("/jpg|jpeg/",$system[1])){$src_img=imagecreatefromjpeg($src);}
		if (preg_match("/png/",$system[1])){$src_img=imagecreatefrompng($src);}
		if (preg_match("/gif/",$system[1])){$src_img=imagecreatefromgif($src);}
		$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
        imagecopyresampled($dst_r,$src_img,0,0,$this->request->data['imgX1'],$this->request->data['imgY1'],
		$targ_w,$targ_h,$this->request->data['imgWidth'],$this->request->data['imgHeight']);
		$new_image='crop_'.$value;
		//header('Content-type: image/jpeg');
		imagejpeg($dst_r,Configure::read('Path.Slide').$new_image,$jpeg_quality);
		$this->request->data['Slide']['image']=$new_image;
		
		}
		////end of block
		//echo "<pre>";print_r($value); die;
			    //$this->request->data['Slide']['image'] = self::_manage_image($this->request->data['Slide']['image']);
				if($image_name!=''){
					unlink($destination.$image_name);
				}
			}else{
				$this->request->data['Slide']['image'] = $image_name;
			}
			
			if(!$id){
				$this->request->data['Slide']['created_at']=date('Y-m-d H:i:s');
				$this->request->data['Slide']['status']=1;
			}else{
				
				$this->request->data['Slide']['updated_at']=date('Y-m-d H:i:s');
			}
			$this->Slide->create();
			$this->Slide->save($this->request->data,array('validate'=>false));
			$id = $this->Slide->id;	
			Cache::delete('slides');
			if ($this->request->data['Slide']['id']) {
				$this->Session->setFlash(__('Slide has been updated successfully'));
			} 
			else {
				$this->Session->setFlash(__('Slide has been added successfully'));
			}
			if(isset($this->request->data['save'])){
				$this->redirect(array('controller' => 'slides', 'action' => 'admin_add',$id));
			
			}else{
				$this->redirect(array('controller' => 'slides','action'=>'admin_index'));
			}
		}
		else{
			if($id!=null){
				$this->request->data = $this->Slide->read(null,$id);
			}else{
				$this->request->data = array();
			   
			}
		} 
		$this->set('url',Controller::referer());
		$referer_url = $this->request->query('back');
		
		if(!empty($referer_url)){
			$referer_url= $this->request->query('back');
		}else{
			$referer_url=(Controller::referer()=="/")? Router::url('/admin/slide_manager/slides/add/'.$id,true) :Controller::referer();
			
		}
		$this->set('referer_url',$referer_url);
		$this->set('themes',$this->System->get_themes());
		
		
	}
	
	function admin_default_image_crop($id=null){
		$path  = $this->webroot;
		$this->Image = $this->Components->load('Image');
		$this->Image->startup($this);
		$slide_image = $this->Slide->find('first',array('fields'=>array('Slide.image'),'conditions'=>array('Slide.id'=>$id)));
		if($this->request->is('post')){
			
			$org_image_breaks = explode('.',$slide_image['Slide']['image']);
			$ext = array_pop($org_image_breaks);
			$origFile = $slide_image['Slide']['image'];

			$src = Configure::read('Path.Slide').$slide_image['Slide']['image'];
			$old_slide = Configure::read('Path.Slide').$slide_image['Slide']['image'];
			$org_image_breaks = implode('.',$org_image_breaks);
			$org_image_breaks = explode('_',$org_image_breaks);
			array_pop($org_image_breaks);
			$org_image_breaks = implode('_',$org_image_breaks);
			$new_name =$org_image_breaks.'_'.time().'.'.$ext;
			$dst =  Configure::read('Path.Slide').$new_name;
			
			$start_width = $this->data['x'];
			$start_height = $this->data['y'];
			$width = $this->data['width'];
			$height = $this->data['height'];
			$key = 'slide_image';
			$thumb = $this->Image->crop($src,$dst,$width,$height,$start_width,$start_height,$this->data['scale']);
			$slide_data = array();
			$slide_data['Slide']['id'] = $id;
			$slide_data['Slide']['image'] = $new_name;
			
			$_options = array(
						'destination'=>Configure::read('Path.Slide'),
						);
			if($this->Slide->save($slide_data,array('validate'=>false))){
				if($slide_image['Slide']['image']!='' && file_exists($old_slide)){
					unlink($old_slide);
				}
				$this->Session->setFlash('Image cropped and saved.');
				$this->redirect(array('controller' => 'slides', 'action' => 'admin_add',$id));
			}
			Cache::delete('site');
			$this->redirect(array('action'=>'admin_add',$id));
		}
		$this->set('slide_image',$slide_image);
	}
	
	function admin_delete($id=null){
		$this->autoRender = false;
		$destination = Configure::read('Path.Slide');
	   // print_r($this->request->data);
		$data=$this->request->data['Slide']['id'];
		$action = $this->request->data['Slide']['action'];
		$ans="0";
		if(!empty($data)){ //print_r($data); die;
			foreach($data as $value){
				if($value!='0'){
					if($action=='Publish'){
						$slide['Slide']['id'] = $value;
						$slide['Slide']['status']=1;
						$this->Slide->create();
						$this->Slide->save($slide);
						$ans="1";
					}
					if($action=='Unpublish'){
						$slide['Slide']['id'] = $value;
						$slide['Slide']['status']=0;
						$this->Slide->create();
						$this->Slide->save($slide);
						$ans="1";
					}
					if($action=='Delete'){
						$slide = $this->Slide->find('first', array('conditions'=> array('Slide.id' => $value),'fields' => array('Slide.image','Slide.logo')));
						$aa= $slide['Slide']['image'];
						$b= substr($aa,0,5);
		              if($b=="crop_")
		                 {
		               $oldimage=substr($aa,5);
	                     }
	                  else
	                   {
			               $oldimage=$aa;
		                } 
						/*echo $aa;
						echo "<br/>";
						print_r($oldimage);
						die;
						echo "<pre>" ;
						print_r($slide);
						die;*/
						if (!empty($slide['Slide']['image']))
						{
						 if($aa==$oldimage)
						 {
							 	
						   @unlink($destination. $slide['Slide']['image']);
					       }
					      else
					      {
							 @unlink($destination. $slide['Slide']['image']);
							 @unlink($destination.$oldimage ); 
						  }
						  
						}
						if (!empty($slide['Slide']['logo'])){
						   @unlink($destination. $slide['Slide']['logo']);
						}	
						$this->Slide->delete($value);
						$ans="2";
					}
				}
			}
			if($ans=="1"){
				$this->Session->setFlash(__('Slide has been '.strtolower($this->data['Slide']['action']).'ed successfully', true));
			}
			else if($ans=="2"){
				$this->Session->setFlash(__('Slide has been '.strtolower($this->data['Slide']['action']).'d successfully', true));
			}else{
				$this->Session->setFlash(__('Please Select any Slide', true),'default','','error');
			}
			
			$this->redirect($this->request->data['Slide']['redirect']);
		}
		$this->redirect($this->request->data['Slide']['redirect']);
			 
	}
	function validation(){
		if(!empty($this->request->data['Slide']['form'])){
			$this->Slide->setValidation($this->request->data['Slide']['form']);
		}
		$this->Slide->set($this->request->data);
		if($this->Slide->validates()){
			return true;
		}else{
			$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
			return false;
		}
	}
	public function ajax_validation($returnType = 'json'){
		
		$this->autoRender = false;
		if(!empty($this->request->data)){
			if(!empty($this->request->data['Slide']['form'])){
				$this->Slide->setValidation($this->request->data['Slide']['form']);
			}
			$this->Slide->set($this->request->data);
			$result = array();
			if($this->Slide->validates()){
					$result['error'] = 0;
			}else{
				$result['error'] = 1;
				$this->Session->setFlash(__('Please fill all the required fields'),'default',array(),'error');
			}
			$errors = array();
			$result['errors'] = $this->Slide->validationErrors;
			foreach($result['errors'] as $field => $data){
			  $errors['Slide'.Inflector::camelize($field)] = array_pop($data);
			}
			$result['errors'] = $errors;
			$view = new View();
			
			$result['error_message'] = $view->element('admin/message');
			echo json_encode($result);
			return;
		}
		echo json_encode(array());
	}

	private function _manage_logo($image = array()) {
			if ($image['error'] > 0) {
				return null;
			} else {
				$existing_logo = array();
				if ($image['error'] > 0) {
					return $existing_logo['Slide']['logo'];
				} else {
					$destination =Configure::read('Path.Slide');
					$ext = explode('.', $image['name']);
					$get_ext = array_pop($ext);
				$name = basename($image['name'],'.'.$get_ext);
		
					$image_logo = $name . '_' . time() . '.' . $get_ext;
					move_uploaded_file($image['tmp_name'], $destination . $image_logo);
					if (!empty($existing_logo)) {
						unlink($destination . $existing_logo['Slide']['logo']);
					}
					return $image_logo;
				}
			}
		}
	function admin_image_crop($id=null){
		$path  = $this->webroot;
		$this->Img = $this->Components->load('Image');
		$this->Img->startup($this);
		$slide_image = $this->Slide->find('first',array('fields'=>array('Slide.image'),'conditions'=>array('Slide.id'=>$id)));
		
		if($this->request->is('post')){
			$this->Image = $this->Components->load('Image');
			$org_image_breaks = explode('.',$slide_image['Slide']['image']);
			$ext = array_pop($org_image_breaks);
			$origFile = $slide_image['Slide']['image'];

			$src = Configure::read('Path.Slide').$slide_image['Slide']['image'];
			$old_slide = Configure::read('Path.Slide').$slide_image['Slide']['image'];
			$org_image_breaks = implode('.',$org_image_breaks);
			$org_image_breaks = explode('_',$org_image_breaks);
			array_pop($org_image_breaks);
			$org_image_breaks = implode('_',$org_image_breaks);
			$new_name =$org_image_breaks.'_'.time().'.'.$ext;
			$dst =  Configure::read('Path.Slide').$new_name;
			
			$start_width = $this->data['start_width'];
			$start_height = $this->data['start_height'];
			$width = $this->data['width'];
			$height = $this->data['height'];
			
			$thumb = $this->Image->crop($src,$dst,$width,$height,$start_width,$start_height,1);
			$slide_data = array();
			$slide_data['Slide']['id'] = $id;
			$slide_data['Slide']['image'] = $new_name;
			
			if($this->Slide->save($slide_data,array('validate'=>false))){
				if($slide_image['Slide']['image']!='' && file_exists($old_slide)){
					unlink($old_slide);
				}
				$this->Session->setFlash('Image cropped and saved.');
				$this->redirect(array('controller' => 'slides', 'action' => 'admin_add',$id));
			}
			
		}
		$this->set('slide_image',$slide_image);
		 
    
	}
	public function admin_dashboard(){
		$home_slide=$this->Slide->find('all',array('limit'=>5,'order'=>array('Slide.id'=>'desc')));
		$this->set('slides',$home_slide);
	}
}
?>
