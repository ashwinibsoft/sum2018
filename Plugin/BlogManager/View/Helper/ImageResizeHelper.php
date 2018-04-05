<?php 
App::uses('Folder', 'Utility');
App::uses('AppHelper', 'View/Helper');
class ImageResizeHelper extends AppHelper {
	public $image;
	public $image_types;
	public $filter = 0;
	
	public $base_image = array();
	public $target_image = array();
	public $no_image = "";
	
	
	private function _setVar($options = array()){
		/* load setting data by cache*/
		$settings = Cache::read('site');
		$options['path'] = $options['source_path']; 
		
		/* Set no image data*/
		if(empty($options['noimg'])){
			$this->no_image = Configure::read('Path.no_image');
		}else{
			$this->no_image = $options['noimg'];
		}
		
		
		/*Set base image data*/
		
		$this->base_image['path'] = $options['path'].DS;
		if(!empty($options['img_name'])){
			$this->base_image['name'] = $options['img_name'];
		}else{
			//echo $this->no_image;
			$breaks = explode(DS,$this->no_image);
			//pr($breaks);
			$this->base_image['name'] = array_pop($breaks);
		}
		if(!file_exists($this->base_image['path'].$options['img_name']) || empty($options['img_name'])){ 
				$this->image_types = getimagesize($this->no_image);
				
				//$breaks = explode('/',$this->no_image);
				//$this->base_image['name'] = array_pop($breaks);
				//$options['path'] = implode(DS,$breaks).DS;
				
		}else{
			$this->image_types = getimagesize($this->base_image['path'].$this->base_image['name']);
		}
		$this->base_image['width'] = $this->image_types[0];
		$this->base_image['height'] = $this->image_types[1];
		
		/*Set target image data*/
		$this->target_image['path'] = $options['path'].DS.'temp'.DS;
		$this->target_image['width'] = $options['width'];
		$this->target_image['height'] = $options['height'];
		$this->target_image['name'] = self::_getThumbName();
		
		//pr($options);
	}
	
	private function _getThumbName(){
		$slice_image_name = explode('.',$this->base_image['name']);
		$ext = array_pop($slice_image_name);
		$thumb_name = implode('.',$slice_image_name).'_'.$this->target_image['width'].'_'.$this->target_image['height'].'.'.$ext;
		return $thumb_name;
	}
	
	
	
	
	
	private function _getThumbPath(){
		$breaks = explode(DS,$this->target_image['path']);
		foreach($breaks as $break){
			array_shift($breaks);
			if($break=="img"){
				break;
			}
		}
		return implode(DS,$breaks).$this->target_image['name'];
	}
	private function _loadFile(){
		$filename = $this->base_image['path'].$this->base_image['name'];
		if(!file_exists($filename)){
			$filename = $this->no_image;
		}
		
		if( $this->image_types[2] == IMAGETYPE_JPEG ) {
			$this->image = imagecreatefromjpeg($filename);
			$exif = exif_read_data($filename);
			if(!empty($exif['Orientation'])) {
				switch($exif['Orientation']) {
				case 8:
					$this->image = imagerotate($this->image,90,0);
					break;
				case 3:
					$this->image = imagerotate($this->image,180,0);
					break;
				case 6:
					$this->image = imagerotate($this->image,-90,0);
					break;
				} 
			}
		} elseif( $this->image_types[2] == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif($filename);
		} elseif( $this->image_types[2] == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng($filename);
		}
		
		
		
		if(!empty($exif['Orientation'])) {
			switch($exif['Orientation']) {
			case 8:
				$this->image = imagerotate($this->image,90,0);
				break;
			case 3:
				$this->image = imagerotate($this->image,180,0);
				break;
			case 6:
				$this->image = imagerotate($this->image,-90,0);
				break;
			} 
		}
		
		
	}
	
	private function _resize(){
		#Figure out the dimensions of the image and the dimensions of the desired thumbnail
		$src_w = imagesx($this->image);
		$src_h = imagesy($this->image);
		
		$tn_w = $this->target_image['width'];
		$tn_h = $this->target_image['height'];
		
		if($src_w > $tn_w && $src_h > $tn_h){
			//$ratio = $tn_h/$src_h;
			//$tn_w = $tn_w * $ratio;
		}

		#Do some math to figure out which way we'll need to crop the image
		#to get it proportional to the new size, then crop or adjust as needed
		do{
			$x_ratio = $tn_w / $src_w;
			$y_ratio = $tn_h / $src_h;
			$calculate = 0;
			
			if (($src_w <= $tn_w) && ($src_h <= $tn_h)) {
				$new_w = $src_w;
				$new_h = $src_h;
			} elseif (($x_ratio * $src_h) < $tn_h) {
				
				$new_h = ceil($x_ratio * ($src_h));
				if(($tn_h-$new_h) > 0){
					$tn_w +=1;
					$calculate = 1;
				}
				$new_w = $tn_w;
				
				
			} else {
				
				$new_w = ceil($y_ratio * $src_w);
				if(($tn_w-$new_w) > 0){
					$tn_h +=1;
					$calculate = 1;
				}

				$new_h = $tn_h;
			}
		}while ($calculate > 0);
		
		
		$tn_w = $this->target_image['width'];
		$tn_h = $this->target_image['height'];
		
		
		$newpic = imagecreatetruecolor(round($new_w), round($new_h));
		
		if( $this->image_types[2] == IMAGETYPE_PNG || $this->image_types[2] == IMAGETYPE_GIF ) {
			imagealphablending($newpic, false);
			imagesavealpha($newpic, true);
			$backgroundColor = imagecolorallocatealpha($newpic,  255,  255,  255, 127);
			imagefilledrectangle($newpic, 0, 0, $tn_w, $tn_h, $backgroundColor);
			imagecolortransparent($newpic, $backgroundColor);
		}
		imagecopyresampled($newpic, $this->image, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);
		$final = imagecreatetruecolor($tn_w, $tn_h);
		$backgroundColor = imagecolorallocate($final, 255, 255, 255);
		//imagefilledrectangle($final, 0, 0, $tn_w, $tn_h, $backgroundColor);
		
		
		
		//$backgroundColor = imagecolorallocate($final, 255, 255, 255);
		imagefill($final, 0, 0, $backgroundColor);
		
		//imagecopyresampled($final, $newpic, 0, 0, ($x_mid - ($tn_w / 2)), ($y_mid - ($tn_h / 2)), $tn_w, $tn_h, $tn_w, $tn_h);
		imagecopy($final, $newpic, (($tn_w - $new_w)/ 2), (($tn_h - $new_h) / 2), 0, 0, $new_w, $new_h);
		imagejpeg($final, $this->target_image['path'].$this->target_image['name'], 100);
		//echo $newpic;
		imagedestroy($final);
		
		
		
	}
	
	public function ResizeImage($imgArr = array()) {
		self::_setVar($imgArr);
		if(file_exists($this->target_image['path'].$this->target_image['name'])){
			return self:: _getThumbPath();
		}
		
		if(!file_exists($this->target_image['path'])) {
			mkdir($this->target_image['path'], 0777);
			$dir = new Folder();
			$dir->chmod($this->target_image['path'], 0777, true, array());	
		}
		self::_loadFile();
		self::_resize();
		return self:: _getThumbPath();
	}
	
	public function deleteThumbImage($imgArr = array()){
		self::_setVar($imgArr);
		if(file_exists($this->target_image['path'].$this->target_image['name'])){
			@unlink($this->target_image['path'].$this->target_image['name']);
		}
	}
}
?>
