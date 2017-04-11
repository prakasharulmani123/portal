<?php
class StaticPagesController extends AppController {
	public $name='StaticPages';
	
/////////////////////////////////////////////////////////////////////////	
	
	public function beforeFilter(){
		$this->set('cpage', 'staticpage');
		parent::beforefilter();
		$this->__validateLoginStatus();
	}

/////////////////////////////////////////////////////////////////////////	

	public function admin_index(){
              $this->layout = "admin-inner";
		if($this->request->is('put') || $this->request->is('post')){
			if($this->StaticPage->save($this->request->data)){
				echo $this->Session->setFlash('Saved Successfully', 'flash_success');
			}
			else{
				echo $this->Session->setFlash('Failed to Save', 'flash_error');
			}
			return $this->redirect('');
		}
		else{
			$this->data = $this->StaticPage->find('first', array('conditions'=>array('StaticPage.static_id'=>1)));
		}
	}

/////////////////////////////////////////////////////////////////////////	

	public function get_static_page_by_id($id=NULL){
		return $this->StaticPage->find('first', array('conditions'=>array('StaticPage.static_id'=>$id)));
	}
}
?>