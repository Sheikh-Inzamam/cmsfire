<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Model extends CI_Model{	
	
	//Table
	var $TABLE = "user";
	//Fields
	var $id = "";
	var $name = "";
	var $password = "";
	var $email = "";
	
	function __construct(){	
		$this->load->database();
		parent::__construct();
	}	
		
	/*
		Inserts only handle posts
	*/
	function insert($isMaster=false)
	{
		if($this->exists(strip_tags($this->input->post('name'))) != -1){
			throw new Exception('User Exists!');
		}

		$name = strip_tags($this->input->post('name'));
		$password = sha1(sha1($this->input->post('password').$this->config->item("salt")));
		$email = strip_tags($this->input->post('email'));
		
		$data = array(
				'name'=>$name,
				'password'=>$password,
				'email'=>$email
		);	
		
		if($isMaster){
			$data['isAdmin'] = 1;
			$data['isMaster'] = 1;
		}
		$this->db->insert($this->TABLE, $data);
		$this->session->set_userdata('name', $name);
	}
	
	function login(){
		$name = strip_tags($this->input->post('name'));
		$password = $this->input->post('password');
		
		if($name == "" || strlen($name) == 0){throw new Exception('Empty Name.');}
		
		$data = array(
			'name' => $name,
			'password' => sha1(sha1($password.$this->config->item("salt")))
		);
		$this->db->select('id, banned');
		$this->db->from($this->TABLE);
		$this->db->where($data);
		$result = $this->db->get();		
		if($result->num_rows() == 0){
			throw new Exception('Incorrect Credentials');
		}else{
			if($result->row()->banned == 0){
				$this->session->set_userdata('name', $name);
			}else{
				throw new Exception('You are banned!');
			}
		}
	}
	
	function getCount(){
		$query = "select count(id) as cnt from ".$this->TABLE.";";
		$result = $this->db->query($query);

		foreach($result->result() as $row){
			return $row->cnt;
		}		
		return 0;		
	}

	function logout(){
		//for safety.  could do though: $this->session->unset_userdata('name');
		$this->session->sess_destroy();
	}
	
	function get_by_name($name){
		if($name == ''){return null;}
		return $this->db->where('name', $name)->get($this->TABLE)->row(0);
	}
	
	function get($data){	
		$this->db->where($data);		
		return $this->db->get($this->TABLE);
	}
	
	function ban($userId){
		$query = "update user set banned = 1 where id = ?";
		$this->db->query($query, array($userId));
	}

	function update()
	{
		$data = array(
			'name' => $this->name,
			'password' => sha1(sha1($this->password.$this->config->item("salt"))),
			'email' => $this->email
		);
		
		$this->db->update($this->TABLE, $data);
	}
	
	/*
		Check if User Exists.	
	*/
	function exists($name)
	{		
		if($name == "" || strlen($name) == 0){throw new Exception('Empty Name.');}
		$this->db->select('id');
		$this->db->from('user');
		$this->db->where('name', $name);
		$result = $this->db->get();
		
		foreach ($result->result() as $row){
			return $row->id;
		}		
		return -1;
	}
	
}

?>