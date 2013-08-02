<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class story extends CI_Controller {
	
	private $name = "";
	private $link = "";
	private $description = "";
	private $domain = "";
	
	public function __construct(){
		parent::__construct();
		$this->redirect_not_logged_in(false);
	}
	
	public function index(){
		$this->load->view('core/story');//body content
	}
	

	public function setUpLinkClickSession($storyId){
		try{
			$this->session->set_userdata('linkClicked',  $storyId);
			$post_data = array('result'=>'Success!');
			echo json_encode($post_data);
		}catch(Exception $ex){
			$post_data = array('result'=>$e->getMessage());
			echo json_encode($post_data);
		}
	}

	public function getLinkClickSession(){
		try{			
			$post_data = array('result'=>$this->session->userdata('linkClicked'));
			echo json_encode($post_data);
		}catch(Exception $ex){
			$post_data = array('result'=>'');
			echo json_encode($post_data);
		}
	}	

	public function submit(){
		try{
			$post_data = array('result'=>'');
			$this->load->model('core/story_model');
			$thumbnail = '';			

			if($this->validate()){
				$this->domain = $this->getDomain($this->link);
				$this->story_model->insert($this->domain, $thumbnail);
				$post_data['result'] = 'Success!';
			}
			echo json_encode($post_data);
		}catch(Exception $e){
			$post_data = array('result'=>$e->getMessage());
			echo json_encode($post_data);
		}
	}

	public function delete($storyId, $json=true){
		try{
			$this->load->model('core/story_model');
			$this->story_model->delete($storyId);
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


	public function getCommentCount($id){
		try{
			$this->load->model('core/story_model');
			if($id == ''){$id = 1;}
			$commentCount = $this->story_model->get_comment_count($id);
			echo json_encode($commentCount);
		}catch(Exception $e){
			$post_data = array('result'=>$e->getMessage());
			echo json_encode($post_data);
		}
	}

	public function display($storyId=-1){		
		$this->load->model('core/user_model');	
		$this->load->model('core/comment_model');
		$this->load->model('core/category_model');
		$this->load->library('securimage');
		$this->load->helper('tag_interpreter_helper');
		$this->load->helper('url');
		$this->load->helper('html');
		

		$data['base'] = '/home';
		$data['pageIndex'] = 0;
		$data['category'] = '';
		$data['storyId'] = $storyId;
		$data['username'] = $this->session->userdata('name');
		$data['isAdmin'] = ((isset($this->user_model->get_by_name($this->session->userdata('name'))->isAdmin) && $this->user_model->get_by_name($this->session->userdata('name'))->isAdmin == 1) ? 'true' : 'false');		
		$data['paginateComments'] = count($this->comment_model->get($storyId, 0, 2)); //initial.		
		$data['categoriesResult'] = $this->category_model->get();

		$this->load->view('template/header', $data);
			$this->load->view('template/navigation', $data);
			$this->load->view('core/storyContent', $data);
		$this->load->view('template/footer');		
	}

	//this is returning the ajax call.
	public function load($pageIndex=1){
		$this->load->model('core/story_model');	
		if($pageIndex == ''){$pageIndex = 1;}

		$story = $this->story_model->get_by_id($pageIndex);
		echo json_encode($story);
	}	


	public function parseTitleAndDescription(){
		try{
			$this->load->helper('parse_title_description');
			$storyMetaData = array();			
			$storyMetaData = parse_title_description_helper($this->input->post('link'));
			$post_data = array('result'=>'Success!', 'title'=>$storyMetaData['title'], 'description'=>$storyMetaData['description']);
			echo json_encode($post_data);
		}catch(Exception $e){
			$post_data = array('result'=>$e->getMessage(), 'title'=>'', 'description'=>'');
			echo json_encode($post_data);
		}
	}

	private function validate(){
		$this->form_validation->set_rules('name', 'Title', 'required|trim|max_length[255]|xss_clean');
		$this->form_validation->set_rules('link', 'link', 'trim|max_length[2048]|xss_clean');
		$this->form_validation->set_rules('description', 'Description', 'trim|max_length[2048]|xss_clean');
		
		$this->link = strip_tags($this->input->post('link'));
		$this->description = strip_tags($this->input->post('description'));			
		$this->name = strip_tags($this->input->post('name'));

		if(strlen($this->name) == 0){
			throw new Exception("Empty Title");
		}

		if(strlen($this->link) == 0 && strlen($this->description) == 0){
			throw new Exception("Both link and description fields are empty");
		}
		
		//test-case, if description null, make sure link is valid.
		if(strlen($this->link) > 0 && strlen($this->description) == 0 && !$this->validateURL($this->link)){
			throw new Exception("Link is not valid");
		}

		if($this->form_validation->run()){
			return true;
		}
			
		throw new Exception(validation_errors());
	}
	
	private function getDomain ( $url ) { 
		$url = trim($url);
		//$url = preg_replace("/^(http:\/\/)*(www.)*/is", "", $url); 
		$url = preg_replace("/^(?:https?|ftp):\/\/*(www.)*/is", "", $url); 
		$url = preg_replace("/\/.*$/is" , "" ,$url); 
		return $url; 
	}
	
	private function validateURL($URL) {            
      if((preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $URL))){
        return true;
      } else{
        return false;
      }
    }
	
}

?>