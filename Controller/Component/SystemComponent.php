<?php
App::uses('Component', 'Controller');
class SystemComponent extends Component{
	private $seo = array(
			'site_title'=>'',
			'separator'=>' | ',
			'site_metakeyword'=>'',
			'site_metadescription'=>'',
			'google_analytics_code'=>''
			);
	private $social = array(
			'facebook'=>'',
			'twitter'=>'',
			'linkedin'=>'',
			'google'=>'',
			'p_interest'=>'',
			'tumblr'=>'',
			'instagram'=>''
			);
	private $facebook = array(
				'og:title'=>'',
				'og:sitname'=>'',
				'og:url'=>'',
				'og:description'=>'',
				'og:image'=>'',
				'fb:app_id'=>'',
				'og:type'=>'',
				'og:locale'=>'',
				'og:locale:alternate'=>array(),
				);
	private $data = array(
				'module'=>null,
				'id'=>null,
				'result'=>array(),
				'banner_image'=>null
			);
	private $shortcode = array();
	private $shortcode_description = array();
	public static $script_for_layout = array('head'=>array(),'foot'=>array());
	public static $css_for_layout = array('head'=>array(),'foot'=>array());
	public static $scriptBlocks = array('head'=>array(),'foot'=>array());
	public static $cssBlocks = array('head'=>array(),'foot'=>array());
	public static $header_modules = array();
	public static $footer_modules = array();
	private $_theme = "";
	var  $settings = array();
	var $components = array('Upload','Email','Image','Plugin');
	private function __set_setting(){
		$settings = Cache::read('site');
		//$settings = array();
		if(empty($settings)){
			$Settings = ClassRegistry::init('Setting');
			//App::uses('Model', 'Setting');
			//$Settings = new Settings();
			$results = $Settings->find('all',array('fields'=>array('Setting.key','Setting.values','Setting.module')));
			$settings = array();
			foreach($results as $result){
				$settings[$result['Setting']['module']][$result['Setting']['key']] = $result['Setting']['values'];
			}
			Cache::write('site', $settings);
		}
		$this->seo= $settings['seo'];
		$this->seo['google_analytics_code'] = $settings['goancode']['site_google_analytic_code'];
		$this->social = $settings['social'];
		$this->settings = $settings;
	}
	private function __setTheme($controller) {
		//pr($controller);
		if($controller->params['admin'] || $controller->params['controller']=="admin"){
			$controller->layout="admin";
			if($controller->name=="CakeError"){
				$controller->layout="admin2";
			}
			
			$this->set_seo('title_for_layout',$this->seo['site_title']);
			//$this->title_for_layout = $this->setting['site']['site_name'];
		}else{
			$controller->theme = $this->_theme=$this->settings['site']['theme'];
			if($this->settings['site']['site_maintenance_status']==1 && !$controller->request->is('ajax')){
				$preview_query = $controller->request->query('preview');
				if(!empty($preview_query) && (int)$controller->Auth->user('id')){
					return;
				}
				
				$controller->layout="maintenance";
				//throw new InternalErrorException('405 Error - Page not found',503);
				//throw new NotFoundException('404 Error - Page not found');
				$this->set_seo('site_title',$this->settings['site']['site_name'].'- Website is currently under maintenance');
				throw new MaintenanceException('404 Error - Page not found');
			}
		}
	}
	public function get_theme(){
		return $this->settings['site']['theme'];
	}
	
	public function __construct(ComponentCollection $collection, $settings = array()){
		parent::__construct($collection);
	}
	public function startup(Controller $controller){
		parent::startup($controller);
	}
	public function initialize(Controller $controller){
		self::__set_setting();
		self::__setTheme($controller);
		self::__check_maintenance_option();
		$this->Plugin->load_plugins();
		$this->Plugin->load_plugin_component($controller);
		self::__load_short_description();
		self::__load_settings_shortcode();
		self::__load_route_shortcode();
		parent::initialize($controller);
	}
	private function __check_maintenance_option(){
		
	}
	public function is_admin($controller){
		if(empty($controller)){
			return false;
		}
		if($controller->params['admin'] || $controller->params['controller']=="admin"){
			return true;
		}
		return false;
	}
	
	private function __load_settings_shortcode(){
		foreach($this->settings as $mod => $module){
			foreach($module  as $key =>$value){
				$code = "{setting-".$mod."-".$key."}";
				$options = self::__get_config_value($mod,$key);
				$this->add_shortcode($code,$value);
				
				if(!empty($options)){
					$options['code'] = $code;
					$options['value'] = $value;
					self::__update_shortcode_description($options);
				}
			}
		}
	}
	private function __load_route_shortcode(){
		$rewrite_urls = Cache::read('routes');
		if(empty($rewrite_urls)){
			$Route = ClassRegistry::init('Route');
			//App::uses('Model', 'Setting');
			//$Settings = new Settings();
			$rewrite_urls = $Route->find('all');
			Cache::write('routes', $rewrite_urls);
		}
		
		foreach($rewrite_urls as $_route){
			$url = json_decode($_route['Route']['values'],true);
			if(empty($url['controller']) || empty($url['action'])){
				continue;
			}
			$code = '{route-'.$_route['Route']['object'].'-'.$_route['Route']['object_id'].'}';
			
			$value = Router::url(array('plugin'=>(empty($url['plugin']))?false:$url['plugin'],
						'controller'=>$url['controller'],
						'action'=>$url['action'],
						$url['id']
						)
					);
			$this->add_shortcode($code,$value);
			$options = array(
				'code'=>$code,
				'value'=>$value,
				'key'=>'',
				'mode'=>$_route['Route']['object'],
				'group'=>'Urls',
				'name'=>$_route['Route']['object_name']
				);
			self::__add_shortcode_description($options);
		}
	}
	
	public function set_seo($key='',$value='',$append = false){
		if(in_array($key,array_keys($this->seo))){
			if($append){
				$this->seo[$key] .= $this->seo['separator']. $value;
			}else{
				$this->seo[$key] = $value;
			}
		}
	}
	
	public function add_js($js = array(),$position = 'head'){
		if(empty($position)){
			$position = 'head';
		}
		if(!empty($js)){
			if(is_array($js)){
				foreach($js as $_js){
					array_push(self::$script_for_layout[$position],$_js);
				}
			}
			if(is_string($js)){
					array_push(self::$script_for_layout[$position],$js);
			}
		}
	}
	public function add_css($css = array(),$position = 'head'){
		if(empty($position)){
			$position = 'head';
		}
		if(!empty($css)){
			if(is_array($css)){
				foreach($css as $_css){
					array_push(self::$css_for_layout[$position],$_css);
				}
			}
			if(is_string($css)){
					array_push(self::$css_for_layout[$position],$css);
			}
		}
	}
	public function add_script_blocks($scripts = array(),$position = 'head'){
		if(empty($position)){
			$position = 'head';
		}
		if(!empty($scripts)){
			if(is_array($scripts)){
				foreach($scripts as $_scripts){
					self::$scriptBlocks[$position][] = $_scripts;
				}
			}
			if(is_string($scripts)){
				self::$scriptBlocks[$position][] = $scripts;
			}
		}
	}
	public function add_css_blocks($css = array(),$position = 'head'){
		if(empty($position)){
			$position = 'head';
		}
		if(!empty($css)){
			if(is_array($css)){
				foreach($css as $_css){
					self::$cssBlocks[$position][] = $_css;
				}
			}
			if(is_string($css)){
				self::$cssBlocks[$position][] = $css;
			}
		}
	}
	public function get_seo($key=''){
		$value ='';
		if(in_array($key,array_keys($this->seo))){
			$value = $this->seo[$key];
		}
		return $value;
	}
	public function get_setting($module='',$key=''){
		$settings = $this->settings;
		if(empty($settings[$module][$key])){
			return null;
		}
		
		return $settings[$module][$key];
	}
	public function set_setting($module='',$key='',$value=''){
		$this->settings[$module][$key] = $value;
	}
	public function set_data($key='',$value){
		$this->data[$key] = $value;
	}
	public function add_shortcode($code="",$value=""){
		$this->shortcode[$code] = $value;
	}
	public function get_shortcode(){
		return $this->shortcode;
	}
	public function beforeRender(Controller $controller){
		$controller->helpers['System'] = array_merge(array('seo'=>$this->seo),array('social'=>$this->social),array('facebook'=>$this->facebook),array('data'=>$this->data),array('settings'=>$this->settings));
		$controller->helpers['ShortCode'] = $this->shortcode;
		$controller->set('G_shortcode_description',$this->shortcode_description);
	}
	public function get_shortcode_description(){
		return $this->shortcode_description;
	}
	public function get_themes(){
		/*Load Theme Start*/
		$view = App::path('View');
		$Folder = new Folder($view[0].DS.'Themed');
		list($themes,$files) = $Folder->read(); 
		/*Load Theme End*/
		$themes = array_combine($themes, $themes);
		return $themes;
		
	}
	private function __load_short_description(){
		Configure::load('config','default',false);
		$this->shortcode_description = Configure::read('ShortCode'); 
		$shortcodes = $this->Plugin->get_plugin_configs('ShortCode');
		foreach($shortcodes as $_shortcode){
			array_push($this->shortcode_description,$_shortcode);
		}
		$rewrite_urls = Cache::read('routes');
		//pr($rewrite_urls);
	}
	private function __get_config_value($mode = "", $key = ""){
		$data = array();
		foreach($this->shortcode_description as $_shortcode_d){
			if($_shortcode_d['mode']==$mode && $_shortcode_d['key']==$key){
				$data = $_shortcode_d;
				break;
			}
		}
		return $data;
	}
	private function __add_shortcode_description($options = array()){
		$this->shortcode_description[] = $options;
	}
	private function __update_shortcode_description($options = array()){
		$_d = array();
		foreach($this->shortcode_description as $_shortcode_d){
			if($_shortcode_d['mode']==$options['mode'] && $_shortcode_d['key']==$options['key']){
				$_shortcode_d = $options;
			}
			$_d[] = $_shortcode_d;
		}
		$this->shortcode_description = $_d;
	}
	
	
	public function excreat($string = ""){
		$pattern = "/href=((\"|'))[^\"']+(?=(\"|'))/";
		//$pattern = "/(?<=href=(\"|'))[^\"']+(?=(\"|'))/";
		$newurl="Javascript:void(0); re";
		$newstring = preg_replace($pattern,$newurl,$string);
		return $newstring;
	}
	
	
}
?>
