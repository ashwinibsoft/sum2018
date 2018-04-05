<?php
Class PdfsController extends ExistingBuyerManagerAppController{
	public $uses = array('ExistingBuyerManager.ExistingBuyer','Country','ExistingBuyerManager.EbLoginDetail','SupplierManager.FeedbackRequest','SupplierManager.FeedbackResponse','QuestionManager.Question','ContentManager.Page');
	public $components=array('Email','RequestHandler','Image');
	var $helpers = array('Captcha','Csv');
	public $paginate = array();
	public $id = null;
	public $template=null;
	
	
	public function view_pdf($id = null) {
    $this->Page->id = $id;
    if (!$this->Page->exists()) {
        throw new NotFoundException(__('Invalid post'));
    }
    // increase memory limit in PHP 
    ini_set('memory_limit', '512M');
    $this->set('post', $this->Page->read(null, 38));
    
    $this->layout = 'pdf';

	}
	
	public function view(){
		
	}
	
	
	
	
}
?>
