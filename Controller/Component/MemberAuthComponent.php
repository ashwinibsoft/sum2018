<?php
App::uses('Component', 'Controller');
App::uses('Router', 'Routing');
App::uses('Security', 'Utility');
App::uses('Debugger', 'Utility');
App::uses('CakeSession', 'Model/Datasource');

class MemberAuthComponent extends Component{
	public $members = array() , $loginAction = array() , $loginRedirect = array(), $logoutRedirect = array() , $userScope = array() ,  $messages = array('direct_access'=>'','auth_fail'=>'');
	public $id = null;
	private $active_member = array();
	//public $model_use = 'MemberManager.Member';
	public $fields = array('email_id','password');
	public $components = array('Session', 'RequestHandler','Auth','Cookie');
	public  $name = 'MemberAuth';
	public static $sessionKey = 'MemberAuth';
	public $results = array();
	public $params = array() , $redirect = array();
	public $request;
	public $response;
	public $controller;
	public $deny_action = array();
	
	public function startup(Controller $controller){
		
		$this->request = $controller->request;
		$this->response = $controller->response;
		$this->controller = $controller;
		
		//$user = $this->Cookie->read('keep_me_login');
		if(!empty($user)) {
			//self::check_user();
		}
		self::__load_member();
		//self::_setvar();
		//self::_load();
	}
	private function __load_member(){
		$this->active_member = $this->Session->read(self::$sessionKey);
	}
	
	/*public function login(){
	
		$conditions = array();
		$model_name = self::_ext_model($this->model_use);
		$conditions[$model_name.'.'.$this->fields[0]] = $this->request->data[$model_name][$this->fields[0]];
		$conditions[$model_name.'.'.$this->fields[1]] = Security::hash(Configure::read('Security.salt').$this->request->data[$model_name][$this->fields[1]]);
		
		if(empty($conditions)){
			return false;
		}
		
		foreach($this->userScope as $field=>$value){
			$conditions[$field] = $value;
		}
		
		$Model = ClassRegistry::init($this->model_use);
		$criteria = array();
		$criteria['fields'] = array('*');
		$criteria['conditions'] = $conditions;
		 
		$results = $Model->find('first',$criteria);
		if(!empty($results))
		{
			$this->updateMemberSession($results[$model_name]);
			
			// redirect after login	
			$this->controller->redirect($this->loginRedirect);
		}else{
			// for de message
			unset($criteria['conditions']['Member.status']);
			$member_status=$Model->find('count',$criteria);
			if($member_status==1){
				$this->Session->setFlash($this->messages['auth_deactivate'],'default','','error');
			}else{
				$this->Session->setFlash($this->messages['auth_fail'],'default','','error');
			}
			$refer_url=$this->loginRedirect;
			$this->controller->redirect($this->loginAction);
		}
	}*/
	
	/*public function check_user()
	{
		$conditions = array();
		$model_name = self::_ext_model($this->model_use);
		$user = $this->Cookie->read('keep_me_login');
		$email = $user['email_id'];
		$pass = $user['pass'];		
		
		$conditions[$model_name.'.'.$this->fields[0]] = $email;
		$conditions[$model_name.'.'.$this->fields[1]] = Security::hash(Configure::read('Security.salt').$pass);
		if(empty($conditions)){
			return false;
		}
		
		
		
		foreach($this->userScope as $field=>$value){
			$conditions[$field] = $value;
		}
		
		$Model = ClassRegistry::init($this->model_use);
		
		$criteria = array();
		$criteria['fields'] = array('*');
		$criteria['conditions'] = $conditions;
		
		$results = $Model->find('first',$criteria);
		if(!empty($results)){
			//$this->Session->write(self::$sessionKey,$results);
			$this->updateMemberSession($results[$model_name]);
		}
	}
	
	private function _load(){
		
		$model = self::_ext_model($this->model_use);
		
		$this->results = $this->Session->read(self::$sessionKey);
		if(!empty($this->results[$this->name]))
		{
			$this->id = $this->results[$this->name]['id'];
			$this->Session->delete('MemberAuth.redirect');
			$this->Session->delete('redirect_url');
		}
		else
		{
			if(in_array($this->params['action'],$this->deny_action)){
				if(!empty($this->loginAction)){
					
					$this->Session->setFlash($this->messages['direct_access'],'default','','error');
					$this->Session->write('MemberAuth.redirect',$this->redirect);
					$this->controller->redirect($this->loginAction);
				}else{
					$this->Session->delete('MemberAuth.redirect');
				}
				
			}
		}
		//print_r($this->members);
	}
	private function _ext_model($model=null){
		$data = explode('.',$model);
		return array_pop($data);
	}
	private function _setvar(){
		$this->params = $this->controller->request->params;
		$this->redirect = array('controller'=>$this->params['controller'],'action'=>$this->params['action'],'pass'=>$this->params['pass']);
	}
	public function id(){
		return $this->id;
	}*/
	
	/*public function logout(){
		$this->Session->delete(self::$sessionKey);
		$this->Session->setFlash(__('You\'re now logged out', true));
		$this->controller->redirect($this->logoutRedirect);
	}*/
	
	//public function updateMemberSession($detail = array()){
	//	$this->Session->delete(self::$sessionKey);
	//	$this->Session->write(self::$sessionKey,$detail);
	//}
	
	public function updateMemberSession($detail = array()){
		//print_r($detail);die;
		$data[$this->name] = $detail;
		$memberType = $this->Session->read(self::$sessionKey.'.memberType');
		if(empty($memberType)){$memberType=$detail['user_type'];}		
		$this->removeMemberSession();		
		$this->Session->write(self::$sessionKey,$data);
		$this->Session->write(self::$sessionKey.'.memberType',$memberType);
	}
	
	public function removeMemberSession(){
		$this->Session->delete(self::$sessionKey);
	}
	
	
	/*public function getActiveField($field){
		 $member=$this->Session->read(self::$sessionKey);
		$model_name = self::_ext_model($this->model_use);
		return $member[$model_name][$field];
		
	}
	
	*/
	public function get_active_member_detail(){
		$member=$this->Session->read(self::$sessionKey);
		return $member;	
	}
	
	public function updateMemberType($memberType){
		$this->Session->write(self::$sessionKey.'.memberType',$memberType);
	}
	
	
	public function is_active_member(){
	$member=$this->Session->read(self::$sessionKey);
		if($member){
			return true;
		}elseif(empty($member)){
			return false;
		}
		//return true or false
	}
	
	/*public function get_owner_type(){
		
		return $this->name;
		//
	}
	public function is_owner(){
		$memberType = $this->Session->read(self::$sessionKey.'.memberType');
		if($memberType==1){
			return true;
		}
		return false;
	}
	public function is_employer(){
	}
	public function is_employee(){
		$memberType = $this->Session->read(self::$sessionKey.'.memberType');
		if($memberType==3){
			return true;
		}
		return false;
	}
	
	public function redirect(){
		//
	}
	
	
	public function get_owner_account_type(){
		
		return $this->Session->read(self::$sessionKey.'.'.$this->name.'.account_type');
		//$memberType = $this->Session->read(self::$sessionKey.'.'.$this->name.'.account_type');
		//print_r($memberType);die;
	}
	
	public function get_owner_id(){
		return $this->Session->read(self::$sessionKey.'.'.$this->name.'.client_id');
	}
	
	public function get_member_id(){
		$active_user_id = $this->Session->read(self::$sessionKey.'.'.$this->name.'.id');
		return $active_user_id;
	}*/
	
	public function beforeRender(Controller $controller){
		$controller->helpers['MemberAuth'] = array('active_user'=>$this->active_member,'name'=>$this->name);
		$controller->set('G_MEMBER_AUTH',$this->active_member);
	}
	
	function get_member_type(){
		//print_r($sessionKey);die;
		$memberType = $this->Session->read(self::$sessionKey.'.memberType');
		return $memberType;
	}
		
		/*function is_active_employer($employee_info=array()){
		if(!empty($employee_info)){
			$Employer = ClassRegistry::init('AccountManager.Employer');
			$Client = ClassRegistry::init('AccountManager.Client');
			$clientinfo = $Client->find('first',array('conditions'=>array('Client.id'=>$employee_info['Employee']['client_id']),'fields'=>array('Client.status')));
			$employerinfo = $Employer->find('first',array('conditions'=>array('Employer.id'=>$employee_info['Employee']['employer_id']),'fields'=>array('Employer.status')));
			if(($clientinfo['Client']['status']==1 || $clientinfo['Client']['status']==3) && $employerinfo['Employer']['status']==1){
				return true;
			} else {
				return false;
			}
		}
	}*/
	
	/*function is_active_user($user_info=array()){
		if(!empty($user_info)){
			$Client = ClassRegistry::init('AccountManager.Client');
			$clientinfo = $Client->find('first',array('conditions'=>array('Client.id'=>$employer_info['Employer']['client_id']),'fields'=>array('Client.status')));
			if($clientinfo['Client']['status']==1 || $clientinfo['Client']['status']==3){
				return true;
			} else {
				return false;
			}
		}
	}*/
	
	/*function get_employer_detail($employerid=null){
		$member_type = $this->get_member_type();
		$employerinfo = array();
		$Employer = ClassRegistry::init('AccountManager.Employer');
		if($member_type==3){
			$employer_id = $this->Session->read(self::$sessionKey.'.'.$this->name.'.employer_id');
			$employerinfo = $Employer->find('first',array('conditions'=>array('Employer.id'=>$employer_id),'fields'=>array('Employer.id','Employer.username','Employer.email_address','client_id','company_name','contact_person')));
		}
		if($member_type==2){
			$employer_id = $this->Session->read(self::$sessionKey.'.'.$this->name.'.id');
			$employerinfo = $Employer->find('first',array('conditions'=>array('Employer.id'=>$employer_id),'fields'=>array('Employer.id','Employer.username','Employer.email_address','client_id','company_name','contact_person')));
		}
		if(!empty($employerid)){
			$employer_id = $employerid;
			$employerinfo = $Employer->find('first',array('conditions'=>array('Employer.id'=>$employer_id),'fields'=>array('Employer.id','Employer.username','Employer.email_address','client_id','company_name','contact_person')));
		}
		if($this->get_owner_account_type()==2 && empty($employerid)){
			$client_id = $this->Session->read(self::$sessionKey.'.'.$this->name.'.id');
			$employerinfo = $Employer->find('first',array('conditions'=>array('Employer.client_id'=>$client_id),'fields'=>array('Employer.id','Employer.username','Employer.email_address','Employer.client_id','company_name','Employer.contact_person')));
		}
		if(!empty($employerinfo)){
			return $employerinfo['Employer'];
		}
	}*/
	
	function get_user_detail($member_id=null){
		$memberinfo = array();
		//$Client = ClassRegistry::init('AccountManager.Client');
		$member_type = $this->get_member_type();
		
		if(!empty($member_id)){
			$c_id = $member_id;
		}else{
			//if($member_type==1){
				$c_id = $this->Session->read(self::$sessionKey.'.'.$this->name.'.id');
				$member_id = $c_id;
			/*}else if($member_type==2){
				$c_id = $this->Session->read(self::$sessionKey.'.'.$this->name.'.id');
				$member_id = $c_id;
			}*/
		}
		//print_r($member_id);die;
		if($member_type == 1){
			$Supplier = ClassRegistry::init('SupplierManager.Supplier');
			if(!empty($member_id)){
				$memberinfo = $Supplier->find('first',array('conditions'=>array('Supplier.id'=>$member_id)));
				if(!empty($memberinfo)){
					return $memberinfo['Supplier'];
				}
			}
		}elseif($member_type == 2){
			$NewBuyer = ClassRegistry::init('NewBuyerManager.NewBuyer');
			if(!empty($member_id)){
				$memberinfo = $NewBuyer->find('first',array('conditions'=>array('NewBuyer.id'=>$member_id)));
				if(!empty($memberinfo)){
					return $memberinfo['NewBuyer'];
				}
			}
		}
		/*if(!empty($client_id)){
			$cid = $client_id;
		} else {
			if($member_type==1){
				$c_id = $this->Session->read(self::$sessionKey.'.'.$this->name.'.id');
				$client_id = $c_id;
			}
			else if($member_type==2){
				$c_id = $this->Session->read(self::$sessionKey.'.'.$this->name.'.client_id');
				$client_id = $c_id;
			}
			else if($member_type==3){
				$c_id = $this->Session->read(self::$sessionKey.'.'.$this->name.'.client_id');
				$client_id = $c_id;
			}
		}
		$clientinfo = $Client->find('first',array('conditions'=>array('Client.id'=>$client_id)));
		if(!empty($clientinfo)){
			return $clientinfo['Client'];
		}*/
	}
	
	/*public function is_accountant(){
		$is_accountant = $this->Session->read(self::$sessionKey.'.'.$this->name.'.is_accountant');
		if($is_accountant==1){
			return true;
		} else {
			return false;
		}
	}
	
	function accountant_emp_employer_id(){
		$member_type = $this->get_member_type();
		if($member_type==3 && $this->is_accountant()){
			$employerid = $this->Session->read(self::$sessionKey.'.'.$this->name.'.employer_id');
			return $employerid;
		} else {
			return null;
		}
	}
	function employee_employer_id(){
		$member_type = $this->get_member_type();
		if($member_type==3){
			$employerid = $this->Session->read(self::$sessionKey.'.'.$this->name.'.employer_id');
			return $employerid;
		}
	}
	
	
	
	
	function check_employee_status(){
		$memberType = $this->get_member_type();
		if($memberType==3){
			$status = $this->Session->read(self::$sessionKey.'.'.$this->name.'.status'); 
			if($status==0){
				return 2;
			} else {
				return 1;
			}
		}
		return 1;
	}
	
	function check_employer_status(){
		$memberType = $this->get_member_type();
		if($memberType==2){
			$status = $this->Session->read(self::$sessionKey.'.'.$this->name.'.status'); 
			if($status==0){
				return 2;
			} else {
				return 1;
			}
		}
		return 1;
	}
	function account_owner_status(){
		$status = $this->Session->read(self::$sessionKey.'.'.$this->name.'.status');
		return $status;
	}
	*/
	
}
?>
