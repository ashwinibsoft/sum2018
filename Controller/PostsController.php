<?php 
class PostsController extends AppController {
	public $uses = array('ContentManager.Page');
	



public function create_pdf(){
 
    $posts = $this->Page->find('all');
 
    $this->set(compact('posts'));
 
    $this->layout = '/pdf/default';
 
    $this->render('/Pdf/my_pdf_view');
 
}

public function download_pdf() {
 
    $this->viewClass = 'Media';
 
    $params = array(
 
        'id' => 'test.pdf',
        'name' => 'your_test' ,
        'download' => true,
        'extension' => 'pdf',
        'path' => WWW_ROOT . 'files/pdf' . DS
    );
 
    $this->set($params);
 
}

public function view_pdf() {
 
    $this->viewClass = 'Media';
 
    $params = array(
 
        'id' => 'test.pdf',
        'name' => 'your_test' ,
        'download' => false,
        'extension' => 'pdf',
        'path' => WWW_ROOT . 'files/pdf' . DS
    );
 
    $this->set($params);
 
}

}
