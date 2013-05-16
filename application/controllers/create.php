<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//home page when user first views
class Create extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$this->redirect_not_logged_in(true);
		$this->load->view('core/create');//body content		
	}
	
	public function submit(){
		try{
			$post_data = array('result'=>'');
			$this->load->model('core/user_model');
			
			if($this->validate()){
				$this->user_model->insert();
				$post_data['result'] = 'Success!';
			}
			echo json_encode($post_data);
		}catch(Exception $e){
			$post_data = array('result'=>$e->getMessage());
			echo json_encode($post_data);
		}
	}

	public function userExists(){
		try{
			$post_data = array('result'=>'');
			$this->load->model('core/user_model');				
			
			if($this->user_model->exists(strip_tags($this->input->post('name'))) == -1){
				$post_data['result'] = 'Name is available';
				$post_data['color'] = "green";
			}else{
				$post_data['result'] = 'Name is taken';
				$post_data['color'] = "red";
			}		
			
			echo json_encode($post_data);
		}catch(Exception $e){
			$post_data = array('result'=>$e->getMessage());
			echo json_encode($post_data);
		}		
	}
	
	private function validate(){
		$this->load->library('securimage'); 
		$this->form_validation->set_rules('name', 'Name', 'required|trim|max_length[255]|xss_clean');
		$this->form_validation->set_rules('password', 'Password', 'required|trim|max_length[255]|xss_clean');
		$this->form_validation->set_rules('repassword', 'Re-Password', 'required|trim|max_length[255]|xss_clean');		
		$this->form_validation->set_rules('email', 'Email', 'trim|max_length[50]|xss_clean');
			

		$inputCode = $this->input->post('imagecode');
        if($this->securimage->check($inputCode) == false){
        	throw new Exception("Captcha is incorrect");
        }

		if(strlen(strip_tags($this->input->post('name'))) == 0 || strlen($this->input->post('name')) != strlen(strip_tags($this->input->post('name')))){
			throw new Exception("Empty name");
		}		

		if($this->input->post('password') != $this->input->post('repassword')){
			throw new Exception("Passwords don't match");
		}
		
		if($this->form_validation->run()){
			return true;
		}
		
		throw new Exception(validation_errors());
	}	
}

?>