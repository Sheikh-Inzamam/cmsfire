<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_model extends CI_Model{
	var $salt = 'PLEASE_CHANGE_THIS_SALT_12312asadf';

	function Login_model()
	{		
	}

	function check_login($username, $password)
	{
		$sha1_password = sha1(sha1(md5($password.$this->salt)));
		$query_str = "select UserId from user where username = ? and password = ?";		
		$result = $this->db->query($query_str,array($username, $sha1_password));
				
		if($result->num_rows() == 1) //succesful
		{
			$this->session->set_userdata('username', $username);	

			$userID = $result->row(0)->UserId;
			$this->session->set_userdata('UserID', $userID);			
			
			$this->update_user_login();
			return $result->row(0)->UserId;
		}
		else
		{
			return false;
		}
	}
	
	//Update user login information
	/*
		increases the login counter, and keeps track of latest login		
	*/	
	function update_user_login()
	{
		$query_str = "update user set login_count = (login_count + 1), last_login = now() where username = ?";		
		$this->db->query($query_str,array($this->session->userdata('username')));
	}
}

?>