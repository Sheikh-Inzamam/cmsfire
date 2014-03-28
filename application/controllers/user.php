<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//category loading when user first views
class User extends CI_Controller {
	//this will be the first page that will load all the content on the server side...
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){		
		$this->load->model('core/category_model');
		$this->load->model('core/user_model');
		$this->load->model('core/comment_model');
		$this->load->helper('convert_time');
		$this->load->helper('generate_list');
		$this->load->library('securimage');
		$this->load->helper('url');
		$this->load->helper('html');		

		$pageIndex = (($this->uri->segment(4) != "") ? $this->uri->segment(4) : 1);
		$username = $this->uri->segment(2);
		$user = $this->user_model->get_by_name($username);
		//check if user exists
		if(isset($user->id)){
			if($pageIndex == ''){$pageIndex = 1;}
			if($pageIndex < 1){$pageIndex = 1;}
			$data['pageIndex'] = $pageIndex;
			$data['category'] = '';
			$data['loadedUsername'] = $username;
			$data['type'] = "comments";
			$data['base'] = '/f';

			$data['userId'] = $user->id;
			$data['yourUserId'] = ((isset($this->user_model->get_by_name($this->session->userdata('name'))->id)) ? $this->user_model->get_by_name($this->session->userdata('name'))->id : -1);
			$data['showNextPage'] = 'false';
			$data['username'] = $this->session->userdata('name');
			$data['isAdmin'] = ((isset($this->user_model->get_by_name($this->session->userdata('name'))->isAdmin) && $this->user_model->get_by_name($this->session->userdata('name'))->isAdmin == 1) ? 'true' : 'false');
			$data['navigationSelectedHot'] = true;
			$data['categoriesResult'] = $this->category_model->get();

			$commentsResultList = $this->comment_model->get_by_userId($user->id, $pageIndex);
			$data['loadContent'] = generate_list_comment_helper($commentsResultList, $this->session->userdata('name'), $data['isAdmin']);
			$nextLinkCount = count($this->comment_model->get_by_userId($user->id, ++$pageIndex));
			
			if($nextLinkCount > 0){
				$data['showNextPage'] = 'true';
			}	

			$this->load->view('template/header', $data);
				$this->load->view('template/navigation', $data);
				$this->load->view('template/navigationUser', $data);
				$this->load->view('core/user', $data);
			$this->load->view('template/footer');			
		}else{			
		}
	}

	public function ban(){
		try{
			$this->load->model('core/user_model');
			$this->load->model('core/story_model');
			$this->load->model('core/comment_model');

			$isAdmin = ((isset($this->user_model->get_by_name($this->session->userdata('name'))->isAdmin) && $this->user_model->get_by_name($this->session->userdata('name'))->isAdmin == 1) ? 'true' : 'false');
			if(!$isAdmin){
				throw new Exception("You are not an admin.");
			}

			$userId = $this->uri->segment(3);
			$userId = $this->security->xss_clean($userId);

			//make sure user exists...
			$user = $this->user_model->get(array('id'=>$userId));
			if($user->num_rows() == 0){
				throw new Exception('User does not exist!');
			}

			$this->user_model->ban($userId);
			$this->story_model->deleteBannedByUserId($userId);
			$this->comment_model->deleteBannedByUserId($userId);
			$post_data = array('result'=>"Success!");
			echo json_encode($post_data);
		}catch(Exception $e){
			$post_data = array('result'=>$e->getMessage());
			echo json_encode($post_data);	
		}
	}


	public function comments(){		
		$this->load->model('core/category_model');
		$this->load->model('core/user_model');
		$this->load->model('core/comment_model');
		$this->load->helper('convert_time');
		$this->load->helper('generate_list');
		$this->load->library('securimage');
		$this->load->helper('url');
		$this->load->helper('html');		

		$pageIndex = (($this->uri->segment(4) != "") ? $this->uri->segment(4) : 1);
		$username = $this->uri->segment(2);
		$user = $this->user_model->get_by_name($username);
		//check if user exists
		if(isset($user->id)){			
			if($pageIndex == ''){$pageIndex = 1;}
			if($pageIndex < 1){$pageIndex = 1;}
			$data['pageIndex'] = $pageIndex;
			$data['category'] = '';
			$data['loadedUsername'] = $username;
			$data['type'] = "comments";
			$data['base'] = '/f';
			$data['showNextPage'] = 'false';
			$data['userId'] = $user->id;
			$data['yourUserId'] = ((isset($this->user_model->get_by_name($this->session->userdata('name'))->id)) ? $this->user_model->get_by_name($this->session->userdata('name'))->id : -1);
			$data['username'] = $this->session->userdata('name');
			$data['isAdmin'] = ((isset($this->user_model->get_by_name($this->session->userdata('name'))->isAdmin) && $this->user_model->get_by_name($this->session->userdata('name'))->isAdmin == 1) ? 'true' : 'false');
			$data['navigationSelectedHot'] = true;
			$data['categoriesResult'] = $this->category_model->get();
						


			$commentsResultList = $this->comment_model->get_by_userId($user->id, $pageIndex);
			$data['loadContent'] = generate_list_comment_helper($commentsResultList, $this->session->userdata('name'), $data['isAdmin']);
			$nextLinkCount = count($this->comment_model->get_by_userId($user->id, ++$pageIndex));
			
			if($nextLinkCount > 0){
				$data['showNextPage'] = 'true';
			}	

			$this->load->view('template/header', $data);
				$this->load->view('template/navigation', $data);
				$this->load->view('template/navigationUser', $data);
				$this->load->view('core/user', $data);
			$this->load->view('template/footer');			
		}else{			
		}
	}

	public function commentsLiked(){		
		$this->load->model('core/category_model');
		$this->load->model('core/user_model');
		$this->load->model('core/comment_model');		
		$this->load->helper('convert_time');
		$this->load->helper('generate_list');
		$this->load->library('securimage');
		$this->load->helper('url');
		$this->load->helper('html');		

		$pageIndex = (($this->uri->segment(4) != "") ? $this->uri->segment(4) : 1);
		$username = $this->uri->segment(2);
		$user = $this->user_model->get_by_name($username);
		//check if user exists
		if(isset($user->id)){
			if($pageIndex == ''){$pageIndex = 1;}
			if($pageIndex < 1){$pageIndex = 1;}
			$data['pageIndex'] = $pageIndex;
			$data['category'] = '';
			$data['loadedUsername'] = $username;
			$data['type'] = "comments-liked";
			$data['base'] = '/f';
			$data['userId'] = $user->id;
			$data['yourUserId'] = ((isset($this->user_model->get_by_name($this->session->userdata('name'))->id)) ? $this->user_model->get_by_name($this->session->userdata('name'))->id : -1);
			$data['showNextPage'] = 'false';
			$data['username'] = $this->session->userdata('name');
			$data['isAdmin'] = ((isset($this->user_model->get_by_name($this->session->userdata('name'))->isAdmin) && $this->user_model->get_by_name($this->session->userdata('name'))->isAdmin == 1) ? 'true' : 'false');
			$data['navigationSelectedHot'] = true;
			$data['categoriesResult'] = $this->category_model->get();

			$commentsResultList = $this->comment_model->get_comments_liked_by_userId($user->id, $pageIndex);
			$data['loadContent'] = generate_list_comment_helper($commentsResultList, $this->session->userdata('name'), $data['isAdmin']);
			$nextLinkCount = count($this->comment_model->get_comments_liked_by_userId($user->id, ++$pageIndex));
			
			if($nextLinkCount > 0){
				$data['showNextPage'] = 'true';
			}	

			$this->load->view('template/header', $data);
				$this->load->view('template/navigation', $data);
				$this->load->view('template/navigationUser', $data);
				$this->load->view('core/user', $data);
			$this->load->view('template/footer');			
		}else{			
		}
	}	

	public function submitted(){		
		$this->load->model('core/category_model');
		$this->load->model('core/user_model');		
		$this->load->model('core/story_model');
		$this->load->helper('convert_time');
		$this->load->helper('generate_list');
		$this->load->library('securimage');
		$this->load->helper('url');
		$this->load->helper('html');		

		$pageIndex = (($this->uri->segment(4) != "") ? $this->uri->segment(4) : 1);
		$username = $this->uri->segment(2);
		$user = $this->user_model->get_by_name($username);
		//check if user exists
		if(isset($user->id)){
			if($pageIndex == ''){$pageIndex = 1;}
			if($pageIndex < 1){$pageIndex = 1;}
			$data['pageIndex'] = $pageIndex;
			$data['category'] = '';
			$data['loadedUsername'] = $username;
			$data['type'] = "submitted";
			$data['base'] = '/f';
			$data['userId'] = $user->id;
			$data['yourUserId'] = ((isset($this->user_model->get_by_name($this->session->userdata('name'))->id)) ? $this->user_model->get_by_name($this->session->userdata('name'))->id : -1);	
			$data['showNextPage'] = 'false';
			$data['username'] = $this->session->userdata('name');
			$data['isAdmin'] = ((isset($this->user_model->get_by_name($this->session->userdata('name'))->isAdmin) && $this->user_model->get_by_name($this->session->userdata('name'))->isAdmin == 1) ? 'true' : 'false');
			$data['navigationSelectedHot'] = true;
			$data['categoriesResult'] = $this->category_model->get();

			$storyResultList = $this->story_model->get_by_userId($user->id, $pageIndex);
			$data['loadContent'] = generate_list_submit_helper($storyResultList, $this->session->userdata('name'), $data['isAdmin']);
			$nextLinkCount = count($this->story_model->get_by_userId($user->id, ++$pageIndex));
			
			if($nextLinkCount > 0){
				$data['showNextPage'] = 'true';
			}	

			$this->load->view('template/header', $data);
				$this->load->view('template/navigation', $data);
				$this->load->view('template/navigationUser', $data);
				$this->load->view('core/user', $data);
			$this->load->view('template/footer');			
		}else{			
		}
	}	

	public function liked(){		
		$this->load->model('core/category_model');
		$this->load->model('core/user_model');		
		$this->load->model('core/story_model');
		$this->load->helper('convert_time');
		$this->load->helper('generate_list');
		$this->load->library('securimage');
		$this->load->helper('url');
		$this->load->helper('html');		

		$pageIndex = (($this->uri->segment(4) != "") ? $this->uri->segment(4) : 1);
		$username = $this->uri->segment(2);
		$user = $this->user_model->get_by_name($username);
		//check if user exists
		if(isset($user->id)){
			if($pageIndex == ''){$pageIndex = 1;}
			if($pageIndex < 1){$pageIndex = 1;}
			$data['pageIndex'] = $pageIndex;
			$data['category'] = '';
			$data['loadedUsername'] = $username;
			$data['type'] = "liked";
			$data['base'] = '/f';
			$data['showNextPage'] = 'false';
			$data['username'] = $this->session->userdata('name');
			$data['userId'] = $user->id;
			$data['yourUserId'] = ((isset($this->user_model->get_by_name($this->session->userdata('name'))->id)) ? $this->user_model->get_by_name($this->session->userdata('name'))->id : -1);			
			$data['isAdmin'] = ((isset($this->user_model->get_by_name($this->session->userdata('name'))->isAdmin) && $this->user_model->get_by_name($this->session->userdata('name'))->isAdmin == 1) ? 'true' : 'false');
			$data['navigationSelectedHot'] = true;
			$data['categoriesResult'] = $this->category_model->get();

			$storyResultList = $this->story_model->get_liked_by_userId($user->id, $pageIndex);			
			$data['loadContent'] = generate_list_submit_helper($storyResultList, $this->session->userdata('name'), $data['isAdmin']);
			$nextLinkCount = count($this->story_model->get_liked_by_userId($user->id, ++$pageIndex));
			
			if($nextLinkCount > 0){
				$data['showNextPage'] = 'true';
			}	

			$this->load->view('template/header', $data);
				$this->load->view('template/navigation', $data);
				$this->load->view('template/navigationUser', $data);
				$this->load->view('core/user', $data);
			$this->load->view('template/footer');			
		}else{			
		}
	}	

	public function getComments(){		
		$userId = $this->uri->segment(3);
		$pageIndex = $this->uri->segment(4);
	}		
}

?>