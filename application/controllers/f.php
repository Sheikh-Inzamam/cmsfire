<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//category loading when user first views
class F extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){		
		$this->load->model('core/category_model');
		$this->load->model('core/user_model');
		$this->load->helper('convert_time');
		$this->load->helper('generate_list');	
		$this->load->library('securimage');
		$this->load->helper('url');
		$this->load->helper('html');		
		
		$category = $this->uri->segment(2);
		//check if category exists
		if($this->categoryExists($category)){
			$pageIndex = $this->uri->segment(3);
			if($pageIndex == ''){$pageIndex = 1;}
			$data['pageIndex'] = $pageIndex;
			$data['category'] = $category;
			$data['base'] = '/f';
			$data['showNextPage'] = 'false';
			$data['username'] = $this->session->userdata('name');
			$data['isAdmin'] = ((isset($this->user_model->get_by_name($this->session->userdata('name'))->isAdmin) && $this->user_model->get_by_name($this->session->userdata('name'))->isAdmin == 1) ? 'true' : 'false');
			$data['navigationSelectedHot'] = true;
			$data['categoriesResult'] = $this->category_model->get();


			$storyResultList = $this->category_model->getLinks($category, $pageIndex);
			$data['loadContent'] = generate_list_submit_helper($storyResultList, $this->session->userdata('name'), $data['isAdmin']);
			$nextLinkCount = count($this->category_model->getLinks($category, ++$pageIndex));
			if($nextLinkCount > 0){
				$data['showNextPage'] = 'true';
			}

			$this->load->view('template/header', $data);
				$this->load->view('template/navigation', $data);
				$this->load->view('template/navigation_2', $data);
				$this->load->view('core/f', $data);
			$this->load->view('template/footer');
		}
	}
	
	public function load(){
		$this->load->model('core/category_model');
		$category = $this->uri->segment(2);
		$pageIndex = $this->uri->segment(4);
		if($pageIndex == ''){$pageIndex = 1;}			
		$links = $this->category_model->getLinks($category, $pageIndex);						
		echo json_encode($links);
	}

	public function add(){
		$category = $this->uri->segment(2);
		if($this->uri->segment(4) != ''){
			header('Location: /f/'.$category.'/add');
		}
		echo "you will be adding to:";
	}

	public function latest(){
		$this->load->model('core/category_model');
		$this->load->model('core/user_model');
		$this->load->helper('convert_time');
		$this->load->helper('generate_list');
		$this->load->library('securimage');
		$this->load->helper('url');
		$this->load->helper('html');
							
		$pageIndex = $this->uri->segment(4);
		if($pageIndex == ''){$pageIndex = 1;}

		$data['base'] = '/f';
		$data['latest'] = 'true';
		$data['pageIndex'] = $pageIndex;
		$data['category'] = $this->uri->segment(2);
		$data['username'] = $this->session->userdata('name');
		$data['showNextPage'] = 'false';
		$data['pageIndex'] = $pageIndex;
		$data['isAdmin'] = ((isset($this->user_model->get_by_name($this->session->userdata('name'))->isAdmin) && $this->user_model->get_by_name($this->session->userdata('name'))->isAdmin == 1) ? 'true' : 'false');
		$data['navigationSelectedLatest'] = true;
		$data['categoriesResult'] = $this->category_model->get();

		$category = $data['category'];

		$storyResultList = $this->category_model->getLinksLatest($category, $pageIndex);
		$data['loadContent'] = generate_list_submit_helper($storyResultList, $this->session->userdata('name'), $data['isAdmin']);		
		$nextLinkCount = count($this->category_model->getLinksLatest($data['category'], ++$pageIndex));
		if($nextLinkCount > 0){
			$data['showNextPage'] = 'true';
		}		

		$this->load->view('template/header', $data);			
			$this->load->view('template/navigation', $data);
			$this->load->view('template/navigation_2', $data);
			$this->load->view('core/f', $data);
		$this->load->view('template/footer');
	}	
	
	private function categoryExists($category){
		$this->load->model('core/category_model');
		$id = $this->category_model->get_by_name($category)->id;

		if(strlen($id) == 0){
			header('Location: /error');
		}
		return true;
	}



	//this is returning the ajax call.
	public function load_latest(){
		$this->load->model('core/category_model');
		$pageIndex = $this->uri->segment(4);
		if($pageIndex == ''){$pageIndex = 1;}
		$category = $this->uri->segment(2);
		
		$links = $this->category_model->getLinksLatest($category, $pageIndex);
		echo json_encode($links);
	}
		
}

?>