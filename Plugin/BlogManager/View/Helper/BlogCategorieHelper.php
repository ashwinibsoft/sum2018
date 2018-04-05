<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Helper
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppHelper', 'View/Helper');
App::uses('FormHelper', 'View/Helper');

/**
 * Page helper library.
 *
 * Custom Helper of ContentManager for Page.
 *
 * @package       Cake.View.Helper
 * @property      HtmlHelper $Html
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html
 */
class BlogCategorieHelper extends AppHelper {
	var $helpers = array('Form');

	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		
	}
	
	public function category_select_mutlilevel($name, $options = array(),$pages = array(),$val = ''){
		$select_element = $this->Form->input($name,$options);
		//$select_split = htmlentities($select_element); 
		$start_tag= str_replace("</select>","",$select_element); 
		$options = self::__arrange_page($pages,$val,0,true);
		$end_tag = "</select>";
		$data = $start_tag.$options.$end_tag;
		//print_r($data);die;
		return $data;
	}
	
	private function __arrange_page($pages,$val,$level=0,$flush=false){
		static $data = "";
		
		if($flush){
			$data = "";
		}
		
		if(!empty($pages)){
			foreach($pages as $_page){
				$gap = "";
				for($i=0;$i<$level;$i++){
					$gap.= "&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				$selected = '';
				if($_page['BlogCategorie']['id']==$val){
					$selected = 'selected="selected"';
				}
				
				
				$data .= '<option value="'.$_page['BlogCategorie']['id'].'" class="level-'.$level.'" '.$selected.' >'.$gap.$_page['BlogCategorie']['cat_name'].'</option>';
				if(!empty($_page['children'])){
					self::__arrange_page($_page['children'],$val,($level+1));
				}
			}
		}
		return $data;
	}
	function category_checkbox_multilevel($name,$options = array(),$pages = array(),$val = array(),$level=0){
		static $data = "";
		if(!empty($pages)){
			foreach($pages as $_page){
				$data .='<div class="row-fluid">';
				$data .= '<div class="checkbox check-primary checkbox-circle">';
				$gap = "";
				$options['checked'] = '';
				for($i=0;$i<$level;$i++){
					$gap.= "&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				$selected = '';
				if(in_array($_page['BlogCategorie']['id'],$val)){
					$options['checked'] = 'checked';
				}
				$options['id'] = Inflector::camelize('BlogCategorie'.$name).$_page['BlogCategorie']['id'].$level;
				$options['value'] = $_page['BlogCategorie']['id'];
				$data .= $gap.$this->Form->checkbox($name, $options);
				$data .= '<label for="'.$options['id'].'">'.$_page['BlogCategorie']['cat_name'].'</label>';
				$data .='</div>';
				$data .='</div>';
				if(!empty($_page['children'])){
					$this->page_checkbox_multilevel($name,$options,$_page['children'],$val,($level+1));
				}
			}
		}
		return $data;
	}
	
	function admin_div_with_expand_option($class=""){
		if(!Configure::read('Settings.expand')){
			return sprintf("<div class='%s' style='display:none;'>",$class);
		}else{
			return sprintf("<div class='%s'>",$class);
		
		}
	}
	function check_setting_expand_option(){
		if(Configure::read('Settings.expand')){
			return true;
		}else{
			return false;
		}
	}
	


}
