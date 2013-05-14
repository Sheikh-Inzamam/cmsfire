<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Image extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('securimage');        
	}	

	private function index(){	
	}

    public function securimage(){
        $this->securimage->show();
    }
} 