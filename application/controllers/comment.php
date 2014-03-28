<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class comment extends CI_Controller {
	
	private $link = "";
	private $description = "";
	private $domain = "";
	
	public function __construct(){
		parent::__construct();
	}
	
	
	public function submit(){
		try{
			$post_data = array('result'=>'');
			$this->load->model('core/comment_model');	
			
			if($this->session->userdata('name') == ""){
				throw new Exception("Please Log In");	
			}

			if($this->validate()){	
				$this->comment_model->insert();
				$post_data['result'] = 'Success!';
			}
			echo json_encode($post_data);
		}catch(Exception $e){
			$post_data = array('result'=>$e->getMessage());
			echo json_encode($post_data);
		}
	}

	public function delete($commentId, $json=true){
		try{
			$this->load->model('core/comment_model');
			$commentId = $this->security->xss_clean($commentId);
			$this->comment_model->delete($commentId);
			$post_data = array('result'=>'Success!');
			if(!$json){
				//redirect page.
				if(isset($_SERVER['HTTP_REFERER'])){
					header('Location: ' . $_SERVER['HTTP_REFERER']);
				}
			}else{
				echo json_encode($post_data);
			}
		}catch(Exception $e){
			$post_data = array('result'=>$e->getMessage());
			echo json_encode($post_data);			
		}
	}

	public function get($storyId, $parentCommentId, $pageIndex){
		try{
			$this->load->model('core/comment_model');			
			
			$storyId = $this->security->xss_clean($storyId);
			$parentCommentId = $this->security->xss_clean($parentCommentId);
			$pageIndex = $this->security->xss_clean($pageIndex);

			if($pageIndex == '' || !is_numeric($pageIndex)){
				$pageIndex = 1;
			}

			$comments = $this->comment_model->get($storyId, $parentCommentId, $pageIndex);
			 
			$returnValArr = array();
			$returnValArr['comments'] = $comments;
			$returnValArr['enablePagination'] = ((count($this->comment_model->get($storyId, $parentCommentId, ++$pageIndex)) > 0) ? "true" : "false");
			echo json_encode($returnValArr);
		}catch(Exception $e){
			$post_data = array('result'=>$e->getMessage());
			echo json_encode($post_data);
		}
	}
	
	private function validate(){
		$this->form_validation->set_rules('comment', 'Comment', 'required|trim|max_length[255]|xss_clean');
		$this->form_validation->set_rules('storyId', 'Story', 'trim|max_length[2048]|xss_clean');
		$this->form_validation->set_rules('parentId', 'Parent', 'trim|max_length[2048]|xss_clean');
		

		if($this->form_validation->run()){
			return true;
		}			
		throw new Exception(validation_errors());
	}

}

?>