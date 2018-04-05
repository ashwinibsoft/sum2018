<?php
App::uses('Component', 'Controller');
class ImageComponent extends Component {
	//public $components = array('System');
	public function __construct(ComponentCollection $collection, $settings = array()){
		parent::__construct($collection);
	}
	public function startup(Controller $controller) {
		parent::startup($controller);
	}
	public function upload($options = array()){
		$type = self::__get_image_type($options['image']['tmp_name']);
		if(!$type){
			return "";
		}
		$source = self::__create_image($options['image']['tmp_name'],$type);
		$src_w = imagesx($source);
		$src_h = imagesy($source);
		$image_info = self::__get_image_info($options['image']['tmp_name']);
		$quality =  self::__calculate_quality($options['image']['size'],$src_w,$src_h);
		
		$resizedImage = imagecreatetruecolor($src_w, $src_h);
		if( $image_info[2] == IMAGETYPE_PNG || $image_info[2] == IMAGETYPE_GIF  ) {
			imagealphablending($resizedImage, false);
			imagesavealpha($resizedImage, true);
			$backgroundColor = imagecolorallocatealpha($resizedImage,  255,  255,  255, 127);
			imagefilledrectangle($resizedImage, 0, 0, $src_w, $src_w, $backgroundColor);
			imagecolortransparent($resizedImage, $backgroundColor);
		}
		
		
		imagecopyresampled($resizedImage, $source, 0, 0, 0, 0, $src_w,$src_h, $src_w, $src_h);
		//header("Content-Type: image/jpeg");
		$this->mkdir($options['destination']);
		$file_name = self::__create_name($options['image']['name']);
		$destination = $options['destination'].$file_name;
		if( $image_info[2] == IMAGETYPE_JPEG ) {
			imagejpeg($resizedImage,$destination,$quality);
		}
		if( $image_info[2] == IMAGETYPE_GIF ) {
			imagegif($resizedImage,$destination);
		}
		if( $image_info[2] == IMAGETYPE_PNG ) {
			imagepng($resizedImage,$destination);
		}
		imagedestroy($resizedImage);
		@chmod($destination,0644);
		return $file_name;
	}
	private function __create_name($name){
		$slice_image_name = explode('.',$name);
		$ext = array_pop($slice_image_name);
		$thumb_name = implode('.',$slice_image_name).'_'.time().'_'.mt_rand(10000000, 99999999).'.'.$ext;
		return $thumb_name;
		
	}
	private function __calculate_quality($image_size,$src_w,$src_h){
		$quality = 100;
		$image_size = ceil($image_size/1024);
		if(($image_size >= 952) ){
			$quality = 98;
		}
		if(($image_size >= 1024 && $image_size < (1024*2))  || ($src_w > 1024) || ($src_h > 768)){ //greater  than 1 mb to 2 mb.
			$quality = 85 - floor($image_size/1024);
		}
		if(($image_size >= (1024*2) && $image_size < (1024*5))  || ($src_w > 1280) || ($src_h > 1024)){ //greater  than 1 mb to 2 mb.
			
			 $quality = 75 - floor($image_size/1024*2);
		}
		if(($image_size >= (1024*5) && $image_size < (1024*7))  || ($src_w > 1400) || ($src_h > 1050)){ //greater  than 5 mb to 10 mb.
			$quality = 65 - floor($image_size/(1024*5));
		}
		if(($image_size >= (1024*7) && $image_size < (1024*8))  || ($src_w > 1680) || ($src_h > 1050)){ //greater  than 5 mb to 10 mb.
			$quality = 55 - floor($image_size/(1024*7));
		}
		if(($image_size >= (1024*8) && $image_size < (1024*9))  || ($src_w > 1920) || ($src_h > 1080)){ //greater  than 5 mb to 10 mb.
			$quality = 45 - floor($image_size/(1024*8));
		}
		if(($image_size >=(1024*9) && $image_size < (1024*10))  || ($src_w > 1920) || ($src_h > 1200)){ //greater  than 5 mb to 10 mb.
			$quality = 35 - floor($image_size/(1024*9));
		}
		if(($image_size >= (1024*10) && $image_size < (1024*11))  || ($src_w > 2560) || ($src_h > 1600)){ //greater  than 10 mb to 15 mb.
			$quality = 25 - floor($image_size/(1024*10));
		}
		
		return (int)$quality;
	}
	private function __get_image_type($image){
		$type = exif_imagetype($image); // [] if you don't have exif you could use getImageSize()
		
		$allowedTypes = array(
			1,  // [] gif
			2,  // [] jpg
			3,  // [] png
			6   // [] bmp
		); 
		if (!in_array($type, $allowedTypes)) {
			return false;
		} 
		return $type;
	}
	private function __get_image_info($image){
		return getimagesize($image);
	}
	private function __create_image($image,$type = false){
		if(!$type){
			$type = self::__get_image_type($image);
		}
		if(!$type){
			return false;
		}
		switch ($type) {
			case 1 :
				$im = imageCreateFromGif($image);
			break;
			case 2 :
				$im = imageCreateFromJpeg($image);
			break;
			case 3 :
				$im = imageCreateFromPng($image);
			break;
			case 6 :
				$im = imageCreateFromBmp($image);
			break;
		}
		
		if($type!=2){
			return $im;
		}
		
		
		$exif = exif_read_data($image);
		
		if(!empty($exif['Orientation'])) {
			switch($exif['Orientation']) {
			case 8:
				$im = imagerotate($im,90,0);
				break;
			case 3:
				$im = imagerotate($im,180,0);
				break;
			case 6:
				$im = imagerotate($im,-90,0);
				break;
			} 
		}
		
		
		return $im;  
	}
	public function crop($src, $dst, $width, $height, $start_width, $start_height, $scale){
		
		$typeImg='';
			$typeImg = self::__get_image_type($src);
		
		
		if($typeImg ==1){
			$im = imageCreateFromGif($src);
		}elseif($typeImg ==2){
			$im = imageCreateFromJpeg($src);
			}elseif($typeImg ==3){
				$im = imageCreateFromPng($src);	
			}elseif($typeImg ==4){
				$im = imageCreateFromBmp($src);	
			}
		//$im = imagecreatefromjpeg($src );
		$src_w = imagesx($im);
		$src_h = imagesy($im);
		
		//zoom-out
		$zoom_out_source_w = $src_w * $scale;
		$zoom_out_source_h = $src_h * $scale;
		
		if($zoom_out_source_w < $width){
			$zoom_out_source_w =$width;
			$ratio = $width/$src_w;
			$zoom_out_source_h = $src_h * $ratio;
		}
			
		$resizedImage = imagecreatetruecolor($zoom_out_source_w, $zoom_out_source_h);
		imagecopyresampled($resizedImage, $im, 0, 0, 0, 0, $zoom_out_source_w,$zoom_out_source_h, $src_w, $src_h);
		
		$x=abs($start_width);
		$y=abs($start_height);
		$newImage = imagecreatetruecolor($width, $height);
		
		zzimagecopyresampled($newImage, $resizedImage, 0, 0, $x, $y, $width, $height, $width, $height);
		header('Content-Type: image/jpeg');
		return imagejpeg($newImage,$dst, 100);
		
	}
	public function mkdir($targetdir) {
		if(!is_dir($targetdir)) {
			App::uses('Folder', 'Utility');
			$dir = new Folder($targetdir, true, 0777);
			chmod ($targetdir , 0777 );
			if(!$dir) {
				return false;
			}
		}else{
			chmod ($targetdir , 0777 );
		}
		return true;
	}
}
