<?php
App::uses('Component', 'Controller');
App::uses('Multibyte', 'I18n');
App::uses('CakeEmail', 'Network/Email');
class MyMailComponent extends Component {
	public $components = array('System');
	public $layout = 'default';
	public $template = 'default';
	public $sendAs = 'html';
	public $smtpOptions =array();
	protected $shortcodes = array();
	protected $mail_data = array(), $to = array(), $from = array(), $subject = "", $body = "", $attachments = array(),$cc = array(), $replyTo = null, $heading ='';
	
	
	public function startup(Controller $controller){
		self::__load_shortcodes();
	}
	private function __load_shortcodes(){
		$this->shortcodes = $this->System->get_shortcode();
	}
	
	private function __merge_shortcode($_shortcode = array()){
		$this->shortcodes =  array_merge($this->shortcodes,$_shortcode);
	}
	private function __replace($content = ''){
		foreach($this->shortcodes as $code => $value){
			$content =str_replace($code,$value,$content);
		}
		return $content;
	}
	
	private function __load_mail($mail_id){
		$mailModel = ClassRegistry::init('Mail');
		$this->mail_data=$mailModel->read(null,$mail_id);
		
		if(empty($this->mail_data)){
			return false;
		}
		
		$this->heading = self::__replace($this->mail_data['Mail']['heading']);
		$this->subject = self::__replace($this->mail_data['Mail']['mail_subject']);
		$this->body = self::__replace($this->mail_data['Mail']['mail_body']);
		$this->from = self::__from($this->mail_data['Mail']['mail_from']);
		
	}
	private function __from($_from = ''){
		/*
		if(empty($_from)){
			return false;
		}
		$_from_email = self::__replace($_from);
		$_from_name = $this->System->get_setting('site','site_name');
		
		if (!filter_var($_from_email, FILTER_VALIDATE_EMAIL)) {
			$_from_name = $_from_email;
			$_from_email = (string)$this->System->get_setting('site','site_contact_email');
		}
		*/
		$_from_name = $this->System->get_setting('site','site_name');
		$_from_email = (string)$this->System->get_setting('site','site_contact_email');
		return array($_from_email=>$_from_name);
	}
	
	
	private function __to($to=null){
		
		if(is_array($to)){
			foreach($to as $_to){
				$_to = self::__replace($_to);
				//if (filter_var($_to, FILTER_VALIDATE_EMAIL)) {
					$this->to[] = $_to;
				//}
			}
		}else{
			$to = self::__replace($to);
			//if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
				$this->to = $to;
			//}
		}
	}
	private function __attachments($files = array()){
		$this->attachments = $files;
	}
	
	public function heading($heading = ""){
		return $this->heading = self::__replace($heading);
	}
	public function subject($subject = ""){
		return $this->subject = self::__replace($subject);
	}
	public function body($body = ""){
		return $this->body = self::__replace($body);
	}
	public function from($_from = ''){
		return self::__from($_from);
	}
	
	
	public function SendMail($mail_id = null,$options = array(),$Mailcontent = array()) {
		
		/* set more short codes*/
		if(empty($options['replacement'])){
			$options['replacement'] = array();
		}
		self::__merge_shortcode($options['replacement']);
		
		
		/* fetch mail data from database*/
		if($mail_id!=null){
			self::__load_mail($mail_id);
		}
		
		/* Set from id*/
		if(!empty($options['from'])){
			$this->from = self::__from($options['from']);
		}
		
		/* Set replyTo id*/
		if(!empty($options['replyTo'])){
			self::__replyTo($options['replyTo']);
		}
		
		
		/* Set to id*/
		if(!empty($options['to'])){
			self::__to($options['to']);
		}
		
		/* Set to attachments*/
		if(!empty($options['attachments'])){
			self::__attachments($options['attachments']);
		}
		
		/* Set template*/
		if(!empty($options['template'])){
			$this->template = $options['template'];
		}
		
		/* Set Layout*/
		if(!empty($options['layout'])){
			$this->layout = $options['layout'];
		}
		
		/* Set emailFormat*/
		if(!empty($options['emailFormat'])){
			$this->sendAs = $options['emailFormat'];
		}
		
		
		
		
		$smtp_status = (int)$this->System->get_setting('smtp','smtp_status');
		$smtp = array();
		
		if($smtp_status==1){
			 $smtp = array(
				'transport' => 'Smtp',
				'from' => array($this->System->get_setting('smtp','smtp_email')=>$this->System->get_setting('site','site_name')),
				'host' => $this->System->get_setting('smtp','smtp_host'),
				'port' => $this->System->get_setting('smtp','smpt_port'),
				'timeout' => 30,
				'username' => $this->System->get_setting('smtp','smtp_email'),
				'password' => $this->System->get_setting('smtp','smtp_password'),
				'client' => null,
				'log' => false,
				//'charset' => 'utf-8',
				//'headerCharset' => 'utf-8',
			);
			$Email = new XEmail('smtp',$smtp);
		}else{
			$Email = new XEmail();
			$Email->from($this->from);
		}
		//echo $this->System->get_theme();
		$Email->theme($this->System->get_theme());
		
		
		
		$Email->to($this->to);
		$Email->subject($this->subject);
		$Email->emailFormat($this->sendAs);
		$Email->template($this->template);
		$Email->layout = $this->layout;
		
		if(empty($this->replyTo)){
			$this->replyTo = $this->System->get_setting('site','site_contact_email');
		}
		$Email->replyTo($this->replyTo);
		
		$Email->attachments($this->attachments);
		
		/*
		echo "<br />THeme:".$this->System->get_theme();
		echo "<br />From:".pr($this->from); 
		echo "<br />To:".$this->to;
		echo "<br />Subject:".$this->subject;
		echo "<br />Email Format:".$this->sendAs;
		echo "<br />Email Template:".$this->template;
		echo "<br />Email layout:".$this->layout;
		echo "<br />Email attachments:"; pr($this->attachments);
		die;
		*/
		/* SET Variable*/
		$mlogo= Configure::read('Site.url')."/img/site/".$this->System->get_setting('site','site_mail_logo');
		
		$vars = array('data'=>$this->body,'heading'=>$this->heading,'logo'=>$mlogo,'url'=>Configure::read('Site.url'),'site_name'=>$this->System->get_setting('site','site_name'));
		//$vars = array('data'=>'This is test','heading'=>'contact test','logo'=>Configure::read('Site.logo'),'url'=>Configure::read('Site.url'),'site_name'=>$this->System->get_setting('site','site_name'));
		if(!empty($options['viewVars'])){
			$vars = array_merge($vars,$options['viewVars']);
		}
		$Email->viewVars($vars);
		/* SET Variable*/
		
		if(!empty($options['message'])){
			$Email->message($options['message']);
		}
		//echo "<br /> vars:"; pr($vars);
		//echo "<br /> message:"; echo $options['message'];
		
		
		if($this->sendAs=="html"){
			$Email->send();
		}else{
			if(!empty($options['message'])){
				$Email->send($options['message']);
			}else{
				$Email->send($this->body);
			}
		}
		//die;
		$this->reset();
	}
	
	public function render(){
		self::__merge_shortcode(array());
		$Email = new XEmail();
		$Email->theme($this->System->get_theme());
		//$Email->from($this->from);
		//$Email->to($this->to);
		$Email->subject(self::__replace($this->subject));
		$Email->emailFormat($this->sendAs);
		$Email->template($this->template);
		$Email->layout = $this->layout;
		
		$vars = array('data'=>self::__replace($this->body),'heading'=>self::__replace($this->heading),'logo'=>Configure::read('Site.logo'),'url'=>Configure::read('Site.url'),'site_name'=>$this->System->get_setting('site','site_name'));
		$Email->viewVars($vars);
		
		return implode($Email->render($this->body),"");
	}
	
	public function reset() {
		$this->mail_data = array();
		$this->to = array();
		$this->from = array();
		$this->cc = array();
		$this->heading=null;
		$this->body = null;
		$this->subject = null;
		$this->replyTo = null;
		$this->attachments = array();
		$this->shortcodes = array();
		self::__load_shortcodes();
	}
	protected function _formatAddresses($addresses) {
		$formatted = array();
		foreach ($addresses as $address) {
			if (preg_match('/((.*))?\s?<(.+)>/', $address, $matches) && !empty($matches[2])) {
				$formatted[$this->_strip($matches[3])] = $matches[2];
			}else{
					$address = $this->_strip($address);
					$formatted[$address] = $address;
				}
			}
		return $formatted;
	}
	protected function _strip($value, $message = false) {
		$search = '%0a|%0d|Content-(?:Type|Transfer-Encoding)\:';
		$search .= '|charset\=|mime-version\:|multipart/mixed|(?:[^a-z]to|b?cc)\:.*';
		if ($message !== true) {
			$search .= '|\r|\n';
		}
		$search = '#(?:' . $search . ')#i';
		while (preg_match($search, $value)) {
			$value = preg_replace($search, '', $value);
		}
		return $value;
	}
	public function replyTo($email){
		self::__replyTo($email);
	}
	private function __replyTo($email){
		$this->replyTo = $email;
	}
	
}

Class XEmail extends CakeEmail {
	
	public $smtp = array();
	
	public function __construct($config = null,$smtp = array()) {
		$this->_appCharset = Configure::read('App.encoding');
		$this->smtp = $smtp;
		if ($this->_appCharset !== null) {
			$this->charset = $this->_appCharset;
		}
		$this->_domain = preg_replace('/\:\d+$/', '', env('HTTP_HOST'));
		if (empty($this->_domain)) {
			$this->_domain = php_uname('n');
		}

		if ($config) {
			$this->config($config);
		}
		if (empty($this->headerCharset)) {
			$this->headerCharset = $this->charset;
		}
	}
	public function config($config = null) {
		if ($config === null) {
			return $this->_config;
		}
		if (!is_array($config)) {
			$config = (string)$config;
		}

		$this->_applyConfig($config);
		return $this;
	}
	protected function _applyConfig($config) {
		if (is_string($config) && ($config!='smtp' && $config!='SMTP')) {
			if (!class_exists($this->_configClass) && !config('email')) {
				throw new ConfigureException(__d('cake_dev', '%s not found.', APP . 'Config' . DS . 'email.php'));
			}
			$configs = new $this->_configClass();
			if (!isset($configs->{$config})) {
				throw new ConfigureException(__d('cake_dev', 'Unknown email configuration "%s".', $config));
			}
			$config = $configs->{$config};
		}else{
			$config = $this->smtp;
			
		}
		$this->_config = $config + $this->_config;
		if (!empty($config['charset'])) {
			$this->charset = $config['charset'];
		}
		if (!empty($config['headerCharset'])) {
			$this->headerCharset = $config['headerCharset'];
		}
		if (empty($this->headerCharset)) {
			$this->headerCharset = $this->charset;
		}
		$simpleMethods = array(
			'from', 'sender', 'to', 'replyTo', 'readReceipt', 'returnPath', 'cc', 'bcc',
			'messageId', 'domain', 'subject', 'viewRender', 'viewVars', 'attachments',
			'transport', 'emailFormat', 'theme', 'helpers', 'emailPattern'
		);
		foreach ($simpleMethods as $method) {
			if (isset($config[$method])) {
				$this->$method($config[$method]);
				unset($config[$method]);
			}
		}
		if (isset($config['headers'])) {
			$this->setHeaders($config['headers']);
			unset($config['headers']);
		}

		if (array_key_exists('template', $config)) {
			$this->_template = $config['template'];
		}
		if (array_key_exists('layout', $config)) {
			$this->_layout = $config['layout'];
		}

		$this->transportClass()->config($config);

	}
	
	public function render($content = null){
		return parent::_render($this->_wrap($content));
	}

	
}
