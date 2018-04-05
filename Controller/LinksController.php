<?php
Class LinksController extends AppController
	{
	public $uses = array('Link','Menu');
	public $paginate = array();
    public $id = null;
    public $ajax_session_name = "Ajax.Links";
	
	public function beforeFilter(){
		parent::beforeFilter();
		$this->UserLib->load_active_user($this);
		$plugin = Inflector::camelize('content_manager');
		
		if($this->params['action']=="admin_index"){
			if((int)$this->UserLib->check_access_permission($plugin)==0 ){
				$this->UserLib->add_authorize_action(array($this->params['action']));
				$this->UserLib->add_authenticate_action(array($this->params['action']));
				$this->UserLib->check_authenticate($this);
				$this->UserLib->check_authorize($this);
			}
		}
		
		//
		if($this->params['action']=="admin_add" || $this->params['action']=="admin_ajax_save"  || $this->params['action']=="admin_ajax_remove"){
			if(!(int)$this->UserLib->check_modify_permission($plugin) ){
				
				if($this->params['action']=="admin_add"){
					$this->UserLib->add_authorize_action(array($this->params['action']));
					$this->UserLib->add_authenticate_action(array($this->params['action']));
					$this->UserLib->check_authenticate($this);
					$this->UserLib->check_authorize($this);
				}
			}
		}
	}    
   
    
		/*This below function is used to display information from database and pagination*/
		function admin_index($menu_id=null, $search=null){
			$pages = array();
			if($this->_is_active_plugins('ContentManager')){
				$this->loadModel('ContentManager.Page');
				$pages = $this->Page->find('threaded',array('recursive' => 2,'conditions'=>array('Page.status'=>1,'OR'=>array('Page.system_page'=>0,'Page.system_page IS NULL'))));
			}
			
			$gallery = array();
			if($this->_is_active_plugins('GalleryManager')){
				$this->loadModel('GalleryManager.Gallery');
				$gallery = $this->Gallery->find('all',array('conditions'=>array('Gallery.status'=>1),'order'=>array('Gallery.id'=>'DESC')));
			}
			
			$links = $this->Link->find('threaded',array('recursive'=>2,'conditions'=>array('Link.menu_id'=>$menu_id,'Link.status'=>1),'order'=>array('Link.reorder'=>'ASC')));
			
			
			/*This below code is used for adding breadcrumbs in page*/
			
            if($menu_id!=0){
				$parent_detail = $this->Menu->read(null,$menu_id);
				if(empty($parent_detail)){
					throw new NotFoundException('This page is restricted');
				}
				$this->request->data['Link']['menu_name'] = $parent_detail['Menu']['name'];
				$this->request->data['Link']['auto_add'] = $parent_detail['Menu']['auto_add'];				
				$this->request->data['Link']['default_menu'] = $parent_detail['Menu']['default_menu'];				
			}
			$all_menus = $this->Menu->find('list',array('fields'=>array('Menu.id','Menu.name')));	
            
            $this->breadcrumbs[] = array(
                'url'=>Router::url('/admin/home'),
                'name'=>'Dashboard'
            );
            $this->breadcrumbs[] = array(
                'url'=>Router::url('/admin/menus'),
                'name'=>'Manage Menu'
            );
            $this->heading=array("Manage","Menu");
           
            
            /*End of adding breadcrumbs in page*/
						
			/*variables are being set for pages to pass data*/
            
            $this->set('menu_id',$menu_id);
            $this->set('parent_detail',$parent_detail);
            $this->set('pages',$pages);
            $this->set('gallery',$gallery);
            $this->set('links',$links);
            $this->set('all_links',json_encode($links));
            $this->set('all_menus',$all_menus);
            $this->set('search',$search);
            $this->set('url','/'.$this->params->url);
            
             /*End of setting variable*/
            
            /*below code for ajax request for pagination or loader*/
             
            if($this->request->is('ajax')){
                $this->layout = '';
                $this -> Render('ajax_admin_index');
               
            }
            
            /*End of if statement*/
			 
        }
		/*Function for add/update data*/ 
		function admin_ajax_save($menu_id=0){
			$this->autoRender = false;
			
			/* Check user permission to modify */
			$plugin = Inflector::camelize('content_manager');
			if(!(int)$this->UserLib->check_modify_permission($plugin) ){
				$this->UserLib->set_modify_error_message();
				$view = new View();
				$data_n['error'] = 1; 
				$data_n['error_message'] = $view->element('admin/message');
				echo json_encode($data_n);
				return;
			}
			
			
			
			
			$data = array();
			if(!empty($this->request->data)){
				$reorder = $this->Link->find('count',array('conditions'=>array('Link.menu_id'=>$menu_id)));
				$link = array();
				$link['Link']['id']="";
				$link['Link']['tag_title']="";
				$link['Link']['menu_id']=$menu_id;
				$link['Link']['ref_id']="";
				$link['Link']['new_window']=0;
				$link['Link']['parent_id']=0;
				$link['Link']['url']=NULL;
				$link['Link']['reorder']=$reorder++;
				$link['Link']['status']=0;
				
				if($this->request->data['Link']['type']=="Page"){
					
					if(!empty($this->request->data['Link']['pages'])){
						$pages = $this->Page->find('list',array('fields'=>array('id','name'),'conditions'=>array('Page.id'=>$this->request->data['Link']['pages'])));
						foreach($this->request->data['Link']['pages'] as $page_id){
							
							$link['Link']['name']=$name =$pages[$page_id];
							$link['Link']['ref_id']=$page_id;
							$link['Link']['module']="Page";
							$link['Link']['auto_add']=0;
							/*
							$link = array();
							$link['id']="";
							$link['name']=$name =$pages[$page_id];
							$link['menu_id']=$menu_id;
							$link['parent_id']=0;
							$link['ref_id']=$page_id;
							$link['module']="Page";
							$link['url']=null;
							$link['reorder']=$reorder++;
							*/
							$this->Link->create();
							$this->Link->save($link);
							$Link = $this->Link->read(null,$this->Link->id);
							
							$data[]  = $Link['Link'];
							
						}
					}
				}
				else if($this->request->data['Link']['type']=="Gallery"){
					$this->loadModel('GalleryManager.Gallery');
				
					if(!empty($this->request->data['Link']['gallery'])){
						$pages = $this->Gallery->find('list',array('fields'=>array('id','name'),'conditions'=>array('Gallery.id'=>$this->request->data['Link']['gallery'])));
						foreach($this->request->data['Link']['gallery'] as $page_id){
							
							$link['Link']['name']=$name =$pages[$page_id];
							$link['Link']['ref_id']=$page_id;
							$link['Link']['module']="Gallery";
							$link['Link']['auto_add']=0;
							/*
							$link = array();
							$link['id']="";
							$link['name']=$name =$pages[$page_id];
							$link['menu_id']=$menu_id;
							$link['parent_id']=0;
							$link['ref_id']=$page_id;
							$link['module']="Page";
							$link['url']=null;
							$link['reorder']=$reorder++;
							*/
							$this->Link->create();
							$this->Link->save($link);
							$Link = $this->Link->read(null,$this->Link->id);
							
							$data[]  = $Link['Link'];
							
						}
					}
				}
				else{
					//if(self::validation('CustomLink')){					
						$link['Link']['name']=$name =$this->request->data['Link']['name'];
						$link['Link']['module']="Custom";
						$link['Link']['url']=$this->request->data['Link']['url'];
						$link['Link']['auto_add']=0;
						$this->Link->create();
						$this->Link->save($link);
						//$link['Link']['id'] = $this->Link->id;
						$Link = $this->Link->read(null,$this->Link->id);
						
						$data[]  = $Link['Link'];	
					//}
				}			
			}
			$data_n['links'] = $data; 
			$data_n['error'] = 0; 
			if(empty($data)){
				$view = new View();
				$data_n['error'] = 1; 
				$this->Session->setFlash(__('Please select checkbox to add on this menu.'),'default',array(),'error');
				$data_n['error_message'] = $view->element('admin/message');
			}
			
			echo json_encode($data_n);
			return;
		}
		function admin_ajax_remove($link_id=0){
			$this->autoRender = false;
			//$this->Link->delete($_POST['link_id']);
			
			
		}
		function admin_add($menu_id=0){
			if(!empty($this->request->data)){
				//echo '<pre>';print_r($this->request->data);die;
				self::__delete_extra_link($menu_id);
				$this->request->data['Link']['links'] = json_decode($this->request->data['Link']['links'],true);
				
				self::__recursive_add($this->request->data['Link']['links']);
				
				$this->Menu->id = $menu_id;
				
				$this->Menu->saveField('name',$this->request->data['Link']['menu_name']);
				if(!empty($this->request->data['Link']['auto_add'])){
					$this->Menu->saveField('auto_add',$this->request->data['Link']['auto_add']);
				}
				//if(!empty($this->request->data['Link']['default_menu'])){
					$this->Menu->updateAll(array('Menu.default_menu' => $this->request->data['Link']['default_menu'],'Menu.auto_add' => $this->request->data['Link']['default_menu']),array('Menu.id' => $menu_id));
					//$this->Menu->saveField('default_menu',$this->request->data['Link']['default_menu']);
					//$this->Menu->saveField('auto_add',$this->request->data['Link']['default_menu']);
				//}
				$this->Menu->saveField('updated_at',date("Y-m-d H:i:s"));
				$this->Session->setFlash(__('Menu has been updated successfully', true));
				$this->redirect(array('action'=>'index',$menu_id));
			}else{
				$this->redirect(array('action'=>'index',$menu_id));
			}
			
		}
		private function __delete_extra_link($menu_id){
			$links = $this->Link->find('list',array('fields'=>array('Link.id'),'conditions'=>array('Link.menu_id'=>$menu_id)));
			$data = array();
			foreach($links as $link){
				if(!in_array($link,array_keys($this->request->data['Link']))){
					$data[] =  $link;
				}
			}
			$this->Link->deleteAll(array('Link.id' => $data));
			
		}
		private function __recursive_add($links,$parent_id = 0){
			$reorder = 0;
			
			foreach($links as $link_data){
				$link = array();
				$link['Link'] = $this->request->data['Link'][$link_data['id']];
				$link['Link']['id'] = $link_data['id'];
				$link['Link']['parent_id'] = (int)$parent_id;
				$link['Link']['reorder'] = (int)$reorder++;
				$link['Link']['status'] = 1;
				$this->Link->create();
				$this->Link->save($link);
				if(isset($link_data['children'])){
					self::__recursive_add($link_data['children'],$link_data['id']);
				}
				
				
				
			}
			
		}
		function admin_add_old($menu_id=0,$id=null){
			
			/*Below code to add breadcrumbs in page*/
			
			$this->breadcrumbs[] = array(
			'url'=>Router::url('/admin/home'),
			'name'=>'Dashboard'
			);
            $this->breadcrumbs[] = array(
                    'url'=>Router::url('/admin/links/index/'.$menu_id),
                    'name'=>'Manage Link'
            );
            $this->breadcrumbs[] = array(
                    'url'=>Router::url('/admin/links/add/'.$menu_id),
                    'name'=>($id==null)?'Add Link':'Update Link'
            );
            
            /****End of adding breadcrumbs*****/
            
            /*This below code is used to add/update of data in table*/
            
            if(!empty($this->request->data)){
				echo '<pre>';print_r($this->request->data);die; 
                if(!$id){
				$this->request->data['Link']['created_at']=date('Y-m-d H:i:s');
				$this->request->data['Link']['menu_id']=$menu_id;
				}else{
				$this->request->data['Link']['updated_at']=date('Y-m-d H:i:s');
				$this->request->data['Link']['menu_id']=$menu_id;
				}
                $this->Link->create();
                $this->Link->save($this->request->data);
                if ($this->request->data['Link']['id']) {
					$this->Session->setFlash(__('Link has been updated successfully'));
					} 
					else {
						$this->Session->setFlash(__('Link has been added successfully'));
					}
                $this->redirect($this->request->data['Link']['redirect']);
                
            }
            
            /*End of add/update of form record db table*/
            
            /*This below code is used to read data from db table if found and return in array*/
            
            else{
                if($id!=null){
                    $this->request->data = $this->Link->read(null,$id);
                }else{
                    $this->request->data = array();
                }
            }
            
            
            $redirect_url=(Controller::referer()=="/")? Router::url('/admin/links/index') :Controller::referer();
            
            $this->set('menu_id',$menu_id);
            $this->set('url',$redirect_url);
            
	    if(isset($this->request->data['Link']['id']) && $this->request->data['Link']['id']){
		    $this->heading = "Update Link";		    
	    }else{
		    $this->heading = "Add Link";
	    }
           
        }
        /*This below function code is used to form input data validation*/
		function validation($action=null){
			//print_r($this->request->data);die;
			if($action=='CustomLink'){
				$this->Link->setValidation('CustomLink');
			}
			$this->Link->set($this->request->data);
			$result = array();
			if ($this->Link->validates()) {
				$result['error'] = 0;
			}else{
				$result['error'] = 1;
			}
		  
		 
			if($this->request->is('ajax')) {
				$this->autoRender = false;
			 
				$result['errors'] = $this->Link->validationErrors;
				$errors = array();
			 
				foreach($result['errors'] as $field => $data){
					$errors['Link'.Inflector::camelize($field)] = array_pop($data);
				}
			 
				$result['errors'] = $errors;
			 
			  echo json_encode($result);
			  //<span class="error-message">Inserisci il nome del proprietario</span>
			  return;
		  }
		  //print_r($result);die; 
		  
		   return $result['error'];
		}
		/**This below function is used to view single record in page this search record by using given id**/
		function admin_view($id = null) {
		$this->layout = '';
		$criteria = array();
        $criteria['conditions'] = array('Link.id'=>$id);
        $link =  $this->Link->find('first', $criteria);
        $this->set('link', $link);
         
      }
		/*End of viewing of record function*/
		function ajax_sort(){
            $this->autoRender = false;
            foreach($_POST['sort'] as $order => $id){
                $link= array();
                $link['Link']['id'] = $id;
                $link['Link']['reorder'] = $order;
                $this->Link->create();
                $this->Link->save($link);
            }
           
        }
		/*This below function is used to delete record from db table by id provided by user*/
		function admin_delete($id=null){
		$this->autoRender = false;
		$data=$this->request->data['Link']['id'];
		$action = $this->request->data['Link']['action'];
		$ans="0";
		foreach($data as $value){
			if($value!='0'){
					if($action=='Delete'){
					$this->Link->delete($value);
					$ans="2";
				}
			}
		}
		if($ans=="2"){
		$this->Session->setFlash(__('Link has been '.$this->data['Link']['action'].'d successfully', true));
		}else{
		$this->Session->setFlash(__('Please Select any Link', true),'default','','error');
		}
		$this->redirect($this->request->data['Link']['redirect']);
				
		}
		/*End of function*/

	}
?>
