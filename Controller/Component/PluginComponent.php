<?php
App::uses('Component', 'Controller');
class PluginComponent extends Component{
	protected $plugins = array();
	public function initialize(Controller $controller) {
	}
	public function load_plugins(){
		$plugins =  Cache::read('plugins');
		if(empty($plugins)){
			App::import('Model', 'Plugin');
			$Plugin = new Plugin();
			$plugins = $Plugin->find('all',array('conditions'=>array('Plugin.status'=>1)));
			$modules = array();
			Cache::write('plugins',$plugins);
		}
		
		$this->plugins = $plugins;
	}
	public function get_plugins(){return $this->plugins;}
	
	public function get_plugin_configs($key = '',$plugin = ''){
		$plugins = $this->plugins;
		$data = array();
		
		if(empty($key) || empty($plugins)){
			return $data;
		}
		
		
		foreach($plugins as $_plugin){
			$path = App::path('Plugin');
			$path = array_pop($path);
		//	echo $path = App::pluginPath($_plugin['Plugin']['title']);
			if(empty($_plugin['Plugin']['title'])){
				continue;
			}
			Configure::load($_plugin['Plugin']['title'].'.config','default',false);
			if(Configure::check($key)){
				
				//$data[] = Configure::read($key);
				foreach(Configure::read($key) as $_shortcode){
					array_push($data,$_shortcode);
				}
				//$modules['plug_id'] = $_plugin['Plugin']['id'];
			}
			Configure::delete($key);
		}
		//print_r($data);die;
		return $data;
	} 
	
	private function __load_plugin_component($controller){
		$plugins = $this->plugins;
		foreach($plugins as $_plugin){
			$plugin_path= App::pluginPath($_plugin['Plugin']['title']);
			if(!file_exists($plugin_path.DS.'Controller'.DS.'Component'.DS.$_plugin['Plugin']['title'].'Component.php')){
				continue;
			}
			$test = $controller->Components->load($_plugin['Plugin']['title'].'.'.$_plugin['Plugin']['title']);
		}
	}
	public function load_plugin_component($controller){
		self::__load_plugin_component($controller);
	}
	//public function load_plugin
	
	
	
	
	
	
}


?>
