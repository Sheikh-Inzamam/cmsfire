<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//home page when user first views
class Login extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$this->redirect_not_logged_in(true);
		$this->load->view('core/login');//body content	
	}
	
	public function submit(){
		try{			
			$post_data = array('result'=>'');
			$this->load->model('core/user_model');
			
			if($this->validate()){
				$this->user_model->login();
				$post_data['result'] = 'Success!';
			}

			echo json_encode($post_data);

		}catch(Exception $e){
			$post_data = array('result'=>$e->getMessage());
			echo json_encode($post_data);
		}
	}

	public function isLoggedIn(){
		try{
			$post_data = array('isLoggedIn'=>'');
			if(strlen($this->session->userdata('name')) > 0){
				$post_data = array('isLoggedIn'=>'true', 'username'=>$this->session->userdata('name'));
			}else{
				$post_data = array('isLoggedIn'=>'false');				
			}

			echo json_encode($post_data);
		}catch(Exception $e){
			$post_data = array('result'=>$e->getMessage());
			echo json_encode($post_data);	
		}
	}
	
	private function validate(){
		$this->form_validation->set_rules('name', 'Name', 'required|trim|max_length[50]|xss_clean');
		$this->form_validation->set_rules('password', 'Password', 'required|trim|max_length[50]|xss_clean');
	
		if($this->form_validation->run()){
			return true;
		}
		
		throw new Exception(validation_errors());
	}			
}

?>